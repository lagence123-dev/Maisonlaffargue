<?php

use MoneticoPaiement\Model\MoneticoPaiementCodeSite;
use MoneticoPaiement\Model\MoneticoPaiementEPT;
use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Model\MoneticoPaiementTransaction;
use MoneticoPaiement\Sdk\Api\MoneticoPaiementApi;
use MoneticoPaiement\Sdk\Api\PaymentSession\ErgonomySettings;
use MoneticoPaiement\Sdk\Api\PaymentSession\MerchantConfiguration;
use MoneticoPaiement\Sdk\Api\PaymentSession\PaymentMean;
use MoneticoPaiement\Sdk\Api\PaymentSession\PaymentSessionInitiation;
use MoneticoPaiement\Sdk\Utilities\HmacComputer;
use MoneticoPaiement\Sdk\Utilities\Models\Payment\Amount;
use MoneticoPaiement\Sdk\Utilities\Helper;
use MoneticoPaiement\Sdk\Utilities\Models\Order\Customer;
use MoneticoPaiement\Sdk\Api\PaymentSession\Order;
use MoneticoPaiement\utilities\PaymentTax;

class MoneticoPaiementPayment_sessionModuleFrontController extends ModuleFrontController
{
    use PaymentTax;

    /**
     * Receive AJAX calls and manage associated actions
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function initContent()
    {
        $this->ajax = true;
        $hmac_computer = new HmacComputer();
        $step = $_POST['step'] ?? null;

        if (!in_array($step, ['init', 'transaction'])) {
            exit(0);
        }

        $code_site_id = $_POST['code_site_id'] ?? null;
        $reference = $_POST['reference'] ?? null;
        $date = $_POST['date'] ?? null;
        $expected_seal = $_POST['seal'] ?? null;

        $code_site = new MoneticoPaiementCodeSite($code_site_id);

        if (!Validate::isLoadedObject($code_site)) {
            exit(0);
        }

        $ept_id = $code_site->getCodeSiteEptId();
        $ept = new MoneticoPaiementEPT($ept_id);

        if (!Validate::isLoadedObject($ept)) {
            exit(0);
        }

        $data_response = false;

        if ($step === 'init') {
            $validated_seal = $hmac_computer->validateSeal([$code_site_id], $ept->getEptMac(), $expected_seal);
            if ($validated_seal) {
                $payment_session_data = $this->initPaymentSession($ept, $code_site);

                if (isset($payment_session_data['id'])) {

                    $fields = [
                        $code_site_id,
                        $payment_session_data['reference'],
                        $_SESSION['payment_session_date_transaction_' . $code_site->getIdCodeSite()]->format("d/m/Y:H:i:s")
                    ];
                    $transaction_seal = $hmac_computer->sealFields($fields, $ept->getEptMac());
                    $data_response = [
                        "session_id" => $payment_session_data['id'],
                        "transaction_seal" => $transaction_seal,
                        "transaction_date" => $_SESSION['payment_session_date_transaction_' . $code_site->getIdCodeSite()]->format("d/m/Y:H:i:s"),
                        "transaction_reference" => $payment_session_data['reference']
                    ];
                }
            }
        } else if ($step === 'transaction') {
            $validated_seal = $hmac_computer->validateSeal([$code_site_id, $reference, $date], $ept->getEptMac(), $expected_seal);
            if ($validated_seal) {
                $data_response = $this->saveTransaction($code_site, $reference, $date, $ept);
            }
        }
        die(json_encode($data_response));
    }

    /**
     * Init payment session and save data in $_SESSION
     * @param $ept
     * @param $code_site
     * @return array|null
     */
    private function initPaymentSession($ept, $code_site)
    {
        $cart = $this->context->cart;
        $date = new \DateTime('now');
        $payment_session_data = $this->requestInitPaymentSession($ept, $code_site, $cart, $date);

        if (isset($payment_session_data)) {
            session_start();
            $_SESSION['payment_session_data_' . $code_site->getIdCodeSite()] = $payment_session_data;
            $_SESSION['payment_session_date_transaction_' . $code_site->getIdCodeSite()] = $date;
        }
        return $payment_session_data;
    }

    /**
     * Create and send request to Monetico API to init payment session
     * @param $ept
     * @param $code_site
     * @param $cart
     * @param $date
     * @return array|null
     */
    private function requestInitPaymentSession($ept, $code_site, $cart, $date)
    {
        $response = null;
        try {
            $merchant_config = new MerchantConfiguration();
            $merchant_config->setVersion('3.0');
            $merchant_config->setPointOfSale($ept->getEptNumber());
            $merchant_config->setLanguage($code_site->getCodeSiteLangue());
            $merchant_config->setConfiguration($code_site->getCodeSiteName());

            $customer = new \Customer($cart->id_customer);
            $monetico_customer = new Customer();
            $monetico_customer->setMail($customer->email);

            $exponent = 2;
            [$total] = $this->getOrderTotalAndTaxStatus($cart);

            $currency = new Currency($cart->id_currency);
            $iso_code = $currency->getIsoCodeById($currency->id);
            $payment_session_amount = new Amount();
            $payment_session_amount->setValue(round($total * pow(10, $exponent)));
            $payment_session_amount->setCurrency($iso_code);
            $payment_session_amount->setExponent($exponent);

            $payment_session_order = new Order();
            $payment_session_order->setAmount($payment_session_amount);

            $payment_session_order->setDate($date);
            $payment_session_order->setComment($this->l('Cart') . ' ' . $cart->id);
            $payment_session_order->setReference(Helper::generateRefOrder());
            $payment_session_order->setCustomer($monetico_customer);

            $ergonomy_settings = new ErgonomySettings();
            $ergonomy_settings->setIntegrationType('payment_component');
            $module_name = $this->module->name;

            $url = str_replace('https://localhost', 'https://127.0.0.1', $this->context->link->getModuleLink($module_name, 'validation', [], true));
            // Note Florent 28/01/2026:
            // locally it typically gives a URL like that https://127.0.0.1/prestashop_9_recette/module/MoneticoPaiement/validation?reference=MP0gAIc41WSF
            // Which doesn't work, only with PS9, because it is transformed to https://localhost/prestashop_9_recette/?reference=MP0gAIc41WSF
            $ergonomy_settings->setRedirectionUrlForFailedPayment($url);
            $ergonomy_settings->setRedirectionUrlForCancelPayment($url);

            $args['reference'] = $payment_session_order->getReference();
            $url = str_replace('https://localhost', 'https://127.0.0.1', $this->context->link->getModuleLink($module_name, 'validation', $args, true));
            $ergonomy_settings->setRedirectionUrlForSuccessfulPayment($url);

            $args['pending'] = true;
            $url = str_replace('https://localhost', 'https://127.0.0.1', $this->context->link->getModuleLink($module_name, 'validation', $args, true));
            $ergonomy_settings->setRedirectionUrlForPendingPayment($url);

            $merchant_config->setErgonomySettings($ergonomy_settings);

            $payment_mean = new PaymentMean();
            $payment_mean->setType('payment_initiation');

            $payment_session_initiation = new PaymentSessionInitiation();
            $payment_session_initiation->setMerchantConfiguration($merchant_config);
            $payment_session_initiation->setOrder($payment_session_order);
            $payment_session_initiation->setPaymentMeans([$payment_mean]);

            $request = new MoneticoPaiementApi();
            $hmacComputer = new HmacComputer();
            $fields = json_encode($payment_session_initiation->getFields());
            $seal = $hmacComputer->sealString($fields, $ept->getEptMac());

            $header = [
                'Content-Type:application/json',
                'MAC:' . strtoupper($seal)
            ];

            $request->process($payment_session_initiation->getUrl($code_site->isCodeSiteSandbox()), $payment_session_initiation->getFields(), $header);
            $payment_session_initiation_response = json_decode($request->getResponse(), true);

            if (is_array($payment_session_initiation_response) && isset($payment_session_initiation_response['state']) &&
                $payment_session_initiation_response['state'] === 'initiated') {

                $response = $payment_session_initiation_response;
            }

        } catch (Exception $exception) {
            PrestaShopLogger::addLog("[Monetico] " . $exception->getMessage() . (_PS_MODE_DEV_ ? ($exception->getTraceAsString()) : ''), 3);
        }

        return $response;
    }

    /**
     * Create, save and update transaction when customer click on "pay" button on the IPA web component
     * @param $code_site
     * @param $reference
     * @param $date
     * @param $ept
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function saveTransaction($code_site, $reference, $date, $ept)
    {
        $cart = $this->context->cart;

        if (!isset($cart)) {
            return false;
        }

        // check if transaction by cart id exist in db
        $monetico_transaction_exist = MoneticoPaiementHelper::getTransactionIdByCartId($cart->id);
        $transaction = new MoneticoPaiementTransaction();
        if ($monetico_transaction_exist) {
            $transaction = new MoneticoPaiementTransaction($monetico_transaction_exist);
        }

        [$total, $has_taxes] = $this->getOrderTotalAndTaxStatus($cart, $transaction);

        $currency = new Currency($cart->id_currency);
        $transaction->setCodeSiteId($code_site->getIdCodeSite());
        $transaction->setCartId($cart->id);
        $transaction->setOrderRef($reference);
        $transaction->setMAC($ept->getEptMac());
        $transaction->setSandboxTransaction($code_site->isCodeSiteSandbox());
        $transaction->setHasTaxes($has_taxes);
        $transaction->setOrderContext(json_encode([
            "TPE" => $ept->getEptNumber(),
            "societe" => $code_site->getCodeSiteName(),
            "reference" => $reference,
            "date" => $date,
            "lgue" => $code_site->getCodeSiteLangue(),
            "montant" => $total . $currency->getIsoCodeById($currency->id),
            'protocole' => 'ipa'
        ]));

        return $transaction->save();
    }

}