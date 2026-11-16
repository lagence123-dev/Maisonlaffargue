<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use MoneticoPaiement\Prestashop\utilities\PaymentStatus;

function upgrade_module_1_4_1($object)
{
    $successful_update = true;

    if (!Configuration::get(PaymentStatus::MP_RECEIVED_PARTIAL_PAYMENT)) {

        $mp_received_partial_payment_state = new OrderState();
        $mp_received_partial_payment_state->name = [];
        $mp_received_partial_payment_state->name[Context::getContext()->language->id] = $object->l('Partially recovered');
        $mp_received_partial_payment_state->invoice = false;
        $mp_received_partial_payment_state->send_email = false;
        $mp_received_partial_payment_state->module_name = $object->name;
        $mp_received_partial_payment_state->color = '#3498D8';
        $mp_received_partial_payment_state->unremovable = true;
        $mp_received_partial_payment_state->hidden = false;
        $mp_received_partial_payment_state->logable = true;
        $mp_received_partial_payment_state->delivery = false;
        $mp_received_partial_payment_state->shipped = false;
        $mp_received_partial_payment_state->paid = true;

        if (!$mp_received_partial_payment_state->save() || !Configuration::updateValue(PaymentStatus::MP_RECEIVED_PARTIAL_PAYMENT, $mp_received_partial_payment_state->id)) {
            $object->createLog(sprintf($object->l('Error while creating order status « %s ».'), $object->l('Partially recovered')), 3);
            $successful_update &= false;
        } else {
            $successful_update &= $object->copyStatusGif($mp_received_partial_payment_state->id);
        }
    }

    if (!Configuration::get(PaymentStatus::MP_DEMO_RECEIVED_PARTIAL_PAYMENT)) {

        $mp_demo_received_partial_payment_state = new OrderState();
        $mp_demo_received_partial_payment_state->name = [];
        $mp_demo_received_partial_payment_state->name[Context::getContext()->language->id] = $object->l('Partially recovered'). ' ' . '[DEMO]';
        $mp_demo_received_partial_payment_state->invoice = false;
        $mp_demo_received_partial_payment_state->send_email = false;
        $mp_demo_received_partial_payment_state->module_name = $object->name;
        $mp_demo_received_partial_payment_state->color = '#3498D8';
        $mp_demo_received_partial_payment_state->unremovable = true;
        $mp_demo_received_partial_payment_state->hidden = false;
        $mp_demo_received_partial_payment_state->logable = true;
        $mp_demo_received_partial_payment_state->delivery = false;
        $mp_demo_received_partial_payment_state->shipped = false;
        $mp_demo_received_partial_payment_state->paid = true;

        if (!$mp_demo_received_partial_payment_state->save() || !Configuration::updateValue(PaymentStatus::MP_DEMO_RECEIVED_PARTIAL_PAYMENT, $mp_demo_received_partial_payment_state->id)) {
            $object->createLog(sprintf($object->l('Error while creating order status « %s ».'), $object->l('Demo Partially recovered ')), 3);
            $successful_update &= false;
        } else {
            $successful_update &= $object->copyStatusGif($mp_demo_received_partial_payment_state->id);
        }
    }

    return $successful_update;
}