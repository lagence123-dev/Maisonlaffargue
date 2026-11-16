<?php
/**
 * Upgrade script for orderwithdrawl v1.4.3.
 *
 * Updates release metadata and keeps existing merchant configuration unchanged.
 * No database schema or configuration migration is required.
 *
 * @author    sjmedia GmbH
 * @copyright 2007-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_4_3($module)
{
    unset($module);

    return true;
}
