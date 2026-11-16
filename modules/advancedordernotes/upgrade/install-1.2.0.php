<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_0($object)
{

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
