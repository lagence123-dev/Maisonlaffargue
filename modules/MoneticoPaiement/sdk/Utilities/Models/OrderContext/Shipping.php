<?php

namespace MoneticoPaiement\Sdk\Utilities\Models\OrderContext;

class Shipping extends Address implements \JsonSerializable
{
    /**
     * @var ?string
     */
    private ?string $shipIndicator = null;

    /**
     * @var ?string
     */
    private ?string $deliveryTimeframe = null;

    /**
     * @var ?\DateTime
     */
    private ?\DateTime $firstUseDate = null;

    /**
     * @var ?bool
     */
    private ?bool $matchBillingAddress = null;

    /**
     * @return ?string
     */
    public function getShipIndicator(): ?string
    {
        return $this->shipIndicator;
    }

    /**
     * @param ?string $shipIndicator
     */
    public function setShipIndicator(?string $shipIndicator): void
    {
        if (in_array($shipIndicator, ['digital_goods', 'travel_and_event', 'billing_address', 'verified_address', 'another_address', 'pick-up', 'other'])) {
            $this->shipIndicator = $shipIndicator;
        }
    }

    /**
     * @return ?string
     */
    public function getDeliveryTimeframe(): ?string
    {
        return $this->deliveryTimeframe;
    }

    /**
     * @param ?string $deliveryTimeframe
     */
    public function setDeliveryTimeframe(?string $deliveryTimeframe): void
    {
        if (in_array($deliveryTimeframe, ['same_day', 'overnight', 'two_day', 'three_day', 'long', 'other', 'none'])) {
            $this->deliveryTimeframe = $deliveryTimeframe;
        }
    }

    /**
     * @return ?\DateTime
     */
    public function getFirstUseDate(): ?\DateTime
    {
        return $this->firstUseDate;
    }

    /**
     * @param ?\DateTime $firstUseDate
     */
    public function setFirstUseDate(?\DateTime $firstUseDate): void
    {
        $this->firstUseDate = $firstUseDate;
    }

    /**
     * @return ?bool
     */
    public function getMatchBillingAddress(): ?bool
    {
        return $this->matchBillingAddress;
    }

    /**
     * @param ?bool $matchBillingAddress
     */
    public function setMatchBillingAddress(?bool $matchBillingAddress): void
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