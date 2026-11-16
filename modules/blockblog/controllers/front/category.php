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

class BlockblogCategoryModuleFrontController extends ModuleFrontController
{
    public $php_self;
	public function init()
	{

		parent::init();
	}
	
	public function setMedia()
	{
        parent::setMedia();
    }

    public function initContent()
    {
        $name_module = "blockblog";

        $this->php_self = 'module-'.$name_module.'-category';

        parent::initContent();


        include_once(_PS_MODULE_DIR_.$name_module.'/classes/blogspm.class.php');
        $obj_blog = new blogspm();

        $category_id = Tools::getValue('category_id');

        if(is_numeric($category_id)) {
            $seo_url_cat = $obj_blog->getSEOURLForCategory(array('id'=>$category_id));
        } else {
            $seo_url_cat = $category_id;
        }

        $data_url = $obj_blog->getSEOURLs();
        $category_url = $data_url['category_url'];


        Tools::redirect($category_url.$seo_url_cat);
    }


}