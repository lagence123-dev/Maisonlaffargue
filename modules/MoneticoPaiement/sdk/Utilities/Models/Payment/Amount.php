<?php

namespace MoneticoPaiement\Sdk\Utilities\Models\Payment;

class Amount
{
    /** @var int $value */
    private int $value;

    /** @var string $currency */
    private string $currency;

    // Most used value, override with setter if needed
    /** @var int $exponent */
    private int $exponent=2;

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
                'value' => $this->getValue(),
                'currency' => $this->getCurrency(),
                'exponent' => $this->getExponent()
            ]
            , function ($value) {
                return !is_null($value);
            });
    }

    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @return void
     */
    public function setValue(int $value): void
    {
        $this->value = round($value);
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return void
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getExponent(): int
    {
        return $this->exponent;
    }

    /**
     * @param int $exponent
     *
     * @return void
     */
    public function setExponent(int $exponent): void
    {
        $this->exponent = $exponent;
    }

}