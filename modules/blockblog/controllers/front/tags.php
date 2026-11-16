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

class BlockblogTagsModuleFrontController extends ModuleFrontController
{
    public $php_self;
    private $_name_module = "blockblog";

    public $display_column_left = false;
    public $display_column_right = false;

    public function __construct()
    {
        parent::__construct();
        if(Configuration::get($this->_name_module.'sidebar_posblog_alltags_alias')==2)
            $this->display_column_right=true;
        if(Configuration::get($this->_name_module.'sidebar_posblog_alltags_alias')==1)
            $this->display_column_left =true;

    }

    public function init()
    {

        parent::init();
    }

    public function setMedia()
    {
        parent::setMedia();



    }



    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {

        $name_module = "blockblog";

        $this->php_self = 'module-'.$name_module.'-tags';
        parent::initContent();



        include_once(_PS_MODULE_DIR_.$name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();


        include_once(_PS_MODULE_DIR_.$name_module.'/blockblog.php');
        $obj_blockblog = new blockblog();

        $obj_blockblog->setSEOUrls();




        $_data_translate = $obj_blockblog->translateItems();



        $cookie = Context::getContext()->cookie;
        $id_lang = (int)($cookie->id_lang);
        $title_alltags = Configuration::get($name_module . 'title_alltags_' . $id_lang);
        $desc_alltags = Configuration::get($name_module . 'desc_alltags_' . $id_lang);
        $key_alltags = Configuration::get($name_module . 'key_alltags_' . $id_lang);

        $title_alltags = Tools::strlen($title_alltags)>0?$title_alltags:$_data_translate['meta_title_tags'];
        $desc_alltags = Tools::strlen($desc_alltags)>0?$desc_alltags:$_data_translate['meta_description_tags'];
        $key_alltags = Tools::strlen($key_alltags)>0?$key_alltags:$_data_translate['meta_keywords_tags'];



        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title_alltags;
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $desc_alltags;
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $key_alltags;
        }

        $this->context->smarty->assign('meta_title', $title_alltags);
        $this->context->smarty->assign('meta_description', $desc_alltags);
        $this->context->smarty->assign('meta_keywords', $key_alltags);

        $tags_page = $obj_blog->getTags();


        $obj_blockblog->setControllersSettings();


        $this->context->smarty->assign(
            array($name_module.'tags_page' => $tags_page)
        );


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:'.$name_module.'/views/templates/front/tags17.tpl');
        }else {
            $this->setTemplate('tags.tpl');
        }

    }
}