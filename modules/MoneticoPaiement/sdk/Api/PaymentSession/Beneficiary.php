<?php

namespace MoneticoPaiement\Sdk\Api\PaymentSession;

use MoneticoPaiement\Sdk\Api\PaymentSession\Kyc\Kyc;

class Beneficiary
{
    private string $iban;
    private Kyc $kyc;

    /**
     * @param string $iban
     */
    public function setIban(string $iban): void
    {
        $this->iban = $iban;
    }

    /**
     * @return string
     */
    public function getIban(): string
    {
        return $this->iban;
    }

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
            'iban' => $this->getIban(),
        ], function ($value) {
            return !is_null($value);
        });
    }
}