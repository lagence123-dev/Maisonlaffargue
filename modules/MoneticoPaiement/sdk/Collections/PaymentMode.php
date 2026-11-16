<?php

namespace MoneticoPaiement\Sdk\Collections;


abstract class PaymentMode
{
    const DEFAULT_LANGUAGE = 'fr';

    const IMMEDIATE = [
        'en' => 'Immediate payment',
        'fr' => 'Paiement immédiat',
    ];
    const DIFFERED = [
        'en' => 'Differed payment',
        'fr' => 'Paiement différé',
    ];

    const INSTALMENT = [
        'en' => 'Instalment payment',
        'fr' => 'Paiement fractionné',
    ];

}