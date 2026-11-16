<?php

namespace MoneticoPaiement\Sdk\Utilities\Models\Order;

class Customer
{
    /** @var string|null $mail */
    private ?string $mail = null;

    /** @var string|null $ip_address */
    private ?string $ip_address = null;

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'mail' => $this->getMail(),
            'ip_address'=>$this->getIpAddress(),
        ], function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return string|null
     */
    public function getMail(): ?string
    {
        return $this->mail;
    }

    /**
     * @param string|null $mail
     *
     * @return void
     */
    public function setMail(?string $mail): void
    {
        $this->mail = $mail;
    }

    /**
     * @return string|null
     */
    public function getIpAddress(): ?string
    {
        return $this->ip_address;
    }

    /**
     * @param string|null $ip_address
     *
     * @return void
     */
    public function setIpAddress(?string $ip_address): void
    {
        $this->ip_address = $ip_address;
    }

}