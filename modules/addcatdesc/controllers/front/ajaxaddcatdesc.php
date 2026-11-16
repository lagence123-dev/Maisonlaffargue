<?php
/**
* addcatdesc module controller file.
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

class AddCatDescAjaxAddCatDescModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        // ajax call from settings of the module
        if (!Tools::isSubmit('secure_key') || Tools::getValue('secure_key') != $this->module->secure_key || !Tools::getValue('action')) {
            exit;
        }

        if (Tools::getValue('action') == 'ajaxCallAddCatDesc' && (int) Tools::getValue('id_category') > 2) {
            if ((int) Configuration::get('ADD_CATDESC_SFP') == 1 || (int) Configuration::get('ADD_CATDESC_FAQ_SFP') == 1) {
                $id_shop = (int) $this->context->shop->id;
                $cat_result = Db::getInstance()->ExecuteS('
    				SELECT t.`id_category`, tl.`content`
    				FROM `' . _DB_PREFIX_ . 'add_category_text` t
    				LEFT JOIN `' . _DB_PREFIX_ . 'add_category_text_lang` tl on (t.`id_text` = tl.`id_text`)
    				WHERE tl.`id_lang` = ' . (int) $this->context->language->id . ' and t.`id_category` = ' . (int) Tools::getValue('id_category') . ' AND t.`id_shop`=' . (int) $id_shop . ' AND t.`active` = 1');
                if (count($cat_result) > 0) {
                    // description processing
                    if ((int) Configuration::get('ADD_CATDESC_SFP') == 1 && (int) Tools::getValue('desc') == 1) {
                        // find shortcode for content
                        $desc_content = Tools::htmlentitiesDecodeUTF8($cat_result[0]['content']);
                        $desc_content = $this->module->findShortcode($desc_content);
                        // #find shortcode

                        $this->context->smarty->assign([
                            'add_category_description' => Tools::htmlentitiesDecodeUTF8($desc_content),
                            'partial_number' => (int) Configuration::get('ADD_CATDESC_PN'),
                        ]);
                        return $this->setTemplate('module:addcatdesc/views/templates/front/addcatdesc-template-content.tpl');
                    }
                    // #description processing
                    // faq processing
                    if ((int) Configuration::get('ADD_CATDESC_FAQ_SFP') == 1 && (int) Tools::getValue('faq') == 1) {
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
                                    $faq_result[$key]['question'] = $this->module->findShortcodeTitle($content_faq);
                                    $faq_result[$key]['answer'] = Tools::htmlentitiesDecodeUTF8($this->module->findShortcode($faq_result[$key]['answer']));
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
                                            $linked_faq_result[$key]['question'] = $this->module->findShortcodeTitle($content_linked_faq);
                                            $linked_faq_result[$key]['answer'] = Tools::htmlentitiesDecodeUTF8($this->module->findShortcode($linked_faq_result[$key]['answer']));
                                            $faq_result[] = $linked_faq_result[$key];
                                        } else {
                                            unset($linked_faq_result[$key]);
                                        }
                                    }
                                }
                            }
                        }
                        // #find faqs linked cat->blck->faq

                        $category = new Category((int) Tools::getValue('id_category'));
                        $category_name = $category->getName();

                        $this->context->smarty->assign([
                            'faq_result' => $faq_result,
                            'category_name' => $category_name,
                        ]);

                        return $this->setTemplate('module:addcatdesc/views/templates/front/addcatdesc-template-faq-content.tpl');
                    }
                    // #faq processing
                }
            }
        }
    }
}
