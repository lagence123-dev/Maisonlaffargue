<?php

use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Model\MoneticoPaiementTransaction;
use MoneticoPaiement\Sdk\Api\MoneticoPaiementApi;
use MoneticoPaiement\Model\MoneticoPaiementEPT;
use MoneticoPaiement\Sdk\Utilities\Helper;
use MoneticoPaiement\Sdk\Utilities\HmacComputer;
use MoneticoPaiement\Sdk\Utilities\Models\OrderContext\Billing;
use MoneticoPaiement\Sdk\Api\PaymentService\Payment;
use MoneticoPaiement\Sdk\Api\PaymentService\Payment\InstalmentPayment;
use MoneticoPaiement\Sdk\Api\PaymentService\MerchantConfiguration;
use MoneticoPaiement\Sdk\Api\PaymentService\Order;
use MoneticoPaiement\Sdk\Api\PaymentService\Payment\PaymentMean;
use MoneticoPaiement\Sdk\Api\PaymentService\PaymentService;
use MoneticoPaiement\Sdk\Utilities\Models\OrderContext\Browser;
use MoneticoPaiement\Sdk\Utilities\Models\OrderContext;
use MoneticoPaiement\Sdk\Utilities\Models\Order\Customer as SdkCustomer;
use MoneticoPaiement\Sdk\Api\PaymentService\Authentication;
use MoneticoPaiement\Model\MoneticoPaiementCodeSite;
use MoneticoPaiement\Sdk\Utilities\Models\Payment\Amount;
use PrestaShop\PrestaShop\Adapter\Entity\Cart;
use MoneticoPaiement\Sdk\Api\PaymentService\Authentication\Details;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentServiceResponseValue;
use MoneticoPaiement\utilities\PaymentTax;
use MoneticoPaiement\Sdk\Response\PaymentService\PaymentServiceResponse;

class MoneticoPaiementpayment_serviceModuleFrontController extends ModuleFrontController
{
    use PaymentTax;

    private $IframeHeight = 300;
    private $IframeWidth = 200;


    public function getPaymentServiceUrl($data)
    {
        return $this->context->link->getModuleLink(MoneticoPaiement::MODULE_NAME, 'payment_service', $data, true);;
    }

    public function getTransactionUrl()
    {
        return $this->context->link->getModuleLink(MoneticoPaiement::MODULE_NAME, 'hf_transaction', [], true);
    }

    /**
     * @return int
     */
    public function getIframeHeight(): int
    {
        return $this->IframeHeight;
    }

    /**
     * @return int
     */
    public function getIframeWidth(): int
    {
        return $this->IframeWidth;
    }


    /**
     * Get billing Context from Cart
     *
     * @param $cart
     *
     * @return \MoneticoPaiement\Sdk\Utilities\Models\OrderContext\Billing
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getBillingContext($cart): Billing
    {
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
     * Main function
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    function postProcess()
    {
        $this->ajax = true;
        $form = $_SERVER['REQUEST_METHOD'] === 'GET' ? $_GET : $_POST;

        $stepName = strval($form['stepName'] ?? $_GET['stepName'] ?? '');
        $cartId = intval($form['cartId'] ?? $_GET['cartId'] ?? 0);

        if (!$cartId) {
            return;
        }
        // check if transaction by cart id exist in db
        $monetico_transaction_exist = MoneticoPaiementHelper::getTransactionIdByCartId($cartId);
        $transaction = new MoneticoPaiementTransaction();
        if ($monetico_transaction_exist) {
            $transaction = new MoneticoPaiementTransaction($monetico_transaction_exist);
        }

        $data = '';
        if ($stepName === 'init') {
            $data = $this->initPaymentService($form, $cartId);
        } else if ($stepName === 'process') {
            $data = $this->processPaymentService($form, $transaction, $cartId);
        } else if ($stepName === 'check_paymentservice_authentication') {
            $data = $this->checkPaymentServiceAuthentication($form, $transaction);
        }

        echo $data;
    }

    /**
     * Initialize the payment service process
     *
     * @param $form
     * @param $cart_id
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function initPaymentService($form, $cart_id)
    {
        // PaymentState
        $seal = strval($form['seal'] ?? '');
        $code_site_id = intval($form['codeSiteId'] ?? 0);
        $token = strval($form['token'] ?? '');
        if ($seal == '' || $code_site_id == 0 || $token == '') {
            return;
        }

        $code_site = new MoneticoPaiementCodeSite($code_site_id);
        $ept = new MoneticoPaiementEPT($code_site->getCodeSiteEptId());
        $hmacComputer = new HmacComputer();

        $validateSeal = $hmacComputer->validateSeal([$ept->getEptNumber(), $code_site_id, $cart_id, $token], $ept->getEptMac(), $seal);

        if (!$validateSeal) {
            return;
        }

        $cart = new \Cart($cart_id);
        $reference = Helper::generateRefOrder();
        $this->createLog(sprintf($this->l('Payment service initiated with reference: %s', "Payment_service"), $reference));
        $datetime_now = new \DateTime('now');
        // check if transaction by cart id exist in db
        $monetico_transaction_exist = MoneticoPaiementHelper::getTransactionIdByCartId($cart->id);
        $transaction = new MoneticoPaiementTransaction();
        if ($monetico_transaction_exist) {
            $transaction = new MoneticoPaiementTransaction($monetico_transaction_exist);
        }

        [$total_to_pay, $has_taxes] = $this->getOrderTotalAndTaxStatus($cart, $transaction);

        $payment_service = new PaymentService();

        $payment_service_configuration = new MerchantConfiguration();
        $payment_service_configuration->setPointOfSale($ept->getEptNumber());
        $payment_service_configuration->setLanguage($code_site->getCodeSiteLangue());
        $payment_service_configuration->setConfiguration($code_site->getCodeSiteName());

        $payment_service->setMerchantConfiguration($payment_service_configuration);

        $psCustomer = new Customer($cart->id_customer);
        $sdkCustomer = new SdkCustomer();
        $sdkCustomer->setMail($psCustomer->email);

        $payment_service_order = new Order();
        $payment_service_order->setDate($datetime_now);
        $payment_service_order->setCustomer($sdkCustomer);

        /* CUSTOMER INVOICE ADDRESS */
        $customer_invoice = $this->getBillingContext($cart);
        /* CUSTOMER ORDER */
        $customer_order = new OrderContext();
        $customer_order->setBilling($customer_invoice);
        /* CUSTOMER BROWSER */
        $customer_browser = new Browser();
        $customer_browser->setAcceptHeader("*/*");
        $customer_browser->setJavaEnabled(true);
        $customer_browser->setJavascriptEnabled(true);
        $customer_browser->setLanguage("fr-FR");
        $customer_browser->setColorDepth(24);
        $customer_browser->setScreenHeight($this->getIframeHeight());
        $customer_browser->setScreenWidth($this->getIframeWidth());
        $customer_browser->setTimezone(-120);
        $customer_browser->setUserAgent($_SERVER['HTTP_USER_AGENT']);

        $customer_order->setBrowser($customer_browser);

        $payment_service_order->setContext($customer_order);
        $payment_service->setOrder($payment_service_order);

        $payment_service_payment_mean = new PaymentMean();

        $payment_service_payment_mean->setPaymentMeanToken($token);

        $payment_service_payment = new Payment();
        $payment_service_payment->setTransactionInitiator("cardholder");
        $payment_service_payment->setReference($reference);
        $payment_service_payment->setPaymentMean($payment_service_payment_mean);

        if ($ept->getEptPaymentMode() === 'INSTALMENT') {
            if (!$ept->isEptInstalmentDefault()) {
                $instalment = new InstalmentPayment();
                // TERM NUMBER
                $nbrEch = $ept->getEptInstalmentTermNb();
                // DATE
                $dataEch = new \DateTime();
                // AMOUNT
                $remaining_amount = $total_to_pay;

                $n = 1;
                $amount_n = round($total_to_pay * $ept->getEptInstalmentPct($n) / 100, 2);
                $instalment->addInstalment($dataEch, round($amount_n * pow(10, 2)));
                $remaining_amount -= $amount_n;
                $n++;

                for ($n; $n < $nbrEch; $n++) {
                    if ($ept->getEptInstalmentPct($n) > 0) {
                        $amount_n = round($total_to_pay * $ept->getEptInstalmentPct($n) / 100, 2);
                        $instalment->addInstalment(Helper::monthShifter($dataEch, $n - 1), round($amount_n * pow(10, 2)));
                        $remaining_amount -= $amount_n;
                    }
                }

                // exception remaining amount = total_to_pay => all percentage/amount is zero
                if ($remaining_amount != $total_to_pay) {
                    $instalment->addInstalment(Helper::monthShifter($dataEch, $n - 1), round($remaining_amount * pow(10, 2)));
                }
                $payment_service_payment->setInstalmentPayment($instalment);
            }
        }

        $currency = new \Currency($cart->id_currency);
        $amount = new Amount();
        $amount->setValue(round($total_to_pay * pow(10, 2)));
        $amount->setCurrency($currency->iso_code);
        $amount->setExponent(2);

        $payment_service_payment->setAmount($amount);
        $payment_service->setPayment($payment_service_payment);

        $payment_service_authentication = new Authentication();

        $hmacComputer = new HmacComputer();

        $merchant_redirection_params = [
            'cartId' => $cart->id,
            'orderRef' => $reference,
            'codeSiteId' => $code_site_id,
            'stepName' => 'check_paymentservice_authentication'
        ];

        // merchant redirection params seal
        $seal = $hmacComputer->sealFields($merchant_redirection_params, $ept->getEptMac());
        $merchant_redirection_params['seal'] = $seal;
        // merchant redirection
        $redirect_url = $this->getPaymentServiceUrl($merchant_redirection_params);
        $payment_service_authentication->setMerchantRedirectionUrl($redirect_url);

        $payment_service_authentication->setChallengeWindowSize("full_screen");

        $payment_service->setAuthentication($payment_service_authentication);

        $transaction->setCodeSiteId($code_site->getIdCodeSite());
        $transaction->setCartId($cart->id);
        $transaction->setOrderRef($reference);
        $transaction->setMAC($ept->getEptMac());
        $transaction->setOrderContext(json_encode($payment_service->getFields()));
        $transaction->setSandboxTransaction($code_site->isCodeSiteSandbox());
        $transaction->setHasTaxes($has_taxes);
        if (!$transaction->save()) {
            return;
        }
        $request = new MoneticoPaiementApi();

        $hmacComputer = new HmacComputer();
        $fields = json_encode($payment_service->getFields());
        $seal = $hmacComputer->sealString($fields, $ept->getEptMac());

        $header = [
            'Content-Type:application/json',
            'MAC:' . $seal
        ];

        $request->setCurlOpt(CURLOPT_TIMEOUT, 30);
        $request->process($payment_service->getUrl($transaction->isSandboxTransaction()), $payment_service->getFields(), $header);
        $dataResponse = $request->getResponse();
        $returnData = json_decode($dataResponse, true);
        $transaction->setPaymentResponseContext($dataResponse);
        if (!$transaction->save()) {
            return;
        }
        // request treatment
        $paymentServiceResponse = new PaymentServiceResponse($returnData);
        $paymentServiceResponse->setTransactionUrl($this->getTransactionUrl());
        //$data = $this->getRequestTreatment($returnData, $cart_id);

        if ($paymentServiceResponse->getReturnCode() === 2) {
            $paymentServiceResponse->setPaymentServiceUrl(
                $this->getPaymentServiceUrl([
                    'stepName' => 'process',
                    'paymentToken' => $returnData['payment_token'] ?? 0,
                    'cartId' => $cart_id
                ])
            );
            $data = $paymentServiceResponse->goToPaymentServiceUrl();
        } else {
            // depending on the return code, this will redirect the modal content toward the 'hf_transaction' controller,
            // or change the modal content to display the error "page".
            // In Wordpress there is no need to call the equivalent of hf_transaction by ajax.
            $data = $paymentServiceResponse->getRequestTreatment($this->context->language->iso_code);
        }

        return $data;
    }

    /**
     * Process to 3ds method request
     *
     * @param $form
     * @param MoneticoPaiementTransaction $transaction
     *
     * @return string
     */
    public function processPaymentService($form, $transaction, $cartId)
    {

        if ($transaction->getPaymentResponseContext() === '') {
            return '';
        }
        $data = json_decode($transaction->getPaymentResponseContext(), true);
        // Note: in Wordpress, the data is coming from the session, under the MoneticoPaiementHostedFieldsCurrentSessionData key
        // instead of the "payment response context".
        $paymentServiceResponse = new PaymentServiceResponse($data);

        // note that the $paymentServiceResponse's "TransactionUrl" is actually the automatic redirect URL
        // when the payment was successful
        $paymentServiceResponse->setTransactionUrl($this->getTransactionUrl());

        $nextStepName = $paymentServiceResponse->getNextStep()->getStep();

        if ($nextStepName == 'technical_information_collecting') {
            $this->createLog(
                sprintf($this->l('Technical information collection for %s', 'payment_service')
                    , $transaction->getOrderRef()
                )
            );

            $process = $this->processTechnicalInformationCollecting($paymentServiceResponse, $form, $transaction, $cartId);
        }
        //
        elseif ($nextStepName == 'cardholder_authentication') {
            // this step can happen without the 'technical_information_collecting' step first
            $this->createLog(sprintf($this->l('Card holder authentication for %s', 'payment_service'), $transaction->getOrderRef()));

            $process = $this->processCardholderAuthentication($paymentServiceResponse, $transaction, $cartId);
        }
        //
        elseif ($nextStepName == 'check_paymentservice_authentication') {
            // Note: this is most likely dead code, as 'check_paymentservice_authentication' is never a next step name...
            // checkPaymentServiceAuthentication handles the request where the customer returns from the ACS.
            $this->createLog(sprintf($this->l('Check payment service authentication for %s', 'payment_service'), $transaction->getOrderRef()));

            $process = $this->checkPaymentServiceAuthentication($form, $transaction);
        }

        return $process;
    }

    /**
     * 4.1 La valeur du champ « step » est « technical_information_collecting »
     * @return string  collect technical information
     */
    private function processTechnicalInformationCollecting(PaymentServiceResponse $paymentServiceResponse, $form, $transaction, $cartId)
    {
        echo $paymentServiceResponse->getRequestTreatment($this->context->language->iso_code);

        return $this->checkCardHolderAuthentication($form, $transaction, $cartId);
    }

    private function checkCardHolderAuthentication($form, $transaction, $cartId)
    {
        // 3.5.2 Phase 2 : envoi des informations techniques au serveur d’authentification
        // Sending technical information to the authentication server
        // La valeur du champ « step » est « technical_information_collecting »

        // début SDK
        $payment_token = $form['paymentToken'];
        $step3_payment_service = new PaymentService();
        $step3_payment_service->setPaymentToken($payment_token);
        $step3_payment_service_authentication = new Authentication();
        $step3_payment_service_authentication->setStatus("threedsmethod_requested");
        $step3_payment_service->setAuthentication($step3_payment_service_authentication);

        $step3_header = [
            'Content-Type:application/json',
            'charset=utf-8'
        ];

        $request = new MoneticoPaiementApi();
        $request->process($step3_payment_service->getUrl($transaction->isSandboxTransaction()), $step3_payment_service->getFields(), $step3_header);
        $dataResponse = $request->getResponse();
        $nextStepData = json_decode($dataResponse, true);

        $transaction->setPaymentResponseContext($dataResponse);
        if (!$transaction->save()) {
            return '';

        }

        $paymentServiceResponse = new PaymentServiceResponse($nextStepData);
        $paymentServiceResponse->setTransactionUrl($this->getTransactionUrl());

        if ($paymentServiceResponse->getReturnCode() === 2) {
            $paymentServiceResponse->setPaymentServiceUrl(
                $this->context->link->getModuleLink(MoneticoPaiement::MODULE_NAME, 'payment_service', [
                    'stepName' => 'process',
                    'paymentToken' => $paymentServiceResponse->getPaymentToken(),
                    'cartId' => $cartId
                ], true)
            );
            $data = $paymentServiceResponse->goToPaymentServiceUrl();
        } else {
            $data = $paymentServiceResponse->getRequestTreatment($this->context->language->iso_code);
        }

        return $data;
    }

    /**
     * 4.2 Le retour effectué est « cardholder_authentication »
     * Proccess Card Holder Authentication
     * @param PaymentServiceResponse $paymentServiceResponse
     * @param $transaction
     * @param $cartId
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function processCardholderAuthentication(PaymentServiceResponse $paymentServiceResponse, $transaction, $cartId)
    {
        return $paymentServiceResponse->getNextStep()->getIframe();
    }

    /**
     * This is the target of the URL built at the beginning,
     * a POST request sent, with the customer from the ACS.
     * We are sending some of the information received both
     * in the body and query string to Monetico.
     *
     * @param $form
     * @param $transaction
     *
     * @return string
     */
    public function checkPaymentServiceAuthentication($form, $transaction)
    {
        $cart_id = intval($form['cartId'] ?? $_GET['cartId'] ?? 0);
        $order_ref = strval($form['orderRef'] ?? $_GET['orderRef'] ?? '');
        $code_site_id = intval($form['codeSiteId'] ?? $_GET['codeSiteId'] ?? 0);
        $step = strval($form['stepName'] ?? $_GET['stepName'] ?? '');
        $seal = strval($form['seal'] ?? $_GET['seal'] ?? '');

        $code_site = new MoneticoPaiementCodeSite($code_site_id);
        $ept = new MoneticoPaiementEPT($code_site->getCodeSiteEptId());

        $hmacComputer = new HmacComputer();
        $valide_seal = $hmacComputer->validateSeal([
            'cartId' => $cart_id,
            'orderRef' => $order_ref,
            'codeSiteId' => $code_site_id,
            'stepName' => $step
        ], $ept->getEptMac(), $seal);


        if (!$valide_seal) {
            return;
        }

        $transaction_payment_response_context = json_decode($transaction->getPaymentResponseContext(), true);
        $payment_token = $transaction_payment_response_context['payment_token'];

        $cres = $form['cres'] ?? null;
        $threeDSSessionData = $form['threeDSSessionData'] ?? null;


        $step4_header = [
            'Content-Type:application/json',
            'charset=utf-8'
        ];

        $step4_payment_service = new PaymentService();
        $step4_payment_service_authentication = new Authentication();
        $details = new Details();

        if (isset($cres)) {
            $details->setCres($cres);
        }

        if (isset($threeDSSessionData)) {
            $details->setThreeDSSessionData($threeDSSessionData);
        }

        $step4_payment_service_authentication->setDetails($details);
        $step4_payment_service->setAuthentication($step4_payment_service_authentication);
        $request = new MoneticoPaiementApi();

        $request->process(
            $step4_payment_service->getUrl($transaction->isSandboxTransaction()),
            $step4_payment_service->getFields(),
            $step4_header
        );
        $dataResponse = $request->getResponse();

        $returnData = json_decode($dataResponse, true);

        $transaction->setPaymentResponseContext($dataResponse);
        if (!$transaction->save()) {
            return '';
        }

        if ($returnData['return_code'] === 2) {
            // go back to payment_service url for next step
            $next_step_url = $this->context->link->getModuleLink(MoneticoPaiement::MODULE_NAME, 'payment_service', [
                'stepName' => 'process',
                'paymentToken' => $returnData['payment_token'] ?? 0,
                'cartId' => $cart_id
            ], true);
            return "<script>window.location.replace('" . $next_step_url . "');</script>";
        }

        $paymentServiceResponse = new PaymentServiceResponse($returnData);
        $paymentServiceResponse->setTransactionUrl($this->getTransactionUrl());

        return $paymentServiceResponse->getRequestTreatment($this->context->language->iso_code);
    }


    public function createLog($message, $severity = 1)
    {
        $log_message = "[Monetico] " . $message;
        PrestaShopLogger::addLog($log_message, $severity);
    }
}