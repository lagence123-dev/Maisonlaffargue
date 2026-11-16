<?php

namespace MoneticoPaiement\Sdk\Api\PaymentSession\Kyc;


class Professional extends Kyc
{
    private string $firm_name;
    private string $legal_form;
    private string $registration_number;
    private string $head_office;

    /**
     * @return string
     */
    public function getFirmName(): string
    {
        return $this->firm_name;
    }

    /**
     * @param string $firm_name
     */
    public function setFirmName(string $firm_name): void
    {
        $this->firm_name = $firm_name;
    }

    /**
     * @return string
     */
    public function getLegalForm(): string
    {
        return $this->legal_form;
    }

    /**
     * @param string $legal_form
     */
    public function setLegalForm(string $legal_form): void
    {
        $this->legal_form = $legal_form;
    }

    /**
     * @return string
     */
    public function getRegistrationNumber(): string
    {
        return $this->registration_number;
    }

    /**
     * @param string $registration_number
     */
    public function setRegistrationNumber(string $registration_number): void
    {
        $this->registration_number = $registration_number;
    }

    /**
     * @return string
     */
    public function getHeadOffice(): string
    {
        return $this->head_office;
    }

    /**
     * @param string $head_office
     */
    public function setHeadOffice(string $head_office): void
    {
        $this->head_office = $head_office;
    }

    public function toArray(): array
    {
        return array_filter([
            'type' =>$this->getType(),
            'email' => $this->getEmail(),
            'firm_name' =>$this->getFirmName(),
            'legal_form' =>$this->getLegalForm(),
            'registration_number' =>$this->getRegistrationNumber(),
            'head_office' =>$this->getHeadOffice(),
        ], function ($value) {
            return !is_null($value);
        });
    }
}