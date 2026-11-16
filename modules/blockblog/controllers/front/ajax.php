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

class BlockblogAjaxModuleFrontController extends ModuleFrontController
{

    public function postProcess()
    {

        $HTTP_X_REQUESTED_WITH = isset($_SERVER['HTTP_X_REQUESTED_WITH'])?$_SERVER['HTTP_X_REQUESTED_WITH']:'';
        if($HTTP_X_REQUESTED_WITH != 'XMLHttpRequest') {
            exit;
        }

        $name_module = 'blockblog';

        include_once(_PS_MODULE_DIR_ . $name_module.'/blockblog.php');
        $obj_blockblog = new blockblog();


        $token = Tools::getValue('token');
        $token_orig = $obj_blockblog->getoken();
        if($token_orig !=$token)
            die('Invalid token.');



        ob_start();
        $error_type = 0;
        $status = 'success';
        $message = '';

        $_html_page_nav = '';
        $_html = '';


        include_once(_PS_MODULE_DIR_ . $name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();





        $smarty = Context::getContext()->smarty;

        $action = Tools::getValue('action');

        $_is_friendly_url = $obj_blog->isURLRewriting();
        $_iso_lng = $obj_blog->getLangISO();

        $_is16 = 1;
        $smarty->assign($name_module.'is16', $_is16);



        switch ($action){

            case 'addcomment':
                $_html = '';
                $error_type = 0;

                $codeCaptcha = Tools::strlen(Tools::getValue('captcha'))>0?Tools::getValue('captcha'):'';

                $cookie = new Cookie($name_module);
                $code = $cookie->secure_code_blockblog;


                if(Configuration::get($name_module.'bcaptcha_type') != 1) {


                    $form_data = Tools::getValue('form_data');

                    parse_str($form_data,$array_form_data);

                    $g_captcha_response = $array_form_data['g-recaptcha-response'];
                    if (isset($g_captcha_response) && !empty($g_captcha_response) && Tools::strlen($g_captcha_response)>0) {


                        $bcaptcha_secret_key = Configuration::get($name_module . 'bcaptcha_secret_key');
                        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $bcaptcha_secret_key . '&response=' . $g_captcha_response);

                        $responseData = json_decode($verifyResponse);


                        if ($responseData->success) {
                            //ok
                        } else {
                            $error_type = 4;
                            $status = 'error';
                        }
                    } else {
                        $error_type = 4;
                        $status = 'error';
                    }

                }


                $id_post = (int) Tools::getValue('id_post');
                $rating = (int)Tools::getValue('rating');
                $name = strip_tags(trim(Tools::getValue('name')));
                $email = trim(Tools::getValue('email'));
                $text_review = strip_tags(trim(Tools::getValue('text_review')));

                if(!Validate::isEmail($email)) {
                    $error_type = 2;
                    $status = 'error';
                }

                if($error_type == 0 && Tools::strlen($name)==0){
                    $error_type = 1;
                    $status = 'error';
                }

                if($error_type == 0 && Tools::strlen($text_review)==0){
                    $error_type = 3;
                    $status = 'error';
                }

                if($error_type == 0 && !$rating){
                    $error_type = 5;
                    $status = 'error';
                }

                if($code != $codeCaptcha && Configuration::get($name_module.'is_captcha_com') && Configuration::get($name_module.'bcaptcha_type') == 1){
                    $error_type = 4;
                    $status = 'error';
                }


                if($error_type == 0){
                    //insert review
                    $_data = array('name' => $name,
                        'email' => $email,
                        'text_review' => $text_review,
                        'rating'=>$rating,
                        'id_post' => $id_post
                    );
                    $obj_blog->saveComment($_data);

                }


                break;
            case 'deleteimg':
                if($obj_blockblog->is_demo){
                    $status = 'error';
                    $message = 'Feature disabled on the demo mode!';
                } else {
                    $item_id = Tools::getValue('item_id');
                    $obj_blog->deleteImg(array('id' => $item_id));
                }
                break;
            case 'deleteimgcat':
                if($obj_blockblog->is_demo){
                    $status = 'error';
                    $message = 'Feature disabled on the demo mode!';
                } else {
                    $item_id = Tools::getValue('item_id');
                    $obj_blog->deleteImg(array('id' => $item_id,'is_category'=>1));
                }
                break;
            case 'deleteimgava':

                if($obj_blockblog->is_demo){
                    $status = 'error';
                    $message = 'Feature disabled on the demo mode!';
                } else {
                    $item_id = Tools::getValue('item_id');
                    $id_customer = Tools::getValue('id_customer');
                    $is_admin = Tools::getValue('is_admin');
                    $obj_blog->deleteAvatar(array('id' => $item_id,'id_customer'=>$id_customer,'is_admin'=>$is_admin));
                }
                break;
            case 'like':
                $ip = $_SERVER['REMOTE_ADDR'];
                $like = (int)Tools::getValue('like');
                $id = (int)Tools::getValue('id');

                $data = $obj_blog->like(array('id'=>$id,'like'=>$like,'ip'=>$ip));
                $status = $data['error'];
                $message = $data['message'];
                $count = $data['count'];

                break;
            case 'category':

                if($action == 'category'){
                    $smarty->assign('is_category_page', 1);
                }


                $page = (int) Tools::getValue('page');



                $obj_blockblog->setSEOUrls();


                if(version_compare(_PS_VERSION_, '1.6', '>')){
                    $smarty->assign($name_module.'is16' , 1);
                } else {
                    $smarty->assign($name_module.'is16' , 0);
                }

                $smarty->assign($name_module.'urlrewrite_on', Configuration::get($name_module.'urlrewrite_on'));
                $smarty->assign($name_module.'p_list_displ_date', Configuration::get($name_module.'p_list_displ_date'));
                $smarty->assign($name_module.'rsson', Configuration::get($name_module.'rsson'));
                $smarty->assign($name_module.'blog_pl_tr', Configuration::get($name_module.'blog_pl_tr'));
                $smarty->assign($name_module.'postl_views', Configuration::get($name_module.'postl_views'));
                $smarty->assign($name_module.'rating_postl', Configuration::get($name_module.'rating_postl'));

                $smarty->assign($name_module.'blog_post_effect', Configuration::get($name_module.'blog_post_effect'));


                $category_id = (int)Tools::getValue('id_item');
                $step = (int) Configuration::get($name_module.'perpage_posts');



                $start = $page;

                $_data = $obj_blog->getPosts(array('start'=>$start,'step'=>$step,'id'=>$category_id));
                $obj_blockblog->setControllersSettings();



                $_data_translate = $obj_blockblog->translateItems();
                $page_translate = $_data_translate['page'];
                $paging = $obj_blog->PageNav($start,$_data['count_all'],
                    $step,
                    array('category_id'=>$category_id,
                        'page'=>$page_translate,
                        //'category_id_page'=>$category_id_page
                    )
                );

                $_html_page_nav = $paging;

                // strip tags for content
                foreach($_data['posts'] as $_k => $_item){
                    $_data['posts'][$_k]['content'] = strip_tags($_item['content']);

                }


                $smarty->assign($name_module.'pic', $obj_blockblog->getCloudImgPath());


                $smarty->assign(array('posts' => $_data['posts'],
                        'count_all' => $_data['count_all'],
                        'paging' => $paging
                    )
                );



                ob_start();

                    echo $obj_blockblog->renderTplListCategory();
                $_html = ob_get_clean();

                break;
            case 'categories':


                $page = (int) Tools::getValue('page');



                $_data_translate = $obj_blockblog->translateItems();

                $obj_blockblog->setSEOUrls();

                $obj_blockblog->setControllersSettings();


                if(version_compare(_PS_VERSION_, '1.6', '>')){
                    $smarty->assign($name_module.'is16' , 1);
                } else {
                    $smarty->assign($name_module.'is16' , 0);
                }

                $smarty->assign($name_module.'urlrewrite_on', Configuration::get($name_module.'urlrewrite_on'));
                $smarty->assign($name_module.'cat_list_display_date', Configuration::get($name_module.'cat_list_display_date'));
                $smarty->assign($name_module.'blog_cat_effect', Configuration::get($name_module.'blog_cat_effect'));

                $smarty->assign($name_module.'blog_catl_tr', Configuration::get($name_module.'blog_catl_tr'));

                $smarty->assign($name_module.'pic', $obj_blockblog->getCloudImgPath());


                $step = (int) Configuration::get($name_module.'perpage_catblog');

                $start = $page;

                $_data = $obj_blog->getCategories(array('start'=>$start,'step'=>$step));


                $page_translate = $_data_translate['page'];

                $paging = $obj_blog->PageNav($start,$_data['count_all'],$step,array('category'=>1,'page'=>$page_translate));

                $_html_page_nav = $paging;

                $smarty->assign(array('categories' => $_data['categories'],
                        'count_all' => $_data['count_all'],
                        'paging' => $paging
                    )
                );


                ob_start();


                    echo $obj_blockblog->renderTplListCategories();
                $_html = ob_get_clean();



                break;
            case 'allposts':
            case 'archive':
            case 'search':

                if($action == 'allposts'){
                    $smarty->assign('is_all_posts_page', 1);
                }

                $id_item = Tools::getValue('id_item');
                $year = null;
                $month = null;
                $search = null;

                if($id_item == "archive"){

                    $year = Tools::getValue('y');
                    $month = Tools::getValue('m');

                }

                if($id_item == "search"){
                    $search = Tools::getValue("search");
                }



                $page = (int) Tools::getValue('page');



                $obj_blockblog->setSEOUrls();

                $_data_translate = $obj_blockblog->translateItems();

                $obj_blockblog->setControllersSettings();


                $_iso_lng = $obj_blog->getLangISO();
                $smarty->assign($name_module.'iso_lng', $_iso_lng);


                $smarty->assign($name_module.'urlrewrite_on', Configuration::get($name_module.'urlrewrite_on'));
                $smarty->assign($name_module.'p_list_displ_date', Configuration::get($name_module.'p_list_displ_date'));
                $smarty->assign($name_module.'rsson', Configuration::get($name_module.'rsson'));
                $smarty->assign($name_module.'blog_pl_tr', Configuration::get($name_module.'blog_pl_tr'));
                $smarty->assign($name_module.'postl_views', Configuration::get($name_module.'postl_views'));
                $smarty->assign($name_module.'rating_postl', Configuration::get($name_module.'rating_postl'));

                $smarty->assign($name_module.'blog_post_effect', Configuration::get($name_module.'blog_post_effect'));

                if(version_compare(_PS_VERSION_, '1.6', '>')){
                    $smarty->assign($name_module.'is16' , 1);
                } else {
                    $smarty->assign($name_module.'is16' , 0);
                }



                $step =(int) Configuration::get($name_module.'perpage_posts');




                $is_search = 0;

                ### search ###
                if(Tools::strlen($search)>0){
                    $is_search = 1;
                }

                ### archives ####
                $is_arch = 0;
                if($year!=0 && $month!=0){
                    $is_arch = 1;
                }



                $start = $page;

                $_data = $obj_blog->getAllPosts(array('start'=>$start,'step'=>$step,
                        'is_search'=>$is_search,'search'=>$search,
                        'is_arch'=>$is_arch,'month'=>$month,'year'=>$year
                    )
                );





                $page_translate = $_data_translate['page'];
                $paging = $obj_blog->PageNav($start,$_data['count_all'],$step,
                    array('all_posts'=>1,'page'=>$page_translate,
                        'is_search'=>$is_search,'search'=>$search,
                        'is_arch'=>$is_arch,'month'=>$month,'year'=>$year
                    )
                );

                $_html_page_nav = $paging;

                // strip tags for content
                foreach($_data['posts'] as $_k => $_item){
                    $_data['posts'][$_k]['content'] = strip_tags($_item['content']);

                }

                $smarty->assign($name_module.'pic', $obj_blockblog->getCloudImgPath());

                $smarty->assign(array('posts' => $_data['posts'],
                        'count_all' => $_data['count_all'],
                        'paging' => $paging,
                        $name_module.'is_search' => $is_search,
                        $name_module.'search' => $search
                    )
                );


                ob_start();


                    echo $obj_blockblog->renderTplListAllPosts();
                $_html = ob_get_clean();


                break;
            case 'allcomments':

                $page = (int) Tools::getValue('page');


                $obj_blockblog->setControllersSettings();

                $obj_blockblog->setSEOUrls();

                $_data_translate = $obj_blockblog->translateItems();

                if(version_compare(_PS_VERSION_, '1.6', '>')){
                    $smarty->assign($name_module.'is16' , 1);
                } else {
                    $smarty->assign($name_module.'is16' , 0);
                }


                $smarty->assign($name_module.'urlrewrite_on', Configuration::get($name_module.'urlrewrite_on'));
                $smarty->assign($name_module.'rsson', Configuration::get($name_module.'rsson'));

                $smarty->assign($name_module.'blog_com_effect', Configuration::get($name_module.'blog_com_effect'));

                $smarty->assign($name_module.'rating_acom', Configuration::get($name_module.'rating_acom'));


                $step = (int) Configuration::get($name_module.'perpage_com');

                $start = $page;

                $_data_com = $obj_blog->getLastComments(array('is_page'=>1,'step'=>$step,'start'=>$start));



                $page_translate = $_data_translate['page'];
                $paging = $obj_blog->PageNav($start,$_data_com['count_all'],$step,
                    array('all_comments'=>1,'page'=>$page_translate,
                    )
                );

                $_html_page_nav = $paging;


                $smarty->assign(array('comments' => $_data_com['comments'],
                        'count_all' => $_data_com['count_all'],
                        'paging' => $paging,
                    )
                );


                ob_start();


                    echo $obj_blockblog->renderTplListAllComments();
                $_html = ob_get_clean();

                break;
            case 'comments':

                $page = (int) Tools::getValue('page');


                $obj_blockblog->setControllersSettings();

                $obj_blockblog->setSEOUrls();

                $start = $page;

                $post_id = Tools::getValue('post_id');
                $post_id = $obj_blog->getTransformSEOURLtoIDPost(array('id'=>$post_id));

                $step = (int) Configuration::get($name_module.'pperpage_com');
                $_data = $obj_blog->getComments(array('start'=>$start,'step'=>$step,'id'=>$post_id));


                $smarty->assign($name_module.'blog_comp_effect', Configuration::get($name_module.'blog_comp_effect'));
                $smarty->assign($name_module.'rating_post', Configuration::get($name_module.'rating_post'));

                $_data_translate = $obj_blockblog->translateItems();
                $page_translate = $_data_translate['page'];

                $paging = $obj_blog->PageNav($start,$_data['count_all'],$step,array('post_id'=>$post_id,'page'=>$page_translate));

                $_html_page_nav = $paging;

                $smarty->assign(array('comments' => $_data['comments'],
                        'count_all' => $_data['count_all'],
                        'paging' => $paging
                    )
                );

                ob_start();


                    echo $obj_blockblog->renderTplListPostComments();
                $_html = ob_get_clean();


                break;
            case 'tag':

                if($action == 'tag'){
                    $smarty->assign('is_tag_page', 1);
                }


                $page = (int) Tools::getValue('page');



                $obj_blockblog->setSEOUrls();


                $tag_id = Tools::getValue('id_item');
                $tag_id = urldecode($tag_id);
                $step = (int) Configuration::get($name_module.'perpage_posts');



                $start = $page;

                $_data = $obj_blog->getPostsForTag(array('start'=>$start,'step'=>$step,'tag_id'=>$tag_id));


                $_data_translate = $obj_blockblog->translateItems();
                $page_translate = $_data_translate['page'];

                $paging = $obj_blog->PageNav($start,$_data['count_all'],$step,array('tag_id'=>$tag_id,'page'=>$page_translate));


                $_html_page_nav = $paging;

                // strip tags for content
                foreach($_data['posts'] as $_k => $_item){
                    $_data['posts'][$_k]['content'] = strip_tags($_item['content']);

                }

                $obj_blockblog->setControllersSettings();



                $smarty->assign(array('posts' => $_data['posts'],
                        'count_all' => $_data['count_all'],
                        'paging' => $paging
                    )
                );



                ob_start();

                echo $obj_blockblog->renderTplListAllPosts();

                $_html = ob_get_clean();

                break;

            case 'author':

                if($action == 'author'){
                    $smarty->assign('is_author_page', 1);
                }


                $page = (int) Tools::getValue('page');


                $author_id = (int)Tools::getValue($name_module.'author_id');


                $original_author_id = $author_id;


                $obj_blockblog->setSEOUrls();

                $info_path = $obj_blog->getAvatarPath(array('id_customer'=>$author_id));


                $avatar = $info_path['avatar'];
                $is_show_ava = $info_path['is_show'];

                $_info_author = $obj_blog->isExistsAuthor(array('author_id' => $author_id));

                $ava_on = (int)Configuration::get($name_module.'ava_on');

                if($_info_author !== NULL || $is_show_ava != 0 || !$ava_on) {

                    $data_customer_info = $obj_blog->getCustomerInfo(array('id_customer' => $author_id));
                    $author_name = $data_customer_info['customer_name'];


                    $obj_blockblog->setControllersSettings();


                    $step = (int)Configuration::get($name_module . 'perpage_posts');
                    $start = $page;

                    $_data = $obj_blog->getPostsForAuthor(array('start' => $start, 'step' => $step, 'author_id' => $author_id));


                    $paging = $obj_blog->PageNav($start, $_data['count_all'], $step, array('is_author' => 1, 'author_id' => $author_id));

                    $_html_page_nav = $paging;

                    // strip authors for content
                    foreach ($_data['posts'] as $_k => $_item) {
                        $_data['posts'][$_k]['content'] = strip_tags($_item['content']);

                    }


                    $smarty->assign(array('posts' => $_data['posts'],
                            'count_all' => $_data['count_all'],
                            'paging' => $paging,
                            $name_module . 'author_id' => $original_author_id,
                            $name_module . 'avatar' => $avatar,
                            $name_module . 'author_name' => $author_name,
                        )
                    );

                    ob_start();

                    echo $obj_blockblog->renderTplListAllPosts();

                    $_html = ob_get_clean();
                } else {
                    $status = 'error';
                    $message = 'Unknown parameters!';
                }

            break;
            case 'authors':
                $ava_on = (int)Configuration::get($name_module.'ava_on');


                if($ava_on) {


                    $obj_blockblog->setSEOUrls();

                    $obj_blockblog->setControllersSettings();

                    $step = (int)Configuration::get($name_module.'perpage_authors');

                    $start = (int) Tools::getValue('page');


                    $search = Tools::getValue("search");
                    $is_search = 0;

                    ### search ###
                    if(Tools::strlen($search)>0){
                        $is_search = 1;

                    }
                    $smarty->assign($name_module.'is_search', $is_search);
                    $smarty->assign($name_module.'search', $search);

                    if(version_compare(_PS_VERSION_, '1.6', '>')){
                        $smarty->assign($name_module.'is16' , 1);
                    } else {
                        $smarty->assign($name_module.'is16' , 0);
                    }


                    include_once(_PS_MODULE_DIR_.$name_module.'/classes/userprofileblockblog.class.php');
                    $obj_userprofileblockblog = new userprofileblockblog();


                    $info_customers = $obj_userprofileblockblog->getShoppersList(array('start' => $start,'step'=>$step,'is_search'=>$is_search,'search'=>$search));



                    $data_urls = $obj_blog->getSEOURLs();
                    $users_url = $data_urls['authors_url'];

                    $paging = $obj_blog->PageNav($start,$info_customers['data_count_customers'],$step,
                                                    array('is_authors'=>1, 'is_search_authors'=>$is_search,'search_authors'=>$search,));

                    $_html_page_nav = $paging;

                    $smarty->assign($name_module.'d_eff_shopu', Configuration::get($name_module.'d_eff_shopu'));

                    $smarty->assign(array(
                        $name_module.'customers' => $info_customers['customers'],
                        $name_module.'data_count_customers' => $info_customers['data_count_customers'],
                        $name_module.'paging' => $paging,
                        $name_module.'users_url'=>$users_url,

                    ));


                    ob_start();

                    echo $obj_blockblog->renderTplListAllAuthors();

                    $_html = ob_get_clean();


                } else {
                    $status = 'error';
                    $message = 'Unknown parameters!';
                }

            break;
            case 'addavatar':

                $files = $_FILES['avatar-review'];
                if(!empty($files['name']))
                {
                    if(!$files['error'])
                    {
                        $type_one = $files['type'];
                        $ext = explode("/",$type_one);

                        if(strpos('_'.$type_one,'image')<1)
                        {
                            $error_type = 8;
                            $status = 'error';

                        }elseif(!in_array($ext[1],array('png','x-png','gif','jpg','jpeg','pjpeg'))) {
                            $error_type = 9;
                            $status = 'error';
                        }
                    }
                }

                $cookie = Context::getContext()->cookie;

                $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
                if (!$id_customer){
                    $status = 'error';
                    $message = 'You must be logged in!';
                }


                    if($status != 'error') {
                    include_once(_PS_MODULE_DIR_.$name_module . '/classes/userprofileblockblog.class.php');
                    $obj = new userprofileblockblog();

                    $show_my_profile = Tools::getValue('show_my_profile');

                    $cookie = Context::getContext()->cookie;
                    $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;


                    $obj->saveImageAvatar(array('show_my_profile' => $show_my_profile,'id_customer'=>$id_customer,));

                    $info = Tools::getValue('info');
                    $obj->updateDataAuthor(array('id_customer'=>$id_customer,'info'=>$info));

                }
            break;
            case 'myblogposts':


                $cookie = Context::getContext()->cookie;

                $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
                if (!$id_customer){
                    $status = 'error';
                    $message = 'You must be logged in!';
                }


                if($status != 'error') {

                    $page = (int) Tools::getValue('page');

                    $obj_blockblog->setSEOUrls();

                    $obj_blockblog->setControllersSettings();


                    $step = (int) Configuration::get($name_module.'perpage_myposts');
                    $start= $page;
                    $_data = $obj_blog->getPostsForAuthor(array('start'=>$start,'step'=>$step,'author_id'=>$id_customer,'is_my'=>1));

                    $paging = $obj_blog->PageNav($start,$_data['count_all'],$step,array('is_myblogposts'=>1,'author_id'=>$id_customer));

                    $_html_page_nav = $paging;

                    // strip authors for content
                    foreach($_data['posts'] as $_k => $_item){
                        $_data['posts'][$_k]['content'] = strip_tags($_item['content']);

                    }


                    $smarty->assign(
                        array(
                            $name_module.'myposts' => $_data['posts'],
                            $name_module.'mycount_all' => $_data['count_all'],
                            $name_module.'paging' => $paging,

                            $name_module.'is_addmyblogposts' => Configuration::get($name_module . 'is_addmyblogposts'),
                            $name_module.'is_editmyblogposts' => Configuration::get($name_module . 'is_editmyblogposts'),
                            $name_module.'is_delmyblogposts' => Configuration::get($name_module . 'is_delmyblogposts'),
                            $name_module.'d_eff_shopmyposts' => Configuration::get($name_module.'d_eff_shopmyposts'),

                            $name_module.'rand'=>rand(1,100),
                        ));

                    ob_start();

                    echo $obj_blockblog->renderTplListMyBlogPosts();

                    $_html = ob_get_clean();

                }
                break;
                case 'myblogcom':


                $cookie = Context::getContext()->cookie;

                $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
                if (!$id_customer){
                    $status = 'error';
                    $message = 'You must be logged in!';
                }


                if($status != 'error') {

                    $page = (int) Tools::getValue('page');

                    $obj_blockblog->setSEOUrls();

                    $obj_blockblog->setControllersSettings();


                    $step = (int) Configuration::get($name_module.'perpage_mycom');
                    $start= $page;


                    $_data = $obj_blog->getComments(array('start'=>$start,'step'=>$step,'id_customer'=>$id_customer,'admin'=>3));


                    $paging = $obj_blog->PageNav($start,$_data['count_all'],$step,array('is_myblogcom'=>1,'author_id'=>$id_customer));


                    $_html_page_nav = $paging;


                    $smarty->assign(
                        array(
                            $name_module.'mycomments' => $_data['comments'],
                            $name_module.'mycount_all' => $_data['count_all'],
                            $name_module.'paging' => $paging,


                            $name_module.'is_editmyblogcom' => Configuration::get($name_module . 'is_editmyblogcom'),
                            $name_module.'is_delmyblogcom' => Configuration::get($name_module . 'is_delmyblogcom'),
                            $name_module.'d_eff_shopmycom' => Configuration::get($name_module.'d_eff_shopmycom'),

                        ));



                    ob_start();

                    echo $obj_blockblog->renderTplListMyBlogComments();

                    $_html = ob_get_clean();

                }
                break;


            case 'gallery':
                $gallery_on = (int)Configuration::get($name_module.'gallery_on');


                if($gallery_on) {


                    $obj_blockblog->setSEOUrls();
                    $obj_blockblog->setControllersSettings();
                    $obj_blockblog->setGallerySettings();

                    $p = (int)Tools::getValue('page');
                    $step = (int) Configuration::get($name_module.'gallery_per_page');

                    $start = $p;


                    $_data = $obj_blog->getGallery(array('start'=>$start,'step'=>$step));


                    $paging = $obj_blog->PageNav($start,$_data['count_all'],$step,array('gallery'=>1));

                    $_html_page_nav = $paging;

                    $smarty->assign(
                        array(
                            $name_module.'gallerypage' => $_data['gallery'],
                            'count_all' => $_data['count_all'],
                            'paging' => $paging,
                            $name_module.'is_ajax_gallery'=>1,
                        )
                    );


                    ob_start();

                    echo $obj_blockblog->renderTplListGallery();

                    $_html = ob_get_clean();


                } else {
                    $status = 'error';
                    $message = 'Unknown parameters!';
                }

                break;
            case 'myloyalty':

                $cookie = Context::getContext()->cookie;
                $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
                if (!$id_customer){
                    $status = 'error';
                    $message = 'You must be as registered customer!';
                } else {



                    include_once(_PS_MODULE_DIR_.$name_module.'/classes/loyalityblog.class.php');
                    $obj_loyalty = new loyalityblog();


                    $prefix_loyalty = $obj_blockblog->getLoyalityPrefix();

                    $obj_blockblog->setSEOUrls();


                    $start = (int) Tools::getValue('page');
                    if($start<0)
                        $start = 0;

                    $step = $obj_loyalty->getStepMy();


                    $data_my = $obj_loyalty->getMyLoyaltyPoints(array('id_customer'=>$id_customer,'start'=>$start));


                    $count_my = $data_my['count_all'];
                    $used_points = $data_my['used_points'];
                    $not_used_points = $data_my['not_used_points'];

                    $paging = $obj_loyalty->paging17(
                        array('start'=>$start,
                            'step'=> (int)$step,
                            'count' => $count_my,
                            'action'=>'myloyalty',

                        )
                    );
                    $_html_page_nav = $paging;

                    $this->context->smarty->assign($name_module.'d_eff_loyalty_my', Configuration::get($name_module.'d_eff_loyalty_my'.$prefix_loyalty));

                    $this->context->smarty->assign(array(
                        $name_module.'my_data' => $data_my['data'],
                        $name_module.'paging' => $paging,


                        'used_points'=>$used_points,
                        'not_used_points'=>$not_used_points,

                    ));


                    ob_start();

                    echo $obj_blockblog->renderListMyLoyalty();

                    $_html = ob_get_clean();

                }

                break;

            default:
                $status = 'error';
                $message = 'Unknown parameters!';
            break;
        }


        $response = new stdClass();
        $content = ob_get_clean();
        $response->status = $status;
        $response->message = $message;
        if($action == "addcomment"){
            $response->params = array('content' => $_html,
                'error_type' => $error_type
            );
        } elseif($action == 'addavatar') {
            $response->params = array('content' => $content,'error_type' => $error_type);
        } elseif($action == 'like') {
            $response->params = array('content' => $content, 'count' => $count);
        }elseif($action == "category" || $action == "categories" || $action == "allposts" || $action == "archive" || $action == "search"
            || $action == "allcomments" || $action == "comments" || $action == "tag" || $action == "author" || $action == "authors"
            || $action == "myblogposts" || $action == "myblogcom" || $action == "gallery" || $action == 'myloyalty') {
            $response->params = array('content' => $_html, 'page_nav' => $_html_page_nav);
        }else {
            $response->params = array('content' => $content);
        }
        echo json_encode($response);
        exit;

    }



}
?>