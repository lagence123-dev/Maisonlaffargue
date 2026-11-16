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
    // Vérification de l'authentification
    validateApiKey();
    
    // API GET simple et efficace
    $sku = Tools::getValue('sku');
    $quantity = Tools::getValue('quantity');
    
    // Valider les paramètres requis
    if (empty($sku)) {
        dieError('Missing required parameter: sku');
    }
    
    if (!isset($quantity) || !is_numeric($quantity) || $quantity < 0) {
        dieError('Missing or invalid parameter: quantity (must be >= 0)');
    }
    
    $quantity = (int) $quantity;
    
    // Traitement de la mise à jour
    $productData = [
        'sku' => $sku,
        'quantity' => $quantity
    ];
    
    $result = updateProductStock($productData, 0);
    
    if ($result['success']) {
        $response = [
            'status' => 'success',
            'message' => 'Stock updated successfully',
            'data' => $result
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => $result['error'],
            'sku' => $sku
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    die();
}

function updateProductStock($productData, $index)
{
    // Valider les données du produit
    if (!isset($productData['sku']) || !isset($productData['quantity'])) {
        return [
            'index' => $index,
            'sku' => $productData['sku'] ?? 'unknown',
            'success' => false,
            'error' => 'Missing sku or quantity'
        ];
    }
    
    $sku = trim($productData['sku']);
    $quantity = $productData['quantity'];
    
    // Valider que la quantité est un nombre
    if (!is_numeric($quantity) || $quantity < 0) {
        return [
            'index' => $index,
            'sku' => $sku,
            'success' => false,
            'error' => 'Quantity must be a positive number'
        ];
    }
    
    $quantity = (int) $quantity;
    
    try {
        // Rechercher le produit par SKU
        $productInfo = findProductBySku($sku);
        
        if (!$productInfo) {
            return [
                'index' => $index,
                'sku' => $sku,
                'success' => false,
                'error' => 'Product not found with this SKU'
            ];
        }
        
        // Mettre à jour le stock
        $updateSuccess = updateStock($productInfo, $quantity);
        
        if ($updateSuccess) {
            return [
                'index' => $index,
                'sku' => $sku,
                'product_id' => $productInfo['id_product'],
                'combination_id' => $productInfo['id_product_attribute'],
                'old_quantity' => $productInfo['old_quantity'],
                'new_quantity' => $quantity,
                'success' => true
            ];
        } else {
            return [
                'index' => $index,
                'sku' => $sku,
                'success' => false,
                'error' => 'Failed to update stock in database'
            ];
        }
        
    } catch (Exception $e) {
        return [
            'index' => $index,
            'sku' => $sku,
            'success' => false,
            'error' => 'Exception: ' . $e->getMessage()
        ];
    }
}

function findProductBySku($sku)
{
    // D'abord chercher dans les combinaisons (variantes)
    $sql = 'SELECT pa.id_product_attribute, pa.id_product, sa.quantity
            FROM ' . _DB_PREFIX_ . 'product_attribute pa
            LEFT JOIN ' . _DB_PREFIX_ . 'stock_available sa 
                ON sa.id_product = pa.id_product 
                AND sa.id_product_attribute = pa.id_product_attribute
            WHERE pa.reference = "' . pSQL($sku) . '"';
    
    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    
    if ($result) {
        return [
            'id_product' => (int) $result['id_product'],
            'id_product_attribute' => (int) $result['id_product_attribute'],
            'old_quantity' => (int) $result['quantity'],
            'is_combination' => true
        ];
    }
    
    // Si pas trouvé dans les combinaisons, chercher dans les produits simples
    $sql = 'SELECT p.id_product, sa.quantity
            FROM ' . _DB_PREFIX_ . 'product p
            LEFT JOIN ' . _DB_PREFIX_ . 'stock_available sa 
                ON sa.id_product = p.id_product 
                AND sa.id_product_attribute = 0
            WHERE p.reference = "' . pSQL($sku) . '"';
    
    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    
    if ($result) {
        return [
            'id_product' => (int) $result['id_product'],
            'id_product_attribute' => 0,
            'old_quantity' => (int) $result['quantity'],
            'is_combination' => false
        ];
    }
    
    // Chercher aussi par EAN13, UPC, ISBN comme fallback
    $sql = 'SELECT p.id_product, sa.quantity
            FROM ' . _DB_PREFIX_ . 'product p
            LEFT JOIN ' . _DB_PREFIX_ . 'stock_available sa 
                ON sa.id_product = p.id_product 
                AND sa.id_product_attribute = 0
            WHERE p.ean13 = "' . pSQL($sku) . '" 
               OR p.upc = "' . pSQL($sku) . '" 
               OR p.isbn = "' . pSQL($sku) . '"';
    
    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    
    if ($result) {
        return [
            'id_product' => (int) $result['id_product'],
            'id_product_attribute' => 0,
            'old_quantity' => (int) $result['quantity'],
            'is_combination' => false
        ];
    }
    
    return null;
}

function updateStock($productInfo, $newQuantity)
{
    $idProduct = $productInfo['id_product'];
    $idProductAttribute = $productInfo['id_product_attribute'];
    
    // Mettre à jour dans stock_available
    $sql = 'UPDATE ' . _DB_PREFIX_ . 'stock_available 
            SET quantity = ' . (int) $newQuantity . '
            WHERE id_product = ' . (int) $idProduct . ' 
            AND id_product_attribute = ' . (int) $idProductAttribute;
    
    $updateResult = Db::getInstance()->execute($sql);
    
    if ($updateResult) {
        // Si c'est une combinaison, mettre à jour aussi la table product_attribute
        if ($idProductAttribute > 0) {
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'product_attribute 
                    SET quantity = ' . (int) $newQuantity . '
                    WHERE id_product_attribute = ' . (int) $idProductAttribute;
            Db::getInstance()->execute($sql);
        }
        
        // Mettre à jour aussi la table product pour les produits simples
        if ($idProductAttribute == 0) {
            $sql = 'UPDATE ' . _DB_PREFIX_ . 'product 
                    SET quantity = ' . (int) $newQuantity . '
                    WHERE id_product = ' . (int) $idProduct;
            Db::getInstance()->execute($sql);
        }
        
        // Recalculer le stock total du produit principal si c'est une variante
        if ($idProductAttribute > 0) {
            updateMainProductStock($idProduct);
        }
        
        return true;
    }
    
    return false;
}

function updateMainProductStock($idProduct)
{
    // Calculer le stock total de toutes les variantes
    $sql = 'SELECT SUM(quantity) as total_quantity 
            FROM ' . _DB_PREFIX_ . 'stock_available 
            WHERE id_product = ' . (int) $idProduct . ' 
            AND id_product_attribute > 0';
    
    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    $totalQuantity = $result ? (int) $result['total_quantity'] : 0;
    
    // Mettre à jour le stock du produit principal
    $sql = 'UPDATE ' . _DB_PREFIX_ . 'stock_available 
            SET quantity = ' . $totalQuantity . '
            WHERE id_product = ' . (int) $idProduct . ' 
            AND id_product_attribute = 0';
    
    Db::getInstance()->execute($sql);
}

function validateApiKey()
{
    $apiKey = Tools::getValue('apiKey');
    if (is_null($apiKey) || empty($apiKey)) {
        dieError('No api key provided');
    }

    // Récupérer la clé API configurée
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
        'status' => 'error',
        'message' => $message
    ];
    header('Content-Type: application/json', true, 400);
    echo json_encode($response);
    die();
}