<?php

namespace MoneticoPaiement\Sdk\Request\Customer;

class CustomerShippingAddress extends CustomerAddress implements \JsonSerializable
{
    /**
     * @var ?string
     */
    private $shipIndicator;

    /**
     * @var ?string
     */
    private $deliveryTimeframe;

    /**
     * @var ?\DateTime
     */
    private $firstUseDate;

    /**
     * @var ?bool
     */
    private $matchBillingAddress;

    /**
     * @return string|null
     */
    public function getShipIndicator()
    {
        return $this->shipIndicator;
    }

    /**
     * @param string|null $shipIndicator
     */
    public function setShipIndicator(string $shipIndicator)
    {
        if (in_array($shipIndicator, ['digital_goods', 'travel_and_event', 'billing_address', 'verified_address', 'another_address', 'pick-up', 'other'])) {
            $this->shipIndicator = $shipIndicator;
        }
    }

    /**
     * @return string|null
     */
    public function getDeliveryTimeframe()
    {
        return $this->deliveryTimeframe;
    }

    /**
     * @param string|null $deliveryTimeframe
     */
    public function setDeliveryTimeframe(string $deliveryTimeframe)
    {
        if (in_array($deliveryTimeframe, ['same_day', 'overnight', 'two_day', 'three_day', 'long', 'other', 'none'])) {
            $this->deliveryTimeframe = $deliveryTimeframe;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getFirstUseDate()
    {
        return $this->firstUseDate;
    }

    /**
     * @param \DateTime|null $firstUseDate
     */
    public function setFirstUseDate(\DateTime $firstUseDate)
    {
        $this->firstUseDate = $firstUseDate;
    }

    /**
     * @return bool|null
     */
    public function getMatchBillingAddress()
    {
        return $this->matchBillingAddress;
    }

    /**
     * @param bool|null $matchBillingAddress
     */
    public function setMatchBillingAddress(bool $matchBillingAddress)
    {
        $this->matchBillingAddress = $matchBillingAddress;
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
            "shipIndicator" => $this->getShipIndicator(),
            "deliveryTimeframe" => $this->getDeliveryTimeframe(),
            "firstUseDate" => $this->getFirstUseDate(),
            "matchBillingAddress" => $this->getMatchBillingAddress()
        ], function ($value) {
            return !is_null($value);
        });
    }

}