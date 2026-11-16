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

class pagenavhelp extends Module {


    private $_name = 'blockblog';

    public function pagenav($start,$count,$step, $_data =null )
    {


        $prefix = isset($_data['prefix'])?$_data['prefix']:'';
        $action = isset($_data['action'])?$_data['action']:'';
        $ajax_url = isset($_data['ajax_url'])?$_data['ajax_url']:'';



        ob_start();
        include(dirname(__FILE__).'/../views/templates/hooks/'.$this->_name.'/pagenav_loyalty_account.phtml');
        $res = ob_get_clean();

        return $res;
    }
}