<?php
/**
 * 2015-2021 Bonpresta
 *
 * Bonpresta Column Category
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
 *  @copyright 2015-2021 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'boncategory/classes/CategoryBon.php');

class Boncategory extends Module
{
    private $category = array();

    public function __construct()
    {
        $this->name = 'boncategory';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->bootstrap = true;
        $this->author = 'Bonpresta';
        $this->module_key = '9e53dd4f1a80b1ce8d5e60161eac4d6c';
        parent::__construct();
        $this->default_language = Language::getLanguage(Configuration::get('PS_LANG_DEFAULT'));
        $this->id_shop = Context::getContext()->shop->id;
        $this->displayName = $this->l('Column Category');
        $this->description = $this->l('Display category in column.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        if (Configuration::get('PS_SSL_ENABLED')) {
            $this->ssl = 'https://';
        } else {
            $this->ssl = 'http://';
        }
    }

    protected function getConfigurations()
    {
        $configurations = array(
            'BON_CATEGORY_ITEM_STATUS' => true,
            'BON_CATEGORY_BACKGROUND' => true,
            'BON_CATEGORY_HEADER' => '#E8325E',
            'BON_CATEGORY_HEADER_COLOR' => '#ffffff',
        );

        return $configurations;
    }

    public function createAjaxController()
    {
        $tab = new Tab();
        $tab->active = 1;
        $languages = Language::getLanguages(false);
        if (is_array($languages)) {
            foreach ($languages as $language) {
                $tab->name[$language['id_lang']] = 'boncategory';
            }
        }
        $tab->class_name = 'AdminAjaxBonCategory';
        $tab->module = $this->name;
        $tab->id_parent = - 1;
        return (bool)$tab->add();
    }

    private function removeAjaxContoller()
    {
        if ($tab_id = (int)Tab::getIdFromClassName('AdminAjaxBonCategory')) {
            $tab = new Tab($tab_id);
            $tab->delete();
        }

        return true;
    }

    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');
        $this->installSamples();
        $configurations = $this->getConfigurations();

        foreach ($configurations as $name => $config) {
            Configuration::updateValue($name, $config);
        }

        return parent::install() &&
            $this->registerHook('displayHeader') &&
            $this->createAjaxController() &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayBoncategory') &&
            $this->registerHook('actionCategoryDelete');
    }

    protected function installSamples()
    {
        $languages = Language::getLanguages(false);
        for ($i = 1; $i <= 10; ++$i) {
            $item = new CategoryBon();
            $item->id_shop = (int)$this->context->shop->id;
            if ($i == 1) {
                $item->id_category = 11;
            } elseif ($i == 2) {
                $item->id_category = 12;
            } else {
                $item->id_category = $i;
            }
            $item->status = 1;
            $item->switch_icon = false;
            $item->sort_order = $i;
            foreach ($languages as $language) {
                $item->content[$language['id_lang']] = '';
            }

            $item->add();
        }
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');
        $configurations = $this->getConfigurations();

        foreach (array_keys($configurations) as $config) {
            Configuration::deleteByName($config);
        }

        return parent::uninstall()
            && $this->removeAjaxContoller();
    }
    protected function clearCache()
    {
        $this->_clearCache('boncategory.tpl');
    }

    public function getContent()
    {
        $output = '';

        if (((bool)Tools::isSubmit('submitCategory'))) {
            $output .= $this->addCategory();
        }

        if (((bool)Tools::isSubmit('submitConfiguration')) == true) {
            if (!$result = $this->preValidateForm()) {
                $output .= $this->postProcess();
                $this->clearCache();
                $output .= $this->displayConfirmation($this->l('Save all settings.'));
            } else {
                $output .= $result;
            }
        }

        if ((bool)Tools::isSubmit('deleteboncategory')) {
            $output .= $this->deleteCategory();
        }

        if ((bool)Tools::isSubmit('statusboncategory')) {
            $output .= $this->updateStatusTab('status');
        }

        if (Tools::getIsset('updateboncategory') || Tools::getValue('updateboncategory')) {
            $output .= $this->renderForm();
        } elseif (Tools::isSubmit('addboncategory')) {
            $output .= $this->renderForm();
        } else {
            $output .= $this->renderCategoriesList();
            $output .= $this->renderTabForm();
        }

        return $output;
    }

    public function renderCategoriesList()
    {
        if (!$tabs = CategoryBon::getCategoryList()) {
            $tabs = array();
        }

        $fields_list = array(
            'id_item' => array(
                'title' => $this->l('Id'),
                'type' => 'text',
                'col' => 6,
                'search' => false,
                'orderby' => false,
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'type'  => 'block_image',
                'align' => 'center',
                'search' => false,
            ),
            'name' => array(
                'title' => $this->l('Category'),
                'type' => 'text',
                'col' => 6,
                'search' => false,
                'orderby' => false,
            ),
            'status' => array(
                'title' => $this->l('Status'),
                'type' => 'bool',
                'active' => 'status',
                'search' => false,
                'orderby' => false,
            ),
            'sort_order' => array(
                'title' => $this->l('Position'),
                'type' => 'text',
                'search' => false,
                'orderby' => false,
                'class' => 'pointer dragHandle'
            )
        );

        $helper = new HelperList();

        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_item';
        $helper->table = 'boncategory';
        $helper->actions = array('edit', 'delete');
        $helper->show_toolbar = true;
        $helper->module = $this;
        $helper->title = $this->displayName;
        $helper->listTotal = count($tabs);
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex
                .'&configure='.$this->name.'&add'.$this->name
                .'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new item')
        );
        $helper->currentIndex = AdminController::$currentIndex
            .'&configure='.$this->name.'&id_shop='.(int)$this->context->shop->id;

        $helper->tpl_vars = array(
            'link' => new Link(),
            'base_dir' => $this->ssl,
            'ps_version' => _PS_VERSION_,
            'id_lang' => $this->context->language->id,
        );
        return $helper->generateList($tabs, $fields_list);
    }

    protected function renderForm()
    {
        $this->getCategoriesList();

        array_shift($this->category);

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => ((int)Tools::getValue('id_item')
                        ? $this->l('Update item')
                        : $this->l('Add item')),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select category'),
                        'name' => 'id_category',
                        'options' => array(
                            'query' => $this->category,
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'name' => 'sort_order',
                        'class' => 'hidden'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Status'),
                        'name' => 'status',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
//                    array(
//                        'type' => 'textarea',
//                        'label' => $this->l('HTML content'),
//                        'name' => 'content',
//                        'autoload_rte' => true,
//                        'lang' => true,
//                    ),
                    array(
                        'type' => 'switch',
                        'form_group_class' => 'bon_switch_icon',
                        'label' => $this->l('Add icon'),
                        'name' => 'switch_icon',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'form_group_class' => 'bon_category_icons_type',
                        'label' => $this->l('Select icon family'),
                        'name' => 'type_icon',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 'outicons',
                                    'name' => $this->l('Outicons')
                                ),
                                array(
                                    'id' => 'material_icons',
                                    'name' => $this->l('Material Icons')
                                ),
                            ),
                            'name' => 'name',
                            'id' => 'id',
                            'index' => 'type_icon'
                        ),
                        'col' => 2,
                    ),
                    array(
                        'type' => 'select',
                        'form_group_class' => 'bon_category_icons',
                        'label' => $this->l('Select an icon'),
                        'name' => 'icon',
                        'options' => array(
                            'query' => $this->getIcons(),
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'buttons' => array(
                    array(
                        'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='
                            .Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Back to list'),
                        'icon' => 'process-icon-back'
                    )
                )
            ),
        );

        if ((bool)Tools::getIsset('updateboncategory') && (int)Tools::getValue('id_item') > 0) {
            $item = new CategoryBon((int)Tools::getValue('id_item'));
            $fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_item', 'value' => (int)$item->id);
        }

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCategory';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getIcons()
    {
        $icons = array(
            array('id_option' => 'material-icons phone', 'name' => '&#xe0cd;', ),
            array('id_option' => 'material-icons store', 'name' => '&#xe8d1;'),
            array('id_option' => 'material-icons headset_mic', 'name' => '&#xe311;'),
            array('id_option' => 'people material-icons', 'name' => '&#xe7fb;'),
            array('id_option' => 'material-icons access_alarms', 'name' => '&#xe191;'),
            array('id_option' => 'material-icons account_circle', 'name' => '&#xe853;'),
            array('id_option' => 'material-icons build', 'name' => '&#xe869;'),
            array('id_option' => 'material-icons brush', 'name' => '&#xe3ae;'),
            array('id_option' => 'material-icons camera_alt', 'name' => '&#xe3b0;'),
            array('id_option' => 'material-icons card_giftcard', 'name' => '&#xe8f6;'),
            array('id_option' => 'material-icons chat', 'name' => '&#xe0b7;'),
            array('id_option' => 'material-icons chat_bubble', 'name' => '&#xe0ca;'),
            array('id_option' => 'material-icons chat_bubble_outline', 'name' => '&#xe0cb;'),
            array('id_option' => 'material-icons chevron_left', 'name' => '&#xe5cb;'),
            array('id_option' => 'material-icons chevron_right', 'name' => '&#xe5cc;'),
            array('id_option' => 'material-icons collections', 'name' => '&#xe3b6;'),
            array('id_option' => 'material-icons contacts', 'name' => '&#xe0ba;'),
            array('id_option' => 'material-icons quick_contacts_dialer', 'name' => '&#xe0cf;'),
            array('id_option' => 'material-icons color_lens', 'name' => '&#xe3b7;'),
            array('id_option' => 'material-icons computer', 'name' => '&#xe30a;'),
            array('id_option' => 'material-icons create', 'name' => '&#xe150;'),
            array('id_option' => 'material-icons crop_original', 'name' => '&#xe3c4;'),
            array('id_option' => 'material-icons desktop_mac', 'name' => '&#xe30b;'),
            array('id_option' => 'material-icons email', 'name' => '&#xe0be;'),
            array('id_option' => 'material-icons expand_less', 'name' => '&#xe5ce;'),
            array('id_option' => 'material-icons expand_more', 'name' => '&#xe5cf;'),
            array('id_option' => 'material-icons favorite', 'name' => '&#xe87e;'),
            array('id_option' => 'material-icons file_download', 'name' => '&#xe2c4;'),
            array('id_option' => 'material-icons forum', 'name' => '&#xe0bf;'),
            array('id_option' => 'material-icons help', 'name' => '&#xe887;'),
            array('id_option' => 'material-icons help_outline', 'name' => '&#xe8fd;'),
            array('id_option' => 'material-icons insert_comment', 'name' => '&#xe24c;'),
            array('id_option' => 'material-icons insert_invitation', 'name' => '&#xe24f;'),
            array('id_option' => 'material-icons insert_link', 'name' => '&#xe250;'),
            array('id_option' => 'material-icons insert_photo', 'name' => '&#xe251;'),
            array('id_option' => 'material-icons local_offer', 'name' => '&#xe54e;'),
            array('id_option' => 'material-icons location_on', 'name' => '&#xe0c8;'),
            array('id_option' => 'material-icons phone_android', 'name' => '&#xe324;'),
            array('id_option' => 'material-icons settings', 'name' => '&#xe8b8;'),
            array('id_option' => 'fl-outicons fl-outicons-headphones46', 'name' => '&#57383;'),
            array('id_option' => 'fl-outicons fl-outicons-house204', 'name' => '&#xE029;'),
            array('id_option' => 'fl-outicons fl-outicons-book270', 'name' => '&#xE007;'),
            array('id_option' => 'fl-outicons fl-outicons-chevron1', 'name' => '&#57357;'),
            array('id_option' => 'fl-outicons fl-outicons-chevron3', 'name' => '&#57359;'),
            array('id_option' => 'fl-outicons fl-outicons-chevron', 'name' => '&#57360;'),
            array('id_option' => 'fl-outicons fl-outicons-chevron2', 'name' => '&#57358;'),
            array('id_option' => 'fl-outicons fl-outicons-building106', 'name' => '&#57353;'),
            array('id_option' => 'fl-outicons fl-outicons-news', 'name' => '&#57401;'),
            array('id_option' => 'fl-outicons fl-outicons-question5', 'name' => '&#57415;'),
            array('id_option' => 'fl-outicons fl-outicons-sales2', 'name' => '&#57419;'),
            array('id_option' => 'fl-outicons fl-outicons-magnifying glass34', 'name' => '&#57394;'),
            array('id_option' => 'fl-outicons fl-outicons-phone14', 'name' => '&#57407;'),
            array('id_option' => 'fl-outicons fl-outicons-photo-camera5', 'name' => '&#57408;'),
            array('id_option' => 'fl-outicons fl-outicons-picture54', 'name' => '&#57409;'),
            array('id_option' => 'fl-outicons fl-outicons-pin12', 'name' => '&#57410;'),
            array('id_option' => 'fl-outicons fl-outicons-play127', 'name' => '&#57411;'),
            array('id_option' => 'fl-outicons fl-outicons-speech bubble34', 'name' => '&#57428;'),
            array('id_option' => 'fl-outicons fl-outicons-speech-balloon2', 'name' => '&#57427;'),
            array('id_option' => 'fl-outicons fl-outicons-truck72', 'name' => '&#57436;'),
            array('id_option' => 'fl-outicons fl-outicons-user189', 'name' => '&#57440;'),
            array('id_option' => 'fl-outicons fl-outicons-treasure', 'name' => '&#57435;'),
            array('id_option' => 'fl-outicons fl-outicons-print', 'name' => '&#57414;'),
            array('id_option' => 'fl-outicons fl-outicons-share53', 'name' => '&#57421;'),
            array('id_option' => 'fl-outicons fl-outicons-pencil9', 'name' => '&#57406;'),
            array('id_option' => 'fl-outicons fl-outicons-heart373', 'name' => '&#57384;'),
            array('id_option' => 'fl-outicons fl-outicons-download194', 'name' => '&#57369;'),
            array('id_option' => 'fl-outicons fl-outicons-gear40', 'name' => '&#57380;'),
            array('id_option' => 'fl-outicons fl-outicons-mail2', 'name' => '&#57396;'),

        );

        return $icons;
    }

    private function getCategoriesList()
    {
        $category = new Category();
        $this->getItemCategories($category->getNestedCategories((int)Configuration::get('PS_HOME_CATEGORY')));
    }

    protected function getItemCategories($categories)
    {
        foreach ($categories as $category) {
            array_push(
                $this->category,
                array(
                    'id' => (int)$category['id_category'],
                    'name' => str_repeat('&nbsp;', '3' * (int)$category['level_depth']).$category['name']
                )
            );

            if (isset($category['children']) && !empty($category['children'])) {
                $this->getItemCategories($category['children']);
            }
        }
    }


    protected function getConfigFormValues()
    {
        if ((bool)Tools::getIsset('updateboncategory') && (int)Tools::getValue('id_item') > 0) {
            $item = new CategoryBon((int)Tools::getValue('id_item'));
        } else {
            $item = new CategoryBon();
        }

        $fields_values = array(
            'id_item' => Tools::getValue('id_item'),
            'id_category' => Tools::getValue('id_category', $item->id_category),
            'sort_order' => Tools::getValue('sort_order', $item->sort_order),
            'icon' => Tools::getValue('icon', $item->icon),
            'type_icon' => Tools::getValue('type_icon', $item->type_icon),
            'status' => Tools::getValue('status', $item->status),
            'switch_icon' => Tools::getValue('switch_icon', $item->switch_icon),
            'content' => Tools::getValue('content', $item->content)
        );

        return $fields_values;
    }

    protected function addCategory()
    {
        if ((int)Tools::getValue('id_item') > 0) {
            $item = new CategoryBon((int)Tools::getValue('id_item'));
        } else {
            $item = new CategoryBon();
        }

        $item->id_category = (int)Tools::getValue('id_category');
        $item->id_shop = (int)$this->context->shop->id;
        $item->icon = pSql(Tools::getValue('icon'));
        $item->type_icon = pSql(Tools::getValue('type_icon'));

        if ((int)Tools::getValue('id_item') > 0) {
            $item->sort_order = Tools::getValue('sort_order');
        } else {
            $item->sort_order = $item->getMaxSortOrder((int)$this->id_shop);
        }

        $item->status = (bool)Tools::getValue('status');
        $item->switch_icon = (bool)Tools::getValue('switch_icon');

        $languages = Language::getLanguages(false);

        foreach ($languages as $language) {
            $item->content[$language['id_lang']] = Tools::getValue('content_'.$language['id_lang']);
            if (Tools::isEmpty(Tools::getValue('content_'.$language['id_lang']))) {
                $item->content[$language['id_lang']] = Tools::getValue('content_'.$this->context->language->id);
            }
        }

        if (!Tools::getValue('id_item')) {
            if (!$item->add()) {
                return $this->displayError($this->l('The item could not be added.'));
            }
        } elseif (!$item->update()) {
            return $this->displayError($this->l('The item could not be updated.'));
        }

        return $this->displayConfirmation($this->l('The item is saved.'));
    }


    protected function deleteCategory()
    {
        $item = new CategoryBon(Tools::getValue('id_item'));
        $res = $item->delete();

        if (!$res) {
            return $this->displayError($this->l('Error occurred when deleting the tab'));
        }

        return $this->displayConfirmation($this->l('The tab is successfully deleted'));
    }

    protected function updateStatusTab($type)
    {
        $item = new CategoryBon(Tools::getValue('id_item'));

        if ($item->$type == 1) {
            $item->$type = 0;
        } else {
            $item->$type = 1;
        }

        if (!$item->update()) {
            return $this->displayError($this->l('The tab status could not be updated.'));
        }

        return $this->displayConfirmation($this->l('The tab status is successfully updated.'));
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
                        'label' => $this->l('Enable'),
                        'name' => 'BON_CATEGORY_ITEM_STATUS',
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
                        'label' => $this->l('Dimming the screen'),
                        'name' => 'BON_CATEGORY_BACKGROUND',
                        'is_bool' => true,
                        'desc' => 'Allows you to dim the screen when you hover over a category.',
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
                        'type' => 'color',
                        'label' => $this->l('Title fill color:'),
                        'name' => 'BON_CATEGORY_HEADER',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Title text color:'),
                        'name' => 'BON_CATEGORY_HEADER_COLOR',
                    ),
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
        $helper->submit_action = 'submitConfiguration';
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(), /* Add values for your inputs */
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

        if (!Validate::isColor(Tools::getValue('BON_CATEGORY_HEADER'))) {
            $errors[] = $this->l('"Title fill color" format error.');
        }
        if (!Validate::isColor(Tools::getValue('BON_CATEGORY_HEADER_COLOR'))) {
            $errors[] = $this->l('"Title text color" format error.');
        }

        if (count($errors)) {
            return $this->displayError(implode('<br />', $errors));
        }

        return false;
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') != $this->name) {
            return;
        }
        Media::addJsDefL('ajax_theme_url', $this->context->link->getAdminLink('AdminAjaxBonCategory'));
        $this->context->smarty->assign('ajax_theme_url', $this->context->link->getAdminLink('AdminAjaxBonCategory'));
        $this->context->controller->addJquery();
        $this->context->controller->addJqueryUI('ui.sortable');
        $this->context->controller->addJS($this->_path.'views/js/boncategory-back.js');
        $this->context->controller->addCSS($this->_path.'views/css/boncategory-back.css');
        $this->context->controller->addCSS($this->_path.'views/css/fl-outicons.css');
    }

    protected function getType($data)
    {
        if (Validate::isInt($data)) {
            return 'int';
        } elseif (Validate::isFloat($data)) {
            return 'float';
        } elseif (Validate::isBool($data)) {
            return 'bool';
        } else {
            return 'string';
        }
    }

    protected function getBlankSettings()
    {
        $settings = $this->getConfigurations();
        $result = array();

        foreach (array_keys($settings) as $name) {
            $data = Configuration::get($name);
            $result[$name] = array('value' => $data, 'type' => $this->getType($data));
        }

        return $result;
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/boncategory-front.css', 'all');
        $this->context->controller->addCSS($this->_path.'views/css/fl-outicons.css', 'all');
        $this->context->controller->addJS($this->_path . '/views/js/boncategory.js');
        $this->context->controller->addJS($this->_path . '/views/js/hoverIntent.js');
        $this->context->controller->addJS($this->_path . '/views/js/superfish.js');
        $this->context->smarty->assign('category_hover', Configuration::get('BON_CATEGORY_BACKGROUND'));
        if (Configuration::get('BON_CATEGORY_ITEM_STATUS')) {
            $this->context->smarty->assign(array(
                'settings' => $this->getBlankSettings(),
                'category_header' => Configuration::get('BON_CATEGORY_HEADER'),
                'category_header_color' => Configuration::get('BON_CATEGORY_HEADER_COLOR'),
            ));
            return $this->display($this->_path, '/views/templates/hook/boncategory-header.tpl');
        }
    }

    public function hookDisplayLeftColumn()
    {
        if (Configuration::get('BON_CATEGORY_ITEM_STATUS')) {
            $data = array();
            $categories = CategoryBon::getFrontCategory();

            if ($categories) {
                foreach ($categories as $key => $category) {
                    $data[$key]['id_item'] = (int)$category['id_item'];
                    $data[$key]['category'] = new Category((int)$category['id_category'], $this->context->language->id);
                    $data[$key]['category_nb'] = $data[$key]['category']
                        ->getProducts($this->context->language->id, 1, 0, null, null, true);
                    $data[$key]['content'] =  $category['content'];
                    $data[$key]['icon'] = $category['icon'];
                    $data[$key]['switch_icon'] = (bool)$category['switch_icon'];
                    $data[$key]['type_icon'] = $category['type_icon'];
                }
            }
            $this->context->smarty->assign('categories', $data);
            $this->context->smarty->assign(
                array(
                    'lang_iso' => 'en',
                    'ps_version' => _PS_VERSION_
                )
            );

            return $this->display($this->_path, '/views/templates/hook/boncategory.tpl');
        }
    }

    public function hookDisplayBoncategory()
    {
        return $this->hookDisplayLeftColumn();
    }

    public function hookActionCategoryDelete($params)
    {
        $data = CategoryBon::getDeletedCategory((int)$params['category']->id_category);

        if ($data) {
            foreach ($data as $item) {
                $this->deleteItem($item);
            }
        }
    }
}
