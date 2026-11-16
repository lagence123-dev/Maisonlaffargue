<?php
/**
 * 2015-2020 Bonpresta
 *
 * Bonpresta HTML Content
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

include_once _PS_MODULE_DIR_ . 'bonconsentgdpr/classes/ClassBonconsentgdpr.php';

class Bonconsentgdpr extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'bonconsentgdpr';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->author = 'Bonpresta';
        $this->module_key = '9ae2e17e4bb93dd10507ed1d016c04c0';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();
        $this->default_language = Language::getLanguage(Configuration::get('PS_LANG_DEFAULT'));
        $this->id_shop = Context::getContext()->shop->id;
        $this->displayName = $this->l('Cookie consent gdpr');
        $this->description = $this->l('Display html content');
        $this->confirmUninstall = $this->l('This module  Uninstall');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        if (Configuration::get('PS_SSL_ENABLED')) {
            $this->ssl = 'https://';
        } else {
            $this->ssl = 'http://';
        }
    }

    public function createAjaxController()
    {
        $tab = new Tab();
        $tab->active = 1;
        $languages = Language::getLanguages(false);
        if (is_array($languages)) {
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = 'bonconsentgdpr';
            }
        }
        $tab->class_name = 'AdminAjaxBonconsentgdpr';
        $tab->module = $this->name;
        $tab->id_parent = -1;

        return (bool) $tab->add();
    }

    private function removeAjaxContoller()
    {
        if ($tab_id = (int) Tab::getIdFromClassName('AdminAjaxBonconsentgdpr')) {
            $tab = new Tab($tab_id);
            $tab->delete();
        }

        return true;
    }

    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';
        $this->installSamples();
        $settings = $this->getModuleSettings();

        foreach ($settings as $name => $value) {
            Configuration::updateValue($name, $value);
        }

        return parent::install()
        && $this->registerHook('displayHeader')
        && $this->createAjaxController()
        && $this->registerHook('displayBackOfficeHeader');
    }

    protected function installSamples()
    {
        $languages = Language::getLanguages(false);
        for ($i = 1; $i <= 1; ++$i) {
            $item = new ClassBonconsentgdpr();
            $item->id_shop = (int) $this->context->shop->id;
            $item->status = 1;
            $item->sort_order = $i;

            if ($i = 1) {
                $item->description = "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
                    ga('create', 'GOOGLE_PROPERTY_ID_GOES_HERE', 'auto');
                    ga('send', 'pageview');";
                foreach ($languages as $language) {
                    $item->title[$language['id_lang']] = 'Google Analytics';
                }
            } elseif ($i = 2) {
                $item->description = "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                    '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                })(window,document,'script','dataLayer','GTM-XXXXXXX');";
                foreach ($languages as $language) {
                    $item->title[$language['id_lang']] = 'Google Tag';
                }
            } elseif ($i = 3) {
                $item->description = " !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                    n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
                    n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
                    t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
                    document,'script','https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', 'FACEBOOK_PIXEL_ID');
                fbq('track', 'PageView');";
                foreach ($languages as $language) {
                    $item->title[$language['id_lang']] = 'Google Tag';
                }
            }

            $item->cookie_type = 'tracking';

            foreach ($languages as $language) {
                $item->title[$language['id_lang']] = 'Google Analytics';
            }
            $item->add();
        }
    }

    public function uninstall()
    {
        include dirname(__FILE__) . '/sql/uninstall.php';

        $settings = $this->getModuleSettings();

        foreach (array_keys($settings) as $name) {
            Configuration::deleteByName($name);
        }

        return parent::uninstall()
        && $this->removeAjaxContoller();
    }

    protected function getModuleSettings()
    {
        $settings = [
            'BON_CONSENT_GDPR_SHOW_CHANGE_PREFERENCES' => true,
            'BON_CONSENT_GDPR_STYLE' => 'simple',
            'BON_CONSENT_GDPR_COLOR_PRIVACY_LINK' => '',
        ];

        return $settings;
    }

    public function getContent()
    {
        $output = '';
        $result = '';

        if (((bool) Tools::isSubmit('submitBonconsentgdprSettingModule')) == true) {
            if (!$errors = $this->validateSettings()) {
                $this->postProcess();
                $output .= $this->displayConfirmation($this->l('Settings updated successful.'));
            } else {
                $output .= $errors;
            }
        }

        if ((bool) Tools::isSubmit('submitUpdateBonconsentgdpr')) {
            if (!$result = $this->preValidateForm()) {
                $output .= $this->addBonconsentgdpr();
            } else {
                $output = $result;
                $output .= $this->renderBonconsentgdprForm();
            }
        }

        if ((bool) Tools::isSubmit('statusbonBonconsentgdpr')) {
            $output .= $this->updateStatusTab();
        }

        if ((bool) Tools::isSubmit('deletebonconsentgdpr')) {
            $output .= $this->deleteBonconsentgdpr();
        }

        if (Tools::getIsset('updatebonconsentgdpr') || Tools::getValue('updatebonconsentgdpr')) {
            $output .= $this->renderBonconsentgdprForm();
        } elseif ((bool) Tools::isSubmit('addbonconsentgdpr')) {
            $output .= $this->renderBonconsentgdprForm();
        } elseif (!$result) {
            $output .= $this->renderBonconsentgdprList();
            $output .= $this->renderFormSettings();
        }

        return $output;
    }

    protected function renderFormSettings()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitBonconsentgdprSettingModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'image_path' => $this->_path . 'views/img',
            'fields_value' => $this->getConfigFormValuesSettings(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'form_group_class' => 'display-block-bonconsentgdpr',
                        'type' => 'switch',
                        'label' => $this->l('Show "change my preferences" button:'),
                        'name' => 'BON_CONSENT_GDPR_SHOW_CHANGE_PREFERENCES',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Style for your GDPR banner:'),
                        'name' => 'BON_CONSENT_GDPR_STYLE',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'simple',
                                    'name' => $this->l('Simple')],
                                [
                                    'id' => 'headline',
                                    'name' => $this->l('Headline')],
                                [
                                    'id' => 'interstitial',
                                    'name' => $this->l('Interstitial')],
                                [
                                    'id' => 'standalone',
                                    'name' => $this->l('Standalone')],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Privacy Policy Link:'),
                        'placeholder' => 'https://example.com/privacy-policy',
                        'name' => 'BON_CONSENT_GDPR_COLOR_PRIVACY_LINK',
                        'lang' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    protected function validateSettings()
    {
        $errors = [];

        // if (Tools::isEmpty(Tools::getValue('BON_CONSENT_GDPR_CAROUSEL_NB'))) {
        //     $errors[] = $this->l('Item is required.');
        // } else {
        //     if (!Validate::isUnsignedInt(Tools::getValue('BON_CONSENT_GDPR_CAROUSEL_NB'))) {
        //         $errors[] = $this->l('Bad item format');
        //     }
        // }

        if ($errors) {
            return $this->displayError(implode('<br />', $errors));
        } else {
            return false;
        }
    }

    protected function getConfigFormValuesSettings()
    {
        $filled_settings = [];
        $settings = $this->getModuleSettings();

        $languages = Language::getLanguages(false);

        foreach (array_keys($settings) as $name) {
            if ('BON_CONSENT_GDPR_COLOR_PRIVACY_LINK' == $name) {
                foreach ($languages as $lang) {
                    $filled_settings[$name][$lang['id_lang']] = Tools::getValue('BON_CONSENT_GDPR_COLOR_PRIVACY_LINK_' . $lang['id_lang'], Configuration::get('BON_CONSENT_GDPR_COLOR_PRIVACY_LINK', $lang['id_lang']));
                }
            } else {
                $filled_settings[$name] = Configuration::get($name);
            }
        }

        return $filled_settings;
    }

    protected function getStringValueType($string)
    {
        if (Validate::isInt($string)) {
            return 'int';
        } elseif (Validate::isFloat($string)) {
            return 'float';
        } elseif (Validate::isBool($string)) {
            return 'bool';
        } else {
            return 'string';
        }
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValuesSettings();
        $languages = Language::getLanguages(false);
        $values = [];

        foreach (array_keys($form_values) as $key) {
            if ('BON_CONSENT_GDPR_COLOR_PRIVACY_LINK' == $key) {
                foreach ($languages as $lang) {
                    $values['BON_CONSENT_GDPR_COLOR_PRIVACY_LINK'][$lang['id_lang']] = Tools::getValue('BON_CONSENT_GDPR_COLOR_PRIVACY_LINK_' . $lang['id_lang']);
                }
                Configuration::updateValue('BON_CONSENT_GDPR_COLOR_PRIVACY_LINK', $values['BON_CONSENT_GDPR_COLOR_PRIVACY_LINK']);
            } else {
                Configuration::updateValue($key, Tools::getValue($key));
            }
        }
    }

    protected function getBonconsentgdprSettings()
    {
        $settings = $this->getModuleSettings();
        $get_settings = [];
        foreach (array_keys($settings) as $name) {
            $data = Configuration::get($name);
            $get_settings[$name] = ['value' => $data, 'type' => $this->getStringValueType($data)];
        }

        return $get_settings;
    }

    protected function renderBonconsentgdprForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => ((int) Tools::getValue('id_tab') ? $this->l('Update html content') : $this->l('Add html content')),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Title'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => true,
                        'col' => 3,
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Enter URL'),
                        'name' => 'cookie_type',
                        'desc' => '<p>Strictly necessary - For example, account login related cookies. Strictly necessary cookies are loaded automatically and cannot be disabled by the user.</p><p>Functionality - For example, remember users language preferences or remembering various choices.</p><p>Tracking and Performance - For example, analytics such as Google Analytics.</p><p>Targeting and Advertising - For example, Google AdSense/AdWords remarketing.</p>',
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'strictly-necessary',
                                    'name' => $this->l('Strictly necessary')],
                                [
                                    'id' => 'functionality',
                                    'name' => $this->l('Functionality')],
                                [
                                    'id' => 'tracking',
                                    'name' => $this->l('Tracking and Performance')],
                                [
                                    'id' => 'targeting',
                                    'name' => $this->l('Targeting and Advertising')],
                            ],
                            'id' => 'id',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Content'),
                        'name' => 'description',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Status'),
                        'name' => 'status',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled'),
                            ],
                        ],
                    ],
                    [
                        'col' => 2,
                        'type' => 'text',
                        'name' => 'sort_order',
                        'class' => 'hidden',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
                'buttons' => [
                    [
                        'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Back to list'),
                        'icon' => 'process-icon-back',
                    ],
                ],
            ],
        ];

        if ((bool) Tools::getIsset('updatebonconsentgdpr') && (int) Tools::getValue('id_tab') > 0) {
            $tab = new ClassBonconsentgdpr((int) Tools::getValue('id_tab'));
            $fields_form['form']['input'][] = ['type' => 'hidden', 'name' => 'id_tab', 'value' => (int) $tab->id];
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitUpdateBonconsentgdpr';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigBonconsentgdprFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form]);
    }

    protected function getConfigBonconsentgdprFormValues()
    {
        if ((bool) Tools::getIsset('updatebonconsentgdpr') && (int) Tools::getValue('id_tab') > 0) {
            $tab = new ClassBonconsentgdpr((int) Tools::getValue('id_tab'));
        } else {
            $tab = new ClassBonconsentgdpr();
        }

        $fields_values = [
            'id_tab' => Tools::getValue('id_tab'),
            'title' => Tools::getValue('title', $tab->title),
            'cookie_type' => Tools::getValue('cookie_type', $tab->cookie_type),
            'status' => Tools::getValue('status', $tab->status),
            'description' => Tools::getValue('description', $tab->description),
            'sort_order' => Tools::getValue('sort_order', $tab->sort_order),
        ];

        return $fields_values;
    }

    public function renderBonconsentgdprList()
    {
        if (!$tabs = ClassBonconsentgdpr::getBonconsentgdprList()) {
            $tabs = [];
        }

        $fields_list = [
            'id_tab' => [
                'title' => $this->l('Id'),
                'type' => 'text',
                'col' => 6,
                'search' => false,
                'orderby' => false,
            ],
            'title' => [
                'title' => $this->l('Title'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
            ],
            'status' => [
                'title' => $this->l('Status'),
                'type' => 'bool',
                'active' => 'status',
                'search' => false,
                'orderby' => false,
            ],
            'sort_order' => [
                'title' => $this->l('Position'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'class' => 'pointer dragHandle',
            ],
        ];

        $helper = new HelperList();

        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_tab';
        $helper->table = 'bonconsentgdpr';
        $helper->actions = ['edit', 'delete'];
        $helper->show_toolbar = true;
        $helper->module = $this;
        $helper->title = $this->displayName;
        $helper->listTotal = count($tabs);
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->toolbar_btn['new'] = [
            'href' => AdminController::$currentIndex
                . '&configure=' . $this->name . '&add' . $this->name
                . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new item'),
        ];
        $helper->currentIndex = AdminController::$currentIndex
            . '&configure=' . $this->name . '&id_shop=' . (int) $this->context->shop->id;

        $helper->tpl_vars = [
            'link' => new Link(),
            'base_dir' => $this->ssl,
            'ps_version' => _PS_VERSION_,
            'lang_iso' => $this->context->language->iso_code,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => $this->_path . 'images/',
        ];

        return $helper->generateList($tabs, $fields_list);
    }

    protected function addBonconsentgdpr()
    {
        $errors = [];

        if ((int) Tools::getValue('id_tab') > 0) {
            $item = new ClassBonconsentgdpr((int) Tools::getValue('id_tab'));
        } else {
            $item = new ClassBonconsentgdpr();
        }

        $item->id_shop = (int) $this->context->shop->id;
        $item->status = (int) Tools::getValue('status');
        $item->cookie_type = Tools::getValue('cookie_type');
        $item->description = Tools::getValue('description');

        if ((int) Tools::getValue('id_tab') > 0) {
            $item->sort_order = Tools::getValue('sort_order');
        } else {
            $item->sort_order = $item->getMaxSortOrder((int) $this->id_shop);
        }

        $languages = Language::getLanguages(false);

        foreach ($languages as $language) {
            $item->title[$language['id_lang']] = Tools::getValue('title_' . $language['id_lang']);
        }

        if (!$errors) {
            if (!Tools::getValue('id_tab')) {
                if (!$item->add()) {
                    return $this->displayError($this->l('The item could not be added.'));
                }
            } elseif (!$item->update()) {
                return $this->displayError($this->l('The item could not be updated.'));
            }

            return $this->displayConfirmation($this->l('The item is saved.'));
        } else {
            return $this->displayError($this->l('Unknown error occurred.'));
        }
    }

    protected function preValidateForm()
    {
        $errors = [];

        if (Tools::isEmpty(Tools::getValue('title_' . $this->default_language['id_lang']))) {
            $errors[] = $this->l('The title is required.');
        } elseif (!Validate::isGenericName(Tools::getValue('title_' . $this->default_language['id_lang']))) {
            $errors[] = $this->l('Bad title format.');
        }

        if (count($errors)) {
            return $this->displayError(implode('<br />', $errors));
        }

        return false;
    }

    protected function deleteBonconsentgdpr()
    {
        $tab = new ClassBonconsentgdpr(Tools::getValue('id_tab'));
        $res = $tab->delete();

        if (!$res) {
            return $this->displayError($this->l('Error occurred when deleting the tab'));
        }

        return $this->displayConfirmation($this->l('The tab is successfully deleted'));
    }

    protected function updateStatusTab()
    {
        $tab = new ClassBonconsentgdpr(Tools::getValue('id_tab'));

        if (1 == $tab->status) {
            $tab->status = 0;
        } else {
            $tab->status = 1;
        }

        if (!$tab->update()) {
            return $this->displayError($this->l('The tab status could not be updated.'));
        }

        return $this->displayConfirmation($this->l('The tab status is successfully updated.'));
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') != $this->name) {
            return;
        }
        Media::addJsDefL('ajax_theme_url', $this->context->link->getAdminLink('AdminAjaxBonconsentgdpr'));
        $this->context->smarty->assign('ajax_theme_url', $this->context->link->getAdminLink('AdminAjaxBonconsentgdpr'));
        $this->context->controller->addJquery();
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addJS($this->_path . 'views/js/bonconsentgdpr_back.js');
        $this->context->controller->addCSS($this->_path . 'views/css/bonconsentgdpr_back.css');
    }

    public function hookdisplayHeader()
    {
        $bonconsentgdpr_front = new ClassBonconsentgdpr();
        $tabs = $bonconsentgdpr_front->getTopFrontItems($this->id_shop, true);
        $result = [];

        foreach ($tabs as $key => $tab) {
            $result[$key]['title'] = $tab['title'];
            $result[$key]['description'] = $tab['description'];
            $result[$key]['cookie_type'] = $tab['cookie_type'];
        }

        $this->context->controller->registerStylesheet('modules-bonconsentgdpr-css', 'modules/' . $this->name . '/views/css/bonconsentgdpr_front.css');

        $this->smarty->assign([
            'bon_consent_gdpr_color_privacy_link' => Configuration::get('BON_CONSENT_GDPR_COLOR_PRIVACY_LINK', $this->context->language->id),
            'bon_consent_gdpr_style' => Configuration::get('BON_CONSENT_GDPR_STYLE'),
            'bon_consent_gdpr_show_change_preferences' => Configuration::get('BON_CONSENT_GDPR_SHOW_CHANGE_PREFERENCES'),
            'items' => $result,
        ]);

        Media::addJsDefL('bon_consent_nb_text', $this->l('We use cookies to give you the best possible experience on our website. By clicking OK, you agree to our cookie policy. If you would like to change your cookie preferences you may do so'));
        Media::addJsDefL('bon_consent_nb_title', $this->l('We use cookies'));
        Media::addJsDefL('bon_consent_nb_agree', $this->l('I agree'));
        Media::addJsDefL('bon_consent_nb_reject', $this->l('I decline'));
        Media::addJsDefL('bon_consent_nb_changep', $this->l('Change my preferences'));
        Media::addJsDefL('bon_consent_pc_title', $this->l('Cookies Preferences Center'));
        Media::addJsDefL('bon_consent_pc_yprivacy_title', $this->l('Your privacy'));
        Media::addJsDefL('bon_consent_pc_yprivacy_text_1', $this->l('Your privacy is important to us'));
        Media::addJsDefL('bon_consent_pc_yprivacy_text_2', $this->l('Cookies are very small text files that are stored on your computer when you visit a website. We use cookies for a variety of purposes and to enhance your online experience on our website (for example, to remember your account login details).'));
        Media::addJsDefL('bon_consent_pc_yprivacy_text_3', $this->l('You can change your preferences and decline certain types of cookies to be stored on your computer while browsing our website. You can also remove any cookies already stored on your computer, but keep in mind that deleting cookies may prevent you from using parts of our website.'));
        Media::addJsDefL('bon_consent_pc_sncssr_text_1', $this->l('Strictly necessary cookies'));
        Media::addJsDefL('bon_consent_pc_sncssr_text_2', $this->l('These cookies are essential to provide you with services available through our website and to enable you to use certain features of our website.'));
        Media::addJsDefL('bon_consent_pc_sncssr_text_3', $this->l('Without these cookies, we cannot provide you certain services on our website.'));
        Media::addJsDefL('bon_consent_pc_fnct_text_1', $this->l('Functionality cookies'));
        Media::addJsDefL('bon_consent_pc_fnct_text_2', $this->l('These cookies are used to provide you with a more personalized experience on our website and to remember choices you make when you use our website.'));
        Media::addJsDefL('bon_consent_pc_fnct_text_3', $this->l('For example, we may use functionality cookies to remember your language preferences or remember your login details.'));
        Media::addJsDefL('bon_consent_pc_trck_text_1', $this->l('Tracking cookies'));
        Media::addJsDefL('bon_consent_pc_trck_text_2', $this->l('These cookies are used to collect information to analyze the traffic to our website and how visitors are using our website.'));
        Media::addJsDefL('bon_consent_pc_trck_text_3', $this->l('For example, these cookies may track things such as how long you spend on the website or the pages you visit which helps us to understand how we can improve our website for you.'));
        Media::addJsDefL('bon_consent_pc_trck_text_4', $this->l('The information collected through these tracking and performance cookies do not identify any individual visitor.'));
        Media::addJsDefL('bon_consent_pc_trgt_text_1', $this->l('Targeting and advertising cookies'));
        Media::addJsDefL('bon_consent_pc_trgt_text_2', $this->l('These cookies are used to show advertising that is likely to be of interest to you based on your browsing habits.'));
        Media::addJsDefL('bon_consent_pc_trgt_text_3', $this->l("These cookies, as served by our content and/or advertising providers, may combine information they collected from our website with other information they have independently collected relating to your web browser's activities across their network of websites."));
        Media::addJsDefL('bon_consent_pc_trgt_text_4', $this->l('If you choose to remove or disable these targeting or advertising cookies, you will still see adverts but they may not be relevant to you.'));
        Media::addJsDefL('bon_consent_pc_minfo_text_1', $this->l('More information'));
        Media::addJsDefL('bon_consent_pc_minfo_text_2', $this->l('For any queries in relation to our policy on cookies and your choices, please contact us.'));
        Media::addJsDefL('bon_consent_pc_minfo_text_3', $this->l('To find out more, please visit our'));
        Media::addJsDefL('bon_consent_pc_save', $this->l('Save my preferences'));
        Media::addJsDefL('bon_consent_nb_ok', $this->l('OK'));
        Media::addJsDefL('bon_consent_inactive', $this->l('Inactive'));
        Media::addJsDefL('bon_consent_always_active', $this->l('Always active'));
        Media::addJsDefL('bon_consent_active', $this->l('Active'));
        Media::addJsDefL('bon_consent_privacy_policy', $this->l('Privacy Policy'));
        Media::addJsDefL('bon_consent_impressum', $this->l('Impressum'));

        return $this->display($this->_path, '/views/templates/hook/bonconsentgdpr-header.tpl');
    }
}
