<?php

use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Model\MoneticoPaiementTransaction;
use MoneticoPaiement\Prestashop\utilities\PaymentStatus;
use MoneticoPaiement\Sdk\Api\Payment\Capture;
use MoneticoPaiement\Sdk\Api\Payment\State;
use MoneticoPaiement\Sdk\Utilities\Helper;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopLogger;

require_once dirname(__FILE__) . '/../../utilities/autoload.utilities.php';

class AdminMoneticoPaiementCaptureController extends \ModuleAdminController
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
        $capture = Tools::getValue('capture');
        $is_instalment_payment = Tools::getValue('instalment_payment');
        $link_values = ['id_order' => $order_id, "vieworder" => ""];

        $_SESSION['MP_ALERT'] = [];
        $_SESSION['MP_ALERT']['MP_STATUS'] = 0;
        if ($capture) {
            $_SESSION['MP_ALERT']['MP_MESSAGE'] = $this->l('An error was encountered during the capture process');
        } else {
            $_SESSION['MP_ALERT']['MP_MESSAGE'] = $this->l('An error was encountered during the cancellation  process');
        }


        if ($order_id) {
            $order = new Order($order_id);

            if ($order && $order->module === $this->module->name) {
                $transaction_id = MoneticoPaiementHelper::getTransactionIdByCartId($order->id_cart);

                if (!empty($transaction_id)) {
                    $transaction = new MoneticoPaiementTransaction($transaction_id);
                    $transaction_commande = json_decode($transaction->getOrderContext(), true);

                    $ept = MoneticoPaiementHelper::getEptFromEptNumber($transaction_commande['TPE']);

                    $capture_request = new Capture();

                    $capture_request->setEpt($transaction_commande['TPE']);

                    $capture_request->setDate(new \DateTime('now'));
                    $capture_request->setOrderDate(\DateTime::createFromFormat("d/m/Y:H:i:s", $transaction_commande['date']));

                    $payment_state = $this->getPaymentState($order->id);
                    $payment_state_result = $payment_state['request_value'];

                    $is_partial_payment = $payment_state_result['modalitepaiement'] === "PP";
                    $total_capture = (bool)Tools::getValue('capture_type');

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
                    //SET the amount without currency
                    $amount_without_currency = Helper::getAmountWithoutCurrency($payment_state_result['montantcommande']);
                    if ($is_instalment_payment && $capture && isset($payment_state_result['echeancier']["echeance"])) {
                        $payment_state_result_echeancier = $payment_state_result['echeancier']["echeance"];
                        $payment_state_result_echeancier_first_amount = $payment_state_result_echeancier[0]["montant"] ?? null;
                        if ($payment_state_result_echeancier_first_amount) {
                            $amount_without_currency = Helper::getAmountWithoutCurrency($payment_state_result_echeancier_first_amount);
                        }
                    }

                    $capture_request->setAmount($amount_without_currency);

                    $is_canceled_instalment_payment = !$capture && $is_instalment_payment;
                    $is_canceled_partial_payment = !$capture && $is_partial_payment;

                    // Captured already amount
                    $payment_already_paid_sum = Helper::getAmountWithoutCurrency($payment_state_result['montantrecouvre']);
                    $capture_request->setCapturedAlreadyAmount($payment_already_paid_sum);

                    $capture_request->setCapturedAmount(0);
                    if ($capture) {
                        if ($is_partial_payment && !$total_capture) {
                            $amount_without_currency = (float)Tools::getValue('amount');
                        } else if ($is_partial_payment && $total_capture) {
                            $amount_without_currency = round($amount_without_currency - $payment_already_paid_sum, 2);
                        }

                        $capture_request->setCapturedAmount($amount_without_currency);
                    }

                    // Captured already amount
                    if ($is_canceled_instalment_payment || $is_canceled_partial_payment) {

                        // if recovery amount is more than 0, that means instalment payment has been partially recovered
                        if ($payment_already_paid_sum > 0) {
                            $capture_request->setStoprecurrence(true);
                        }

                        $capture_request->setRemainingAmount(0);

                    } else {
                        $order_amount_without_currency = Helper::getAmountWithoutCurrency($payment_state_result['montantcommande']);

                        if ($is_partial_payment && !$total_capture) {
                            $amount = (float)Tools::getValue('amount');
                            $remaining_amount = round($order_amount_without_currency - $payment_already_paid_sum - $amount, 2);
                        } else if ($is_partial_payment && $total_capture) {
                            $remaining_amount = round($order_amount_without_currency - $amount_without_currency - $payment_already_paid_sum, 2);
                        } else {
                            $remaining_amount = round($order_amount_without_currency - $amount_without_currency, 2);
                        }
                        $capture_request->setRemainingAmount($remaining_amount);
                    }

                    $currency = new Currency($order->id_currency);
                    $capture_request->setCurrency($currency->iso_code);
                    $capture_request->setReference($transaction_commande['reference']);
                    $capture_request->setLangue($transaction_commande['lgue']);
                    $capture_request->setCodeSociete($transaction_commande['societe']);
                    $capture_request->setEptMAC($ept['ept_mac']);

                    $capture_request_response = $capture_request->check($transaction->isSandboxTransaction());

                    $mode_demo_active = boolval(Configuration::get('MP_DEMO_MODE_ACTIVE'));

                    if ($capture_request_response['success']) {
                        $_SESSION['MP_ALERT']['MP_STATUS'] = 1;
                        if ($capture) {
                            $_SESSION['MP_ALERT']['MP_MESSAGE'] = $this->l('Capture has been successfully executed');
                            // pending -> paid
                            if ($is_instalment_payment || $is_partial_payment) {
                                $order_history = new OrderHistory();
                                $order_history->id_order = $order->id;
                                if ($mode_demo_active) {
                                    $status = $is_partial_payment ? PaymentStatus::MP_DEMO_RECEIVED_PARTIAL_PAYMENT : PaymentStatus::MP_DEMO_RECEIVED_SPLIT_PAYMENT;
                                } else {
                                    $status = $is_partial_payment ? PaymentStatus::MP_RECEIVED_PARTIAL_PAYMENT : PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT[1];
                                }
                                $order_history->changeIdOrderState(Configuration::get($status), $order_id);
                                $order_history->save();
                            }

                        } else {
                            $_SESSION['MP_ALERT']['MP_MESSAGE'] = $this->l('Cancellation has been successfully executed');
                            //update order state to Canceled
                            $order_history = new OrderHistory();
                            $order_history->id_order = $order->id;
                            if ($mode_demo_active) {
                                $order_history->changeIdOrderState(Configuration::get('MP_DEMO_CANCELED'), $order_id);
                            } else {
                                $order_history->changeIdOrderState(Configuration::get('PS_OS_CANCELED'), $order_id);
                            }
                            $order_history->save();
                        }
                        $_SESSION['MP_ALERT']['MP_DESC'] = $capture_request_response;
                    } else {
                        $_SESSION['MP_ALERT']['MP_STATUS'] = 1;
                        $_SESSION['MP_ALERT']['MP_DESC'] = $capture_request_response;
                        $this->createLog($_SESSION['MP_ALERT']['MP_MESSAGE'] . ' : ' . htmlspecialchars($capture_request_response['desc']) . ' ' . htmlspecialchars($capture_request_response['rmk']));
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
     *
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
