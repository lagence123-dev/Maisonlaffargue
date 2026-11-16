<?php

namespace MoneticoPaiement\Sdk\Request\Customer;

class CustomerAddress
{
    /**
     * @var ?string
     */
    private $civility;

    /**
     * @var ?string
     */
    private $name;

    /**
     * @var ?string
     */
    private $firstName;

    /**
     * @var ?string
     */
    private $lastName;

    /**
     * @var ?string
     */
    private $middleName;

    /**
     * @var ?string
     */
    private $address;

    /**
     * @var string
     */
    private $addressLine1;

    /**
     * @var ?string
     */
    private $addressLine2;

    /**
     * @var ?string
     */
    private $addressLine3;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $country;

    /**
     * @var ?string
     */
    private $stateOrProvince;

    /**
     * @var ?string
     */
    private $countrySubdivision;

    /**
     * @var ?string
     */
    private $email;

    /**
     * @var ?string
     */
    private $phone;

    /**
     * @return string|null
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * @param string|null $civility
     */
    public function setCivility(string $civility)
    {
        $this->civility = mb_substr($civility, 0, 32);
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(string $name)
    {
        $this->name = mb_substr($name, 0, 45);
    }

    /**
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = mb_substr($firstName, 0, 45);
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = mb_substr($lastName, 0, 45);
    }

    /**
     * @return string|null
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string|null $middleName
     */
    public function setMiddleName(string $middleName)
    {
        $this->middleName = mb_substr($middleName, 0, 150);
    }

    /**
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(string $address)
    {
        $this->address = mb_substr($address, 0, 255);
    }

    /**
     * @return string
     */
    public function getAddressLine1()
    {
        return $this->addressLine1;
    }

    /**
     * @param string $addressLine1
     */
    public function setAddressLine1(string $addressLine1)
    {
        $this->addressLine1 = mb_substr($addressLine1, 0, 50);
    }

    /**
     * @return string|null
     */
    public function getAddressLine2()
    {
        return $this->addressLine2;
    }

    /**
     * @param string|null $addressLine2
     */
    public function setAddressLine2(string $addressLine2)
    {
        $this->addressLine2 = mb_substr($addressLine2, 0, 50);
    }

    /**
     * @return string|null
     */
    public function getAddressLine3()
    {
        return $this->addressLine3;
    }

    /**
     * @param string|null $addressLine3
     */
    public function setAddressLine3(string $addressLine3)
    {
        $this->addressLine3 = mb_substr($addressLine3, 0, 50);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = mb_substr($city, 0, 50);
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode(string $postalCode)
    {
        $this->postalCode = mb_substr($postalCode, 0, 50);
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country)
    {
        if (mb_strlen($country) === 2) {
            $this->country = strtoupper($country);
        } else {
            $this->country = 'FR';
        }
    }

    /**
     * @return string|null
     */
    public function getStateOrProvince()
    {
        if (isset($this->stateOrProvince) && mb_strlen($this->stateOrProvince) === 2 && in_array($this->getCountry(), ["US", "CA"])) {
            return $this->getCountry() . "-" . $this->stateOrProvince;
        }
    }

    /**
     * @param string|null $stateOrProvince
     */
    public function setStateOrProvince(string $stateOrProvince)
    {
        if (mb_strlen($stateOrProvince) === 2) {
            $this->stateOrProvince = $stateOrProvince;
        }
    }

    /**
     * @return string|null
     */
    public function getCountrySubdivision()
    {
        return $this->countrySubdivision;
    }

    /**
     * @param string|null $countrySubdivision
     */
    public function setCountrySubdivision(string $countrySubdivision)
    {
        if (mb_strlen($countrySubdivision) > 0) {
            $this->countrySubdivision = $this->getCountry() . '-' . $countrySubdivision;
        }
    }

    /**
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(string $email)
    {
        if (mb_strlen($email) <= 254 && preg_match('/^.+@.+\..+$/', $email)) {
            $this->email = $email;
        }
    }

    /**
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     *  Like +33-61234567
     */
    public function setPhone(string $phone)
    {
        if (mb_strlen($phone) <= 18) {
            $this->phone = $phone;
        }
    }

}