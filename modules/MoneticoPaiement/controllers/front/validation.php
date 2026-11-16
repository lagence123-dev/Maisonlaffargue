<?php

use GuzzleHttp\Client;
use \MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Prestashop\utilities\PaymentStatus;

/***
 * Class MoneticoPaiementValidationModuleFrontController
 *
 * This class will be used when a customer return to PrestaShop
 */
class MoneticoPaiementValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $transactionID = Tools::getValue('reference');

        $cart = $this->context->cart;
        if (!$transactionID || mb_strlen($transactionID) > 50 || !ctype_alnum($transactionID) || $cart->id_customer === 0 || $cart->id_address_delivery === 0 || $cart->id_address_invoice === 0 || !$this->module->active) {
            $this->errors[] = $this->l('Payment failed... Try again');
            $this->redirectWithNotifications('index.php?controller=order&step=1');
        }

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] === MoneticoPaiement::MODULE_NAME) {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            die($this->module->l('This payment method is not available.', 'validation'));
        }

        $transaction = MoneticoPaiementHelper::getTransactionByOrderRef($transactionID);
        $customer = new Customer($cart->id_customer);

        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
            return;
        }

        $order = Order::getByCartId((int)$transaction['cart_id']);
        if (!$order) {
            $this->setTemplate('module:MoneticoPaiement/views/templates/hook/payment_return.tpl');
        } else {
            $order_confirmation_url = 'index.php?controller=order-confirmation&id_cart=' . (int)$transaction['cart_id'] . '&id_module=' . (int)$this->module->id . '&id_order=' . $order->id . '&key=' . $customer->secure_key;
            $order_context = json_decode($transaction['order_context'], true);
            $payment_protocol = $order_context['protocole'] ?? '';
            $is_loan_payment = $payment_protocol === 'loan';
            $is_ipa_payment = $payment_protocol === 'ipa';
            $is_pending = $order->getCurrentState() == (int)Configuration::get(PaymentStatus::MP_PENDING);

            if (($is_loan_payment || $is_ipa_payment) && $is_pending) {
                $str = $is_loan_payment ? 'Cofidis' : $this->l('your bank');
                $msg = $this->l('Your payment has been placed on hold for a response from %s. You will be informed as soon as a reply is given.');
                $this->info[] = sprintf($msg, $str);
                $this->redirectWithNotifications($order_confirmation_url);
            } else {
                Tools::redirect($order_confirmation_url);
            }
        }
    }
}
