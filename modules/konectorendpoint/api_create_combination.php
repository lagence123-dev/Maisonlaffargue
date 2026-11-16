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

    $productReference = Tools::getValue('product_reference');
    if (empty($productReference)) {
        dieError('Missing required parameter: product_reference');
    }

    $idProduct = findProductIdByReference($productReference);
    if (!$idProduct) {
        dieError('Product not found with reference: ' . $productReference);
    }

    $attributesRaw = Tools::getValue('attributes');
    if (empty($attributesRaw)) {
        dieError('Missing required parameter: attributes');
    }

    $attributes = json_decode($attributesRaw, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($attributes)) {
        dieError('Invalid attributes format: must be a JSON array of {type, value} objects');
    }

    $product = new Product($idProduct);
    if (!Validate::isLoadedObject($product)) {
        dieError('Product not found with id: ' . $idProduct);
    }

    $result = createCombination($product, $attributes);

    header('Content-Type: application/json');
    echo json_encode([
        'status'  => 'success',
        'message' => 'Combination created successfully',
        'data'    => $result
    ]);
    die();
}

function createCombination($product, $attributes)
{
    $combination = new Combination();
    $combination->id_product       = $product->id;
    $combination->reference        = (string) Tools::getValue('reference', '');
    $combination->price            = (float) Tools::getValue('price', 0);
    $combination->weight           = (float) Tools::getValue('weight', 0);
    $combination->ean13            = (string) Tools::getValue('ean13', '');
    $combination->upc              = (string) Tools::getValue('upc', '');
    $combination->isbn             = (string) Tools::getValue('isbn', '');
    $combination->mpn              = (string) Tools::getValue('mpn', '');
    $combination->minimal_quantity = max(1, (int) Tools::getValue('minimal_quantity', 1));
    $combination->default_on       = (int)(bool) Tools::getValue('is_default', 0);
    $combination->available_date   = Tools::getValue('available_date', '0000-00-00');

    if (!$combination->add()) {
        dieError('Failed to create combination');
    }

    $attributeIds = resolveAttributes($attributes);
    $combination->setAttributes($attributeIds);

    $quantity = (int) Tools::getValue('quantity', 0);
    StockAvailable::setQuantity($product->id, $combination->id, $quantity);

    if ($combination->default_on) {
        Product::updateDefaultAttribute($product->id);
    }

    return [
        'id_product'     => $product->id,
        'id_combination' => $combination->id,
        'reference'      => $combination->reference,
        'attributes'     => $attributeIds,
        'price_impact'   => $combination->price,
        'quantity'       => $quantity,
        'is_default'     => (bool) $combination->default_on
    ];
}

// Pour chaque {"type": "Taille", "value": "M"}, trouve ou crée le groupe et la valeur
function resolveAttributes($attributes)
{
    $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
    $attributeIds  = [];

    foreach ($attributes as $index => $attr) {
        if (empty($attr['type']) || empty($attr['value'])) {
            dieError('Each attribute must have a "type" and a "value" (index ' . $index . ')');
        }

        $typeName  = trim($attr['type']);
        $valueName = trim($attr['value']);
        $groupType = !empty($attr['group_type']) ? $attr['group_type'] : 'select';

        $groupId = findOrCreateAttributeGroup($typeName, $groupType, $defaultLangId);
        $attrId  = findOrCreateAttributeValue($valueName, $groupId, $defaultLangId);

        $attributeIds[] = $attrId;
    }

    return $attributeIds;
}

function findOrCreateAttributeGroup($name, $groupType, $defaultLangId)
{
    $sql = 'SELECT agl.id_attribute_group FROM ' . _DB_PREFIX_ . 'attribute_group_lang agl WHERE agl.name = "' . pSQL($name) . '" AND agl.id_lang = ' . (int) $defaultLangId;

    $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    if (!empty($rows)) {
        return (int) $rows[0]['id_attribute_group'];
    }

    $group = new AttributeGroup();
    $group->name           = [$defaultLangId => $name];
    $group->public_name    = [$defaultLangId => $name];
    $group->group_type     = in_array($groupType, ['select', 'radio', 'color']) ? $groupType : 'select';
    $group->is_color_group = ($groupType === 'color') ? 1 : 0;
    $group->position       = (int) AttributeGroup::getHigherPosition() + 1;

    if (!$group->add()) {
        dieError('Failed to create attribute group: ' . $name);
    }

    return (int) $group->id;
}

function findOrCreateAttributeValue($name, $groupId, $defaultLangId)
{
    $sql = 'SELECT al.id_attribute FROM ' . _DB_PREFIX_ . 'attribute_lang al INNER JOIN ' . _DB_PREFIX_ . 'attribute a ON a.id_attribute = al.id_attribute WHERE al.name = "' . pSQL($name) . '" AND al.id_lang = ' . (int) $defaultLangId . ' AND a.id_attribute_group = ' . (int) $groupId;

    $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    if (!empty($rows)) {
        return (int) $rows[0]['id_attribute'];
    }

    $attribute = new Attribute();
    $attribute->id_attribute_group = (int) $groupId;
    $attribute->name               = [$defaultLangId => $name];
    $attribute->position           = (int) Attribute::getHigherPosition($groupId) + 1;

    if (!$attribute->add()) {
        dieError('Failed to create attribute value: ' . $name);
    }

    return (int) $attribute->id;
}

/**
 * Recherche d'un id_product a partir d'une reference, en testant plusieurs champs.
 * Aligne sur la logique de api_update_stock.php : product.reference, puis
 * product_attribute.reference (variante existante), puis supplier_reference / ean13 / upc / isbn.
 * Retourne toujours l'id du produit parent (jamais l'id de combinaison).
 *
 * Note : pas de LIMIT (rejete par le serveur), on filtre cote PHP.
 */
function findProductIdByReference($reference)
{
    $ref = pSQL($reference);
    $db  = Db::getInstance(false);

    // 1) Reference produit parent (cas standard)
    $rows = $db->executeS('SELECT id_product FROM ' . _DB_PREFIX_ . 'product WHERE reference = "' . $ref . '" ORDER BY id_product DESC');
    if (!empty($rows)) {
        return (int) $rows[0]['id_product'];
    }

    // 2) Reference d'une combinaison existante : on remonte au parent
    $rows = $db->executeS('SELECT id_product FROM ' . _DB_PREFIX_ . 'product_attribute WHERE reference = "' . $ref . '" ORDER BY id_product DESC');
    if (!empty($rows)) {
        return (int) $rows[0]['id_product'];
    }

    // 3) Reference fournisseur / EAN13 / UPC / ISBN sur le produit
    $rows = $db->executeS('SELECT id_product FROM ' . _DB_PREFIX_ . 'product WHERE supplier_reference = "' . $ref . '" OR ean13 = "' . $ref . '" OR upc = "' . $ref . '" OR isbn = "' . $ref . '" ORDER BY id_product DESC');
    if (!empty($rows)) {
        return (int) $rows[0]['id_product'];
    }

    // 4) EAN13 / UPC / ISBN sur une combinaison
    $rows = $db->executeS('SELECT id_product FROM ' . _DB_PREFIX_ . 'product_attribute WHERE ean13 = "' . $ref . '" OR upc = "' . $ref . '" OR isbn = "' . $ref . '" ORDER BY id_product DESC');
    if (!empty($rows)) {
        return (int) $rows[0]['id_product'];
    }

    return null;
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
