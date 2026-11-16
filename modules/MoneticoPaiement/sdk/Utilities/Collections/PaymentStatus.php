<?php

namespace MoneticoPaiement\Sdk\Utilities\Collections;

abstract class PaymentStatus
{
    // PENDING
    const MP_PENDING = 'MP_PENDING';
    // REFUSED
    const MP_REFUSED_PAYMENT = 'MP_REFUSED_PAYMENT';
    // RECEIVED
    const MP_RECEIVED_PAYMENT = 'MP_RECEIVED_PAYMENT';
    const MP_RECEIVED_SPLIT_PAYMENT = [
        '1' => 'MP_RECEIVED_PAYMENT_1',
        '2' => 'MP_RECEIVED_PAYMENT_2',
        '3' => 'MP_RECEIVED_PAYMENT_3',
        '4' => 'MP_RECEIVED_PAYMENT_4',
    ];
    const MP_RECEIVED_PARTIAL_PAYMENT = 'MP_RECEIVED_PARTIAL_PAYMENT';
    // SANDBOX
    const MP_SANDBOX_PAYMENT = 'MP_SANDBOX_PAYMENT';
    // ANOMALIE
    const MP_WARNING = 'MP_WARNING';
    // ERROR
    const MP_ERROR_PAYMENT = 'MP_ERROR_PAYMENT';
    const MP_ERROR_SPLIT_PAYMENT = [
        '1' => 'MP_ERROR_PAYMENT_1',
        '2' => 'MP_ERROR_PAYMENT_2',
        '3' => 'MP_ERROR_PAYMENT_3',
        '4' => 'MP_ERROR_PAYMENT_4',
    ];
    const MP_ERROR_PAYMENT_ORDER_TOTAL = 'MP_ERROR_PAYMENT_ORDER_TOTAL';
    // REFUND
    const MP_PARTIAL_REFUND = 'MP_PARTIAL_REFUND';
    // DEMO
    const MP_DEMO_PAYMENT = 'MP_DEMO_PAYMENT';
    const MP_DEMO_RECEIVED_SPLIT_PAYMENT = 'MP_DEMO_RECEIVED_SPLIT_PAYMENT';
    const MP_DEMO_RECEIVED_PARTIAL_PAYMENT = 'MP_DEMO_RECEIVED_PARTIAL_PAYMENT';
    const MP_DEMO_CANCELED = 'MP_DEMO_CANCELED';

    const MP_TRANSLATED_STATUSES = [
        self::MP_PENDING => [
            'en' => 'Pending',
            'fr' => 'En attente',
        ],
        self::MP_REFUSED_PAYMENT => [
            'en' => 'Payment refused',
            'fr' => 'Paiement refusé',
        ],
        self::MP_RECEIVED_PAYMENT => [
            'en' => 'Payment accepted',
            'fr' => 'Paiement accepté',
        ],
        self::MP_RECEIVED_SPLIT_PAYMENT[1] => [
            'en' => 'Received instalment number 1',
            'fr' => 'Réception de l\'échéance numéro 1',
        ],
        self::MP_RECEIVED_SPLIT_PAYMENT[2] => [
            'en' => 'Received instalment number 2',
            'fr' => 'Réception de l\'échéance numéro 2',
        ],
        self::MP_RECEIVED_SPLIT_PAYMENT[3] => [
            'en' => 'Received instalment number 3',
            'fr' => 'Réception de l\'échéance numéro 3',
        ],
        self::MP_RECEIVED_SPLIT_PAYMENT[4] => [
            'en' => 'Payment completed',
            'fr' => ' Paiement terminé',
        ],
        self::MP_RECEIVED_PARTIAL_PAYMENT => [
            'en' => 'Partial recovery',
            'fr' => 'Recouvrement partiel',
        ],
        self::MP_WARNING => [
            'en' => 'Warning',
            'fr' => 'Attention',
        ],
        self::MP_ERROR_PAYMENT => [
            'en' => 'Error Payment',
            'fr' => 'Erreur de paiement',
        ],
        self::MP_ERROR_SPLIT_PAYMENT[1] => [
            'en' => 'Error first Payment',
            'fr' => 'Erreur du premier paiement',
        ],
        self::MP_ERROR_SPLIT_PAYMENT[2] => [
            'en' => 'Error second Payment',
            'fr' => 'Erreur du second paiement',
        ],
        self::MP_ERROR_SPLIT_PAYMENT[3] => [
            'en' => 'Error third Payment',
            'fr' => 'Erreur du troisième paiement',
        ],
        self::MP_ERROR_SPLIT_PAYMENT[4] => [
            'en' => 'Error fourth Payment',
            'fr' => 'Erreur du quatrième paiement',
        ],
        self::MP_ERROR_PAYMENT_ORDER_TOTAL => [
            'en' => 'Error Payment Order Total',
            'fr' => 'Erreur sur le total de la commande',
        ],
        self::MP_PARTIAL_REFUND => [
            'en' => 'Partial Refund',
            'fr' => 'Remboursement partiel',
        ],
        self::MP_DEMO_CANCELED => [
            'en' => 'Canceled',
            'fr' => 'Annulé',
        ],
    ];

    public static function setMultilingualName(&$order_state, $state_name, $languages, $suffix = null)
    {
        $translated_statuses = PaymentStatus::MP_TRANSLATED_STATUSES[$state_name];

        foreach ($languages as $language) {
            $iso = substr($language['locale'], 0, 2);
            $name = $translated_statuses[$iso] ?? $translated_statuses['en'];

            if (isset($suffix)) {
                $name .= ' ' . $suffix;
            }
            $order_state->name[$language['id_lang']] = $name;
        }
    }
}