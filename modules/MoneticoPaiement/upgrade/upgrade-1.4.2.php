<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use MoneticoPaiement\Prestashop\utilities\PaymentStatus;

function upgrade_module_1_4_2($object)
{
    $successful_update = true;

    $languages = Language::getLanguages();

    // We do this check because in a very specific case we might get an erreur. See comment below for more details
    // https://gitlab.cm-cic.fr/cmaf/ei/4000/4200/4220/emci/cms-php/prestashop-module/-/issues/63#note_4797518
    if (version_compare(_PS_VERSION_, '1.8', '<')) {
        $order_state_exists_in_database = OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_WARNING), 'configuration');
    } else {
        $order_state_exists_in_database = OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_WARNING));
    }

    if (!Configuration::get(PaymentStatus::MP_WARNING) || !$order_state_exists_in_database) {
        if (!empty(Configuration::get(PaymentStatus::MP_WARNING))) {
            $object->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_WARNING));
        }

        $mp_warning_state = new OrderState();
        $mp_warning_state->name = [];
        PaymentStatus::setMultilingualName($mp_warning_state, PaymentStatus::MP_WARNING, $languages);
        $mp_warning_state->invoice = false;
        $mp_warning_state->send_email = false;
        $mp_warning_state->module_name = $object->name;
        $mp_warning_state->color = '#f8dda7';
        $mp_warning_state->unremovable = true;
        $mp_warning_state->hidden = false;
        $mp_warning_state->logable = true;
        $mp_warning_state->delivery = false;
        $mp_warning_state->shipped = false;
        $mp_warning_state->paid = false;
        if (!$mp_warning_state->save() || !Configuration::updateValue(PaymentStatus::MP_WARNING, $mp_warning_state->id)) {
            $object->createLog(sprintf($object->l('Error while creating order status « %s ».'), $object->l('Warning')), 3);
            $successful_update &= false;
        } else {
            $successful_update &= $object->copyStatusGif($mp_warning_state->id);
        }
    }

    return $successful_update;
}