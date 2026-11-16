<?php

namespace MoneticoPaiement\Sdk\Api\PaymentService\Payment;

use MoneticoPaiement\Sdk\Api\PaymentService\Payment\PaymentOptions\MerchantIdentification;

class PaymentOptions
{
    /** @var ?MerchantIdentification $merchant_identification */
    private ?MerchantIdentification $merchant_identification = null;

    /** @var ?string $special_payment_mode */
    private ?string $special_payment_mode = null;

    /** @return array */
    public function toArray(): array
    {
        return array_filter([
            'merchant_identification' => $this->getMerchantIdentification(),
            'special_payment_mode' => $this->getSpecialPaymentMode(),
        ], function ($value) {
            return !is_null($value);
        });
    }

    /** @return ?MerchantIdentification */
    public function getMerchantIdentification(): ?MerchantIdentification
    {
        return $this->merchant_identification;
    }

    /**
     * @param ?MerchantIdentification $merchant_identification
     *
     * @return void
     */
    public function setMerchantIdentification(?MerchantIdentification $merchant_identification): void
    {
        $this->merchant_identification = $merchant_identification;
    }

    /** @return ?string */
    public function getSpecialPaymentMode(): ?string
    {
        return $this->special_payment_mode;
    }

    /**
     * @param string|null $special_payment_mode
     *
     * @return void
     */
    public function setSpecialPaymentMode(?string $special_payment_mode): void
    {
        $this->special_payment_mode = $special_payment_mode;
    }


}