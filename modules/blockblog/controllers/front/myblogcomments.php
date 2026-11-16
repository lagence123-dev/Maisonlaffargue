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

class BlockblogMyblogcommentsModuleFrontController extends ModuleFrontController
{
    public $php_self;

    private $_name_module = "blockblog";
    public $display_column_left = false;
    public $display_column_right = false;

    public function __construct()
    {
        parent::__construct();
        if(Configuration::get($this->_name_module.'sidebar_posblog_mbcom_alias')==2)
            $this->display_column_right=true;
        if(Configuration::get($this->_name_module.'sidebar_posblog_mbcom_alias')==1)
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

        if(Configuration::get($module_name.'d_eff_shopmycom') != "d_eff_shopmycom") {

            $this->context->controller->addJs(__PS_BASE_URI__ . 'modules/' . $module_name . '/views/js/wow.js');
            $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $module_name . '/views/css/animate.css');

        }

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$module_name.'/views/css/font-custom.min.css');


        // add rating functional only when edit blog comment page//
        $action = Tools::getValue('action');
        $id_item = (int)Tools::getValue('id');

        if($action == 'edit' && $id_item) {
            $this->context->controller->addJS(__PS_BASE_URI__ . 'modules/' . $module_name . '/views/js/bootstrap-rating-input.js');
        }
        // add rating functional only when edit blog comment page//

    }


    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $name_module = 'blockblog';
        $this->php_self = 'module-'.$name_module.'-myblogcomments';


        parent::initContent();



        $myblogcom_on = Configuration::get($name_module.'myblogcom_on');

        if (!$myblogcom_on)
            Tools::redirect('index.php');

        $cookie = Context::getContext()->cookie;

        $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
        if (!$id_customer)
            Tools::redirect('authentication.php');


        include_once(_PS_MODULE_DIR_.$name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();
        $is_show_customer_data = $obj_blog->getGroupPermissionsForBlog();
        $is_show_customer = $is_show_customer_data['is_show'];

        if(!$is_show_customer)
            Tools::redirect('index.php');




        include_once(_PS_MODULE_DIR_ . $name_module . '/blockblog.php');
        $obj_blockblog = new blockblog();
        $_data_translate = $obj_blockblog->translateItems();

        $obj_blockblog->setSEOUrls();

        $obj_blockblog->setControllersSettings();

        $action = Tools::getValue('action');
        $id_item = Tools::getValue('id');

        include_once(_PS_MODULE_DIR_.$name_module.'/classes/userprofileblockblog.class.php');
        $obj_userprofileblockblog = new userprofileblockblog();

        $info_customer = $obj_userprofileblockblog->getCustomerInfo();
        $status_author = $info_customer['status_author'];



        if($action == 'edit'){


            // cancel edit comment form
            $blockblogcancel = Tools::getValue('blockblogcancel');

            if($blockblogcancel || (($status_author == 0 || $status_author == 2) && $status_author !== "")) {
                $data_url = $obj_blog->getSEOURLs();
                $myblogcomments_url = $data_url['myblogcomments_url'];
                Tools::redirect($myblogcomments_url);
            }
            // cancel edit comment form


            // edit comment
            $_data = $obj_blog->getCommentItem(array('id' => $id_item,'is_myaccount_edit'=>1,'id_customer'=>$id_customer));


            $id_customer_related_to_comment = isset($_data['comments'][0]['id_customer']) ? (int)$_data['comments'][0]['id_customer'] : 0;
            //$id_customer_related_to_comment_status = isset($_data['comments'][0]['status']) ? (int)$_data['comments'][0]['status'] : 0;

            //var_dump(!in_array($id_customer_related_to_comment_status, array(3)));exit;

            if ($id_customer_related_to_comment == $id_customer) {


                ## update item ##
                $errors = array();

                $blockblogupdate = Tools::getValue('blockblogupdate');
                if($blockblogupdate){

                    $rating_blockblog = (int)Tools::getValue('rating-blockblog');
                    $comment_blockblog = Tools::getValue('comment-blockblog');



                    if(Tools::strlen($comment_blockblog)==0){
                        $errors[] = $_data_translate['msg_comm'];
                    }

                    if(!$rating_blockblog){
                        $errors[] = $_data_translate['msg_rate'];
                    }




                    if(count($errors)==0) {
                        $id_item = Tools::getValue('id');

                        $data_update = array(
                            'id' => $id_item,
                            'rating-blockblog' => $rating_blockblog,
                            'comment-blockblog' => $comment_blockblog,
                            'status' => 3
                        );

                        $obj_blog->updateCommentMyAccount($data_update);


                        $data_url = $obj_blog->getSEOURLs();
                        $myblogcomments_url = $data_url['myblogcomments_url'];
                        Tools::redirect($myblogcomments_url);
                    }

                }

                ## update item ##


                $title_mycom =$_data_translate['meta_title_myblogcommentsedit'];
                $desc_mycom = $_data_translate['meta_description_myblogcommentsedit'];
                $key_mycom = $_data_translate['meta_keywords_myblogcommentsedit'];


                if (version_compare(_PS_VERSION_, '1.7', '>')) {
                    $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title_mycom;
                    $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $desc_mycom;
                    $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $key_mycom;
                }

                $this->context->smarty->assign('meta_title', $title_mycom);
                $this->context->smarty->assign('meta_description', $desc_mycom);
                $this->context->smarty->assign('meta_keywords', $key_mycom);



                $data_avatar = $obj_userprofileblockblog->getAvatarForCustomer(array('id_customer' => $id_customer));
                $avatar = $data_avatar['avatar'];

                $name_customer = isset($_data['comments'][0]['name']) ? $_data['comments'][0]['name'] : '';
                $email_customer = isset($_data['comments'][0]['email']) ? $_data['comments'][0]['email'] : '';


                $comment_customer = isset($_data['comments'][0]['comment']) ? $_data['comments'][0]['comment'] : '';
                $comment_rating = isset($_data['comments'][0]['rating']) ? (int)$_data['comments'][0]['rating'] : 0;
                $comment_time_add = isset($_data['comments'][0]['time_add']) ? $_data['comments'][0]['time_add'] : '';


                $this->context->smarty->assign(
                    array(
                        $name_module.'c_avatar' => $avatar,
                        $name_module.'name_c' => $name_customer,
                        $name_module.'email_c' => $email_customer,


                        $name_module . 'comment' => $comment_customer,
                        $name_module . 'rating' => $comment_rating,
                        $name_module . 'time_add' => $comment_time_add,

                        $name_module . 'action' => $action,
                        $name_module . 'id_item' => $id_item,

                        $name_module . 'mycomments'=> $_data['comments'][0],

                        $name_module.'errors'=>$errors,


                    ));

                if (version_compare(_PS_VERSION_, '1.7', '>')) {
                    $this->setTemplate('module:' . $name_module . '/views/templates/front/myblogcommentsedit17.tpl');
                } else {
                    $this->setTemplate('myblogcommentsedit.tpl');
                }

            } else {
                $data_url = $obj_blog->getSEOURLs();
                $myblogcomments_url = $data_url['myblogcomments_url'];
                Tools::redirect($myblogcomments_url);
            }

            // edit comment
        } else {


            if((($status_author == 0 || $status_author == 2) && $status_author !== "") && $action == 'delete') {
                $data_url = $obj_blog->getSEOURLs();
                $myblogcomments_url = $data_url['myblogcomments_url'];
                Tools::redirect($myblogcomments_url);
                exit;
            }


            // delete comment
            if ($action == 'delete') {
                $_data = $obj_blog->getCommentItem(array('id' => $id_item));
                $id_customer_related_to_comment = isset($_data['comments'][0]['id_customer']) ? (int)$_data['comments'][0]['id_customer'] : 0;
                $id_customer_related_to_comment_status = isset($_data['comments'][0]['status']) ? (int)$_data['comments'][0]['status'] : 0;

                if ($id_customer_related_to_comment == $id_customer && !in_array($id_customer_related_to_comment_status, array(2))) {
                    $obj_blog->updateCommentStatus(array('id' => $id_item, 'status' => 2));
                } else {
                    $data_url = $obj_blog->getSEOURLs();
                    $myblogcomments_url = $data_url['myblogcomments_url'];
                    Tools::redirect($myblogcomments_url);
                }

            }
            // delete comment



            // display list of comments
            $id_lang = (int)($cookie->id_lang);
            $title_mycom = Configuration::get($name_module . 'title_mycom_' . $id_lang);
            $desc_mycom = Configuration::get($name_module . 'desc_mycom_' . $id_lang);
            $key_mycom = Configuration::get($name_module . 'key_mycom_' . $id_lang);

            $title_mycom = Tools::strlen($title_mycom) > 0 ? $title_mycom : $_data_translate['meta_title_myblogcomments'];
            $desc_mycom = Tools::strlen($desc_mycom) > 0 ? $desc_mycom : $_data_translate['meta_description_myblogcomments'];
            $key_mycom = Tools::strlen($key_mycom) > 0 ? $key_mycom : $_data_translate['meta_keywords_myblogcomments'];


            if (version_compare(_PS_VERSION_, '1.7', '>')) {
                $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title_mycom;
                $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $desc_mycom;
                $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $key_mycom;
            }

            $this->context->smarty->assign('meta_title', $title_mycom);
            $this->context->smarty->assign('meta_description', $desc_mycom);
            $this->context->smarty->assign('meta_keywords', $key_mycom);


            $step = (int)Configuration::get($name_module . 'perpage_mycom');
            $start = 0;

            $_data = $obj_blog->getComments(array('start' => $start, 'step' => $step, 'id_customer' => $id_customer, 'admin' => 3));


            $paging = $obj_blog->PageNav($start, $_data['count_all'], $step, array('is_myblogcom' => 1, 'author_id' => $id_customer));



            $this->context->smarty->assign(
                array(
                    $name_module . 'mycomments' => $_data['comments'],
                    $name_module . 'mycount_all' => $_data['count_all'],
                    $name_module . 'paging' => $paging,


                    $name_module . 'is_editmyblogcom' => Configuration::get($name_module . 'is_editmyblogcom'),
                    $name_module . 'is_delmyblogcom' => Configuration::get($name_module . 'is_delmyblogcom'),
                    $name_module . 'd_eff_shopmycom' => Configuration::get($name_module . 'd_eff_shopmycom'),

                    $name_module . 'action' => $action,
                    $name_module . 'id_item' => $id_item,

                    $name_module.'status_author'=>$status_author,

                ));


            if (version_compare(_PS_VERSION_, '1.7', '>')) {
                $this->setTemplate('module:' . $name_module . '/views/templates/front/myblogcomments17.tpl');
            } else {
                $this->setTemplate('myblogcomments.tpl');
            }
            // display list of comments

        }

    }
}