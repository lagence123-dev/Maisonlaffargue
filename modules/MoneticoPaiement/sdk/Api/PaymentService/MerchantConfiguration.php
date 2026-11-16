<?php

namespace MoneticoPaiement\Sdk\Api\PaymentService;

use MoneticoPaiement\Sdk\Utilities\Models;

class MerchantConfiguration extends Models\MerchantConfiguration implements \JsonSerializable
{
    /** @var string */
    private string $version = "3.0";

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return void
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return array
     */
    public function toArray(): array {
        return $this->jsonSerialize();
    }

    /**
     * @return string[]
     */
    public function jsonSerialize():array
    {
        return array_filter([
            'configuration' => $this->getConfiguration(),
            'language' => $this->getLanguage(),
            'point_of_sale' => $this->getPointOfSale(),
            'version' => $this->getVersion()
        ], function ($value) {
            return !is_null($value);
        });
    }
}