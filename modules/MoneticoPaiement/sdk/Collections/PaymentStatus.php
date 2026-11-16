<?php


namespace MoneticoPaiement\Sdk\Collections;


abstract class PaymentStatus
{
    // RECEIVED_PAYMENT
    const MP_RECEIVED_PAYMENT = 'MP_RECEIVED_PAYMENT';
    const MP_RECEIVED_SPLIT_PAYMENT = [
        '1' => 'MP_RECEIVED_PAYMENT_1',
        '2' => 'MP_RECEIVED_PAYMENT_2',
        '3' => 'MP_RECEIVED_PAYMENT_3',
        '4' => 'MP_RECEIVED_PAYMENT_4',
    ];
    // SANDBOX
    const MP_SANDBOX_PAYMENT = 'MP_SANDBOX_PAYMENT';
    // ERROR
    const MP_ERROR_PAYMENT = 'MP_ERROR_PAYMENT';
    const MP_ERROR_SPLIT_PAYMENT = [
        '1' => 'MP_ERROR_PAYMENT_1',
        '2' => 'MP_ERROR_PAYMENT_2',
        '3' => 'MP_ERROR_PAYMENT_3',
        '4' => 'MP_ERROR_PAYMENT_4',
    ];
    const MP_ERROR_PAYMENT_ORDER_TOTAL = 'MP_ERROR_PAYMENT_ORDER_TOTAL';
    // REFUND
    const MP_PARTIAL_REFUND = 'MP_PARTIAL_REFUND';
}