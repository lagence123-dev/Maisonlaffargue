<?php
/**
* addcatdesc module main file.
*
* @author    Webproggi <webproggi@gmail.com>
* @copyright Copyright &copy; 2010-2023
* @license   http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*
* @version 0.2
*/
if (!defined('_PS_VERSION_')) {
    exit;
}

class AddCatDesc extends Module
{
    private $_html;
    private $_display;
    private $display_blck_tmp;
    private $display_faq_tmp;
    private $tab_class = 'AdminAddCatDesc';
    private $tab_module = 'addcatdesc';
    public $var_name_prefix = 'add_';

    public function __construct()
    {
        $this->name = 'addcatdesc';
        $this->tab = 'front_office_features';
        $this->version = '1.9.8';
        $this->author = 'Webproggi';
        $this->need_instance = 0;
        $this->module_key = '4092bbdaaa225b5ed112d822bb52af39';
        $this->secure_key = Tools::encrypt($this->name);
        $this->ps_versions_compliancy = ['min' => '1.7.6.0', 'max' => _PS_VERSION_];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Category page module with additional description');
        $this->description = $this->l('Adds additional information to the category page.');
    }

    public function install()
    {
        if (parent::install() && $this->registerHook('displayAddCatDesc') && $this->registerHook('displayHeader') && $this->registerHook('displayBackOfficeCategory') && $this->registerHook('categoryAddition') && $this->registerHook('categoryUpdate')) {
            if (version_compare(_PS_VERSION_, '1.7.7') >= 0) {
                $this->registerHook('displayFooterCategory');
            }

            // add admin controller support
            if (!$this->existsTab($this->tab_class)) {
                if (!$this->addTab($this->tab_class, -1)) {
                    return false;
                }
            }
            // #add admin controller support

            $prestaCharSet = (version_compare(_PS_VERSION_, '1.7.7') >= 0) ? 'utf8mb4' : 'utf8';

            $res = Db::getInstance()->Execute('
    		CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'add_category_text`(
    		`id_text` int(10) unsigned NOT NULL auto_increment,
    		`id_shop` int(10) unsigned NOT NULL,
    		`id_category` int(10) unsigned NOT NULL,
            `active` tinyint(1) NOT NULL default 1,
    		PRIMARY KEY (`id_text`, `id_category`))
    		ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=' . $prestaCharSet);

            if ($res) {
                $res &= Db::getInstance()->Execute('
    			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'add_category_text_lang`(
    			`id_text` int(10) unsigned NOT NULL,
    			`id_lang` int(10) unsigned NOT NULL,
    			`content` LONGTEXT,
    			PRIMARY KEY (`id_text`, `id_lang`)
    			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=' . $prestaCharSet);
            }

            if ($res) {
                $res &= $this->installFaqTables();
            }

            if ($res) {
                $prestaBackGrndColor = (version_compare(_PS_VERSION_, '1.7.8') >= 0) ? '#f6f6f6' : '#f1f1f1';
                $res &= Configuration::updateGlobalValue('ADD_CATDESC_SET', 0);
                $res &= Configuration::updateValue('ADD_CATDESC_NCF', 100);
                $res &= Configuration::updateValue('ADD_CATDESC_CUTB', 0);
                $res &= Configuration::updateValue('ADD_CATDESC_CUTE', 0);
                $res &= Configuration::updateValue('ADD_CATDESC_PN', 0);
                $res &= Configuration::updateValue('ADD_CATDESC_PC', $prestaBackGrndColor);
                $res &= Configuration::updateValue('ADD_CATDESC_SFP', 0);
                $res &= Configuration::updateValue('ADD_CATDESC_FAQ_SFP', 0);
                $res &= Configuration::updateValue('ADD_CATDESC_SSC', 0);
                $res &= Configuration::updateValue('ADD_CATDESC_PH', 0);
                $res &= Configuration::updateValue('ADD_CATDESC_PP', 0);
            }

            if (!$res) {
                $res &= $this->uninstall();
            }

            return (bool) $res;
        }

        return false;
    }

    public function installFaqTables()
    {
        $prestaCharSet = (version_compare(_PS_VERSION_, '1.7.7') >= 0) ? 'utf8mb4' : 'utf8';

        $res = Db::getInstance()->Execute('
    			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'add_category_faqblock_tmp`(
    			`id_blck` int(10) unsigned NOT NULL auto_increment,
    			`title` TEXT,
                `order` int(11) NOT NULL default 0,
                `active` tinyint(1) NOT NULL default 1,
    			PRIMARY KEY (`id_blck`)
    			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=' . $prestaCharSet);

        if ($res) {
            $res &= Db::getInstance()->Execute('
    			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'add_category_faq`(
    			`id_faq` int(10) unsigned NOT NULL auto_increment,
    			`id_blck` int(10) unsigned NOT NULL,
                `id_text` int(10) unsigned NOT NULL,
                `order` int(11) NOT NULL default 0,
                `active` tinyint(1) NOT NULL default 1,
                `linked_tmp` int(10) unsigned NOT NULL default 0,
    			PRIMARY KEY (`id_faq`)
    			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=' . $prestaCharSet);
        }

        if ($res) {
            $res &= Db::getInstance()->Execute('
    			CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'add_category_faq_lang`(
    			`id_faq` int(10) unsigned NOT NULL,
    			`id_lang` int(10) unsigned NOT NULL,
    			`question` TEXT,
			    `answer` LONGTEXT,
    			PRIMARY KEY (`id_faq`, `id_lang`)
    			) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=' . $prestaCharSet);
        }

        return (bool) $res;
    }

    public function uninstall()
    {
        $res = Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'add_category_text`');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'add_category_text_lang`');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'add_category_faqblock_tmp`');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'add_category_faq`');
        $res &= Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'add_category_faq_lang`');
        $res &= Configuration::deleteByName('ADD_CATDESC_SET');
        $res &= Configuration::deleteByName('ADD_CATDESC_NCF');
        $res &= Configuration::deleteByName('ADD_CATDESC_CUTB');
        $res &= Configuration::deleteByName('ADD_CATDESC_CUTE');
        $res &= Configuration::deleteByName('ADD_CATDESC_PN');
        $res &= Configuration::deleteByName('ADD_CATDESC_PC');
        $res &= Configuration::deleteByName('ADD_CATDESC_SFP');
        $res &= Configuration::deleteByName('ADD_CATDESC_FAQ_SFP');
        $res &= Configuration::deleteByName('ADD_CATDESC_SSC');
        $res &= Configuration::deleteByName('ADD_CATDESC_SFLD');
        $res &= Configuration::deleteByName('ADD_CATDESC_PH');
        $res &= Configuration::deleteByName('ADD_CATDESC_PP');
        $res &= Configuration::deleteByName('ADD_CATDESC_LANGL');

        if (!$res || !parent::uninstall()) {
            return false;
        }

        return true;
    }

    public function existsTab($tab_class)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_tab AS id
        FROM `' . _DB_PREFIX_ . 'tab` t WHERE LOWER(t.`class_name`) = \'' . pSQL($tab_class) . '\'');
        if (count($result) == 0) {
            return false;
        }

        return true;
    }

    private function addTab($tab_class, $id_parent)
    {
        $tab = new Tab();
        $tab->class_name = $tab_class;
        $tab->id_parent = $id_parent;
        $tab->module = $this->tab_module;
        $tab->name[(int) Configuration::get('PS_LANG_DEFAULT')] = $this->l('Ajax tab AddDescCat');
        $tab->add();

        return true;
    }

    private function removeTab($tab_class)
    {
        $id_tab = Tab::getIdFromClassName($tab_class);
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $tab->delete();

            return true;
        }

        return true;
    }

    private function initForm()
    {
        $helper = new HelperForm();

        $helper->module = $this;
        $helper->name_controller = 'addcatdesc';
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->languages = $this->context->controller->_languages;
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $this->context->controller->default_form_language;
        $helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
        $helper->toolbar_scroll = true;

        $helper->toolbar_btn = $this->initToolbar();

        return $helper;
    }

    public function getContent()
    {
        $this->_html = '';
        $this->_postProcess();
        if ((int) Configuration::get('ADD_CATDESC_NCF') == 0) {
            Configuration::updateValue('ADD_CATDESC_NCF', 100);
        }

        if (Tools::isSubmit('addCategoryText') || Tools::isSubmit('editCategoryText')) {
            $this->displayAddForm();
        } elseif (Tools::isSubmit('addTmpBlock') || Tools::isSubmit('editTmpBlock')) {
            $this->displayAddFaqBlockForm();
        } elseif (Tools::isSubmit('addFaq') || Tools::isSubmit('editFaq')) {
            $this->displayAddFaqForm();
        } else {
            $this->displayForm();
        }

        return $this->_html;
    }

    private function _postProcess()
    {
        $currentIndex = AdminController::$currentIndex;
        $id_shop = (int) $this->context->shop->id;

        if (Tools::isSubmit('objFilterSubmit')) {
            $filter_column_name = Tools::getValue('filter_column_name');
            if (empty($filter_column_name)) {
                $this->_html .= $this->displayError($this->l('Search field has to be filled'));
            } else {
                $filter_column_name = trim(Tools::getValue('filter_column_name'));
                $filter_column_name = Tools::substr($filter_column_name, 0, 50);
                Configuration::updateValue('ADD_CATDESC_SFLD', $filter_column_name);
                $this->_html .= $this->displayConfirmation($this->l('Filter was set.'));
            }
        } elseif (Tools::isSubmit('objCancelSubmit')) {
            Configuration::updateValue('ADD_CATDESC_SFLD', '');
            $this->_html .= $this->displayConfirmation($this->l('Filter was canceled.'));
        }

        if (Tools::isSubmit('langSubmit')) {
            Configuration::updateValue('ADD_CATDESC_LANGL', (int) Tools::getValue('lang_arr_sel'));
        } elseif (Tools::isSubmit('langSubmitTmp')) {
            Configuration::updateValue('ADD_CATDESC_LANGL', (int) Tools::getValue('lang_arr_sel_tmp'));
        } elseif (Tools::isSubmit('langSubmitFaq')) {
            Configuration::updateValue('ADD_CATDESC_LANGL', (int) Tools::getValue('lang_arr_sel_faq'));
            $id_blck = (int) Tools::getValue('id_blck');
            $id_text = (int) Tools::getValue('id_text');

            if ((int) Tools::getValue('id_blck') > 0) {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&editTmpBlock&id_blck=' . (int) $id_blck);
            } else {
                $res = $this->getCategoryFormParams($id_text, (int) Tools::getValue('lang_arr_sel_faq'));
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&editCategoryText&id_cat=' . (int) $res[0]['id_category'] . '&id_shop_cat=' . (int) $res[0]['id_shop']);
            }
        }

        if (Tools::isSubmit('submitSaveCategory') || Tools::isSubmit('submitSaveCategoryStay')) {
            // add
            Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_text` (`id_category`, `id_shop`) 
				VALUES(' . (int) Tools::getValue('id_cat') . ', ' . (int) $id_shop . ')');

            $id_text = Db::getInstance()->Insert_ID();
            $languages = Language::getLanguages(false);

            foreach ($languages as $language) {
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_text_lang` (`id_text`, `id_lang`, `content`) VALUES(' . (int) $id_text . ', ' . (int) $language['id_lang'] . ', "' . pSQL(Tools::getValue('content_' . $language['id_lang']), true) . '")');
            }

            // multishop and saving to all categories - enabled
            $this->saveCatShops();

            if (Tools::isSubmit('submitSaveCategory')) {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&addBottomCategoryText');
            } else {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&addBottomCategoryText&editCategoryText&id_cat=' . (int) Tools::getValue('id_cat') . '&id_shop_cat=' . (int) $id_shop);
            }
        } elseif (Tools::isSubmit('submitEditCategory') || Tools::isSubmit('submitEditCategoryStay')) {
            // edit
            $languages = Language::getLanguages(false);
            $id_text = (int) Tools::getValue('id_text');

            Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'add_category_text`
				SET `id_category` = ' . (int) Tools::getValue('id_cat') . ' 
				WHERE `id_text` = ' . (int) $id_text);

            // clear data before update
            Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'add_category_text_lang`	WHERE `id_text` = ' . (int) $id_text);

            foreach ($languages as $language) {
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_text_lang` (`id_text`, `id_lang`, `content`) VALUES(' . (int) $id_text . ', ' . (int) $language['id_lang'] . ', "' . pSQL(Tools::getValue('content_' . $language['id_lang']), true) . '")');
            }

            // tie to faq templates
            $cnt_template = $this->getCntBlockFaqTmp();
            if (isset($cnt_template[0]['cnt']) && $cnt_template[0]['cnt'] > 0) {
                // delete links
                Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq` WHERE `id_text` = ' . (int) $id_text . ' AND `linked_tmp` = 1');
                // insert new links
                $sel_template = Tools::getValue('sel_template');
                foreach ($sel_template as $id_blck) {
                    Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_faq` (`id_blck`, `id_text`, `linked_tmp`) VALUES(' . (int) $id_blck . ', ' . (int) $id_text . ', 1)');
                }
            }
            // #tie to faq templates

            if (Tools::isSubmit('submitEditCategory')) {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&editBottomCategoryText');
            } else {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&editBottomCategoryText&editCategoryText&id_cat=' . (int) Tools::getValue('id_cat') . '&id_shop_cat=' . (int) Tools::getValue('id_shop_cat'));
            }
        } elseif (Tools::isSubmit('submitAddFaqBlock') || Tools::isSubmit('submitAddFaqBlockStay')) {
            $faq_title = Tools::getValue('faq_title');
            if (empty($faq_title)) {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&addTmpBlock&addFaqBlockEmptyTitle');
            } else {
                // add title
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` (`title`) VALUES("' . pSQL($faq_title) . '")');
                $id_blck = Db::getInstance()->Insert_ID();

                if (Tools::isSubmit('submitAddFaqBlockStay')) {
                    Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&editTmpBlock&id_blck=' . (int) $id_blck . '&addFaqBlockSuccess');
                } else {
                    $this->_html .= $this->displayConfirmation($this->l('Template created successfully.'));
                }
            }
        } elseif (Tools::isSubmit('submitEditFaqBlock') || Tools::isSubmit('submitEditFaqBlockStay')) {
            $faq_title = Tools::getValue('faq_title');
            if (empty($faq_title)) {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&editTmpBlock&id_blck=' . Tools::getValue('id_blck') . '&addFaqBlockEmptyTitle');
            } else {
                // edit title
                Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` SET `title` = "' . pSQL($faq_title) . '" WHERE `id_blck` = ' . (int) Tools::getValue('id_blck'));

                if (Tools::isSubmit('submitEditFaqBlockStay')) {
                    Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&editTmpBlock&id_blck=' . (int) Tools::getValue('id_blck') . '&editFaqBlockSuccess');
                } else {
                    $this->_html .= $this->displayConfirmation($this->l('Template edited successfully.'));
                }
            }
        } elseif (Tools::isSubmit('submitAddFaqTmp') || Tools::isSubmit('submitEditFaqTmp')) {
            $id_blck = (int) Tools::getValue('id_blck');
            // add faq to template
            if ($id_blck == 0) {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&emptyTmpBlock');
            }

            $languages = Language::getLanguages(false);
            if (Tools::isSubmit('submitEditFaqTmp')) {
                foreach ($languages as $language) {
                    Db::getInstance()->Execute('
    					UPDATE `' . _DB_PREFIX_ . 'add_category_faq_lang` 
    					SET `question` = "' . pSQL(Tools::getValue('question_' . (int) $language['id_lang']), true) . '", `answer` = "' . pSQL(Tools::getValue('answer_' . (int) $language['id_lang']), true) . '"
    					WHERE `id_faq` = ' . (int) Tools::getValue('id_faq') . ' 
    					AND `id_lang`= ' . (int) $language['id_lang']);
                }
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&editFaqTmp&editTmpBlock&id_blck=' . (int) $id_blck);
            } else {
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_faq` (`id_blck`, `id_text`) VALUES(' . (int) $id_blck . ', 0)');
                $id_faq = Db::getInstance()->Insert_ID();

                foreach ($languages as $language) {
                    Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_faq_lang` (`id_faq`, `id_lang`, `question`, `answer`) VALUES(' . (int) $id_faq . ', ' . (int) $language['id_lang'] . ', "' . pSQL(Tools::getValue('question_' . $language['id_lang']), true) . '", "' . pSQL(Tools::getValue('answer_' . $language['id_lang']), true) . '")');
                }
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&addFaqTmp&editTmpBlock&id_blck=' . (int) $id_blck);
            }
        } elseif (Tools::isSubmit('submitAddFaqCat') || Tools::isSubmit('submitEditFaqCat')) {
            $id_text = (int) Tools::getValue('id_text');
            $id_cat = (int) Tools::getValue('id_cat');
            $id_shop_cat = (int) Tools::getValue('id_shop_cat');
            // add faq to category
            if ($id_text == 0 || $id_cat == 0 || $id_shop_cat == 0) {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&emptyFaqCat');
            }

            $languages = Language::getLanguages(false);

            if (Tools::isSubmit('submitAddFaqCat')) {
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_faq` (`id_blck`, `id_text`) VALUES(0, ' . (int) $id_text . ')');
                $id_faq = Db::getInstance()->Insert_ID();

                foreach ($languages as $language) {
                    Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_faq_lang` (`id_faq`, `id_lang`, `question`, `answer`) VALUES(' . (int) $id_faq . ', ' . (int) $language['id_lang'] . ', "' . pSQL(Tools::getValue('question_' . $language['id_lang']), true) . '", "' . pSQL(Tools::getValue('answer_' . $language['id_lang']), true) . '")');
                }

                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&editCategoryText&addFaqCat&id_cat=' . (int) $id_cat . '&id_shop_cat=' . (int) $id_shop_cat);
            } else {
                foreach ($languages as $language) {
                    Db::getInstance()->Execute('
    					UPDATE `' . _DB_PREFIX_ . 'add_category_faq_lang` 
    					SET `question` = "' . pSQL(Tools::getValue('question_' . (int) $language['id_lang']), true) . '", `answer` = "' . pSQL(Tools::getValue('answer_' . (int) $language['id_lang']), true) . '"
    					WHERE `id_faq` = ' . (int) Tools::getValue('id_faq') . ' 
    					AND `id_lang`= ' . (int) $language['id_lang']);
                }
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&editCategoryText&editFaqTmp&id_cat=' . (int) $id_cat . '&id_shop_cat=' . (int) $id_shop_cat);
            }
        } elseif (Tools::getValue('deleteboxhidden') == 1) {
            $arr_desc = Tools::getValue('descBox');
            if (is_array($arr_desc) && count($arr_desc) > 0) {
                foreach ($arr_desc as $id_text) {
                    Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'add_category_text` WHERE `id_text` = ' . (int) $id_text);

                    Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'add_category_text_lang`	WHERE `id_text` = ' . (int) $id_text);

                    // delete faq links
                    $sql_query = 'SELECT b.`id_faq` FROM `' . _DB_PREFIX_ . 'add_category_faq` b WHERE b.`linked_tmp` != 1 AND b.`id_text` = ' . (int) $id_text;
                    $res = Db::getInstance()->executeS($sql_query);

                    if (isset($res) && count($res) > 0) {
                        foreach ($res as $key => $faq) {
                            Db::getInstance()->Execute('
                            DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq_lang` 
                            WHERE `id_faq` = ' . (int) $faq['id_faq']);
                        }
                    }

                    Db::getInstance()->Execute('
        				DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq` 
        				WHERE `id_text` = ' . (int) $id_text);
                }
                $this->_html .= $this->displayConfirmation($this->l('Deletion successful'));
            }
        } elseif (Tools::getValue('activeboxhidden') == 1 || Tools::getValue('activeboxhidden') == 2) {
            $arr_desc = Tools::getValue('descBox');
            if (is_array($arr_desc) && count($arr_desc) > 0) {
                foreach ($arr_desc as $id_text) {
                    Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'add_category_text` SET `active` = ' . (Tools::getValue('activeboxhidden') == 1 ? 1 : 0) . ' WHERE `id_text` = ' . (int) $id_text);
                }
                $this->_html .= $this->displayConfirmation($this->l('Status changed.'));
            }
        } elseif (Tools::isSubmit('changeStatus') && Tools::getValue('id_text')) {
            $status = (Tools::getValue('act') == 'true' ? 0 : 1);
            Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'add_category_text` SET `active` = ' . (int) $status . ' WHERE `id_text` = ' . (int) Tools::getValue('id_text'));
            $this->_html .= $this->displayConfirmation($this->l('Status changed.'));
        } elseif (Tools::isSubmit('deleteCategoryText') && Tools::getValue('id_text')) {
            // delete
            Db::getInstance()->Execute('
				DELETE FROM `' . _DB_PREFIX_ . 'add_category_text` 
				WHERE `id_text` = ' . (int) Tools::getValue('id_text'));

            Db::getInstance()->Execute('
				DELETE FROM `' . _DB_PREFIX_ . 'add_category_text_lang` 
				WHERE `id_text` = ' . (int) Tools::getValue('id_text'));

            // delete faq links
            $sql_query = 'SELECT b.`id_faq` FROM `' . _DB_PREFIX_ . 'add_category_faq` b WHERE b.`linked_tmp` != 1 AND b.`id_text` = ' . (int) Tools::getValue('id_text');
            $res = Db::getInstance()->executeS($sql_query);

            if (isset($res) && count($res) > 0) {
                foreach ($res as $key => $faq) {
                    Db::getInstance()->Execute('
                    DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq_lang` 
                    WHERE `id_faq` = ' . (int) $faq['id_faq']);
                }
            }

            Db::getInstance()->Execute('
				DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq` 
				WHERE `id_text` = ' . (int) Tools::getValue('id_text'));

            Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&deleteBottomCategoryText');
        } elseif (Tools::isSubmit('deleteTmpBlock') && Tools::getValue('id_blck')) {
            $id_blck = (int) Tools::getValue('id_blck');
            // delete template with faq
            Db::getInstance()->Execute('
				DELETE FROM `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` 
				WHERE `id_blck` = ' . (int) $id_blck);

            $sql_query = 'SELECT b.`id_faq` FROM `' . _DB_PREFIX_ . 'add_category_faq` b WHERE b.`id_blck` = ' . (int) $id_blck;
            $res = Db::getInstance()->executeS($sql_query);

            if (isset($res) && count($res) > 0) {
                foreach ($res as $key => $faq) {
                    Db::getInstance()->Execute('
                    DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq_lang` 
                    WHERE `id_faq` = ' . (int) $faq['id_faq']);
                }
            }

            Db::getInstance()->Execute('
				DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq` 
				WHERE `id_blck` = ' . (int) $id_blck);

            Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&deleteBottomCategoryText');
        } elseif (Tools::getValue('deleteboxblckhidden') == 1) {
            $arr_blck = Tools::getValue('descBoxBlck');
            if (is_array($arr_blck) && count($arr_blck) > 0) {
                foreach ($arr_blck as $id_blck) {
                    Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` WHERE `id_blck` = ' . (int) $id_blck);

                    $sql_query = 'SELECT b.`id_faq` FROM `' . _DB_PREFIX_ . 'add_category_faq` b WHERE b.`id_blck` = ' . (int) $id_blck;
                    $res = Db::getInstance()->executeS($sql_query);

                    if (isset($res) && count($res) > 0) {
                        foreach ($res as $key => $faq) {
                            Db::getInstance()->Execute('
                            DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq_lang` 
                            WHERE `id_faq` = ' . (int) $faq['id_faq']);
                        }
                    }

                    Db::getInstance()->Execute('
        				DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq` 
        				WHERE `id_blck` = ' . (int) $id_blck);
                }
                $this->_html .= $this->displayConfirmation($this->l('Deletion successful'));
            }
        } elseif (Tools::getValue('activeboxblckhidden') == 1 || Tools::getValue('activeboxblckhidden') == 2) {
            $arr_blck = Tools::getValue('descBoxBlck');
            if (is_array($arr_blck) && count($arr_blck) > 0) {
                foreach ($arr_blck as $id_blck) {
                    Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` SET `active` = ' . (Tools::getValue('activeboxblckhidden') == 1 ? 1 : 0) . ' WHERE `id_blck` = ' . (int) $id_blck);
                }
                $this->_html .= $this->displayConfirmation($this->l('Status changed.'));
            }
        } elseif (Tools::isSubmit('changeStatusBlock') && Tools::getValue('id_blck')) {
            $status = (Tools::getValue('act') == 'true' ? 0 : 1);
            Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` SET `active` = ' . (int) $status . ' WHERE `id_blck` = ' . (int) Tools::getValue('id_blck'));
            $this->_html .= $this->displayConfirmation($this->l('Status changed.'));
        } elseif (Tools::isSubmit('deleteFaq') && Tools::getValue('id_faq')) {
            $id_faq = (int) Tools::getValue('id_faq');
            $id_blck = (int) Tools::getValue('id_blck');
            $id_text = (int) Tools::getValue('id_text');

            Db::getInstance()->Execute('
				DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq` 
				WHERE `id_faq` = ' . (int) $id_faq);

            Db::getInstance()->Execute('
				DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq_lang` 
				WHERE `id_faq` = ' . (int) $id_faq);

            if ($id_blck > 0) {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&deleteBottomCategoryText&editTmpBlock&id_blck=' . (int) $id_blck);
            } else {
                $id_lang = (int) $this->context->language->id;
                $res = $this->getCategoryFormParams($id_text, $id_lang);
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&deleteBottomCategoryText&editCategoryText&id_cat=' . (int) $res[0]['id_category'] . '&id_shop_cat=' . (int) $res[0]['id_shop']);
            }
        } elseif (Tools::getValue('deleteboxfaqhidden') == 1) {
            $arr_faq = Tools::getValue('descBoxFaq');
            $id_blck = (int) Tools::getValue('id_blck');
            $id_text = (int) Tools::getValue('id_text');

            if (is_array($arr_faq) && count($arr_faq) > 0) {
                foreach ($arr_faq as $id_faq) {
                    Db::getInstance()->Execute('
        				DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq` 
        				WHERE `id_faq` = ' . (int) $id_faq);

                    Db::getInstance()->Execute('
        				DELETE FROM `' . _DB_PREFIX_ . 'add_category_faq_lang` 
        				WHERE `id_faq` = ' . (int) $id_faq);
                }
            }

            if ($id_blck > 0) {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&deleteBottomCategoryText&editTmpBlock&id_blck=' . (int) $id_blck);
            } else {
                $id_lang = (int) $this->context->language->id;
                $res = $this->getCategoryFormParams($id_text, $id_lang);
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&deleteBottomCategoryText&editCategoryText&id_cat=' . (int) $res[0]['id_category'] . '&id_shop_cat=' . (int) $res[0]['id_shop']);
            }
        } elseif (Tools::isSubmit('changeStatusFaq') && Tools::getValue('id_faq') && ((int) Tools::getValue('id_blck') > 0 || (int) Tools::getValue('id_text') > 0)) {
            $id_faq = (int) Tools::getValue('id_faq');
            $id_blck = (int) Tools::getValue('id_blck');
            $id_text = (int) Tools::getValue('id_text');

            $status = (Tools::getValue('act') == 'true' ? 0 : 1);
            Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'add_category_faq` SET `active` = ' . (int) $status . ' WHERE `id_faq` = ' . (int) $id_faq);

            if ($id_blck > 0) {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&statusChanged&editTmpBlock&id_blck=' . (int) $id_blck);
            } else {
                $id_lang = (int) $this->context->language->id;
                $res = $this->getCategoryFormParams($id_text, $id_lang);
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&statusChanged&&editCategoryText&id_cat=' . (int) $res[0]['id_category'] . '&id_shop_cat=' . (int) $res[0]['id_shop']);
            }
        } elseif (Tools::getValue('activeboxfaqhidden') == 1 || Tools::getValue('activeboxfaqhidden') == 2) {
            $arr_faq = Tools::getValue('descBoxFaq');
            $id_blck = (int) Tools::getValue('id_blck');
            $id_text = (int) Tools::getValue('id_text');

            if (is_array($arr_faq) && count($arr_faq) > 0) {
                foreach ($arr_faq as $id_faq) {
                    Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'add_category_faq` SET `active` = ' . (Tools::getValue('activeboxfaqhidden') == 1 ? 1 : 0) . ' WHERE `id_faq` = ' . (int) $id_faq);
                }
            }
            if ($id_blck > 0) {
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&statusChanged&editTmpBlock&id_blck=' . (int) $id_blck);
            } else {
                $id_lang = (int) $this->context->language->id;
                $res = $this->getCategoryFormParams($id_text, $id_lang);
                Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&statusChanged&&editCategoryText&id_cat=' . (int) $res[0]['id_category'] . '&id_shop_cat=' . (int) $res[0]['id_shop']);
            }
        } elseif (Tools::getValue('action') == 'updatePositions') {
            $this->updatePositions();
        } elseif (Tools::isSubmit('submitSaveNum')) {
            if (!Validate::isInt(Tools::getValue('num_cat_field')) || !Validate::isInt(Tools::getValue('cutb')) || !Validate::isInt(Tools::getValue('cute')) || !Validate::isInt(Tools::getValue('partial_number')) || !Validate::isInt(Tools::getValue('partial_height'))) {
                $this->_html .= $this->displayError($this->l('Numbers have to be integer'));
            } else {
                Configuration::updateValue('ADD_CATDESC_NCF', (int) Tools::getValue('num_cat_field'));
                Configuration::updateValue('ADD_CATDESC_CUTB', (int) Tools::getValue('cutb'));
                Configuration::updateValue('ADD_CATDESC_CUTE', (int) Tools::getValue('cute'));
                Configuration::updateValue('ADD_CATDESC_PN', (int) Tools::getValue('partial_number'));
                Configuration::updateValue('ADD_CATDESC_PH', (int) Tools::getValue('partial_height'));
                Configuration::updateValue('ADD_CATDESC_PP', Tools::getValue('partial_position'));
                if ((int) Configuration::get('ADD_CATDESC_PN') > 0 && (int) Tools::getValue('partial_number') > 0) {
                    if (!Validate::isColor(Tools::getValue('partial_color'))) {
                        $this->_html .= $this->displayError($this->l('Color has to be in color format'));
                    } else {
                        Configuration::updateValue('ADD_CATDESC_PC', Tools::getValue('partial_color'));
                    }
                }
                Configuration::updateValue('ADD_CATDESC_SFP', (int) Tools::getValue('show_fp'));
                Configuration::updateValue('ADD_CATDESC_FAQ_SFP', (int) Tools::getValue('show_faq_fp'));
                Configuration::updateValue('ADD_CATDESC_SSC', (int) Tools::getValue('show_sc'));
                $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
            }
        } elseif (Tools::isSubmit('addBottomCategoryText')) {
            $this->_html = $this->displayConfirmation($this->l('Category text added'));
        } elseif (Tools::isSubmit('editBottomCategoryText')) {
            $this->_html = $this->displayConfirmation($this->l('Category text edited'));
        } elseif (Tools::isSubmit('deleteBottomCategoryText')) {
            $this->_html .= $this->displayConfirmation($this->l('Deletion successful'));
        } elseif (Tools::isSubmit('addFaqBlockEmptyTitle')) {
            $this->_html .= $this->displayError($this->l('Template title have to be filled'));
        } elseif (Tools::isSubmit('addFaqBlockSuccess')) {
            $this->_html .= $this->displayConfirmation($this->l('Template created successfully. Add new FAQ.'));
        } elseif (Tools::isSubmit('editFaqBlockSuccess')) {
            $this->_html .= $this->displayConfirmation($this->l('Template edited.'));
        } elseif (Tools::isSubmit('emptyTmpBlock')) {
            $this->_html .= $this->displayError($this->l('Can not add FAQ. The template is empty.'));
        } elseif (Tools::isSubmit('emptyFaqCat')) {
            $this->_html .= $this->displayError($this->l('Can not add FAQ. The category is empty.'));
        } elseif (Tools::isSubmit('addFaqTmp')) {
            $this->_html .= $this->displayConfirmation($this->l('New FAQ added to template.'));
        } elseif (Tools::isSubmit('editFaqTmp')) {
            $this->_html .= $this->displayConfirmation($this->l('FAQ edited'));
        } elseif (Tools::isSubmit('addFaqCat')) {
            $this->_html .= $this->displayConfirmation($this->l('New FAQ added to category.'));
        } elseif (Tools::isSubmit('statusChanged')) {
            $this->_html .= $this->displayConfirmation($this->l('Status changed.'));
        }
    }

    protected function updatePositions()
    {
        if (Tools::getValue('faq_tmp_list') || Tools::getValue('faq_text_list')) {
            if (count(Tools::getValue('faq_text_list')) > 1) {
                $positions = Tools::getValue('faq_text_list');
                $and_el = ' AND `id_text` = ';
            } else {
                $positions = Tools::getValue('faq_tmp_list');
                $and_el = ' AND `id_blck` = ';
            }

            $pos_new = 0;

            foreach ($positions as $position => $value) {
                $pos = explode('_', $value);
                if (isset($pos[1])) {
                    $pos_new = $position + 1;
                    $query = 'UPDATE `' . _DB_PREFIX_ . 'add_category_faq` SET `order` = ' . (int) $pos_new . ' WHERE `id_faq` = ' . (int) $pos[1] . $and_el . (int) $pos[2];

                    Db::getInstance()->execute($query);
                }
            }
        }
    }

    protected function getListContent($id_lang, $start, $end, $where)
    {
        $this->checkFaqTables();
        $id_shop = (int) $this->context->shop->id;
        $id_shop_group = (int) Shop::getContextShopGroupID();
        $where_shop = '';
        if ($id_shop_group != 0) {
            $where_shop = 'AND p.`id_shop` = ' . (int) $id_shop;
        }

        $sql_query = 'SELECT p.`id_text`, p.`active`, cl.`name`, cl.`link_rewrite`, pl.`content`, m.`id_category`, p.`id_shop`, ps.`name` as shop_name FROM `' . _DB_PREFIX_ . 'add_category_text` p LEFT JOIN `' . _DB_PREFIX_ . 'add_category_text_lang` pl on (p.`id_text` = pl.`id_text`) LEFT JOIN `' . _DB_PREFIX_ . 'category` m on (p.`id_category` = m.`id_category`) LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl on (m.`id_category` = cl.`id_category`) LEFT JOIN `' . _DB_PREFIX_ . 'shop` ps on (p.`id_shop` = ps.`id_shop`) WHERE cl.`id_lang` = ' . (int) $id_lang . ' ' . $where_shop . ' ' . $where . ' AND pl.`id_lang` = ' . (int) $id_lang . ' ' . Shop::addSqlRestrictionOnLang('cl');
        if (Tools::getValue('orderIdText')) {
            if (Tools::getValue('orderIdText') == 'ASC') {
                $sql_query .= ' ORDER BY p.`id_text` ASC';
            } elseif (Tools::getValue('orderIdText') == 'DESC') {
                $sql_query .= ' ORDER BY p.`id_text` DESC';
            }
        }
        if (Tools::getValue('orderIdCat')) {
            if (Tools::getValue('orderIdCat') == 'ASC') {
                $sql_query .= ' ORDER BY m.`id_category` ASC';
            } elseif (Tools::getValue('orderIdCat') == 'DESC') {
                $sql_query .= ' ORDER BY m.`id_category` DESC';
            }
        }
        if (Tools::getValue('orderCatName')) {
            if (Tools::getValue('orderCatName') == 'ASC') {
                $sql_query .= ' ORDER BY cl.`name` ASC';
            } elseif (Tools::getValue('orderCatName') == 'DESC') {
                $sql_query .= ' ORDER BY cl.`name` DESC';
            }
        }

        $sql_query .= ' LIMIT ' . (int) $start . ', ' . (int) $end;

        $res = Db::getInstance()->executeS($sql_query);

        if (isset($res) && count($res) > 0) {
            foreach ($res as $key => $cat) {
                $res[$key]['link'] = $this->context->link->getCategoryLink($res[$key]['id_category'], $res[$key]['link_rewrite'], null, null, $res[$key]['id_shop']);
                $res[$key]['content'] = strip_tags(Tools::htmlentitiesDecodeUTF8($res[$key]['content']));
                $res[$key]['cnt_faq'] = $this->getCntFaqText((int) $res[$key]['id_text']);
                $res[$key]['linked_blck'] = $this->getLinkedBlckText((int) $res[$key]['id_text']);
            }
        }

        return $res;
    }

    protected function getFAQListCat($id_text, $id_lang)
    {
        if ($id_text == 0) {
            return [];
        }

        $sql_query = 'SELECT b.`id_faq`, b.`id_text`, b.`order`, b.`active`, bl.`question`, bl.`answer` FROM `' . _DB_PREFIX_ . 'add_category_faq` b LEFT JOIN `' . _DB_PREFIX_ . 'add_category_faq_lang` bl on (b.`id_faq` = bl.`id_faq`) WHERE b.`id_text` = ' . (int) $id_text . ' AND bl.`id_lang` = ' . (int) $id_lang . ' ORDER BY b.`order`';
        $res = Db::getInstance()->executeS($sql_query);

        if (isset($res) && count($res) > 0) {
            foreach ($res as $key => $faq) {
                $res[$key]['question'] = strip_tags(Tools::htmlentitiesDecodeUTF8($res[$key]['question']));
                $res[$key]['answer'] = strip_tags(Tools::htmlentitiesDecodeUTF8($res[$key]['answer']));
            }
        }

        return $res;
    }

    protected function getFAQListTmp($id_blck, $id_lang)
    {
        $sql_query = 'SELECT b.`id_faq`, b.`id_blck`, b.`order`, b.`active`, bl.`question`, bl.`answer` FROM `' . _DB_PREFIX_ . 'add_category_faq` b LEFT JOIN `' . _DB_PREFIX_ . 'add_category_faq_lang` bl on (b.`id_faq` = bl.`id_faq`) WHERE b.`id_blck` = ' . (int) $id_blck . ' AND bl.`id_lang` = ' . (int) $id_lang . ' ORDER BY b.`order`';
        $res = Db::getInstance()->executeS($sql_query);

        if (isset($res) && count($res) > 0) {
            foreach ($res as $key => $faq) {
                $res[$key]['question'] = strip_tags(Tools::htmlentitiesDecodeUTF8($res[$key]['question']));
                $res[$key]['answer'] = strip_tags(Tools::htmlentitiesDecodeUTF8($res[$key]['answer']));
            }
        }

        return $res;
    }

    protected function getCntFaqText($id_text)
    {
        $sql_query = 'SELECT count(*) as cnt FROM `' . _DB_PREFIX_ . 'add_category_faq` b WHERE b.`id_text` = ' . (int) $id_text . ' AND b.`linked_tmp` = 0';
        $res = Db::getInstance()->executeS($sql_query);

        return $res[0]['cnt'];
    }

    protected function getLinkedBlckText($id_text)
    {
        $sql_query = 'SELECT t.`title` FROM `' . _DB_PREFIX_ . 'add_category_faq` b LEFT JOIN `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` t on (b.`id_blck` = t.`id_blck`) WHERE b.`id_text` = ' . (int) $id_text . ' AND b.`linked_tmp` = 1';
        $res = Db::getInstance()->executeS($sql_query);

        return $res;
    }

    protected function getCntBlockFaqTmp()
    {
        $sql_query = 'SELECT count(*) as cnt FROM `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` b WHERE b.`active` = 1';
        $res = Db::getInstance()->executeS($sql_query);

        return $res;
    }

    protected function checkFaqTables()
    {
        // check for faq tables
        $sql_check = 'SHOW TABLES LIKE "' . _DB_PREFIX_ . 'add_category_faqblock_tmp"';
        $res_check = Db::getInstance()->executeS($sql_check);
        if (empty($res_check)) {
            // add tables for faq
            $this->installFaqTables();
        }
        // #check for faq tables
    }

    protected function getListBlockFaqTmp($title_only = false, $id_lang = 0)
    {
        $this->checkFaqTables();

        $where = '';
        if ($title_only) {
            $where = 'WHERE b.`active` = 1';
        }

        $sql_query = 'SELECT b.`id_blck`, b.`title`, b.`active` FROM `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` b ' . $where;
        $res = Db::getInstance()->executeS($sql_query);

        if ($title_only) {
            return $res;
        }

        if (isset($res) && count($res) > 0) {
            foreach ($res as $key => $block) {
                $sql_query = 'SELECT b.`id_faq`, b.`id_blck`, b.`active`, bl.`question`, bl.`answer` FROM `' . _DB_PREFIX_ . 'add_category_faq` b LEFT JOIN `' . _DB_PREFIX_ . 'add_category_faq_lang` bl on (b.`id_faq` = bl.`id_faq`) WHERE b.`id_blck` = ' . (int) $res[$key]['id_blck'] . ' AND bl.`id_lang` = ' . (int) $id_lang . ' ORDER BY b.`order`';
                $res_faq = Db::getInstance()->executeS($sql_query);
                $res[$key]['faq'] = $res_faq;
            }
        }

        return $res;
    }

    protected function getListTmp($id_text)
    {
        if ($id_text == 0) {
            return [];
        }

        $sql_query = 'SELECT distinct b.`id_blck` FROM `' . _DB_PREFIX_ . 'add_category_faq` b WHERE b.`id_blck`!=0 AND b.`id_text`=' . (int) $id_text;
        $res = Db::getInstance()->executeS($sql_query);

        return $res;
    }

    protected function displayForm()
    {
        // lang block
        $id_shop = (int) $this->context->shop->id;

        $language_arr = $this->getLangArr();
        $id_lang = $this->getLangCurId($language_arr);
        // #lang block

        $this->context->controller->addJqueryPlugin('tablednd');
        $this->context->controller->addJS(_PS_JS_DIR_ . 'admin-dnd.js');

        $this->_display = 'index';

        $partial_color_array = [
            'type' => 'hidden',
            'label' => '',
            'name' => 'partial_color',
        ];

        $partial_height_array = [
            'type' => 'hidden',
            'label' => '',
            'name' => 'partial_height',
        ];

        $partial_pos_array = [
            'type' => 'hidden',
            'label' => '',
            'name' => 'partial_position',
        ];

        if ((int) Configuration::get('ADD_CATDESC_PN') > 0) {
            $partial_color_array = [
                'type' => 'color',
                'label' => $this->l('Background color for partial view'),
                'name' => 'partial_color',
                'desc' => $this->l('This parameter works when Length for partial view > 0. Set background color for partial view.'),
                'class' => 'color-small',
            ];
            $partial_height_array = [
                'type' => 'text',
                'label' => $this->l('Height for partial view'),
                'name' => 'partial_height',
                'desc' => $this->l('This parameter works when Length for partial view > 0. Set height of the partial view\'s wrapper in pixels. Set "0" - the height will be default = 140px.'),
                'hint' => $this->l('Set height of the partial view\'s wrapper.'),
                'class' => 'el-small',
            ];
            $partial_pos_array = [
                'type' => 'select_position',
                'label' => $this->l('Button position for partial view'),
                'name' => 'partial_position',
                'class' => 'el-small',
            ];
        }

        $this->fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Module configuration'),
                'image' => _PS_ADMIN_IMG_ . 'information.png',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Number of categories in the list:'),
                    'name' => 'num_cat_field',
                    'class' => 'el-small',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Start of the segment for drop-down list in add,edit form:'),
                    'name' => 'cutb',
                    'desc' => $this->l('Set "0", if you want to show all free categories in drop-down list'),
                    'hint' => $this->l('This parameter is used for very large amount of free categories in your shop and reduce the size and time for loading of the drop-down list in add,edit form.'),
                    'class' => 'el-small',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Number of categories for drop-down list in add,edit form:'),
                    'name' => 'cute',
                    'desc' => $this->l('Set "0", if you want to show all free categories in drop-down list'),
                    'hint' => $this->l('This parameter is used for very large amount of free categories in your shop and reduce the size and time for loading of the drop-down list in add,edit form.'),
                    'class' => 'el-small',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Length for partial view'),
                    'name' => 'partial_number',
                    'desc' => $this->l('Set number of symbols. Set "0", if you want to show additional description fully. Partial view will be shown if length of additional description more then this parameter.'),
                    'hint' => $this->l('This parameter is used if you want to show additional description category with drop-down effect in front-office'),
                    'class' => 'el-small',
                ],
                $partial_height_array,
                $partial_color_array,
                $partial_pos_array,
                [
                    'type' => 'switch',
                    'name' => 'show_fp',
                    'label' => $this->l('Show description in first page only'),
                    'desc' => $this->l('Turn On or turn Off additional description of category in pagination or filtration process in front-office.'),
                    'hint' => $this->l('This parameter is used if you want to show additional description category in one page (first page) only, when you have pagination or filtration process in category page.'),
                    'href' => '',
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
                    'type' => 'switch',
                    'name' => 'show_faq_fp',
                    'label' => $this->l('Show FAQ in first page only'),
                    'desc' => $this->l('Turn On or turn Off FAQ of category in pagination or filtration process in front-office.'),
                    'hint' => $this->l('This parameter is used if you want to show FAQ in one page (first page) only, when you have pagination or filtration process in category page.'),
                    'href' => '',
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
                    'type' => 'switch',
                    'name' => 'show_sc',
                    'label' => $this->l('Enable shortcodes'),
                    'desc' => $this->l('Turn On or turn Off shortcode support'),
                    'hint' => $this->l('This parameter is used if you want to display products using shortcodes. See information tab.'),
                    'href' => '',
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
            ],
            'submit' => [
                'name' => 'submitSaveNum',
                'title' => $this->l('Save'),
            ],
        ];

        // pagination block
        $currentIndex = AdminController::$currentIndex;
        // number categories for the page
        $com_num = (int) Configuration::get('ADD_CATDESC_NCF');

        $where = '';
        $start = 0;
        $end = 0;
        $red_ost = '';
        $totalpages = 0;
        $pages = [];
        $notpage = 0;
        if ($com_num != 0) {
            $p = (int) Tools::getValue('p');

            $id_shop_group = (int) Shop::getContextShopGroupID();
            $where_shop = '';
            if ($id_shop_group != 0) {
                $where_shop = 'AND p.`id_shop` = ' . (int) $id_shop;
            }

            // if search field was activated
            $search_fld = Configuration::get('ADD_CATDESC_SFLD');
            if (!empty($search_fld)) {
                $where = 'AND cl.`name` like "%' . pSQL($search_fld) . '%"';
            }

            $sql_count = 'SELECT count(*) as num FROM `' . _DB_PREFIX_ . 'add_category_text` p LEFT JOIN `' . _DB_PREFIX_ . 'add_category_text_lang` pl on (p.`id_text` = pl.`id_text`) LEFT JOIN `' . _DB_PREFIX_ . 'category` m on (p.`id_category` = m.`id_category`) LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl on (m.`id_category` = cl.`id_category`) WHERE cl.`id_lang` = ' . (int) $id_lang . ' ' . $where_shop . ' ' . $where . 'AND pl.`id_lang` = ' . (int) $id_lang . ' ' . Shop::addSqlRestrictionOnLang('cl');

            $count_results = Db::getInstance()->ExecuteS($sql_count);

            if ($count_results[0]['num'] > $com_num) {
                $totalpages = ceil($count_results[0]['num'] / $com_num);

                if (Tools::getValue('orderIdText')) {
                    if (Tools::getValue('orderIdText') == 'ASC') {
                        $red_ost .= '&orderIdText=ASC';
                    } elseif (Tools::getValue('orderIdText') == 'DESC') {
                        $red_ost .= '&orderIdText=DESC';
                    }
                }
                if (Tools::getValue('orderIdCat')) {
                    if (Tools::getValue('orderIdCat') == 'ASC') {
                        $red_ost .= '&orderIdCat=ASC';
                    } elseif (Tools::getValue('orderIdCat') == 'DESC') {
                        $red_ost .= '&orderIdCat=DESC';
                    }
                }
                if (Tools::getValue('orderCatName')) {
                    if (Tools::getValue('orderCatName') == 'ASC') {
                        $red_ost .= '&orderCatName=ASC';
                    } elseif (Tools::getValue('orderCatName') == 'DESC') {
                        $red_ost .= '&orderCatName=DESC';
                    }
                }

                if ($p > $totalpages) {
                    Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . $red_ost);
                }

                $currentpage = 1;

                $start = ($currentpage - 1) * $com_num;
                $end = $start + $com_num;

                if ($p > 0) {
                    $start = ($p - 1) * $com_num;
                    $end = $com_num;
                }

                // pagination
                for ($i = 0; $i < $totalpages; $i++) {
                    if ($totalpages > 6 && ($p == $totalpages)) {
                        // for last page
                        if ($i >= $p - 3) {
                            $pages[$i] = $currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&p=' . ($i + 1) . $red_ost;
                        }
                    } elseif ($totalpages > 6 && ($p == 0 || $p == 1)) {
                        // for first page
                        if ($i <= 2) {
                            $pages[$i] = $currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&p=' . ($i + 1) . $red_ost;
                        }
                    } elseif ($totalpages > 6 && ($p - 1 != 0) && ($i >= $p - 2) && ($i < $p + 1)) {
                        // for centre pages
                        $pages[$i] = $currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&p=' . ($i + 1) . $red_ost;
                    } elseif ($totalpages <= 6) {
                        // for other pages
                        $pages[$i] = $currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&p=' . ($i + 1) . $red_ost;
                    }
                }
            } else {
                $start = 0;
                $end = $com_num;
                // no show pagination
                $notpage = 1;
            }
        }
        // end pagination block

        $title_text_cat = $this->getFreeCategoriesNum() == 0 ? $this->l('You have no free categories for descriptions. Add new category or delete records from the list') : '';

        $this->fields_form[1]['form'] = [
            'legend' => [
                'title' => $this->l('List of categories description') . ' <' . $this->l('Total free categories:') . ' ' . $this->getFreeCategoriesNum() . '> ' . $title_text_cat,
                'icon' => 'icon-list',
            ],
            'input' => [
                [
                    'type' => 'cat_list',
                    'label' => '',
                    'name' => 'cat_list',
                    'values' => $this->getListContent($id_lang, $start, $end, $where),
                ],
            ],
        ];

        $this->fields_form[2]['form'] = [
            'legend' => [
                'title' => $this->l('List of FAQ block templates (Use them to link and copy FAQs in categories)'),
                'icon' => 'icon-list',
                'class' => 'faq_blck_add',
                'href' => $currentIndex . '&amp;configure=' . $this->name . '&amp;token=' . Tools::getAdminTokenLite('AdminModules') . '&amp;addTmpBlock',
                'desc' => $this->l('Add new FAQ template block'),
            ],
            'input' => [
                [
                    'type' => 'faq_blck_tmp_list',
                    'label' => '',
                    'name' => 'faq_blck_tmp_list',
                    'values' => $this->getListBlockFaqTmp(false, $id_lang),
                ],
            ],
        ];

        $this->fields_form[3]['form'] = [
            'legend' => [
                'title' => $this->l('Information'),
                'image' => _PS_ADMIN_IMG_ . 'information.png',
            ],
            'input' => [
                [
                    'type' => 'info_text',
                    'label' => '',
                    'name' => 'info_text_name',
                ],
            ],
        ];

        if (version_compare(_PS_VERSION_, '1.8.0') < 0) {
            $this->fields_form[3]['form'] = [
                'legend' => [
                    'title' => $this->l('Installation and Uninstallation of the module'),
                    'image' => _PS_ADMIN_IMG_ . 'warning.gif',
                    'class' => 'noshow',
                ],
                'input' => [
                    [
                        'type' => 'button_activator',
                        'name' => 'activator',
                        'label' => '',
                        'param' => $this->context->shop->physical_uri . $this->context->shop->virtual_uri . 'modules/' . $this->name,
                        'module_url' => $this->context->link->getAdminLink('AdminAddCatDesc'),
                        'secure_key' => $this->secure_key,
                    ],
                ],
            ];

            $this->fields_form[4]['form'] = [
                'legend' => [
                    'title' => $this->l('Information'),
                    'image' => _PS_ADMIN_IMG_ . 'information.png',
                    'class' => 'noshowforinfo',
                ],
                'input' => [
                    [
                        'type' => 'info_text',
                        'label' => '',
                        'name' => 'info_text_name',
                    ],
                ],
            ];
        }

        $helper = $this->initForm();

        $helper->tpl_vars = [
            'info_install' => Configuration::get('ADD_CATDESC_SET'),
            'prestashop_version177' => version_compare(_PS_VERSION_, '1.7.7'),
            'fields_value' => [
                'num_cat_field' => (int) Configuration::get('ADD_CATDESC_NCF'),
                'cutb' => (int) Configuration::get('ADD_CATDESC_CUTB'),
                'cute' => (int) Configuration::get('ADD_CATDESC_CUTE'),
                'partial_number' => (int) Configuration::get('ADD_CATDESC_PN'),
                'partial_height' => (int) Configuration::get('ADD_CATDESC_PH'),
                'partial_position' => (int) Configuration::get('ADD_CATDESC_PP'),
                'partial_color' => Configuration::get('ADD_CATDESC_PC'),
                'show_fp' => (int) Configuration::get('ADD_CATDESC_SFP'),
                'show_faq_fp' => (int) Configuration::get('ADD_CATDESC_FAQ_SFP'),
                'show_sc' => (int) Configuration::get('ADD_CATDESC_SSC'),
            ],
            'lastpage' => $currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&p=' . $totalpages . $red_ost,
            'totalpages' => $totalpages,
            'pages' => $pages,
            'notpage' => $notpage,
            'link' => $this->context->link,
            'new_version' => (version_compare(_PS_VERSION_, '1.7.6') >= 0) ? 1 : 0,
            'new_version178' => (version_compare(_PS_VERSION_, '1.7.8') >= 0) ? 1 : 0,
            'new_version180' => (version_compare(_PS_VERSION_, '1.8.0') >= 0) ? 1 : 0,
            'filter_column_name' => Configuration::get('ADD_CATDESC_SFLD'),
            'multishop' => $this->getShopContext(),
            'language_arr' => $language_arr,
            'language_cur' => (int) $id_lang,
        ];

        $this->_html .= $helper->generateForm($this->fields_form);
    }

    public function moduleLink()
    {
        if (Configuration::get('PS_REWRITING_SETTINGS') == 0) {
            $front_link = $this->context->link->getModuleLink('addcatdesc', 'ajaxaddcatdesc') . '&secure_key=' . $this->secure_key;
        } else {
            $front_link = $this->context->link->getModuleLink('addcatdesc', 'ajaxaddcatdesc') . '?secure_key=' . $this->secure_key;
        }

        return $front_link;
    }

    protected function getLangArr()
    {
        $id_shop_group = (int) Shop::getContextShopGroupID();
        $id_lang = (int) Context::getContext()->language->id;
        $id_shop = (int) $this->context->shop->id;

        if ($id_shop_group != 0) {
            $language_arr = Language::getLanguages(true, (int) $id_shop);
        } else {
            $language_arr = Language::getLanguages(true);
        }

        return $language_arr;
    }

    protected function getLangCurId($language_arr)
    {
        $id_lang = (int) Context::getContext()->language->id;

        if ((int) Configuration::get('ADD_CATDESC_LANGL') > 0 && count($language_arr) > 1) {
            $id_lang = (int) Configuration::get('ADD_CATDESC_LANGL');
        } elseif (count($language_arr) == 1) {
            $id_lang = $language_arr[0]['id_lang'];
        }

        return $id_lang;
    }

    protected function displayAddForm()
    {
        $currentIndex = AdminController::$currentIndex;

        $form_tmp_arr = [
            'type' => 'empty_tmp_name_sel',
            'label' => '',
            'name' => 'empty_tmp_name_sel',
        ];

        $id_cat = 0;
        if (Tools::isSubmit('editCategoryText') && Tools::getValue('id_cat')) {
            $this->_display = 'edit';
            $id_cat = (int) Tools::getValue('id_cat');
            $editCategoryText = (int) Tools::getValue('editCategoryText');

            $this->context->controller->addJqueryPlugin('tablednd');
            $this->context->controller->addJS(_PS_JS_DIR_ . 'admin/dnd.js');

            if ($this->getShopContext()) {
                $form_arr = [
                    'type' => 'shop_name_cur',
                    'label' => $this->l('Shop name'),
                    'name' => 'shop_name_cur',
                ];
            } else {
                $form_arr = [
                    'type' => 'hidden_shop_name_ex',
                    'label' => '',
                    'name' => 'hidden_shop_name_ex',
                ];
            }

            $get_list_blck_arr_val = $this->getListBlockFaqTmp(true);

            if (!empty($get_list_blck_arr_val)) {
                $form_tmp_arr = [
                    'type' => 'tmp_name_sel',
                    'label' => $this->l('Tie to FAQ templates:'),
                    'name' => 'tmp_name_sel',
                    'desc' => $this->l('Tie FAQ templates to the category. Changes in chosen templates will be influence for this category also. Use CTRL + mouse button to add more then one template.'),
                    'values' => $get_list_blck_arr_val,
                ];
            }
        } else {
            $this->_display = 'add';
            $form_arr = [
                'type' => 'shop_switch',
                'label' => '',
                'name' => 'shop_switch',
            ];
        }

        $id_text = 0;
        $href_el = '';
        $category_front_link = '';
        $id_shop = 0;

        if (Tools::getValue('id_cat')) {
            $id_shop = Tools::getValue('id_shop_cat');
            $text_sql = 'SELECT t.`id_text`, cl.`link_rewrite` FROM `' . _DB_PREFIX_ . 'add_category_text` t LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl on (t.`id_category` = cl.`id_category`) WHERE t.`id_shop` = ' . (int) $id_shop . ' AND t.`id_category` = ' . (int) $id_cat;
            $res_text = Db::getInstance()->ExecuteS($text_sql);
            $id_text = (int) $res_text[0]['id_text'];
            $href_el = '&id_text=' . (int) $id_text;
            $category_front_link = $this->context->link->getCategoryLink((int) $id_cat, (int) $res_text[0]['link_rewrite'], null, null, (int) $id_shop);
        }

        $this->fields_form[0]['form'] = [
            'tinymce' => true,
            'legend' => [
                'title' => isset($id_cat) && isset($editCategoryText) ? $this->l('Edit the description or FAQ') : $this->l('Add new description or FAQ for category.'),
                'image' => isset($id_cat) && isset($editCategoryText) ? _PS_ADMIN_IMG_ . 'edit.gif' : _PS_ADMIN_IMG_ . 'add.gif',
            ],
            'input' => [
                [
                    'type' => 'select_category',
                    'label' => $this->l('Choose free category'),
                    'name' => 'id_cat',
                    'options' => [
                        'query' => isset($id_cat) && isset($editCategoryText) ? $this->getFreeCategories($id_cat, (int) Tools::getValue('id_shop_cat')) : $this->getFreeCategories(),
                        'id' => 'id_category',
                        'name' => 'category_name',
                    ],
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Text'),
                    'lang' => true,
                    'name' => 'content',
                    'cols' => 40,
                    'rows' => 10,
                    'class' => 'rte',
                    'autoload_rte' => true,
                ],
                $form_tmp_arr,
                $form_arr,
                [
                    'type' => 'submit_text_button',
                    'label' => '',
                    'name' => 'submitTextButton',
                ],
            ],
        ];

        // lang block
        $language_arr = $this->getLangArr();
        $id_lang = $this->getLangCurId($language_arr);
        // #lang block

        $this->fields_form[1]['form'] = [
            'legend' => [
                'title' => $this->l('List of FAQ for this category:'),
                'icon' => 'icon-list',
                'class' => 'faq_cat_list',
                'desc' => $this->l('Add FAQ for this category'),
                'href' => $currentIndex . '&amp;configure=' . $this->name . '&amp;token=' . Tools::getAdminTokenLite('AdminModules') . '&amp;addFaq' . $href_el,
            ],
            'input' => [
                [
                    'type' => 'faq_tmp_list',
                    'label' => '',
                    'name' => 'faq_tmp_list',
                    'values' => isset($id_cat) && isset($editCategoryText) ? $this->getFAQListCat($id_text, $id_lang) : '',
                ],
            ],
        ];

        if (Tools::getValue('id_cat')) {
            $this->fields_value['id_cat'] = Tools::getValue('id_cat');
            $this->context->controller->getLanguages();

            // add hidden field id_text
            $this->fields_form[0]['form']['input'][] = ['type' => 'hidden', 'name' => 'id_text'];
            $this->fields_value['id_text'] = (int) $id_text;

            $this->fields_form[0]['form']['input'][] = ['type' => 'hidden', 'name' => 'id_shop_cat'];
            $this->fields_value['id_shop_cat'] = (int) Tools::getValue('id_shop_cat');

            foreach ($this->context->controller->_languages as $language) {
                $res_sql = 'SELECT tl.`content` FROM `' . _DB_PREFIX_ . 'add_category_text` t LEFT JOIN `' . _DB_PREFIX_ . 'add_category_text_lang` tl on (t.`id_text`=tl.`id_text`) WHERE t.`id_shop` = ' . (int) $id_shop . ' AND t.`id_category` = ' . (int) $id_cat . ' AND tl.`id_lang` = ' . (int) $language['id_lang'];
                $res = Db::getInstance()->ExecuteS($res_sql);
                $this->fields_value['content'][$language['id_lang']] = Tools::htmlentitiesDecodeUTF8($res[0]['content']);
            }

            $this->context->smarty->assign([
                'shop_name_cur' => Shop::getShop((int) $id_shop),
                'hidden_shop_name_ex' => '',
            ]);
        } else {
            $this->context->controller->getLanguages();
            foreach ($this->context->controller->_languages as $language) {
                $this->fields_value['content'][$language['id_lang']] = Tools::getValue('content_' . $language['id_lang']);
            }
            $this->context->smarty->assign([
                'shop_switch' => $this->getShopContext(),
            ]);
        }

        $shop_arr = Shop::getShops(true);
        $this->context->smarty->assign([
            'new_version178' => (version_compare(_PS_VERSION_, '1.7.8') >= 0) ? 1 : 0,
            'link' => $this->context->link,
            'display' => isset($id_cat) && isset($editCategoryText) ? 'edit' : 'add',
            // don't show savestay button in addition for multishop
            'shop_num' => count($shop_arr),
            'tmp_list_ch' => $this->getListTmp((int) $id_text),
            'module_url' => $this->context->link->getAdminLink('AdminAddCatDesc'),
            'secure_key' => $this->secure_key,
            'id_text' => (int) $id_text,
            'language_arr' => $language_arr,
            'language_cur' => (int) $id_lang,
            'currentIndex' => AdminController::$currentIndex . '&configure=' . $this->name,
            'category_front_link' => $category_front_link,
        ]);

        $helper = $this->initForm();
        $helper->fields_value = isset($this->fields_value) ? $this->fields_value : [];
        $this->_html .= $helper->generateForm($this->fields_form);
    }

    private function saveCatShops()
    {
        if (Tools::getValue('save_all')) {
            $shop_arr = Shop::getShops(true);
            if (count($shop_arr) > 1) {
                foreach ($shop_arr as $value) {
                    $sql_content_query = 'SELECT p.`id_text` FROM `' . _DB_PREFIX_ . 'add_category_text` p WHERE p.`id_shop` = ' . (int) $value['id_shop'] . ' AND p.`id_category` = ' . (int) Tools::getValue('id_cat');
                    $res_content = Db::getInstance()->executeS($sql_content_query);
                    if (empty($res_content)) {
                        Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_text` (`id_category`, `id_shop`) VALUES(' . (int) Tools::getValue('id_cat') . ', ' . (int) $value['id_shop'] . ')');

                        $id_text = Db::getInstance()->Insert_ID();
                        $languages = Language::getLanguages(false);

                        foreach ($languages as $language) {
                            Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_text_lang` (`id_text`, `id_lang`, `content`) VALUES(' . (int) $id_text . ', ' . (int) $language['id_lang'] . ', "' . pSQL(Tools::getValue('content_' . $language['id_lang']), true) . '")');
                        }
                    }
                }
            }
        }
    }

    private function displayAddFaqBlockForm()
    {
        $currentIndex = AdminController::$currentIndex;

        // lang block
        $language_arr = $this->getLangArr();
        $id_lang = $this->getLangCurId($language_arr);
        // #lang block

        $href_el = '';
        if (Tools::isSubmit('editTmpBlock') && Tools::getValue('id_blck')) {
            $this->display_blck_tmp = 'editTmpBlock';
            $id_blck = (int) Tools::getValue('id_blck');
            $href_el = '&id_blck=' . (int) $id_blck;
            $this->context->controller->addJqueryPlugin('tablednd');
            $this->context->controller->addJS(_PS_JS_DIR_ . 'admin/dnd.js');
        } else {
            $this->display_blck_tmp = 'addTmpBlock';
        }

        $this->fields_form[0]['form'] = [
            'legend' => [
                'title' => isset($id_blck) && $this->display_blck_tmp == 'editTmpBlock' ? $this->l('Edit FAQ template') : $this->l('Add new FAQ template'),
                'image' => isset($id_blck) && $this->display_blck_tmp == 'editTmpBlock' ? _PS_ADMIN_IMG_ . 'edit.gif' : _PS_ADMIN_IMG_ . 'add.gif',
                'href' => $currentIndex . '&amp;configure=' . $this->name . '&amp;token=' . Tools::getAdminTokenLite('AdminModules') . '&amp;addFaq' . $href_el,
                'desc' => $this->l('Add new FAQ to template'),
                'class' => isset($id_blck) && $this->display_blck_tmp == 'editTmpBlock' ? 'faq_blck_edit' : '',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('FAQ template title'),
                    'name' => 'faq_title',
                    'class' => 'el-middle',
                    'desc' => $this->display_blck_tmp == 'editTmpBlock' ? $this->l('Edit FAQ template title. Template title is used for back-office only.') : $this->l('Enter FAQ template title and save before add the FAQ. Template title is used for back-office only.'),
                    'required' => true,
                ],
                [
                    'type' => 'submit_text_button',
                    'label' => '',
                    'name' => 'submit_text_button',
                ],
            ],
        ];

        $this->fields_form[1]['form'] = [
            'legend' => [
                'title' => $this->l('List of questions for this template:'),
                'icon' => 'icon-list',
                'href' => $currentIndex . '&amp;configure=' . $this->name . '&amp;token=' . Tools::getAdminTokenLite('AdminModules') . '&amp;addFaq' . $href_el,
                'desc' => $this->l('Add new FAQ to template'),
                'class' => isset($id_blck) && $this->display_blck_tmp == 'editTmpBlock' ? 'faq_blck_edit' : '',
            ],
            'input' => [
                [
                    'type' => 'faq_tmp_list',
                    'label' => '',
                    'name' => 'faq_tmp_list',
                    'values' => isset($id_blck) && $this->display_blck_tmp == 'editTmpBlock' ? $this->getFAQListTmp($id_blck, $id_lang) : '',
                ],
            ],
        ];

        if ($this->display_blck_tmp == 'editTmpBlock') {
            $res_sql = 'SELECT t.`id_blck`, t.`title` FROM `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` t WHERE t.`id_blck` = ' . (int) $id_blck;
            $res = Db::getInstance()->ExecuteS($res_sql);
            $this->fields_value['faq_title'] = $res[0]['title'];
            $this->fields_form[0]['form']['input'][] = ['type' => 'hidden', 'name' => 'id_blck'];
            $this->fields_value['id_blck'] = (int) Tools::getValue('id_blck');
        } else {
            $fill_field = Tools::getValue('faq_title');
            if (empty($fill_field)) {
                $res_sql = 'SELECT count(*) as cnt FROM `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` t';
                $res = Db::getInstance()->ExecuteS($res_sql);
                $fill_field = $this->l('Template 1');
                if (!empty($res)) {
                    $id = (int) $res[0]['cnt'] + 1;
                    $fill_field = $this->l('Template ') . (int) $id;
                }
            }

            $this->fields_value['faq_title'] = $fill_field;
        }

        $helper = $this->initForm();
        $this->context->smarty->assign(
            [
                'link' => $this->context->link,
                'display' => $this->display_blck_tmp,
                'new_version178' => (version_compare(_PS_VERSION_, '1.7.8') >= 0) ? 1 : 0,
                'language_arr' => $language_arr,
                'language_cur' => (int) $id_lang,
                'currentIndex' => AdminController::$currentIndex . '&configure=' . $this->name,
            ]
        );

        $helper->fields_value = isset($this->fields_value) ? $this->fields_value : [];
        $this->_html .= $helper->generateForm($this->fields_form);
    }

    private function getCategoryFormParams($id_text, $id_lang)
    {
        $check_sql = 'SELECT t.`id_category`, t.`id_shop`, tl.`name`, CONCAT_WS("-", t.`id_category`, tl.`name`) as category_name FROM `' . _DB_PREFIX_ . 'add_category_text` t LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` tl on (t.`id_category`=tl.`id_category`) WHERE tl.`id_lang` = ' . (int) $id_lang . ' AND t.`id_text` = ' . (int) $id_text;
        $res = Db::getInstance()->ExecuteS($check_sql);

        return $res;
    }

    private function displayAddFaqForm()
    {
        if (Tools::isSubmit('editFaq') && Tools::getValue('id_faq')) {
            $this->display_faq_tmp = 'editFaq';
            $id_faq = (int) Tools::getValue('id_faq');
        } else {
            $this->display_faq_tmp = 'addFaq';
        }

        $id_blck = (int) Tools::getValue('id_blck');
        $id_text = (int) Tools::getValue('id_text');
        $id_shop_cat = 0;
        $id_cat = 0;

        $title_el = '';
        if ($id_text > 0) {
            // take category name for title
            $id_lang = (int) Context::getContext()->language->id;

            $res = $this->getCategoryFormParams($id_text, $id_lang);

            $title_el = $res[0]['category_name'] . $this->l(' (Category)');
            $id_shop_cat = (int) $res[0]['id_shop'];
            $id_cat = (int) $res[0]['id_category'];
        } elseif ($id_blck > 0) {
            // take template name for title
            $check_sql = 'SELECT t.`title` FROM `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` t WHERE t.`id_blck` = ' . (int) $id_blck;
            $res = Db::getInstance()->ExecuteS($check_sql);
            $title_el = $res[0]['title'] . $this->l(' (Template)');
        }

        $this->fields_form[0]['form'] = [
            'tinymce' => true,
            'legend' => [
                'title' => isset($id_faq) && $this->display_faq_tmp == 'editFaq' ? $this->l('Edit FAQ') : $this->l('Add new FAQ to: ') . $title_el,
                'image' => isset($id_faq) && $this->display_faq_tmp == 'editFaq' ? _PS_ADMIN_IMG_ . 'edit.gif' : _PS_ADMIN_IMG_ . 'add.gif',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Question'),
                    'name' => 'question',
                    'lang' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->l('Answer'),
                    'lang' => true,
                    'name' => 'answer',
                    'cols' => 40,
                    'rows' => 10,
                    'class' => 'rte',
                    'autoload_rte' => true,
                ],
                [
                    'type' => 'submit_text_button',
                    'label' => '',
                    'name' => 'submit_text_button',
                ],
            ],
        ];

        $this->fields_form[0]['form']['input'][] = ['type' => 'hidden', 'name' => 'id_blck'];
        $this->fields_value['id_blck'] = (int) Tools::getValue('id_blck');

        $this->fields_form[0]['form']['input'][] = ['type' => 'hidden', 'name' => 'id_text'];
        $this->fields_value['id_text'] = (int) Tools::getValue('id_text');

        $this->fields_form[0]['form']['input'][] = ['type' => 'hidden', 'name' => 'id_shop_cat'];
        $this->fields_value['id_shop_cat'] = (int) $id_shop_cat;

        $this->fields_form[0]['form']['input'][] = ['type' => 'hidden', 'name' => 'id_cat'];
        $this->fields_value['id_cat'] = (int) $id_cat;

        if ($this->display_faq_tmp == 'editFaq') {
            // add hidden field id_faq
            $this->fields_form[0]['form']['input'][] = ['type' => 'hidden', 'name' => 'id_faq'];
            $this->fields_value['id_faq'] = (int) Tools::getValue('id_faq');

            foreach ($this->context->controller->getLanguages() as $language) {
                $sql_query = 'SELECT b.`id_blck`, b.`id_faq`, bl.`id_lang`, bl.`question`, bl.`answer` FROM `' . _DB_PREFIX_ . 'add_category_faq` b LEFT JOIN `' . _DB_PREFIX_ . 'add_category_faq_lang` bl on (b.`id_faq` = bl.`id_faq`) WHERE b.`id_faq` = ' . (int) $id_faq . ' AND bl.`id_lang` = ' . (int) $language['id_lang'];

                $res = Db::getInstance()->ExecuteS($sql_query);
                $this->fields_value['question'][$language['id_lang']] = $res[0]['question'];
                $this->fields_value['answer'][$language['id_lang']] = html_entity_decode($res[0]['answer'], ENT_COMPAT, 'UTF-8');
            }
        } else {
            foreach ($this->context->controller->getLanguages() as $language) {
                $this->fields_value['question'][$language['id_lang']] = Tools::getValue('question_' . $language['id_lang']);
                $this->fields_value['answer'][$language['id_lang']] = Tools::getValue('answer_' . $language['id_lang']);
            }
        }

        $helper = $this->initForm();

        $this->context->smarty->assign(
            [
                'link' => $this->context->link,
                'display' => $this->display_faq_tmp,
                'id_blck' => $id_blck,
                'id_text' => $id_text,
                'id_shop_cat' => $id_shop_cat,
                'id_cat' => $id_cat,
            ]
        );

        $helper->fields_value = isset($this->fields_value) ? $this->fields_value : [];
        $this->_html .= $helper->generateForm($this->fields_form);
    }

    private function getShopContext()
    {
        $shop_arr = Shop::getShops(true);

        if (count($shop_arr) > 1) {
            return true;
        }

        return false;
    }

    private function getFreeCategories($id_cat = null, $id_shop_cat = null)
    {
        $id_shop = (int) $this->context->shop->id;
        if ($id_shop_cat > 0) {
            $id_shop = $id_shop_cat;
        }
        $id_lang = (int) Context::getContext()->language->id;
        $language_arr = Language::getLanguages(true, (int) $id_shop);

        if ((int) Configuration::get('ADD_CATDESC_LANGL') > 0 && count($language_arr) > 1) {
            $id_lang = (int) Configuration::get('ADD_CATDESC_LANGL');
        } elseif (count($language_arr) == 1) {
            $id_lang = $language_arr[0]['id_lang'];
        }

        $limit_sql = '';
        $cutb = (int) Configuration::get('ADD_CATDESC_CUTB');
        $cute = (int) Configuration::get('ADD_CATDESC_CUTE');
        if ($cute > 0) {
            $limit_sql = 'LIMIT ' . $cutb . ',' . $cute;
        }

        // check for free categories
        $check_sql = 'SELECT p.`id_category`, pl.`name`, CONCAT_WS("-", p.`id_category`, pl.`name`) as category_name FROM `' . _DB_PREFIX_ . 'category` p LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` pl on (p.`id_category`=pl.`id_category`) LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs on (p.`id_category` = cs.`id_category`) where p.`id_category` != 1 AND pl.`id_shop` = ' . (int) $id_shop . ' AND pl.`id_lang` = ' . (int) $id_lang . ' AND cs.`id_shop` = ' . (int) $id_shop . ' AND p.`id_category` not in (SELECT t.`id_category` FROM `' . _DB_PREFIX_ . 'add_category_text` t WHERE t.`id_shop` = ' . (int) $id_shop . ') group by p.`id_category` ' . $limit_sql;

        if (isset($id_cat)) {
            $check_sql .= ' UNION SELECT t.`id_category`, tl.`name`, CONCAT_WS("-", t.`id_category`, tl.`name`) as category_name FROM `' . _DB_PREFIX_ . 'add_category_text` t LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` tl on (t.`id_category`=tl.`id_category`) WHERE tl.`id_shop` = ' . (int) $id_shop . ' AND tl.`id_lang` = ' . (int) $id_lang . ' AND t.`id_category` = ' . (int) $id_cat;
        }

        $check = Db::getInstance()->ExecuteS($check_sql);

        return $check;
    }

    private function getFreeCategoriesNum()
    {
        $id_shop = (int) $this->context->shop->id;
        $id_lang = (int) Context::getContext()->language->id;

        // check for free categories
        $check_sql = 'SELECT count(p.`id_category`) as cnt FROM `' . _DB_PREFIX_ . 'category` p LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` pl on (p.`id_category`=pl.`id_category`) LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs on (p.`id_category` = cs.`id_category`) where p.`id_category` != 1 AND pl.`id_shop` = ' . (int) $id_shop . ' AND cs.`id_shop` = ' . (int) $id_shop . ' AND pl.`id_lang` = ' . (int) $id_lang . ' AND p.`id_category` not in (SELECT t.`id_category` FROM `' . _DB_PREFIX_ . 'add_category_text` t WHERE t.`id_shop` = ' . (int) $id_shop . ')';

        $check = Db::getInstance()->ExecuteS($check_sql);

        return (int) $check[0]['cnt'];
    }

    public function hookDisplayFooter()
    {
        $this->hookdisplayAddCatDesc();

        return $this->display(__FILE__, 'addcatdesc-template.tpl');
    }

    public function hookDisplayFooterCategory()
    {
        $this->hookdisplayAddCatDesc();

        return $this->display(__FILE__, 'addcatdesc-template.tpl');
    }

    public function hookDisplayLeftColumn()
    {
        $this->hookdisplayAddCatDesc();

        return $this->display(__FILE__, 'addcatdesc-template.tpl');
    }

    public function hookDisplayRightColumn()
    {
        $this->hookdisplayAddCatDesc();

        return $this->display(__FILE__, 'addcatdesc-template.tpl');
    }

    public function hookdisplayAddCatDesc()
    {
        if (Tools::getIsset('id_category')) {
            $id_shop = (int) $this->context->shop->id;

            $cat_result = Db::getInstance()->ExecuteS('
				SELECT t.`id_category`, tl.`content`
				FROM `' . _DB_PREFIX_ . 'add_category_text` t
				LEFT JOIN `' . _DB_PREFIX_ . 'add_category_text_lang` tl on (t.`id_text` = tl.`id_text`)
				WHERE tl.`id_lang` = ' . (int) $this->context->language->id . ' and t.`id_category` = ' . (int) Tools::getValue('id_category') . ' AND t.`id_shop`=' . (int) $id_shop . ' AND t.`active` = 1');

            if (count($cat_result) > 0) {
                $faq_result = [];
                $linked_faq_result = [];

                // find faqs not linked
                $sql_faq = 'SELECT f.`id_faq`, fl.`question`, fl.`answer`
    				FROM `' . _DB_PREFIX_ . 'add_category_text` t
                    LEFT JOIN `' . _DB_PREFIX_ . 'add_category_faq` f on (t.`id_text` = f.`id_text`)
    				LEFT JOIN `' . _DB_PREFIX_ . 'add_category_faq_lang` fl on (f.`id_faq` = fl.`id_faq`)
    				WHERE fl.`id_lang` = ' . (int) $this->context->language->id . ' AND t.`id_category` = ' . (int) Tools::getValue('id_category') . ' AND t.`id_shop`=' . (int) $id_shop . ' AND t.`active` = 1 AND f.`active` = 1 AND f.`linked_tmp` != 1 ORDER BY f.`order`';

                $faq_result = Db::getInstance()->ExecuteS($sql_faq);

                if (isset($faq_result) && count($faq_result) > 0) {
                    foreach ($faq_result as $key => $faq) {
                        $content_faq = Tools::htmlentitiesDecodeUTF8($faq_result[$key]['question']);
                        if (!empty($content_faq)) {
                            $faq_result[$key]['question'] = $this->findShortcodeTitle($content_faq);
                            $faq_result[$key]['answer'] = Tools::htmlentitiesDecodeUTF8($this->findShortcode($faq_result[$key]['answer']));
                        } else {
                            unset($faq_result[$key]);
                        }
                    }
                }
                // #find faqs not linked

                // find faqs linked cat->blck->faq
                $sql_linked_blck = 'SELECT f.`id_faq`, f.`id_blck`
    				FROM `' . _DB_PREFIX_ . 'add_category_text` t
                    LEFT JOIN `' . _DB_PREFIX_ . 'add_category_faq` f on (t.`id_text` = f.`id_text`)
                    INNER JOIN `' . _DB_PREFIX_ . 'add_category_faqblock_tmp` b on (f.`id_blck` = b.`id_blck` AND b.`active` = 1)
    				WHERE t.`id_category` = ' . (int) Tools::getValue('id_category') . ' AND t.`id_shop`=' . (int) $id_shop . ' AND t.`active` = 1 AND f.`active` = 1 AND f.`linked_tmp` = 1 ORDER BY f.`order`';

                $linked_blck_result = Db::getInstance()->ExecuteS($sql_linked_blck);

                if (isset($linked_blck_result) && count($linked_blck_result) > 0) {
                    foreach ($linked_blck_result as $key => $linked_blck) {
                        $sql_linked_faq = 'SELECT f.`id_faq`, fl.`question`, fl.`answer`
    				FROM `' . _DB_PREFIX_ . 'add_category_faq` f
    				LEFT JOIN `' . _DB_PREFIX_ . 'add_category_faq_lang` fl on (f.`id_faq` = fl.`id_faq`)
    				WHERE fl.`id_lang` = ' . (int) $this->context->language->id . ' AND f.`id_blck` = ' . (int) $linked_blck['id_blck'] . ' AND f.`active` = 1 AND f.`linked_tmp` != 1 ORDER BY f.`order`';
                        $linked_faq_result = Db::getInstance()->ExecuteS($sql_linked_faq);

                        if (isset($linked_faq_result) && count($linked_faq_result) > 0) {
                            foreach ($linked_faq_result as $key => $faq) {
                                $content_linked_faq = Tools::htmlentitiesDecodeUTF8($linked_faq_result[$key]['question']);
                                if (!empty($content_linked_faq)) {
                                    $linked_faq_result[$key]['question'] = $this->findShortcodeTitle($content_linked_faq);
                                    $linked_faq_result[$key]['answer'] = Tools::htmlentitiesDecodeUTF8($this->findShortcode($linked_faq_result[$key]['answer']));
                                    $faq_result[] = $linked_faq_result[$key];
                                } else {
                                    unset($linked_faq_result[$key]);
                                }
                            }
                        }
                    }
                }
                // #find faqs linked cat->blck->faq

                // find shortcode
                $desc_content = Tools::htmlentitiesDecodeUTF8($cat_result[0]['content']);
                $desc_content = $this->findShortcode($desc_content);
                // #find shortcode

                $category = new Category((int) Tools::getValue('id_category'));
                $category_name = $category->getName();

                $this->smarty->assign([
                    'add_category_description' => $desc_content,
                    'partial_number' => (int) Configuration::get('ADD_CATDESC_PN'),
                    'faq_result' => $faq_result,
                    'category_name' => $category_name,
                ]);

                if ((int) Configuration::get('ADD_CATDESC_SFP') > 0) {
                    if (Tools::getIsset('page') || Tools::getIsset('q')) {
                        $this->smarty->assign([
                            'add_cat_desc_wrap_set' => 1,
                        ]);
                    }
                }

                if ((int) Configuration::get('ADD_CATDESC_FAQ_SFP') > 0) {
                    if (Tools::getIsset('page') || Tools::getIsset('q')) {
                        $this->smarty->assign([
                            'add_cat_desc_wrap_faq_set' => 1,
                        ]);
                    }
                }

                return $this->fetch('module:addcatdesc/views/templates/front/addcatdesc-template.tpl');
            }
        } else {
            return false;
        }
    }

    public function hookdisplayHeader()
    {
        if (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'category') {
            return;
        }

        $id_shop = (int) $this->context->shop->id;
        $cat_result = Db::getInstance()->ExecuteS('
				SELECT count(*) as cnt
				FROM `' . _DB_PREFIX_ . 'add_category_text` t
				WHERE t.`id_category` = ' . (int) Tools::getValue('id_category') . ' AND t.`id_shop`=' . (int) $id_shop . ' AND t.`active` = 1');

        if (!empty($cat_result[0]['cnt'])) {
            $this->context->controller->addCSS($this->_path . 'views/css/addcatdesc.css');
            if ((int) Configuration::get('ADD_CATDESC_PN') > 0 || (int) Configuration::get('ADD_CATDESC_SFP') > 0 || (int) Configuration::get('ADD_CATDESC_FAQ_SFP') > 0) {
                $this->context->controller->addJS($this->_path . 'views/js/addcatdesc.js');
                $this->context->smarty->assign([
                    'partial_number' => (int) Configuration::get('ADD_CATDESC_PN'),
                    'partial_height' => (int) Configuration::get('ADD_CATDESC_PH'),
                    'partial_position' => (int) Configuration::get('ADD_CATDESC_PP'),
                    'partial_color' => $this->formatColor(Configuration::get('ADD_CATDESC_PC')),
                    'show_fp' => (int) Configuration::get('ADD_CATDESC_SFP'),
                    'show_faq_fp' => (int) Configuration::get('ADD_CATDESC_FAQ_SFP'),
                    'ajax_link' => $this->moduleLink(),
                    'ajax_action' => 'ajaxCallAddCatDesc',
                    'ajax_category' => (int) Tools::getValue('id_category'),
                ]);

                return $this->display(__FILE__, 'addcatdesc-header.tpl');
            }
        }
    }

    public function hookdisplayBackOfficeCategory($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.6') >= 0) {
            if (version_compare(_PS_VERSION_, '1.7.8') >= 0) {
                $id_cat = (int) Tools::getValue('id_category');
            } else {
                $id_cat = $params['request']->get('categoryId');
            }
            $this->context->smarty->assign([
                'new_tpl_version' => 1,
            ]);
        } else {
            $id_cat = (int) Tools::getValue('id_category');
        }

        // edit
        if ((int) $id_cat > 0) {
            $id_lang = (int) $this->context->language->id;
            $id_shop = (int) $this->context->shop->id;
            $currentIndex = AdminController::$currentIndex;

            $text_sql = 'SELECT t.`id_text`, t.`active` FROM `' . _DB_PREFIX_ . 'add_category_text` t WHERE t.`id_category` = ' . (int) $id_cat . ' AND t.`id_shop` = ' . (int) $id_shop;
            $res_text = Db::getInstance()->ExecuteS($text_sql);

            $languages = Language::getLanguages(false);
            $content = [];

            foreach ($languages as $language) {
                $res_sql = 'SELECT tl.`content` FROM `' . _DB_PREFIX_ . 'add_category_text` t LEFT JOIN `' . _DB_PREFIX_ . 'add_category_text_lang` tl on (t.`id_text`=tl.`id_text`) WHERE t.`id_category` = ' . (int) $id_cat . ' AND tl.`id_lang` = ' . (int) $language['id_lang'] . ' AND t.`id_shop` = ' . (int) $id_shop;

                $res = Db::getInstance()->ExecuteS($res_sql);
                if ($res) {
                    $content[$language['id_lang']]['iso_code'] = $language['iso_code'];
                    $content[$language['id_lang']]['content'] = Tools::htmlentitiesDecodeUTF8($res[0]['content']);
                } else {
                    $content[$language['id_lang']]['iso_code'] = $language['iso_code'];
                    $content[$language['id_lang']]['content'] = '';
                }
            }

            $this->context->smarty->assign([
                'active_add' => empty($res_text) ? 0 : $res_text[0]['active'],
                'content' => $content,
                'contentLang' => $languages,
            ]);
        } else {
            // add
            $languages = Language::getLanguages(false);
            $content = [];

            foreach ($languages as $language) {
                $content[$language['id_lang']]['iso_code'] = $language['iso_code'];
                $content[$language['id_lang']]['content'] = '';
            }

            $this->context->smarty->assign([
                'active_add' => 1,
                'content' => $content,
                'contentLang' => $languages,
            ]);
        }

        return $this->display(__FILE__, 'addcatdesc-backoffice.tpl');
    }

    public function hookCategoryAddition($params)
    {
        if ((int) Tools::getValue('additional_description_hidden') == 1) {
            $id_shop = (int) $this->context->shop->id;
            $sql_count = 'SELECT p.`id_text` FROM `' . _DB_PREFIX_ . 'add_category_text` p WHERE p.`id_category` = ' . (int) $params['category']->id . ' AND p.`id_shop` = ' . (int) $id_shop;

            $count_results = Db::getInstance()->ExecuteS($sql_count);
            if (empty($count_results)) {
                // add
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_text` (`id_category`, `active`, `id_shop`) 
            				VALUES(' . (int) $params['category']->id . ', ' . (int) Tools::getValue('active_add') . ', ' . (int) $id_shop . ')');
                $id_text = Db::getInstance()->Insert_ID();
            } else {
                $id_text = $count_results[0]['id_text'];
                Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'add_category_text` SET `active` = ' . (int) Tools::getValue('active_add') . ' WHERE `id_text` = ' . (int) $id_text);
            }
            // clear data before insertion or update
            Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'add_category_text_lang`	WHERE `id_text` = ' . (int) $id_text);

            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_text_lang` (`id_text`, `id_lang`, `content`) VALUES(' . (int) $id_text . ', ' . (int) $language['id_lang'] . ', "' . pSQL(Tools::getValue('additional_description_' . $language['id_lang']), true) . '")');
            }
        }
    }

    public function hookCategoryUpdate($params)
    {
        if ((int) Tools::getValue('additional_description_hidden') == 1) {
            $id_shop = (int) $this->context->shop->id;
            $sql_count = 'SELECT p.`id_text` FROM `' . _DB_PREFIX_ . 'add_category_text` p WHERE p.`id_category` = ' . (int) $params['category']->id . ' AND p.`id_shop` = ' . (int) $id_shop;
            $count_results = Db::getInstance()->ExecuteS($sql_count);

            if (empty($count_results)) {
                // add
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_text` (`id_category`, `active`, `id_shop`) VALUES(' . (int) $params['category']->id . ', ' . (int) Tools::getValue('active_add') . ', ' . (int) $id_shop . ')');
                $id_text = Db::getInstance()->Insert_ID();
            } else {
                $id_text = $count_results[0]['id_text'];
                Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'add_category_text` SET `active` = ' . (int) Tools::getValue('active_add') . ' WHERE `id_text` = ' . (int) $id_text);
            }
            // clear data before insertion or update
            Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'add_category_text_lang`	WHERE `id_text` = ' . (int) $id_text);

            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_text_lang` (`id_text`, `id_lang`, `content`) VALUES(' . (int) $id_text . ', ' . (int) $language['id_lang'] . ', "' . pSQL(Tools::getValue('additional_description_' . $language['id_lang']), true) . '")');
            }
        }
    }

    public function initToolbar()
    {
        $current_index = AdminController::$currentIndex;
        $token = Tools::getAdminTokenLite('AdminModules');

        $back = Tools::safeOutput(Tools::getValue('back', ''));

        if (!isset($back) || empty($back)) {
            $back = $current_index . '&amp;configure=' . $this->name . '&token=' . $token;
        }

        switch ($this->_display) {
            case 'add':
                $this->toolbar_btn['cancel'] = [
                    'href' => $back,
                    'desc' => $this->l('Cancel'),
                    'no_free_cat' => 1,
                ];
                break;
            case 'edit':
                $this->toolbar_btn['cancel'] = [
                    'href' => $back,
                    'desc' => $this->l('Cancel'),
                    'no_free_cat' => 1,
                ];
                break;
            case 'index':
                $this->toolbar_btn['new'] = [
                    'href' => $current_index . '&amp;configure=' . $this->name . '&amp;token=' . $token . '&amp;addCategoryText',
                    'desc' => $this->l('Add new description'),
                    'free_cat' => $this->getFreeCategories(),
                ];
                break;
            default:
                break;
        }

        switch ($this->display_blck_tmp) {
            case 'editTmpBlock':
                $this->toolbar_btn['new'] = [
                    'href' => $current_index . '&amp;configure=' . $this->name . '&amp;token=' . $token . '&amp;addCategoryText',
                    'desc' => $this->l('Add new description'),
                ];
                break;
            case 'addTmpBlock':
                $this->toolbar_btn['new'] = [
                    'href' => $current_index . '&amp;configure=' . $this->name . '&amp;token=' . $token . '&amp;addCategoryText',
                    'desc' => $this->l('Add new description'),
                ];
                break;
            default:
                break;
        }

        switch ($this->display_faq_tmp) {
            case 'editFaq':
                $this->toolbar_btn['cancel'] = [
                    'href' => $current_index . '&amp;configure=' . $this->name . '&amp;token=' . $token . '&amp;addCategoryText',
                    'desc' => $this->l('Add new description'),
                ];
                break;
            case 'addFaq':
                $this->toolbar_btn['cancel'] = [
                    'href' => $current_index . '&amp;configure=' . $this->name . '&amp;token=' . $token . '&amp;addCategoryText',
                    'desc' => $this->l('Add new description'),
                ];
                break;
            default:
                break;
        }

        return $this->toolbar_btn;
    }

    /**
    # Author:     Jarrod Oberto
    # Date:       09-05-11
    # Purpose:    Determine color method passed in and return color as RGB
    # Param in:   (mixed) $value: (array) Could be an array of RGB
    #               (str) Could be hex #ffffff or #fff, fff, ffffff
     * @param mixed $value
     */
    public function formatColor($value)
    {
        $rgbArray = [];

        // *** If it's an array it should be R, G, B
        if (is_array($value)) {
            if (key($value) == 0 && count($value) == 3) {
                $rgbArray['r'] = $value[0];
                $rgbArray['g'] = $value[1];
                $rgbArray['b'] = $value[2];
            } else {
                $rgbArray = $value;
            }
        } elseif (Tools::strtolower($value) == 'transparent') {
            $rgbArray = [
                'r' => 255,
                'g' => 255,
                'b' => 255,
                'a' => 127,
            ];
        } else {
            // *** ...Else it should be hex. Let's make it RGB
            $rgbArray = $this->hex2dec($value);
        }

        return $rgbArray;
    }

    /**
     * # Purpose:  Convert #hex color to RGB
     *
     * @param mixed $hex
     */
    public function hex2dec($hex)
    {
        $color = str_replace('#', '', $hex);

        if (Tools::strlen($color) == 3) {
            $color = $color . $color;
        }

        $rgb = [
          'r' => hexdec(Tools::substr($color, 0, 2)),
          'g' => hexdec(Tools::substr($color, 2, 2)),
          'b' => hexdec(Tools::substr($color, 4, 2)),
        ];
        $res = implode(',', $rgb);

        return $res;
    }

    /**
     * # Purpose:  Find shortcode in content
     *
     * @param mixed $content
     */
    public function findShortcode($content)
    {
        if ((int) Configuration::get('ADD_CATDESC_SSC') > 0) {
            $id_lang = (int) $this->context->language->id;
            $id_shop = (int) $this->context->shop->id;

            // $var_name_prefix = add_
            // [#add_product:ID] => product
            // ID of the product
            preg_match_all('/\[#' . $this->var_name_prefix . 'product:[0-9]*\]/', $content, $matches);
            foreach ($matches[0] as $key => $value) {
                $res = (int) preg_replace('/[^0-9.]/', '', $value);
                if ($res > 0) {
                    $tmp = '[#add_product:' . $res . ']';
                    $pos = strpos($content, $tmp);
                    if ($pos !== false) {
                        $link = Context::getContext()->link->getProductLink($res);
                        if (!empty($link)) {
                            $product_link = "<a class='add-cat-prod-link' href='" . $link . "'>" . Product::getProductName($res) . "</a>";
                            $content = substr_replace($content, $product_link, $pos, Tools::strlen($tmp));
                        }
                    }
                }
            }

            // [#add_product_cat:ID-cat:NUM-products:PARAM:ORDER:VIEW] => products
            // ID-cat - ID of the category - if ID = 0 - set the current category ID
            // NUM-products - Number of products in this category
            // PARAM - Parameter of products in this category - PRICE, DATE_ADD, DATE_UPD, ID_PRODUCT
            // ORDER - ORDER of products in this category
            // VIEW - view of product block  = 1 - through comma, 2 - ul list

            preg_match_all('/\[#' . $this->var_name_prefix . 'product_cat:[0-9]*:[0-9]*:[a-zA-Z_]+:[a-zA-Z]+:[0-9]*\]/', $content, $matches);

            foreach ($matches[0] as $key => $value) {
                if (substr($value, 0, 18) == '[#add_product_cat:') {
                    $substr = substr($value, 18);
                    $pieces = explode(':', $substr);
                    $id_cat = (int) $pieces[0] == 0 ? (int) Tools::getValue('id_category') : (int) $pieces[0]; // id category
                    $limit = (int) $pieces[1] > 0 ? ' LIMIT 0,' . (int) $pieces[1] : ''; // number of products in category

                    if (strtoupper(trim($pieces[2])) == 'SALES') {
                        $orderBy = ' ORDER BY ps.`quantity`'; // parameter order by
                    } else {
                        $orderBy = trim($pieces[2]) != '' ? ' ORDER BY p.`' . trim($pieces[2]) . '`' : ''; // parameter order by
                    }

                    $orderWay = trim($pieces[3]) != '' ? trim($pieces[3]) : ''; // order way ASC/DESC
                    $view = (int) str_replace(']', '', trim($pieces[4])); // view

                    if ($limit != '' && $orderBy != '' && $orderWay != '') {
                        $sql = 'SELECT p.id_product, pl.`link_rewrite`, pl.`name`
                        FROM `' . _DB_PREFIX_ . 'category_product` cp
        				LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = cp.`id_product`
                        LEFT JOIN `' . _DB_PREFIX_ . 'product_sale` ps ON p.`id_product` = ps.`id_product`
        				' . Shop::addSqlAssociation('product', 'p') . Product::sqlStock('p', 0) . '
        				LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
        					ON (p.`id_product` = pl.`id_product`
        					AND pl.`id_lang` = ' . (int) $id_lang . Shop::addSqlRestrictionOnLang('pl') . ')
        				
        				WHERE product_shop.`id_shop` = ' . (int) $id_shop . '
        					AND cp.`id_category` = ' . (int) $id_cat
                            . ' AND product_shop.`active` = 1'
                            . ' AND product_shop.`visibility` IN ("both", "catalog")' . pSQL($orderBy) . ' ' . pSQL($orderWay) . $limit;

                        $sql_results = Db::getInstance()->ExecuteS($sql);

                        $prod_arr = [];
                        $product_links = '';

                        if (!empty($sql_results)) {
                            foreach ($sql_results as $key => $prod) {
                                if ($view == 1) {
                                    $prod_arr[] = "<a class='add-cat-prod-link' href='" . Context::getContext()->link->getProductLink((int) $prod['id_product']) . "'>" . Product::getProductName((int) $prod['id_product']) . "</a>";
                                } else {
                                    $prod_arr[] = "<li><a class='add-cat-prod-link' href='" . Context::getContext()->link->getProductLink((int) $prod['id_product']) . "'>" . Product::getProductName((int) $prod['id_product']) . "</a></li>";
                                }
                            }
                            if (!empty($prod_arr)) {
                                $tmp = '[#add_product_cat:' . (int) $pieces[0] . ':' . (int) $pieces[1] . ':' . trim($pieces[2]) . ':' . trim($pieces[3]) . ':' . (int) $pieces[4] . ']';
                                $pos = strpos($content, $tmp);
                                if ($view == 1) {
                                    $product_links = implode(', ', $prod_arr);
                                } else {
                                    $product_links = '<ul>' . implode('', $prod_arr) . '</ul>';
                                }
                                $content = substr_replace($content, $product_links, $pos, Tools::strlen($tmp));
                            }
                        } else {
                            $tmp = '[#add_product_cat:' . (int) $id_cat . ':' . (int) $pieces[1] . ':' . trim($pieces[2]) . ':' . trim($pieces[3]) . ':' . (int) $pieces[4] . ']';
                            $pos = strpos($content, $tmp);
                            $content = substr_replace($content, $product_links, $pos, Tools::strlen($tmp));
                        }
                    }
                }
            }
            $content = $this->findCatName($content);
            $content = $this->findCatLink($content);
        }

        return $content;
    }

    /**
     * # Purpose:  Find shortcode in FAQ title
     *
     * @param mixed $title
     */
    public function findShortcodeTitle($title)
    {
        if ((int) Configuration::get('ADD_CATDESC_SSC') > 0) {
            $title = $this->findCatName($title);
            $title = $this->findCatLink($title);
        }
        return $title;
    }

    /**
     * # Purpose:  Find shortcode in FAQ title
     *
     * @param mixed $title
     */
    public function findCatName($title)
    {
        // $var_name_prefix = add_
        // [#add_categoryname:ID] => category name
        // ID of the category
        preg_match_all('/\[#' . $this->var_name_prefix . 'categoryname:[0-9]*\]/', $title, $matches);
        foreach ($matches[0] as $key => $value) {
            $res = (int) preg_replace('/[^0-9.]/', '', $value);
            if ($res >= 0) {
                $tmp = '[#add_categoryname:' . $res . ']';
                $pos = strpos($title, $tmp);

                if ($pos !== false) {
                    if ($res > 0) {
                        $category = new Category($res);
                    } else {
                        $category = new Category((int) Tools::getValue('id_category'));
                    }
                    $cat_name = $category->getName();
                    if (!empty($cat_name)) {
                        $title = substr_replace($title, $cat_name, $pos, Tools::strlen($tmp));
                    }
                }
            }
        }
        return $title;
    }

    /**
     * # Purpose:  Find shortcode in FAQ title
     *
     * @param mixed $title
     */
    public function findCatLink($title)
    {
        // $var_name_prefix = add_
        // [#add_categorylink:ID] => category link
        // ID of the category
        preg_match_all('/\[#' . $this->var_name_prefix . 'categorylink:[0-9]*\]/', $title, $matches);
        foreach ($matches[0] as $key => $value) {
            $res = (int) preg_replace('/[^0-9.]/', '', $value);
            if ($res >= 0) {
                $tmp = '[#add_categorylink:' . $res . ']';
                $pos = strpos($title, $tmp);

                if ($pos !== false) {
                    if ($res > 0) {
                        $category = new Category($res);
                        $link = Context::getContext()->link->getCategoryLink($res);
                    } else {
                        $category = new Category((int) Tools::getValue('id_category'));
                        $link = Context::getContext()->link->getCategoryLink((int) Tools::getValue('id_category'));
                    }

                    if (!empty($link)) {
                        $cat_name = $category->getName();
                        $category_link = "<a class='add-category-link' href='" . $link . "'>" . $cat_name . "</a>";
                        $title = substr_replace($title, $category_link, $pos, Tools::strlen($tmp));
                    }
                }
            }
        }
        return $title;
    }
}
