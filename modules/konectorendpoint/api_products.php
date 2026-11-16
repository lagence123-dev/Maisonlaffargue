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
    KonectorEndpoint::allowApiRequest();

    $queryParams = getQueryParameters();

    $languages = Language::getLanguages();
    $products = Product::getProducts(
        $languages[0],
        $queryParams->fromId,
        $queryParams->limit,
        'id_product',
        'desc'
    );

    $productsMin = [];

    foreach ($products as $oneProduct) {
        $minProduct = [];
        $minProduct["id_product"] = $oneProduct['id_product'];
        $minProduct["ean13"] = $oneProduct['ean13'];
        $minProduct["isbn"] = $oneProduct['isbn'];
        $minProduct["upc"] = $oneProduct['upc'];
        $minProduct["mpn"] = $oneProduct['mpn'];
        $minProduct["quantity"] = $oneProduct['quantity'];
        $minProduct["minimal_quantity"] = $oneProduct['minimal_quantity'];
        $minProduct["low_stock_threshold"] = $oneProduct['low_stock_threshold'];
        $minProduct["price"] = $oneProduct['price'];
        $minProduct["reference"] = $oneProduct['reference'];
        $minProduct["supplier_reference"] = $oneProduct['supplier_reference'];
        $minProduct["location"] = $oneProduct['location'];
        $minProduct["customizable"] = $oneProduct['customizable'];
        $minProduct["uploadable_files"] = $oneProduct['uploadable_files'];
        $minProduct["text_fields"] = $oneProduct['text_fields'];
        $minProduct["active"] = $oneProduct['active'];
        $minProduct["available_for_order"] = $oneProduct['available_for_order'];
        $minProduct["available_date"] = $oneProduct['available_date'];
        $minProduct["is_virtual"] = $oneProduct['is_virtual'];
        $minProduct["date_add"] = $oneProduct['date_add'];
        $minProduct["date_upd"] = $oneProduct['date_upd'];
        $minProduct["name"] = $oneProduct['name'];
        array_push($productsMin, $minProduct);
    }

    $response = [
        'status' => 'success',
        'message' => '',
        'products' => $productsMin
    ];

    header('Content-Type: application/json');

    echo json_encode($response);
    die();
}

class QueryParameters
{
    public $fromId;
    public $limit;
}

function getQueryParameters()
{
    $queryParameters = new QueryParameters();


    $filterType = Tools::getValue('filterType');
    if (is_null($filterType)) {
        dieError('No filter type key');
    }

    $fromId = Tools::getValue('fromId', null);
    $limit = Tools::getValue('limit', null);

    $queryParameters->fromId = $fromId;
    $queryParameters->limit = $limit;

    return $queryParameters;
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
