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

class loyalityblog extends Module
{

    private $_name;
    private $step = 50;

    public function __construct()
    {
        $this->_name = "blockblog";
        $this->initContext();
    }

    private function initContext()
    {
        $this->context = Context::getContext();
    }

    private function getPrefix()
    {
        include_once(_PS_MODULE_DIR_ . $this->_name . '/blockblog.php');
        $obj_blockblog = new blockblog();
        $_prefix = $obj_blockblog->getLoyalityPrefix();
        return $_prefix;

    }

    private function getPrefixDB()
    {
        include_once(_PS_MODULE_DIR_ . $this->_name . '/blockblog.php');
        $obj_blockblog = new blockblog();
        $_prefix = $obj_blockblog->getLoyalityDbPrefix();
        return $_prefix;

    }




    public function installLoyalityProgramTables(){

        Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` (
			`id_loyalty` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_loyalty_status` INT UNSIGNED NOT NULL DEFAULT 1,
			`id_customer` INT UNSIGNED NOT NULL,
			`id_post` INT UNSIGNED DEFAULT NULL DEFAULT 0,
			`id_comment` INT UNSIGNED DEFAULT NULL DEFAULT 0,
			`id_order` INT UNSIGNED DEFAULT NULL DEFAULT 0,
			`id_review_product` INT UNSIGNED DEFAULT NULL DEFAULT 0,
			`id_review_shop` INT UNSIGNED DEFAULT NULL DEFAULT 0,
			`type` varchar(500) default NULL,
			`points` INT NOT NULL DEFAULT 0,
			`is_use` INT NOT NULL DEFAULT 0,
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NOT NULL,
			PRIMARY KEY (`id_loyalty`),
			INDEX index_loyalty_loyalty_status (`id_loyalty_status`),
			INDEX index_loyalty_order (`id_order`),
			INDEX index_loyalty_review_product (`id_review_product`),
			INDEX index_loyalty_review_shop (`id_review_shop`),
			INDEX index_loyalty_customer (`id_customer`)
		) DEFAULT CHARSET=utf8 ;');

        return true;

    }

    public function uninstallTable(){
        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->getPrefixDB().'_loyalty');
    }

    public function saveLoyalityAction($data){

        $prefix = $this->getPrefix();

        // check if enable loyality program
        if(!Configuration::get($this->_name.'loyality_on'.$prefix))
            return;

        $type = $data['type'];
        $id_customer = $data['id_customer'];
        $id_post = isset($data['id_post'])?$data['id_post']:0;
        $id_comment = isset($data['id_comment'])?$data['id_comment']:0;
        $id_order = isset($data['id_order'])?$data['id_order']:0;
        $id_loyalty_status = $data['id_loyalty_status'];



        // loyality program only for registered customers
        if(!$id_customer)
            return;

        switch($type){
            case 'loyality_add_blog_post':
            case 'loyality_add_blog_post_logo':
            case 'loyality_like_blog_post':



                // is enable points for action $type
                if(Configuration::get($this->_name.$type.'_status') != $type)
                    return;

                // How many times customer can get points
                $times = (int)Configuration::get($this->_name.$type.'_times');
                $times_in_db = $this->countTimes(array('id_customer'=>$id_customer,'type'=>$type));


                if($times_in_db>=$times){
                    return;
                }

                $points = (int)Configuration::get($this->_name.$type.'_points');

                $sql = 'INSERT into `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` SET
						   id_loyalty_status = '.(int)($id_loyalty_status).',
						   id_customer = '.(int)($id_customer).',
						   id_post = '.(int)($id_post).',
						   `type` = \''.pSQL($type).'\',
						   points = '.(int)($points).',
						   date_add  = "'.pSQL(date("Y-m-d H:i:s")).'",
						   date_upd  = "'.pSQL(date("Y-m-d H:i:s")).'"
						   ';


                Db::getInstance()->Execute($sql);

                $this->sendNotificationPoints(array('id_customer'=>$id_customer,'points'=>$points, 'type'=>$type));
            break;
            case 'loyality_add_blog_comment':

                // is enable points for action $type
                if(Configuration::get($this->_name.$type.'_status') != $type)
                    return;

                // How many times customer can get points
                $times = (int)Configuration::get($this->_name.$type.'_times');
                $times_in_db = $this->countTimes(array('id_customer'=>$id_customer,'type'=>$type));

                if($times_in_db>=$times){
                    return;
                }

                $points = (int)Configuration::get($this->_name.$type.'_points');

                $sql = 'INSERT into `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` SET
						   id_loyalty_status = '.(int)($id_loyalty_status).',
						   id_customer = '.(int)($id_customer).',
						   id_comment = '.(int)($id_comment).',
						   `type` = \''.pSQL($type).'\',
						   points = '.(int)($points).',
						   date_add  = "'.pSQL(date("Y-m-d H:i:s")).'",
						   date_upd  = "'.pSQL(date("Y-m-d H:i:s")).'"
						   ';

                Db::getInstance()->Execute($sql);


                $this->sendNotificationPoints(array('id_customer'=>$id_customer,'points'=>$points, 'type'=>$type));
            break;
            case 'loyality_user_my_avatar':
            case 'loyality_user_my_show':
            case 'loyality_registration':

                // is enable points for action $type
                if(Configuration::get($this->_name.$type.'_status') != $type)
                    return;


                // How many times customer can get points

                $times_in_db = $this->countTimes(array('id_customer'=>$id_customer,'type'=>$type));
                if($times_in_db>=1){
                    return;
                }

                $points = (int)Configuration::get($this->_name.$type.'_points');

                $sql = 'INSERT into `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` SET
						   id_loyalty_status = '.(int)($id_loyalty_status).',
						   id_customer = '.(int)($id_customer).',
						   `type` = \''.pSQL($type).'\',
						   points = '.(int)($points).',
						   date_add  = "'.pSQL(date("Y-m-d H:i:s")).'",
						   date_upd  = "'.pSQL(date("Y-m-d H:i:s")).'"
						   ';
                Db::getInstance()->Execute($sql);

                $this->sendNotificationPoints(array('id_customer'=>$id_customer,'points'=>$points, 'type'=>$type));

            break;
            case 'loyality_order':

                // is enable points for action $type
                if(Configuration::get($this->_name.$type.'_status') != $type)
                    return;


                // How many times customer can get points
                $times = (int)Configuration::get($this->_name.$type.'_times');
                $times_in_db = $this->countTimes(array('id_customer'=>$id_customer,'type'=>$type));


                if($times_in_db>=$times){
                    return;
                }

                $points = (int)Configuration::get($this->_name.$type.'_points');


                // check order status
                $current_state = isset($data['current_state'])?$data['current_state']:0;
                $orderstatuses = Configuration::get($this->_name.'lorderstatuses');
                $orderstatuses = explode(",",$orderstatuses);
                if(in_array($current_state,$orderstatuses)) {
                    $id_loyalty_status = 1;
                }
                // check order status


                $sql_validity_of_points = $this->validityPeriodOfPoints();

                $sql_count = 'SELECT COUNT(*) AS "count" FROM `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty`
                WHERE id_customer = '.(int)($id_customer).' AND id_order = '.(int)($id_order).' AND `type` = \''.pSQL($type).'\' '.$sql_validity_of_points.'';

                $data_count_orders = Db::getInstance()->getRow($sql_count);

                if($data_count_orders['count']) break;

                $sql = 'INSERT into `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` SET
						   id_loyalty_status = '.(int)($id_loyalty_status).',
						   id_customer = '.(int)($id_customer).',
						   id_order = '.(int)($id_order).',
						   `type` = \''.pSQL($type).'\',
						   points = '.(int)($points).',
						   date_add  = "'.pSQL(date("Y-m-d H:i:s")).'",
						   date_upd  = "'.pSQL(date("Y-m-d H:i:s")).'"
						   ';

                Db::getInstance()->Execute($sql);

                if($id_loyalty_status == 1) {
                    $this->sendNotificationPoints(array('id_customer' => $id_customer, 'points' => $points, 'type' => $type));
                }

            break;
        }




    }


    public function setLoyalityStatus($data){

        $prefix = $this->getPrefix();

        // check if enable loyality program
        if(!Configuration::get($this->_name.'loyality_on'.$prefix))
            return;

        $sql_txt = '';
        $id_loyalty_status = $data['id_loyalty_status'];
        $id_customer = $data['id_customer'];
        $id_comment = isset($data['id_comment'])?$data['id_comment']:0;
        if($id_comment){
            $sql_txt = 'AND id_comment = '.$id_comment.'';
        }
        $id_post = isset($data['id_post'])?$data['id_post']:0;
        if($id_post){
            $sql_txt = 'AND id_post = '.$id_post.'';
        }
        $id_order = isset($data['id_order'])?$data['id_order']:0;
        if($id_order){
            $sql_txt = 'AND id_order = '.$id_order.'';

            if($id_loyalty_status == 1) {
                $type = "loyality_order";
                $points = (int)Configuration::get($this->_name . $type . '_points');
                $this->sendNotificationPoints(array('id_customer' => $id_customer, 'points' => $points, 'type' => $type));
            }
        }
        $type = $data['type'];


        $sql = 'UPDATE `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` SET
						   id_loyalty_status = '.(int)($id_loyalty_status).', date_upd  = "'.pSQL(date("Y-m-d H:i:s")).'"
						   WHERE type = "'.pSQL($type).'" AND id_customer = '.(int)$id_customer.' AND is_use = 0 '.$sql_txt.'
						   ';

        //var_dump($data);echo $sql;exit;

        Db::getInstance()->Execute($sql);


    }


    public function deleteLoyalityPoints($data){

        $prefix = $this->getPrefix();

        // check if enable loyality program
        if(!Configuration::get($this->_name.'loyality_on'.$prefix))
            return;

        $sql_txt = '';
        $id_customer = $data['id_customer'];
        $id_order = isset($data['id_order'])?$data['id_order']:0;
        if($id_order){
            $sql_txt = 'AND id_order = '.$id_order.'';
        }
        $id_post = isset($data['id_post'])?$data['id_post']:0;
        if($id_order){
            $sql_txt = 'AND id_post = '.$id_post.'';
        }
        $id_comment = isset($data['id_comment'])?$data['id_comment']:0;
        if($id_order){
            $sql_txt = 'AND id_comment = '.$id_comment.'';
        }
        $type = $data['type'];


        $sql = 'DELETE FROM `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty`
						   WHERE type = "'.pSQL($type).'" AND id_customer = '.(int)$id_customer.' AND is_use = 0 '.$sql_txt.'
						   ';

        Db::getInstance()->Execute($sql);

    }


    public function updateIsUsePoints($data){
        $id_customer = $data['id_customer'];
        $sql = 'UPDATE `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` SET
						   is_use = 1
						   WHERE id_loyalty_status = 1 and id_customer = '.(int)$id_customer.'';

        Db::getInstance()->Execute($sql);
    }


    private function validityPeriodOfPoints(){

        $prefix = $this->getPrefix();
        $validity_period = Configuration::get($this->_name.'validity_period'.$prefix);
        $sql_period = '';
        if ((int)$validity_period > 0)
            $sql_period = ' AND datediff(NOW(),date_add) <= '.(int)$validity_period;

        return $sql_period;
    }

    public function countTimes($data){
        $id_customer = $data['id_customer'];
        $type = $data['type'];
        $sql = isset($data['sql'])?$data['sql']:'';

        $sql_validity_of_points = $this->validityPeriodOfPoints();

        $sql_count = '
			SELECT COUNT(`id_loyalty`) AS "count"
			FROM `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty`
			WHERE id_customer = ' . (int)$id_customer . ' and type = "'.pSQL($type).'" '.$sql.''.$sql_validity_of_points;



        $times = Db::getInstance()->getRow($sql_count);
        return $times['count'];
    }

    public function countAllTimes($data){
        $id_customer = $data['id_customer'];

        $times = array();
        if($id_customer){


            $sql_validity_of_points = $this->validityPeriodOfPoints();

        $sql_count = '
			SELECT `type`, COUNT(`type`) AS "count"
			FROM `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty`
			WHERE id_customer = ' . (int)$id_customer . ' '.$sql_validity_of_points.' group by `type`
			';
            $times = Db::getInstance()->ExecuteS($sql_count);
        }
        return $times;
    }

    public function getOrderStatuses($data = null){


        $cookie = $this->context->cookie;

        $id_lang = (int)isset($data['id_lang'])?$data['id_lang']:(int)($cookie->id_lang);

        $query = 'SELECT * from ' . _DB_PREFIX_ . 'order_state os join ' . _DB_PREFIX_ . 'order_state_lang osl on(osl.id_order_state = os.id_order_state)
                    WHERE osl.id_lang = '.(int)$id_lang;

        return Db::getInstance()->ExecuteS($query);

    }

    public function loyalitysettings($data){

        $_prefix = $data['prefix'];

        $table = $data['table'];
        $this_data = $data['this'];
        $identifier = $data['identifier'];
        $tab = $data['tab'];
        $uri = $data['uri'];

        $yes_title = $data['yes_title'];
        $no_title = $data['no_title'];
        $loyality_on_title = $data['loyality_on_title'];
        $loyality_on_desc = $data['loyality_on_desc'];

        $loyality_points_desc = $data['loyality_points_desc'];



        $title = $data['title'];
        $sel_statuses_title = $data['sel_statuses_title'];
        $sel_statuses_desc = $data['sel_statuses_desc'];

        //$cancel_statuses_title = $data['cancel_statuses_title'];
        //$cancel_statuses_desc = $data['cancel_statuses_desc'];

        $validity_period_title = $data['validity_period_title'];

        $days = $data['days'];

        $update_title = $data['update_title'];


        $cookie = $this->context->cookie;
        $id_lang = (int)($cookie->id_lang);



        $curs = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);

        $discount_type_currency_value = array();

        foreach ($curs AS $_cur){

            $discount_type_currency_value[$_cur['id_currency']] = array('amount' => Tools::getValue($_prefix.'amount['.(int)($_cur['id_currency']).']', Configuration::get($_prefix.'amount_'.(int)($_cur['id_currency']))),
                'currency'=>$_cur['sign'],
                'name'=>htmlentities($_cur['name'], ENT_NOQUOTES, 'utf-8'),
                'name_item' => $_prefix.'amount'
            );


        }


        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $title,
                    'icon' => 'fa fa-cogs fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $loyality_on_title,
                        'name' => 'loyality_on'.$_prefix,
                        'desc'=> '<b style="color:red">'.$loyality_on_desc.'</b>',

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
                        'type' => 'cms_pages_loyalty_points',
                        'label' => '',
                        'name' => 'currency_val',
                        'values'=> $discount_type_currency_value,
                        'desc'=>$loyality_points_desc,
                    ),

                    array(
                        'type' => 'text',
                        'label' => $validity_period_title,
                        'name' => 'validity_period'.$_prefix,
                        'suffix' => $days,

                    ),

                    array(
                        'type' => 'text_custom_order_statuses_loyality',
                        'label' => $sel_statuses_title,
                        'name' => 'orderstatuses',
                        'value'=> $this->getOrderStatuses(array('id_lang'=>$id_lang)),
                        'orderstatuses'=> explode(",",Configuration::get($this->_name.$_prefix.'orderstatuses')),
                        'desc'=>$sel_statuses_desc
                    ),

                    /*array(
                        'type' => 'text_custom_order_statuses_loyality',
                        'label' => $cancel_statuses_title,
                        'name' => 'orderstatusescan',
                        'value'=> $this->getOrderStatuses(array('id_lang'=>$id_lang)),
                        'orderstatuses'=> explode(",",Configuration::get($this->_name.$_prefix.'orderstatusescan')),
                        'desc' => $cancel_statuses_desc
                    ),*/



                ),
            ),
        );




        $fields_form3 = array(
            'form' => array(


                'submit' => array(
                    'title' => $update_title,
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
        $helper->submit_action = 'loyalitysettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->_name.'&tab_module='.$tab.'&module_name='.$this->_name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $uri,
            'fields_value' => $this->getConfigFieldsValuesSettings(array('prefix'=>$_prefix)),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );





        return  $helper->generateForm(array($fields_form,$fields_form3));
    }

    public function getConfigFieldsValuesSettings($data){

        $_prefix = $data['prefix'];



        $data_config = array(
            'loyality_on'.$_prefix =>  Configuration::get($this->_name.'loyality_on'.$_prefix),
            'validity_period'.$_prefix=>  Configuration::get($this->_name.'validity_period'.$_prefix),
        );

        return $data_config;

    }


    public function voucherloyality($data){


        $_prefix_shop_reviews = $data['prefix'];

        $table = $data['table'];
        $this_data = $data['this'];
        $identifier = $data['identifier'];
        $tab = $data['tab'];
        $uri = $data['uri'];


        $title_translate = $data['title'];
        $vis_on_title = $data['vis_on_title'];
        $vis_on_desc = $data['vis_on_desc'];
        $yes_title = $data['yes_title'];
        $no_title = $data['no_title'];

        $coupondesc_title = $data['coupondesc_title'];
        $coupondesc_desc = $data['coupondesc_desc'];
        $vouchercode_title = $data['vouchercode_title'];
        $vouchercode_desc = $data['vouchercode_desc'];
        $tax_title = $data['tax_title'];
        $tax1 = $data['tax1'];
        $tax2 = $data['tax2'];

        $isminamount = $data['isminamount'];
        $select_cat_title = $data['select_cat_title'];
        $select_cat_desc = $data['select_cat_desc'];
        $sdvvalid_title = $data['sdvvalid_title'];
        $sdvvalid_desc = $data['sdvvalid_desc'];
        $highlight_title = $data['highlight_title'];
        $highlight_desc = $data['highlight_desc'];
        $cumulativeother_title = $data['cumulativeother_title'];
        $cumulativereduc_title = $data['cumulativereduc_title'];
        $save_title = $data['save_title'];

        $selected_categories = $data['selected_categories'];


        $curs = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);

        $min_checkout_value = array();

        foreach ($curs AS $_cur){



            $min_checkout_value[$_cur['id_currency']]  = array('amount' => Tools::getValue('sdminamount'.$_prefix_shop_reviews.'['.(int)($_cur['id_currency']).']', Configuration::get('sdminamount'.$_prefix_shop_reviews.'_'.(int)($_cur['id_currency']))),
                'currency'=>$_cur['sign'],
                'name'=>htmlentities($_cur['name'], ENT_NOQUOTES, 'utf-8'),
                'name_item' => 'sdminamount'.$_prefix_shop_reviews
            );
        }






        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $title_translate,
                    'icon' => 'fa fa-usd fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $vis_on_title,
                        'name' => 'vis_on'.$_prefix_shop_reviews,
                        'desc' => '<b style="color:red">'.$vis_on_desc.'</b>',

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
                        'type' => 'text',
                        'label' => $coupondesc_title,
                        'name' => 'coupondesc'.$_prefix_shop_reviews,
                        'lang' => true,
                        'hint' => $coupondesc_desc,
                        'desc' => $coupondesc_desc
                    ),

                    array(
                        'type' => 'text',
                        'label' => $vouchercode_title,
                        'name' => 'vouchercode'.$_prefix_shop_reviews,
                        'desc' => $vouchercode_desc,
                    ),


                    array(
                        'type' => 'select',
                        'label' => $tax_title,
                        'name' => 'tax'.$_prefix_shop_reviews,
                        'options' => array(
                            'query' => array(
                                array(
                                    'id' => '0',
                                    'name' =>$tax1
                                ),

                                array(
                                    'id' => '1',
                                    'name' => $tax2,
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        )
                    ),



                    array(
                        'type' => 'checkbox_custom_loyalty',
                        'label' => $isminamount,
                        'name' => $this->_name.'isminamount'.$_prefix_shop_reviews,
                        'hint' => $isminamount,
                        'values' => array(
                            'query' => array(
                                array(
                                    'id' => $this->_name.'isminamount'.$_prefix_shop_reviews,
                                    'name' => '',
                                    'val' => 1
                                ),
                            ),
                            'id' => 'id',
                            'name' => 'name'
                        ),
                    ),


                    array(
                        'type' => 'cms_pages_loyalty',
                        'label' => '',
                        'name' => 'mincheckout_val'.$_prefix_shop_reviews,
                        'values'=> $min_checkout_value,

                    ),

                    array(
                        'type' => 'cms_categories',
                        'label' => $select_cat_title,
                        'hint' => $select_cat_desc,
                        'desc' => $select_cat_desc,
                        'name' => 'select_cat'.$_prefix_shop_reviews,
                        'values'=> $selected_categories,

                    ),

                    array(
                        'type' => 'text_validity',
                        'label' => $sdvvalid_title,
                        'name' => 'sdvvalid'.$_prefix_shop_reviews,
                        'value'=> Configuration::get($this->_name.'sdvvalid'.$_prefix_shop_reviews),
                        'desc' =>$sdvvalid_desc,
                    ),

                    array(
                        'type' => 'switch',
                        'label' => $highlight_title,
                        'name' => 'highlight'.$_prefix_shop_reviews,
                        'desc'=>$highlight_desc,

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
                        'type' => 'switch',
                        'label' => $cumulativeother_title,
                        'name' => 'cumulativeother'.$_prefix_shop_reviews,

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
                        'type' => 'switch',
                        'label' => $cumulativereduc_title,
                        'name' => 'cumulativereduc'.$_prefix_shop_reviews,

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
                ),

                'submit' => array(
                    'title' => $save_title,
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this_data;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $identifier;
        $helper->submit_action = 'vouchersettings'.$_prefix_shop_reviews;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->_name.'&tab_module='.$tab.'&module_name='.$this->_name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $uri,
            'fields_value' => $this->getConfigFieldsValuesCouponSettings(array('prefix'=>$_prefix_shop_reviews)),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        ob_start();
        include(dirname(__FILE__).'/../views/templates/hooks/'.$this->_name.'/_voucherloyality_js.phtml');
        $_html = ob_get_clean();

        return $_html . $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValuesCouponSettings($data){

        $_prefix_shop_reviews = $data['prefix'];

        $languages = Language::getLanguages(false);
        $fields_fcoupondesc = array();

        foreach ($languages as $lang)
        {
            $fields_fcoupondesc[$lang['id_lang']] = Configuration::get($this->_name.'coupondesc'.$_prefix_shop_reviews.'_'.$lang['id_lang']);
        }

        $data_config = array(
            'vis_on'.$_prefix_shop_reviews => Configuration::get($this->_name.'vis_on'.$_prefix_shop_reviews),
            'vouchercode'.$_prefix_shop_reviews => Configuration::get($this->_name.'vouchercode'.$_prefix_shop_reviews),

            'coupondesc'.$_prefix_shop_reviews => $fields_fcoupondesc,

            'tax'.$_prefix_shop_reviews =>  Configuration::get($this->_name.'tax'.$_prefix_shop_reviews),

            $this->_name.'isminamount'.$_prefix_shop_reviews =>  Configuration::get($this->_name.'isminamount'.$_prefix_shop_reviews),



            'cumulativeother'.$_prefix_shop_reviews => Configuration::get($this->_name.'cumulativeother'.$_prefix_shop_reviews),

            'cumulativereduc'.$_prefix_shop_reviews =>  Configuration::get($this->_name.'cumulativereduc'.$_prefix_shop_reviews),
            'highlight'.$_prefix_shop_reviews =>  Configuration::get($this->_name.'highlight'.$_prefix_shop_reviews),
        );
        return $data_config;
    }



    public function pointsactions($data){
        $table = $data['table'];
        $this_data = $data['this'];
        $identifier = $data['identifier'];
        $tab = $data['tab'];
        $uri = $data['uri'];
        $update_title = $data['update_title'];
        $title_main = $data['title_main'];
        $data_translate = $data;

        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $title_main,
                    'icon' => 'fa fa-dot-circle-o fa-lg'
                ),
                'input' => array(

                    $this->block_points_actions($data_translate),
                ),
            ),
        );

        $fields_form1 = array(
            'form' => array(
                'submit' => array(
                    'title' => $update_title,
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
        $helper->submit_action = 'pointsactionssettings';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->_name.'&tab_module='.$tab.'&module_name='.$this->_name;
        $helper->tpl_vars = array(
            'uri' => $uri,
            'fields_value' => $this->getConfigFieldsValuesPointsSettings(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        return   $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesPointsSettings(){

        $data_config = array(
            'is_blocklr' => (int)Configuration::get($this->_name.'is_blocklr'),
        );

        return $data_config;
    }

    private function block_points_actions($data){

        $loyality_title = $data['loyality_title'];



        $values = array();
        foreach($data['loyality_points'] as $k=>$item){
            $values[$k]=array(
                'name'=>$item['name'],
                'status' => Configuration::get($this->_name.$k.'_status'),
                'points' => array('points' => (int)Configuration::get($this->_name.$k.'_points'), 'name'=>$k.'_points'),
                'times' => array('times' => (int)Configuration::get($this->_name.$k.'_times'), 'name'=>$k.'_times','count_times'=>isset($item['count_times'])?$item['count_times']:0),
                'product' => array('product' => Configuration::get($this->_name.$k.'_product'),'is_product'=>isset($item['is_product'])?$item['is_product']:0),
            );
        }



        $block_last_reviews = array(
            'type' => 'loyality_points_per_action',
            'label' => $loyality_title,
            'name' => 'loyality_points_per_action',
            'values'=> $values,


        );

        return $block_last_reviews;
    }


    public function loyaltymessages($data){

        $table = $data['table'];
        $this_data = $data['this'];
        $identifier = $data['identifier'];
        $tab = $data['tab'];
        $uri = $data['uri'];
        $update_title = $data['update_title'];
        $title_main = $data['title_main'];

        $available_points = $data['available_points'];
        $label1 = $data['label1'];
        $label2 = $data['label2'];

        $desc1 = $data['desc1'];
        $desc2 = $data['desc2'];


        $data_messages = array();
        $i=1;

        foreach($available_points as $k => $item) {

            $this->array_push_pos($data_messages, $i,

                array(
                    'type' => 'textarea',
                    'label' => $label1.' '.$label2.' '.$item['name'],
                    'name' => $k.'description',
                    'autoload_rte' => FALSE,
                    'lang' => TRUE,
                    'rows' => 5,
                    'cols' => 40,
                    'desc' => $desc1.' <code>[points]</code>. '.$desc2,

                ),

                $k.'description'
            );

            $i++;


        }


        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $title_main,
                    'icon' => 'fa fa-comment fa-lg'
                ),
                'input' =>

                    $data_messages,
            ),
        );

        $fields_form1 = array(
            'form' => array(
                'submit' => array(
                    'title' => $update_title,
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
        $helper->submit_action = 'loyaltymessagessettings';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->_name.'&tab_module='.$tab.'&module_name='.$this->_name;
        $helper->tpl_vars = array(
            'uri' => $uri,
            'fields_value' => $this->getConfigFieldsValuesMsgSettings(array('available_points'=>$available_points)),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        return   $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesMsgSettings($data){

        $available_points = $data['available_points'];

        $languages = Language::getLanguages(false);
        $fields_description = array();


        $data_config = array();

        foreach($available_points as $k => $item) {

            foreach ($languages as $lang) {
                $fields_description[$lang['id_lang']] = Configuration::get($this->_name.$k.'description_' . $lang['id_lang']);
            }

            $data_config[$k.'description'] = $fields_description;
        }


        return $data_config;
    }


    public function loyaltymy($data){

        $_prefix = $data['prefix'];

        $table = $data['table'];
        $this_data = $data['this'];
        $identifier = $data['identifier'];
        $tab = $data['tab'];
        $uri = $data['uri'];

        $d_eff_loyalty_my_title = $data['d_eff_loyalty_my_title'];
        $d_eff_loyalty_val = $data['d_eff_loyalty_val'];


        $title = $data['title'];

        $my_account = $data['my_account'];

        $update_title = $data['update_title'];

        $fields_form = array(
            'form'=> array(
                'legend' => array(
                    'title' => $title,
                    'icon' => 'fa fa-user fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'text',
                        'label' => $my_account,
                        'name' => 'perpagemy'.$_prefix,

                    ),
                    array(
                        'type' => 'wow_display_effect',
                        'label' => $d_eff_loyalty_my_title,
                        'name' => 'd_eff_loyalty_my'.$_prefix,
                        'id' => 'd_eff_loyalty_my'.$_prefix,
                        'lang' => FALSE,
                        'value' => $d_eff_loyalty_val
                    ),

                ),
            ),
        );




        $fields_form3 = array(
            'form' => array(


                'submit' => array(
                    'title' => $update_title,
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
        $helper->submit_action = 'loyalitymysettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->_name.'&tab_module='.$tab.'&module_name='.$this->_name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $uri,
            'fields_value' => $this->getConfigFieldsValuesLoyaltyMySettings(array('prefix'=>$_prefix)),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );





        return  $helper->generateForm(array($fields_form,$fields_form3));
    }

    public function getConfigFieldsValuesLoyaltyMySettings($data){

        $_prefix = $data['prefix'];

        $data_config = array(
            'd_eff_loyalty_my'.$_prefix =>  Configuration::get($this->_name.'d_eff_loyalty_my'.$_prefix),
            'perpagemy'.$_prefix=>  Configuration::get($this->_name.'perpagemy'.$_prefix),
        );

        return $data_config;

    }



    public function loyaltyemails($data){

        $_prefix = $data['prefix'];

        $table = $data['table'];
        $this_data = $data['this'];
        $identifier = $data['identifier'];
        $tab = $data['tab'];
        $uri = $data['uri'];

        $title = $data['title'];
        $noti_title = $data['noti_title'];

        $update_title = $data['update_title'];

        $fields_form = array(
            'form'=> array(

                'legend' => array(
                    'title' => $title,
                    'icon' => 'fa fa-envelope-o fa-lg'
                ),
                'input' => array(


                    array(
                        'type' => 'checkbox_custom_store',
                        'label' => $noti_title,
                        'name' => 'noti'.$_prefix,
                        'values' => array(
                            'value' => (int)Configuration::get($this->_name.'noti'.$_prefix)
                        ),
                    ),
                ),

            ),
        );

        $fields_form1 = array(
            'form' => array(


                'submit' => array(
                    'title' => $update_title,
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
        $helper->submit_action = 'loyaltyemailssettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->_name.'&tab_module='.$tab.'&module_name='.$this->_name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $uri,
            'fields_value' => $this->getConfigFieldsValuesLoyaltyemailsShopSettings(array('prefix'=>$_prefix)),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return  $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesLoyaltyemailsShopSettings($data){

        $data_config = array(

        );

        return $data_config;
    }

    private function _emailsubjects16_desc($data){

        $desc = isset($data['desc'])?$data['desc']:'';
        $name_template = isset($data['name'])?$data['name']:'';

        ob_start();
        include(dirname(__FILE__).'/../views/templates/hooks/'.$this->_name.'/'.__FUNCTION__.'.phtml');
        $_html = ob_get_clean();


        return $_html;
    }

    public function loyaltyemailssubjects($data){

        $_prefix = $data['prefix'];

        $table = $data['table'];
        $this_data = $data['this'];
        $identifier = $data['identifier'];
        $tab = $data['tab'];
        $uri = $data['uri'];

        $update_title = $data['update_title'];
        $yes_title = $data['yes_title'];
        $no_title = $data['no_title'];
        $title = $data['title'];


        $em_desc1 = $data['em_desc1'];
        $em_desc2 = $data['em_desc2'];
        $em_desc3 = $data['em_desc3'];
        $em_desc4 = $data['em_desc4'];
        $em_desc5 = $data['em_desc5'];

        $is_points = $data['is_points'];
        $points = $data['points'];

        $is_loyaltyvouch = $data['is_loyaltyvouch'];
        $loyaltyvouch = $data['loyaltyvouch'];

        $fields_form = array(
            'form'=> array(
                //'tinymce' => FALSE,
                'legend' => array(
                    'title' => $title,
                    'icon' => 'fa fa-envelope-o fa-lg'
                ),
                'input' => array(

                    array(
                        'type' => 'switch',
                        'label' => $is_points,
                        'name' => 'is_points'.$_prefix,
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

                        'desc' => $is_points,
                    ),

                    array(
                        'type' => 'text',
                        'label' => $points,
                        'name' => 'points'.$_prefix,
                        'id' => 'points'.$_prefix,
                        'lang' => TRUE,
                        'desc' => $em_desc1.' '
                            . $em_desc2
                            .'  "mails" '
                            . $em_desc3
                            .' "'.$this->_name.'" '
                            .$em_desc4
                            .$this->_emailsubjects16_desc(array('name'=>'loyalty-points','desc'=>$em_desc5))

                    ),
                    array(
                        'type' => 'switch',
                        'label' => $is_loyaltyvouch,
                        'name' => 'is_loyaltyvouch'.$_prefix,

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
                        'desc' => $is_loyaltyvouch,
                    ),

                    array(
                        'type' => 'text',
                        'label' => $loyaltyvouch,
                        'name' => 'loyaltyvouch'.$_prefix,
                        'id' => 'loyaltyvouch'.$_prefix,
                        'lang' => TRUE,
                        'desc' => $em_desc1.' '
                            . $em_desc2
                            .'  "mails" '
                            . $em_desc3
                            .' "'.$this->_name.'" '
                            .$em_desc4
                            .$this->_emailsubjects16_desc(array('name'=>'loyalty-voucher','desc'=>$em_desc5))

                    ),


                ),
            ),
        );

        $fields_form1 = array(
            'form' => array(
                'submit' => array(
                    'title' => $update_title,
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
        $helper->submit_action = 'loyaltyemailssubjectssettings';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->_name.'&tab_module='.$tab.'&module_name='.$this->_name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $uri,
            'fields_value' => $this->getConfigFieldsValuesEmailSubjectsSettings(array('prefix'=>$_prefix)),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return  $helper->generateForm(array($fields_form,$fields_form1));
    }

    public function getConfigFieldsValuesEmailSubjectsSettings($data){

        $_prefix = $data['prefix'];

        $languages = Language::getLanguages(false);
        $fields_loyaltyvouch = array();
        $fields_points = array();

        foreach ($languages as $lang)
        {
            $fields_loyaltyvouch[$lang['id_lang']] =  Configuration::get($this->_name.'loyaltyvouch'.$_prefix.'_'.$lang['id_lang']);
            $fields_points[$lang['id_lang']] =  Configuration::get($this->_name.'points'.$_prefix.'_'.$lang['id_lang']);

        }

        $data_config = array(
            'loyaltyvouch'.$_prefix => $fields_loyaltyvouch,
            'points'.$_prefix => $fields_points,

            'is_loyaltyvouch'.$_prefix => Configuration::get($this->_name.'is_loyaltyvouch'.$_prefix),
            'is_points'.$_prefix => Configuration::get($this->_name.'is_points'.$_prefix),


        );
        return $data_config;
    }

    private function array_push_pos(&$array,$pos=0,$value = [],$key='')
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


    public function getItemForStat($data){

        $id_customer = $data['id_customer'];
        $start = $data['start'];
        $step = $data['step'];


        include_once(_PS_MODULE_DIR_ . $this->_name . '/blockblog.php');
        $obj_blockblog = new blockblog();

        $points_data = $obj_blockblog->getAvailablePoints();

        $sql_validity_of_points = $this->validityPeriodOfPoints();

        $sql = 'SELECT * FROM `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` s WHERE id_loyalty_status = 1 and s.`id_customer` = '.(int)$id_customer.' '.$sql_validity_of_points.' order by date_add desc LIMIT '.(int)$start.' ,'.(int)$step.'';
        $data = Db::getInstance()->ExecuteS($sql);


        $not_used_points = 0;
        $used_points = 0;

        foreach ($data as $k => $item) {
            $type = $item['type'];
            $name = $points_data[$type]['name'];
            $data[$k]['name_action'] = $name;

            if($item['is_use']==0){
                $not_used_points += $item['points'];
            } else {
                $used_points += $item['points'];
            }


        }


        $data_count = Db::getInstance()->getRow('SELECT COUNT(*) AS "count" FROM `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` s WHERE id_loyalty_status = 1 and s.`id_customer` = '.(int)$id_customer.' '.$sql_validity_of_points.'');




        $sql = 'SELECT c.id_customer as id, c.firstname, c.lastname FROM  `'. _DB_PREFIX_ . 'customer` c WHERE c.id_customer = '.(int)$id_customer.'';
        $data_customer = Db::getInstance()->ExecuteS($sql);


        return array('data'=>$data,
                    'count_all'=>$data_count['count'],
                     'data_customer'=>$data_customer,
                    'used_points'=>$used_points,
                    'not_used_points'=>$not_used_points,
        );

    }

    public function getStep(){
        return $this->step;
    }

    public function getStepMy(){
        return  Configuration::get($this->_name.'perpagemy'.$this->getPrefix());
    }

    public function paging17($data)
    {
        $start = $data['start'];
        $count = $data['count'];
        $step = $data['step'];
        $data['prefix'] = $this->_name;

        include_once(_PS_MODULE_DIR_.$this->_name.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();


        $data_url = $obj_blog->getSEOURLs();
        $data['ajax_url'] = $data_url['ajax_url'];




        include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/pagenavhelp.class.php');
        $obj = new pagenavhelp();
        return $obj->pagenav($start, $count, $step, $data);
    }

    public function getMyLoyaltyPoints($data){

        $id_customer = $data['id_customer'];
        $start = $data['start'];
        $step = $this->getStepMy();

        include_once(_PS_MODULE_DIR_ . $this->_name . '/blockblog.php');
        $obj_blockblog = new blockblog();

        $points_data = $obj_blockblog->getAvailablePoints();


        $sql_validity_of_points = $this->validityPeriodOfPoints();

        $sql = 'SELECT pc.* FROM `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` pc where id_loyalty_status = 1 and id_customer = '.(int)($id_customer).' '.$sql_validity_of_points.'  ORDER BY date_add desc LIMIT '.(int)($start).', '.(int)($step);

        $data = Db::getInstance()->ExecuteS($sql);

        foreach ($data as $k => $item) {
            $type = $item['type'];
            $name = $points_data[$type]['name'];
            $data[$k]['name_action'] = $name;


        }


        $sql = 'SELECT pc.* FROM `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` pc where id_loyalty_status = 1 and id_customer = '.(int)($id_customer).' '.$sql_validity_of_points.'';

        $data_all = Db::getInstance()->ExecuteS($sql);

        $not_used_points = 0;
        $used_points = 0;

        foreach ($data_all as $k => $item) {

            if($item['is_use']==0){
                $not_used_points += $item['points'];
            } else {
                $used_points += $item['points'];
            }


        }

        $result = Db::getInstance()->getRow('SELECT COUNT(*) AS "count" FROM `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` pc where id_loyalty_status = 1 and  id_customer = '.(int)($id_customer).' '.$sql_validity_of_points.'');

        return array('data' => $data, 'count_all' => (int)$result['count'], 'used_points'=>$used_points,'not_used_points'=>$not_used_points,);
    }


    public function getVoucherValue(){

        $prefix = $this->getPrefix();
        $cookie = $this->context->cookie;
        $id_currency = (int)$cookie->id_currency;
        $voucher_points = Configuration::get($prefix.'amount_'.(int)$id_currency);

        $currency = new CurrencyCore($cookie->id_currency);

        //echo "<pre>";var_dump($currency);exit;

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $my_currency_iso_code = isset($currency->symbol)?$currency->symbol:$currency->sign;
        } else {
            $my_currency_iso_code = $currency->sign;
        }

        //var_dump($my_currency_iso_code);


        return array('voucher_points'=>$voucher_points,'my_currency_iso_code'=>$my_currency_iso_code);
    }

    public function createVoucher($data){
        $id_customer = $data['id_customer'];

        $prefix = $this->getPrefix();

        $cookie = $this->context->cookie;
        $name_module = $this->_name;
        $code_module = Configuration::get($name_module.'vouchercode'.$prefix);

        $uid = $id_customer;


        ## select not used points for customer ##
        $sql_validity_of_points = $this->validityPeriodOfPoints();

        $sql = 'SELECT pc.* FROM `'._DB_PREFIX_.$this->getPrefixDB().'_loyalty` pc where id_loyalty_status = 1 and id_customer = '.(int)($id_customer).' '.$sql_validity_of_points.'';

        $data_all = Db::getInstance()->ExecuteS($sql);

        $not_used_points = 0;
        $used_points = 0;

        foreach ($data_all as $k => $item) {
            if($item['is_use']==0){
                $not_used_points += $item['points'];
            } else {
                $used_points += $item['points'];
            }
        }

        $data_voucher = $this->getVoucherValue();
        $voucher_points = $data_voucher['voucher_points'];

        ## select not used points for customer ##


        Db::getInstance()->Execute('BEGIN');


        $id_currency = (int)$cookie->id_currency;
        $id_discount_type = 2;
        $value = $not_used_points*$voucher_points;


        $current_language = (int)$cookie->id_lang;

        $coupon = new CartRule();

        $gen_pass = Tools::strtoupper(Tools::passwdGen(8));

        foreach (Language::getLanguages() AS $language){
            $coupon->name[(int)$language['id_lang']] = $code_module.'-'.$gen_pass;
        }
        $coupon->description = Configuration::get($name_module.'coupondesc'.$prefix.'_'.$current_language);


        $codename = $code_module.'-'.$gen_pass;
        $category = explode(",",Configuration::get($name_module.'catbox'.$prefix));


        $coupon->code = $codename;
        $type = 'reduction_amount';

        $coupon->$type = ($value);

        $is_tax = (int)Configuration::get($name_module.'tax'.$prefix);

        $coupon->reduction_currency = (int)($id_currency);
        if(Configuration::get($name_module.'isminamount'.$prefix) == true ||
            Configuration::get($name_module.'isminamount'.$prefix) == 1){
            $coupon->minimum_amount = (int)(Configuration::get('sdminamount'.$prefix.'_'.(int)$id_currency));
            $coupon->minimum_amount_currency = (int)($id_currency);
            $coupon->minimum_amount_tax= (int)$is_tax;
        }

        if($id_discount_type == 2)
            $coupon->reduction_tax = (int)$is_tax;


        if (sizeof($category)>0) {
            $coupon->product_restriction = 1;

            if($id_discount_type == 1){
                $coupon->reduction_product = -2;
            }
        }


        // shared data
        $coupon->value = ($value);
        $coupon->id_customer = $uid;
        $coupon->quantity = 1;
        $coupon->quantity_per_user = 1;

        // cumulable

        $coupon->cart_rule_restriction = ((Configuration::get($name_module.'cumulativeother'.$prefix))==0?1:0);

        $coupon->cumulable = (int)(Configuration::get($name_module.'cumulativeother'.$prefix));

        $coupon->highlight = (int)(Configuration::get($name_module.'highlight'.$prefix));

        if(version_compare(_PS_VERSION_, '1.7', '>')){
            $coupon->reduction_exclude_special = ((int)(Configuration::get($name_module.'cumulativereduc'.$prefix))==0)?1:0;
        } else {
            $coupon->cumulable_reduction = (int)(Configuration::get($name_module.'cumulativereduc'.$prefix));
        }

        // cumulable


        $coupon->active = 1;

        $start_date = date('Y-m-d H:i:s');
        $coupon->date_from = $start_date;

        $different = strtotime(date('Y-m-d H:i:s')) + Configuration::get($this->_name.'sdvvalid'.$prefix)*24*60*60;
        $end_date = date('Y-m-d H:i:s',$different);
        $coupon->date_to = $end_date;



        $is_voucher_create = $coupon->add(true, false);

        if ($is_voucher_create && sizeof($category)>0)
        {
            // add a cart rule
            $is_voucher_create = $this->addProductRule($coupon->id, 1, 'categories', $category);
        }

        $this->updateIsUsePoints(array('id_customer'=>$uid));

        if (!$is_voucher_create){
            Db::getInstance()->Execute('ROLLBACK');
        }

        $code_v = $codename;

        Db::getInstance()->Execute('COMMIT');



        $data_voucher = array('voucher_code'=>$code_v,'date_until' => date('d/m/Y H:i:s',$different));

        if (Configuration::get($this->_name . 'vis_on' . $prefix) == 1) {

            $data_customer = $this->_getInfoCustomerDB(array('id_customer'=>$id_customer));


            $id_currency = (int)$cookie->id_currency;
            $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
            foreach ($cur AS $_cur){
                if($id_currency == $_cur['id_currency']){
                    $valuta = $_cur['sign'];
                }
            }


            $this->sendNotificationCreatedVoucher(
                array(
                    'email' => $data_customer['email'],
                    'name' => $data_customer['firstname'].' '.$data_customer['lastname'],
                    'data_voucher' => $data_voucher,
                    'id_lang' => $current_language,
                    'discountvalue'=>$value.$valuta,
                    'is_tax'=>$is_tax,
                )
            );

        }
        // send voucher with discount //


        return $data_voucher;


    }

    public function addProductRule($cart_rule_id, $qty, $type, array $ids)
    {
        $insert = false;

        // set transaction
        Db::getInstance()->Execute('BEGIN');

        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_group (id_cart_rule, quantity) VALUES('
            . (int)($cart_rule_id) . ', ' . (int)($qty) . ')';

        // only if group rule is added
        if (Db::getInstance()->Execute($sql)) {

            $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule (id_product_rule_group, type) VALUES('
                . (int)(Db::getInstance()->Insert_ID()) . ', "' . pSQL($type) . '")';

            // only if product rule is added
            if (Db::getInstance()->Execute($sql)) {

                if (!empty($ids)) {
                    $insert = true;

                    $iLastInsertId = Db::getInstance()->Insert_ID();

                    foreach ($ids as $id) {
                        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_value (id_product_rule, id_item) VALUES('
                            . (int)($iLastInsertId) . ', ' . (int)($id) . ')';

                        if (!Db::getInstance()->Execute($sql)) {
                            $insert = false;
                        }

                    }

                    $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_shop (id_cart_rule, id_shop) VALUES('
                        . (int)($cart_rule_id) . ', ' . (int)$this->getIdShop() . ')';

                    if (!Db::getInstance()->Execute($sql)) {
                        $insert = false;
                    }
                }
            }
        }
        // commit or rollback transaction
        $insert = ($insert)? Db::getInstance()->Execute('COMMIT') : Db::getInstance()->Execute('ROLLBACK');

        return $insert;
    }

    private function _getInfoCustomerDB($data){
        $id_customer = $data['id_customer'];
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'customer`
		        WHERE `id_customer` = '.(int)$id_customer.'';
        $result = Db::getInstance()->GetRow($sql);

        return $result;
    }


    public function sendNotificationCreatedVoucher($data = null)
    {

        $prefix = $this->getPrefix();



        if (Configuration::get($this->_name . 'noti' . $prefix) == 1 && Configuration::get($this->_name.'is_loyaltyvouch'.$prefix) == 1) {


            $discountvalue = $data['discountvalue'];
            $is_tax = $data['is_tax'];


            include_once(_PS_MODULE_DIR_ . $this->_name . '/blockblog.php');
            $obj = new blockblog();

            $_data_translate_custom = $obj->translateCustom();


            $firsttext = $_data_translate_custom['firsttext'];

            $tax = $is_tax;
            if ($tax) {
                $tax_text = ' (' . $_data_translate_custom['tax_included'] . ') ';
            } else {
                $tax_text = ' (' . $_data_translate_custom['tax_excluded'] . ') ';
            }


            $secondtext = $_data_translate_custom['secondtext'];
            $threetext = $_data_translate_custom['threetext'];


            $discountvalue = $discountvalue . $tax_text;




            $email_customer = $data['email'];
            $customer_name = $data['name'];
            $voucher_code = $data['data_voucher']['voucher_code'];
            $date_until = $data['data_voucher']['date_until'];
            $id_lang = $data['id_lang'];

            include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/blogspm.class.php');
            $obj = new blogspm();
            $data_url = $obj->getSEOURLs(array('id_lang' => $id_lang));
            $loyalty_account_url = $data_url['loyalty_account_url'];


            /* Email generation */
            $templateVars = array(
                '{firsttext}' => $firsttext,
                '{discountvalue}' => $discountvalue,
                '{secondtext}' => $secondtext,
                '{threetext}' => $threetext,
                '{voucher_code}' => $voucher_code,
                '{date_until}' => $date_until,
                '{customer_name}' => $customer_name,
                '{loyalty_account_url}' => $loyalty_account_url,

            );


            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            } else {
                $id_lang_current = Language::getIdByIso('en');
            }

            $subject_voucher = Configuration::get($this->_name . 'loyaltyvouch' . $prefix . '_' . $id_lang);

            $id_shop = $this->getIdShop();



            /* Email sending */
            Mail::Send($id_lang_current, 'loyalty-voucher', $subject_voucher, $templateVars,
                $email_customer, 'Voucher Form', NULL, NULL,
                NULL, NULL, dirname(__FILE__) . '/../mails/', NULL, $id_shop);


        }

    }


    public function sendNotificationPoints($data = null)
    {

        $prefix = $this->getPrefix();

        if(Configuration::get($this->_name . 'noti' . $prefix) == 1 && Configuration::get($this->_name.'is_points'.$prefix) == 1) {

            $points = $data['points'];
            $id_customer = $data['id_customer'];
            $type = $data['type'];

            $data_customer = $this->_getInfoCustomerDB(array('id_customer'=>$id_customer));

            $cookie = $this->context->cookie;
            $id_lang = (int)($cookie->id_lang);


            $subject_thank_you = Configuration::get($this->_name . 'points' . $prefix . '_' . $id_lang);

            include_once(_PS_MODULE_DIR_ . $this->_name . '/classes/blogspm.class.php');
            $obj = new blogspm();
            $data_url = $obj->getSEOURLs(array('id_lang' => $id_lang));
            $loyalty_account_url = $data_url['loyalty_account_url'];


            include_once(_PS_MODULE_DIR_ . $this->_name . '/blockblog.php');
            $obj = new blockblog();

            $all_points = $obj->getAvailablePoints();

            $action_name = (isset($all_points[$type]['name'])?$all_points[$type]['name']:(isset($all_points[$type])?$all_points[$type]:''));


            $email = $data_customer['email'];
            /* Email generation */
            $templateVars = array(
                '{name}' => $data_customer['firstname'].' '.$data_customer['lastname'],
                '{points}'=>isset($points['points'])?$points['points']:$points,
                '{action_name}'=>$action_name,
                '{loyalty_account_url}'=>$loyalty_account_url

            );


            /* Email sending */

            $iso_lng = Language::getIsoById((int)($id_lang));

            $dir_mails = _PS_MODULE_DIR_ . '/' . $this->_name . '/' . 'mails/';

            if (is_dir($dir_mails . $iso_lng . '/')) {
                $id_lang_current = $id_lang;
            } else {
                $id_lang_current = Language::getIdByIso('en');
            }

            $id_shop = $this->getIdShop();

            //var_dump($id_lang_current);var_dump($subject_thank_you);var_dump($templateVars); var_dump($email); var_dump($id_shop);exit;

            Mail::Send($id_lang_current, 'loyalty-points', $subject_thank_you, $templateVars,
                $email, 'Thank you Form', NULL, NULL,
                NULL, NULL, dirname(__FILE__) . '/../mails/',NULL, $id_shop);


        }


    }

    public function getIdShop()
    {
        $id_shop = Context::getContext()->shop->id;
        return $id_shop;
    }

}