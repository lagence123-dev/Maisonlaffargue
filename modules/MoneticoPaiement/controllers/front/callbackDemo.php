<?php

use MoneticoPaiement\Model\MoneticoPaiementCodeSite;
use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Prestashop\utilities\PaymentStatus;
use MoneticoPaiement\utilities\PaymentTax;
use MoneticoPaiement\Model\MoneticoPaiementTransaction;

class MoneticoPaiementCallbackDemoModuleFrontController extends ModuleFrontController
{
    use PaymentTax;
    public function postProcess()
    {
        parent::postProcess();
        $cart = $this->context->cart;

        if (Configuration::get('MP_DEMO_MODE_ORDERS') && !empty($cart)) {
            $order_reference = Tools::getValue('reference');

            $status = PaymentStatus::MP_DEMO_PAYMENT;
            $customer = new Customer($cart->id_customer);
            $transaction_id = MoneticoPaiementHelper::getTransactionByOrderRef($order_reference);
            $transaction = new MoneticoPaiementTransaction($transaction_id);
            [$total] = $this->getOrderTotalAndTaxStatus($cart, $transaction);
            $currency = $cart->id_currency;
            $payment_method = $this->module->displayName;
            $shop = new Shop($this->context->shop->id);

            $this->module->validateOrder($cart->id, Configuration::get($status), $total, $payment_method, null,
                ['transaction_id' => $order_reference], $currency, false, $customer->secure_key, $shop);

            Tools::redirect($this->context->link->getModuleLink(\MoneticoPaiement::MODULE_NAME, 'validation', ['reference' => $order_reference]));
        } else {
            $this->setTemplate('module:MoneticoPaiement/views/templates/hook/demo_no_order_return.tpl');
        }
    }
}