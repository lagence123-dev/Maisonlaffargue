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

ob_start();
require_once(_PS_MODULE_DIR_ . 'blockblog/classes/BlockblogcommentsItems.php');

class AdminBlockblogcommentsController extends ModuleAdminController{

    private $_name_controller = 'AdminBlockblogcomments';
    private $_name_module = 'blockblog';
    private $_data_table_avatar = 'blog_avatar2customer';
    private  $_id_lang;
    private  $_id_shop;
    private  $_iso_code;


    public function __construct()

	{

            $this->bootstrap = true;
            $this->context = Context::getContext();
            $this->table = 'blog_comments';


            $this->identifier = 'id';
            $this->className = 'BlockblogcommentsItems';


            $this->lang = false;

            $this->_orderBy = 'id';
            $this->_orderWay = 'DESC';


            $this->allow_export = false;

            $this->list_no_link = true;


            require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blogspm.class.php');
            $bloghelp_obj = new blogspm();


            $id_lang =  $this->context->cookie->id_lang;
            $this->_id_lang = $id_lang;
            $id_shop =  $this->context->shop->id;
            $this->_id_shop = $id_shop;

            $iso_code = Language::getIsoById($id_lang);
            $this->_iso_code = $iso_code;

            $this->_select .= 'a.id, cd.title, a.rating, a.id_post, a.id_customer as author_id, a.name as author,
                               IF(LENGTH(a.comment)>50,CONCAT(SUBSTR(a.comment,1,50),"..."),a.comment) as comment,
                               c.img, a.time_add , a.id_lang, a.id_shop, a.status, cd.seo_url ';
            $this->_join .= '  JOIN `' . _DB_PREFIX_ .'blog_post` c  ON (c.id = a.id_post AND FIND_IN_SET(a.id_shop,c.ids_shops)) ';
            $this->_join .= '  JOIN `' . _DB_PREFIX_ .'blog_post_data` cd  ON (c.id = cd.id_item and a.id_lang = cd.id_lang) ';

            //$this->_join .= '  LEFT JOIN `' . _DB_PREFIX_ . $this->_data_table_avatar.'` d ON (d.id_customer = a.id_customer)';


            $this->_select .= ', (SELECT group_concat(sh.`name` SEPARATOR \', \')
                    FROM `'._DB_PREFIX_.'shop` sh
                    WHERE sh.`active` = 1 AND sh.deleted = 0 AND sh.`id_shop` = a.id_shop
                    ) as shop_name';

            $this->_select .= ', (SELECT group_concat(l.`iso_code` SEPARATOR \', \')
	            FROM `'._DB_PREFIX_.'lang` l
	            JOIN
	            `'._DB_PREFIX_.'lang_shop` ls
	            ON(l.id_lang = ls.id_lang)
	            WHERE l.`active` = 1 AND ls.id_shop = '.(int)$id_shop.' AND l.`id_lang` = a.id_lang) as language';




            $this->addRowAction('edit');
            $this->addRowAction('delete');
            //$this->addRowAction('view');
            //$this->addRowAction('&nbsp;');



            if($bloghelp_obj->isURLRewriting()){
              $is_rewrite = 1;
            } else {
               $is_rewrite = 0;
            }

            // is cloud ?? //
//            if(defined('_PS_HOST_MODE_')){
//                $logo_img_path = '../modules/'.$this->_name_module.'/upload/';
//            } else {
//                $logo_img_path = '../upload/'.$this->_name_module.'/';
//            }
            $logo_img_path = '../modules/'.$this->_name_module.'/upload/';
            // is cloud ?? //


            // is cloud ?? //
//            if(defined('_PS_HOST_MODE_')){
//                $logo_img_path_avatar = 'modules/'.$this->_name_module.'/upload/';
//            } else {
//                $logo_img_path_avatar = 'upload/'.$this->_name_module.'/';
//            }
            $logo_img_path_avatar = 'modules/'.$this->_name_module.'/upload/';
            // is cloud ?? //

            $all_laguages = Language::getLanguages(true);

            $is16= 0;
            if(version_compare(_PS_VERSION_, '1.6', '>')) {
                $is16= 1;
            }



            $data_url = $bloghelp_obj->getSEOURLs();
            $post_url = $data_url['post_url'];
            $author_url = $data_url['author_url'];


            $data_rating = array();
            for($i=1;$i<=5;$i++){
                $data_rating[$i] = $i;
            }


            $data_seo_uls_set = $bloghelp_obj->getSeoUrlsSettings(array('id_lang'=>$id_lang));

            $blog_alias = $data_seo_uls_set['blog_alias'];
            $blog_author_alias = $data_seo_uls_set['blog_author_alias'];


            ##shops##
            $data_shop_uris = array();
            $data_shop = Shop::getShops();
            foreach($data_shop as $item){
                $id_shop_uri = $item['id_shop'];
                $uri = $item['uri'];
                $uri = Tools::substr($uri,1);
                $data_shop_uris[$id_shop_uri] = $uri;
            }
            ###shops##



        $this->fields_list = array(
                'id' => array(
                    'title' => $this->l('ID'),
                    'align' => 'center',
                    'search' => true,
                    'orderby' => true,

                ),

                'author' => array(
                    'title' => $this->l('Author'),
                    'width' => 'auto',
                    'orderby' => true,
                    'type_custom' => 'customer_name',
                    'ava_on'=>Configuration::get($this->_name_module.'ava_on'),
                    'user_url'=>$author_url,
                    'alias_url'=>$blog_author_alias,

                    'is_rewrite' => $is_rewrite,
                    'iso_code' => count($all_laguages)>1?$this->_iso_code."/":"",
                    'base_dir_ssl' => _PS_BASE_URL_SSL_."/",
                    'is16'=>$is16,
                    'count_languages'=>count($all_laguages),

                    'data_shop_uris'=>$data_shop_uris,
                    'alias_url_blog'=>$blog_alias,
                ),

                'title' => array(
                    'title' => $this->l('Post'),
                    'width' => 'auto',
                    'orderby' => true,
                    'type_custom' => 'title_comment',
                    'is_rewrite' => $is_rewrite,
                    'iso_code' => count($all_laguages)>1?$this->_iso_code."/":"",
                    'base_dir_ssl' => _PS_BASE_URL_SSL_."/",
                    'logo_img_path' => $logo_img_path,
                    'is16'=>$is16,
                    'item_url'=>$post_url,
                    'count_languages'=>count($all_laguages),
                    'alias_url'=>$blog_alias,
                    'data_shop_uris'=>$data_shop_uris,

                ),

                'rating' => array(
                    'title' => $this->l('Rating'),
                    'width' => 'auto',
                    'type' => 'select',
                    'orderby' => FALSE,
                    'list' => $data_rating,
                    'filter_key' => 'a!rating',
                    'type_custom' => 'rating',
                ),

                'shop_name' => array(
                    'title' => $this->l('Shop'),
                    'width' => 'auto',
                    'search' => false

                ),

                'language' => array(
                    'title' => $this->l('Language'),
                    'width' => 'auto',
                    'search' => false

                ),

                'comment' => array(
                    'title' => $this->l('Comment'),
                    'width' => 'auto',
                    'search' => TRUE

                ),


                'time_add' => array(
                    'title' => $this->l('Date add'),
                    'width' => 'auto',
                    'search' => false,

                ),


               /* 'status' => array(
                    'title' => $this->l('Status'),
                    'width' => 40,
                    'align' => 'center',
                    'type' => 'bool',
                    'orderby' => FALSE,
                    'type_custom' => 'is_active',
                    'token_custom'=>Tools::getAdminTokenLite('AdminBlockblogajax'),
                    'ajax_link' => $this->context->link->getAdminLink('AdminBlockblogajax'),
                ),*/


                'status' => array(
                    'title' => $this->l('Status'),
                    'width' => '100',
                    'type' => 'select',
                    'align' => 'center',
                    'icon' => array(
                        0 => array('value'=>0),
                        1 => array('value'=>1),
                        2 => array('value'=>2),
                        3 => array('value'=>3),
                    ),
                    'list' => array(0 => $this->l('No'), 1=> $this->l('Yes'), 2=> $this->l('The customer has deleted his comment'),3=> $this->l('The customer has changed his comment')),
                    'filter_key' => 'a!status',
                    'orderby' => false,
                    'type_custom' => 'is_active',
                    'token_custom'=>Tools::getAdminTokenLite('AdminBlockblogajax'),
                    'ajax_link' => $this->context->link->getAdminLink('AdminBlockblogajax'),

                ),

            );


        /*if(Configuration::get($this->_name_module.'ava_on') == 1){

            $this->array_push_pos($this->fields_list, 1,

                array(
                    'title' => $this->l('Avatar'),
                    'width' => 'auto',
                    'search' => false,
                    'align' => 'center',
                    'orderby' => FALSE,
                    'type_custom' => 'avatar',
                    'base_dir_ssl' => _PS_BASE_URL_SSL_.__PS_BASE_URI__,
                    'path_img_cloud'=> $logo_img_path_avatar.'avatar'.DIRECTORY_SEPARATOR,
                    'ava_on'=>Configuration::get($this->_name_module.'ava_on'),

                ),

                'avatar'
            );
        }*/


            $this->bulk_actions = array(
                'delete' => array(
                    'text' => $this->l('Delete selected'),
                    'icon' => 'icon-trash',
                    'confirm' => $this->l('Delete selected items?')
                )
            );





		parent::__construct();
		
	}




    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        $list = parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
        $this->_listsql = false;
        return $list;
    }

    public function initPageHeaderToolbar()
    {

        parent::initPageHeaderToolbar();
    }

    public function initToolbar() {

        parent::initToolbar();
        /*$this->toolbar_btn['add_item'] = array(
                                            'href' => self::$currentIndex.'&add'.$this->_name_module.'&token='.$this->token,
                                            'desc' => $this->l('Add new category', null, null, false),
                                        );
        */
        //unset($this->toolbar_btn['new']);

        //echo "<pre>"; var_dump($this->toolbar_btn['new']);exit;
        $this->toolbar_btn['new']= array(
            'href' => '#',
            'desc' => '',
        );



    }



    public function postProcess()
    {


        require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blogspm.class.php');
        $bloghelp_obj = new blogspm();


        if (Tools::isSubmit('update_item')) {
                $id = Tools::getValue('id');
                ## update item ##

                $comments_name = Tools::getValue("comments_name");
                $comments_email = Tools::getValue("comments_email");
                $comments_comment = Tools::getValue("comments_comment");
                $comments_status = Tools::getValue("comments_status");
                $time_add = Tools::getValue("time_add");

                $response = Tools::getValue("response");
                $is_noti = Tools::getValue("is_noti");
                $rating = Tools::getValue("rating");

                $post_images_ava = Tools::getValue("post_images_ava");
                $id_customer = Tools::getValue("id_customer");

                $data = array('comments_name' => $comments_name,
                            'comments_email' => $comments_email,
                            'comments_comment' => $comments_comment,
                            'comments_status' => $comments_status,
                            'time_add'=>$time_add,
                            'response' => $response,
                            'is_noti' => $is_noti,
                            'rating'=>$rating,
                            'id_editcomments' => $id,

                            'post_images_ava' => $post_images_ava,
                            'id_customer'=>$id_customer,

                );


                if(!$comments_name)
                    $this->errors[] = $this->l('Please fill the Customer Name');

                if(!$comments_email)
                    $this->errors[] = $this->l('Please fill the Customer Email');

                if(!$comments_comment)
                    $this->errors[] = $this->l('Please fill the Comment');

                if(!$time_add)
                    $this->errors[] = $this->l('Please select Date Add');



             if (empty($this->errors)) {

                 $data_errors = $bloghelp_obj->updateComment($data);

                 $error = $data_errors['error'];
                 if($error){
                     $error_text = $data_errors['error_text'];
                     $this->errors[] = $error_text;
                     $this->display = 'add';
                     return FALSE;
                 } else {

                     Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . Tools::getAdminTokenLite($this->_name_controller));
                 }

            }else{

                $this->display = 'add';
                return FALSE;
            }

            ## update item ##
            } elseif (Tools::isSubmit('submitBulkdelete' . $this->_name_module)) {
                ### delete more than one  items ###
                if ($this->tabAccess['delete'] === '1' || !empty($this->tabAccess['delete'])) {
                    if (Tools::getValue($this->list_id . 'Box')) {


                        $object = new $this->className();

                        if ($object->deleteSelection(Tools::getValue($this->list_id . 'Box'))) {
                            Tools::redirectAdmin(self::$currentIndex . '&conf=2' . '&token=' . $this->token);
                        }
                        $this->errors[] = $this->l('An error occurred while deleting this selection.');
                    } else {
                        $this->errors[] = $this->l('You must select at least one element to delete.');
                    }
                } else {
                    $this->errors[] = $this->l('You do not have permission to delete this.');
                }
                ### delete more than one  items ###
            } elseif (Tools::isSubmit('delete' . $this->_name_module)) {
                ## delete item ##

                $id = Tools::getValue('id');

                $bloghelp_obj->deleteComment(array('id' => $id));

                Tools::redirectAdmin(self::$currentIndex . '&conf=1&token=' . Tools::getAdminTokenLite($this->_name_controller));
                ## delete item ##
            } else {
               return parent::postProcess(true);
            }




    }


    public function setMedia($isNewTheme=null)
    {

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            parent::setMedia($isNewTheme);
        } else {
            parent::setMedia();
        }

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/css/admin.css');

        $this->context->controller->addJs(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/js/image-files.js');
        $this->context->controller->addJs(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/js/admin.js');
        $this->addJqueryUi(array('ui.core','ui.widget','ui.datepicker'));

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/css/custom_menu.css');

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/css/font-custom.min.css');


        $this->context->controller->addJs(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/js/bootstrap-rating-input.js');


    }


    public function renderForm()
    {
        if (!($this->loadObject(true)))
            return;

        if (Validate::isLoadedObject($this->object)) {
            $this->display = 'update';
        }


        $id = (int)Tools::getValue('id');

        require_once(_PS_MODULE_DIR_ . ''.$this->_name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();



        require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/blockblog.php');
        $blockblog = new blockblog();
        $is_demo = $blockblog->is_demo;
        if($is_demo){
            ob_start();
            include(dirname(__FILE__).'/../../views/templates/hooks/' . $this->_name_module . '/feature_disabled_on_the_demo.phtml');
            $is_demo = ob_get_clean();
        } else {
            $is_demo = '';
        }

        if($id) {

            $_data = $obj_blog->getCommentItem(array('id'=>$id));


            //$author =  isset($_data['comments'][0]['name']) ? $_data['comments'][0]['name'] :'' ;
            $id_customer =  isset($_data['comments'][0]['id_customer']) ? (int) $_data['comments'][0]['id_customer'] :'' ;

            $time_add = $_data['comments'][0]['time_add'];

            $status = $_data['comments'][0]['status'];

            $id_lang = $_data['comments'][0]['id_lang'];
            $data_lng = Language::getLanguage($id_lang);
            $name_lang = $data_lng['name'];
            $iso_code = $data_lng['iso_code'];

            $rating = $_data['comments'][0]['rating'];


            $id_shop = $_data['comments'][0]['id_shop'];

            $shops = Shop::getShops();
            $name_shop = '';
            foreach($shops as $_shop){
                 $id_shop_lists = $_shop['id_shop'];
                 if($id_shop == $id_shop_lists)
                    $name_shop = $_shop['name'];
            }

            $post_id = (int)$_data['comments'][0]['id_post'];
            $_info_cat = $obj_blog->getPostItem(array('id' => $post_id,'is_admin'=>1,'id_customer'=>$id_customer));


            $seo_url = isset($_info_cat['post']['data'][1]['seo_url'])?$_info_cat['post']['data'][1]['seo_url']:'';
            $img = isset($_info_cat['post']['data'][1]['img'])?$_info_cat['post']['data'][1]['img']:'';



            $avatar =  isset($_info_cat['post'][0]['avatar']) ? $_info_cat['post'][0]['avatar'] :'' ;
            $is_exist_ava =  isset($_info_cat['post'][0]['is_exist_ava']) ? $_info_cat['post'][0]['is_exist_ava'] :0 ;



            // is cloud ?? //
//            if(defined('_PS_HOST_MODE_')){
//                $logo_img_path = '../modules/'.$this->_name_module.'/upload/';
//            } else {
//                $logo_img_path = '../upload/'.$this->_name_module.'/';
//            }
            $logo_img_path = '../modules/'.$this->_name_module.'/upload/';
            // is cloud ?? //

            // is cloud ?? //
//            if(defined('_PS_HOST_MODE_')){
//                $logo_img_path_avatar = '../modules/'.$this->_name_module.'/upload/';
//            } else {
//                $logo_img_path_avatar = '../upload/'.$this->_name_module.'/';
//            }
            $logo_img_path_avatar = '../modules/'.$this->_name_module.'/upload/';
            // is cloud ?? //

            if($obj_blog->isURLRewriting()){
                $is_rewrite = 1;
            } else {
                $is_rewrite = 0;
            }

        }



        if($id){
            $title_item_form = $this->l('Edit comment:');
        }



        $all_laguages = Language::getLanguages(true);

        $is16= 0;
        if(version_compare(_PS_VERSION_, '1.6', '>')) {
            $is16= 1;
        }


        $data_url = $obj_blog->getSEOURLs();
        $post_url = $data_url['post_url'];
        $ajax_url = $data_url['ajax_url'];


        ##shops##
        $data_shop_uris = array();
        $data_shop = Shop::getShops();
        foreach($data_shop as $item){
            $id_shop_uri = $item['id_shop'];
            $uri = $item['uri'];
            $uri = Tools::substr($uri,1);
            $data_shop_uris[$id_shop_uri] = $uri;
        }
        ###shops##


        $data_seo_uls_set = $obj_blog->getSeoUrlsSettings(array('id_lang'=>$id_lang));

        $blog_alias = $data_seo_uls_set['blog_alias'];


        $this->fields_form = array(
            'tinymce' => TRUE,
            'legend' => array(
                'title' => $title_item_form,
                //'icon' => 'fa fa-list fa-lg'
            ),
            'input' => array(

                array(
                    'type' => 'language_item',
                    'label' => $this->l('ID'),
                    'name' => 'language_item',
                    'values'=> $id,

                ),

                array(
                    'type' => 'language_item',
                    'label' => $this->l('Language'),
                    'name' => 'language_item',
                    'values'=> $name_lang,

                ),

                array(
                    'type' => 'shop_item',
                    'label' => $this->l('Shop'),
                    'name' => 'shop_item',
                    'values'=> $name_shop,

                ),

                array(
                    'type' => 'item_url',
                    'label' => $this->l('Post URL'),

                    'name' => 'item_url',
                    'is_rewrite' => $is_rewrite,
                    'iso_code' => count($all_laguages)>1?$iso_code."/":"",
                    'base_dir_ssl' => _PS_BASE_URL_SSL_."/",
                    'logo_img_path' => $logo_img_path,
                    'seo_url' => $seo_url,
                    'img'=>$img,
                    'id_shop' => $id_shop,
                    'id_lang'=>$id_lang,
                    'post_id' =>$post_id,
                    'is16'=>$is16,
                    'item_url'=>$post_url,
                    'data_shop_uris'=>$data_shop_uris,
                    'alias_url'=>$blog_alias,

                ),

                array(
                    'type' => 'text_rating_custom',
                    'label' => $this->l('Rating:'),
                    'name' => 'rating',
                    'id' => 'rating',
                    'lang' => false,
                    'required' => TRUE,
                    'rating' => $rating,

                ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Customer Name'),
                    'name' => 'comments_name',
                    'id' => 'comments_name',
                    'lang' => FALSE,
                    'required' => TRUE,
                    'size' => 5000,
                    'maxlength' => 5000,
                ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Customer Email'),
                    'name' => 'comments_email',
                    'id' => 'comments_email',
                    'lang' => FALSE,
                    'required' => TRUE,
                    'size' => 5000,
                    'maxlength' => 5000,
                ),


                array(
                    'type' => 'textarea',
                    'label' => $this->l('Comment'),
                    'name' => 'comments_comment',
                    'id' => 'comments_comment',
                    'required' => true,
                    'autoload_rte' => FALSE,
                    'lang' => FALSE,
                    'rows' => 8,
                    'cols' => 40,

                ),

                array(
                    'type' => 'textarea',
                    'label' => $this->l('Response'),
                    'name' => 'response',
                    'id' => 'response',
                    'required' => FALSE,
                    'autoload_rte' => FALSE,
                    'lang' => FALSE,
                    'rows' => 8,
                    'cols' => 40,

                ),


                array(
                    'type' => 'checkbox_custom',
                    'label' => $this->l('Send a response on the comment notification to the customer:'),
                    'name' => 'is_noti',
                    'values' => array(
                        'value' => 0
                    ),


                ),

                array(
                    'type' => 'item_date',
                    'label' => $this->l('Date Add'),
                    'name' => 'date_on',
                    'time_add' => $time_add,
                    'required' => TRUE,
                ),

                /*array(
                    'type' => 'switch',
                    'label' => $this->l('Status'),
                    'name' => 'comments_status',
                    'required' => FALSE,
                    'class' => 't',
                    'is_bool' => TRUE,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )

                    ),
                ),*/





            ),


        );

        if(in_array($status,array(2,3))){

            $this->array_push_pos($this->fields_form['input'], 11,
                array(
                    'type' => 'select',
                    'label' => $this->l('Status'),
                    'name' => 'comments_status',
                    'required' => FALSE,


                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 0,
                                'name' => $this->l('No')),

                            array(
                                'id' => 1,
                                'name' => $this->l('Yes'),
                            ),

                            array(
                                'id' => 2,
                                'name' => $this->l('The customer has deleted his comment'),
                            ),

                            array(
                                'id' => 3,
                                'name' => $this->l('The customer has changed his comment'),
                            ),


                        ),

                        'id' => 'id',
                        'name' => 'name'
                    ),



                )
            );

        } else {
            $this->array_push_pos($this->fields_form['input'], 11,
                array(
                    'type' => 'select',
                    'label' => $this->l('Status'),
                    'name' => 'comments_status',
                    'required' => FALSE,


                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 0,
                                'name' => $this->l('No')),

                            array(
                                'id' => 1,
                                'name' => $this->l('Yes'),
                            ),



                        ),

                        'id' => 'id',
                        'name' => 'name'
                    ),



                )
            );
        }

        if(Configuration::get($this->_name_module.'ava_on') == 1 && $id_customer) {




            $this->array_push_pos($this->fields_form['input'], 5,
                array(
                    'type' => 'avatar_custom',
                    'label' => $this->l('Author Avatar:'),
                    'name' => 'avatar-review',
                    'id' => 'avatar-review',
                    'lang' => false,
                    'required' => false,
                    'value' => $avatar,

                    'logo_img' => $avatar,
                    'logo_img_path' => $logo_img_path_avatar,

                    'id_item' => $id,

                    'desc' => $this->l('Allow formats *.jpg; *.jpeg; *.png; *.gif.'),
                    'is_demo' => $is_demo,
                    'max_upload_info' => ini_get('upload_max_filesize'),
                    'post_max_size'=>ini_get('post_max_size'),

                    'id_customer' => $id_customer,
                    'is_exist_ava' => $is_exist_ava,
                    'ajax_url'=>$ajax_url,

                    'post_id'=>$post_id,


                )
            );
        }


        $this->fields_form['submit'] = array(
            'title' => ($id)?$this->l('Update'):$this->l('Save'),
        );




        if($id) {

            $this->tpl_form_vars = array(
               'fields_value' => $this->getConfigFieldsValuesForm(array('id'=>$id)),
            );

            $this->submit_action = 'update_item';
        }



        return parent::renderForm();
    }



    public function getConfigFieldsValuesForm($data_in){



        $id = (int)Tools::getValue('id');
        if($id) {
            $id = $data_in['id'];
            require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blogspm.class.php');
            $obj_blog = new blogspm();
            $_data = $obj_blog->getCommentItem(array('id'=>$id));


            $name = $_data['comments'][0]['name'];
            $email = $_data['comments'][0]['email'];
            $comment = $_data['comments'][0]['comment'];
            $response = $_data['comments'][0]['response'];
            $status = $_data['comments'][0]['status'];

           $config_array = array(
                'comments_name' => $name,
                'comments_email' => $email,
                'comments_comment' => $comment,
                'response'=>$response,
                'comments_status' => $status,
            );
        } else {
            $config_array = array();
        }
        return $config_array;
    }


    private function array_push_pos(&$array,$pos=0,$value,$key='')
    {
        if (!is_array($array)) {return false;}
        else
        {
            if (Tools::strlen($key) == 0) {$key = $pos;}
            $c = count($array);
            $one = array_slice($array,0,$pos);
            $two = array_slice($array,$pos,$c);
            $one[$key] = $value;
            $array = array_merge($one,$two);
            return;
        }
    }

    public function l($string , $class = NULL, $addslashes = false, $htmlentities = true){
        if(version_compare(_PS_VERSION_, '1.7', '<')) {
            return parent::l($string);
        } else {
            //$class = array();
            //return Context::getContext()->getTranslator()->trans($string, $class, $addslashes, $htmlentities);
            return Translate::getModuleTranslation($this->_name_module, $string, $this->_name_module);
        }
    }


	
}





?>

