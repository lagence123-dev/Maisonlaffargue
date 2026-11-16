<?php

namespace MoneticoPaiement\Sdk\Utilities\Models\OrderContext;

class Billing extends Address implements \JsonSerializable
{

    /**
     * @var ?string
     */
    private ?string $mobilePhone = null;

    /**
     * @var ?string
     */
    private ?string $homePhone = null;

    /**
     * @var ?string
     */
    private ?string $workPhone = null;

    /**
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    /**
     * @param ?string $mobilePhone
     */
    public function setMobilePhone(?string $mobilePhone): void
    {
        if (mb_strlen($mobilePhone) <= 18) {
            $this->mobilePhone = $mobilePhone;
        }
    }

    /**
     * @return string|null
     */
    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    /**
     * @param ?string $homePhone
     */
    public function setHomePhone(?string $homePhone): void
    {
        if (mb_strlen($homePhone) <= 18) {
            $this->homePhone = $homePhone;
        }
    }

    /**
     * @return string|null
     */
    public function getWorkPhone(): ?string
    {
        return $this->workPhone;
    }

    /**
     * @param ?string $workPhone
     */
    public function setWorkPhone(?string $workPhone): void
    {
        if (mb_strlen($workPhone) <= 18) {
            $this->workPhone = $workPhone;
        }
    }

    /**
     * @return array|mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
            "civility" => $this->getCivility(),
            "name" => $this->getName(),
            "firstName" => $this->getFirstName(),
            "lastName" => $this->getLastName(),
            "middleName" => $this->getMiddleName(),
            "address" => $this->getAddress(),
            "addressLine1" => $this->getAddressLine1(),
            "addressLine2" => $this->getAddressLine2(),
            "addressLine3" => $this->getAddressLine3(),
            "city" => $this->getCity(),
            "postalCode" => $this->getPostalCode(),
            "country" => $this->getCountry(),
            "stateOrProvince" => $this->getStateOrProvince(),
            "countrySubdivision" => $this->getCountrySubdivision(),
            "email" => $this->getEmail(),
            "phone" => $this->getPhone(),
            "mobilePhone" => $this->getMobilePhone(),
            "homePhone" => $this->getHomePhone(),
            "workPhone" => $this->getWorkPhone()
        ], function ($value) {
            return !is_null($value);
        });
    }
}