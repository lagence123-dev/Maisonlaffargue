<?php
/**
 * Upgrade script for orderwithdrawl v1.3.1.
 *
 * Adds structured partial-withdrawal form fields and mail placeholders.
 * No database schema changes are required; existing settings stay untouched.
 *
 * @author    sjmedia GmbH
 * @copyright 2007-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_1($module)
{
    $ok = true;

    if (!$module->registerHook('displayOrderWithdrawlCta')) {
        $ok = false;
    }

    return $ok;
}
