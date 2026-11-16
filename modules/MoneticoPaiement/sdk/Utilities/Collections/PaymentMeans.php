<?php

namespace MoneticoPaiement\Sdk\Utilities\Collections;

abstract class PaymentMeans
{
    const COFIDIS_1_EURO = '1euro';
    const COFIDIS_3X_CB = '3xcb';
    const COFIDIS_4X_CB = '4xcb';
    const COFIDIS_5_10_12XCB = '5-10-12xcb';
    const COFIDIS_PAYLATER = 'paylater';
    const COFIDIS_LOAN = 'loan';
    const PAYPAL = 'paypal';
    const LYFPAY = 'lyfpay';
}