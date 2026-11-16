<?php

namespace MoneticoPaiement\Sdk\Api\PaymentService\Payment\PaymentOptions;

class MerchantIdentification
{
    /** @var string|null $name */
    private ?string $name = null;

    /** @var string|null $locality */
    private ?string $locality = null;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return void
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getLocality(): ?string
    {
        return $this->locality;
    }

    /**
     * @param string|null $locality
     *
     * @return void
     */
    public function setLocality(?string $locality): void
    {
        $this->locality = $locality;
    }


}