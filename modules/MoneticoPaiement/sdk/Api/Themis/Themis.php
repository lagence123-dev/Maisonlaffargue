<?php

namespace MoneticoPaiement\Sdk\Api\Themis;

use MoneticoPaiement\Sdk\Utilities\HmacComputer;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentEnvironment;

class Themis
{
    /**
     * @var string
     * API Request URL
     */
    private $URL;

    /**
     * @var string
     * CMS name
     */
    private $cmsName;

    /**
     * @var string
     * Module version
     */
    private $moduleVersion;

    /**
     * @var string
     * EPT id
     */
    private $PointOfSaleId;

    /**
     * @var string
     * $cmsName/$moduleVersion hashed (SHA1 with EPT security key)
     */
    private $Hmac;

    /**
     * @var string
     * Language ISO code
     */
    private $EuroInfoCultre;

    /***
     * @param $moduleVersion
     * @param $cmsName
     */
    public function __construct($moduleVersion, $cmsName)
    {
        $this->setModuleVersion($moduleVersion);
        $this->setCmsName($cmsName);
    }

    /**
     * @return string
     */
    public function getCmsName(): string
    {
        return $this->cmsName;
    }

    /**
     * @param string $cmsName
     */
    public function setCmsName(string $cmsName): void
    {
        $this->cmsName = $cmsName;
    }

    /**
     * @return string
     */
    public function getModuleVersion(): string
    {
        return $this->moduleVersion;
    }

    /**
     * @param string $moduleVersion
     */
    public function setModuleVersion(string $moduleVersion): void
    {
        $this->moduleVersion = $moduleVersion;
    }

    /**
     * @return string
     */
    public function getHmac(): string
    {
        return $this->Hmac;
    }

    /***
     * @param string $ept_mac
     */
    public function setHmac($ept_mac)
    {
        $hmacComputer = new HmacComputer();
        $stringToSeal = $this->getCmsName() . '/' . $this->getModuleVersion();
        $this->Hmac = $hmacComputer->sealString($stringToSeal, $ept_mac);
    }

    /**
     * @return string
     */
    public function getPointOfSaleId(): string
    {
        return $this->PointOfSaleId;
    }

    /**
     * @param string $PointOfSaleId
     */
    public function setPointOfSaleId(string $PointOfSaleId): void
    {
        $this->PointOfSaleId = $PointOfSaleId;
    }

    /**
     * @return string
     */
    public function getEuroInfoCultre(): string
    {
        return $this->EuroInfoCultre;
    }

    /**
     * @param string $EuroInfoCultre
     */
    public function setEuroInfoCultre(string $EuroInfoCultre): void
    {
        $this->EuroInfoCultre = $EuroInfoCultre;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->URL;
    }

    /**
     * @param bool $withPlanning
     */
    public function setUrl($withPlanning): void
    {
        $url = sprintf('/v1/modules/%s/versions/%s', $this->getCmsName(), $this->getModuleVersion());
        if ($withPlanning) {
            $url .= "/future-module-statuses";
        }
        $this->URL = PaymentEnvironment::API_THEMIS_URI . $url;
    }

    /**
     * @return array|mixed
     */
    public function getHeaderData(): array
    {
        return [
            'PointOfSaleId: ' . $this->getPointOfSaleId(),
            'Hmac: ' . $this->getHmac(),
            'EuroInfo-CultureUI: ' . $this->getEuroInfoCultre(),
        ];
    }
}