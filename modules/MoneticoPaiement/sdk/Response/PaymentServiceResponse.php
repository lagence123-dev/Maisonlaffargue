<?php

namespace MoneticoPaiement\Sdk\Response\PaymentService;

use MoneticoPaiement\Sdk\Response\PaymentService\NextStep;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentServiceResponseValue;

class PaymentServiceResponse
{

    // Urls
    public string $paymentServiceUrl;
    public string $transactionUrl;

    // Data
    private ?NextStep $next_step = null;
    private string $payment_token;
    private string $request_token;
    private int $returnCode;

    public function __construct(array $response)
    {
        $this->setReturnCode($response['return_code']);
        $this->setPaymentToken($response['payment_token']);
        $this->setRequestToken($response['request_token']);

        if (isset($response['next_step'])) {
            $this->setNextStep(new NextStep($response['next_step']));
        }
    }

    /**
     * @return NextStep|null
     */
    public function getNextStep(): ?NextStep
    {
        return $this->next_step;
    }

    /**
     * @param NextStep $next_step
     */
    public function setNextStep(NextStep $next_step): void
    {
        $this->next_step = $next_step;
    }

    public function getPaymentToken(): string
    {
        return $this->payment_token;
    }

    public function setPaymentToken(string $payment_token): void
    {
        $this->payment_token = $payment_token;
    }

    public function getRequestToken(): string
    {
        return $this->request_token;
    }

    public function setRequestToken(string $request_token): void
    {
        $this->request_token = $request_token;
    }

    public function getReturnCode(): int
    {
        return $this->returnCode;
    }

    public function setReturnCode(int $returnCode): void
    {
        $this->returnCode = $returnCode;
    }

    /**
     * @return string
     */
    public function getPaymentServiceUrl()
    {
        return $this->paymentServiceUrl;
    }

    /**
     * @param string $paymentServiceUrl
     * @return void
     */
    public function setPaymentServiceUrl(string $paymentServiceUrl): void
    {
        $this->paymentServiceUrl = $paymentServiceUrl;
    }

    /**
     * @return string
     */
    public function getTransactionUrl()
    {
        return $this->transactionUrl;
    }

    /**
     * @param string $transactionUrl
     * @return void
     */
    public function setTransactionUrl(string $transactionUrl): void
    {
        $this->transactionUrl = $transactionUrl;
    }

    /**
     * @return string Go to next Step (Payment Service)
     */
    public function goToPaymentServiceUrl()
    {
        $next_step_url = $this->getPaymentServiceUrl();

        // The setTimeout here seems sometimes necessary to let the request done at the same time
        // in the iframe (especially the one from the technical_information_collecting step) be over.
        // If it is a little slow and the redirect here is done too quickly, the previous request gets cancelled.
        return "<script>setTimeout(() => window.location.replace('" . $next_step_url . "'), 1_000);</script>";
    }

    /**
     * @param $language_iso_code
     * @return string
     */
    public function getRequestTreatment($language_iso_code): string
    {
        $data = '';
        if ($this->getReturnCode() === 2) {
            $data = $this->getNextStep()->getIframe();
        } else {
            $data .= "<div class='page-content page-cms'>";

       //     $data .= "<link rel='stylesheet' href='" . _THEME_CSS_DIR_ . "theme.css' type='css' media='all'>";
            if ($this->getReturnCode() === 1) {
                // Successful payment
                $data .= ("<h2 class='text-uppercase'>" . PaymentServiceResponseValue::SUCCESS[$language_iso_code]
                        ?? PaymentServiceResponseValue::SUCCESS[PaymentServiceResponseValue::DEFAULT_LANGUAGE]) . "</h2>";
                // close window & process
            } else {
                // return code < 0 => error
                $data .= ("<h2 class='text-uppercase'>" . PaymentServiceResponseValue::FAIL[$language_iso_code]
                        ?? PaymentServiceResponseValue::FAIL[PaymentServiceResponseValue::DEFAULT_LANGUAGE]) . "</h2>";
            }
            $data .= "<p>" . (PaymentServiceResponseValue::REDIRECT[$language_iso_code]
                    ?? PaymentServiceResponseValue::REDIRECT[PaymentServiceResponseValue::DEFAULT_LANGUAGE]) . "</p>";
            $data .= "<button id='redirectBtn' class='btn btn-primary'>" . (PaymentServiceResponseValue::REDIRECT_BTN[$language_iso_code]
                    ?? PaymentServiceResponseValue::REDIRECT[PaymentServiceResponseValue::DEFAULT_LANGUAGE]) . "</button>";
            $data .= "</div>";
            // close window & process

            $data .= "<script>

                function redirect(){
                    window.top.location.replace('" . $this->getTransactionUrl() . "');
                }
                
                setTimeout(function(){ 
                    redirect()
                }, 3000);  
                
                document.getElementById('redirectBtn').addEventListener('click',function(){
                   redirect()
                })
                
            </script>";

        }

        return $data;
    }


}