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

class ps17helpblockblog {
    private $_name = 'blockblog';

    public function setMissedVariables(){
        $smarty = Context::getContext()->smarty;



        $custom_ssl_var = 0;
        if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
            $custom_ssl_var = 1;

        if ($custom_ssl_var == 1)
            $base_dir_ssl = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
        else
            $base_dir_ssl = _PS_BASE_URL_.__PS_BASE_URI__;

        $smarty->assign('base_dir_ssl' , $base_dir_ssl);


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $smarty->assign($this->_name.'is17' , 1);
        }

    }
}