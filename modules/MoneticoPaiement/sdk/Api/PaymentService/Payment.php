<?php

namespace MoneticoPaiement\Sdk\Api\PaymentService;

use MoneticoPaiement\Sdk\Utilities\Models\Payment\Amount;
use MoneticoPaiement\Sdk\Api\PaymentService\Payment\{
    PaymentMean,
    PaymentOptions,
    InstalmentPayment,
    PreauthorisationPayment
};


class Payment
{
    /** @var string $transaction_initiator */
    private string $transaction_initiator;

    /** @var string $reference */
    private string $reference;

    /** @var ?string $comment */
    private ?string $comment = null;

    /** @var PaymentMean $payment_mean */
    private PaymentMean $payment_mean;

    /** @var Amount $amount */
    private Amount $amount;

    /** @var ?PaymentOptions $payment_options */
    private ?PaymentOptions $payment_options = null;

    /** @var ?InstalmentPayment $instalment_payment */
    private ?InstalmentPayment $instalment_payment = null;

    /** @var ?PreauthorisationPayment $preauthorisation_payment */
    private ?PreauthorisationPayment $preauthorisation_payment = null;

    /**
     * @return array
     */
    public function toArray():array
    {
        return array_filter([
            'transaction_initiator' => $this->getTransactionInitiator(),
            'reference' => $this->getReference(),
            'comment' => $this->getComment(),
            'payment_mean' => $this->getPaymentMean()->toArray(),
            'amount' => $this->getAmount()->toArray(),
            'payment_options' => $this->getPaymentOptions() ? $this->getPaymentOptions()->toArray() : null,
            'instalment_payment' => $this->getInstalmentPayment() ? $this->getInstalmentPayment()->toArray() : null,
            'preauthorisation_payment' => $this->getPreauthorisationPayment() ? $this->getPreauthorisationPayment()->toArray() : null
        ], function ($value) {
            return !is_null($value);
        });
    }
    /**
     * @return string
     */
    public function getTransactionInitiator(): string
    {
        return $this->transaction_initiator;
    }

    /**
     * @param string $transaction_initiator
     *
     * @return void
     */
    public function setTransactionInitiator(string $transaction_initiator): void
    {
        $this->transaction_initiator = $transaction_initiator;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return void
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @return ?string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @param string|null $comment
     *
     * @return void
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return PaymentMean
     */
    public function getPaymentMean(): PaymentMean
    {
        return $this->payment_mean;
    }

    /**
     * @param PaymentMean $payment_mean
     *
     * @return void
     */
    public function setPaymentMean(PaymentMean $payment_mean): void
    {
        $this->payment_mean = $payment_mean;
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
     *
     * @return void
     */
    public function setAmount(Amount $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return ?PaymentOptions
     */
    public function getPaymentOptions(): ?PaymentOptions
    {
        return $this->payment_options;
    }

    /**
     * @param PaymentOptions|null $payment_options
     *
     * @return void
     */
    public function setPaymentOptions(?PaymentOptions $payment_options): void
    {
        $this->payment_options = $payment_options;
    }

    /**
     * @return ?InstalmentPayment
     */
    public function getInstalmentPayment(): ?InstalmentPayment
    {
        return $this->instalment_payment;
    }

    /**
     * @param \MoneticoPaiement\Sdk\Api\PaymentService\Payment\InstalmentPayment|null $instalment_payment
     *
     * @return void
     */
    public function setInstalmentPayment(?InstalmentPayment $instalment_payment): void
    {
        $this->instalment_payment = $instalment_payment;
    }

    /**
     * @return ?PreauthorisationPayment
     */
    public function getPreauthorisationPayment(): ?PreauthorisationPayment
    {
        return $this->preauthorisation_payment;
    }

    /**
     * @param \MoneticoPaiement\Sdk\Api\PaymentService\Payment\PreauthorisationPayment|null $preauthorisation_payment
     *
     * @return void
     */
    public function setPreauthorisationPayment(?PreauthorisationPayment $preauthorisation_payment): void
    {
        $this->preauthorisation_payment = $preauthorisation_payment;
    }
}