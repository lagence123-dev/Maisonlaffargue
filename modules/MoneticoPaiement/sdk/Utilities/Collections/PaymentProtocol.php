<?php

namespace MoneticoPaiement\Sdk\Utilities\Collections;

abstract class PaymentProtocol
{
    const DEFAULT_LANGUAGE = 'fr';

    const PROTOCOL = [
        'cb' => [
            'fr' => 'Carte de paiement',
            'en' => 'Payment card'
        ],
        'ipa' => [
            'fr' => 'Virement',
            'en' => 'Bank transfer'
        ],
        'paypal' => [
            'fr' => 'Paypal',
            'en' => 'Paypal'
        ],
        'lyfpay' => [
            'fr' => 'Lyf Pay',
            'en' => 'Lyf Pay'
        ],
        '3xcb' => [
            'fr' => '3xCB Cofidis',
            'en' => '3xCB Cofidis'
        ],
        '4xcb' => [
            'fr' => '4xCB Cofidis',
            'en' => '4xCB Cofidis'
        ],
        '5-10-12xcb' => [
            'fr' => '5-12xCB Cofidis',
            'en' => '5-12xCB Cofidis'
        ],
        'paylater' => [
            'fr' => 'Paylater Cofidis',
            'en' => 'Paylater Cofidis'
        ],
        'loan' => [
            'fr' => 'Cofidis',
            'en' => 'Cofidis'
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

    const COFIDIS_PROTOCOL_TRANS = [
        '3xcb' => [
            'fr' => 'Paiement en 3xCB avec Cofidis',
        ],
        '4xcb' => [
            'fr' => 'Paiement en 4xCB avec Cofidis',
        ],
        '5-10-12xcb' => [
            'fr' => 'Paiement de 5 à 12xCB avec Cofidis',
        ],
        'paylater' => [
            'fr' => 'Achetez le maintenant et payez plus tard avec Cofidis',
        ],
        'loan' => [
            'fr' => 'Paiement en plusieurs fois avec Cofidis',
        ],
    ];

    const COFIDIS_PROTOCOLS = ['3xcb', '4xcb', '5-10-12xcb', 'paylater', 'loan'];

    public static function getCofidisProtocols()
    {
        return self::COFIDIS_PROTOCOLS;
    }

    public static function getCofidisProtocolsNames()
    {
        return self::COFIDIS_PROTOCOL_TRANS;
    }

    /**
     * @param string $lang Two-digit lowercase locale. Actually only work with "fr".
     *
     * @return array<string, string> Keys are the Cofidis protocols, values are their translated names in the given language.
     */
    public static function getCofidisProtocolsTranslatedNames(string $lang = self::DEFAULT_LANGUAGE): array
    {
        $translated_names = [];
        foreach (self::COFIDIS_PROTOCOL_TRANS as $key => $protocol) {
            $translated_names[$key] = $protocol[$lang];
        }
        return $translated_names;
    }
}