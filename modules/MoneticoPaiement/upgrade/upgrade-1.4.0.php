<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopLogger;

function upgrade_module_1_4_0($object)
{
    $successful_update = true;

    if (!$object->registerHook('actionAdminControllerInitAfter')) {
        PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while registering hook %s.'), 'actionAdminControllerInitAfter'), 3);
        $successful_update &= false;
    }

    if (!$object->registerHook('displayDashboardTop')) {
        PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while registering hook %s.'), 'displayDashboardTop'), 3);
        $successful_update &= false;
    }

    if (!$object->registerHook('actionPresentPaymentOptions')) {
        PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while registering hook %s.'), 'actionPresentPaymentOptions'), 3);
        $successful_update &= false;
    }
        if (!$object->registerHook('actionFrontControllerSetMedia')) {
        PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while registering hook %s.'), 'actionFrontControllerSetMedia'), 3);
        $successful_update &= false;
    }

    if (!$object->registerHook('displayPaymentByBinaries')) {
        PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while registering hook %s.'), 'displayPaymentByBinaries'), 3);
        $successful_update &= false;
    }

    return $successful_update;
}