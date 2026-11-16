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

if (!defined('_PS_VERSION_')) {
    exit;
}

class ClassBonmaintenance extends ObjectModel
{
    public $id_tab;
    public $id_shop;
    public $image;
    public $title_text;
    public $description;
    public $colorhex;
    public $colorhex2;
    public $colorhex3;
    public $select_type;
    public $data_start;
    public $data_end;

    public static $definition = array(
        'table' => 'bonmaintenance',
        'primary' => 'id_tab',
        'multilang' => true,
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt', 'required' => true),
            'image' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
            'title_text' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'description' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
            'colorhex' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
            'colorhex2' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
            'colorhex3' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
            'select_type' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
            'data_start' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'data_end' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public function delete()
    {
        $res = true;
        $images = $this->image;

        if ($images) {
            foreach ($images as $image) {
                if ($image && file_exists(_PS_MODULE_DIR_.'bonmaintenance/views/img/'.$image)) {
                    $res &= @unlink(_PS_MODULE_DIR_.'bonmaintenance/views/img/'.$image);
                }
            }
        }

        $res &= parent::delete();
        return $res;
    }

    public function getTopFrontItems($id_shop)
    {
        $now = date('Y-m-d H:i:00');

        $sql = 'SELECT *
                FROM ' . _DB_PREFIX_ . 'bonmaintenance bonn
                JOIN ' . _DB_PREFIX_ . 'bonmaintenance_lang bonnl
                ON bonn.id_tab = bonnl.id_tab
                WHERE bonnl.id_lang = '.(int)Context::getContext()->language->id.'
                AND bonn.`data_end` >= \''.$now.'\'
                AND bonn.`data_start` <= \''.$now.'\'
                AND bonn.id_shop ='.(int)$id_shop;

        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            return array();
        }

        return $result;
    }

}
