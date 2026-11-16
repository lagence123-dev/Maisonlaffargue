<?php

namespace MoneticoPaiement\Sdk\Api\PaymentSession\Kyc;


class Kyc
{
    private string $type;
    private string $email;

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
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function toArray(): array
    {
        return array_filter([
            'type' =>$this->getType(),
            'email' => $this->getEmail(),
        ], function ($value) {
            return !is_null($value);
        });
    }
}