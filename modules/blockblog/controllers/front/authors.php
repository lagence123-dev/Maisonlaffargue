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

class BlockblogAuthorsModuleFrontController extends ModuleFrontController
{
    public $php_self;

    private $_name_module = "blockblog";
    public $display_column_left = false;
    public $display_column_right = false;

    public function __construct()
    {
        parent::__construct();
        if(Configuration::get($this->_name_module.'sidebar_posblog_authors_alias')==2)
            $this->display_column_right=true;
        if(Configuration::get($this->_name_module.'sidebar_posblog_authors_alias')==1)
            $this->display_column_left =true;

    }

    public function init()
    {
       parent::init();
    }

    public function setMedia()
    {
        parent::setMedia();

        $name_module = 'blockblog';
        if(Configuration::get($name_module.'d_eff_shopu') != "disable_all_effects") {

            $this->context->controller->addJs(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/js/wow.js');
            $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/css/animate.css');
        }
    }


    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $name_module = 'blockblog';
        $this->php_self = 'module-'.$name_module.'-authors';

        parent::initContent();




        $ava_on = (int)Configuration::get($name_module.'ava_on');
        if (!$ava_on)
            Tools::redirect('index.php');

        include_once(_PS_MODULE_DIR_.$name_module.'/blockblog.php');
        $obj_blockblog = new blockblog();


        $obj_blockblog->setControllersSettings();

        $obj_blockblog->setSEOUrls();


        $_data_translate = $obj_blockblog->translateItems();


        $cookie = Context::getContext()->cookie;
        $id_lang = (int)($cookie->id_lang);
        $title_allauthors = Configuration::get($name_module . 'title_allauthors_' . $id_lang);
        $desc_allauthors = Configuration::get($name_module . 'desc_allauthors_' . $id_lang);
        $key_allauthors = Configuration::get($name_module . 'key_allauthors_' . $id_lang);

        $title_allauthors = Tools::strlen($title_allauthors)>0?$title_allauthors:$_data_translate['meta_title_authors'];
        $desc_allauthors = Tools::strlen($desc_allauthors)>0?$desc_allauthors:$_data_translate['meta_description_authors'];
        $key_allauthors = Tools::strlen($key_allauthors)>0?$key_allauthors:$_data_translate['meta_keywords_authors'];


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title_allauthors;
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $desc_allauthors;
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $key_allauthors;
        }

        $this->context->smarty->assign('meta_title' , $title_allauthors);
        $this->context->smarty->assign('meta_description' , $desc_allauthors);
        $this->context->smarty->assign('meta_keywords' , $key_allauthors);


        include_once(_PS_MODULE_DIR_.$name_module.'/classes/userprofileblockblog.class.php');
        $obj = new userprofileblockblog();


        $step = (int)Configuration::get($name_module.'perpage_authors');


        $start = (int)Tools::getValue('p');
        if($start<0)
            $start = 0;


        $search = Tools::getValue("search");
        $is_search = 0;

        ### search ###
        if(Tools::strlen($search)>0){
            $is_search = 1;

        }
        $this->context->smarty->assign($name_module.'is_search', $is_search);
        $this->context->smarty->assign($name_module.'search', $search);

        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $this->context->smarty->assign($name_module.'is16' , 1);
        } else {
            $this->context->smarty->assign($name_module.'is16' , 0);
        }

        $info_customers = $obj->getShoppersList(array('start' => $start,'step'=>$step,'is_search'=>$is_search,'search'=>$search));


        include_once(_PS_MODULE_DIR_.$name_module.'/classes/blogspm.class.php');
        $obj_blogspm = new blogspm();

        $data_urls = $obj_blogspm->getSEOURLs();
        $users_url = $data_urls['authors_url'];

        $paging = $obj_blogspm->PageNav($start,$info_customers['data_count_customers'],$step,array('is_authors'=>1, 'is_search_authors'=>$is_search,'search_authors'=>$search,));


        $this->context->smarty->assign($name_module.'d_eff_shopu', Configuration::get($name_module.'d_eff_shopu'));

        $this->context->smarty->assign(array(
            $name_module.'customers' => $info_customers['customers'],
            $name_module.'data_count_customers' => $info_customers['data_count_customers'],
            $name_module.'paging' => $paging,
            $name_module.'users_url'=>$users_url,

        ));




        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:' . $name_module . '/views/templates/front/authors17.tpl');
        }else {
            $this->setTemplate('authors.tpl');
        }


    }
}