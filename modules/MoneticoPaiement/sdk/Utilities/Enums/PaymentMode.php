<?php

declare(strict_types=1);

namespace MoneticoPaiement\Sdk\Utilities\Enums;

/**
 * @see \MoneticoPaiement\Sdk\Utilities\Collections\PaymentMode
 */
final class PaymentMode
{
    private function __construct()
    {
    }

    public const IMMEDIATE = 'IMMEDIATE';
    public const DEFERRED = 'DEFERRED';
    public const INSTALMENT = 'INSTALMENT';
    public const PARTIAL = 'PARTIAL';

    /**
     * @var array<self::*, array{en: string, fr: string}>
     */
    private static array $translations = [
        self::IMMEDIATE => [
            'en' => 'Immediate payment',
            'fr' => 'Paiement immédiat',
        ],

        self::DEFERRED => [
            'en' => 'Deferred payment',
            'fr' => 'Paiement différé',
        ],

        self::INSTALMENT => [
            'en' => 'Instalment payment',
            'fr' => 'Paiement fractionné',
        ],

        self::PARTIAL => [
            'en' => 'Partial payment',
            'fr' => 'Paiement partiel',
        ],
    ];

    /**
     * @param self::*   $mode
     * @param 'fr'|'en' $lang
     */
    public static function getTranslation(string $mode, string $lang): string
    {
        if ($mode === 'DIFFERED') {
            $mode = self::DEFERRED;
        }

        $defaultLang = 'fr';
        $mode = strtoupper($mode);

        return self::$translations[$mode][$lang] ?? self::$translations[$mode][$defaultLang];
    }
}