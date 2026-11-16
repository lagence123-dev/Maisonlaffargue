<?php

namespace MoneticoPaiement\Sdk\Api\PaymentSession;

class ErgonomySettings
{
    private string $redirection_url_for_successful_payment = '';
    private string $redirection_url_for_failed_payment = '';
    private string $redirection_url_for_pending_payment = '';
    private string $redirection_url_for_cancel_payment = '';
    private ?string $integration_type = null;

    /**
     * @return string
     */
    public function getRedirectionUrlForSuccessfulPayment(): string
    {
        return $this->redirection_url_for_successful_payment;
    }

    /**
     * @param string $redirection_url_for_successful_payment
     * @return void
     */
    public function setRedirectionUrlForSuccessfulPayment(string $redirection_url_for_successful_payment): void
    {
        $this->redirection_url_for_successful_payment = $redirection_url_for_successful_payment;
    }

    /**
     * @return string
     */
    public function getRedirectionUrlForFailedPayment(): string
    {
        return $this->redirection_url_for_failed_payment;
    }

    /**
     * @param string $redirection_url_for_failed_payment
     * @return void
     */
    public function setRedirectionUrlForFailedPayment(string $redirection_url_for_failed_payment): void
    {
        $this->redirection_url_for_failed_payment = $redirection_url_for_failed_payment;
    }

    /**
     * @return string
     */
    public function getRedirectionUrlForPendingPayment(): string
    {
        return $this->redirection_url_for_pending_payment;
    }

    /**
     * @param string $redirection_url_for_pending_payment
     * @return void
     */
    public function setRedirectionUrlForPendingPayment(string $redirection_url_for_pending_payment): void
    {
        $this->redirection_url_for_pending_payment = $redirection_url_for_pending_payment;
    }

    /**
     * @return string
     */
    public function getRedirectionUrlForCancelPayment(): string
    {
        return $this->redirection_url_for_cancel_payment;
    }

    /**
     * @param string $redirection_url_for_cancel_payment
     * @return void
     */
    public function setRedirectionUrlForCancelPayment(string $redirection_url_for_cancel_payment): void
    {
        $this->redirection_url_for_cancel_payment = $redirection_url_for_cancel_payment;
    }

    /**
     * @return ?string
     */
    public function getIntegrationType(): ?string
    {
        return $this->integration_type;
    }

    /**
     * @param string $integration_type
     * @return void
     */
    public function setIntegrationType(string $integration_type): void
    {
        $this->integration_type = $integration_type;
    }

    public function toArray():array
    {
        return array_filter([
            'integration_type' => $this->getIntegrationType(),
            'redirection_url_for_successful_payment' => $this->getRedirectionUrlForSuccessfulPayment(),
            'redirection_url_for_failed_payment' => $this->getRedirectionUrlForFailedPayment(),
            'redirection_url_for_pending_payment' => $this->getRedirectionUrlForPendingPayment(),
            'redirection_url_for_cancel_payment' => $this->getRedirectionUrlForCancelPayment(),
        ], function ($value) {
            return !is_null($value);
        });
    }
}