<?php

namespace MoneticoPaiement\Sdk\Utilities\Models\OrderContext;

class Client extends Address implements \JsonSerializable
{
    /**
     * @var ?string
     */
    private ?string $birthLastName = null;

    /**
     * @var ?string
     */
    private ?string $birthCity = null;

    /**
     * @var ?string
     */
    private ?string $birthPostalCode = null;

    /**
     * @var ?string
     */
    private ?string $birthCountry = null;

    /**
     * @var ?string
     */
    private ?string $birthStateOrProvince = null;

    /**
     * @var ?string
     */
    private ?string $birthCountrySubdivision = null;

    /**
     * @var ?\DateTime
     */
    private ?\DateTime $birthdate = null;

    /**
     * @var ?string
     */
    private ?string $nationalIDNumber = null;

    /**
     * @var ?bool
     */
    private ?bool $suspiciousAccountActivity = null;

    /**
     * @var ?string
     */
    private ?string $authenticationMethod = null;

    /**
     * @var ?\DateTime
     */
    private ?\DateTime $authenticationTimestamp = null;

    /**
     * @var ?string
     */
    private ?string $priorAuthenticationMethod = null;

    /**
     * @var ?\DateTime
     */
    private ?\DateTime $priorAuthenticationTimestamp = null;

    /**
     * @var ?\DateTime
     */
    private ?\DateTime $paymentMeanAge = null;

    /**
     * @var ?int
     */
    private ?int $lastYearTransactions = null;

    /**
     * @var ?int
     */
    private ?int $last24HoursTransactions = null;

    /**
     * @var ?int
     */
    private ?int $addCardNbLast24Hours = null;

    /**
     * @var ?int
     */
    private ?int $last6MonthsPurchase = null;

    /**
     * @var ?\DateTime
     */
    private ?\DateTime $lastPasswordChange = null;

    /**
     * @var ?\DateTime
     */
    private ?\DateTime $accountAge = null;

    /**
     * @var ?\DateTime
     */
    private ?\DateTime $lastAccountModification = null;

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
            "birthLastName" => $this->getBirthLastName(),
            "birthCity" => $this->getBirthCity(),
            "birthPostalCode" => $this->getBirthPostalCode(),
            "birthCountry" => $this->getBirthCountry(),
            "birthStateOrProvince" => $this->getBirthStateOrProvince(),
            "birthCountrySubdivision" => $this->getBirthCountrySubdivision(),
            "birthdate" => $this->getFormatedBirthdate(),
            "phone" => $this->getPhone(),
            "nationalIDNumber" => $this->getNationalIDNumber(),
            "suspiciousAccountActivity" => $this->getSuspiciousAccountActivity(),
            "authenticationMethod" => $this->getAuthenticationMethod(),
            "authenticationTimestamp" => $this->getFormatedAuthenticationTimestamp(),
            "priorAuthenticationMethod" => $this->getPriorAuthenticationMethod(),
            "priorAuthenticationTimestamp" => $this->getFormatedPriorAuthenticationTimestamp(),
            "paymentMeanAge" => $this->getFormatedPaymentMeanAge(),
            "lastYearTransactions" => $this->getLastYearTransactions(),
            "last24HoursTransactions" => $this->getLast24HoursTransactions(),
            "addCardNbLast24Hours" => $this->getAddCardNbLast24Hours(),
            "last6MonthsPurchase" => $this->getLast6MonthsPurchase(),
            "lastPasswordChange" => $this->getFormatedLastPasswordChange(),
            "accountAge" => $this->getFormatedAccountAge(),
            "lastAccountModification" => $this->getFormatedLastAccountModification()
        ], function ($value) {
            return !is_null($value);
        });
    }


    /**
     * @return string|null
     */
    public function getBirthLastName(): ?string
    {
        return $this->birthLastName;
    }

    /**
     * @param string|null $birthLastName
     */
    public function setBirthLastName(?string $birthLastName): void
    {
        $this->birthLastName = mb_substr($birthLastName, 0, 45);
    }

    /**
     * @return string|null
     */
    public function getBirthCity(): ?string
    {
        return $this->birthCity;
    }

    /**
     * @param string|null $birthCity
     */
    public function setBirthCity(?string $birthCity): void
    {
        $this->birthCity = mb_substr($birthCity, 0, 50);;
    }

    /**
     * @return string|null
     */
    public function getBirthPostalCode(): ?string
    {
        return $this->birthPostalCode;
    }

    /**
     * @param string|null $birthPostalCode
     */
    public function setBirthPostalCode(?string $birthPostalCode): void
    {
        $this->birthPostalCode = mb_substr($birthPostalCode, 0, 10);
    }

    /**
     * @return string|null
     */
    public function getBirthCountry(): ?string
    {
        return $this->birthCountry;
    }

    /**
     * @param string|null $birthCountry
     */
    public function setBirthCountry(?string $birthCountry): void
    {
        if (mb_strlen($birthCountry) === 2) {
            $this->birthCountry = strtoupper($birthCountry);
        }
    }

    /**
     * @return string|null
     */
    public function getBirthStateOrProvince(): ?string
    {
        return $this->birthStateOrProvince;
    }

    /**
     * @param string|null $birthStateOrProvince
     */
    public function setBirthStateOrProvince(?string $birthStateOrProvince): void
    {
        if (mb_strlen($birthStateOrProvince) === 2) {
            $this->birthStateOrProvince = $birthStateOrProvince;
        }
    }

    /**
     * @return string|null
     */
    public function getBirthCountrySubdivision(): ?string
    {
        return $this->birthCountrySubdivision;
    }

    /**
     * @param string|null $birthCountrySubdivision
     */
    public function setBirthCountrySubdivision(?string $birthCountrySubdivision): void
    {
        if (mb_strlen($birthCountrySubdivision) === 2) {
            $this->birthCountrySubdivision = $birthCountrySubdivision;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthdate(): ?\Datetime
    {
        return $this->birthdate;
    }

    /**
     * @return string|null
     */
    public function getFormatedBirthdate()
    {
        if ($this->getBirthdate() instanceof \DateTime) {
            return $this->getBirthdate()->format("Y-m-d");
        }
        return null;
    }

    /**
     * @param \DateTime|null $birthdate
     */
    public function setBirthdate(?\DateTime $birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    /**
     * @return string|null
     */
    public function getNationalIDNumber(): ?string
    {
        return $this->nationalIDNumber;
    }

    /**
     * @param string|null $nationalIDNumber
     */
    public function setNationalIDNumber(?string $nationalIDNumber): void
    {
        $this->nationalIDNumber = mb_substr($nationalIDNumber, 0, 255);
    }

    /**
     * @return bool|null
     */
    public function getSuspiciousAccountActivity(): ?bool
    {
        return $this->suspiciousAccountActivity;
    }

    /**
     * @param bool|null $suspiciousAccountActivity
     */
    public function setSuspiciousAccountActivity(?bool $suspiciousAccountActivity): void
    {
        $this->suspiciousAccountActivity = $suspiciousAccountActivity;
    }

    /**
     * @return string|null
     */
    public function getAuthenticationMethod(): ?string
    {
        return $this->authenticationMethod;
    }

    /**
     * @param string|null $authenticationMethod
     */
    public function setAuthenticationMethod(?string $authenticationMethod): void
    {
        if (in_array($authenticationMethod, ['guest', 'own_credentials', 'federated_id', 'issuer_credentials', 'third_party_authentication', 'fido'])) {
            $this->authenticationMethod = $authenticationMethod;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getAuthenticationTimestamp(): ?\Datetime
    {
        return $this->authenticationTimestamp;
    }

    /**
     * @return string|null
     */
    public function getFormatedAuthenticationTimestamp(): ?string
    {
        if ($this->getAuthenticationTimestamp() instanceof \DateTime) {
            return $this->getAuthenticationTimestamp()->format("Y-m-d\TH:i:s\Z");
        }
        return null;
    }

    /**
     * @param \DateTime|null $authenticationTimestamp
     */
    public function setAuthenticationTimestamp(?\DateTime $authenticationTimestamp): void
    {
        $this->authenticationTimestamp = $authenticationTimestamp;
    }

    /**
     * @return string|null
     */
    public function getPriorAuthenticationMethod(): ?string
    {
        return $this->priorAuthenticationMethod;
    }

    /**
     * @param string|null $priorAuthenticationMethod
     */
    public function setPriorAuthenticationMethod(?string $priorAuthenticationMethod): void
    {
        if (in_array($priorAuthenticationMethod, ['frictionless', 'challenge', 'AVS_verified', 'other'])) {
            $this->priorAuthenticationMethod = $priorAuthenticationMethod;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getPriorAuthenticationTimestamp(): ?\Datetime
    {
        return $this->priorAuthenticationTimestamp;
    }

    /**
     * @return string|null
     */
    public function getFormatedPriorAuthenticationTimestamp(): ?string
    {
        if ($this->getPriorAuthenticationTimestamp() instanceof \DateTime) {
            return $this->getPriorAuthenticationTimestamp()->format("Y-m-d\TH:i:s\Z");
        }
        return null;
    }

    /**
     * @param \DateTime|null $priorAuthenticationTimestamp
     */
    public function setPriorAuthenticationTimestamp(?\DateTime $priorAuthenticationTimestamp): void
    {
        $this->priorAuthenticationTimestamp = $priorAuthenticationTimestamp;
    }

    /**
     * @return \DateTime|null
     */
    public function getPaymentMeanAge(): ?\Datetime
    {
        return $this->paymentMeanAge;
    }

    /**
     * @return string|null
     */
    public function getFormatedPaymentMeanAge(): ?string
    {
        if ($this->getPaymentMeanAge() instanceof \DateTime) {
            return $this->getPaymentMeanAge()->format("Y-m-d");
        }
        return null;
    }

    /**
     * @param \DateTime|null $paymentMeanAge
     */
    public function setPaymentMeanAge(?\DateTime $paymentMeanAge): void
    {
        $this->paymentMeanAge = $paymentMeanAge;
    }

    /**
     * @return int|null
     */
    public function getLastYearTransactions(): ?int
    {
        return $this->lastYearTransactions;
    }

    /**
     * @param int|null $lastYearTransactions
     */
    public function setLastYearTransactions(?int $lastYearTransactions): void
    {
        if ($lastYearTransactions >= 0) {
            $this->lastYearTransactions = $lastYearTransactions;
        }
    }

    /**
     * @return int|null
     */
    public function getLast24HoursTransactions(): ?int
    {
        return $this->last24HoursTransactions;
    }

    /**
     * @param int|null $last24HoursTransactions
     */
    public function setLast24HoursTransactions(?int $last24HoursTransactions): void
    {
        if ($last24HoursTransactions >= 0) {
            $this->last24HoursTransactions = $last24HoursTransactions;
        }
    }

    /**
     * @return int|null
     */
    public function getAddCardNbLast24Hours(): ?int
    {
        return $this->addCardNbLast24Hours;
    }

    /**
     * @param int|null $addCardNbLast24Hours
     */
    public function setAddCardNbLast24Hours(?int $addCardNbLast24Hours): void
    {
        if ($addCardNbLast24Hours >= 0) {
            $this->addCardNbLast24Hours = $addCardNbLast24Hours;
        }
    }

    /**
     * @return int|null
     */
    public function getLast6MonthsPurchase(): ?int
    {
        return $this->last6MonthsPurchase;
    }

    /**
     * @param int|null $last6MonthsPurchase
     */
    public function setLast6MonthsPurchase(?int $last6MonthsPurchase): void
    {
        if ($last6MonthsPurchase >= 0) {
            $this->last6MonthsPurchase = $last6MonthsPurchase;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getLastPasswordChange(): ?\Datetime
    {
        return $this->lastPasswordChange;
    }

    /**
     * @return string|null
     */
    public function getFormatedLastPasswordChange(): ?string
    {
        if ($this->getLastPasswordChange() instanceof \DateTime) {
            return $this->getLastPasswordChange()->format("Y-m-d");
        }
        return null;
    }

    /**
     * @param \DateTime|null $lastPasswordChange
     */
    public function setLastPasswordChange(?\DateTime $lastPasswordChange): void
    {
        $this->lastPasswordChange = $lastPasswordChange;
    }

    /**
     * @return \DateTime|null
     */
    public function getAccountAge(): ?\Datetime
    {
        return $this->accountAge;
    }

    /**
     * @return string|null
     */
    public function getFormatedAccountAge(): ?string
    {
        if ($this->getAccountAge() instanceof \DateTime) {
            return $this->getAccountAge()->format("Y-m-d");
        }
        return null;
    }

    /**
     * @param \DateTime|null $accountAge
     */
    public function setAccountAge(?\DateTime $accountAge): void
    {
        $this->accountAge = $accountAge;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastAccountModification(): ?\Datetime
    {
        return $this->lastAccountModification;
    }

    /**
     * @return string|null
     */
    public function getFormatedLastAccountModification(): ?string
    {
        if ($this->getLastAccountModification() instanceof \DateTime) {
            return $this->getLastAccountModification()->format("Y-m-d");
        }
        return null;
    }

    /**
     * @param \DateTime|null $lastAccountModification
     */
    public function setLastAccountModification(?\DateTime $lastAccountModification): void
    {
        $this->lastAccountModification = $lastAccountModification;
    }
}