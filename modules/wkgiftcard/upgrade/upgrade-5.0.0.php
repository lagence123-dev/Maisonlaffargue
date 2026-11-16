<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_0_0($module)
{
    $module->unregisterHook(['displayAdminProductsExtra', 'actionAdminControllerSetMedia', 'actionProductSave']);
    $module->registerHook(['actionFrontControllerSetMedia', 'displayProductAdditionalInfo', 'displayHeader', 'displayOrderConfirmation2']);

    $status = [2, 5];
    Configuration::updateValue('WK_GIFT_CARD_PREFIX', 'GC-');
    Configuration::updateValue('WK_GIFT_CARD_ORDER_STATUS', json_encode($status));

    $module->uninstallTab();
    $module->callInstallTab();

    $module->installOverrides();

    $queries = [
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_gift_card`
        ADD `valid_upto` INT NOT NULL AFTER `point_type`;',
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_gift_card`
        CHANGE `create_voucher` `active` TINYINT UNSIGNED NOT NULL DEFAULT '0';",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_gift_card`
        ADD `dynamic_price` TINYINT NOT NULL DEFAULT '0' AFTER `id_voucher_product`;",
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_gift_card`
        DROP `end_date`;',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_gift_card`
        DROP `start_date`;',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_gift_card`
        DROP `id_product`;',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_gift_card_order_details`
        ADD `id_shop` INT unsigned NOT NULL AFTER `id`;',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_gift_card_order_details`
        ADD `recipient_name` varchar(255) NOT NULL AFTER `used`;',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_gift_card_order_details`
        ADD `recipient_email` varchar(255) NOT NULL AFTER `recipient_name`;',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_gift_card_order_details`
        ADD `message` text NULL DEFAULT NULL AFTER `recipient_email`;',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_gift_card_order_details`
        ADD `delivery_date` date NOT NULL AFTER `message`;',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_gift_card_order_details`
        ADD `from_name` varchar(255) NOT NULL AFTER `recipient_email`;',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_gift_card_shop` (
            `id` int(10) unsigned NOT NULL,
            `id_shop` int(10) unsigned NOT NULL,
            `id_voucher_product` int(10) unsigned default 1,
            `dynamic_price` TINYINT NOT NULL DEFAULT '0',
            `amount` decimal(20,2) NOT NULL DEFAULT '0.00',
            `together` tinyint(1) unsigned NOT NULL DEFAULT '1',
            `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
            `choose_product`tinyint(1) unsigned NOT NULL DEFAULT '1',
            `point_type` text NOT NULL ,
            `valid_upto` INT NOT NULL ,
            `date_add` date  DEFAULT NULL,
            `date_upd` date  DEFAULT NULL,
            PRIMARY KEY  (`id`, `id_shop`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8",
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_gift_card_product` (
            `id_wk_gift_card_product` INT(10) unsigned NOT NULL auto_increment,
            `id_wk_gift_card` INT(10) DEFAULT 0,
            `id_product` INT(10),
            PRIMARY KEY (`id_wk_gift_card_product`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_gift_card_product_shop` (
            `id_wk_gift_card_product` INT(10) unsigned NOT NULL,
            `id_wk_gift_card` INT(10) DEFAULT 0,
            `id_shop` int(10) unsigned NOT NULL,
            `id_product` INT(10),
            PRIMARY KEY (`id_wk_gift_card_product`, `id_shop`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_gift_card_cart` (
            `id_wk_gift_card_cart` int(11) NOT NULL AUTO_INCREMENT,
            `id_cart` int(11) NOT NULL,
            `id_shop` int(10) unsigned NOT NULL,
            `id_product` int(11) NOT NULL,
            `is_dynamic_price_type` TINYINT NOT NULL DEFAULT '0',
            `recipient_name` varchar(255) NOT NULL,
            `recipient_email` varchar(255) NOT NULL,
            `from_name` varchar(255) NOT NULL,
            `message` text NULL DEFAULT NULL,
            `delivery_date` date NOT NULL,
            `dynamic_amount` decimal(20,2) NOT NULL DEFAULT '0.00',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_wk_gift_card_cart`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_gift_card_delivery_schedule` (
            `id_wk_gift_card_delivery_schedule` int(10) unsigned NOT NULL auto_increment,
            `id_wk_gift_card_order_details` int(10) unsigned NOT NULL,
            `is_send` tinyint(1) unsigned NOT NULL DEFAULT '0',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_wk_gift_card_delivery_schedule`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8",
    ];
    $db = Db::getInstance();
    $success = true;
    foreach ($queries as $query) {
        $success &= $db->execute($query);
    }
    if ($success) {
        return true;
    }

    return true;
}
