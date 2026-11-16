<?php
/**
 * 2015-2020 Bonpresta
 *
 * Bonpresta HTML Content
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
 *  @copyright 2015-2020 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class ClassBonconsentgdpr extends ObjectModel
{
    public $id_tab;
    public $id_shop;
    public $title;
    public $description;
    public $status;
    public $cookie_type;
    public $sort_order;
    public $url;

    public static $definition = [
        'table' => 'bonconsentgdpr',
        'primary' => 'id_tab',
        'multilang' => true,
        'fields' => [
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true],
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'cookie_type' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255],
            'description' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'],
            'status' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'sort_order' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt'],
        ],
    ];

    public static function getBonconsentgdprList()
    {
        $sql = 'SELECT bonh.*, bonhl.`title`
                FROM ' . _DB_PREFIX_ . 'bonconsentgdpr bonh
                JOIN ' . _DB_PREFIX_ . 'bonconsentgdpr_lang bonhl
                ON (bonh.`id_tab` = bonhl.`id_tab`)
                AND bonh.`id_shop` = ' . (int) Context::getContext()->shop->id . '
                AND bonhl.`id_lang` = ' . (int) Context::getContext()->language->id . '
                ORDER BY bonh.`sort_order`';

        if (!$result = Db::getInstance()->executeS($sql)) {
            return false;
        }

        return $result;
    }

    public static function getMaxSortOrder($id_shop)
    {
        $result = Db::getInstance()->ExecuteS('
            SELECT MAX(sort_order) AS sort_order
            FROM `' . _DB_PREFIX_ . 'bonconsentgdpr`
            WHERE id_shop = ' . (int) $id_shop);

        if (!$result) {
            return false;
        }

        foreach ($result as $res) {
            $result = $res['sort_order'];
        }

        $result = $result + 1;

        return $result;
    }

    public static function getTopFrontItems($id_shop, $only_active = false)
    {
        $sql = 'SELECT *
                FROM ' . _DB_PREFIX_ . 'bonconsentgdpr bonh
                JOIN ' . _DB_PREFIX_ . 'bonconsentgdpr_lang bonhl
                ON bonh.id_tab = bonhl.id_tab
                AND bonhl.id_lang = ' . (int) Context::getContext()->language->id . '
                AND bonh.id_shop =' . (int) $id_shop;

        if ($only_active) {
            $sql .= ' AND `status` = 1';
        }

        $sql .= ' ORDER BY `sort_order`';

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return [];
        }

        return $result;
    }
}
