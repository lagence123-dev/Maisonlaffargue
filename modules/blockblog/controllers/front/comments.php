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

class BlockblogCommentsModuleFrontController extends ModuleFrontController
{
    public $php_self;
    private $_name_module = "blockblog";

    public $display_column_left = false;
    public $display_column_right = false;

    public function __construct()
    {
        parent::__construct();
        if(Configuration::get($this->_name_module.'sidebar_posblog_allcom_alias')==2)
            $this->display_column_right=true;
        if(Configuration::get($this->_name_module.'sidebar_posblog_allcom_alias')==1)
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
        if(Configuration::get($name_module.'blog_com_effect') != "disable_all_effects") {


            $this->context->controller->addJs(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/js/wow.js');
            $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/css/animate.css');
        }
   }

	
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{

        $name_module = "blockblog";

        $this->php_self = 'module-'.$name_module.'-comments';

		parent::initContent();




        include_once(_PS_MODULE_DIR_.$name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();


        include_once(_PS_MODULE_DIR_.$name_module.'/blockblog.php');
        $obj_blockblog = new blockblog();

        $obj_blockblog->setSEOUrls();

        $_data_translate = $obj_blockblog->translateItems();


        $cookie = Context::getContext()->cookie;
        $id_lang = (int)($cookie->id_lang);
        $title_allcom = Configuration::get($name_module . 'title_allcom_' . $id_lang);
        $desc_allcom = Configuration::get($name_module . 'desc_allcom_' . $id_lang);
        $key_allcom = Configuration::get($name_module . 'key_allcom_' . $id_lang);

        $title_allcom = Tools::strlen($title_allcom)>0?$title_allcom:$_data_translate['meta_title_all_comments'];
        $desc_allcom = Tools::strlen($desc_allcom)>0?$desc_allcom:$_data_translate['meta_description_all_comments'];
        $key_allcom = Tools::strlen($key_allcom)>0?$key_allcom:$_data_translate['meta_keywords_all_comments'];


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title_allcom;
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $desc_allcom;
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $key_allcom;
        }

        $this->context->smarty->assign('meta_title' , $title_allcom);
        $this->context->smarty->assign('meta_description' , $desc_allcom);
        $this->context->smarty->assign('meta_keywords' , $key_allcom);



        $obj_blockblog->setControllersSettings();



        $p = (int)Tools::getValue('p');
        $step = (int) Configuration::get($name_module.'perpage_com');

        $start = (int)(($p - 1)*$step);
        if($start<0)
            $start = 0;

        $_data_com = $obj_blog->getLastComments(array('is_page'=>1,'step'=>$step,'start'=>$start));

        //echo "<pre>"; var_dump($_data_com);exit;

        $page_translate = $_data_translate['page'];
        $paging = $obj_blog->PageNav($start,$_data_com['count_all'],$step,
                                    array('all_comments'=>1,'page'=>$page_translate,)
        );



        
        $this->context->smarty->assign(array('comments' => $_data_com['comments'],
                                            'count_all' => $_data_com['count_all'],
                                            'paging' => $paging,
            )
        );



        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:'.$name_module.'/views/templates/front/all-comments17.tpl');
        }else {
            $this->setTemplate('all-comments.tpl');
        }


    }
}