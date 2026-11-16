<?php
/**
 * Upgrade script for orderwithdrawl v1.2.2
 *
 * Adds a configurable start mode for the legal withdrawal period.
 *
 * Existing installations keep their previous behavior by defaulting to
 * order_date calculation.
 *
 * @author    sjmedia GmbH
 * @copyright 2024 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_2($module)
{
    return Configuration::updateValue('ORDERWITHDRAWL_PERIOD_START_MODE', 'order_date');
}
