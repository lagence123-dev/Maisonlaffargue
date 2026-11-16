<?php

namespace MoneticoPaiement\Sdk\Api\PaymentSession\Kyc;


class Individual extends Kyc
{
    private string $first_name;
    private string $last_name;
    private string $birth_date;
    private string $birth_city;

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName(string $first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     */
    public function setLastName(string $last_name): void
    {
        $this->last_name = $last_name;
    }

    /**
     * @return string
     */
    public function getBirthDate(): string
    {
        return $this->birth_date;
    }

    /**
     * @param string $birth_date
     */
    public function setBirthDate(string $birth_date): void
    {
        $this->birth_date = $birth_date;
    }

    /**
     * @return string
     */
    public function getBirthCity(): string
    {
        return $this->birth_city;
    }

    /**
     * @param string $birth_city
     */
    public function setBirthCity(string $birth_city): void
    {
        $this->birth_city = $birth_city;
    }

    public function toArray(): array
    {
        return array_filter([
            'type' =>$this->getType(),
            'email' => $this->getEmail(),
            'first_name' =>$this->getFirstName(),
            'last_name' =>$this->getLastName(),
            'birth_date' =>$this->getBirthDate(),
            'birth_city' =>$this->getBirthCity(),
        ], function ($value) {
            return !is_null($value);
        });
    }
}