<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

use MoneticoPaiement\Prestashop\utilities\PaymentStatus;

function upgrade_module_1_3_0($object)
{
    $successful_update = true;

    // DEMO MODE TAB
    $demo_tab = new Tab();
    $demo_tab->name = [];
    $demo_tab->name[Context::getContext()->language->id] = $object->l('Demo mode');
    $demo_tab->class_name = 'AdminMoneticoPaiementDemo';
    $demo_tab->id_parent = 0;
    $demo_tab->module = $object->name;
    if (!$demo_tab->add()) {
        $object->createLog($object->l('An error has occurred during the process to create the demo mode tab'), 3);
        $successful_update &= false;
    }

    // DEMO STATE
    if (!Configuration::get(PaymentStatus::MP_DEMO_PAYMENT)) {

        $mp_demo_payment_state = new OrderState();
        $mp_demo_payment_state->name = [];
        $mp_demo_payment_state->name[Context::getContext()->language->id] = $object->l('Payment accepted') . " [DEMO]";
        $mp_demo_payment_state->invoice = false;
        $mp_demo_payment_state->send_email = false;
        $mp_demo_payment_state->module_name = $object->name;
        $mp_demo_payment_state->color = '#3498D8';
        $mp_demo_payment_state->unremovable = true;
        $mp_demo_payment_state->hidden = false;
        $mp_demo_payment_state->logable = true;
        $mp_demo_payment_state->delivery = false;
        $mp_demo_payment_state->shipped = false;
        $mp_demo_payment_state->paid = true;

        if (!$mp_demo_payment_state->save() || !Configuration::updateValue(PaymentStatus::MP_DEMO_PAYMENT, $mp_demo_payment_state->id)) {
            $object->createLog(sprintf($object->l('Error while creating order status « %s ».'), $object->l('Received demo Payment')), 3);
            $successful_update &= false;
        } else {
            $successful_update &= $object->copyStatusGif($mp_demo_payment_state->id);
        }
    }

    if (!Configuration::get(PaymentStatus::MP_DEMO_RECEIVED_SPLIT_PAYMENT)) {

        $mp_demo_received_payment_1_state = new OrderState();
        $mp_demo_received_payment_1_state->name = [];
        $mp_demo_received_payment_1_state->name[Context::getContext()->language->id] = $object->l('Received instalment number 1') . ' ' . '[DEMO]';
        $mp_demo_received_payment_1_state->invoice = false;
        $mp_demo_received_payment_1_state->send_email = false;
        $mp_demo_received_payment_1_state->module_name = $object->name;
        $mp_demo_received_payment_1_state->color = '#3498D8';
        $mp_demo_received_payment_1_state->unremovable = true;
        $mp_demo_received_payment_1_state->hidden = true;
        $mp_demo_received_payment_1_state->logable = true;
        $mp_demo_received_payment_1_state->delivery = false;
        $mp_demo_received_payment_1_state->shipped = false;
        $mp_demo_received_payment_1_state->paid = true;

        if (!$mp_demo_received_payment_1_state->save() || !Configuration::updateValue(PaymentStatus::MP_DEMO_RECEIVED_SPLIT_PAYMENT, $mp_demo_received_payment_1_state->id)) {
            $object->createLog(sprintf($object->l('Error while creating order status « %s ».'), $object->l('Received demo instalment number 1')), 3);
            $successful_update &= false;
        } else {
            $successful_update &= $object->copyStatusGif($mp_demo_received_payment_1_state->id);
        }
    }

    if (!Configuration::get(PaymentStatus::MP_DEMO_CANCELED)) {

        $mp_demo_canceled = new OrderState();
        $mp_demo_canceled->name = [];
        $mp_demo_canceled->name[Context::getContext()->language->id] = $object->l('Canceled') . " " . "[DEMO]";
        $mp_demo_canceled->invoice = false;
        $mp_demo_canceled->send_email = false;
        $mp_demo_canceled->module_name = $object->name;
        $mp_demo_canceled->color = '#2C3E50';
        $mp_demo_canceled->unremovable = true;
        $mp_demo_canceled->hidden = false;
        $mp_demo_canceled->logable = true;
        $mp_demo_canceled->delivery = false;
        $mp_demo_canceled->shipped = false;
        $mp_demo_canceled->paid = true;

        if (!$mp_demo_canceled->save() || !Configuration::updateValue(PaymentStatus::MP_DEMO_CANCELED, $mp_demo_canceled->id)) {
            $object->createLog(sprintf($object->l('Error while creating order status « %s ».'), $object->l('Canceled demo Order')), 3);
            $successful_update &= false;
        } else {
            $successful_update &= $object->copyStatusGif($mp_demo_canceled->id);
        }
    }

    return $successful_update;
}