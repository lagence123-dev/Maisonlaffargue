<?php

namespace MoneticoPaiement\Sdk\Utilities\Models;

use MoneticoPaiement\Sdk\Utilities\Models\OrderContext\Shipping;
use MoneticoPaiement\Sdk\Utilities\Models\OrderContext\Client;
use MoneticoPaiement\Sdk\Utilities\Models\OrderContext\Billing;
use MoneticoPaiement\Sdk\Utilities\Models\OrderContext\ShoppingCart;
use MoneticoPaiement\Sdk\Utilities\Models\OrderContext\Browser;

class OrderContext implements \JsonSerializable
{

    /**
     * @var ?Client
     */
    private ?Client $client = null;
    /**
     * @var Billing
     */
    private Billing $billing;

    /**
     * @var ?Shipping
     */
    private ?Shipping $shipping = null;

    /**
     * @var ?ShoppingCart
     */
    private ?ShoppingCart $shopping = null;

    /**
     * @var ?Browser
     */
    private ?Browser $browser = null;

    /**
     * @return array|mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
            'billing' => $this->getBilling(),
            'client' => $this->getClient(),
            'shipping' => $this->getShipping(),
            'shoppingCart' => $this->getShoppingCart(),
            'browser' => $this->getBrowser()
        ], function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return ?Client
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @return Billing
     */
    public function getBilling(): Billing
    {
        return $this->billing;
    }

    /**
     * @param Billing $billing
     */
    public function setBilling(Billing $billing): void
    {
        $this->billing = $billing;
    }

    /**
     * @return ?Shipping
     */
    public function getShipping(): ?Shipping
    {
        return $this->shipping;
    }

    /**
     * @param Shipping $shipping
     */
    public function setShipping(Shipping $shipping): void
    {
        $this->shipping = $shipping;
    }

    /**
     * @return ?ShoppingCart
     */
    public function getShoppingCart(): ?ShoppingCart
    {
        return $this->shopping;
    }

    /**
     * @param ShoppingCart $shopping
     */
    public function setShoppingCart(ShoppingCart $shopping): void
    {
        $this->shopping = $shopping;
    }

    /**
     * @return ?Browser
     */
    public function getBrowser(): ?Browser
    {
        return $this->browser;
    }

    /**
     * @param Browser $browser
     */
    public function setBrowser(Browser $browser): void
    {
        $this->browser = $browser;
    }
}