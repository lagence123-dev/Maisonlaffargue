<?php
/**
 *   Copyright since 2009 ohmyweb!
 *
 *   @author    ohmyweb <contact@ohmyweb.fr>
 *   @copyright since 2009 ohmyweb!
 *   @license   Proprietary - no redistribution without authorization
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_3_0_1($module)
{
    return
        Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'dwfproductextrafields` ADD INDEX(`active`);')
        && Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'product_extra_field` ADD INDEX(`id_product`);')
    ;
}
