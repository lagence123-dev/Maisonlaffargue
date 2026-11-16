<?php

namespace MoneticoPaiement\Sdk\Api\PaymentService\Payment;

class InstalmentPayment
{
    /** @var array|null $instalments */
    private ?array $instalments = null;

    public function toArray(): array
    {
        return array_filter([
                'instalments' => $this->getInstalments(),
            ]
            , function ($value) {
                return !is_null($value);
            });
    }

    /**
     * @param \DateTime $date
     * @param int       $amount
     *
     * @return void
     */
    public function addInstalment(\DateTime $date, int $amount): void
    {
        $this->instalments[] = [
            'amount' => [
                'value' => $amount
            ],
            'date' => $date->format('Y-m-d'),
        ];
    }

    /**
     * @param string $date
     * @param int       $amount
     *
     * @return void
     */
    public function addInstalmentFromString(string $date, int $amount): void
    {
        $this->instalments[] = [
            'amount' => [
                'value' => $amount
            ],
            'date' => $date,
        ];
    }

    /**
     * @return ?array
     */
    public function getInstalments(): ?array
    {
        return $this->instalments;
    }

    /**
     * @param array|null $instalments
     *
     * @return $this
     */
    public function setInstalments(?array $instalments): InstalmentPayment
    {
        $this->instalments = $instalments;
        return $this;
    }


}