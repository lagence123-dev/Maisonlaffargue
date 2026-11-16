<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if ( ! defined('_PS_VERSION_')) {
    exit;
}

class KonectorEndpoint extends Module
{

    const LIVE_MODE_CONFIGURATION_KEY = 'KONECTORENDPOINT_LIVE_MODE';
    const VERSION_CONFIGURATION_KEY = 'KONECTORENDPOINT_VERSION';
    const API_KEY_CONFIGURATION_KEY = 'KONECTORENDPOINT_API_KEY';
    protected $config_form = false;

    public function __construct()
    {
        $this->name          = 'konectorendpoint';
        $this->tab           = 'export';
        $this->version       = '0.1.3';
        $this->author        = 'KONECTOR';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Konector Endpoint');
        $this->description = $this->l(
            'Enables Konector to fetch data from your store!'
        );

        $this->confirmUninstall = $this->l(
            'You are going to uninstall Konector Endpoint. Are you sure?'
        );
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue(self::LIVE_MODE_CONFIGURATION_KEY, false);

        return parent::install()
               && $this->registerHooks();
    }

    public function uninstall()
    {
        Configuration::deleteByName(self::LIVE_MODE_CONFIGURATION_KEY);

        return parent::uninstall();
    }

    public function registerHooks() {
        return $this->registerHook('header')
               && $this->registerHook('moduleRoutes')
               && $this->registerHook('backOfficeHeader');
    }

    /*
	 * Blog upgrade process
	 * @return bool
	 */
    public function upgradeModuleAction()
    {
        $currentHooks = Db::getInstance()->executeS('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook_module` WHERE `id_module` = '.(int)$this->id);
        foreach ($currentHooks as $row) {
            $this->unregisterHook((int)$row['id_hook']);
        }
        $this->registerHooks();
        Configuration::updateValue(self::VERSION_CONFIGURATION_KEY, $this->version);
        return true;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitKonectorEndpointModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch(
            $this->local_path.'views/templates/admin/configure.tpl'
        );
        $output .= $this->renderForm();

        return $output;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return [
            'KONECTORENDPOINT_LIVE_MODE'   => Configuration::get(
                'KONECTORENDPOINT_LIVE_MODE',
                true
            ),
            self::API_KEY_CONFIGURATION_KEY => Configuration::get(
                self::API_KEY_CONFIGURATION_KEY,
                null
            ),
        ];
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar             = false;
        $helper->table                    = $this->table;
        $helper->module                   = $this;
        $helper->default_form_language    = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get(
            'PS_BO_ALLOW_EMPLOYEE_FORM_LANG',
            0
        );

        $helper->identifier    = $this->identifier;
        $helper->submit_action = 'submitKonectorEndpointModule';
        $helper->currentIndex  = $this->context->link->getAdminLink(
                'AdminModules',
                false
            )
                                 .'&configure='.$this->name.'&tab_module='
                                 .$this->tab.'&module_name='.$this->name;
        $helper->token         = Tools::getAdminTokenLite('AdminModules');

        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='
                          .$this->name.'&save'.$this->name.
                          '&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&token='
                          .Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list'),
            ],
        ];

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            /* Add values for your inputs */
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon'  => 'icon-cogs',
                ],
                'input'  => [
                    [
                        'type'    => 'switch',
                        'label'   => $this->l('Live mode'),
                        'name'    => 'KONECTORENDPOINT_LIVE_MODE',
                        'is_bool' => true,
                        'desc'    => $this->l('Use this module in live mode'),
                        'values'  => [
                            [
                                'id'    => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id'    => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type'   => 'text',
                        'length' => 32,
                        'name'   => self::API_KEY_CONFIGURATION_KEY,
                        'label'  => $this->l('API Key'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS(
                $this->_path.'views/css/back.css'
            );
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    /*
	 * Blog module url routes
	 * @param $params
	 * @return array|null
	 */
    public function hookModuleRoutes($params)
    {

        if ( ! Configuration::get('PS_REWRITING_SETTINGS')) {
            return null;
        }

        $my_link = [];

        $my_link['konectorendpoint_orders'] = [
            'controller' => 'orders',
            'rule'       => '/orders',
            'keywords'   => [],
            'params'     => [
                'fc'     => 'module',
                'module' => 'konectorendpoint',
            ],
        ];

        return $my_link;
    }

    static function allowApiRequest()
    {
        $apiKey = Tools::getValue('apiKey');
        if (is_null($apiKey)) {
            dieError('No api key');
        }

        // Validate api key
        $expectedApiKey = Configuration::get(KonectorEndpoint::API_KEY_CONFIGURATION_KEY);
        if ($expectedApiKey !== $apiKey) {
            $response = [
                'status' => 'error',
                'message' => 'invalid request'
            ];
            header('Content-Type: application/json', true, 400);
            echo json_encode($response);
            die();
        }
    }
}
