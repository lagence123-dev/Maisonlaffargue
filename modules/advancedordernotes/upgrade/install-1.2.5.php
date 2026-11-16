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

function upgrade_module_1_2_5($object)
{

	 $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'adv_ordernotes_product` (
					`id_product` int(99) NOT NULL,
					`note` TEXT NOT NULL,
					`date_upd` datetime NOT NULL,
					PRIMARY KEY (`id_product`),
  					KEY `id_product` (`id_product`)
					) ENGINE= '._MYSQL_ENGINE_.' CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ';

		 Db::getInstance()->execute($sql);

		return true;

}