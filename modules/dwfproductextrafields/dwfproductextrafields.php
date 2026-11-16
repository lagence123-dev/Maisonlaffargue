<?php
/**
 *   Copyright since 2009 ohmyweb!
 *
 *   @author    ohmyweb <contact@ohmyweb.fr>
 *   @copyright since 2009 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use DwfProductExtraFields\Form\Modifier\ProductFormModifier;
use DwfProductExtraFields\Component\HttpFoundation\File\OhUploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use PrestaShopBundle\Utils\FloatParser;

require_once __DIR__ . '/vendor/autoload.php';

class DwfProductExtraFields extends Module
{
    public $adminControllers = [
        'adminAjax' => 'AdminAjaxDwfproductextrafields',
    ];

    public function __construct()
    {
        $this->bootstrap = true;
        $this->name = 'dwfproductextrafields';
        $this->tab = 'front_office_features';
        $this->version = '5.0.0';
        $this->author = 'ohmyweb';

        parent::__construct();

        $this->displayName = $this->trans('Product Extra Fields', [], 'Modules.Dwfproductextrafields.Admin');
        $this->description = $this->trans('Add extra fields to the product page', [], 'Modules.Dwfproductextrafields.Admin');
        $this->module_key = '6f6189603f7433c3ccbd3e9eb19aa692';
//        $this->author_address = '0x8F66ED89875e0CC587f27Fb5166B633388A602c7';

        $this->ps_versions_compliancy = ['min' => '1.7.8.0', 'max' => _PS_VERSION_];
    }

    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    public function install()
    {
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/DwfProductExtraFieldsClass.php';

        if (parent::install()
            && $this->installFiles()
            && DwfProductExtraFieldsClass::createTable()
            && $this->registerToAllHooks()
            && $this->installDB()
            && $this->installTab()
        ) {
            return true;
        }

        return false;
    }

    protected function registerToAllHooks()
    {
        $hooks = [
            'actionDispatcherBefore',
            'actionAdminControllerSetMedia',
            'actionAdminDuplicateAfter',
            'actionAdminProductsControllerDeleteAfter',
            'displayProductExtraFields',
            'displayProductExtraContent',
            'actionGetProductPropertiesAfter',
            'addWebserviceResources',
        ];

        if (version_compare(_PS_VERSION_, '9.0.0', '>=')
            || (version_compare(_PS_VERSION_, '8.0.0', '>=')
                && Db::getInstance()->getValue('SELECT `state` FROM `'._DB_PREFIX_.'feature_flag` WHERE name = "product_page_v2"')
            )
        ) {
            $hooks[] = 'actionAfterUpdateProductFormHandler';
            $hooks[] = 'actionProductFormBuilderModifier';
        } else {
            $hooks[] = 'actionAdminProductsControllerSaveAfter';
            $hooks[] = 'displayAdminProductsExtra';
        }

        $res = true;
        foreach ($hooks as $hook) {
            $res &= $this->registerHook($hook);
        }

        return $res;
    }

    public function installDB()
    {
        $prefix = _DB_PREFIX_;
        $engine = _MYSQL_ENGINE_;

        $statements = [];

        $statements[] = "
            CREATE TABLE IF NOT EXISTS `".$prefix."product_extra_field`(
                `id_product_extra_field` INT(10) NOT NULL AUTO_INCREMENT,
                `id_shop_default` INT(10) NOT NULL,
                `id_product` INT(10) NOT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`id_product_extra_field`),
                INDEX(`id_product`)
            ) ENGINE=".$engine." DEFAULT CHARSET=utf8mb4;
        ";

        $statements[] = "
            CREATE TABLE IF NOT EXISTS `".$prefix."product_extra_field_shop` (
                `id_product_extra_field` int(10) NOT NULL,
                `id_shop` int(10) NOT NULL,
                PRIMARY KEY (`id_product_extra_field`, `id_shop`)
            ) ENGINE=".$engine." DEFAULT CHARSET=utf8mb4;
        ";

        $statements[] = "
            CREATE TABLE IF NOT EXISTS `".$prefix."product_extra_field_lang`(
                `id_product_extra_field` INT(10) NOT NULL,
                `id_shop` INT(11) NOT NULL,
                `id_lang` INT(10) unsigned NOT NULL,
                PRIMARY KEY (`id_product_extra_field`, `id_shop`, `id_lang`)
            ) ENGINE=".$engine." DEFAULT CHARSET=utf8mb4;
        ";

        foreach ($statements as $statement) {
            if (!Db::getInstance()->Execute($statement)) {
                $this->_errors[] = $this->trans('Unable to execute the query : [statement]', ['[statement]' => $statement], 'Modules.Dwfproductextrafields.Admin');
                return false;
            }
        }

        return true;
    }

    public function installFiles()
    {
        $res = true;
        if (!file_exists(_PS_ROOT_DIR_._PS_JS_DIR_.'jquery/plugins/jquery.unserialize.js')) {
            copy(_PS_MODULE_DIR_ . $this->name.'/views/js/jquery.unserialize.js', _PS_ROOT_DIR_.'/js/jquery/plugins/jquery.unserialize.js');
        }
        return $res;
    }

    public function installTab()
    {
        $res = true;

        foreach ($this->adminControllers as $controller_name) {
            $tab = new Tab();
            $tab->class_name = $controller_name;
            $tab->module = $this->name;
            $tab->active = true;
            $tab->id_parent = -1;
            $tab->name = array_fill_keys(
                Language::getIDs(false),
                $this->displayName
            );
            $res && $tab->add();
        }

        return $res;
    }

    public function uninstall()
    {
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/DwfProductExtraFieldsClass.php';

        if (parent::uninstall()
            && DwfProductExtraFieldsClass::deleteDbTable()
            && $this->uninstallDB()
            && $this->uninstallTab()
        ) {
            return true;
        }
        return false;
    }

    public function uninstallDB()
    {
        $prefix = _DB_PREFIX_;

        $statements = [];

        $statements[] = "DROP TABLE IF EXISTS `".$prefix."product_extra_field`";
        $statements[] = "DROP TABLE IF EXISTS `".$prefix."product_extra_field_shop`";
        $statements[] = "DROP TABLE IF EXISTS `".$prefix."product_extra_field_lang`";

        foreach ($statements as $statement) {
            if (!Db::getInstance()->Execute($statement)) {
                return false;
            }
        }

        return true;
    }

    public function uninstallTab()
    {
        $res = true;

        foreach ($this->adminControllers as $controller_name) {
            $id_tab = (int) Tab::getIdFromClassName($controller_name);
            $tab = new Tab($id_tab);

            if (Validate::isLoadedObject($tab)) {
                $res &= $tab->delete();
            }
        }

        return $res;
    }

    public function getContent()
    {
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/DwfProductExtraFieldsClass.php';

        $adminController = $this->context->link->getAdminLink($this->adminControllers['adminAjax']);

        $html_before = '';
        $html_after = '';
        $id_dwfproductextrafields = (int)Tools::getValue('id_dwfproductextrafields');

        if (Tools::isSubmit('savedwfproductextrafields')) {
            if ($id_dwfproductextrafields = Tools::getValue('id_dwfproductextrafields')) {
                $dwfproductextrafields = new DwfProductExtraFieldsClass((int)$id_dwfproductextrafields);
                $confMessageId = 4;
            } else {
                $dwfproductextrafields = new DwfProductExtraFieldsClass();
                $confMessageId = 3;
            }
            $dwfproductextrafields->copyFromPost();

            if ($dwfproductextrafields->validateFields(false) && $dwfproductextrafields->validateFieldsLang(false)) {
                if ($dwfproductextrafields->isValidFieldname()) {
                    $dwfproductextrafields->save();

                    Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&conf='.$confMessageId);
                } else {
                    $html_before .= $this->displayError($this->trans('This fieldname already exists in database.', [], 'Modules.Dwfproductextrafields.Admin'));
                }
            } else {
                $html_before .= $this->displayError($this->trans('An error occurred while attempting to save.', [], 'Modules.Dwfproductextrafields.Admin'));
            }
        }

        if (Tools::isSubmit('updatedwfproductextrafields') || Tools::isSubmit('adddwfproductextrafields')) {
            $this->context->controller->addJqueryUi('ui.sortable');
            $this->context->controller->addJqueryPlugin('unserialize');
            $this->context->controller->addJS($this->_path.'views/js/dwfproductextrafields.js?v='.$this->version);
            $this->context->controller->addCSS($this->_path.'views/css/dwfproductextrafields.css', 'all');

            Media::addJsDef([
                'dwfproductextrafields_adminController' => $adminController,
            ]);

            $helper = $this->initForm();
            $this->context->smarty->assign([
                'languages'       => Language::getLanguages(false),
                'default_lang'    => (int)Configuration::get('PS_LANG_DEFAULT'),
            ]);
            if ($id_dwfproductextrafields) {
                $dwfproductextrafields = new DwfProductExtraFieldsClass((int)$id_dwfproductextrafields);
                $helper->fields_value['fieldname'] = $dwfproductextrafields->fieldname;
                foreach (Language::getLanguages(false) as $lang) {
                    if (!empty($dwfproductextrafields->label[(int)$lang['id_lang']])) {
                        $helper->fields_value['label'][(int)$lang['id_lang']] = $dwfproductextrafields->label[(int)$lang['id_lang']];
                    } else {
                        $helper->fields_value['label'][(int)$lang['id_lang']] = '';
                    }
                    if (!empty($dwfproductextrafields->hint[(int)$lang['id_lang']])) {
                        $helper->fields_value['hint'][(int)$lang['id_lang']] = $dwfproductextrafields->hint[(int)$lang['id_lang']];
                    } else {
                        $helper->fields_value['hint'][(int)$lang['id_lang']] = '';
                    }
                }

                $helper->fields_value['type'] = $dwfproductextrafields->type;
                $helper->fields_value['location'] = $dwfproductextrafields->location;
                $helper->fields_value['active'] = (int)$dwfproductextrafields->active;

                $this->context->smarty->assign([
                    'field' => (array)$dwfproductextrafields,
                ]);

                if ($dwfproductextrafields->location == 'vars') {
                    $html_after .= $this->display(__FILE__, 'views/templates/admin/info.tpl');
                }
            } else {
                $helper->fields_value['fieldname'] = Tools::getValue('fieldname');
                foreach (Language::getLanguages(false) as $lang) {
                    $helper->fields_value['label'][(int)$lang['id_lang']] = Tools::getValue('label_'.(int)$lang['id_lang'], '');
                    $helper->fields_value['hint'][(int)$lang['id_lang']] = Tools::getValue('hint_'.(int)$lang['id_lang'], '');
                }

                $helper->fields_value['type'] = Tools::getValue('type');
                $helper->fields_value['location'] = Tools::getValue('location');
                $helper->fields_value['active'] = (int)Tools::getValue('active');
            }

            $helper->fields_value['config'] = $this->display(__FILE__, 'views/templates/admin/config-empty.tpl');

            if ($id_dwfproductextrafields = Tools::getValue('id_dwfproductextrafields')) {
                $this->fields_form[0]['form']['input'][] = ['type' => 'hidden', 'name' => 'id_dwfproductextrafields'];
                $helper->fields_value['id_dwfproductextrafields'] = (int)$id_dwfproductextrafields;
            }

            return $html_before.$helper->generateForm($this->fields_form).$html_after;
        } elseif (Tools::isSubmit('deletedwfproductextrafields')) {
            $dwfproductextrafields = new DwfProductExtraFieldsClass((int)$id_dwfproductextrafields);
            $dwfproductextrafields->delete();
            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
        } elseif (Tools::getIsset('statusdwfproductextrafields')) {
            $dwfproductextrafields = new DwfProductExtraFieldsClass((int)$id_dwfproductextrafields);
            if ($dwfproductextrafields->active == 0) {
                $dwfproductextrafields->active = 1;
            } else {
                $dwfproductextrafields->active = 0;
            }
            $dwfproductextrafields->save();
            Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
        } else {
            if (Tools::getValue('id_dwfproductextrafields') && Tools::getIsset('position') && Tools::getIsset('way')) {
                $productextrafields = new DwfProductExtraFieldsClass((int)Tools::getValue('id_dwfproductextrafields'));
                $productextrafields->updatePosition(Tools::getValue('way'), Tools::getValue('position'));
            }

            $helper = $this->initList();


            Media::addJsDef([
                'dwfproductextrafields_adminController' => $adminController,
                'module_name' => $this->name,
                'module_identifier' => 'id_' . $this->name,
                'currentIndex' => $this->context->link->getAdminLink($this->adminControllers['adminAjax']),
                'token' => Tools::getAdminTokenLite($this->adminControllers['adminAjax']),
            ]);
            return $html_before.$helper->generateList($this->getListContent((int)Configuration::get('PS_LANG_DEFAULT')), $this->fields_list);
        }
    }

    public function getExtraFieldImagePath($id)
    {
        if(!file_exists(_PS_ROOT_DIR_._PS_IMG_.'modules' . DIRECTORY_SEPARATOR . 'dwfproductextrafields')) {
            if(!file_exists(_PS_ROOT_DIR_._PS_IMG_.'modules')) {
                mkdir(_PS_ROOT_DIR_._PS_IMG_.'modules');
            }
            mkdir(_PS_ROOT_DIR_._PS_IMG_.'modules' . DIRECTORY_SEPARATOR . 'dwfproductextrafields');
        }

        return ltrim(_PS_IMG_.'modules/dwfproductextrafields', '/');
    }

    protected function getListContent($id_lang)
    {
        return Db::getInstance()->ExecuteS('
            SELECT pef.`id_dwfproductextrafields`, pef.`fieldname`, pefl.`label`, pefl.`hint`, pef.`type`, pef.`config`, pef.`location`, pef.`position`, pef.active
            FROM `'._DB_PREFIX_.'dwfproductextrafields` pef, `'._DB_PREFIX_.'dwfproductextrafields_lang` pefl
            WHERE pef.id_dwfproductextrafields = pefl.id_dwfproductextrafields
            AND pefl.`id_lang` = '.(int)$id_lang.'
            ORDER BY pef.position ASC');
    }

    protected function initForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $this->fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->trans('New Product Extra Field.', [], 'Modules.Dwfproductextrafields.Admin'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Fieldname', [], 'Modules.Dwfproductextrafields.Admin'),
                    'name' => 'fieldname',
                    'size' => 33,
                    'required' => true,
                    'hint' => $this->trans('Forbidden characters:', [], 'Modules.Dwfproductextrafields.Admin').' !<>,;?=+()@#�{}-$%:'
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Label', [], 'Admin.Global'),
                    'lang' => true,
                    'name' => 'label',
                    'size' => 33,
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Hint', [], 'Modules.Dwfproductextrafields.Admin'),
                    'lang' => true,
                    'name' => 'hint',
                    'size' => 33,
                    'hint' => $this->trans('This message will be displayed on the product admin page, above the field, to help you fill this', [], 'Modules.Dwfproductextrafields.Admin'),
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Type', [], 'Modules.Dwfproductextrafields.Admin'),
                    'name' => 'type',
                    'required' => false,
                    'default_value' => 'text',
                    'options' => [
                        'query' => [
                            ['key' => 'textarea_mce', 'name' => $this->trans('Textarea with tinymce editor', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'textarea', 'name' => $this->trans('Textarea', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'text', 'name' => $this->trans('Text', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'non-translatable_text', 'name' => $this->trans('non-translatable text', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'checkbox', 'name' => $this->trans('Checkbox', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'selector', 'name' => $this->trans('Selector', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'integer', 'name' => $this->trans('Integer', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'decimal', 'name' => $this->trans('Decimal', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'price', 'name' => $this->trans('Price', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'date', 'name' => $this->trans('Date', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'datetime', 'name' => $this->trans('Datetime', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'image', 'name' => $this->trans('Image', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'color', 'name' => $this->trans('Color', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'repeater', 'name' => $this->trans('Repeater', [], 'Modules.Dwfproductextrafields.Admin')],
                        ],
                        'name' => 'name',
                        'id' => 'key'
                    ]
                ],
                [
                    'type' => 'free',
                    'name' => 'config',
                    'col' => 9
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Location', [], 'Modules.Dwfproductextrafields.Admin'),
                    'name' => 'location',
                    'required' => false,
                    'default_value' => 'tab',
                    'options' => [
                        'query' => [
                            ['key' => 'tab', 'name' => $this->trans('Product tab', [], 'Modules.Dwfproductextrafields.Admin')],
                            ['key' => 'vars', 'name' => $this->trans('Product var', [], 'Modules.Dwfproductextrafields.Admin')],
                        ],
                        'name' => 'name',
                        'id' => 'key'
                    ]
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Status', [], 'Admin.Global'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', [], 'Admin.Global')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', [], 'Admin.Global')
                        ]
                    ],
                ]
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = 'dwfproductextrafields';
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = [
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            ];
        }

        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->toolbar_scroll = true;
        $helper->title = $this->displayName;
        $helper->submit_action = 'savedwfproductextrafields';
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->trans('Save', [], 'Admin.Actions'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->trans('Back to list', [], 'Admin.Actions')
            ]
        ];
        return $helper;
    }

    protected function initList()
    {
        $this->fields_list = [
            'id_dwfproductextrafields' => [
                'title' => $this->trans('ID', [], 'Admin.Global'),
                'width' => 50,
                'type' => 'text',
            ],
            'label' => [
                'title' => $this->trans('Label', [], 'Admin.Global'),
                'width' => 140,
                'type' => 'text',
                'filter_key' => 'pefl!label'
            ],
            'type' => [
                'title' => $this->trans('Type', [], 'Admin.Global'),
                'width' => 70,
                'type' => 'text',
                'filter_key' => 'pef!type'
            ],
            'location' => [
                'title' => $this->trans('Location', [], 'Modules.Dwfproductextrafields.Admin'),
                'width' => 70,
                'type' => 'text',
                'filter_key' => 'pef!location'
            ],
            'position' => [
                'title' => $this->trans('Position', [], 'Admin.Global'),
                'width' => 70,
                'filter_key' => 'pef!position',
                'align' => 'center',
                'position' => 'position'
            ],
            'active' => [
                'title' => $this->trans('Status', [], 'Admin.Global'),
                'width' => 25,
                'filter_key' => 'pef!active',
                'align' => 'center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false
            ],
        ];

        if (Shop::isFeatureActive()) {
            $this->fields_list['id_shop'] = ['title' => $this->trans('ID Shop', [], 'Admin.Global'), 'align' => 'center', 'width' => 25, 'type' => 'int'];
        }

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_dwfproductextrafields';
        $helper->position_identifier = 'id_dwfproductextrafields';
        $helper->orderBy = 'position';
        $helper->orderWay = 'ASC';
        $helper->actions = ['edit', 'delete'];
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = [
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->trans('Add new', [], 'Admin.Actions')
        ];

        $helper->listTotal = count($this->getListContent((int)Configuration::get('PS_LANG_DEFAULT')));
        $helper->title = $this->displayName;
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        return $helper;
    }

    protected function buildAdminFields($params)
    {
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/DwfProductExtraFieldsClass.php';
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/ProductExtraField.php';

        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields = [];
        $active_fields = DwfProductExtraFieldsClass::getActiveFields();
        foreach ($active_fields as $field) {
            $extra_field = ProductExtraField::getByIdProduct((int)$params['id_product']);
            $field_el = [
                'id'        => $field->fieldname.'_'.$field->id,
                'label'     => $field->label[$this->context->language->id],
                'hint'      => $field->hint[$this->context->language->id],
                'name'      => $field->fieldname,
                'value'     => $extra_field->{$field->fieldname},
                'type'      => $field->type,
                'is_multi_lang' => $field->isMultilangField(),
                'location'  => $field->location,
                'required'  => false,
            ];

            if ($field_el['type'] == 'selector') {
                $config = json_decode($field->config, true);
                $choices = [];
                if ($config['values']) {
                    foreach ($config['values'] as $val) {
                        $key = array_search($default_lang, array_column($val['label'], 'id_lang'));
                        $choices[] = ['key' => $val['value'], 'name' => $val['label'][$key]['value']];
                    }
                    $field_el['choices'] = $choices;
                    $field_el['multiple'] = (bool)$config['multiple'];
                }
                $field_el['value'] = explode(',', $extra_field->{$field->fieldname});
            } elseif ($field_el['type'] == 'repeater') {
                $config = json_decode($field->config, true);
                $elements = [];
                if ($config['elements']) {
                    $values = [];
                    if (!empty($extra_field->{$field->fieldname})) {
                        foreach ($params['languages'] as $lang) {
                            if (!empty($jsonValues = $extra_field->{$field->fieldname}[$lang['id_lang']])) {
                                $arrayValues = json_decode($jsonValues, true);
                                if (is_array($arrayValues) && count($arrayValues) > 0) {
                                    for ($i = 0; $i < count($arrayValues); $i++) {
                                        foreach ($config['elements'] as $k => $el) {
                                            $key = array_search($default_lang, array_column($el['name'], 'id_lang'));
                                            $values[$i][$k]['type'] = $el['type'];
                                            $values[$i][$k]['id'] = $el['key'];
                                            $values[$i][$k]['name'] = html_entity_decode($el['name'][$key]['value'], ENT_QUOTES);
                                            if (in_array($el['type'], ['text', 'textarea', 'textarea_mce'])) {
                                                $values[$i][$k]['value'][$lang['id_lang']] = (isset($arrayValues[$i][$el['key']]) ? $arrayValues[$i][$el['key']] : '');
                                            } else {
                                                $values[$i][$k]['value'] = (isset($arrayValues[$i][$el['key']]) ? $arrayValues[$i][$el['key']] : '');
                                            }

                                            if ($el['type'] == 'image' && $values[$i][$k]['value']) {
                                                $image = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $this->getExtraFieldImagePath((int)$params['id_product']) . '/' . $values[$i][$k]['value'];
                                                if (file_exists($image)) {
                                                    $image_url = ImageManager::thumbnail($image, 'extrafield_' . $field->fieldname . '-' . $el['key'] . $i . '.jpg', 100, 'jpg', true, true);
                                                    $image_size = file_exists($image) ? filesize($image) / 1000 : false;

                                                    $file_infos = pathinfo($values[$i][$k]['value']);

                                                    $values[$i][$k]['basename'] = $file_infos['filename'];
                                                    $values[$i][$k]['image_zoom'] = $image_url ? $this->context->shop->physical_uri . $this->getExtraFieldImagePath((int)$params['id_product']) . '/' . $values[$i][$k]['value'] : false;
                                                    $values[$i][$k]['image'] = $image_url ? str_replace('../img/tmp/', _PS_TMP_IMG_, $image_url) : false;
                                                    $values[$i][$k]['size'] = $image_size;
                                                    $values[$i][$k]['delete_url'] = AdminController::$currentIndex . '&id_product=' . (int)$params['id_product'] . '&updateproduct&key_tab=ModuleDwfproductextrafields&token=' . Tools::getAdminTokenLite('AdminProducts') . '&deleteImageExtraField=' . $field_el['name'];
                                                } else {
                                                    $values[$i][$k]['value'] = '';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    foreach ($config['elements'] as $el) {
                        $key = array_search($default_lang, array_column($el['name'], 'id_lang'));
                        $elements[] = [
                            'type'  => $el['type'],
                            'id'    => $el['key'],
                            'name'  => html_entity_decode($el['name'][$key]['value'], ENT_QUOTES),
                        ];
                    }
                    $field_el['collapsed_default'] = (bool)$config['collapsed_default'];
                    $field_el['elements'] = $elements;
                    $field_el['values'] = $values;
                }
            } elseif ($field_el['type'] == 'image') {
                if ($field_el['value']) {
                    $image = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$this->getExtraFieldImagePath((int)$params['id_product']).'/'.$field_el['value'];
                    if (file_exists($image)) {
                        $image_url = ImageManager::thumbnail($image, 'extrafield_' . $field->fieldname . '.jpg', 100, 'jpg', true, true);
                        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

                        $file_infos = pathinfo($field_el['value']);

                        $field_el['basename'] = $file_infos['filename'];
                        $field_el['image_zoom'] = $image_url ? $this->context->shop->physical_uri . $this->getExtraFieldImagePath((int)$params['id_product']) . '/' . $field_el['value'] : false;
                        $field_el['image'] = $image_url ? str_replace('../img/tmp/', _PS_TMP_IMG_, $image_url) : false;
                        $field_el['size'] = $image_size;
                        $field_el['delete_url'] = AdminController::$currentIndex . '&id_product=' . (int)$params['id_product'] . '&updateproduct&key_tab=ModuleDwfproductextrafields&token=' . Tools::getAdminTokenLite('AdminProducts') . '&deleteImageExtraField=' . $field_el['name'];
                    } else {
                        $field_el['value'] = '';
                    }
                }
                $field_el['upload_url'] = AdminController::$currentIndex.'&id_product='.(int)$params['id_product'].'&updateproduct&key_tab=ModuleDwfproductextrafields&token='.Tools::getAdminTokenLite('AdminProducts').'&fieldType=image&uploadImageExtraField='.$field_el['name'];
            }

            $fields[] = $field_el;
        }

        return $fields;
    }


    public function hookActionDispatcherBefore()
    {
        if (!defined('_PS_ADMIN_DIR_')) {
            require_once _PS_MODULE_DIR_ . 'dwfproductextrafields/models/ProductExtraField.php';
            $this->context->smarty->registerPlugin('modifier', 'productExtraFieldValue', ['ProductExtraField', 'getProductExtraFieldValue']);
        }
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        if ($this->context->controller->controller_name == 'AdminProducts') {
            if (version_compare(_PS_VERSION_, '8.0.0', '>=')
                && (
                    Db::getInstance()->getValue('SELECT `state` FROM `'._DB_PREFIX_.'feature_flag` WHERE name = "product_page_v2"')
                    || version_compare(_PS_VERSION_, '9.0.0', '>=')
                )
            ) {
                $this->context->controller->addJS(__PS_BASE_URI__.'js/admin/tinymce.inc.js');
                $this->context->controller->addJS(__PS_BASE_URI__.'js/admin/tinymce_loader.js');
            }
            require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/DwfProductExtraFieldsClass.php';
            $active_fields = DwfProductExtraFieldsClass::getActiveFields();
            if (count($active_fields)) {
                foreach ($active_fields as $field) {
                    if ($field->type == 'color') {
                        $this->context->controller->addJqueryPlugin('colorpicker');
                        return;
                    }
                }
            }
        }
    }

    public function hookActionGetProductPropertiesAfter($params)
    {
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/DwfProductExtraFieldsClass.php';
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/ProductExtraField.php';

        $active_fields = DwfProductExtraFieldsClass::getActiveFields();
        if (count($active_fields)) {
            $extra_field = ProductExtraField::getByIdProduct($params['product']['id_product']);

            foreach ($active_fields as $field) {
                if ($extra_field->id) {
                    if ($field->isMultilangField()) {
                        if ($field->type == 'repeater') {
                            $params['product']['dwf_' . $field->fieldname] = null;
                            if (!empty($extra_field->{$field->fieldname}[$params['id_lang']])) {
                                $repeater = json_decode($extra_field->{$field->fieldname}[$params['id_lang']], true);
                                if (!empty($repeater) && count($repeater)) {
                                    $config = json_decode($field->config, true);
                                    $keys = array_keys(array_column($config['elements'], 'type'), 'image');
                                    if (count($keys)) {
                                        foreach ($repeater as &$el) {
                                            foreach ($keys as $key) {
                                                if (isset($el[$config['elements'][$key]['key']]) && $el[$config['elements'][$key]['key']] && file_exists(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $this->getExtraFieldImagePath($params['product']['id_product']) . '/' . $el[$config['elements'][$key]['key']])) {
                                                    $el[$config['elements'][$key]['key']] = $this->context->link->getMediaLink($this->context->shop->physical_uri . $this->getExtraFieldImagePath($params['product']['id_product']) . '/' . $el[$config['elements'][$key]['key']]);
                                                } else {
                                                    $el[$config['elements'][$key]['key']] = null;
                                                }
                                            }
                                        }
                                    }
                                    $params['product']['dwf_' . $field->fieldname] = $repeater;
                                }
                            }
                        } else {
                            $params['product']['dwf_'.$field->fieldname] = $extra_field->{$field->fieldname}[$params['id_lang']];
                        }
                    } elseif ($field->type == 'image') {
                        if ($extra_field->{$field->fieldname} && file_exists(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$this->getExtraFieldImagePath($params['product']['id_product']).'/'.$extra_field->{$field->fieldname})) {
                            $params['product']['dwf_' . $field->fieldname] = $this->context->link->getMediaLink($this->context->shop->physical_uri . $this->getExtraFieldImagePath($params['product']['id_product']) . '/' . $extra_field->{$field->fieldname});
                        } else {
                            $params['product'][ 'dwf_' . $field->fieldname ] = null;
                        }
                    } elseif ($field->type == 'selector') {

                        $label = '';
                        $config = json_decode($field->config, true);

                        if (!empty($config['multiple'])) {
                            $params['product']['dwf_' . $field->fieldname] = [];
                            $params['product']['dwf_' . $field->fieldname . '_label'] = [];
                        } else {
                            $params['product']['dwf_' . $field->fieldname] = '';
                            $params['product']['dwf_' . $field->fieldname . '_label'] = '';
                        }

                        if (!empty($extra_field->{$field->fieldname})) {
                            foreach (explode(',', $extra_field->{$field->fieldname}) as $val) {
                                $key = array_search($val, array_column($config['values'], 'value'));
                                if (false !== $key) {
                                    $currentValue = $config['values'][$key];
                                    $keyLang = array_search($this->context->language->id, array_column($currentValue['label'], 'id_lang'));
                                    if (false !== $keyLang) {
                                        // label for current language
                                        $label = $currentValue['label'][$keyLang]['value'];
                                    }

                                    if (!$label) {
                                        $default_language = (int)Configuration::get('PS_LANG_DEFAULT');
                                        $keyLang = array_search($default_language, array_column($currentValue['label'], 'id_lang'));
                                        if (false !== $keyLang) {
                                            // if label for current language, getting the label for default
                                            $label = $currentValue['label'][$keyLang]['value'];
                                        }
                                    }
                                }

                                if ($config['multiple']) {
                                    $params['product']['dwf_' . $field->fieldname][] = $val;
                                    $params['product']['dwf_' . $field->fieldname . '_label'][] = $label;
                                } else {
                                    $params['product']['dwf_' . $field->fieldname] = $val;
                                    $params['product']['dwf_' . $field->fieldname . '_label'] = $label;
                                }
                            }
                        }
                    } else {
                        $params['product']['dwf_'.$field->fieldname] = $extra_field->{$field->fieldname};
                    }
                } else {
                    $params['product']['dwf_'.$field->fieldname] = null;
                }
            }
        }
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        if (Validate::isLoadedObject($product = new Product((int)$params['id_product']))) {
            $iso_tiny_mce = $this->context->language->iso_code;
            $iso_tiny_mce = (file_exists(_PS_ROOT_DIR_ . _PS_JS_DIR_.'tiny_mce/langs/'.$iso_tiny_mce.'.js') ? $iso_tiny_mce : 'en');

            $languages = Language::getLanguages(false);
            $params['languages'] = $languages;

            $this->context->smarty->assign([
                'languages' => $languages,
                'defaultLanguageIso' => Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT')),
                'defaultFormLanguage' => (int)Configuration::get('PS_LANG_DEFAULT'),
                'currentIndex_modules' => 'index.php?controller=AdminModules&configure='.$this->name,
                'token_modules' => Tools::getAdminTokenLite('AdminModules')
            ]);

            if (Tools::getIsset('deleteImageExtraField')) {
                require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/ProductExtraField.php';
                $field_name = Tools::getValue('deleteImageExtraField');
                $extra_field = ProductExtraField::getByIdProduct($product->id);
                unlink(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$this->getExtraFieldImagePath($product->id).'/'.$extra_field->{$field_name});
                $extra_field->{$field_name} = '';
                $extra_field->save();

                Tools::redirectAdmin(AdminController::$currentIndex.'&id_product='.$product->id.'&updateproduct&key_tab=ModuleDwfproductextrafields&token='.Tools::getAdminTokenLite('AdminProducts'));
            }

            $fields = $this->buildAdminFields($params);

            $adminController = $this->context->link->getAdminLink($this->adminControllers['adminAjax'], true, [], ['id_product' => $product->id]);
            $this->context->smarty->assign([
                'dwfproductextrafields_adminController' => $adminController,
                'product_id' => $product->id,
                'warnings' => [],
                'fields' => $fields,
                'iso_tiny_mce' => $iso_tiny_mce,
            ]);

            if (version_compare(_PS_VERSION_, '8.0.0', '>=')
                && (
                    Db::getInstance()->getValue('SELECT `state` FROM `'._DB_PREFIX_.'feature_flag` WHERE name = "product_page_v2"')
                    || version_compare(_PS_VERSION_, '9.0.0', '>=')
                )
            ) {
                $template = 'module:dwfproductextrafields/views/templates/admin/adminProduct-extraFields-v2.tpl';
            } else {
                $template = 'module:dwfproductextrafields/views/templates/admin/adminProduct-extraFields.tpl';
            }

            return $this->context->smarty->fetch($template);
        }
    }

    protected function updateProductExtraFields($productId, $extraFieldsValues)
    {
        if (Validate::isLoadedObject($product = new Product((int)$productId))) {
            require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/DwfProductExtraFieldsClass.php';
            require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/ProductExtraField.php';

            $floatParser = new FloatParser();

            $active_fields = DwfProductExtraFieldsClass::getActiveFields();
            if (count($active_fields)) {
                $languages = Language::getLanguages(false);
                $extra_field = ProductExtraField::getByIdProduct($product->id);
                foreach ($active_fields as $field) {
                    if ($field->type == 'image') {
                        if (!empty(Tools::isSubmit('ModuleDwfproductextrafields'))) {
                            if (!empty($extraFieldsValues[$field->fieldname . '_files'])) {
                                $extra_field->{$field->fieldname} = $extraFieldsValues[$field->fieldname . '_files'];
                            }
                        } else {
                            /** @var OhUploadedFile $file_upload */
                            if (!empty($extraFieldsValues[$field->fieldname])) {
                                $file_upload = $extraFieldsValues[$field->fieldname];
                                if (!empty($file_upload)) {
                                    if (!$file_upload->isValid()) {
                                        $this->context->controller->errors[] = $file_upload->getErrorMessage();
                                    } elseif (strlen($file_upload->getClientOriginalName()) >= 128) {
                                        $this->context->controller->errors[] = $this->trans('The name of the uploaded file for the "[filename]" field is too long. It must be less than 128 characters.', ['[filename]' => $field->label], 'Modules.Dwfproductextrafields.Admin');
                                    } else {
                                        if (!file_exists(_PS_IMG_DIR_ . 'modules/' . $this->name . '/' . $productId)) {
                                            mkdir(_PS_IMG_DIR_ . 'modules/' . $this->name . '/' . $productId);
                                        }

                                        $fileNewName = uniqid() . '_' . $file_upload->getClientOriginalName();
                                        if ($res = $file_upload->move(_PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $this->getExtraFieldImagePath($productId), $fileNewName)) {
                                            $extra_field->{$field->fieldname} = $fileNewName;
                                        }
                                    }
                                }
                            }
                        }
                    } elseif ($field->type == 'repeater') {
                        $repeater = $extraFieldsValues[$field->fieldname];
                        if (is_array($repeater)) {
                            if (isset($repeater['LINEID'])) {
                                unset($repeater['LINEID']);
                            }

                            if (count($repeater)) {
                                $config = json_decode($field->config, true);
                                if ($config['elements']) {
                                    $i = 0;
                                    $value = [];
                                    foreach ($repeater as $rep) {
                                        foreach ($config['elements'] as $element) {
                                            if (in_array($element['type'], ['text', 'textarea', 'textarea_mce'])) {
                                                foreach ($languages as $lang) {
                                                    if (isset($rep[$element['key'] . '_' . $lang['id_lang']])) {
                                                        $value[$lang['id_lang']][$i][$element['key']] = $rep[$element['key'] . '_' . $lang['id_lang']];
                                                    } else {
                                                        $value[$lang['id_lang']][$i][$element['key']] = '';
                                                    }
                                                }
                                            } elseif ($element['type'] == 'image') {
                                                foreach ($languages as $lang) {
                                                    if (isset($rep[$element['key'] . '_files'])) {
                                                        $value[$lang['id_lang']][$i][$element['key']] = $rep[$element['key'] . '_files'];
                                                    } else {
                                                        $value[$lang['id_lang']][$i][$element['key']] = '';
                                                    }
                                                }
                                            } else {
                                                foreach ($languages as $lang) {
                                                    if ($element['type'] == 'checkbox') {
                                                        $value[$lang['id_lang']][$i][$element['key']] = 0;
                                                    }

                                                    if (isset($rep[$element['key']])) {
                                                        $value[$lang['id_lang']][$i][$element['key']] = $rep[$element['key']];
                                                    } else {
                                                        $value[$lang['id_lang']][$i][$element['key']] = '';
                                                    }
                                                }
                                            }
                                        }
                                        $i++;
                                    }
                                }

                                foreach ($languages as $lang) {
                                    $extra_field->{$field->fieldname}[$lang['id_lang']] = json_encode($value[$lang['id_lang']]);
                                }
                            } else {
                                foreach ($languages as $lang) {
                                    $extra_field->{$field->fieldname}[$lang['id_lang']] = '';
                                }
                            }
                        }
                    } elseif ($field->type != 'selector' && $field->isMultilangField()) {
                        foreach ($languages as $lang) {
                            if (isset($extraFieldsValues[$field->fieldname][$lang['id_lang']])) {
                                $extra_field->{$field->fieldname}[$lang['id_lang']] = $extraFieldsValues[$field->fieldname][$lang['id_lang']];
                            } else {
                                $extra_field->{$field->fieldname}[$lang['id_lang']] = '';
                            }
                        }
                    } else {
                        if (in_array($field->type, ['checkbox', 'integer', 'price', 'decimal'])) {
                            $extra_field->{$field->fieldname} = 0;
                        }

                        if (isset($extraFieldsValues[$field->fieldname])) {
                            if (is_array($extraFieldsValues[$field->fieldname])) {
                                $extra_field->{$field->fieldname} = implode(',', $extraFieldsValues[$field->fieldname]);
                            } else {
                                if (in_array($field->type, ['price', 'decimal'])) {
                                    $extra_field->{$field->fieldname} = $floatParser->fromString($extraFieldsValues[$field->fieldname]);
                                } elseif ($field->type == 'integer') {
                                    $extra_field->{$field->fieldname} = (int)$extraFieldsValues[$field->fieldname];
                                } elseif ($field->type == 'datetime') {
                                    if ($extraFieldsValues[$field->fieldname]) {
                                        $extra_field->{$field->fieldname} = date('Y-m-d H:i:s', strtotime($extraFieldsValues[$field->fieldname]));
                                    } else {
                                        $extra_field->{$field->fieldname} = null;
                                    }
                                } elseif ($field->type == 'date') {
                                    if ($extraFieldsValues[$field->fieldname]) {
                                        $extra_field->{$field->fieldname} = date('Y-m-d', strtotime($extraFieldsValues[$field->fieldname]));
                                    } else {
                                        $extra_field->{$field->fieldname} = null;
                                    }
                                } else {
                                    $extra_field->{$field->fieldname} = $extraFieldsValues[$field->fieldname];
                                }
                            }
                        }
                    }
                }
                $extra_field->save();
            }
        }
    }

    public function hookActionAfterUpdateProductFormHandler($params)
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')
            && (
                Db::getInstance()->getValue('SELECT `state` FROM `'._DB_PREFIX_.'feature_flag` WHERE name = "product_page_v2"')
                || version_compare(_PS_VERSION_, '9.0.0', '>=')
            )
        ) {
            $data = $params['form_data']['dwfproductextrafields'] ?? [];
            $this->updateProductExtraFields($params['id'], $data);
        }
    }

    public function hookActionAdminProductsControllerSaveAfter($params)
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '<') ||
            (version_compare(_PS_VERSION_, '8.0.0', '>=') AND !Db::getInstance()->getValue('SELECT `state` FROM `'._DB_PREFIX_.'feature_flag` WHERE name = "product_page_v2"'))
        ) {
            $data = Tools::getValue('dwfproductextrafields', []);
            $this->updateProductExtraFields((int)Tools::getValue('id_product'), $data);
        }
    }

    /**
     * @param $params
     */
    public function hookActionAdminDuplicateAfter($params)
    {
        if (
            !empty($params['route'])
            && in_array($params['route'], ['admin_products_duplicate_shop', 'admin_products_bulk_duplicate_shop'])
            && !empty($params['id_product'])
            && !empty($params['id_product_new'])
        ) {
            require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/DwfProductExtraFieldsClass.php';
            require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/ProductExtraField.php';

            $active_fields = DwfProductExtraFieldsClass::getActiveFields();
            if (count($active_fields)) {
                $extra_field = ProductExtraField::getByIdProduct($params['id_product']);
                $duplicatedField = new ProductExtraField();
                $duplicatedField->id_product = $params['id_product_new'];
                foreach ($active_fields as $field) {
                    if ($field->type == 'image') {
                        $duplicatedField->{$field->fieldname} = '';
                        if (file_exists(_PS_ROOT_DIR_.$this->getExtraFieldImagePath($extra_field->id_product).'/'.$extra_field->{$field->fieldname})) {
                            $new_path = _PS_ROOT_DIR_.$this->getExtraFieldImagePath($duplicatedField->id_product).'/';
                            $new_file = uniqid().Tools::strtolower(Tools::substr($extra_field->{$field->fieldname}, -5));

                            if (ImageManager::resize(_PS_ROOT_DIR_.$this->getExtraFieldImagePath($extra_field->id_product).'/'.$extra_field->{$field->fieldname}, $new_path.$new_file, null, null, 'jpg', false)) {
                                $duplicatedField->{$field->fieldname} = $new_file;
                            }
                        }
                    } else {
                        $duplicatedField->{$field->fieldname} = $extra_field->{$field->fieldname};
                        if ($field->type == 'repeater') {
                            $config = json_decode($field->config, true);
                            $keys = array_keys(array_column($config['elements'], 'type'), 'image');
                            if (count($keys)) {
                                $languages = Language::getLanguages(false);
                                $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
                                $repeaterDefault = json_decode($duplicatedField->{$field->fieldname}[$default_lang], true);
                                foreach ($repeaterDefault as &$el) {
                                    foreach ($keys as $key) {
                                        $originalImage = $el[$config['elements'][$key]['key']];
                                        $el[$config['elements'][$key]['key']] = null;
                                        if (isset($originalImage) && $originalImage && file_exists(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$this->getExtraFieldImagePath($extra_field->id_product).'/'.$originalImage)) {
                                            $new_path = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$this->getExtraFieldImagePath($duplicatedField->id_product).'/';
                                            $new_file = uniqid().Tools::strtolower(Tools::substr($originalImage, -5));
                                            if (ImageManager::resize(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$this->getExtraFieldImagePath($extra_field->id_product).'/'.$originalImage, $new_path.$new_file, null, null, 'jpg', false)) {
                                                $el[$config['elements'][$key]['key']] = $new_file;
                                            }
                                        }
                                    }
                                }
                                $duplicatedField->{$field->fieldname}[$default_lang] = json_encode($repeaterDefault);

                                if (count($languages) > 1) {
                                    foreach ($languages as $lang) {
                                        if ($lang['id_lang'] != $default_lang) {
                                            foreach ($repeaterDefault as $i => $el) {
                                                foreach ($keys as $key) {
                                                    $repeater = json_decode($duplicatedField->{$field->fieldname}[$lang['id_lang']], true);
                                                    $repeater[$i][$config['elements'][$key]['key']] = $el[$config['elements'][$key]['key']];
                                                }
                                            }
                                            $duplicatedField->{$field->fieldname}[$lang['id_lang']] = json_encode($repeater);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $duplicatedField->save();
            }
        }
    }

    public function hookActionAdminProductsControllerDeleteAfter($params)
    {
        $id_product = false;
        if (!empty($params['product_id'])) {
            $id_product = (int)$params['product_id'];
        } elseif (!empty($params['product_list_id'])) {
            $id_product = (int)$params['product_list_id'];
        }
        if ($id_product) {
            require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/ProductExtraField.php';

            $extra_field = ProductExtraField::getByIdProduct($id_product);
            $extra_field->delete();
        }
    }

    public function hookdisplayProductExtraFields($params)
    {
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/ProductExtraField.php';

        if (!empty($params['id_product'])) {
            $id_product = (int)$params['id_product'];
        } elseif (!empty(Tools::getValue('id_product'))) {
            $id_product = (int)Tools::getValue('id_product');
        } else {
            return;
        }

        $fields = [];
        $extra_field = ProductExtraField::getByIdProduct($id_product);
        if ($extra_field->id) {
            $active_fields = DwfProductExtraFieldsClass::getActiveFields();
            foreach ($active_fields as $field) {
                if ($field->location == 'tab' && $extra_field->{$field->fieldname}[Context::getContext()->language->id]) {
                    $fields[] = [
                        'id' => $field->id,
                        'label' => $field->label[Context::getContext()->language->id],
                        'content' => $extra_field->{$field->fieldname}[Context::getContext()->language->id],
                    ];
                }
            }
        }
        $this->smarty->assign('fields_name', $fields);

        return $this->display(__FILE__, 'display-extrafields.tpl');
    }

    public function hookDisplayProductExtraContent($params)
    {
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/DwfProductExtraFieldsClass.php';
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/ProductExtraField.php';

        $fields = [];
        $extra_field = ProductExtraField::getByIdProduct($params['product']->id);
        if ($extra_field->id) {
            $active_fields = DwfProductExtraFieldsClass::getActiveFields();
            foreach ($active_fields as $field) {
                if ($field->location == 'tab' && $extra_field->{$field->fieldname}[Context::getContext()->language->id]) {
                    $fields[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())
                        ->setTitle($field->label[Context::getContext()->language->id])
                        ->setContent($extra_field->{$field->fieldname}[Context::getContext()->language->id])
                        ->setAttr([
                            'id' => $field->id,
                            'class' => 'extra_'.$field->id
                        ])
                    ;
                }
            }
        }
        return $fields;
    }

    public function hookAddWebserviceResources($params) {
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/ProductExtraField.php';
        return [
            'product_extra_fields' => [
                'description' => 'The product extra fields',
                'class' => 'ProductExtraField'
            ],
        ];
    }

    public function hookActionProductFormBuilderModifier(array $params): void
    {
        $productId = isset($params['id']) ? (int) $params['id'] : null;
        if (Validate::isLoadedObject($product = new Product($productId))) {

            require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/ProductExtraField.php';
            if (!empty($fieldname = Tools::getValue('deleteExtraFieldImage'))
                AND Validate::isLoadedObject($extra_field = ProductExtraField::getByIdProduct($productId))
                AND !empty($extra_field->{$fieldname})
            ) {
                $image = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $this->getExtraFieldImagePath($productId).$extra_field->{$fieldname};
                if (file_exists($image)) {
                    unlink($image);
                }
                $extra_field->{$fieldname} = '';
                $extra_field->save();

                /** @var RequestStack $requestStack */
                $requestStack = $this->get('request_stack');
                $session = $requestStack->getSession();
                if ($session) {
                    $session->getFlashBag()->add(
                        'success',
                        $this->trans('The image was successfully deleted.', [], 'Admin.Notifications.Success')
                    );
                }
                Tools::redirectAdmin($this->get('router')->generate('admin_products_edit', ['productId' => $productId], UrlGeneratorInterface::ABSOLUTE_URL).'#tab-product_dwfproductextrafields-tab');
            }

            $languages = Language::getLanguages(false);

            $fields = $this->buildAdminFields([
                'id_product' => $productId,
                'languages' => $languages,
            ]);

            /** @var ProductFormModifier $productFormModifier */
            $productFormModifier = $this->get(ProductFormModifier::class);
            $productFormModifier->modify($params['form_builder'], $productId, $fields);
        }


    }

}
