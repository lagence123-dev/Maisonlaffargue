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

function upgrade_module_2_2_3($module)
{

    ## regenerate tabs ##


    $tab_id = Tab::getIdFromClassName("AdminBlockblog");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }

    if(version_compare(_PS_VERSION_, '1.7', '>')) {
        $tab_id = Tab::getIdFromClassName("AdminBlockblogtest");
        if($tab_id){
            $tab = new Tab($tab_id);
            $tab->delete();
        }

        $tab_id = Tab::getIdFromClassName("AdminBlockblogtest");
        if($tab_id){
            $tab = new Tab($tab_id);
            $tab->delete();
        }
    }

    $tab_id = Tab::getIdFromClassName("AdminBlockblogcategories");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }

    $tab_id = Tab::getIdFromClassName("AdminBlockblogposts");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }

    $tab_id = Tab::getIdFromClassName("AdminBlockblogcomments");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }

    $tab_id = Tab::getIdFromClassName("AdminBlockbloggallery");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }

    $tab_id = Tab::getIdFromClassName("AdminBlockblogajax");
    if($tab_id){
        $tab = new Tab($tab_id);
        $tab->delete();
    }


    $module->createAdminTabs15();
    ## regenerate tabs ##




    /// clear smarty cache ///

    Tools::clearSmartyCache();

    /// clear smarty cache ///

    return true;

}