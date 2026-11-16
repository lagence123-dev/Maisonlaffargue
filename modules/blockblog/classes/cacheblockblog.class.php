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

class cacheblockblog extends Module{



    public function clearSmartyCacheModule($data = null){

        $name_module = "blockblog";


        if(version_compare(_PS_VERSION_, '1.6', '>')) {

            include_once(_PS_MODULE_DIR_.$name_module.'/'.$name_module.'.php');
            $obj = new $name_module();
            $is_mobile = $obj->getIsMobile();

            $ids_product_current = isset($data['id_products'])?$data['id_products']:0;
            $skip_files = array(".","..","index.php",".DS_Store");
            $product_page_files = array("TabContent.tpl","tab.tpl","productactions.tpl");

            // !!! only hooks, which have view on the mobile and desktop  !!! //
            $mobile_desktop_page_files = array("footer.tpl","home.tpl","left.tpl","right.tpl",
                                                "blogcategoriesspm.tpl","blogarchivesspm.tpl","blogcommentsspm.tpl","bloggallery.tpl",
                                                "blogpostsspm.tpl","blogsearchspm.tpl","blogtagsspm.tpl","blogtopauthors.tpl");
            // !!! only hooks, which have view on the mobile and desktop  !!! //


            // clear cache for hooks //
            $template_path_hooks = "views".DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."hooks".DIRECTORY_SEPARATOR;
            $dir_hooks = _PS_MODULE_DIR_.$name_module.DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."templates".DIRECTORY_SEPARATOR."hooks";

            $dh  = opendir($dir_hooks);
            while (false !== ($filename = readdir($dh))) {

                if(in_array($filename,$skip_files))
                    continue;


                $template_name = $template_path_hooks.$filename;

                if(in_array($filename,$product_page_files)){

                    if ($ids_product_current) {

                        foreach($ids_product_current as $id_product_current) {
                            $cache_id = $name_module . '|' . $id_product_current;
                            $this->_clearCache($template_name, $cache_id);

                        }


                    }

                } else {

                    if(in_array($filename,$mobile_desktop_page_files))
                        $cache_id = $name_module .'|' . $filename.$is_mobile;
                     else
                        $cache_id = $name_module.'|' .$filename;

                    $this->_clearCache($template_name, $cache_id);

                }



            }
            // clear cache for hooks //


            // clear cache on the home page only for ps 17 //
            if(version_compare(_PS_VERSION_, '1.7', '>')) {
                $template_name = "module:ps_featuredproducts/views/templates/hook/ps_featuredproducts.tpl";
                $cache_id = "ps_featuredproducts";
                $this->_clearCache($template_name, $cache_id);
            }
            // clear cache on the home page only for ps 17 //


        }


    }




}