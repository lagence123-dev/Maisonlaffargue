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

class WkGiftCardCart extends ObjectModel
{
    public $id_cart;
    public $id_product;
    public $is_dynamic_price_type;
    public $recipient_name;
    public $from_name;
    public $recipient_email;
    public $message;
    public $delivery_date;
    public $id_shop;
    public $id_customization;
    public $dynamic_amount;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'wk_gift_card_cart',
        'primary' => 'id_wk_gift_card_cart',
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'is_dynamic_price_type' => ['type' => self::TYPE_BOOL],
            'recipient_name' => ['type' => self::TYPE_STRING],
            'from_name' => ['type' => self::TYPE_STRING],
            'recipient_email' => ['type' => self::TYPE_STRING],
            'message' => ['type' => self::TYPE_STRING],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_customization' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'dynamic_amount' => ['type' => self::TYPE_FLOAT],
            'delivery_date' => ['type' => self::TYPE_DATE,  'validate' => 'isDate'],
            'date_add' => ['type' => self::TYPE_DATE,  'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    public static function getGiftCartProductCartInfo($idProduct, $idCart, $idShop, $id_customization = false)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_cart` wgcc
        WHERE wgcc.`id_product` = ' . (int) $idProduct . '
        AND wgcc.`id_shop` = ' . (int) $idShop . '
        AND wgcc.`id_cart` = ' . (int) $idCart;

        if ($id_customization) {
            $sql .= ' AND wgcc.`id_customization` = ' . (int) $id_customization;
        }

        return Db::getInstance()->getRow($sql);
    }

    public static function getGiftCardCartByCartId($idCart, $idShop)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_cart` wgcc
            WHERE wgcc.`id_shop` = ' . (int) $idShop . ' AND wgcc.`id_cart` = ' . (int) $idCart
        );
    }

    public static function deleteCurrentCustomerCarts($idProduct)
    {
        $giftCarts = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_cart`
            WHERE `id_product` = ' . (int) $idProduct . ' AND `id_cart` NOT IN (SELECT `id_cart` FROM `' . _DB_PREFIX_ . 'orders` WHERE id_shop = ' . (int) Context::getContext()->shop->id . ')'
        );
        if (!empty($giftCarts)) {
            foreach ($giftCarts as $giftCardCart) {
                $giftCardCartObj = new WkGiftCardCart((int) $giftCardCart['id_wk_gift_card_cart']);
                if (!$giftCardCartObj->delete()) {
                    return false;
                }
                $conditionPrestashopCart = '`id_cart` = ' . (int) $giftCardCart['id_cart'];
                Db::getInstance()->delete(
                    'cart_product',
                    $conditionPrestashopCart
                );
            }
        }
    }
}
