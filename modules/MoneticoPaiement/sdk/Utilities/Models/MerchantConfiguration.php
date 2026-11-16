<?php

namespace MoneticoPaiement\Sdk\Utilities\Models;

class MerchantConfiguration implements \JsonSerializable
{
    /**
     * @var string
     */
    public string $configuration;
    /**
     * @var string
     */
    public string $language;
    /**
     * @var string
     */
    public string $point_of_sale;

    /**
     * @return string
     */
    public function getConfiguration(): string
    {
        return $this->configuration;
    }

    /**
     * @param string $configuration
     */
    public function setConfiguration(string $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getPointOfSale(): string
    {
        return $this->point_of_sale;
    }

    /**
     * @param string $pointOfSale
     */
    public function setPointOfSale(string $pointOfSale): void
    {
        $this->point_of_sale = $pointOfSale;
    }

    public function toArray(): array {
        return $this->jsonSerialize();
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
            'configuration' => $this->getConfiguration(),
            'language' => $this->getLanguage(),
            'point_of_sale' => $this->getPointOfSale()
        ], function ($value) {
            return !is_null($value);
        });
    }
}