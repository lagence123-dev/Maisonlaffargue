<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'bonmaintenance/classes/ClassBonmaintenance.php');

class Bonmaintenance extends Module
{
    public function __construct()
    {
        $this->name = 'bonmaintenance';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->author = 'Bonpresta';
        $this->module_key = '78be011ccd1b1760c66aca84ceef53d8';
        $this->author_address = '0xf66a8C20b52eD708FB78F0D347C9e0Bc7c6b3073';
        $this->need_instance = 1;
        $this->bootstrap = true;
        parent::__construct();
        $this->default_language = Language::getLanguage(Configuration::get('PS_LANG_DEFAULT'));
        $this->id_shop = Context::getContext()->shop->id;
        $this->displayName = $this->l('Store Undergoing Maintenance');
        $this->description = $this->l('Display maintenance popup');
        $this->confirmUninstall = $this->l('This module  Uninstall');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }


    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');
        $this->installSamples();

        $settings = $this->getModuleSettings();

        foreach ($settings as $name => $value) {
            Configuration::updateValue($name, $value);
        }

        return parent::install() &&
        $this->registerHook('DisplayBackOfficeHeader') &&
        $this->registerHook('displayBonMaintenance');

    }

	protected function installSamples()
	{
		$now = date('Y-m-d H:i:00');
		$languages = Language::getLanguages(false);
		$item = new ClassBonmaintenance();
		$item->data_start = $now;
		$item->data_end = (new DateTime("+1 month"))->format("Y-m-d H:i:00");
		$item->id_shop = (int)$this->context->shop->id;
        $item->select_type = "image";
        $item->colorhex = "#ffeb3b";
        $item->colorhex2 = "#74efff";
        $item->colorhex3 = "#9c27b0";

		foreach ($languages as $language) {
			$item->title_text[$language['id_lang']] = '<h1>Opening Soon!</h1>';
			$item->description[$language['id_lang']] = '<p>We\'ll back soon. Thank you for your patience.</p>';
			$item->image[$language['id_lang']] = 'sample-1.jpg';
		}
       
		$item->add();
	}

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        $settings = $this->getModuleSettings();

        foreach (array_keys($settings) as $name) {
            Configuration::deleteByName($name);
        }

        return parent::uninstall();
    }

    protected function getModuleSettings()
    {
        $settings = array(
            'BON_MAINTENANCE_TITLE_COLOR' => '#3a3a3a',
            'BON_MAINTENANCE_CONTENT_COLOR' => '#3a3a3a',
            'BON_MAINTENANCE_BACKGROUND_COLOR' => 'transparent',
            'BON_MAINTENANCE_COUNTDOWN_COLOR' => '#3a3a3a',
            'BON_MAINTENANCE_SOCIAL_COLOR' => '#3a3a3a',
            'BON_MAINTENANCE_ADDRESS_COLOR' => '#3a3a3a',
            'BON_MAINTENANCE_CONTENT_OPACITY' => '1',
            'BON_MAINTENANCE_FACEBOOK' => '#',
            'BON_MAINTENANCE_TWITTER' => '#',
            'BON_MAINTENANCE_RSS' => '#',
            'BON_MAINTENANCE_YOUTUBE' => '#',
            'BON_MAINTENANCE_PINTEREST' => '#',
            'BON_MAINTENANCE_VIMEO' => '#',
            'BON_MAINTENANCE_INSTAGRAM' => '#',
            'BON_MAINTENANCE_LINKEDIN' => '#',
        );
        return $settings;
    }

    public function getContent()
    {

        $output = '';
        $result ='';

        if (((bool)Tools::isSubmit('submitBonmaintenanceSettingModule')) == true) {
            if (!$errors = $this->validateSettings()) {
                $this->postProcess();
                $output .= $this->displayConfirmation($this->l('Settings updated successful.'));
            } else {
                $output .= $errors;
            }
        }

        if ((bool)Tools::isSubmit('submitUpdateBonmaintenance')) {
            if (!$result = $this->preValidateForm()) {
                $output .= $this->addBonmaintenance();
            } else {
                $output = $result;
                $output .= $this->renderBonmaintenanceForm();
            }
        }

        if (!$result) {
            $output .= $this->renderBonmaintenanceForm();
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
        $helper->submit_action = 'submitBonmaintenanceSettingModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&module_tab=1';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'image_path' => $this->_path.'views/img/',
            'fields_value' => $this->getConfigFormValuesSettings(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings Links'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'color',
                        'label' => $this->l('Title Color'),
                        'name' => 'BON_MAINTENANCE_TITLE_COLOR',
                        'col' => 3,
                    ), 
                    array(
                        'type' => 'color',
                        'label' => $this->l('Content Color'),
                        'name' => 'BON_MAINTENANCE_CONTENT_COLOR',
                        'col' => 3,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Background Color'),
                        'name' => 'BON_MAINTENANCE_BACKGROUND_COLOR',
                        'col' => 3,
                        'desc' => $this->l(
                            'Background Color For Opacity'
                        ),
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Countdown Color'),
                        'name' => 'BON_MAINTENANCE_COUNTDOWN_COLOR',
                        'col' => 3,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Social Icons Color'),
                        'name' => 'BON_MAINTENANCE_SOCIAL_COLOR',
                        'col' => 3,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Address Color'),
                        'name' => 'BON_MAINTENANCE_ADDRESS_COLOR',
                        'col' => 3,
                    ),    
                    array(
                        'type' => 'text',
                        'label' => $this->l('Background opacity'),
                        'name' => 'BON_MAINTENANCE_CONTENT_OPACITY',
                        'col' => 3,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Facebook URL'),
                        'name' => 'BON_MAINTENANCE_FACEBOOK',
                        'desc' => $this->l('Your Facebook fan page.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Twitter URL'),
                        'name' => 'BON_MAINTENANCE_TWITTER',
                        'desc' => $this->l('Your official Twitter account.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('RSS URL'),
                        'name' => 'BON_MAINTENANCE_RSS',
                        'desc' => $this->l('The RSS feed of your choice (your blog, your store, etc.).'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('YouTube URL'),
                        'name' => 'BON_MAINTENANCE_YOUTUBE',
                        'desc' => $this->l('Your official YouTube account.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Pinterest URL:'),
                        'name' => 'BON_MAINTENANCE_PINTEREST',
                        'desc' => $this->l('Your official Pinterest account.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Vimeo URL:'),
                        'name' => 'BON_MAINTENANCE_VIMEO',
                        'desc' => $this->l('Your official Vimeo account.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Instagram URL:'),
                        'name' => 'BON_MAINTENANCE_INSTAGRAM',
                        'desc' => $this->l('Your official Instagram account.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('LinkedIn URL:'),
                        'name' => 'BON_MAINTENANCE_LINKEDIN',
                        'desc' => $this->l('Your official LinkedIn account.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function validateSettings()
    {
        $errors = array();

        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_TITLE_COLOR')) && !Validate::isColor(Tools::getValue('BON_MAINTENANCE_TITLE_COLOR'))) {
            $errors[] = $this->l('Title Color format error.');
        }

        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_CONTENT_COLOR')) && !Validate::isColor(Tools::getValue('BON_MAINTENANCE_CONTENT_COLOR'))) {
            $errors[] = $this->l('Content Color format error.');
        }

        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_BACKGROUND_COLOR')) && !Validate::isColor(Tools::getValue('BON_MAINTENANCE_BACKGROUND_COLOR'))) {
            $errors[] = $this->l('Background Color format error.');
        }

        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_COUNTDOWN_COLOR')) && !Validate::isColor(Tools::getValue('BON_MAINTENANCE_COUNTDOWN_COLOR'))) {
            $errors[] = $this->l('Countdown Color format error.');
        }

        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_SOCIAL_COLOR')) && !Validate::isColor(Tools::getValue('BON_MAINTENANCE_SOCIAL_COLOR'))) {
            $errors[] = $this->l('Social Icons Color format error.');
        }

        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_ADDRESS_COLOR')) && !Validate::isColor(Tools::getValue('BON_MAINTENANCE_ADDRESS_COLOR'))) {
            $errors[] = $this->l('Address Color format error.');
        }

        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_CONTENT_OPACITY')) && !Validate::isUnsignedInt(Tools::getValue('BON_MAINTENANCE_CONTENT_OPACITY'))) {
            $errors[] = $this->l('Bad background opacity format');
        }

        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_FACEBOOK')) && !Validate::isUrl(Tools::getValue('BON_MAINTENANCE_FACEBOOK'))) {
            $errors[] = $this->l('Bad Facebook URL format');
        }
        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_TWITTER')) && !Validate::isUrl(Tools::getValue('BON_MAINTENANCE_TWITTER'))) {
            $errors[] = $this->l('Bad Twitter URL format');
        }
        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_RSS')) && !Validate::isUrl(Tools::getValue('BON_MAINTENANCE_RSS'))) {
            $errors[] = $this->l('Bad RSS URL format');
        }
        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_YOUTUBE')) && !Validate::isUrl(Tools::getValue('BON_MAINTENANCE_YOUTUBE'))) {
            $errors[] = $this->l('Bad YouTube URL format');
        }
        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_PINTEREST')) && !Validate::isUrl(Tools::getValue('BON_MAINTENANCE_PINTEREST'))) {
            $errors[] = $this->l('Bad Pinterest URL format');
        }
        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_VIMEO')) && !Validate::isUrl(Tools::getValue('BON_MAINTENANCE_VIMEO'))) {
            $errors[] = $this->l('Bad Vimeo URL format');
        }
        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_INSTAGRAM')) && !Validate::isUrl(Tools::getValue('BON_MAINTENANCE_INSTAGRAM'))) {
            $errors[] = $this->l('Bad Instagram URL format');
        }
        if (!Tools::isEmpty(Tools::getValue('BON_MAINTENANCE_LINKEDIN')) && !Validate::isUrl(Tools::getValue('BON_MAINTENANCE_LINKEDIN'))) {
            $errors[] = $this->l('Bad LinkedIn URL format');
        }


        if ($errors) {
            return $this->displayError(implode('<br />', $errors));
        } else {
            return false;
        }
    }

    protected function getConfigFormValuesSettings()
    {
        $filled_settings = array();
        $settings = $this->getModuleSettings();

        foreach (array_keys($settings) as $name) {
            $filled_settings[$name] = Configuration::get($name);
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

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    protected function getBonmaintenanceSettings()
    {
        $settings = $this->getModuleSettings();
        $get_settings = array();
        foreach (array_keys($settings) as $name) {
            $data = Configuration::get($name);
            $get_settings[$name] = array('value' => $data, 'type' => $this->getStringValueType($data));
        }

        return $get_settings;
    }

    protected function renderBonmaintenanceForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('BON_Maintenance Page'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => $this->l('Type:'),
                        'name' => 'select_type',
                        'form_group_class' => 'content_type',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => 'gradient',
                                    'name' => $this->l('Gradient')),
                                array(
                                    'id' => 'image',
                                    'name' => $this->l('Image')),
                                array(
                                    'id' => 'video',
                                    'name' => $this->l('Video')),
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Color 1'),
                        'name' => 'colorhex',
                        'form_group_class' => 'files_color_hex hidden',
                        'col' => 3,
                        'desc' => $this->l('Format Color HEX'),
                    ),  
                    array(
                        'type' => 'color',
                        'label' => $this->l('Color 2'),
                        'name' => 'colorhex2',
                        'form_group_class' => 'files_color_hex hidden',
                        'col' => 3,
                        'desc' => $this->l('Format Color HEX'),
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Color 3'),
                        'name' => 'colorhex3',
                        'form_group_class' => 'files_color_hex hidden',
                        'col' => 3,
                        'desc' => $this->l('Format Color HEX'),
                    ),
                    array(
                        'type' => 'files_lang',
                        'label' => $this->l('Image/Video'),
                        'name' => 'image',
                        'form_group_class' => 'files_image hidden',
                        'lang' => true,
                        'col' => 6,
                        'desc' => $this->l(
                            'If the parallax type image - format file .png, 
                            .jpg, .gif. If the parallax type video - format file .mp4, .webm, .ogv.'
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Title'),
                        'name' => 'title_text',
                        'autoload_rte' => true,
                        'lang' => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Content'),
                        'name' => 'description',
                        'autoload_rte' => true,
                        'lang' => true,
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('Start Date'),
                        'name' => 'data_start',
                        'col' => 6,
                        'required' => true
                    ),
                    array(
                        'type' => 'datetime',
                        'label' => $this->l('End Date'),
                        'name' => 'data_end',
                        'col' => 6,
                        'required' => true
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );


        $tab = new ClassBonmaintenance(1);
        $fields_form['form']['images'] = $tab->image;

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitUpdateBonmaintenance';
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&module_tab=1';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigBonmaintenanceFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'image_baseurl' => $this->_path.'views/img/',
            'id_tab' => 1
        );

        return $helper->generateForm(array($fields_form));
    }

    protected function getConfigBonmaintenanceFormValues()
    {
        $tab = new ClassBonmaintenance(1);

        $fields_values = array(
            'image' => Tools::getValue('image', $tab->image),
            'colorhex' => Tools::getValue('colorhex', $tab->colorhex),
            'colorhex2' => Tools::getValue('colorhex2', $tab->colorhex2),
            'colorhex3' => Tools::getValue('colorhex3', $tab->colorhex3),
            'select_type' => Tools::getValue('select_type', $tab->select_type),
            'data_start' => Tools::getValue('data_start', $tab->data_start),
            'data_end' => Tools::getValue('data_end', $tab->data_end),
            
        );

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $fields_values['title_text'][$lang['id_lang']] = Tools::getValue(
                'title_text_' . (int) $lang['id_lang'],
                isset($tab->title_text[$lang['id_lang']]) ? $tab->title_text[$lang['id_lang']] : ''
            );
            $fields_values['description'][$lang['id_lang']] = Tools::getValue(
                'description_' . (int) $lang['id_lang'],
                isset($tab->description[$lang['id_lang']]) ? $tab->description[$lang['id_lang']] : ''
            );
        }
       
        return $fields_values;
    }

    protected function addBonmaintenance()
    {
        $errors = array();

        $item = new ClassBonmaintenance((int)Tools::getValue('module_tab'));

        $item->id_shop = (int)$this->context->shop->id;
        $item->data_start = Tools::getValue('data_start');
        $item->data_end = Tools::getValue('data_end');
        $item->select_type = Tools::getValue('select_type');
        $item->colorhex = Tools::getValue('colorhex');
        $item->colorhex2 = Tools::getValue('colorhex2');
        $item->colorhex3 = Tools::getValue('colorhex3');
        $languages = Language::getLanguages(false);

        foreach ($languages as $language) {
            $item->title_text[$language['id_lang']] = Tools::getValue('title_text_'.$language['id_lang']);
            $item->description[$language['id_lang']] = Tools::getValue('description_'.$language['id_lang']);
            $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image_'.$language['id_lang']]['name'], '.'), 1));

            if (Tools::getValue('select_type') == 'video') {
                $salt = sha1(microtime());
                if (!move_uploaded_file($_FILES['image_' . $language['id_lang']]['tmp_name'], dirname(__FILE__) .
                    '/views/img/' . $salt . '_' . $_FILES['image_' . $language['id_lang']]['name'])) {
                } else {
                    if (isset($_FILES['image_' . $language['id_lang']]) && isset($_FILES['image_' .
                            $language['id_lang']]['tmp_name']) && !empty($_FILES['image_' .
                            $language['id_lang']]['tmp_name'])) {
                        $item->image[$language['id_lang']] = $salt .
                            '_' . $_FILES['image_' . $language['id_lang']]['name'];
                    } elseif (Tools::getValue('image_old_' . $language['id_lang']) != '') {
                        $item->image[$language['id_lang']] = Tools::getValue('image_old_' . $language['id_lang']);
                    }
                }
            } elseif (Tools::getValue('select_type') == 'image') {
                $imagesize = $_FILES['image_'.$language['id_lang']]["tmp_name"] != '' ? @getimagesize($_FILES['image_'.$language['id_lang']]["tmp_name"]) : '';
                if (isset($_FILES['image_'.$language['id_lang']])
                    && isset($_FILES['image_'.$language['id_lang']]['tmp_name'])
                    && !empty($_FILES['image_'.$language['id_lang']]['tmp_name'])
                    && !empty($imagesize)
                    && in_array(
                        Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)),
                        array('jpg', 'gif', 'jpeg', 'png')
                    )
                    && in_array($type, array('jpg', 'gif', 'jpeg', 'png'))) {
                    $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                    $salt = sha1(microtime());
                    if ($error = ImageManager::validateUpload($_FILES['image_'.$language['id_lang']])) {
                        $errors[] = $error;
                    } elseif (!$temp_name || !move_uploaded_file($_FILES['image_'.
                        $language['id_lang']]['tmp_name'], $temp_name)) {
                        return false;
                    } elseif (!ImageManager::resize($temp_name, dirname(__FILE__).
                        '/views/img/'.$salt.'_'.$_FILES['image_'.
                        $language['id_lang']]['name'], null, null, $type)) {
                        $errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
                    }

                    if (isset($temp_name)) {
                        @unlink($temp_name);
                    }
                    $item->image[$language['id_lang']] = $salt.'_'.$_FILES['image_'.$language['id_lang']]['name'];
                } elseif (Tools::getValue('image_old_'.$language['id_lang']) != '') {
                    $item->image[$language['id_lang']] = Tools::getValue('image_old_'.$language['id_lang']);
                }
            }
        }
        
        if (!$errors) {
            if (!$item->id_tab) {
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
        $errors = array();
        $languages = Language::getLanguages(false);
        $banner = new ClassBonmaintenance((int)Tools::getValue('module_tab'));
         
        $old_image = $banner->image;
        $from = Tools::getValue('data_start');
        $to = Tools::getValue('data_end');

        if (!Tools::isEmpty(Tools::getValue('colorhex')) && !Validate::isColor(Tools::getValue('colorhex'))) {
            $errors[] = $this->l('Color 1 format error.');
        }

        if (!Tools::isEmpty(Tools::getValue('colorhex2')) && !Validate::isColor(Tools::getValue('colorhex2'))) {
            $errors[] = $this->l('Color 2 format error.');
        }

        if (!Tools::isEmpty(Tools::getValue('colorhex3')) && !Validate::isColor(Tools::getValue('colorhex3'))) {
            $errors[] = $this->l('Color 3 format error.');
        }

        if (Tools::isEmpty(Tools::getValue('data_start'))) {
            $errors[] = $this->l('The data start is required.');
        }

        if (Tools::isEmpty(Tools::getValue('data_end'))) {
            $errors[] = $this->l('The data end is required.');
        }

        if (!Validate::isDate($to) || !Validate::isDate($from)) {
            $errors[] = $this->l('Invalid date field');
        } elseif (strtotime($to) <= strtotime($from)) {
            $errors[] = $this->l('Invalid date range');
        }

        if (!$old_image && (!isset($_FILES['image_' . $id_lang_default]) ||
                Tools::isEmpty($_FILES['image_' . $id_lang_default]['tmp_name']))) {
            $errors[] = $this->l('The file is required.');
        }

        if (Tools::getValue('select_type') == 'image') {
            foreach ($languages as $lang) {
                if (!Tools::isEmpty($_FILES['image_' . $lang['id_lang']]['type'])) {
                    if (ImageManager::validateUpload($_FILES['image_' . $lang['id_lang']], 4000000)) {
                        $errors[] = $this->l('Image format not recognized, allowed format is: .gif, .jpg, .png');
                    }
                }
            }
        }

        if (Tools::getValue('select_type') == 'video') {
            $info = new SplFileInfo($_FILES['image_' . $this->default_language['id_lang']]['name']);
            if ($_FILES['image_' . $this->default_language['id_lang']]['name'] != '') {
                if ($info->getExtension() != 'mp4' && $info->getExtension()
                    != 'webm' && $info->getExtension() != 'ogv') {
                    $errors[] = $this->l('Video format not recognized, allowed format is: .mp4, .webm, .ogv');
                }
            }
        }
        
        foreach ($languages as $lang) {
            if (!empty($_FILES['image_' . $lang['id_lang']]['type'])) {
                if (ImageManager::validateUpload($_FILES['image_' . $lang['id_lang']], 4000000)) {
                    $errors[] = $this->l('Image format not recognized, allowed format is: .gif, .jpg, .png');
                }
            }
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
        $this->context->controller->addJquery();
        $this->context->controller->addJS($this->_path . 'views/js/bonmaintenance_back.js');
        $this->context->controller->addCSS($this->_path . 'views/css/bonmaintenance_back.css');
    }

    public function hookdisplayBonMaintenance()
    {
        $maintenance_front = new ClassBonmaintenance();
        $tabs = $maintenance_front->getTopFrontItems($this->id_shop);

        Media::addJsDefL('bon_maintenance_url', $this->_path.'ajax.php');
        
        $result = array();

        foreach ($tabs as $key => $tab) {
            $result[$key]['title_text'] = $tab['title_text'];
            $result[$key]['description'] = $tab['description'];
            $result[$key]['image'] = $tab['image'];
            $result[$key]['data_end'] = $tab['data_end'];
            $result[$key]['colorhex'] = $tab['colorhex'];
            $result[$key]['colorhex2'] = $tab['colorhex2'];
            $result[$key]['colorhex3'] = $tab['colorhex3'];
            $result[$key]['select_type'] = $tab['select_type'];
        }
            
        $this->smarty->assign(array(
            'js' => $this->_path.'views/js/',
            'css' => $this->_path.'views/css/',
            'image_baseurl'=> $this->_path . 'views/img/',
            'items'=> $result,
            'maintenance_title_color' => Configuration::get('BON_MAINTENANCE_TITLE_COLOR'),
            'maintenance_content_color' => Configuration::get('BON_MAINTENANCE_CONTENT_COLOR'),
            'maintenance_background_color' => Configuration::get('BON_MAINTENANCE_BACKGROUND_COLOR'),
            'maintenance_countdown_color' => Configuration::get('BON_MAINTENANCE_COUNTDOWN_COLOR'),
            'maintenance_social_color' => Configuration::get('BON_MAINTENANCE_SOCIAL_COLOR'),
            'maintenance_address_color' => Configuration::get('BON_MAINTENANCE_ADDRESS_COLOR'),
            'maintenance_content_opacity' => Configuration::get('BON_MAINTENANCE_CONTENT_OPACITY'),
            'maintenance_facebook' => Configuration::get('BON_MAINTENANCE_FACEBOOK'),
            'maintenance_twitter' => Configuration::get('BON_MAINTENANCE_TWITTER'),
            'maintenance_rss' => Configuration::get('BON_MAINTENANCE_RSS'),
            'maintenance_youtube' => Configuration::get('BON_MAINTENANCE_YOUTUBE'),
            'maintenance_pinterest' => Configuration::get('BON_MAINTENANCE_PINTEREST'),
            'maintenance_vimeo' => Configuration::get('BON_MAINTENANCE_VIMEO'),
            'maintenance_instagram' => Configuration::get('BON_MAINTENANCE_INSTAGRAM'),
            'maintenance_linkedin' => Configuration::get('BON_MAINTENANCE_LINKEDIN'),
        ));
        return $this->fetch('module:bonmaintenance/views/templates/hook/bonmaintenance.tpl');
    }
}
