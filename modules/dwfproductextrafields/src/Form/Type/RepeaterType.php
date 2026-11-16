<?php
/**
 *   Copyright since 2009 ohmyweb!
 *
 *   @author    ohmyweb <contact@ohmyweb.fr>
 *   @copyright since 2009 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
 */

namespace DwfProductExtraFields\Form\Type;

if (!defined('_PS_VERSION_')) {
    exit;
}

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RepeaterType extends FormType
{
    public function getBlockPrefix(): string
    {
        return 'dwfproductextrafields_repeater';
    }

    public function getParent(): string
    {
        return FormType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        // Les données du repeater sont déjà passées via $options['data'] qui sera accessible via view.vars['data']
        // ou directement fusionnées si on les définit ici explicitement pour le template.
        if (isset($options['data']) && is_array($options['data'])) {
            $view->vars = array_merge($view->vars, $options['data']);
        }

        // On injecte les variables globales nécessaires au template field_repeater.html.twig
        if (isset($options['locales'])) {
            $view->vars['languages'] = $options['locales'];
        }
        $view->vars['defaultFormLanguage'] = (int)\Configuration::get('PS_LANG_DEFAULT');
        $view->vars['defaultLanguageIso'] = \Language::getIsoById($view->vars['defaultFormLanguage']);
        $view->vars['dwfproductextrafields_adminController'] = \Context::getContext()->link->getAdminLink('AdminAjaxDwfProductExtraFields', true, [], ['id_product' => $options['productId']]);
        $view->vars['productId'] = $options['productId'];
        $view->vars['values'] = $options['values'];
        $view->vars['elements'] = $options['elements'];
        $view->vars['collapsed_default'] = $options['collapsed_default'];

        // On injecte explicitement les options de l'UI Kit de PrestaShop dans les variables de vue
        // car elles sont requises par les templates comme prestashop_ui_kit_base.html.twig.
        $view->vars['column_breaker'] = $options['column_breaker'];
        $view->vars['columns_number'] = $options['columns_number'];
        $view->vars['label_tag_name'] = $options['label_tag_name'];
        $view->vars['label_subtitle'] = $options['label_subtitle'];
        $view->vars['label_help_box'] = $options['label_help_box'];
        if (!empty($options['external_link'])) {
            $view->vars['external_link'] = $options['external_link'];
        }
        $view->vars['modify_all_shops'] = $options['modify_all_shops'];
        $view->vars['multistore_dropdown'] = $options['multistore_dropdown'];
        $view->vars['multistore_configuration_key'] = $options['multistore_configuration_key'];
        $view->vars['disabling_switch'] = $options['disabling_switch'];
        $view->vars['disabling_switch_label'] = $options['disabling_switch_label'];
        $view->vars['disabling_switch_event'] = $options['disabling_switch_event'];
        $view->vars['switch_state_on_disable'] = $options['switch_state_on_disable'];
        $view->vars['disabled_value'] = $options['disabled_value'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'locales' => [],
            'form_theme' => '@Modules/dwfproductextrafields/views/templates/admin/field_repeater.html.twig',
            'data_class' => null,
            'productId' => null,
            'values' => [],
            'elements' => [],
            'collapsed_default' => false,
            'allow_extra_fields' => true,
            'column_breaker' => false,
            'columns_number' => 1,
            'label_tag_name' => 'label',
            'label_subtitle' => '',
            'label_help_box' => '',
            'external_link' => null,
            'modify_all_shops' => false,
            'multistore_dropdown' => false,
            'multistore_configuration_key' => '',
            'disabling_switch' => false,
            'disabling_switch_label' => false,
            'disabling_switch_event' => null,
            'switch_state_on_disable' => 'off',
            'disabled_value' => null,
        ]);
    }
}
