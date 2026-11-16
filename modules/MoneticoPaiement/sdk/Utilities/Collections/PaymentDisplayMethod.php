<?php
namespace MoneticoPaiement\Sdk\Utilities\Collections;

class PaymentDisplayMethod
{
    // default language if current language is not found
    const DEFAULT_LANGUAGE = 'fr';

    const deported = [
        'en' => 'Deported',
        'fr' => 'Déporté',
    ];

    const hostedfields = [
        'en' => 'Integrated',
        'fr' => 'Intégré',
    ];
}