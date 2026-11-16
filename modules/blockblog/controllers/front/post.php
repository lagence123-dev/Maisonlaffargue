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

class BlockblogPostModuleFrontController extends ModuleFrontController
{
    public $php_self;
	public function init()
	{

		parent::init();
	}
	
	public function setMedia()
	{
		parent::setMedia();
    }

    public function initContent()
    {
        $name_module = "blockblog";

        $this->php_self = 'module-'.$name_module.'-post';

        parent::initContent();


        include_once(_PS_MODULE_DIR_.$name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();

        $post_id = Tools::getValue('post_id');

        if(is_numeric($post_id)) {

            $seo_url_post = $obj_blog->getSEOURLForPost(array('id' => $post_id));
        } else {
            $seo_url_post = $post_id;
        }

        $data_url = $obj_blog->getSEOURLs();
        $post_url = $data_url['post_url'];


        Tools::redirect($post_url.$seo_url_post);
    }
	
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent_old()
	{

        $name_module = "blockblog";

        $this->php_self = 'module-'.$name_module.'-post';

		parent::initContent();

        $post_id = Tools::getValue('post_id');
        $post_id_page = $post_id;




        include_once(_PS_MODULE_DIR_.$name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();


        include_once(_PS_MODULE_DIR_.$name_module.'/blockblog.php');
        $obj_blockblog = new blockblog();

        $obj_blockblog->setSEOUrls();




        if(Configuration::get($name_module.'urlrewrite_on') == 1 && is_numeric($post_id)){
            // redirect to seo url
            $seo_url_post = $obj_blog->getSEOURLForPost(array('id'=>$post_id));

            $data_url = $obj_blog->getSEOURLs();
            $post_url = $data_url['post_url'];

            Tools::redirect($post_url.$seo_url_post);

        }

        $post_id = $obj_blog->getTransformSEOURLtoIDPost(array('id'=>$post_id));

        $_info_cat = $obj_blog->getPostItem(array('id' => $post_id,'site'=>1));


        if(empty($_info_cat['post'][0]['id']) || $_info_cat['post'][0]['status'] == 0){
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



        $obj_blockblog->setControllersSettings();



        ########### category info ##################
        $is_active = 0;
        $ids_cat = $_info_cat['post'][0]['category_ids'];
        $category_data = array();
        foreach($ids_cat as $k => $cat_id){
            $_info_ids = $obj_blog->getCategoryItem(array('id' => $cat_id));


            $category0 = isset($_info_ids['category'][0])?$_info_ids['category'][0]:array();


            //echo "<pre>"; var_dump($ids_cat);

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


        $this->context->smarty->assign(array('posts' => $_info_cat['post'],
                'category_data' => $category_data,
                'is_active' => $is_active,
                'related_products'=>$data_related_products,
                'related_posts'=>$data_related_posts,

                $name_module.'_msg_name'=>$_data_translate['msg_name'],
                $name_module.'_msg_em'=>$_data_translate['msg_em'],
                $name_module.'_msg_comm'=>$_data_translate['msg_comm'],
                $name_module.'_msg_cap'=>$_data_translate['msg_cap'],

                $name_module.'snip_publisher' => Configuration::get('PS_SHOP_NAME'),
                $name_module.'snip_width'=>Configuration::get($name_module.'post_img_width'),
                $name_module.'snip_height'=>Configuration::get($name_module.'post_img_width')
            )
        );


        if(version_compare(_PS_VERSION_, '1.7', '>')) {


            $this->setTemplate('module:'.$name_module.'/views/templates/front/post17.tpl');
        }else {
            $this->setTemplate('post.tpl');
        }




    }
}