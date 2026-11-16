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

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'bonmaintenance` (
    `id_tab` int(11) NOT NULL AUTO_INCREMENT,
	`id_shop` int(11) NOT NULL DEFAULT \'1\',
	`data_start` datetime NOT NULL,
	`data_end` datetime NOT NULL,
	`colorhex` VARCHAR(100) NOT NULL,
	`colorhex2` VARCHAR(100) NOT NULL,
	`colorhex3` VARCHAR(100) NOT NULL,
	`select_type` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id_tab`, `id_shop`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'bonmaintenance_lang` (
	`id_tab` int(10) unsigned NOT NULL,
	`id_lang` int(11) NOT NULL,
	`image` VARCHAR(100) NOT NULL,
	`title_text` text NOT NULL,
	`description` text NOT NULL,
	PRIMARY KEY (`id_tab`, `id_lang`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
