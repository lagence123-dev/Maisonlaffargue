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

class BlockblogcommentsItems extends ObjectModel
{

    /** @var string Name */
    public $id;
    public $title;
    public $count_posts;
    public $shop_name;
    public $language;
    public $time_add;
    public $status;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'blog_comments',
        'primary' => 'id',
        'fields' => array(
            'id' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedInt','required' => true,),

            'title' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName', 'size' => 128),
            'count_posts' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),

            'shop_name' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName', 'size' => 128),
            'language' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName', 'size' => 128),
            'time_add' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName', 'size' => 128),
            'status' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),

        ),

    );




    public function deleteSelection($selection)
    {
        foreach ($selection as $value) {
            $obj = new BlockblogcommentsItems($value);
            if (!$obj->delete()) {
                return false;
            }
        }
        return true;
    }

    public function delete()
    {
        $return = false;

        if (!$this->hasMultishopEntries() || Shop::getContext() == Shop::CONTEXT_ALL) {

            require_once(_PS_MODULE_DIR_ . 'blockblog/classes/blogspm.class.php');
            $blog_obj = new blogspm();
            $blog_obj->deleteComment(array('id'=>(int)$this->id));


            $return = true;
        }
        return $return;
    }


    
}
?>
