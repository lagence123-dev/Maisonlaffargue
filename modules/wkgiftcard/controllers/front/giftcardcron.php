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

class WkGiftCardGiftCardCronModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $objWkGiftCard = Module::getInstanceByName('wkgiftcard');
        if (!Tools::getValue('token') || (Tools::getValue('token') != $objWkGiftCard->secure_key)) {
            exit('Token Invalid.');
        }
        $deliverySchedule = WkGiftCardDeliverySchedule::getNotSendDelivery();
        if (!empty($deliverySchedule)) {
            foreach ($deliverySchedule as $delivery) {
                $giftCardOrderDetailsObj = new GiftCardOrderDetails((int) $delivery['id_wk_gift_card_order_details']);
                if ($objWkGiftCard->sendMailToCustomer($giftCardOrderDetailsObj->id_order, $giftCardOrderDetailsObj->id_shop, null)) {
                    $giftCardDeliveryScheduleObj = new WkGiftCardDeliverySchedule((int) $delivery['id_wk_gift_card_delivery_schedule']);
                    $giftCardDeliveryScheduleObj->is_send = 1;
                    $giftCardDeliveryScheduleObj->save();
                }
            }
        }
        exit('Cron executes successfully.');
    }
}
