<?php

namespace MoneticoPaiement\Sdk\Collections;

abstract class PaymentProtocol
{
    const DEFAULT_LANGUAGE = 'fr';

    const PROTOCOL = [
        'cb' => [
            'fr' => 'Carte de paiement',
            'en' => 'Payment card'
        ],
        'paypal' => [
            'fr' => 'Paypal',
            'en' => 'Paypal'
        ],
        'lyfpay' => [
            'fr' => 'Lyf Pay',
            'en' => 'Lyf Pay'
        ],
        '1euro' => [
            'fr' => '1euro.com avec Cofidis',
            'en' => '1euro.com with Cofidis'
        ],
        '3xcb' =>[
            'fr' => '3xCB avec Cofidis',
            'en' => '3xCB with Cofidis'
        ],
        '4xcb' => [
            'fr' => '4xCB avec Cofidis',
            'en' => '4xCB with Cofidis'
        ],
        'giropay' => [
            'fr' => 'Giropay',
            'en' => 'Giropay'
        ],
        'sofort' => [
            'fr' => 'Sofort',
            'en' => 'Sofort'
        ],
    ];
}