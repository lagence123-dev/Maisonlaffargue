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

class WkGiftCardDeliverySchedule extends ObjectModel
{
    public $id_wk_gift_card_order_details;
    public $is_send;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'wk_gift_card_delivery_schedule',
        'primary' => 'id_wk_gift_card_delivery_schedule',
        'fields' => [
            'id_wk_gift_card_order_details' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'is_send' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'date_add' => ['type' => self::TYPE_DATE,  'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    public static function isAlreadyExistGiftCardOrderDetails($idWkGiftCardOrderDetails)
    {
        $sql = 'SELECT `id_wk_gift_card_delivery_schedule` FROM `' . _DB_PREFIX_ . 'wk_gift_card_delivery_schedule`
        WHERE `id_wk_gift_card_order_details` = ' . (int) $idWkGiftCardOrderDetails;

        return Db::getInstance()->getValue(
            $sql
        );
    }

    public static function getNotSendDelivery()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_delivery_schedule`
        WHERE `is_send` = 0';

        return Db::getInstance()->executeS(
            $sql
        );
    }
}
