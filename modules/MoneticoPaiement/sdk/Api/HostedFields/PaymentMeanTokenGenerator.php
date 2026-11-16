<?php

namespace MoneticoPaiement\Sdk\Api\HostedFields;

use MoneticoPaiement\Sdk\Utilities\Models\MerchantConfiguration;
use MoneticoPaiement\Sdk\Api\HostedFields\PaymentMeanTokenGenerator\Context;
use MoneticoPaiement\Sdk\Utilities\Interfaces\MoneticoRequestInterface;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentEnvironment;

class PaymentMeanTokenGenerator implements MoneticoRequestInterface
{
    /** @var string */
    private string $action = 'InitializePaymentMean';

    /** @var ?Context */
    private ?Context $context  = null;

    /** @var \DateTime */
    private \DateTime $datetime;

    /** @var MerchantConfiguration */
    private MerchantConfiguration $merchantConfiguration;

    /**
     * @param bool $isSandbox
     *
     * @return string
     */
    public function getUrl(bool $isSandbox=false): string
    {
        $baseUrl = $isSandbox ? PaymentEnvironment::API_PAYMENT_V2_SANDBOX_URI
                                : PaymentEnvironment::API_PAYMENT_V2_PROD_URI;
        return $baseUrl . PaymentEnvironment::PAYMENT_MEAN_TOKEN_GENERATOR_CGI;
    }

    /**
     * @return array
     */
    public function getFields():array {
        return array_filter([
            'action' => $this->getAction(),
            'context' => $this->getContext(),
            'datetime' => $this->getDatetime()->format('Y-m-d\TH:i:s'),
            'merchant_configuration' => $this->getMerchantConfiguration()->jsonSerialize(),
        ], function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return void
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return Context|null
     */
    public function getContext(): ?Context
    {
        return $this->context;
    }

    /**
     * @param ?Context $context
     *
     * @return void
     */
    public function setContext(?Context $context): void
    {
        $this->context = $context;
    }

    /**
     * @return \DateTime
     */
    public function getDatetime(): \DateTime
    {
        return $this->datetime;
    }

    /**
     * @param \DateTime $datetime
     *
     * @return void
     */
    public function setDatetime(\DateTime $datetime): void
    {
        $this->datetime = $datetime;
    }

    /**
     * @return MerchantConfiguration
     */
    public function getMerchantConfiguration(): MerchantConfiguration
    {
        return $this->merchantConfiguration;
    }

    /**
     * @param \MoneticoPaiement\Sdk\Utilities\Models\MerchantConfiguration $merchantConfiguration
     *
     * @return void
     */
    public function setMerchantConfiguration(MerchantConfiguration $merchantConfiguration): void
    {
        $this->merchantConfiguration = $merchantConfiguration;
    }


}