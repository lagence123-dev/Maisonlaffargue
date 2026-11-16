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

    $order = new Order($queryParams->id);
    $orderArray['payment'] = $order->payment;
    $orderArray['module'] = $order->module;
    $orderArray['total_discounts'] = $order->total_discounts;
    $orderArray['total_discounts_tax_incl'] = $order->total_discounts_tax_incl;
    $orderArray['total_discounts_tax_excl'] = $order->total_discounts_tax_excl;
    $orderArray['total_paid'] = $order->total_paid;
    $orderArray['total_paid_tax_incl'] = $order->total_paid_tax_incl;
    $orderArray['total_paid_tax_excl'] = $order->total_paid_tax_excl;
    $orderArray['total_products'] = $order->total_products;
    $orderArray['total_products_wt'] = $order->total_products_wt;
    $orderArray['total_shipping'] = $order->total_shipping;
    $orderArray['total_shipping_tax_incl'] = $order->total_shipping_tax_incl;
    $orderArray['total_shipping_tax_excl'] = $order->total_shipping_tax_excl;
    $orderArray['carrier_tax_rate'] = $order->carrier_tax_rate;
    $orderArray['total_wrapping'] = $order->total_wrapping;
    $orderArray['invoice_number'] = $order->invoice_number;
    $orderArray['delivery_number'] = $order->delivery_number;
    $orderArray['valid'] = $order->valid;
    $orderArray['date_add'] = $order->date_add;
    $orderArray['date_upd'] = $order->date_upd;
    $orderArray['reference'] = $order->reference;
    // $orderArray['note'] = $order->note;

    $orderState = new OrderState($order->current_state);
    if (! Validate::isLoadedObject($orderState)) {
        dieError('Order state not loaded');
    }
    $orderArray['order_state'] = [];
    $orderArray['order_state']['name'] = $orderState->name;
    $orderArray['order_state']['delivery'] = $orderState->delivery;
    $orderArray['order_state']['hidden'] = $orderState->hidden;
    $orderArray['order_state']['shipped'] = $orderState->shipped;
    $orderArray['order_state']['paid'] = $orderState->paid;


    $customer = new Customer($order->id_customer);
    if (! Validate::isLoadedObject($customer)) {
        dieError('Customer not loaded');
    }
    $orderArray['customer'] = [];
    $orderArray['customer']['id'] = $customer->id;
    $orderArray['customer']['note'] = $customer->note;
    $orderArray['customer']['id_gender'] = $customer->id_gender;
    $orderArray['customer']['lastname'] = $customer->lastname;
    $orderArray['customer']['firstname'] = $customer->firstname;
    $orderArray['customer']['birthday'] = $customer->birthday;
    $orderArray['customer']['email'] = $customer->email;
    $orderArray['customer']['newsletter'] = $customer->newsletter;
    $orderArray['customer']['company'] = $customer->company;
    $orderArray['customer']['siret'] = $customer->siret;
    $orderArray['customer']['active'] = $customer->active;
    $orderArray['customer']['is_guest'] = $customer->is_guest;
    $orderArray['customer']['date_add'] = $customer->date_add;
    $orderArray['customer']['date_upd'] = $customer->date_upd;

    $addressDelivery = new Address($order->id_address_delivery);
    if (! Validate::isLoadedObject($addressDelivery)) {
        dieError('$addressDelivery not loaded');
    }
    $orderArray['address_delivery']['country'] = $addressDelivery->country;
    $orderArray['address_delivery']['alias'] = $addressDelivery->alias;
    $orderArray['address_delivery']['company'] = $addressDelivery->company;
    $orderArray['address_delivery']['lastname'] = $addressDelivery->lastname;
    $orderArray['address_delivery']['firstname'] = $addressDelivery->firstname;
    $orderArray['address_delivery']['address1'] = $addressDelivery->address1;
    $orderArray['address_delivery']['address2'] = $addressDelivery->address2;
    $orderArray['address_delivery']['postcode'] = $addressDelivery->postcode;
    $orderArray['address_delivery']['city'] = $addressDelivery->city;
    $orderArray['address_delivery']['other'] = $addressDelivery->other;
    $orderArray['address_delivery']['phone'] = $addressDelivery->phone;
    $orderArray['address_delivery']['phone_mobile'] = $addressDelivery->phone_mobile;
    $orderArray['address_delivery']['vat_number'] = $addressDelivery->vat_number;
    $orderArray['address_delivery']['dni'] = $addressDelivery->dni;
    $orderArray['address_delivery']['date_upd'] = $addressDelivery->date_upd;



    $orderArray['shippings'] = $order->getShipping();

    $orderArray['cart_rules'] = $order->getCartRules();

    $orderArray['products'] = $order->getProductsDetail();

    $orderArray['payments'] = $order->getOrderPayments();


    $response = [
        'status' => 'success',
        'message' => '',
        'order' => $orderArray
    ];

    header('Content-Type: application/json');

    echo json_encode($response);
    die();
}

class FindOneQueryParameters
{
    public $id;
}

function getQueryParameters()
{
    $queryParameters = new FindOneQueryParameters();

    $id = Tools::getValue('id');
    if (is_null($id)) {
        dieError('id');
    }

    $queryParameters->id = $id;

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
