<?php

declare(strict_types=1);

namespace MoneticoPaiement\Sdk\Utilities\Enums;

final class PaymentProtocol
{
    private function __construct()
    {
    }

    public const cb = 'cb';
    public const paypal = 'paypal';
    public const lyfpay = 'lyfpay';
    public const cofidis_3xcb = '3xcb';
    public const cofidis_4xcb = '4xcb';
    public const cofidis_5_10_12xcb = '5-10-12xcb';
    public const cofidis_paylater = 'paylater';
    public const cofidis_loan = 'loan';
    public const giropay = 'giropay';
    public const sofort = 'sofort';
    public const ipa = 'ipa';
}