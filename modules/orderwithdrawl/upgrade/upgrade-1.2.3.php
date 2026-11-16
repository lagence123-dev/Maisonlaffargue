<?php
/**
 * Upgrade script for orderwithdrawl v1.2.3
 *
 * Adds optional custom trigger statuses for the shipping-based withdrawal period.
 * When empty, the module keeps using the first order status marked as shipped.
 *
 * @author    sjmedia GmbH
 * @copyright 2007-2025 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_3($module)
{
    return Configuration::updateValue('ORDERWITHDRAWL_TRIGGER_STATE_IDS', '');
}
