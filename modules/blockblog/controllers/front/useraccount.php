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

class BlockblogUseraccountModuleFrontController extends ModuleFrontController
{
    public $php_self;

    private $_name_module = "blockblog";
    public $display_column_left = false;
    public $display_column_right = false;

    public function __construct()
    {
        parent::__construct();
        if(Configuration::get($this->_name_module.'sidebar_posblog_mava_alias')==2)
            $this->display_column_right=true;
        if(Configuration::get($this->_name_module.'sidebar_posblog_mava_alias')==1)
            $this->display_column_left =true;

    }

    public function init()
    {

        parent::init();
    }

    public function setMedia()
    {
        parent::setMedia();

        $module_name = 'blockblog';

        $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $module_name . '/views/css/blogmyaccount.css');
    }


    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $name_module = 'blockblog';
        $this->php_self = 'module-'.$name_module.'-useraccount';


        parent::initContent();




        $ava_on = Configuration::get($name_module.'ava_on');

        if (!$ava_on)
            Tools::redirect('index.php');

        $cookie = Context::getContext()->cookie;

        $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
        if (!$id_customer)
            Tools::redirect('authentication.php');


        // loyality program //
        include_once(_PS_MODULE_DIR_ . $name_module . '/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        $loyality->saveLoyalityAction(array('id_loyalty_status' => 1, 'id_customer' => $id_customer, 'type' => 'loyality_user_my_show'));
        // loyality program //

        include_once(_PS_MODULE_DIR_.$name_module.'/classes/userprofileblockblog.class.php');
        $obj = new userprofileblockblog();

        include_once(_PS_MODULE_DIR_.$name_module.'/blockblog.php');
        $obj_blockblog = new blockblog();
        $_data_translate = $obj_blockblog->translateItems();

        $obj_blockblog->setSEOUrls();

        $obj_blockblog->setControllersSettings();


        $info_customer = $obj->getCustomerInfo();
        $avatar_thumb = $info_customer['avatar_thumb'];
        $exist_avatar = $info_customer['exist_avatar'];
        $is_show = $info_customer['is_show'];
        $info = $info_customer['info'];
        $status_author = $info_customer['status_author'];

        $is_demo = $obj_blockblog->is_demo;




        $id_lang = (int)($cookie->id_lang);
        $title_myava = Configuration::get($name_module . 'title_myava_' . $id_lang);
        $desc_myava = Configuration::get($name_module . 'desc_myava_' . $id_lang);
        $key_myava = Configuration::get($name_module . 'key_myava_' . $id_lang);

        $title_myava = Tools::strlen($title_myava)>0?$title_myava:$_data_translate['meta_title_myaccount'];
        $desc_myava = Tools::strlen($desc_myava)>0?$desc_myava:$_data_translate['meta_description_myaccount'];
        $key_myava = Tools::strlen($key_myava)>0?$key_myava:$_data_translate['meta_keywords_myaccount'];


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title_myava;
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $desc_myava;
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $key_myava;
        }

        $this->context->smarty->assign('meta_title' , $title_myava);
        $this->context->smarty->assign('meta_description' , $desc_myava);
        $this->context->smarty->assign('meta_keywords' , $key_myava);

        $this->context->smarty->assign(array(
            $name_module.'avatar_thumb' => $avatar_thumb,
            $name_module.'exist_avatar' => $exist_avatar,
            $name_module.'is_show' => $is_show,
            $name_module.'info'=>$info,
            $name_module.'status_author'=>$status_author,

            $name_module.'ava_msg8'=>$_data_translate['ava_msg8'],
            $name_module.'ava_msg9'=>$_data_translate['ava_msg9'],
            $name_module.'is_demo'=>$is_demo,

        ));

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:' . $name_module . '/views/templates/front/useraccount17.tpl');
        }else {
            $this->setTemplate('useraccount.tpl');
        }


    }
}