<?php
/**
 * Version TRACEE de api_create_product.php
 * Identique au code de production mais avec un trace dans la reponse
 * pour identifier exactement pourquoi la verification d'existence echoue.
 *
 * Usage : appelle .../modules/konectorendpoint/api_create_product_traced.php
 *         avec les memes parametres que api_create_product.php
 *
 * NE CREE PAS LE PRODUIT : retourne uniquement le trace.
 */

include_once('../../config/config.inc.php');
include_once('../../init.php');

$trace = [];

try {
    handleRequest();
} catch (Exception $e) {
    $trace[] = ['EXCEPTION' => $e->getMessage()];
    respond('error', $e->getMessage());
}

function handleRequest()
{
    global $trace;

    validateApiKey();
    $trace[] = ['step' => 'apiKey OK'];

    $name = Tools::getValue('name');
    $trace[] = ['step' => 'name received', 'value' => $name, 'empty' => empty($name)];
    if (empty($name)) {
        respond('error', 'Missing required parameter: name');
    }

    // Recuperation reference - exactement comme le code de production
    $referenceRaw = Tools::getValue('reference', '');
    $trace[] = [
        'step'          => 'reference raw',
        'value'         => $referenceRaw,
        'type'          => gettype($referenceRaw),
        'length'        => is_string($referenceRaw) ? strlen($referenceRaw) : 'N/A',
        'hex'           => is_string($referenceRaw) ? bin2hex($referenceRaw) : 'N/A',
    ];

    $reference = trim((string) $referenceRaw);
    $trace[] = [
        'step'          => 'reference trimmed',
        'value'         => $reference,
        'length'        => strlen($reference),
        'hex'           => bin2hex($reference),
        'is_empty'      => ($reference === ''),
        'condition_ok'  => ($reference !== ''),
    ];

    if ($reference !== '') {
        $trace[] = ['step' => 'ENTERING duplicate check block'];

        $existingId = findProductIdByReference($reference);
        $trace[] = [
            'step'        => 'findProductIdByReference result',
            'value'       => $existingId,
            'type'        => gettype($existingId),
            'is_truthy'   => (bool) $existingId,
        ];

        if ($existingId) {
            $trace[] = ['step' => 'DUPLICATE FOUND - would return existing'];
            respond('success', 'Product already exists', [
                'already_exists' => true,
                'id_product'     => $existingId,
                'trace'          => $trace,
            ]);
        } else {
            $trace[] = ['step' => 'NO DUPLICATE - would proceed to creation'];
        }
    } else {
        $trace[] = ['step' => 'reference is empty - skipping duplicate check'];
    }

    // On NE CREE PAS le produit, on retourne juste le trace
    respond('debug', 'Trace complete - product NOT created', [
        'trace' => $trace,
    ]);
}

function findProductIdByReference($reference)
{
    global $trace;

    $sql = 'SELECT id_product FROM ' . _DB_PREFIX_ . 'product WHERE reference = "' . pSQL($reference) . '" ORDER BY id_product DESC LIMIT 1';

    $trace[] = ['step' => 'findProductIdByReference SQL', 'sql' => $sql];

    $db  = Db::getInstance(false);
    $row = $db->getRow($sql);

    $trace[] = [
        'step'    => 'findProductIdByReference raw result',
        'row'     => $row,
        'type'    => gettype($row),
        'is_false'=> ($row === false),
        'is_array'=> is_array($row),
        'db_error'=> $db->getMsgError(),
    ];

    // Bonus : on liste TOUS les produits avec cette reference
    $allMatches = $db->executeS('SELECT id_product, reference, active, date_add FROM ' . _DB_PREFIX_ . 'product WHERE reference = "' . pSQL($reference) . '"');
    $trace[] = [
        'step'       => 'all products with this reference',
        'count'      => is_array($allMatches) ? count($allMatches) : 0,
        'matches'    => $allMatches,
    ];

    return $row ? (int) $row['id_product'] : null;
}

function validateApiKey()
{
    $apiKey = Tools::getValue('apiKey');
    if (empty($apiKey)) {
        respond('error', 'No api key provided');
    }
    $expected = Configuration::get('KONECTORENDPOINT_API_KEY');
    if (empty($expected) || $expected !== $apiKey) {
        respond('error', 'Invalid or missing API key config');
    }
}

function respond($status, $message, $extra = [])
{
    global $trace;
    header('Content-Type: application/json');
    echo json_encode(array_merge([
        'status'  => $status,
        'message' => $message,
        'trace'   => $trace,
    ], $extra), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    die();
}
