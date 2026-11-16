<?php

namespace MoneticoPaiement\Sdk\Response\PaymentSession;

class NextStep
{
    private string $recommended_implementation;
    private string $url;

    /**
     * @return string
     */
    public function getRecommendedImplementation(): string
    {
        return $this->recommended_implementation;
    }

    /**
     * @param string $recommended_implementation
     */
    public function setRecommendedImplementation(string $recommended_implementation): void
    {
        $this->recommended_implementation = $recommended_implementation;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}