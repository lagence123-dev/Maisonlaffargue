<?php

namespace MoneticoPaiement\Sdk\Api\PaymentService;

use MoneticoPaiement\Sdk\Api\PaymentService\Authentication\Details;

class Authentication
{
    /** @var ?string $merchant_preference */
    private ?string $merchant_preference = null;

    /** @var ?string $merchant_redirection_url */
    private ?string $merchant_redirection_url = null;

    /** @var ?string $challenge_window_size */
    private ?string $challenge_window_size = null;

    /** @var ?bool $disable_authentication */
    private ?bool $disable_authentication = null;

    /** @var ?string $status */
    private ?string $status = null;

    /** @var ?Details $details */
    private ?Details $details = null;

    /**
     * @return array
     */
    public function toArray():array
    {
        return array_filter([
            'merchant_preference' => $this->getMerchantPreference(),
            'merchant_redirection_url' => $this->getMerchantRedirectionUrl(),
            'challenge_window_size' => $this->getChallengeWindowSize(),
            'disable_authentication' => $this->getDisableAuthentication(),
            'status' => $this->getStatus(),
            'details' => $this->getDetails() ? $this->getDetails()->toArray() : null,
        ], function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return ?string
     */
    public function getMerchantPreference(): ?string
    {
        return $this->merchant_preference;
    }

    /**
     * @param string|null $merchant_preference
     *
     * @return void
     */
    public function setMerchantPreference(?string $merchant_preference): void
    {
        $this->merchant_preference = $merchant_preference;
    }

    /**
     * @return ?string
     */
    public function getMerchantRedirectionUrl(): ?string
    {
        return $this->merchant_redirection_url;
    }

    /**
     * @param string|null $merchant_redirection_url
     *
     * @return void
     */
    public function setMerchantRedirectionUrl(?string $merchant_redirection_url): void
    {
        $this->merchant_redirection_url = $merchant_redirection_url;
    }

    /**
     * @return ?string
     */
    public function getChallengeWindowSize(): ?string
    {
        return $this->challenge_window_size;
    }

    /**
     * @param string|null $challenge_window_size
     *
     * @return void
     */
    public function setChallengeWindowSize(?string $challenge_window_size): void
    {
        $this->challenge_window_size = $challenge_window_size;
    }

    /**
     * @return ?bool
     */
    public function getDisableAuthentication(): ?bool
    {
        return $this->disable_authentication;
    }

    /**
     * @param bool|null $disable_authentication
     *
     * @return void
     */
    public function setDisableAuthentication(?bool $disable_authentication): void
    {
        $this->disable_authentication = $disable_authentication;
    }

    /**
     * @return ?string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     *
     * @return void
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return ?Details
     */
    public function getDetails(): ?Details
    {
        return $this->details;
    }

    /**
     * @param \MoneticoPaiement\Sdk\Api\PaymentService\Authentication\Details|null $details
     *
     * @return void
     */
    public function setDetails(?Details $details): void
    {
        $this->details = $details;
    }
}