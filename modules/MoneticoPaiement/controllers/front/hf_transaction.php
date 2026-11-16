<?php

use MoneticoPaiement\Model\MoneticoPaiementEPT;
use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Model\MoneticoPaiementTransaction;
use MoneticoPaiement\Prestashop\utilities\PaymentStatus;
use MoneticoPaiement\Sdk\Utilities\Collections\ACSResponseValue;
use \PrestaShop\PrestaShop\Adapter\Entity\Order;
use MoneticoPaiement\Model\MoneticoPaiementCodeSite;
use MoneticoPaiement\utilities\PaymentTax;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class MoneticoPaiementhf_transactionModuleFrontController extends ModuleFrontController
{

    use PaymentTax;
    function process()
    {

        $this->ajax = true;
        $cart = $this->context->cart;
        $defaultLang = $this->context->language->iso_code;

        if ($cart->id === null) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // check if transaction by cart id exist in db
        $monetico_transaction_exist = MoneticoPaiementHelper::getTransactionIdByCartId($cart->id);
        if ($monetico_transaction_exist) {
            $transaction = new MoneticoPaiementTransaction($monetico_transaction_exist);
        } else {
            $transaction = new MoneticoPaiementTransaction();
        }

        $customer = new Customer($cart->id_customer);
        $currency = new Currency($cart->id_currency);
        [$total] = $this->getOrderTotalAndTaxStatus($cart, $transaction);

        $order_context = json_decode($transaction->getOrderContext(), true);
        $payment_response = json_decode($transaction->getPaymentResponseContext(), true);
        $order_context_formating = $this->transformHfOrderContext($order_context);
        $payment_response_formating = $this->transformHfReponseContext($payment_response);

        if (isset($payment_response) && isset($order_context["payment"])) {
            try {
                $shop_constraint  = ShopConstraint::allShops();
                if (Shop::getContext() === Shop::CONTEXT_SHOP) {
                    $shop_constraint = ShopConstraint::shop(Shop::getContextShopID());
                } else if (Shop::getContext() === Shop::CONTEXT_GROUP) {
                    $shop_constraint = ShopConstraint::shopGroup(Shop::getContextShopGroupID());
                }

                $configuration = new Configuration();
                $code_site = new MoneticoPaiementCodeSite($transaction->getCodeSiteId());
                $ept = new MoneticoPaiementEPT($code_site->getCodeSiteEptId());

                $data_send_date = $configuration->get('MONETICO_PAIEMENT_DATA_SEND_DATE_' . $code_site->getIdCodeSite(), null, $shop_constraint->getShopGroupId());
                $date_date = date("d-m-y");

                if ($data_send_date === null || $data_send_date != $date_date) {
                    $this->statsSend($ept->getEptNumber(), $code_site->getCodeSiteName(), $ept->getEptMac());
                    $configuration->set('MONETICO_PAIEMENT_DATA_SEND_DATE_' . $code_site->getIdCodeSite(), $date_date, $shop_constraint->getShopGroupId());
                }
            } catch (\Exception $e) {
                $this->createLog('An error occurred when calling ApiStats. ' . $e->getMessage());
            }

            if ($payment_response["return_code"] === 1) {
                $response_amount = $this->formatAmount($order_context["payment"]["amount"]);
                $response_currency = Currency::getIdByIsoCode($order_context["payment"]["amount"]["currency"]);

                if ($total === $response_amount && $currency->id === $response_currency) {
                    $status = PaymentStatus::MP_RECEIVED_PAYMENT;
                    if ($transaction->isSandboxTransaction()) {
                        $status = PaymentStatus::MP_SANDBOX_PAYMENT;
                    }
                } else {
                    $status = PaymentStatus::MP_ERROR_PAYMENT_ORDER_TOTAL;
                }

                $transaction->setOrderContext(json_encode($order_context_formating));
                $transaction->setPaymentResponseContext(json_encode($payment_response_formating));
                $transaction->save();

                $payment_method = $this->module->displayName;

                if ($this->module->validateOrder($cart->id, Configuration::get($status), $response_amount, $payment_method, null,
                    ['transaction_id' => $transaction->getOrderRef()], $response_currency, false, $customer->secure_key)) {
                    $this->createLog(sprintf($this->l('Transaction %s has been validated','hf_transaction'),$transaction->getOrderRef()));
                    $order = Order::getByCartId($transaction->getCartId());
                    Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $transaction->getCartId() . '&id_module=' . (int)$this->module->id . '&id_order=' . $order->id . '&key=' . $customer->secure_key);
                }
            } else {
                $this->createLog(sprintf($this->l('Transaction %s has been rejected','hf_transaction'),$transaction->getOrderRef()));
                $this->errors[] = ACSResponseValue::getReturnCodeResponse($payment_response["return_code"], $defaultLang);
                $this->redirectWithNotifications('index.php?controller=order&step=4');
            }
        }
        Tools::redirect('index.php?controller=order&step=1');
    }


    /**
     * Convert order context to use recovery/canceled action
     *
     * @param array $order_context
     *
     * @return array
     */
    public function transformHfOrderContext(array $order_context)
    {
        $new_order_context = $order_context;
        if (isset($order_context["merchant_configuration"])
            && isset($order_context["order"])
            && isset($order_context["payment"]["amount"])) {
            $new_order_context = [
                "TPE" => $order_context["merchant_configuration"]["point_of_sale"],
                "date" => $this->formatDate($order_context["order"]["date"]),
                "reference" => $order_context["payment"]["reference"],
                "lgue" => $order_context["merchant_configuration"]["language"],
                "societe" => $order_context["merchant_configuration"]["configuration"],
                "montant" => $this->formatAmount($order_context["payment"]["amount"]) . $order_context["payment"]["amount"]["currency"],
            ];
        }

        return $new_order_context;
    }

    /**
     * Convert response context to use refund action
     *
     * @param array $response_context
     *
     * @return array
     */
    public function transformHfReponseContext(array $response_context)
    {
        $new_response_context = $response_context;
        if (isset($response_context["payment"]["authorisation"])) {
            $new_response_context = [
                "numauto" => $response_context["payment"]["authorisation"]["number"],
            ];
        }

        return $new_response_context;
    }

    /**
     * Formating amount from XXXX to XX.XX
     *
     * @param array $amount
     *
     * @return float
     */
    public function formatAmount(array $amount)
    {
        $amount_formating = intval($amount["value"]) / pow(10, intval($amount["exponent"]));
        return round($amount_formating, 2);
    }

    /**
     * Transform date Y-M-DTHH:MM:SS to D/M/Y:HH:MM:SS
     *
     * @param $date
     *
     * @return \DateTime
     */
    public function formatDate($date)
    {

        $new_date = new \DateTime();
        $new_date->createFromFormat(DateTimeInterface::ATOM, $date);
        return $new_date->format("d/m/Y:H:i:s");
    }

    public function createLog($message, $severity = 1)
    {
        $log_message = "[Monetico] " . $message;
        PrestaShopLogger::addLog($log_message, $severity);
    }

    public function statsSend(string $ept_number, string $code_site, string $mac): bool
    {
        /** @var \MoneticoPaiement $module */
        $module = Module::getInstanceByName(MoneticoPaiement::MODULE_NAME);

        return $module->ApiStatSend($ept_number, $code_site, $mac);
    }
}