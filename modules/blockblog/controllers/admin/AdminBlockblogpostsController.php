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
require_once(_PS_MODULE_DIR_ . 'blockblog/classes/BlockblogpostsItems.php');

class AdminBlockblogpostsController extends ModuleAdminController{

    private $_name_controller = 'AdminBlockblogposts';
    private $_name_module = 'blockblog';
    private $_data_table = 'blog_post_data';
    private $_data_table_avatar = 'blog_avatar2customer';
    private  $_id_lang;
    private  $_id_shop;
    private  $_iso_code;
    private $path_img_cloud;
    private $path_img_cloud_avatar;


    protected $position_identifier = 'position';

    public function __construct()

	{

            $this->bootstrap = true;
            $this->context = Context::getContext();
            $this->table = 'blog_post';


            $this->identifier = 'id_tabs';
            $this->className = 'BlockblogpostsItems';

            $this->list_id = 'blogpost';
            $this->_defaultOrderBy = 'position';
            //$this->_defaultOrderWay = 'DESC';


            $this->lang = false;

            /*$this->_orderBy = 'id';
            $this->_orderWay = 'DESC';*/


            $this->allow_export = false;

            $this->list_no_link = true;


            require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blogspm.class.php');
            $bloghelp_obj = new blogspm();
            $id_lang = $bloghelp_obj->getMostPopularLanguageForPosts(array('id_lang'=>$this->context->cookie->id_lang));

            //$id_lang =  $this->context->cookie->id_lang;
            $this->_id_lang = $id_lang;
            $id_shop =  $this->context->shop->id;
            $this->_id_shop = $id_shop;

            $iso_code = Language::getIsoById($id_lang);
            $this->_iso_code = $iso_code;

            //$this->_select .= 'a.id,a.is_slider, a.author, a.author_id, d.avatar_thumb, a.id as id_tabs, a.img, c.title, a.time_add , SUBSTRING(a.ids_shops,1,1) as ids_shops, a.ids_groups , c.seo_url, c.id_lang, '.$id_shop.' as id_shop, a.status ';
            $this->_select .= 'a.id,a.is_slider, a.author, a.author_id,  a.id as id_tabs, a.img, c.title, a.time_add , SUBSTRING(a.ids_shops,1,1) as ids_shops, a.ids_groups , c.seo_url, c.id_lang, '.$id_shop.' as id_shop, a.status ';
            //$this->_join .= '  LEFT JOIN `' . _DB_PREFIX_ . $this->_data_table.'` c ON (c.id_item = a.id and c.id_lang = '.(int)$id_lang.')';
        $this->_join .= '  LEFT JOIN `' . _DB_PREFIX_ . $this->_data_table.'` c ON (c.id_item = a.id)';


            //$this->_join .= '  LEFT JOIN `' . _DB_PREFIX_ . $this->_data_table_avatar.'` d ON (d.id_customer = a.author_id and FIND_IN_SET(d.id_shop,a.ids_shops))';

        $this->_select .= ', (select count(*) from `' . _DB_PREFIX_ . $this->_data_table.'` c where c.id_item = a.id) as count_posts_for_lang';

        $this->_select .= ', (SELECT group_concat(sh.`name` SEPARATOR \', \')
                    FROM `'._DB_PREFIX_.'shop` sh
                    WHERE sh.`active` = 1 AND sh.deleted = 0 AND sh.`id_shop`
                    IN(SELECT
                          SUBSTRING_INDEX(SUBSTRING_INDEX(pt_in.ids_shops, \',\', sh_in.id_shop), \',\', -1) name
                        FROM
                          '._DB_PREFIX_.'shop as sh_in INNER JOIN '._DB_PREFIX_.$this->table.' pt_in
                          ON CHAR_LENGTH(pt_in.ids_shops)
                             -CHAR_LENGTH(REPLACE(pt_in.ids_shops, \',\', \'\'))>=sh_in.id_shop-1
                        WHERE pt_in.id =  a.id
                        ORDER BY
                          id, sh_in.id_shop)
                    ) as shop_name';

            $this->_select .= ', (SELECT group_concat(l.`iso_code` SEPARATOR \', \')
	            FROM `'._DB_PREFIX_.'lang` l
	            JOIN
	            `'._DB_PREFIX_.'lang_shop` ls
	            ON(l.id_lang = ls.id_lang)
	            WHERE l.`active` = 1 AND ls.id_shop = '.(int)$id_shop.' AND l.`id_lang`
	            IN( select pt_d.id_lang FROM `'._DB_PREFIX_.$this->_data_table.'` pt_d WHERE pt_d.id_item = a.id)) as language';

            $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_comments` pc1
				    WHERE pc1.id_post = a.id) as count_comments ';

           /* $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_post_like` pclike
				    WHERE pclike.post_id = a.id) as count_likes ';*/

        $this->_where .= ' GROUP BY a.id';


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

            $this->path_img_cloud = $logo_img_path;
            // is cloud ?? //


            // is cloud ?? //
//            if(defined('_PS_HOST_MODE_')){
//                $logo_img_path_avatar = 'modules/'.$this->_name_module.'/upload/';
//            } else {
//                $logo_img_path_avatar = 'upload/'.$this->_name_module.'/';
//            }
            $logo_img_path_avatar = 'modules/'.$this->_name_module.'/upload/';
            $this->path_img_cloud_avatar = $logo_img_path_avatar;
                // is cloud ?? //

            $all_laguages = Language::getLanguages(true);


            $is16= 0;
            if(version_compare(_PS_VERSION_, '1.6', '>')) {
                $is16= 1;
            }



            $id_lang = $bloghelp_obj->getMostPopularLanguageForPosts(array('id_lang'=>$this->context->cookie->id_lang,'is_admin'=>1));

            $data_url = $bloghelp_obj->getSEOURLs(array('id_lang'=>$id_lang));
            $post_url = $data_url['post_url'];
            $author_url = $data_url['author_url'];


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

        $data_seo_uls_set = $bloghelp_obj->getSeoUrlsSettings(array('id_lang'=>$id_lang));

        $blog_alias = $data_seo_uls_set['blog_alias'];
        $blog_author_alias = $data_seo_uls_set['blog_author_alias'];

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

                    'is_rewrite' => $is_rewrite,
                    'iso_code' => count($all_laguages)>1?$this->_iso_code."/":"",
                    'count_all_lang'=>count($all_laguages),
                    'base_dir_ssl' => _PS_BASE_URL_SSL_."/",
                    'is16'=>$is16,
                    'item_url'=>$author_url,
                    'count_languages'=>count($all_laguages),
                    'data_shop_uris'=>$data_shop_uris,
                    'alias_url'=>$blog_author_alias,
                    'alias_url_blog'=>$blog_alias,
                ),

                'img' => array(
                    'title' => $this->l('Image'),
                    'width' => 'auto',
                    'search' => false,
                    'align' => 'center',
                    'logo_img_path' => $logo_img_path,
                    'type_custom' => 'img',
                    'orderby' => true,

                ),

                'title' => array(
                    'title' => $this->l('Title'),
                    'width' => 'auto',
                    'orderby' => true,
                    'type_custom' => 'title_post',
                    'is_rewrite' => $is_rewrite,
                    'iso_code' => count($all_laguages)>1?$this->_iso_code."/":"",
                    'count_all_lang'=>count($all_laguages),
                    'base_dir_ssl' => _PS_BASE_URL_SSL_."/",
                    'is16'=>$is16,
                    'item_url'=>$post_url,
                    'count_languages'=>count($all_laguages),
                    'data_shop_uris'=>$data_shop_uris,
                    'alias_url'=>$blog_alias,

                ),

                /*'count_comments' => array(
                    'title' => $this->l('Count comments'),
                    'width' => 'auto',
                    'search' => false,
                    'align' => 'center',

                ),*/



                'position' => array(
                    'title' => $this->l('Position'),
                    'filter_key' => 'a!position',
                    'position' => 'position',
                    'align' => 'center',
                    'class' => 'fixed-width-md',
                    'search' => false,

                ),

                'ids_groups' => array(
                    'title' => $this->l('Groups'),
                    'width' => 'auto',
                    'search' => true,
                    'align' => 'center',
                    'hint'=>$this->l('Group permissions'),

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

                'time_add' => array(
                    'title' => $this->l('Date add'),
                    'width' => 'auto',
                    'search' => false,

                ),

                'is_slider' => array(
                    'title' => $this->l('Slider'),
                    'width' => 'auto',
                    'align' => 'center',
                    'type' => 'bool',
                    'orderby' => FALSE,
                    'hint'=>$this->l('Post included in the Slider? Click to Include / Exclude post in the silder'),
                    'type_custom' => 'is_slider',
                    'token_custom'=>Tools::getAdminTokenLite('AdminBlockblogajax'),

                ),

                'is_comments' => array(
                    'title' => $this->l('Comments'),
                    'width' => 'auto',
                    'align' => 'center',
                    'type' => 'bool',
                    'orderby' => FALSE,
                    'hint'=>$this->l('Click to Enable / Disable Comments'),
                    'type_custom' => 'is_comments',
                    'token_custom'=>Tools::getAdminTokenLite('AdminBlockblogajax'),

                ),

                'is_fbcomments' => array(
                    'title' => $this->l('FB Comments'),
                    'width' => 'auto',
                    'align' => 'center',
                    'type' => 'bool',
                    'orderby' => FALSE,
                    'hint'=>$this->l('Click to Enable / Disable Facebook Comments'),
                    'type_custom' => 'is_fbcomments',
                    'token_custom'=>Tools::getAdminTokenLite('AdminBlockblogajax'),

                ),


                /*'status' => array(
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
                'width' => 'auto',
                'type' => 'select',
                'align' => 'center',
                'icon' => array(
                    0 => array('value'=>0),
                    1 => array('value'=>1),
                    2 => array('value'=>2),
                    3 => array('value'=>3),
                    4 => array('value'=>4),
                ),
                'list' => array(
                    0 => $this->l('No'),
                    1 => $this->l('Yes'),
                    2 => $this->l('The customer has deleted his post'),
                    3 => $this->l('The customer has changed his post'),
                    4 => $this->l('The customer has added new post'),
                ),
                'filter_key' => 'a!status',
                'orderby' => false,
                'type_custom' => 'is_active',
                'token_custom'=>Tools::getAdminTokenLite('AdminBlockblogajax'),
                'ajax_link' => $this->context->link->getAdminLink('AdminBlockblogajax'),

            ),


        );



        if(Configuration::get($this->_name_module.'ava_on') == 1){

            /*$this->array_push_pos($this->fields_list, 1,
                array(
                    'title' => $this->l('Avatar'),
                    'width' => 'auto',
                    'search' => false,
                    'align' => 'center',
                    'orderby' => FALSE,
                    'type_custom' => 'avatar',
                    'base_dir_ssl' => _PS_BASE_URL_SSL_.__PS_BASE_URI__,
                    'path_img_cloud'=>$this->path_img_cloud_avatar.'avatar'.DIRECTORY_SEPARATOR,

                ),

                'avatar'
            );*/
        }

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
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['add_item'] = array(
                'href' => self::$currentIndex.'&addblog_post&token='.$this->token,
                'desc' => $this->l('Add new post', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initToolbar() {

        parent::initToolbar();
        /*$this->toolbar_btn['add_item'] = array(
                                            'href' => self::$currentIndex.'&add'.$this->_name_module.'&token='.$this->token,
                                            'desc' => $this->l('Add new post', null, null, false),
                                        );
        *///unset($this->toolbar_btn['new']);

    }



    public function postProcess()
    {


        require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blogspm.class.php');
        $bloghelp_obj = new blogspm();


        if (Tools::isSubmit('add_item')) {
            ## add item ##
            //$seo_url = Tools::getValue("seo_url");
            $languages = Language::getLanguages(false);
            $data_title_content_lang = array();
            $data_validation = array();
            $data_validation_title = array();


            $ids_related_products = Tools::getValue("inputAccessories");

            $ids_related_posts = Tools::getValue("ids_related_posts");

            $time_add = Tools::getValue("time_add");

            $cat_shop_association = Tools::getValue("cat_shop_association");

            $group_association = Tools::getValue("group_association");

            $after_time_add = Tools::getValue("after_time_add");
            $is_rss = Tools::getValue("is_rss");
            $publish_date = Tools::getValue("publish_date");

            $info = Tools::getValue("info");

            foreach ($languages as $language){
                $id_lang = $language['id_lang'];
                $post_title = Tools::getValue("post_title_".$id_lang);
                $post_seokeywords = Tools::getValue("post_seokeywords_".$id_lang);
                $post_seodescription = Tools::getValue("post_seodescription_".$id_lang);
                $post_content = Tools::getValue("content_".$id_lang);
                $tags = Tools::getValue("tags_".$id_lang);
                $seo_url = Tools::getValue("seo_url_".$id_lang);

                if(Tools::strlen($post_title)>0 && Tools::strlen($post_content)>0)
                {
                    $data_title_content_lang[$id_lang] = array('post_title' => $post_title,
                                                                'post_seokeywords' => $post_seokeywords,
                                                                'post_seodescription' => $post_seodescription,
                                                                'post_content' => $post_content,
                                                                'tags' => $tags,
                                                                'seo_url' => $seo_url
                    );

                }

                if(Tools::strlen($post_content)>0) {
                    $data_validation[$id_lang] = array('post_content' => $post_content);
                }

                if(Tools::strlen($post_title)>0) {
                    $data_validation_title[$id_lang] = array('post_title' => $post_title);
                }

            }


            $ids_categories = Tools::getValue("ids_categories");
            $post_status = Tools::getValue("post_status");
            $post_iscomments = Tools::getValue("post_iscomments");
            $post_isfbcomments = Tools::getValue("post_isfbcomments");

            $author = Tools::getValue("author");
            $id_customer = Tools::getValue("id_customer");

            $post_images_ava = Tools::getValue("post_images_ava");
            $is_slider = Tools::getValue("is_slider");

            $data = array(

                        'data_title_content_lang'=>$data_title_content_lang,
                        'ids_categories' => $ids_categories,
                        'post_status' => $post_status,
                        'post_iscomments' => $post_iscomments,
                        'post_isfbcomments'=>$post_isfbcomments,
                        'cat_shop_association' => $cat_shop_association,
                        'related_products'=>$ids_related_products,
                        'ids_related_posts'=>$ids_related_posts,
                        'group_association' => $group_association,
                        'after_time_add'=>$after_time_add,
                        'publish_date'=>$publish_date,
                        'is_rss'=>$is_rss,
                        'time_add' => $time_add,
                        'info'=>$info,

                        'author'=>$author,

                        'id_customer' => $id_customer,

                        'post_images_ava' => $post_images_ava,
                        'is_slider'=>$is_slider,

            );


            if(sizeof($data_validation_title)==0)
                $this->errors[] = $this->l('Please fill the Title');

            if(sizeof($data_validation)==0)
                $this->errors[] = $this->l('Please fill the Content');

            if(!$author)
                $this->errors[] = $this->l('Please fill the Author name');


            if(!($group_association))
                $this->errors[] = $this->l('Please select the Group permissions');

            if(!($cat_shop_association))
                $this->errors[] = $this->l('Please select the Shop');
            if(!$time_add)
                $this->errors[] = $this->l('Please select Date Add');



            if (empty($this->errors)) {


                Db::getInstance()->Execute('BEGIN');
                $data_errors = $bloghelp_obj->savePost($data);
                $error = $data_errors['error'];
                if($error){
                    Db::getInstance()->Execute('ROLLBACK');
                    $error_text = $data_errors['error_text'];
                    $this->errors[] = $error_text;
                    $this->display = 'add';
                    return FALSE;
                } else {
                    Db::getInstance()->Execute('COMMIT');
                    Tools::redirectAdmin(self::$currentIndex . '&conf=3&token=' . Tools::getAdminTokenLite($this->_name_controller));
                }
            } else {
                $this->display = 'add';
                return FALSE;
             }
            ## add item ##

        } elseif(Tools::isSubmit('update_item')) {
                $id = Tools::getValue('id_tabs');


                //echo "<pre>"; var_dump($_FILES);exit;


                //$seo_url = Tools::getValue("seo_url");
                $languages = Language::getLanguages(false);
                $data_title_content_lang = array();
                $data_validation = array();
                $data_validation_title = array();

                $ids_related_products = Tools::getValue("inputAccessories");

                $ids_related_posts = Tools::getValue("ids_related_posts");


                $time_add = Tools::getValue("time_add");
                $cat_shop_association = Tools::getValue("cat_shop_association");

                $group_association = Tools::getValue("group_association");

                $after_time_add = Tools::getValue("after_time_add");
                $is_rss = Tools::getValue("is_rss");
                $publish_date = Tools::getValue("publish_date");

                $info = Tools::getValue("info");


                foreach ($languages as $language){
                    $id_lang = $language['id_lang'];
                    $post_title = Tools::getValue("post_title_".$id_lang);
                    $post_seokeywords = Tools::getValue("post_seokeywords_".$id_lang);
                    $post_seodescription = Tools::getValue("post_seodescription_".$id_lang);
                    $post_content = Tools::getValue("content_".$id_lang);
                    $tags = Tools::getValue("tags_".$id_lang);
                    $seo_url = Tools::getValue("seo_url_".$id_lang);

                    if(Tools::strlen($post_title)>0 && Tools::strlen($post_content)>0)
                    {
                        $data_title_content_lang[$id_lang] = array('post_title' => $post_title,
                                                                    'post_seokeywords' => $post_seokeywords,
                                                                    'post_seodescription' => $post_seodescription,
                                                                    'post_content' => $post_content,
                                                                    'seo_url'=>$seo_url,
                                                                    'tags' => $tags,
                        );

                    }

                    if(Tools::strlen($post_content)>0) {
                        $data_validation[$id_lang] = array('post_content' => $post_content);
                    }

                    if(Tools::strlen($post_title)>0) {
                        $data_validation_title[$id_lang] = array('post_title' => $post_title);
                    }
                }


                $ids_categories = Tools::getValue("ids_categories");
                $post_status = Tools::getValue("post_status");
                $post_iscomments = Tools::getValue("post_iscomments");
                $post_images = Tools::getValue("post_images");
                $post_isfbcomments = Tools::getValue("post_isfbcomments");

                $author = Tools::getValue("author");

                $post_images_ava = Tools::getValue("post_images_ava");
                $id_customer = Tools::getValue("id_customer");

                //$count_views = Tools::getValue("count_views");
                //$count_likes = Tools::getValue("count_likes");

                $is_slider = Tools::getValue("is_slider");

                $data = array('data_title_content_lang'=>$data_title_content_lang,
                            'ids_categories' => $ids_categories,
                            'post_status' => $post_status,
                            'post_iscomments' => $post_iscomments,
                            'post_isfbcomments'=>$post_isfbcomments,
                            'id_editposts' => $id,
                            'post_images' => $post_images,
                            'cat_shop_association' => $cat_shop_association,
                            'related_products'=>$ids_related_products,
                            'ids_related_posts'=>$ids_related_posts,
                            'group_association' => $group_association,
                            'is_rss'=>$is_rss,
                            'after_time_add' => $after_time_add,
                            'publish_date'=>$publish_date,
                            'time_add' => $time_add,
                            'author'=>$author,
                            'info'=>$info,



                            'post_images_ava' => $post_images_ava,
                            'id_customer' => $id_customer,

                            //'count_views'=>$count_views,
                            //'count_likes'=>$count_likes,

                            'is_slider'=>$is_slider,
                );



                if(sizeof($data_title_content_lang)==0)
                    $this->errors[] = $this->l('Please fill the Title');

                if(sizeof($data_validation)==0)
                    $this->errors[] = $this->l('Please fill the Content');

                if(!$author)
                    $this->errors[] = $this->l('Please fill the Author name');

                if(!($cat_shop_association))
                    $this->errors[] = $this->l('Please select the Shop');

                if(!($group_association))
                    $this->errors[] = $this->l('Please select the Group permissions');

                if(!$time_add)
                    $this->errors[] = $this->l('Please select Date Add');

                /*if($count_views == NULL)
                    $this->errors[] = $this->l('Please fill the Count Views');

                if($count_likes == NULL)
                    $this->errors[] = $this->l('Please fill the Count Likes');*/





             if (empty($this->errors)) {

                 $data_errors = $bloghelp_obj->updatePost($data);
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
            } elseif (Tools::isSubmit('submitBulkdelete' . $this->table)) {
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

                $id = Tools::getValue('id_tabs');

                $bloghelp_obj->deletePost(array('id' => $id));

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

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/css/blog17.css');

        $this->context->controller->addCSS(__PS_BASE_URI__.'js/jquery/plugins/autocomplete/jquery.autocomplete.css');
        $this->context->controller->addJs(__PS_BASE_URI__.'js/jquery/plugins/autocomplete/jquery.autocomplete.js');

        $this->context->controller->addJs(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/js/image-files.js');
        $this->context->controller->addJs(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/js/admin.js');

        $this->context->controller->addJs(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/js/custom_menu.js');


        $this->addJqueryUi(array('ui.core','ui.widget','ui.datepicker'));

        $this->addJqueryPlugin('tagify');

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/css/font-custom.min.css');

    }


    public function renderForm()
    {
        if (!($this->loadObject(true)))
            return;

        if (Validate::isLoadedObject($this->object)) {
            $this->display = 'update';
        } else {
            $this->display = 'add';
        }


        $id = (int)Tools::getValue('id_tabs');

        require_once(_PS_MODULE_DIR_ . ''.$this->_name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();

        $data_url = $obj_blog->getSEOURLs();
        $ajax_url = $data_url['ajax_url'];

        $related_categories  = $obj_blog->getCategories(array('admin'=>1));
        $related_posts  = $obj_blog->getRelatedPosts(array('admin'=>1,'id'=>$id));


        // is cloud ?? //
//        if(defined('_PS_HOST_MODE_')){
//            $logo_img_path_ralated = '../modules/'.$this->_name_module.'/upload/';
//        } else {
//            $logo_img_path_ralated = '../upload/'.$this->_name_module.'/';
//        }
        $logo_img_path_ralated = '../modules/'.$this->_name_module.'/upload/';
        // is cloud ?? //




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


        $id_lang =  $this->context->cookie->id_lang;
        $data_seo_url = $obj_blog->getSEOURLs();

        if($id) {

            $_data = $obj_blog->getPostItem(array('id'=>$id));

            //echo "<pre>";var_dump($_data);exit;

            //$count_like =  isset($_data['post'][0]['count_like']) ? $_data['post'][0]['count_like'] : 0 ;
            //$count_views =  isset($_data['post'][0]['count_views']) ? $_data['post'][0]['count_views'] : 0 ;

            $id_shop = isset($_data['post'][0]['ids_shops']) ? explode(",",$_data['post'][0]['ids_shops']) : array();
            $ids_groups = isset($_data['post'][0]['ids_groups']) ? explode(",",$_data['post'][0]['ids_groups']) : array();
            $time_add = isset($_data['post'][0]['time_add']) ? $_data['post'][0]['time_add'] :date("Y-m-d H:i:s") ;
            $logo_img = isset($_data['post'][0]['img']) ? $_data['post'][0]['img'] :'' ;


            $avatar =  isset($_data['post'][0]['avatar']) ? $_data['post'][0]['avatar'] :'' ;
            $is_exist_ava =  isset($_data['post'][0]['is_exist_ava']) ? $_data['post'][0]['is_exist_ava'] :0 ;
            $id_customer =  isset($_data['post'][0]['author_id']) ? $_data['post'][0]['author_id'] : 0 ;


            $author =  isset($_data['post'][0]['author']) ? $_data['post'][0]['author'] :'' ;
            $customer_name = isset($_data['post'][0]['customer_name']) ? $_data['post'][0]['customer_name'] :'admin' ;
            $admin_url_to_customer = isset($_data['post'][0]['author_url']) ? $_data['post'][0]['author_url'].$id_customer."-".$author :0 ;




            // is cloud ?? //
//            if(defined('_PS_HOST_MODE_')){
//                $logo_img_path = '../modules/'.$this->_name_module.'/upload/'.$logo_img;
//            } else {
//                $logo_img_path = '../upload/'.$this->_name_module.'/'.$logo_img;
//            }
            $logo_img_path = '../modules/'.$this->_name_module.'/upload/'.$logo_img;
            // is cloud ?? //

            $category_ids = isset($_data['post'][0]['category_ids']) ?$_data['post'][0]['category_ids']: array();
            $related_posts_selected = isset($_data['post'][0]['related_posts']) ?explode(",",$_data['post'][0]['related_posts']): array();
            $related_products_selected = isset($_data['post'][0]['related_products']) ?$_data['post'][0]['related_products']: 0;

            $publish_date = isset($_data['post'][0]['publish_date']) ? $_data['post'][0]['publish_date'] :date("Y-m-d H:i:s",strtotime("-1 day")) ;

            if($publish_date == '0000-00-00 00:00:00'){
                $publish_date = date("Y-m-d H:i:s",strtotime("-1 day"));
            }

            $after_time_add = isset($_data['post'][0]['after_time_add']) ? $_data['post'][0]['after_time_add'] :0 ;

            $status = isset($_data['post'][0]['status']) ? $_data['post'][0]['status'] :0 ;


            //$is_slider =  isset($_data['post'][0]['is_slider']) ? $_data['post'][0]['is_slider'] : 0 ;

        } else {


            $id_shop = Tools::getValue('cat_shop_association')?Tools::getValue('cat_shop_association'):array();

            $time_add = date("Y-m-d H:i:s");
            $publish_date = date("Y-m-d H:i:s",strtotime("-1 day"));
            $logo_img = '';
            $logo_img_path = '';
            $category_ids = array();
            $related_posts_selected = array();
            $related_products_selected = 0;
            $ids_groups = Tools::getValue('group_association')?Tools::getValue('group_association'):array();
            $after_time_add = 0;


            $id_customer = 0;

            $status = '';

            $info_path = $obj_blog->getAvatarPath(array('id_customer'=>$id_customer));

            $avatar = isset($info_path['avatar'])?$info_path['avatar']:'';
            $is_exist_ava = $info_path['is_exist'];


            $data_customer_info = $obj_blog->getCustomerInfo(array('id_customer'=>$id_customer));
            $customer_name = $data_customer_info['customer_name'];



            $admin_url_to_customer = $data_seo_url['author_url'].$id_customer."-".$customer_name;

            //$count_like =  0;
            //$count_views =  0;

            //$is_slider = 0;

        }




        $related_products = (($related_products_selected!=0) ? $obj_blog->getProducts($related_products_selected) : array());


        if($id){
            $title_item_form = $this->l('Edit post:');
        } else{
            $title_item_form = $this->l('Add new post:');
        }



        $all_laguages = Language::getLanguages(true);

        if($obj_blog->isURLRewriting()){
            $is_rewrite = 1;
        } else {
            $is_rewrite = 0;
        }

        $is16= 0;
        if(version_compare(_PS_VERSION_, '1.6', '>')) {
            $is16= 1;
        }

        $data_lng = Language::getLanguage($id_lang);
        $iso_code = $data_lng['iso_code'];

        $data_url = $obj_blog->getSEOURLs();
        $post_url = $data_url['post_url'];
        $category_url =$data_url['category_url'];


        $data_seo_urls_set = $obj_blog->getSeoUrlsSettings(array('id_lang'=>$id_lang));

        $blog_alias_blog = $data_seo_urls_set['blog_alias'];
        $blog_alias = $data_seo_urls_set['blog_author_alias'];


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


        $this->fields_form = array(
            'tinymce' => TRUE,
            'legend' => array(
                'title' => $title_item_form,
                //'icon' => 'fa fa-list fa-lg'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'post_title',
                    'id' => 'post_title',
                    'lang' => true,
                    'required' => TRUE,
                    'size' => 5000,
                    'maxlength' => 5000,
                ),



                array(
                    'type' => 'textarea',
                    'label' => $this->l('SEO Keywords'),
                    'name' => 'post_seokeywords',
                    'id' => 'post_seokeywords',
                    'required' => FALSE,
                    'autoload_rte' => FALSE,
                    'lang' => TRUE,
                    'rows' => 8,
                    'cols' => 40,

                ),

                array(
                    'type' => 'textarea',
                    'label' => $this->l('SEO Description'),
                    'name' => 'post_seodescription',
                    'id' => 'post_seodescription',
                    'required' => FALSE,
                    'autoload_rte' => FALSE,
                    'lang' => TRUE,
                    'rows' => 8,
                    'cols' => 40,

                ),

                array(
                    'type' => 'textarea',
                    'label' => $this->l('Content'),
                    'name' => 'content',
                    'id' => 'content',
                    'required' => TRUE,
                    'autoload_rte' => TRUE,
                    'lang' => TRUE,
                    'rows' => 8,
                    'cols' => 40,

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
                    'type' => 'textarea',
                    'label' => $this->l('Author information'),
                    'name' => 'info',
                    'id' => 'info',
                    'required' => false,
                    'autoload_rte' => false,
                    'lang' => false,
                    'rows' => 4,
                    'cols' => 30,

                ),

                array(
                    'type' => 'tags',
                    'label' => $this->l('Tags'),
                    'name' => 'tags',
                    'lang' => true,
                    'hint' => array(
                        //$this->l('Invalid characters:').' &lt;&gt;;=#{}',
                        $this->l('To add "tags" click in the field, write something, and then press "Enter."')
                    ),
                    'desc' => array(
                        //$this->l('Invalid characters:').' &lt;&gt;;=#{}',
                        $this->l('To add "tags" click in the field, write something, and then press "Enter."')
                    )
                ),

                array(
                    'type' => 'post_image_custom',
                    'label' => $this->l('Logo Image'),
                    'name' => 'post_image',
                    'logo_img'=>$logo_img,
                    'logo_img_path' => $logo_img_path,
                    'id_post' => $id,
                    'required' => FALSE,
                    'desc' => $this->l('Allow formats *.jpg; *.jpeg; *.png; *.gif.'),
                    'is_demo' => $is_demo,
                    'max_upload_info' => ini_get('upload_max_filesize'),
                    'post_max_size'=>ini_get('post_max_size'),
                    'ajax_url' =>$ajax_url,
                ),

                array(
                    'type' => 'related_categories',
                    'label' => $this->l('Select categories'),
                    'name' => 'ids_categories',
                    'values'=>$related_categories['categories'],
                    'selected_data'=>$category_ids,
                    'required' => false,
                    'name_field_custom'=>'ids_categories',
                    'logo_img_path' => $logo_img_path_ralated,
                    'is_post'=>0,

                    'is_rewrite' => $is_rewrite,
                    'iso_code' => count($all_laguages)>1?$iso_code."/":"",
                    'base_dir_ssl' => _PS_BASE_URL_SSL_.__PS_BASE_URI__,
                    'id_shop' => $id_shop,
                    'id_lang'=>$id_lang,
                    'is16'=>$is16,
                    'item_url'=>$category_url,

                ),

                array(
                    'type' => 'related_products',
                    'label' => $this->l('Related Products'),
                    'name' => 'related_products',
                    'values'=>$related_products,
                    'selected_data'=>$related_products_selected,
                    'required' => false,
                    'desc' => $this->l('Begin typing the first letters of the product name, then select the product from the drop-down list'),
                    'token_custom'=>Tools::getAdminTokenLite('AdminProducts'),
                    'is_ps1760'=>(version_compare(_PS_VERSION_, '1.7.6', '>=')?1:0),
                    //'admin_products_link' => $this->context->link->getAdminLink('AdminProductsController'),


                ),

                array(
                    'type' => 'related_categories',
                    'label' => $this->l('Related posts'),
                    'name' => 'ids_categories',
                    'values'=>$related_posts['related_posts'],
                    'selected_data'=>$related_posts_selected,
                    'required' => false,
                    'name_field_custom'=>'ids_related_posts',
                    'logo_img_path' => $logo_img_path_ralated,
                    'is_post'=>1,


                    'is_rewrite' => $is_rewrite,
                    'iso_code' => count($all_laguages)>1?$iso_code."/":"",
                    'base_dir_ssl' => _PS_BASE_URL_SSL_.__PS_BASE_URI__,
                    'id_shop' => $id_shop,
                    'id_lang'=>$id_lang,
                    'is16'=>$is16,
                    'item_url'=>$post_url,

                ),



                array(
                    'type' => 'cms_pages',
                    'label' => $this->l('Shop association'),
                    'name' => 'cat_shop_association',
                    'values'=>Shop::getShops(),
                    'selected_data'=>$id_shop,
                    'required' => TRUE,
                ),

                array(
                    'type' => 'group_association',
                    'label' => $this->l('Group permissions'),
                    'name' => 'group_association',
                    'values'=>Group::getGroups($id_lang),
                    'selected_data'=>$ids_groups,
                    'required' => TRUE,
                    'desc' => $this->l('Select customers group who can view post')
                ),

                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Comments'),
                    'name' => 'post_iscomments',
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
                ),

                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Facebook Comments'),
                    'name' => 'post_isfbcomments',
                    'required' => FALSE,
                    'class' => 't',
                    'is_bool' => TRUE,
                    'desc' => (Configuration::get($this->_name_module.'appid')?'':'<b style="color:red">'.$this->l('Make sure that you have set up the Facebook API in the module settings').'</b>'),
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
                ),

                array(
                    'type' => 'item_date',
                    'label' => $this->l('Date Add'),
                    'name' => 'date_on',
                    'time_add' => $time_add,
                    'required' => TRUE,
                ),

                array(
                    'type' => 'switch',
                    'label' => $this->l('Display post after Publish date'),
                    'name' => 'after_time_add',
                    'desc' => $this->l('If you created a new blog post and set a "Publish date" in the future (2025-05-16 13:18:14 - for example) and set status to "yes". The blog post isn\'t displayed on the frontend before "Publish date" 2025-05-16 13:18:14.'),
                    'required' => FALSE,
                    'class' => 't',
                    'is_bool' => TRUE,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),

                array(
                    'type' => 'publish_date',
                    'label' => $this->l('Publish date'),
                    'name' => 'publish_date',
                    'publish_date' => $publish_date,
                    'required' => FALSE,
                ),

                array(
                    'type' => 'switch',
                    'label' => $this->l('Include post in the RSS feed'),
                    'name' => 'is_rss',
                    'required' => FALSE,
                    'class' => 't',
                    'is_bool' => TRUE,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),

                array(
                    'type' => 'switch',
                    'label' =>  $this->l('Include post in the Slider'),
                    'desc'=>$this->l('Post must contain image'),
                    'name' => 'is_slider',
                    'required' => FALSE,
                    'class' => 't',
                    'is_bool' => TRUE,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),

                /*array(
                    'type' => 'text',
                    'label' => $this->l('Count Views'),
                    'name' => 'count_views',
                    'id' => 'count_views',
                    'lang' => false,
                    'required' => TRUE,
                    'size' => 1024,
                    'maxlength' => 1024,
                ),

                array(
                    'type' => 'text',
                    'label' => $this->l('Count Likes'),
                    'name' => 'count_likes',
                    'id' => 'count_likes',
                    'lang' => false,
                    'required' => TRUE,
                    'size' => 1024,
                    'maxlength' => 1024,
                ),*/



            ),


        );


        if(in_array($status,array(2,3,4))){

            $this->array_push_pos($this->fields_form['input'], 20,
                array(
                    'type' => 'select',
                    'label' => $this->l('Status'),
                    'name' => 'post_status',
                    'required' => FALSE,


                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 0,
                                'name' => $this->l('Disabled')),

                            array(
                                'id' => 1,
                                'name' => $this->l('Enabled'),
                            ),

                            array(
                                'id' => 2,
                                'name' => $this->l('The customer has deleted his post'),
                            ),

                            array(
                                'id' => 3,
                                'name' => $this->l('The customer has changed his post'),
                            ),

                            array(
                                'id' => 4,
                                'name' => $this->l('The customer has added new post'),
                            ),


                        ),

                        'id' => 'id',
                        'name' => 'name'
                    ),



                )
            );

        } else {
            $this->array_push_pos($this->fields_form['input'], 20,
                array(
                    'type' => 'select',
                    'label' => $this->l('Status'),
                    'name' => 'post_status',
                    'required' => FALSE,


                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 0,
                                'name' => $this->l('Disabled')),

                            array(
                                'id' => 1,
                                'name' => $this->l('Enabled'),
                            ),



                        ),

                        'id' => 'id',
                        'name' => 'name'
                    ),



                )
            );
        }


        if($obj_blog->isURLRewriting()){
            $this->array_push_pos($this->fields_form['input'],1,
                array(
                    'type' => 'text',
                    'label' => $this->l('Identifier (SEO URL)'),
                    'name' => 'seo_url',
                    'id' => 'seo_url',
                    'lang' => true,
                    'required' => FALSE,
                    'size' => 5000,
                    'maxlength' => 5000,
                    'desc' => $this->l('You can leave the field blank - then Identifier (SEO URL) is generated automatically!').' (eg: http://domain.com/blog/p-identifier)',



                )
            );
        }






        if(Configuration::get($this->_name_module.'ava_on') == 1) {


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
                    'logo_img_path' => $logo_img_path,

                    'id_item' => $id,

                    'desc' => $this->l('Allow formats *.jpg; *.jpeg; *.png; *.gif.'),
                    'is_demo' => $is_demo,
                    'max_upload_info' => ini_get('upload_max_filesize'),
                    'post_max_size'=>ini_get('post_max_size'),

                    'id_customer' => $id_customer,
                    'is_exist_ava' => $is_exist_ava,
                    'ajax_url'=>$ajax_url,



                )
            );


            $this->array_push_pos($this->fields_form['input'], 7,
                array(
                    'type' => 'customer_url',
                    'label' => $this->l('Author:'),

                    'name' => 'customer_url',
                    'values' => $customer_name,
                    'url' => $admin_url_to_customer,

                    'is_rewrite' => $is_rewrite,
                    'iso_code' => count($all_laguages)>1?$this->_iso_code."/":"",
                    'base_dir_ssl' => _PS_BASE_URL_SSL_."/",

                    'is16'=>$is16,
                    'count_languages'=>count($all_laguages),

                    'data_shop_uris'=>$data_shop_uris,
                    'alias_url_blog'=>$blog_alias_blog,
                    'alias_url'=>$blog_alias,


                    'id_customer'=>$id_customer,
                    'author_url'=>$data_seo_url['author_url'],

                    'id_shop'=>current($id_shop),



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
        } else {
            $this->submit_action = 'add_item';
            $this->tpl_form_vars = array(
                'fields_value' => $this->getConfigFieldsValuesForm(array('id'=>0)),
            );
        }


        ob_start();
        include(dirname(__FILE__).'/../../views/templates/hooks/' . $this->_name_module . '/script_blockblogposts.phtml');
        $_html = ob_get_clean();

        return parent::renderForm().$_html;
    }


    

    public function getConfigFieldsValuesForm($data_in){



        $id = (int)Tools::getValue('id_tabs');
        if($id !== null) {
            $id = $data_in['id'];
            require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blogspm.class.php');
            $obj_blog = new blogspm();
            $_data = $obj_blog->getPostItem(array('id'=>$id));


            $languages = Language::getLanguages(false);
            $fields_title = array();
            $fields_seo_keywords = array();
            $fields_seo_description = array();
            $fields_content = array();
            $fields_tags = array();
            $seo_url = array();


            foreach ($languages as $lang)
            {
                $fields_title[$lang['id_lang']] = isset($_data['post']['data'][$lang['id_lang']]['title'])?$_data['post']['data'][$lang['id_lang']]['title']:'';

                $fields_seo_keywords[$lang['id_lang']] = isset($_data['post']['data'][$lang['id_lang']]['seo_keywords'])?$_data['post']['data'][$lang['id_lang']]['seo_keywords']:'';

                $fields_seo_description[$lang['id_lang']] = isset($_data['post']['data'][$lang['id_lang']]['seo_description'])?$_data['post']['data'][$lang['id_lang']]['seo_description']:'';

                $fields_content[$lang['id_lang']] = isset($_data['post']['data'][$lang['id_lang']]['content'])?$_data['post']['data'][$lang['id_lang']]['content']:'';

                $fields_tags[$lang['id_lang']] = isset($_data['post']['data'][$lang['id_lang']]['tags'])?$_data['post']['data'][$lang['id_lang']]['tags']:'';

                $seo_url[$lang['id_lang']] = isset($_data['post']['data'][$lang['id_lang']]['seo_url'])?$_data['post']['data'][$lang['id_lang']]['seo_url']:'';
            }

            //$seo_url = isset($_data['post']['data'][$this->_id_lang]['seo_url'])?$_data['post']['data'][$this->_id_lang]['seo_url']:"";
            $status = isset($_data['post'][0]['status'])?$_data['post'][0]['status']:0;
            $is_comments = isset($_data['post'][0]['is_comments'])?$_data['post'][0]['is_comments']:0;

            $after_time_add = isset($_data['post'][0]['after_time_add'])?$_data['post'][0]['after_time_add']:0;
            $is_rss = isset($_data['post'][0]['is_rss'])?$_data['post'][0]['is_rss']:1;

            $post_isfbcomments = isset($_data['post'][0]['is_fbcomments'])?$_data['post'][0]['is_fbcomments']:0;

            $author = isset($_data['post'][0]['author'])?$_data['post'][0]['author']:'admin';
            $info = isset($_data['post'][0]['info'])?$_data['post'][0]['info']:'';


            if($id == 0) {
                include_once(_PS_MODULE_DIR_ . $this->_name_module . '/classes/userprofileblockblog.class.php');
                $obj_userprofileblockblog = new userprofileblockblog();
                $data_customer_info = $obj_userprofileblockblog->getCustomerInfo(array('id_customer' => 0));
                $info = $data_customer_info['info'];
            }


            //$count_views = isset($_data['post'][0]['count_views'])?$_data['post'][0]['count_views']:0;
            //$count_likes = isset($_data['post'][0]['count_likes'])?$_data['post'][0]['count_likes']:0;

            $is_slider = isset($_data['post'][0]['is_slider'])?$_data['post'][0]['is_slider']:0;

            $config_array = array(
                'post_title' => $fields_title,
                'content'=>$fields_content,
                'seo_url' => $seo_url,
                'post_seokeywords' => $fields_seo_keywords,
                'post_seodescription' => $fields_seo_description,
                'post_iscomments' => $is_comments,
                'post_isfbcomments' => $post_isfbcomments,
                'post_status' => $status,
                'tags'=>$fields_tags,
                'after_time_add'=>$after_time_add,
                'is_rss'=>$is_rss,
                'author'=>$author,
                //'count_views'=>$count_views,
                //'count_likes'=>$count_likes,
                'is_slider'=>$is_slider,
                'info'=>$info,
            );
        } else {


            $config_array = array('author'=>'admin',);
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


    public function ajaxProcessUpdatePositions()
    {

        $way = (int)Tools::getValue('way');
        $id_attribute_group = (int)Tools::getValue('id');
        $positions = Tools::getValue('tabs');

        $new_positions = array();
        foreach ($positions as $v) {
            if (count(explode('_', $v)) == 4) {
                $new_positions[] = $v;
            }
        }

        foreach ($new_positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int)$pos[2] === $id_attribute_group) {
                if ($object = new $this->className((int)$pos[2])) {
                    if (isset($position) && $object->updatePosition($way, $position)) {
                        echo 'ok position '.(int)$position.' for item '.(int)$pos[2].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update the '.(int)$id_attribute_group.' item to position '.(int)$position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "The ('.(int)$id_attribute_group.') item cannot be loaded."}';
                }

                break;
            }
        }


    }
	
}





?>

