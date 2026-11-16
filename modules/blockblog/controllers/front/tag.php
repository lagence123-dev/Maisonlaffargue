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

class BlockblogTagModuleFrontController extends ModuleFrontController
{
    public $php_self;
    private $_name_module = "blockblog";


    public $display_column_left = false;
    public $display_column_right = false;

    public function __construct()
    {
        parent::__construct();
        if(Configuration::get($this->_name_module.'sidebar_posblog_tag_alias')==2)
            $this->display_column_right=true;
        if(Configuration::get($this->_name_module.'sidebar_posblog_tag_alias')==1)
            $this->display_column_left =true;

    }


    public function init()
    {

        parent::init();
    }

    public function setMedia()
    {
        parent::setMedia();


        $name_module = $this->_name_module;
        if(Configuration::get($name_module.'blog_post_effect') != "disable_all_effects") {

            $this->context->controller->addJs(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/js/wow.js');
            $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/css/animate.css');
        }

        $this->context->controller->addJs(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/js/owl.carousel.js');
        $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/css/owl.carousel.css');
        $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/css/owl.theme.default.css');
    }



    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {

        $name_module = "blockblog";

        $this->php_self = 'module-'.$name_module.'-tag';
        parent::initContent();

        $tag_id = Tools::getValue('tag_id');



        include_once(_PS_MODULE_DIR_.$name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();


        include_once(_PS_MODULE_DIR_.$name_module.'/blockblog.php');
        $obj_blockblog = new blockblog();

        $obj_blockblog->setSEOUrls();


        $_info_tag = $obj_blog->isExistsTag(array('tag_id' => $tag_id));





        if(empty($_info_tag)){
            $data_url = $obj_blog->getSEOURLs();
            $tags_url = $data_url['tags_url'];

            Tools::redirect($tags_url);
        }

        $_data_translate = $obj_blockblog->translateItems();

        $title = $_data_translate['meta_title_tag'].' '.$tag_id;
        $seo_description = $_data_translate['meta_description_tag'].' '.$tag_id;
        $seo_keywords = $_data_translate['meta_keywords_tag'].' '.$tag_id;


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title;
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $seo_description;
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $seo_keywords;
        }

        $this->context->smarty->assign('meta_title', $title);
        $this->context->smarty->assign('meta_description', $seo_description);
        $this->context->smarty->assign('meta_keywords', $seo_keywords);


        $obj_blockblog->setControllersSettings();




        $p = (int)Tools::getValue('p');
        $step = (int) Configuration::get($name_module.'perpage_posts');

        $start = (int)(($p - 1)*$step);
        if($start<0)
            $start = 0;

        $_data = $obj_blog->getPostsForTag(array('start'=>$start,'step'=>$step,'tag_id'=>$tag_id));



        $page_translate = $_data_translate['page'];
        $paging = $obj_blog->PageNav($start,$_data['count_all'],$step,array('tag_id'=>$tag_id,'page'=>$page_translate));

        // strip tags for content
        foreach($_data['posts'] as $_k => $_item){
            $_data['posts'][$_k]['content'] = strip_tags($_item['content']);

        }





        $this->context->smarty->assign(array('posts' => $_data['posts'],
                'count_all' => $_data['count_all'],
                'paging' => $paging,
                $name_module.'tag_id'=>$tag_id
            )
        );

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:'.$name_module.'/views/templates/front/tag17.tpl');
        }else {
            $this->setTemplate('tag.tpl');
        }

    }
}