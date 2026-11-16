<?php

namespace MoneticoPaiement\Sdk\Api\PaymentSession;

class PaymentMean
{
    private string $type;
    private ?Payer $payer = null;
    private ?Beneficiary $beneficiary = null;
    private ?string $transfer_type = null;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return Beneficiary|null
     */
    public function getBeneficiary(): ?Beneficiary
    {
        return $this->beneficiary;
    }

    /**
     * @param Beneficiary $beneficiary
     */
    public function setBeneficiary(Beneficiary $beneficiary): void
    {
        $this->beneficiary = $beneficiary;
    }

    /**
     * @return Payer|null
     */
    public function getPayer(): ?Payer
    {
        return $this->payer;
    }

    /**
     * @param Payer $payer
     */
    public function setPayer(Payer $payer): void
    {
        $this->payer = $payer;
    }

    /**
     * @return string|null
     */
    public function getTransferType(): ?string
    {
        return $this->transfer_type;
    }

    /**
     * @param string $transfer_type
     */
    public function setTransferType(string $transfer_type): void
    {
        $this->transfer_type = $transfer_type;
    }

    public function toArray(): array
    {
        return array_filter([
            'type' =>$this->getType(),
            'payer'=> $this->getPayer() !== null ? $this->getPayer()->toArray() : null,
            'beneficiary' => $this->getBeneficiary() !== null ? $this->getBeneficiary()->toArray() : null,
            'transfer_type' => $this->getTransferType(),
        ], function ($value) {
            return !is_null($value);
        });
    }
}