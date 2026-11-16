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

class WkGiftCardDb
{
    /**
     * Execute SQL query
     */
    public function createTables()
    {
        if ($sql = $this->getModuleSql()) {
            foreach ($sql as $query) {
                if ($query) {
                    if (!Db::getInstance()->execute(trim($query))) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     *  SQL query for Table Creation
     */
    public function getModuleSql()
    {
        return [
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_gift_card` (
                `id` int(10) unsigned NOT NULL auto_increment,
                `id_voucher_product` int(10) unsigned default 1,
                `dynamic_price` TINYINT NOT NULL DEFAULT '0',
                `amount` decimal(20,2) NOT NULL DEFAULT '0.00',
                `together` tinyint(1) unsigned NOT NULL DEFAULT '1',
                `free_shipping` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `choose_product`tinyint(1) unsigned NOT NULL DEFAULT '1',
                `point_type` text NOT NULL ,
                `valid_upto` INT NOT NULL ,
                `is_category_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `allow_categories` varchar(128) DEFAULT NULL,
                `date_add` date  DEFAULT NULL,
                `date_upd` date  DEFAULT NULL,
                PRIMARY KEY  (`id`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8",
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_gift_card_shop` (
                `id` int(10) unsigned NOT NULL,
                `id_shop` int(10) unsigned NOT NULL,
                `id_voucher_product` int(10) unsigned default 1,
                `dynamic_price` TINYINT NOT NULL DEFAULT '0',
                `amount` decimal(20,2) NOT NULL DEFAULT '0.00',
                `together` tinyint(1) unsigned NOT NULL DEFAULT '1',
                `free_shipping` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `choose_product`tinyint(1) unsigned NOT NULL DEFAULT '1',
                `point_type` text NOT NULL ,
                `valid_upto` INT NOT NULL ,
                `is_category_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `allow_categories` varchar(128) DEFAULT NULL,
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
                `id_customization` int(10) unsigned NOT NULL,
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
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_gift_card_order_details` (
                `id` int(10) unsigned NOT NULL auto_increment,
                `id_shop` int(10) unsigned NOT NULL,
                `id_customization` int(10) unsigned NOT NULL,
                `id_card_rule` int(10) unsigned default 1,
                `id_product` int(10) unsigned default 1,
                `id_order` int(10) unsigned default 1,
                `id_customer` int(10) unsigned default 1,
                `customer_email` varchar(128) NOT NULL,
                `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `quantity` text,
                `genrate_code` varchar(254) NOT NULL,
                `used`  text,
                `recipient_name` varchar(255) NOT NULL,
                `recipient_email` varchar(255) NOT NULL,
                `from_name` varchar(255) NOT NULL,
                `message` text NULL DEFAULT NULL,
                `delivery_date` date NOT NULL,
                `date_to` datetime NOT NULL,
                `date_from` datetime NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8",
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_gift_card_delivery_schedule` (
                `id_wk_gift_card_delivery_schedule` int(10) unsigned NOT NULL auto_increment,
                `id_wk_gift_card_order_details` int(10) unsigned NOT NULL,
                `is_send` tinyint(1) unsigned NOT NULL DEFAULT '0',
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_wk_gift_card_delivery_schedule`)
            ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8",
        ];
    }

    /**
     * Execute SQL query for Table Deletion
     */
    public function deleteTables()
    {
        return Db::getInstance()->execute(
            'DROP TABLE IF EXISTS
            `' . _DB_PREFIX_ . 'wk_gift_card`,
            `' . _DB_PREFIX_ . 'wk_gift_card_shop`,
            `' . _DB_PREFIX_ . 'wk_gift_card_product`,
            `' . _DB_PREFIX_ . 'wk_gift_card_product_shop`,
            `' . _DB_PREFIX_ . 'wk_gift_card_cart`,
            `' . _DB_PREFIX_ . 'wk_gift_card_delivery_schedule`,
            `' . _DB_PREFIX_ . 'wk_gift_card_order_details`'
        );
    }
}
