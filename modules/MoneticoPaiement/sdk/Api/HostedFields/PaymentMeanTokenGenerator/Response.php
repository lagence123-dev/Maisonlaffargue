<?php

namespace MoneticoPaiement\Sdk\Api\HostedFields\PaymentMeanTokenGenerator;

class Response
{
    /** @var array */
    private array $entryFields;

    /** @var string */
    private string $errorMessage;

    /** @var int */
    private int $returnCode;

    /** @var string */
    private string $token;

    public function __construct($response = null)
    {
        if (isset($response['entry_fields'])) {
            $this->setEntryFields($response['entry_fields']);
        }

        if (isset($response['error_message'])) {
            $this->setErrorMessage($response['error_message']);
        }

        if (isset($response['return_code'])) {
            $this->setReturnCode($response['return_code']);
        }

        if (isset($response['token'])) {
            $this->setToken($response['token']);
        }
    }

    /**
     * @return array
     */
    public function getEntryFields(): array
    {
        return $this->entryFields;
    }

    /**
     * @param array $entryFields
     */
    public function setEntryFields(array $entryFields): void
    {
        $this->entryFields = $entryFields;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage(string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return int
     */
    public function getReturnCode(): int
    {
        return $this->returnCode;
    }

    /**
     * @param int $returnCode
     */
    public function setReturnCode(int $returnCode): void
    {
        $this->returnCode = $returnCode;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }


}