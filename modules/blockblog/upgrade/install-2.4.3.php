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

function upgrade_module_2_4_3($module)
{

    $name_module = "blockblog";

    ### positions ##

    $data_blocks_for_home_page = array('posts','auth','gallery');

    foreach($module->getAllAvailablePositionsBlog() as $alias_position => $item_position) {

        // connect positions for current position
        $i = 1;
        $positions = array();
        foreach ($module->getBlocksArrayPrefix() as $_key_type => $_values_type) {

            if($alias_position == "home") {
                if (!in_array($_key_type, $data_blocks_for_home_page))
                    continue;
            }

            $key = $alias_position."_".$_key_type;
            $position_item = $i;
            $positions[$key] = $position_item;


            $i++;
        }


        $positions = json_encode($positions);
        Configuration::updateValue($name_module . '_orderby' . $alias_position, $positions);

        // connect positions for current position


        Configuration::updateValue($name_module.$alias_position."mobile", 1);
        Configuration::updateValue($name_module.$alias_position, 1);

        foreach ($module->getBlocksArrayPrefix() as $_key_type => $_values_type) {

            Configuration::updateValue($name_module . '_' . $alias_position . $_key_type . "mobile", $alias_position.$_key_type."mobile");
            Configuration::updateValue($name_module . '_' . $alias_position . $_key_type , $alias_position.$_key_type);
        }

    }

    ### positions ##


    foreach(array("m","s") as $val) {
        Configuration::updateValue($name_module . $val.'cat_custom_hook', 1);
        Configuration::updateValue($name_module . $val.'posts_custom_hook', 1);
        Configuration::updateValue($name_module . $val.'comm_custom_hook', 1);
        Configuration::updateValue($name_module . $val.'search_custom_hook', 1);
        Configuration::updateValue($name_module . $val.'arch_custom_hook', 1);
        Configuration::updateValue($name_module . $val.'tags_custom_hook', 1);
        Configuration::updateValue($name_module . $val.'auth_custom_hook', 1);
        Configuration::updateValue($name_module . $val.'gallery_custom_hook', 1);
    }


    // meta title, description, keywords, alias
    $myloyalty = 'Loyalty Program';

    $languages = Language::getLanguages(false);
    foreach ($languages as $language){
        $i = $language['id_lang'];
        Configuration::updateValue($name_module.'title_loyalty_'.$i, $myloyalty);
        Configuration::updateValue($name_module.'desc_loyalty_'.$i, $myloyalty);
        Configuration::updateValue($name_module.'key_loyalty_'.$i, $myloyalty);
        Configuration::updateValue($name_module.'blog_loyalty_alias_'.$i, 'loyalty');
    }
    // meta title, description, keywords, alias




    $_prefix_loyality = "l";


    Configuration::updateValue($name_module.'loyality_on'.$_prefix_loyality, 1);

    $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
    foreach ($cur AS $_cur){
        if(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency']){
            Configuration::updateValue('lamount_'.(int)$_cur['id_currency'], 0.1);
        }
    }

    Configuration::updateValue($name_module.'lorderstatuses', implode(",",array(2,5)));

    //Configuration::updateValue($name_module.$_prefix_loyality.'orderstatusescan', implode(",",array(2,5)));


    Configuration::updateValue($name_module.'validity_period'.$_prefix_loyality, 365);


    // voucher settings - loyality program

    Configuration::updateValue($name_module.'vis_on'.$_prefix_loyality, 1);

    $languages = Language::getLanguages(false);
    foreach ($languages as $language){
        $i = $language['id_lang'];
        $iso = Tools::strtoupper(Language::getIsoById($i));

        $coupondesc = 'Blog PRO + Loyalty Program';
        Configuration::updateValue($name_module.'coupondesc'.$_prefix_loyality.'_'.$i, $coupondesc.' '.$iso);
    }

    Configuration::updateValue($name_module.'vouchercode'.$_prefix_loyality, "LOYALITY");

    Configuration::updateValue($name_module.'tax'.$_prefix_loyality, 1);


    Configuration::updateValue($name_module.'sdvvalid'.$_prefix_loyality, 365);

    // cumulable
    Configuration::updateValue($name_module.'cumulativeother'.$_prefix_loyality, 0);
    Configuration::updateValue($name_module.'cumulativereduc'.$_prefix_loyality, 0);
    // cumulable

    Configuration::updateValue($name_module.'highlight'.$_prefix_loyality, 0);
    // categories
    Configuration::updateValue($name_module.'catbox'.$_prefix_loyality, $module->getIdsCategories());
    // categories

    // voucher settings - loyality program


    // points and actions //
    $points_and_actions = $module->getAvailablePoints();

    foreach($points_and_actions as $k=> $item){

        $points = $item['points'];

        Configuration::updateValue($name_module.$k.'_points', (int)$points);
        Configuration::updateValue($name_module.$k.'_status', $k);

        $is_times = isset($item['count_times'])?$item['count_times']:0;
        Configuration::updateValue($name_module . $k . '_times', (int)$is_times);

        $languages = Language::getLanguages(false);
        foreach ($languages as $language){
            $i = $language['id_lang'];
            Configuration::updateValue($name_module.$k.'description_'.$i, $item['desc_points']);
        }
    }

    // points and actions //

    // my account //
    Configuration::updateValue($name_module.'d_eff_loyalty_my'.$_prefix_loyality, 'flipInX');
    Configuration::updateValue($name_module.'perpagemy'.$_prefix_loyality, 10);
    // me account //


    /* enable/disable emails */
    Configuration::updateValue($name_module.'noti'.$_prefix_loyality, 1);

    Configuration::updateValue($name_module.'is_points'.$_prefix_loyality, 1);
    Configuration::updateValue($name_module.'is_loyaltyvouch'.$_prefix_loyality, 1);

    $languages = Language::getLanguages(false);
    foreach ($languages as $language){
        $i = $language['id_lang'];
        Configuration::updateValue($name_module.'points'.$_prefix_loyality.'_'.$i, 'You received loyalty points');
        Configuration::updateValue($name_module.'loyaltyvouch'.$_prefix_loyality.'_'.$i, 'You transform points into a voucher and get voucher with discount');
    }
    /* enable/disable emails */


    $module->installLoyalityProgramTables();

    $module->registerHook('OrderConfirmation');
    $module->registerHook('actionValidateOrder');
    $module->registerHook('displayPaymentReturn');
    $module->registerHook('actionBeforeAddOrder');
    $module->registerHook('actionOrderStatusUpdate');
    $module->registerHook('actionCustomerAccountAdd');
    $module->registerHook('displayCustomerAccountForm');

    version_compare(_PS_VERSION_, '1.7', '>')? $module->registerHook('displayProductButtons') : $module->registerHook('productActions');


    ## regenerate tabs in the admin panel ##
    $module->uninstallTab15();

    $module->createAdminTabs15();
    ## regenerate tabs in the admin panel ##


    /// clear smarty cache ///
     Tools::clearSmartyCache();
    /// clear smarty cache ///

    return true;

}