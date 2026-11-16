<?php
/**
 * Upgrade script for orderwithdrawl v1.4.0.
 *
 * Adds the optional server-side product selection mode for partial
 * withdrawals. Existing shops keep the mode disabled until merchants enable
 * it explicitly in the Back Office.
 *
 * @author    sjmedia GmbH
 * @copyright 2007-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_4_0($module)
{
    unset($module);

    if (!Configuration::hasKey('ORDERWITHDRAWL_PRODUCT_SELECTION_MODE')) {
        return Configuration::updateValue('ORDERWITHDRAWL_PRODUCT_SELECTION_MODE', 0);
    }

    return true;
}
