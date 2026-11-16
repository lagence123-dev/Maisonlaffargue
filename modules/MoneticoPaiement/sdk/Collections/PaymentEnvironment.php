<?php

namespace MoneticoPaiement\Sdk\Collections;

abstract class PaymentEnvironment
{
    // URI
    const URI = 'https://p.monetico-services.com';
    const API_URI = "https://payment-api.e-i.com";
    const SANDBOX_URI = 'https://p.monetico-services.com/test';
    const SANDBOX_API_URI = "https://payment-api.e-i.com/test";
    // PAYMENT
    const PAYMENT_FORM = 'paiement.cgi';
    const PAYMENT_CAPTURE = 'capture_paiement.cgi';
    const PAYMENT_RECREDIT = 'recredit_paiement.cgi';
    const PAYMENT_STATE = 'etatpaiement.cgi';
    // BOARD
    const BOARD = 'https://www.monetico-services.com';
}