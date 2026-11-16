<?php

use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Sdk\Collections\PaymentEnvironment;

class MoneticoPaiementOverride extends MoneticoPaiement
{
    public function getPaymentState($order_id)
    {
        $order = new Order($order_id);
        $transaction_id = MoneticoPaiementHelper::getTransactionIdByCartId($order->id_cart);
        if ($transaction_id > 0) {
            return parent::getPaymentState($order_id);
        }

        // Return error
        $url = PaymentEnvironment::API_URI . '/' . PaymentEnvironment::PAYMENT_STATE;
        return [
            'success' => false,
            'request_value' => "",
            'response_value' => [
                'desc' => 'No transaction',
                'rmk' => '( URL= ' . $url . ')',
            ]
        ];
    }
}