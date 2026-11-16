<?php
/**
 * Endpoint de debug : capture tout ce qui est recu par le module
 * et l'enregistre dans debug.log a cote du fichier, accessible via FTP/navigateur.
 *
 * Usage : remplace temporairement l'URL d'appel cote Node.js par celui-ci.
 * Exemple : .../modules/konectorendpoint/api_debug_capture.php
 *
 * A SUPPRIMER APRES DEBUG (expose les donnees recues).
 */

include_once('../../config/config.inc.php');
include_once('../../init.php');

$logFile = __DIR__ . '/debug.log';

// Capture brute du body HTTP (utile si Node envoie du JSON)
$rawBody = file_get_contents('php://input');

$capture = [
    'timestamp'        => date('Y-m-d H:i:s'),
    'method'           => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
    'content_type'     => $_SERVER['CONTENT_TYPE'] ?? 'none',
    'content_length'   => $_SERVER['CONTENT_LENGTH'] ?? 0,
    'query_string'     => $_SERVER['QUERY_STRING'] ?? '',
    'GET'              => $_GET,
    'POST'             => $_POST,
    'raw_body'         => $rawBody,
    'raw_body_hex'     => bin2hex($rawBody),
    'raw_body_length'  => strlen($rawBody),
];

// Test Tools::getValue sur les parametres attendus
$capture['Tools_getValue'] = [
    'apiKey'            => Tools::getValue('apiKey'),
    'product_reference' => Tools::getValue('product_reference'),
    'reference'         => Tools::getValue('reference'),
    'attributes'        => Tools::getValue('attributes'),
    'name'              => Tools::getValue('name'),
];

// Si product_reference a une valeur, on l'analyse en detail
$pr = Tools::getValue('product_reference');
if (!empty($pr)) {
    $capture['product_reference_analysis'] = [
        'value'      => $pr,
        'length'     => strlen($pr),
        'hex'        => bin2hex($pr),
        'trimmed'    => trim($pr),
        'trimmed_eq' => (trim($pr) === $pr) ? 'no_whitespace' : 'has_whitespace',
    ];

    // Test direct en BDD avec la valeur recue
    $sqlExact = 'SELECT id_product, reference FROM ' . _DB_PREFIX_ . 'product
                 WHERE reference = "' . pSQL($pr) . '" LIMIT 1';
    $sqlTrimmed = 'SELECT id_product, reference FROM ' . _DB_PREFIX_ . 'product
                   WHERE reference = "' . pSQL(trim($pr)) . '" LIMIT 1';

    $capture['db_test'] = [
        'sql_exact'     => $sqlExact,
        'result_exact'  => Db::getInstance(false)->getRow($sqlExact),
        'sql_trimmed'   => $sqlTrimmed,
        'result_trimmed'=> Db::getInstance(false)->getRow($sqlTrimmed),
    ];
}

// Ecriture dans le fichier log
$logLine = "=== " . $capture['timestamp'] . " ===\n"
         . print_r($capture, true)
         . "\n\n";
@file_put_contents($logFile, $logLine, FILE_APPEND);

// Renvoi aussi dans la reponse HTTP
header('Content-Type: application/json');
echo json_encode([
    'status'  => 'debug',
    'message' => 'Request captured',
    'log_file' => $logFile,
    'capture' => $capture,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);