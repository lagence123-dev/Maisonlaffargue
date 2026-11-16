<?php

namespace MoneticoPaiement\Sdk\Api\PaymentService\Authentication;

class Details
{

    /** @var string|null $cres */
    private ?string $cres = null;

    /** @var string|null $threeDSSessionData */
    private ?string $threeDSSessionData = null;

    /** @var string|null $ACPRes */
    private ?string $ACPRes = null;


    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'cres' => $this->getCres(),
            'threeDSSessionData' => $this->getThreeDSSessionData(),
            'ACPRes' => $this->getACPRes()
        ], function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return string|null
     */
    public function getCres(): ?string
    {
        return $this->cres;
    }

    /**
     * @param string|null $cres
     *
     * @return void
     */
    public function setCres(?string $cres): void
    {
        $this->cres = $cres;
    }

    /**
     * @return string|null
     */
    public function getThreeDSSessionData(): ?string
    {
        return $this->threeDSSessionData;
    }

    /**
     * @param string|null $threeDSSessionData
     *
     * @return void
     */
    public function setThreeDSSessionData(?string $threeDSSessionData): void
    {
        $this->threeDSSessionData = $threeDSSessionData;
    }

    /**
     * @return string|null
     */
    public function getACPRes(): ?string
    {
        return $this->ACPRes;
    }

    /**
     * @param string|null $ACPRes
     *
     * @return void
     */
    public function setACPRes(?string $ACPRes): void
    {
        $this->ACPRes = $ACPRes;
    }
}