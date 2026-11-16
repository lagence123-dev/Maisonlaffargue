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

class BlockblogMyblogpostsModuleFrontController extends ModuleFrontController
{
    public $php_self;

    private $_name_module = "blockblog";
    public $display_column_left = false;
    public $display_column_right = false;

    public function __construct()
    {
        parent::__construct();
        if(Configuration::get($this->_name_module.'sidebar_posblog_mbposts_alias')==2)
            $this->display_column_right=true;
        if(Configuration::get($this->_name_module.'sidebar_posblog_mbposts_alias')==1)
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

        if(Configuration::get($module_name.'d_eff_shopmyposts') != "disable_all_effects") {

            $this->context->controller->addJs(__PS_BASE_URI__ . 'modules/' . $module_name . '/views/js/wow.js');
            $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $module_name . '/views/css/animate.css');

        }

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$module_name.'/views/css/font-custom.min.css');

        $action = Tools::getValue('action');
        $id_item = (int)Tools::getValue('id');

        if(($action == 'edit' && $id_item) || ($action == 'add')) {

            $this->context->controller->addJs(__PS_BASE_URI__.'modules/'.$module_name.'/views/js/image-files.js');

            // include simple wysiwyg editor
            $this->context->controller->addJs(__PS_BASE_URI__ . 'modules/' . $module_name . '/views/js/redactor.js');
            $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $module_name . '/views/css/redactor.min.css');
            // include simple wysiwyg editor
        }
    }


    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $name_module = 'blockblog';
        $this->php_self = 'module-'.$name_module.'-myblogposts';


        parent::initContent();



        $myblogposts_on = Configuration::get($name_module.'myblogposts_on');

        if (!$myblogposts_on)
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


        include_once(_PS_MODULE_DIR_.$name_module.'/blockblog.php');
        $obj_blockblog = new blockblog();
        $_data_translate = $obj_blockblog->translateItems();

        $obj_blockblog->setSEOUrls();

        $obj_blockblog->setControllersSettings();


        $action = Tools::getValue('action');
        $id_item = Tools::getValue('id');

        $id_lang = (int)$cookie->id_lang;

        $data_url = $obj_blog->getSEOURLs();
        $myblogposts_url = $data_url['myblogposts_url'];
        $ajax_url = $data_url['ajax_url'];


        include_once(_PS_MODULE_DIR_.$name_module.'/classes/userprofileblockblog.class.php');
        $obj_userprofileblockblog = new userprofileblockblog();

        $info_customer = $obj_userprofileblockblog->getCustomerInfo();
        $status_author = $info_customer['status_author'];



        if($action == 'edit' || $action == 'add'){


            // cancel edit post form
            $blockblogcancel = Tools::getValue('blockblogcancel');



            if($blockblogcancel || (($status_author == 0 || $status_author == 2) && $status_author !== "")) {
                Tools::redirect($myblogposts_url);
            }
            // cancel edit post form


            // edit post
            $_data = $obj_blog->getPostItem(array('id' => $id_item));
            $id_customer_related_to_post = isset($_data['post'][0]['author_id']) ? $_data['post'][0]['author_id'] : 0;
            $name_customer = isset($_data['post'][0]['customer_name']) ? $_data['post'][0]['customer_name'] :'admin' ;


            if ($id_customer_related_to_post == $id_customer || $action == 'add') {


                ## update item ##
                $errors = array();

                $blockblogupdate = Tools::getValue('blockblogupdate');
                if($blockblogupdate){

                    $post_title_blockblog = Tools::getValue('post-title-blockblog');
                    $post_content_blockblog = Tools::getValue('post-content-blockblog');



                    if(Tools::strlen($post_content_blockblog)==0){
                        $errors[] = $_data_translate['msg_content'];
                    }

                    if(Tools::strlen($post_title_blockblog)==0){
                        $errors[] = $_data_translate['msg_title'];
                    }

                    $files = isset($_FILES['post_image'])?$_FILES['post_image']:null;

                    if(!empty($files['name']))
                    {
                        if(!$files['error'])
                        {
                            $type_one = $files['type'];
                            $ext = explode("/",$type_one);

                            if(strpos('_'.$type_one,'image')<1)
                            {
                                $errors[] = $_data_translate['ava_msg8'];

                            }elseif(!in_array($ext[1],array('png','x-png','gif','jpg','jpeg','pjpeg'))) {
                                $errors[] = $_data_translate['ava_msg9'];
                            }
                        }
                    }


                    $ids_categories = Tools::getValue("ids_categories");

                    $post_images = Tools::getValue("post_images");

                    if(count($errors)==0) {
                        $id_item = Tools::getValue('id');


                        if($action == 'add'){

                            $group_association_data = Group::getGroups($id_lang);
                            $group_association = array();
                            foreach($group_association_data as $_item_group){
                                $group_association[] = $_item_group['id_group'];
                            }


                            $data_add = array(
                                'post_images' => $post_images,
                                'id_customer' => $id_customer,
                                'ids_categories'=>$ids_categories,

                                'id_lang' => $id_lang,
                                'post_title' => $post_title_blockblog,
                                'post_content' => $post_content_blockblog,

                                'group_association'=>$group_association,
                                'id_customer'=>$id_customer,

                                'post_status' => 4
                            );


                            $data_errors = $obj_blog->savePostByCustomer($data_add);

                        } else {

                            $data_update = array(
                                'id_editposts' => $id_item,
                                'ids_categories'=>$ids_categories,

                                'post_images' => $post_images,
                                'id_customer' => $id_customer,

                                'id_lang' => $id_lang,
                                'post_title' => $post_title_blockblog,
                                'post_content' => $post_content_blockblog,


                                'post_status' => 3
                            );

                            $data_errors = $obj_blog->updatePostByCustomer($data_update);
                        }

                        $error = $data_errors['error'];
                        if($error){
                            $error_text = $data_errors['error_text'];
                            $errors[] = $error_text;

                        } else {

                            Tools::redirect($myblogposts_url);
                        }
                    }

                }

                ## update item ##


                if($action == 'add'){
                    $title_mypost = $_data_translate['meta_title_myblogpostadd'];
                    $desc_mypost = $_data_translate['meta_description_myblogpostadd'];
                    $key_mypost = $_data_translate['meta_keywords_myblogpostadd'];

                } else {
                    $title_mypost = $_data_translate['meta_title_myblogpostedit'];
                    $desc_mypost = $_data_translate['meta_description_myblogpostedit'];
                    $key_mypost = $_data_translate['meta_keywords_myblogpostedit'];
                }


                if (version_compare(_PS_VERSION_, '1.7', '>')) {
                    $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title_mypost;
                    $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $desc_mypost;
                    $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $key_mypost;
                }

                $this->context->smarty->assign('meta_title', $title_mypost);
                $this->context->smarty->assign('meta_description', $desc_mypost);
                $this->context->smarty->assign('meta_keywords', $key_mypost);




                $data_avatar = $obj_userprofileblockblog->getAvatarForCustomer(array('id_customer' => $id_customer));
                $avatar = $data_avatar['avatar'];


                $email_customer = isset($_data['post'][0]['customer_email']) ? $_data['post'][0]['customer_email'] :'' ;





                $comment_customer = isset($_data['post']['data'][$id_lang]['content']) ? $_data['post']['data'][$id_lang]['content'] : '';
                $title_customer = isset($_data['post']['data'][$id_lang]['title']) ? $_data['post']['data'][$id_lang]['title'] : '';
                $comment_time_add = isset($_data['post'][0]['time_add']) ? $_data['post'][0]['time_add'] :date("Y-m-d H:i:s") ;


                $category_ids = isset($_data['post'][0]['category_ids']) ?$_data['post'][0]['category_ids']: array();


                $related_categories  = $obj_blog->getCategories(array('is_myaccount'=>1));


                $ids_categories = Configuration::get($name_module.'ids_categories');
                $ids_categories = $ids_categories?explode(",",$ids_categories):array();

                    $related_categories_tmp = array();
                    foreach ($related_categories['categories'] as $k => $item_cat) {
                        $id_cat = $item_cat['id'];
                        if (in_array($id_cat, $ids_categories)) {
                            $related_categories_tmp[] = $item_cat;
                        }
                    }

                    $related_categories['categories'] = $related_categories_tmp;



                $is_demo = $obj_blockblog->is_demo;

                // gdpr
                $this->context->smarty->assign(array('id_module' => $obj_blockblog->getIdModule()));
                // gdpr
                $this->context->smarty->assign(
                    array(
                        $name_module.'c_avatar' => $avatar,
                        $name_module.'name_c' => $name_customer,
                        $name_module.'email_c' => $email_customer,

                        $name_module . 'title' => $title_customer,
                        $name_module . 'comment' => $comment_customer,
                        $name_module . 'time_add' => $comment_time_add,

                        $name_module . 'action' => $action,
                        $name_module . 'id_item' => $id_item,

                        $name_module . 'post'=> $_data['post'][0],

                        $name_module.'errors'=>$errors,
                        $name_module.'rand'=>rand(1,100),

                        $name_module.'rel_cat'=>$related_categories['categories'],
                        $name_module.'category_ids'=>$category_ids,

                        $name_module.'ajax_url'=>$ajax_url,
                        $name_module.'is_demo'=>$is_demo,


                    ));

                if (version_compare(_PS_VERSION_, '1.7', '>')) {
                    $this->setTemplate('module:' . $name_module . '/views/templates/front/myblogpostedit17.tpl');
                } else {
                    $this->setTemplate('myblogpostedit.tpl');
                }

            } else {
                Tools::redirect($myblogposts_url);
            }

            // edit comment
        } else {

            if((($status_author == 0 || $status_author == 2) && $status_author !== "") && $action == 'delete') {
                Tools::redirect($myblogposts_url);
            }


            // delete post
            if ($action == 'delete') {
                $_data = $obj_blog->getPostItem(array('id' => $id_item));
                $id_customer_related_to_post = isset($_data['post'][0]['author_id']) ? $_data['post'][0]['author_id'] : 0;
                $id_customer_related_to_post_status = isset($_data['post'][0]['status']) ? (int)$_data['post'][0]['status'] : 0;

                if ($id_customer_related_to_post == $id_customer && !in_array($id_customer_related_to_post_status, array(2))) {
                    $obj_blog->updatePostStatus(array('id' => $id_item, 'status' => 2));
                } else {
                    Tools::redirect($myblogposts_url);
                }

            }
            // delete post


            $id_lang = (int)($cookie->id_lang);
            $title_myposts = Configuration::get($name_module . 'title_myposts_' . $id_lang);
            $desc_myposts = Configuration::get($name_module . 'desc_myposts_' . $id_lang);
            $key_myposts = Configuration::get($name_module . 'key_myposts_' . $id_lang);

            $title_myposts = Tools::strlen($title_myposts) > 0 ? $title_myposts : $_data_translate['meta_title_myblogposts'];
            $desc_myposts = Tools::strlen($desc_myposts) > 0 ? $desc_myposts : $_data_translate['meta_description_myblogposts'];
            $key_myposts = Tools::strlen($key_myposts) > 0 ? $key_myposts : $_data_translate['meta_keywords_myblogposts'];


            if (version_compare(_PS_VERSION_, '1.7', '>')) {
                $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title_myposts;
                $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $desc_myposts;
                $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $key_myposts;
            }

            $this->context->smarty->assign('meta_title', $title_myposts);
            $this->context->smarty->assign('meta_description', $desc_myposts);
            $this->context->smarty->assign('meta_keywords', $key_myposts);


            $step = (int)Configuration::get($name_module . 'perpage_myposts');
            $start = 0;
            $_data = $obj_blog->getPostsForAuthor(array('start' => $start, 'step' => $step, 'author_id' => $id_customer, 'is_my' => 1));

            $paging = $obj_blog->PageNav($start, $_data['count_all'], $step, array('is_myblogposts' => 1, 'author_id' => $id_customer));

            // strip authors for content
            foreach ($_data['posts'] as $_k => $_item) {
                $_data['posts'][$_k]['content'] = strip_tags($_item['content']);

            }


            $this->context->smarty->assign(
                array(
                    $name_module . 'myposts' => $_data['posts'],
                    $name_module . 'mycount_all' => $_data['count_all'],
                    $name_module . 'paging' => $paging,

                    $name_module.'rand'=>rand(1,100),
                    $name_module . 'is_addmyblogposts' => Configuration::get($name_module . 'is_addmyblogposts'),
                    $name_module . 'is_editmyblogposts' => Configuration::get($name_module . 'is_editmyblogposts'),
                    $name_module . 'is_delmyblogposts' => Configuration::get($name_module . 'is_delmyblogposts'),
                    $name_module . 'd_eff_shopmyposts' => Configuration::get($name_module . 'd_eff_shopmyposts'),

                    $name_module . 'action' => $action,
                    $name_module . 'id_item' => $id_item,

                    $name_module.'status_author'=>$status_author,


                ));


            if (version_compare(_PS_VERSION_, '1.7', '>')) {
                $this->setTemplate('module:' . $name_module . '/views/templates/front/myblogposts17.tpl');
            } else {
                $this->setTemplate('myblogposts.tpl');
            }
        }

    }
}