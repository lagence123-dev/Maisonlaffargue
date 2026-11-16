<?php
/**
 * Upgrade script for orderwithdrawl v1.4.1.
 *
 * Adds the optional automatic rejection switch for expired withdrawal
 * requests. Existing shops keep it disabled until merchants explicitly enable
 * the risk-marked setting in the Back Office.
 *
 * @author    sjmedia GmbH
 * @copyright 2007-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_4_1($module)
{
    unset($module);

    if (!Configuration::hasKey('ORDERWITHDRAWL_AUTO_REJECT_EXPIRED')) {
        return Configuration::updateValue('ORDERWITHDRAWL_AUTO_REJECT_EXPIRED', 0);
    }

    return true;
}
