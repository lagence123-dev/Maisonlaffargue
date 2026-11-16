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

class AdminBlockblogajaxController extends ModuleAdminController
{



    public function ajaxProcessBlockblogAjax()
    {

        $HTTP_X_REQUESTED_WITH = isset($_SERVER['HTTP_X_REQUESTED_WITH'])?$_SERVER['HTTP_X_REQUESTED_WITH']:'';
        if($HTTP_X_REQUESTED_WITH != 'XMLHttpRequest') {
            exit;
        }

        $module_name = 'blockblog';


        ob_start();
        $status = 'success';
        $message = '';



        if(Tools::getValue('token') == Tools::getAdminTokenLite('AdminBlockblogajax')) {


            switch (Tools::getValue('action_custom')) {
                case 'active' :


                    include_once(_PS_MODULE_DIR_ . $module_name . '/classes/blogspm.class.php');
                    $obj_blog = new blogspm();


                    $id = (int)Tools::getValue('id');
                    $value = (int)Tools::getValue('value');
                    if ($value == 0) {
                        $value = 1;
                    } else {
                        $value = 0;
                    }
                    $type_action = Tools::getValue('type_action');

                    switch ($type_action) {
                        case 'category':
                            $obj_blog->updateCategoryStatus(array('id' => $id, 'status' => $value));
                            break;
                        case 'is_active':
                            $obj_blog->updatePostStatus(array('id' => $id, 'status' => $value));
                            break;
                        case 'comment':
                            $obj_blog->updateCommentStatus(array('id' => $id, 'status' => $value));
                            break;
                        case 'is_comments':
                            $obj_blog->updateCommentsForPostStatus(array('id' => $id, 'status' => $value));
                            break;
                        case 'is_fbcomments':
                            $obj_blog->updateFBCommentsForPostStatus(array('id' => $id, 'status' => $value));
                            break;
                        case 'is_slider':
                            $obj_blog->updateSliderForPostStatus(array('id' => $id, 'status' => $value));
                        break;
                        case 'gallery':
                            $obj_blog->updateGalleryStatus(array('id' => $id, 'status' => $value));
                            break;
                        case 'galleryfeatured':
                            $obj_blog->updateGalleryFeatured(array('id' => $id, 'status' => $value));
                            break;
                        default :
                            $status = 'error';
                            $message = 'Invalid action';
                        break;
                    }


                    break;
                    case 'enabledisableposition':

                        $alias_position = Tools::getValue('alias_position');
                        $alias_position = Tools::str_replace_once("desktop","",$alias_position);
                        $is_active = Tools::getValue('is_active');
                        Configuration::updateValue($module_name . $alias_position , $is_active);

                        include_once(_PS_MODULE_DIR_ . $module_name . '/'.$module_name.'.php');
                        $obj_blog = new $module_name();
                        $obj_blog->clearSmartyCacheBlog();


                        break;
                    case 'enabledisablepositionblog':

                        $alias_position = Tools::getValue('alias_position');
                        $alias_position = Tools::str_replace_once("_desktop","",$alias_position);



                        $data_alias_position = explode("_",$alias_position);

                        $alias_position = $data_alias_position[0];
                        $alias_position = Tools::str_replace_once("mobile","",$alias_position);


                        $prefix = $data_alias_position[1];
                        $mobile = isset($data_alias_position[2])?$data_alias_position[2]:'';



                        $is_active = Tools::getValue('is_active');

                        if($is_active == 1){
                            $is_active_data = $alias_position.$prefix.$mobile;
                        } else{
                            $is_active_data = '';
                        }

                        //var_dump($module_name.'_'.$alias_position.$prefix.$mobile);exit;

                        Configuration::updateValue($module_name.'_'.$alias_position.$prefix.$mobile, $is_active_data);

                        include_once(_PS_MODULE_DIR_ . $module_name . '/'.$module_name.'.php');
                        $obj_blog = new $module_name();
                        $obj_blog->clearSmartyCacheBlog();


                        break;
                    case 'orderbypositionblog':


                        $orderby_serialize = Tools::getValue('orderby_serialize');
                        $orderby_serialize = explode("&",$orderby_serialize);

                        $positions_pre = array();

                        foreach($orderby_serialize as $k => $item) {
                            $item = explode("=", $item);
                            $prefix_item = $item[0];
                            $position_item = $item[1];


                            $prefix_item_data = explode("-",$prefix_item);

                            $alias_position = $prefix_item_data[2];

                            $block_alias =$prefix_item_data[3];
                            $block_alias = Tools::str_replace_once("[]","",$block_alias);

                            $key = $alias_position."_".$block_alias;
                            $positions_pre[$key] = $position_item;

                        }


                        $positions = array();
                        $i = 1;
                        foreach($positions_pre as $key => $position){
                            $positions[$key]=$i;
                            $i++;

                        }


                        Configuration::updateValue($module_name . '_orderby' . $alias_position, json_encode($positions));

                        include_once(_PS_MODULE_DIR_ . $module_name . '/'.$module_name.'.php');
                        $obj_blog = new $module_name();
                        $obj_blog->clearSmartyCacheBlog();


                        break;
                default :
                    $status = 'error';
                    $message = 'Invalid action';
                    break;
            }

        } else {
            $status = 'error';
            $message = 'Invalid token.';
        }

        $response = new stdClass();
        $content = ob_get_clean();
        $response->status = $status;
        $response->message = $message;
        $response->params = array('content' => $content);


        echo json_encode($response);
        exit;

    }
}
?>