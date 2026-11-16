<?php
/**
 * Diagnostic exhaustif : meme code que api_test_sql, meme requete,
 * mais avec affichage de TOUS les parametres de connexion BDD.
 *
 * Compare la sortie de ce fichier avec celle de api_test_sql.php :
 * si les db_name/db_server/db_prefix sont identiques mais le resultat differe,
 * c'est qu'il y a un probleme tres specifique a creuser.
 */

include_once('../../config/config.inc.php');
include_once('../../init.php');

header('Content-Type: application/json');

$db = Db::getInstance(false);

// Reproduit EXACTEMENT la meme requete que api_test_sql.php
$sqlExact = 'SELECT id_product, reference FROM ' . _DB_PREFIX_ . 'product WHERE reference = "LSACL15"';

// Test avec valeur passee en parametre GET (pour reproduire le cas du bug)
$prFromParam = Tools::getValue('product_reference', 'LSACL15');
$sqlFromParam = 'SELECT id_product, reference FROM ' . _DB_PREFIX_ . 'product WHERE reference = "' . pSQL($prFromParam) . '"';

// Verification que la connexion est bien etablie
$dbLinkOk = false;
try {
    $db->getLink();
    $dbLinkOk = true;
} catch (Exception $e) {
    $dbLinkErr = $e->getMessage();
}

$result = [
    // Constantes de connexion
    'constants' => [
        '_DB_SERVER_'   => defined('_DB_SERVER_') ? _DB_SERVER_ : 'UNDEFINED',
        '_DB_NAME_'     => defined('_DB_NAME_') ? _DB_NAME_ : 'UNDEFINED',
        '_DB_USER_'     => defined('_DB_USER_') ? _DB_USER_ : 'UNDEFINED',
        '_DB_PREFIX_'   => defined('_DB_PREFIX_') ? _DB_PREFIX_ : 'UNDEFINED',
        '_PS_VERSION_'  => defined('_PS_VERSION_') ? _PS_VERSION_ : 'UNDEFINED',
        '_PS_ROOT_DIR_' => defined('_PS_ROOT_DIR_') ? _PS_ROOT_DIR_ : 'UNDEFINED',
    ],

    // Chemins resolus
    'paths' => [
        '__DIR__'             => __DIR__,
        '__FILE__'            => __FILE__,
        'config_realpath'     => realpath(__DIR__ . '/../../config/config.inc.php'),
        'init_realpath'       => realpath(__DIR__ . '/../../init.php'),
        'parameters_realpath' => realpath(__DIR__ . '/../../app/config/parameters.php'),
        'getcwd'              => getcwd(),
        'SCRIPT_FILENAME'     => $_SERVER['SCRIPT_FILENAME'] ?? null,
        'DOCUMENT_ROOT'       => $_SERVER['DOCUMENT_ROOT'] ?? null,
        'HTTP_HOST'           => $_SERVER['HTTP_HOST'] ?? null,
    ],

    // Capture brute de l'URL et de la query string pour detecter un rewriting
    'url_capture' => [
        'REQUEST_URI'   => $_SERVER['REQUEST_URI'] ?? null,
        'QUERY_STRING'  => $_SERVER['QUERY_STRING'] ?? null,
        'REQUEST_METHOD'=> $_SERVER['REQUEST_METHOD'] ?? null,
        'REDIRECT_URL'  => $_SERVER['REDIRECT_URL'] ?? null,
        'REDIRECT_QUERY_STRING' => $_SERVER['REDIRECT_QUERY_STRING'] ?? null,
        'PATH_INFO'     => $_SERVER['PATH_INFO'] ?? null,
        'ORIG_PATH_INFO'=> $_SERVER['ORIG_PATH_INFO'] ?? null,
        'GET_raw'       => $_GET,
        'POST_raw'      => $_POST,
        'REQUEST_raw'   => $_REQUEST,
        'GET_count'     => count($_GET),
        'GET_keys'      => array_keys($_GET),
    ],

    // Statut connexion
    'connection' => [
        'link_ok' => $dbLinkOk,
        'error'   => $dbLinkErr ?? null,
        'msg_error' => $db->getMsgError(),
    ],

    // Verification reelle : on liste les bases accessibles et la base courante
    'live_db' => [
        'current_database' => $db->getRow('SELECT DATABASE() as db'),
        'mysql_version'    => $db->getRow('SELECT VERSION() as v'),
        'all_databases'    => $db->executeS('SHOW DATABASES'),
    ],

    // La requete qui doit trouver LSACL15
    'queries' => [
        'sql_hardcoded' => $sqlExact,
        'result_hardcoded' => $db->getRow($sqlExact),
        'sql_from_param' => $sqlFromParam,
        'result_from_param' => $db->getRow($sqlFromParam),
        'param_received' => $prFromParam,
        'param_hex' => bin2hex($prFromParam),
    ],

    // Compte total + echantillon
    'sanity' => [
        'total_products' => $db->getRow('SELECT COUNT(*) as nb FROM ' . _DB_PREFIX_ . 'product'),
        'product_212'    => $db->getRow('SELECT id_product, reference, active FROM ' . _DB_PREFIX_ . 'product WHERE id_product = 212'),
        'show_tables_product' => $db->executeS('SHOW TABLES LIKE "%product%"'),
    ],
];

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
