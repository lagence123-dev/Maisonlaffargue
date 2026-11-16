<?php

namespace MoneticoPaiement\Sdk\Api\PaymentSession;

use MoneticoPaiement\Sdk\Api\MoneticoPaiementApi;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentEnvironment;
use MoneticoPaiement\Sdk\Utilities\Interfaces\MoneticoRequestInterface;

class PaymentSessionInitiation implements MoneticoRequestInterface
{
    /** @var MerchantConfiguration $merchant_configuration */
    private MerchantConfiguration $merchant_configuration;

    /** @var Order $order */
    private Order $order;

    /** @var PaymentMean[]|null */
    private ?array $payment_means = null;

    /**
     * @return MerchantConfiguration
     */
    public function getMerchantConfiguration(): MerchantConfiguration
    {
        return $this->merchant_configuration;
    }

    /**
     * @param MerchantConfiguration $merchant_configuration
     */
    public function setMerchantConfiguration(MerchantConfiguration $merchant_configuration): void
    {
        $this->merchant_configuration = $merchant_configuration;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return array|null
     */
    public function getPaymentMeans(): ?array
    {
        return $this->payment_means;
    }

    /**
     * @param array|null $payment_means
     */
    public function setPaymentMeans(?array $payment_means): void
    {
        $this->payment_means = $payment_means;
    }

    /**
     * @param bool $isSandbox
     * @return string
     */
    public function getUrl(bool $isSandbox = false): string
    {
        $baseUrl = $isSandbox ? PaymentEnvironment::API_PAYMENT_V2_SANDBOX_URI
            : PaymentEnvironment::API_PAYMENT_V2_PROD_URI;
        return $baseUrl . PaymentEnvironment::PAYMENT_SESSION_API;
    }

    public function getFields(): array
    {
        $payment_means = $this->getPaymentMeans();
        $pm = [];

        if (isset($payment_means)) {
            foreach ($payment_means as $payment_mean) {
                $pm[] = $payment_mean->toArray();
            }
        }

        return array_filter([
            'merchant_configuration' => $this->getMerchantConfiguration()->toArray(),
            'order' => $this->getOrder()->toArray(),
            'payment_means' => !empty($pm) ? $pm : null,
        ], function ($value) {
            return !is_null($value);
        });
    }
	
	/**
     * Send a request to Monetico API to check if the web component can de displayed or if a new payment session must be initialized
     * @param $session_id
     * @param $isSandbox
     * @return bool
     */
    public static function canRestart($session_id, $isSandbox): bool
    {
        $response = false;
        $request = new MoneticoPaiementApi();
        $header = [
            'Content-Type:application/json',
            'payment-session-id:' . $session_id
        ];

        $request_url = $isSandbox ? PaymentEnvironment::API_PAYMENT_V2_SANDBOX_URI
            : PaymentEnvironment::API_PAYMENT_V2_PROD_URI;

        $request->process($request_url . PaymentEnvironment::PAYMENT_SESSION_API . '/' . $session_id,
            ["action" => "restart"], $header, '', 'PATCH');

        $payment_session_response = json_decode($request->getResponse(), true);

        if (is_array($payment_session_response) && !isset($payment_session_response['error'])) {
            $response = true;
        }

        return $response;
    }
}