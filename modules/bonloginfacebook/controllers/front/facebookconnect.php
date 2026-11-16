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
require_once '../../../../config/config.inc.php';

$cont = new FrontController();
$cont->init();
$context = Context::getContext();
$logged = $context->customer->isLogged();

$result_url = 'https://graph.facebook.com/me?fields=id,gender,link,email,first_name,last_name,birthday&access_token=' . trim(Tools::getValue('accessToken'));
$ch = curl_init($result_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
$user_json_encoded_result = curl_exec($ch);
curl_close($ch);
if ($user_json_encoded_result) {
    $user = json_decode($user_json_encoded_result);
    if ($user) {
        $fb_id = (int) $user->id;
        $firstname = pSQL($user->first_name);
        $lastname = pSQL($user->last_name);
        if (isset($user->email)) {
            $email = pSQL($user->email);
        } else {
            $email = pSQL('ID_' . $fb_id . '@empty.email');
        }
        srand((float) microtime() * 1000000);
        $passwd = pSQL(Tools::substr(uniqid(rand()), 0, 12));
        $real_passwd = pSQL($passwd);
        $passwd = md5(pSQL(_COOKIE_KEY_ . $passwd));
        $date_add = date('Y-m-d H:i:s');
        $date_updated = pSQL($date_add);
        $last_pass_gen = pSQL($date_updated);
        $active = 1;
        $secure_key = md5(uniqid(rand(), true));
        if ($logged) {
            if (Customer::customerExists($email)) {
                $customer = new Customer();
                $authentication = $customer->getByEmail(trim($email));
                if (!$authentication || !$customer->id) {
                } else {
                }
            } else {
            }
        } else {
            if (Customer::customerExists($email)) {
                $customer = new Customer();
                $authentication = $customer->getByEmail(trim($email));
                if (!$authentication || !$customer->id) {
                } else {
                    Hook::exec('actionBeforeAuthentication');
                    $context->cookie->id_customer = (int) $customer->id;
                    $context->cookie->customer_lastname = pSQL($customer->lastname);
                    $context->cookie->customer_firstname = pSQL($customer->firstname);
                    $context->cookie->logged = 1;
                    $context->cookie->is_guest = (int) $customer->isGuest();
                    $context->cookie->passwd = pSQL($customer->passwd);
                    $context->cookie->email = pSQL($customer->email);
                    // Customer is logged in
                    $customer->logged = 1;

                    // Add customer to the context
                    $context->customer = $customer;

                    // Used to init session
                    $context->updateCustomer($customer);

                    if (Configuration::get('PS_CART_FOLLOWING') && (empty($context->cookie->id_cart) || 0 == Cart::getNbProducts($context->cookie->id_cart)) && $id_cart = (int) Cart::lastNoneOrderedCart($context->customer->id)) {
                        $context->cart = new Cart($id_cart);
                    } else {
                        $context->cart->id_carrier = 0;
                        $context->cart->setDeliveryOption(null);
                        $context->cart->id_address_delivery = Address::getFirstCustomerAddressId((int) $customer->id);
                        $context->cart->id_address_invoice = Address::getFirstCustomerAddressId((int) $customer->id);
                    }
                    $context->cart->id_customer = (int) $customer->id;
                    $context->cart->secure_key = $customer->secure_key;
                    $context->cart->save();

                    $context->cookie->id_cart = (int) $context->cart->id;
                    $context->cart->autosetProductAddress();

                    Hook::exec('actionAuthentication');

                    // Login information have changed, so we check if the cart rules still apply
                    CartRule::autoRemoveFromCart($context);
                    CartRule::autoAddToCart($context);
                }
            } else {
                $id_default_group = 1;
                $sql = 'insert into `' . _DB_PREFIX_ . 'customer` SET
                id_default_group = ' . $id_default_group . ',
                firstname = \'' . pSQL($firstname) . '\', lastname = \'' . pSQL($lastname) . '\',
                email = \'' . pSQL($email) . '\', passwd = \'' . pSQL($passwd) . '\',
                last_passwd_gen = \'' . pSQL($last_pass_gen) . '\', 
                secure_key = \'' . pSQL($secure_key) . '\', active = ' . (int) $active . ',
                date_add = \'' . pSQL($date_add) . '\', date_upd = \'' . pSQL($date_updated) . '\' ';
                defined('_MYSQL_ENGINE_') ? $result = Db::getInstance()->Execute($sql) : $result = Db::getInstance()->Execute($sql);
                $insert_id = Db::getInstance()->Insert_ID();
                $id_group = 1;
                $sql = 'INSERT into `' . _DB_PREFIX_ . 'customer_group` SET
                id_customer = ' . (int) $insert_id . ', id_group = ' . (int) $id_group . ' ';
                defined('_MYSQL_ENGINE_') ? $result = Db::getInstance()->Execute($sql) : $result = Db::getInstance()->Execute($sql);
                $sql = 'INSERT into `' . _DB_PREFIX_ . 'fbconnect_customer` SET
                cust_id = ' . (int) $insert_id . '';
                defined('_MYSQL_ENGINE_') ? $result = Db::getInstance()->Execute($sql) : $result = Db::getInstance()->Execute($sql);
                $customer = new Customer();
                $authentication = $customer->getByEmail(trim($email));
                if (!$authentication || !$customer->id) {
                } else {
                    $context->smarty->assign('confirmation', 1);
                    Hook::exec('actionBeforeAuthentication');
                    $context->cookie->id_customer = (int) $customer->id;
                    $context->cookie->customer_lastname = pSQL($customer->lastname);
                    $context->cookie->customer_firstname = pSQL($customer->firstname);
                    $context->cookie->logged = 1;
                    $context->cookie->is_guest = 0;
                    $context->cookie->passwd = pSQL($customer->passwd);
                    $context->cookie->email = pSQL($customer->email);
                    $customer->logged = 1;

                    // Add customer to the context
                    $context->customer = $customer;

                    // Used to init session
                    $context->updateCustomer($customer);

                    if (Configuration::get('PS_CART_FOLLOWING') && (empty($context->cookie->id_cart) || 0 == Cart::getNbProducts($context->cookie->id_cart)) && $id_cart = (int) Cart::lastNoneOrderedCart($context->customer->id)) {
                        $context->cart = new Cart($id_cart);
                    } else {
                        $context->cart->id_carrier = 0;
                        $context->cart->setDeliveryOption(null);
                        $context->cart->id_address_delivery = Address::getFirstCustomerAddressId((int) $customer->id);
                        $context->cart->id_address_invoice = Address::getFirstCustomerAddressId((int) $customer->id);
                    }
                    $context->cart->id_customer = (int) $customer->id;
                    $context->cart->secure_key = $customer->secure_key;
                    $context->cart->save();

                    $context->cookie->id_cart = (int) $context->cart->id;
                    $context->cart->autosetProductAddress();

                    Hook::exec('actionAuthentication');

                    // Login information have changed, so we check if the cart rules still apply
                    CartRule::autoRemoveFromCart($context);
                    CartRule::autoAddToCart($context);

                    // Customer is now logged in.

                    return true;
                }
            }
        }
    }
}
