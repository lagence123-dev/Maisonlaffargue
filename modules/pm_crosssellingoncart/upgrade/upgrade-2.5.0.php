<?php
/**
 * @author    Presta-Module.com <support@presta-module.com> - https://www.presta-module.com
 * @copyright Presta-Module - https://www.presta-module.com
 * @license   see file: LICENSE.txt
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
function upgrade_module_2_5_0($module)
{
    $res = true;
    $res &= $module->registerHook('displayBackOfficeHeader');
    $res &= $module->migrateLegacyConfiguration();
    return $res;
}
