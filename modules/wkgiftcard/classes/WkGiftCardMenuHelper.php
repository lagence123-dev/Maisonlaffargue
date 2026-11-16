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
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class WkGiftCardMenuHelper
{
    /**
     * [addTopMenuGiftCard - Add Auction menu in block top menu module]
     */
    public static function addTopMenuGiftCard()
    {
        if (Shop::getContextShopID(true) === null) {
            $shops = Shop::getContextListShopID();
        } else {
            $shops = [Shop::getContextShopID(true)];
        }
        foreach ($shops as $shopId) {
            $linksLabel = [];
            $labels = [];

            $link = new Link();

            $languages = Language::getLanguages();
            foreach ($languages as $val) {
                $giftCardPageLink = $link->getModuleLink(
                    'wkgiftcard',
                    'giftcardlist',
                    [],
                    null,
                    $val['id_lang'],
                    $shopId,
                    false
                );
                $linksLabel[$val['id_lang']] = $giftCardPageLink;
                $labels[$val['id_lang']] = 'Gift card';
            }

            $idLinksMenutop = WkGiftCardMenuHelper::addMenu($linksLabel, $labels, (int) $shopId, 0);
            if ($idLinksMenutop) {
                $newItem = 'LNK' . $idLinksMenutop;
                // Add new menu with available menus
                $lastItems = Configuration::get('MOD_BLOCKTOPMENU_ITEMS');
                $itemsArr = explode(',', $lastItems);
                $itemsArr[] = $newItem;

                $shopGroupId = Shop::getGroupFromShop($shopId);
                Configuration::updateValue(
                    'MOD_BLOCKTOPMENU_ITEMS',
                    (string) implode(',', $itemsArr),
                    false,
                    (int) $shopGroupId,
                    (int) $shopId
                );
                $auctionMenuId = Configuration::get('WK_GIFT_CARD_MENU_ID') . ',' . $idLinksMenutop;
                Configuration::updateValue('WK_GIFT_CARD_MENU_ID', $auctionMenuId);
            }
        }

        WkGiftCardMenuHelper::clearMainMenuCache();
    }

    public static function addMenu($link, $label, $idShop, $newWindow = 0)
    {
        if (!is_array($label)) {
            return false;
        }
        if (!is_array($link)) {
            return false;
        }

        Db::getInstance()->insert(
            'linksmenutop',
            [
                'new_window' => (int) $newWindow,
                'id_shop' => (int) $idShop,
            ]
        );

        $idLinksMenutop = Db::getInstance()->Insert_ID();

        foreach ($label as $idLang => $label) {
            Db::getInstance()->insert(
                'linksmenutop_lang',
                [
                    'id_linksmenutop' => (int) $idLinksMenutop,
                    'id_lang' => (int) $idLang,
                    'id_shop' => (int) $idShop,
                    'label' => pSQL($label),
                    'link' => pSQL($link[$idLang]),
                ]
            );
        }

        if ($idLinksMenutop) {
            return $idLinksMenutop;
        } else {
            return false;
        }
    }

    /**
     * [displayTopMenuGiftCard - Show and hide Auction menu]
     *
     * @return [type] [description]
     */
    public static function displayTopMenuGiftCard()
    {
        $giftCardMenuId = Configuration::get('WK_GIFT_CARD_MENU_ID');
        $giftCardMenuIdArray = explode(',', $giftCardMenuId);
        foreach ($giftCardMenuIdArray as $id) {
            if (!empty($id)) {
                $giftCardItem = 'LNK' . $id;
                $lastItems = Configuration::get('MOD_BLOCKTOPMENU_ITEMS');
                $displaymenu = Configuration::get('WK_GIFT_CARD_MENU');
                $itemsArr = explode(',', $lastItems);
                if ($displaymenu) {
                    if (!in_array($giftCardItem, $itemsArr)) {
                        $itemsArr[] = $giftCardItem;
                    }
                } else {
                    if (($itemkey = array_search($giftCardItem, $itemsArr)) !== false) {
                        unset($itemsArr[$itemkey]);
                    }
                }

                WkGiftCardMenuHelper::updateTopMenuItems($itemsArr);
            }
        }

        return true;
    }

    public static function updateTopMenuItems($itemsArr)
    {
        $shops = Shop::getContextListShopID();
        foreach ($shops as $shopId) {
            $shopGroupId = Shop::getGroupFromShop($shopId);

            if (count($shops) == 1) {
                if (is_array($itemsArr) && count($itemsArr)) {
                    Configuration::updateValue(
                        'MOD_BLOCKTOPMENU_ITEMS',
                        (string) implode(',', $itemsArr),
                        false,
                        (int) $shopGroupId,
                        (int) $shopId
                    );
                } else {
                    Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', '', false, (int) $shopGroupId, (int) $shopId);
                }
            }
        }

        WkGiftCardMenuHelper::clearMainMenuCache();

        return true;
    }

    public static function clearMainMenuCache()
    {
        $dir = _PS_CACHE_DIR_ . DIRECTORY_SEPARATOR . 'ps_mainmenu';
        if (!is_dir($dir)) {
            return;
        }

        foreach (scandir($dir) as $entry) {
            if (preg_match('/\.json$/', $entry)) {
                unlink($dir . DIRECTORY_SEPARATOR . $entry);
            }
        }

        if (version_compare(_PS_VERSION_, '1.7.6.1', '>')) {
            PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()->get('prestashop.core.cache.clearer.cache_clearer_chain')->clear();
        }
    }

    /**
     * [removeTopMenuGiftCard - Remove Auction menu when module is uninstall]
     *
     * @return [type] [description]
     */
    public static function removeTopMenuGiftCard()
    {
        $giftCardMenuId = Configuration::get('WK_GIFT_CARD_MENU_ID');
        $giftCardMenuIdArray = explode(',', $giftCardMenuId);
        $idShop = Shop::getContextShopID(true);

        foreach ($giftCardMenuIdArray as $id) {
            if ($id) {
                $auctionItem = 'LNK' . $id;

                $lastItems = Configuration::get('MOD_BLOCKTOPMENU_ITEMS');
                $itemsArr = explode(',', $lastItems);
                if (($itemkey = array_search($auctionItem, $itemsArr)) !== false) {
                    unset($itemsArr[$itemkey]);
                }

                WkGiftCardMenuHelper::updateTopMenuItems($itemsArr);

                // Remove from database
                if ($idShop === null) {
                    Db::getInstance()->delete('linksmenutop', 'id_linksmenutop = ' . (int) $id);
                    Db::getInstance()->delete('linksmenutop_lang', 'id_linksmenutop = ' . (int) $id);
                } else {
                    Db::getInstance()->delete('linksmenutop', 'id_linksmenutop = ' . (int) $id . ' AND id_shop = ' . (int) $idShop);
                    Db::getInstance()->delete('linksmenutop_lang', 'id_linksmenutop = ' . (int) $id . ' AND id_shop = ' . (int) $idShop);
                }
            }
        }

        return true;
    }
}
