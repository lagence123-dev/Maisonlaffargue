<?php

namespace MoneticoPaiement\Sdk\Response;

use MoneticoPaiement\Sdk\Response\PaymentSession\Error;
use MoneticoPaiement\Sdk\Response\PaymentSession\NextStep;

class PaymentSessionResponse
{
    private string $id;
    private string $expiration_date;
    private string $state;
    private ?Error $error = null;
    private ?NextStep $next_step = null;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getExpirationDate(): string
    {
        return $this->expiration_date;
    }

    /**
     * @param string $expiration_date
     */
    public function setExpirationDate(string $expiration_date): void
    {
        $this->expiration_date = $expiration_date;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return Error|null
     */
    public function getError(): ?Error
    {
        return $this->error;
    }

    /**
     * @param Error $error
     */
    public function setError(Error $error): void
    {
        $this->error = $error;
    }

    /**
     * @return NextStep|null
     */
    public function getNextStep(): ?NextStep
    {
        return $this->next_step;
    }

    /**
     * @param NextStep $next_step
     */
    public function setNextStep(NextStep $next_step): void
    {
        $this->next_step = $next_step;
    }
}