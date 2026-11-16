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

class BlockblogSitemapModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {

        $name = "blockblog";



        $token = Tools::getValue('token');

        if(md5($name._PS_BASE_URL_SSL_) == $token){

            if(Configuration::get($name.'sitemapon') == 1) {

                include_once(_PS_MODULE_DIR_ . $name . '/classes/blogspm.class.php');
                $obj_blog = new blogspm();

                $obj_blog->generateSitemap();
                echo 'Success: Sitemap succesfully generated!';
            } else {
                echo 'Sitemap disabled!';
            }

        } else {
            echo 'Error: Access denien! Invalid token!';
        }

        exit;
    }
}
?>