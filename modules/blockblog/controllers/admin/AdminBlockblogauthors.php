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
require_once(_PS_MODULE_DIR_ . 'blockblog/classes/BlogAvatarCustomer.php');

class AdminBlockblogauthorsController extends ModuleAdminController {

    private $_name_module = 'blockblog';
    private $_name_controller = 'AdminBlockblogauthors';

    public function __construct()
    {
        $name_module = 'blockblog';

        $this->bootstrap = true;
        $this->context = Context::getContext();

        $this->module = $name_module;


        $this->table = 'blog_avatar2customer';
        $this->className = 'BlogAvatarCustomer';
        $this->identifier = 'id_customer';

        $this->toolbar_title = $this->l('Community Authors');

        $this->lang = false;

        $this->_orderBy = 'count_posts';
        $this->_orderWay = 'DESC';


        $this->allow_export = false;
        $this->list_no_link = true;

        $id_lang =  $this->context->cookie->id_lang;


        $this->_select .= 'c.id_customer as id_customer, c.firstname, c.lastname, c.email, a.is_show,
                            IF(LENGTH(a.info)>50,CONCAT(SUBSTR(a.info,1,50),"..."),a.info) as info,
                            a.avatar_thumb, a.is_show, a.status, a.info';
        $this->_join .= '  JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer = a.id_customer and c.deleted = 0)';
        $this->_join .= ' JOIN ' . _DB_PREFIX_ . 'shop s on (s.id_shop = c.id_shop) ';



        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_post` bp
				    WHERE bp.author_id = a.id_customer and FIND_IN_SET(a.id_shop,bp.ids_shops)) as count_posts ';

        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_post` bp
				    WHERE bp.author_id = a.id_customer and FIND_IN_SET(a.id_shop,bp.ids_shops) and status = 1) as count_posts_active ';


        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_post` bp
				    WHERE bp.author_id = a.id_customer and FIND_IN_SET(a.id_shop,bp.ids_shops) and status != 1) as count_posts_noactive ';

        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_comments` bp
				    WHERE bp.id_customer = a.id_customer and bp.id_shop = a.id_shop) as count_comments ';
        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_comments` bp
				    WHERE bp.id_customer = a.id_customer and bp.id_shop = a.id_shop and status = 1) as count_comments_active ';
        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_comments` bp
				    WHERE bp.id_customer = a.id_customer and bp.id_shop = a.id_shop and status != 1) as count_comments_noactive ';


        $this->_select .= ', (SELECT group_concat(sh.`name` SEPARATOR \', \')
                    FROM `'._DB_PREFIX_.'shop` sh
                    WHERE sh.`active` = 1 AND sh.deleted = 0 AND sh.`id_shop` = c.id_shop
                    ) as shop_name';

        $this->_select .= ' , (SELECT pc2.author FROM  `' . _DB_PREFIX_ . 'blog_post` pc2  WHERE pc2.`author_id` = a.id_customer LIMIT 1) as author_name';

        /*$this->_select .= ', (SELECT group_concat(l.`iso_code` SEPARATOR \', \')
	            FROM `'._DB_PREFIX_.'lang` l
	            JOIN
	            `'._DB_PREFIX_.'lang_shop` ls
	            ON(l.id_lang = ls.id_lang)
	            WHERE l.`active` = 1 AND ls.id_shop = a.id_shop AND l.`id_lang` = '.$id_lang.') as language';*/



        $this->addRowAction('edit');
        //$this->addRowAction('delete');
        //$this->addRowAction('view');
        //$this->addRowAction('&nbsp;');



        ### shops ###

        $shops = Shop::getShops();
        $data_shops = array();
        foreach($shops as $_shop){
            $data_shops[$_shop['id_shop']]= $_shop['name'];
        }
        ### shops ###




        // is cloud ?? //
//        if(defined('_PS_HOST_MODE_')){
//            $logo_img_path_avatar = 'modules/'.$this->_name_module.'/upload/';
//        } else {
//            $logo_img_path_avatar = 'upload/'.$this->_name_module.'/';
//        }
        $logo_img_path_avatar = 'modules/'.$this->_name_module.'/upload/';
        // is cloud ?? //


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
            'id_customer' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
                'search' => false,
                'orderby' => true,

            ),


            'author_name' => array(
                'title' => $this->l('Author'),
                'width' => 'auto',
                'orderby' => FALSE,
                'search'=>FALSE,
                'type_custom'=>'author_name_custom'


            ),

            'email' => array(
                'title' => $this->l('E-mail'),
                'width' => 'auto',
                'hint' => $this->l('You can search customer by E-mail'),
                'filter_key' => 'c!email',

            ),
            'shop_name' => array(
                'title' => $this->l('Shop'),
                'width' => 'auto',
                'orderby' => false,
                'filter_key' => 'a!id_shop',
                'type' => 'select', 'list' => $data_shops
            ),


            'info' => array(
                'title' => $this->l('Information'),
                'width' => 'auto',
                'align' => 'center',
                'type' => 'text',
                'orderby' => FALSE,
                'search'=>TRUE,



            ),


            'count_posts' => array(
                'title' => $this->l('Count posts'),
                'width' => 'auto',
                'align' => 'center',
                'type' => 'text',
                'orderby' => true,
                'search'=>FALSE,
                'type_custom' => 'count_posts',


            ),


            'count_comments' => array(
                'title' => $this->l('Count comments'),
                'width' => 'auto',
                'align' => 'center',
                'type' => 'text',
                'orderby' => true,
                'search'=>FALSE,
                'type_custom' => 'count_comments',


            ),


            'is_show' => array(

                'title' => $this->l('Show customer profile on the site'),
                'width' => '100',
                'type' => 'select',
                'align' => 'center',
                'icon' => array(
                    0 => array('value'=>0),
                    1 => array('value'=>1),


                ),
                'list' => array(0 => $this->l('No'), 1=> $this->l('Yes'),),
                'filter_key' => 'a!is_show',
                'orderby' => false,

                'type_custom' => 'is_show',
                'hint'=>$this->l('This option is not taken into account, if admin disabled customer profile')

            ),



            'status' => array(

                'title' => $this->l('Status'),
                'width' => '100',
                'type' => 'select',
                'align' => 'center',
                'icon' => array(
                    0 => array('value'=>0),
                    1 => array('value'=>1),
                    2 => array('value'=>2),

                ),
                'list' => array(0 => $this->l('Disabled and visible posts'), 1=> $this->l('Enabled'), 2=> $this->l('Disabled and hidden posts')),
                'filter_key' => 'a!status',
                'orderby' => false,
                'type_custom' => 'is_active',


            ),

        );

        if(Configuration::get($this->_name_module.'ava_on') == 1){

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
        }


        parent::__construct();
    }




    public function initToolbar() {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);

    }


    public function postProcess()
    {


        require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blogspm.class.php');
        $bloghelp_obj = new blogspm();


        if (Tools::isSubmit('update_item')) {
            $id = Tools::getValue('id_customer');
            ## update item ##

            $author = Tools::getValue("author");
            $info = Tools::getValue("info");
            $status = Tools::getValue("status");

            $is_del = Tools::getValue("is_del");

            $is_show = Tools::getValue("is_show");

            $post_images_ava = Tools::getValue("post_images_ava");
            $id_customer = Tools::getValue("id_customer");



            $data = array(
                'author' => $author,
                'info' => $info,
                'status' => $status,

                'id_edit' => $id,

                'is_del'=>$is_del,
                'is_show'=>$is_show,

                'post_images_ava' => $post_images_ava,
                'id_customer'=>$id_customer,

            );


            if(!$author)
                $this->errors[] = $this->l('Please fill the Author Name');


            if (empty($this->errors)) {

                $data_errors = $bloghelp_obj->updateAuthor($data);

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


        $this->context->controller->addJs(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/js/admin.js');

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/css/custom_menu.css');

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/css/font-custom.min.css');




    }


    public function renderForm()
    {
        if (!($this->loadObject(true)))
            return;

        if (Validate::isLoadedObject($this->object)) {
            $this->display = 'update';
        }


        $id = (int)Tools::getValue('id_customer');

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


            include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/userprofileblockblog.class.php');
            $obj = new userprofileblockblog();

            $info_customer = $obj->getCustomerInfo(array('id_customer'=>$id,'is_admin'=>1));


            $id_shop = isset($info_customer['result']['id_shop'])?$info_customer['result']['id_shop']:0;

            $is_show = isset($info_customer['result']['is_show'])?$info_customer['result']['is_show']:0;

            $data_customer_info = $obj_blog->getCustomerInfo(array('id_customer'=>$id));
            $author_email = $data_customer_info['email'];
            $customer_name = isset($data_customer_info['customer_name'])?$data_customer_info['customer_name']:'';




            $data_author = $obj_blog->getDataFromAuthorAdmin(array('id'=>$id,'id_shop'=>$id_shop));

            $post_id = isset($data_author['data'][0]['id'])?$data_author['data'][0]['id']:0;



            $data_count_all = isset($data_author['data_count_all']['count'])?$data_author['data_count_all']['count']:0;
            $data_count_active = isset($data_author['data_count_active']['count'])?$data_author['data_count_active']['count']:0;
            $data_count_noactive = isset($data_author['data_count_noactive']['count'])?$data_author['data_count_noactive']['count']:0;



            $_info_cat = $obj_blog->getPostItem(array('id' => $post_id,'is_admin'=>1,'id_customer'=>$id));


            $avatar =  isset($_info_cat['post'][0]['avatar']) ? $_info_cat['post'][0]['avatar'] :'' ;
            $name_author =  isset($_info_cat['post'][0]['author']) ? $_info_cat['post'][0]['author'] :$customer_name ;



            $exist_avatar = $info_customer['exist_avatar'];




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
            $title_item_form = $this->l('Edit Author:');
        }



        $all_laguages = Language::getLanguages(true);

        $is16= 0;
        if(version_compare(_PS_VERSION_, '1.6', '>')) {
            $is16= 1;
        }


        $data_url = $obj_blog->getSEOURLs();
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

        $id_lang =  $this->context->cookie->id_lang;
        $data_lng = Language::getLanguage($id_lang);

        $iso_code = $data_lng['iso_code'];

        $data_seo_urls_set = $obj_blog->getSeoUrlsSettings(array('id_lang'=>$id_lang));

        $blog_alias_blog = $data_seo_urls_set['blog_alias'];
        $blog_alias = $data_seo_urls_set['blog_author_alias'];

        $admin_url_to_customer = $data_url['author_url'].$id."-".$name_author;


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
                    'name' => 'id_customer',
                    'values'=> $id,

                ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Author name'),
                    'name' => 'author',
                    'id' => 'author',
                    'lang' => false,
                    'required' => TRUE,
                    'size' => 1024,
                    'maxlength' => 1024,
                ),

                array(
                    'type' => 'customer_url',
                    'label' => $this->l('Author:'),

                    'name' => 'customer_url',
                    'values' => $name_author,
                    'url' => $admin_url_to_customer,

                    'is_rewrite' => $is_rewrite,
                    'iso_code' => count($all_laguages)>1?$iso_code."/":"",
                    'base_dir_ssl' => _PS_BASE_URL_SSL_.__PS_BASE_URI__,

                    'is16'=>$is16,
                    'count_languages'=>count($all_laguages),

                    'data_shop_uris'=>$data_shop_uris,
                    'alias_url_blog'=>$blog_alias_blog,
                    'alias_url'=>$blog_alias,


                    'id_customer'=>$id,
                    'author_url'=>$data_url['author_url'],

                    'id_shop'=>$id_shop,



                ),

                array(
                    'type' => 'language_item',
                    'label' => $this->l('Email:'),
                    'name' => 'id_customer',
                    'values'=> $author_email,

                ),


                array(
                    'type' => 'checkbox_custom',
                    'label' => $this->l('Show customer profile on the site'),
                    'name' => 'is_show',
                    'values' => array(
                        'value' => $is_show
                    ),
                    'desc'=>$this->l('This option is not taken into account, if admin disabled customer profile')


                ),

               array(
                    'type' => 'textarea',
                    'label' => $this->l('Information'),
                    'name' => 'info',
                    'id' => 'info',
                    'required' => FALSE,
                    'autoload_rte' => false,
                    'rows' => 8,
                    'cols' => 40,
                ),


                array(
                    'type' => 'count_posts_item',
                    'label' => $this->l('Total blog posts'),
                    'name' => 'id_customer',
                    'values'=> $data_count_all ,
                    'active' => $data_count_active,
                    'noactive' => $data_count_noactive,

                ),

                array(
                    'type' => 'checkbox_custom',
                    'label' => $this->l('Delete all blog posts'),
                    'name' => 'is_del',
                    'values' => array(
                        'value' => 0
                    ),
                    'desc'=>$this->l('You can delete all blog posts of the customer. IMPORTANT! The action is irreversible! You loose all blog posts by customer!')


                ),


                array(
                    'type' => 'select',
                    'label' => $this->l('Status'),
                    'name' => 'status',
                    'required' => FALSE,

                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 0,
                                'name' => $this->l('Disabled and visible posts')),

                            array(
                                'id' => 1,
                                'name' => $this->l('Enabled'),
                            ),

                            array(
                                'id' => 2,
                                'name' => $this->l('Disabled and hidden posts'),
                            ),

                        ),

                        'id' => 'id',
                        'name' => 'name'
                    ),
                )





            ),


        );


        if(Configuration::get($this->_name_module.'ava_on') == 1 && $id) {




            $this->array_push_pos($this->fields_form['input'], 1,
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

                    'id_customer' => $id,
                    'is_exist_ava' => $exist_avatar,
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



        $id = (int)Tools::getValue('id_customer');
        if($id) {
            $id = $data_in['id'];

            include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/userprofileblockblog.class.php');
            $obj = new userprofileblockblog();

            $info_customer = $obj->getCustomerInfo(array('id_customer'=>$id,'is_admin'=>1));
            $result = $info_customer['result'];

            $info = $result['info'];
            $status = $result['status'];

            $id_shop = isset($info_customer['result']['id_shop'])?$info_customer['result']['id_shop']:0;




            require_once(_PS_MODULE_DIR_ . ''.$this->_name_module.'/classes/blogspm.class.php');
            $obj_blog = new blogspm();


            $data_author = $obj_blog->getDataFromAuthorAdmin(array('id'=>$id,'id_shop'=>$id_shop));



            $post_id = isset($data_author['data'][0]['id'])?$data_author['data'][0]['id']:0;

            $_info_cat = $obj_blog->getPostItem(array('id' => $post_id,'is_admin'=>1,'id_customer'=>$id));

            $name_author =  isset($_info_cat['post'][0]['author']) ? $_info_cat['post'][0]['author'] :'' ;

            $config_array = array(
                'author' => $name_author,

                'info' => $info,
                'status' => $status,
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

    /* override function $this->l() for compatible with Prestashop 1.7.x.x */
    public function l($string , $class = NULL, $addslashes = false, $htmlentities = true){
        if(version_compare(_PS_VERSION_, '1.7', '<')) {
            return parent::l($string);
        } else {
            return Translate::getModuleTranslation($this->_name_module, $string, $this->_name_module);
        }
    }






}

?>
