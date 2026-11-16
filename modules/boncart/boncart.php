<?php
/**
 * 2015-2020 Bonpresta
 *
 * Bonpresta Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the General Public License (GPL 2.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/GPL-2.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the module to newer
 * versions in the future.
 *
 *  @author    Bonpresta
 *  @copyright 2015-2020 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;

class Boncart extends Module
{
    public function __construct()
    {
        $this->name = 'boncart';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->bootstrap = true;
        $this->author = 'Bonpresta';
        $this->module_key = '501dfbfe7bed75b0385446410aff54db';
        parent::__construct();
        $this->default_language = Language::getLanguage(Configuration::get('PS_LANG_DEFAULT'));
        $this->id_shop = Context::getContext()->shop->id;
        $this->displayName = $this->l('Shopping cart');
        $this->description = $this->l('Display shopping cart.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    protected function getConfigurations()
    {
        $configurations = array(
            'CART_ENABLE' => true,
            'CART_ENABLE_RELATED_PRODUCT'  => true,
            // 'CART_RELATED_PRODUCT_LIMIT' => 5,
        );

        return $configurations;
    }

    public function install()
    {
        $configurations = $this->getConfigurations();

        foreach ($configurations as $name => $config) {
            Configuration::updateValue($name, $config);
        }

        return parent::install() &&
        $this->registerHook('displayBonCart') &&
        $this->registerHook('displayBonCartAccessories') &&
        $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        $configurations = $this->getConfigurations();

        foreach (array_keys($configurations) as $config) {
            Configuration::deleteByName($config);
        }

        return parent::uninstall();
    }

    public function getContent()
    {
        $output = '';
        $result = '';

        if ((bool)Tools::isSubmit('submitSettings')) {
            if (!$result = $this->preValidateForm()) {
                $output .= $this->postProcess();
                $output .= $this->displayConfirmation($this->l('Save all settings.'));
            } else {
                $output = $result;
                $output .= $this->renderTabForm();
            }
        }

        if (!$result) {
            $output .= $this->renderTabForm();
        }

        return $output;
    }

    protected function renderTabForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable:'),
                        'name' => 'CART_ENABLE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'enable',
                                'value' => 1,
                                'label' => $this->l('Yes')),
                            array(
                                'id' => 'disable',
                                'value' => 0,
                                'label' => $this->l('No')),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable related product display:'),
                        'name' => 'CART_ENABLE_RELATED_PRODUCT',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'enable',
                                'value' => 1,
                                'label' => $this->l('Yes')),
                            array(
                                'id' => 'disable',
                                'value' => 0,
                                'label' => $this->l('No')),
                        ),
                    ),
                    // array(
                    //     'type' => 'text',
                    //     'label' => $this->l('Show related product quantity'),
                    //     'name' => 'CART_RELATED_PRODUCT_LIMIT',
                    //     'col' => 2,
                    // ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSettings';
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        $fields = array();
        $configurations = $this->getConfigurations();

        foreach (array_keys($configurations) as $config) {
            $fields[$config] = Configuration::get($config);
        }

        return $fields;
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFieldsValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    protected function preValidateForm()
    {
        $errors = array();

        /*if (Tools::isEmpty(Tools::getValue('CART_PHONE'))) {
            $errors[] = $this->l('The id phone required.');
        } else {
            if (!Validate::isPhoneNumber(Tools::getValue('CART_PHONE'))) {
                $errors[] = $this->l('Bad phone format');
            }
        }

        if (!Validate::isColor(Tools::getValue('CART_COLOR'))) {
            $errors[] = $this->l('"Background button" format error.');
        }
*/
        if (count($errors)) {
            return $this->displayError(implode('<br />', $errors));
        }

        return false;
    }

    public function hookDisplayHeader()
    {
        if (Configuration::get('CART_ENABLE')) {
            $this->context->controller->addJS($this->_path . '/views/js/boncart.js');
            $this->context->controller->addCSS($this->_path . '/views/css/boncart.css');
        }
    }

    private function getCartSummaryURL()
    {
        return $this->context->link->getPageLink(
            'cart',
            null,
            $this->context->language->id,
            array(
                'action' => 'show'
            ),
            false,
            null,
            true
        );
    }

    public function hookDisplayBonCart()
    {
        if (Configuration::get('CART_ENABLE')) {
            $cart_url = $this->getCartSummaryURL();
            $this->context->smarty->assign(array(
                'cart_url' => $cart_url
            ));
            return $this->display($this->_path, '/views/templates/hook/boncart.tpl');
        }
    }

    public function hookDisplayNav1()
    {
        return $this->hookDisplayHeader();
    }

    public function hookDisplayBonCartAccessories($params) {
        if (Configuration::get('CART_ENABLE_RELATED_PRODUCT')) {
            $result = [];

            $product = new Product($params['id_product']);

            $accessories = $product->getAccessories($this->context->language->id);

            if (is_array($accessories)) {
                foreach ($accessories as $key => $accessory) {
                    $image = new Image();
                    $product_accessories = (new ProductAssembler($this->context))->assembleProduct(['id_product' => $accessory['id_product']]);
                    $presenterFactory = new ProductPresenterFactory($this->context);
                    $presentationSettings = $presenterFactory->getPresentationSettings();
                    $presenter = new ProductListingPresenter(new ImageRetriever($this->context->link), $this->context->link, new PriceFormatter(), new ProductColorsRetriever(), $this->context->getTranslator());
                    $result[$key]['info'] = $presenter->present($presentationSettings, $product_accessories, $this->context->language);
                    $result[$key]['image'] = $image->getCover($accessory['id_product']);
                }
            }

            $this->context->smarty->assign([
                'result' => $result,
                'limit'=> 5,
            ]);

            // $this->context->smarty->assign([
            //     'result' => $result,
            //     'limit'=> Configuration::get('CART_RELATED_PRODUCT_LIMIT'),
            // ]);
            
            return $this->fetch('module:boncart/views/templates/hook/boncart-accessories.tpl');
        }
    }
}
