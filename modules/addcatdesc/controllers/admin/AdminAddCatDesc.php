<?php
/**
* addcatdesc module admin controller file.
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

class AdminAddCatDescController extends ModuleAdminController
{
    public function initContent()
    {
        parent::initContent();

        // ajax call from settings of the module
        if (!Tools::isSubmit('secure_key') || Tools::getValue('secure_key') != $this->module->secure_key || !Tools::getValue('action')) {
            exit;
        }

        if (Tools::getValue('action') == 'activateAddCatDesc') {
            $settings = (int) Configuration::getGlobalValue('ADD_CATDESC_SET');
            if ($settings == 1) {
                echo $this->module->displayWarning($this->module->l('You activated this module in templates/catalog/listing/product-list.tpl file'));
                exit;
            } else {
                $version17 = version_compare(_PS_VERSION_, '1.7');

                if ($version17 >= 0) {
                    $searchString = '</section>';
                } else {
                    echo $this->module->displayError($this->module->l('Wrong version of prestashop!'));
                    exit;
                }

                $cat_tpl = _PS_THEME_DIR_ . 'templates/catalog/listing/product-list.tpl';
                $cat_bakup = _PS_THEME_DIR_ . 'templates/catalog/listing/product-list-acd.tpl.bak';
                $theme_file = Tools::file_get_contents($cat_tpl);

                if (strpos($theme_file, 'displayAddCatDesc') !== false) {
                    Configuration::updateGlobalValue('ADD_CATDESC_SET', 1);
                    echo $this->module->displayWarning($this->module->l('You activated this module in product-list.tpl file'));
                    exit;
                } elseif (strpos($theme_file, $searchString) !== false) {
                    if (!is_writable(_PS_THEME_DIR_ . 'templates/catalog/listing/')) {
                        echo $this->module->displayError($this->module->l('Cannot create the backup, your theme directory is not writable'));
                        exit;
                    }
                    file_put_contents($cat_bakup, $theme_file);
                    $last_pos = strripos($theme_file, $searchString);
                    $substrLast = Tools::substr($theme_file, $last_pos);
                    $substrFirst = Tools::substr($theme_file, 0, $last_pos);

                    $theme_substr = str_replace($searchString, ' <!-- Additional category description -->
			     {hook h=\'displayAddCatDesc\' mod=\'addcatdesc\'}' . "\n" . $searchString, $substrLast);

                    file_put_contents($cat_tpl, $substrFirst . $theme_substr);
                    Configuration::updateGlobalValue('ADD_CATDESC_SET', 1);
                    echo $this->module->displayConfirmation($this->module->l('Product-list.tpl was changed succesfully! The module has been activated!'));
                    exit;
                } else {
                    echo $this->module->displayError($this->module->l('Something went wrong - an error occurred!'));
                }
                exit;
            }
        } elseif (Tools::getValue('action') == 'deactivateAddCatDesc') {
            $settings = (int) Configuration::getGlobalValue('ADD_CATDESC_SET');
            if ($settings == 0) {
                echo $this->module->displayWarning($this->module->l('You deactivated this module in product-list.tpl file'));
                exit;
            } else {
                $version17 = version_compare(_PS_VERSION_, '1.7');

                if ($version17 >= 0) {
                    $cat_tpl = _PS_THEME_DIR_ . 'templates/catalog/listing/product-list.tpl';
                    $cat_bakup = _PS_THEME_DIR_ . 'templates/catalog/listing/product-list-acd.tpl.bak';

                    if (file_exists($cat_bakup)) {
                        if (unlink($cat_tpl)) {
                            if (rename($cat_bakup, $cat_tpl)) {
                                Configuration::updateGlobalValue('ADD_CATDESC_SET', 0);
                                echo $this->module->displayConfirmation($this->module->l('Product-list.tpl was changed succesfully! The module has been deactivated!'));
                                exit;
                            } else {
                                echo $this->module->displayError($this->module->l('Error: Cannot rename') . ' ' . $cat_bakup);
                                exit;
                            }
                        } else {
                            echo $this->module->displayError($this->module->l('Error: Cannot delete') . ' ' . $cat_tpl);
                            exit;
                        }
                    } else {
                        $theme_file = Tools::file_get_contents($cat_tpl);
                        if (strpos($theme_file, '{hook h=\'displayAddCatDesc\' mod=\'addcatdesc\'}') !== false) {
                            echo $this->module->displayError($this->module->l('Error: backup file') . ' "' . $cat_bakup . '" ' . $this->module->l('not found! Please deactivate module manually!'));
                            exit;
                        } else {
                            Configuration::updateGlobalValue('ADD_CATDESC_SET', 0);
                            echo $this->module->displayWarning($this->module->l('You deactivated this module in product-list.tpl file'));
                            exit;
                        }
                        echo $this->module->displayError($this->module->l('Error: backup file') . ' "' . $cat_bakup . '" ' . $this->module->l('not found! Please deactivate module manually!'));
                        exit;
                    }
                } else {
                    echo $this->module->displayWarning($this->module->l('Wrong version of prestashop!'));
                    exit;
                }

                echo $this->module->displayWarning($this->module->l('Cannot deactivate unactive functionality'));
                exit;
            }
        } elseif (Tools::getValue('action') == 'copyTmpToCat') {
            $id_blck = (int) Tools::getValue('id_blck');
            $id_text = (int) Tools::getValue('id_text');
            $languages = Language::getLanguages(false);

            if ($id_blck > 0 && $id_text > 0) {
                $sql_query = 'SELECT b.`id_faq` FROM `' . _DB_PREFIX_ . 'add_category_faq` b WHERE b.`active` = 1 AND b.`id_blck` = ' . (int) $id_blck . ' AND b.`linked_tmp` != 1';
                $res_bl = Db::getInstance()->executeS($sql_query);
                $num_rows = 0;

                if (isset($res_bl) && count($res_bl) > 0) {
                    foreach ($res_bl as $faq) {
                        $id_faq_tmp = $faq['id_faq'];

                        Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_faq` (`id_blck`, `id_text`) VALUES(0, ' . (int) $id_text . ')');
                        $id_faq = Db::getInstance()->Insert_ID();
                        $num_rows++;

                        foreach ($languages as $language) {
                            // make copy from template faq
                            Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'add_category_faq_lang` (`id_faq`, `id_lang`, `question`, `answer`) SELECT ' . (int) $id_faq . ', ' . (int) $language['id_lang'] . ', `question`, `answer` FROM `' . _DB_PREFIX_ . 'add_category_faq_lang` WHERE `id_lang` = ' . (int) $language['id_lang'] . ' AND `id_faq` = ' . (int) $id_faq_tmp);
                        }
                    }
                }

                $vars = [
                    'success' => true,
                    'warning' => '',
                    'num_rows' => $this->module->displayConfirmation((int) $num_rows . ' ' . $this->module->l('rows added.')),
                ];
            } else {
                $vars = [
                    'success' => false,
                    'warning' => $this->module->displayWarning($this->module->l('Wrong parameters for category')),
                ];
            }

            echo json_encode($vars);
            exit;
        }
    }
}
