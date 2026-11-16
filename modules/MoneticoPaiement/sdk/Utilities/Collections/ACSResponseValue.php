<?php

namespace MoneticoPaiement\Sdk\Utilities\Collections;

class ACSResponseValue
{
    // default language if current language is not found
    const DEFAULT_LANGUAGE = 'fr';

    const DEFAULT_RESPONSE = [
        'fr' => 'Un problème est survenu et votre paiement n\'a pas pu aboutir, veuillez réessayer.',
    ];

    const RESPONSE = [
        '0' => [
            'fr' => 'Votre tentative de paiement a été refusée, veuillez réessayer.'
        ],
        '-4' => [
            'fr' => 'La date d\'expiration de la carte de paiement est invalide, veuillez vérifier votre saisie. ',
        ],
        '-5' => [
            'fr' => 'Le numéro de la carte de paiement est invalide, veuillez vérifier votre saisie.',
        ],
        '-6' => [
            'fr' => 'Votre tentative de paiement a expiré. Veuillez recharger la page.',
        ],
        '-9' => [
            'fr' => 'Le cryptogramme visuel est invalide.',
        ],
        '-10' => [
            'fr' => 'Le paiement a déjà été effectué.',
        ],
        '-11' => [
            'fr' => 'Le paiement a déjà été effectué.',
        ],
        '-12' => [
            'fr' => 'Le paiement a été annulé.',
        ],
        '-13' => [
            'fr' => 'Le paiement est en cours de traitement.',
        ],
        '-14' => [
            'fr' => 'Le nombre maximal de tentatives de paiement a été atteint. Veuillez recharger la page et réessayer.',
        ],
        '-21' => [
            'fr' => 'Votre tentative de paiement a été refusée, veuillez réessayer.',
        ],
        '-22' => [
            'fr' => 'Votre moyen de paiement enregistré est expiré.',
        ],
        '-23' => [
            'fr' => 'Votre tentative de paiement a été refusée, veuillez réessayer.',
        ],
        '-24' => [
            'fr' => 'Le cryptogramme visuel est manquant.',
        ],
        '-27' => [
            'fr' => 'Le réseau de la carte de paiement n’est pas accepté.',
        ],
    ];

    /**
     * @param string $error_number
     * @param string $defaultLang
     *
     * @return string
     */
    public static function getReturnCodeResponse($error_number, $defaultLang = self::DEFAULT_LANGUAGE)
    {
        $error_number = (string) $error_number;
        if (isset(self::RESPONSE[$error_number])) {
            return self::RESPONSE[$error_number][$defaultLang] ?? self::RESPONSE[$error_number][self::DEFAULT_LANGUAGE];
        }
        return self::DEFAULT_RESPONSE[$defaultLang] ?? self::DEFAULT_RESPONSE[self::DEFAULT_LANGUAGE];
    }
}