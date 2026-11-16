<?php

namespace MoneticoPaiement\Sdk\Api\Stats;

use MoneticoPaiement\Sdk\Utilities\Collections\PaymentEnvironment;

class Stats implements \JsonSerializable
{
    const URL = 'EMC2_STATSCMS/api/usage-report';
    /**
     * @var string
     * Site Code
     */
    private $SiteCode;
    /**
     * @var string
     * CMS Name
     */
    private $CmsName;
    /**
     * @var string
     * CMS Version
     */
    private $CmsVersion;
    /**
     * @var string
     * CMS Module Version
     */
    private $CmsModuleVersion;
    /**
     * @var string
     * Php Version
     */
    private $PhpVersion;
    /**
     * @var string
     * Php Version
     */
    private $MerchantUrl;
    /**
     * @var string
     * Php Version
     */
    private $OSVersion;

    /**
     * @return string
     */
    public function getSiteCode(): string
    {
        return $this->SiteCode;
    }

    /**
     * @param string $SiteCode
     */
    public function setSiteCode(string $SiteCode): void
    {
        $this->SiteCode = $SiteCode;
    }

    /**
     * @return string
     */
    public function getCmsName(): string
    {
        return $this->CmsName;
    }

    /**
     * @param string $CmsName
     */
    public function setCmsName(string $CmsName): void
    {
        $this->CmsName = $CmsName;
    }

    /**
     * @return string
     */
    public function getCmsVersion(): string
    {
        return $this->CmsVersion;
    }

    /**
     * @param string $CmsVersion
     */
    public function setCmsVersion(string $CmsVersion): void
    {
        $this->CmsVersion = $CmsVersion;
    }

    /**
     * @return string
     */
    public function getCmsModuleVersion(): string
    {
        return $this->CmsModuleVersion;
    }

    /**
     * @param string $CmsModuleVersion
     */
    public function setCmsModuleVersion(string $CmsModuleVersion): void
    {
        $this->CmsModuleVersion = $CmsModuleVersion;
    }

    /**
     * @return string
     */
    public function getPhpVersion(): string
    {
        return $this->PhpVersion;
    }

    /**
     * @param string $PhpVersion
     */
    public function setPhpVersion(string $PhpVersion): void
    {
        $this->PhpVersion = $PhpVersion;
    }

    /**
     * @return string
     */
    public function getMerchantUrl(): string
    {
        return $this->MerchantUrl;
    }

    /**
     * @param string $MerchantUrl
     */
    public function setMerchantUrl(string $MerchantUrl): void
    {
        $this->MerchantUrl = $MerchantUrl;
    }

    /**
     * @return string
     */
    public function getOSVersion(): string
    {
        return $this->OSVersion;
    }

    /**
     * @param string $OSVersion
     */
    public function setOSVersion(string $OSVersion): void
    {
        $this->OSVersion = $OSVersion;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return PaymentEnvironment::API_URI . PaymentEnvironment::STATSCMS;
    }

    /**
     * @return array|mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        $stats = [];
        $stats['SiteCode'] = $this->getSiteCode();
        $stats['CmsName'] = $this->getCmsName();
        $stats['CmsVersion'] = $this->getCmsVersion();
        $stats['ModuleVersion'] = $this->getCmsModuleVersion();
        $stats['PhpVersion'] = $this->getPhpVersion();
        $stats['MerchantUrl'] = $this->getMerchantUrl();
        $stats['OSVersion'] = $this->getOSVersion();
        return $stats;
    }
}