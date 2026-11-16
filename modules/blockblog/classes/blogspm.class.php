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
#[\AllowDynamicProperties]
class blogspm extends Module{
	
	private $_width = 1000;
	private $_height = 1000;
	private $_name = 'blockblog';
	private $_id_shop;
	private $_is15;
	private $_is_rewriting_settings;
	private $_lang_iso;
	private $_http_host;
	
	private $_is_cloud;
	private $path_img_cloud;

    private $_is_show_now = 1; // If you created a new blog post or category and set a date in the future (2020 year - for example) and status to "yes". The blog post or category isn't displayed on the frontend.

    private $_id_group;

    private $_img_category_list;
    private $_img_category_page;

    private $_img_gallery_width;
    private $_img_gallery_height;
    private $_img_gallery_folder;

    private $_img_slider_width;
    private $_img_slider_height;
    private $_img_slider_width_mobile;
    private $_img_slider_height_mobile;
    private $_img_slider_folder;

    private $_img_posts_width;
    private $_img_posts_height;

    public function __construct(){

		$this->_id_shop = Context::getContext()->shop->id;
		$this->_is15 = 1;

		if (defined('_PS_HOST_MODE_'))
			$this->_is_cloud = 1;
		else
			$this->_is_cloud = 0;
		
		
		$this->_id_group = (int) Group::getCurrent()->id;


        // gallery
        $this->_img_category_list = Configuration::get($this->_name . 'clists_img_width');
        $this->_img_category_page = Configuration::get($this->_name . 'cat_img_width');


        $this->_img_gallery_width = Configuration::get($this->_name . 'gallery_width');
        $this->_img_gallery_height = Configuration::get($this->_name . 'gallery_height');

        $this->_img_gallery_width_block = Configuration::get($this->_name . 'gallery_w');

        $this->_img_gallery_width_block_home = Configuration::get($this->_name . 'gallery_w_h');

        $this->_img_posts_width = Configuration::get($this->_name.'lists_img_width');
        //$this->_img_posts_height = Configuration::get($this->_name.'lists_img_height');
        $this->_img_posts_height = Configuration::get($this->_name.'lists_img_width');



        $this->_img_gallery_folder = "gallery";

        // gallery


        // slider
        $this->_img_slider_folder = 'slider';
        $this->_img_slider_width = Configuration::get($this->_name . 'slider_widthfancytr');
        $this->_img_slider_height = Configuration::get($this->_name . 'slider_heightfancytr');


        // slider for mobile devices
        $this->_img_slider_height_mobile = Configuration::get($this->_name.'slider_heightfancytr_mobile');
        $this->_img_slider_width_mobile = Configuration::get($this->_name.'slider_widthfancytr_mobile');
        // slider for mobile devices

        // for test
		//$this->_is_cloud = 1;
		// for test
		
		
//		if($this->_is_cloud){
//			$this->path_img_cloud = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;
//		} else {
//			$this->path_img_cloud = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR;
//		}
        $this->path_img_cloud = _PS_MODULE_DIR_.$this->_name.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;

		$this->_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;


		
		$this->initContext();
	}
	
	private function initContext()
	{
		$this->context = Context::getContext();
	}
	
	public function saveCategory($data){

        $status = (int)$data['status'];
		
		$ids_shops = implode(",",$data['cat_shop_association']);

        $group_association = implode(",",$data['group_association']);

        $related_posts = $data['ids_related_posts']?$data['ids_related_posts']:array();


        $time_add = $data['time_add'];
		
		$sql = 'INSERT into `'._DB_PREFIX_.'blog_category` SET
							   `ids_shops` = "'.pSQL($ids_shops).'",
							   `ids_groups` = "'.pSQL($group_association).'",
							    time_add = "'.pSQL($time_add).'",
							    status = '.(int)$status.'
							   ';
		
		Db::getInstance()->Execute($sql);
		
		$post_id = Db::getInstance()->Insert_ID();


        ## set  position ##
        /*$sql_count = 'select count(*) as count from `'._DB_PREFIX_.'blog_category`';
        $result_count = Db::getInstance()->executeS($sql_count);
        $position_id = (int)$result_count[0]['count'] - 1;*/

        $position_id = $post_id-1;
        Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'blog_category
							SET position = '. (int)($position_id) .' WHERE id = ' . (int)($post_id));


        ## set  position ##

		
		foreach($data['data_title_content_lang'] as $language => $item){
		
		$category_title = $item['category_title'];
		$category_seokeywords = $item['category_seokeywords'];
		$category_seodescription = $item['category_seodescription'];
        $category_content = $item['category_content'];
		
		$seo_url_pre = Tools::strlen($item['seo_url'])>0?$item['seo_url']:$category_title;
	    $seo_url = $this->_translit($seo_url_pre);
		
	   
	    $sql = 'SELECT count(*) as count
				FROM `'._DB_PREFIX_.'blog_category_data` pc
				WHERE seo_url = "'.pSQL($seo_url,true).'" AND id_lang = '.(int)$language;
		$data_seo_url = Db::getInstance()->GetRow($sql);
		
		if($data_seo_url['count'] != 0)
			$seo_url = $seo_url."-".Tools::strtolower(Tools::passwdGen(6));
		
		$sql = 'INSERT into `'._DB_PREFIX_.'blog_category_data` SET
							   `id_item` = \''.(int)($post_id).'\',
							   `id_lang` = \''.(int)($language).'\',
							   `title` = \''.pSQL($category_title).'\',
							   `seo_keywords` = \''.pSQL($category_seokeywords).'\',
							   `seo_description` = \''.pSQL($category_seodescription).'\',
							   `content` = \''.pSQL($category_content,true).'\',
							   `seo_url` = \''.pSQL($seo_url).'\'
							   ';
		
		Db::getInstance()->Execute($sql);
		
		}


        include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
        $obj = new blockblog();

        if($obj->is_demo == 0) {
            $error_data = $this->saveImage(array('post_id' => $post_id, 'is_category'=>1));
            $error = $error_data['error'];
            $error_text = $error_data['error_text'];
        } else {
            $error = 0;
            $error_text = '';
        }



        $sql = 'DELETE FROM `'._DB_PREFIX_.'blog_category2post`
					   WHERE `category_id` = \''.(int)($post_id).'\'';
        Db::getInstance()->Execute($sql);

        if($related_posts) {
            foreach ($related_posts as $id_post_related) {
                $sql = 'INSERT into `' . _DB_PREFIX_ . 'blog_category2post` SET
							   `category_id` = \'' . (int)($post_id) . '\',
							   `post_id` = \'' . (int)($id_post_related) . '\'
							   ';
                Db::getInstance()->Execute($sql);

            }
        }


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //

        return array('error'=>$error,'error_text'=>$error_text,'post_id'=>$post_id);

		
	}
	
	public function updateCategory($data){
		
		$id = (int)$data['id_editcategory'];
        $status = (int)$data['status'];
		
		$ids_shops = implode(",",$data['cat_shop_association']);
        $group_association = implode(",",$data['group_association']);
        $time_add = $data['time_add'];


        $related_posts = $data['ids_related_posts']?$data['ids_related_posts']:array();




        $post_images = $data['post_images'];
		
		$sql_update = 'UPDATE `'._DB_PREFIX_.'blog_category` SET 
						`ids_shops` = "'.pSQL($ids_shops).'", `ids_groups` = "'.pSQL($group_association).'", `time_add` = "'.pSQL($time_add).'", status = '.(int)$status.'
						WHERE id ='.(int)$id;
		Db::getInstance()->Execute($sql_update);
		
		/// delete tabs data
		$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_category_data` WHERE id_item = '.(int)$id.'';
		Db::getInstance()->Execute($sql);
		
		foreach($data['data_title_content_lang'] as $language => $item){
		
		$category_title = $item['category_title'];
		$category_seokeywords = $item['category_seokeywords'];
		$category_seodescription = $item['category_seodescription'];
            $category_content = $item['category_content'];
		
		$seo_url_pre = Tools::strlen($item['seo_url'])>0?$item['seo_url']:$category_title;
	    $seo_url = $this->_translit($seo_url_pre);
		
		$sql = 'SELECT count(*) as count
				FROM `'._DB_PREFIX_.'blog_category_data` pc
				WHERE seo_url = "'.pSQL($seo_url,true).'" AND id_lang = '.(int)$language;
		$data_seo_url = Db::getInstance()->GetRow($sql);
		
		if($data_seo_url['count'] != 0)
			$seo_url = $seo_url."-".Tools::strtolower(Tools::passwdGen(6));
			
			
		$sql = 'INSERT into `'._DB_PREFIX_.'blog_category_data` SET
							   `id_item` = \''.(int)($id).'\',
							   `id_lang` = \''.(int)($language).'\',
							   `title` = \''.pSQL($category_title).'\',
							   `seo_keywords` = \''.pSQL($category_seokeywords).'\',
							   `seo_description` = \''.pSQL($category_seodescription).'\',
							   `content` = \''.pSQL($category_content,true).'\',
							   `seo_url` = \''.pSQL($seo_url).'\'
							   
							   ';
		Db::getInstance()->Execute($sql);
		
		}


        include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
        $obj = new blockblog();

        if($obj->is_demo == 0) {
            $error_data = $this->saveImage(array('post_id' => $id, 'is_category'=>1,'post_images'=>$post_images));
            $error = $error_data['error'];
            $error_text = $error_data['error_text'];
        } else {
            $error = 0;
            $error_text = '';
        }




        $sql = 'DELETE FROM `'._DB_PREFIX_.'blog_category2post`
					   WHERE `category_id` = \''.(int)($id).'\'';
        Db::getInstance()->Execute($sql);

        if($related_posts) {
            foreach ($related_posts as $id_post_related) {
                $sql = 'INSERT into `' . _DB_PREFIX_ . 'blog_category2post` SET
							   `category_id` = \'' . (int)($id) . '\',
							   `post_id` = \'' . (int)($id_post_related) . '\'
							   ';
                Db::getInstance()->Execute($sql);

            }
        }


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //


        return array('error'=>$error,'error_text'=>$error_text);
		
	}



    public function updateCategoryStatus($data){

        $id = (int)$data['id'];
        $status = (int)$data['status'];

        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_category` SET
						`status` = '.(int)$status.'
						WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //

    }


    public function updatePostStatus($data){

        $id = (int)$data['id'];
        $status = (int)$data['status'];


        $_data_post_before_update = $this->getPostItem(array('id'=>$id));
        $id_customer = isset($_data_post_before_update['post'][0]['author_id']) ? $_data_post_before_update['post'][0]['author_id'] :0 ;


        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_post` SET
						`status` = '.(int)$status.'
						WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);


        // loyality program //
        include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        // if customer delte post through my account
        if(!in_array($status,array(0,1))){
            $status = 0;
        }

        if(in_array($status,array(0,1))){
            $loyality->setLoyalityStatus(array('id_loyalty_status' => $status, 'id_customer' => $id_customer, 'id_post' => $id, 'type' => 'loyality_add_blog_post'));
            $loyality->setLoyalityStatus(array('id_loyalty_status' => $status, 'id_customer' => $id_customer, 'id_post' => $id, 'type' => 'loyality_add_blog_post_logo'));
        }
        // loyality program //




        // delete post send email to admin
        if($status == 2){
            $this->sendNotificationSuggestionToAdminWhenCustomerDeletePost(array('id'=>$id));
        }
        // delete post send email to admin

        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //


    }


    public function sendNotificationSuggestionToAdminWhenCustomerDeletePost($data = null){

        if(Configuration::get($this->_name.'noti') == 1 && Configuration::get($this->_name.'is_deleteblogpostcust') == 1){

            include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
            $obj = new blockblog();


            $id_post = $data['id'];

            ### get post info ###

            $_info_post = $this->getPostItem(array('id' => $id_post));

            $cookie = $this->context->cookie;
            $id_lang = (int)$cookie->id_lang;


            $title_post = isset($_info_post['post']['data'][$id_lang]['title'])?$_info_post['post']['data'][$id_lang]['title']:'';
            $img = isset($_info_post['post'][0]['img'])?$_info_post['post'][0]['img']:'';
            if(Tools::strlen($img)>0) {
                $img = $this->getHttpost().$obj->getCloudImgPath().$img;
            }

            $seo_url =  isset($_info_post['post']['data'][$id_lang]['seo_url'])?$_info_post['post']['data'][$id_lang]['seo_url']:'';
            $data_url = $this->getSEOURLs();
            $post_url = $data_url['post_url'];

            if($this->isURLRewriting()) {
                $com_url = $post_url . $seo_url;
            } else {
                $com_url = $post_url.'&post_id='.$id_post;
            }
            ### get post info ###


            $name = isset($_info_post['post'][0]['customer_name']) ? $_info_post['post'][0]['customer_name'] :'admin' ;
            $text = isset($_info_post['post']['data'][$id_lang]['content'])?strip_tags($_info_post['post']['data'][$id_lang]['content']):'';
            $text = Tools::substr($text,0,100);
            if(Tools::strlen($text)>100){
                $text .= $text."...";
            }
            $email = isset($_info_post['post'][0]['customer_email']) ? $_info_post['post'][0]['customer_email'] :'' ;

            $time_add = isset($_info_post['post'][0]['time_add']) ? $_info_post['post'][0]['time_add'] :date("Y-m-d H:i:s") ;


            /* Email generation */
            $templateVars = array(
                '{name}' => $name,
                '{text}' => Tools::stripslashes($text),
                '{title_post}' => $title_post,
                '{post_url}' => $com_url,
                '{picture}'=>$img,
                '{email}'=>$email,
                '{time_add}'=>$time_add,
            );


            /* Email sending */

            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            }
            else {
                $id_lang_current = Language::getIdByIso('en');
            }

            $subject = Configuration::get($this->_name . 'deleteblogpostcust_' . $id_lang_current);


            Mail::Send($id_lang_current, 'admin-customer-deleteblogpost', $subject, $templateVars,
                Configuration::get($this->_name.'mail'), 'Response Form', NULL, NULL,
                NULL, NULL, dirname(__FILE__).'/../mails/');



        }

    }


    public function updateCommentStatus($data){

        $id = (int)$data['id'];
        $status = (int)$data['status'];


        $_data_comm = $this->getCommentItem(array('id'=>$id));
        $id_customer = $_data_comm['comments'][0]['id_customer'];



        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_comments` SET
						`status` = '.(int)$status.'
						WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);


        // loyality program //
        include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        if(!in_array($status,array(0,1))){
            $status = 0;
        }

        if(in_array($status,array(0,1))){
            $loyality->setLoyalityStatus(array('id_loyalty_status' => $status, 'id_customer' => $id_customer, 'id_comment' => $id, 'type' => 'loyality_add_blog_comment'));

        }
        // loyality program //


        // delete comment send email to admin
        if($status == 2){
            $this->sendNotificationSuggestionToAdminWhenCustomerDeleteComment(array('id'=>$id));
        }
        // delete comment send email to admin


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //



    }


    public function sendNotificationSuggestionToAdminWhenCustomerDeleteComment($data = null){

        if(Configuration::get($this->_name.'noti') == 1 && Configuration::get($this->_name.'is_deleteblogcomcust') == 1){

            include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
            $obj = new blockblog();


            $id_editcomments = $data['id'];

            $_data_comm = $this->getCommentItem(array('id'=>$id_editcomments));
            $id_post = $_data_comm['comments'][0]['id_post'];
            $id_lang = $_data_comm['comments'][0]['id_lang'];

            ### get post info ###

            $_info_post = $this->getPostItem(array('id' => $id_post,'site'=>1));

            $title_post = isset($_info_post['post'][0]['title'])?$_info_post['post'][0]['title']:'';
            $img = isset($_info_post['post'][0]['img'])?$_info_post['post'][0]['img']:'';
            if(Tools::strlen($img)>0) {
                $img = $this->getHttpost().$obj->getCloudImgPath().$img;
            }

            $seo_url =  isset($_info_post['post'][0]['seo_url'])?$_info_post['post'][0]['seo_url']:'';
            $data_url = $this->getSEOURLs();
            $post_url = $data_url['post_url'];

            if($this->isURLRewriting()) {
                $com_url = $post_url . $seo_url.'#blogcomments';
            } else {
                $com_url = $post_url.'&post_id='.$id_post.'#blogcomments';
            }
            ### get post info ###


            $name = $_data_comm['comments'][0]['name'];
            $comments_comment = $_data_comm['comments'][0]['comment'];
            $email = $_data_comm['comments'][0]['email'];
            $time_add = $_data_comm['comments'][0]['time_add'];


            /* Email generation */
            $templateVars = array(
                '{name}' => $name,
                '{text}' => Tools::stripslashes($comments_comment),
                '{title_post}' => $title_post,
                '{com_url}' => $com_url,
                '{picture}'=>$img,
                '{email}'=>$email,
                '{time_add}'=>$time_add,
            );


            /* Email sending */

            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            }
            else {
                $id_lang_current = Language::getIdByIso('en');
            }

            $subject = Configuration::get($this->_name . 'deleteblogcomcust_' . $id_lang_current);


            Mail::Send($id_lang_current, 'admin-customer-deleteblogcomment', $subject, $templateVars,
                Configuration::get($this->_name.'mail'), 'Response Form', NULL, NULL,
                NULL, NULL, dirname(__FILE__).'/../mails/');



        }

    }

    public function updateSliderForPostStatus($data){

        $id = (int)$data['id'];
        $status = (int)$data['status'];

        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_post` SET
						`is_slider` = '.(int)$status.'
						WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //



    }

    public function updateCommentsForPostStatus($data){

        $id = (int)$data['id'];
        $status = (int)$data['status'];

        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_post` SET
						`is_comments` = '.(int)$status.'
						WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);



        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //



    }

    public function updateFBCommentsForPostStatus($data){

        $id = (int)$data['id'];
        $status = (int)$data['status'];

        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_post` SET
						`is_fbcomments` = '.(int)$status.'
						WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);

        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //



    }
	
private function  _translit( $str )
	{
    $str  = str_replace(array("®","'",'"','`','?','!','.','=',':','&','+',',','’', ')', '(', '$', '{', '}'), array(''), $str );
		
	$arrru = array ("А","а","Б","б","В","в","Г","г","Д","д","Е","е","Ё","ё","Ж","ж","З","з","И","и","Й","й","К","к","Л","л","М","м","Н","н", "О","о","П","п","Р","р","С","с","Т","т","У","у","Ф","ф","Х","х","Ц","ц","Ч","ч","Ш","ш","Щ","щ","Ъ","ъ","Ы","ы","Ь", "ь","Э","э","Ю","ю","Я","я",
    " ","-",",","«","»","+","/","(",")",".");

    $arren = array ("a","a","b","b","v","v","g","g","d","d","e","e","e","e","zh","zh","z","z","i","i","y","y","k","k","l","l","m","m","n","n", "o","o","p","p","r","r","s","s","t","t","u","u","ph","f","h","h","c","c","ch","ch","sh","sh","sh","sh","","","i","i","","","e", "e","yu","yu","ya","ya",
    "-","-","","","","","","","","");

	$textout = '';
    $textout = str_replace($arrru,$arren,$str);
    
    $textout = str_replace("--","-",$textout);
    
    $separator = "-";
    $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
    $special_cases = array( '&' => 'and');
    $textout = mb_strtolower( trim( $textout ), 'UTF-8' );
    $textout = str_replace( array_keys($special_cases), array_values( $special_cases), $textout );
    $textout = preg_replace( $accents_regex, '$1', htmlentities( $textout, ENT_QUOTES, 'UTF-8' ) );
    $textout = preg_replace("/[^a-z0-9]/u", "$separator", $textout);
    $textout = preg_replace("/[$separator]+/u", "$separator", $textout);
    
    if(Tools::strlen($textout)==0)
    	$textout = Tools::strtolower(Tools::passwdGen(6));
    	
     return Tools::strtolower($textout);
	}



	public function deleteCategory($data){
		
		$id = $data['id'];


        $this->deleteImg(array('id' => $id,'is_category'=>1));
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_category`
							   WHERE id ='.(int)$id.'';
		Db::getInstance()->Execute($sql);
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_category_data`
							   WHERE id_item ='.(int)$id.'';
		Db::getInstance()->Execute($sql);
		
		// get all posts_id for category //
		$sql = '
			SELECT post_id 
			FROM  `'._DB_PREFIX_.'blog_category2post` c2p
			WHERE c2p.category_id = '.(int)$id.'';
		$posts_ids = Db::getInstance()->ExecuteS($sql);
		
		// delete post_id x category_id
		$tmp_array_posts_ids = array();
		foreach($posts_ids as $_item){
				$id_post = $_item['post_id'];
				$tmp_array_posts_ids[] = $id_post;
				
					$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_category2post`
								   WHERE post_id = '.(int)$id_post.' AND 
								   category_id = '.(int)$id.'';
					
				    Db::getInstance()->Execute($sql);
		}

		// delete empty posts
		foreach($tmp_array_posts_ids as $item){
			$data_count = Db::getInstance()->getRow('
			SELECT COUNT(*) AS "count"
			FROM `'._DB_PREFIX_.'blog_category2post` c2p
			WHERE c2p.post_id = '.(int)$item.' 
			');
			
			if($data_count['count'] == 0){
				$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_post`
							   WHERE id ='.(int)$item.'';
				Db::getInstance()->Execute($sql);
					
				$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_post_data`
							   WHERE id_item ='.(int)$item.'';
				Db::getInstance()->Execute($sql);
				
			}
		}


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //


		
	}
	
	public function getTransformSEOURLtoID($_data){
		
	if(!is_numeric($_data['id'])){
			$id = $_data['id'];
			$sql = '
					SELECT pc.id_item as id
					FROM `'._DB_PREFIX_.'blog_category_data` pc
					WHERE seo_url = "'.pSQL($id).'"';
			$data_id = Db::getInstance()->GetRow($sql);
			$id = $data_id['id'];


		} else {
			$id = $_data['id'];
		}


		
		return $id;
	}
	
	public function getSEOURLForCategory($_data){
		
			$id = $_data['id'];
			$sql = '
					SELECT pc.seo_url
					FROM `'._DB_PREFIX_.'blog_category_data` pc
					WHERE id_item = "'.pSQL($id).'"';
			$data_id = Db::getInstance()->GetRow($sql);
			$id = $data_id['seo_url'];
		return $id;
	}
	
	public function getTransformSEOURLtoIDPost($_data){
		//var_dump(is_numeric($_data['id']));
	if(!is_numeric($_data['id'])){
			$id = $_data['id'];
			$sql = '
					SELECT pc.id_item as id
					FROM `'._DB_PREFIX_.'blog_post_data` pc
					WHERE seo_url = "'.pSQL($id).'"';

			$data_id = Db::getInstance()->GetRow($sql);


			$id = $data_id['id'];
		} else {
			$id = $_data['id'];
		}
		
		return $id;
	}
	
	public function getSEOURLForPost($_data){
			$id = $_data['id'];
			$sql = '
					SELECT pc.seo_url
					FROM `'._DB_PREFIX_.'blog_post_data` pc
					WHERE id_item = "'.pSQL($id).'"';
			$data_id = Db::getInstance()->GetRow($sql);
			$id = $data_id['seo_url'];
		
		return $id;
	}
	
	public function getIdPostifFriendlyURLEnable($data){
			$seo_url = $data['seo_url'];
		    $id_lang = $data['id_lang'];
			$sql = '
					SELECT pc.id_item as id_post
					FROM `'._DB_PREFIX_.'blog_post_data` pc
					WHERE pc.seo_url = "'.pSQL($seo_url).'" and pc.id_lang = '.(int)$id_lang;
			//echo $sql;
			$data_id = Db::getInstance()->GetRow($sql);
			$id_post = $data_id['id_post'];
			
			return $id_post;
	}
	
	public function getSEOFriendlyURLifFriendlyURLEnable($data){
			$id_post = $data['id_post'];
		    $id_lang = $data['id_lang'];
			$sql = '
					SELECT pc.seo_url
					FROM `'._DB_PREFIX_.'blog_post_data` pc
					WHERE pc.id_item = "'.(int)$id_post.'" and pc.id_lang = '.(int)$id_lang;
			//echo $sql;exit;
			$data_id = Db::getInstance()->GetRow($sql);
			$seo_url = $data_id['seo_url'];
			
			return $seo_url;
	}
	
public function getIdCatifFriendlyURLEnable($data){
			$seo_url = $data['seo_url'];
		    $id_lang = $data['id_lang'];
			$sql = '
					SELECT pc.id_item as id_cat
					FROM `'._DB_PREFIX_.'blog_category_data` pc
					WHERE pc.seo_url = "'.pSQL($seo_url).'" and pc.id_lang = '.(int)$id_lang;
			//echo $sql;
			$data_id = Db::getInstance()->GetRow($sql);
			$id_post = $data_id['id_cat'];
			
			return $id_post;
	}
	
	public function getSEOFriendlyURLifFriendlyURLEnableCat($data){
			$id_post = $data['id_cat'];
		    $id_lang = $data['id_lang'];
			$sql = '
					SELECT pc.seo_url
					FROM `'._DB_PREFIX_.'blog_category_data` pc
					WHERE pc.id_item = "'.(int)$id_post.'" and pc.id_lang = '.(int)$id_lang;
			//echo $sql;exit;
			$data_id = Db::getInstance()->GetRow($sql);
			$seo_url = $data_id['seo_url'];
			
			return $seo_url;
	}
	
	public function getCategoryItem($_data){
		$id = $_data['id'];
		$admin = isset($_data['admin'])?$_data['admin']:0;
		
		if($admin == 1){
				$sql = '
					SELECT pc.*
					FROM `'._DB_PREFIX_.'blog_category` pc
					WHERE id = '.(int)$id;
			$item = Db::getInstance()->ExecuteS($sql);
			
			foreach($item as $k => $_item){
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_category_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				foreach ($items_data as $item_data){
		    			$item['data'][$item_data['id_lang']]['title'] = $item_data['title'];
		    			$item['data'][$item_data['id_lang']]['seo_description'] = $item_data['seo_description'];
		    			$item['data'][$item_data['id_lang']]['seo_keywords'] = $item_data['seo_keywords'];
		    			$item['data'][$item_data['id_lang']]['seo_url'] = $item_data['seo_url'];
                        $item['data'][$item_data['id_lang']]['content'] = $item_data['content'];
                        $item['data'][$item_data['id_lang']]['time_add'] = $_item['time_add'];
                        $item['data'][$item_data['id_lang']]['status'] = $_item['status'];
		    	}


                $sql = '
                        SELECT pc.post_id
                        FROM `'._DB_PREFIX_.'blog_category2post` pc
                        WHERE pc.`category_id` = '.(int)$_item['id'].'
                        ';

                $post_ids = Db::getInstance()->ExecuteS($sql);

                $data_post_ids = array();
                foreach($post_ids as $k => $v){
                    $data_post_ids[] = $v['post_id'];
                }

                $item[0]['posts_ids'] = $data_post_ids;
		    	
			}
		} else {
			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;
			

				$sql = '
					SELECT pc.*
					FROM `'._DB_PREFIX_.'blog_category` pc
					LEFT JOIN `'._DB_PREFIX_.'blog_category_data` pc1
					ON(pc1.id_item = pc.id)
					WHERE pc.`id` = '.(int)$id.'
					AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
                    AND FIND_IN_SET('.$this->_id_shop.',pc.ids_shops)
					AND pc1.id_lang = '.(int)$current_language.'
					AND pc.status = 1
					ORDER BY pc.`position` ASC';
			
			$item = Db::getInstance()->ExecuteS($sql);

            //echo "<pre>"; var_dump($item);
			
			foreach($item as $k => $_item){


                ## generate thumbs ###
                if(Tools::strlen($_item['img'])>0){
                    $this->generateThumbImages(array('img'=>$_item['img'],
                            'width'=>$this->_img_category_page,
                            'height'=>$this->_img_category_page
                        )
                    );
                    $img = Tools::substr($_item['img'],0,-4)."-".$this->_img_category_page."x".$this->_img_category_page.".jpg";
                } else {
                    $img = $_item['img'];
                }

                $item[$k]['img'] = $img;

                ## generate thumbs ###

				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_category_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				foreach ($items_data as $item_data){
		    		
		    		if($current_language == $item_data['id_lang']){
		    			$item[$k]['title'] = $item_data['title'];
		    			$item[$k]['seo_description'] = $item_data['seo_description'];
		    			$item[$k]['seo_keywords'] = $item_data['seo_keywords'];
		    			$item[$k]['seo_url'] = $item_data['seo_url'];
                        $item[$k]['content'] = $item_data['content'];
		    			
		    		}
		    	}
		    }
			
		}
		//var_dump($item);
	   return array('category' => $item);
	}
	
public function getCategories($_data){
		$admin = isset($_data['admin'])?$_data['admin']:null;
        $is_myaccount = isset($_data['is_myaccount'])?$_data['is_myaccount']:null;
		$items = array();
		if($admin) {
            // admin handler

            $start = isset($_data['start']) ? $_data['start'] : '';
            $step = isset($_data['step']) ? $_data['step'] : '';

            $cookie = $this->context->cookie;
            $current_language = (int)$cookie->id_lang;


            if (Tools::strlen($start) > 0 && Tools::strlen($step) > 0) {
                $sql = '
				SELECT pc.*,
				(select count(*) as count from `' . _DB_PREFIX_ . 'blog_post` pc1
				    LEFT JOIN `' . _DB_PREFIX_ . 'blog_category2post` c2p
				    ON(pc1.id = c2p.post_id)
				    WHERE c2p.category_id = pc.id ) as count_posts
				FROM `' . _DB_PREFIX_ . 'blog_category` pc
				ORDER BY pc.`time_add` DESC LIMIT ' . (int)$start . ' ,' . (int)$step . '';
                //	echo $sql;
            } else {
                $sql = '
				SELECT pc.*,
				(select count(*) as count from `' . _DB_PREFIX_ . 'blog_post` pc1
				    LEFT JOIN `' . _DB_PREFIX_ . 'blog_category2post` c2p
				    ON(pc1.id = c2p.post_id)
				    WHERE c2p.category_id = pc.id ) as count_posts
				FROM `' . _DB_PREFIX_ . 'blog_category` pc
				ORDER BY pc.`time_add` DESC';
            }
            $categories = Db::getInstance()->ExecuteS($sql);


            foreach ($categories as $k => $_item) {


                ## generate thumbs ###
                if (Tools::strlen($_item['img']) > 0) {
                    $this->generateThumbImages(array('img' => $_item['img'],
                            'width' => $this->_img_category_list,
                            'height' => $this->_img_category_list
                        )
                    );
                    $img = Tools::substr($_item['img'], 0, -4) . "-" . $this->_img_category_list . "x" . $this->_img_category_list . ".jpg";
                } else {
                    $img = $_item['img'];
                }

                $items[$k]['img'] = $img;

                ## generate thumbs ###


                $items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `' . _DB_PREFIX_ . 'blog_category_data` pc
				WHERE pc.id_item = ' . (int)$_item['id'] . '
				');


                $cookie = $this->context->cookie;
                $defaultLanguage = $cookie->id_lang;

                $tmp_title = '';
                $tmp_id = '';
                $tmp_time_add = '';
                $tmp_seo_url = '';

                // languages
                $languages_tmp_array = array();

                foreach ($items_data as $item_data) {
                    $languages_tmp_array[] = $item_data['id_lang'];

                    $title = isset($item_data['title']) ? $item_data['title'] : '';
                    $id = isset($item_data['id_item']) ? $item_data['id_item'] : '';
                    $time_add = isset($categories[$k]['time_add']) ? $categories[$k]['time_add'] : '';
                    $seo_url = isset($categories[$k]['seo_url']) ? $categories[$k]['seo_url'] : '';

                    if (Tools::strlen($tmp_title) == 0) {
                        if (Tools::strlen($title) > 0)
                            $tmp_title = $title;
                    }

                    if (Tools::strlen($tmp_id) == 0) {
                        if (Tools::strlen($id) > 0)
                            $tmp_id = $id;
                    }

                    if (Tools::strlen($tmp_time_add) == 0) {
                        if (Tools::strlen($time_add) > 0)
                            $tmp_time_add = $time_add;
                    }

                    if (Tools::strlen($tmp_seo_url) == 0) {
                        if (Tools::strlen($seo_url) > 0)
                            $tmp_seo_url = $seo_url;
                    }

                    if ($defaultLanguage == $item_data['id_lang']) {
                        $items[$k]['title'] = $item_data['title'];
                        $items[$k]['id'] = $id;
                        $items[$k]['time_add'] = $time_add;
                        $items[$k]['seo_url'] = $item_data['seo_url'];
                    }

                }

                if (@Tools::strlen($items[$k]['title']) == 0)
                    $items[$k]['title'] = $tmp_title;

                if (@Tools::strlen($items[$k]['id']) == 0)
                    $items[$k]['id'] = $tmp_id;

                if (@Tools::strlen($items[$k]['time_add']) == 0)
                    $items[$k]['time_add'] = $tmp_time_add;

                $items[$k]['count_posts'] = $categories[$k]['count_posts'];

                // languages
                $items[$k]['ids_lng'] = $languages_tmp_array;

                $lang_for_category = array();
                foreach ($languages_tmp_array as $lng_id) {
                    $data_lng = Language::getLanguage($lng_id);
                    $lang_for_category[] = $data_lng['iso_code'];
                }
                $lang_for_category = implode(",", $lang_for_category);

                $items[$k]['iso_lang'] = $lang_for_category;

                $items[$k]['status'] = $_item['status'];
            }

            $data_count_categories = Db::getInstance()->getRow('
			SELECT COUNT(`id`) AS "count"
			FROM `' . _DB_PREFIX_ . 'blog_category`
			');

            // admin handler
        }elseif($is_myaccount){
                // admin handler

                $start = isset($_data['start'])?$_data['start']:'';
                $step = isset($_data['step'])?$_data['step']:'';

                $cookie = $this->context->cookie;
                $current_language = (int)$cookie->id_lang;

                $id_shop = $this->_id_shop;

                if(Tools::strlen($start)>0 && Tools::strlen($step)>0){
                    $sql = '
				SELECT pc.*,
				(select count(*) as count from `'._DB_PREFIX_.'blog_post` pc1
				    LEFT JOIN `'._DB_PREFIX_.'blog_category2post` c2p
				    ON(pc1.id = c2p.post_id)
				    LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc1.author_id = ba2c.id_customer)
				    WHERE c2p.category_id = pc.id and pc1.status = 1 and ba2c.status != 2) as count_posts
				FROM `'._DB_PREFIX_.'blog_category` pc

				WHERE
				FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
                AND pc.status = 1

				ORDER BY pc.`time_add` DESC LIMIT '.(int)$start.' ,'.(int)$step.'';
                    //	echo $sql;
                } else {
                    $sql = '
				SELECT pc.*,
				(select count(*) as count from `'._DB_PREFIX_.'blog_post` pc1
				    LEFT JOIN `'._DB_PREFIX_.'blog_category2post` c2p
				    ON(pc1.id = c2p.post_id)
				    WHERE c2p.category_id = pc.id ) as count_posts
				FROM `'._DB_PREFIX_.'blog_category` pc
				WHERE
				FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
                AND pc.status = 1

				ORDER BY pc.`time_add` DESC';
                }
                $categories = Db::getInstance()->ExecuteS($sql);


                foreach($categories as $k => $_item){



                    ## generate thumbs ###
                    if(Tools::strlen($_item['img'])>0){
                        $this->generateThumbImages(array('img'=>$_item['img'],
                                'width'=>$this->_img_category_list,
                                'height'=>$this->_img_category_list
                            )
                        );
                        $img = Tools::substr($_item['img'],0,-4)."-".$this->_img_category_list."x".$this->_img_category_list.".jpg";
                    } else {
                        $img = $_item['img'];
                    }

                    $items[$k]['img'] = $img;

                    ## generate thumbs ###


                    $items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_category_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');


                    $cookie = $this->context->cookie;
                    $defaultLanguage =  $cookie->id_lang;

                    $tmp_title = '';
                    $tmp_id = '';
                    $tmp_time_add = '';
                    $tmp_seo_url = '';

                    // languages
                    $languages_tmp_array = array();

                    foreach ($items_data as $item_data){
                        $languages_tmp_array[] = $item_data['id_lang'];

                        $title = isset($item_data['title'])?$item_data['title']:'';
                        $id = isset($item_data['id_item'])?$item_data['id_item']:'';
                        $time_add = isset($categories[$k]['time_add'])?$categories[$k]['time_add']:'';
                        $seo_url = isset($categories[$k]['seo_url'])?$categories[$k]['seo_url']:'';

                        if(Tools::strlen($tmp_title)==0){
                            if(Tools::strlen($title)>0)
                                $tmp_title = $title;
                        }

                        if(Tools::strlen($tmp_id)==0){
                            if(Tools::strlen($id)>0)
                                $tmp_id = $id;
                        }

                        if(Tools::strlen($tmp_time_add)==0){
                            if(Tools::strlen($time_add)>0)
                                $tmp_time_add = $time_add;
                        }

                        if(Tools::strlen($tmp_seo_url)==0){
                            if(Tools::strlen($seo_url)>0)
                                $tmp_seo_url = $seo_url;
                        }

                        if($defaultLanguage == $item_data['id_lang']){
                            $items[$k]['title'] = $item_data['title'];
                            $items[$k]['id'] = $id;
                            $items[$k]['time_add'] = $time_add;
                            $items[$k]['seo_url'] = $item_data['seo_url'];
                        }

                    }

                    if(@Tools::strlen($items[$k]['title'])==0)
                        $items[$k]['title'] = $tmp_title;

                    if(@Tools::strlen($items[$k]['id'])==0)
                        $items[$k]['id'] = $tmp_id;

                    if(@Tools::strlen($items[$k]['time_add'])==0)
                        $items[$k]['time_add'] = $tmp_time_add;

                    $items[$k]['count_posts'] = $categories[$k]['count_posts'];

                    // languages
                    $items[$k]['ids_lng'] = $languages_tmp_array;

                    $lang_for_category = array();
                    foreach($languages_tmp_array as $lng_id){
                        $data_lng = Language::getLanguage($lng_id);
                        $lang_for_category[] = $data_lng['iso_code'];
                    }
                    $lang_for_category = implode(",",$lang_for_category);

                    $items[$k]['iso_lang'] = $lang_for_category;

                    $items[$k]['status'] = $_item['status'];
                }

                $data_count_categories = Db::getInstance()->getRow('
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_category`
			');

                // admin handler


		} else{

            // site handler

			$start = $_data['start'];
			$step = (int)$_data['step'];
			
			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;

            $order_by = 'ORDER BY pc.`'.Configuration::get($this->_name.'blog_cat_order').'` '.Configuration::get($this->_name.'blog_cat_ad').'';
			
			$items_tmp = Db::getInstance()->ExecuteS('
			SELECT pc.*,
				   (select count(*) as count from `'._DB_PREFIX_.'blog_post` pc1 
				    LEFT JOIN `'._DB_PREFIX_.'blog_category2post` c2p
				    ON(pc1.id = c2p.post_id)
				    LEFT JOIN `'._DB_PREFIX_.'blog_post_data` bpd
				    ON(bpd.id_item = pc1.id)
				    WHERE c2p.category_id = pc.id AND bpd.id_lang = '.(int)$current_language.'
					AND pc1.status = 1
					AND FIND_IN_SET('.(int)$this->_id_shop.',pc1.ids_shops)
					AND FIND_IN_SET('.(int)$this->_id_group.',pc1.ids_groups)
					'.(($this->_is_show_now)?'AND (pc1.`publish_date` <= NOW())':'').'
					) as count_posts
			FROM `'._DB_PREFIX_.'blog_category` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_category_data` pc_d
			on(pc.id = pc_d.id_item)
			WHERE pc_d.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
            AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
            AND pc.status = 1

			'.$order_by.' LIMIT '.(int)$start.' ,'.(int)$step.'');
			
			$items = array();
			
			foreach($items_tmp as $k => $_item){


                ## generate thumbs ###
                if(Tools::strlen($_item['img'])>0){

                    $this->generateThumbImages(array('img'=>$_item['img'],
                            'width'=>$this->_img_category_list,
                            'height'=>$this->_img_category_list
                        )
                    );
                    $img = Tools::substr($_item['img'],0,-4)."-".$this->_img_category_list."x".$this->_img_category_list.".jpg";
                } else {
                    $img = $_item['img'];
                }

                $items[$k]['img'] = $img;

                ## generate thumbs ###

				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_category_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				
				
				foreach ($items_data as $item_data){
		    		
		    		if($current_language == $item_data['id_lang']){
		    			$items[$k]['title'] = $item_data['title'];
		    			$items[$k]['seo_description'] = $item_data['seo_description'];
		    			$items[$k]['seo_keywords'] = $item_data['seo_keywords'];
		    			$items[$k]['count_posts'] = $_item['count_posts'];
		    			$items[$k]['id'] = $_item['id'];
		    			$items[$k]['time_add'] = $_item['time_add'];
		    			$items[$k]['seo_url'] = $item_data['seo_url'];
                        $items[$k]['content'] = $item_data['content'];
		    		} 
		    	}
		    }
			
			$data_count_categories = Db::getInstance()->getRow('
			SELECT COUNT(pc.`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_category` pc LEFT JOIN `'._DB_PREFIX_.'blog_category_data` pc_d
			on(pc.id = pc_d.id_item)
			WHERE pc_d.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			AND pc.status = 1

			');

            // site handler
		}	
		return array('categories' => $items, 'count_all' => $data_count_categories['count'] );
	}
	
	public function getPosts($_data){
		$admin = isset($_data['admin'])?$_data['admin']:null;
		
		$id = isset($_data['id'])?$_data['id']:0;
		
		if($admin == 1){
			$sql = '
			SELECT pc.*,
			(select count(*) as count from `'._DB_PREFIX_.'blog_comments` pc1 
				    WHERE pc1.id_post = pc.id and pc1.status=1) as count_comments,
			(select count(*) as count from `'._DB_PREFIX_.'blog_post_like` pclike
				    WHERE pclike.post_id = pc.id) as count_likes
			FROM `'._DB_PREFIX_.'blog_post` pc LEFT JOIN `'._DB_PREFIX_.'blog_category2post` c2p
			ON(pc.id = c2p.post_id)
			WHERE c2p.category_id = '.(int)$id.' 
			ORDER BY pc.`time_add` DESC';
            $items = Db::getInstance()->ExecuteS($sql);
			
			foreach($items as $k => $_item){
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				$cookie = $this->context->cookie;
				$defaultLanguage =  $cookie->id_lang;
				
				$tmp_title = '';
				
				// languages
				$languages_tmp_array = array();
				
				foreach ($items_data as $item_data){
					$languages_tmp_array[] = $item_data['id_lang'];
					
		    		$title = isset($item_data['title'])?$item_data['title']:'';
		    		if(Tools::strlen($tmp_title)==0){
		    			if(Tools::strlen($title)>0)
		    					$tmp_title = $title; 
		    		}
		    		
		    		if($defaultLanguage == $item_data['id_lang']){
		    			$items[$k]['title'] = $item_data['title'];
		    		} 
		    		
		    	}
		    	
		    	// languages
		    	$items[$k]['ids_lng'] = $languages_tmp_array;
		    	
		    	if(@Tools::strlen($items[$k]['title'])==0)
		    		$items[$k]['title'] = $tmp_title;
		    	
				}
			
			
			$data_count_posts = Db::getInstance()->getRow('
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_post`  as pc LEFT JOIN `'._DB_PREFIX_.'blog_category2post` c2p
			ON(pc.id = c2p.post_id)
			WHERE c2p.category_id = '.(int)$id.'
			');
			
		} elseif($admin == 2){
			$start = $_data['start'];
			$step = $_data['step'];
			$sql = '
			SELECT pc.*,
			(select count(*) as count from `'._DB_PREFIX_.'blog_comments` pc1
				    WHERE pc1.id_post = pc.id and pc1.status=1) as count_comments,
			(select count(*) as count from `'._DB_PREFIX_.'blog_post_like` pclike
				    WHERE pclike.post_id = pc.id) as count_likes
			FROM `'._DB_PREFIX_.'blog_post` pc
			ORDER BY pc.`time_add` DESC  LIMIT '.(int)$start.' ,'.(int)$step.'';
            $items = Db::getInstance()->ExecuteS($sql);
			
			
			foreach($items as $k => $_item){
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				$cookie = $this->context->cookie;
				$defaultLanguage =  $cookie->id_lang;
				
				$tmp_title = '';
				// languages
				$languages_tmp_array = array();
				
				
				foreach ($items_data as $item_data){
		    		$languages_tmp_array[] = $item_data['id_lang'];
					
		    		$title = isset($item_data['title'])?$item_data['title']:'';
		    		if(Tools::strlen($tmp_title)==0){
		    			if(Tools::strlen($title)>0)
		    					$tmp_title = $title; 
		    		}
		    		
		    		
		    		if($defaultLanguage == $item_data['id_lang']){
		    			$items[$k]['title'] = $item_data['title'];
		    		} 
		    	}
		    	
		    	if(@Tools::strlen($items[$k]['title'])==0)
		    		$items[$k]['title'] = $tmp_title;
		    		
		    	// languages
		    	$items[$k]['ids_lng'] = $languages_tmp_array;
		    	
		    	
				}
			
			$data_count_posts = Db::getInstance()->getRow('
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_post`  as pc
			');
		} else{
			$start = $_data['start'];
			$step = $_data['step'];
			
			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;

            $order_by = 'ORDER BY pc.`'.Configuration::get($this->_name.'blog_post_order').'` '.Configuration::get($this->_name.'blog_post_ad').'';

			/*$sql = '
			SELECT pc.* , ba2c.status as status_author,
				(select count(*) as count from `'._DB_PREFIX_.'blog_comments` c2pc 
				 where c2pc.id_post = pc.id and c2pc.status = 1 and c2pc.id_shop = '.(int)$this->_id_shop.' 
				 and c2pc.id_lang = '.(int)$current_language.' ) 
				 as count_comments,
				 (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1) as count_like,
                (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1 and ip = \''.pSQL($_SERVER['REMOTE_ADDR']).'\') as is_liked_post,
                (select count(*) as count from `'._DB_PREFIX_.'blog_comments` pcc WHERE pcc.id_post = pc.id and pcc.status = 1 and pcc.id_lang = '.(int)$current_language.') as count_comments

			FROM `'._DB_PREFIX_.'blog_post` pc LEFT JOIN `'._DB_PREFIX_.'blog_category2post` c2p
			ON(pc.id = c2p.post_id)
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			on(pc.id = pc_d.id_item)
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)

			WHERE pc.status = 1 and ba2c.status != 2 and pc_d.id_lang = '.(int)$current_language.' AND c2p.category_id = '.(int)$id.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
            AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups) AND ba2c.id_shop = '.(int)$this->_id_shop.'
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			'.$order_by.' LIMIT '.(int)$start.' ,'.(int)$step.'';*/


            $sql = '
			SELECT pc.* , 1 as status_author,
				(select count(*) as count from `'._DB_PREFIX_.'blog_comments` c2pc
				 where c2pc.id_post = pc.id and c2pc.status = 1 and c2pc.id_shop = '.(int)$this->_id_shop.'
				 and c2pc.id_lang = '.(int)$current_language.' )
				 as count_comments,
				 (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1) as count_like,
                (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1 and ip = \''.pSQL($_SERVER['REMOTE_ADDR']).'\') as is_liked_post,
                (select count(*) as count from `'._DB_PREFIX_.'blog_comments` pcc WHERE pcc.id_post = pc.id and pcc.status = 1 and pcc.id_lang = '.(int)$current_language.') as count_comments

			FROM `'._DB_PREFIX_.'blog_post` pc LEFT JOIN `'._DB_PREFIX_.'blog_category2post` c2p
			ON(pc.id = c2p.post_id)
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			on(pc.id = pc_d.id_item)


			WHERE pc.status = 1 and pc_d.id_lang = '.(int)$current_language.' AND c2p.category_id = '.(int)$id.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
            AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			'.$order_by.' LIMIT '.(int)$start.' ,'.(int)$step.'';

			
			$items = Db::getInstance()->ExecuteS($sql);	
			
			foreach($items as $k1=>$_item){
				$id_post = $_item['id'];
				
				if(Tools::strlen($_item['img'])>0){
					$this->generateThumbImages(array('img'=>$_item['img'], 
		    												 'width'=>$this->_img_posts_width,
		    												 'height'=>$this->_img_posts_height
		    												)
		    											);
		    		$img = Tools::substr($_item['img'],0,-4)."-".$this->_img_posts_width."x".$this->_img_posts_height.".jpg";
		    	} else {
		    		$img = $_item['img'];
				}
		    		
		    	$items[$k1]['img'] = $img;


                $items[$k1]['author'] = $_item['author'];
                $author_id = $_item['author_id'];
                $items[$k1]['author_id'] = $author_id;

                $info_path = $this->getAvatarPath(array('id_customer'=>$author_id,'id_shop'=>$this->_id_shop));

                $items[$k1]['avatar'] = $info_path['avatar'];
                $items[$k1]['is_show_ava'] = $info_path['is_show'];
				
				$category_ids = Db::getInstance()->ExecuteS('
				SELECT pc.category_id
				FROM `'._DB_PREFIX_.'blog_category2post` pc
				WHERE pc.`post_id` = '.(int)$id_post.'');
				$data_category_ids = array();
				foreach($category_ids as $k => $v){
					$_info_ids = $this->getCategoryItem(array('id' => $v['category_id']));
                    $category_data_ids = isset($_info_ids['category'][0])?$_info_ids['category'][0]:array();
					$ids_item = sizeof($category_data_ids)>0?$category_data_ids:array();
					//var_dump($ids_item); echo "<br><hr><br>";
					if(sizeof($ids_item)>0){
					$data_category_ids[] = $ids_item;
					}
				}
				
				$items[$k1]['category_ids'] = $data_category_ids;
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				foreach ($items_data as $item_data){
		    		
		    		if($current_language == $item_data['id_lang']){
		    			$items[$k1]['title'] = $item_data['title'];
		    			$items[$k1]['seo_description'] = $item_data['seo_description'];
		    			$items[$k1]['seo_keywords'] = $item_data['seo_keywords'];
		    			$items[$k1]['content'] = $item_data['content'];
		    			$items[$k1]['id'] = $_item['id'];
		    			$items[$k1]['time_add'] = $_item['time_add'];
		    			$items[$k1]['seo_url'] = $item_data['seo_url'];
		    		} 
		    	}
				
			}
			
			/*$sql = '
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_post` pc LEFT JOIN `'._DB_PREFIX_.'blog_category2post` c2p
			ON(pc.id = c2p.post_id)
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			on(pc.id = pc_d.id_item )
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)

			WHERE c2p.category_id = '.(int)$id.' and pc_d.id_lang = '.(int)$current_language.' 
			AND pc.status = 1 and ba2c.status != 2 AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
            AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups) AND ba2c.id_shop = '.(int)$this->_id_shop.'
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			';*/


            $sql = '
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_post` pc LEFT JOIN `'._DB_PREFIX_.'blog_category2post` c2p
			ON(pc.id = c2p.post_id)
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			on(pc.id = pc_d.id_item )


			WHERE c2p.category_id = '.(int)$id.' and pc_d.id_lang = '.(int)$current_language.'
			AND pc.status = 1  AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
            AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			';


			$data_count_posts = Db::getInstance()->getRow($sql);
		}	
		return array('posts' => $items, 'count_all' => $data_count_posts['count'] );
	}
	
	
	public function  getAllPosts($_data){


		
			$start = $_data['start'];
			$step = $_data['step'];
			$is_search = isset($_data['is_search'])?$_data['is_search']:0;
			$search = isset($_data['search'])?$_data['search']:'';
			$is_arch = isset($_data['is_arch'])?$_data['is_arch']:0;
			$month = isset($_data['month'])?$_data['month']:0;
			$year = isset($_data['year'])?$_data['year']:0;

			$sql_condition = '';
			if($is_search == 1){
				$sql_condition = "AND (
	    		   LOWER(pc_d.title) LIKE BINARY LOWER('%".pSQL(trim($search))."%')
	    		   OR
	    		   LOWER(pc_d.content) LIKE BINARY LOWER('%".pSQL(trim($search))."%')
	    		   ) ";
			}
			
			if($is_arch == 1){
				$sql_condition = 'AND YEAR(pc.time_add) = "'.$year.'" AND
    							  MONTH(pc.time_add) = "'.$month.'"';
			}
			
			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;


            $order_by = 'ORDER BY pc.'.Configuration::get($this->_name.'blog_post_order').' '.Configuration::get($this->_name.'blog_post_ad').'';

        //$order_by = 'ORDER BY pc_d.title asc';
			
			/*$sql = '
			SELECT pc.*, ba2c.status as status_author,
				(select count(*) as count from `'._DB_PREFIX_.'blog_comments` c2pc 
				 where c2pc.id_post = pc.id and c2pc.status = 1 and c2pc.id_shop = '.(int)$this->_id_shop.' 
				 and c2pc.id_lang = '.(int)$current_language.' ) 
				 as count_comments,
				 (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1) as count_like,
                (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1 and ip = \''.pSQL($_SERVER['REMOTE_ADDR']).'\') as is_liked_post,
                (select count(*) as count from `'._DB_PREFIX_.'blog_comments` pcc WHERE pcc.id_post = pc.id and pcc.status = 1 and pcc.id_lang = '.(int)$current_language.') as count_comments
			FROM `'._DB_PREFIX_.'blog_post` pc 
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			on(pc.id = pc_d.id_item)
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE pc.status = 1 and pc_d.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops) '.$sql_condition.'
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			AND ba2c.status != 2 AND ba2c.id_shop = '.(int)$this->_id_shop.'

			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			'.$order_by.' LIMIT '.(int)$start.' ,'.(int)$step.'';*/


        $sql = '
			SELECT pc.*, 1 as status_author,
				(select count(*) as count from `'._DB_PREFIX_.'blog_comments` c2pc
				 where c2pc.id_post = pc.id and c2pc.status = 1 and c2pc.id_shop = '.(int)$this->_id_shop.'
				 and c2pc.id_lang = '.(int)$current_language.' )
				 as count_comments,
				 (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1) as count_like,
                (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1 and ip = \''.pSQL($_SERVER['REMOTE_ADDR']).'\') as is_liked_post,
                (select count(*) as count from `'._DB_PREFIX_.'blog_comments` pcc WHERE pcc.id_post = pc.id and pcc.status = 1 and pcc.id_lang = '.(int)$current_language.') as count_comments
			FROM `'._DB_PREFIX_.'blog_post` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			on(pc.id = pc_d.id_item)
			WHERE pc.status = 1 and pc_d.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops) '.$sql_condition.'
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)


			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			'.$order_by.' LIMIT '.(int)$start.' ,'.(int)$step.'';

			$items = Db::getInstance()->ExecuteS($sql);	

			foreach($items as $k1=>$_item){
				$id_post = $_item['id'];
				
				if(Tools::strlen($_item['img'])>0){
					$this->generateThumbImages(array('img'=>$_item['img'], 
		    												 'width'=>$this->_img_posts_width,
		    												 'height'=>$this->_img_posts_height
		    												)
		    											);
		    		$img = Tools::substr($_item['img'],0,-4)."-".$this->_img_posts_width."x".$this->_img_posts_height.".jpg";
		    	} else {
		    		$img = $_item['img'];
				}
		    		
		    	$items[$k1]['img'] = $img;


                $items[$k1]['author'] = $_item['author'];
                $author_id = $_item['author_id'];
                $items[$k1]['author_id'] = $author_id;

                $info_path = $this->getAvatarPath(array('id_customer'=>$author_id,'id_shop'=>$this->_id_shop));

                $items[$k1]['avatar'] = $info_path['avatar'];
                $items[$k1]['is_show_ava'] = $info_path['is_show'];


				
				$category_ids = Db::getInstance()->ExecuteS('
				SELECT pc.category_id
				FROM `'._DB_PREFIX_.'blog_category2post` pc
				WHERE pc.`post_id` = '.(int)$id_post.'');
				$data_category_ids = array();
				foreach($category_ids as $v){
					$_info_ids = $this->getCategoryItem(array('id' => $v['category_id']));
                    $category_info_ds = isset($_info_ids['category'][0])?$_info_ids['category'][0]:array();
					$ids_item = sizeof($category_info_ds)>0?$category_info_ds:array();
					
					if(sizeof($ids_item)>0){
					$data_category_ids[] = $ids_item;
					}
				}
				
				$items[$k1]['category_ids'] = $data_category_ids;
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				foreach ($items_data as $item_data){
		    		
		    		if($current_language == $item_data['id_lang']){
		    			$items[$k1]['title'] = $item_data['title'];
		    			$items[$k1]['seo_description'] = $item_data['seo_description'];
		    			$items[$k1]['seo_keywords'] = $item_data['seo_keywords'];
		    			$items[$k1]['content'] = $item_data['content'];
		    			$items[$k1]['id'] = $_item['id'];
		    			$items[$k1]['time_add'] = $_item['time_add'];
		    			$items[$k1]['seo_url'] = $item_data['seo_url'];
		    		} 
		    	}
				
			}
			
			/*$sql = '
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_post` pc 
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			on(pc.id = pc_d.id_item )
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE  pc_d.id_lang = '.(int)$current_language.' 
			AND pc.status = 1 AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			AND ba2c.status != 2 AND ba2c.id_shop = '.(int)$this->_id_shop.'
			 '.$sql_condition.'
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			';*/


        $sql = '
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_post` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			on(pc.id = pc_d.id_item )
			WHERE  pc_d.id_lang = '.(int)$current_language.'
			AND pc.status = 1 AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)

			 '.$sql_condition.'
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			';



			
			$data_count_posts = Db::getInstance()->getRow($sql);


		
		return array('posts' => $items, 'count_all' => $data_count_posts['count'] );
	}
	
	
	public function savePost($data){

        $cat_shop_association = $data['cat_shop_association']?$data['cat_shop_association']:array();
		$ids_shops = implode(",",$cat_shop_association);

        $group_association = implode(",",$data['group_association']);




        $related_posts = $data['ids_related_posts']?$data['ids_related_posts']:array();

		$related_posts = implode(",",$related_posts);
		if(Tools::strlen($related_posts)==0) $related_posts = 0;
		
		$related_products = $data['related_products']?$data['related_products']:'';
		$related_products = explode("-",$related_products,-1);
        $related_products_array = $related_products; //for cache
	    $related_products = implode(",",$related_products);
		if(Tools::strlen($related_products)==0) $related_products = 0;
		
		
		$time_add = date('Y-m-d H:i:s',strtotime($data['time_add']));

        $ids_categories = (!empty($data['ids_categories']) && sizeof($data['ids_categories'])>0)?$data['ids_categories']:array();
		$post_status = $data['post_status'];
		$post_iscomments = $data['post_iscomments'];
        $post_isfbcomments = $data['post_isfbcomments'];

        $after_time_add = $data['after_time_add'];
        if($after_time_add) {
            $publish_date = $data['publish_date'];
        } else {
            //$publish_date = '0000-00-00 00:00:00';
        }
        $is_rss = $data['is_rss'];

        $author = $data['author'];
        $is_slider = $data['is_slider'];


        $info = $data['info'];



        $sql = 'INSERT into `'._DB_PREFIX_.'blog_post` SET
							   `status` = \''.(int)($post_status).'\',
							   `is_comments` = \''.(int)($post_iscomments).'\',
							   `is_fbcomments` = \''.(int)($post_isfbcomments).'\',
							   `ids_shops` = "'.pSQL($ids_shops).'",
							   `related_products` = "'.pSQL($related_products).'",
							   `related_posts` = "'.pSQL($related_posts).'",
							   `ids_groups` = "'.pSQL($group_association).'",
							   `after_time_add` = \''.(int)($after_time_add).'\',
							   `publish_date` = "'.(!empty($publish_date)?pSQL($publish_date):NULL).'",
							   `is_rss` = \''.(int)($is_rss).'\',
							   `is_slider` = \''.(int)($is_slider).'\',
							   `author` = "'.pSQL($author).'",
							   `time_add` = "'.pSQL($time_add).'"
							   ';
		Db::getInstance()->Execute($sql);
		
		$post_id = Db::getInstance()->Insert_ID();


        ## set  position ##
        /*$sql_count = 'select count(*) as count from `'._DB_PREFIX_.'blog_post`';
        $result_count = Db::getInstance()->executeS($sql_count);
        $position_id = (int)$result_count[0]['count'] - 1;*/

        /*$sql_count = 'select id from `'._DB_PREFIX_.'blog_post` order by id desc limit 1';
        $result_count = Db::getInstance()->executeS($sql_count);
        $position_id = (int)$result_count[0]['id'] + 1;*/

        $position_id = $post_id-1;

        Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'blog_post
							SET position = '. (int)($position_id) .' WHERE id = ' . (int)($post_id));
        ## set  position ##

		
		foreach($data['data_title_content_lang'] as $language => $item){
		
		$post_title = $item['post_title'];
		$post_seokeywords = $item['post_seokeywords'];
		$post_seodescription = $item['post_seodescription'];
		$post_content = $item['post_content'];


        $tags = $item['tags'];
        $tags = explode(",",$tags);
        $tags = array_unique($tags);
        $tags = implode(",",$tags);



        $seo_url_pre = Tools::strlen($item['seo_url'])>0?$item['seo_url']:$post_title;
	    $seo_url = $this->_translit($seo_url_pre);
	    
	    
	    $sql = 'SELECT count(*) as count
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE seo_url = "'.pSQL($seo_url,true).'" AND id_lang = '.(int)$language;
		$data_seo_url = Db::getInstance()->GetRow($sql);
		
		if($data_seo_url['count'] != 0)
			$seo_url = $seo_url."-".Tools::strtolower(Tools::passwdGen(6));
		
		
		$sql = 'INSERT into `'._DB_PREFIX_.'blog_post_data` SET
							   `id_item` = \''.(int)($post_id).'\',
							   `id_lang` = \''.(int)($language).'\',
							   `title` = \''.pSQL($post_title).'\',
							   `seo_keywords` = \''.pSQL($post_seokeywords).'\',
							   `seo_description` = \''.pSQL($post_seodescription).'\',
							   `content` = "'.pSQL($post_content,true).'",
							   `tags` = "'.pSQL($tags).'",
							   `seo_url` = "'.pSQL($seo_url).'"
							   ';
		
		
		Db::getInstance()->Execute($sql);
		
		}

        include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
        $obj = new blockblog();

        if($obj->is_demo == 0) {


            $error_data = $this->saveImage(array('post_id' => $post_id));

            $error = $error_data['error'];
            $error_text = $error_data['error_text'];

            $id_customer = $data['id_customer'];
            $post_images_ava = $data['post_images_ava'];
            $this->saveImageAvatar(array('id' => $post_id, 'post_images' => $post_images_ava,'id_customer'=>$id_customer));


            ### posts API ###
            $data_api = array(
                'post_id'=>$post_id,
            );
            $this->postToWallPosts($data_api);
            ### posts API ###



        } else {
            $error = 0;
            $error_text = '';
        }


        // author info //
        include_once(_PS_MODULE_DIR_.$this->_name . '/classes/userprofileblockblog.class.php');
        $obj_userprofileblockblog = new userprofileblockblog();
        $obj_userprofileblockblog->updateDataAuthor(array('id_customer'=>$id_customer,'info'=>$info));
        // author info //



        if($ids_categories) {
          foreach ($ids_categories as $id_cat) {


                $sql = 'INSERT into `' . _DB_PREFIX_ . 'blog_category2post` SET
							   `category_id` = \'' . pSQL($id_cat) . '\',
							   `post_id` = \'' . pSQL($post_id) . '\'
							   ';

                Db::getInstance()->Execute($sql);

            }
        }


        // clear cache only for blog //
        $this->clearSmartyCacheBlog(array('id_products'=>$related_products_array));
        // clear cache only for blog //

        return array('error'=>$error,'error_text'=>$error_text,'post_id'=>$post_id);

		
	}


    public function postToWallPosts($data){

        $cookie = $this->context->cookie;
        $id_lang = (int)($cookie->id_lang);


        $id_post = $data['post_id'];




        include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
        $obj = new blockblog();


        ### get post info ###

        $_info_post = $this->getPostItem(array('id' => $id_post,'site'=>1));

        $title_post = isset($_info_post['post'][0]['title'])?$_info_post['post'][0]['title']:'';
        $img = isset($_info_post['post'][0]['img'])?$_info_post['post'][0]['img']:'';


        if(Tools::strlen($img)>0) {
            $image_path = _PS_MODULE_DIR_ .$this->_name . "/upload/" . $img;
            $picture = $this->getHttpost().$obj->getCloudImgPath().$img;
        } else {
            $picture = '';
            $image_path  = '';
        }

        $seo_url =  isset($_info_post['post'][0]['seo_url'])?$_info_post['post'][0]['seo_url']:'';
        $data_url = $this->getSEOURLs();
        $post_url = $data_url['post_url'];

        if($this->isURLRewriting()) {
            $post_url = $post_url . $seo_url;
        } else {
            $post_url = $post_url.'&post_id='.$id_post;
        }
        ### get post info ###



        include_once(_PS_MODULE_DIR_.$this->_name.'/classes/postshelp.class.php');
        $postshelp = new postshelp();


        $data_post = array(
            'name'=>$this->_name,
            'product_name'=>$title_post,
            'product_link'=>$post_url,
            'image' => $picture,
            'id_lang'=>$id_lang,
            'image_path'=>$image_path,
        );


        $postshelp->postToAPI($data_post);
    }

    public function savePostByCustomer($data){

        $ids_shops = $this->_id_shop;

        $group_association = implode(",",$data['group_association']);


        $related_posts = 0;
        $related_products = 0;


        $time_add = date('Y-m-d H:i:s');

        $ids_categories = !empty($data['ids_categories'])?$data['ids_categories']:array();

        $ids_categories = sizeof($ids_categories)>0?$ids_categories:array();


/*
        $ids_categories_allowed = Configuration::get($this->_name.'ids_categories');
        $ids_categories_allowed = $ids_categories_allowed?explode(",",$ids_categories_allowed):array();
        $ids_categories = array_diff($ids_categories_allowed,$ids_categories);*/


        $post_status = $data['post_status'];
        $post_iscomments = 0;
        $post_isfbcomments = 0;


        $after_time_add = 0;
        $publish_date = '0000-00-00 00:00:00';
        $is_rss = 0;


        include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
        $obj_userprofileblockblog = new userprofileblockblog();


        $id_customer = $data['id_customer'];
        $customer_data = $obj_userprofileblockblog->getInfoAboutCustomer(array('id_customer' => $id_customer, 'is_full' => 1));
        $author = $customer_data['customer_name'];



        $sql = 'INSERT into `'._DB_PREFIX_.'blog_post` SET
							   `status` = \''.(int)($post_status).'\',
							   `is_comments` = \''.(int)($post_iscomments).'\',
							   `is_fbcomments` = \''.(int)($post_isfbcomments).'\',
							   `ids_shops` = "'.pSQL($ids_shops).'",
							   `related_products` = "'.pSQL($related_products).'",
							   `related_posts` = "'.pSQL($related_posts).'",
							   `ids_groups` = "'.pSQL($group_association).'",
							   `after_time_add` = \''.(int)($after_time_add).'\',
							   `publish_date` = "'.pSQL($publish_date).'",
							   `is_rss` = \''.(int)($is_rss).'\',
							   `author` = "'.pSQL($author).'",
							   `author_id` = '.(int)($id_customer).',
							   `time_add` = "'.pSQL($time_add).'"
							   ';
        Db::getInstance()->Execute($sql);

        $post_id = Db::getInstance()->Insert_ID();


        ## set  position ##

        $position_id = $post_id-1;

        Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'blog_post
							SET position = '. (int)($position_id) .' WHERE id = ' . (int)($post_id));
        ## set  position ##



        ## add post data ##
        $id_lang = $data['id_lang'];

        $post_title = $data['post_title'];
        $post_seokeywords = '';
        $post_seodescription = '';
        $post_content = $data['post_content'];


        $tags = '';


        $seo_url = $this->_translit($post_title);


        $sql = 'SELECT count(*) as count
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE seo_url = "'.pSQL($seo_url,true).'" AND id_lang = '.(int)$id_lang;
        $data_seo_url = Db::getInstance()->GetRow($sql);

        if($data_seo_url['count'] != 0)
            $seo_url = $seo_url."-".Tools::strtolower(Tools::passwdGen(6));


        $sql = 'INSERT into `'._DB_PREFIX_.'blog_post_data` SET
							   `id_item` = \''.(int)($post_id).'\',
							   `id_lang` = \''.(int)($id_lang).'\',
							   `title` = \''.pSQL($post_title).'\',
							   `seo_keywords` = \''.pSQL($post_seokeywords).'\',
							   `seo_description` = \''.pSQL($post_seodescription).'\',
							   `content` = "'.pSQL($post_content,true).'",
							   `tags` = "'.pSQL($tags).'",
							   `seo_url` = "'.pSQL($seo_url).'"
							   ';



        Db::getInstance()->Execute($sql);
        ## add post data ##


        include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
        $obj = new blockblog();

        if($obj->is_demo == 0) {


            $error_data = $this->saveImage(array('post_id' => $post_id));

            $error = $error_data['error'];
            $error_text = $error_data['error_text'];
            $is_exists = $error_data['is_exists'];


        } else {
            $error = 0;
            $error_text = '';
            $is_exists = false;
        }


        // loyality program //
        include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        $loyality->saveLoyalityAction(array('id_loyalty_status' => 0, 'id_customer' => $id_customer, 'id_post'=>$post_id, 'type' => 'loyality_add_blog_post'));
        // loyality program //


        if ($is_exists) {
            // loyality program //
            $loyality->saveLoyalityAction(array('id_loyalty_status' => 0, 'id_customer' => $id_customer,'id_post'=>$post_id,  'type' => 'loyality_add_blog_post_logo'));
            // loyality program //
        }

        // need to create record in the table blog_avatar2customer
        if($id_customer !== NULL){


            $query = 'SELECT * from '._DB_PREFIX_.'blog_avatar2customer
												WHERE id_customer = '.(int)$id_customer.' AND id_shop = '.(int)$this->_id_shop.'';

            $_info = Db::getInstance()->GetRow($query);

            $avatar = $_info['avatar_thumb'];
            //$show_my_profile = $_info['is_show'];
            $show_my_profile = 1;

            // if exist record
            $query = 'SELECT COUNT(*) as count from '._DB_PREFIX_.'blog_avatar2customer
												WHERE id_customer = '.(int)$id_customer.' AND id_shop = '.(int)$this->_id_shop.'';

            $result = Db::getInstance()->GetRow($query);
            $exist_record = $result['count'];

            if($exist_record){
                //update
                $query = 'UPDATE '._DB_PREFIX_.'blog_avatar2customer SET avatar_thumb = "'.pSQL($avatar).'"
                            WHERE id_customer = '.(int)$id_customer;
            } else {
                // insert
                $query = 'INSERT INTO '._DB_PREFIX_.'blog_avatar2customer (id_customer, avatar_thumb,is_show,id_shop)
                             VALUES ('.(int)$id_customer.', "'.pSQL($avatar).'", '.(int)$show_my_profile.','.(int)$this->_id_shop.') ';
            }

            Db::getInstance()->Execute($query);
        }
        // need to create record in the table blog_avatar2customer

        if(count($ids_categories)>0) {
            foreach ($ids_categories as $id_cat) {


                $sql = 'INSERT into `' . _DB_PREFIX_ . 'blog_category2post` SET
							   `category_id` = \'' . pSQL($id_cat) . '\',
							   `post_id` = \'' . pSQL($post_id) . '\'
							   ';

                Db::getInstance()->Execute($sql);

            }
        }

        $this->sendNotificationSuggestionToAdminWhenCustomerAddPost(
            array(
                'id'=>$post_id
            )
        );

        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //

        return array('error'=>$error,'error_text'=>$error_text,'post_id'=>$post_id);


    }



    public function sendNotificationSuggestionToAdminWhenCustomerAddPost($data = null){

        if(Configuration::get($this->_name.'noti') == 1 && Configuration::get($this->_name.'is_addblogpostcust') == 1){

            include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
            $obj = new blockblog();
            $_data_translate = $obj->translateItems();

            $id_post = $data['id'];


            ### get post info ###

            $_info_post = $this->getPostItem(array('id' => $id_post));

            $cookie = $this->context->cookie;
            $id_lang = (int)$cookie->id_lang;


            $title_post = isset($_info_post['post']['data'][$id_lang]['title'])?$_info_post['post']['data'][$id_lang]['title']:'';
            $img = isset($_info_post['post'][0]['img'])?$_info_post['post'][0]['img']:'';
            if(Tools::strlen($img)>0) {
                $img = $this->getHttpost().$obj->getCloudImgPath().$img;
            }

            $seo_url =  isset($_info_post['post']['data'][$id_lang]['seo_url'])?$_info_post['post']['data'][$id_lang]['seo_url']:'';
            $data_url = $this->getSEOURLs();
            $post_url = $data_url['post_url'];

            if($this->isURLRewriting()) {
                $com_url = $post_url . $seo_url;
            } else {
                $com_url = $post_url.'&post_id='.$id_post;
            }
            ### get post info ###


            $name = isset($_info_post['post'][0]['customer_name']) ? $_info_post['post'][0]['customer_name'] :'admin' ;
            $text = isset($_info_post['post']['data'][$id_lang]['content'])?strip_tags($_info_post['post']['data'][$id_lang]['content']):'';
            $text = Tools::substr($text,0,100);
            if(Tools::strlen($text)>100){
                $text .= $text."...";
            }
            $email = isset($_info_post['post'][0]['customer_email']) ? $_info_post['post'][0]['customer_email'] :'' ;

            $time_add = isset($_info_post['post'][0]['time_add']) ? $_info_post['post'][0]['time_add'] :date("Y-m-d H:i:s") ;


            ## get categories ##
            $sql = '
			SELECT pc.category_id, pc.post_id
			FROM `'._DB_PREFIX_.'blog_category2post` pc LEFT JOIN  `'._DB_PREFIX_.'blog_category` pc2  ON(pc2.id = pc.category_id)
			WHERE pc.`post_id` = '.(int)$id_post.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc2.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc2.ids_groups)

			';

            $category_ids = Db::getInstance()->ExecuteS($sql);

            $data_category_ids = array();
            foreach($category_ids as $k => $v){
                $data_category_ids[] = $v['category_id'];
            }

            $ids_cat = $data_category_ids;
            $category_data = array();
            foreach($ids_cat as $k => $cat_id){
                $_info_ids = $this->getCategoryItem(array('id' => $cat_id));


                $category0 = isset($_info_ids['category'][0])?$_info_ids['category'][0]:array();


                if(sizeof($category0)>0) {

                    if(empty($_info_ids['category'][0]['title']))
                        continue;


                    $category_data[] = $_info_ids['category'][0]['title'];
                }
            }

            $category_data_to_post = '';

            if(count($category_data)>0){
                $category_data_to_post = implode(", ",$category_data);
                $category_data_to_post = $_data_translate['selected_categories_for_email'].' : '.$category_data_to_post;
            }

            ### get categories ##




            /* Email generation */
            $templateVars = array(
                '{name}' => $name,
                '{email}'=>$email,
                '{text}' => Tools::stripslashes($text),
                '{title_post}' => $title_post,
                '{post_url}' => $com_url,
                '{picture}'=>$img,
                '{time_add}'=>$time_add,
                '{categories}'=>$category_data_to_post,
            );


            /* Email sending */

            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            }
            else {
                $id_lang_current = Language::getIdByIso('en');
            }

            $subject = Configuration::get($this->_name . 'addblogpostcust_' . $id_lang_current);

            //echo "<pre>";var_dump($templateVars);var_dump($id_lang_current); var_dump($subject);exit;

            Mail::Send($id_lang_current, 'admin-customer-addblogpost', $subject, $templateVars,
                Configuration::get($this->_name.'mail'), 'Response Form', NULL, NULL,
                NULL, NULL, dirname(__FILE__).'/../mails/');



        }

    }

    public function updatePostByCustomer($data){

        $id_editposts = $data['id_editposts'];

        $ids_categories = $data['ids_categories'];
        $post_status = $data['post_status'];
        $post_images = $data['post_images'];

        include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
        $obj = new blockblog();


        ### get old data ##

        $_info_post = $this->getPostItem(array('id' => $id_editposts));
        $cookie = $this->context->cookie;
        $id_lang = (int)$cookie->id_lang;
        $title_post_old = isset($_info_post['post']['data'][$id_lang]['title'])?$_info_post['post']['data'][$id_lang]['title']:'';
        $text_old = isset($_info_post['post']['data'][$id_lang]['content'])?strip_tags($_info_post['post']['data'][$id_lang]['content']):'';
        $author_id = isset($_info_post['post'][0]['author_id'])?strip_tags($_info_post['post'][0]['author_id']):0;
        $text_old = Tools::substr($text_old,0,100);
        if(Tools::strlen($text_old)>100){
            $text_old .= $text_old."...";
        }


        ## get categories ##
        $sql = '
			SELECT pc.category_id, pc.post_id
			FROM `'._DB_PREFIX_.'blog_category2post` pc LEFT JOIN  `'._DB_PREFIX_.'blog_category` pc2  ON(pc2.id = pc.category_id)
			WHERE pc.`post_id` = '.(int)$id_editposts.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc2.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc2.ids_groups)

			';

        $category_ids = Db::getInstance()->ExecuteS($sql);

        $data_category_ids = array();
        foreach($category_ids as $k => $v){
            $data_category_ids[] = $v['category_id'];
        }

        $ids_cat = $data_category_ids;
        $category_data = array();
        foreach($ids_cat as $k => $cat_id){
            $_info_ids = $this->getCategoryItem(array('id' => $cat_id));


            $category0 = isset($_info_ids['category'][0])?$_info_ids['category'][0]:array();


            if(sizeof($category0)>0) {

                if(empty($_info_ids['category'][0]['title']))
                    continue;


                $category_data[] = $_info_ids['category'][0]['title'];
            }
        }

        $category_data_to_post_old = '';
        if(count($category_data)>0){
            $category_data_to_post_old = implode(", ",$category_data);
        }

        ### get categories ##
        ### get old data ##


        // update
        $sql = 'UPDATE `'._DB_PREFIX_.'blog_post` SET
							  `status` = \''.(int)($post_status).'\'

							    WHERE id = '.(int)$id_editposts.'
							   ';

        Db::getInstance()->Execute($sql);
        // update

        $id_lang = $data['id_lang'];
        $post_title = $data['post_title'];
        $post_content = $data['post_content'];

        $sql = 'UPDATE `'._DB_PREFIX_.'blog_post_data` SET
							  `title` = \''.pSQL($post_title).'\',
							  `content` = "'.pSQL($post_content,true).'"


							    WHERE id_item = '.(int)$id_editposts.' and id_lang = '.(int)$id_lang.'
							   ';

        Db::getInstance()->Execute($sql);



        if($obj->is_demo == 0) {
            $error_data = $this->saveImage(array('post_id' => $id_editposts, 'post_images' => $post_images));
            $error = $error_data['error'];
            $error_text = $error_data['error_text'];
            $is_exists = $error_data['is_exists'];

        } else {
            $error = 0;
            $error_text = '';
            $is_exists  = false;
        }



        if ($is_exists) {
            // loyality program //
            include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
            $loyality = new loyalityblog();
            $loyality->saveLoyalityAction(array('id_loyalty_status' => 0, 'id_customer' => $author_id,'id_post'=>$id_editposts,  'type' => 'loyality_add_blog_post_logo'));
            // loyality program //
        }

        $sql = 'DELETE FROM `'._DB_PREFIX_.'blog_category2post`
					   WHERE `post_id` = \''.(int)($id_editposts).'\'';
        Db::getInstance()->Execute($sql);


        if($ids_categories) {


            foreach ($ids_categories as $id_cat) {
                $sql = 'INSERT into `' . _DB_PREFIX_ . 'blog_category2post` SET
							   `category_id` = \'' . (int)($id_cat) . '\',
							   `post_id` = \'' . (int)($id_editposts) . '\'
							   ';
                Db::getInstance()->Execute($sql);

            }
        }


        $this->sendNotificationSuggestionToAdminWhenCustomerEditPost(
            array(
                'id'=>$id_editposts,
                'title_post_old'=>$title_post_old,
                'text_old'=>$text_old,
                'category_data_to_post_old'=>$category_data_to_post_old,
            )
        );

        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //


        return array('error'=>$error,'error_text'=>$error_text);

    }


    public function sendNotificationSuggestionToAdminWhenCustomerEditPost($data = null){

        if(Configuration::get($this->_name.'noti') == 1 && Configuration::get($this->_name.'is_editblogpostcust') == 1){

            include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
            $obj = new blockblog();
            $_data_translate = $obj->translateItems();

            $id_post = $data['id'];

            $title_post_old = $data['title_post_old'];
            $text_old = $data['text_old'];
            $category_data_to_post_old = $data['category_data_to_post_old'];

            ### get post info ###

            $_info_post = $this->getPostItem(array('id' => $id_post));

            $cookie = $this->context->cookie;
            $id_lang = (int)$cookie->id_lang;


            $title_post = isset($_info_post['post']['data'][$id_lang]['title'])?$_info_post['post']['data'][$id_lang]['title']:'';
            $img = isset($_info_post['post'][0]['img'])?$_info_post['post'][0]['img']:'';
            if(Tools::strlen($img)>0) {
                $img = $this->getHttpost().$obj->getCloudImgPath().$img;
            }

            $seo_url =  isset($_info_post['post']['data'][$id_lang]['seo_url'])?$_info_post['post']['data'][$id_lang]['seo_url']:'';
            $data_url = $this->getSEOURLs();
            $post_url = $data_url['post_url'];

            if($this->isURLRewriting()) {
                $com_url = $post_url . $seo_url;
            } else {
                $com_url = $post_url.'&post_id='.$id_post;
            }
            ### get post info ###


            $name = isset($_info_post['post'][0]['customer_name']) ? $_info_post['post'][0]['customer_name'] :'admin' ;
            $text = isset($_info_post['post']['data'][$id_lang]['content'])?strip_tags($_info_post['post']['data'][$id_lang]['content']):'';
            $text = Tools::substr($text,0,100);
            if(Tools::strlen($text)>100){
                $text .= $text."...";
            }
            $email = isset($_info_post['post'][0]['customer_email']) ? $_info_post['post'][0]['customer_email'] :'' ;

            $time_add = isset($_info_post['post'][0]['time_add']) ? $_info_post['post'][0]['time_add'] :date("Y-m-d H:i:s") ;


            ## get categories ##
            $sql = '
			SELECT pc.category_id, pc.post_id
			FROM `'._DB_PREFIX_.'blog_category2post` pc LEFT JOIN  `'._DB_PREFIX_.'blog_category` pc2  ON(pc2.id = pc.category_id)
			WHERE pc.`post_id` = '.(int)$id_post.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc2.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc2.ids_groups)

			';

            $category_ids = Db::getInstance()->ExecuteS($sql);

            $data_category_ids = array();
            foreach($category_ids as $k => $v){
                $data_category_ids[] = $v['category_id'];
            }

            $ids_cat = $data_category_ids;
            $category_data = array();
            foreach($ids_cat as $k => $cat_id){
                $_info_ids = $this->getCategoryItem(array('id' => $cat_id));


                $category0 = isset($_info_ids['category'][0])?$_info_ids['category'][0]:array();


                if(sizeof($category0)>0) {

                    if(empty($_info_ids['category'][0]['title']))
                        continue;


                    $category_data[] = $_info_ids['category'][0]['title'];
                }
            }

            $category_data_to_post = '';
            $category_data_to_post_orig = '';
            if(count($category_data)>0){
                $category_data_to_post = implode(", ",$category_data);
                $category_data_to_post_orig =$category_data_to_post;
                $category_data_to_post = $_data_translate['selected_categories_for_email'].' : '.$category_data_to_post;
            }

            ### get categories ##




            $title_post_old_txt = '';
            if($title_post != $title_post_old) {
                $title_post_old_txt .= '<br /><br /><span style="color:#333"><strong>'.$_data_translate['old_blog_post_title'].' :</strong></span> ' . $title_post_old . '';
            }

            $text_old_txt = '';
            if($text != $text_old) {
                $text_old_txt .= '<br /><br /><span style="color:#333"><strong>'.$_data_translate['old_blog_post_content'].' :</strong></span> ' . $text_old . '';
            }

            $category_data_to_post_old_txt = '';
            if($category_data_to_post_orig != $category_data_to_post_old && Tools::strlen($category_data_to_post_old)>0) {
                $category_data_to_post_old_txt .= '<br /><br /><span style="color:#333"><strong>'.$_data_translate['old_selected_categories'].' :</strong></span> <img src="' . $category_data_to_post_old . '" />';
            }



            /* Email generation */
            $templateVars = array(
                '{name}' => $name,
                '{email}'=>$email,
                '{text}' => Tools::stripslashes($text).$text_old_txt,
                '{title_post}' => $title_post.$title_post_old_txt,
                '{post_url}' => $com_url,
                '{picture}'=>$img,
                '{time_add}'=>$time_add,
                '{categories}'=>$category_data_to_post.$category_data_to_post_old_txt,
            );


            /* Email sending */

            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            }
            else {
                $id_lang_current = Language::getIdByIso('en');
            }

            $subject = Configuration::get($this->_name . 'editblogpostcust_' . $id_lang_current);


            Mail::Send($id_lang_current, 'admin-customer-editblogpost', $subject, $templateVars,
                Configuration::get($this->_name.'mail'), 'Response Form', NULL, NULL,
                NULL, NULL, dirname(__FILE__).'/../mails/');



        }

    }
	
	public function updatePost($data){



        $cat_shop_association = $data['cat_shop_association']?$data['cat_shop_association']:array();
        $ids_shops = implode(",",$cat_shop_association);

        $group_association = implode(",",$data['group_association']);

        $related_posts = $data['ids_related_posts']?$data['ids_related_posts']:array();

        $related_posts = implode(",",$related_posts);
        if(Tools::strlen($related_posts)==0) $related_posts = 0;

        $related_products = $data['related_products']?$data['related_products']:'';
        $related_products = explode("-",$related_products,-1);

	    $related_products = implode(",",$related_products);
		if(Tools::strlen($related_products)==0) $related_products = 0;
	    
		$time_add = date('Y-m-d H:i:s',strtotime($data['time_add']));
		
		$id_editposts = $data['id_editposts'];
		
		$ids_categories = $data['ids_categories'];
		$post_status = $data['post_status'];
		$post_iscomments = $data['post_iscomments'];
		$post_images = $data['post_images'];

        $post_isfbcomments = $data['post_isfbcomments'];

        $after_time_add = $data['after_time_add'];


        if($after_time_add) {
            $publish_date = $data['publish_date'];
        } else {
            //$publish_date = '0000-00-00 00:00:00';
            $publish_date = $data['publish_date'];
        }
        $is_rss = $data['is_rss'];

        $author = $data['author'];

        $info = $data['info']?$data['info']:'';


        $_data_post_before_update = $this->getPostItem(array('id'=>$id_editposts));
        $status_post_before_update = isset($_data_post_before_update['post'][0]['status']) ? $_data_post_before_update['post'][0]['status'] :0 ;
        $related_products_array = isset($_data_post_before_update['post'][0]['related_products']) ? explode(",",$_data_post_before_update['post'][0]['related_products']) :0 ;


        $is_slider = $data['is_slider'];

        /*$count_views = $data['count_views'];
        $count_likes = $data['count_likes'];


        if($count_likes !== NULL){

        }*/


        // update
		$sql = 'UPDATE `'._DB_PREFIX_.'blog_post` SET
							  `status` = \''.(int)($post_status).'\',
							   `author` = "'.pSQL($author).'",
							   `is_comments` = \''.(int)($post_iscomments).'\',
							   `is_fbcomments` = \''.(int)($post_isfbcomments).'\',
							   `ids_shops` = "'.pSQL($ids_shops).'",
							   `related_products` = "'.pSQL($related_products).'",
							   `related_posts` = "'.pSQL($related_posts).'",
							   `ids_groups` = "'.pSQL($group_association).'",
							   `after_time_add` = \''.(int)($after_time_add).'\',
							   `publish_date` = "'.pSQL($publish_date).'",

							   `is_rss` = \''.(int)($is_rss).'\',
							   `is_slider` = \''.(int)($is_slider).'\',

							   `time_add` = "'.pSQL($time_add).'"
							    WHERE id = '.(int)$id_editposts.'
							   ';
	
	 	Db::getInstance()->Execute($sql);
		
		/// delete tabs data
		$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_post_data` WHERE id_item = '.(int)$id_editposts.'';
		Db::getInstance()->Execute($sql);
		
		foreach($data['data_title_content_lang'] as $language => $item){
		
		$post_title = $item['post_title'];
		$post_seokeywords = $item['post_seokeywords'];
		$post_seodescription = $item['post_seodescription'];
		$post_content = $item['post_content'];

        $tags = $item['tags'];
        $tags = explode(",",$tags);
        $tags = array_unique($tags);
        $tags = implode(",",$tags);


		$seo_url_pre = Tools::strlen($item['seo_url'])>0?$item['seo_url']:$post_title;
	    $seo_url = $this->_translit($seo_url_pre);
	    
	    $sql = 'SELECT count(*) as count
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE seo_url = "'.pSQL($seo_url,true).'" AND id_lang = '.(int)$language;
		$data_seo_url = Db::getInstance()->GetRow($sql);
		
		if($data_seo_url['count'] != 0)
			$seo_url = $seo_url."-".Tools::strtolower(Tools::passwdGen(6));
		
		$sql = 'INSERT into `'._DB_PREFIX_.'blog_post_data` SET
							   `id_item` = \''.(int)($id_editposts).'\',
							   `id_lang` = \''.(int)($language).'\',
							   `title` = \''.pSQL($post_title).'\',
							   `seo_keywords` = \''.pSQL($post_seokeywords).'\',
							   `seo_description` = \''.pSQL($post_seodescription).'\',
							   `content` = "'.pSQL($post_content,true).'",
							   `tags` = \''.pSQL($tags).'\',
							   `seo_url` = \''.pSQL($seo_url).'\'

							   ';
			Db::getInstance()->Execute($sql);
		
		}

        // loyality program //
        include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        // loyality program //

        include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
        $obj = new blockblog();
        if($obj->is_demo == 0) {
            $error_data = $this->saveImage(array('post_id' => $id_editposts, 'post_images' => $post_images));
            $error = $error_data['error'];
            $error_text = $error_data['error_text'];
            $is_exists = $error_data['is_exists'];

            $id_customer = $data['id_customer'];
            $post_images_ava = $data['post_images_ava'];
            $data_avatar = $this->saveImageAvatar(array('id' => $id_editposts, 'post_images' => $post_images_ava,'id_customer'=>$id_customer));

            $is_exists_avatar = $data_avatar['is_exists'];
            if($is_exists_avatar){
                // loyality program //
                $loyality->saveLoyalityAction(array('id_loyalty_status' => 1, 'id_customer' => $id_customer, 'type' => 'loyality_user_my_show'));
                // loyality program //
            }


            if(in_array($post_status,array(0,1))){
                // loyality program //
                if($is_exists){
                    $loyality->saveLoyalityAction(array('id_loyalty_status' => $post_status, 'id_customer' => $id_customer,'id_post'=>$id_editposts,  'type' => 'loyality_add_blog_post_logo'));
                } else {
                    $loyality->setLoyalityStatus(array('id_loyalty_status' => $post_status, 'id_customer' => $id_customer, 'id_post' => $id_editposts, 'type' => 'loyality_add_blog_post_logo'));
                }
                // loyality program //
            }

        } else {
            $error = 0;
            $error_text = '';
        }



        if(in_array($post_status,array(0,1))){
            // loyality program //
            $loyality->setLoyalityStatus(array('id_loyalty_status' => $post_status, 'id_customer' => $id_customer, 'id_post' => $id_editposts, 'type' => 'loyality_add_blog_post'));
            // loyality program //
        }
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_category2post`
					   WHERE `post_id` = \''.(int)($id_editposts).'\'';
		Db::getInstance()->Execute($sql);

        if($ids_categories) {
            foreach ($ids_categories as $id_cat) {
                $sql = 'INSERT into `' . _DB_PREFIX_ . 'blog_category2post` SET
							   `category_id` = \'' . (int)($id_cat) . '\',
							   `post_id` = \'' . (int)($id_editposts) . '\'
							   ';
                Db::getInstance()->Execute($sql);

            }
        }


        // author info //
        include_once(_PS_MODULE_DIR_.$this->_name . '/classes/userprofileblockblog.class.php');
        $obj_userprofileblockblog = new userprofileblockblog();
        $obj_userprofileblockblog->updateDataAuthor(array('id_customer'=>$id_customer,'info'=>$info));
        // author info //


        // change status of the post send email to admin
        if(in_array($status_post_before_update,array(3))){

            $this->sendNotificationToCustomerWhenEditedPostApprovedByAdmin(array('id'=>$id_editposts,'status_post_before_update'=>$status_post_before_update));
        }

        if(in_array($status_post_before_update,array(4))){

            $this->sendNotificationToCustomerWhenAddedPostApprovedByAdmin(array('id'=>$id_editposts,'status_post_before_update'=>$status_post_before_update));

            ### posts API ###
            $data_api = array(
                'post_id'=>$id_editposts,
            );
            $this->postToWallPosts($data_api);
            ### posts API ###

        }
        // change status of the post send email to admin


        // clear cache only for blog //
        $this->clearSmartyCacheBlog(array('id_products'=>$related_products_array));
        // clear cache only for blog //

        return array('error'=>$error,'error_text'=>$error_text);

	}


    public function sendNotificationToCustomerWhenAddedPostApprovedByAdmin($data = null){
        if(Configuration::get($this->_name.'noti') == 1 && Configuration::get($this->_name.'is_addblogposttocust') == 1){
            $this->sendNotificationToCustomerWhenAddedEditedPostApprovedByAdmin($data);
        }
    }

    public function sendNotificationToCustomerWhenEditedPostApprovedByAdmin($data = null){

        if(Configuration::get($this->_name.'noti') == 1 && Configuration::get($this->_name.'is_editblogposttocust') == 1){

            $this->sendNotificationToCustomerWhenAddedEditedPostApprovedByAdmin($data);



        }
    }

    public function sendNotificationToCustomerWhenAddedEditedPostApprovedByAdmin($data = null){

            $status_post_before_update = $data['status_post_before_update'];

            include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
            $obj = new blockblog();
            $_data_translate = $obj->translateItems();

            $id_post = $data['id'];

            ### get post info ###

            $_info_post = $this->getPostItem(array('id' => $id_post));

            $cookie = $this->context->cookie;
            $id_lang = (int)$cookie->id_lang;


            $title_post = isset($_info_post['post']['data'][$id_lang]['title'])?$_info_post['post']['data'][$id_lang]['title']:'';
            $img = isset($_info_post['post'][0]['img'])?$_info_post['post'][0]['img']:'';
            if(Tools::strlen($img)>0) {
                $img = $this->getHttpost().$obj->getCloudImgPath().$img;
            }

            $seo_url =  isset($_info_post['post']['data'][$id_lang]['seo_url'])?$_info_post['post']['data'][$id_lang]['seo_url']:'';
            $data_url = $this->getSEOURLs();
            $post_url = $data_url['post_url'];

            if($this->isURLRewriting()) {
                $com_url = $post_url . $seo_url;
            } else {
                $com_url = $post_url.'&post_id='.$id_post;
            }
            ### get post info ###


            $name = isset($_info_post['post'][0]['customer_name']) ? $_info_post['post'][0]['customer_name'] :'admin' ;
            $text = isset($_info_post['post']['data'][$id_lang]['content'])?strip_tags($_info_post['post']['data'][$id_lang]['content']):'';
            $text = Tools::substr($text,0,100);
            if(Tools::strlen($text)>100){
                $text .= $text."...";
            }
            $email = isset($_info_post['post'][0]['customer_email']) ? $_info_post['post'][0]['customer_email'] :'' ;

            $time_add = isset($_info_post['post'][0]['time_add']) ? $_info_post['post'][0]['time_add'] :date("Y-m-d H:i:s") ;



            ## get categories ##
            $sql = '
			SELECT pc.category_id, pc.post_id
			FROM `'._DB_PREFIX_.'blog_category2post` pc LEFT JOIN  `'._DB_PREFIX_.'blog_category` pc2  ON(pc2.id = pc.category_id)
			WHERE pc.`post_id` = '.(int)$id_post.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc2.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc2.ids_groups)

			';

            $category_ids = Db::getInstance()->ExecuteS($sql);

            $data_category_ids = array();
            foreach($category_ids as $k => $v){
                $data_category_ids[] = $v['category_id'];
            }

            $ids_cat = $data_category_ids;
            $category_data = array();
            foreach($ids_cat as $k => $cat_id){
                $_info_ids = $this->getCategoryItem(array('id' => $cat_id));


                $category0 = isset($_info_ids['category'][0])?$_info_ids['category'][0]:array();


                if(sizeof($category0)>0) {

                    if(empty($_info_ids['category'][0]['title']))
                        continue;


                    $category_data[] = $_info_ids['category'][0]['title'];
                }
            }

            $category_data_to_post = '';
            if(count($category_data)>0){
                $category_data_to_post = implode(", ",$category_data);
                $category_data_to_post = $_data_translate['selected_categories_for_email'].' : '.$category_data_to_post;
            }

            ### get categories ##

            /* Email generation */
            $templateVars = array(
                '{name}' => $name,
                '{email}'=>$email,
                '{text}' => Tools::stripslashes($text),
                '{title_post}' => $title_post,
                '{post_url}' => $com_url,
                '{picture}'=>$img,
                '{time_add}'=>$time_add,
                '{categories}'=>$category_data_to_post,
            );


            /* Email sending */

            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            }
            else {
                $id_lang_current = Language::getIdByIso('en');
            }

            if($status_post_before_update == 3) {
                $subject = Configuration::get($this->_name . 'editblogposttocust_' . $id_lang_current);
                $template = 'customer-approveeditblogpost';
            } elseif($status_post_before_update == 4){
                $subject = Configuration::get($this->_name . 'addblogposttocust_' . $id_lang_current);
                $template = 'customer-approveaddblogpost';
            }



        if(!empty($template) && !empty($subject)) {
            Mail::Send($id_lang_current, $template, $subject, $templateVars,
                $email, 'Response Form', NULL, NULL,
                NULL, NULL, dirname(__FILE__) . '/../mails/');
        }



    }
	
	public function saveImage($data = null){
		
		$error = 0;
		$error_text = '';
		
		$post_id = $data['post_id'];
		$post_images = isset($data['post_images'])?$data['post_images']:'';
        $is_category = isset($data['is_category'])?$data['is_category']:0;
        $is_gallery = isset($data['is_gallery'])?$data['is_gallery']:0;
        $is_exists = false;



		$files = $_FILES['post_image'];
		############### files ###############################
		if(!empty($files['name']))
			{
		      if(!$files['error'])
		      {
				  $type_one = $files['type'];
				  $ext = explode("/",$type_one);
				  
				  if(strpos('_'.$type_one,'image')<1)
				  {
				  	$error_text = $this->l('Invalid image file type, please try again! Allow formats *.jpg; *.jpeg; *.png; *.gif.');
				  	$error = 1;

				  }elseif(!in_array($ext[1],array('png','x-png','gif','jpg','jpeg','pjpeg'))){
				  	$error_text = $this->l('Wrong file format, please try again!');
				  	$error = 1;
				  	
				  } else {

                      $cookie = $this->context->cookie;
                      $current_language = (int)$cookie->id_lang;



                      if($is_category) {
                          $_info_cat = $this->getCategoryItem(array('id' => $post_id, 'admin' => 1));
                          $post_item = $_info_cat['category'][0];

                          $img_post = $post_item['img'];
                          $seo_url = "cat-" . $_info_cat['category']['data'][$current_language]['seo_url'];


                      }elseif($is_gallery){

                          $_data = $this->getGalleryItem(array('id'=>$post_id));
                          $img_post = isset($_data['gallery'][0]['img']) ? $_data['gallery'][0]['img'] :'' ;
                          $id_galley_post = isset($_data['gallery'][0]['id']) ? $_data['gallery'][0]['id'] :0 ;
                          $img_title = isset($_data['gallery']['data'][$current_language]['title']) ? $_data['gallery']['data'][$current_language]['title'] :'' ;


                          $seo_url = $this->_translit($img_title).'-'.$id_galley_post;



                      } else {
                          $info_post = $this->getPostItem(array('id'=>$post_id));

                          $post_item = isset($info_post['post'][0])?$info_post['post'][0]:null;
                          $img_post = isset($post_item['img'])?$post_item['img']:'';
                          $seo_url = isset($info_post['post']['data'][$current_language]['seo_url'])?$info_post['post']['data'][$current_language]['seo_url']:null;
                      }



				  		if(Tools::strlen($img_post)>0){
				  			
				  			// delete old avatars

				  			$name_thumb = current(explode(".",$img_post));

                            if($is_gallery){
                                unlink($this->path_img_cloud .$this->_img_gallery_folder.DIRECTORY_SEPARATOR.  $name_thumb . ".jpg");
                            } else {
                                unlink($this->path_img_cloud .  $name_thumb . ".jpg");
                            }


                            ## for post ##
                            if($is_category) {

                                $posts_block_img = $this->path_img_cloud . $name_thumb . '-' . $this->_img_category_page . 'x' . $this->_img_category_page . '.jpg';
                                @unlink($posts_block_img);

                                $lists_img = $this->path_img_cloud . $name_thumb . '-' . $this->_img_category_list . 'x' . $this->_img_category_list . '.jpg';
                                @unlink($lists_img);

                            }elseif($is_gallery){


                                $lists_img = $this->path_img_cloud . $this->_img_gallery_folder.DIRECTORY_SEPARATOR. $name_thumb . '-' . $this->_img_gallery_width . 'x' . $this->_img_gallery_height . '.jpg';
                                @unlink($lists_img);

                                $lists_img = $this->path_img_cloud . $this->_img_gallery_folder.DIRECTORY_SEPARATOR. $name_thumb . '-' . $this->_img_gallery_width_block . 'x' . $this->_img_gallery_width_block . '.jpg';
                                @unlink($lists_img);

                                $lists_img = $this->path_img_cloud . $this->_img_gallery_folder.DIRECTORY_SEPARATOR. $name_thumb . '-' . $this->_img_gallery_width_block_home . 'x' . $this->_img_gallery_width_block_home . '.jpg';
                                @unlink($lists_img);

                            } else {

                                $posts_block_img =  $this->path_img_cloud . $name_thumb . '-' . Configuration::get($this->_name . 'posts_block_img_width') . 'x' . Configuration::get($this->_name . 'posts_block_img_width') . '.jpg';
                                @unlink($posts_block_img);

                                $lists_img =  $this->path_img_cloud . $name_thumb . '-' . Configuration::get($this->_name . 'lists_img_width') . 'x' . Configuration::get($this->_name . 'lists_img_height') . '.jpg';
                                @unlink($lists_img);

                                $post_img =  $this->path_img_cloud . $name_thumb . '-' . Configuration::get($this->_name . 'post_img_width') . 'x' . Configuration::get($this->_name . 'post_img_width') . '.jpg';
                                @unlink($post_img);


                            }
                            ## for post ##
						
				  			
				  		}

					  	srand((double)microtime()*1000000);
					 	$uniq_name_image = isset($seo_url)?$seo_url:uniqid(rand());
					 	$type_one = Tools::substr($type_one,6,Tools::strlen($type_one)-6);
					 	$filename = $uniq_name_image.'.'.$type_one;


                          if($is_gallery){

                              move_uploaded_file($files['tmp_name'], $this->path_img_cloud . $this->_img_gallery_folder . DIRECTORY_SEPARATOR . $filename);


                              $name_img = $this->path_img_cloud . $this->_img_gallery_folder . DIRECTORY_SEPARATOR . $filename;
                              $dir_without_ext = $this->path_img_cloud . $this->_img_gallery_folder . DIRECTORY_SEPARATOR . $uniq_name_image;

                          } else {
                              move_uploaded_file($files['tmp_name'], $this->path_img_cloud . $filename);


                              $name_img = $this->path_img_cloud . $filename;
                              $dir_without_ext = $this->path_img_cloud . $uniq_name_image;
                          }

						$this->copyImage(array('dir_without_ext'=>$dir_without_ext,
												'name'=>$name_img)
										);




                      if($is_category) {

                          // Image in lists category page
                          $this->copyImage(array('dir_without_ext' => $dir_without_ext,
                                  'name' => $name_img,
                                  'width' => $this->_img_category_list,
                                  'height' => $this->_img_category_list
                              )
                          );
                          // Image in lists category page

                          // Image on category page
                          $this->copyImage(array('dir_without_ext' => $dir_without_ext,
                                  'name' => $name_img,
                                  'width' => $this->_img_category_page,
                                  'height' => $this->_img_category_page
                              )
                          );
                          // Image on category page

                      }elseif($is_gallery){

                          $this->copyImage(array('dir_without_ext' => $dir_without_ext,
                                  'name' => $name_img,
                                  'width' => $this->_img_gallery_width,
                                  'height' => $this->_img_gallery_height
                              )
                          );

                          $this->copyImage(array('dir_without_ext' => $dir_without_ext,
                                  'name' => $name_img,
                                  'width' => $this->_img_gallery_width_block,
                                  'height' => $this->_img_gallery_width_block
                              )
                          );


                          $this->copyImage(array('dir_without_ext' => $dir_without_ext,
                                  'name' => $name_img,
                                  'width' => $this->_img_gallery_width_block_home,
                                  'height' => $this->_img_gallery_width_block_home
                              )
                          );

                      } else {


                          ## for post ##

                          //Image in the block "Blog Posts recents"
                          $this->copyImage(array('dir_without_ext' => $dir_without_ext,
                                  'name' => $name_img,
                                  'width' => Configuration::get($this->_name . 'posts_block_img_width'),
                                  'height' => Configuration::get($this->_name . 'posts_block_img_width')
                              )
                          );
                          // Image in the block "Blog Posts recents"

                          // Image in lists posts
                          $this->copyImage(array('dir_without_ext' => $dir_without_ext,
                                  'name' => $name_img,
                                  'width' => Configuration::get($this->_name . 'lists_img_width'),
                                  'height' => Configuration::get($this->_name . 'lists_img_height')
                              )
                          );
                          // Image in lists posts

                          // Image on post page
                          $this->copyImage(array('dir_without_ext' => $dir_without_ext,
                                  'name' => $name_img,
                                  'width' => Configuration::get($this->_name . 'post_img_width'),
                                  'height' => Configuration::get($this->_name . 'post_img_width')
                              )
                          );
                          // Image on post page


                          $this->copyImage(array('dir_without_ext' => $dir_without_ext,
                                  'name' => $name_img,
                                  'width' => $this->_img_slider_width,
                                  'height' => $this->_img_slider_height
                              )
                          );

                          $this->copyImage(array('dir_without_ext' => $dir_without_ext,
                                  'name' => $name_img,
                                  'width' => $this->_img_slider_width_mobile,
                                  'height' => $this->_img_slider_height_mobile
                              )
                          );

                          ## for post ##

                      }


                      if($is_gallery){
                          // delete original image
                          @unlink($this->path_img_cloud .$this->_img_gallery_folder. DIRECTORY_SEPARATOR. $uniq_name_image . "." . $ext[1]);


                          $img_return = $uniq_name_image . '.jpg';

                      } else {

                          // delete original image
                          @unlink($this->path_img_cloud . $uniq_name_image . "." . $ext[1]);


                          $img_return = $uniq_name_image . '.jpg';
                      }

                      $is_exists = true;
                        $this->_updateImgToPost(array('post_id' => $post_id,'img' => $img_return, 'is_category'=>$is_category, 'is_gallery'=>$is_gallery));


				  }
				}
				else
					{
					### check  for errors ####
			      	switch($files['error'])
						{
                            case '1':
                                $upload_max_filesize = ini_get('upload_max_filesize');
                                // convert to bytes
                                preg_match('/[0-9]+/', $upload_max_filesize, $match);
                                $upload_max_filesize_in_bytes = $match[0] * 1024 * 1024;
                                // convert to bytes
                                $error_text = $this->l('The size of the uploaded file exceeds the').$upload_max_filesize_in_bytes.'b';
                            break;
							case '2':
								$error_text = $this->l('The size of  the uploaded file exceeds the specified parameter  MAX_FILE_SIZE in HTML form.');
								break;
							case '3':
								$error_text = $this->l('Loaded only a portion of the file');
								break;
							case '4':
								$error_text = $this->l('The file was not loaded (in the form user pointed the wrong path  to the file). ');
								break;
							case '6':
								$error_text = $this->l('Invalid  temporary directory.');
								break;
							case '7':
								$error_text = $this->l('Error writing file to disk');
								break;
							case '8':
								$error_text = $this->l('File download aborted');
								break;
							case '999':
							default:
								$error_text = $this->l('Unknown error code!');
							break;
						}
						$error = 1;
			      	########
					   
					}
			}  else {

				if($post_images != "on"){
                        $this->_updateImgToPost(array('post_id' => $post_id,'img' => "",  'is_category'=>$is_category));
				}
			}
			
		return array('error' => $error,
					 'error_text' => $error_text, 'is_exists'=>$is_exists);
	
	
	}



	public function deleteImg($data = null){
		$id = $data['id'];

        $cookie = $this->context->cookie;
        $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;

        if($id_customer>0) {
            // loyality program //
            include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
            $loyality = new loyalityblog();
            $loyality->deleteLoyalityPoints(array('id_loyalty_status' => 0, 'id_customer' => $id_customer, 'id_post' => $id, 'type' => 'loyality_add_blog_post_logo'));
            // loyality program //
        }


        $is_category = isset($data['is_category'])?$data['is_category']:0;
        $is_gallery = isset($data['is_gallery'])?$data['is_gallery']:0;


        if($is_category) {
            $_data = $this->getCategoryItem(array('id' => $id, 'admin' => 1));
            $img = $_data['category'][0]['img'];
        } elseif($is_gallery) {
            $_data = $this->getGalleryItem(array('id' => $id));
            $img = isset($_data['gallery'][0]['img']) ? $_data['gallery'][0]['img'] : '';

        } else {
            $_data = $this->getPostItem(array('id'=>$id));

            $img = isset($_data['post'][0]['img'])?$_data['post'][0]['img']:'';
            $img = str_replace('-' . Configuration::get($this->_name . 'post_img_width') . 'x' . Configuration::get($this->_name . 'post_img_width'), '', $img);
        }

		$this->_updateImgToPost(array('post_id' => $id,
				  					  'img' =>  "",
                                       'is_category'=>$is_category,
                                       'is_gallery'=>$is_gallery,
				  					 )
				  				);



        if($is_gallery){
            @unlink($this->path_img_cloud . $this->_img_gallery_folder. DIRECTORY_SEPARATOR. $img);

        } else {
            @unlink($this->path_img_cloud . $img);
        }

		
		$name_thumb = current(explode(".",$img));


        if($is_category) {

            $posts_block_img =  $this->path_img_cloud . $name_thumb . '-' . $this->_img_category_page . 'x' . $this->_img_category_page . '.jpg';
            @unlink($posts_block_img);

            $lists_img =  $this->path_img_cloud . $name_thumb . '-' . $this->_img_category_list . 'x' . $this->_img_category_list . '.jpg';
            @unlink($lists_img);

        } elseif($is_gallery) {

            $lists_img = $this->path_img_cloud . $this->_img_gallery_folder . DIRECTORY_SEPARATOR . $name_thumb . '-' . $this->_img_gallery_width . 'x' . $this->_img_gallery_height . '.jpg';
            @unlink($lists_img);

            $lists_img = $this->path_img_cloud . $this->_img_gallery_folder . DIRECTORY_SEPARATOR . $name_thumb . '-' . $this->_img_gallery_width_block . 'x' . $this->_img_gallery_width_block . '.jpg';
            @unlink($lists_img);

            $lists_img = $this->path_img_cloud . $this->_img_gallery_folder . DIRECTORY_SEPARATOR . $name_thumb . '-' . $this->_img_gallery_width_block_home . 'x' . $this->_img_gallery_width_block_home . '.jpg';
            @unlink($lists_img);
        }else {
            $posts_block_img =  $this->path_img_cloud . $name_thumb . '-' . Configuration::get($this->_name . 'posts_block_img_width') . 'x' . Configuration::get($this->_name . 'posts_block_img_width') . '.jpg';
            @unlink($posts_block_img);

            $lists_img =  $this->path_img_cloud . $name_thumb . '-' . Configuration::get($this->_name . 'lists_img_width') . 'x' . Configuration::get($this->_name . 'lists_img_height') . '.jpg';
            @unlink($lists_img);

            $post_img =  $this->path_img_cloud . $name_thumb . '-' . Configuration::get($this->_name . 'post_img_width') . 'x' . Configuration::get($this->_name . 'post_img_width') . '.jpg';
            @unlink($post_img);

            $post_img =  $this->path_img_cloud . $name_thumb . '-' . Configuration::get($this->_name . 'slider_widthfancytr') . 'x' . Configuration::get($this->_name . 'slider_heightfancytr') . '.jpg';
            @unlink($post_img);

            $post_img =  $this->path_img_cloud . $name_thumb . '-' . Configuration::get($this->_name . 'slider_widthfancytr_mobile') . 'x' . Configuration::get($this->_name . 'slider_heightfancytr_mobile') . '.jpg';
            @unlink($post_img);

            $post_img =  $this->path_img_cloud . $name_thumb . '-' . Configuration::get($this->_name . 'posts_w_h') . 'x' . Configuration::get($this->_name . 'posts_w_h') . '.jpg';
            @unlink($post_img);

        }



        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //


	}
	
	private function _updateImgToPost($data = null){
		
		$post_id = $data['post_id'];
		$img = $data['img'];
        $is_category = isset($data['is_category'])?$data['is_category']:0;
        $is_gallery = isset($data['is_gallery'])?$data['is_gallery']:0;

        if($is_category) {
            // update
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'blog_category` SET
							   `img` = \'' . pSQL($img) . '\'
							   WHERE id = ' . (int)$post_id . '
							   ';

        }elseif($is_gallery){
            // update
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'blog_gallery` SET
							   `img` = \'' . pSQL($img) . '\'
							   WHERE id = ' . (int)$post_id . '
							   ';
        } else {

            // update
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'blog_post` SET
							   `img` = \'' . pSQL($img) . '\'
							   WHERE id = ' . (int)$post_id . '
							   ';
        }

		Db::getInstance()->Execute($sql);


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //
		
	}
	
	public function getHttp(){
		$smarty = $this->context->smarty;
		$http = null;
		if(defined('_MYSQL_ENGINE_')){
				$http = @$smarty->tpl_vars['base_dir']->value;
		} else {
				$http = @$smarty->_tpl_vars['base_dir'];
		}
		if(empty($http)) $http = $_SERVER['HTTP_HOST'];
		return $http;
	}
	
	public function getAllImg(){
			
			$path = $_SERVER['DOCUMENT_ROOT']."/upload/".$this->_name."/";
			
			$d = @opendir($path);
         	
         	$data = array();
         	
			if(!$d) return;
			
			while(($e=readdir($d)) !== false)
			{
				if($e =='.' || $e=='..') continue;
				$data[] = $e;	
			}
			return array('data'=>$data);
	}
	
	public function copyImage($data){
	
		$filename = $data['name'];
		$dir_without_ext = $data['dir_without_ext'];
		
		$is_height_width = 0;
		if(isset($data['width']) && isset($data['height'])){
			$is_height_width = 1;
		}
			
		
		$width = isset($data['width'])?$data['width']:$this->_width;
		$height = isset($data['height'])?$data['height']:$this->_height;
		
		$width_orig_custom = $width;
		$height_orig_custom = $height;
		
		if (!$width){ $width = 85;}
		if (!$height){ $height = 85;}
		// Content type
		$size_img = getimagesize($filename);
		// Get new dimensions


        ## rotated ##
       /* $filename = imagecreatefromstring($filename);
        $exif = exif_read_data($filename);

        if(!empty($exif['Orientation'])) {
            switch($exif['Orientation']) {
                case 8:
                    $filename = imagerotate($filename,90,0);
                    break;
                case 3:
                    $filename = imagerotate($filename,180,0);
                    break;
                case 6:
                    $filename = imagerotate($filename,-90,0);
                    break;
            }
        }*/
        ## rotated ##

		list($width_orig, $height_orig) = getimagesize($filename);



        ## rotated ##
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($filename);

            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 6:
                        list($height_orig, $width_orig) = getimagesize($filename);
                        break;
                }
            }
        }
        ## rotated ##


		$ratio_orig = $width_orig/$height_orig;



		if($width_orig>$height_orig){
		    $height =  $width/$ratio_orig;
		}else{ 
		    $width = $height*$ratio_orig;
		}
		if($width_orig<$width){
			$width = $width_orig;
			$height = $height_orig;
		}
	
		$image_p = imagecreatetruecolor($width, $height);
		$bgcolor=ImageColorAllocate($image_p, 255, 255, 255);
		//   
		imageFill($image_p, 5, 5, $bgcolor);
	
		if ($size_img[2]==2){ $image = imagecreatefromjpeg($filename);}                         
		else if ($size_img[2]==1){  $image = imagecreatefromgif($filename);}                         
		else if ($size_img[2]==3) { $image = imagecreatefrompng($filename); }


        ## rotated ##
        if(!empty($exif['Orientation'])) {
            switch($exif['Orientation']) {
                case 6:
                    $image = imagerotate($image,-90,0);
               break;
            }
        }
        ## rotated ##

		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		// Output
		
		if ($is_height_width)
			$users_img = $dir_without_ext.'-'.$width_orig_custom.'x'.$height_orig_custom.'.jpg';
		else
		 	$users_img = $dir_without_ext.'.jpg';
		
		if ($size_img[2]==2)  imagejpeg($image_p, $users_img, 100);                         
		else if ($size_img[2]==1)  imagejpeg($image_p, $users_img, 100);                        
		else if ($size_img[2]==3)  imagejpeg($image_p, $users_img, 100);
		imageDestroy($image_p);
		imageDestroy($image);
		//unlink($filename);

	}
	
	
	public function deletePost($data){
		
		$id = $data['id'];


        $this->deleteImg(array('id' => $id));

		$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_post`
							   WHERE id ='.(int)$id.'';
		Db::getInstance()->Execute($sql);
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_post_data`
							   WHERE id_item ='.(int)$id.'';

		Db::getInstance()->Execute($sql);
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_category2post`
							   WHERE post_id ='.(int)$id.'';
		Db::getInstance()->Execute($sql);
		
		// delete comments
		$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_comments`
							   WHERE id_post ='.(int)$id.'';
		Db::getInstance()->Execute($sql);


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //


				
	}



	
	public function getPostItem($_data){
			$id = $_data['id'];
			$site = isset($_data['site'])?$_data['site']:'';
		
			if($site==1){


			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;
				/*$sql = '
			SELECT pc.*, ba2c.status as status_author,
			(select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1) as count_like,
                (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1 and ip = \''.pSQL($_SERVER['REMOTE_ADDR']).'\') as is_liked_post,
                (select count(*) as count from `'._DB_PREFIX_.'blog_comments` pcc WHERE pcc.id_post = pc.id and pcc.status = 1 and pcc.id_lang = '.(int)$current_language.') as count_comments
			FROM `'._DB_PREFIX_.'blog_post` pc LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc1
			ON(pc1.id_item = pc.id)
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE pc.`id` = '.(int)$id.' AND pc1.id_lang = '.(int)$current_language.' 
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			AND ba2c.status != 2 AND ba2c.id_shop = '.(int)$this->_id_shop.'
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			';*/

                $sql = '
			SELECT pc.*, 1 as status_author,
			(select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1) as count_like,
                (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1 and ip = \''.pSQL($_SERVER['REMOTE_ADDR']).'\') as is_liked_post,
                (select count(*) as count from `'._DB_PREFIX_.'blog_comments` pcc WHERE pcc.id_post = pc.id and pcc.status = 1 and pcc.id_lang = '.(int)$current_language.') as count_comments
			FROM `'._DB_PREFIX_.'blog_post` pc LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc1
			ON(pc1.id_item = pc.id)
			WHERE pc.`id` = '.(int)$id.' AND pc1.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			';

			$item = Db::getInstance()->ExecuteS($sql);
			
			foreach($item as $k => $_item){
				
				if(Tools::strlen($_item['img'])>0){
                    $item[$k]['img_orig'] = $_item['img'];
                    $this->generateThumbImages(array('img'=>$_item['img'],
		    												 'width'=>Configuration::get($this->_name.'post_img_width'),
		    												 'height'=>Configuration::get($this->_name.'post_img_width') 
		    												)
		    											);
		    		$img = Tools::substr($_item['img'],0,-4)."-".Configuration::get($this->_name.'post_img_width')."x".Configuration::get($this->_name.'post_img_width').".jpg";
				} else {
		    		$img = $_item['img'];
				}
		    	
		    	$item[$k]['img'] = $img;
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				foreach ($items_data as $item_data){
					if($current_language == $item_data['id_lang']){
		    			$item[$k]['title'] = $item_data['title'];
		    			$item[$k]['content'] = $item_data['content'];
		    			$item[$k]['seo_keywords'] = $item_data['seo_keywords'];
		    			$item[$k]['seo_description'] = $item_data['seo_description'];
		    			$item[$k]['seo_url'] = $item_data['seo_url'];

                        $tags = $item_data['tags'];

                        ## author ##
                        $item[$k]['author'] = $_item['author'];
                        $author_id = $_item['author_id'];
                        $item[$k]['author_id'] = $author_id;

                        $info_path = $this->getAvatarPath(array('id_customer'=>$author_id,'id_shop'=>$this->_id_shop));

                        //var_dump($author_id);var_dump($info_path);exit;

                        $item[$k]['avatar'] = $info_path['avatar'];
                        $item[$k]['is_show_ava'] = $info_path['is_show'];
                        ## author ##



                        if(Tools::strlen($tags)>0)
                            $tags = explode(",",$tags);
                        else
                            $tags = null;

                        $item[$k]['tags'] = $tags;
                        $item[$k]['time_add'] = $_item['time_add'];
					}
		    		
		    	}

                $item[$k]['time_add_rss'] = date(DATE_ATOM,strtotime($_item['time_add']));
		    	
			}
			
			
			$sql = '
			SELECT pc.category_id, pc.post_id
			FROM `'._DB_PREFIX_.'blog_category2post` pc LEFT JOIN  `'._DB_PREFIX_.'blog_category` pc2  ON(pc2.id = pc.category_id)
			WHERE pc.`post_id` = '.(int)$id.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc2.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc2.ids_groups)

			';
			
			$category_ids = Db::getInstance()->ExecuteS($sql);
                //echo "<pre>"; var_dump($category_ids);
			$data_category_ids = array();
			foreach($category_ids as $k => $v){
				$data_category_ids[] = $v['category_id'];
			}
			
			$item[0]['category_ids'] = $data_category_ids;
			
			
			} else {


                $is_admin = isset($_data['is_admin'])?$_data['is_admin']:0;
                // admin handler
			
			$item = Db::getInstance()->ExecuteS('
			SELECT pc.*,
			(select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1) as count_like
			FROM `'._DB_PREFIX_.'blog_post` pc
			WHERE pc.`id` = '.(int)$id.'
			');
			
			foreach($item as $k => $_item){



                if(Tools::strlen($_item['img'])>0){
                    $item[$k]['img_orig'] = $_item['img'];
                    $this->generateThumbImages(array('img'=>$_item['img'],
                            'width'=>Configuration::get($this->_name.'post_img_width'),
                            'height'=>Configuration::get($this->_name.'post_img_width')
                        )
                    );
                    $img = Tools::substr($_item['img'],0,-4)."-".Configuration::get($this->_name.'post_img_width')."x".Configuration::get($this->_name.'post_img_width').".jpg";
                } else {
                    $img = $_item['img'];
                }

                $item[$k]['img'] = $img;

				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');



                $item[$k]['author'] = $_item['author'];
                $author_id = ($is_admin)?$_data['id_customer']:$_item['author_id'];
                $item[$k]['author_id'] = $author_id;

                $ids_shops  = $_item['ids_shops'];

                $info_path = $this->getAvatarPath(array('id_customer'=>$author_id,'id_shop'=>$ids_shops));

                //var_dump($author_id);var_dump($info_path);exit;

                $item[$k]['info'] = $info_path['info'];
                $item[$k]['avatar'] = $info_path['avatar'];
                $item[$k]['is_show_ava'] = $info_path['is_show'];
                $item[$k]['is_exist_ava'] = $info_path['is_exist'];

                $data_seo_url = $this->getSEOURLs();
                $user_url = $data_seo_url['author_url'];

                $item[$k]['author_url'] = $user_url;

                $data_customer_info = $this->getCustomerInfo(array('id_customer'=>$author_id));
                $author_name = $data_customer_info['customer_name'];
                $author_email = $data_customer_info['email'];
                $item[$k]['customer_name'] = $author_name;
                $item[$k]['customer_email'] = $author_email;
				
				foreach ($items_data as $item_data){
		    			$item['data'][$item_data['id_lang']]['title'] = $item_data['title'];
		    			$item['data'][$item_data['id_lang']]['content'] = $item_data['content'];
		    			$item['data'][$item_data['id_lang']]['seo_keywords'] = $item_data['seo_keywords'];
		    			$item['data'][$item_data['id_lang']]['seo_description'] = $item_data['seo_description'];
		    			$item['data'][$item_data['id_lang']]['seo_url'] = $item_data['seo_url'];
                        $item['data'][$item_data['id_lang']]['tags'] = $item_data['tags'];

                        $item['data'][$item_data['id_lang']]['img'] = $_item['img'];



		    	}
		    	
			}

			$category_ids = Db::getInstance()->ExecuteS('
			SELECT pc.category_id, pc.post_id
			FROM `'._DB_PREFIX_.'blog_category2post` pc
			WHERE pc.`post_id` = '.(int)$id.'');
			
			$data_category_ids = array();
			foreach($category_ids as $k => $v){
				$data_category_ids[] = $v['category_id'];
			}
			
			$item[0]['category_ids'] = $data_category_ids;

                // admin handler
			}
			
	   return array('post' => $item);
	}



	public function  getComments($_data){
		$admin = isset($_data['admin'])?$_data['admin']:null;
		$id = isset($_data['id'])?$_data['id']:0;
		
		if($admin == 1){
			
			$posts = Db::getInstance()->ExecuteS('
			SELECT pc.*
			FROM `'._DB_PREFIX_.'blog_comments` pc
			WHERE pc.id_post = '.(int)$id.'
			ORDER BY pc.`time_add` DESC');
			
			$data_count_posts = Db::getInstance()->getRow('
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_comments`  as pc
			WHERE pc.id_post = '.(int)$id.'
			');
			
		} elseif($admin == 2){
			$start = $_data['start'];
			$step = $_data['step'];
			
			$posts = Db::getInstance()->ExecuteS('
			SELECT pc.*
			FROM `'._DB_PREFIX_.'blog_comments` pc
			ORDER BY pc.`time_add` DESC  LIMIT '.(int)$start.' ,'.(int)$step.'');
			
			$data_count_posts = Db::getInstance()->getRow('
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_comments`  as pc
			');
        } elseif($admin == 3){

            $cookie = $this->context->cookie;
            $current_language = (int)$cookie->id_lang;

            $start = $_data['start'];
            $step = $_data['step'];

            $id_customer = isset($_data['id_customer'])?$_data['id_customer']:0;

            $sql = '
			SELECT pc.*, pc1.title as title, pc1.seo_url, pc1.content, pc2.img
			FROM `'._DB_PREFIX_.'blog_comments` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_post` pc2 ON(pc.id_post = pc2.id)
            LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc1 ON(pc1.id_item = pc2.id)


			WHERE pc.`id_customer` = '.(int)$id_customer.' and pc.id_lang = '.(int)$current_language.'
			and pc.id_shop = '.(int)$this->_id_shop.' AND pc1.id_lang = '.(int)$current_language.'

			AND (pc.status = 1 OR pc.status = 0  OR pc.status = 3)
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc2.ids_shops)

            ORDER BY pc.`time_add` DESC LIMIT '.(int)$start.' ,'.(int)$step.'';

            $posts = Db::getInstance()->ExecuteS($sql);

            $data_count_posts = Db::getInstance()->getRow('
			SELECT COUNT(*) AS "count"
			FROM `'._DB_PREFIX_.'blog_comments` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_post` pc2 ON(pc.id_post = pc2.id)
            LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc1 ON(pc1.id_item = pc2.id)

			WHERE  pc.`id_customer` = '.(int)$id_customer.' and pc.id_lang = '.(int)$current_language.'
			and pc.id_shop = '.(int)$this->_id_shop.' AND

			pc1.id_lang = '.(int)$current_language.' AND (pc.status = 1 OR pc.status = 0 OR pc.status = 3)
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc2.ids_shops)

			');


		} else{
			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;
			
			$start = $_data['start'];
			$step = $_data['step'];



            $sql = '
			SELECT pc.*, pc1.title as post_title
			FROM `'._DB_PREFIX_.'blog_comments` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_post` pc2 ON(pc.id_post = pc2.id)
            LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc1 ON(pc1.id_item = pc2.id)


			WHERE pc.`id_post` = '.(int)$id.' and pc.status = 1 and pc.id_lang = '.(int)$current_language.'
			and pc.id_shop = '.(int)$this->_id_shop.' AND

			pc1.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc2.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc2.ids_groups)
			AND pc2.status = 1
			'.(($this->_is_show_now)?'AND (pc2.`publish_date` <= NOW())':'').'


            ORDER BY pc.`time_add` DESC LIMIT '.(int)$start.' ,'.(int)$step.'';


			$posts = Db::getInstance()->ExecuteS($sql);


            include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
            $obj_userprofileblockblog = new userprofileblockblog();


            foreach ($posts as $k => $item_data){

                    ## author ##
                    $author_id = $item_data['id_customer'];

                    if($author_id != 0) {

                        $sql = 'select ba2c.status from `'._DB_PREFIX_.'blog_avatar2customer` ba2c WHERE ba2c.id_customer = '.(int)$author_id.' and ba2c.id_shop = '.(int)$this->_id_shop.'';
                        $status_author_data = Db::getInstance()->getRow($sql);
                        $status_author = $status_author_data['status'];


                        $_info_author = $this->isExistsAuthor(array('author_id' => $author_id));


                        $info_path = $this->getAvatarPath(array('id_customer' => $author_id,'id_shop'=>$this->_id_shop,'status_author'=>$status_author));



                        $posts[$k]['status_author'] = $status_author;


                        $posts[$k]['avatar'] = $info_path['avatar'];




                        if($_info_author === NULL){
                            // if author not have posts, then author without link to page with all posts by author
                            $posts[$k]['is_show_ava'] = 0;
                        } else {
                            $posts[$k]['is_show_ava'] = $info_path['is_show'];
                        }

                            $posts[$k]['author_id'] = $author_id;

                            $customer_data = $obj_userprofileblockblog->getInfoAboutCustomer(array('id_customer' => $author_id, 'is_full' => 1));
                            $name_customer = $customer_data['customer_name'];


                            $posts[$k]['author'] = $name_customer;






                    } else {
                        $posts[$k]['avatar'] = '';
                        $posts[$k]['is_show_ava'] = 0;
                        $posts[$k]['author_id'] = 0;
                        $posts[$k]['author'] = $item_data['name'];
                    }
                    ## author ##

            }

            //echo "<pre>"; var_dump($posts);exit;

			
			$data_count_posts = Db::getInstance()->getRow('
			SELECT COUNT(*) AS "count"
			FROM `'._DB_PREFIX_.'blog_comments` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_post` pc2 ON(pc.id_post = pc2.id)
            LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc1 ON(pc1.id_item = pc2.id)


			WHERE pc.id_post = '.(int)$id.' and pc.status = 1 and pc.id_lang = '.(int)$current_language.'
			and pc.id_shop = '.(int)$this->_id_shop.' AND

			pc1.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc2.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc2.ids_groups)
			AND pc2.status = 1
			'.(($this->_is_show_now)?'AND (pc2.`publish_date` <= NOW())':'').'
			');
		}	
		return array('comments' => $posts, 'count_all' => $data_count_posts['count'] );
	}
	
	public function deleteComment($data){
		
		$id = $data['id'];

        $_data_comm = $this->getCommentItem(array('id'=>$id));
        $id_customer = $_data_comm['comments'][0]['id_customer'];

        // delete comments
		$sql = 'DELETE FROM `'._DB_PREFIX_.'blog_comments`
							   WHERE id ='.(int)$id.'';
		Db::getInstance()->Execute($sql);

        // loyality program //
        include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        $loyality->deleteLoyalityPoints(array('id_loyalty_status' => 0, 'id_customer' => $id_customer, 'id_comment' => $id, 'type' => 'loyality_add_blog_comment'));
        // loyality program //

        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //
				
	}
	
	public function getCommentItem($_data){
			$id = $_data['id'];
		
            $is_myaccount_edit = isset($_data['is_myaccount_edit'])?$_data['is_myaccount_edit']:0;
            if($is_myaccount_edit) {
                $cookie = $this->context->cookie;
                $current_language = (int)$cookie->id_lang;

                $id_customer = isset($_data['id_customer']) ? $_data['id_customer'] : 0;

                $sql = '
                SELECT pc.*, pc1.title as title, pc1.seo_url, pc1.content, pc2.img
                FROM `' . _DB_PREFIX_ . 'blog_comments` pc
                LEFT JOIN `' . _DB_PREFIX_ . 'blog_post` pc2 ON(pc.id_post = pc2.id)
                LEFT JOIN `' . _DB_PREFIX_ . 'blog_post_data` pc1 ON(pc1.id_item = pc2.id)

                WHERE pc.`id_customer` = ' . (int)$id_customer . ' and pc1.id_lang = ' . (int)$current_language . '
                 AND pc.id = '.(int)$id.'
                ';


            } else {
                $sql = '
                SELECT pc.*
                FROM `'._DB_PREFIX_.'blog_comments` pc
                WHERE pc.`id` = '.(int)$id.'';

            }



        $comment = Db::getInstance()->ExecuteS($sql);

	   return array('comments' => $comment);
	}



    public function updateCommentMyAccount($data){

        $id_editcomments = $data['id'];

        $comments_comment = $data['comment-blockblog'];

        $rating = $data['rating-blockblog'];

        $status = $data['status'];

        if($rating < 0)
            $rating = 1;

        if($rating > 5)
            $rating = 5;


        $_data_comm = $this->getCommentItem(array('id'=>$id_editcomments));
        $old_comments_comment = $_data_comm['comments'][0]['comment'];
        $old_rating = $_data_comm['comments'][0]['rating'];



        // update
        $sql = 'UPDATE `'._DB_PREFIX_.'blog_comments` SET
							   `comment` = \''.pSQL($comments_comment).'\',
							    rating = \''.(int)($rating).'\',
							    `status` = \''.(int)($status).'\'
							   WHERE id = '.(int)$id_editcomments.'
							   ';
        Db::getInstance()->Execute($sql);


        // updated avg_rating for blog post//
        $_data_comm = $this->getCommentItem(array('id'=>$id_editcomments));
        $id_post = $_data_comm['comments'][0]['id_post'];
        $id_customer = $_data_comm['comments'][0]['id_customer'];

        $result_avg = Db::getInstance()->getRow('select ceil(AVG(`rating`)) AS "avg_rating" from `'._DB_PREFIX_.'blog_comments` where id_post ='.(int)$id_post);
        $result_avg_rating = $result_avg['avg_rating'];


        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_post` SET `avg_rating` = "'.(int)$result_avg_rating.'" WHERE id ='.(int)$id_post;
        Db::getInstance()->Execute($sql_update);
        // updated avg_rating for blog post//



        // loyality program //
        if(!in_array($status,array(0,1))){
            $status = 0;
        }
        include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        $loyality->setLoyalityStatus(array('id_loyalty_status' => $status, 'id_customer' => $id_customer, 'id_comment'=>$id_editcomments, 'type' => 'loyality_add_blog_comment'));
        // loyality program //



        $this->sendNotificationToAdminWhenCustomerDeleteComment(array('id'=>$id_editcomments,'old_comment'=>$old_comments_comment,'old_rating'=>$old_rating));

    }



    public function sendNotificationToAdminWhenCustomerDeleteComment($data = null){

        if(Configuration::get($this->_name.'noti') == 1
            && Configuration::get($this->_name.'is_editblogcomcust') == 1
        ){

            include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
            $obj = new blockblog();
            $_data_translate = $obj->translateItems();

            $id_editcomments = $data['id'];

            $_data_comm = $this->getCommentItem(array('id'=>$id_editcomments));
            $id_post = $_data_comm['comments'][0]['id_post'];
            $id_lang = $_data_comm['comments'][0]['id_lang'];

            ### get post info ###

            $_info_post = $this->getPostItem(array('id' => $id_post,'site'=>1));

            $title_post = isset($_info_post['post'][0]['title'])?$_info_post['post'][0]['title']:'';
            $img = isset($_info_post['post'][0]['img'])?$_info_post['post'][0]['img']:'';
            if(Tools::strlen($img)>0) {
                $img = $this->getHttpost().$obj->getCloudImgPath().$img;
            }

            $seo_url =  isset($_info_post['post'][0]['seo_url'])?$_info_post['post'][0]['seo_url']:'';
            $data_url = $this->getSEOURLs();
            $post_url = $data_url['post_url'];

            if($this->isURLRewriting()) {
                $com_url = $post_url . $seo_url.'#blogcomments';
            } else {
                $com_url = $post_url.'&post_id='.$id_post.'#blogcomments';
            }
            ### get post info ###


            $name = $_data_comm['comments'][0]['name'];
            $comments_comment = $_data_comm['comments'][0]['comment'];
            $rating = $_data_comm['comments'][0]['rating'];
            $email = $_data_comm['comments'][0]['email'];
            $time_add = $_data_comm['comments'][0]['time_add'];

            $old_comment = $data['old_comment'];
            $old_rating = $data['old_rating'];


            $old_comment_txt = '';
            if($comments_comment != $old_comment) {
                $old_comment_txt .= '<br /><br /><span style="color:#333"><strong>'.$_data_translate['old_comment'].' :</strong></span> ' . $old_comment ;
            }

            $old_rating_txt = '';
            if($rating != $old_rating) {
                $old_rating_txt .= '<br /><br /><span style="color:#333"><strong>'.$_data_translate['old_rating'].' :</strong></span> ' . $old_rating ;
            }

            /* Email generation */
            $templateVars = array(
                '{name}' => $name,
                '{text}' => Tools::stripslashes($comments_comment).$old_comment_txt,
                '{title_post}' => $title_post,
                '{com_url}' => $com_url,
                '{picture}'=>$img,
                '{email}'=>$email,
                '{time_add}'=>$time_add,
                '{rating}'=>$rating.$old_rating_txt,
            );


            /* Email sending */

            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            }
            else {
                $id_lang_current = Language::getIdByIso('en');
            }

            $subject = Configuration::get($this->_name . 'editblogcomcust_' . $id_lang_current);


            Mail::Send($id_lang_current, 'admin-customer-editblogcomment', $subject, $templateVars,
                Configuration::get($this->_name.'mail'), 'Response Form', NULL, NULL,
                NULL, NULL, dirname(__FILE__).'/../mails/');



        }

    }
	
	public function updateComment($data){
	
		$id_editcomments = $data['id_editcomments'];

		$comments_name = $data['comments_name'];
		$comments_email = $data['comments_email'];
		$comments_comment = $data['comments_comment'];
		$comments_status = $data['comments_status'];
        $time_add = $data['time_add'];
        $response = $data['response'];
        $is_noti = $data['is_noti'];
        $rating = $data['rating'];
        $id_customer = $data['id_customer'];

        if($rating < 0)
            $rating = 1;

        if($rating > 5)
            $rating = 5;

        $_data_comm = $this->getCommentItem(array('id'=>$id_editcomments));
        $comments_status_before_update = $_data_comm['comments'][0]['status'];

        // update
		$sql = 'UPDATE `'._DB_PREFIX_.'blog_comments` SET
							   `name` = \''.pSQL($comments_name).'\',
							   `email` = \''.pSQL($comments_email).'\',
							   `comment` = \''.pSQL($comments_comment).'\',
							   `response` = \''.pSQL($response).'\',
							   `time_add` = \''.pSQL($time_add).'\',
							    rating = \''.(int)($rating).'\',
							   `status` = \''.(int)($comments_status).'\'
							   WHERE id = '.(int)$id_editcomments.'
							   ';
		Db::getInstance()->Execute($sql);


        // loyality program //
        include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        $loyality->setLoyalityStatus(array('id_loyalty_status' => $comments_status, 'id_customer' => $id_customer, 'id_comment'=>$id_editcomments, 'type' => 'loyality_add_blog_comment'));
        // loyality program //


        // updated avg_rating for blog post//
        $_data_comm = $this->getCommentItem(array('id'=>$id_editcomments));
        $id_post = $_data_comm['comments'][0]['id_post'];

        $result_avg = Db::getInstance()->getRow('select ceil(AVG(`rating`)) AS "avg_rating" from `'._DB_PREFIX_.'blog_comments` where id_post ='.(int)$id_post);
        $result_avg_rating = $result_avg['avg_rating'];


        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_post` SET `avg_rating` = "'.(int)$result_avg_rating.'" WHERE id ='.(int)$id_post;
        Db::getInstance()->Execute($sql_update);
        // updated avg_rating for blog post//


        include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
        $obj = new blockblog();
        $error = 0;
        $error_text = '';

        if($obj->is_demo == 0) {

            if($id_customer) {

                $post_images_ava = $data['post_images_ava'];
                $error_data = $this->saveImageAvatar(array('id' => $id_post, 'post_images' => $post_images_ava, 'id_customer' => $id_customer));

                $error = $error_data['error'];
                $error_text = $error_data['error_text'];
            }
        }


        if($is_noti){
            // send response notification for blog post
            $this->sendNotificationResponse($data);
            // send response notification for blog post
        }

        // change status of the comment send email to admin
        if($comments_status_before_update == 3){
            $this->sendNotificationToCustomerWhenEditedCommentApprovedByAdmin(array('id'=>$id_editcomments));
        }
        // change status of the comment send email to admin

        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //

        return array('error'=>$error,'error_text'=>$error_text);
		
	}


    public function sendNotificationToCustomerWhenEditedCommentApprovedByAdmin($data = null){

        if(Configuration::get($this->_name.'noti') == 1 && Configuration::get($this->_name.'is_editblogcomtocust') == 1){

            include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
            $obj = new blockblog();


            $id_editcomments = $data['id'];

            $_data_comm = $this->getCommentItem(array('id'=>$id_editcomments));
            $id_post = $_data_comm['comments'][0]['id_post'];
            $id_lang = $_data_comm['comments'][0]['id_lang'];

            ### get post info ###

            $_info_post = $this->getPostItem(array('id' => $id_post,'site'=>1));

            $title_post = isset($_info_post['post'][0]['title'])?$_info_post['post'][0]['title']:'';
            $img = isset($_info_post['post'][0]['img'])?$_info_post['post'][0]['img']:'';
            if(Tools::strlen($img)>0) {
                $img = $this->getHttpost().$obj->getCloudImgPath().$img;
            }

            $seo_url =  isset($_info_post['post'][0]['seo_url'])?$_info_post['post'][0]['seo_url']:'';
            $data_url = $this->getSEOURLs();
            $post_url = $data_url['post_url'];

            if($this->isURLRewriting()) {
                $com_url = $post_url . $seo_url.'#blogcomments';
            } else {
                $com_url = $post_url.'&post_id='.$id_post.'#blogcomments';
            }
            ### get post info ###


            $name = $_data_comm['comments'][0]['name'];
            $comments_comment = $_data_comm['comments'][0]['comment'];
            $email = $_data_comm['comments'][0]['email'];
            $time_add = $_data_comm['comments'][0]['time_add'];
            $rating = $_data_comm['comments'][0]['rating'];


            /* Email generation */
            $templateVars = array(
                '{name}' => $name,
                '{text}' => Tools::stripslashes($comments_comment),
                '{title_post}' => $title_post,
                '{com_url}' => $com_url,
                '{picture}'=>$img,
                '{email}'=>$email,
                '{time_add}'=>$time_add,
                '{rating}'=>$rating,
            );


            /* Email sending */

            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            }
            else {
                $id_lang_current = Language::getIdByIso('en');
            }

            $subject = Configuration::get($this->_name . 'editblogcomtocust_' . $id_lang_current);



            Mail::Send($id_lang_current, 'customer-approveeditblogcomment', $subject, $templateVars,
                $email, 'Response Form', NULL, NULL,
                NULL, NULL, dirname(__FILE__).'/../mails/');



        }

    }


    public function sendNotificationResponse($data = null){

        if(Configuration::get($this->_name.'noti') == 1 && Configuration::get($this->_name.'is_responseblogc') == 1){

            include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
            $obj = new blockblog();


            $id_editcomments = $data['id_editcomments'];

            $_data_comm = $this->getCommentItem(array('id'=>$id_editcomments));
            $id_post = $_data_comm['comments'][0]['id_post'];
            $id_lang = $_data_comm['comments'][0]['id_lang'];

            ### get post info ###

            $_info_post = $this->getPostItem(array('id' => $id_post,'site'=>1));

            $title_post = isset($_info_post['post'][0]['title'])?$_info_post['post'][0]['title']:'';
            $img = isset($_info_post['post'][0]['img'])?$_info_post['post'][0]['img']:'';
            if(Tools::strlen($img)>0) {
                $img = $this->getHttpost().$obj->getCloudImgPath().$img;
            }

            $seo_url =  isset($_info_post['post'][0]['seo_url'])?$_info_post['post'][0]['seo_url']:'';
            $data_url = $this->getSEOURLs();
            $post_url = $data_url['post_url'];

            if($this->isURLRewriting()) {
                $com_url = $post_url . $seo_url.'#blogcomments';
            } else {
                $com_url = $post_url.'&post_id='.$id_post.'#blogcomments';
            }
            ### get post info ###


            $name = $data['comments_name'];
            $comments_comment = $data['comments_comment'];
            $response = $data['response'];
            $email = $data['comments_email'];


            /* Email generation */
            $templateVars = array(
                '{name}' => $name,
                '{text}' => Tools::stripslashes($comments_comment),
                '{response}' => Tools::stripslashes($response),
                '{title_post}' => $title_post,
                '{com_url}' => $com_url,
                '{picture}'=>$img,
            );


            /* Email sending */

            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            }
            else {
                $id_lang_current = Language::getIdByIso('en');
            }

            $subject = Configuration::get($this->_name . 'responseblogc_' . $id_lang_current);

            Mail::Send($id_lang_current, 'response-blogcomment', $subject, $templateVars,
                $email, 'Response Form', NULL, NULL,
                NULL, NULL, dirname(__FILE__).'/../mails/');



        }

    }
	
	public function saveComment($_data){
		$name = $_data['name'];
		$email = $_data['email'];
		$text_review =  $_data['text_review'];
		$id_post = $_data['id_post'];
        $rating = (int)$_data['rating'];

        $id_shop = isset($_data['id_shop'])?$_data['id_shop']:$this->_id_shop;

        if($rating < 0)
            $rating = 1;

        if($rating > 5)
            $rating = 5;


        $time_add = isset($_data['time_add'])?'\''.pSQL($_data['time_add']).'\'':'NOW()';
        $status = isset($_data['status'])?$_data['status']:0;



        $cookie = $this->context->cookie;
		$current_language = (int)$cookie->id_lang;
        $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;

        $current_language = isset($_data['id_lang'])?$_data['id_lang']:$current_language;
		
		$sql = 'INSERT into `'._DB_PREFIX_.'blog_comments` SET
							   `id_lang` = \''.(int)($current_language).'\',
							   `name` = \''.pSQL($name).'\',
							   `email` = \''.pSQL($email).'\',
							   `id_customer` = \''.(int)($id_customer).'\',
							   `comment` = \''.pSQL($text_review).'\',
							   `id_post` = \''.(int)($id_post).'\',
							    rating = '.(int)($rating).',
							   `id_shop` = \''.(int)($id_shop).'\',
							   `status` = \''.(int)($status).'\',
							   `time_add` = '.$time_add.'
							   ';
		Db::getInstance()->Execute($sql);


        $id_comment = Db::getInstance()->Insert_ID();


        // loyality program //
        include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        $loyality->saveLoyalityAction(array('id_loyalty_status' => 0, 'id_customer' => $id_customer, 'id_comment'=>$id_comment, 'type' => 'loyality_add_blog_comment'));
        // loyality program //

        // !!! IMPORTANt rating is calculated after updated comment in admin panel by admin !!!!

        // updated avg_rating for blog post//
        /*$result_avg = Db::getInstance()->getRow('select ceil(AVG(`rating`)) AS "avg_rating" from `'._DB_PREFIX_.'blog_comments` where id_post ='.(int)$id_post);
        $result_avg_rating = $result_avg['avg_rating'];

        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_post` SET `avg_rating` = "'.(int)$result_avg_rating.'" WHERE id ='.(int)$id_post;
        Db::getInstance()->Execute($sql_update);*/
        // updated avg_rating for blog post//

        // !!! IMPORTANt rating is calculated after updated comment in admin panel by admin !!!!


        $is_noti = isset($_data['is_noti'])?$_data['is_noti']:1;

		
		if(Configuration::get($this->_name.'noti') == 1 && $is_noti && Configuration::get($this->_name.'is_newblogc') == 1){

			include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
			$obj = new blockblog();

            ### get post info ###

            $_info_post = $this->getPostItem(array('id' => $id_post,'site'=>1));

            $title_post = isset($_info_post['post'][0]['title'])?$_info_post['post'][0]['title']:'';
            $img = isset($_info_post['post'][0]['img'])?$_info_post['post'][0]['img']:'';
            if(Tools::strlen($img)>0) {
                $img = $this->getHttpost().$obj->getCloudImgPath().$img;
            }

            $seo_url =  isset($_info_post['post'][0]['seo_url'])?$_info_post['post'][0]['seo_url']:'';
            $data_url = $this->getSEOURLs();
            $post_url = $data_url['post_url'];

            if($this->isURLRewriting()) {
                $com_url = $post_url . $seo_url.'#blogcomments';
            } else {
                $com_url = $post_url.'&post_id='.$id_post.'#blogcomments';
            }
            ### get post info ###


            /* Email generation */
		$templateVars = array(
			'{email}' => $email,
			'{name}' => $name,
			'{message}' => Tools::stripslashes($text_review),
            '{img}' => $img,
            '{rating}' => $rating,
             '{title_post}' => $title_post,
            '{com_url}' => $com_url,
		);
		
		
		$id_lang = (int)($cookie->id_lang);
		
		$iso_code = Language::getIsoById($id_lang);
		
		if (is_dir(_PS_MODULE_DIR_ . $this->_name.'/mails/' . $iso_code . '/') && !empty($iso_code)) {
			$id_lang_mail = $id_lang;
		} else {
			$id_lang_mail = Configuration::get('PS_LANG_DEFAULT');
		}
				
		/* Email sending */

        $subject = Configuration::get($this->_name . 'newblogc_' . $id_lang_mail);

		Mail::Send($id_lang_mail, 'comment', $subject, $templateVars, 
			Configuration::get($this->_name.'mail'), 'Blog Comment Form', $email, $name,
			NULL, NULL, dirname(__FILE__).'/../mails/');
		}


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //
	}
	
	public function PageNav($start,$count,$step, $_data =null )
	{

        $data_url = $this->getSEOURLs();
        $ajax_url = $data_url['ajax_url'];


		$_admin = isset($_data['admin'])?$_data['admin']:null;
		$category_id = isset($_data['category_id'])?$_data['category_id']:0;


        $tag_id = isset($_data['tag_id'])?$_data['tag_id']:0;

		$post_id = isset($_data['post_id'])?$_data['post_id']:0;
		$is_category = isset($_data['category'])?$_data['category']:0;
		
		$all_posts = isset($_data['all_posts'])?$_data['all_posts']:'';
		$is_search = isset($_data['is_search'])?$_data['is_search']:0;
		
		$is_arch = isset($_data['is_arch'])?$_data['is_arch']:0;
		$month = isset($_data['month'])?$_data['month']:0;
		$year = isset($_data['year'])?$_data['year']:0;
		
		$all_comments = isset($_data['all_comments'])?$_data['all_comments']:'';

        $is_author =isset($_data['is_author'])?$_data['is_author']:'';
        $is_authors =isset($_data['is_authors'])?$_data['is_authors']:'';


		
		include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
		$obj = new blockblog();
		$_data_translate = $obj->translateItems();
		$page_translate = $_data_translate['page'];

        $res = '';

        include(dirname(__FILE__).'/../views/templates/hooks/'.$this->_name.'/'.__FUNCTION__.'.phtml');
		
		
		return $res;
	}
	
	public function getCategoriesBlock($data = null){
		$limit  = (int)Configuration::get($this->_name.'blog_bcat');


        $is_tab = isset($data['is_tab'])?$data['is_tab']:0;

        if($is_tab){
            $order_by = 'ORDER BY pc.`'.Configuration::get($this->_name.'blog_catp_order').'` '.Configuration::get($this->_name.'blog_catp_ad').'';
        } else {
            $order_by = 'ORDER BY pc.`'.Configuration::get($this->_name.'blog_catbl_order').'` '.Configuration::get($this->_name.'blog_catbl_ad').'';
        }

        //$order_by = 'ORDER BY pc_d.title asc';

			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;
			
			$sql = '
			SELECT pc.*,
			(select count(*) as count from `'._DB_PREFIX_.'blog_post` pc1
				    LEFT JOIN `'._DB_PREFIX_.'blog_category2post` c2p
				    ON(pc1.id = c2p.post_id)

				    WHERE c2p.category_id = pc.id and pc1.status = 1) as count_posts

			FROM `'._DB_PREFIX_.'blog_category` pc 
			LEFT JOIN `'._DB_PREFIX_.'blog_category_data` pc_d
			ON(pc.id = pc_d.id_item)


			WHERE pc_d.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			AND pc.status = 1


			 '.$order_by.' LIMIT '.(int)$limit;



			$items = Db::getInstance()->ExecuteS($sql);
			$items_tmp = array();
			foreach($items as $k => $_item){
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_category_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				foreach ($items_data as $item_data){
		    		if($current_language == $item_data['id_lang']){
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['title'] = $item_data['title'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['seo_description'] = $item_data['seo_description'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['seo_keywords'] = $item_data['seo_keywords'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['time_add'] = $_item['time_add'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['id'] = $_item['id'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['seo_url'] = $item_data['seo_url'];
                        $items_tmp[$k]['data'][$item_data['id_lang']]['count_posts'] = $_item['count_posts'];
		    		}
		    	}
		    	
			}


			
		return array('categories' => $items_tmp );
	}
	
	public function getRecentsPosts($data=null){
		$is_home = isset($data['is_home'])?$data['is_home']:0;

        $id_category_item_custom_hook = isset($data['id_category'])?$data['id_category']:0;
        $sql_custom_hook = '';
        $sql_custom_hook_left_join = '';


		if($is_home == 1){
			$limit  =(int) Configuration::get($this->_name.'blog_bp_h');
			$posts_block_img_width = Configuration::get($this->_name.'posts_w_h');
            $order_by = 'ORDER BY pc.`'.Configuration::get($this->_name.'blog_postblh_order').'` '.Configuration::get($this->_name.'blog_postblh_ad').'';
		} else {

            if($id_category_item_custom_hook){
                $sql_custom_hook_left_join = 'LEFT JOIN `'._DB_PREFIX_.'blog_category2post` c2p ON(c2p.post_id = pc.id) ';
                $sql_custom_hook = ' AND c2p.category_id = '.(int)$id_category_item_custom_hook;
            }

			$limit  =(int) Configuration::get($this->_name.'blog_bposts');
			$posts_block_img_width = Configuration::get($this->_name.'posts_block_img_width');
            $order_by = 'ORDER BY pc.`'.Configuration::get($this->_name.'blog_postbl_order').'` '.Configuration::get($this->_name.'blog_postbl_ad').'';
		}


		
		
			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;
			
			$sql = '
			SELECT pc.*, ba2c.status as status_author,
			(select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1) as count_like,
		    (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1 and ip = \''.pSQL($_SERVER['REMOTE_ADDR']).'\') as is_liked_post,
		    (select count(*) as count from `'._DB_PREFIX_.'blog_comments` pcc WHERE pcc.id_post = pc.id and pcc.status = 1 and pcc.id_lang = '.(int)$current_language.') as count_comments
			FROM `'._DB_PREFIX_.'blog_post` pc 
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			ON(pc.id = pc_d.id_item)
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			'.$sql_custom_hook_left_join.'
			WHERE pc.status = 1 AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups) AND ba2c.status != 2 AND ba2c.id_shop = '.(int)$this->_id_shop.'

			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			AND pc_d.id_lang = '.(int)$current_language.' '.$sql_custom_hook.'
			'.$order_by.' LIMIT '.(int)$limit;
			
			$items = Db::getInstance()->ExecuteS($sql);
			$items_tmp = array();
			foreach($items as $k => $_item){
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				foreach ($items_data as $item_data){
		    		if($current_language == $item_data['id_lang']){
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['title'] = $item_data['title'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['seo_description'] = $item_data['seo_description'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['seo_keywords'] = $item_data['seo_keywords'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['content'] = $item_data['content'];
		    			
		    			
		    			
		    			
		    			if(Tools::strlen($_item['img'])>0){
		    				$this->generateThumbImages(array('img'=>$_item['img'], 
		    												 'width'=>$posts_block_img_width,
		    												 'height'=>$posts_block_img_width 
		    												)
		    											);
		    				$img = Tools::substr($_item['img'],0,-4)."-".$posts_block_img_width."x".$posts_block_img_width.".jpg";
		    			} else {
		    				$img = $_item['img'];
		    			}
		    			
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['img'] = $img;
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['status'] = $_item['status'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['is_comments'] = $_item['is_comments'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['time_add'] = $_item['time_add'];	
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['id'] = $_item['id'];
		    			$items_tmp[$k]['data'][$item_data['id_lang']]['seo_url'] = $item_data['seo_url'];

                        $items_tmp[$k]['data'][$item_data['id_lang']]['count_like'] = $_item['count_like'];
                        $items_tmp[$k]['data'][$item_data['id_lang']]['is_liked_post'] = $_item['is_liked_post'];
                        $items_tmp[$k]['data'][$item_data['id_lang']]['count_comments'] = $_item['count_comments'];

                        $items_tmp[$k]['data'][$item_data['id_lang']]['count_views'] = $_item['count_views'];
                        $items_tmp[$k]['data'][$item_data['id_lang']]['avg_rating'] = $_item['avg_rating'];

		    		}
		    	}
		    	
			}
			//exit;
			
			
		return array('posts' => $items_tmp );
	} 
	
	
	public function getArchives(){

			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;
			
			$sql = 'SELECT 
						    YEAR(`time_add`) AS YEAR, 
						    MONTH(`time_add`) AS MONTH,
						    COUNT(*) AS TOTAL ,
						    time_add
						FROM  `'._DB_PREFIX_.'blog_post` pc
						LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
						ON(pc.id = pc_d.id_item)
						LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)

						WHERE pc.status = 1 and ba2c.status !=2 AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
						AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups) AND ba2c.id_shop = '.(int)$this->_id_shop.'

						'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
						AND pc_d.id_lang = '.(int)$current_language.'
						GROUP BY YEAR, MONTH';
			
			$items = Db::getInstance()->ExecuteS($sql);
			$items_tmp = array();
			
			
			foreach($items as $_item){
				$year = $_item['YEAR'];
				$month = $_item['MONTH'];
				$total = $_item['TOTAL'];
				$time_add = $_item['time_add'];
			
					
				$items_tmp[$year][] = array('year'=>$year,
									 'month'=>$month,
									 'total' =>$total,
									 'time_add' => $time_add
									);
				
			}
			
		return array('posts' => $items_tmp );
	} 
	
	public function generateThumbImages($data){
		
		$filename = $data['img'];
		$orig_name_img= $data['img'];
		$width = $data['width'];
		$height = $data['height'];
        $is_gallery = isset($data['is_gallery'])?$data['is_gallery']:0;
		
		$filename = Tools::substr($filename,0,-4)."-".$width."x".$height.".jpg";
		
		$name_img = $this->path_img_cloud.$filename;
		
		
		if(@filesize($name_img)==0){
		
		$uniq_name_image_without_ext = current(explode(".",$orig_name_img));
		
		$dir_without_ext = $this->path_img_cloud.($is_gallery?($this->_img_gallery_folder.DIRECTORY_SEPARATOR):'').$uniq_name_image_without_ext;
						
		$this->copyImage(
			array('dir_without_ext'=>$dir_without_ext,
			      'name'=>$this->path_img_cloud.($is_gallery?($this->_img_gallery_folder.DIRECTORY_SEPARATOR):'').$orig_name_img,
				  'width'=>$width,
				  'height'=>$height
				  )
				    );	
		}
		
		
						
	}
	
	public function createRSSFile($post_title,$post_description,$post_link,$post_pubdate,$img,$author,$count_comments)
	{


        $data_url = $this->getSEOURLs();
        $post_url = $data_url['post_url'];

        $post_link = $post_url.$post_link;

        $returnITEM = '';
        include(dirname(__FILE__).'/../views/templates/hooks/'.$this->_name.'/'.__FUNCTION__.'.phtml');
		return $returnITEM;
	}


    public function countItemsForRSS(){
        $sql = '
			SELECT count(*) as count
			FROM `'._DB_PREFIX_.'blog_post` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			on(pc.id = pc_d.id_item)
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)

			WHERE pc.status = 1 and ba2c.status != 2

			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'

			AND pc.is_rss = 1

			';

        $items = Db::getInstance()->getRow($sql);

        return array('count'=>$items['count']);
    }
	
	public function getItemsForRSS($data = null){
			
			$step = Configuration::get($this->_name.'number_rssitems');
			
			$cookie = $this->context->cookie;
			$current_language = isset($data['id_lang'])?$data['id_lang']:(int)$cookie->id_lang;
            $id_shop = isset($data['id_shop'])?$data['id_shop']:$this->_id_shop;
			
			
			$sql = '
			SELECT pc.*,
			 (select count(*) as count from `'._DB_PREFIX_.'blog_comments` c2pc
				 where c2pc.id_post = pc.id and c2pc.status = 1 and c2pc.id_shop = '.(int)$id_shop.'
				 and c2pc.id_lang = '.(int)$current_language.' )
				 as count_comments

			FROM `'._DB_PREFIX_.'blog_post` pc 
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			on(pc.id = pc_d.id_item)
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)

			WHERE pc.status = 1 and ba2c.status != 2 and pc_d.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$id_shop.',pc.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups) AND ba2c.id_shop = '.(int)$this->_id_shop.'

			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'

			AND pc.is_rss = 1

			ORDER BY pc.`time_add` DESC LIMIT '.(int)$step.'';
			
			$items = Db::getInstance()->ExecuteS($sql);	
			
			foreach($items as $k1=>$_item){
				
				if(Tools::strlen($_item['img'])>0){
					$this->generateThumbImages(array('img'=>$_item['img'], 
		    												 'width'=>$this->_img_posts_width,
		    												 'height'=>$this->_img_posts_height
		    												)
		    											);
		    		$img = Tools::substr($_item['img'],0,-4)."-".$this->_img_posts_width."x".$this->_img_posts_height.".jpg";
		    	} else {
		    		$img = $_item['img'];
				}
		    		
		    	$items[$k1]['img'] = $img;
				
				
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');
				
				foreach ($items_data as $item_data){
		    		
		    		if($current_language == $item_data['id_lang']){
		    			$items[$k1]['title'] = $item_data['title'];
		    			$items[$k1]['seo_description'] = htmlspecialchars(strip_tags($item_data['content']));
		    			$items[$k1]['pubdate'] = date('D, d M Y H:i:s +0000',strtotime($_item['time_add']));
		    			if($this->isURLRewriting()){
		    			$items[$k1]['page'] = $item_data['seo_url'];
		    			} else {
		    			$items[$k1]['page'] = $item_data['id_item'];
		    				
		    			}
		    			
		    		} 
		    	}
				
			}
			
			
			
		
			return array('items' => $items);
	}
	
	public function getfacebooklocale()
	{
        $locales = array();

        if (($xml=simplexml_load_file(_PS_MODULE_DIR_ . $this->_name."/lib/facebook_locales.xml")) === false)
            return $locales;

        for ($i=0;$i<sizeof($xml);$i++)
        {
            $locale = $xml->locale[$i]->codes->code->standard->representation;
            $locales[]= $locale;
        }

        return $locales;
	}
	
 	public function getfacebooklib($id_lang){
    	
    	$lang = new Language((int)$id_lang);
		
    	$lng_code = isset($lang->language_code)?$lang->language_code:$lang->iso_code;
    	if(strstr($lng_code, '-')){
			$res = explode('-', $lng_code);
			$language_iso = Tools::strtolower($res[0]).'_'.Tools::strtoupper($res[1]);
			$rss_language_iso = Tools::strtolower($res[0]);
		} else {
			$language_iso = Tools::strtolower($lng_code).'_'.Tools::strtoupper($lng_code);
			$rss_language_iso = $lng_code;
		}
			
			
		if (!in_array($language_iso, $this->getfacebooklocale()))
			$language_iso = "en_US";
		
		if (Configuration::get('PS_SSL_ENABLED') == 1)
			$url = "https://";
		else
			$url = "http://";
		
		
		
		return array('url'=>$url . 'connect.facebook.net/'.$language_iso.'/all.js#xfbml=1',
					  'lng_iso' => $language_iso, 'rss_language_iso' => $rss_language_iso);
    }
    
    private function _getCategoriesForSitemap($_data = null){

        $id_shop = isset($_data['id_shop'])?$_data['id_shop']:$this->_id_shop;

        $id_lang = $_data['id_lang'];

        $data_url = $this->getSEOURLs(array('id_shop'=>$id_shop,'is_sitemap'=>1));

        $category_url = $data_url['category_url'];

    		
			$sql = '
			SELECT pc.*
			FROM `'._DB_PREFIX_.'blog_category` pc 
            WHERE pc.status = 1 AND FIND_IN_SET('.(int)$id_shop.',pc.ids_shops)
			 ORDER BY pc.`time_add` DESC ';


            $i = 0;
			
			$items = Db::getInstance()->ExecuteS($sql);
			$items_tmp = array();
			foreach($items as $_item){
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_category_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].' and pc.id_lang = '.(int)$id_lang.'

				');
				
				foreach ($items_data as $item_data){

                    ## image ###
                    if (Tools::strlen($_item['img']) > 0) {
                        $img = Tools::substr($_item['img'], 0, -4) . ".jpg";
                    } else {
                        $img = $_item['img'];
                    }


                    $items_tmp[$i]['data'][$item_data['id_lang']]['img'] = $img;

                    ## image ###


                    $title = $item_data['title'];
                    $items_tmp[$i]['data'][$item_data['id_lang']]['title'] = $title;


                    $time_add = $_item['time_add'];
                    $items_tmp[$i]['data'][$item_data['id_lang']]['time_add'] = $time_add;



                    if (Configuration::get('PS_REWRITING_SETTINGS')) {
                        $url = $category_url.$item_data['seo_url'];

                    } else {
                        $url = $category_url.$_item['id'];
                    }



                    ### handle language ###
                    if($id_shop) {
                        $all_laguages = Language::getLanguages(true,$id_shop);
                    } else {
                        $all_laguages = Language::getLanguages(true);
                    }

                    if($this->isURLRewriting() && sizeof($all_laguages)>1) {
                        $iso_lang = Language::getIsoById((int)($item_data['id_lang']));

                        $iso_lng_generated = $this->getLangISO();
                        $url = str_replace("/".$iso_lng_generated,"/".$iso_lang."/",$url);
                    }
                    ### handle language ###


                    $items_tmp[$i]['data'][$item_data['id_lang']]['url'] = $url;



		    	}
		    	$i++;
			}
			
			return array('all_categories'=>$items_tmp);
    }
    
    private function _getPostsForSitemap($_data = null){

        $id_shop = isset($_data['id_shop'])?$_data['id_shop']:$this->_id_shop;

        $id_lang = $_data['id_lang'];

        $data_url = $this->getSEOURLs(array('id_shop'=>$id_shop,'is_sitemap'=>1));
        $post_url = $data_url['post_url'];

			
			$sql = '
			SELECT pc.*
			FROM `'._DB_PREFIX_.'blog_post` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)

			WHERE pc.status = 1 AND ba2c.status != 2 AND FIND_IN_SET('.(int)$id_shop.',pc.ids_shops) AND ba2c.id_shop = '.(int)$this->_id_shop.'
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'

			 ORDER BY pc.`time_add` DESC';


			
			$items = Db::getInstance()->ExecuteS($sql);
			$items_tmp = array();
            $i=0;
			foreach($items as $_item){

				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].' and pc.id_lang = '.(int)$id_lang.'
				');



				foreach ($items_data as $item_data){


                    if(Tools::strlen($_item['img'])>0){

                        $img = Tools::substr($_item['img'],0,-4).".jpg";
                    } else {
                        $img = $_item['img'];
                    }

                    $items_tmp[$i]['data'][$item_data['id_lang']]['img'] = $img;


                    $title = $item_data['title'];
                    $items_tmp[$i]['data'][$item_data['id_lang']]['title'] = $title;


                    $time_add = $_item['time_add'];
                    $items_tmp[$i]['data'][$item_data['id_lang']]['time_add'] = $time_add;




                    if (Configuration::get('PS_REWRITING_SETTINGS')) {
                        $url = $post_url.$item_data['seo_url'];

                    } else {
                        $url = $post_url.$_item['id'];
                    }


                    ### handle language ###
                    /*if($id_shop) {
                        $all_laguages = Language::getLanguages(true,$id_shop);
                    } else {
                        $all_laguages = Language::getLanguages(true);
                    }

                    if($this->isURLRewriting() && sizeof($all_laguages)>1) {
                        $iso_lang = Language::getIsoById((int)($item_data['id_lang']));

                        $iso_lng_generated = $this->getLangISO();
                        $url = str_replace("/".$iso_lng_generated,"/".$iso_lang."/",$url);
                    }*/

                    $url = $this->handleLanguagesForSitemap(array('url'=>$url,'id_lang' => $id_lang,'id_shop' => $id_shop));

                    ### handle language ###


                    $items_tmp[$i]['data'][$item_data['id_lang']]['url'] = $url;





		    	}
                $i++;
		    	
			}

			
		return array('all_posts' => $items_tmp );
	} 

    public function handleLanguagesForSitemap($data){
        $id_shop = $data['id_shop'];
        $id_lang = $data['id_lang'];
        $url = $data['url'];

        ### handle language ###
        if($id_shop) {
            $all_laguages = Language::getLanguages(true,$id_shop);
        } else {
            $all_laguages = Language::getLanguages(true);
        }

        if($this->isURLRewriting() && sizeof($all_laguages)>1) {
            $iso_lang = Language::getIsoById((int)($id_lang));

            $iso_lng_generated = $this->getLangISO();
            $url = str_replace("/".$iso_lng_generated,"/".$iso_lang."/",$url);
        }
        ### handle language ###


        return $url;
    }
    
    public function generateSitemap()
    {

        include_once(_PS_MODULE_DIR_ . $this->_name . '/blockblog.php');
        $obj_blockblog = new blockblog();


        /*$xmls_urls = array_chunk($all_posts, (int)Configuration::get($this->_name.'sitemap_limit'));

            foreach ($xmls_urls as $xml_key => $xml_urls) {*/


        $all_shops = Shop::getShops();
        foreach ($all_shops as $_shop) {

            $id_shop = (int)$_shop['id_shop'];

            $all_laguages = Language::getLanguages(true, $id_shop);


            foreach ($all_laguages as $item_lng) {

                $id_lang = $item_lng['id_lang'];
                $iso_code = $item_lng['iso_code'];


                $filename = $this->path_img_cloud . "blog_" . $iso_code . "_" . $id_shop . ".xml";

                //if(@filesize($filename)==0){
                $new_sitemap = '<?xml version="1.0" encoding="UTF-8"?>
									<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

									</urlset>';
                file_put_contents($filename, $new_sitemap);
                //}
                $xml = simplexml_load_file($filename);
                unset($xml->url);
                $sxe = new SimpleXMLElement($xml->asXML());



                $data_url = $this->getSEOURLs(array('id_lang' => $id_lang, 'id_shop' => $id_shop));




                // all categories page //
                if (Configuration::get($this->_name . 'pages_blog_allcat') == 1) {

                    $all_categories_data = $this->_getCategoriesForSitemap(array('id_shop' => $id_shop, 'id_lang' => $id_lang, 'iso_code' => $iso_code));
                    $all_categories = $all_categories_data['all_categories'];

                    if(count($all_categories)>0) {
                        $categories_url = $data_url['categories_url'];
                        $categories_url = $this->handleLanguagesForSitemap(array('url'=>$categories_url,'id_lang' => $id_lang,'id_shop' => $id_shop));

                        $url = $sxe->addChild('url');
                        $url->addChild('loc', $categories_url);
                        $url->addChild('priority', '0.6');
                        $url->addChild('changefreq', 'monthly');
                        $url->addChild('lastmod', date('c'));
                    }

                }
                // all categories page //

                // blog category
                if (Configuration::get($this->_name . 'pages_blog_cat') == 1) {

                    $all_categories_data = $this->_getCategoriesForSitemap(array('id_shop' => $id_shop, 'id_lang' => $id_lang, 'iso_code' => $iso_code));
                    $all_categories = $all_categories_data['all_categories'];
                    //echo "<pre>"; var_dump($all_categories); exit;
                    foreach ($all_categories as $cat) {

                        foreach ($cat['data'] as $item_cat) {
                            $catlink = $item_cat['url'];
                            $catlink = str_replace('&', '&amp;', $catlink);

                            $time_add = $item_cat['time_add'];


                            $url = $sxe->addChild('url');
                            $url->addChild('loc', $catlink);
                            $url->addChild('priority', '0.6');
                            $url->addChild('changefreq', 'monthly');
                            $url->addChild('lastmod', date('c', strtotime($time_add)));


                            $img = $item_cat['img'];
                            if (Tools::strlen($img) > 0) {

                                $title = $item_cat['title'];

                                $img = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . $obj_blockblog->getCloudImgPath() . $img;

                                $image = $url->addChild('image:image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
                                $image->addChild('image:loc', $img, 'http://www.google.com/schemas/sitemap-image/1.1');
                                $image->addChild('image:caption', $title, 'http://www.google.com/schemas/sitemap-image/1.1');
                                $image->addChild('image:title', $title, 'http://www.google.com/schemas/sitemap-image/1.1');

                            }
                        }
                    }
                }
                // blog category


                // blog main page //
                if (Configuration::get($this->_name . 'pages_main_blog') == 1) {

                    $all_posts_data = $this->_getPostsForSitemap(array('id_shop' => $id_shop, 'id_lang' => $id_lang, 'iso_code' => $iso_code));
                    $all_posts = $all_posts_data['all_posts'];

                    if(count($all_posts)>0) {
                        $posts_url = $data_url['posts_url'];
                        $posts_url = $this->handleLanguagesForSitemap(array('url'=>$posts_url,'id_lang' => $id_lang,'id_shop' => $id_shop));

                        $url = $sxe->addChild('url');
                        $url->addChild('loc', $posts_url);
                        $url->addChild('priority', '0.6');
                        $url->addChild('changefreq', 'monthly');
                        $url->addChild('lastmod', date('c'));
                    }

                }
                // blog main page //


                // blog posts
                if (Configuration::get($this->_name . 'pages_blog_post') == 1) {

                    $all_posts_data = $this->_getPostsForSitemap(array('id_shop' => $id_shop, 'id_lang' => $id_lang, 'iso_code' => $iso_code));
                    $all_posts = $all_posts_data['all_posts'];


                    foreach ($all_posts as $post) {

                        foreach ($post['data'] as $item_post) {
                            //echo "<pre>"; var_dump($item_post);exit;

                            $postlink = $item_post['url'];
                            $postlink = str_replace('&', '&amp;', $postlink);

                            $time_add = $item_post['time_add'];

                            $url = $sxe->addChild('url');
                            $url->addChild('loc', $postlink);
                            $url->addChild('priority', '0.9');
                            $url->addChild('changefreq', 'weekly');
                            $url->addChild('lastmod', date('c', strtotime($time_add)));


                            $img = $item_post['img'];
                            if (Tools::strlen($img) > 0) {

                                $title = $item_post['title'];

                                $img = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . $obj_blockblog->getCloudImgPath() . $img;

                                $image = $url->addChild('image:image', null, 'http://www.google.com/schemas/sitemap-image/1.1');
                                $image->addChild('image:loc', $img, 'http://www.google.com/schemas/sitemap-image/1.1');
                                $image->addChild('image:caption', $title, 'http://www.google.com/schemas/sitemap-image/1.1');
                                $image->addChild('image:title', $title, 'http://www.google.com/schemas/sitemap-image/1.1');

                            }
                        }
                    }


                }
                // blog posts


                // all comments page //
                if (Configuration::get($this->_name . 'pages_allcom') == 1) {

                    $_data_com = $this->getLastComments(array('is_page'=>1,'step'=>10,'start'=>0, 'id_shop' => $id_shop, 'id_lang' => $id_lang));
                    $all_comments = $_data_com['comments'];

                    if(count($all_comments)>0) {
                        $comments_url = $data_url['comments_url'];
                        $comments_url = $this->handleLanguagesForSitemap(array('url'=>$comments_url,'id_lang' => $id_lang,'id_shop' => $id_shop));

                        $url = $sxe->addChild('url');
                        $url->addChild('loc', $comments_url);
                        $url->addChild('priority', '0.6');
                        $url->addChild('changefreq', 'monthly');
                        $url->addChild('lastmod', date('c'));
                    }

                }
                // all comments page //


                // all authors page //
                if (Configuration::get($this->_name . 'pages_authors') == 1) {

                    include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
                    $obj_userprofile = new userprofileblockblog();

                    $info_customers = $obj_userprofile->getShoppersList(array('start' => 0,'step'=>20,'is_search'=>0,'search'=>null,'id_lang' => $id_lang,'id_shop' => $id_shop));

                    if($info_customers['data_count_customers']>0) {
                        $authors_url = $data_url['authors_url'];
                        $authors_url = $this->handleLanguagesForSitemap(array('url'=>$authors_url,'id_lang' => $id_lang,'id_shop' => $id_shop));

                        $url = $sxe->addChild('url');
                        $url->addChild('loc', $authors_url);
                        $url->addChild('priority', '0.6');
                        $url->addChild('changefreq', 'monthly');
                        $url->addChild('lastmod', date('c'));
                    }

                }
                // all authors page //

                // author page
                if (Configuration::get($this->_name . 'pages_author') == 1) {

                    include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
                    $obj_userprofile = new userprofileblockblog();

                    $info_customers = $obj_userprofile->getShoppersList(array('start' => 0,'step'=>20,'is_search'=>0,'search'=>null,'id_lang' => $id_lang,'id_shop' => $id_shop));

                    $info_customers = $obj_userprofile->getShoppersList(array('start' => 0,'step'=>$info_customers['data_count_customers']+1,'is_search'=>0,'search'=>null,'id_lang' => $id_lang,'id_shop' => $id_shop));

                    foreach ($info_customers['customers'] as $author) {


                        $author_url = $data_url['author_url'];
                        $author_url = $this->handleLanguagesForSitemap(array('url' => $author_url, 'id_lang' => $id_lang, 'id_shop' => $id_shop));
                        $author_url = $author_url.$author['id_customer']."-".$author['customer_name'];

                        $url = $sxe->addChild('url');
                        $url->addChild('loc', $author_url);
                        $url->addChild('priority', '0.9');
                        $url->addChild('changefreq', 'monthly');
                        $url->addChild('lastmod', date('c'));
                    }
                }
                // author page


                // all tags page //
                if (Configuration::get($this->_name . 'pages_alltags') == 1) {

                    $tags_page = $this->getTags(array('id_lang' => $id_lang,'id_shop' => $id_shop));

                    if(count($tags_page)>0) {
                        $tags_url = $data_url['tags_url'];
                        $tags_url = $this->handleLanguagesForSitemap(array('url' => $tags_url, 'id_lang' => $id_lang, 'id_shop' => $id_shop));

                        $url = $sxe->addChild('url');
                        $url->addChild('loc', $tags_url);
                        $url->addChild('priority', '0.6');
                        $url->addChild('changefreq', 'monthly');
                        $url->addChild('lastmod', date('c'));
                    }

                }
                // all tags page //

                // tag page
                if (Configuration::get($this->_name . 'pages_tag') == 1) {

                    $tags_page = $this->getTags(array('id_lang' => $id_lang,'id_shop' => $id_shop));

                    foreach ($tags_page as $tag) {


                            $tag_url = $data_url['tag_url'];
                            $tag_url = $this->handleLanguagesForSitemap(array('url' => $tag_url, 'id_lang' => $id_lang, 'id_shop' => $id_shop));
                            $tag_url = $tag_url.$tag['query'];

                            $url = $sxe->addChild('url');
                            $url->addChild('loc', $tag_url);
                            $url->addChild('priority', '0.9');
                            $url->addChild('changefreq', 'monthly');
                            $url->addChild('lastmod', date('c'));
                    }
                }
                // tag page


                // gallery page //
                if (Configuration::get($this->_name . 'pages_gallery') == 1) {

                    $_data = $this->getGallery(array('start'=>0,'step'=>10, 'id_lang' => $id_lang, 'id_shop' => $id_shop));

                    if($_data['count_all']>0) {

                        $gallery_url = $data_url['gallery_url'];
                        $gallery_url = $this->handleLanguagesForSitemap(array('url' => $gallery_url, 'id_lang' => $id_lang, 'id_shop' => $id_shop));

                        $url = $sxe->addChild('url');
                        $url->addChild('loc', $gallery_url);
                        $url->addChild('priority', '0.6');
                        $url->addChild('changefreq', 'monthly');
                        $url->addChild('lastmod', date('c'));
                    }

                }
                // gallery page //



                if (count($sxe) > 0) {
                    $sxe->asXML($filename);
                } else {
                    @unlink($filename);
                }

            }


        }


        ## generate main blog sitemap ##

        $all_shops = Shop::getShops();

        foreach($all_shops as $_shop) {

            $id_shop = (int)$_shop['id_shop'];

            $filename = $this->path_img_cloud . "blog_" . $id_shop . ".xml";
            $xml_file = '<?xml version="1.0" encoding="UTF-8" ?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>';

            file_put_contents($filename, $xml_file);

            $xml = simplexml_load_file($filename);
            unset($xml->url);
            $xml_feed = new SimpleXMLElement($xml->asXML());


            $all_laguages = Language::getLanguages(true,$id_shop);

            foreach($all_laguages as $item_lng) {

                $iso_code = $item_lng['iso_code'];

//                if ($this->_is_cloud) {
//                    $path = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/' . $this->_name . '/upload/blog_' . $iso_code . '_' . $id_shop . '.xml';
//                    $is_exists = _PS_MODULE_DIR_."..".DIRECTORY_SEPARATOR.'modules/' . $this->_name . '/upload/blog_' . $iso_code . '_' . $id_shop . '.xml';
//                }else{
//                    $path = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'upload/' . $this->_name . '/blog_' .$iso_code .'_'. $id_shop . '.xml';
//                    $is_exists = _PS_MODULE_DIR_."..".DIRECTORY_SEPARATOR.'upload/' . $this->_name . '/blog_' .$iso_code .'_'. $id_shop . '.xml';
//                }
                $path = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/' . $this->_name . '/upload/blog_' . $iso_code . '_' . $id_shop . '.xml';
                $is_exists = _PS_MODULE_DIR_."..".DIRECTORY_SEPARATOR.'modules/' . $this->_name . '/upload/blog_' . $iso_code . '_' . $id_shop . '.xml';



                if(!@file_exists($is_exists)){
                    continue;
                } else {

                    $sitemap = $xml_feed->addChild('sitemap');
                    $sitemap->addAttribute('lang', $iso_code);
                    $sitemap->addAttribute('type', 'text/html');
                    $sitemap->addAttribute('charset', 'UTF-8');

                    $sitemap->addChild('loc', $path);
                    $sitemap->addChild('lastmod', date('c'));
                }


                if (count($xml_feed) > 0) {
                    $xml_feed->asXML($filename);
                } else {
                    @unlink($filename);
                }

            }

        }


        ## generate main blog sitemap ##


    //}
             
    }
    
	public function getLangISO(){


        $cookie = $this->context->cookie;
        $id_lang = (int)$cookie->id_lang;

        if($this->_id_shop) {
            $all_laguages = Language::getLanguages(true,$this->_id_shop);
        } else {
            $all_laguages = Language::getLanguages(true);
        }


        if($this->isURLRewriting() && sizeof($all_laguages)>1)
            $iso_lang = Language::getIsoById((int)($id_lang))."/";
        else
            $iso_lang = '';

        return $iso_lang;


    }
    
    public function isURLRewriting(){
    	$_is_rewriting_settings = 0;
    	if(Configuration::get('PS_REWRITING_SETTINGS') && Configuration::get($this->_name.'urlrewrite_on') == 1){
			$_is_rewriting_settings = 1;
		} 
		return $_is_rewriting_settings;
    }
	
    
	public function getProducts($related_products) {

		$context = Context::getContext();
        $id_lang = $context->language->id;

        $query = 'SELECT distinct p.`id_product`, p.`reference`, pl.`name`, 
                    pl.`description_short`, pl.`link_rewrite`
                    FROM  `' . _DB_PREFIX_ . 'product` p 
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
                            p.`id_product` = pl.`id_product`
                            AND pl.`id_lang` = ' . (int) $id_lang . '
                    )
                    
                    WHERE p.`id_product` IN('.pSQL($related_products).')';

            $query .= ' AND p.`active` = 1 ';

        $query .= ' ORDER BY pl.`name` DESC';
        return Db::getInstance()->executeS($query);
    }
    
    
public function getRelatedPosts($_data){
		$admin = isset($_data['admin'])?$_data['admin']:null;
		$items = array();
		if($admin){
			$id = isset($_data['id'])?$_data['id']:0;
			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;
			
			
			$sql = '
			SELECT pc.* 
			FROM `'._DB_PREFIX_.'blog_post` pc 
			WHERE id != '.(int)$id.'
			ORDER BY pc.`time_add` DESC';
			
			
			$posts = Db::getInstance()->ExecuteS($sql);
			
			
			foreach($posts as $k => $_item){
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');


                if(Tools::strlen($posts[$k]['img'])>0){
                    $items[$k]['img_orig'] = $posts[$k]['img'];
                    $this->generateThumbImages(array('img'=>$posts[$k]['img'],
                            'width'=>Configuration::get($this->_name.'rp_img_width'),
                            'height'=>Configuration::get($this->_name.'rp_img_width')
                        )
                    );
                    $img = Tools::substr($posts[$k]['img'],0,-4)."-".Configuration::get($this->_name.'rp_img_width')."x".Configuration::get($this->_name.'rp_img_width').".jpg";
                } else {
                    $img = $posts[$k]['img'];
                }

                $items[$k]['img'] = $img;


                $items[$k]['status'] = $posts[$k]['status'];

				$tmp_title = '';
				$tmp_id = '';
				$tmp_time_add = '';

				// languages
				$languages_tmp_array = array();
				
				foreach ($items_data as $item_data){
					$languages_tmp_array[] = $item_data['id_lang'];
		    		
		    		$title = isset($item_data['title'])?$item_data['title']:'';
		    		$id = isset($item_data['id_item'])?$item_data['id_item']:'';
		    		$time_add = isset($posts[$k]['time_add'])?$posts[$k]['time_add']:'';
		    		
		    		if(Tools::strlen($tmp_title)==0){
		    			if(Tools::strlen($title)>0)
		    					$tmp_title = $title; 
		    		}
		    		
					if(Tools::strlen($tmp_id)==0){
		    			if(Tools::strlen($id)>0)
		    					$tmp_id = $id; 
		    		}
		    		
					if(Tools::strlen($tmp_time_add)==0){
		    			if(Tools::strlen($time_add)>0)
		    					$tmp_time_add = $time_add; 
		    		}
		    		
		    		if($current_language == $item_data['id_lang']){
		    			$items[$k]['title'] = $item_data['title'];
		    			$items[$k]['seo_url'] = $item_data['seo_url'];
		    			$items[$k]['id'] = $id;
		    			$items[$k]['time_add'] = $time_add;
		    		}
		    		
		    	}
		    	
		    	if(@Tools::strlen($items[$k]['title'])==0)
		    		$items[$k]['title'] = $tmp_title;
		    		
		    	if(@Tools::strlen($items[$k]['id'])==0)
		    		$items[$k]['id'] = $tmp_id;
		    		
		    	if(@Tools::strlen($items[$k]['time_add'])==0)
		    		$items[$k]['time_add'] = $tmp_time_add;
		    	
		    	
		    	// languages
		    	$items[$k]['ids_lng'] = $languages_tmp_array;


                $lang_for_category = array();
                foreach($languages_tmp_array as $lng_id){
                    $data_lng = Language::getLanguage($lng_id);
                    $lang_for_category[] = $data_lng['iso_code'];
                }
                $lang_for_category = implode(",",$lang_for_category);

                $items[$k]['iso_lang'] = $lang_for_category;
			}
			
			$data_count_related_posts = Db::getInstance()->getRow('
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_post` WHERE status = 1 
			');
			
		} else {
			
			$cookie = $this->context->cookie;
			$current_language = (int)$cookie->id_lang;
			$related_data = $_data['related_data'];


            $order_by = 'ORDER BY pc.`'.Configuration::get($this->_name.'blog_rp_order').'` '.Configuration::get($this->_name.'blog_rp_ad').'';
			
			$sql = '
			SELECT pc.*, ba2c.status as status_author,
			 (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1) as count_like,
                (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1 and ip = \''.pSQL($_SERVER['REMOTE_ADDR']).'\') as is_liked_post,
                (select count(*) as count from `'._DB_PREFIX_.'blog_comments` pcc WHERE pcc.id_post = pc.id and pcc.status = 1 and pcc.id_lang = '.(int)$current_language.') as count_comments
			FROM `'._DB_PREFIX_.'blog_post` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE pc.status = 1 AND ba2c.status != 2
			AND pc.id IN('.pSQL($related_data).')
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops) AND ba2c.id_shop = '.(int)$this->_id_shop.'
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'

			'.$order_by.'';
			
			$posts = Db::getInstance()->ExecuteS($sql);
			
			
			foreach($posts as $k => $_item){
				$items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].' AND id_lang='.(int)$current_language.'
				');


                if(Tools::strlen($posts[$k]['img'])>0){
                    $items[$k]['img_orig'] = $posts[$k]['img'];
                    $this->generateThumbImages(array('img'=>$posts[$k]['img'],
                            'width'=>Configuration::get($this->_name.'rp_img_width'),
                            'height'=>Configuration::get($this->_name.'rp_img_width')
                        )
                    );
                    $img = Tools::substr($posts[$k]['img'],0,-4)."-".Configuration::get($this->_name.'rp_img_width')."x".Configuration::get($this->_name.'rp_img_width').".jpg";
                } else {
                    $img = $posts[$k]['img'];
                }

                $items[$k]['img'] = $img;
				
				$tmp_title = '';
				$tmp_id = '';
				$tmp_time_add = '';

				
				foreach ($items_data as $item_data){
					
		    		$title = isset($item_data['title'])?$item_data['title']:'';
		    		$id = isset($item_data['id_item'])?$item_data['id_item']:'';
		    		$time_add = isset($posts[$k]['time_add'])?$posts[$k]['time_add']:'';
		    		
		    		if(Tools::strlen($tmp_title)==0){
		    			if(Tools::strlen($title)>0)
		    					$tmp_title = $title; 
		    		}
		    		
					if(Tools::strlen($tmp_id)==0){
		    			if(Tools::strlen($id)>0)
		    					$tmp_id = $id; 
		    		}
		    		
					if(Tools::strlen($tmp_time_add)==0){
		    			if(Tools::strlen($time_add)>0)
		    					$tmp_time_add = $time_add; 
		    		}
		    		
		    		if($current_language == $item_data['id_lang']){
		    			$items[$k]['title'] = $item_data['title'];
		    			$items[$k]['seo_url'] = $item_data['seo_url'];
		    			$items[$k]['id'] = $id;
		    			$items[$k]['time_add'] = $time_add;
		    		}
		    		
		    	}
		    	
		    	if(@Tools::strlen($items[$k]['title'])==0)
		    		$items[$k]['title'] = $tmp_title;
		    		
		    	if(@Tools::strlen($items[$k]['id'])==0)
		    		$items[$k]['id'] = $tmp_id;
		    		
		    	if(@Tools::strlen($items[$k]['time_add'])==0)
		    		$items[$k]['time_add'] = $tmp_time_add;

                $items[$k]['count_like'] = $posts[$k]['count_like'];
                $items[$k]['is_liked_post'] = $posts[$k]['is_liked_post'];
                $items[$k]['count_comments'] = $posts[$k]['count_comments'];
                $items[$k]['count_views'] = $posts[$k]['count_views'];
                $items[$k]['is_comments'] = $posts[$k]['is_comments'];
                $items[$k]['avg_rating'] = $posts[$k]['avg_rating'];


		    	
		    	
		  	}
			
			$data_count_related_posts = Db::getInstance()->getRow('
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_post` pc
			 LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE pc.status = 1 and ba2c.status != 2  AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops) AND ba2c.id_shop = '.(int)$this->_id_shop.'
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			');	
		}
		
		
		return array('related_posts' => $items, 'count_all' => $data_count_related_posts['count'] );
	}
	
	

    
    public function getSiteURL($id_shop = null){
    	$http_host = $this->_http_host;
    	$uri = '';
    	if(Tools::strlen($id_shop)!=0){
    		$shops = Shop::getShops(false);
			foreach($shops as $shop){
				if($id_shop == $shop['id_shop']){
					$uri = $shop['uri'];
					$uri = Tools::substr($uri, 1);
					break;
				}
			}
    	}
				
    	return $http_host.$uri;
    }
    
    
	public function productData($data){
		$product = $data['product'];
        $is_related_products = isset($data['is_related_products'])?$data['is_related_products']:0;
		
		
		if(is_object($product) && !empty($product->id)){
			
		$cookie = $this->context->cookie;
		$id_lang = (int)($cookie->id_lang);	
		
			/* Product URL */
			$link = Context::getContext()->link;

            $id_shop = $this->_id_shop;

			$category = new Category((int)($product->id_category_default), $id_lang,$id_shop);

            if (version_compare(_PS_VERSION_, '1.5.5', '>=')) {

                   $product_url = $link->getProductLink((int)$product->id, null, null, null,
                    									 $id_lang, $id_shop, 0, true);


             }
             elseif (version_compare(_PS_VERSION_, '1.5', '>')) {


               if (Configuration::get('PS_REWRITING_SETTINGS')) {
                     $product_url = $link->getProductLink((int)$product->id, null, null, null, 
                     									 $id_lang, $id_shop, 0, true);
               }
                else {
                    $product_url = $link->getProductLink((int)$product->id, null, null, null, 
                     									 $id_lang, $id_shop, 0, false);
                 }
            }
            else {
                  $product_url = $link->getProductLink((int)$product->id, @$product->link_rewrite,
                 									 $category->link_rewrite, $product->ean13, $id_lang);
            }
            
            

			/* Image */
			$image = Image::getCover((int)($product->id));

			if ($image)
			{

                if($is_related_products){
                    $type_img = Configuration::get($this->_name.'img_size_rp');
                } else {
                    $available_types = ImageType::getImagesTypes('products');

                    foreach ($available_types as $type) {
                        $width = $type['width'];

                            if ($width < 400) {
                                $type_img = $type['name'];
                                break;
                            }

                    }
                }
					$image_link = $link->getImageLink(@$product->link_rewrite[$id_lang], (int)($product->id).'-'.(int)($image['id_image']),$type_img);
			

			}
			else
			{
				$image_link = false;
				
			}
			
			}else {
				$image_link= false;
				$product_url = false;
			}
            
            return array('product_url'=>$product_url,'image_link'=>$image_link);
	}
	
	public function getRelatedProducts($data){
		$cookie = $this->context->cookie;
		$id_lang = (int)($cookie->id_lang);
		
		$related_data = explode(",",$data['related_data']);
		
		$data_products = array();
		
		foreach($related_data as $_product_id){
			
		if($_product_id != 0){	
			$_obj_product = new Product($_product_id,false,$id_lang, $this->_id_shop);
	    	
	    	$data_product = $this->productData(array('product'=>$_obj_product,'is_related_products'=>1));
			
	    	$picture = $data_product['image_link'];
			$product_url = $data_product['product_url'];
	    		
	    	$productname = ($_obj_product->name);
	    	
	    	$desc = ($_obj_product->description_short != "") ? $_obj_product->description_short : $_obj_product->description;
	    	$desc = (strip_tags($desc));
			
	    	$data_products[] = array('title'=>$productname,'description'=>$desc,'picture'=>$picture,'product_url'=>$product_url);
		}
		
		}
		return $data_products;
	}
	
	public function getRelatedPostsForPost($data){
		
			$related_data = $data['related_data'];
			$post_id = $data['post_id'];
		
			$data_rel_posts  = $this->getRelatedPosts(array('id'=>$post_id,'related_data'=>$related_data)); 
				
			$data_posts = array();
			
			foreach($data_rel_posts['related_posts'] as $_item){
				$name = isset($_item['title'])?$_item['title']:'';
				$id_post = isset($_item['id'])?$_item['id']:'';
				$seo_url = isset($_item['seo_url'])?$_item['seo_url']:'';
                $count_like = $_item['count_like'];
                $is_liked_post = $_item['is_liked_post'];
                $count_comments = $_item['count_comments'];
                $time_add = $_item['time_add'];
                $img = $_item['img'];
                $count_views = $_item['count_views'];
                $is_comments = $_item['is_comments'];
                $avg_rating = $_item['avg_rating'];


				$data_posts[] = array('title'=>$name,'seo_url'=>$seo_url,'id'=>$id_post,
                                      'count_like'=>$count_like,
                                      'is_liked_post'=>$is_liked_post,
                                      'count_comments'=>$count_comments,
                                      'count_views'=>$count_views,
                                       'time_add'=>$time_add, 'img'=>$img,
                                      'is_comments'=>$is_comments,
                                        'avg_rating'=>$avg_rating
                                    );
		       
			}
		
		return $data_posts;
	}




    public function getRelatedPostsForProduct($_data){

            $items = array();

            $cookie = $this->context->cookie;
            $current_language = (int)$cookie->id_lang;
            $id_product = $_data['id_product'];


            $limit  = (int)Configuration::get($this->_name.'blog_relposts');


            $order_by = 'ORDER BY pc.`'.Configuration::get($this->_name.'blog_postbl_order').'` '.Configuration::get($this->_name.'blog_postbl_ad').'';

            $sql = '
			SELECT pc.*, ba2c.status as status_author,
			 (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1) as count_like,
                (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1 and ip = \''.pSQL($_SERVER['REMOTE_ADDR']).'\') as is_liked_post,
                (select count(*) as count from `'._DB_PREFIX_.'blog_comments` pcc WHERE pcc.id_post = pc.id and pcc.status = 1 and pcc.id_lang = '.(int)$current_language.') as count_comments
			FROM `'._DB_PREFIX_.'blog_post` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE pc.status = 1 and ba2c.status != 2
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			AND FIND_IN_SET('.(int)$id_product.',pc.related_products) AND ba2c.id_shop = '.(int)$this->_id_shop.'
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'

			'.$order_by.' LIMIT '.(int)$limit;



            $posts = Db::getInstance()->ExecuteS($sql);


            foreach($posts as $k => $_item){
                $items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].' AND id_lang='.(int)$current_language.'
				');


                if(Tools::strlen($posts[$k]['img'])>0){
                    $items[$k]['img_orig'] = $posts[$k]['img'];
                    $this->generateThumbImages(array('img'=>$posts[$k]['img'],
                            'width'=>Configuration::get($this->_name.'rp_img_widthp'),
                            'height'=>Configuration::get($this->_name.'rp_img_widthp')
                        )
                    );
                    $img = Tools::substr($posts[$k]['img'],0,-4)."-".Configuration::get($this->_name.'rp_img_widthp')."x".Configuration::get($this->_name.'rp_img_widthp').".jpg";
                } else {
                    $img = $posts[$k]['img'];
                }

                $items[$k]['img'] = $img;

                $tmp_title = '';
                $tmp_id = '';
                $tmp_time_add = '';


                foreach ($items_data as $item_data){

                    $title = isset($item_data['title'])?$item_data['title']:'';
                    $id = isset($item_data['id_item'])?$item_data['id_item']:'';
                    $time_add = isset($posts[$k]['time_add'])?$posts[$k]['time_add']:'';

                    if(Tools::strlen($tmp_title)==0){
                        if(Tools::strlen($title)>0)
                            $tmp_title = $title;
                    }

                    if(Tools::strlen($tmp_id)==0){
                        if(Tools::strlen($id)>0)
                            $tmp_id = $id;
                    }

                    if(Tools::strlen($tmp_time_add)==0){
                        if(Tools::strlen($time_add)>0)
                            $tmp_time_add = $time_add;
                    }

                    if($current_language == $item_data['id_lang']){
                        $items[$k]['title'] = $item_data['title'];
                        $items[$k]['seo_url'] = $item_data['seo_url'];
                        $items[$k]['id'] = $id;
                        $items[$k]['time_add'] = $time_add;
                    }

                }

                if(@Tools::strlen($items[$k]['title'])==0)
                    $items[$k]['title'] = $tmp_title;

                if(@Tools::strlen($items[$k]['id'])==0)
                    $items[$k]['id'] = $tmp_id;

                if(@Tools::strlen($items[$k]['time_add'])==0)
                    $items[$k]['time_add'] = $tmp_time_add;

                $items[$k]['count_like'] = $posts[$k]['count_like'];
                $items[$k]['is_liked_post'] = $posts[$k]['is_liked_post'];
                $items[$k]['count_comments'] = $posts[$k]['count_comments'];
                $items[$k]['count_views'] = $posts[$k]['count_views'];
                $items[$k]['is_comments'] = $posts[$k]['is_comments'];
                $items[$k]['avg_rating'] = $posts[$k]['avg_rating'];




            }

            $sql_count = '
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_post` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE pc.status = 1 and ba2c.status != 2 AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops) AND ba2c.id_shop = '.(int)$this->_id_shop.'
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			AND FIND_IN_SET('.(int)$id_product.',pc.related_products)
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			';



            $data_count_related_posts = Db::getInstance()->getRow($sql_count);



        return array('related_posts' => $items, 'count_all' => $data_count_related_posts['count'] );
    }
	
	public function getLastComments($data = null){
			$cookie = $this->context->cookie;
			$current_language = isset($data['id_lang'])?$data['id_lang']:(int)$cookie->id_lang;
		    $id_shop = isset($data['id_shop'])?$data['id_shop']:$this->_id_shop;

			$is_page = isset($data['is_page'])?$data['is_page']:0;
			$count_all = 0;
			
			if($is_page ==1){
				$start = $data['start'];
				$step = $data['step'];


                $sql = '
				SELECT  ba2c.status as status_author, pc.*, pc.id_post as post_id, pc1.seo_url as post_seo_url, pc1.title as post_title
				FROM `'._DB_PREFIX_.'blog_comments` pc
				LEFT JOIN `'._DB_PREFIX_.'blog_post` pc2 ON(pc.id_post = pc2.id)
				LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc1 ON(pc1.id_item = pc2.id)
                LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc2.author_id = ba2c.id_customer)

				WHERE pc.status = 1 AND ba2c.status != 2 and ba2c.id_shop = '.(int)$id_shop.'
				and pc.id_lang = '.(int)$current_language.'
				and pc.id_shop = '.(int)$id_shop.' AND

				pc1.id_lang = '.(int)$current_language.'
			    AND FIND_IN_SET('.(int)$id_shop.',pc2.ids_shops)
			    AND FIND_IN_SET('.(int)$this->_id_group.',pc2.ids_groups)
			    AND pc2.status = 1
			    '.(($this->_is_show_now)?'AND (pc2.`publish_date` <= NOW())':'').'
				ORDER BY pc.`time_add` DESC LIMIT '.(int)$start.' ,'.(int)$step.'';



                $comments = Db::getInstance()->ExecuteS($sql);


                include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
                $obj_userprofileblockblog = new userprofileblockblog();


                foreach ($comments as $k => $item_data){


                    ## author ##
                    $author_id = $item_data['id_customer'];

                    if($author_id != 0) {



                        $sql = 'select ba2c.status from `'._DB_PREFIX_.'blog_avatar2customer` ba2c WHERE ba2c.id_customer = '.(int)$author_id.' and ba2c.id_shop = '.(int)$id_shop.'';
                        $status_author_data = Db::getInstance()->getRow($sql);
                        $status_author = $status_author_data['status'];


                        $info_path = $this->getAvatarPath(array('id_customer' => $author_id,'id_shop'=>$id_shop,'status_author'=>$status_author));

                        //var_dump($author_id);var_dump($info_path);exit;
                        $comments[$k]['avatar'] = $info_path['avatar'];
                        $comments[$k]['is_show_ava'] = $info_path['is_show'];
                        $comments[$k]['author_id'] = $author_id;

                        $comments[$k]['status_author'] = $status_author;

                        $customer_data = $obj_userprofileblockblog->getInfoAboutCustomer(array('id_customer' => $author_id, 'is_full' => 1));
                        $name_customer = $customer_data['customer_name'];


                        $comments[$k]['author'] = $name_customer;

                    } else {
                        $comments[$k]['avatar'] = '';
                        $comments[$k]['is_show_ava'] = 0;
                        $comments[$k]['author_id'] = 0;
                        $comments[$k]['author'] = $item_data['name'];
                    }
                    ## author ##

                }


                $data_count_com = Db::getInstance()->getRow('
				SELECT COUNT(*) AS "count"
				FROM `'._DB_PREFIX_.'blog_comments` pc
				LEFT JOIN `'._DB_PREFIX_.'blog_post` pc2 ON(pc.id_post = pc2.id)
				LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc1 ON(pc1.id_item = pc2.id)
				LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc2.author_id = ba2c.id_customer)

				WHERE pc.status = 1
				AND ba2c.status != 2 and ba2c.id_shop = '.(int)$id_shop.'
				and pc.id_lang = '.(int)$current_language.'
				and pc.id_shop = '.(int)$id_shop.' AND

				pc1.id_lang = '.(int)$current_language.'
			    AND FIND_IN_SET('.(int)$id_shop.',pc2.ids_shops)
			    AND FIND_IN_SET('.(int)$this->_id_group.',pc2.ids_groups)
			    AND pc2.status = 1
			    '.(($this->_is_show_now)?'AND (pc2.`publish_date` <= NOW())':'').'
				');


				$count_all = $data_count_com['count'];
			
			} else {


                $id_post_item_custom_hook = isset($data['id_post'])?$data['id_post']:0;
                $sql_custom_hook = '';

                if($id_post_item_custom_hook){
                    $sql_custom_hook = 'AND pc.id_post='.(int)$id_post_item_custom_hook;
                }
				
				$limit = (int)Configuration::get($this->_name.'blog_com');

                $sql = '
				SELECT pc.*, pc1.seo_url as post_seo_url, pc.id_post as post_id, ba2c.status as status_author
				FROM `'._DB_PREFIX_.'blog_comments` pc
				LEFT JOIN `'._DB_PREFIX_.'blog_post` pc2 ON(pc.id_post = pc2.id)
				LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc1 ON(pc1.id_item = pc2.id)
				LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc2.author_id = ba2c.id_customer)

				WHERE pc.status = 1 AND ba2c.status != 2 and pc.id_lang = '.(int)$current_language.'
				and pc.id_shop = '.(int)$id_shop.' AND

				pc1.id_lang = '.(int)$current_language.'
			    AND FIND_IN_SET('.(int)$id_shop.',pc2.ids_shops)
			    AND FIND_IN_SET('.(int)$this->_id_group.',pc2.ids_groups)
			    AND pc2.status = 1 '.$sql_custom_hook.'
			    '.(($this->_is_show_now)?'AND (pc2.`publish_date` <= NOW())':'').'
                ORDER BY pc.`time_add` DESC LIMIT '.(int)$limit;


				$comments = Db::getInstance()->ExecuteS($sql);





                include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
                $obj_userprofileblockblog = new userprofileblockblog();


                foreach ($comments as $k => $item_data){


                    ## author ##
                    $author_id = $item_data['id_customer'];

                    if($author_id != 0) {



                        $sql = 'select ba2c.status from `'._DB_PREFIX_.'blog_avatar2customer` ba2c WHERE ba2c.id_customer = '.(int)$author_id.' and ba2c.id_shop = '.(int)$id_shop.'';
                        $status_author_data = Db::getInstance()->getRow($sql);
                        $status_author = $status_author_data['status'];


                        $info_path = $this->getAvatarPath(array('id_customer' => $author_id,'id_shop'=>$id_shop,'status_author'=>$status_author));

                        //var_dump($author_id);var_dump($info_path);exit;
                        $comments[$k]['avatar'] = $info_path['avatar'];
                        $comments[$k]['is_show_ava'] = $info_path['is_show'];
                        $comments[$k]['author_id'] = $author_id;

                        $comments[$k]['status_author'] = $status_author;

                        $customer_data = $obj_userprofileblockblog->getInfoAboutCustomer(array('id_customer' => $author_id, 'is_full' => 1));
                        $name_customer = $customer_data['customer_name'];


                        $comments[$k]['author'] = $name_customer;

                    } else {
                        $comments[$k]['avatar'] = '';
                        $comments[$k]['is_show_ava'] = 0;
                        $comments[$k]['author_id'] = 0;
                        $comments[$k]['author'] = $item_data['name'];
                    }
                    ## author ##

                }


	
			}
		return array('comments' => $comments,'count_all'=>$count_all);
	}


    public function getSeoUrlsSettings($data){
        $id_lang = $data['id_lang'];


        //$all_shops = Shop::getShops();

            //$id_shop_group = (int)$_shop['id_shop_group'];
            //$id_shop = (int)$_shop['id_shop'];


        $blog_alias = Configuration::get($this->_name.'blog_alias_'.$id_lang);


        $blog_cat_alias = Configuration::get($this->_name.'blog_cat_alias_'.$id_lang);
        $blog_allcom_alias = Configuration::get($this->_name.'blog_allcom_alias_'.$id_lang);
        $blog_alltags_alias = Configuration::get($this->_name.'blog_alltags_alias_'.$id_lang);
        $blog_tag_alias = Configuration::get($this->_name.'blog_tag_alias_'.$id_lang);
        $blog_authors_alias = Configuration::get($this->_name.'blog_authors_alias_'.$id_lang);
        $blog_author_alias = Configuration::get($this->_name.'blog_author_alias_'.$id_lang);
        $blog_gallery_alias = Configuration::get($this->_name.'blog_gallery_alias_'.$id_lang);
        $blog_mava_alias = Configuration::get($this->_name.'blog_mava_alias_'.$id_lang);
        $blog_mbposts_alias = Configuration::get($this->_name.'blog_mbposts_alias_'.$id_lang);
        $blog_mbcom_alias = Configuration::get($this->_name.'blog_mbcom_alias_'.$id_lang);
        $blog_mbloyality_alias = Configuration::get($this->_name.'blog_loyalty_alias_'.$id_lang);



        $blog_alias = (!empty($blog_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_alias:'blog';
        $blog_cat_alias = (!empty($blog_cat_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_cat_alias:'categories';
        $blog_allcom_alias = (!empty($blog_allcom_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_allcom_alias:'comments';
        $blog_alltags_alias = (!empty($blog_alltags_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_alltags_alias:'tags';
        $blog_tag_alias = (!empty($blog_tag_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_tag_alias:'tag';
        $blog_authors_alias = (!empty($blog_authors_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_authors_alias:'authors';
        $blog_author_alias = (!empty($blog_author_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_author_alias:'author';
        $blog_gallery_alias = (!empty($blog_gallery_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_gallery_alias:'gallery';
        $blog_mava_alias = (!empty($blog_mava_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_mava_alias:'useraccount';
        $blog_mbposts_alias = (!empty($blog_mbposts_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_mbposts_alias:'myblogposts';
        $blog_mbcom_alias = (!empty($blog_mbcom_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_mbcom_alias:'myblogcomments';
        $blog_mbloyality_alias = (!empty($blog_mbloyality_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_mbloyality_alias:'loyalty';

        //new
        $blog_rss_alias = Configuration::get($this->_name.'blog_rss_alias_'.$id_lang);

        $blog_rss_alias = (!empty($blog_rss_alias) && Configuration::get('PS_REWRITING_SETTINGS'))?$blog_rss_alias:'rss';


        return array(
                    'blog_alias'=>$blog_alias,
                    'blog_cat_alias'=>$blog_cat_alias,
                    'blog_allcom_alias'=>$blog_allcom_alias,
                    'blog_alltags_alias'=>$blog_alltags_alias,
                    'blog_tag_alias'=>$blog_tag_alias,
                    'blog_authors_alias'=>$blog_authors_alias,
                    'blog_author_alias'=>$blog_author_alias,
                    'blog_gallery_alias'=>$blog_gallery_alias,
                    'blog_mava_alias'=>$blog_mava_alias,
                    'blog_mbposts_alias'=>$blog_mbposts_alias,
                    'blog_mbcom_alias'=>$blog_mbcom_alias,
                    'blog_mbloyality_alias'=>$blog_mbloyality_alias,

                    'blog_rss_alias'=>$blog_rss_alias,
                    );

    }

    public function getSEOURLs($_data = null){
        $iso_code = $this->getLangISO();

        $cookie = $this->context->cookie;
        $id_lang = isset($_data['id_lang'])?$_data['id_lang']:(int)$cookie->id_lang;

        // id_shop for sitemap only
        $id_shop = isset($_data['id_shop'])?$_data['id_shop']:(int)$this->_id_shop;
        $is_sitemap = isset($_data['is_sitemap'])?$_data['is_sitemap']:0;


        $is_ssl = false;
        if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
            $is_ssl = true;


        include_once(_PS_MODULE_DIR_.$this->_name.'/'.$this->_name.'.php');
        $obj_module = new $this->_name();
        $token = $obj_module->getoken();


        $delimeter_rewrite = "&";
        if(Configuration::get('PS_REWRITING_SETTINGS')){
            $delimeter_rewrite = "?";
        }



        $data_seo_uls_set = $this->getSeoUrlsSettings(array('id_lang'=>$id_lang));

        $blog_alias = $data_seo_uls_set['blog_alias'];
        $blog_cat_alias = $data_seo_uls_set['blog_cat_alias'];
        $blog_allcom_alias = $data_seo_uls_set['blog_allcom_alias'];
        $blog_alltags_alias = $data_seo_uls_set['blog_alltags_alias'];
        $blog_tag_alias = $data_seo_uls_set['blog_tag_alias'];
        $blog_authors_alias = $data_seo_uls_set['blog_authors_alias'];
        $blog_author_alias = $data_seo_uls_set['blog_author_alias'];
        $blog_gallery_alias = $data_seo_uls_set['blog_gallery_alias'];
        $blog_mava_alias = $data_seo_uls_set['blog_mava_alias'];
        $blog_mbposts_alias = $data_seo_uls_set['blog_mbposts_alias'];
        $blog_mbcom_alias = $data_seo_uls_set['blog_mbcom_alias'];
        $blog_mbloyality_alias = $data_seo_uls_set['blog_mbloyality_alias'];

        $blog_rss_alias = $data_seo_uls_set['blog_rss_alias'];

        $link = new Link();

        if($this->isURLRewriting()){

            ## when url rewrite is disabled ##
            if($is_sitemap){

                $data_shop = Shop::getShop($id_shop);

                if($is_ssl)
                   $http_host = "https://".$data_shop['domain_ssl'].$data_shop['uri'];
                else
                    $http_host = "http://".$data_shop['domain'].$data_shop['uri'];


            } else {
                $http_host = $this->getHttpost();
            }





                $categories_url = $http_host . $iso_code. $blog_alias.'/'.$blog_cat_alias;
                $category_url = $http_host . $iso_code. $blog_alias.'/c-';

                $posts_url = $http_host . $iso_code. $blog_alias;
                $post_url = $http_host .  $iso_code.$blog_alias.'/p-';

                $comments_url = $http_host . $iso_code. $blog_alias.'/'.$blog_allcom_alias;


                $tags_url = $http_host . $iso_code. $blog_alias.'/'.$blog_alltags_alias;
                $tag_url = $http_host . $iso_code. $blog_alias.'/'.$blog_tag_alias.'/';

                $authors_url = $http_host . $iso_code. $blog_alias.'/'.$blog_authors_alias;
                $author_url = $http_host . $iso_code. $blog_alias.'/'.$blog_author_alias.'/';

                $gallery_url = $http_host . $iso_code. $blog_alias.'/'.$blog_gallery_alias;

                $useraccount_url = $http_host . $iso_code. $blog_alias.'/'.$blog_mava_alias;
                $myblogposts_url = $http_host . $iso_code. $blog_alias.'/'.$blog_mbposts_alias;
                $myblogcomments_url = $http_host . $iso_code. $blog_alias.'/'.$blog_mbcom_alias;


            $loyalty_account_url = $http_host . $iso_code. $blog_alias.'/'.$blog_mbloyality_alias;


                // add new urls

                $rss_url = $http_host . $iso_code. $blog_alias.'/'.$blog_rss_alias;

            ## when url rewrite is disabled ##

        } else {



                ## when url rewrite is enabled ##




                $categories_url = $link->getModuleLink($this->_name, 'categories', array(), $is_ssl, $id_lang, $id_shop);
                $category_url = $link->getModuleLink($this->_name, $blog_alias, array(), $is_ssl, $id_lang, $id_shop);
                $category_url .= $delimeter_rewrite.'category_id=';


                $posts_url = $link->getModuleLink($this->_name, $blog_alias, array(), $is_ssl, $id_lang, $id_shop);

                $post_url = $link->getModuleLink($this->_name, $blog_alias, array(), $is_ssl, $id_lang, $id_shop);
                $post_url .= $delimeter_rewrite.'post_id=';

                $comments_url = $link->getModuleLink($this->_name, 'comments', array(), $is_ssl, $id_lang, $id_shop);

                $tags_url = $link->getModuleLink($this->_name, 'tags', array(), $is_ssl, $id_lang, $id_shop);
                $tag_url = $link->getModuleLink($this->_name, 'tag', array(), $is_ssl, $id_lang, $id_shop);
                $tag_url .= $delimeter_rewrite.'tag_id=';


                $authors_url = $link->getModuleLink($this->_name, 'authors', array(), $is_ssl, $id_lang, $id_shop);
                $author_url = $link->getModuleLink($this->_name, 'author', array(), $is_ssl, $id_lang, $id_shop);
                $author_url .= $delimeter_rewrite.'author_id=';

                $gallery_url = $link->getModuleLink($this->_name, 'gallery', array(), $is_ssl, $id_lang, $id_shop);

                $useraccount_url = $link->getModuleLink($this->_name, 'useraccount',  array(), $is_ssl, $id_lang,$id_shop);
                $myblogposts_url = $link->getModuleLink($this->_name, 'myblogposts',  array(), $is_ssl, $id_lang,$id_shop);
                $myblogcomments_url = $link->getModuleLink($this->_name, 'myblogcomments',  array(), $is_ssl, $id_lang,$id_shop);

                // add new urls

                $rss_url = $link->getModuleLink($this->_name, 'rss', array(), $is_ssl, $id_lang, $id_shop);

                $loyalty_account_url= $link->getModuleLink($this->_name, 'loyalty',  array(), $is_ssl, $id_lang,$id_shop);



            ## when url rewrite is enabled ##

        }




        $ajax_url = $link->getModuleLink($this->_name, 'ajax', array(), $is_ssl, $id_lang, $id_shop).$delimeter_rewrite.'token='.$token;
        $captcha_url = $link->getModuleLink($this->_name, 'captcha', array(), $is_ssl, $id_lang, $id_shop);
        $sitemap_url = $link->getModuleLink($this->_name, 'sitemap', array(), $is_ssl, $id_lang, $id_shop);

        $my_account = $link->getPageLink("my-account", true, $id_lang,null,false,$id_shop);



        return array(
                     'categories_url' => $categories_url, 'category_url'=>$category_url,
                     'posts_url'=>$posts_url, 'post_url'=>$post_url,
                     'comments_url'=>$comments_url,
                     'tags_url'=>$tags_url, 'tag_url'=>$tag_url,
                     'ajax_url'=>$ajax_url, 'captcha_url' => $captcha_url,

                     'rss_url'=>$rss_url,

                     'sitemap_url'=>$sitemap_url,
                     'author_url'=>$author_url,'authors_url'=>$authors_url,
                     'my_account' => $my_account,'useraccount_url'=>$useraccount_url,
                     'myblogposts_url'=>$myblogposts_url,'myblogcomments_url'=>$myblogcomments_url,
                     'loyalty_account_url'=>$loyalty_account_url,
                     'gallery_url'=>$gallery_url,

                    );
    }

    public function getHttpost(){

            $custom_ssl_var = 0;
            if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                $custom_ssl_var = 1;


            if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1 && (bool)Configuration::get('PS_SSL_ENABLED_EVERYWHERE')) {

                //$_http_host = _PS_BASE_URL_SSL_ . __PS_BASE_URI__;
                $_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;

            } else {
                //$_http_host = _PS_BASE_URL_ . __PS_BASE_URI__;
                $_http_host = Tools::getShopDomain(true, true) . __PS_BASE_URI__;
            }


        return $_http_host;
    }

    public function like($data){
        $like = (int)$data['like'];
        $id = (int)$data['id'];
        $ip = pSQL($data['ip']);
        $sql_exists = "select count(*) as count from `"._DB_PREFIX_."blog_post_like` where ip = '".pSQL($ip)."' and post_id = ".(int)$id." and `like` = ".(int)$like;
        $is_exists = Db::getInstance()->getRow($sql_exists);


        $error = 'error';

        include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
        $obj = new blockblog();
        $_data_translate = $obj->translateItems();
        $message = htmlspecialchars($_data_translate['message_like']);

        $count = 0;

        if($is_exists['count'] == 0){
            $error = 'success';
            $message = "";
            $sql = "insert into `"._DB_PREFIX_."blog_post_like` set post_id = ".(int)$id.", `like` = ".(int)$like.", ip = '".pSQL($ip)."'";
            Db::getInstance()->Execute($sql);

            $sql_count_likes = "select count(*) as count from `"._DB_PREFIX_."blog_post_like` where post_id = ".(int)$id." and `like` = ".(int)$like;
            $count_likes = Db::getInstance()->getRow($sql_count_likes);
            $count = $count_likes['count'];

            // clear cache only for blog //
            $this->clearSmartyCacheBlog();
            // clear cache only for blog //
        }

        $cookie = $this->context->cookie;
        $id_customer = (int)$cookie->id_customer;
        if($id_customer>0){

            // loyality program //
            include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
            $loyality = new loyalityblog();
            $loyality->saveLoyalityAction(array('id_loyalty_status' => 1, 'id_customer' => $id_customer, 'id_post'=>$id, 'type' => 'loyality_like_blog_post'));
            // loyality program //

        }



        return array('error'=>$error, 'message'=>$message, 'count'=>$count);
    }



    public function getPostsForTag($_data){


            $tag = $_data['tag_id'];


            $start = $_data['start'];
            $step = $_data['step'];

            $cookie = $this->context->cookie;
            $current_language = (int)$cookie->id_lang;

            $order_by = 'ORDER BY pc.`'.Configuration::get($this->_name.'blog_post_order').'` '.Configuration::get($this->_name.'blog_post_ad').'';

            $sql = '
			SELECT pc.* , ba2c.status as status_author,
				(select count(*) as count from `'._DB_PREFIX_.'blog_comments` c2pc
				 where c2pc.id_post = pc.id and c2pc.status = 1 and c2pc.id_shop = '.(int)$this->_id_shop.'
				 and c2pc.id_lang = '.(int)$current_language.' )
				 as count_comments,
				 (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1) as count_like,
                (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1 and ip = \''.pSQL($_SERVER['REMOTE_ADDR']).'\') as is_liked_post,
                (select count(*) as count from `'._DB_PREFIX_.'blog_comments` pcc WHERE pcc.id_post = pc.id and pcc.status = 1 and pcc.id_lang = '.(int)$current_language.') as count_comments

			FROM  `'._DB_PREFIX_.'blog_post_data` pc_d LEFT JOIN `'._DB_PREFIX_.'blog_post` pc ON(pc.id = pc_d.id_item)
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE pc.status = 1 and pc_d.id_lang = '.(int)$current_language.'
			AND ba2c.status != 2
			AND FIND_IN_SET("'.pSQL($tag).'",pc_d.tags)
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
            AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups) AND ba2c.id_shop = '.(int)$this->_id_shop.'
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			'.$order_by.' LIMIT '.(int)$start.' ,'.(int)$step.'';



            $items = Db::getInstance()->ExecuteS($sql);



            foreach($items as $k1=>$_item){
                $id_post = $_item['id'];



                if(Tools::strlen($_item['img'])>0){
                    $this->generateThumbImages(array('img'=>$_item['img'],
                            'width'=>$this->_img_posts_width,
                            'height'=>$this->_img_posts_height
                        )
                    );
                    $img = Tools::substr($_item['img'],0,-4)."-".$this->_img_posts_width."x".$this->_img_posts_height.".jpg";
                } else {
                    $img = $_item['img'];
                }

                $items[$k1]['img'] = $img;


                $items[$k1]['author'] = $_item['author'];
                $author_id = $_item['author_id'];
                $items[$k1]['author_id'] = $author_id;

                $info_path = $this->getAvatarPath(array('id_customer'=>$author_id,'id_shop'=>$this->_id_shop));


                $items[$k1]['avatar'] = $info_path['avatar'];
                $items[$k1]['is_show_ava'] = $info_path['is_show'];

                $category_ids = Db::getInstance()->ExecuteS('
				SELECT pc.category_id
				FROM `'._DB_PREFIX_.'blog_category2post` pc
				WHERE pc.`post_id` = '.(int)$id_post.'');
                $data_category_ids = array();
                foreach($category_ids as $k => $v){
                    $_info_ids = $this->getCategoryItem(array('id' => $v['category_id']));
                    $category_info_ds = isset($_info_ids['category'][0])?$_info_ids['category'][0]:array();
                    $ids_item = sizeof($category_info_ds)>0?$category_info_ds:array();

                    if(sizeof($ids_item)>0){
                        $data_category_ids[] = $ids_item;
                    }
                }

                $items[$k1]['category_ids'] = $data_category_ids;

                $items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');

                foreach ($items_data as $item_data){

                    if($current_language == $item_data['id_lang']){
                        $items[$k1]['title'] = $item_data['title'];
                        $items[$k1]['seo_description'] = $item_data['seo_description'];
                        $items[$k1]['seo_keywords'] = $item_data['seo_keywords'];
                        $items[$k1]['content'] = $item_data['content'];
                        $items[$k1]['id'] = $_item['id'];
                        $items[$k1]['time_add'] = $_item['time_add'];
                        $items[$k1]['seo_url'] = $item_data['seo_url'];
                    }
                }

            }

            $sql = '
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_post_data` pc_d LEFT JOIN `'._DB_PREFIX_.'blog_post` pc ON(pc.id = pc_d.id_item)
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE FIND_IN_SET("'.pSQL($tag).'",pc_d.tags)
			and pc_d.id_lang = '.(int)$current_language.'
			AND pc.status = 1 AND ba2c.status != 2
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops) AND ba2c.id_shop = '.(int)$this->_id_shop.'
            AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			';

            $data_count_posts = Db::getInstance()->getRow($sql);

        return array('posts' => $items, 'count_all' => $data_count_posts['count'] );
    }


    public function isExistsTag($data){

        $tag = $data['tag_id'];

        $cookie = $this->context->cookie;
        $id_lang = (int)$cookie->id_lang;


        $sql = '
					SELECT DISTINCT pc_d.id_item
					FROM `'._DB_PREFIX_.'blog_post_data` pc_d LEFT JOIN `'._DB_PREFIX_.'blog_post` pc ON(pc.id = pc_d.id_item)
					LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
					WHERE FIND_IN_SET("'.pSQL($tag).'",pc_d.tags)
					and pc_d.id_lang = '.(int)$id_lang.'
					and ba2c.status != 2
					AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops) AND ba2c.id_shop = '.(int)$this->_id_shop.'
					AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
					'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
					AND pc.status = 1';

        //echo $sql;exit;


        $data_id = Db::getInstance()->GetRow($sql);
        $id_item = $data_id['id_item'];

        return $id_item;

    }


    public function getTags($data = null) {

        $limit = isset($data['limit'])?$data['limit']:null;
        $cookie = $this->context->cookie;
        $id_lang = isset($data['id_lang'])?$data['id_lang']:(int)$cookie->id_lang;
        $id_shop = isset($data['id_shop'])?$data['id_shop']:$this->_id_shop;


        $sql = '
					SELECT pc_d.tags
					FROM `'._DB_PREFIX_.'blog_post_data` pc_d JOIN `'._DB_PREFIX_.'blog_post` pc ON(pc.id = pc_d.id_item)
					LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
					WHERE pc_d.id_lang = '.(int)$id_lang.'
					and ba2c.status != 2
					AND FIND_IN_SET('.(int)$id_shop.',pc.ids_shops) AND ba2c.id_shop = '.(int)$this->_id_shop.'
					AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
					'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
					AND pc.status = 1 ';

        //echo $sql;

        $items = Db::getInstance()->ExecuteS($sql);


        $data_all = array();
        $data_all_tmp = array();

        foreach($items as $item){


            if($item['tags']) {


                $item_tags = explode(",", $item['tags']);

                foreach ($item_tags as $item_tag) {


                    if (!in_array($item_tag, $data_all_tmp)) {
                        $data_all_tmp[] = $item_tag;
                        $data_all[$item_tag] = 1;
                    } else {
                        $data_all[$item_tag] = $data_all[$item_tag] + 1;
                    }

                }
            }


        }
        arsort($data_all);


        ### limit in action ##
        if($limit){
            $data_all = array_slice($data_all,0,$limit);
        }
        ### limit in action ##


        $rows = array();
        foreach($data_all as $k => $val){
            $rows[] = array('count'=>$val,'query'=>$k);
        }




        if(sizeof($rows)>0){
            $arr = $this->FontSize($rows);
            $arr = $this->SortingByName($arr);
        } else {
            $arr = array();
        }



        return $arr;
    }

    private function FontSize($arr){
        $font_min       = 100;
        $font_max       = 250;
        $font_sep       = '%';
        $font_size_step = 10;

        $count_tags = count($arr);
        $max_prod_count = $arr[0]['count'];
        $min_prod_count = $arr[$count_tags - 1]['count'];
        $step_prod =(($max_prod_count - $min_prod_count) / ($font_max - $font_min)) * $font_size_step;
        if($step_prod <0){
            $step_prod = 0-$step_prod;
        }
        $step = 0;

        for($i = 0; $i < count($arr); $i++){
            if ($step_prod !== 0) {
                $step = round((int) $arr[$i]['count'] / $step_prod);

                $font_size = round($font_min + $step * $font_size_step);
                if($font_size > $font_max) $font_size = $font_max;
                $arr[$i]['font'] = $font_size.$font_sep;
            }
        }
        return $arr;
    }



    private function SortingByName($arr){
        for($i=0;$i<count($arr);$i++){
            for($j=0;$j<count($arr);$j++){
                if( $i < $j && $arr[$i]['query'] > $arr[$j]['query'] ){
                    $buf = array();
                    $buf = $arr[$i];
                    $arr[$i] = $arr[$j];
                    $arr[$j] = $buf;
                }
            }
        }
        return $arr;
    }


    public function statisticsHandler($data = null){

        $post_id = isset($data['post_id'])?$data['post_id']:0;

        if($post_id) {

            $ip = $_SERVER['REMOTE_ADDR'];

            $sql = 'SELECT count(*) as count
					FROM `' . _DB_PREFIX_ . 'blog_views_statistics` pc
					WHERE pc.ip = "' . pSQL($ip) . '"
					and pc.post_id = ' . (int)$post_id . '
					';

            $data_exists = Db::getInstance()->GetRow($sql);
            $is_exists = $data_exists['count'];

            if(!$is_exists){

                // insert record in the blog_views_statistics
                $sql = "insert into `"._DB_PREFIX_."blog_views_statistics` set post_id = ".(int)$post_id.", ip = '".pSQL($ip)."'";
                Db::getInstance()->Execute($sql);


                // get count_views for post
                $sql = 'SELECT count(*) as count
					FROM `' . _DB_PREFIX_ . 'blog_views_statistics` pc
					WHERE pc.post_id = ' . (int)$post_id . '
					';

                $data_count_views = Db::getInstance()->GetRow($sql);
                $count_views = $data_count_views['count'];

                // update count_posts counter
                Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'blog_post
							SET count_views = '. (int)($count_views) .' WHERE id = ' . (int)($post_id));


                // clear cache only for blog //
                $this->clearSmartyCacheBlog();
                // clear cache only for blog //

            }

        }



    }



    public function parseWordpressXml($data_in){


        $select_language = $data_in['select_language'];
        $group_association = $data_in['group_association'];
        $cat_shop_association = $data_in['cat_shop_association'];

        $error = 0;
        $error_text = '';


        $files = $_FILES['xml_file'];

        ############### files ###############################
        if(!empty($files['name']))
        {


            if(!$files['error'])
            {
                $type_one = $files['type'];
                $ext = explode("/",$type_one);


                if(strpos('_'.$type_one,'xml')<1)
                {
                    $error_text = $this->l('Invalid file type, please try again!');
                    $error = 1;
                } elseif(!in_array($ext[1],array('xml')) ){
                    $error_text = $this->l('Wrong file format, please try again!');
                    $error = 1;

                } else {

                    include_once(_PS_MODULE_DIR_.$this->_name.'/classes/xmlblockblog.class.php');


                    $xml_file = $files['tmp_name'];
                    $xml_file = trim(Tools::file_get_contents($xml_file));


                    /// fixed bug //
                    $xml_file = preg_replace('/\<wp:meta_value\>(.*?)\<\/wp:meta_value\>/ims', '', $xml_file);
                    /// fixed bug //

                    //var_dump($xml_file);exit;

                    $xml_array = xmlblockblog::toArray(xmlblockblog::build($xml_file));


                    if (!empty($xml_array['rss']['channel']['wp:category']) && count($xml_array['rss']['channel']['wp:category']) > 0)
                    {

                        $modif_categories_parents = array();
                        $categories_title = array();
                        foreach ($xml_array['rss']['channel']['wp:category'] as $value)
                        {

                            $categories_title[$value['wp:category_nicename']] = $value['wp:cat_name'];

                            $id_import_category = 0;
                            $id_import_category = (int)$this->isExistsCategory(array('id_lang'=>(int)$select_language,'ids_shops'=>$cat_shop_association,'title'=>$value['wp:cat_name']));
                            if (!$id_import_category)
                            {

                                ### save not exists category ###
                                $data_title_content_lang_category = array();

                                $data_title_content_lang_category[$select_language] =
                                    array('category_title' => $value['wp:cat_name'],
                                          'category_seokeywords' => '',
                                          'category_seodescription' => '',
                                          'category_content'=>'',
                                          'seo_url' =>$value['wp:cat_name']
                                        );

                                $category_data = array(
                                    'data_title_content_lang'=>$data_title_content_lang_category,
                                    'cat_shop_association' => $cat_shop_association,
                                    'group_association' => $group_association,
                                    'status'=>1,
                                    'time_add' => date("Y-m-d H:i:s")
                                );



                                $id_import_category_data = $this->saveCategory($category_data);
                                $id_import_category = $id_import_category_data['post_id'];

                                ### save not exists category ###

                            }
                            if ($value['wp:category_parent'] != '')
                            {
                                $modif_categories_parents[$value['wp:category_nicename']]['id_import_category'] = $id_import_category;
                                $modif_categories_parents[$value['wp:category_nicename']]['parent'] = $value['wp:category_parent'];
                                $modif_categories_parents[$value['wp:category_nicename']]['title'] = $value['wp:cat_name'];
                            }
                        }

                        foreach ($modif_categories_parents as $value)
                        {
                            $id_import_category = (int)$this->isExistsCategory(array('id_lang'=>(int)$select_language,'ids_shops'=>$cat_shop_association,'title'=>$value['title']));

                            if (!$id_import_category)
                            {

                                ### save not exists category ###
                                $data_title_content_lang_category = array();

                                $data_title_content_lang_category[$select_language] =
                                    array('category_title' => $value['title'],
                                        'category_seokeywords' => '',
                                        'category_seodescription' => '',
                                        'category_content'=>'',
                                        'seo_url' =>$value['title']
                                    );

                                $category_data = array(
                                    'data_title_content_lang'=>$data_title_content_lang_category,
                                    'cat_shop_association' => $cat_shop_association,
                                    'group_association' => $group_association,
                                    'status'=>1,
                                    'time_add' => date("Y-m-d H:i:s")
                                );


                                $this->saveCategory($category_data);

                                ### save not exists category ###

                            }


                        }
                    }






                    if (!empty($xml_array['rss']['channel']['item']) && count($xml_array['rss']['channel']['item']) > 0)
                    {


                        $liste_items = array();

                        if (isset($xml_array['rss']['channel']['item']['title']))
                            $liste_items[0] = $xml_array['rss']['channel']['item'];
                        else
                            $liste_items = $xml_array['rss']['channel']['item'];


                        //echo "<pre>"; var_dump($liste_items);exit;

                        foreach ($liste_items as $v_item)
                        {
                            if ($v_item['wp:post_type'] == 'post')
                            {


                                $data_title_content_lang_post = array();


                                /*if($v_item['title'] == "U.S. Anti-Missile Defense System Challenges North Korea") {
                                    echo "<pre>"; var_dump($v_item);
                                }*/

                                $post_title = $v_item['title'];
                                $post_content = $v_item['content:encoded'];
                                $post_seokeywords = '';
                                $post_seodescription = '';
                                $seo_url_post = $v_item['title'];
                                $tags_post = '';



                                if ($v_item['wp:status'] == 'publish')
                                    $post_status = 1;
                                else
                                    $post_status = 0;



                                /* category and tags */
                                if (isset($v_item['category']) && count($v_item['category']) > 0)
                                {
                                    $import_categories = array();
                                    $import_categories_id = array();
                                    if (isset($v_item['category']['@domain']))
                                    {

                                        if ($v_item['category']['@domain'] == 'category')
                                            $import_categories[] = $v_item['category']['@'];

                                        if ($v_item['category']['@domain'] == 'post_tag')
                                            $key_words = $v_item['category']['@'];
                                    }
                                    else
                                    {

                                        if (count($v_item['category']) > 0)
                                        {
                                            foreach ($v_item['category'] as $v_category)
                                                if ($v_category['@domain'] == 'category')
                                                    $import_categories[] = $v_category['@'];
                                            $import_categories = array_unique($import_categories);
                                        }


                                        $import_tags = array();
                                        if (count($v_item['category']) > 0)
                                        {
                                            foreach ($v_item['category'] as $v_tag)
                                                if ($v_tag['@domain'] == 'post_tag')
                                                    $import_tags[] = trim($v_tag['@']);
                                            $import_tags = array_unique($import_tags);
                                        }
                                        $key_words = '';
                                        if (count($import_tags) > 0)
                                            foreach ($import_tags as $v_import_tag)
                                                $key_words .= $v_import_tag.', ';
                                        $tags_post = rtrim($key_words, ', ');
                                    }

                                    if (count($import_categories) > 0)
                                    {
                                        foreach ($import_categories as $v_import_categorie)
                                        {

                                            $id_import_category = (int)$this->isExistsCategory(
                                                array('id_lang'=>(int)$select_language,
                                                     'ids_shops'=>$cat_shop_association,
                                                     'title'=>$v_import_categorie)
                                            );
                                            if ($id_import_category) {
                                                $import_categories_id[] = $id_import_category;
                                            } else {

                                                ### save not exists category ###
                                                $data_title_content_lang_category = array();

                                                $data_title_content_lang_category[$select_language] =
                                                    array('category_title' => $v_import_categorie,
                                                        'category_seokeywords' => '',
                                                        'category_seodescription' => '',
                                                        'category_content'=>'',
                                                        'seo_url' =>$v_import_categorie
                                                    );

                                                $category_data = array(
                                                    'data_title_content_lang'=>$data_title_content_lang_category,
                                                    'cat_shop_association' => $cat_shop_association,
                                                    'group_association' => $group_association,
                                                    'status'=>1,
                                                    'time_add' => date("Y-m-d H:i:s")
                                                );



                                                $id_import_category_data = $this->saveCategory($category_data);
                                                $id_import_category = $id_import_category_data['post_id'];


                                                ### save not exists category ###

                                                $import_categories_id[] = $id_import_category;
                                            }
                                        }
                                    }


                                }
                                /* category and tags */

                                $post_content = str_replace("\n","<br/>",$post_content);

                                $data_title_content_lang_post[$select_language] =
                                    array('post_title' => $post_title,
                                        'post_seokeywords' => $post_seokeywords,
                                        'post_seodescription' => $post_seodescription,
                                        'post_content' => $post_content,
                                        'seo_url'=>$seo_url_post,
                                        'tags' => $tags_post);


                                $publish_date_post = '0000-00-00 00:00:00';
                                $time_add_post = $v_item['wp:post_date'];

                                $post_data = array(
                                    'data_title_content_lang'=>$data_title_content_lang_post,
                                    'ids_categories' => $import_categories_id,
                                    'post_status' => $post_status,
                                    'post_iscomments' =>1,
                                    'post_isfbcomments'=>1,
                                    'post_images' => null,
                                    'cat_shop_association' => $cat_shop_association,
                                    'related_products'=>null,
                                    'ids_related_posts'=>null,
                                    'group_association' => $group_association,
                                    'is_rss'=>1,
                                    'after_time_add' => 0,
                                    'publish_date'=>$publish_date_post,
                                    'time_add' => $time_add_post
                                );


                                //echo "<pre>"; var_dump($post_data);exit;

                                $post_id_data = $this->savePost($post_data);
                                $post_id = $post_id_data['post_id'];


                                //echo "<pre>"; var_dump($v_item['wp:attachment_url']); echo "<br><hr><br>";

                                ### image ###
                                /*$attachment_url = isset($v_item['wp:attachment_url'])?$v_item['wp:attachment_url']:null;
                                if($attachment_url) {

                                    $attachment_namefile_new = explode("/",$attachment_url);
                                    var_dump($attachment_url);
                                    var_dump($attachment_namefile_new);
                                    exit;

                                    //Tools::copy($attachment_url,$this->path_img_cloud.$attachment_namefile_new);

                                }*/
                                ### image ###
                                //$post_id = 1; // for test


                                if ($post_id)
                                {




                                    if (isset($v_item['wp:comment']) && count($v_item['wp:comment']) > 0)
                                    {




                                        if (isset($v_item['wp:comment']['wp:comment_author']))
                                        {
                                            $comments_name		= (trim($v_item['wp:comment']['wp:comment_author']) == '' ? $this->l('Nobody') : $v_item['wp:comment']['wp:comment_author']);

                                            $comments_comment	= $v_item['wp:comment']['wp:comment_content'];


                                            $time_add		= $v_item['wp:comment']['wp:comment_date'];

                                            $comments_email	= $v_item['wp:comment']['wp:comment_author_email'];


                                            if ((int)$v_item['wp:comment']['wp:comment_approved'] == 1)
                                                $comments_status		= 1;
                                            else
                                                $comments_status		= 0;

                                            foreach($cat_shop_association as $_id_shop_comment) {

                                                $data_comment = array('name' => $comments_name,
                                                    'email' => $comments_email,
                                                    'text_review' => $comments_comment,
                                                    'status' => $comments_status,
                                                    'time_add' => $time_add,
                                                    'id_shop'=>$_id_shop_comment,
                                                    'id_lang' => $select_language,
                                                    'response' => '',
                                                    'id_post'=>$post_id,
                                                    'is_noti' => 0,
                                                    'rating' => 5,

                                                );



                                                //echo "<pre>"; var_dump($data_comment);
                                                $this->saveComment($data_comment);

                                                // updated avg_rating for blog post//
                                                $result_avg = Db::getInstance()->getRow('select ceil(AVG(`rating`)) AS "avg_rating"
                                                                            from `'._DB_PREFIX_.'blog_comments` where id_post ='.(int)$post_id);
                                                $result_avg_rating = $result_avg['avg_rating'];

                                                $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_post` SET `avg_rating` = "'.(int)$result_avg_rating.'" WHERE id ='.(int)$post_id;
                                                Db::getInstance()->Execute($sql_update);
                                                // updated avg_rating for blog post//
                                            }



                                        }
                                        else
                                        {
                                            foreach ($v_item['wp:comment'] as $v_comment)
                                            {



                                                $comments_name		= (trim($v_comment['wp:comment_author']) == '' ? $this->l('Nobody') : $v_comment['wp:comment_author']);

                                                $comments_comment	= $v_comment['wp:comment_content'];

                                                $time_add		= $v_comment['wp:comment_date'];

                                                if ((int)$v_comment['wp:comment_approved'] == 1)
                                                    $comments_status		= 1;
                                                else
                                                    $comments_status		= 0;

                                                foreach($cat_shop_association as $_id_shop_comment) {

                                                    $data_comment = array('comments_name' => $comments_name,
                                                        'comments_email' => $comments_email,
                                                        'comments_comment' => $comments_comment,
                                                        'comments_status' => $comments_status,
                                                        'time_add' => $time_add,
                                                        'id_shop'=>$_id_shop_comment,
                                                        'id_lang' => $select_language,
                                                        'response' => '',
                                                        'id_post'=>$post_id,
                                                        'is_noti' => 0,
                                                        'rating' => 5,

                                                    );

                                                    $this->saveComment($data_comment);

                                                    // updated avg_rating for blog post//
                                                    $result_avg = Db::getInstance()->getRow('select ceil(AVG(`rating`)) AS "avg_rating"
                                                                            from `'._DB_PREFIX_.'blog_comments` where id_post ='.(int)$post_id);
                                                    $result_avg_rating = $result_avg['avg_rating'];

                                                    $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_post` SET `avg_rating` = "'.(int)$result_avg_rating.'" WHERE id ='.(int)$post_id;
                                                    Db::getInstance()->Execute($sql_update);
                                                    // updated avg_rating for blog post//
                                                }


                                            }
                                        }
                                    }

                                }





                            }
                        }




                    }
                    else {
                        $error = 1;
                        $error_text = $this->l('No items to import');
                    }





                }
            } else {
                ### check  for errors ####
                switch($files['error'])
                {
                    case '1':
                        $upload_max_filesize = ini_get('upload_max_filesize');
                        // convert to bytes
                        preg_match('/[0-9]+/', $upload_max_filesize, $match);
                        $upload_max_filesize_in_bytes = $match[0] * 1024 * 1024;
                        // convert to bytes
                        $error_text = $this->l('The size of the uploaded file exceeds the').$upload_max_filesize_in_bytes.'b';
                        break;
                    case '2':
                        $error_text = $this->l('The size of  the uploaded file exceeds the specified parameter  MAX_FILE_SIZE in HTML form.');
                        break;
                    case '3':
                        $error_text = $this->l('Loaded only a portion of the file');
                        break;
                    case '4':
                        $error_text = $this->l('The file was not loaded (in the form user pointed the wrong path  to the file). ');
                        break;
                    case '6':
                        $error_text = $this->l('Invalid  temporary directory.');
                        break;
                    case '7':
                        $error_text = $this->l('Error writing file to disk');
                        break;
                    case '8':
                        $error_text = $this->l('File download aborted');
                        break;
                    case '999':
                    default:
                        $error_text = $this->l('Unknown error code!');
                    break;
                }
                $error = 1;
                ########

            }
        } else {
            $error = 1;
            $error_text = $this->l('Please select the Upload file');
        }

        return array('error'=>$error,'error_text'=>$error_text);

    }


    public function isExistsCategory($data){

        $title = $data['title'];

        $ids_shops = $data['ids_shops'];
        $id_lang = $data['id_lang'];


        foreach($ids_shops as $id_shop) {

            $sql = 'SELECT pc.id
					FROM `' . _DB_PREFIX_ . 'blog_category` pc
					LEFT JOIN `' . _DB_PREFIX_ . 'blog_category_data` pc1
					ON(pc1.id_item = pc.id)
					WHERE pc1.`title` = "' . pSQL($title) . '"
					AND FIND_IN_SET(' . (int)$id_shop . ',pc.ids_shops)
					AND pc1.id_lang = ' . (int)$id_lang . '';

            $item = Db::getInstance()->getRow($sql);

            if($item['id']){

                return $item['id'];
            }

        }

        return isset($item['id'])?$item['id']:0;
    }

    public function getMostPopularLanguageForGallery($data){
        $id_lang = isset($data['id_lang'])?$data['id_lang']:0;
        $is_admin = isset($data['is_admin'])?$data['is_admin']:0;



        if($is_admin){
            $sql = 'SELECT count(*) as count
			FROM `'._DB_PREFIX_.'blog_gallery_data` pc
			WHERE pc.id_lang = '.(int)$id_lang.'
			';
            $count_items = Db::getInstance()->getRow($sql);

            if($count_items['count']>0){
                return  $id_lang;
            }
        }


        $sql = 'SELECT DISTINCT pc.id_lang, (select count(pcc.id_lang) from `'._DB_PREFIX_.'blog_gallery_data` pcc where pcc.id_lang = pc.id_lang) as count_lang
		        FROM `'._DB_PREFIX_.'blog_gallery_data` pc ORDER BY count_lang DESC limit 1';

        $id_lang_data = Db::getInstance()->ExecuteS($sql);

        return isset($id_lang_data[0]['id_lang'])?$id_lang_data[0]['id_lang']:$id_lang;
    }




    public function getMostPopularLanguageForCategories($data){
        $id_lang = isset($data['id_lang'])?$data['id_lang']:0;
        $is_admin = isset($data['is_admin'])?$data['is_admin']:0;



        if($is_admin){
            $sql = 'SELECT count(*) as count
			FROM `'._DB_PREFIX_.'blog_category_data` pc
			WHERE pc.id_lang = '.(int)$id_lang.'
			';
            $count_items = Db::getInstance()->getRow($sql);

            if($count_items['count']>0){
                return  $id_lang;
            }
        }


        $sql = 'SELECT DISTINCT pc.id_lang, (select count(pcc.id_lang) from `'._DB_PREFIX_.'blog_category_data` pcc where pcc.id_lang = pc.id_lang) as count_lang
		        FROM `'._DB_PREFIX_.'blog_category_data` pc ORDER BY count_lang DESC limit 1';

        $id_lang_data = Db::getInstance()->ExecuteS($sql);

        return isset($id_lang_data[0]['id_lang'])?$id_lang_data[0]['id_lang']:$id_lang;




    }


    public function getMostPopularLanguageForPosts($data){
        $id_lang = isset($data['id_lang'])?$data['id_lang']:0;
        $is_admin = isset($data['is_admin'])?$data['is_admin']:0;


        if($is_admin){
            $sql = 'SELECT count(*) as count
			FROM `'._DB_PREFIX_.'blog_post_data` pc
			WHERE pc.id_lang = '.(int)$id_lang.'
			';
            $count_items = Db::getInstance()->getRow($sql);

            if($count_items['count']>0){
                return  $id_lang;
            }
        }


        $sql = 'SELECT DISTINCT pc.id_lang, (select count(pcc.id_lang) from `'._DB_PREFIX_.'blog_post_data` pcc where pcc.id_lang = pc.id_lang) as count_lang
			FROM `'._DB_PREFIX_.'blog_post_data` pc ORDER BY count_lang DESC limit 1';

        $id_lang_data = Db::getInstance()->ExecuteS($sql);

        return isset($id_lang_data[0]['id_lang'])?$id_lang_data[0]['id_lang']:$id_lang;


    }




    public function isExistsAuthor($data){

        $author_id = $data['author_id'];



        $sql = 'SELECT DISTINCT pc.author_id
					FROM  `'._DB_PREFIX_.'blog_post` pc
					LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
					WHERE
					pc.author_id = '.(int)$author_id.' AND ba2c.status != 2
					AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
					AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
					'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
					AND pc.status = 1';


        $data_id = Db::getInstance()->GetRow($sql);
        $author_id = isset($data_id['author_id'])?$data_id['author_id']:0;

        return $author_id;

    }


    public function updateAuthor($data){

        $id_edit = $data['id_edit'];

        $author = $data['author'];
        $info = $data['info'];
        $status = $data['status'];
        $id_customer = $data['id_customer'];

        $is_del = $data['is_del'];

        $is_show = $data['is_show'];


        include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
        $obj = new userprofileblockblog();

        $info_customer = $obj->getCustomerInfo(array('id_customer'=>$id_edit,'is_admin'=>1));


        $id_shop = isset($info_customer['result']['id_shop'])?$info_customer['result']['id_shop']:0;


        $data_author = $this->getDataFromAuthorAdmin(array('id'=>$id_edit,'id_shop'=>$id_shop));

        $post_id = isset($data_author['data'][0]['id'])?$data_author['data'][0]['id']:0;



        $info_customer = $obj->getCustomerInfo(array('id_customer'=>$id_customer,'is_admin'=>1));


        $status_before_save = isset($info_customer['result']['status'])?$info_customer['result']['status']:0;



        if($is_del){
            // delete all posts by author


            $step = 1000000;

            // 2. delete posts by customer
            $_data = $this->getPostsForAuthor(array('start' => 0, 'step' => $step, 'author_id' => $id_customer, 'is_gdpr' => 1));
            $posts = $_data['posts'];

            foreach($posts as $post) {
                $id = (int)$post['id'];
                $this->deletePost(array('id' => $id));

                // 3.delete likes and views
                $this->deleteLikesAndViewsGDPR(array('id' => $id));


            }

            $this->sendNotificationToCustomerWhenDeletedAllPostsFromAdmin(array('id'=>$id_edit,'author'=>$author));

            // delete all posts by author
        } else {
            // update author name
            $sql = 'UPDATE `'._DB_PREFIX_.'blog_post` SET
							  `author` = "'.pSQL($author).'"

							    WHERE id = '.(int)$post_id.'
							   ';
            Db::getInstance()->Execute($sql);
            // update author name
        }




        // update
        $sql = 'UPDATE `'._DB_PREFIX_.'blog_avatar2customer` SET
							   `info` = \''.pSQL($info).'\',
							   `is_show` = \''.pSQL($is_show).'\',
							   `status` = \''.pSQL($status).'\'
							   WHERE id_customer = '.(int)$id_edit.'
							   ';
        Db::getInstance()->Execute($sql);



        include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
        $obj = new blockblog();
        $error = 0;
        $error_text = '';

        if($obj->is_demo == 0) {

            if($id_customer) {

                $post_images_ava = $data['post_images_ava'];
                $error_data = $this->saveImageAvatar(array('id' => $post_id, 'post_images' => $post_images_ava, 'id_customer' => $id_customer));

                $error = $error_data['error'];
                $error_text = $error_data['error_text'];
            }
        }



        // change status of the comment send email to admin
        if($status_before_save != 1 && $status_before_save != $status){

            $this->sendNotificationToCustomerWhenChandedStatusFromAdmin(array('author'=>$author,'status'=>$status,'id'=>$id_edit));

        }
        // change status of the comment send email to admin

        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //

        return array('error'=>$error,'error_text'=>$error_text);

    }




    public function sendNotificationToCustomerWhenDeletedAllPostsFromAdmin($data = null){

        if(Configuration::get($this->_name.'noti') == 1 && Configuration::get($this->_name.'is_deleteallpoststocust') == 1){


            $id = $data['id'];

            include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
            $obj_uprofile = new userprofileblockblog();


            $info_customer_db = $obj_uprofile->getInfoCustomerDB(array('id_customer' => $id));
            $email = $info_customer_db['email'];

            $author = $data['author'];

            /* Email generation */
            $templateVars = array(
                '{name}' => $author,

            );

            $cookie = $this->context->cookie;
            $id_lang = (int)$cookie->id_lang;

            /* Email sending */

            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            }
            else {
                $id_lang_current = Language::getIdByIso('en');
            }

            $subject = Configuration::get($this->_name . 'deleteallpoststocust_' . $id_lang_current);



            Mail::Send($id_lang_current, 'customer-deleteallpoststocust', $subject, $templateVars,
                $email, 'Response Form', NULL, NULL,
                NULL, NULL, dirname(__FILE__).'/../mails/');



        }

    }


    public function sendNotificationToCustomerWhenChandedStatusFromAdmin($data = null){

        if(Configuration::get($this->_name.'noti') == 1 && Configuration::get($this->_name.'is_changestatustocust') == 1){

            include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
            $obj = new blockblog();

            $data_translate = $obj->translateItems();
            $c_disabled = $data_translate['c_disabled'];
            $c_enabled = $data_translate['c_enabled'];
            $c_disabled_hidden = $data_translate['c_disabled_hidden'];
            $status_current  = $data['status'];
            switch($status_current){
                case 0:
                    $status = $c_disabled;
                break;
                case 1:
                    $status = $c_enabled;
                break;
                case 2:
                    $status = $c_disabled_hidden;
                break;
            }


            $id = $data['id'];

            include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
            $obj_uprofile = new userprofileblockblog();


            $info_customer_db = $obj_uprofile->getInfoCustomerDB(array('id_customer' => $id));
            $email = $info_customer_db['email'];

            $author = $data['author'];

            /* Email generation */
            $templateVars = array(
                '{name}' => $author,
                '{status}' => $status,
            );



            $cookie = $this->context->cookie;
            $id_lang = (int)$cookie->id_lang;

            /* Email sending */

            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            }
            else {
                $id_lang_current = Language::getIdByIso('en');
            }

            $subject = Configuration::get($this->_name . 'changestatustocust_' . $id_lang_current);



            Mail::Send($id_lang_current, 'customer-changestatustocust', $subject, $templateVars,
                $email, 'Response Form', NULL, NULL,
                NULL, NULL, dirname(__FILE__).'/../mails/');



        }

    }


    public function getPostsForAuthor($_data){

        $is_gdpr = isset($_data['is_gdpr'])?$_data['is_gdpr']:0;

        $author_id = $_data['author_id'];


        $start = $_data['start'];
        $step = $_data['step'];

        $is_my = isset($_data['is_my'])?$_data['is_my']:0;

        if($is_my){
            $sql_cond = 'AND (pc.status = 1 OR pc.status = 0  OR pc.status = 3 OR pc.status = 4)';
        } else {
            $sql_cond = ' '.($is_gdpr?'':'AND pc.status = 1 AND ba2c.status != 2').'
			AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'';
        }

        $cookie = $this->context->cookie;
        $current_language = (int)$cookie->id_lang;

        $order_by = 'ORDER BY pc.`'.Configuration::get($this->_name.'blog_post_order').'` '.Configuration::get($this->_name.'blog_post_ad').'';

        $sql = '
			SELECT pc.* , ba2c.status as status_author,
				(select count(*) as count from `'._DB_PREFIX_.'blog_comments` c2pc
				 where c2pc.id_post = pc.id and c2pc.status = 1 and c2pc.id_shop = '.(int)$this->_id_shop.'
				 and c2pc.id_lang = '.(int)$current_language.' )
				 as count_comments,
				 (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1) as count_like,
                (select count(*) from `'._DB_PREFIX_.'blog_post_like` al where al.post_id = pc.id and al.like = 1 and ip = \''.pSQL($_SERVER['REMOTE_ADDR']).'\') as is_liked_post,
                (select count(*) as count from `'._DB_PREFIX_.'blog_comments` pcc WHERE pcc.id_post = pc.id and pcc.status = 1 and pcc.id_lang = '.(int)$current_language.') as count_comments

			FROM  `'._DB_PREFIX_.'blog_post_data` pc_d LEFT JOIN `'._DB_PREFIX_.'blog_post` pc ON(pc.id = pc_d.id_item)
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE  pc_d.id_lang = '.(int)$current_language.'
			AND pc.author_id = '.(int)$author_id.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops) AND ba2c.id_shop = '.(int)$this->_id_shop.'



			'.$sql_cond.'
			'.$order_by.' LIMIT '.(int)$start.' ,'.(int)$step.'';

        //echo $sql;


        $items = Db::getInstance()->ExecuteS($sql);



        foreach($items as $k1=>$_item){
            $id_post = $_item['id'];



            if(Tools::strlen($_item['img'])>0){
                $this->generateThumbImages(array('img'=>$_item['img'],
                        'width'=>$this->_img_posts_width,
                        'height'=>$this->_img_posts_height
                    )
                );
                $img = Tools::substr($_item['img'],0,-4)."-".$this->_img_posts_width."x".$this->_img_posts_height.".jpg";
            } else {
                $img = $_item['img'];
            }

            $items[$k1]['img'] = $img;

            $category_ids = Db::getInstance()->ExecuteS('
				SELECT pc.category_id
				FROM `'._DB_PREFIX_.'blog_category2post` pc
				WHERE pc.`post_id` = '.(int)$id_post.'');
            $data_category_ids = array();
            foreach($category_ids as $k => $v){
                $_info_ids = $this->getCategoryItem(array('id' => $v['category_id']));

                $category_info = !empty($_info_ids['category'][0])?$_info_ids['category'][0]:array();
                $ids_item = sizeof($category_info)>0?$category_info:array();
                //var_dump($ids_item); echo "<br><hr><br>";
                if(sizeof($ids_item)>0){
                    $data_category_ids[] = $ids_item;
                }
            }


            $items[$k1]['category_ids'] = $is_gdpr?json_encode($data_category_ids):$data_category_ids;




            $items[$k1]['author'] = $_item['author'];
            $author_id = $_item['author_id'];
            $items[$k1]['author_id'] = $author_id;

            $info_path = $this->getAvatarPath(array('id_customer'=>$author_id,'id_shop'=>$this->_id_shop));

            $items[$k1]['avatar'] = $info_path['avatar'];
            $items[$k1]['is_show_ava'] = $info_path['is_show'];


            $items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');

            foreach ($items_data as $item_data){

                if($current_language == $item_data['id_lang']){
                    $items[$k1]['title'] = $item_data['title'];
                    $items[$k1]['seo_description'] = $item_data['seo_description'];
                    $items[$k1]['seo_keywords'] = $item_data['seo_keywords'];
                    $items[$k1]['content'] = $item_data['content'];
                    $items[$k1]['id'] = $_item['id'];
                    $items[$k1]['time_add'] = $_item['time_add'];
                    $items[$k1]['seo_url'] = $item_data['seo_url'];
                }
            }

        }

        $sql = '
			SELECT COUNT(`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_post_data` pc_d LEFT JOIN `'._DB_PREFIX_.'blog_post` pc ON(pc.id = pc_d.id_item)
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE pc.author_id = '.(int)$author_id.'
			and pc_d.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops) AND ba2c.id_shop = '.(int)$this->_id_shop.'

			'.$sql_cond.'
			';

        //echo $sql;

        $data_count_posts = Db::getInstance()->getRow($sql);

        return array('posts' => $items, 'count_all' => $data_count_posts['count'] );
    }


    public function getDataFromAuthorAdmin($_data){
        $id = $_data['id'];
        $id_shop = isset($_data['id_shop'])?$_data['id_shop']:0;


            $sql = '
                SELECT pc2.*
                FROM  `' . _DB_PREFIX_ . 'blog_post` pc2

                WHERE pc2.`author_id` = ' . (int)$id . '
                  LIMIT 1
                ';

        $data = Db::getInstance()->ExecuteS($sql);


        $sql_count_all = 'select count(*) as count from `'._DB_PREFIX_.'blog_post` bp
				    WHERE bp.author_id = '.(int)$id.' and FIND_IN_SET('.(int)$id_shop.',bp.ids_shops)';

        $data_count_all = Db::getInstance()->getRow($sql_count_all);

        $sql_count_active = 'select count(*) as count from `'._DB_PREFIX_.'blog_post` bp
				    WHERE bp.author_id = '.(int)$id.' and FIND_IN_SET('.(int)$id_shop.',bp.ids_shops) and status = 1';

        $data_count_active = Db::getInstance()->getRow($sql_count_active);


        $sql_count_noactive = 'select count(*) as count from `'._DB_PREFIX_.'blog_post` bp
				    WHERE bp.author_id = '.(int)$id.' and FIND_IN_SET('.(int)$id_shop.',bp.ids_shops) and status != 1';

        $data_count_noactive = Db::getInstance()->getRow($sql_count_noactive);




        return array('data' => $data, 'data_count_all'=>$data_count_all,'data_count_active'=>$data_count_active,'data_count_noactive'=>$data_count_noactive);
    }


    public function getCustomerInfo($data){

        $id_customer = $data['id_customer'];


        if($id_customer == 0){

            include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
            $obj = new userprofileblockblog();

            $customer_name = $obj->getAdminName();
            $email =  Configuration::get($this->_name.'mail');


        } else {

            $customer_obj = new Customer($id_customer);

            $firstname = $customer_obj->firstname;
            $lastname = $customer_obj->lastname;
            $email = $customer_obj->email;

            if(Tools::strlen($firstname)>0 || Tools::strlen($lastname)>0) {
                $customer_name = $firstname . ' ' . $lastname;
            } else {
                $customer_name = '';
            }
        }

        return array('customer_name'=>$customer_name,'email'=>$email);
    }




    public function getAvatarPath($data){


        include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
        $obj = new userprofileblockblog();

        return $obj->getAvatarPath($data);
    }


    public function saveImageAvatar($data = null){
        include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
        $obj = new userprofileblockblog();

        return $obj->saveImageAvatar($data);


    }


    public function deleteAvatar($data){
        include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
        $obj = new userprofileblockblog();


        $id = (int)$data['id'];
        $is_admin = (int)$data['is_admin'];
        $id_customer = (int)$data['id_customer'];
        $info_post = $this->getPostItem(array('id'=>$id,'is_admin'=>$is_admin,'id_customer'=>$id_customer));
        $img = $info_post['post'][0]['avatar'];
        $data['avatar'] = $img;

        return $obj->deleteAvatar($data);
    }


    public function getCountSliders(){
        $cookie = $this->context->cookie;
        $current_language = (int)$cookie->id_lang;

        $sql = '
			SELECT count(*) as count
			FROM `'._DB_PREFIX_.'blog_post` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			on(pc.id = pc_d.id_item)
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE pc.status = 1 and pc_d.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
            AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups)
            AND ba2c.status != 2 AND ba2c.id_shop = '.(int)$this->_id_shop.'
            AND pc.img != \'\' AND pc.is_slider = 1
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			';

         $items = Db::getInstance()->getRow($sql);
        $count_items = isset($items['count'])?$items['count']:0;
        return $count_items;
    }

    public function getSliderForPosts($_data = null){

            $is_mobile = isset($_data['is_mobile'])?$_data['is_mobile']:0;


            $limit = Configuration::get($this->_name.'slide_limit');
            $slider_titlefancytr = Configuration::get($this->_name.'slider_titlefancytr');

            $cookie = $this->context->cookie;
            $current_language = (int)$cookie->id_lang;

            $order_by = 'ORDER BY pc.`'.Configuration::get($this->_name.'blog_post_order').'` '.Configuration::get($this->_name.'blog_post_ad').'';

            $sql = '
			SELECT pc.*, ba2c.status as status_author
			FROM `'._DB_PREFIX_.'blog_post` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_post_data` pc_d
			on(pc.id = pc_d.id_item)
			LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` ba2c ON(pc.author_id = ba2c.id_customer)
			WHERE pc.status = 1 and pc_d.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$this->_id_shop.',pc.ids_shops)
            AND FIND_IN_SET('.(int)$this->_id_group.',pc.ids_groups) AND ba2c.id_shop = '.(int)$this->_id_shop.'
            AND ba2c.status != 2
            AND pc.img != \'\' AND pc.is_slider = 1
			'.(($this->_is_show_now)?'AND (pc.`publish_date` <= NOW())':'').'
			'.$order_by.' LIMIT '.(int)$limit.'';



            $items = Db::getInstance()->ExecuteS($sql);

            foreach($items as $k1=>$_item){

                if(Tools::strlen($_item['img'])>0){
                    $this->generateThumbImages(array('img'=>$_item['img'],
                            'width'=>(($is_mobile)?$this->_img_slider_width_mobile:$this->_img_slider_width),
                            'height'=>(($is_mobile)?$this->_img_slider_height_mobile:$this->_img_slider_height)
                        )
                    );
                    $img = Tools::substr($_item['img'],0,-4)."-".
                            (($is_mobile)?$this->_img_slider_width_mobile:$this->_img_slider_width).
                            "x".
                            (($is_mobile)?$this->_img_slider_height_mobile:$this->_img_slider_height).
                            ".jpg";
                } else {
                    $img = $_item['img'];
                }

                $items[$k1]['img'] = $img;


                $items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_post_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');

                foreach ($items_data as $item_data){

                    if($current_language == $item_data['id_lang']){

                        $title = Tools::substr($item_data['title'],0,$slider_titlefancytr);
                        if(Tools::strlen($item_data['title'])>$slider_titlefancytr){
                            $title = $title."...";
                        }

                        $items[$k1]['title'] = $title;


                        $items[$k1]['content'] = $item_data['content'];
                        $items[$k1]['id'] = $_item['id'];
                        $items[$k1]['seo_url'] = $item_data['seo_url'];
                    }
                }

            }



        return array('slides' => $items );
    }


    public function getGalleryItem($_data){
        $id = $_data['id'];

            $sql = '
					SELECT pc.*
					FROM `'._DB_PREFIX_.'blog_gallery` pc
					WHERE id = '.(int)$id;
            $item = Db::getInstance()->ExecuteS($sql);

            foreach($item as $k => $_item){

                $items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_gallery_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');

                foreach ($items_data as $item_data){
                    $item['data'][$item_data['id_lang']]['title'] = $item_data['title'];

                    $item['data'][$item_data['id_lang']]['content'] = $item_data['content'];
                    $item['data'][$item_data['id_lang']]['time_add'] = $_item['time_add'];
                    $item['data'][$item_data['id_lang']]['status'] = $_item['status'];
                }


            }

        return array('gallery' => $item);
    }


    public function saveGallery($data){

        $status = (int)$data['status'];
        $is_featured = (int)$data['is_featured'];

        $ids_shops = implode(",",$data['cat_shop_association']);


        $time_add = $data['time_add'];

        $sql = 'INSERT into `'._DB_PREFIX_.'blog_gallery` SET
							   `ids_shops` = "'.pSQL($ids_shops).'",
							    time_add = "'.pSQL($time_add).'",
							    is_featured = '.(int)$is_featured.',
							    status = '.(int)$status.'
							   ';

        Db::getInstance()->Execute($sql);

        $post_id = Db::getInstance()->Insert_ID();


        ## set  position ##

        $position_id = $post_id-1;
        Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'blog_gallery
							SET position = '. (int)($position_id) .' WHERE id = ' . (int)($post_id));


        ## set  position ##


        foreach($data['data_title_content_lang'] as $language => $item){

            $gallery_title = $item['gallery_title'];
            $content = $item['content'];

            $sql = 'INSERT into `'._DB_PREFIX_.'blog_gallery_data` SET
							   `id_item` = \''.(int)($post_id).'\',
							   `id_lang` = \''.(int)($language).'\',
							   `title` = \''.pSQL($gallery_title).'\',
							   `content` = \''.pSQL($content).'\'
							   ';

            Db::getInstance()->Execute($sql);

        }


        include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
        $obj = new blockblog();

        if($obj->is_demo == 0) {


            $error_data = $this->saveImage(array('post_id' => $post_id, 'is_gallery'=>1));
            $error = $error_data['error'];
            $error_text = $error_data['error_text'];
        } else {
            $error = 0;
            $error_text = '';
        }



        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //

        return array('error'=>$error,'error_text'=>$error_text,'post_id'=>$post_id);


    }

    public function updateGallery($data){

        $id = (int)$data['id_editcategory'];
        $status = (int)$data['status'];
        $is_featured = (int)$data['is_featured'];

        $ids_shops = implode(",",$data['cat_shop_association']);
        $time_add = $data['time_add'];



        $post_images = $data['post_images'];

        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_gallery` SET
						`ids_shops` = "'.pSQL($ids_shops).'", `time_add` = "'.pSQL($time_add).'", is_featured = '.(int)$is_featured.', status = '.(int)$status.'
						WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);

        /// delete tabs data
        $sql = 'DELETE FROM `'._DB_PREFIX_.'blog_gallery_data` WHERE id_item = '.(int)$id.'';
        Db::getInstance()->Execute($sql);

        foreach($data['data_title_content_lang'] as $language => $item){

            $gallery_title = $item['gallery_title'];
            $content = $item['content'];


            $sql = 'INSERT into `'._DB_PREFIX_.'blog_gallery_data` SET
							   `id_item` = \''.(int)($id).'\',
							   `id_lang` = \''.(int)($language).'\',
							   `title` = \''.pSQL($gallery_title).'\',
							   `content` = \''.pSQL($content).'\'

							   ';
            Db::getInstance()->Execute($sql);

        }


        include_once(_PS_MODULE_DIR_.$this->_name.'/blockblog.php');
        $obj = new blockblog();

        if($obj->is_demo == 0) {
            $error_data = $this->saveImage(array('post_id' => $id, 'is_gallery'=>1,'post_images'=>$post_images));
            $error = $error_data['error'];
            $error_text = $error_data['error_text'];
        } else {
            $error = 0;
            $error_text = '';
        }


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //


        return array('error'=>$error,'error_text'=>$error_text);
    }

    public function updateGalleryStatus($data){

        $id = (int)$data['id'];
        $status = (int)$data['status'];

        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_gallery` SET
						`status` = '.(int)$status.'
						WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //

    }

    public function updateGalleryFeatured($data){

        $id = (int)$data['id'];
        $status = (int)$data['status'];

        $sql_update = 'UPDATE `'._DB_PREFIX_.'blog_gallery` SET
						`is_featured` = '.(int)$status.'
						WHERE id ='.(int)$id;
        Db::getInstance()->Execute($sql_update);


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //

    }

    public function deleteGallery($data){

        $id = $data['id'];


        $this->deleteImg(array('id' => $id,'is_gallery'=>1));

        $sql = 'DELETE FROM `'._DB_PREFIX_.'blog_gallery`
							   WHERE id ='.(int)$id.'';
        Db::getInstance()->Execute($sql);

        $sql = 'DELETE FROM `'._DB_PREFIX_.'blog_gallery_data`
							   WHERE id_item ='.(int)$id.'';
        Db::getInstance()->Execute($sql);


        // clear cache only for blog //
        $this->clearSmartyCacheBlog();
        // clear cache only for blog //



    }



    public function getGallery($_data){

            $is_featured = isset($_data['is_featured'])?$_data['is_featured']:0;

            $is_home = isset($_data['is_home'])?$_data['is_home']:0;
            $is_left_right_footer = isset($_data['is_left_right_footer'])?$_data['is_left_right_footer']:0;


            if($is_home) {
                $limit = 'LIMIT '.(int)Configuration::get($this->_name.'blog_galleryh');
            } elseif($is_left_right_footer){
                $limit = 'LIMIT '.(int)Configuration::get($this->_name.'blog_gallery');
            } else {
                $start = $_data['start'];
                $step = (int)$_data['step'];

                $limit = 'LIMIT '.(int)$start.' ,'.(int)$step.'';
            }


            $cond = '';
            if($is_featured){
                $cond = ' AND pc.is_featured = 1 ';
            }

            // site handler

            $id_shop = isset($_data['id_shop'])?$_data['id_shop']:$this->_id_shop;

            $cookie = $this->context->cookie;
            $current_language = isset($_data['id_lang'])?$_data['id_lang']:(int)$cookie->id_lang;


            $items_tmp = Db::getInstance()->ExecuteS('
			SELECT pc.*

			FROM `'._DB_PREFIX_.'blog_gallery` pc
			LEFT JOIN `'._DB_PREFIX_.'blog_gallery_data` pc_d
			on(pc.id = pc_d.id_item)
			WHERE pc_d.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$id_shop.',pc.ids_shops)
            AND pc.status = 1 '.$cond.'
            ORDER BY pc.`position` DESC  '.$limit.'');

            $items = array();

            foreach($items_tmp as $k => $_item){


                $items[$k]['img'] = $_item['img'];

                ## generate thumbs ###
                if(Tools::strlen($_item['img'])>0){
                    $this->generateThumbImages(
                        array(
                            'img'=>$_item['img'],
                            'width'=>$this->_img_gallery_width,
                            'height'=>$this->_img_gallery_height,
                            'is_gallery'=>1,
                        )
                    );
                    $img = Tools::substr($_item['img'],0,-4)."-".$this->_img_gallery_width."x".$this->_img_gallery_height.".jpg";
                    $items[$k]['img_thumb'] = $img;

                    $this->generateThumbImages(
                        array(
                            'img'=>$_item['img'],
                            'width'=>$this->_img_gallery_width_block,
                            'height'=>$this->_img_gallery_width_block,
                            'is_gallery'=>1,
                        )
                    );
                    $img = Tools::substr($_item['img'],0,-4)."-".$this->_img_gallery_width_block."x".$this->_img_gallery_width_block.".jpg";
                    $items[$k]['img_thumb_block'] = $img;

                    $this->generateThumbImages(
                        array(
                            'img'=>$_item['img'],
                            'width'=>$this->_img_gallery_width_block_home,
                            'height'=>$this->_img_gallery_width_block_home,
                            'is_gallery'=>1,
                        )
                    );
                    $img = Tools::substr($_item['img'],0,-4)."-".$this->_img_gallery_width_block_home."x".$this->_img_gallery_width_block_home.".jpg";
                    $items[$k]['img_thumb_block_home'] = $img;
                } else {
                    $img = $_item['img'];
                    $items[$k]['img_thumb'] = $img;
                }





                ## generate thumbs ###


                $items_data = Db::getInstance()->ExecuteS('
				SELECT pc.*
				FROM `'._DB_PREFIX_.'blog_gallery_data` pc
				WHERE pc.id_item = '.(int)$_item['id'].'
				');



                foreach ($items_data as $item_data){

                    if($current_language == $item_data['id_lang']){
                        $items[$k]['title'] = $item_data['title'];
                        $items[$k]['id'] = $_item['id'];
                        $items[$k]['time_add'] = $_item['time_add'];
                        $items[$k]['content'] = $item_data['content'];
                    }
                }
            }

            $data_count_items = Db::getInstance()->getRow('
			SELECT COUNT(pc.`id`) AS "count"
			FROM `'._DB_PREFIX_.'blog_gallery` pc LEFT JOIN `'._DB_PREFIX_.'blog_gallery_data` pc_d
			on(pc.id = pc_d.id_item)
			WHERE pc_d.id_lang = '.(int)$current_language.'
			AND FIND_IN_SET('.(int)$id_shop.',pc.ids_shops)
			AND pc.status = 1
             '.$cond.'
			');

            // site handler

        return array('gallery' => $items, 'count_all' => $data_count_items['count'] );
    }





    public function deleteGDPRCustomerData($email){
        $data_customer = Customer::getCustomersByEmail($email);
        if(count($data_customer)>0) {
            $id_customer = $data_customer[0]['id_customer'];

            // 1. delete avatar //
            include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
            $obj_userprofileblockblog = new userprofileblockblog();

            $info_path = $obj_userprofileblockblog->getAvatarPath(array('id_customer'=>$id_customer));
            $avatar = $info_path['avatar_thumb'];
            if(Tools::strlen($avatar)>0) {
                $obj_userprofileblockblog->deleteAvatarGDPR(array('id_customer' => $id_customer, 'avatar' => $avatar));
            }

            $step = 1000000;

            // 2. delete posts by customer
            $_data = $this->getPostsForAuthor(array('start' => 0, 'step' => $step, 'author_id' => $id_customer, 'is_my' => 1));
            $posts = $_data['posts'];

            foreach($posts as $post) {
                $id = (int)$post['id'];
                $this->deletePost(array('id' => $id));

                // 3.delete likes and views
                $this->deleteLikesAndViewsGDPR(array('id' => $id));

            }

            // 4. delete commnets by customer //
            $_data = $this->getComments(array('start' => 0, 'step' => $step, 'id_customer' => $id_customer, 'admin' => 3));
            $comments = $_data['comments'];
            foreach($comments as $comment) {
                $id = (int)$comment['id'];
                $this->deleteComment(array('id' => $id));
            }

            // loyalty
            include_once(_PS_MODULE_DIR_ . $this->_name . '/blockblog.php');
            $obj_blockblog = new blockblog();
            $_prefix = $obj_blockblog->getLoyalityDbPrefix();

            $sql =  'DELETE FROM `'._DB_PREFIX_.$_prefix.'_loyalty` WHERE id_customer = ' . (int)$id_customer;
            Db::getInstance()->Execute($sql);

        }

        return true;



    }


    private function deleteLikesAndViewsGDPR($data){
        $id = $data['id'];
        // views
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'blog_views_statistics` WHERE post_id = ' . (int)$id;
        Db::getInstance()->Execute($sql);
        // likes
        $sql =  'DELETE FROM `'._DB_PREFIX_.'blog_post_like` WHERE post_id = ' . (int)$id;
        Db::getInstance()->Execute($sql);

    }



    public function getGroupPermissionsForBlog(){

        $cookie = $this->context->cookie;
        $id_customer_cookie = isset($cookie->id_customer)?$cookie->id_customer:0;


        ## group ##
        if(!$id_customer_cookie) {
            $_id_group = (int) Group::getCurrent()->id;
        } else {
            $_id_group = Customer::getDefaultGroupId($id_customer_cookie);
        }
        ## group ##




        $ids_groups = Configuration::get($this->_name.'blog_ids_groups');
        $ids_groups = explode(",",$ids_groups);
        if(in_array($_id_group,$ids_groups)){
            $is_show = 1;
        } else {
            $is_show = 0;
        }



        return array('is_show'=>$is_show,);
    }


    public function getGDPRCustomerData($email){
        $data_customer = Customer::getCustomersByEmail($email);
        $customer_data = array();
        if(count($data_customer)>0) {
            $id_customer = $data_customer[0]['id_customer'];

            $step = 1000000;

            // 1. get posts by customer
            $_data = $this->getPostsForAuthor(array('start' => 0, 'step' => $step, 'author_id' => $id_customer, 'is_my' => 1, 'is_gdpr'=> 1));
            $posts = $_data['posts'];
            if(count($posts)>0) {


                $customer_data['customer_blog_posts'] = json_encode($posts);


            }

            // 2. get avatar //
            include_once(_PS_MODULE_DIR_.$this->_name.'/classes/userprofileblockblog.class.php');
            $obj_userprofileblockblog = new userprofileblockblog();
            $info_path = $obj_userprofileblockblog->getAvatarPath(array('id_customer'=>$id_customer));

            $avatar = $info_path['avatar_thumb'];
            if(Tools::strlen($avatar)>0) {
                $customer_data['customer_avatar'] = json_encode($info_path['avatar']);
            }



            // 3. get comments by customer //
            $_data = $this->getComments(array('start' => 0, 'step' => $step, 'id_customer' => $id_customer, 'admin' => 3));
            $comments = $_data['comments'];
            if(count($comments)>0) {
                $customer_data['customer_blog_comments'] = json_encode($comments);
            }

            // loyalty
            include_once(_PS_MODULE_DIR_ . $this->_name . '/blockblog.php');
            $obj_blockblog = new blockblog();
            $_prefix = $obj_blockblog->getLoyalityDbPrefix();

            $sql = 'SELECT * FROM `'._DB_PREFIX_.$_prefix.'_loyalty` pc
		        	WHERE  `id_customer` = '.(int)$id_customer.'
		        	';
            $result_loyalty = Db::getInstance()->ExecuteS($sql);
            if(count($result_loyalty)>0)
                $customer_data['loyalty'] = json_encode($result_loyalty);


        }

        return $customer_data;

    }


    public function clearSmartyCacheBlog($data = null){
        include_once(_PS_MODULE_DIR_.$this->_name.'/classes/cacheblockblog.class.php');
        $obj = new cacheblockblog();
        $obj->clearSmartyCacheModule($data);
    }


}