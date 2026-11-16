<?php

namespace MoneticoPaiement\Sdk\Utilities\Traits;

use MoneticoPaiement\Sdk\Utilities\Collections\Currency;
use MoneticoPaiement\Sdk\Utilities\Collections\Language;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentAdditionalNetwork;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentDisplayMethod;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentMode;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentProtocol;
use MoneticoPaiement\Sdk\Utilities\Collections\ThreeDSecureChallengeValue;

trait WhiteList
{
    /**
     * @return string[]
     */
    public static function getCurrencyWhiteList()
    {
        return array_merge(Currency::PRINCIPAL_CURRENCIES, Currency::OTHER_CURRENCIES);
    }

    /**
     * @return array
     */
    public static function getLanguageWhiteList()
    {
        $payment_mode_class = new \ReflectionClass(Language::class);
        $pm_constants = $payment_mode_class->getConstants();
        $data_language = [];

        foreach ($pm_constants as $key => $payment_mode) {
            if ($key != 'DEFAULT_LANGUAGE') {
                $data_language[] = $key;
            }
        }

        return $data_language;
    }

    /**
     * @return array
     */
    public static function getPaymentModeWhiteList()
    {
        $payment_mode_class = new \ReflectionClass(PaymentMode::class);
        $pm_constants = $payment_mode_class->getConstants();
        $data_payment_mode = [];

        foreach ($pm_constants as $key => $payment_mode) {
            if ($key != 'DEFAULT_LANGUAGE') {
                $data_payment_mode[] = $key;
            }
        }

        return $data_payment_mode;
    }

    /**
     * @return array
     */
    public static function getCodeRetourWhiteList()
    {
        return [
            'payetest',
            'paiement',
            'Annulation',
            'paiement_pf[1]',
            'Annulation_pf[1]',
            'paiement_pf[2]',
            'Annulation_pf[2]',
            'paiement_pf[3]',
            'Annulation_pf[3]',
            'paiement_pf[4]',
            'Annulation_pf[4]',
            'attente_partenaire',
            'acceptance_refused',
            'acceptance_pending',
            'acceptance_successful'
        ];
    }

    /**
     * @return array
     */
    public static function getThreeDSecureChallengeValueWhiteList()
    {
        $payment_mode_class = new \ReflectionClass(ThreeDSecureChallengeValue::class);
        $pm_constants = $payment_mode_class->getConstants();
        $data_3dsecure_challenge = [];

        foreach ($pm_constants as $key => $payment_mode) {
            $data_3dsecure_challenge[] = $key;
        }

        return $data_3dsecure_challenge;
    }

    /**
     * @return array
     */
    public static function getPaymentProtocolWhiteList(){
        $protocols = PaymentProtocol::PROTOCOL;
        $data_protocols = [];
        foreach ($protocols as $key => $protocol){
            $data_protocols[] = $key;
        }
        return $data_protocols;
    }

    /**
     * @return array
     */
    public static function getPaymentAdditionalNetworkWhitelist(){
        $additional_cards = new \ReflectionClass(PaymentAdditionalNetwork::class);
        $ac_constants = $additional_cards->getConstants();
        $data_additional_cards  = [];
        foreach ($ac_constants as $key => $additional_card) {
            $data_additional_cards[] = $key;
        }
        return $data_additional_cards;
    }

    /**
     * @return array
     */
    public static function getPaymentDisplayMethodWhitelist()
    {
        $display_methods = new \ReflectionClass(PaymentDisplayMethod::class);
        $dm_constants = $display_methods->getConstants();
        $data_display_methods = [];
        foreach ($dm_constants as $key => $display_methods) {
            if ($key != 'DEFAULT_LANGUAGE') {
                $data_display_methods[] = $key;
            }
        }
        return $data_display_methods;
    }

}