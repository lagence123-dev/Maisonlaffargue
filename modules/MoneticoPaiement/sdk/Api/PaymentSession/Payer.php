<?php

namespace MoneticoPaiement\Sdk\Api\PaymentSession;

use MoneticoPaiement\Sdk\Api\PaymentSession\Kyc\Kyc;

class Payer
{
    private Kyc $kyc;

    /**
     * @param Kyc $kyc
     */
    public function setKyc(Kyc $kyc): void
    {
        $this->kyc = $kyc;
    }

    /**
     * @return Kyc
     */
    public function getKyc(): Kyc
    {
        return $this->kyc;
    }

    public function toArray(): array
    {
        return array_filter([
            'kyc' =>$this->getKyc()->toArray(),
        ], function ($value) {
            return !is_null($value);
        });
    }
}