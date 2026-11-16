<?php

namespace MoneticoPaiement\Sdk\Utilities\Models\OrderContext;

class Address
{
    /**
     * @var ?string
     */
    private ?string $civility = null;

    /**
     * @var ?string
     */
    private ?string $name = null;

    /**
     * @var ?string
     */
    private ?string $firstName = null;

    /**
     * @var ?string
     */
    private ?string $lastName = null;

    /**
     * @var ?string
     */
    private ?string $middleName = null;

    /**
     * @var ?string
     */
    private ?string $address = null;

    /**
     * @var ?string
     */
    private ?string $addressLine1 = null;

    /**
     * @var ?string
     */
    private ?string $addressLine2 = null;

    /**
     * @var ?string
     */
    private ?string $addressLine3 = null;

    /**
     * @var ?string
     */
    private ?string $city = null;

    /**
     * @var ?string
     */
    private ?string $postalCode = null;

    /**
     * @var ?string
     */
    private ?string $country = null;

    /**
     * @var ?string
     */
    private ?string $stateOrProvince = null;

    /**
     * @var ?string
     */
    private ?string $countrySubdivision = null;

    /**
     * @var ?string
     */
    private ?string $email = null;

    /**
     * @var ?string
     */
    private ?string $phone = null;

    /**
     * @return string|null
     */
    public function getCivility(): ?string
    {
        return $this->civility;
    }

    /**
     * @param ?string $civility
     */
    public function setCivility(?string $civility): void
    {
        $this->civility = mb_substr($civility, 0, 32);
    }

    /**
     * @return string|nulll
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param ?string $name
     */
    public function setName(?string $name): void
    {
        $this->name = mb_substr($name, 0, 45);
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param ?string $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = mb_substr($firstName, 0, 45);
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param ?string $lastName
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    /**
     * @param ?string $middleName
     */
    public function setMiddleName(?string $middleName): void
    {
        $this->middleName = mb_substr($middleName, 0, 150);
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param ?string $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = mb_substr($address, 0, 255);
    }

    /**
     * @return string|null
     */
    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    /**
     * @param ?string $addressLine1
     */
    public function setAddressLine1(?string $addressLine1): void
    {
        $this->addressLine1 = mb_substr($addressLine1, 0, 50);
    }

    /**
     * @return string|null
     */
    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    /**
     * @param ?string $addressLine2
     */
    public function setAddressLine2(?string $addressLine2): void
    {
        $this->addressLine2 = mb_substr($addressLine2, 0, 50);
    }

    /**
     * @return string|null
     */
    public function getAddressLine3(): ?string
    {
        return $this->addressLine3;
    }

    /**
     * @param ?string $addressLine3
     */
    public function setAddressLine3(?string $addressLine3): void
    {
        $this->addressLine3 = mb_substr($addressLine3, 0, 50);
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param ?string $city
     */
    public function setCity(?string $city): void
    {
        $this->city = mb_substr($city, 0, 50);
    }

    /**
     * @return string|null
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param ?string $postalCode
     */
    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = mb_substr($postalCode, 0, 50);
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param ?string $country
     */
    public function setCountry(?string $country): void
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
    public function getStateOrProvince(): ?string
    {
        $state_or_province = null;
        if (isset($this->stateOrProvince) && mb_strlen($this->stateOrProvince) === 2 && in_array($this->getCountry(), ["US", "CA"])) {
            $state_or_province = $this->getCountry() . "-" . $this->stateOrProvince;
        }
        return $state_or_province;
    }

    /**
     * @param ?string $stateOrProvince
     */
    public function setStateOrProvince(?string $stateOrProvince): void
    {
        $this->stateOrProvince = $stateOrProvince;
    }

    /**
     * @return string|null
     */
    public function getCountrySubdivision(): ?string
    {
        return $this->countrySubdivision;
    }

    /**
     * @param ?string $countrySubdivision
     */
    public function setCountrySubdivision(?string $countrySubdivision): void
    {
        if (mb_strlen($countrySubdivision) > 0) {
            $this->countrySubdivision = $this->getCountry() . '-' . $countrySubdivision;
        }
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param ?string $email
     */
    public function setEmail(?string $email): void
    {
        if (mb_strlen($email) <= 254 && preg_match('/^.+@.+\..+$/', $email)) {
            $this->email = $email;
        }
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param ?string $phone
     *  Like +33-61234567
     */
    public function setPhone(?string $phone): void
    {
        if (mb_strlen($phone) <= 18) {
            $this->phone = $phone;
        }
    }

}