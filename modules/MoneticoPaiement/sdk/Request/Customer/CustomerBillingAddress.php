<?php

namespace MoneticoPaiement\Sdk\Request\Customer;

use MoneticoPaiement\Sdk\Request\Customer\CustomerAddress;

class CustomerBillingAddress extends CustomerAddress implements \JsonSerializable
{

    /**
     * @var ?string
     */
    private $mobilePhone;

    /**
     * @var ?string
     */
    private $homePhone;

    /**
     * @var ?string
     */
    private $workPhone;

    /**
     * @return string|null
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @param string|null $mobilePhone
     */
    public function setMobilePhone(string $mobilePhone)
    {
        if (mb_strlen($mobilePhone) <= 18) {
            $this->mobilePhone = $mobilePhone;
        }
    }

    /**
     * @return string|null
     */
    public function getHomePhone()
    {
        return $this->homePhone;
    }

    /**
     * @param string|null $homePhone
     */
    public function setHomePhone(string $homePhone)
    {
        if (mb_strlen($homePhone) <= 18) {
            $this->homePhone = $homePhone;
        }
    }

    /**
     * @return string|null
     */
    public function getWorkPhone()
    {
        return $this->workPhone;
    }

    /**
     * @param string|null $workPhone
     */
    public function setWorkPhone(string $workPhone)
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