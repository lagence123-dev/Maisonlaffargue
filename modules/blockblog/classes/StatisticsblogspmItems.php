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

class StatisticsblogspmItems extends ObjectModel
{


    /** @var string Name */
    public $id_customer;

    /*private $prefix_db;
    public function __construct(){
        $this->prefix_db = $this->getPrefixDB(); // error is on this line
    }*/

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'spm_loyalty',
        'primary' => 'id_customer',
        'fields' => array(
            'id_customer' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedInt','required' => true,),
        ),

    );


   /* public function getPrefixDB()
    {
        include_once(_PS_MODULE_DIR_ .  'blockblog/blockblog.php');
        $obj_blockblog = new blockblog();
        $_prefix = $obj_blockblog->getLoyalityDbPrefix();
        return $_prefix;

    }*/



}
?>
