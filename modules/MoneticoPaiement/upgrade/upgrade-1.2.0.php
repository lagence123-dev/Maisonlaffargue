<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_0($object)
{
    // update monetico code site table
    $show_current_columns_result = \Db::getInstance()->executeS("SHOW COLUMNS FROM `" . _DB_PREFIX_ . "monetico_code_site`");

    $successful_update = true;

    if (!in_array('code_site_payment_method', array_column($show_current_columns_result, 'Field'))) {
        $successful_update &= \Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . 'monetico_code_site`  ADD `code_site_payment_method` VARCHAR(64) NOT NULL
           ');
    }

    if (!in_array('code_site_amount_min', array_column($show_current_columns_result, 'Field'))) {
        $successful_update &= \Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . 'monetico_code_site`  ADD `code_site_amount_min` FLOAT unsigned
           ');
    }

    if (!in_array('code_site_amount_max', array_column($show_current_columns_result, 'Field'))) {
        $successful_update &= \Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . 'monetico_code_site`  ADD `code_site_amount_max` FLOAT unsigned
           ');
    }

    return $successful_update;
}