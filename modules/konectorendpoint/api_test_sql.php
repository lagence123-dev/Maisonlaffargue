<?php
include_once('../../config/config.inc.php');
include_once('../../init.php');

header('Content-Type: application/json');

$db = Db::getInstance(false);

$result = [
    'db_prefix' => _DB_PREFIX_,
    'db_name' => _DB_NAME_,
    'test_query' => $db->getRow('SELECT COUNT(*) as nb FROM ' . _DB_PREFIX_ . 'product'),
    'find_LSACL15_exact' => $db->getRow('SELECT id_product, reference FROM ' . _DB_PREFIX_ . 'product WHERE reference = "LSACL15"'),
    'find_LSACL15_like' => $db->executeS('SELECT id_product, reference FROM ' . _DB_PREFIX_ . 'product WHERE reference LIKE "%LSACL%" LIMIT 10'),
    'find_in_attributes' => $db->executeS('SELECT id_product, reference FROM ' . _DB_PREFIX_ . 'product_attribute WHERE reference LIKE "%LSACL%" LIMIT 10'),
    'last_error' => $db->getMsgError(),
];

echo json_encode($result, JSON_PRETTY_PRINT);