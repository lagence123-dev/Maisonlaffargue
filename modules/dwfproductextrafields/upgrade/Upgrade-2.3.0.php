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

function upgrade_module_2_3_0($module)
{
    return
        $module->unregisterHook(Hook::getIdByName('actionAdminProductsControllerDuplicateAfter'))
        && $module->registerHook('actionAdminDuplicateAfter')
    ;
}
