<?php
/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @version   Release: $Revision: 14011 $
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
header("Access-Control-Allow-Origin: *");
require(dirname(__FILE__).'/../../../config/config.inc.php');
require(dirname(__FILE__).'/../../../init.php');
require(dirname(__FILE__).'/../advancedordernotes.php');


$adv_path = str_replace('advancedordernotes/controllers', 'advancedordernotes/', dirname(__FILE__));
define("advanced_on_path", $adv_path);

class AjaxController
{

	private $function_pre = 'ajax_';

	public function __construct()
	{
		if (Tools::getIsset('action'))
		{

			if( Tools::getValue('token') != Configuration::get('aon_token'))
				die('bad token');


			$function_name = $this->function_pre.Tools::getValue('action');
			if (method_exists('AjaxController', $function_name))
				$this->$function_name();
		}
	}


	public function only_employees()
	{
		$cookie = new Cookie('psAdmin');

		if (!$cookie->id_employee)
		{
			die();
		}

	}


	public function ajax_update_status()
	{
		$this->only_employees();
		$status =  Tools::getValue('status'); 


		$sql = 'UPDATE  `'._DB_PREFIX_.'adv_ordernotes` SET note_status = "'.pSQL($status).'"  WHERE id ='.(int)Tools::getValue('id_note');
		$r = Db::getInstance()->execute($sql);

	}



	public function ajax_update_textarea()
	{
		$this->only_employees();
		$note =  Tools::getValue('note'); 


		$sql = 'UPDATE  `'._DB_PREFIX_.'adv_ordernotes` SET note = "'.pSQL($note).'"  WHERE id ='.(int)Tools::getValue('id_note');
		$r = Db::getInstance()->execute($sql);

	}


	public function ajax_add_sticky_note()
	{
		$this->only_employees();
		$note_stickys =  Configuration::get('aon_note_stickys'); 

		if($note_stickys  != 1)
			return false;

		$id_employee = Tools::getValue('id_employee');
		$note = Tools::getValue('note');
		$date_expire = Tools::getValue('date_expire');
		$cookie = new Cookie('psAdmin');

		$sql = 'INSERT INTO  `'._DB_PREFIX_.'adv_ordernotes_stickies`  (id, id_employee, id_owner, note, color, date_add, date_expire )  VALUES ( NULL, '.(int)$id_employee.','.(int)$cookie->id_employee.', "'.pSQL($note).'", "", "'.date('Y-m-d H:i:s').'", "'.date('Y-m-d H:i:s', strtotime('+'.pSQL($date_expire).' days')).'")'; 
		Db::getInstance()->execute($sql);

	}

	public function ajax_update_product_note()
	{
		$this->only_employees();

		$id_product = Tools::getValue('id_product');
		$note = Tools::getValue('note');

		$r = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes_product`  WHERE id_product='.(int)$id_product);

		if(!$r)
		{
			$sql = 'INSERT INTO  `'._DB_PREFIX_.'adv_ordernotes_product` ( id_product, note, date_upd) VALUES ( '.(int)$id_product.', "'.pSQL($note).'", "'.date('Y-m-d H:i:s').'")'; 
			Db::getInstance()->execute($sql);			
		}
		else
		{
			$sql = 'UPDATE `'._DB_PREFIX_.'adv_ordernotes_product` SET note = "'.pSQL($note).'", date_upd = "'.date('Y-m-d H:i:s').'" WHERE id_product = '.(int)$id_product; 
			Db::getInstance()->execute($sql);		
		}


	}

	public function ajax_add_order_note()
	{
		
		// CHECK IF IS ADMIN 
		$this->only_employees();

		$note = pSQL(Tools::getValue('note'));
		$id_order = (int)(Tools::getValue('hidden_id_order'));
		$date = date('Y-m-d H:i:s');
		$id_employee = (int)(Tools::getValue('hidden_id_employee'));
		$note_status = Tools::getValue('note_status');

		$ajaxorder = new AdvancedOrderNotes();
		echo $ajaxorder->ajax_add_order_note($id_order, $note, $date, $id_employee, $note_status );
		die();


	}

	public function ajax_add_order_note_customer()
	{
		
		// CHECK IF IS ADMIN 
		$this->only_employees();

		$note = pSQL(Tools::getValue('note'));
		$id_customer = (int)(Tools::getValue('hidden_id_customer'));
		$date = date('Y-m-d H:i:s');
		$id_employee = (int)(Tools::getValue('hidden_id_employee'));
		$note_status = Tools::getValue('note_status');

		$ajaxorder = new AdvancedOrderNotes();
		echo $ajaxorder->ajax_add_order_note_customer($id_customer, $note, $date, $id_employee, $note_status );
		die();


	}


	public function ajax_remove_order_note()
	{
		
		// CHECK IF IS ADMIN 
		$this->only_employees();
		$note_status_delete =  (int)Configuration::get('aon_note_status_delete'); 

		if($note_status_delete != 1)
			return false;


		$id_note = (int)(Tools::getValue('id_note'));
		
		$sql = 'DELETE FROM `'._DB_PREFIX_.'adv_ordernotes`  WHERE  id = '.(int)$id_note; 
		Db::getInstance()->execute($sql);

		die();


	}


	public function ajax_delete_sticky_note()
	{
		$this->only_employees();

		$id_note = (int)Tools::getValue('id_note');
		$sql = 'DELETE FROM   `'._DB_PREFIX_.'adv_ordernotes_stickies` WHERE id = '.(int)$id_note;
		Db::getInstance()->execute($sql);

		die();
	}

	public function ajax_get_order_act_info()
	{

		$this->only_employees();
		$group_by = Configuration::get('aon_comments_show');

		$id_order = (int)Tools::getValue('order_id');

		

		$context = Context::getContext();

		if($group_by == 'email')
		{
			$order = new Order($id_order);
			$customer = new Customer($order->id_customer);
			$customer_email = $customer->email;

			$sql2 = 'SELECT o.id_order FROM  `'._DB_PREFIX_.'customer` c INNER JOIN   `'._DB_PREFIX_.'orders` o on o.id_customer = c.id_customer WHERE c.email =  "'.pSQL($customer_email).'" ';
			$results = Db::getInstance()->executeS($sql2);
			$order_ids = array();
			foreach($results as $k => $v)
				$order_ids[] = $v['id_order'];

			$sql = 'SELECT * FROM  `'._DB_PREFIX_.'adv_ordernotes` WHERE id_order IN ('.pSQL(implode(',', $order_ids)).') ORDER BY date ASC';
			$r = Db::getInstance()->executeS($sql);

		}
		else
		{
			$sql = 'SELECT * FROM  `'._DB_PREFIX_.'adv_ordernotes` WHERE id_order = '.(int)$id_order. ' ORDER BY date ASC';
			$r = Db::getInstance()->executeS($sql);
		}



		$to_send = array();
		$i = 0;


		foreach($r as $res)
		{

			$employee = new Employee($res['id_employee']);

			$os =  Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'adv_ordernotes_statuses` WHERE name= "'.pSQL($res['note_status']).'" ');


			if($os['id'])
			{
				$to_send[$i]['note_color'] = $os['color'];
				$to_send[$i]['note_background'] = $os['background'];
				$to_send[$i]['note_status'] = $res['note_status'];
			}
			else
			{
				$to_send[$i]['note_color'] ='';
				$to_send[$i]['note_background'] = '';
				$to_send[$i]['note_status'] = '';
			}




			$to_send[$i]['employee_name'] = $employee->lastname;
			$to_send[$i]['message'] = $res['note'];
			$to_send[$i]['date_add'] = $res['date'];
			$i++;

		}


		$context->smarty->assign(array(
				'aon_notes' => $to_send

		));


		$c = new AdvancedOrderNotes;
		$tpl =  $c->display(advanced_on_path, 'views/templates/hook/order_info_packs.tpl');
			
		die($tpl);
	




	}

	public function ajax_check_if_order_has_note()
	{

	
		$this->only_employees();
		$order_ids = Tools::getValue('order_ids');
		$answer = array();
		$group_by = Configuration::get('aon_comments_show');
		
		if($order_ids)
		{
			if(sizeof($order_ids) >= 1)
			{
				foreach($order_ids as $id_order)
				{

					if($group_by == 'email')
					{
						$order = new Order($id_order);
						$customer = new Customer($order->id_customer);
						$customer_email = $customer->email;

						$sql2 = 'SELECT o.id_order FROM  `'._DB_PREFIX_.'customer` c INNER JOIN   `'._DB_PREFIX_.'orders` o on o.id_customer = c.id_customer WHERE c.email =  "'.pSQL($customer_email).'" ';
						$results = Db::getInstance()->executeS($sql2);

						$order_ids2 = array();
						foreach($results as $result)
						{
							$order_ids2[] = $result['id_order'];
						}

				
						$sql = 'SELECT count(id) FROM  `'._DB_PREFIX_.'adv_ordernotes` WHERE  id_order IN ('.pSQL(implode(',', $order_ids2)).')';
						$r = Db::getInstance()->getRow($sql);	


						foreach($order_ids2 as $o)
						{
							if($r['count(id)'] > 0)
								$answer[$o] = $r['count(id)'];

						}


					}
					elseif($group_by == 'phone')
					{
						$order = new Order($id_order);
						$customer = new Customer($order->id_customer);
						$customer_email = $customer->email;

						$sql2 = 'SELECT o.id_order FROM  `'._DB_PREFIX_.'customer` c INNER JOIN   `'._DB_PREFIX_.'orders` o on o.id_customer = c.id_customer WHERE c.email =  "'.pSQL($customer_email).'" ';
						$results = Db::getInstance()->executeS($sql2);

						$order_ids2 = array();
						foreach($results as $result)
						{
							$order_ids2[] = $result['id_order'];
						}

				
						$sql = 'SELECT count(id) FROM  `'._DB_PREFIX_.'adv_ordernotes` WHERE  id_order IN ('.implode(',', $order_ids2).')';
						$r = Db::getInstance()->getRow($sql);	


						foreach($order_ids2 as $o)
						{
							if($r['count(id)'] > 0)
								$answer[$o] = $r['count(id)'];

						}




					}
					else
					{
						$sql = 'SELECT count(id) FROM  `'._DB_PREFIX_.'adv_ordernotes` WHERE id_order = '.(int)$id_order;
						$r = Db::getInstance()->getRow($sql);				
					}




					if($r['count(id)'] > 0)
						$answer[$id_order] = $r['count(id)'];

				}
			}
		}

		

		die(

			Tools::jsonEncode(
					$answer
				)	

			);


	}

	public function ajax_search_orders()
	{

		// CHECK IF IS ADMIN 
		
		$this->only_employees();

		$id_order = Tools::getValue('id_order');
		$customer_name = Tools::getValue('customer_name');
		$email = Tools::getValue('email');
		$phone = Tools::getValue('phone');
		$note_part = Tools::getValue('note_part');


		$sql = 'SELECT * FROM  `'._DB_PREFIX_.'address`   psa 
		INNER JOIN  `'._DB_PREFIX_.'orders` pso 
		ON 
		pso.id_address_delivery = psa.id_address
		INNER  JOIN  `'._DB_PREFIX_.'customer` psc
		ON 
		pso.id_customer = psc.id_customer  

		INNER  JOIN  `'._DB_PREFIX_.'adv_ordernotes` pon
		ON 
		pon.id_order = pso.id_order  


		WHERE 1=1 ';

		if(!empty($phone))
			$sql .= ' 
			AND
				( psa.phone LIKE "%'.pSQL($phone).'%" 
					OR 
				psa.phone_mobile LIKE "%'.pSQL($phone).'%" ) ';

		if(!empty($customer_name))
			$sql .= ' 
			AND
				( psc.lastname LIKE "%'.pSQL($customer_name).'%" 
					OR 
				psc.firstname LIKE "%'.pSQL($customer_name).'%" ) ';



		if(!empty($note_part))
			$sql .= ' 
			AND
				( pon.note LIKE "%'.pSQL($note_part).'%" ) ';


		if(!empty($email))
			$sql .= ' 
			AND
				( psc.email = "'.pSQL($email).'"  ) ';

		if(!empty($id_order))
			$sql .= ' 
			AND
				( pso.id_order = "'.pSQL($id_order).'"  ) ';

		
		$addresses = Db::getInstance()->executeS($sql);
		
		$notes = array();

		foreach($addresses as $n):

			$info = array();
			$order_info = new Order($n['id_order']);
			$customer_info = new Customer($order_info->id_customer);
			$address = new Address($order_info->id_address_delivery);
			$employee = new Employee($n['id_employee']);

			$info['id_order'] = $n['id_order'];
			$info['id_customer'] = $order_info->id_customer;
			$info['full_name'] = $customer_info->lastname . ' ' .$customer_info->firstname;
			$info['email'] = $customer_info->email;

			if(!empty($address->phone))
				$info['phone_number'] = $address->phone;
			else
				$info['phone_number'] = $address->phone_mobile;	
						
			$info['country'] = $address->country;

			if(!empty($address->address1))
				$info['address'] = $address->address1;
			else
				$info['address'] = $address->address2;	
				
			$info['note'] = $n['note'];
			$info['date'] = $n['date'];
			$info['city'] = $address->city;	
			$info['employee'] = Tools::substr($employee->firstname,0 , 1).'. '.$employee->lastname;

			$notes[] = $info;

		endforeach;


		die(
			Tools::jsonEncode($notes)
			);

	}

}