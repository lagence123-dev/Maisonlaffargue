<?php

use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Model\MoneticoPaiementRefund;
use MoneticoPaiement\Model\MoneticoPaiementTransaction;
use MoneticoPaiement\Prestashop\utilities\PaymentStatus;
use MoneticoPaiement\Sdk\Api\Payment\Refund;
use MoneticoPaiement\Sdk\Api\Payment\State;
use MoneticoPaiement\Sdk\Utilities\Helper;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopLogger;

require_once dirname(__FILE__) . '/../../utilities/autoload.utilities.php';

class AdminMoneticoPaiementRefundController extends \ModuleAdminController
{
    use LegacyLangCompatTrait;

    public function init()
    {
        parent::init();
        $this->bootstrap = true;
        $this->ajax = true;
    }

    public function initContent()
    {
        $order_id = Tools::getValue('order_id');
        $recredite_amount = floatval(Tools::getValue('amount'));
        $is_total_refund = (bool)Tools::getValue('refund_type');
        $link_values = ['id_order' => $order_id, "vieworder" => ""];

        $_SESSION['MP_ALERT'] = [];
        $_SESSION['MP_ALERT']['MP_STATUS'] = 0;
        $_SESSION['MP_ALERT']['MP_MESSAGE'] = $this->l('An error was encountered during the refund process');

        if ($order_id && ($is_total_refund || !$is_total_refund && is_float($recredite_amount) && $recredite_amount > 0)) {
            $order = new Order($order_id);
            if ($order && $order->module === $this->module->name) {
                // get ref transaction from cart id
                $transaction_id = MoneticoPaiementHelper::getTransactionIdByCartId($order->id_cart);

                if (!empty($transaction_id)) {
                    $transaction = new MoneticoPaiementTransaction($transaction_id);
                    $transaction_commande = json_decode($transaction->getOrderContext(), true);
                    // EPT
                    $ept = MoneticoPaiementHelper::getEptFromEptNumber($transaction_commande['TPE']);
                    $refund_request = new Refund();

                    $refund_request->setEpt($transaction_commande['TPE']);

                    $refund_request->setDate(new \DateTime('now'));
                    $order_date = \DateTime::createFromFormat("d/m/Y:H:i:s", $transaction_commande['date']);
                    $refund_request->setOrderDate($order_date);

                    // use payment state to get already refund amount and numauto if necessary
                    $payment_state = $this->getPaymentState($order_id);
                    $payment_state_result = $payment_state['request_value'];

                    // check if payment state request is not a success
                    if (!$payment_state['success']) {
                        $payment_state_response = $payment_state['response_value'];
                        // prepare displayed message
                        $_SESSION['MP_ALERT']['MP_STATUS'] = 1;
                        $_SESSION['MP_ALERT']['MP_DESC'] = $payment_state_response;
                        // add log
                        $alert_description = $payment_state_response['desc'] ?? '' . $payment_state_response['rmk'] ?? '';
                        $this->createLog($_SESSION['MP_ALERT']['MP_MESSAGE'] . ' : ' . $alert_description);
                        // redirect to order page
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders', true, $link_values, $link_values));
                    }

                    $amount_without_currency = Helper::getAmountWithoutCurrency($payment_state_result['montantcommande']);
                    $possible_amount_without_currency = Helper::getAmountWithoutCurrency($payment_state_result['montantrecouvre']);

                    $refund_already_amount = 0;

                    if (isset($payment_state_result['recredits']['total'])) {
                        $payment_state_result_recredits_total = $payment_state_result['recredits']['total'];
                        $refund_already_amount = Helper::getAmountWithoutCurrency($payment_state_result_recredits_total);
                    }

                    $refund_request->setAmount($amount_without_currency);

                    $possible_amount = round($possible_amount_without_currency - $refund_already_amount, 2);

                    if ($is_total_refund) {
                        $recredite_amount = $possible_amount;
                    }

                    $refund_request->setRecreditedAmount($recredite_amount);
                    $refund_request->setAlreadyRecreditedAmount($refund_already_amount);

                    $currency = new Currency($order->id_currency);
                    $refund_request->setCurrency($currency->iso_code);
                    $refund_request->setReference($transaction_commande['reference']);
                    $refund_request->setLangue($transaction_commande['lgue']);
                    $refund_request->setCodeSociete($transaction_commande['societe']);
                    $refund_request->setEptMAC($ept['ept_mac']);

                    // process to refund
                    $refund_request_response = $refund_request->check($transaction->isSandboxTransaction());

                    $_SESSION['MP_ALERT']['MP_DESC'] = $refund_request_response;

                    if ($refund_request_response['success']) {
                        // if request is a success
                        $_SESSION['MP_ALERT']['MP_STATUS'] = 1;
                        $_SESSION['MP_ALERT']['MP_MESSAGE'] = $this->l('Refund has been successfully executed');
                        // add refund to database
                        $refund = new MoneticoPaiementRefund();
                        $refund->setOrderId($order->id);
                        $refund->setAmount(floatval($recredite_amount));
                        $refund->setRefundContext(json_encode($refund_request->getFormFields()));
                        if ($refund->save()) {
                            // we add a new history with the new state
                            $order_history = new OrderHistory();
                            $order_history->id_order = $order->id;

                            // if the refund is the maximum amount
                            $display_refunded_status = $possible_amount === $recredite_amount && (
                                    $payment_state_result['modalitepaiement'] === "PF" ||
                                    $possible_amount_without_currency === $amount_without_currency ||
                                    ($payment_state_result['modalitepaiement'] === "PP" && $payment_state_result['etat'] === "PA")
                                );

                            if ($display_refunded_status) {
                                $new_stat_id = Configuration::get('PS_OS_REFUND');
                            } else {
                                $new_stat_id = Configuration::get(PaymentStatus::MP_PARTIAL_REFUND);
                            }
                            $order_history->changeIdOrderState($new_stat_id, $order->id);
                            $order_history->save();
                        }
                    } else {
                        $_SESSION['MP_ALERT']['MP_DESC'] = $refund_request_response;
                        $this->createLog($_SESSION['MP_ALERT']['MP_MESSAGE'] . ' : ' . $refund_request_response['desc'] . ' ' . $refund_request_response['rmk']);
                    }
                }
            }
        }

        Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders', true, $link_values, $link_values));

    }

    public function createLog($message, $severity = 1)
    {
        $log_message = "[Monetico] " . $message;
        PrestaShopLogger::addLog($log_message, $severity);
    }

    /**
     * Get current Payment State
     * @param $order_id
     *
     * @return array
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getPaymentState($order_id)
    {
        $order = new Order($order_id);

        $payment_state_request = new State();
        $transaction_id = MoneticoPaiementHelper::getTransactionIdByCartId($order->id_cart);
        if (empty($transaction_id)) {
            $message = sprintf($this->l('The Monetico transaction was not found for the order %s.'), $order->id_cart);
            $this->createLog($message, 3);
            return [
                'success' => 0,
                'response_value' => [
                    'desc' => $message
                ]
            ];
        }

        $transaction = new MoneticoPaiementTransaction($transaction_id);
        $transaction_commande = json_decode($transaction->getOrderContext(), true);

        $payment_state_request->setEpt($transaction_commande['TPE']);
        $payment_state_request->setDate(\DateTime::createFromFormat("d/m/Y:H:i:s", $transaction_commande['date']));
        $currency = new \Currency($order->id_currency);

        $payment_state_request->setCurrency($currency->iso_code);
        $amount_without_currency = Helper::getAmountWithoutCurrency($transaction_commande['montant']);
        $payment_state_request->setAmount($amount_without_currency);

        $payment_state_request->setReference($transaction_commande['reference']);
        $payment_state_request->setCodeSociete($transaction_commande['societe']);
        $payment_state_request->setResponseLangue($this->context->language->iso_code);

        $ept = MoneticoPaiementHelper::getEptFromEptNumber($transaction_commande['TPE']);
        if (!$ept) {
            return [
                'success' => 0,
                'response_value' => [
                    'desc' => sprintf($this->l('EPT used for this order was not found. Check if the EPT %s exist.'), $transaction_commande['TPE'])
                ]
            ];
        }

        $payment_state_request->setEptMAC($ept['ept_mac']);

        return $payment_state_request->check($transaction->isSandboxTransaction());
    }
}
