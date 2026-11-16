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

function upgrade_module_2_1_0()
{
    require_once _PS_MODULE_DIR_.'dwfproductextrafields/models/DwfProductExtraFieldsClass.php';

    $res = true;

    $statements = array();

    $statements[] = 'ALTER TABLE `'._DB_PREFIX_.'dwfproductextrafields` CONVERT TO CHARACTER SET utf8mb4;';
    $statements[] = 'ALTER TABLE `'._DB_PREFIX_.'dwfproductextrafields_lang` CONVERT TO CHARACTER SET utf8mb4;';

    $statements[] = 'ALTER TABLE `'._DB_PREFIX_.'product_extra_field` CONVERT TO CHARACTER SET utf8mb4;';
    $statements[] = 'ALTER TABLE `'._DB_PREFIX_.'product_extra_field_shop` CONVERT TO CHARACTER SET utf8mb4;';
    $statements[] = 'ALTER TABLE `'._DB_PREFIX_.'product_extra_field_lang` CONVERT TO CHARACTER SET utf8mb4;';

    $extraFields = new PrestaShopCollection('DwfProductExtraFieldsClass');
    foreach ($extraFields as $extraField) {
        if ($extraField->isMultilangField()) {
            $statements[] = 'ALTER TABLE `'._DB_PREFIX_.'product_extra_field_lang` 
                                                CHANGE `'.pSQL($extraField->fieldname).'` `'.pSQL($extraField->fieldname).'` '.$extraField->getFieldType() . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;';
        }
    }

    foreach ($statements as $statement) {
        $res &= Db::getInstance()->execute($statement);
    }

    return $res;
}
