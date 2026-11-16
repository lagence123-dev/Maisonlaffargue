<?php

include_once('../../config/config.inc.php');
include_once('../../init.php');
include_once('./konectorendpoint.php');

try {
    handleRequest();
} catch (Exception $e) {
    dieError($e->getMessage());
}

function handleRequest()
{
    validateApiKey();

    $name = Tools::getValue('name');
    if (empty($name)) {
        dieError('Missing required parameter: name');
    }

    // Si la reference est fournie, verifier qu'aucun produit n'existe deja avec cette reference
    $reference = trim((string) Tools::getValue('reference', ''));
    if ($reference !== '') {
        $existingId = findProductIdByReference($reference);
        if ($existingId) {
            $existing = new Product($existingId);
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => 'success',
                'message' => 'Product already exists with reference: ' . $reference,
                'already_exists' => true,
                'data'    => [
                    'id_product' => (int) $existing->id,
                    'reference'  => $existing->reference,
                    'name'       => $existing->name,
                    'price'      => (float) $existing->price,
                    'active'     => (bool) $existing->active
                ]
            ]);
            die();
        }
    }

    $result = createProduct();

    header('Content-Type: application/json');
    echo json_encode([
        'status'  => 'success',
        'message' => 'Product created successfully',
        'already_exists' => false,
        'data'    => $result
    ]);
    die();
}

/**
 * Verifie l'existence d'un produit par sa reference.
 * Recherche dans product.reference uniquement (cle metier pour la creation).
 *
 * Note : pas de LIMIT (rejete par le serveur), on filtre cote PHP.
 */
function findProductIdByReference($reference)
{
    $rows = Db::getInstance(false)->executeS('SELECT id_product FROM ' . _DB_PREFIX_ . 'product WHERE reference = "' . pSQL($reference) . '" ORDER BY id_product DESC');
    return !empty($rows) ? (int) $rows[0]['id_product'] : null;
}

function createProduct()
{
    $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');

    $product = new Product();

    // name: chaîne simple ou JSON multilingue {"1":"Nom FR","2":"Name EN"}
    $product->name = parseMultilingual(Tools::getValue('name'), $defaultLangId);

    // link_rewrite est obligatoire dans PrestaShop
    $product->link_rewrite = [];
    foreach ($product->name as $langId => $langName) {
        $product->link_rewrite[$langId] = Tools::str2url($langName);
    }

    $product->description       = parseMultilingual(Tools::getValue('description'), $defaultLangId, '');
    $product->description_short = parseMultilingual(Tools::getValue('description_short'), $defaultLangId, '');
    $product->meta_title        = parseMultilingual(Tools::getValue('meta_title'), $defaultLangId, '');
    $product->meta_description  = parseMultilingual(Tools::getValue('meta_description'), $defaultLangId, '');

    $product->reference           = (string) Tools::getValue('reference', '');
    $product->price               = (float) Tools::getValue('price', 0);
    $product->id_category_default = (int) Tools::getValue('id_category_default', (int) Configuration::get('PS_HOME_CATEGORY'));
    $product->id_tax_rules_group  = (int) Tools::getValue('id_tax_rules_group', 0);
    $product->ean13               = (string) Tools::getValue('ean13', '');
    $product->upc                 = (string) Tools::getValue('upc', '');
    $product->isbn                = (string) Tools::getValue('isbn', '');
    $product->mpn                 = (string) Tools::getValue('mpn', '');
    $product->weight              = (float) Tools::getValue('weight', 0);
    $product->active              = (int)(bool) Tools::getValue('active', 0);
    $product->available_for_order = (int)(bool) Tools::getValue('available_for_order', 1);
    $product->minimal_quantity    = max(1, (int) Tools::getValue('minimal_quantity', 1));
    $product->show_price          = 1;
    $product->indexed             = 1;
    $product->visibility          = Tools::getValue('visibility', 'both');
    $product->id_supplier         = (int) Tools::getValue('id_supplier', 0);
    $product->id_manufacturer     = (int) Tools::getValue('id_manufacturer', 0);

    if (!$product->add()) {
        dieError('Failed to create product');
    }

    $product->addToCategories([$product->id_category_default]);

    $quantity = (int) Tools::getValue('quantity', 0);
    StockAvailable::setQuantity($product->id, 0, $quantity);

    return [
        'id_product' => $product->id,
        'reference'  => $product->reference,
        'name'       => $product->name,
        'price'      => $product->price,
        'quantity'   => $quantity,
        'active'     => (bool) $product->active
    ];
}

// Accepte une chaîne simple ou un JSON encodé {"1":"valeur FR","2":"valeur EN"}
function parseMultilingual($raw, $defaultLangId, $default = '')
{
    if (empty($raw)) {
        return [$defaultLangId => $default];
    }
    $decoded = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        return $decoded;
    }
    return [$defaultLangId => $raw];
}

function validateApiKey()
{
    $apiKey = Tools::getValue('apiKey');

    if (empty($apiKey)) {
        dieError('No api key provided');
    }

    $expectedApiKey = Configuration::get('KONECTORENDPOINT_API_KEY');
    if (empty($expectedApiKey)) {
        dieError('API key not configured in module settings');
    }

    if ($expectedApiKey !== $apiKey) {
        dieError('Invalid API key');
    }
}

function dieError($message)
{
    $response = [
        'status'  => 'error',
        'message' => $message
    ];
    header('Content-Type: application/json', true, 400);
    echo json_encode($response);
    die();
}
