<?php
defined('_PS_VERSION_') or exit;
class CMSCategory extends CMSCategoryCore
{
    /*
    * module: creativeelements
    * date: 2024-09-22 09:44:29
    * version: 2.10.1
    */
    const CE_OVERRIDE = true;
    /*
    * module: creativeelements
    * date: 2024-09-22 09:44:29
    * version: 2.10.1
    */
    public function __construct($id = null, $idLang = null)
    {
        parent::__construct($id, $idLang);
        $ctrl = Context::getContext()->controller;
        if ($ctrl instanceof CmsController && !CmsController::$initialized && !$this->active && Tools::getIsset('id_employee') && Tools::getIsset('adtoken')) {
            $tab = 'AdminCmsContent';
            if (Tools::getAdminToken($tab . (int) Tab::getIdFromClassName($tab) . (int) Tools::getValue('id_employee')) == Tools::getValue('adtoken')) {
                $this->active = 1;
            }
        }
    }
}
