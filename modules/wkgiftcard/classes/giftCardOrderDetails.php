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

class GiftCardOrderDetails extends ObjectModel
{
    public $id_card_rule;
    public $id_shop;
    public $id_customization;
    public $id_product;
    public $id_order;
    public $id_customer;
    public $customer_email;
    public $active;
    public $quantity;
    public $genrate_code;
    public $date_from;
    public $date_to;
    public $used;
    public $recipient_name;
    public $from_name;
    public $recipient_email;
    public $message;
    public $delivery_date;
    public $date_add;
    public $date_upd;
    public static $definition = [
        'table' => 'wk_gift_card_order_details',
        'primary' => 'id',
        'fields' => [
            'id_card_rule' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_customization' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_order' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'customer_email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'quantity' => ['type' => self::TYPE_STRING],
            'genrate_code' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 254],
            'used' => ['type' => self::TYPE_STRING],
            'date_from' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'recipient_name' => ['type' => self::TYPE_STRING],
            'from_name' => ['type' => self::TYPE_STRING],
            'recipient_email' => ['type' => self::TYPE_STRING],
            'message' => ['type' => self::TYPE_STRING],
            'delivery_date' => ['type' => self::TYPE_DATE,  'validate' => 'isDate'],
            'date_to' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
        ],
    ];

    public function getAllInfoByOrderId($orderId, $idShop, $id_customization = false)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_order_details`
            WHERE `id_shop` = ' . (int) $idShop . ' AND `id_order` = ' . (int) $orderId;

        if ($id_customization) {
            $sql .= ' AND `id_customization` = ' . (int) $id_customization;

            return Db::getInstance()->getRow($sql);
        }

        return Db::getInstance()->executeS($sql);
    }

    public function getCartRulesByCartId($id, $idShop)
    {
        return Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_order_details`
        WHERE `id_shop` = ' . (int) $idShop . ' AND `id_card_rule` =' . (int) $id);
    }

    public function getWkPsEmailTemplateContent($templateName, $mailType, $var)
    {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mailType && $email_configuration != Mail::TYPE_BOTH) {
            return '';
        }

        $default_mail_template_path = _PS_MODULE_DIR_ . 'wkgiftcard/views/templates/admin/' . DIRECTORY_SEPARATOR .
        $templateName;

        if (Tools::file_exists_cache($default_mail_template_path)) {
            Context::getContext()->smarty->assign('list', $var);

            return Context::getContext()->smarty->fetch($default_mail_template_path);
        }

        return '';
    }

    public function getSelectedProductForGiftProduct($productId, $query, $excludeIds, $excludePacks, $excludeVirtuals)
    {
        $context = Context::getContext();
        $sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image`id_image,
        il.`legend`, p.`cache_default_attribute`
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product
                AND pl.id_lang = ' . (int) $context->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product`
                    AND image_shop.cover=1
                    AND image_shop.id_shop=' . (int) $context->shop->id . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image`
                AND il.`id_lang` = ' . (int) $context->language->id . ')
                WHERE p.`id_product` !=' . (int) $productId . ' AND p.`active` = 1 ' . '
                AND p.`id_product` NOT IN (SELECT `id_voucher_product`
                FROM `' . _DB_PREFIX_ . 'wk_gift_card`)
                AND (pl.name LIKE \'%' . pSQL($query) . '%\'
                OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
                (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . pSQL($excludeIds) . ') ' : ' ') .
                ($excludePacks ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
                ' GROUP BY p.id_product';

        $product_info = Db::getInstance()->executeS($sql);

        return $product_info;
    }

    public function getRecipientCartRuleByRecipientEmail($email, $id_shop = false)
    {
        if (!$id_shop) {
            $id_shop = Context::getContext()->shop->id;
        }
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_order_details` wgcod WHERE wgcod.`id_shop` = ' . (int) $id_shop . ' AND wgcod.`recipient_email` = "' . pSQL($email) . '"';

        return Db::getInstance()->executeS($sql);
    }

    public function getGiftCardOrderInfoByProductID($productId, $idShop)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_order_details`
            WHERE `id_shop` = ' . (int) $idShop . ' AND `id_product` = ' . (int) $productId;

        return Db::getInstance()->executeS($sql);
    }
}
