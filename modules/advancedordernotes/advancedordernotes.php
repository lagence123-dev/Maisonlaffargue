<?php
/**
*    2007-2017 PrestaShop
*
*    NOTICE OF LICENSE
*
*    This source file is subject to the Academic Free License (AFL 3.0)
*    that is bundled with this package in the file LICENSE.txt.
*    It is also available through the world-wide-web at this URL:
*    http://opensource.org/licenses/afl-3.0.php
*    If you did not receive a copy of the license and are unable to
*    obtain it through the world-wide-web, please send an email
*    to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*    @author    PrestaShop SA <contact@prestashop.com>
*    @copyright 2007-2017 PrestaShop SA
*    @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class AdvancedOrderNotes extends Module
{
	public $require_email;
	public $product_button;
	public $fake_order;
	public $show_on_leave;
	public $under_shopping;
	public $_html;
	public $cart_navigation;



	public function __construct()
	{
		$this->name = 'advancedordernotes';
		$this->tab = 'front_office_features';
		$this->version = '1.3.07';
		$this->author = 'advancedplugins';
		$this->need_instance = 1;

		$this->module_key = 'd74ef1fb220d228552656e9eff0cf24e';
		$this->author_address = '0x6b0EA5e7A2019Ca82d288436edf45e1B9a3540b5';

		parent::__construct();
		$this->bootstrap = 1;

		$this->displayName = $this->l('Advanced Order Notes');
		$this->description = $this->l('Allows your employees to add notes about orders and clients.');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall the Advanced Order Notes?');
	
	}

	public function install()
	{
		
		if (!parent::install() ||  !$this->registerHook('Header') || !$this->registerHook('displayBackOfficeHeader') || !$this->registerHook('displayAdminOrder') || !$this->registerHook('displayAdminCustomers') || !$this->registerHook('displayAdminProductsExtra')  || !$this->installDb()) 
			return false;
			
		$this->createAdminTabs();
		return true;
	}


  public function hookDisplayAdminProductsExtra($params) 
  {	

  	$id_product = (int)Tools::getValue('id_product');
  	$product = new Product(	$id_product );

	    if (Validate::isLoadedObject($product ))
	    {
	 
	 			$content = '';
	 			$sql = 'SELECT * FROM  `'._DB_PREFIX_.'adv_ordernotes_product` WHERE id_product ='.(int)$id_product; 
				$r = Db::getInstance()->getRow($sql);	

				if($r)
					$context = $r['note'];

				$aon_token = Configuration::get('aon_token');

				$this->context->smarty->assign(array(
				    'product_note' => $context,
				    'aon_token' => $aon_token,
				    'aon_url' => Tools::getHttpHost(true).__PS_BASE_URI__,
				    'order_product_id' => $id_product
				));


	        return $this->display(__FILE__, '/views/templates/admin/product_tab.tpl');
	    }

    }





	private function installModuleTab($title, $class_sfx = '', $parent = '')
    {
        $class = 'Admin'.Tools::ucfirst($this->name).Tools::ucfirst($class_sfx);

 		Configuration::updateValue('aon_comments_show', 'order');
 		Configuration::updateValue('aon_note_status', 1);
 		Configuration::updateValue('aon_note_status_delete', 1);
 		Configuration::updateValue('aon_hook_position', 'normal');

        @copy(_PS_MODULE_DIR_.$this->name.'/logo.png', _PS_IMG_DIR_.'t/'.$class.'.png');
        
        if ($parent == '')
        {
            # validate module
            $position = Tab::getCurrentTabId();
        } 
        else 
        {
            # validate module
            $position = Tab::getIdFromClassName($parent);
        }

        $tab1 = new Tab();
        $tab1->class_name = $class;
        $tab1->module = $this->name;
        $tab1->id_parent = (int)$position;
        $langs = Language::getLanguages(false);

        foreach ($langs as $l) 
        {
            # validate module
            $tab1->name[$l['id_lang']] = $title;
        }

        $tab1->add(true, false);
    }


    private function uninstallModuleTab($class_sfx = '')
    {
        $tab_class = 'Admin'.Tools::ucfirst($this->name).Tools::ucfirst($class_sfx);

        $id_tab = Tab::getIdFromClassName($tab_class);
        if ($id_tab != 0) {
            $tab = new Tab($id_tab);
            $tab->delete();
            return true;
        }
        return false;
    }




	private function createAdminTabs()
	{	
		@copy(dirname(__FILE__).'/logo.png', _PS_ROOT_DIR_.'/img/t/AdminAdvancedOrderNotes.gif');
			


            
        $class = 'Admin'.Tools::ucfirst($this->name).'Management';
        $tab1 = new Tab();
        $tab1->class_name = $class;
        $tab1->module = $this->name;
        $tab1->id_parent = 0;
        $langs = Language::getLanguages(false);
        foreach ($langs as $l) 
        {
            $tab1->name[$l['id_lang']] = $this->l('Order Notes');
        }
        
        $tab1->add(true, false);

        $this->installModuleTab('Dashboard', 'Dashboard', 'Admin'.Tools::ucfirst($this->name).'Management');




	    $comments_show = Configuration::get('aon_token');

		if(!$comments_show)
			Configuration::updateValue('aon_token', md5(_PS_BASE_URL_.rand(1,100000)));





	}

	public function uninstall()
	{
		$this->uninstallTab();
		parent::uninstall();

		return true;
	}





	private function uninstallTab()
	{
		$tab_id = Tab::getIdFromClassName('AdvancedOrderNotes');
		if ($tab_id)
		{
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		
		$tab_id = Tab::getIdFromClassName('AdminAdvancedOrderNotes');
		if ($tab_id)
		{
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		

		$tab_id = Tab::getIdFromClassName('AdminAdvancedordernotesManagement');
		if ($tab_id)
		{
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		
		$tab_id = Tab::getIdFromClassName('AdminAdvancedOrderNotesDashboard');
		if ($tab_id)
		{
			$tab = new Tab($tab_id);
			$tab->delete();
		}
		

		//$sql = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'adv_ordernotes`';
		//Db::getInstance()->execute($sql);


		$this->uninstallModuleTab( 'Dashboard' );

		@unlink(_PS_ROOT_DIR_.'/img/t/AdminAdvancedOrderNotes.gif');

		return true;
	}


	public function installDb()
	{

		 $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'adv_ordernotes` (
					`id` int(99) NOT NULL AUTO_INCREMENT,
					`id_order` int(99) NOT NULL,
					`id_employee` int(11) NOT NULL,
					`note` text NOT NULL,
					`note_status` varchar(255) NOT NULL,
					`date` datetime NOT NULL,
					`status` int(1) NOT NULL,
					PRIMARY KEY (`id`),
  					KEY `id` (`id`)
					) ENGINE= '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ';

		 Db::getInstance()->execute($sql);

		 $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'adv_ordernotes_product` (
					`id_product` int(99) NOT NULL,
					`note` TEXT NOT NULL,
					`date_upd` datetime NOT NULL,
					PRIMARY KEY (`id_product`),
  					KEY `id_product` (`id_product`)
					) ENGINE= '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ';

		 Db::getInstance()->execute($sql);


		 $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'adv_ordernotes_statuses` (
					`id` int(99) NOT NULL AUTO_INCREMENT,
					`name` varchar(255) NOT NULL,
					`background` varchar(255) NOT NULL,
					`color` varchar(255) NOT NULL,
					PRIMARY KEY (`id`),
  					KEY `id` (`id`)
					) ENGINE= '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ';


		Db::getInstance()->execute($sql);


		 $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'adv_ordernotes_stickies` (
					`id` int(99) NOT NULL AUTO_INCREMENT,
					`id_employee` int(10) NOT NULL,
					`id_owner` int(10) NOT NULL,
					`note` TEXT NOT NULL,
					`color` varchar(255) NOT NULL,
					`date_add` datetime NOT NULL,
					`date_expire` datetime NOT NULL,
					PRIMARY KEY (`id`),
					INDEX (date_add),
					INDEX (date_expire),
  					KEY `id` (`id`)
					) ENGINE= '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ';


		Db::getInstance()->execute($sql);







		$c = Db::getInstance()->getRow('SELECT count(*) FROM `'._DB_PREFIX_.'adv_ordernotes_statuses`');
		
		if($c['count(*)'] == 0)
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'adv_ordernotes_statuses` ( id, name, background, color ) VALUES ( NULL, "DONE", "#dff0d8", "#3c763d") , ( NULL, "INFO", "#31708f", "#d9edf7"),  ( NULL, "CANCELED", "#a94442", "#f2dede")');


		$c = Db::getInstance()->executeS('SHOW FIELDS FROM  `'._DB_PREFIX_.'adv_ordernotes`');

		$need_collumn = 1;
		$need_collumn2 = 1;

		foreach($c as $q)
		{

			if($q['Field'] == 'note_status')
				$need_collumn = 0;

			if($q['Field'] == 'id_employee')
				$need_collumn2 = 0;

		}

		if($need_collumn == 1)
	   		Db::getInstance()->execute(	'ALTER TABLE `'._DB_PREFIX_.'adv_ordernotes` ADD COLUMN note_status varchar(255) NOT NULL');


		if($need_collumn2 == 1)
	   		Db::getInstance()->execute(	'ALTER TABLE `'._DB_PREFIX_.'adv_ordernotes` ADD COLUMN id_employee int(11) NOT NULL');


		return true;
	}


	public function hookdisplayAdminCustomers()
	{

		$id_customer = (int) Tools::getValue('id_customer');
		$comments_show = Configuration::get('aon_comments_show');
		$note_status = Configuration::get('aon_note_status');
		$note_status_delete =  Configuration::get('aon_note_status_delete'); 
		

		$customer = new Customer($id_customer);
	


		$sql = 'SELECT * FROM `'._DB_PREFIX_.'customer` pc INNER JOIN `'._DB_PREFIX_.'orders` po ON pc.id_customer = po.id_customer INNER JOIN `'._DB_PREFIX_.'adv_ordernotes` pon ON pon.id_order = po.id_order  WHERE pc.email = "'.pSQL($customer->email).'"';
		$notes = Db::getInstance()->executeS($sql);


		$size = 0;
		$count_size = 0;

		if( isset($notes[0]))
			$size = 1;

		$count_size = count($notes);
		
		$context = Context::getContext();
		$note_filter = array();
		$id_employee = $context->cookie->id_employee;


		$employees = Employee::getEmployees();
		

		$i = 1;
		


		if(!empty($notes))
		{
			foreach($notes as $note)
			{

				$employee_name = '';

				foreach ($employees as $emp)
				{

					if(isset($emp['id_employee']) && isset($note['id_employee']))
					{
						if($emp['id_employee'] == $note['id_employee'] )
							$employee_name = $emp['firstname'].' '.$emp['lastname'];					
					}


				}

				$os =  Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes_statuses` WHERE name= "'.pSQL($note['note_status']).'" ');

				$note_filter[$i]['note_color'] ='';
				$note_filter[$i]['note_background'] = '';

				if($os['id'])
				{
					$note_filter[$i]['note_color'] = $os['color'];
					$note_filter[$i]['note_background'] = $os['background'];
				}
			
				$note_filter[$i]['nr'] = $i;
				$note_filter[$i]['note'] = $note['note'];
				$note_filter[$i]['id'] = $note['id'];
				$note_filter[$i]['note_status'] = $note['note_status'];

				$note_filter[$i]['employee'] = $employee_name;
				$note_filter[$i]['date'] = $note['date'];
				$i++;
			}			
		}


		$cookie = new Cookie('psAdmin');
		$is_admin = false;

		if ($cookie->id_employee)
		{
			$employee = new Employee($cookie->id_employee);
			
			if($employee->id_profile == _PS_ADMIN_PROFILE_)
		    	$is_admin = true;
		}


		$this->context->smarty->assign(array(
		
		    'id_customer' => $id_customer,
		    'notes' => $note_filter,
		    'size' => $size,
		    'enable_admin' => Configuration::get('aon_enable_admin'),
		    'aon_url' => Tools::getHttpHost(true).__PS_BASE_URI__,
		    'aon_is_admin' => $is_admin,
		    'count_size' => $count_size,
		    'note_status' => $note_status,
		    'note_status_delete' => $note_status_delete,
		    'note_statuses' => self::getAllNoteStatuses(),
		    'id_employee' => $id_employee,
		    'aon_token' => Configuration::get('aon_token')
		));


		return $this->display(__FILE__, '/views/templates/hook/detail_tab_customers.tpl');

	}

	public function hookdisplayAdminOrder()
	{

		$id_order = (int) Tools::getValue('id_order');
		$comments_show = Configuration::get('aon_comments_show');
		$note_status = Configuration::get('aon_note_status');
		$note_status_delete =  Configuration::get('aon_note_status_delete');




		if ($comments_show == 'phone'):

			$order_info = new Order($id_order);
			$id_address_invoice = $order_info->id_address_delivery;
			$address_invoice = new Address($id_address_invoice);
			$ai_phone = $address_invoice->phone;
			$ai_phone_mobile = $address_invoice->phone_mobile;



			if( !empty($ai_phone) && !empty($ai_phone_mobile) )
			{
				$sql = 'SELECT * FROM `'._DB_PREFIX_.'address` WHERE phone LIKE "%'.pSQL($ai_phone).'%" OR phone_mobile LIKE "%'.pSQL($ai_phone_mobile).'%"';
			}
			elseif(!empty($ai_phone) && empty($ai_phone_mobile) )
			{
				$sql = 'SELECT * FROM `'._DB_PREFIX_.'address` WHERE phone LIKE "%'.pSQL($ai_phone).'%"';
			}
			elseif(empty($ai_phone) && !empty($ai_phone_mobile) )
			{
				$sql = 'SELECT * FROM `'._DB_PREFIX_.'address` WHERE  phone_mobile LIKE "%'.pSQL($ai_phone_mobile).'%"';
			}

			$get_all_address = Db::getInstance()->executeS($sql);
			$ids = array();
			foreach($get_all_address as $gad){
				$sql = 'SELECT * FROM `'._DB_PREFIX_.'orders` WHERE id_address_delivery = '.(int)$gad['id_address'].' OR id_address_invoice ='.(int)$gad['id_address'];
				$s =  Db::getInstance()->ExecuteS($sql);
				
				foreach($s as $x)
					$ids[] = $x['id_order'];

			}

			$ids = implode(',', $ids);
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes` WHERE id_order IN ('.pSQL($ids).')';
			$notes = Db::getInstance()->executeS($sql);
		elseif($comments_show == 'order'):
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes` WHERE id_order = '.(int)$id_order;
			$notes = Db::getInstance()->executeS($sql);
		elseif($comments_show == 'email'):
			$order_info = new Order($id_order);
			$customer = new Customer($order_info->id_customer);
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'customer` pc INNER JOIN `'._DB_PREFIX_.'orders` po ON pc.id_customer = po.id_customer INNER JOIN `'._DB_PREFIX_.'adv_ordernotes` pon ON pon.id_order = po.id_order  WHERE pc.email = "'.pSQL($customer->email).'"';
			$notes = Db::getInstance()->executeS($sql);
		endif;


		$size = 0;
		$count_size = 0;

		if( isset($notes[0]))
			$size = 1;

		$count_size = count($notes);
		
		$context = Context::getContext();
		$note_filter = array();
		$id_employee = $context->cookie->id_employee;


		$employees = Employee::getEmployees();
		

		$i = 1;
		


		if(!empty($notes))
		{
			foreach($notes as $note)
			{

				$employee_name = '';

				foreach ($employees as $emp)
				{

					if(isset($emp['id_employee']) && isset($note['id_employee']))
					{
						if($emp['id_employee'] == $note['id_employee'] )
							$employee_name = $emp['firstname'].' '.$emp['lastname'];					
					}


				}

				$os =  Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes_statuses` WHERE name= "'.pSQL($note['note_status']).'" ');

				$note_filter[$i]['note_color'] ='';
				$note_filter[$i]['note_background'] = '';

				if($os['id'])
				{
					$note_filter[$i]['note_color'] = $os['color'];
					$note_filter[$i]['note_background'] = $os['background'];
				}
			
				$note_filter[$i]['nr'] = $i;
				$note_filter[$i]['note'] = $note['note'];
				$note_filter[$i]['id'] = $note['id'];
				$note_filter[$i]['note_status'] = $note['note_status'];

				$note_filter[$i]['employee'] = $employee_name;
				$note_filter[$i]['date'] = $note['date'];
				$i++;
			}			
		}

		$cookie = new Cookie('psAdmin');
		$is_admin = false;

		if ($cookie->id_employee)
		{
			$employee = new Employee($cookie->id_employee);
			
			if($employee->id_profile == _PS_ADMIN_PROFILE_)
		    	$is_admin = true;
		}

		$aon_statuses =  Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes_statuses` '); 

		

		$this->context->smarty->assign(array(
		
		    'id_order' => $id_order,
		    'notes' => $note_filter,
		    'aon_statuses' => $aon_statuses,
		    'size' => $size,
		    'aon_url' => Tools::getHttpHost(true).__PS_BASE_URI__,
		    'count_size' => $count_size,
		    'note_status' => $note_status,
		    'aon_is_admin' =>  $is_admin,
		    'note_status_delete' => $note_status_delete,
		    'enable_admin' => Configuration::get('aon_enable_admin'),
		    'note_statuses' => self::getAllNoteStatuses(),
		    'id_employee' => $id_employee,
		    'aon_token' => Configuration::get('aon_token')
		));


		return $this->display(__FILE__, '/views/templates/hook/detail_tab.tpl');
	}


	public function hookdisplayBackOfficeHeader($params)
	{

		//  echo "<pre>";print_r($_GET);echo "</pre>";
		//  echo "<pre>";print_r($params);echo "</pre>";
		$token = Configuration::get('aon_token');
		$hook_position = Configuration::get('aon_hook_position');

		$this->context->smarty->assign(array(
			    '_path' => $this->_path,
			    'aon_hook_position' => $hook_position,
			    'aon_url' => Tools::getHttpHost(true).__PS_BASE_URI__,
			    'aon_token' => $token
		));
		$html  = '';

		if(   
			Tools::getValue('controller') == 'Adminclientordernotes'  ||  
			Tools::getValue('controller') == 'AdminDmuListeCommandes'  ||  
			Tools::getValue('controller')  == 'AdminOrders' ||  
			Tools::getValue('controller')  == 'AdminCustomers' ||  
			Tools::getValue('tab')  == 'AdminOrders' ||  
			Tools::getValue('controller') == 'AdminOrdermanager'  ||  
			Tools::getValue('tab')  == 'AdminCustomers' || 
			(substr_count($_SERVER['REQUEST_URI'], 'sell/orders/') > 0 && substr_count($_SERVER['REQUEST_URI'], '/view') > 0 ) || 
			(substr_count($_SERVER['REQUEST_URI'], 'sell/customers/') > 0 && substr_count($_SERVER['REQUEST_URI'], '/view') > 0 )

		)
		{
			if(Tools::getIsset('id_order') || Tools::getIsset('id_customer') || 	(substr_count($_SERVER['REQUEST_URI'], 'sell/orders/') > 0 && substr_count($_SERVER['REQUEST_URI'], '/view') > 0 ) || (substr_count($_SERVER['REQUEST_URI'], 'sell/customers/') > 0 && substr_count($_SERVER['REQUEST_URI'], '/view') > 0 ))
				$html .=  $this->display(__FILE__, '/views/templates/admin/defines.tpl');
			else
				$html .=  $this->display(__FILE__, '/views/templates/admin/defines_backoffice.tpl');

		}
		
		$note_stickys =  Configuration::get('aon_note_stickys'); 

		if($note_stickys  == 1)
		{
			$current_employee_id = $this->context->cookie->id_employee;
			$employees = Db::getInstance()->executeS(' SELECT `id_employee`, `firstname`, `lastname`  FROM `'._DB_PREFIX_.'employee`  WHERE `active` = 1  ORDER BY `lastname` ASC  ');
			$sticky_notes2 = Db::getInstance()->executeS(' SELECT * FROM  `'._DB_PREFIX_.'adv_ordernotes_stickies`  WHERE `date_expire` >= "'.date('Y-m-d H:i:s').'"  AND ( id_employee = 0 OR id_employee = '.(int)$current_employee_id.') ORDER BY `date_add` DESC  ');


			$employees = Employee::getEmployees();
		

			foreach($sticky_notes2  as $k => $v)
			{
				$employee_name = '';
				foreach ($employees as $emp){

					if($emp['id_employee'] == $v['id_owner'] )
						$employee_name = $emp['firstname'].' '.Tools::substr($emp['lastname'],0, 1).'.';

	
				}

				$sticky_notes2[$k]['employee_name'] = $employee_name;
			}

			$is_15 = 0;
		    if (version_compare(_PS_VERSION_, '1.6', '<')) 
	        {
	            $is_15 = 1;
	        }
	        	

			$this->context->smarty->assign(array(
				    'aon_employees' => 	$employees,
				    'aon_hook_position' => $hook_position,
			    	'aon_url' => Tools::getHttpHost(true).__PS_BASE_URI__,
				    'is_15' => $is_15,
				    'aon_sticky_notes' => $sticky_notes2

			));

			$html .=  $this->display(__FILE__, '/views/templates/admin/sticky.tpl');
		}

		return $html;
	}

	public function get_customer_last_order($id_customer)
	{

		$sql = 'SELECT * FROM `'._DB_PREFIX_.'orders` WHERE id_customer = '.(int)$id_customer;
		$c = Db::getInstance()->getRow($sql);	

		if($c)
			return $c['id_order'];

		return false;
	}

	public function ajax_add_order_note_customer($id_customer, $note, $date, $id_employee, $note_status = '')
	{


		$id_order = (int)$this->get_customer_last_order($id_customer);


		$sql = 'SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes` WHERE id_order = '.(int)$id_order;
		$notes = Db::getInstance()->executeS($sql);

		$sql = 'INSERT INTO `'._DB_PREFIX_.'adv_ordernotes` (id, id_order, id_employee, note, date, status, note_status ) VALUES (NULL, '.(int)$id_order .', '.(int)$id_employee.', "'.pSQL($note).'", "'.pSQL($date).'", 1, "'.pSQL($note_status).'")';
		Db::getInstance()->execute($sql);
		
		$employees = Employee::getEmployees();
		$employee_name = '';


		$sql2 = 'SELECT id FROM  `'._DB_PREFIX_.'adv_ordernotes`  ORDER by id DESC';
		$r = Db::getInstance()->getRow($sql2);

		$note_status_delete =  (int)Configuration::get('aon_note_status_delete'); 


		foreach ($employees as $emp){

			if($emp['id_employee'] == $id_employee )
				$employee_name = $emp['firstname'].' '.$emp['lastname'];

		}

		$os =  Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes_statuses` WHERE name= "'.pSQL($note_status).'" ');


		$context = Context::getContext();

		if($os['id'])
		{
			$context->smarty->assign(array(
				    'note_color' => $os['color'],
				    'note_status' => $note_status,
				    'note_background' =>  $os['background']
				));

		}
		else
		{
			$context->smarty->assign(array(
					'note_status' => '',
				    'note_color' => '',
				    'note_background' =>  ''
				));
		}




		$context->smarty->assign(array(
			    'notes_count' => (count($notes)+1),
			    'note' => $note,
			    'note_id' => $r['id'],
			    'note_status_delete'  => $note_status_delete,
			    'employee_name' => $employee_name,
			    'date' => $date
			));


		return $this->display(__FILE__, '/views/templates/admin/note_row.tpl');


	}

	public function ajax_add_order_note($id_order, $note, $date, $id_employee, $note_status = '')
	{


		$sql = 'SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes` WHERE id_order = '.(int)$id_order;
		$notes = Db::getInstance()->executeS($sql);

		$sql = 'INSERT INTO `'._DB_PREFIX_.'adv_ordernotes` (id, id_order, id_employee, note, date, status, note_status ) VALUES (NULL, '.(int)$id_order .', '.(int)$id_employee.', "'.pSQL($note).'", "'.pSQL($date).'", 1, "'.pSQL($note_status).'")';
		Db::getInstance()->execute($sql);
		
		$employees = Employee::getEmployees();
		$employee_name = '';


		$sql2 = 'SELECT id FROM  `'._DB_PREFIX_.'adv_ordernotes`  ORDER by id DESC';
		$r = Db::getInstance()->getRow($sql2);

		$note_status_delete =  (int)Configuration::get('aon_note_status_delete'); 


		foreach ($employees as $emp){

			if($emp['id_employee'] == $id_employee )
				$employee_name = $emp['firstname'].' '.$emp['lastname'];

		}

		$os =  Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes_statuses` WHERE name= "'.pSQL($note_status).'" ');


		$context = Context::getContext();

		if($os['id'])
		{
			$context->smarty->assign(array(
				    'note_color' => $os['color'],
				    'note_status' => $note_status,
				    'note_background' =>  $os['background']
				));

		}
		else
		{
			$context->smarty->assign(array(
					'note_status' => '',
				    'note_color' => '',
				    'note_background' =>  ''
				));
		}




		$context->smarty->assign(array(
			    'notes_count' => (count($notes)+1),
			    'note' => $note,
			    'note_id' => $r['id'],
			    'note_status_delete'  => $note_status_delete,
			    'employee_name' => $employee_name,
			    'date' => $date
			));


		return $this->display(__FILE__, '/views/templates/admin/note_row.tpl');


	}


	public function displayForm()
	{

	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	    	
	       if (version_compare(_PS_VERSION_, '1.6', '>=')) 
	        {
	            $radio_switch = 'switch';
	        } else 
	        {
	            $radio_switch = 'radio';
	        }


	    $fields_form = array();
	    $fields_form[0]['form'] = array(

	        'legend' => array(
	            'title' => $this->l('Settings'),
	        ),

	        'input' => array(


            	array(
					  'type' => 'select',
					  'label' => $this->l('Group notes by'),
					  'name' => 'product_button',
					  'required' => false,
					  'desc' => $this->l('Phone = Show same ( universal notes ) at every order with that phone number |||  Order = Show only notes related to that order;'),
					  'lang' => false,
					  'options' => array(
						        'query' => array(
									array('key' => 'phone', 'name' => 'Phone'),
									array('key' => 'email', 'name' => 'Email'),
									array('key' => 'order', 'name' => 'Order')
						        ),
						        'id' => 'key',
						        'name' => 'name',
						    )
					),

            	array(
					  'type' => 'select',
					  'label' => $this->l('Form position in the Admin Order Page'),
					  'name' => 'hook_position',
					  'required' => false,
					  'desc' => $this->l('Top - the form will be displayed on top of the admin order page | Normal - The form will be displayed normally near the end of the admin order page '),
					  'lang' => false,
					  'options' => array(
						        'query' => array(
									array('key' => 'top', 'name' => 'Top'),
									array('key' => 'normal', 'name' => 'Normal')
						        ),
						        'id' => 'key',
						        'name' => 'name',
						    )
					),


   						 array(
	                        'type' => $radio_switch,
	                        'class' => 't', 
	                        'is_bool' => true, 
	                        'lang' => false,
	                        'label' => $this->l('Note status'),
	                        'name' => 'note_status',
	                        'desc' => $this->l('Enable the option to select a status when adding a order note.'),
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
	                        'type' => $radio_switch,
	                        'class' => 't', 
	                        'is_bool' => true, 
	                        'lang' => false,
	                        'label' => $this->l('Note delete option'),
	                        'name' => 'note_status_delete',
	                        'desc' => $this->l('Enable the option to delete a note  once it has been posted.'),
	                        'values' => array(
	                            array(
	                                'id' => 'active_on',
	                                'value' => 1,
	                                'label' => $this->l('ON')
	                            ),
	                            array(
	                                'id' => 'active_off',
	                                'value' => 0,
	                                'label' => $this->l('OFF')
	                            )
	                        ),
	                    ),
			          

   						 array(
	                        'type' => $radio_switch,
	                        'class' => 't', 
	                        'is_bool' => true, 
	                        'lang' => false,
	                        'label' => $this->l('Enable sticky notes'),
	                        'name' => 'note_stickys',
	                        'desc' => $this->l('Enable sticky notes in the backoffice.'),
	                        'values' => array(
	                            array(
	                                'id' => 'active_on',
	                                'value' => 1,
	                                'label' => $this->l('YES')
	                            ),
	                            array(
	                                'id' => 'active_off',
	                                'value' => 0,
	                                'label' => $this->l('NO')
	                            )
	                        ),
	                    ),

   						 array(
	                        'type' => $radio_switch,
	                        'class' => 't', 
	                        'is_bool' => true, 
	                        'lang' => false,
	                        'label' => $this->l('Administrator editor'),
	                        'name' => 'enable_admin',
	                        'desc' => $this->l('Enable administrator to edit the notes'),
	                        'values' => array(
	                            array(
	                                'id' => 'active_on',
	                                'value' => 1,
	                                'label' => $this->l('YES')
	                            ),
	                            array(
	                                'id' => 'active_off',
	                                'value' => 0,
	                                'label' => $this->l('NO')
	                            )
	                        ),
	                    ),
			  		 array(
					            'type'  => 'textarea',
					            'name'  => 'note_statuses',
					            'label' => $this->l('Note statuses'),
					            'desc' => $this->l('One note status per line, the characteristics are NAME,TEXT_COLOR,BG_COLOR'),
					    ),


	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'btn btn-default pull-right'
	        )
	    );
	     



	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar

	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;
	    $helper->toolbar_scroll = true;
	    $helper->submit_action = 'submit'.$this->name;

	    $helper->toolbar_btn = array(
	        'save' =>
	        array(
	            'desc' => $this->l('Save'),
	            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	            '&token='.Tools::getAdminTokenLite('AdminModules'),
	        ),
	        'back' => array(
	            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	            'desc' => $this->l('Back to list')
	        )
	    );
	     
	    // Load current value
	    $helper->fields_value['product_button'] = Configuration::get('aon_comments_show');
	    $helper->fields_value['note_status'] = Configuration::get('aon_note_status');
	    $helper->fields_value['note_status_delete'] = Configuration::get('aon_note_status_delete');
	    $helper->fields_value['note_stickys'] = Configuration::get('aon_note_stickys'); 
	    $helper->fields_value['hook_position'] = Configuration::get('aon_hook_position'); 
	 	$helper->fields_value['enable_admin'] = Configuration::get('aon_enable_admin');    



	    $statuses = self::getAllNoteStatuses(true);
	    $textarea = '';
	    foreach($statuses as $st)
	    {
	    	$textarea .= $st['name'].','.$st['color'].','.$st['background'].PHP_EOL;
	    }

	    $helper->fields_value['note_statuses'] = rtrim($textarea);

	    return $helper->generateForm($fields_form);
	}


	public static function getAllNoteStatuses($no_id = false)
	{
		if($no_id == true)
			return Db::getInstance()->executeS('SELECT name,background,color FROM  `'._DB_PREFIX_.'adv_ordernotes_statuses`');
		else
			return Db::getInstance()->executeS('SELECT * FROM  `'._DB_PREFIX_.'adv_ordernotes_statuses`');	
	}

	public function getContent()
	{

	    $output = null;
	    $comments_show = Configuration::get('aon_token');


		if(!$comments_show)
			Configuration::updateValue('aon_token', md5(_PS_BASE_URL_.rand(1,100000)));


	 
	    if (Tools::isSubmit('submit'.$this->name))
	    {
			Configuration::updateValue('aon_comments_show', Tools::getValue('product_button'));
			Configuration::updateValue('aon_note_status', Tools::getValue('note_status'));
			Configuration::updateValue('aon_note_status_delete', Tools::getValue('note_status_delete'));		
			Configuration::updateValue('aon_note_stickys', Tools::getValue('note_stickys'));
			Configuration::updateValue('aon_hook_position', Tools::getValue('hook_position'));	
			Configuration::updateValue('aon_enable_admin', Tools::getValue('enable_admin'));
			

			$note_statuses = Tools::getValue('note_statuses');
	
			$expl = array_filter(explode(PHP_EOL, $note_statuses));
			Db::getInstance()->execute('TRUNCATE   `'._DB_PREFIX_.'adv_ordernotes_statuses`');

			foreach($expl as $e)
			{
				$n = array_filter(explode(',', $e));
				Db::getInstance()->execute('INSERT INTO   `'._DB_PREFIX_.'adv_ordernotes_statuses` ( id, name, background, color ) VALUES ( NULL, "'.rtrim(ltrim($n[0])).'","'.rtrim(ltrim($n[1])).'","'.rtrim(ltrim($n[2])).'")');
		
			}

			Configuration::updateValue('aon_note_statuses', Tools::getValue('note_statuses'));	

	        $output .= $this->displayConfirmation($this->l('Settings updated'));
	    }

	    $output .= $this->display(__FILE__, '/views/templates/admin/rate.tpl');
	    return $output.$this->displayForm().$this->display(__FILE__, '/views/templates/admin/iframe.tpl');;

	}


}