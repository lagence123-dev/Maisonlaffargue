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

class BlockblogBlogModuleFrontController extends ModuleFrontController
{

    public $php_self;
    private $_name_module = "blockblog";

    public $display_column_left = false;
    public $display_column_right = false;

    public function __construct()
    {
        parent::__construct();


        $p = Tools::getValue('p');




        include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();



        if($obj_blog->isURLRewriting()) {

            ### enabled URL Rewriting ##

            $data_p = explode("-", $p);



            $current_item = current($data_p);



            if(Tools::strlen($current_item)>0){

                switch($current_item){
                    case 'p':

                        // post page
                        if(Configuration::get($this->_name_module.'sidebar_posblog_post_alias')==2)
                            $this->display_column_right=true;
                        if(Configuration::get($this->_name_module.'sidebar_posblog_post_alias')==1)
                            $this->display_column_left =true;

                        break;
                    case 'c':
                        // category page
                        if(Configuration::get($this->_name_module.'sidebar_posblog_cat_item_alias')==2)
                            $this->display_column_right=true;
                        if(Configuration::get($this->_name_module.'sidebar_posblog_cat_item_alias')==1)
                            $this->display_column_left =true;

                        break;
                    default:



                        if(ctype_digit($p)) {



                        } else {



                            if(Configuration::get($this->_name_module.'sidebar_posblog_alias')==2)
                                $this->display_column_right=true;
                            if(Configuration::get($this->_name_module.'sidebar_posblog_alias')==1)
                                $this->display_column_left =true;
                        }
                        break;
                }

            } else {

                if(ctype_digit($p)) {



                } else {

                    if(Configuration::get($this->_name_module.'sidebar_posblog_alias')==2)
                        $this->display_column_right=true;
                    if(Configuration::get($this->_name_module.'sidebar_posblog_alias')==1)
                        $this->display_column_left =true;
                }
            }

            ### enabled URL Rewriting ##


        } else {

            ### disabled URL Rewriting ##

            $post_id = Tools::getValue('post_id');
            if($post_id){
                if(Configuration::get($this->_name_module.'sidebar_posblog_post_alias')==2)
                    $this->display_column_right=true;
                if(Configuration::get($this->_name_module.'sidebar_posblog_post_alias')==1)
                    $this->display_column_left =true;
            }

            $category_id = Tools::getValue('category_id');


            if($category_id){
                if(Configuration::get($this->_name_module.'sidebar_posblog_cat_item_alias')==2)
                    $this->display_column_right=true;
                if(Configuration::get($this->_name_module.'sidebar_posblog_cat_item_alias')==1)
                    $this->display_column_left =true;

            }




            if(!$post_id && !$category_id){
                if(ctype_digit($p)) {



                } else {


                    if(Configuration::get($this->_name_module.'sidebar_posblog_alias')==2)
                        $this->display_column_right=true;
                    if(Configuration::get($this->_name_module.'sidebar_posblog_alias')==1)
                        $this->display_column_left =true;
                }
            }

            ### disabled URL Rewriting ##

        }

    }
	
	public function init()
	{

		parent::init();
	}
	
	public function setMedia()
	{



        parent::setMedia();

        $name_module = $this->_name_module;
        if(Configuration::get($name_module.'blog_post_effect') != "disable_all_effects" ||
            Configuration::get($name_module.'blog_com_effect') != "disable_all_effects" ||
            Configuration::get($name_module.'blog_comp_effect') != "disable_all_effects"
        ) {

            $this->context->controller->addJs(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/js/wow.js');
            $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/css/animate.css');
        }

        $this->context->controller->addJs(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/js/owl.carousel.js');
        $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/css/owl.carousel.css');
        $this->context->controller->addCSS(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/css/owl.theme.default.css');

        if(Configuration::get($name_module . 'slider_b_on')){
            $this->context->controller->addJs(__PS_BASE_URI__ . 'modules/' . $name_module . '/views/js/fancytr/jqFancyTransitions.js');
        }

    }

	
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{



        //$name_module = $this->_name_module;

        parent::initContent();

        $p = Tools::getValue('p');




        include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();


        /*if(ctype_digit($p)){
            // blog pagination
            $this->_bloglist();

        } else {*/

            if($obj_blog->isURLRewriting()) {

                ### enabled URL Rewriting ##

                $data_p = explode("-", $p);



                $current_item = current($data_p);



                if(Tools::strlen($current_item)>0){

                    switch($current_item){
                        case 'p':

                            // post page
                            $this->_blogpost();
                        break;
                        case 'c':
                            // category page
                            $this->_blogcategory();

                        break;
                        default:



                            if(ctype_digit($p)) {



                                $data_url = $obj_blog->getSEOURLs();
                                $blog_url = $data_url['posts_url'];
                                Tools::redirect($blog_url);

                            } else {



                                $this->_bloglist();
                            }
                        break;
                    }

                } else {

                    if(ctype_digit($p)) {

                        include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/blogspm.class.php');
                        $obj_blog = new blogspm();

                        $data_url = $obj_blog->getSEOURLs();
                        $blog_url = $data_url['posts_url'];
                        Tools::redirect($blog_url);

                    } else {

                        $this->_bloglist();
                    }
                }

                ### enabled URL Rewriting ##


            } else {

                ### disabled URL Rewriting ##

                $post_id = Tools::getValue('post_id');
                if($post_id){
                    $this->_blogpost();
                }

                $category_id = Tools::getValue('category_id');


                if($category_id){
                    $this->_blogcategory();

                }




                if(!$post_id && !$category_id){
                    if(ctype_digit($p)) {

                        include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/blogspm.class.php');
                        $obj_blog = new blogspm();


                        $data_url = $obj_blog->getSEOURLs();
                        $blog_url = $data_url['posts_url'];
                        Tools::redirect($blog_url);

                    } else {


                        $this->_bloglist();
                    }
                }

                ### disabled URL Rewriting ##

            }




        //}



	}

    private function _blogcategory(){



        /*if(Configuration::get($this->_name_module.'sidebar_posblog_cat_item_alias')==2)
            $this->display_column_right=true;
        if(Configuration::get($this->_name_module.'sidebar_posblog_cat_item_alias')==1)
            $this->display_column_left =true;*/

        $name_module = $this->_name_module;

        $this->php_self = 'module-'.$name_module.'-category';

        include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();


        if($obj_blog->isURLRewriting()) {
            $category_id = Tools::getValue('p');
            $category_id = Tools::substr($category_id,2);
            //$category_id = str_replace("c-","",$category_id);
        } else {
            $category_id = Tools::getValue('category_id');
        }



        $category_id_page = $category_id;







        include_once(_PS_MODULE_DIR_.$this->_name_module.'/blockblog.php');
        $obj_blockblog = new blockblog();

        $obj_blockblog->setSEOUrls();

        $obj_blockblog->setControllersSettings();

        if($obj_blog->isURLRewriting() && is_numeric($category_id)){
            // redirect to seo url
            $seo_url_cat = $obj_blog->getSEOURLForCategory(array('id'=>$category_id));

            $data_url = $obj_blog->getSEOURLs();
            $category_url = $data_url['categories_url'];


            Tools::redirect($category_url.$seo_url_cat);
        }

        $category_id = $obj_blog->getTransformSEOURLtoID(array('id'=>$category_id));



        $_info_cat = $obj_blog->getCategoryItem(array('id' => $category_id));



        if(empty($_info_cat['category'][0]['id'])){
            $data_url = $obj_blog->getSEOURLs();
            $blog_url = $data_url['categories_url'];

            Tools::redirect($blog_url);
        }

        $title = isset($_info_cat['category'][0]['title'])?$_info_cat['category'][0]['title']:'';
        $seo_description = isset($_info_cat['category'][0]['seo_description'])?$_info_cat['category'][0]['seo_description']:'';
        $seo_keywords = isset($_info_cat['category'][0]['seo_keywords'])?$_info_cat['category'][0]['seo_keywords']:'';


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title;
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $seo_description;
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $seo_keywords;
        }

        $this->context->smarty->assign('meta_title', $title);
        $this->context->smarty->assign('meta_description', $seo_description);
        $this->context->smarty->assign('meta_keywords', $seo_keywords);





        /// set settings ///
        $obj_blockblog->setControllersSettings();
        /// set settings ///



        $p = (int)Tools::getValue('p');
        $step = (int) Configuration::get($name_module.'perpage_posts');

        $start = (int)(($p - 1)*$step);
        if($start<0)
            $start = 0;

        $_data = $obj_blog->getPosts(array('start'=>$start,'step'=>$step,'id'=>$category_id));




        $_data_translate = $obj_blockblog->translateItems();
        $page_translate = $_data_translate['page'];
        $paging = $obj_blog->PageNav($start,$_data['count_all'],$step,array('category_id'=>$category_id,'page'=>$page_translate,'category_id_page'=>$category_id_page));

        // strip tags for content
        foreach($_data['posts'] as $_k => $_item){
            $_data['posts'][$_k]['content'] = strip_tags($_item['content']);

        }





        $this->context->smarty->assign(array('posts' => $_data['posts'],
                'count_all' => $_data['count_all'],
                'paging' => $paging,
                'blog_category'=>$_info_cat['category'][0],
                $name_module.'category_id'=>$category_id_page
            )
        );


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:'.$name_module.'/views/templates/front/category17.tpl');
        }else {
            $this->setTemplate('category.tpl');
        }
    }


    private function _blogpost()
    {

       /* if(Configuration::get($this->_name_module.'sidebar_posblog_post_alias')==2)
            $this->display_column_right=true;
        if(Configuration::get($this->_name_module.'sidebar_posblog_post_alias')==1)
            $this->display_column_left =true;*/


        $name_module = $this->_name_module;

        $this->php_self = 'module-'.$name_module.'-post';

        include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();


        if($obj_blog->isURLRewriting()) {
            $post_id = Tools::getValue('p');
            $post_id = Tools::substr($post_id,2);
            //$post_id = str_replace("p-","",$post_id);
        } else {
            $post_id = Tools::getValue('post_id');
        }
        $post_id_page = $post_id;


        include_once(_PS_MODULE_DIR_.$this->_name_module.'/blockblog.php');
        $obj_blockblog = new blockblog();

        $obj_blockblog->setSEOUrls();


        if($obj_blog->isURLRewriting() && is_numeric($post_id)){

            // redirect to seo url
            $seo_url_post = $obj_blog->getSEOURLForPost(array('id'=>$post_id));

            $data_url = $obj_blog->getSEOURLs();
            $post_url = $data_url['post_url'];

            Tools::redirect($post_url.$seo_url_post);
        }


        $post_id = $obj_blog->getTransformSEOURLtoIDPost(array('id'=>$post_id));

        $_info_cat = $obj_blog->getPostItem(array('id' => $post_id,'site'=>1));



        // count views handler
        $obj_blog->statisticsHandler(array('post_id'=>$post_id));
        // count views handler



        if(empty($_info_cat['post'][0]['id']) || in_array($_info_cat['post'][0]['status'],array(0,2,3,4))){
            $data_url = $obj_blog->getSEOURLs();
            $posts_url = $data_url['posts_url'];


            Tools::redirect($posts_url);
        }

        $title = isset($_info_cat['post'][0]['title'])?$_info_cat['post'][0]['title']:'';
        $seo_description = isset($_info_cat['post'][0]['seo_description'])?$_info_cat['post'][0]['seo_description']:'';
        $seo_keywords = isset($_info_cat['post'][0]['seo_keywords'])?$_info_cat['post'][0]['seo_keywords']:'';

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title;
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $seo_description;
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $seo_keywords;
        }

        $this->context->smarty->assign('meta_title' , $title);
        $this->context->smarty->assign('meta_description' , $seo_description);
        $this->context->smarty->assign('meta_keywords' , $seo_keywords);



        /// set settings ///
        $obj_blockblog->setControllersSettings();
        /// set settings ///

        ########### category info ##################
        $is_active = 0;
        $ids_cat = $_info_cat['post'][0]['category_ids'];
        $category_data = array();
        foreach($ids_cat as $k => $cat_id){
            $_info_ids = $obj_blog->getCategoryItem(array('id' => $cat_id));


            $category0 = isset($_info_ids['category'][0])?$_info_ids['category'][0]:array();


            if(sizeof($category0)>0) {

                $is_active = 1;
                if(empty($_info_ids['category'][0]['title']))
                    $is_active = 0;


                $category_data[] = $_info_ids['category'][0];
            }
        }
        ########## end category info ###############


        ### related products ####
        $related_products = $_info_cat['post'][0]['related_products'];
        $data_related_products = $obj_blog->getRelatedProducts(array('related_data'=>$related_products));
        $this->context->smarty->assign($name_module.'blog_rp_tr', Configuration::get($name_module.'blog_rp_tr'));
        ### related products ####


        $this->context->smarty->assign($name_module.'postrel_views', Configuration::get($name_module.'postrel_views'));


        ### related posts ###
        $related_posts = $_info_cat['post'][0]['related_posts'];
        $data_related_posts = $obj_blog->getRelatedPostsForPost(array('related_data'=>$related_posts,'post_id'=>$post_id));
        ### related posts ###


        $p = (int)Tools::getValue('p');
        $step = (int) Configuration::get($name_module.'pperpage_com');

        $start = (int)(($p - 1)*$step);
        if($start<0)
            $start = 0;

        $_data = $obj_blog->getComments(array('start'=>$start,'step'=>$step,'id'=>$post_id));


        $_data_translate = $obj_blockblog->translateItems();
        $page_translate = $_data_translate['page'];

        $paging = $obj_blog->PageNav($start,$_data['count_all'],$step,array('post_id'=>$post_id_page,'page'=>$page_translate));




        $this->context->smarty->assign(array('comments' => $_data['comments'],
                'count_all' => $_data['count_all'],
                'paging' => $paging
            )
        );


        $id_lang = $obj_blockblog->getIdLang();

        $data_locale = $obj_blog->getfacebooklib($id_lang);
        $lng_iso =$data_locale['lng_iso'];


        $cookie = Context::getContext()->cookie;
        $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
        $name_customer = '';
        $email_customer = '';
        $avatar = '';
        if($id_customer) {
            include_once(_PS_MODULE_DIR_.$name_module.'/classes/userprofileblockblog.class.php');
            $obj_userprofileblockblog = new userprofileblockblog();


            $customer_data = $obj_userprofileblockblog->getInfoAboutCustomer(array('id_customer' => $id_customer, 'is_full' => 1));
            $name_customer = $customer_data['customer_name'];
            $email_customer = $customer_data['email'];

            $data_avatar = $obj_userprofileblockblog->getAvatarForCustomer(array('id_customer' => $id_customer));
            $avatar = $data_avatar['avatar'];
        }

        // gdpr
        $this->context->smarty->assign(array('id_module' => $obj_blockblog->getIdModule()));
        // gdpr

        $this->context->smarty->assign(array('posts' => $_info_cat['post'],
                'category_data' => $category_data,
                'is_active' => $is_active,
                'related_products'=>$data_related_products,
                'related_posts'=>$data_related_posts,

                $name_module.'_msg_rate'=>$_data_translate['msg_rate'],
                $name_module.'_msg_name'=>$_data_translate['msg_name'],
                $name_module.'_msg_em'=>$_data_translate['msg_em'],
                $name_module.'_msg_comm'=>$_data_translate['msg_comm'],
                $name_module.'_msg_cap'=>$_data_translate['msg_cap'],

                $name_module.'snip_publisher' => Configuration::get('PS_SHOP_NAME'),
                $name_module.'snip_width'=>Configuration::get($name_module.'post_img_width'),
                $name_module.'snip_height'=>Configuration::get($name_module.'post_img_width'),

                $name_module.'number_fc'=>Configuration::get($name_module.'number_fc'),
                $name_module.'lng_iso'=>$lng_iso,

                $name_module.'appid'=>Configuration::get($name_module.'appid'),


                $name_module.'name_c' => $name_customer,
                $name_module.'email_c' => $email_customer,
                $name_module.'c_avatar' => $avatar,
                $name_module.'id_customer'=>$id_customer,


            )
        );


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:'.$name_module.'/views/templates/front/post17.tpl');
        }else {
            $this->setTemplate('post.tpl');
        }




    }


    private function _bloglist(){

        if(Configuration::get($this->_name_module.'sidebar_posblog_alias')==2)
            $this->display_column_right=true;
        if(Configuration::get($this->_name_module.'sidebar_posblog_alias')==1)
            $this->display_column_left =true;


        $name_module = $this->_name_module;

        $this->php_self = 'module-'.$name_module.'-blog';


        include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();


        include_once(_PS_MODULE_DIR_.$this->_name_module.'/blockblog.php');
        $obj_blockblog = new blockblog();

        $obj_blockblog->setSEOUrls();

        $_data_translate = $obj_blockblog->translateItems();

        $cookie = Context::getContext()->cookie;
        $id_lang = (int)($cookie->id_lang);
        $title_allposts = Configuration::get($name_module . 'title_allposts_' . $id_lang);
        $desc_allposts = Configuration::get($name_module . 'desc_allposts_' . $id_lang);
        $key_allposts = Configuration::get($name_module . 'key_allposts_' . $id_lang);

        $title_allposts = Tools::strlen($title_allposts)>0?$title_allposts:$_data_translate['meta_title_all_posts'];
        $desc_allposts = Tools::strlen($desc_allposts)>0?$desc_allposts:$_data_translate['meta_description_all_posts'];
        $key_allposts = Tools::strlen($key_allposts)>0?$key_allposts:$_data_translate['meta_keywords_all_posts'];



        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $title_allposts;
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $desc_allposts;
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $key_allposts;
        }

        $this->context->smarty->assign('meta_title' , $title_allposts);
        $this->context->smarty->assign('meta_description' , $desc_allposts);
        $this->context->smarty->assign('meta_keywords' , $key_allposts);

        $_iso_lng = $obj_blog->getLangISO();
        $this->context->smarty->assign($name_module.'iso_lng', $_iso_lng);







            $obj_blockblog->setControllersSettings();

        $p = (int)Tools::getValue('p');
        $step =(int) Configuration::get($name_module.'perpage_posts');





        $search = Tools::getValue("search");


        $is_search = 0;

        ### search ###
        if(Tools::strlen($search)>0){
            $is_search = 1;
        }

        ### archives ####
        $year = (int)Tools::getValue("y");
        $month = (int)Tools::getValue("m");
        $is_arch = 0;
        if($year!=0 && $month!=0){
            $is_arch = 1;
        }

        $start = (int)(($p - 1)*$step);
        if($start<0)
            $start = 0;

        $_data = $obj_blog->getAllPosts(array('start'=>$start,'step'=>$step,
                'is_search'=>$is_search,'search'=>$search,
                'is_arch'=>$is_arch,'month'=>$month,'year'=>$year
            )
        );



        /*if(count($_data['posts'])==0) {
            $data_url = $obj_blog->getSEOURLs();
            $posts_url = $data_url['posts_url'];
            Tools::redirect($posts_url);
        }*/


        $page_translate = $_data_translate['page'];
        $paging = $obj_blog->PageNav($start,$_data['count_all'],$step,
            array('all_posts'=>1,'page'=>$page_translate,
                'is_search'=>$is_search,'search'=>$search,
                'is_arch'=>$is_arch,'month'=>$month,'year'=>$year
            )
        );

        // strip tags for content
        foreach($_data['posts'] as $_k => $_item){
            $_data['posts'][$_k]['content'] = strip_tags($_item['content']);

        }


        $this->context->smarty->assign(array('posts' => $_data['posts'],
                'count_all' => $_data['count_all'],
                'paging' => $paging,
                $name_module.'is_search' => $is_search,
                $name_module.'search' => $search,

                $name_module.'is_arch' => $is_arch,
                $name_module.'year' => $year,
                $name_module.'month' => $month,

                $name_module . 'slider_b_on' => Configuration::get($name_module . 'slider_b_on'),
            )
        );

        if(Configuration::get($name_module . 'slider_b_on')) {
            $obj_blockblog->setSliderSettings();
        }



        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:'.$name_module.'/views/templates/front/all-posts17.tpl');
        }else {
            $this->setTemplate('all-posts.tpl');
        }
    }
}