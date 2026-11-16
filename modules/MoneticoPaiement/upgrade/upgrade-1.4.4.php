<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use MoneticoPaiement\Prestashop\utilities\PaymentStatus;

function upgrade_module_1_4_4($object)
{
    $successful_update = true;

    if (!$object->registerHook('displayOrderDetail')) {
        PrestaShopLogger::addLog('[Monetico] ' . sprintf($object->l('Error while registering hook.'), 'displayOrderDetail'), 3);
        $successful_update &= false;
    }

    if (!$object->installDb()) {
        PrestaShopLogger::addLog('[Monetico] ' . $object->l('Error while checking db tables.'), 3);
        $successful_update &= false;
    }

    $languages = Language::getLanguages();

    //PENDING state
    if (!Configuration::get(PaymentStatus::MP_PENDING) ||
        !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_PENDING), 'order_state')) {

        if (!empty(Configuration::get(PaymentStatus::MP_PENDING))) {
            $object->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_PENDING));
        }

        $mp_pending_state = new OrderState();
        $mp_pending_state->name = [];
        PaymentStatus::setMultilingualName($mp_pending_state, PaymentStatus::MP_PENDING, $languages);
        $mp_pending_state->invoice = false;
        $mp_pending_state->send_email = false;
        $mp_pending_state->module_name = $object->name;
        $mp_pending_state->color = '#34209E';
        $mp_pending_state->unremovable = true;
        $mp_pending_state->hidden = false;
        $mp_pending_state->logable = true;
        $mp_pending_state->delivery = false;
        $mp_pending_state->shipped = false;
        $mp_pending_state->paid = true;

        if (!$mp_pending_state->save() || !Configuration::updateValue(PaymentStatus::MP_PENDING, $mp_pending_state->id)) {
            $object->createLog(sprintf($object->l('Error while creating order status « %s ».'), $object->l('Pending payement')), 3);
            $successful_update &= false;
        } else {
            $successful_update &= $object->copyStatusGif($mp_pending_state->id);
        }
    }

    // REFUSED state
    if (!Configuration::get(PaymentStatus::MP_REFUSED_PAYMENT) ||
        !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_REFUSED_PAYMENT), 'order_state')) {

        if (!empty(Configuration::get(PaymentStatus::MP_REFUSED_PAYMENT))) {
            $object->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_REFUSED_PAYMENT));
        }

        $mp_refused_payment_state = new OrderState();
        $mp_refused_payment_state->name = [];
        PaymentStatus::setMultilingualName($mp_refused_payment_state, PaymentStatus::MP_REFUSED_PAYMENT, $languages);
        $mp_refused_payment_state->invoice = false;
        $mp_refused_payment_state->send_email = false;
        $mp_refused_payment_state->module_name = $object->name;
        $mp_refused_payment_state->color = '#2C3E50';
        $mp_refused_payment_state->unremovable = true;
        $mp_refused_payment_state->hidden = false;
        $mp_refused_payment_state->logable = true;
        $mp_refused_payment_state->delivery = false;
        $mp_refused_payment_state->shipped = false;
        $mp_refused_payment_state->paid = false;
        if (!$mp_refused_payment_state->save() || !Configuration::updateValue(PaymentStatus::MP_REFUSED_PAYMENT, $mp_refused_payment_state->id)) {
            $object->createLog(sprintf($object->l('Error while creating order status « %s ».'), $object->l('Refused Payment')), 3);
            $successful_update &= false;
        } else {
            $successful_update &= $object->copyStatusGif($mp_refused_payment_state->id);
        }
    }

    return $successful_update;
}