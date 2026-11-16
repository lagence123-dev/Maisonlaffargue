<?php

namespace MoneticoPaiement\Sdk\Response\PaymentSession;

use MoneticoPaiement\Sdk\Api\PaymentSession\PaymentMean;
use MoneticoPaiement\Sdk\Utilities\Models\Payment\Amount;

class Data
{
    private Amount $amount;
    private PaymentMean $payment_mean;

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
     * @return PaymentMean
     */
    public function getPaymentMean(): PaymentMean
    {
        return $this->payment_mean;
    }

    /**
     * @param PaymentMean $payment_mean
     */
    public function setPaymentMean(PaymentMean $payment_mean): void
    {
        $this->payment_mean = $payment_mean;
    }
}