<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_2($object)
{
    $successful_update = true;

    if (!$object->registerHook('actionAdminControllerSetMedia')) {
        PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while registering hook.'), 'actionAdminControllerSetMedia'), 3);
        $successful_update &= false;
    }

    if (!$object->installDb()) {
        PrestaShopLogger::addLog('[Monetico] ' . $object->l('Error while checking db tables.'), 3);
        $successful_update &= false;
    }

    return $successful_update;
}