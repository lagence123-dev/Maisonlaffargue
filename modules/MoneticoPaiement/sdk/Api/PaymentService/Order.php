<?php

namespace MoneticoPaiement\Sdk\Api\PaymentService;

use MoneticoPaiement\Sdk\Utilities\Models\OrderContext;
use MoneticoPaiement\Sdk\Utilities\Models\OrderModel;

class Order extends OrderModel
{
    /** @var OrderContext $context */
    private OrderContext $context;

    /**
     * @return array
     */
    public function toArray():array
    {
        return array_filter([
            'date' => $this->getDate()->format('Y-m-d\TH:i:s'),
            'customer' => $this->getCustomer() ? $this->getCustomer()->toArray() : null,
            'context' => $this->getContext()->jsonSerialize()
        ], function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return OrderContext
     */
    public function getContext(): OrderContext
    {
        return $this->context;
    }

    /**
     * @param OrderContext $context
     *
     * @return void
     */
    public function setContext(OrderContext $context): void
    {
        $this->context = $context;
    }

}