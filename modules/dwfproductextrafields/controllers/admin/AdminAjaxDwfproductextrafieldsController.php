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

class AdminAjaxDwfproductextrafieldsController extends ModuleAdminController
{
    /**
     * @var DwfProductExtraFields
     */
    public $module;

    public $ajax = true;

    public function ajaxProcessGetConfigByType()
    {

        $type = Tools::getValue('type');

        $return = [
            'config' => null,
            'html' => null,
            'location' => false,
        ];


        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/DwfProductExtraFieldsClass.php';
        if ($id_field = (int)Tools::getValue('id_field')
            AND Validate::isLoadedObject($field = new DwfProductExtraFieldsClass($id_field))
        ) {
            if ($type == $field->type) {
                $return['config'] = $field->config;
            }
        }

        $this->context->smarty->assign(array(
            'languages'       => Language::getLanguages(false),
            'default_lang'    => (int)Configuration::get('PS_LANG_DEFAULT'),
            'jsonConfig' => $return['config']
        ));

        switch ($type) {
            case 'selector':
                $return['html'] = $this->context->smarty->fetch('module:dwfproductextrafields/views/templates/admin/config-selector.tpl');
                break;
            case 'repeater':
                $return['html'] = $this->context->smarty->fetch('module:dwfproductextrafields/views/templates/admin/config-repeater.tpl');
                break;
            case 'textarea_mce':
            case 'textarea':
                $return['location'] = true;
                break;
        }

        die(json_encode($return));
    }

    public function ajaxProcessUpdatePositions()
    {
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/DwfProductExtraFieldsClass.php';

        $way = (int)Tools::getValue('way');
        $id_dwfproductextrafields = (int)Tools::getValue('id');
        $positions = Tools::getValue('dwfproductextrafields');

        if (is_array($positions)) {
            foreach ($positions as $position => $value) {
                $pos = explode('_', $value);
                if (isset($pos[2]) && (int)$pos[2] === $id_dwfproductextrafields) {
                    if ($productextrafields = new DwfProductExtraFieldsClass((int)$pos[2])) {
                        if (isset($position) && $productextrafields->updatePosition($way, $position)) {
                            echo 'ok position '.(int)$position.' for extrafield '.(int)$pos[2]."\r\n";
                        } else {
                            echo '{"hasError" : true, "errors" : "Can not update extrafield '.(int)$id_dwfproductextrafields.' to position '.(int)$position.' "}';
                        }
                    } else {
                        echo '{"hasError" : true, "errors" : "This extrafield ('.(int)$id_dwfproductextrafields.') can t be loaded"}';
                    }

                    break;
                }
            }
        }

        die;
    }

    public function ajaxProcessCallAddImage()
    {
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/ProductExtraField.php';
        $product = new Product((int)Tools::getValue('id_product'));
        if (!Validate::isLoadedObject($product)) {
            $files = array();
            $files[0]['error'] = Tools::displayError('Cannot add image because product creation failed.');
        }

        $res = false;
        $field = Tools::getValue('field');
        $fieldname = Tools::getValue('fieldname');
        $fieldType = Tools::getValue('fieldType', 'image');
        $image_uploader = new HelperImageUploader($field);
        $image_uploader->setAcceptTypes(array('jpeg', 'gif', 'png', 'jpg', 'webp'));
        $files = $image_uploader->process();

        foreach ($files as &$file) {
            if (isset($file['error']) && (!is_numeric($file['error']) || $file['error'] != 0)) {
                continue;
            }

            $new_path = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$this->module->getExtraFieldImagePath($product->id).'/';
            $path_parts = pathinfo($file['name']);
            $new_file = uniqid(). '_' . Tools::str2url($path_parts['filename']).'.'.$path_parts['extension'];

            $error = 0;
            if (!ImageManager::resize($file['save_path'], $new_path.$new_file, null, null, 'jpg', false, $error)) {
                switch ($error) {
                    case ImageManager::ERROR_FILE_NOT_EXIST:
                        $file['error'] = Tools::displayError('An error occurred while copying image, the file does not exist anymore.');
                        break;

                    case ImageManager::ERROR_FILE_WIDTH:
                        $file['error'] = Tools::displayError('An error occurred while copying image, the file width is 0px.');
                        break;

                    case ImageManager::ERROR_MEMORY_LIMIT:
                        $file['error'] = Tools::displayError('An error occurred while copying image, check your memory limit.');
                        break;

                    default:
                        $file['error'] = Tools::displayError('An error occurred while copying image.');
                        break;
                }

                continue;
            }

            unlink($file['save_path']);
            //Necesary to prevent hacking
            unset($file['save_path']);

            $file_infos = pathinfo($new_file);

            $file['basename'] = $file_infos['filename'];
            $file['status']   = 'ok';
            $file['file']     = $new_file;
            $file['path']     = $this->context->shop->physical_uri . $this->module->getExtraFieldImagePath($product->id).'/';

            $image = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$this->module->getExtraFieldImagePath($product->id).'/'.$new_file;
            $image_thumbnail = ImageManager::thumbnail($image, 'extrafield_'.$field.'.jpg', 100, 'jpg', true, true);
            $image_size = file_exists($image) ? filesize($image) / 1000 : false;

            $file['thumbnail'] = $image_thumbnail ? str_replace('../img/tmp/', _PS_TMP_IMG_, $image_thumbnail) : false;
            $file['size_kb'] = $image_size . 'kb';

            @unlink(_PS_TMP_IMG_DIR_.$field.'_'.(int)$product->id.'.jpg');

            $medias = array();
            $extrafields = ProductExtraField::getByIdProduct($product->id);
            if ($fieldType == 'gallery' && $extrafields->{$fieldname}) {
                $medias = explode(',', $extrafields->{$fieldname});
            }
            $medias[] = $new_file;
            $extrafields->{$fieldname} = implode(',', $medias);
            $res = $extrafields->save();
        }

        die(json_encode(array('status' => $res, $image_uploader->getName() => $files)));
    }

    public function ajaxProcessCallDeleteImage()
    {
        require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/ProductExtraField.php';

        $product = new Product((int)Tools::getValue('id_product'));
        $file = Tools::getValue('file');
        if (!Validate::isLoadedObject($product) || !$file) {
            $files = array();
            $files[0]['error'] = Tools::displayError('Cannot add image because product creation failed.');
        }

        if ($repeaterReference = Tools::getValue('reference')) {
            $field = Tools::getValue('field');
            $repeaterData = explode('|', $repeaterReference);
            $extrafields = ProductExtraField::getByIdProduct($product->id);
            if (!empty($repeaterData[0]) && $extrafields->{$repeaterData[0]}) {
                $languages = Language::getLanguages(false);
                foreach ($languages as $lang) {
                    if ($extrafields->{$repeaterData[0]}[$lang['id_lang']]) {
                        $repeater = json_decode($extrafields->{$repeaterData[0]}[$lang['id_lang']], true);
                        if ($repeater && count($repeater)) {
                            foreach ($repeater as $i => &$rep) {
                                if (!empty($repeaterData[2]) && $rep[$repeaterData[2]] && $rep[$repeaterData[2]] == $file) {
                                    unlink(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$this->module->getExtraFieldImagePath($product->id).'/'.$file);
                                    $rep[$repeaterData[2]] = '';
                                }
                            }
                            $extrafields->{$repeaterData[0]}[$lang['id_lang']] = json_encode($repeater);
                            $extrafields->save();
                        }
                    }
                }
                $file_infos = pathinfo($file);
                die(json_encode(array($field => $files, 'basename' => $file_infos['filename'], 'deleted' => $file)));
            }
        } else {
            $field = Tools::getValue('field');
            $extrafields = ProductExtraField::getByIdProduct($product->id);
            if ($extrafields->{$field}) {
                $medias = explode(',', $extrafields->{$field});
                if ($file) {
                    unlink(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$this->module->getExtraFieldImagePath($product->id).'/'.$file);

                    $newMedias = implode(',', array_diff($medias, array($file)));
                    $extrafields->{$field} = $newMedias;
                    $extrafields->save();

                    $files = array();
                    if ($newMedias) {
                        foreach ($newMedias as $newMedia) {
                            $files[] = array('name' => $newMedia);
                        }
                    }

                    $file_infos = pathinfo($file);

                    die(json_encode(array($field => $files, 'basename' => $file_infos['filename'], 'deleted' => $file)));
                }
            }
        }
    }


}
