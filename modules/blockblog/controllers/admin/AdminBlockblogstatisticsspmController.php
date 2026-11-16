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
require_once(_PS_MODULE_DIR_ . 'blockblog/classes/StatisticsblogspmItems.php');

class AdminBlockblogstatisticsspmController extends ModuleAdminController {

    private $_name_controller = 'AdminBlockblogstatisticsspm';
    private $_name_module = 'blockblog';

    public function __construct()
    {

        $name_module = 'blockblog';

        $this->bootstrap = true;

        $this->module = $name_module;

        $this->table = $this->getPrefixDB().'_loyalty';
        $this->className = 'StatisticsblogspmItems';
        $this->identifier = 'id_customer';

        $this->lang = false;

        $this->_orderBy = 'id_customer';
        $this->_orderWay = 'DESC';


        $this->allow_export = false;
        $this->list_no_link = false;

        $id_shop =  Context::getContext()->shop->id;
        $this->_id_shop = $id_shop;


        $validity_period = Configuration::get($name_module.'validity_periodl');
        $sql_period = '';
        if ((int)$validity_period > 0)
            $sql_period = ' AND datediff(NOW(),l.date_add) <= '.(int)$validity_period;

        $sql_period_a = '';
        if ((int)$validity_period > 0)
            $sql_period_a = ' AND datediff(NOW(),a.date_add) <= '.(int)$validity_period;



        $this->_select .= 'c.id_customer as user_id, c.firstname, c.lastname';
        $this->_join .= ' JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer = (select distinct id_customer from ' . _DB_PREFIX_ . ''.$this->table.' where id_customer = a.id_customer) and c.deleted = 0)';

        $this->_where .= $sql_period_a;
        $this->_where .= ' GROUP BY a.id_customer';

        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_post` bp
				    WHERE bp.author_id = a.id_customer and FIND_IN_SET('.(int)$this->_id_shop.',bp.ids_shops)) as count_posts ';
        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_post` bp
				    WHERE bp.author_id = a.id_customer and FIND_IN_SET('.(int)$this->_id_shop.',bp.ids_shops) and status = 1) as count_posts_active ';
        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_post` bp
				    WHERE bp.author_id = a.id_customer and FIND_IN_SET('.(int)$this->_id_shop.',bp.ids_shops) and status != 1) as count_posts_noactive ';


        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_comments` bp
				    WHERE bp.id_customer = a.id_customer and bp.id_shop = '.(int)$this->_id_shop.') as count_comments ';
        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_comments` bp
				    WHERE bp.id_customer = a.id_customer and bp.id_shop = '.(int)$this->_id_shop.' and status = 1) as count_comments_active ';
        $this->_select .= ', (select count(*) as count from `'._DB_PREFIX_.'blog_comments` bp
				    WHERE bp.id_customer = a.id_customer and bp.id_shop = '.(int)$this->_id_shop.' and status != 1) as count_comments_noactive ';



        $id_shop =  Context::getContext()->shop->id;


        $this->_select .=
            ', (SELECT count(*) FROM `' . _DB_PREFIX_ . 'orders` as o WHERE o.id_customer = a.id_customer AND o.id_shop = '.(int)$id_shop.'
            and id_order in(select id_order from '._DB_PREFIX_.$this->table.' l where l.id_customer = a.id_customer)) as total_orders
             , (SELECT SUM(total_paid_tax_incl) FROM `' . _DB_PREFIX_ . 'orders` as o WHERE o.id_customer = a.id_customer AND o.id_shop = '.(int)$id_shop.'
             and id_order in(select id_order from '._DB_PREFIX_.$this->table.' l where l.id_customer = a.id_customer)) as  total_money
        ';


        $this->_select .= ', (SELECT SUM(l.points) FROM `'._DB_PREFIX_.$this->table.'` l  WHERE id_loyalty_status = 1 and l.`id_customer` = a.id_customer '.$sql_period.') as count_points ';

        $this->_select .= ', (SELECT SUM(l.points) FROM `'._DB_PREFIX_.$this->table.'` l  WHERE id_loyalty_status = 1 and l.`id_customer` = a.id_customer and is_use = 1 '.$sql_period.') as used_points ';

        $this->_select .= ', (SELECT SUM(l.points) FROM `'._DB_PREFIX_.$this->table.'` l  WHERE id_loyalty_status = 1 and l.`id_customer` = a.id_customer and is_use = 0 '.$sql_period.') as not_used_points ';



        //$this->addRowAction('view');
        $this->addRowAction('edit');

        //$this->addRowAction('');

        /*if(version_compare(_PS_VERSION_, '1.7', '<')) {
            $cookie = Context::getContext()->cookie;
            $id_currency = $cookie->id_currency;

            $currency = new Currency($id_currency);
            //echo "<pre>";var_dump($currency);
        } else {
            $currency = 0;
        }*/


        $this->fields_list = array(
            'user_id' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'filter_key' => 'a!id_customer',
                'width' => 25,
                'search' => false,
                'orderby' => true,

            ),
            'firstname' => array(
                'title' => $this->l('First Name'),
                'width' => 'auto',
                'hint' => $this->l('You can search customer by First Name'),
                'filter_key' => 'c!firstname',
                'align' => 'center',
            ),
            'lastname' => array(
                'title' => $this->l('Last Name'),
                'width' => 'auto',
                'hint' => $this->l('You can search customer by Last Name'),
                'filter_key' => 'c!lastname',
                'align' => 'center',

            ),



            'count_posts_total' => array(
                'title' => $this->l('Blog posts'),
                'hint' => $this->l('Total Blog posts / Active Blog posts / Not active Blog posts'),
                'width' => 'auto',
                'align' => 'center',
                'search' => false,
                'orderby' => true,
                'type_custom'=>'total_items_blog_posts',
            ),

            'count_blog_comments_total' => array(
                'title' => $this->l('Blog comments'),
                'hint' => $this->l('Total Blog Comments / Active Blog Comments / Not active Blog Comments'),
                'width' => 'auto',
                'align' => 'center',
                'search' => false,
                'orderby' => true,
                'type_custom'=>'total_items_blog_comments',
            ),

            'count_points' => array(
                'title' => $this->l('Loyalty points'),
                'hint'=> $this->l('Total Loyalty points / Used Loyalty points / Not used Loyalty points'),
                'width' => 'auto',
                'align' => 'center',
                'search' => false,
                'orderby' => true,
                'type_custom'=>'total_items_loyalty',
            ),


            'total_money' => array(
                'title' => $this->l('Orders / Money'),
                'hint'=>$this->l('Valid orders placed / Total money spent since registration'),
                'width' => 'auto',
                'type_custom'=>'total_items',
                'search' => false,
                'orderby' => true,
                'is_16'=>version_compare(_PS_VERSION_, '1.7', '<'),
                //'currency'=>$currency,


            ),

        );






        parent::__construct();
    }


    public function initToolbar() {
        parent::initToolbar();
        $this->toolbar_btn['new'] = array('href'=>'','desc'=>'');

        unset($this->toolbar_btn['new']);

    }

    public function setMedia($isNewTheme=null)
    {

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            parent::setMedia($isNewTheme);
        } else {
            parent::setMedia();
        }

        //$this->context->controller->addJs(__PS_BASE_URI__.'modules/fourconandref/views/js/admin.js');

        //$this->context->controller->addCSS(__PS_BASE_URI__.'modules/fourconandref/views/css/admin.css');

    }


    public function postProcess()
    {

        return parent::postProcess(true);

    }



    public function renderForm()
    {


        $id = Tools::getValue('id_customer');
        $name_module = 'blockblog';

        if (!($this->loadObject(true)))
            return;


        if(Tools::getValue('start_mod')){
            $start = Tools::getValue('start_mod');
        } else {
            $start = 0;
        }


        include_once(_PS_MODULE_DIR_.$name_module.'/classes/loyalityblog.class.php');
        $obj = new loyalityblog();


        $step = $obj->getStep();

        $info = $obj->getItemForStat(array('start'=>$start,'step' => $step,'id_customer'=>$id));



        $current_index = AdminController::$currentIndex;
        $token = Tools::getAdminTokenLite($this->_name_controller);
        $currentUrl = $current_index.'&amp;token='.$token.'';



        $paging = $this->PageNav($start,$info['count_all'],$step,$currentUrl,$id);



        $this->fields_form = array(
            'tinymce' => TRUE,

            'legend' => array(
                'title' => $info['data_customer'][0]['firstname'].' '.$info['data_customer'][0]['lastname'].' '.$this->l('statistics').':',
                'icon' => 'icon-list-alt'
            ),
            'input' => array(
                array(
                    'type' => 'cms_blocks_custom',
                    'label' => '',
                    'name' => 'cms_blocks_custom',
                    'values' => $info,
                    'paging' => $paging,

                )
            ),
            'buttons' => array(

            )


        );

        return parent::renderForm();
    }

    private function getPrefixDB()
    {
        include_once(_PS_MODULE_DIR_ .  'blockblog/blockblog.php');
        $obj_blockblog = new blockblog();
        $_prefix = $obj_blockblog->getLoyalityDbPrefix();
        return $_prefix;

    }


    public function PageNav($start,$count,$step,$currentUrl,$customer_id)
    {

        ob_start();
        $number_tab = 99;
        include(dirname(__FILE__).'/../../views/templates/hooks/'.$this->_name_module.'/PageNav_loyalty.phtml');
        $res = ob_get_clean();


        return $res;
    }


    public function l($string , $class = NULL, $addslashes = false, $htmlentities = true){
        if(version_compare(_PS_VERSION_, '1.7', '<')) {
            return parent::l($string);
        } else {
            return Translate::getModuleTranslation($this->_name_module, $string, $this->_name_module);
        }
    }


}

?>
