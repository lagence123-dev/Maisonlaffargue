<?php

namespace MoneticoPaiement\Sdk\Api\PaymentService\Payment;

class PaymentMean
{
    /** @var string|null $wallet_id */
    private ?string $wallet_id = null;

    /** @var string|null $hpan */
    private ?string $hpan = null;

    /** @var string|null $name */
    private ?string $name = null;

    /** @var string|null $account_number */
    private ?string $account_number = null;

    /** @var string|null $expiry_date */
    private ?string $expiry_date = null;

    /** @var string|null $cardholdername */
    private ?string $cardholdername = null;

    /** @var string|null $birth_date */
    private ?string $birth_date = null;

    /** @var string|null $cvx */
    private ?string $cvx = null;

    /** @var string|null $scheme */
    private ?string $scheme = null;

    /** @var string|null $default_scheme */
    private ?string $default_scheme = null;

    /** @var string|null $payment_mean_token */
    private ?string $payment_mean_token = null;

    /**
     * @return array|mixed
     */
    public function toArray(): array
    {
        return array_filter([
            'wallet_id' => $this->getWalletId(),
            'hpan' => $this->getHpan(),
            'name' => $this->getName(),
            'account_number' => $this->getAccountNumber(),
            'expiry_date' => $this->getExpiryDate(),
            'cardholdername' => $this->getCardholdername(),
            'birth_date' => $this->getBirthDate(),
            'cvx' => $this->getCvx(),
            'scheme' => $this->getScheme(),
            'default_scheme' => $this->getDefaultScheme(),
            'payment_mean_token' => $this->getPaymentMeanToken(),
        ], function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return string|null
     */
    public function getWalletId(): ?string
    {
        return $this->wallet_id;
    }

    /**
     * @param string|null $wallet_id
     *
     * @return void
     */
    public function setWalletId(?string $wallet_id): void
    {
        $this->wallet_id = $wallet_id;
    }

    /**
     * @return string|null
     */
    public function getHpan(): ?string
    {
        return $this->hpan;
    }

    /**
     * @param string|null $hpan
     *
     * @return void
     */
    public function setHpan(?string $hpan): void
    {
        $this->hpan = $hpan;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getAccountNumber(): ?string
    {
        return $this->account_number;
    }

    /**
     * @param string|null $account_number
     *
     * @return void
     */
    public function setAccountNumber(?string $account_number): void
    {
        $this->account_number = $account_number;
    }

    /**
     * @return string|null
     */
    public function getExpiryDate(): ?string
    {
        return $this->expiry_date;
    }

    /**
     * @param string|null $expiry_date
     *
     * @return void
     */
    public function setExpiryDate(?string $expiry_date): void
    {
        $this->expiry_date = $expiry_date;
    }

    /**
     * @return string|null
     */
    public function getCardholdername(): ?string
    {
        return $this->cardholdername;
    }

    /**
     * @param string|null $cardholdername
     *
     * @return void
     */
    public function setCardholdername(?string $cardholdername): void
    {
        $this->cardholdername = $cardholdername;
    }

    /**
     * @return string|null
     */
    public function getBirthDate(): ?string
    {
        return $this->birth_date;
    }

    /**
     * @param string|null $birth_date
     *
     * @return void
     */
    public function setBirthDate(?string $birth_date): void
    {
        $this->birth_date = $birth_date;
    }

    /**
     * @return string|null
     */
    public function getCvx(): ?string
    {
        return $this->cvx;
    }

    /**
     * @param string|null $cvx
     *
     * @return void
     */
    public function setCvx(?string $cvx): void
    {
        $this->cvx = $cvx;
    }

    /**
     * @return string|null
     */
    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    /**
     * @param string|null $scheme
     *
     * @return void
     */
    public function setScheme(?string $scheme): void
    {
        $this->scheme = $scheme;
    }

    /**
     * @return string|null
     */
    public function getDefaultScheme(): ?string
    {
        return $this->default_scheme;
    }

    /**
     * @param string|null $default_scheme
     *
     * @return void
     */
    public function setDefaultScheme(?string $default_scheme): void
    {
        $this->default_scheme = $default_scheme;
    }

    /**
     * @return string|null
     */
    public function getPaymentMeanToken(): ?string
    {
        return $this->payment_mean_token;
    }

    /**
     * @param string|null $payment_mean_token
     *
     * @return void
     */
    public function setPaymentMeanToken(?string $payment_mean_token): void
    {
        $this->payment_mean_token = $payment_mean_token;
    }
}