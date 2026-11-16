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

class postshelp  extends Module {

    private $_name = 'blockblog';
    private $_is_vk = 0;
    private $_is_instagram = 0;
    private $_is_pinterest = 0;

    private $_name_module_wall_post='pstwitterpostspm';


    public function round($val, $precision = 2)
    {
        if (method_exists('Tools', 'ps_round')) {
            $val = Tools::ps_round($val, $precision);
        }
        else {
            $val = round($val, $precision);
        }

        return $val;
    }

    public function postToAPI($data){


        $product_name = $data['product_name'];
        $product_link = $data['product_link'];

        $picture = $data['image'];
        $id_lang = $data['id_lang'];

        $image_path = $data['image_path'];




        // product data for post
        $data_product = array();
        $data_product['data_product']['image_link'] = $picture;
        $data_product['data_product']['product_url'] = $product_link;
        $data_product['data_product']['price'] = ''; // stub
        $data_product['data_product']['description'] = ''; // stub
        $data_product['data_product']['product_name'] = $product_name;
        $data_product['data_product']['image_path'] = $image_path;
        // product data for post






        $name = $data['name'];

        $data_requrements = $this->checkrequirements();
        $is_pstwitterpost = $data_requrements['pstwitterpost'];
        $is_psvkpost = $data_requrements['psvkpost'];

        // post to Twitter //
        $name_social_item = 'tw';
        if(Configuration::get($name.$name_social_item.'post_on') && $is_pstwitterpost){
            require_once(dirname(__FILE__).'/../../../modules/'.$this->_name_module_wall_post.'/'.$this->_name_module_wall_post.'.php');
            $obj_pstwitterpost = new $this->_name_module_wall_post();

            $status = Configuration::get($name.$name_social_item.'desc'.'_'.$id_lang).': '.$product_name  .' - '.$product_link;


            $data_array_item = array('status' => $status, 'data_product' => $data_product);
            $obj_pstwitterpost->postWithTwitter($data_array_item);

        }
        // post to Twitter //


        if($this->_is_instagram) {
            // post to Instagram //
            $name_social_item = 'i';
            if (Configuration::get($name . $name_social_item . 'post_on') && $is_pstwitterpost) {

                require_once(dirname(__FILE__) . '/../../../modules/' . $this->_name_module_wall_post . '/' . $this->_name_module_wall_post . '.php');
                $obj_pstwitterpost = new $this->_name_module_wall_post();

                $status = Configuration::get($name . $name_social_item . 'desc' . '_' . $id_lang) . ': ' . $product_name . ' - ' . $product_link;


                $data_array_item = array('status' => $status, 'data_product' => $data_product);
                $obj_pstwitterpost->postWithInstagram($data_array_item);


            }
            // post to Instagram //
        }


        if($this->_is_pinterest) {
            // post to Pinterst //
            $name_social_item = 'p';
            if (Configuration::get($name . $name_social_item . 'post_on') && $is_pstwitterpost) {
                require_once(dirname(__FILE__) . '/../../../modules/' . $this->_name_module_wall_post . '/' . $this->_name_module_wall_post . '.php');
                $obj_pstwitterpost = new $this->_name_module_wall_post();

                $status = Configuration::get($name . $name_social_item . 'desc' . '_' . $id_lang) . ': ' . $product_name . ' - ' . $product_link;

                $data_array_item = array('status' => $status, 'data_product' => $data_product);
                $obj_pstwitterpost->postWithPinterest($data_array_item);


            }
            // post to Pinterst //
        }


        if($this->_is_vk) {
            // post to Vkontakte //
            if (Configuration::get($name . 'vkpost_on') && $is_psvkpost) {
                require_once(dirname(__FILE__) . '/../../../modules/psvkpost/psvkpost.php');
                $obj_psvkpost = new psvkpost();

                $status = Configuration::get($name . 'vkdesc' . '_' . $id_lang) . ': ' . $product_name . ' - ' . $product_link;
                $obj_psvkpost->postWithAPI(array('status' => $status, 'image' => $picture, 'product_url' => $product_link));
            }
            // post to Vkontakte //
        }

    }

    public function postsSettings($data){
        $_html = '';
        $title = $data['translate']['title'];
        $hint1 = $data['translate']['hint1'];
        $hint2 = $data['translate']['hint2'];

        $title_pstwitterpost = $data['translate']['title_pstwitterpost'];
        $title_psvkpost = $data['translate']['title_psvkpost'];
        $buy_module_psvkpost = $data['translate']['buy_module_psvkpost'];
        $buy_module_pstwitterpost = $data['translate']['buy_module_pstwitterpost'];


        $data_requrements = $this->checkrequirements();
        $is_pstwitterpost = $data_requrements['pstwitterpost'];
        $is_psvkpost = $data_requrements['psvkpost'];


        include(dirname(__FILE__).'/../views/templates/hooks/'.$this->_name.'/'.__FUNCTION__.'.phtml');


        return $_html;
    }

    private function _psvkform16($data){
        require_once(_PS_MODULE_DIR_.$this->_name .'/blockblog.php');
        $obj_blockblog = new blockblog();
        return $obj_blockblog->psvkform16($data);

    }



    private function _pstwitterform16($data){
        require_once(_PS_MODULE_DIR_.$this->_name .'/blockblog.php');
        $obj_blockblog = new blockblog();
        return $obj_blockblog->pstwitterform16($data);

    }




    public function checkrequirements(){
        // Vkontakte Wall Post
        $is_on_psvkpost = 0;

        if (file_exists(dirname(__FILE__).'/../../../modules/psvkpost/psvkpost.php'))
        {
            if(version_compare(_PS_VERSION_, '1.5', '>')) {
                $_is_psvkpost_active = Module::isEnabled('psvkpost');
                if ($_is_psvkpost_active)
                    $is_on_psvkpost = 1;
            } else {
                $is_on_psvkpost = 1;
            }
        }


        // Twitter Wall Post
        $is_on_pstwitterpost = 0;
        if (file_exists(dirname(__FILE__).'/../../../modules/'.$this->_name_module_wall_post.'/'.$this->_name_module_wall_post.'.php'))
        {
            if(version_compare(_PS_VERSION_, '1.5', '>')) {
                $_is_psvkpost_active = Module::isEnabled($this->_name_module_wall_post);
                if($_is_psvkpost_active)
                    $is_on_pstwitterpost = 1;
            } else {
                $is_on_pstwitterpost = 1;
            }
        }


        return array('psvkpost'=>$is_on_psvkpost,'pstwitterpost'=>$is_on_pstwitterpost);
    }



    public function psvkform16($data){

        $table = $data['table'];
        $this_data = $data['this'];
        $identifier = $data['identifier'];
        $tab = $data['tab'];
        $uri = $data['uri'];

        $yes_title = $data['yes_title'];
        $no_title = $data['no_title'];

        $update_button = $data['translate']['translate']['update_button'];
        $enable_pstwitterpost = $data['translate']['translate']['enable_psvkpost'];

        $template_text = $data['translate']['translate']['template_text'];
        $name = $data['translate']['translate']['name'];

        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $enable_pstwitterpost,
                    'icon' => 'fa fa-vk fa-lg'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $enable_pstwitterpost,
                        'name' => 'vkpost_on',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $yes_title
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $no_title
                            )
                        ),
                    ),
                    array(
                        'type' => 'text_autopost',
                        'label' => $template_text,
                        'name' => 'vkdesc',
                        'id' => 'vkdesc',
                        'lang' => TRUE,
                        'text_before' => '',
                        'text_after' => ' - {Blog post title} - {Blog post URL}',
                        'desc'=>'Example: New blog post has been added in our shop - Test blog post - https://www.test.com/blog/testblogpost',
                    ),
                ),
            ),
        );

        $fields_form1 = array(
            'form' => array(
                'submit' => array(
                    'title' => $update_button,
                )
            ),
        );


        $helper = new HelperForm();
        $helper->table = $table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this_data;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $identifier;
        $helper->submit_action = 'psvkpostsettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$name.'&tab_module='.$tab.'&module_name='.$name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $uri,
            'fields_value' => $this->getConfigFieldsValuesPsvkpostsettingsSettings($name),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return  $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesPsvkpostsettingsSettings($name){
        $languages = Language::getLanguages(false);
        $fields_vkdesc = array();

        foreach ($languages as $lang)
        {
            $fields_vkdesc[$lang['id_lang']] =  Configuration::get($name.'vkdesc_'.$lang['id_lang']);
        }

        $data_config = array(
            'vkdesc' => $fields_vkdesc,
            'vkpost_on' => Configuration::get($name.'vkpost_on'),
        );

        return $data_config;
    }


    public function pstwitterform16($data){

        $table = $data['table'];
        $this_data = $data['this'];
        $identifier = $data['identifier'];
        $tab = $data['tab'];
        $uri = $data['uri'];

        $yes_title = $data['yes_title'];
        $no_title = $data['no_title'];

        $update_button = $data['translate']['translate']['update_button'];

        $enable_pstwitterpost_main = $data['translate']['translate']['enable_pstwitterpost_main'];

        $enable_pstwitterpost = $data['translate']['translate']['enable_pstwitterpost'];

        $enable_pstwitterpost_i = $data['translate']['translate']['enable_pstwitterpost_i'];

        $enable_pstwitterpost_p = $data['translate']['translate']['enable_pstwitterpost_p'];

        $template_text = $data['translate']['translate']['template_text'];
        $name = $data['translate']['translate']['name'];

        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $enable_pstwitterpost_main,
                    'icon' => 'fa fa-paper-plane fa-lg'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $enable_pstwitterpost,
                        'name' => 'twpost_on',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $yes_title
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $no_title
                            )
                        ),
                    ),
                    array(
                        'type' => 'text_autopost',
                        'label' => $template_text.':',
                        'name' => 'twdesc',
                        'id' => 'twdesc',
                        'lang' => TRUE,
                        'text_before' => '',
                        'text_after' => ' - {Blog post title} - {Blog post URL}',
                        'desc'=>'Example: New blog post has been added in our shop - Test blog post - https://www.test.com/blog/testblogpost',
                    ),


                    /*array(
                        'type' => 'switch',
                        'label' => $enable_pstwitterpost_i,
                        'name' => 'ipost_on',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $yes_title
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $no_title
                            )
                        ),
                    ),
                    array(
                        'type' => 'text_autopost',
                        'label' => $template_text.':',
                        'name' => 'idesc',
                        'id' => 'idesc',
                        'lang' => TRUE,
                        'text_before' => '',
                        'text_after' => ' - {Blog post title} - {Blog post URL}',
                        'desc'=>'Example: New blog post has been added in our shop - Test blog post - https://www.test.com/blog/testblogpost',
                    ),*/



                   /* array(
                        'type' => 'switch',
                        'label' => $enable_pstwitterpost_p,
                        'name' => 'ppost_on',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $yes_title
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $no_title
                            )
                        ),
                    ),
                    array(
                        'type' => 'text_autopost',
                        'label' => $template_text.':',
                        'name' => 'pdesc',
                        'id' => 'pdesc',
                        'lang' => TRUE,
                        'text_before' => '',
                        'text_after' => ' - {Blog post title} - {Blog post URL}',
                        'desc'=>'Example: New blog post has been added in our shop - Test blog post - https://www.test.com/blog/testblogpost',
                    ),*/
                ),
            ),
        );

        $fields_form1 = array(
            'form' => array(
                'submit' => array(
                    'title' => $update_button,
                )
            ),
        );

        $helper = new HelperForm();
        $helper->table = $table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this_data;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $identifier;
        $helper->submit_action = 'pstwitterpostsettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$name.'&tab_module='.$tab.'&module_name='.$name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $uri,
            'fields_value' => $this->getConfigFieldsValuesPstwitterpostsettingsSettings($name),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return  $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesPstwitterpostsettingsSettings($name){
        $languages = Language::getLanguages(false);
        $fields_twdesc = array();
        $fields_idesc = array();
        $fields_pdesc = array();

        foreach ($languages as $lang)
        {
            $fields_twdesc[$lang['id_lang']] =  Configuration::get($name.'twdesc_'.$lang['id_lang']);
            $fields_idesc[$lang['id_lang']] =  Configuration::get($name.'idesc_'.$lang['id_lang']);
            $fields_pdesc[$lang['id_lang']] =  Configuration::get($name.'pdesc_'.$lang['id_lang']);
        }

        $data_config = array(
            'twdesc' => $fields_twdesc,
            'twpost_on' => Configuration::get($name.'twpost_on'),

            /*'idesc' => $fields_idesc,
            'ipost_on' => Configuration::get($name.'ipost_on'),*/

            /*'pdesc' => $fields_pdesc,
            'ppost_on' => Configuration::get($name.'ppost_on'),*/
        );

        return $data_config;
    }
}