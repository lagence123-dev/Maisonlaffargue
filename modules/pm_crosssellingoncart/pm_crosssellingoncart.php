<?php
/**
 * Cross Selling On Cart
 *
 * @author    Presta-Module.com <support@presta-module.com> - https://www.presta-module.com
 * @copyright Presta-Module 2024 - https://www.presta-module.com
 * @license   see file: LICENSE.txt
 *
 * @version   2.8.0
 *
 *           ____     __  __
 *          |  _ \   |  \/  |
 *          | |_) |  | |\/| |
 *          |  __/   | |  | |
 *          |_|      |_|  |_|
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once dirname(__FILE__) . '/vendor/autoload.php';
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
class pm_crosssellingoncart extends Module implements WidgetInterface
{
    public static $modulePrefix = 'CSOC';
    public $baseConfigUrl;
    private $defaultLanguage;
    private $isoLang;
    private $languages;
    public $prefixFieldsOptions = 'PM_CSOC';
    protected $copyrightLink = [
        'link' => '',
        'img' => '//www.presta-module.com/img/logo-module.JPG',
    ];
    protected $supportLinks;
    const DYNAMIC_CSS = 'views/css/pm_crosssellingoncart_dynamic.css';
    const DEFAULT_NB_ACCESSORIES = 3;
    const DEFAULT_NB_CROSSSELLING = 3;
    protected $defaultConfiguration = [
        'TITLE_BLOC' => [],
        'IMAGE_SIZE' => 'home_' . 'default',
        'DISPLAY_IN_PRODUCT' => false,
        'DISPLAY_IN_CART' => true,
        'IDENTICAL_PRODUCTY' => false,
        'NB_PRODUCT' => 4,
        'NB_PRODUCT_CART' => 2,
        'NB_PRODUCT_TABLET' => 2,
        'NB_PRODUCT_MOBILE' => 1,
        'DISPLAY_IMG' => true,
        'DISPLAY_TITLE' => true,
        'DISPLAY_PRICE' => true,
        'DISPLAY_BUTTON' => true,
        'DISPLAY_AVAILABILITY' => true,
        'ACCESSORIES' => true,
        'NB_ACCESSORIES' => self::DEFAULT_NB_ACCESSORIES,
        'ACCESSORIES_IN_PRODUCT' => false,
        'CROSSSELLING' => true,
        'NB_CROSSSELLING' => self::DEFAULT_NB_CROSSSELLING,
        'CROSSSELLING_NB_DAYS' => 160,
        'PRODUCT_SELECTION' => [],
        'ADVANCED_STYLES' => '',
        'LAST_VERSION' => null,
        'AM' => [],
        'AMD' => null,
        'PMM' => [],
        'PMMD' => null,
    ];
    protected $casPieAieKeys = [
        'TITLE_BLOC',
        'ADVANCED_STYLES',
        'LAST_VERSION',
        'AM',
        'AMD',
        'PMM',
        'PMMD',
    ];
    public function __construct()
    {
        $this->name = 'pm_crosssellingoncart';
        $this->tab = 'merchandizing';
        $this->version = '2.8.0';
        $this->author = 'Presta-Module';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '59efac46ab4b457a5a71fef1941f8c40';
        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => _PS_VERSION_,
        ];
        parent::__construct();
        $this->languages = Language::getLanguages(false);
        $this->isoLang = Language::getIsoById($this->context->cookie->id_lang);
        $this->defaultLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
        foreach ($this->languages as $lang) {
            $this->defaultConfiguration['TITLE_BLOC'][(int)$lang['id_lang']] = '';
        }
        $controller = Tools::getValue('controller');
        $this->baseConfigUrl = $_SERVER['SCRIPT_NAME'] . (!empty($controller) ? '?controller=' . $controller : '') . '&configure=' . $this->name . '&token=' . Tools::getValue('token');
        $this->displayName = 'Cross Selling On Cart';
        $this->description = $this->l('Increase your sales with cross-selling. Display relevant product recommendations on cart and product pages');
        $docUrlTab = [];
        $docUrlTab['fr'] = '#/fr/cross-selling-on-cart/';
        $docUrlTab['en'] = '#/en/cross-selling-on-cart-3/';
        $docUrl = $docUrlTab['en'];
        if ($this->isoLang == 'fr') {
            $docUrl = $docUrlTab['fr'];
        }
        $forumUrlTab = [];
        $forumUrlTab['fr'] = 'http://www.prestashop.com/forums/topic/102385-module-pm-cross-selling-on-cart-est-maintenant-compatible-avec-modalcart/';
        $forumUrlTab['en'] = 'http://www.prestashop.com/forums/topic/102388-module-cross-selling-on-cart/';
        $forumUrl = $forumUrlTab['en'];
        if ($this->isoLang == 'fr') {
            $forumUrl = $forumUrlTab['fr'];
        }
        $this->supportLinks = [
            ['link' => $forumUrl, 'target' => '_blank', 'label' => $this->l('Forum topic')],
            ['link' => $docUrl, 'target' => '_blank', 'label' => $this->l('Online documentation')],
            ['link' => 'https://addons.prestashop.com/contact-form.php?id_product=2412', 'target' => '_blank', 'label' => $this->l('Support contact')],
        ];
    }
    public function setDefaultConfiguration()
    {
        $this->updateModuleConfiguration($this->defaultConfiguration);
    }
    public function getModuleConfiguration($key = null, $idShop = null)
    {
        $configuration = Configuration::get('PM_' . self::$modulePrefix . '_CONF', null, null, $idShop);
        if (!empty($configuration)) {
            $decodedConfiguration = json_decode($configuration, true);
            if (isset($decodedConfiguration['TITLE_BLOC']) && !is_array($decodedConfiguration['TITLE_BLOC'])) {
                $titleBlockLangs = [];
                foreach ($this->languages as $lang) {
                    $titleBlockLangs[(int)$lang['id_lang']] = $decodedConfiguration['TITLE_BLOC'];
                }
                $decodedConfiguration['TITLE_BLOC'] = $titleBlockLangs;
            }
            if (!empty($key)) {
                if (isset($decodedConfiguration[$key])) {
                    return $decodedConfiguration[$key];
                }
                return false;
            }
            return $decodedConfiguration;
        }
        return $this->defaultConfiguration;
    }
    public function updateModuleConfiguration($newConfiguration)
    {
        $currentConfiguration = $this->getModuleConfiguration();
        $mergedConfiguration = array_merge($currentConfiguration, $newConfiguration);
        Configuration::updateValue('PM_' . self::$modulePrefix . '_CONF', json_encode($mergedConfiguration));
    }
    public function install()
    {
        $this->setDefaultConfiguration();
        $res = parent::install() &&
                $this->registerHook('displayShoppingCartFooter') &&
                $this->registerHook('displayHeader') &&
                $this->registerHook('displayFooterProduct');
        if (version_compare(_PS_VERSION_, '1.7.8.0', '>=')) {
            $res &= $this->registerHook('displayCartModalFooter');
        }
        return $res;
    }
    private function getDataSerialized($data, $type = 'base64')
    {
        if (is_array($data)) {
            return array_map($type . '_encode', [$data]);
        } else {
            return current(array_map($type . '_encode', [$data]));
        }
    }
    private function getDataUnserialized($data, $type = 'base64')
    {
        if (is_array($data)) {
            return array_map($type . '_decode', [$data]);
        } else {
            return current(array_map($type . '_decode', [$data]));
        }
    }
    protected function generateAdvancedStylesFiles()
    {
        $content = $this->getDataUnserialized($this->getModuleConfiguration('ADVANCED_STYLES'));
        if (Shop::isFeatureActive()) {
            $contextShops = Shop::getContextListShopID();
        } else {
            $contextShops = [1];
        }
        foreach ($contextShops as $idShop) {
            $dynamicCssFile = str_replace('.css', '-' . $idShop . '.css', dirname(__FILE__) . '/' . self::DYNAMIC_CSS);
            $shopStyles = $this->getAdvancedStylesDb($idShop);
            if ($shopStyles !== false) {
                $content = $shopStyles;
            }
            file_put_contents($dynamicCssFile, $content);
        }
    }
    public function getAdvancedStylesDb($idShop = null)
    {
        $advancedCssFileDb = $this->getDataUnserialized($this->getModuleConfiguration('ADVANCED_STYLES', $idShop));
        if ($advancedCssFileDb !== false) {
            return trim($advancedCssFileDb);
        }
        return false;
    }
    private function prepareProductForTemplate(array $rawProduct)
    {
        $productAssembler = new ProductAssembler($this->context);
        $product = $productAssembler->assembleProduct($rawProduct);
        $factory = new ProductPresenterFactory($this->context, new TaxConfiguration());
        $presenter = $factory->getPresenter();
        $settings = $factory->getPresentationSettings();
        $product = $presenter->present(
            $settings,
            $product,
            $this->context->language
        );
        if (is_object($product) && $product instanceof PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray) {
            $product = $product->jsonSerialize();
        }
        $configuration = $this->getModuleConfiguration();
        if (Configuration::get('PS_STOCK_MANAGEMENT') && $configuration['DISPLAY_AVAILABILITY']) {
            if (Tools::isEmpty($product['availability_message'])) {
                if ($product['availability'] == 'available') {
                    $product['availability_message'] = $this->l('Available');
                } else {
                    $product['availability_message'] = $this->l('Unavailable');
                }
            }
            $product['flags'][] = [
                'type' => $product['availability'],
                'label' => $product['availability_message'],
            ];
        }
        return $product;
    }
    protected function prepareMultipleProductsForTemplate(array $products)
    {
        return array_map([$this, 'prepareProductForTemplate'], $products);
    }
    public function getImageType($includePixelsSize = false)
    {
        $result = [];
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT it. `id_image_type`, it.`name`, it.`products`, it.`width`, it.`height`
            FROM `' . _DB_PREFIX_ . 'image_type` it
            WHERE it.`products` = 1
        ');
        $image = [];
        foreach ($result as $k => $img) {
            $image[$k] = [];
            $image[$k]['value'] = $img['name'];
            $image[$k]['name'] = $img['name'] . ($includePixelsSize ? ' (' . $img['width'] . 'px * ' . $img['height'] . ' px)' : '');
        }
        return $image;
    }
    public function postProcess()
    {
        $formValues = $this->getModuleConfiguration();
        $languages = Language::getLanguages(false);
        $newConfiguration = [];
        foreach (array_keys($formValues) as $key) {
            if ($key == 'TITLE_BLOC') {
                $content = [];
                foreach ($languages as $lang) {
                    $idLang = (int)$lang['id_lang'];
                    $content[$idLang] = Tools::getValue('TITLE_BLOC_' . $idLang);
                }
                $newConfiguration[$key] = $content;
            } elseif ($key == 'CROSSSELLING_NB_DAYS') {
                $newConfiguration[$key] = min(max(1, Tools::getValue($key)), 200);
            } elseif ($key == 'PRODUCT_SELECTION') {
                $productList = explode('-', Tools::getValue('PM_CSOC_inputProducts'));
                $productList = array_map('intval', array_unique(array_filter($productList)));
                $newConfiguration[$key] = $productList;
            } elseif ($key == 'ADVANCED_STYLES') {
                $newConfiguration[$key] = $this->getDataSerialized(Tools::getValue($key));
            } elseif (Tools::getIsset($key)) {
                $newConfiguration[$key] = Tools::getValue($key);
            }
        }
        $this->updateModuleConfiguration($newConfiguration);
        $this->generateAdvancedStylesFiles();
        if (Tools::getIsset('dismissRating')) {
            Configuration::updateGlobalValue('PM_' . self::$modulePrefix . '_DISMISS_RATING', 1);
        }
    }
    public function loadAssets()
    {
        $this->context->controller->addJS($this->_path . 'views/js/admin17.js');
        $this->context->controller->addJS($this->_path . 'views/js/codemirror/codemirror.js');
        $this->context->controller->addJS($this->_path . 'views/js/codemirror/css.js');
        $this->context->controller->addCSS($this->_path . 'views/css/codemirror/codemirror.css');
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
    }
    private function checkIfModuleIsUpdate($updateDb = false, $displayConfirm = true)
    {
        if (!$this->isRegisteredInHook('displayHeader')) {
            $this->registerHook('displayHeader');
        }
        if (!$this->isRegisteredInHook('displayFooterProduct')) {
            $this->registerHook('displayFooterProduct');
        }
        if (!$this->isRegisteredInHook('displayShoppingCartFooter')) {
            $this->registerHook('displayShoppingCartFooter');
        }
        if ($this->isRegisteredInHook('displayBackOfficeHeader')) {
            $this->unregisterHook('displayBackOfficeHeader');
        }
        if (version_compare(_PS_VERSION_, '1.7.8.0', '>=') && !$this->isRegisteredInHook('displayCartModalFooter')) {
            $this->registerHook('displayCartModalFooter');
        }
        $moduleConfiguration = $this->getModuleConfiguration();
        if (!$updateDb && (empty($moduleConfiguration['LAST_VERSION']) || $this->version != $moduleConfiguration['LAST_VERSION'])) {
            return false;
        }
        if ($updateDb) {
            unset($_GET['makeUpdate']);
            if (!$this->migrateLegacyConfiguration()) {
                $this->context->controller->errors[] = $this->l('Could not migrate the configuration of the module. Please contact our support.');
                return false;
            }
            $this->updateModuleConfiguration(['LAST_VERSION' => $this->version]);
            if ($displayConfirm) {
                $this->context->controller->confirmations[] = $this->l('Module updated successfully');
            }
        }
        return true;
    }
    public function migrateLegacyConfiguration()
    {
        $hasNewConfigurationFormat = Configuration::hasKey('PM_' . self::$modulePrefix . '_CONF');
        if ($hasNewConfigurationFormat) {
            $moduleConfiguration = $this->getModuleConfiguration();
            if (isset($moduleConfiguration['PRODUCT_SELECTION']) && is_array($moduleConfiguration['PRODUCT_SELECTION'])) {
                return true;
            }
        }
        $legacyConfigurationKeys = array_map(function ($entry) {
            return 'PM_' . self::$modulePrefix . '_' . $entry;
        }, array_keys($this->defaultConfiguration));
        $oldConfigurationValues = Configuration::getMultiple($legacyConfigurationKeys);
        $migratedConfiguration = [];
        if (!is_array($oldConfigurationValues)) {
            return false;
        }
        foreach ($oldConfigurationValues as $configurationKey => $configurationValue) {
            $newKey = str_replace('PM_' . self::$modulePrefix . '_', '', $configurationKey);
            if ($configurationValue === false && $newKey != 'TITLE_BLOC') {
                continue;
            }
            if ($newKey == 'TITLE_BLOC') {
                if ($hasNewConfigurationFormat) {
                    continue;
                }
                $migratedConfiguration[$newKey] = [];
                foreach ($this->languages as $lang) {
                    $migratedConfiguration[$newKey][(int)$lang['id_lang']] = (string)Configuration::get($configurationKey, (int)$lang['id_lang']);
                }
            } elseif ($newKey == 'PRODUCT_SELECTION') {
                $unserializedProductsSelection = call_user_func('unser' . 'ialize', $configurationValue, ['allow_classes' => false]);
                $migratedConfiguration[$newKey] = array_map('intval', array_unique(array_filter($unserializedProductsSelection)));
            } else {
                $migratedConfiguration[$newKey] = $configurationValue;
            }
        }
        if ($hasNewConfigurationFormat) {
            $moduleConfiguration = $this->getModuleConfiguration();
            if (is_string($moduleConfiguration['PRODUCT_SELECTION'])) {
                $unserializedProductsSelection = call_user_func('unser' . 'ialize', $moduleConfiguration['PRODUCT_SELECTION'], ['allow_classes' => false]);
                $migratedConfiguration['PRODUCT_SELECTION'] = array_map('intval', array_unique(array_filter($unserializedProductsSelection)));
            }
        }
        $this->updateModuleConfiguration($migratedConfiguration);
        foreach ($legacyConfigurationKeys as $legacyKey) {
            Configuration::deleteByName($legacyKey);
        }
        return true;
    }
    public function getContent()
    {
        $this->loadAssets();
        if (Tools::getValue('makeUpdate')) {
            $this->checkIfModuleIsUpdate(true);
        }
        if (!$this->checkIfModuleIsUpdate(false)) {
            $this->context->smarty->assign([
                'baseConfigUrl' => $this->baseConfigUrl,
            ]);
            return $this->fetchTemplate('new-version-detected.tpl');
        }
        if ((bool)Tools::isSubmit('submitpm_crosssellingoncartModule') == true) {
            $this->postProcess();
            if (empty($this->context->controller->errors)) {
                $this->context->controller->confirmations[] = $this->l('Configuration has successfully been saved');
            }
        }
        return $this->showRating(true) . $this->renderForm() . $this->displaySupport();
    }
    public function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->submit_action = 'submitpm_crosssellingoncartModule';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $moduleConfiguration = $this->getModuleConfiguration();
        $moduleConfiguration['ADVANCED_STYLES'] = $this->getDataUnserialized($moduleConfiguration['ADVANCED_STYLES']);
        $helper->tpl_vars = [
            'fields_value' => $moduleConfiguration,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'module_dir' => $this->_path,
            'module_prefix' => self::$modulePrefix,
            'id_shop' => $this->context->shop->id,
            'ps_version' => _PS_VERSION_,
        ];
        if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            $router = $this->getContainer()->get('router');
            $url = $router->generate('admin_products_search_products_for_association', [
                'languageCode' => $this->context->language->getIsoCode(),
            ]);
        } else {
            $url = 'index.php?controller=AdminProducts&ajax=1&action=productsList&excludeVirtuals=0&forceJson=1&token=' . Tools::getAdminTokenLite('AdminProducts');
        }
        Media::addJsDef([
            'pmCsocAjaxProductsListUrl' => $url,
        ]);
        return $helper->generateForm([$this->getConfigForm()]);
    }
    protected function getConfigForm()
    {
        $configForm = [
            'form' => [
                'tabs' => [
                    'settings' => $this->l('Settings'),
                    'style' => $this->l('Advanced Styles'),
                ],
                'input' => [
                    [
                        'type' => 'title',
                        'content' => $this->l('General options'),
                        'name' => '',
                        'tab' => 'settings',
                    ],
                    [
                        'type' => 'text',
                        'name' => 'TITLE_BLOC',
                        'label' => $this->l('Block title'),
                        'lang' => true,
                        'tab' => 'settings',
                    ],
                    [
                        'type' => 'select',
                        'name' => 'IMAGE_SIZE',
                        'label' => $this->l('Product image size'),
                        'desc' => $this->l('Choose the product image size'),
                        'tab' => 'settings',
                        'class' => 'fixed-width-lg',
                        'options' => [
                            'query' => self::getImageType(true),
                            'id' => 'value',
                            'name' => 'name',
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'name' => 'DISPLAY_IN_PRODUCT',
                        'label' => $this->l('Display cross-selling on product pages'),
                        'desc' => $this->l('When on, a cross selling block is displayed at the bottom of product pages'),
                        'is_bool' => true,
                        'tab' => 'settings',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display cross-selling on cart pages'),
                        'name' => 'DISPLAY_IN_CART',
                        'is_bool' => true,
                        'tab' => 'settings',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display products that are already in the cart'),
                        'name' => 'IDENTICAL_PRODUCTY',
                        'is_bool' => true,
                        'tab' => 'settings',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'title',
                        'content' => $this->l('Number of products to be displayed simultaneously'),
                        'name' => '',
                        'tab' => 'settings',
                    ],
                    [
                        'type' => 'text',
                        'name' => 'NB_PRODUCT',
                        'label' => $this->l('On computers (product page)'),
                        'tab' => 'settings',
                        'class' => 'fixed-width-sm',
                        'suffix' => $this->l('products'),
                        'maxlength' => 2,
                        'form_group_class' => 'pm_csoc_product_options',
                    ],
                    [
                        'type' => 'text',
                        'name' => 'NB_PRODUCT_CART',
                        'label' => $this->l('On computers (cart page)'),
                        'tab' => 'settings',
                        'class' => 'fixed-width-sm',
                        'suffix' => $this->l('products'),
                        'maxlength' => 2,
                        'form_group_class' => 'pm_csoc_cart_options',
                    ],
                    [
                        'type' => 'text',
                        'name' => 'NB_PRODUCT_TABLET',
                        'label' => $this->l('On tablets'),
                        'tab' => 'settings',
                        'class' => 'fixed-width-sm',
                        'suffix' => $this->l('products'),
                        'maxlength' => 2,
                    ],
                    [
                        'type' => 'text',
                        'name' => 'NB_PRODUCT_MOBILE',
                        'label' => $this->l('On mobiles'),
                        'tab' => 'settings',
                        'class' => 'fixed-width-sm',
                        'suffix' => $this->l('products'),
                        'maxlength' => 2,
                    ],
                    [
                        'type' => 'title',
                        'content' => $this->l('Product appearance'),
                        'name' => '',
                        'tab' => 'settings',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display image ?'),
                        'name' => 'DISPLAY_IMG',
                        'is_bool' => true,
                        'tab' => 'settings',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display title ?'),
                        'name' => 'DISPLAY_TITLE',
                        'is_bool' => true,
                        'tab' => 'settings',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display price ?'),
                        'name' => 'DISPLAY_PRICE',
                        'is_bool' => true,
                        'tab' => 'settings',
                        'class' => 'col-lg-2',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display "Add to Cart" button ?'),
                        'name' => 'DISPLAY_BUTTON',
                        'is_bool' => true,
                        'tab' => 'settings',
                        'class' => 'col-lg-2',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display availability ?'),
                        'name' => 'DISPLAY_AVAILABILITY',
                        'is_bool' => true,
                        'disabled' => !Configuration::get('PS_STOCK_MANAGEMENT'),
                        'tab' => 'settings',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'title',
                        'content' => $this->l('Accessories'),
                        'name' => '',
                        'tab' => 'settings',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Display products\' accessories ?'),
                        'name' => 'ACCESSORIES',
                        'is_bool' => true,
                        'tab' => 'settings',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'name' => 'NB_ACCESSORIES',
                        'label' => $this->l('Maximum number of accessories to be displayed'),
                        'tab' => 'settings',
                        'class' => 'fixed-width-xs',
                        'maxlength' => 2,
                        'form_group_class' => 'pm_csoc_accessories_options',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Force display of accessories in product page ?'),
                        'name' => 'ACCESSORIES_IN_PRODUCT',
                        'is_bool' => true,
                        'tab' => 'settings',
                        'form_group_class' => 'pm_csoc_accessories_options',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'title',
                        'content' => $this->l('Products often purchased together'),
                        'name' => '',
                        'tab' => 'settings',
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Suggest products that are often purchased together ?'),
                        'name' => 'CROSSSELLING',
                        'is_bool' => true,
                        'tab' => 'settings',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Yes'),
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'name' => 'NB_CROSSSELLING',
                        'label' => $this->l('Maximum number of products to be displayed'),
                        'tab' => 'settings',
                        'class' => 'fixed-width-xs',
                        'form_group_class' => 'pm_csoc_crossselling_options',
                        'maxlength' => 2,
                    ],
                    [
                        'type' => 'text',
                        'name' => 'CROSSSELLING_NB_DAYS',
                        'label' => $this->l('Use valid orders less than X days old'),
                        'desc' => $this->l('Min: 1 - Max: 200'),
                        'tab' => 'settings',
                        'class' => 'fixed-width-xs',
                        'suffix' => $this->l('days'),
                        'form_group_class' => 'pm_csoc_crossselling_options',
                        'maxlength' => 3,
                    ],
                    [
                        'type' => 'title',
                        'content' => $this->l('Compulsory products'),
                        'name' => '',
                        'tab' => 'settings',
                    ],
                    [
                        'type' => 'produitsimposes',
                        'name' => 'PRODUCT_SELECTION',
                        'label' => $this->l('Products to be imposed'),
                        'tab' => 'settings',
                        'values' => $this->getCompulsoryProductList(),
                        'form_group_class' => 'clearfix',
                    ],
                    [
                        'type' => 'title',
                        'content' => $this->l('Advanced Styles (CSS)'),
                        'desc' => $this->l('Enter your CSS rules here :'),
                        'name' => '',
                        'tab' => 'style',
                        'col' => 12,
                        'label' => '',
                    ],
                    [
                        'type' => 'advancedstyles',
                        'name' => 'ADVANCED_STYLES',
                        'tab' => 'style',
                        'col' => 12,
                        'label' => '',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
        return $configForm;
    }
    protected function getCompulsoryProductList()
    {
        $productList = $this->getModuleConfiguration('PRODUCT_SELECTION');
        $listToBeDisplayed = [];
        if (!empty($productList)) {
            foreach ($productList as $idProduct) {
                $product = new Product($idProduct, false, (int)$this->context->employee->id_lang);
                if (Validate::isLoadedObject($product)) {
                    $listToBeDisplayed[] = [
                        'id' => $product->id,
                        'name' => $product->name . ' (ref: ' . $product->reference . ')',
                        'image_link' => Context::getContext()->link->getImageLink($product->link_rewrite, $product->getCoverWs()),
                    ];
                }
            }
        }
        return $listToBeDisplayed;
    }
    public function getElementProducts($products, $idLang, $getProductsProperties = true)
    {
        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_shop.id_product_attribute) id_product_attribute, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
                pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image,
                il.`legend`, m.`name` AS manufacturer_name, cl.`name` AS category_default,
                DATEDIFF(product_shop.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
                INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY)) > 0 AS new,
                product_shop.price AS orderprice
            FROM `' . _DB_PREFIX_ . 'product` p
            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (p.`id_product` = pa.`id_product`)
            ' . $this->context->shop->addSqlAssociation('product', 'p') . '
            ' . Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1') . '
            ' . Product::sqlStock('p', 'product_attribute_shop', false, $this->context->shop) . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = ' . (int)$idLang . $this->context->shop->addSqlRestrictionOnLang('pl') . '
            )
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (
                p.`id_category_default` = cl.`id_category`
                AND cl.`id_lang` = ' . (int)$idLang . $this->context->shop->addSqlRestrictionOnLang('cl') . '
            )
            LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (p.`id_product` = i.`id_product`)
            ' . Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$idLang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
            LEFT JOIN `' . _DB_PREFIX_ . 'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
                AND tr.`id_country` = ' . (int)$this->context->country->id . '
                AND tr.`id_state` = 0
            )
            LEFT JOIN `' . _DB_PREFIX_ . 'tax` t ON (t.`id_tax` = tr.`id_tax`)
            LEFT JOIN `' . _DB_PREFIX_ . 'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = ' . (int)$idLang . ')
            WHERE product_shop.`id_shop` = ' . (int)$this->context->shop->id . '
            AND product_shop.`active` = 1
            AND product_shop.`available_for_order` = 1
            AND product_shop.`id_product` IN (' . implode(',', array_map('intval', $products)) . ')
            AND product_shop.`visibility` IN ("both", "catalog") '
            . (Configuration::get('PS_STOCK_MANAGEMENT') ? '
            AND IF (
                (stock.`id_product_attribute` = 0 AND pa.`id_product_attribute` IS NULL)
                OR
                (product_attribute_shop.`id_product_attribute` IS NOT NULL AND stock.`id_product_attribute`=product_attribute_shop.`id_product_attribute`), 1, 0
            )
            AND IF (stock.`quantity` > 0, 1, IF (stock.`out_of_stock` = 2, ' . (int)Configuration::get('PS_ORDER_OUT_OF_STOCK') . ' = 1, stock.`out_of_stock` = 1))
            ' : '') . '
            GROUP BY product_shop.`id_product`
            ORDER BY FIELD(product_shop.`id_product`, ' . implode(',', array_map('intval', $products)) . ')
        ';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        if ($getProductsProperties) {
            $result = $this->prepareMultipleProductsForTemplate($result);
        }
        if ($getProductsProperties) {
            return Product::getProductsProperties($idLang, $result);
        } else {
            return $result;
        }
    }
    protected function getAccessoriesLight($productList, $limit, $ignoreList = [])
    {
        if (!self::isFilledArray($productList)) {
            return false;
        }
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT DISTINCT `id_product_2` AS id_product
            FROM `' . _DB_PREFIX_ . 'accessory` a
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = a.`id_product_2`)
            LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_category` = p.`id_category_default` AND cp.`id_product` = a.`id_product_2`)
            LEFT JOIN `' . _DB_PREFIX_ . 'category_group` ctg ON (ctg.`id_category` = cp.`id_category` AND ctg.`id_group` = ' . (int)Group::getCurrent()->id . ')
            WHERE `id_product_1` IN (' . implode(',', array_map('intval', $productList)) . ')
            ' . (self::isFilledArray($ignoreList) ? 'AND `id_product_2` NOT IN (' . implode(',', array_map('intval', $ignoreList)) . ')' : '') . '
            AND ctg.id_group IS NOT NULL
            ORDER BY RAND()
            LIMIT ' . (int)$limit
        );
    }
    protected function getCrossSellingLight($productList, $limit, $nbDays)
    {
        if (!self::isFilledArray($productList)) {
            return false;
        }
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
            SELECT DISTINCT od.product_id
            FROM ' . _DB_PREFIX_ . 'order_detail od
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = od.`product_id`)
            LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_category` = p.`id_category_default` AND cp.`id_product` = od.`product_id`)
            LEFT JOIN `' . _DB_PREFIX_ . 'category_group` ctg ON (ctg.`id_category` = cp.`id_category` AND ctg.`id_group` = ' . (int)Group::getCurrent()->id . ')
            WHERE od.product_id NOT IN (' . implode(',', array_map('intval', $productList)) . ') AND od.id_order IN (
                SELECT od.id_order FROM ' . _DB_PREFIX_ . 'orders o
                JOIN ' . _DB_PREFIX_ . 'order_detail od ON (od.id_order = o.id_order)
                WHERE o.valid = 1 AND od.product_id IN (' . implode(',', array_map('intval', $productList)) . ') AND TO_DAYS("' . date('Y-m-d') . ' 00:00:00") - TO_DAYS(o.date_add) <= ' . $nbDays . '
            )
             AND ctg.id_group IS NOT NULL
            LIMIT ' . (int)$limit
        );
    }
    public static function isFilledArray($array)
    {
        return $array && is_array($array) && count($array);
    }
    protected function showRating($show = false)
    {
        $dismiss = (int)Configuration::getGlobalValue('PM_' . self::$modulePrefix . '_DISMISS_RATING');
        if ($show && $dismiss != 1 && $this->getNbDaysModuleUsage() >= 3) {
            return $this->fetchTemplate('core/rating.tpl');
        }
        return '';
    }
    private function getNbDaysModuleUsage()
    {
        $sql = 'SELECT DATEDIFF(NOW(),date_add)
                FROM ' . _DB_PREFIX_ . 'configuration
                WHERE name = \'' . pSQL('PM_' . self::$modulePrefix . '_CONF') . '\'
                ORDER BY date_add ASC';
        return (int)Db::getInstance()->getValue($sql);
    }
    protected function fetchTemplate($tpl, $customVars = [], $configOptions = [])
    {
        $this->context->smarty->assign([
            'ps_major_version' => Tools::substr(str_replace('.', '', _PS_VERSION_), 0, 2),
            'module_name' => $this->name,
            'module_path' => $this->_path,
            'current_iso_lang' => $this->context->language->iso_code,
            'current_id_lang' => (int)$this->context->language->id,
            'options' => $configOptions,
            'languages' => $this->languages,
            'base_config_url' => $this->baseConfigUrl,
            'default_language' => $this->defaultLanguage,
        ]);
        if (count($customVars)) {
            $this->context->smarty->assign($customVars);
        }
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/' . $tpl);
    }
    
    private function getPMdata()
    {
        $param = [];
        $param[] = 'ver-' . _PS_VERSION_;
        $param[] = 'current-' . $this->name;
        
        $result = $this->getPMAddons();
        if ($result && is_array($result) && count($result)) {
            foreach ($result as $moduleName => $moduleVersion) {
                $param[] = $moduleName . '-' . $moduleVersion;
            }
        }
        return $this->getDataSerialized(implode('|', $param));
    }
    private function getPMAddons()
    {
        $pmAddons = [];
        $result = Db::getInstance()->ExecuteS('SELECT DISTINCT name FROM ' . _DB_PREFIX_ . 'module WHERE name LIKE "pm_%"');
        if ($result && is_array($result) && count($result)) {
            foreach ($result as $module) {
                $instance = Module::getInstanceByName($module['name']);
                if ($instance && !empty($instance->version)) {
                    $pmAddons[$module['name']] = $instance->version;
                }
            }
        }
        return $pmAddons;
    }
    private function doHttpRequest($data = [], $c = 'prestashop', $s = 'api.addons')
    {
        $data = array_merge([
            'version' => _PS_VERSION_,
            'iso_lang' => Tools::strtolower($this->context->language->iso_code),
            'iso_code' => Tools::strtolower(Country::getIsoById((int)Configuration::get('PS_COUNTRY_DEFAULT'))),
            'module_key' => $this->module_key,
            'method' => 'contributor',
            'action' => 'all_products',
        ], $data);
        $postData = http_build_query($data);
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'content' => $postData,
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'timeout' => 15,
            ],
        ]);
        $response = Tools::file_get_contents('https://' . $s . '.' . $c . '.com', false, $context);
        if (empty($response)) {
            return false;
        }
        $responseToJson = json_decode($response, true);
        if (empty($responseToJson)) {
            return false;
        }
        return $responseToJson;
    }
    private function getAddonsModulesFromApi()
    {
        $modules = $this->getModuleConfiguration('AM');
        $modules_date = (int)$this->getModuleConfiguration('AMD');
        if (!empty($modules) && !empty($modules_date) && strtotime('+2 day', $modules_date) > time()) {
            return json_decode($modules, true);
        }
        $jsonResponse = $this->doHttpRequest();
        if (empty($jsonResponse['products'])) {
            return [];
        }
        $dataToStore = [];
        foreach ($jsonResponse['products'] as $addonsEntry) {
            $dataToStore[(int)$addonsEntry['id']] = [
                'name' => $addonsEntry['name'],
                'displayName' => $addonsEntry['displayName'],
                'url' => $addonsEntry['url'],
                'compatibility' => $addonsEntry['compatibility'],
                'version' => $addonsEntry['version'],
                'description' => $addonsEntry['description'],
            ];
        }
        $this->updateModuleConfiguration([
            'AM' => json_encode($dataToStore),
            'AMD' => time(),
        ]);
        return $dataToStore;
    }
    private function getPMModulesFromApi()
    {
        $modules = $this->getModuleConfiguration('PMM');
        $modules_date = (int)$this->getModuleConfiguration('PMMD');
        if (!empty($modules) && !empty($modules_date) && strtotime('+2 day', $modules_date) > time()) {
            return json_decode($modules, true);
        }
        $jsonResponse = $this->doHttpRequest(['list' => $this->getPMAddons()], 'presta-module', 'api-addons');
        if (empty($jsonResponse)) {
            return [];
        }
        $this->updateModuleConfiguration([
            'PMM' => json_encode($jsonResponse),
            'PMMD' => time(),
        ]);
        return $jsonResponse;
    }
    private function shuffleArray(&$a)
    {
        if (is_array($a) && count($a)) {
            $ks = array_keys($a);
            shuffle($ks);
            $new = [];
            foreach ($ks as $k) {
                $new[$k] = $a[$k];
            }
            $a = $new;
            return true;
        }
        return false;
    }
    private function displaySupport()
    {
        $pm_addons_products = $this->getAddonsModulesFromApi();
        $pm_products = $this->getPMModulesFromApi();
        if (!is_array($pm_addons_products)) {
            $pm_addons_products = [];
        }
        if (!is_array($pm_products)) {
            $pm_products = [];
        }
        $this->shuffleArray($pm_addons_products);
        if (is_array($pm_addons_products)) {
            if (!empty($pm_products['ignoreList']) && is_array($pm_products['ignoreList']) && count($pm_products['ignoreList'])) {
                foreach ($pm_products['ignoreList'] as $ignoreId) {
                    if (isset($pm_addons_products[$ignoreId])) {
                        unset($pm_addons_products[$ignoreId]);
                    }
                }
            }
            $addonsList = $this->getPMAddons();
            if ($addonsList && is_array($addonsList) && count($addonsList)) {
                foreach (array_keys($addonsList) as $moduleName) {
                    foreach ($pm_addons_products as $k => $pm_addons_product) {
                        if ($pm_addons_product['name'] == $moduleName) {
                            unset($pm_addons_products[$k]);
                            break;
                        }
                    }
                }
            }
        }
        $vars = [
            'support_links' => (is_array($this->supportLinks) && count($this->supportLinks) ? $this->supportLinks : []),
            'copyright_link' => (is_array($this->copyrightLink) && count($this->copyrightLink) ? $this->copyrightLink : false),
            'pm_module_version' => $this->version,
            'pm_data' => $this->getPMdata(),
            'pm_products' => $pm_products,
            'pm_addons_products' => $pm_addons_products,
        ];
        return $this->fetchTemplate('core/support.tpl', $vars);
    }
    public function hookDisplayHeader($params)
    {
        $hookParams = [];
        $hookParams['display'] = false;
        if ($this->context->controller instanceof ProductController) {
            $hookParams['on_product_page'] = true;
            $hookParams['id_product'] = Tools::getValue('id_product', 0);
        }
        $jsDefs = $this->getWidgetVariables('', $hookParams);
        Media::addJsDef(['pm_crosssellingoncart' => $jsDefs]);
        $this->context->controller->registerJavascript('modules-pm_crosssellingoncart-front', 'modules/' . $this->name . '/views/js/front.js', ['server' => 'local', 'position' => 'bottom', 'priority' => 150]);
        $this->context->controller->addJS($this->_path . 'views/js/owl-carousel/owl.carousel.min.js');
        if (isset($this->context->controller->php_self) && ($this->context->controller->php_self == 'order' || $this->context->controller->php_self == 'order-opc')) {
            $this->context->controller->addJS($this->_path . 'views/js/pm_crosssellingoncart.js');
        }
        $this->context->controller->addCSS($this->_path . 'views/css/owl-carousel/owl.carousel.css', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/owl-carousel/owl.theme.css', 'all');
        $this->context->controller->addCSS($this->_path . 'views/css/pm_crosssellingoncart.css', 'all');
        $this->context->controller->addCSS($this->_path . str_replace('.css', '-' . $this->context->shop->id . '.css', self::DYNAMIC_CSS), 'all');
        if (version_compare(_PS_VERSION_, '1.7.8.0', '>=')) {
            $this->context->controller->addCSS($this->_path . 'views/css/pm_crosssellingoncart_178.css', 'all');
        }
    }
    public function hookDisplayShoppingCartFooter($params)
    {
        $moduleConfiguration = $this->getModuleConfiguration();
        if (!$moduleConfiguration['DISPLAY_IN_CART'] && !isset($params['id_product'])) {
            return false;
        }
        return $this->renderWidget('displayShoppingCartFooter', $params);
    }
    public function hookDisplayFooterProduct()
    {
        $moduleConfiguration = $this->getModuleConfiguration();
        if (!$moduleConfiguration['DISPLAY_IN_PRODUCT']) {
            return;
        }
        $params = [
            'on_product_page' => true,
            'id_product' => (int)Tools::getValue('id_product'),
        ];
        return $this->renderWidget('displayFooterProduct', $params);
    }
    public function hookDisplayCartModalFooter($params)
    {
        return $this->renderWidget('displayCartModalFooter', $params);
    }
    public function renderWidget($hookName, array $configuration)
    {
        if (defined('_PS_ADMIN_DIR_')) {
            return;
        }
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        return $this->fetch('module:' . $this->name . '/views/templates/front/pm_crosssellingoncart-17.tpl');
    }
    public function getWidgetVariables($hookName, array $params)
    {
        if (isset($params['products'])) {
            $cart_products = $params['products'];
        } else {
            $cart = $this->context->cart;
            $cart_products = $cart->getProducts(true);
        }
        $moduleConfiguration = $this->getModuleConfiguration();
        if (!$moduleConfiguration['DISPLAY_IMG']
            && !$moduleConfiguration['DISPLAY_TITLE']
            && !$moduleConfiguration['DISPLAY_PRICE']
            && !$moduleConfiguration['DISPLAY_BUTTON']
            && !$moduleConfiguration['DISPLAY_AVAILABILITY']
        ) {
            return [];
        }
        $displayProductAlreadyInCart = (bool)$moduleConfiguration['IDENTICAL_PRODUCTY'];
        $nbProductSelection = (int)$moduleConfiguration['NB_PRODUCT'];
        $nbProductSelectionCart = (int)$moduleConfiguration['NB_PRODUCT_CART'];
        $nbProductSelectionTablet = (int)$moduleConfiguration['NB_PRODUCT_TABLET'];
        $nbProductSelectionMobile = (int)$moduleConfiguration['NB_PRODUCT_MOBILE'];
        $nbAccessories = (int)$moduleConfiguration['NB_ACCESSORIES'];
        if (!$nbAccessories) {
            $nbAccessories = (int)$this->defaultConfiguration['NB_ACCESSORIES'];
        }
        $nbCrossSelling = (int)$moduleConfiguration['NB_CROSSSELLING'];
        if (!$nbCrossSelling) {
            $nbCrossSelling = (int)$this->defaultConfiguration['NB_CROSSSELLING'];
        }
        $nbDays = (int)$moduleConfiguration['CROSSSELLING_NB_DAYS'];
        $imageSize = $moduleConfiguration['IMAGE_SIZE'];
        $blockTitle = $moduleConfiguration['TITLE_BLOC'][(int) $this->context->cookie->id_lang];
        $postProductSelection = @array_filter($moduleConfiguration['PRODUCT_SELECTION']);
        $productSelection = [];
        foreach ($postProductSelection as $k => $idProduct) {
            if (!Product::checkAccessStatic((int) $idProduct, false)) {
                unset($postProductSelection[$k]);
            }
        }
        $productsOnCart = [];
        foreach ($cart_products as $k => $v) {
            $productsOnCart[] = $v['id_product'];
        }
        $productArray = [];
        if (isset($params['id_product'])) {
            $productArray[] = (int) $params['id_product'];
            $productsOnCart[] = (int) $params['id_product'];
        }
        $productsOnCart = array_unique($productsOnCart);
        $forceShowAccessories = true;
        if (isset($params['on_product_page']) && $params['on_product_page'] && !(int)$moduleConfiguration['ACCESSORIES_IN_PRODUCT']) {
            $forceShowAccessories = false;
        }
        if ($forceShowAccessories && $moduleConfiguration['ACCESSORIES']) {
            if (isset($params['on_product_page']) && $params['on_product_page']) {
                $productAccessories = $this->getAccessoriesLight($productArray, $nbAccessories);
            } else {
                $productAccessories = $this->getAccessoriesLight($productsOnCart, $nbAccessories, $productArray);
            }
            if (self::isFilledArray($productAccessories)) {
                foreach ($productAccessories as $product) {
                    $postProductSelection[] = (int) $product['id_product'];
                }
            }
        }
        if ($moduleConfiguration['CROSSSELLING']) {
            $productsCrossSelling = $this->getCrossSellingLight($productsOnCart, $nbCrossSelling, $nbDays);
            if (self::isFilledArray($productsCrossSelling)) {
                foreach ($productsCrossSelling as $product) {
                    $postProductSelection[] = (int) $product['product_id'];
                }
            }
        }
        if (self::isFilledArray($postProductSelection)) {
            $postProductSelection = array_unique($postProductSelection);
            if (!$displayProductAlreadyInCart) {
                $postProductSelection = array_diff($postProductSelection, $productsOnCart);
            }
            if (isset($params['id_product']) && $params['id_product']) {
                $key = array_search((int) $params['id_product'], $postProductSelection);
                if ($key !== false) {
                    unset($postProductSelection[(int) $key]);
                }
            }
            if (self::isFilledArray($postProductSelection)) {
                $productSelection = $this->getElementProducts($postProductSelection, $this->context->cookie->id_lang);
            }
        }
        $taxConfiguration = new TaxConfiguration();
        foreach ($productSelection as $id => $product) {
            if ($taxConfiguration->includeTaxes()) {
                $productSelection[$id]['price'] = $this->formatPrice($product['price']);
            } else {
                $productSelection[$id]['price'] = $this->formatPrice($product['price_tax_exc']);
            }
        }
        if (isset($params['display']) && !$params['display']) {
            $jsDefs = [];
            $jsDefs['product_selection'] = $productSelection;
            $jsDefs['products_quantity'] = (isset($params['on_product_page']) && $params['on_product_page'] ? (int) $nbProductSelection : (int) $nbProductSelectionCart);
            $jsDefs['products_quantity_tablet'] = (int)$nbProductSelectionTablet;
            $jsDefs['products_quantity_mobile'] = (int)$nbProductSelectionMobile;
            $jsDefs['order_page_link'] = $this->context->link->getPageLink(isset($this->context->controller->php_self) && ($this->context->controller->php_self == 'order' || $this->context->controller->php_self == 'order-opc') ? $this->context->controller->php_self : 'order');
            $jsDefs['nbItems'] = ((int)count($productSelection) < (int)$jsDefs['products_quantity'] ? (int)count($productSelection) : (int)$jsDefs['products_quantity']);
            return $jsDefs;
        }
        if (count($productSelection) <= 0) {
            return [];
        }
        $assigns = [
            'csoc_order_page_link' => $this->context->link->getPageLink(isset($this->context->controller->php_self) && ($this->context->controller->php_self == 'order' || $this->context->controller->php_self == 'order-opc') ? $this->context->controller->php_self : 'order'),
            'csoc_product_selection' => $productSelection,
            'csoc_bloc_title' => $blockTitle,
            'csoc_products_quantity' => (isset($params['on_product_page']) ? (int) $nbProductSelection : (int) $nbProductSelectionCart),
            'csoc_products_quantity_tablet' => $nbProductSelectionTablet,
            'csoc_products_quantity_mobile' => $nbProductSelectionMobile,
            'tax_enabled' => Configuration::get('PS_TAX'),
            'imageSize' => $imageSize,
            'ps_version' => _PS_VERSION_,
            'csoc_static_token' => Tools::getToken(false),
            'csoc_display' => $moduleConfiguration,
            'on_product_page' => (isset($params['on_product_page']) ? $params['on_product_page'] : false),
            'carouselCssClass' => version_compare(_PS_VERSION_, '1.7.8.0', '>') ? 'carousel' : null
        ];
        $imageType = ['type' => $imageSize];
        $assigns['widthSize'] = Image::getWidth($imageType);
        return $assigns;
    }
    public function formatPrice($price, $currency = null)
    {
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            return Tools::displayPrice($price, $currency);
        }
        $context = Context::getContext();
        if (empty($currency)) {
            $currency = $context->currency;
        } elseif (is_int($currency)) {
            $currency = Currency::getCurrencyInstance($currency);
        }
        $locale = Tools::getContextLocale($context);
        $currencyCode = is_array($currency) ? $currency['iso_code'] : $currency->iso_code;
        return $locale->formatPrice($price, $currencyCode);
    }
}
