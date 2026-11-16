<?php

namespace MoneticoPaiement\Sdk\Api\HostedFields\PaymentMeanTokenGenerator;

class Context implements \JsonSerializable
{
    /** @var string */
    private string $useCase;

    /** @var ?string */
    private ?string $walletId = null;

    /**
     * @return string
     */
    public function getUseCase(): string
    {
        return $this->useCase;
    }

    /**
     * @param string $useCase
     *
     * @return void
     */
    public function setUseCase(string $useCase): void
    {
        $this->useCase = $useCase;
    }

    /**
     * @return ?string
     */
    public function getWalletId(): ?string
    {
        return $this->walletId;
    }

    /**
     * @param string|null $walletId
     *
     * @return void
     */
    public function setWalletId(?string $walletId): void
    {
        $this->walletId = $walletId;
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return array_filter([
            'use_case' => $this->getUseCase(),
            'wallet_id' => $this->getWalletId(),
        ], function ($value) {
            return !is_null($value);
        });
    }
}