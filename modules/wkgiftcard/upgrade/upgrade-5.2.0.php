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

function upgrade_module_5_2_0($module)
{
    $module->uninstallOverrides();
    $module->installOverrides();

    $module->registerHook([
        'displayOverrideTemplate',
        'displayAfterProductVariant',
        'displayAfterCartProductLine',
        'displayAfterProductName',
        'displayProductPriceBlock',
        'actionCustomerAccountAdd',
        'displayAdminOrderMain',
    ]);

    $module->unregisterHook('displayProductAdditionalInfo');
    $module->unregisterHook('displayOrderConfirmation2');

    $wkQueries = [
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_gift_card` ADD  `is_category_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0'",
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_gift_card` ADD `allow_categories` varchar(128) DEFAULT NULL',
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_gift_card_shop` ADD  `is_category_restriction` tinyint(1) unsigned NOT NULL DEFAULT '0'",
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_gift_card_shop` ADD `allow_categories` varchar(128) DEFAULT NULL',
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_gift_card_cart` ADD `id_customization` int(10) unsigned NOT NULL DEFAULT '0'",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_gift_card_order_details` ADD `id_customization` int(10) unsigned NOT NULL DEFAULT '0'",
    ];

    $dbInstance = Db::getInstance();
    $success = true;
    foreach ($wkQueries as $query) {
        $success &= $dbInstance->execute(trim($query));
    }

    return $success;
}
