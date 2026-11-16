<?php

namespace MoneticoPaiement\Sdk\Api\PaymentService;

use MoneticoPaiement\Sdk\Utilities\Collections\PaymentEnvironment;
use MoneticoPaiement\Sdk\Utilities\Interfaces\MoneticoRequestInterface;

class PaymentService implements MoneticoRequestInterface
{
    /** @var ?string $payment_token */
    private ?string $payment_token = null;

     /** @var ?MerchantConfiguration $merchant_configuration */
    private ?MerchantConfiguration $merchant_configuration = null;

     /** @var ?Order $order */
    private ?Order $order = null;

     /** @var ?Payment $payment */
    private ?Payment $payment = null;

     /** @var ?Authentication $authentication */
    private ?Authentication $authentication = null;

    /**
     * @return array
     */
    public function getFields():array
    {
        return array_filter([
            'payment_token' => $this->getPaymentToken(),
            'merchant_configuration' => $this->getMerchantConfiguration() ? $this->getMerchantConfiguration()->toArray() : null,
            'order' => $this->getOrder() ? $this->getOrder()->toArray() : null,
            'payment' => $this->getPayment() ? $this->getPayment()->toArray() : null,
            'authentication' => $this->getAuthentication() ? $this->getAuthentication()->toArray() : null,
        ], function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return ?string
     */
    public function getPaymentToken(): ?string
    {
        return $this->payment_token;
    }

    /**
     * @param string|null $payment_token
     *
     * @return void
     */
    public function setPaymentToken(?string $payment_token): void
    {
        $this->payment_token = $payment_token;
    }

    /**
     * @return \MoneticoPaiement\Sdk\Api\PaymentService\MerchantConfiguration|null
     */
    public function getMerchantConfiguration(): ?MerchantConfiguration
    {
        return $this->merchant_configuration;
    }

    /**
     * @param \MoneticoPaiement\Sdk\Api\PaymentService\MerchantConfiguration|null $merchant_configuration
     *
     * @return void
     */
    public function setMerchantConfiguration(?MerchantConfiguration $merchant_configuration): void
    {
        $this->merchant_configuration = $merchant_configuration;
    }

    /**
     * @return \MoneticoPaiement\Sdk\Api\PaymentService\Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param \MoneticoPaiement\Sdk\Api\PaymentService\Order|null $order
     *
     * @return void
     */
    public function setOrder(?Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return \MoneticoPaiement\Sdk\Api\PaymentService\Payment|null
     */
    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    /**
     * @param \MoneticoPaiement\Sdk\Api\PaymentService\Payment|null $payment
     *
     * @return void
     */
    public function setPayment(?Payment $payment): void
    {
        $this->payment = $payment;
    }

    /**
     * @return \MoneticoPaiement\Sdk\Api\PaymentService\Authentication|null
     */
    public function getAuthentication(): ?Authentication
    {
        return $this->authentication;
    }

    /**
     * @param \MoneticoPaiement\Sdk\Api\PaymentService\Authentication|null $authentication
     *
     * @return void
     */
    public function setAuthentication(?Authentication $authentication): void
    {
        $this->authentication = $authentication;
    }

    /**
     * @param bool $isSandbox
     *
     * @return string
     */
    public function getUrl(bool $isSandbox=false): string
    {
        $baseUrl = $isSandbox ? PaymentEnvironment::API_PAYMENT_V2_SANDBOX_URI
            : PaymentEnvironment::API_PAYMENT_V2_PROD_URI;
        return $baseUrl . PaymentEnvironment::PAYMENT_SERVICE_CGI;
    }
}