<?php

namespace MoneticoPaiement\Sdk\Utilities\Collections;

/**
 * @deprecated Use the PaymentMode enum instead
 * @see \MoneticoPaiement\Sdk\Utilities\Enums\PaymentMode
 */
abstract class PaymentMode
{
    const DEFAULT_LANGUAGE = 'fr';

    const IMMEDIATE = [
        'en' => 'Immediate payment',
        'fr' => 'Paiement immédiat',
    ];

    // Note: this is spelled wrong !
    // This should be DEFERRED, but beware, do not change, this is used both in Wordpress and Prestashop !
    const DIFFERED = [
        'en' => 'Deferred payment',
        'fr' => 'Paiement différé',
    ];

    const INSTALMENT = [
        'en' => 'Instalment payment',
        'fr' => 'Paiement fractionné',
    ];

    const PARTIAL = [
        'en' => 'Partial payment',
        'fr' => 'Paiement partiel',
    ];

    const TOUT_MODE = [
        'en' => 'Bank transfer payment',
        'fr' => 'Paiement par virement',
    ];
}