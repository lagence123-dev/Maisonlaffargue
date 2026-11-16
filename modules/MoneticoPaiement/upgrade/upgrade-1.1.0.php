<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use MoneticoPaiement\Prestashop\utilities\PaymentStatus;
use PrestaShop\PrestaShop\Adapter\Entity\Language as PrestashopLanguage;
use PrestaShop\PrestaShop\Adapter\Entity\OrderState;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopLogger;

function upgrade_module_1_1_0($object)
{
    // update monetico EPT table
    $show_current_columns_result = \Db::getInstance()->executeS("SHOW COLUMNS FROM `"._DB_PREFIX_."monetico_ept` LIKE '%ept_instalment%'");

    $successful_update = true;

    if(!in_array('ept_instalment_default',array_column($show_current_columns_result, 'Field'))){
        $successful_update &= \Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . 'monetico_ept`  ADD `ept_instalment_default` BOOLEAN NULL
           ');
    }

    if(!in_array('ept_instalment_term_nb',array_column($show_current_columns_result, 'Field'))){
        $successful_update &= \Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . 'monetico_ept`  ADD `ept_instalment_term_nb` INT(1) NULL
           ');
    }
    if(!in_array('ept_instalment_pct_1',array_column($show_current_columns_result, 'Field'))){
        $successful_update &= \Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . 'monetico_ept`  ADD `ept_instalment_pct_1` INT(2) NULL
           ');
    }
    if(!in_array('ept_instalment_pct_2',array_column($show_current_columns_result, 'Field'))){
        $successful_update &= \Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . 'monetico_ept`  ADD `ept_instalment_pct_2` INT(2) NULL
           ');
    }
    if(!in_array('ept_instalment_pct_3',array_column($show_current_columns_result, 'Field'))){
        $successful_update &= \Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . 'monetico_ept`  ADD `ept_instalment_pct_3` INT(2) NULL
           ');
    }
    if(!in_array('ept_instalment_pct_4',array_column($show_current_columns_result, 'Field'))){
        $successful_update &= \Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . 'monetico_ept`  ADD `ept_instalment_pct_4` INT(2) NULL
           ');
    }

    if(!$successful_update){
        PrestaShopLogger::addLog($object->l('The ept table update has failed'), 3);
        return false;
    }

    // add instalment payment status
    $current_language_iso_code = Context::getContext()->language->iso_code;
    $current_language_id_lang = PrestashopLanguage::getIdByIso($current_language_iso_code);

    if (!Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["1"])) {
        $mp_received_payment_1_state = new OrderState();
        $mp_received_payment_1_state->name = [];
        $mp_received_payment_1_state->name[$current_language_id_lang] = $object->l('Received instalment number 1');
        $mp_received_payment_1_state->invoice = false;
        $mp_received_payment_1_state->send_email = false;
        $mp_received_payment_1_state->module_name = $object->name;
        $mp_received_payment_1_state->color = '#3498D8';
        $mp_received_payment_1_state->unremovable = true;
        $mp_received_payment_1_state->hidden = false;
        $mp_received_payment_1_state->logable = true;
        $mp_received_payment_1_state->delivery = false;
        $mp_received_payment_1_state->shipped = false;
        $mp_received_payment_1_state->paid = true;

        if (!$mp_received_payment_1_state->save() || !Configuration::updateValue(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["1"], $mp_received_payment_1_state->id)) {
            PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while creating order status « %s ».'), $object->l('Received instalment number 1')), 3);
            return false;
        }
    }
    if (!Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["2"])) {
        $mp_received_payment_2_state = new OrderState();
        $mp_received_payment_2_state->name = [];
        $mp_received_payment_2_state->name[$current_language_id_lang] = $object->l('Received instalment number 2');
        $mp_received_payment_2_state->invoice = false;
        $mp_received_payment_2_state->send_email = false;
        $mp_received_payment_2_state->module_name = $object->name;
        $mp_received_payment_2_state->color = '#3498D8';
        $mp_received_payment_2_state->unremovable = true;
        $mp_received_payment_2_state->hidden = false;
        $mp_received_payment_2_state->logable = true;
        $mp_received_payment_2_state->delivery = false;
        $mp_received_payment_2_state->shipped = false;
        $mp_received_payment_2_state->paid = true;

        if (!$mp_received_payment_2_state->save() || !Configuration::updateValue(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["2"], $mp_received_payment_2_state->id)) {
            PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while creating order status « %s ».'), $object->l('Received instalment number 2')), 3);
            return false;
        }
    }
    if (!Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["3"])) {
        $mp_received_payment_3_state = new OrderState();
        $mp_received_payment_3_state->name = [];
        $mp_received_payment_3_state->name[$current_language_id_lang] = $object->l('Received instalment number 3');
        $mp_received_payment_3_state->invoice = false;
        $mp_received_payment_3_state->send_email = false;
        $mp_received_payment_3_state->module_name = $object->name;
        $mp_received_payment_3_state->color = '#3498D8';
        $mp_received_payment_3_state->unremovable = true;
        $mp_received_payment_3_state->hidden = false;
        $mp_received_payment_3_state->logable = true;
        $mp_received_payment_3_state->delivery = false;
        $mp_received_payment_3_state->shipped = false;
        $mp_received_payment_3_state->paid = true;

        if (!$mp_received_payment_3_state->save() || !Configuration::updateValue(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["3"], $mp_received_payment_3_state->id)) {
            PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while creating order status « %s ».'), $object->l('Received instalment number 3')), 3);
            return false;
        }
    }
    if (!Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["4"])) {

        $mp_received_payment_4_state = new OrderState();
        $mp_received_payment_4_state->name = [];
        $mp_received_payment_4_state->name[$current_language_id_lang] = $object->l('Payment completed');
        $mp_received_payment_4_state->invoice = false;
        $mp_received_payment_4_state->send_email = false;
        $mp_received_payment_4_state->module_name = $object->name;
        $mp_received_payment_4_state->color = '#3498D8';
        $mp_received_payment_4_state->unremovable = true;
        $mp_received_payment_4_state->hidden = false;
        $mp_received_payment_4_state->logable = true;
        $mp_received_payment_4_state->delivery = false;
        $mp_received_payment_4_state->shipped = false;
        $mp_received_payment_4_state->paid = true;

        if (!$mp_received_payment_4_state->save() || !Configuration::updateValue(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["4"], $mp_received_payment_4_state->id)) {
            PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while creating order status « %s ».'), $object->l('Payment completed')), 3);
            return false;
        }
    }
    if (!Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["1"])) {

        $mp_error_payment_1_state = new OrderState();
        $mp_error_payment_1_state->name = [];
        $mp_error_payment_1_state->name[$current_language_id_lang] = $object->l('Error first Payment');
        $mp_error_payment_1_state->invoice = false;
        $mp_error_payment_1_state->send_email = false;
        $mp_error_payment_1_state->module_name = $object->name;
        $mp_error_payment_1_state->color = '#E74C3C';
        $mp_error_payment_1_state->unremovable = true;
        $mp_error_payment_1_state->hidden = false;
        $mp_error_payment_1_state->logable = true;
        $mp_error_payment_1_state->delivery = false;
        $mp_error_payment_1_state->shipped = false;
        $mp_error_payment_1_state->paid = false;
        if (!$mp_error_payment_1_state->save() || !Configuration::updateValue(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["1"], $mp_error_payment_1_state->id)) {
            PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while creating order status « %s ».'), $object->l('Error first Payment')), 3);
            return false;
        }
    }
    if (!Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["2"])) {

        $mp_error_payment_2_state = new OrderState();
        $mp_error_payment_2_state->name = [];
        $mp_error_payment_2_state->name[$current_language_id_lang] = $object->l('Error second Payment');
        $mp_error_payment_2_state->invoice = false;
        $mp_error_payment_2_state->send_email = false;
        $mp_error_payment_2_state->module_name = $object->name;
        $mp_error_payment_2_state->color = '#E74C3C';
        $mp_error_payment_2_state->unremovable = true;
        $mp_error_payment_2_state->hidden = false;
        $mp_error_payment_2_state->logable = true;
        $mp_error_payment_2_state->delivery = false;
        $mp_error_payment_2_state->shipped = false;
        $mp_error_payment_2_state->paid = false;

        if (!$mp_error_payment_2_state->save() || !Configuration::updateValue(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["2"], $mp_error_payment_2_state->id)) {
            PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while creating order status « %s ».'), $object->l('Error second Payment')), 3);
            return false;
        }
    }
    if (!Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["3"])) {

        $mp_error_payment_3_state = new OrderState();
        $mp_error_payment_3_state->name = [];
        $mp_error_payment_3_state->name[$current_language_id_lang] = $object->l('Error third Payment');
        $mp_error_payment_3_state->invoice = false;
        $mp_error_payment_3_state->send_email = false;
        $mp_error_payment_3_state->module_name = $object->name;
        $mp_error_payment_3_state->color = '#E74C3C';
        $mp_error_payment_3_state->unremovable = true;
        $mp_error_payment_3_state->hidden = false;
        $mp_error_payment_3_state->logable = true;
        $mp_error_payment_3_state->delivery = false;
        $mp_error_payment_3_state->shipped = false;
        $mp_error_payment_3_state->paid = false;

        if (!$mp_error_payment_3_state->save() || !Configuration::updateValue(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["3"], $mp_error_payment_3_state->id)) {
            PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while creating order status « %s ».'), $object->l('Error third Payment')), 3);
            return false;
        }
    }
    if (!Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["4"])) {

        $mp_error_payment_4_state = new OrderState();
        $mp_error_payment_4_state->name = [];
        $mp_error_payment_4_state->name[$current_language_id_lang] = $object->l('Error fourth Payment');
        $mp_error_payment_4_state->invoice = false;
        $mp_error_payment_4_state->send_email = false;
        $mp_error_payment_4_state->module_name = $object->name;
        $mp_error_payment_4_state->color = '#E74C3C';
        $mp_error_payment_4_state->unremovable = true;
        $mp_error_payment_4_state->hidden = false;
        $mp_error_payment_4_state->logable = true;
        $mp_error_payment_4_state->delivery = false;
        $mp_error_payment_4_state->shipped = false;
        $mp_error_payment_4_state->paid = false;

        if (!$mp_error_payment_4_state->save() || !Configuration::updateValue(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["4"], $mp_error_payment_4_state->id)) {
            PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while creating order status « %s ».'), $object->l('Error fourth Payment')), 3);
            return false;
        }
    }
    // HOOK
    $object->registerHook('actionOrderStatusUpdate');

    return $successful_update;
}