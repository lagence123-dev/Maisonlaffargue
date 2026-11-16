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
class userprofileblockblog extends Module {
	
	private $_step;
	private $_http;

    private $_name;
    private $_http_host;
    private $_is_cloud;
    private $_prefix = "r";
    private $_id_shop;
    private $_admin_name = 'admin';
    private $_privilegies = array(0,2);
	
	
	public function __construct(){
		parent::__construct();

        $this->_name = "blockblog";


		$this->_step = (int)Configuration::get($this->_name.$this->_prefix.'page_shoppers');
		$this->_http = $this->_http();

        if(version_compare(_PS_VERSION_, '1.6', '>')){

            $this->_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
        } else {
            $this->_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
        }

        if(version_compare(_PS_VERSION_, '1.5', '>')){
            $this->_id_shop = Context::getContext()->shop->id;
        } else {
            $this->_id_shop = 0;
        }


        if (defined('_PS_HOST_MODE_'))
            $this->_is_cloud = 1;
        else
            $this->_is_cloud = 0;


        // for test
        //$this->_is_cloud = 1;
        // for test

//        if($this->_is_cloud){
//            $this->path_img_cloud = DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;
//        } else {
//            $this->path_img_cloud = DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR;
//        }
        $this->path_img_cloud = DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;

		

		
		$this->initContext();
	}
	
	private function initContext()
	{
		$this->context = Context::getContext();
	}

    public function getObjectParent(){
        include_once(_PS_MODULE_DIR_.$this->_name .'/blockblog.php');
        $obj = new blockblog();
        return $obj;
    }


    public function getAdminName(){
        return $this->_admin_name;
    }


    public function getShoppersList($data = null){
		
		
		$start = $data['start'];
		$step = isset($data['step'])?$data['step']:$this->_step;

		$search = isset($data['search'])?$data['search']:0;
        $is_search = isset($data['is_search'])?$data['is_search']:0;

        $id_shop = isset($data['id_shop'])?$data['id_shop']:$this->_id_shop;

        $cookie = $this->context->cookie;
        $current_language = isset($data['id_lang'])?$data['id_lang']:(int)$cookie->id_lang;

        $sql_shop = '';
        if($id_shop){
            $sql_shop .= ' AND pc.id_shop = '.(int)$id_shop;
        }

        $sql_condition_search = '';
        if($is_search == 1){
            $sql_condition_search = " AND
                    (bp.author_id
                        IN(
                            SELECT id_customer FROM  `"._DB_PREFIX_."customer` pc
                            WHERE pc.active = 1 AND pc.deleted = 0 ".$sql_shop."
                            AND (
                                LOWER(pc.lastname) LIKE BINARY LOWER('%".pSQL($search)."%')
                                    OR
                                LOWER(pc.firstname) LIKE BINARY LOWER('%".pSQL($search)."%')
                                )

                        )
                    )
                    OR (
                            LOWER(bp.author) LIKE BINARY LOWER('%".pSQL($search)."%')
                            and a2c.status != 2 AND a2c.status != 0
                         )

                     ";
        }




        $sql_ids_authors_with_admin = 'SELECT DISTINCT bp.author_id, a2c.status as status_author,
        (select count(*) from ' . _DB_PREFIX_ . 'blog_post pbp where pbp.author_id = bp.author_id and pbp.status = 1 AND
        FIND_IN_SET('.(int)$id_shop.',pbp.ids_shops)) as count_posts

        FROM  ' . _DB_PREFIX_ . 'blog_post bp
        LEFT JOIN ' . _DB_PREFIX_ . 'blog_post_data bpd on(bp.id = bpd.id_item)
         LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` a2c
		on(a2c.id_customer = bp.author_id)


        where bp.status = 1 AND bpd.id_lang = '.(int)$current_language.' AND a2c.status != 2 AND a2c.status != 0 AND (a2c.`is_show` = 1 OR a2c.`is_show` IS NULL) AND a2c.id_shop = '.(int)$id_shop.'  AND FIND_IN_SET('.(int)$id_shop.',bp.ids_shops) '.$sql_condition_search.'
        ORDER BY count_posts DESC LIMIT '.(int)$start.' ,'.(int)$step.'';




        $customers_ids = Db::getInstance()->ExecuteS($sql_ids_authors_with_admin);

        $customers = array();
        $i = 0;
        foreach($customers_ids as $customer_ids_data) {

            $id_customer = $customer_ids_data['author_id'];
            $count_posts = $customer_ids_data['count_posts'];

            $info_customer_db = $this->getInfoCustomerDB(array('id_customer' => $id_customer));



            if($id_customer == 0){
                // for admin customer (admin id_customer = 0)
                $sql_customer = '
                            SELECT a2c.avatar_thumb, a2c.info
                            FROM  `' . _DB_PREFIX_ . 'blog_avatar2customer` a2c
                            WHERE (a2c.`is_show` = 1 OR a2c.`is_show` IS NULL)
                            AND a2c.id_shop = ' . (int)$id_shop . '
                            and a2c.id_customer = ' . (int)$id_customer . '';
            } else {
                $sql_customer = '
                            SELECT pc.*, a2c.avatar_thumb, a2c.info
                            FROM `' . _DB_PREFIX_ . 'customer` pc LEFT JOIN `' . _DB_PREFIX_ . 'blog_avatar2customer` a2c
                            on(a2c.id_customer = pc.id_customer)
                            WHERE pc.active = 1 AND pc.deleted = 0 ' . $sql_shop . ' AND (a2c.`is_show` = 1 OR a2c.`is_show` IS NULL)
                            AND a2c.id_shop = ' . (int)$id_shop . '
                            and pc.id_customer = ' . (int)$id_customer . '';
            }


            $_item_customer = Db::getInstance()->getRow($sql_customer);





            $customer_data = $this->getCustomerInfo(array('id_customer' => $id_customer));
            $id_gender = isset($_item_customer['id_gender'])?$_item_customer['id_gender']:$customer_data['id_gender'];
            $avatar_thumb = isset($customer_data['avatar_thumb'])?$customer_data['avatar_thumb']:$_item_customer['avatar_thumb'];

            $info = isset($customer_data['info'])?$_item_customer['info']:'';

            $firstname = isset($info_customer_db['firstname'])?$info_customer_db['firstname']:'';
            $lastname = isset($info_customer_db['lastname'])?$info_customer_db['lastname']:'';



            $customers[$i]['customer_name'] = isset($firstname)?($firstname.' '.$lastname):$this->getAdminName();


            // only if customer was been deleted in the admin panel from table ps_customers
            if($id_customer != 0){
                if(!$info_customer_db){

                    $sql_deleted_author_info = 'SELECT DISTINCT bp.author
                    FROM  ' . _DB_PREFIX_ . 'blog_post bp where  bp.author_id = '.(int)$id_customer.'
                    ';

                    $_item_deleted_author_info = Db::getInstance()->getRow($sql_deleted_author_info);

                    $customers[$i]['customer_name'] = isset($_item_deleted_author_info['author'])?$_item_deleted_author_info['author']:$this->getAdminName();
                }
            }
            // only if customer was been deleted in the admin panel from table ps_customers




            // user with avatar
            $info_path = $this->getAvatarPath(array('id_gender' => $id_gender, 'avatar' => $avatar_thumb, 'id_customer' => $id_customer, 'is_user' => 1));

            $customers[$i]['avatar_thumb'] = $info_path['avatar'];
            $customers[$i]['exist_avatar'] = $info_path['is_exist'];
            $customers[$i]['count_posts'] = $count_posts;
            $customers[$i]['id_customer'] = $id_customer;
            $customers[$i]['info'] = $info;

            $i++;
        }



        $sql_count = 'SELECT count(DISTINCT bp.author_id) as count
        FROM  ' . _DB_PREFIX_ . 'blog_post bp
        LEFT JOIN ' . _DB_PREFIX_ . 'blog_post_data bpd on(bp.id = bpd.id_item)
        LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` a2c
		on(a2c.id_customer = bp.author_id)

        where bp.status = 1 AND bpd.id_lang = '.(int)$current_language.' AND a2c.status != 2 AND a2c.status != 0 AND (a2c.`is_show` = 1 OR a2c.`is_show` IS NULL)
        AND a2c.id_shop = '.(int)$id_shop.'
        AND FIND_IN_SET('.(int)$id_shop.',bp.ids_shops) '.$sql_condition_search.'';

        $data_count_customers = Db::getInstance()->getRow($sql_count);
		
		return array('customers' => $customers, 
					'data_count_customers' => $data_count_customers['count'],
					);
	}
	
	
	public function getShoppersBlock($data = null){

        $cookie = $this->context->cookie;
        $current_language = isset($data['id_lang'])?$data['id_lang']:(int)$cookie->id_lang;

        $sql_shop = '';
        if($this->_id_shop){
            $sql_shop .= ' AND pc.id_shop = '.(int)$this->_id_shop;
        }
		
		$start = $data['start'];
		$step = isset($data['step'])?$data['step']:$this->_step;

        $sql_ids_authors_with_admin = 'SELECT DISTINCT bp.author_id, a2c.status as status_author,
        (select count(*) from ' . _DB_PREFIX_ . 'blog_post pbp where pbp.author_id = bp.author_id and pbp.status = 1 AND FIND_IN_SET('.(int)$this->_id_shop.',pbp.ids_shops)) as count_posts
        FROM  ' . _DB_PREFIX_ . 'blog_post bp
        LEFT JOIN ' . _DB_PREFIX_ . 'blog_post_data bpd on(bp.id = bpd.id_item)
        LEFT JOIN `'._DB_PREFIX_.'blog_avatar2customer` a2c

		on(a2c.id_customer = bp.author_id)

        where bp.status = 1 AND bpd.id_lang = '.(int)$current_language.' AND a2c.status != 2 AND a2c.status != 0 AND (a2c.`is_show` = 1 OR a2c.`is_show` IS NULL) AND a2c.id_shop = '.(int)$this->_id_shop.' AND FIND_IN_SET('.(int)$this->_id_shop.',bp.ids_shops)
        ORDER BY count_posts DESC LIMIT '.(int)$start.' ,'.(int)$step.'';


        $customers_ids = Db::getInstance()->ExecuteS($sql_ids_authors_with_admin);

        $customers = array();
        $i = 0;
        foreach($customers_ids as $customer_ids_data) {

            $id_customer = $customer_ids_data['author_id'];
            $count_posts = $customer_ids_data['count_posts'];


            $info_customer_db = $this->getInfoCustomerDB(array('id_customer' => $id_customer));




            $sql_customer = '
                            SELECT pc.*, a2c.avatar_thumb
                            FROM `' . _DB_PREFIX_ . 'customer` pc LEFT JOIN `' . _DB_PREFIX_ . 'blog_avatar2customer` a2c
                            on(a2c.id_customer = pc.id_customer)
                            WHERE pc.active = 1 AND pc.deleted = 0 AND a2c.id_shop = '.(int)$this->_id_shop.' ' . $sql_shop . ' AND (a2c.`is_show` = 1 OR a2c.`is_show` IS NULL)
                            and pc.id_customer = '.(int)$id_customer.'';


            $_item_customer = Db::getInstance()->getRow($sql_customer);





            $customer_data = $this->getCustomerInfo(array('id_customer' => $id_customer));
            if(empty($customer_data)){
                $customer_data = array();
            }
            if(empty($_item_customer)){
                $_item_customer = array();
            }

            $id_gender = isset($_item_customer['id_gender'])?$_item_customer['id_gender']:($customer_data['id_gender']);
            $avatar_thumb = isset($customer_data['avatar_thumb'])?$customer_data['avatar_thumb']:$_item_customer['avatar_thumb'];



            $firstname = isset($info_customer_db['firstname'])?$info_customer_db['firstname']:'';
            $lastname = isset($info_customer_db['lastname'])?$info_customer_db['lastname']:'';



            $customers[$i]['customer_name'] = isset($firstname)?($firstname.' '.$lastname):$this->getAdminName();


            // only if customer was been deleted in the admin panel from table ps_customers
            if($id_customer != 0){
                if(!$info_customer_db){

                    $sql_deleted_author_info = 'SELECT DISTINCT bp.author
                    FROM  ' . _DB_PREFIX_ . 'blog_post bp where  bp.author_id = '.(int)$id_customer.'
                    ';

                    $_item_deleted_author_info = Db::getInstance()->getRow($sql_deleted_author_info);

                    $customers[$i]['customer_name'] = isset($_item_deleted_author_info['author'])?$_item_deleted_author_info['author']:$this->getAdminName();
                }
            }
            // only if customer was been deleted in the admin panel from table ps_customers




            // user with avatar
            $info_path = $this->getAvatarPath(array('id_gender' => $id_gender, 'avatar' => $avatar_thumb, 'id_customer' => $id_customer, 'is_user' => 1));

            $customers[$i]['avatar_thumb'] = $info_path['avatar'];
            $customers[$i]['exist_avatar'] = $info_path['is_exist'];
            $customers[$i]['count_posts'] = $count_posts;
            $customers[$i]['id_customer'] = $id_customer;


            $i++;
        }

        //echo "<pre>"; var_dump($customers);exit;


		return array('customers' => $customers);
	}
	

	


    public function getAvatarPath($data){

        $is_exists = 0;
        $gender_txt = '';

        $is_user_functional = isset($data['is_user'])?$data['is_user']:0;

        $id_customer = $data['id_customer'];

        $status_author = isset($data['status_author'])?$data['status_author']:1;
        $is_show = 1;
        $avatar_thumb = '';
        $info = '';


        if($id_customer !== NULL){
            $customer_data = $this->getCustomerInfo(array('id_customer' => $id_customer));
            $id_gender = $customer_data['id_gender'];

            $id_shop = isset($data['id_shop'])?$data['id_shop']:$this->_id_shop;

            $query = 'SELECT avatar_thumb, is_show, info from '._DB_PREFIX_.'blog_avatar2customer
												WHERE id_customer = '.(int)$id_customer.' AND id_shop = '.(int)$id_shop.' ';


            $result = Db::getInstance()->ExecuteS($query);




            $avatar = isset($result[0]['avatar_thumb'])?$result[0]['avatar_thumb']:'';
            $info = isset($result[0]['info'])?$result[0]['info']:'';

            if(count($result) ==0){


                include_once(_PS_MODULE_DIR_.$this->_name  . '/classes/blogspm.class.php');
                $obj_blog = new blogspm();
                $_info_author = $obj_blog->isExistsAuthor(array('author_id' => (int)$id_customer));
                 //var_dump($_info_author);
                if($_info_author === NULL){
                    $is_show = NULL;
                } else {
                    $is_show = 1;
                }

            } else {
                $is_show = isset($result[0]['is_show'])?$result[0]['is_show']:null;

            }

            // if author disabled by admin (admin panel -> tab "Authors"

            if(in_array($status_author,$this->_privilegies)){
                $is_show = 0;
            }
            // if author disabled by admin (admin panel -> tab "Authors"


            if(Tools::strlen($avatar)>0){
                $avatar_thumb = $avatar;

            }
        } else {
            $id_gender = 0;
        }

        // user with avatar

        $obj = $this->getObjectParent();
        $http = $this->getHttpost();

        $_data_translate = $obj->translateItems();
        $male = $_data_translate['male'];
        $female = $_data_translate['female'];
        $avatar_thumb_orig = $avatar_thumb;

        if(
            Tools::strlen($avatar_thumb)>0
            && !in_array($status_author,$this->_privilegies)
        ){


//            if($this->_is_cloud){
//                $path_img_cloud = "modules/".$this->_name."/upload/" . $this->_name . "/avatar/";
//            } else {
//                $path_img_cloud = "upload/" . $this->_name . "/avatar/";
//            }
            $path_img_cloud = "modules/".$this->_name."/upload/" . $this->_name . "/avatar/";


            $avatar_thumb =  $http.$path_img_cloud.$avatar_thumb;
            $is_exists = 1;

            switch($id_gender){
                case 1:
                    //male
                    $gender_txt =$male;
                    break;
                case 2:
                    //female
                    $gender_txt = $female;
                    break;
            }

        } else {

            if (($is_show || $is_show === NULL) || $is_user_functional || in_array($status_author,$this->_privilegies)) {

                    // user without avatar
                    switch ($id_gender) {
                        case 1:
                            //male
                            $avatar_thumb = $http . "modules/" . $this->_name . "/views/img/avatar_m.gif";
                            $gender_txt = $male;
                            break;
                        case 2:
                            //female
                            $avatar_thumb = $http . "modules/" . $this->_name . "/views/img/avatar_w.gif";
                            $gender_txt = $female;
                            break;
                        default:
                            //unknown
                            //$avatar_thumb = $http . "modules/" . $this->_name . "/views/img/avatar_n.gif";
                            $avatar_thumb = $http . "modules/" . $this->_name . "/views/img/avatar_m.gif";
                            break;
                    }
                }

        }



        return array('avatar'=> $avatar_thumb, 'avatar_thumb'=>$avatar_thumb_orig,
                     'is_exist' => $is_exists,'is_show'=>$is_show, 'gender_txt'=>$gender_txt,
                     'info'=>$info,
                    );
    }




    public function saveImageAvatar($data = null){

        $error = 0;
        $error_text = '';
        $is_exists = false;

        $files = isset($_FILES['avatar-review'])?$_FILES['avatar-review']:null;
        $id_customer = $data['id_customer'];



        $show_my_profile = isset($data['show_my_profile'])?$data['show_my_profile']:2;


        $item_id = isset($data['id'])?$data['id']:0;
        $post_images = isset($data['post_images'])?$data['post_images']:'';




        ############### files ###############################
        if(!empty($files['name']))
        {

            if(!$files['error'])
            {

                include_once(_PS_MODULE_DIR_.$this->_name  . '/classes/blogspm.class.php');
                $obj_blogspm = new blogspm();


                if($item_id) {

                        $info_post = $obj_blogspm->getPostItem(array('id' => $item_id,'is_admin'=>1,'id_customer'=>$id_customer));

                        $post_item = $info_post['post'][0];
                        $img_post = $post_item['avatar'];



                } else {
                    $_info = $this->getCustomerInfo();
                    $img_post = isset($_info['avatar_thumb']) ? $_info['avatar_thumb'] : '';
                }


                if(Tools::strlen($img_post)>0){

                    // delete old avatars
                    $name_thumb = explode("/",$img_post);
                    $name_thumb = end($name_thumb);

                    @unlink(dirname(__FILE__).$this->path_img_cloud."avatar".DIRECTORY_SEPARATOR.$name_thumb);


                }


                $type_one = $files['type'];


                srand((double)microtime()*1000000);
                $uniq_name_image = uniqid(rand());
                $type_one = Tools::substr($type_one,6,Tools::strlen($type_one)-6);
                $filename = $uniq_name_image.'.'.$type_one;



                move_uploaded_file($files['tmp_name'], dirname(__FILE__).$this->path_img_cloud."avatar".DIRECTORY_SEPARATOR.$filename);



                $obj_blogspm->copyImage(array('dir_without_ext'=>dirname(__FILE__).$this->path_img_cloud."avatar".DIRECTORY_SEPARATOR.$uniq_name_image,
                        'name'=>dirname(__FILE__).$this->path_img_cloud."avatar".DIRECTORY_SEPARATOR.$filename)
                );


                $this->saveAvatar(array(
                        'avatar' => $uniq_name_image.'.jpg',
                        'id'=>isset($data['id'])?$data['id']:0,
                        'id_customer'=>$id_customer,
                        'show_my_profile' => $show_my_profile,


                    )
                );
                $unlink = dirname(__FILE__).$this->path_img_cloud."avatar".DIRECTORY_SEPARATOR.$uniq_name_image.".".$type_one;
                //var_dump($unlink);exit;
                @unlink($unlink);

                $is_exists = true;


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

        } else {


            if($post_images != "on"){


                $img_post = '';
                if($show_my_profile != 2) {
                    $_info = $this->getCustomerInfo();

                    $img_post = isset($_info['avatar_thumb']) ? $_info['avatar_thumb'] : '';

                    $img_post = explode("/",$img_post);
                    $img_post = end($img_post);


                    ## fixed bug, when customer try update "Show my profile on the site" without upload avatar in the My account -> User Profile section
                    $standard_avatars = array("avatar_n.gif","avatar_m.gif","avatar_w.gif");
                    if(in_array($img_post,$standard_avatars)){
                        $img_post = "";
                    }
                    ## fixed bug, when customer try update "Show my profile on the site" without upload avatar in the My account -> User Profile section

                }


                $array = array(
                    'avatar' => "",
                    'id'=>$item_id,
                    'id_customer'=>$id_customer,
                    'show_my_profile' => $show_my_profile,
                    'avatar' =>$img_post,

                );




                $this->saveAvatar($array);
            }
        }

        return array('error' => $error,'error_text' => $error_text, 'is_exists'=>$is_exists);

    }


    public function saveAvatar($data){
        $avatar = $data['avatar'];
        $id_customer = $data['id_customer'];


        $show_my_profile = isset($data['show_my_profile'])?$data['show_my_profile']:0;
        $update_sql_cond = '';
        if($show_my_profile != 2) {
            switch($show_my_profile){
                case 'on':
                    $show_my_profile = 1;
                    break;
                default:
                    $show_my_profile = 0;
                    break;
            }
            $update_sql_cond = ' , is_show = '.(int)$show_my_profile.'';
        } else {
            $show_my_profile = 1;
        }

        // loyality program //
        include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        // loyality program //


        $action_ajax = Tools::getValue('action');
        $show_my_profile_ajax = Tools::getValue('show_my_profile');


        //addavatar
        if(
            //$show_my_profile == 1
            $show_my_profile_ajax == 'on' && $action_ajax == 'addavatar'
        ){
            // loyality program //
            $loyality->saveLoyalityAction(array('id_loyalty_status' => 1, 'id_customer' => $id_customer, 'type' => 'loyality_user_my_show'));
            // loyality program //
            $show_my_profile = 1;
        }

        if($action_ajax == 'addavatar') {

            // loyality program //
            $loyality->setLoyalityStatus(array('id_loyalty_status'=>$show_my_profile,'id_customer'=>$id_customer,'type'=>'loyality_user_my_show'));
            // loyality program //
        }


        $id_shop = $this->_id_shop;


        if($id_customer !== NULL){
            // if exist record
            $query = 'SELECT COUNT(*) as count from '._DB_PREFIX_.'blog_avatar2customer
												WHERE id_customer = '.(int)$id_customer.' AND id_shop = '.(int)$this->_id_shop.'';

            $result = Db::getInstance()->GetRow($query);
            $exist_record = $result['count'];

            if($exist_record){
                //update
                $query = 'UPDATE '._DB_PREFIX_.'blog_avatar2customer SET avatar_thumb = "'.pSQL($avatar).'" '.$update_sql_cond.'
                            WHERE id_customer = '.(int)$id_customer.' AND id_shop = '.(int)$this->_id_shop.'';
            } else {
                // insert
                $query = 'INSERT INTO '._DB_PREFIX_.'blog_avatar2customer (id_customer, avatar_thumb,is_show,id_shop)
                             VALUES ('.(int)$id_customer.', "'.pSQL($avatar).'", '.(int)$show_my_profile.','.(int)$id_shop.') ';
            }

            // loyality program //
            if(Tools::strlen($avatar)>0) {
                $loyality->saveLoyalityAction(array('id_loyalty_status' => 1, 'id_customer' => $id_customer, 'type' => 'loyality_user_my_avatar'));
            }
            // loyality program //
            //var_dump($query);exit;
            Db::getInstance()->Execute($query);
        }




        if(Tools::strlen($avatar)==0)
            @unlink(dirname(__FILE__).$this->path_img_cloud."avatar".DIRECTORY_SEPARATOR.$avatar);


        $this->_clearSmartyCache();


    }

    public function updateDataAuthor($data){

        $id_customer = $data['id_customer'];
        $info = $data['info'];

        //update
        $query = 'UPDATE '._DB_PREFIX_.'blog_avatar2customer SET info = "'.pSQL($info).'"
                    WHERE id_customer = '.(int)$id_customer.' AND id_shop = '.(int)$this->_id_shop.'';
        Db::getInstance()->Execute($query);


        $this->_clearSmartyCache();


    }

    public function deleteAvatarGDPR($data){
        $avatar = $data['avatar'];
        $id_customer = $data['id_customer'];


        if($id_customer !== NULL){

            $id_shop = $this->_id_shop;
            $query = 'DELETE from '._DB_PREFIX_.'blog_avatar2customer
												WHERE id_customer = '.(int)$id_customer.' AND id_shop='.(int)$id_shop;
            Db::getInstance()->Execute($query);
        }



        if(Tools::strlen($avatar)==0)
            @unlink(dirname(__FILE__).$this->path_img_cloud."avatar".DIRECTORY_SEPARATOR.$avatar);
    }
	
	private function _http(){


        $http = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;


		return $http;
	}




	public function getCustomerInfo($data = null){
		$cookie = $this->context->cookie;
		
		$exist_avatar = 0;
		$is_show = 0;
        $id_gender = 0;
        $id_customer = isset($data['id_customer'])?$data['id_customer']:0;
        $is_admin = isset($data['is_admin'])?$data['is_admin']:0;



		
		if($cookie->logged || $is_admin){
			$id_customer = $is_admin?$id_customer:(($cookie->id_customer != $id_customer && !$cookie->id_customer)?$id_customer:$cookie->id_customer);

            //var_dump($id_customer);var_dump($cookie->id_customer);
			
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'blog_avatar2customer`
		        WHERE `id_customer` = '.(int)$id_customer.' AND id_shop = '.(int)$this->_id_shop.'';

    	$result = Db::getInstance()->GetRow($sql);


        $status_author = isset($result['status'])?$result['status']:'';
    	$avatar_thumb = isset($result['avatar_thumb'])?$result['avatar_thumb']:'';
    	$is_show = isset($result['is_show'])?$result['is_show']:1;
        $info = isset($result['info'])?$result['info']:'';


        $info_customer_db = $this->getInfoCustomerDB(array('id_customer' => $id_customer));
        $id_gender = $info_customer_db['id_gender'];


    	
		// user with avatar
		if(Tools::strlen($avatar_thumb)>0){
			//$avatar_thumb = $this->_http."upload/".$avatar_thumb;

//            if ($this->_is_cloud) {
//                $path_img_cloud = "modules/" . $this->_name . "/upload/" . $this->_name . "/avatar/";
//            } else {
//                $path_img_cloud = "upload/" . $this->_name . "/avatar/";
//            }
            $path_img_cloud = "modules/" . $this->_name . "/upload/" . $this->_name . "/avatar/";


            $avatar_thumb = $this->_http.$path_img_cloud.$avatar_thumb;

			$exist_avatar = 1;
		} else {
			// user without avatar
			$info_customer_db = $this->getInfoCustomerDB(array('id_customer' => $id_customer));

			switch($info_customer_db['id_gender']){
				case 1:
					//male
					$avatar_thumb = $this->_http."modules/" . $this->_name . "/views/img/avatar_m.gif";
				break;
				case 2:
					//female
					$avatar_thumb = $this->_http."modules/" . $this->_name . "/views/img/avatar_w.gif";
				break;
				default:
					//unknown
					//$avatar_thumb = $this->_http."modules/" . $this->_name . "/views/img/avatar_n.gif";
                    $avatar_thumb = $this->_http."modules/" . $this->_name . "/views/img/avatar_m.gif";
				break;
				
			}
			
		}
		 
    		return array('id_customer'=>$id_customer, 'id_gender'=>$id_gender, 'result'=>$result,
						 'avatar_thumb' => $avatar_thumb, 'exist_avatar' => $exist_avatar, 'is_show' => $is_show,'info'=>$info,
                         'status_author'=>$status_author);
		} else {


            $sql = 'SELECT * FROM `'._DB_PREFIX_.'blog_avatar2customer`
		        WHERE `id_customer` = '.(int)$id_customer.' AND id_shop = '.(int)$this->_id_shop.'';
            $result = Db::getInstance()->GetRow($sql);

            $info = isset($result['info'])?$result['info']:'';
            $status_author = isset($result['status'])?$result['status']:'';

			return array('id_customer'=>$id_customer,'avatar' => '','avatar_thumb' => '', 'exist_avatar' => $exist_avatar
						 , 'is_show' => $is_show, 'id_gender'=>$id_gender,'info'=>$info,'status_author'=>$status_author);
		}
	}




	public function getInfoCustomerDB($data){
		$id_customer = $data['id_customer'];
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'customer` 
		        WHERE `id_customer` = '.(int)$id_customer.'';
    	$result = Db::getInstance()->GetRow($sql);
    	
    	return $result;
	}

    public function deleteAvatar($data){
        $id = $data['id'];
        $id_customer = $data['id_customer'];


        $img = $data['avatar'];

        $this->saveAvatar(array(
                'avatar' => "",
                'id'=>$id,
                'id_customer' => $id_customer,

            )
        );

        $img = explode("/",$img);
        $img = end($img);
        @unlink(dirname(__FILE__).$this->path_img_cloud."avatar".DIRECTORY_SEPARATOR.$img);


        // loyality program //
        include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/loyalityblog.class.php');
        $loyality = new loyalityblog();
        $loyality->deleteLoyalityPoints(array('id_loyalty_status' => 0, 'id_customer' => $id_customer, 'type' => 'loyality_user_my_avatar'));
        // loyality program //
    }




    public function getHttpost(){
            $custom_ssl_var = 0;
            if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                $custom_ssl_var = 1;


            if ($custom_ssl_var == 1)
                $_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
            else
                $_http_host = _PS_BASE_URL_.__PS_BASE_URI__;

        return $_http_host;
    }


    public function getInfoAboutCustomer($data=null){
        $id_customer = (int) $data['id_customer'];
        $is_full = isset($data['is_full'])?$data['is_full']:0;
        //get info about customer
        $sql = '
	        	SELECT * FROM `'._DB_PREFIX_.'customer`
		        WHERE `active` = 1 AND `id_customer` = \''.(int)($id_customer).'\'
		        AND `deleted` = 0 AND id_shop = '.(int)($this->_id_shop).'  '.(defined(_MYSQL_ENGINE_)?"AND `is_guest` = 0":"").'
		        ';

        $result = Db::getInstance()->GetRow($sql);


        if(!$is_full)
            $lastname = Tools::strtoupper(Tools::substr($result['lastname'],0,1));
        else
            $lastname = $result['lastname'];

        $firstname = $result['firstname'];
        $customer_name = $firstname . " " . $lastname;
        $email = $result['email'];


        return array('customer_name' => $customer_name,'email'=>$email);
    }


    public function getAvatarForCustomer($data){
        $id_customer = $data['id_customer'];

        $query = 'SELECT avatar_thumb, is_show, status as status_author from '._DB_PREFIX_.'blog_avatar2customer
												WHERE id_customer = '.(int)$id_customer.' AND id_shop = '.(int)$this->_id_shop.'';

        $result = Db::getInstance()->ExecuteS($query);


        $avatar = isset($result[0]['avatar_thumb'])?$result[0]['avatar_thumb']:'';
        $status_author = isset($result[0]['status_author'])?$result[0]['status_author']:0;


        if(Tools::strlen($avatar)>0 && !in_array($status_author,$this->_privilegies)) {

//            if ($this->_is_cloud) {
//                $path_img_cloud = "modules/" . $this->_name . "/upload/" . $this->_name . "/avatar/";
//            } else {
//                $path_img_cloud = "upload/" . $this->_name . "/avatar/";
//            }
            $path_img_cloud = "modules/" . $this->_name . "/upload/" . $this->_name . "/avatar/";


            $http = $this->getHttpost();


            $avatar = $http . $path_img_cloud . $avatar;
        } else {
            // user without avatar
            $info_customer_db = $this->getInfoCustomerDB(array('id_customer' => $id_customer));
            switch($info_customer_db['id_gender']){
                case 1:
                    //male
                    $avatar = $this->_http."modules/" . $this->_name . "/views/img/avatar_m.gif";
                    break;
                case 2:
                    //female
                    $avatar = $this->_http."modules/" . $this->_name . "/views/img/avatar_w.gif";
                    break;
                default:
                    //unknown
                    //$avatar = $this->_http."modules/" . $this->_name . "/views/img/avatar_n.gif";
                    $avatar = $this->_http."modules/" . $this->_name . "/views/img/avatar_m.gif";
                    break;

            }
        }



        return array('avatar'=>$avatar);
    }


    public function paging17($data)
    {
        $start = $data['start'];
        $count = $data['count'];
        $step = $data['step'];
        $data['prefix'] = $this->_name.'u';



        include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/pagenavhelp.class.php');
        $obj = new pagenavhelp();
        return $obj->pagenav($start, $count, $step, $data);
    }

    private function _clearSmartyCache(){

        include_once(_PS_MODULE_DIR_.$this->_name.'/classes/cacheblockblog.class.php');
        $obj = new cacheblockblog();
        $obj->clearSmartyCacheModule();
    }



}