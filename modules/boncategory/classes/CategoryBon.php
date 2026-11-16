<?php
/**
 * 2015-2021 Bonpresta
 *
 * Bonpresta Column Category
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the General Public License (GPL 2.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/GPL-2.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the module to newer
 * versions in the future.
 *
 *  @author    Bonpresta
 *  @copyright 2015-2021 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class CategoryBon extends ObjectModel
{
    public $id_shop;
    public $id_category;
    public $sort_order;
    public $status;
    public $content;
    public $icon;
    public $type_icon;
    public $switch_icon;

    public static $definition = array(
        'table'  => 'boncategory',
        'primary' => 'id_item',
        'multilang' => true,
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'id_category'=> array('type' => self::TYPE_INT, 'required' => true, 'validate' => 'isunsignedInt'),
            'sort_order' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
            'type_icon' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml','size' => 128),
            'icon' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 128),
            'status' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'switch_icon' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'content' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 4000),
        ),
    );


    public static function getCategoryList()
    {
        $sql = 'SELECT hc.*, cl.`name`, hcl.`content`
                FROM '._DB_PREFIX_.'boncategory hc
                LEFT JOIN '._DB_PREFIX_.'boncategory_lang hcl
                ON (hc.`id_item` = hcl.`id_item`)
                LEFT JOIN '._DB_PREFIX_.'category_lang cl
                ON (hc.`id_category` = cl.`id_category`)
                WHERE cl.`id_lang` = '.(int)Context::getContext()->language->id.'
                AND cl.`id_shop` = '.(int)Context::getContext()->shop->id.'
                AND hcl.`id_lang` = '.(int)Context::getContext()->language->id.'
                ORDER BY hc.`sort_order`';

        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result;
    }

    public static function getFrontCategory()
    {
        $sql = 'SELECT hc.*, cl.`name`, hcl.`content`
                FROM '._DB_PREFIX_.'boncategory hc
                LEFT JOIN '._DB_PREFIX_.'boncategory_lang hcl
                ON (hc.`id_item` = hcl.`id_item`)
                LEFT JOIN '._DB_PREFIX_.'category_lang cl
                ON (hc.`id_category` = cl.`id_category`)
                WHERE cl.`id_lang` = '.(int)Context::getContext()->language->id.'
                AND cl.`id_shop` = '.(int)Context::getContext()->shop->id.'
                AND hcl.`id_lang` = '.(int)Context::getContext()->language->id.'
                AND hc.`status` = 1
                ORDER BY hc.`sort_order`';


        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result;
    }

    public static function getMaxSortOrder($id_shop)
    {
        $result = Db::getInstance()->ExecuteS('
            SELECT MAX(sort_order) AS sort_order
            FROM `'._DB_PREFIX_.'boncategory`
            WHERE id_shop = '.(int)$id_shop);

        if (!$result) {
            return false;
        }

        foreach ($result as $res) {
            $result = $res['sort_order'];
        }

        $result = $result + 1;

        return $result;
    }

    public static function getDeletedCategory($id_category)
    {
        $data = array();

        $sql = 'SELECT `id_item`
                FROM '._DB_PREFIX_.'boncategory
                WHERE `id_category` = '.(int)$id_category;

        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        foreach ($result as $res) {
            $data[] = $res['id_item'];
        }

        return $data;
    }
}
