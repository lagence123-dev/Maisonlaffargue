<?php
/**
 * Upgrade script for orderwithdrawl v1.4.2.
 *
 * Improves the Back Office UI for product and category exclusions. The release
 * keeps the existing configuration keys unchanged, so no data migration is
 * required.
 *
 * @author    sjmedia GmbH
 * @copyright 2007-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_4_2($module)
{
    unset($module);

    return true;
}
