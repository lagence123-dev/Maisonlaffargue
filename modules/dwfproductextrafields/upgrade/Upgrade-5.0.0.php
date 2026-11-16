<?php
/**
 *   Copyright since 2009 ohmyweb!
 *
 *   @author    ohmyweb <contact@ohmyweb.fr>
 *   @copyright since 2009 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_5_0_0($module)
{
    $hooksToUnregister = [];
    $hooks = [];

    if (version_compare(_PS_VERSION_, '9.0.0', '>=')
        || (version_compare(_PS_VERSION_, '8.0.0', '>=')
            && Db::getInstance()->getValue('SELECT `state` FROM `'._DB_PREFIX_.'feature_flag` WHERE name = "product_page_v2"')
        )
    ) {
        $hooksToUnregister[] = 'actionAdminProductsControllerSaveAfter';
        $hooksToUnregister[] = 'displayAdminProductsExtra';

        $hooks[] = 'actionAfterUpdateProductFormHandler';
        $hooks[] = 'actionProductFormBuilderModifier';
    } else {
        $hooksToUnregister[] = 'actionAfterUpdateProductFormHandler';

        $hooks[] = 'actionAdminProductsControllerSaveAfter';
        $hooks[] = 'displayAdminProductsExtra';
    }

    $res = true;
    foreach ($hooksToUnregister as $hook) {
        $res &= $module->unregisterHook(Hook::getIdByName($hook));
    }
    foreach ($hooks as $hook) {
        $res &= $module->registerHook($hook);
    }

    return $res;
}
