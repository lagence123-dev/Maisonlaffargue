<?php

namespace MoneticoPaiement\Sdk\Utilities\Collections;

abstract class PaymentEnvironment
{
    // URI
    const URI = 'https://p.monetico-services.com/';
    const SANDBOX_URI = 'https://p.monetico-services.com/test/';
    const API_PAYMENT_PROD_URI = "https://payment-api.e-i.com/";
    const API_PAYMENT_SANDBOX_URI = "https://payment-api.e-i.com/test/";
    const API_PAYMENT_V2_PROD_URI = "https://payment-api.e-i.com/";
    const API_PAYMENT_V2_SANDBOX_URI = "https://payment-api.e-i.com/test/";

    const API_URI = "https://api-nc.e-i.com/";
    const API_THEMIS_URI = 'https://api.e-i.com/EMCI_THEMIS_VERSIONING';

    const PAYMENT_FORM = 'paiement.cgi';

    // API V1
    const PAYMENT_CAPTURE = 'capture_paiement.cgi';
    const PAYMENT_RECREDIT = 'recredit_paiement.cgi';
    const PAYMENT_STATE = 'etatpaiement.cgi';

    // API V2
    const PAYMENT_SERVICE_CGI = 'paymentservice.cgi';
    const PAYMENT_MEAN_TOKEN_GENERATOR_CGI = 'paymentmeantokengenerator.cgi';
    const STATSCMS = 'EMC2_STATSCMS/api/usage-report';
    const PAYMENT_SESSION_API = 'payment-session-api/payment-session';
    // HOSTEDFIELDS
    const SDK_HOSTEDFIELDS = 'hostedfields/hostedfields.js';
    // IPA
    const SDK_IPA = 'js/payment-component.min.js';
    // BOARD
    const BOARD = 'https://www.monetico-services.com';
    // DOWNLOAD PAGE
    const DOWNLOAD_PAGE = 'https://www.monetico-paiement.fr/fr/installer/telechargements/kit_telechargeable.aspx';
    // LOGOS TDBCOM
    const MONETICO_TDB_LOGO_URL = 'https://www.monetico-services.com/root/images/appli/tdbcom/';
}