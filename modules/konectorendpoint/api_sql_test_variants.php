<?php
/**
 * Teste plusieurs variantes de la requete pour identifier ce qui plante.
 */
include_once('../../config/config.inc.php');
include_once('../../init.php');

header('Content-Type: application/json');

$db = Db::getInstance(false);

$tests = [
    'T1_no_orderby_no_limit' => 'SELECT id_product FROM ps_product WHERE reference = "LSACL15"',
    'T2_with_limit_only'     => 'SELECT id_product FROM ps_product WHERE reference = "LSACL15" LIMIT 1',
    'T3_with_orderby_only'   => 'SELECT id_product FROM ps_product WHERE reference = "LSACL15" ORDER BY id_product DESC',
    'T4_full_query'          => 'SELECT id_product FROM ps_product WHERE reference = "LSACL15" ORDER BY id_product DESC LIMIT 1',
    'T5_backticks'           => 'SELECT `id_product` FROM `ps_product` WHERE `reference` = "LSACL15" ORDER BY `id_product` DESC LIMIT 1',
    'T6_single_quotes'       => "SELECT id_product FROM ps_product WHERE reference = 'LSACL15' ORDER BY id_product DESC LIMIT 1",
    'T7_limit_with_offset'   => 'SELECT id_product FROM ps_product WHERE reference = "LSACL15" ORDER BY id_product DESC LIMIT 0, 1',
    'T8_no_where_limit'      => 'SELECT id_product FROM ps_product ORDER BY id_product DESC LIMIT 1',
    'T9_count_only'          => 'SELECT COUNT(*) as nb FROM ps_product WHERE reference = "LSACL15"',
];

$results = [];
$sqlMode = $db->getRow('SELECT @@SESSION.sql_mode as mode');

foreach ($tests as $name => $sql) {
    $row = $db->getRow($sql);
    $results[$name] = [
        'sql'      => $sql,
        'row'      => $row,
        'is_false' => ($row === false),
        'error'    => $db->getMsgError(),
    ];
}

echo json_encode([
    'sql_mode'        => $sqlMode,
    'mysql_version'   => $db->getRow('SELECT VERSION() as v'),
    'tests'           => $results,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
