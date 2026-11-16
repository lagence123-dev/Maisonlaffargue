<?php


namespace MoneticoPaiement\Sdk\Request\Customer;

use MoneticoPaiement\Sdk\Request\Customer\CustomerShippingAddress;
use MoneticoPaiement\Sdk\Request\Customer\CustomerData;
use MoneticoPaiement\Sdk\Request\Customer\CustomerBillingAddress;
use MoneticoPaiement\Sdk\Request\Customer\CustomerShoppingCart;


class CustomerOrder implements \JsonSerializable
{

    /**
     * @var ?CustomerData
     */
    private $customer;
    /**
     * @var CustomerBillingAddress
     */
    private $customerBillingAddress;

    /**
     * @var ?CustomerShippingAddress
     */
    private $customerShippingAddress;

    /**
     * @var ?CustomerShoppingCart
     */
    private $customerShoppingCart;

    /**
     * @return array|mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
            'billing' => $this->getCustomerBillingAddress(),
            'client' => $this->getCustomer(),
            'shipping' => $this->getCustomerShippingAddress(),
            'shoppingCart' => $this->getCustomerShoppingCart()
        ], function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return CustomerData
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param CustomerData|null $customer
     */
    public function setCustomer(CustomerData $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return CustomerBillingAddress
     */
    public function getCustomerBillingAddress()
    {
        return $this->customerBillingAddress;
    }

    /**
     * @param CustomerBillingAddress $customerBillingAddress
     */
    public function setCustomerBillingAddress(CustomerBillingAddress $customerBillingAddress)
    {
        $this->customerBillingAddress = $customerBillingAddress;
    }

    /**
     * @return  CustomerShippingAddress|null
     */
    public function getCustomerShippingAddress()
    {
        return $this->customerShippingAddress;
    }

    /**
     * @param CustomerShippingAddress|null $customerShippingAddress
     */
    public function setCustomerShippingAddress(CustomerShippingAddress $customerShippingAddress)
    {
        $this->customerShippingAddress = $customerShippingAddress;
    }

    /**
     * @return CustomerShoppingCart|null
     */
    public function getCustomerShoppingCart()
    {
        return $this->customerShoppingCart;
    }

    /**
     * @param CustomerShoppingCart|null $customerShoppingCart
     */
    public function setCustomerShoppingCart(CustomerShoppingCart $customerShoppingCart)
    {
        $this->customerShoppingCart = $customerShoppingCart;
    }
}