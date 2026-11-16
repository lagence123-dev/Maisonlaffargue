<?php

namespace MoneticoPaiement\Sdk\Utilities\Collections;

class PaymentServiceResponseValue
{

    // default language if current language is not found
    const DEFAULT_LANGUAGE = 'fr';

    const REDIRECT = [
        'fr'=>'Vous allez être redirigé dans quelques secondes',
        'en'=>'You\'ll be redirected in a few seconds',
    ];

    const REDIRECT_BTN = [
        'fr'=>'Si vous n\'êtes pas redirigé, cliquez ici',
        'en'=>'If you are not redirected, click here',
    ];

    const SUCCESS = [
        'fr'=>'Authentification avec succès',
        'en'=>'Authentication succeeded',
    ];

    const FAIL = [
        'fr' => 'Authentification échouée',
        'en' => 'Authentication failed',
    ];
}