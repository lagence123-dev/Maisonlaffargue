<?php

namespace MoneticoPaiement\Sdk\Api\PaymentSession;

use MoneticoPaiement\Sdk\Utilities\Models;

class MerchantConfiguration extends Models\MerchantConfiguration implements \JsonSerializable
{
    /** @var string */
    private string $version = "3.0";

    private ?ErgonomySettings $ergonomy_settings = null;

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
     * @return ErgonomySettings
     */
    public function getErgonomySettingsettings(): ErgonomySettings
    {
        return $this->ergonomy_settings;
    }

    /**
     * @param ErgonomySettings $ergonomy_settings
     * @return void
     */
    public function setErgonomySettings(ErgonomySettings $ergonomy_settings): void
    {
        $this->ergonomy_settings = $ergonomy_settings;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    /**
     * @return string[]
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'configuration' => $this->getConfiguration(),
            'language' => $this->getLanguage(),
            'point_of_sale' => $this->getPointOfSale(),
            'version' => $this->getVersion(),
            'ergonomy_settings' => $this->getErgonomySettingsettings()->toArray(),
        ], function ($value) {
            return !is_null($value);
        });
    }
}