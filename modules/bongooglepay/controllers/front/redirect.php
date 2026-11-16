<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/
include dirname(__FILE__) . '/../../../../config/config.inc.php';
require_once '../../../../init.php';
require_once(_PS_MODULE_DIR_.'/bongooglepay/bongooglepay.php');

$cont = new FrontController();
$cont->init();
$context = Context::getContext();

$module = new Bongooglepay();
$private_key = Configuration::get('BON_PROVIDER_SECRET');
$public_key = Configuration::get('BON_PROVIDER_ID');
$payment_provider = Configuration::get('BON_PAYMENT_PROVIDER');
$create_order = Configuration::get('BONGOOGLEPAY_ORDER');
$post_url = '';
$website_domain = $_SERVER['HTTP_HOST'];
$host = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://') . $website_domain;

$get_data = Tools::file_get_contents('php://input');
$data = json_decode($get_data);

$pageName = $data->pageName;
$googleToken = $data->gpay_token;
$description = $data->paymentDescription;

$order_id = '';

$ch = curl_init();

if ('product' == $pageName) {
    $product = $module->infoOnProductPage();
    $product_name = $data->product_name;
    $product_id = $data->product_id;
    $product_url = $product[0];
    $totalPrice = $data->totalPrice;
    $currency = $data->currency;
    $payerEmail = $data->payerEmail;
    $first_name = $data->payerName;
    $last_name = $data->payerName;
    $phoneNumber = $data->phoneNumber;
    $senderCity = $data->senderCity;
    $senderPostalCode = $data->senderPostalCode;
    $senderAddress = $data->shippingAddress;
    $senderCountryCode = $data->senderCountryCode;
    $result_url = '';
    $bytes = random_bytes(9);
    $order_id = bin2hex($bytes);
} elseif ('checkout' == $pageName) {
    $payment_data = $module->googlePayContent();
    $cart_id = $payment_data['id_cart'];
    $product_number = 1;
    $totalPrice = $payment_data['total'];
    $currency = $payment_data['currency'];
    $payerEmail = $payment_data['payer_email'];
    $first_name = $payment_data['firstname'];
    $last_name = $payment_data['lastname'];
    $phoneNumber = $payment_data['phone'];
    $senderCity = $payment_data['sender_city'];
    $senderAddress = $payment_data['sender_address'];
    $senderPostalCode = $payment_data['sender_postcode'];
    $product_name_arr = $payment_data['name'];
    $result_url = 'http://' . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'index.php?controller=history';
    $order_id = $cart_id;
}

if ('stripe' == $payment_provider) {
    require 'vendor/autoload.php';

    \Stripe\Stripe::setApiKey($private_key);

    header('Content-Type: application/json');

    if ('checkout' == $pageName) {
        $product_name = implode(', ', $product_name_arr);
    }

    $charge = \Stripe\Charge::create([
        'amount' => (float) $totalPrice * 100,
        'currency' => $currency,
        'source' => $googleToken,
    ]);

    $stripe_status = json_encode(['status' => $charge->status]);
}
if ('liqpay' == $payment_provider) {
    $post_url = 'https://www.liqpay.ua/api/request';

    $json_string = [
        'action' => 'pay',
        'version' => 3,
        'paytype' => 'gpay',
        'public_key' => $public_key,
        'order_id' => $order_id,
        'description' => $description,
        'amount' => $totalPrice,
        'currency' => $currency,
        'gpay_token' => $googleToken,
        'product_name' => $product_name,
        // 'product_url' => $product_url
        'sender_first_name' => $first_name,
        'sender_last_name' => $last_name,
        'sender_city' => $senderCity,
        'sender_address' => $senderAddress,
        // 'sender_phone' => $phoneNumber,
        'sender_mail' => $payerEmail,
        'sender_postal_code' => $senderPostalCode,
        'result_url' => $result_url,
    ];
    $json = json_encode($json_string, JSON_UNESCAPED_UNICODE);

    $send_data = $json;

    $signature = base64_encode(sha1($private_key . $send_data . $private_key, 1));

    $post_fields = [
        'data' => $send_data,
        'signature' => $signature,
    ];

    $fields_string = http_build_query($post_fields);
}

if ('adyen' == $payment_provider) {
    $post_url = 'https://checkout-test.adyen.com/v66/payments';
    $post_fields = [
        'amount' => [
            'currency' => $currency,
            'value' => (float) $totalPrice * 100,
        ],
        'reference' => $order_id,
        'paymentMethod' => [
            'type' => 'paywithgoogle',
            'googlePayToken' => $googleToken,
        ],
        'merchantAccount' => $public_key,
        'returnUrl' => __PS_BASE_URI__,
    ];
    $fields_string = json_encode($post_fields);
    $headers = [];
    $headers[] = 'Content-type: application/json';
    $headers[] = 'x-API-key: ' . $private_key;
}

if ('checkoutltd' == $payment_provider) {
    $get_token_url = 'https://api.checkout.com/tokens';
    $get_token_data = [
        'type' => 'googlepay',
        'token_data' => $googleToken,
    ];

    $get_token_string = json_encode($get_token_data);
    $headers_token = [];
    $headers_token[] = 'Content-type: application/json';
    $headers_token[] = 'Authorization: ' . $public_key;

    curl_setopt($ch, CURLOPT_URL, $get_token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_token);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $get_token_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $get_token_result = curl_exec($ch);
    $checkoutltd_result = json_decode($get_token_result);
    $checkoutltd_token = $checkoutltd_result->token;

    $post_url = 'https://api.checkout.com/payments';
    $post_fields = [
        'source' => [
            'type' => 'token',
            'token' => $checkoutltd_token,
        ],
        'amount' => (float) $totalPrice * 100,
        'currency' => $currency,
        'reference' => $order_id,
    ];

    $fields_string = json_encode($post_fields);

    $headers = [];
    $headers[] = 'Content-type: application/json';
    $headers[] = 'Authorization: ' . $private_key;
}
if ('stripe' != $payment_provider) {
    curl_setopt($ch, CURLOPT_URL, $post_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $result = curl_exec($ch);
    $parsed_result = json_decode($result);
}

if ('liqpay' == $payment_provider || 'checkoutltd' == $payment_provider) {
    $result_status = $parsed_result->status;
} elseif ('adyen' == $payment_provider) {
    $result_status = $parsed_result->resultCode;
} elseif ('stripe' == $payment_provider) {
    $result = json_decode($stripe_status);
    $result_status = $result->status;
}

if ('success' == $result_status || 'sender_verify' == $result_status || 'Authorised' == $result_status || 'Authorized' == $result_status || 'succeeded' == $result_status) {
    if ('product' == $pageName) {
        if (1 == $create_order) {
            // create customer
            if ($context->cookie->id_customer) {
                $customer = new Customer($context->cookie->id_customer);
            }
            if (!isset($customer) or !$customer->id) {
                $customer = new Customer();
                $customer->email = $payerEmail;
                $customer->lastname = $last_name;
                $customer->firstname = $first_name;
                $customer->passwd = 'no password';
                $customer->add();

                $context->customer = $customer;
                $context->smarty->assign('confirmation', 1);
                $context->cookie->id_customer = (int) $customer->id;
                $context->cookie->customer_lastname = $customer->lastname;
                $context->cookie->customer_firstname = $customer->firstname;
                $context->cookie->passwd = $customer->passwd;
                $context->cookie->logged = 1;

                if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE')) {
                    $context->cookie->account_created = 1;
                }
                $customer->logged = 1;
                $context->cookie->email = $customer->email;
                $context->cookie->is_guest = !Tools::getValue('is_new_customer', 1);

                $address = new Address(
                    null,
                    $context->language->id
                );

                $country = new Country();

                $country_id = $country->getByIso($senderCountryCode);

                $address->address1 = $senderAddress;
                $address->postcode = $senderPostalCode;
                $address->city = $senderCity;
                $address->phone = $phoneNumber;
                $address->firstname = $customer->firstname;
                $address->lastname = $customer->lastname;
                $address->id_customer = (int) $customer->id;
                $address->id_country = $country_id;
                $address->id_state = 0;
                $address->alias = 'My Address';
                $address->save();
            }

            // create cart
            if ($context->cookie->id_cart) {
                $context->cart->delete();
                $context->cookie->id_cart = 0;
            }

            $cart = new Cart();
            $cart->id_customer = (int) $context->cookie->id_customer;
            $cart->id_address_delivery = (int) Address::getFirstCustomerAddressId($cart->id_customer);
            $cart->id_address_invoice = $cart->id_address_delivery;
            $cart->id_lang = (int) $context->cookie->id_lang;
            $cart->id_currency = (int) $context->cookie->id_currency;
            $cart->id_carrier = 1;
            $cart->recyclable = 0;
            $cart->gift = 0;
            $cart->add();
            $context->cookie->id_cart = (int) $cart->id;
            $cart->update();

            $cart_id = $cart->id;
            $cart->updateQty(1, $product_id);

            $module->validateOrder(
                (int)$cart_id,
                Configuration::get('PS_OS_PAYMENT'),
                $totalPrice,
                $module->displayName,
                null,
                [],
                null,
                $context->customer->secure_key
            );
            $order_id = Order::getOrderByCartId((int) $cart_id);

            $order = new Order($order_id);
            $customer = new CustomerCore($order->id_customer);
            $module = Module::getInstanceByName($order->module);

            $url = $context->link->getPageLink(
                'order-confirmation',
                true,
                null,
                [
                    'id_cart' => (int) $context->cart->id,
                    'id_module' => (int) $module->id,
                    'id_order' => (int) $order_id,
                    'key' => $customer->secure_key,
                ]
            );
            exit(json_encode(['success' => 1, 'alert' => $module->successAlert, 'alertDescription' => $module->successDescription, 'payment_provider' => $payment_provider, 'redirect_url' => $url, 'result' => $result_status]));
        } else {
            exit(json_encode(['success' => 1, 'alert' => $module->successAlert, 'alertDescription' => $module->successDescription, 'payment_provider' => $payment_provider, 'result' => $result_status]));
        }

    } elseif ('checkout' == $pageName) {
        $module->validateOrder((int)$cart_id, Configuration::get('PS_OS_PREPARATION'), $totalPrice, $module->displayName);
        $order_id = Order::getOrderByCartId((int) $cart_id);
        $order = new Order($order_id);
        $customer = new CustomerCore($order->id_customer);
        $module = Module::getInstanceByName($order->module);
        $redirect_url = 'order-confirmation?id_cart=' . $cart_id . '&id_module=' . $module->id . '&id_order=' . $order_id . '&key=' . $customer->secure_key;
        exit(json_encode(['success' => 1, 'alert' => $module->successAlert, 'alertDescription' => $module->successDescription, 'result' => $result, 'redirect_url' => $redirect_url]));
    }
} else {
    if ('product' == $pageName) {
        exit(json_encode(['success' => 2, 'error' => $module->errorAlert, 'result' => $result]));
    } elseif ('checkout' == $pageName) {
    }
}
