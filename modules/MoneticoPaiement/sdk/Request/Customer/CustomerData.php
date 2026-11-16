<?php

namespace MoneticoPaiement\Sdk\Request\Customer;

use MoneticoPaiement\Sdk\Request\Customer\CustomerAddress;

class CustomerData extends CustomerAddress implements \JsonSerializable
{
    /**
     * @var ?string
     */
    private $birthLastName;

    /**
     * @var ?string
     */
    private $birthCity;

    /**
     * @var ?string
     */
    private $birthPostalCode;

    /**
     * @var ?string
     */
    private $birthCountry;

    /**
     * @var ?string
     */
    private $birthStateOrProvince;

    /**
     * @var ?string
     */
    private $birthCountrySubdivision;

    /**
     * @var ?\DateTime
     */
    private $birthdate;

    /**
     * @var ?string
     */
    private $nationalIDNumber;

    /**
     * @var ?bool
     */
    private $suspiciousAccountActivity;

    /**
     * @var ?string
     */
    private $authenticationMethod;

    /**
     * @var ?\DateTime
     */
    private $authenticationTimestamp;

    /**
     * @var ?string
     */
    private $priorAuthenticationMethod;

    /**
     * @var ?\DateTime
     */
    private $priorAuthenticationTimestamp;

    /**
     * @var ?\DateTime
     */
    private $paymentMeanAge;

    /**
     * @var ?int
     */
    private $lastYearTransactions;

    /**
     * @var ?int
     */
    private $last24HoursTransactions;

    /**
     * @var ?int
     */
    private $addCardNbLast24Hours;

    /**
     * @var ?int
     */
    private $last6MonthsPurchase;

    /**
     * @var ?\DateTime
     */
    private $lastPasswordChange;

    /**
     * @var ?\DateTime
     */
    private $accountAge;

    /**
     * @var ?\DateTime
     */
    private $lastAccountModification;

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
    public function getBirthLastName()
    {
        return $this->birthLastName;
    }

    /**
     * @param string|null $birthLastName
     */
    public function setBirthLastName(string $birthLastName)
    {
        $this->birthLastName = mb_substr($birthLastName, 0, 45);
    }

    /**
     * @return string|null
     */
    public function getBirthCity()
    {
        return $this->birthCity;
    }

    /**
     * @param string|null $birthCity
     */
    public function setBirthCity(string $birthCity)
    {
        $this->birthCity = $birthCity;
    }

    /**
     * @return string|null
     */
    public function getBirthPostalCode()
    {
        return $this->birthPostalCode;
    }

    /**
     * @param string|null $birthPostalCode
     */
    public function setBirthPostalCode(string $birthPostalCode)
    {
        $this->birthPostalCode = mb_substr($birthPostalCode, 0, 10);
    }

    /**
     * @return string|null
     */
    public function getBirthCountry()
    {
        return $this->birthCountry;
    }

    /**
     * @param string|null $birthCountry
     */
    public function setBirthCountry(string $birthCountry)
    {
        if (mb_strlen($birthCountry) === 2) {
            $this->birthCountry = strtoupper($birthCountry);
        }
    }

    /**
     * @return string|null
     */
    public function getBirthStateOrProvince()
    {
        return $this->birthStateOrProvince;
    }

    /**
     * @param string|null $birthStateOrProvince
     */
    public function setBirthStateOrProvince(string $birthStateOrProvince)
    {
        if (mb_strlen($birthStateOrProvince) === 2) {
            $this->birthStateOrProvince = $birthStateOrProvince;
        }
    }

    /**
     * @return string|null
     */
    public function getBirthCountrySubdivision()
    {
        return $this->birthCountrySubdivision;
    }

    /**
     * @param string|null $birthCountrySubdivision
     */
    public function setBirthCountrySubdivision(string $birthCountrySubdivision)
    {
        if (mb_strlen($birthCountrySubdivision) === 2) {
            $this->birthCountrySubdivision = $birthCountrySubdivision;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthdate()
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
    }

    /**
     * @param \DateTime|null $birthdate
     */
    public function setBirthdate(\DateTime $birthdate)
    {
        $this->birthdate = $birthdate;
    }

    /**
     * @return string|null
     */
    public function getNationalIDNumber()
    {
        return $this->nationalIDNumber;
    }

    /**
     * @param string|null $nationalIDNumber
     */
    public function setNationalIDNumber(string $nationalIDNumber)
    {
        $this->nationalIDNumber = mb_substr($nationalIDNumber, 0, 255);
    }

    /**
     * @return bool|null
     */
    public function getSuspiciousAccountActivity()
    {
        return $this->suspiciousAccountActivity;
    }

    /**
     * @param bool|null $suspiciousAccountActivity
     */
    public function setSuspiciousAccountActivity(bool $suspiciousAccountActivity)
    {
        $this->suspiciousAccountActivity = $suspiciousAccountActivity;
    }

    /**
     * @return string|null
     */
    public function getAuthenticationMethod()
    {
        return $this->authenticationMethod;
    }

    /**
     * @param string|null $authenticationMethod
     */
    public function setAuthenticationMethod(string $authenticationMethod)
    {
        if (in_array($authenticationMethod, ['guest', 'own_credentials', 'federated_id', 'issuer_credentials', 'third_party_authentication', 'fido'])) {
            $this->authenticationMethod = $authenticationMethod;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getAuthenticationTimestamp()
    {
        return $this->authenticationTimestamp;
    }

    /**
     * @return string|null
     */
    public function getFormatedAuthenticationTimestamp()
    {
        if ($this->getAuthenticationTimestamp() instanceof \DateTime) {
            return $this->getAuthenticationTimestamp()->format("Y-m-d\TH:i:s\Z");
        }
        return null;
    }

    /**
     * @param \DateTime|null $authenticationTimestamp
     */
    public function setAuthenticationTimestamp(\DateTime $authenticationTimestamp)
    {
        $this->authenticationTimestamp = $authenticationTimestamp;
    }

    /**
     * @return string|null
     */
    public function getPriorAuthenticationMethod()
    {
        return $this->priorAuthenticationMethod;
    }

    /**
     * @param string|null $priorAuthenticationMethod
     */
    public function setPriorAuthenticationMethod(string $priorAuthenticationMethod)
    {
        if (in_array($priorAuthenticationMethod, ['frictionless', 'challenge', 'AVS_verified', 'other'])) {
            $this->priorAuthenticationMethod = $priorAuthenticationMethod;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getPriorAuthenticationTimestamp()
    {
        return $this->priorAuthenticationTimestamp;
    }

    /**
     * @return string|null
     */
    public function getFormatedPriorAuthenticationTimestamp()
    {
        if ($this->getPriorAuthenticationTimestamp() instanceof \DateTime) {
            return $this->getPriorAuthenticationTimestamp()->format("Y-m-d\TH:i:s\Z");
        }
        return null;
    }

    /**
     * @param \DateTime|null $priorAuthenticationTimestamp
     */
    public function setPriorAuthenticationTimestamp(\DateTime $priorAuthenticationTimestamp)
    {
        $this->priorAuthenticationTimestamp = $priorAuthenticationTimestamp;
    }

    /**
     * @return \DateTime|null
     */
    public function getPaymentMeanAge()
    {
        return $this->paymentMeanAge;
    }

    /**
     * @return string|null
     */
    public function getFormatedPaymentMeanAge()
    {
        if ($this->getPaymentMeanAge() instanceof \DateTime) {
            return $this->getPaymentMeanAge()->format("Y-m-d");
        }
        return null;
    }

    /**
     * @param \DateTime|null $paymentMeanAge
     */
    public function setPaymentMeanAge(\DateTime $paymentMeanAge)
    {
        $this->paymentMeanAge = $paymentMeanAge;
    }

    /**
     * @return int|null
     */
    public function getLastYearTransactions()
    {
        return $this->lastYearTransactions;
    }

    /**
     * @param int|null $lastYearTransactions
     */
    public function setLastYearTransactions(int $lastYearTransactions)
    {
        if ($lastYearTransactions >= 0) {
            $this->lastYearTransactions = $lastYearTransactions;
        }
    }

    /**
     * @return int|null
     */
    public function getLast24HoursTransactions()
    {
        return $this->last24HoursTransactions;
    }

    /**
     * @param int|null $last24HoursTransactions
     */
    public function setLast24HoursTransactions(int $last24HoursTransactions)
    {
        if ($last24HoursTransactions >= 0) {
            $this->last24HoursTransactions = $last24HoursTransactions;
        }
    }

    /**
     * @return int|null
     */
    public function getAddCardNbLast24Hours()
    {
        return $this->addCardNbLast24Hours;
    }

    /**
     * @param int|null $addCardNbLast24Hours
     */
    public function setAddCardNbLast24Hours(int $addCardNbLast24Hours)
    {
        if ($addCardNbLast24Hours >= 0) {
            $this->addCardNbLast24Hours = $addCardNbLast24Hours;
        }
    }

    /**
     * @return int|null
     */
    public function getLast6MonthsPurchase()
    {
        return $this->last6MonthsPurchase;
    }

    /**
     * @param int|null $last6MonthsPurchase
     */
    public function setLast6MonthsPurchase(int $last6MonthsPurchase)
    {
        if ($last6MonthsPurchase >= 0) {
            $this->last6MonthsPurchase = $last6MonthsPurchase;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getLastPasswordChange()
    {
        return $this->lastPasswordChange;
    }

    /**
     * @return string|null
     */
    public function getFormatedLastPasswordChange()
    {
        if ($this->getLastPasswordChange() instanceof \DateTime) {
            return $this->getLastPasswordChange()->format("Y-m-d");
        }
        return null;
    }

    /**
     * @param \DateTime|null $lastPasswordChange
     */
    public function setLastPasswordChange(\DateTime $lastPasswordChange)
    {
        $this->lastPasswordChange = $lastPasswordChange;
    }

    /**
     * @return \DateTime|null
     */
    public function getAccountAge()
    {
        return $this->accountAge;
    }

    /**
     * @return string|null
     */
    public function getFormatedAccountAge()
    {
        if ($this->getAccountAge() instanceof \DateTime) {
            return $this->getAccountAge()->format("Y-m-d");
        }
        return null;
    }

    /**
     * @param \DateTime|null $accountAge
     */
    public function setAccountAge(\DateTime $accountAge)
    {
        $this->accountAge = $accountAge;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastAccountModification()
    {
        return $this->lastAccountModification;
    }

    /**
     * @return string|null
     */
    public function getFormatedLastAccountModification()
    {
        if ($this->getLastAccountModification() instanceof \DateTime) {
            return $this->getLastAccountModification()->format("Y-m-d");
        }
        return null;
    }

    /**
     * @param \DateTime|null $lastAccountModification
     */
    public function setLastAccountModification(\DateTime $lastAccountModification)
    {
        $this->lastAccountModification = $lastAccountModification;
    }
}