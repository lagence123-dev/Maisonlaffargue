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

class BlockblogGalleryModuleFrontController extends ModuleFrontController
{
    public $php_self;
    private $_name_module = "blockblog";


    public $display_column_left = false;
    public $display_column_right = false;

    public function __construct()
    {
        parent::__construct();
        if(Configuration::get($this->_name_module.'sidebar_posblog_gallery_alias')==2)
            $this->display_column_right=true;
        if(Configuration::get($this->_name_module.'sidebar_posblog_gallery_alias')==1)
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
        $this->php_self = 'module-'.$name_module.'-gallery';
        parent::initContent();



        $gallery_on = Configuration::get($name_module.'gallery_on');

        if (!$gallery_on)
            Tools::redirect('index.php');


        include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();


        include_once(_PS_MODULE_DIR_.$this->_name_module.'/blockblog.php');
        $obj_blockblog = new blockblog();

        $_data_translate = $obj_blockblog->translateItems();

        $obj_blockblog->setSEOUrls();
        $obj_blockblog->setGallerySettings();


        $cookie = Context::getContext()->cookie;
        $id_lang = (int)($cookie->id_lang);
        $title_allcat = Configuration::get($name_module . 'title_gallery_' . $id_lang);
        $desc_allcat = Configuration::get($name_module . 'desc_gallery_' . $id_lang);
        $key_allcat = Configuration::get($name_module . 'key_gallery_' . $id_lang);

        $title_allcat = Tools::strlen($title_allcat)>0?$title_allcat:$_data_translate['meta_title_gallery'];
        $desc_allcat = Tools::strlen($desc_allcat)>0?$desc_allcat:$_data_translate['meta_description_gallery'];
        $key_allcat = Tools::strlen($key_allcat)>0?$key_allcat:$_data_translate['meta_keywords_gallery'];

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title_allcat;
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $desc_allcat;
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $key_allcat;
        }

        $this->context->smarty->assign('meta_title' , $title_allcat);
        $this->context->smarty->assign('meta_description' , $desc_allcat);
        $this->context->smarty->assign('meta_keywords' , $key_allcat);




        $obj_blockblog->setControllersSettings();


        $p = (int)Tools::getValue('p');
        $step = (int) Configuration::get($name_module.'gallery_per_page');

        $start = (int)(($p - 1)*$step);
        if($start<0)
            $start = 0;


        $_data = $obj_blog->getGallery(array('start'=>$start,'step'=>$step));


        $page_translate = $_data_translate['page'];

        $paging = $obj_blog->PageNav($start,$_data['count_all'],$step,array('gallery'=>1,'page'=>$page_translate));



        $this->context->smarty->assign(
            array(
                $name_module.'gallerypage' => $_data['gallery'],
                'count_all' => $_data['count_all'],
                'paging' => $paging,
                $name_module.'is_ajax_gallery'=>0,
            )
        );


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:'.$name_module.'/views/templates/front/gallery17.tpl');
        }else {

            $this->setTemplate('gallery.tpl');
        }




    }
}