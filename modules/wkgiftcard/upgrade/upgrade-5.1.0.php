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

function upgrade_module_5_1_0($module)
{
    $module->registerHook(['displayProductListReviews']);

    // delete already added cart but not order on upgrade because dynamic price is tax include in this version
    $giftCarts = Db::getInstance()->executeS(
        'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_cart`
        WHERE `id_cart` NOT IN (SELECT `id_cart` FROM `' . _DB_PREFIX_ . 'orders`)'
    );
    if (!empty($giftCarts)) {
        foreach ($giftCarts as $giftCardCart) {
            $giftCardCartObj = new WkGiftCardCart((int) $giftCardCart['id_wk_gift_card_cart']);
            $giftCardCartObj->delete();
            $conditionPrestashopCart = '`id_cart` = ' . (int) $giftCardCart['id_cart'];
            Db::getInstance()->delete(
                'cart_product',
                $conditionPrestashopCart
            );
        }
    }

    $queries = [
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_gift_card`
        ADD `free_shipping` TINYINT NOT NULL DEFAULT '0' AFTER `together`;",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_gift_card_shop`
        ADD `free_shipping` TINYINT NOT NULL DEFAULT '0' AFTER `together`;",
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
