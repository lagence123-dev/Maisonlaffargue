<?php

/**
 * This is actually added for the v1.5.0
 */
function upgrade_module_1_4_11($object)
{
    // Update the monetico_transaction table
    $showCurrentColumnsResult = \Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'monetico_transaction`');

    $successfulUpdate = true;

    if (!in_array('has_taxes', array_column($showCurrentColumnsResult, 'Field'))) {
        $successfulUpdate &= \Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'monetico_transaction` ADD `has_taxes` BOOLEAN DEFAULT 1');
    }

    return $successfulUpdate;
}