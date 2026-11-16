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
class WkGiftCardProduct extends ObjectModel
{
    public $id_wk_gift_card;
    public $id_product;

    public static $definition = [
        'table' => 'wk_gift_card_product',
        'primary' => 'id_wk_gift_card_product',
        'fields' => [
            'id_wk_gift_card' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'shop' => true],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        Shop::addTableAssociation('wk_gift_card_product', ['type' => 'shop', 'primary' => 'id_wk_gift_card_product']);
        parent::__construct($id, $idLang, $idShop);
    }

    public function deleteAllAttchedProduct($idWkGiftCard)
    {
        $attachProducts = Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_product` WHERE `id_wk_gift_card` = ' . (int) $idWkGiftCard
        );
        if (!empty($attachProducts)) {
            foreach ($attachProducts as $attachProduct) {
                $giftCardProductObj = new WkGiftCardProduct((int) $attachProduct['id_wk_gift_card_product']);
                $giftCardProductObj->delete();
            }
        }
    }

    public static function getAllByGiftCardID($idWkGiftCard)
    {
        $sql = 'SELECT wgcps.`id_product`, wgcps.`id_shop` FROM `' . _DB_PREFIX_ . 'wk_gift_card_product`  wgcp
            LEFT JOIN `' . _DB_PREFIX_ . 'wk_gift_card_product_shop` wgcps ON (wgcps.`id_wk_gift_card_product` = wgcp.`id_wk_gift_card_product` AND wgcps.`id_shop` = ' . (int) Context::getContext()->shop->id . ')
            WHERE wgcps.`id_shop` = ' . (int) Context::getContext()->shop->id . ' AND wgcps.`id_wk_gift_card` = ' . (int) $idWkGiftCard;

        return Db::getInstance()->executeS(
            $sql
        );
    }

    public static function getAllowedCategory($idWkGiftCard)
    {
        $sql = 'SELECT wgc.`allow_categories`, wgc.`is_category_restriction` FROM `' . _DB_PREFIX_ . 'wk_gift_card` wgc WHERE wgc.`id` = ' . (int) $idWkGiftCard;

        return Db::getInstance()->getRow($sql);
    }
}
