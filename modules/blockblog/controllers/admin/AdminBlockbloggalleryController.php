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
require_once(_PS_MODULE_DIR_ . 'blockblog/classes/BlockbloggalleryItems.php');

class AdminBlockbloggalleryController extends ModuleAdminController{

    private $_name_controller = 'AdminBlockbloggallery';
    private $_name_module = 'blockblog';
    private $_data_table = 'blog_gallery_data';
    private  $_id_lang;
    private  $_id_shop;
    private  $_iso_code;

    protected $position_identifier = 'position';

    public function __construct()

    {


        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'blog_gallery';


        $this->identifier = 'id_tabs';
        $this->className = 'BlockbloggalleryItems';

        $this->list_id = 'bloggallery';
        $this->_defaultOrderBy = 'position';



        //$this->_orderBy = 'id';
        //$this->_orderWay = 'DESC';

        $this->lang = false;


        $this->allow_export = false;

        $this->list_no_link = true;


        require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blogspm.class.php');
        $bloghelp_obj = new blogspm();
        $id_lang = $bloghelp_obj->getMostPopularLanguageForGallery(array('id_lang'=>$this->context->cookie->id_lang));



        //$id_lang =  $this->context->cookie->id_lang;
        $this->_id_lang = $id_lang;
        $id_shop =  $this->context->shop->id;
        $this->_id_shop = $id_shop;

        $iso_code = Language::getIsoById($id_lang);
        $this->_iso_code = $iso_code;

        $this->_select .= 'a.id, a.id as id_tabs, a.img, c.title, a.time_add,  c.id_lang, '.$id_shop.' as id_shop, a.status ';
        $this->_join .= '  JOIN `' . _DB_PREFIX_ . $this->_data_table.'` c ON (c.id_item = a.id and c.id_lang = '.(int)$id_lang.')';


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




        $this->addRowAction('edit');
        $this->addRowAction('delete');
        //$this->addRowAction('view');
        //$this->addRowAction('&nbsp;');




        // is cloud ?? //
//        if(defined('_PS_HOST_MODE_')){
//            $logo_img_path = '../modules/'.$this->_name_module.'/gallery/upload/';
//        } else {
//            $logo_img_path = '../upload/'.$this->_name_module.'/gallery/';
//        }
        $logo_img_path = '../modules/'.$this->_name_module.'/gallery/upload/';
        // is cloud ?? //




        $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'search' => true,
                'orderby' => true,

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
                'align' => 'center',
                'search' => true,
                'orderby' => true,

            ),

            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-md',
                'search' => false,
            ),



            'shop_name' => array(
                'title' => $this->l('Shop'),
                'width' => 'auto',
                'search' => false

            ),

            'language' => array(
                'title' => $this->l('Language'),
                'width' => 'auto',
                'search' => false,
                'align' => 'center',

            ),

            'time_add' => array(
                'title' => $this->l('Date add'),
                'width' => 'auto',
                'search' => false,
                'align' => 'center',

            ),

            'is_featured' => array(
                'title' => $this->l('Featured'),
                'width' => 'auto',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => FALSE,
                'type_custom' => 'is_active_featured',
                'token_custom'=>Tools::getAdminTokenLite('AdminBlockblogajax'),
                'ajax_link' => $this->context->link->getAdminLink('AdminBlockblogajax'),

            ),

            'status' => array(
                'title' => $this->l('Status'),
                'width' => 40,
                'align' => 'center',
                'type' => 'bool',
                'orderby' => FALSE,
                'type_custom' => 'is_active',
                'token_custom'=>Tools::getAdminTokenLite('AdminBlockblogajax'),
                'ajax_link' => $this->context->link->getAdminLink('AdminBlockblogajax'),
            ),

        );

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
                'href' => self::$currentIndex.'&addblog_gallery&token='.$this->token,
                'desc' => $this->l('Add new item', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initToolbar() {

        parent::initToolbar();
        /*$this->toolbar_btn['add_item'] = array(
                                            'href' => self::$currentIndex.'&add'.$this->_name_module.'&token='.$this->token,
                                            'desc' => $this->l('Add new category', null, null, false),
                                        );
        *///unset($this->toolbar_btn['new']);

    }



    public function postProcess()
    {


        require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blogspm.class.php');
        $bloghelp_obj = new blogspm();




        if (Tools::isSubmit('add_item')) {
            ## add item ##
            $time_add = Tools::getValue("time_add");
            $languages = Language::getLanguages(false);
            $data_title_content_lang = array();
            $data_validation = array();

            $cat_shop_association = Tools::getValue("cat_shop_association");

            foreach ($languages as $language){
                $id_lang = $language['id_lang'];
                $gallery_title = Tools::getValue("gallery_title_".$id_lang);
                $content = Tools::getValue("content_".$id_lang);

                if(Tools::strlen($gallery_title)>0)
                {
                    $data_title_content_lang[$id_lang] = array(
                        'gallery_title' => $gallery_title,
                        'content'=>$content,

                    );
                    $data_validation[$id_lang] = array('gallery_title' => $gallery_title,);
                }
            }

            $status = Tools::getValue('status');
            $is_featured = Tools::getValue('is_featured');
            $data = array(
                'data_title_content_lang'=>$data_title_content_lang,
                'cat_shop_association' => $cat_shop_association,
                'time_add' => $time_add,
                'status' => $status,
                'is_featured'=>$is_featured,
            );

            $files = $_FILES['post_image'];


            if(sizeof($data_validation)==0)
                $this->errors[] = $this->l('Please fill the Title');

            if(Tools::strlen($files['name'])==0)
                $this->errors[] = $this->l('Please select the Image');

            if(!($cat_shop_association))
                $this->errors[] = $this->l('Please select the Shop');

            if(!$time_add)
                $this->errors[] = $this->l('Please select Date Add');


            if (empty($this->errors)) {




                Db::getInstance()->Execute('BEGIN');
                $data_errors = $bloghelp_obj->saveGallery($data);
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
            ## update item ##
            $time_add = Tools::getValue("time_add");
            $cat_shop_association = Tools::getValue("cat_shop_association");

            $post_images = Tools::getValue("post_images");


            $languages = Language::getLanguages(false);
            $data_title_content_lang = array();
            $data_validation = array();

            foreach ($languages as $language){
                $id_lang = $language['id_lang'];
                $gallery_title = Tools::getValue("gallery_title_".$id_lang);
                $content = Tools::getValue("content_".$id_lang);

                if(Tools::strlen($gallery_title)>0)
                {
                    $data_title_content_lang[$id_lang] = array(
                        'gallery_title' => $gallery_title,
                        'content'=>$content,

                    );
                    $data_validation[$id_lang] = array('gallery_title' => $gallery_title,);
                }
            }

            $status = Tools::getValue('status');
            $is_featured = Tools::getValue('is_featured');

            $data = array(

                'data_title_content_lang'=>$data_title_content_lang,
                'id_editcategory' => $id,
                'cat_shop_association' => $cat_shop_association,
                'status' => $status,
                'is_featured'=>$is_featured,
                'time_add' => $time_add,
                'post_images' => $post_images,
            );





            if(sizeof($data_validation)==0)
                $this->errors[] = $this->l('Please fill the Title');

            if(!($cat_shop_association))
                $this->errors[] = $this->l('Please select the Shop');
            if(!$time_add)
                $this->errors[] = $this->l('Please select Date Add');



            if (empty($this->errors)) {

                $data_errors = $bloghelp_obj->updateGallery($data);

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

            $bloghelp_obj->deleteGallery(array('id' => $id));

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

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/css/blog17.css');

        $this->context->controller->addCSS(__PS_BASE_URI__.'js/jquery/plugins/autocomplete/jquery.autocomplete.css');
        $this->context->controller->addJs(__PS_BASE_URI__.'js/jquery/plugins/autocomplete/jquery.autocomplete.js');


        $this->context->controller->addJs(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/js/image-files.js');
        $this->context->controller->addJs(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/js/admin.js');

        $this->context->controller->addJs(__PS_BASE_URI__.'modules/'.$this->_name_module.'/views/js/custom_menu.js');


        $this->addJqueryUi(array('ui.core','ui.widget','ui.datepicker'));

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



        $data_url = $obj_blog->getSEOURLs();
        $ajax_url = $data_url['ajax_url'];





        if($id) {

            $_data = $obj_blog->getGalleryItem(array('id'=>$id));


            $id_shop = isset($_data['gallery'][0]['ids_shops']) ? explode(",",$_data['gallery'][0]['ids_shops']) : array();
            $time_add = isset($_data['gallery'][0]['time_add']) ? $_data['gallery'][0]['time_add'] :'' ;
            $logo_img = isset($_data['gallery'][0]['img']) ? $_data['gallery'][0]['img'] :'' ;


            // is cloud ?? //
//            if(defined('_PS_HOST_MODE_')){
//                $logo_img_path = '../modules/'.$this->_name_module.'/upload/gallery/'.$logo_img;
//            } else {
//                $logo_img_path = '../upload/'.$this->_name_module.'/gallery/'.$logo_img;
//            }
            $logo_img_path = '../modules/'.$this->_name_module.'/upload/gallery/'.$logo_img;
            // is cloud ?? //


        } else {

            $id_shop = Tools::getValue('cat_shop_association')?Tools::getValue('cat_shop_association'):array();
            $time_add = date("Y-m-d H:i:s");

            $logo_img = '';
            $logo_img_path = '';


        }



        if($id){
            $title_item_form = $this->l('Edit item:');
        } else{
            $title_item_form = $this->l('Add new item:');
        }



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
                    'name' => 'gallery_title',
                    'id' => 'gallery_title',
                    'lang' => true,
                    'required' => TRUE,
                    'size' => 5000,
                    'maxlength' => 5000,
                ),





                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'content',
                    'id' => 'content',
                    'required' => FALSE,
                    'autoload_rte' => FALSE,
                    'lang' => TRUE,
                    'rows' => 8,
                    'cols' => 40,

                ),




                array(
                    'type' => 'gal_image_custom',
                    'label' => $this->l('Image'),
                    'name' => 'post_image',
                    'logo_img'=>$logo_img,
                    'logo_img_path' => $logo_img_path,
                    'id_post' => $id,
                    'required' => true,
                    'desc' => $this->l('Allow formats *.jpg; *.jpeg; *.png; *.gif.'),
                    'is_demo' => $is_demo,
                    'max_upload_info' => ini_get('upload_max_filesize'),
                    'ajax_url' =>$ajax_url,
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
                    'type' => 'item_date',
                    'label' => $this->l('Date Add'),
                    'name' => 'date_on',
                    'time_add' => $time_add,
                    'required' => TRUE,
                ),


                array(
                    'type' => 'switch',
                    'label' => $this->l('Featured'),
                    'name' => 'is_featured',
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
                    'label' => $this->l('Status'),
                    'name' => 'status',
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


            ),


        );





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

        }



        return parent::renderForm();
    }



    public function getConfigFieldsValuesForm($data_in){



        $id = (int)Tools::getValue('id_tabs');
        if($id) {
            $id = $data_in['id'];
            require_once(_PS_MODULE_DIR_ . '' . $this->_name_module . '/classes/blogspm.class.php');
            $obj_blog = new blogspm();
            $_data = $obj_blog->getGalleryItem(array('id'=>$id));


            $languages = Language::getLanguages(false);
            $fields_title = array();

            $fields_content = array();

            foreach ($languages as $lang)
            {
                $fields_title[$lang['id_lang']] = isset($_data['gallery']['data'][$lang['id_lang']]['title'])?$_data['gallery']['data'][$lang['id_lang']]['title']:'';


                $fields_content[$lang['id_lang']] = isset($_data['gallery']['data'][$lang['id_lang']]['content'])?$_data['gallery']['data'][$lang['id_lang']]['content']:'';


            }

            $status = isset($_data['gallery'][0]['status'])?$_data['gallery'][0]['status']:"";
            $is_featured = isset($_data['gallery'][0]['is_featured'])?$_data['gallery'][0]['is_featured']:"";

            $config_array = array(
                'gallery_title' => $fields_title,
                'content'=>$fields_content,
                'status' => $status,
                'is_featured'=>$is_featured,
            );
        } else {
            $config_array = array();
        }
        return $config_array;
    }



    public function l($string , $class = NULL, $addslashes = false, $htmlentities = true){
        if(version_compare(_PS_VERSION_, '1.7', '<')) {
            return parent::l($string);
        } else {
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

