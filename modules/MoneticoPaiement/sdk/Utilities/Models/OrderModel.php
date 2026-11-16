<?php

namespace MoneticoPaiement\Sdk\Utilities\Models;

use MoneticoPaiement\Sdk\Utilities\Models\Order\Customer;

class OrderModel
{
    /** @var \DateTime $date */
    private \DateTime $date;

    /** @var Customer|null $customer */
    private ?Customer $customer = null;

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    public function toArray():array
    {
        return array_filter([
            'date' => $this->getDate()->format('Y-m-d\TH:i:s'),
            'customer' => $this->getCustomer() ? $this->getCustomer()->toArray() : null
        ], function ($value) {
            return !is_null($value);
        });
    }

}