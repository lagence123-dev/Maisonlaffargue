<?php

use MoneticoPaiement\Model\MoneticoPaiementAliasBC;
use MoneticoPaiement\Model\MoneticoPaiementEPT;
use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Model\MoneticoPaiementTransaction;
use MoneticoPaiement\Sdk\PaymentPage\PartialPaymentRequest;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentEnvironment;
use MoneticoPaiement\Model\MoneticoPaiementCodeSite;
use MoneticoPaiement\Sdk\Api\Stats\Stats;
use MoneticoPaiement\Sdk\Api\MoneticoPaiementApi;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentProtocol;
use MoneticoPaiement\Sdk\Utilities\HmacComputer;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopLogger;
use MoneticoPaiement\Sdk\Utilities\Helper as MoneticoPaiementSDKHelper;
use MoneticoPaiement\Sdk\Utilities\Models\OrderContext\Billing;
use MoneticoPaiement\Sdk\Utilities\Models\OrderContext\Client;
use MoneticoPaiement\Sdk\Utilities\Models\OrderContext;
use MoneticoPaiement\Sdk\Utilities\Models\OrderContext\Shipping;
use MoneticoPaiement\Sdk\PaymentPage\DifferedPaymentRequest;
use MoneticoPaiement\Sdk\PaymentPage\ImmediatePaymentRequest;
use MoneticoPaiement\Sdk\PaymentPage\InstalmentPaymentRequest;
use MoneticoPaiement\Sdk\Utilities\Helper;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use MoneticoPaiement\utilities\PaymentTax;

/***
 * Class MoneticoPaiementTransactionModuleFrontController
 *
 * This class will be used to create a transaction for get some information about a certain order (like code site and ref_commande, because order is not insert in database)
 */
class MoneticoPaiementTransactionModuleFrontController extends ModuleFrontController
{
    use PaymentTax;

    /**
     * Get billing context / customer billing address
     *
     * @param $cart
     */
    public function getBillingContext($cart)
    {
        /* CUSTOMER BILLING ADDRESS */

        // get invoice address
        $invoice_address = new Address($cart->id_address_invoice);
        $invoice_address_country = new Country($invoice_address->id_country);

        // customer invoice
        $customer_invoice = new Billing();
        // Obligatory
        $customer_invoice->setAddressLine1($invoice_address->address1);
        $customer_invoice->setCity($invoice_address->city);
        $customer_invoice->setPostalCode($invoice_address->postcode);
        $customer_invoice->setCountry($invoice_address_country->iso_code);
        // Obligatory if necessary (like USA)
        if ($invoice_address->id_state > 0) {
            $state = new State($invoice_address->id_state);
            $customer_invoice->setStateOrProvince($state->iso_code);
        }
        // Optional
        $customer_invoice->setFirstName($invoice_address->firstname);
        $customer_invoice->setLastName($invoice_address->lastname);

        if ($invoice_address->address2) {
            $customer_invoice->setAddressLine2($invoice_address->address2);
        }


        return $customer_invoice;
    }

    /**
     * Get shipping context / customer shipping address
     *
     * @param $cart
     */
    public function getShippingContext($cart)
    {

        $delivery_address = new Address($cart->id_address_delivery);
        $delivery_address_country = new Country($delivery_address->id_country);
        $customer_delivery = new Shipping();

        // Obligatory if necessary (like USA)
        $customer_delivery->setAddressLine1($delivery_address->address1);

        if ($delivery_address->address2) {
            $customer_delivery->setAddressLine2($delivery_address->address2);
        }

        $customer_delivery->setCity($delivery_address->city);
        $customer_delivery->setPostalCode($delivery_address->postcode);
        $customer_delivery->setCountry($delivery_address_country->iso_code);

        if ($delivery_address->id_state > 0) {
            $state = new State($delivery_address->id_state);
            $customer_delivery->setStateOrProvince($state->iso_code);
        }

        // Optional
        $customer_delivery->setFirstName($delivery_address->firstname);
        $customer_delivery->setLastName($delivery_address->lastname);
        $customer_delivery->setMatchBillingAddress($cart->id_address_delivery === $cart->id_address_invoice);

        return $customer_delivery;
    }

    /**
     * Get customer context / customer data
     *
     * @param $cart
     */
    public function getCustomerContext($cart)
    {
        $customer = new Customer($cart->id_customer);
        $customer_data = new Client();

        $customer_data->setFirstName($customer->firstname);
        $customer_data->setLastName($customer->lastname);
        $customer_data->setEmail($customer->email);

        return $customer_data;
    }


    public function initContent()
    {
        $this->setTemplate('module:' . $this->module->name . '/views/templates/transaction.tpl');
        $cart = $this->context->cart;

        $shop_constraint  = ShopConstraint::allShops();
        if (Shop::getContext() === Shop::CONTEXT_SHOP) {
            $shop_constraint = ShopConstraint::shop(Shop::getContextShopID());
        } else if (Shop::getContext() === Shop::CONTEXT_GROUP) {
            $shop_constraint = ShopConstraint::shopGroup(Shop::getContextShopGroupID());
        }

        $configuration = new Configuration();

        $reference = Helper::generateRefOrder();

        $payment_form = $_POST;
        $code_site_id = (int)$payment_form['MoneticoPaymentMethodNumber'] ?? 0;
        if ($cart->id === 0 || $code_site_id === 0) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $code_site = new MoneticoPaiementCodeSite($code_site_id);

        if ($code_site->getIdCodeSite() === 0 && !$code_site->isCodeSiteActive()) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $onError = function ($level, $message, $file, $line) {
            throw new ErrorException($message, 0, $level, $file, $line);
        };

        // check if transaction by cart id exist in db
        $monetico_transaction_exist = MoneticoPaiementHelper::getTransactionIdByCartId($cart->id);
        if ($monetico_transaction_exist) {
            $transaction = new MoneticoPaiementTransaction($monetico_transaction_exist);
        } else {
            $transaction = new MoneticoPaiementTransaction();
        }

        [$total_to_pay, $has_taxes] = $this->getOrderTotalAndTaxStatus($cart, $transaction);

        set_error_handler($onError);
        try {

            $ept = new MoneticoPaiementEPT($code_site->getCodeSiteEptId());
            // SEND DATA TO MONETICO PAIEMENT API
            $data_send_date = $configuration->get('MONETICO_PAIEMENT_DATA_SEND_DATE_' . $code_site->getIdCodeSite(), null, $shop_constraint->getShopGroupId());
            $date_day = date("d-m-y");

            if ($data_send_date === null || $data_send_date != $date_day) {
                $this->statsSend($ept->getEptNumber(), $code_site->getCodeSiteName(), $ept->getEptMac());
                $configuration->set('MONETICO_PAIEMENT_DATA_SEND_DATE_' . $code_site->getIdCodeSite(), $date_day, $shop_constraint->getShopGroupId());
            }


            if ($code_site->isCodeSiteSandbox()) {
                $redirect_url = PaymentEnvironment::SANDBOX_URI;
            } else {
                $redirect_url = PaymentEnvironment::URI;
            }

            $customer_id = $cart->id_customer;
            // Find if this customer is a guest (we use the default user group)
            $ps_customer = new Customer($customer_id);
            /* CUSTOMER INVOICE ADDRESS */
            $customer_invoice = $this->getBillingContext($cart);
            /* CUSTOMER SHIPPING ADDRESS : Optional   */
            $customer_delivery = $this->getShippingContext($cart);
            /* CUSTOMER : Optional   */
            $customer = $this->getCustomerContext($cart);
            /* CUSTOMER ORDER */
            $customer_order = new OrderContext();
            $customer_order->setBilling($customer_invoice);
            $customer_order->setShipping($customer_delivery);
            $customer_order->setClient($customer);

            $currency = new \Currency($cart->id_currency);
            $invoice_address = new Address($cart->id_address_invoice);

            // code site filter by amount
            $code_sites_ids = MoneticoPaiementHelper::getCodeSiteIdsByAmount($total_to_pay);

            $code_sites_ids = MoneticoPaiementHelper::codeSiteFilter($code_sites_ids, [
                'code_site_devises' => $currency->iso_code,
                'code_site_shop_ids' => $cart->id_shop,
            ]);
            // Code site filter by optional element
            $code_sites_ids = MoneticoPaiementHelper::codeSiteFilterOptional($code_sites_ids, [
                'code_site_countries' => $invoice_address->id_country,
                'code_site_shops_languages' => $this->context->language->id,
            ]);

            // check if code site id exist with filter
            if (!in_array($code_site_id, $code_sites_ids)) {
                Tools::redirect('index.php?controller=order&step=1');
            }

            $ept = new MoneticoPaiementEPT($code_site->code_site_ept_id);
            switch (strtoupper($ept->ept_payment_mode)) {
                case 'IMMEDIATE':
                    $payment_request = new ImmediatePaymentRequest();
                    break;
                case 'DIFFERED':
                    $payment_request = new DifferedPaymentRequest();
                    break;
                case 'INSTALMENT':
                    $payment_request = new InstalmentPaymentRequest();
                    break;
                case 'PARTIAL':
                    $payment_request = new PartialPaymentRequest();
                    break;
                default:
                    $payment_request = new ImmediatePaymentRequest();
            }

            $payment_request->setEpt($ept->getEptNumber());
            $payment_request->setMail($customer->getEmail());
            $payment_request->setCodeSociete($code_site->getCodeSiteName());
            $payment_request->setCleMac($ept->getEptMac());
            $payment_request->setReference($reference);
            $payment_request->setAmount($total_to_pay);
            $payment_request->setLangue($code_site->getCodeSiteLangue());
            $payment_request->setCurrency($currency->iso_code);
            $payment_request->setContexteCommande($customer_order);
            $payment_request->setDate(new \DateTime('now'));
            $payment_request->setTexteLibre($this->l('Cart', 'Transaction') . ' ' . $this->context->cart->id);
            // additional data for instalment payment
            if ($payment_request instanceof InstalmentPaymentRequest) {

                // PERCENTAGE
                if (!$ept->isEptInstalmentDefault()) {

                    // TERM NUMBER
                    $payment_request->setNbrEch($ept->getEptInstalmentTermNb());
                    // DATE
                    $date_time_now = new \DateTime();
                    $payment_request->setDateEchN($date_time_now, 1);
                    // AMOUNT
                    $remaining_amount = $total_to_pay;
                    for ($n = 1; $n < $payment_request->getNbrEch(); $n++) {
                        if ($ept->getEptInstalmentPct($n) > 0) {
                            $amount_n = round($total_to_pay * $ept->getEptInstalmentPct($n) / 100, 2);
                            $payment_request->setMontantEchN($amount_n, $n);
                            $remaining_amount -= $amount_n;
                        }
                    }
                    // exception remaining amount = total_to_pay => all percentage/amount is zero
                    if ($remaining_amount != $total_to_pay) {
                        $payment_request->setMontantEchN($remaining_amount, $payment_request->getNbrEch());
                    }
                }
            }

            if ($code_site->IsCodeSiteExpressPayment() && !$ps_customer->is_guest) {
                $alias = MoneticoPaiementHelper::getAlias($code_site->getIdCodeSite(), $customer_id);
                if (empty($alias)) {
                    // new alias
                    $alias = Helper::generateRefOrder('', 64);
                    $new_alias = new MoneticoPaiementAliasBC();
                    $new_alias->setCodeSiteId($code_site->getIdCodeSite());
                    $new_alias->setUserId($customer_id);
                    $new_alias->setAlias($alias);
                    $new_alias->setCertified(false);
                    $new_alias->save();
                }
                $payment_request->setAliasCb($alias);
            }

            $payment_request->setThreeDSecureChallenge(MoneticoPaiementHelper::get3dsv2Constraint($code_site->getIdCodeSite(), $total_to_pay));
            $demo_mode_active = boolval($configuration->get('MP_DEMO_MODE_ACTIVE', null, $shop_constraint));

            $payment_request->setUrlRetourOk($this->context->link->getModuleLink(MoneticoPaiement::MODULE_NAME, ($demo_mode_active ? 'callbackDemo' : 'validation'), ['reference' => $reference]));
            $payment_request->setUrlRetourErreur($this->context->link->getModuleLink(MoneticoPaiement::MODULE_NAME, 'validation'));

            if ($code_site->getCodeSitePaymentMethod() && $code_site->getCodeSitePaymentMethod() != 'cb') {
                $payment_request->setProtocole($code_site->getCodeSitePaymentMethod());
            }

            $transaction->setCodeSiteId($code_site->getIdCodeSite());
            $transaction->setCartId($cart->id);
            $transaction->setOrderRef($reference);
            $transaction->setMAC($ept->getEptMac());
            $transaction->setOrderContext(json_encode($payment_request->getFormFields()));
            $transaction->setSandboxTransaction($code_site->isCodeSiteSandbox());
            $transaction->setHasTaxes($has_taxes);

            if ($payment_request->getAliasCb()) {
                $alias_id = MoneticoPaiementHelper::getAliasId($payment_request->getAliasCb());
                if ($alias_id) {
                    $transaction->setAliasId($alias_id);
                }
            }

            // Filter the protocol that we want to disable on the payment page
            $excludedProtocol = MoneticoPaiementHelper::getExcludedProtocol($total_to_pay);
            if (!empty($excludedProtocol)) {
                $payment_request->setDesactiveMoyenPaiement($excludedProtocol);
            }

            if (!$transaction->save()) {
                Tools::redirect('index.php?controller=order&step=1');
            }

            $form_output = '<form method="POST" action="' . $redirect_url . PaymentEnvironment::PAYMENT_FORM . '" name="Monetico">';
            foreach ($payment_request->getFormFields() as $key => $value) {
                $form_output .= '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . (in_array($key, ['url_retour_ok', 'url_retour_err']) ? $value : htmlentities($value)) . '">';
            }

            $form_output .= '</form>';
            $form_output .= '<script> document.forms["Monetico"].submit(); </script>';
            echo $form_output;

        } catch (Throwable $throwable) {
            PrestaShopLogger::addLog("[Monetico] " . $throwable->getMessage() . (_PS_MODE_DEV_ ? ($throwable->getTraceAsString()) : ''), 3);
            Tools::redirect('index.php?controller=order&step=1');
        } finally {
            restore_error_handler();
        }

        return;
    }

    public function statsSend(string $ept_number, string $code_site, string $mac): bool
    {
        /** @var \MoneticoPaiement $module */
        $module = Module::getInstanceByName(MoneticoPaiement::MODULE_NAME);

        return $module->ApiStatSend($ept_number, $code_site, $mac);
    }
}