<?php

namespace MoneticoPaiement\Sdk\Api\PaymentSession;

use MoneticoPaiement\Sdk\Utilities\Models\OrderModel;
use MoneticoPaiement\Sdk\Utilities\Models\Payment\Amount;

class Order extends OrderModel
{
    private ?string $comment = null;
    private string $reference = '';
    private Amount $amount;

    private ?string $environment = null;

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string|null $reference
     */
    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return Amount
     */
    public function getAmount(): Amount
    {
        return $this->amount;
    }

    /**
     * @param Amount $amount
     */
    public function setAmount(Amount $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string|null
     */
    public function getEnvironment(): ?string
    {
        return $this->environment;
    }

    /**
     * @param string|null $environment
     */
    public function setEnvironment(?string $environment): void
    {
        $this->environment = $environment;
    }

    public function toArray(): array
    {
        return array_filter([
            'customer' => $this->getCustomer() ? $this->getCustomer()->toArray() : null,
            'date' => $this->getDate()->format('Y-m-d\TH:i:s'),
            'comment' => $this->getComment(),
            'reference' => $this->getReference(),
            'amount' => $this->getAmount()->toArray(),
            'environment' => $this->getEnvironment()
        ], function ($value) {
            return !is_null($value);
        });
    }
}