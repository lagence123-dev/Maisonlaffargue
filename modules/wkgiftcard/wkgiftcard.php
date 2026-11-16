<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
include_once 'classes/WkGiftCardRequiredClasses.php';

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;

class WkGiftCard extends Module
{
    public $secure_key = '';
    public $giftCardDb = '';
    public $html = '';
    public $message_allowed_char = 250;
    public $psFileUploadLimit = '';
    public $errors = [];

    public function __construct()
    {
        $this->name = 'wkgiftcard';
        $this->tab = 'front_office_features';
        $this->version = '5.2.1';
        $this->module_key = 'aaa379a2b0db07918aa451a0f740675c';
        $this->author = 'Webkul';
        $this->secure_key = Tools::encrypt($this->name);
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Gift Card');
        $this->description = $this->l('Add gift card and minimize the product cost');
        $this->giftCardDb = new WkGiftCardDb();
        $this->psFileUploadLimit = Configuration::get('PS_LIMIT_UPLOAD_FILE_VALUE');
    }

    public function hookDisplayHeader()
    {
        /* To remove gift card from cart if product is deleted */
        if (isset($this->context->cart->id) && $this->context->cart->id) {
            $giftCardCartInfo = WkGiftCardCart::getGiftCardCartByCartId($this->context->cart->id, $this->context->shop->id);
            if ($giftCardCartInfo) {
                foreach ($giftCardCartInfo as $giftCardCart) {
                    $product = new Product($giftCardCart['id_product']);
                    if (!Validate::isLoadedObject($product)) {
                        $giftCardCartObj = new WkGiftCardCart((int) $giftCardCart['id_wk_gift_card_cart']);
                        $giftCardCartObj->delete();
                    }
                }
            }
        }
    }

    public function getTemplate($template)
    {
        $path = _PS_MODULE_DIR_ . 'wkgiftcard/views/templates/front/pdf/' . $template . '.tpl';

        return $path;
    }

    public function getContent()
    {
        // start cross-selling
        Media::addJsDef([
            'wkModuleAddonKey' => $this->module_key,
            'wkModuleAddonsId' => 31480,
            'wkModuleTechName' => $this->name,
            'wkModuleDoc' => file_exists(_PS_MODULE_DIR_ . $this->name . '/docs/doc_en.pdf'),
        ]);
        $this->context->controller->addJs('https://prestashop.webkul.com/crossselling/wkcrossselling.min.js?t=' . time());
        // end

        $this->context->controller->addCSS(_MODULE_DIR_ . $this->name . '/views/css/lib/jquery-confirm.min.css');
        $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/lib/jquery-confirm.min.js');

        $cronURL = $this->context->link->getModuleLink('wkgiftcard', 'giftcardcron');
        $cronURL .= '?token=' . $this->secure_key;
        $this->context->smarty->assign(
            [
                'cron_url' => $cronURL,
            ]
        );
        if (Tools::isSubmit('btnSubmit')) {
            $this->postProcess();
        }

        if (Tools::isSubmit('giftCard_adv_submit')) {
            $this->postProcess();
        }

        $this->html .= $this->renderForm();
        $this->html .= $this->context->smarty->fetch($this->local_path . 'views/templates/admin/cron_settings.tpl');

        if (Tools::getIsset('wkdeletebannericon') && (Tools::getValue('wkdeletebannericon') == 1)) {
            $image = _PS_MODULE_DIR_ . $this->name . '/views/img/banner/' . Configuration::get('WK_GIFT_CARD_BANNER_IMAGE', $this->context->shop->id);
            if (file_exists($image) && Configuration::get('WK_GIFT_CARD_BANNER_IMAGE', $this->context->shop->id)) {
                Configuration::updateValue('WK_GIFT_CARD_BANNER_IMAGE_' . $this->context->shop->id, '123');
                unlink($image);
            }

            $this->redirectToConfiguration(1);
        }

        if (Tools::getIsset('wkdeletepdficon') && (Tools::getValue('wkdeletepdficon') == 1)) {
            $idShop = $this->context->shop->id;
            @unlink(_PS_MODULE_DIR_ . 'wkgiftcard/views/img/' . Configuration::get('WK_VOUCHER_PDF_IMG'));
            $logoName = $idShop . '_voucher_pdf_img.jpg';
            $storeLogoPath = _PS_MODULE_DIR_ . 'wkgiftcard/views/img/' . $logoName;
            ImageManager::resize($_FILES['WK_VOUCHER_PDF_IMG']['tmp_name'], $storeLogoPath);
            if (Configuration::updateValue('WK_VOUCHER_PDF_IMG', $logoName)) {
                $this->redirectToConfiguration(1);
            }
        }

        return $this->html;
    }

    /**
     * Redirect to configuration page with conf value
     *
     * @param conf
     *
     * @return void
     */
    public function redirectToConfiguration($conf)
    {
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' .
            $this->tab . '&module_name=' . $this->name . '&conf=' . $conf
        );
    }

    private function postProcess()
    {
        $tmpName = '';
        if (Tools::isSubmit('btnSubmit')) {
            $sendPDFInEmail = trim(Tools::getValue('WK_GIFT_CARD_SEND_PDF_IN_EMAIL'));
            $isUsedProductImage = Tools::getValue('WK_GIFT_CARD_PRODUCT_PDF_IMG');
            $prefix = trim(Tools::getValue('WK_GIFT_CARD_PREFIX'));
            if (empty($prefix)) {
                $this->context->controller->errors[] = $this->l('Gift card voucher code prefix is required.');
            } elseif (!Validate::isDiscountName($prefix)) {
                $this->context->controller->errors[] = $this->l('Gift card voucher code prefix is invalid.');
            }
            if (empty(Tools::getValue('WK_GIFT_CARD_ORDER_STATUS'))) {
                $this->context->controller->errors[] = $this->l('Status to send voucher code is required.');
            }
            $notExistImg = true;
            if (!empty(Configuration::get('WK_VOUCHER_PDF_IMG'))) {
                $psImgUrl = _PS_MODULE_DIR_ . 'wkgiftcard/views/img/' . Configuration::get('WK_VOUCHER_PDF_IMG');
                if (file_exists($psImgUrl)) {
                    $notExistImg = false;
                }
            } else {
                $notExistImg = false;
            }
            if ($sendPDFInEmail && !$isUsedProductImage) {
                if (empty($_FILES['WK_VOUCHER_PDF_IMG']['name']) && $notExistImg) {
                    $this->context->controller->errors[] = $this->l('Upload image for PDF is required');
                }
                if ($_FILES['WK_VOUCHER_PDF_IMG']['size'] != 0) {
                    if ($_FILES['WK_VOUCHER_PDF_IMG']['size'] > ($this->psFileUploadLimit * 1024 * 1024)) {
                        $this->context->controller->errors[] = $this->l('Image must be less than ') . $this->psFileUploadLimit . ' MB';
                    }

                    if (0 == $_FILES['WK_VOUCHER_PDF_IMG']['error']) {
                        if (!ImageManager::isCorrectImageFileExt($_FILES['WK_VOUCHER_PDF_IMG']['name'], ['jpg', 'jpeg', 'png', 'gif'])) {
                            $this->context->controller->errors[] = $this->l('Invalid image extension. Only jpg, jpeg, png, gif allowed.');
                        }
                    }
                }
            }

            if (!count($this->context->controller->errors)) {
                if (Tools::getValue('WK_GIFT_CARD_MENU')) {
                    Configuration::updateValue('WK_GIFT_CARD_MENU', 1);
                } else {
                    Configuration::updateValue('WK_GIFT_CARD_MENU', 0);
                }
                if (Module::isEnabled('ps_mainmenu') && Configuration::get('WK_GIFT_CARD_MENU') == 1) {
                    WkGiftCardMenuHelper::removeTopMenuGiftCard();
                    WkGiftCardMenuHelper::addTopMenuGiftCard();
                    WkGiftCardMenuHelper::displayTopMenuGiftCard();
                } elseif (Module::isEnabled('ps_mainmenu') && Configuration::get('WK_GIFT_CARD_MENU') == 0) {
                    WkGiftCardMenuHelper::removeTopMenuGiftCard();
                }
                if ($sendPDFInEmail && !$isUsedProductImage) {
                    if (Tools::getValue('WK_VOUCHER_PDF_IMG')) {
                        if (!empty($_FILES['WK_VOUCHER_PDF_IMG']['name'])) {
                            $idShop = $this->context->shop->id;
                            @unlink(_PS_MODULE_DIR_ . 'wkgiftcard/views/img/' . Configuration::get('WK_VOUCHER_PDF_IMG'));
                            $logoName = $idShop . '_voucher_pdf_img.jpg';
                            $storeLogoPath = _PS_MODULE_DIR_ . 'wkgiftcard/views/img/' . $logoName;
                            ImageManager::resize($_FILES['WK_VOUCHER_PDF_IMG']['tmp_name'], $storeLogoPath);
                            Configuration::updateValue('WK_VOUCHER_PDF_IMG', $logoName);
                        }
                    }
                }
                Configuration::updateValue('WK_GIFT_CARD_PREFIX', $prefix);
                $giftCardOrderStatus = Tools::getValue('WK_GIFT_CARD_ORDER_STATUS');
                if ($giftCardOrderStatus) {
                    Configuration::updateValue('WK_GIFT_CARD_ORDER_STATUS', json_encode($giftCardOrderStatus));
                } else {
                    Configuration::updateValue('WK_GIFT_CARD_ORDER_STATUS', json_encode([]));
                }
                Configuration::updateValue('WK_GIFT_CARD_DISPLAY_DELIVERY_DATE', Tools::getValue('WK_GIFT_CARD_DISPLAY_DELIVERY_DATE'));
                Configuration::updateValue('WK_GIFT_CARD_SEND_PDF_IN_EMAIL', $sendPDFInEmail);
                Configuration::updateValue('WK_GIFT_CARD_PRODUCT_PDF_IMG', Tools::getValue('WK_GIFT_CARD_PRODUCT_PDF_IMG'));
                Configuration::updateValue('WK_GIFT_CARD_ASSOCIATE_CUSTOMER_EMAIL_TO_VOUCHER', Tools::getValue('WK_GIFT_CARD_ASSOCIATE_CUSTOMER_EMAIL_TO_VOUCHER'));

                $this->redirectToConfiguration(4);
            }
        }

        if (Tools::isSubmit('giftCard_adv_submit')) {
            if (!empty($_FILES['WK_GIFT_CARD_BANNER_IMAGE']['name'])) {
                $validIcon = false;
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES['WK_GIFT_CARD_BANNER_IMAGE']['name'], '.'), 1));
                $imagesize = @getimagesize($_FILES['WK_GIFT_CARD_BANNER_IMAGE']['tmp_name']);
                if (isset($_FILES['WK_GIFT_CARD_BANNER_IMAGE'], $_FILES['WK_GIFT_CARD_BANNER_IMAGE']['tmp_name'])
                    && !empty($_FILES['WK_GIFT_CARD_BANNER_IMAGE']['tmp_name'])
                    && !empty($imagesize)
                    && in_array(
                        Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)),
                        ['jpg', 'gif', 'jpeg', 'png']
                    )
                ) {
                    // list($icon_width, $icon_height) = $imagesize;
                    $tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                    if ($error = ImageManager::validateUpload($_FILES['WK_GIFT_CARD_BANNER_IMAGE'])) {
                        $this->context->controller->errors[] = $error;
                    } elseif (!$tmpName || !move_uploaded_file($_FILES['WK_GIFT_CARD_BANNER_IMAGE']['tmp_name'], $tmpName)) {
                        return false;
                    }
                }

                if (!in_array($type, ['jpg', 'gif', 'jpeg', 'png'])) {
                    $this->context->controller->errors[] = $this->l('Image format not recognized, allowed formats are: .gif, .jpg, .png');
                }

                if ($_FILES['WK_GIFT_CARD_BANNER_IMAGE']['size'] > ($this->psFileUploadLimit * 1024 * 1024)) {
                    $this->context->controller->errors[] = $this->l('Image must be less than ') . $this->psFileUploadLimit . ' MB';
                }

                if (empty($this->context->controller->errors)) {
                    $validIcon = true;
                }
            }

            if (!count($this->context->controller->errors)) {
                if (isset($validIcon) && $validIcon) {
                    $idShop = $this->context->shop->id;
                    $path = $_FILES['WK_GIFT_CARD_BANNER_IMAGE']['name'];
                    $ext = pathinfo($path, PATHINFO_EXTENSION);

                    // upload icon image
                    if (!ImageManager::resize(
                        $tmpName,
                        _PS_MODULE_DIR_ . 'wkgiftcard/views/img/banner/' . $idShop . '.' . $ext,
                        null,
                        null,
                        $ext,
                        false,
                        $error
                    )
                    ) {
                        switch ($error) {
                            case ImageManager::ERROR_FILE_NOT_EXIST:
                                $this->errors[] = $this->l('An error occurred while copying image') . ', ' .
                                    $this->l('the file does not exist anymore.');
                                break;
                            case ImageManager::ERROR_FILE_WIDTH:
                                $this->errors[] = $this->l('An error occurred while copying image') . ', ' .
                                    $this->l('the file width is 0px.');
                                break;
                            case ImageManager::ERROR_MEMORY_LIMIT:
                                $this->errors[] = $this->l('An error occurred while copying image') . ', ' .
                                    $this->l('check your memory limit.');
                                break;
                            default:
                                $this->errors[] = $this->l('An error occurred while uploading the image.');
                                break;
                        }
                    }

                    $image = _PS_MODULE_DIR_ . 'wkgiftcard/views/img/banner/' . $idShop . '.' . $ext;
                    $thumbnailSize = 350;
                    $cacheName = $this->table . '_' . $thumbnailSize . '_' . $idShop . '.' . $ext;
                    ImageManager::thumbnail($image, $cacheName, $thumbnailSize, $ext, true, true);
                    $thumbnailSize = 50;
                    $cacheName = $this->table . '_' . $thumbnailSize . '_' . $idShop . '.' . $ext;
                    ImageManager::thumbnail($image, $cacheName, $thumbnailSize, $ext, true, true);
                    // update icon name in table

                    $giftCardImage[$idShop] = $idShop . '.' . $ext;
                    Configuration::updateValue('WK_GIFT_CARD_BANNER_IMAGE', $giftCardImage);

                    if ($tmpName) {
                        @unlink($tmpName);
                    }
                }

                $this->redirectToConfiguration(4);
            }
        }
    }

    /**
     * To generate image thumbnail
     *
     * @param thumbnailSize
     * @param imageName
     * @param imgUrl
     *
     * @return bool && html
     */
    public function getImageThumbnail($thumbnailSize, $imageName, $imgUrl)
    {
        $cacheName = $this->table . '_' . $thumbnailSize . '_' . $imageName;
        $ext = pathinfo($imageName, PATHINFO_EXTENSION);
        $image_url = ImageManager::thumbnail($imgUrl, $cacheName, $thumbnailSize, $ext, true, true);

        if ($image_url) {
            return $image_url;
        }

        return false;
    }

    public function renderForm()
    {
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        asort($statuses);
        // Get default language
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $fieldsForm = [];
        $image = '';
        $logoName = Configuration::get('WK_VOUCHER_PDF_IMG');
        if ($logoName) {
            $psImgUrl = _PS_MODULE_DIR_ . $this->name . '/views/img/' . $logoName;
            if ($imgExist = file_exists($psImgUrl)) {
                $this->context->smarty->assign(
                    [
                        'previewImage' => $this->getImageThumbnail(100, $logoName, $psImgUrl),
                    ]
                );
                $smarty = $this->context->smarty;
                $image = $smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/wk_voucher_img.tpl');
            }
        }

        $message = str_replace(
            '%upload_mb%',
            $this->psFileUploadLimit,
            $this->l('You can upload file up to max size %upload_mb% MB.')
        );

        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('General'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Gift card voucher code prefix'),
                    'required' => true,
                    'name' => 'WK_GIFT_CARD_PREFIX',
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Status to send voucher code'),
                    'name' => 'WK_GIFT_CARD_ORDER_STATUS',
                    'class' => 'chosen',
                    'multiple' => true,
                    'required' => true,
                    'options' => [
                        'query' => $statuses,
                        'id' => 'id_order_state',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display gift card menu'),
                    'hint' => $this->l('If yes, customer can see gift card menu on top.'),
                    'name' => 'WK_GIFT_CARD_MENU',
                    'is_bool' => true,
                    'disabled' => (Module::isEnabled('ps_mainmenu') ? false : true),
                    'desc' => (!Module::isEnabled('ps_mainmenu') ? $this->l(
                        'Prestashop Main Menu module must be enable'
                    ) : false),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display delivery date to customers'),
                    'name' => 'WK_GIFT_CARD_DISPLAY_DELIVERY_DATE',
                    'required' => false,
                    'is_bool' => true,
                    'desc' => $this->l('If yes, cron should be setup for future delivery date.'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Send voucher details in a PDF on the email '),
                    'name' => 'WK_GIFT_CARD_SEND_PDF_IN_EMAIL',
                    'required' => false,
                    'is_bool' => true,
                    'desc' => $this->l('If yes, gift card voucher details send in a PDF attached on the email.'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Send product image in pdf '),
                    'name' => 'WK_GIFT_CARD_PRODUCT_PDF_IMG',
                    'required' => false,
                    'is_bool' => true,
                    'desc' => $this->l('If yes, product image is used in PDF.'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'file',
                    'form_group_class' => 'wkpdfimg' . (Configuration::get('WK_GIFT_CARD_PRODUCT_PDF_IMG') ? ' wk-hide' : ''),
                    'label' => $this->l('Upload image for PDF'),
                    'name' => 'WK_VOUCHER_PDF_IMG',
                    'required' => true,
                    'image' => (isset($imgExist) && $imgExist) ? $image : false,
                    'width' => '130px',
                    'display_image' => true,
                    'delete_url' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&wkdeletepdficon=1',
                    'hint' => $message,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Associate customer email to voucher'),
                    'name' => 'WK_GIFT_CARD_ASSOCIATE_CUSTOMER_EMAIL_TO_VOUCHER',
                    'required' => false,
                    'is_bool' => true,
                    'desc' => $this->l('If yes, recipient email address will associated to the generated voucher.'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'btnSubmit',
            ],
        ];

        $image = _PS_MODULE_DIR_ . $this->name . '/views/img/banner/' . Configuration::get('WK_GIFT_CARD_BANNER_IMAGE', $this->context->shop->id);
        $image_url = false;

        if (file_exists($image) && Configuration::get('WK_GIFT_CARD_BANNER_IMAGE', $this->context->shop->id)) {
            $imageName = Configuration::get('WK_GIFT_CARD_BANNER_IMAGE', $this->context->shop->id);
            $this->context->smarty->assign(
                [
                    'previewImage' => $this->getImageThumbnail(130, $imageName, $image),
                ]
            );
            $smarty = $this->context->smarty;
            $image_url = $smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/wk_voucher_img.tpl');
        }

        $fieldsForm[1]['form'] = [
            'legend' => [
                'title' => $this->l('Gift card page'),
                'icon' => 'icon-picture',
            ],
            'input' => [
                [
                    'type' => 'file',
                    'label' => $this->l('Banner image'),
                    'name' => 'WK_GIFT_CARD_BANNER_IMAGE',
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
                    'width' => '130px',
                    'delete_url' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&wkdeletebannericon=1',
                    'desc' => $this->l('Recommended image size') . ':  1080X106',
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'giftCard_adv_submit',
            ],
        ];

        $helper = new HelperForm();
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&tab_module=' .
        $this->tab . '&module_name=' . $this->name;
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;
        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm($fieldsForm);
    }

    public function getConfigFieldsValues()
    {
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        asort($statuses);
        $configVars = [
            'WK_GIFT_CARD_PREFIX' => Tools::getValue(
                'WK_GIFT_CARD_PREFIX',
                Configuration::get('WK_GIFT_CARD_PREFIX')
            ),
            'WK_GIFT_CARD_ORDER_STATUS[]' => Tools::getValue(
                'WK_GIFT_CARD_ORDER_STATUS[]',
                json_decode(Configuration::get('WK_GIFT_CARD_ORDER_STATUS'))
            ),
            'WK_GIFT_CARD_DISPLAY_DELIVERY_DATE' => Tools::getValue(
                'WK_GIFT_CARD_DISPLAY_DELIVERY_DATE',
                Configuration::get('WK_GIFT_CARD_DISPLAY_DELIVERY_DATE')
            ),
            'WK_GIFT_CARD_SEND_PDF_IN_EMAIL' => Tools::getValue(
                'WK_GIFT_CARD_SEND_PDF_IN_EMAIL',
                Configuration::get('WK_GIFT_CARD_SEND_PDF_IN_EMAIL')
            ),
            'WK_GIFT_CARD_PRODUCT_PDF_IMG' => Tools::getValue(
                'WK_GIFT_CARD_PRODUCT_PDF_IMG',
                Configuration::get('WK_GIFT_CARD_PRODUCT_PDF_IMG')
            ),
            'WK_GIFT_CARD_MENU' => Tools::getValue(
                'WK_GIFT_CARD_MENU',
                Configuration::get('WK_GIFT_CARD_MENU')
            ),
            'WK_GIFT_CARD_ASSOCIATE_CUSTOMER_EMAIL_TO_VOUCHER' => Tools::getValue(
                'WK_GIFT_CARD_ASSOCIATE_CUSTOMER_EMAIL_TO_VOUCHER',
                Configuration::get('WK_GIFT_CARD_ASSOCIATE_CUSTOMER_EMAIL_TO_VOUCHER')
            ),
        ];

        return $configVars;
    }

    /**
     * create voucher for product after order
     *
     * @param array $params
     *
     * @return null
     */
    public function hookActionValidateOrder($params)
    {
        $idShop = $params['cart']->id_shop;
        $idOrder = $params['order']->id;
        $idCurrency = $params['order']->id_currency;
        $together = Tools::getValue('ps_giftcard');
        $customer_id = $this->context->customer->id;
        $object_customer = new Customer($customer_id);
        $email = $object_customer->email;
        $objOrder = new Order($idOrder);
        $cartProduct = $objOrder->getProducts();
        if ($cartProduct) {
            foreach ($cartProduct as $cartProduct_name) {
                $productId = $cartProduct_name['product_id'];
                $psObjectGiftcard = new WkGiftCardObj();
                $getProductInfo = $psObjectGiftcard->getProductAllInfo($productId, $this->context->shop->id);
                if ($getProductInfo) {
                    $idVoucherProduct = $getProductInfo['id_voucher_product'];
                    if ($idVoucherProduct) {
                        $cartExist = WkGiftCardCart::getGiftCartProductCartInfo($productId, $params['order']->id_cart, $idShop, $cartProduct_name['id_customization']);
                        $recipientName = isset($cartExist['recipient_name']) ? $cartExist['recipient_name'] : '';
                        $fromName = isset($cartExist['from_name']) ? $cartExist['from_name'] : '';
                        $recipientEmail = isset($cartExist['recipient_email']) ? $cartExist['recipient_email'] : '';
                        $message = isset($cartExist['message']) ? $cartExist['message'] : '';
                        $deliveryDate = isset($cartExist['delivery_date']) ? $cartExist['delivery_date'] : date('Y-m-d');
                        $psObjectGiftcard = new WkGiftCardObj();
                        $startdate = isset($cartExist['delivery_date']) ? $cartExist['delivery_date'] : date('Y-m-d');
                        $validUpto = $getProductInfo['valid_upto'];
                        $enddate = date('Y-m-d', strtotime('+' . $validUpto . ' months', strtotime($startdate)));
                        $attachProducts = WkGiftCardProduct::getAllByGiftCardID($getProductInfo['id']);
                        $allowedCategories = WkGiftCardProduct::getAllowedCategory($getProductInfo['id']);
                        $together = $getProductInfo['together'];
                        $freeShipping = $getProductInfo['free_shipping'];
//                        $amount = $cartProduct_name['unit_price_tax_incl'];
                        $amount = $cartExist['dynamic_amount'];
                        $qty = $cartProduct_name['product_quantity'];
                        $cartRule = new CartRule();
                        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        // if recipient email id register as customer then create cart rule for customer so customer can see cart rule in your account section
                        if (Configuration::get('WK_GIFT_CARD_ASSOCIATE_CUSTOMER_EMAIL_TO_VOUCHER')) {
                            $idCustomer = Customer::customerExists($recipientEmail, true);
                            if ($idCustomer) {
                                $cartRule->id_customer = $idCustomer;
                            }
                        }
                        if (!$together) {
                            $code = $this->generateGiftVoucherCode($chars);
                            if ($code) {
                                $cartRule->reduction_tax = 1; // voucher with tax included
                                $cartRule->free_shipping = $freeShipping;
                                $cartRule->reduction_amount = $amount;
                                if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
                                    $cartRule->shop_restriction = 1;
                                }
                                $cartRule->reduction_currency = $idCurrency;
                                $cartRule->minimum_amount_currency = $idCurrency;
                                $cartRule->date_from = $startdate;
                                if (!empty($attachProducts)) {
                                    $cartRule->product_restriction = 1;
                                }

                                if (isset($allowedCategories['is_category_restriction']) && $allowedCategories['is_category_restriction']) {
                                    $cartRule->product_restriction = 1;
                                }

                                $cartRule->date_to = $enddate;
                                $cartRule->code = $code;
                                $cartRule->quantity = $qty;
                                $cartRule->quantity_per_user = $qty;
                                $giftCardOrderStatus = json_decode(Configuration::get('WK_GIFT_CARD_ORDER_STATUS'));
                                // send mail if order status exist
                                if (in_array($params['orderStatus']->id, $giftCardOrderStatus)) {
                                    $cartRule->active = 1;
                                } else {
                                    $cartRule->active = 0;
                                }
                                $cartRule->name = [];
                                foreach (Language::getLanguages(true) as $lang) {
//                                    $cartRule->name[$lang['id_lang']] = $code;
                                    $cartRule->name[$lang['id_lang']] = $fromName;
                                }
                                $cartRule->add();
                                $current_id = CartRule::getIdByCode($code);
                                $objectGiftCard = new WkGiftCardObj();
                                // Product Restriction and Category Restriction
                                $productSelected = $objectGiftCard->productSelection($current_id, $getProductInfo['id'], 1, $this->context->shop->id);
                                // // Add category Restriction
                                // $categorySelected = $objectGiftCard->productSelection($current_id, $getProductInfo['id'], 1, $this->context->shop->id, true);
                                $orderInfo = new CartRule($current_id);
                                $quant = $orderInfo->quantity;
                                $active = $orderInfo->active;
                                $objectCardAllInfo = new GiftCardOrderDetails();
                                $objectCardAllInfo->id_card_rule = $current_id;
                                $objectCardAllInfo->id_shop = $this->context->shop->id;
                                $objectCardAllInfo->id_customization = $cartProduct_name['id_customization'];
                                $objectCardAllInfo->id_customization = $this->context->shop->id;
                                $objectCardAllInfo->id_product = $idVoucherProduct;
                                $objectCardAllInfo->id_order = $idOrder;
                                $objectCardAllInfo->id_customer = $customer_id;
                                $objectCardAllInfo->customer_email = $email;
                                $objectCardAllInfo->active = $active;
                                $objectCardAllInfo->quantity = $quant;
                                $objectCardAllInfo->used = 0;
                                $objectCardAllInfo->recipient_name = $recipientName;
                                $objectCardAllInfo->from_name = $fromName;
                                $objectCardAllInfo->recipient_email = $recipientEmail;
                                $objectCardAllInfo->message = $message;
                                $objectCardAllInfo->delivery_date = $deliveryDate;
                                $objectCardAllInfo->genrate_code = $code;
                                $objectCardAllInfo->date_from = $startdate;
                                $objectCardAllInfo->date_to = $enddate;
                                $objectCardAllInfo->save();
                            }
                        } else {
                            for ($j = 0; $j < $qty; ++$j) {
                                $code = '';
                                $code = $this->generateGiftVoucherCode($chars);
                                if ($code) {
                                    $cartRule->reduction_tax = 1; // voucher with tax included
                                    $cartRule->free_shipping = $freeShipping;
                                    $cartRule->reduction_amount = $amount;
                                    $cartRule->reduction_currency = $idCurrency;
                                    if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
                                        $cartRule->shop_restriction = 1;
                                    }
                                    $cartRule->minimum_amount_currency = $idCurrency;
                                    $cartRule->date_from = $startdate;
                                    if (!empty($attachProducts)) {
                                        $cartRule->product_restriction = 1;
                                    }

                                    if (isset($allowedCategories['is_category_restriction']) && $allowedCategories['is_category_restriction']) {
                                        $cartRule->product_restriction = 1;
                                    }

                                    $cartRule->date_to = $enddate;
                                    $cartRule->code = $code;
                                    $cartRule->quantity = 1;
                                    $cartRule->quantity_per_user = 1;
                                    $giftCardOrderStatus = json_decode(Configuration::get('WK_GIFT_CARD_ORDER_STATUS'));
                                    if (in_array($params['orderStatus']->id, $giftCardOrderStatus)) {
                                        $cartRule->active = 1;
                                    } else {
                                        $cartRule->active = 0;
                                    }
                                    $cartRule->name = [];
                                    foreach (Language::getLanguages(true) as $lang) {
//                                        $cartRule->name[$lang['id_lang']] = $code;
                                        $cartRule->name[$lang['id_lang']] = $fromName;
                                    }
                                    $cartRule->add();
                                    $current_id = CartRule::getIdByCode($code);
                                    $objectGiftCard = new WkGiftCardObj();
                                    // Product Restriction and Category Restriction
                                    $productSelected = $objectGiftCard->productSelection($current_id, $getProductInfo['id'], 1, $this->context->shop->id);
                                    // // Add category Restriction
                                    // $categorySelected = $objectGiftCard->productSelection($current_id, $getProductInfo['id'], 1, $this->context->shop->id, true);
                                    $orderInfo = new CartRule($current_id);
                                    $quant = $orderInfo->quantity;
                                    $active = $orderInfo->active;
                                    $objectCardAllInfo = new GiftCardOrderDetails();
                                    $objectCardAllInfo->id_card_rule = $current_id;
                                    $objectCardAllInfo->id_shop = $this->context->shop->id;
                                    $objectCardAllInfo->id_customization = $cartProduct_name['id_customization'];
                                    $objectCardAllInfo->id_product = $idVoucherProduct;
                                    $objectCardAllInfo->id_order = $idOrder;
                                    $objectCardAllInfo->id_customer = $customer_id;
                                    $objectCardAllInfo->customer_email = $email;
                                    $objectCardAllInfo->active = $active;
                                    $objectCardAllInfo->quantity = $quant;
                                    $objectCardAllInfo->used = 0;
                                    $objectCardAllInfo->recipient_name = $recipientName;
                                    $objectCardAllInfo->from_name = $fromName;
                                    $objectCardAllInfo->recipient_email = $recipientEmail;
                                    $objectCardAllInfo->message = $message;
                                    $objectCardAllInfo->delivery_date = $deliveryDate;
                                    $objectCardAllInfo->genrate_code = $code;
                                    $objectCardAllInfo->date_from = $startdate;
                                    $objectCardAllInfo->date_to = $enddate;
                                    $objectCardAllInfo->save();
                                }
                            }
                        }
                    }
                }
            }
        }
        $giftCardOrderStatus = json_decode(Configuration::get('WK_GIFT_CARD_ORDER_STATUS'));
        // send mail if order status exist
        if (in_array($params['orderStatus']->id, $giftCardOrderStatus)) {
            $this->sendMailToCustomer($idOrder, $idShop);
        }
        $cart = new Cart($params['order']->id_cart);
        $cartRule = $cart->getCartRules();
        if ($cartRule) {
            foreach ($cartRule as $cart) {
                $objectCardAllInfo = new GiftCardOrderDetails();
                $is_cart_exist = $objectCardAllInfo->getCartRulesByCartId($cart['id_cart_rule'], $idShop);
                if ($is_cart_exist) {
                    $objectCardAllInfo = new GiftCardOrderDetails($is_cart_exist['id']);
                    ++$objectCardAllInfo->used;
                    $objectCardAllInfo->save();
                }
            }
        }

        // delete gift card cart data after place order
        $giftCardCartInfo = WkGiftCardCart::getGiftCardCartByCartId($params['order']->id_cart, $params['order']->id_shop);
        if (is_array($giftCardCartInfo)) {
            foreach ($giftCardCartInfo as $giftCardCart) {
                $giftCardCartObj = new WkGiftCardCart((int) $giftCardCart['id_wk_gift_card_cart']);
                $giftCardCartObj->delete();
            }
        }
    }

    public function hookDisplayAfterProductVariant($params)
    {
        $idProduct = $params['product']->id_product;
        if (!$idProduct) {
            $idProduct = Tools::getValue('id_product');
        }
        if ($idProduct) {
            $objGiftCard = new WkGiftCardObj();
            $productObj = new Product((int) $idProduct);
            $quantity = StockAvailable::getQuantityAvailableByProduct($idProduct);
            if ($objGiftCard->getProductAllInfo($idProduct, $this->context->shop->id) && $quantity) {
                if ($this->context->customer->isLogged() && $this->context->customer->id) {
                    $objCustomer = new Customer((int) $this->context->customer->id);
                    if (Validate::isLoadedObject($objCustomer)) {
                        $this->context->smarty->assign([
                            'self_email' => $objCustomer->email,
                            'self_name' => $objCustomer->firstname . ' ' . $objCustomer->lastname,
                        ]);
                    }
                }

                $this->context->smarty->assign(
                    [
                        'is_catalog_mode' => Configuration::get('PS_CATALOG_MODE'),
                        'minimal_quantity' => $productObj->minimal_quantity,
                        'id_product' => $idProduct,
                        'is_dynamic_price_gift_card' => WkGiftCardObj::isDynamicPriceProduct($idProduct, $this->context->shop->id),
                        'wk_delivery_date' => date('d-m-Y'),
                        'wk_show_delivery_date' => Configuration::get('WK_GIFT_CARD_DISPLAY_DELIVERY_DATE'),
                        'from_name' => $this->context->customer->firstname . ' ' . $this->context->customer->lastname,
                        'is_customer_login' => $this->context->customer->isLogged(),
                    ]
                );

                return $this->fetch('module:wkgiftcard/views/templates/hook/wk_gift_card_interface.tpl');
            }
        }
    }

    public function hookDisplayAfterCartProductLine($params)
    {
        if (Tools::getValue('controller') == 'cart') {
            $idProduct = $params['product']->id_product;
            $idProductAttribute = $params['product']->id_product_attribute;
            $idCustomization = $params['product']->id_customization;
            $objProduct = new Product($idProduct);
            $productPrice = $objProduct->getPrice(true, $idProductAttribute);
            $productPrice = Tools::convertPriceFull(
                $productPrice,
                $this->context->currency,
                new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'))
            );
            $modalKey = (int) $idProduct . '_' . (int) $idCustomization;

            $recipientDetails = WkGiftCardCart::getGiftCartProductCartInfo(
                $idProduct,
                $this->context->cart->id,
                $this->context->shop->id,
                $idCustomization
            );

            if ($recipientDetails) {
                $this->context->smarty->assign([
                    'recipient_details' => $recipientDetails,
                    'option_model_key' => $modalKey,
                    'idCustomization' => $idCustomization,
                    'wk_option_page' => Tools::getValue('controller'),
                    'wk_show_delivery_date' => Configuration::get('WK_GIFT_CARD_DISPLAY_DELIVERY_DATE'),
                ]);

                return $this->display(__FILE__, 'wk_gift_card_recipient_modal.tpl');
            }
        }
    }

    public function hookDisplayAfterProductName($params)
    {
        if (Tools::getValue('controller') == 'orderdetail') {
            $idProduct = $params['product']['id_product'];
            $id_order = Tools::getValue('id_order');
            $idCustomization = $params['product']['id_customization'];
            $modalKey = (int) $idProduct . '_' . (int) $idCustomization;
            $objGiftCardOrderDetails = new GiftCardOrderDetails();
            $objWkGiftCardObj = new WkGiftCardObj();
            if (!$objWkGiftCardObj->checkVoucherIdByProductId($idProduct)) {
                return;
            }
            $recipientDetails = $objGiftCardOrderDetails->getAllInfoByOrderId(
                $id_order,
                $this->context->shop->id,
                $idCustomization
            );
            if ($recipientDetails) {
                $this->context->smarty->assign([
                    'recipient_details' => $recipientDetails,
                    'option_model_key' => $modalKey,
                    'idCustomization' => $idCustomization,
                    'wk_option_page' => Tools::getValue('controller'),
                    'wk_show_delivery_date' => Configuration::get('WK_GIFT_CARD_DISPLAY_DELIVERY_DATE'),
                ]);

                return $this->display(__FILE__, 'wk_gift_card_recipient_modal.tpl');
            }
        }
    }

    public function hookDisplayProductPriceBlock($params)
    {
        if ('orderconfirmation' == Tools::getValue('controller') && $params['type'] == 'unit_price') {
            $id_order = Tools::getValue('id_order');
            $idCustomization = $params['product']['id_customization'];

            $idProduct = $params['product']['id_product'];

            $modalKey = (int) $idProduct . '_' . (int) $idCustomization;
            if ($id_order && $idCustomization) {
                $objGiftCardOrderDetails = new GiftCardOrderDetails();
                $recipientDetails = $objGiftCardOrderDetails->getAllInfoByOrderId(
                    $id_order,
                    $this->context->shop->id,
                    $idCustomization
                );

                if ($recipientDetails) {
                    $this->context->smarty->assign([
                        'recipient_details' => $recipientDetails,
                        'option_model_key' => $modalKey,
                        'idCustomization' => $idCustomization,
                        'wk_option_page' => Tools::getValue('controller'),
                        'wk_show_delivery_date' => Configuration::get('WK_GIFT_CARD_DISPLAY_DELIVERY_DATE'),
                    ]);

                    return $this->fetch('module:' . $this->name . '/views/templates/hook/wk_gift_card_recipient_modal.tpl');
                }
            }
        }

        if ('order' == Tools::getValue('controller')) {
            return $this->fetch('module:' . $this->name . '/views/templates/hook/customization_remove.tpl');
        }
    }

    /**
     * Assign vouchers to new customer
     *
     * @param array $params
     *
     * @return null
     */
    public function hookActionCustomerAccountAdd($params)
    {
        if (Configuration::get('WK_GIFT_CARD_ASSOCIATE_CUSTOMER_EMAIL_TO_VOUCHER')) {
            if (isset($params['newCustomer'])) {
                $newCustomerId = $params['newCustomer']->id;
                $newCustomerEmail = $params['newCustomer']->email;
                $objGiftCardOrderDetails = new GiftCardOrderDetails();
                $purchasedGiftCardDetails = $objGiftCardOrderDetails->getRecipientCartRuleByRecipientEmail($newCustomerEmail, $this->context->shop->id);
                if ($purchasedGiftCardDetails) {
                    foreach ($purchasedGiftCardDetails as $purchasedGiftCard) {
                        if ($idCartRule = $purchasedGiftCard['id_card_rule']) {
                            $objCartRule = new CartRule((int) $idCartRule);
                            if (Validate::isLoadedObject($objCartRule)) {
                                $objCartRule->id_customer = $newCustomerId;
                                $objCartRule->save();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * generate the vouchercode for giftproduct
     *
     * @param array $chars
     *
     * @return string
     */
    public function generateGiftVoucherCode($chars)
    {
        $charsA = str_split($chars);
        $code = Configuration::get('WK_GIFT_CARD_PREFIX');
        for ($i = 0; $i < 5; ++$i) {
            $random = mt_rand(0, Tools::strlen($chars));
            $code .= isset($charsA[$random]) ? $charsA[$random] : '';
        }

        return $code;
    }

    /**
     * send email to the customer after payment accepted
     *
     * @param array $params
     *
     * @return bool
     */
    public function hookActionOrderStatusUpdate($params)
    {
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            $langId = $params['cart']->id_lang;
            $idShop = $params['cart']->id_shop;
            $idOrder = Tools::getValue('id_order');
            $order = new Order($idOrder);
        } else {
            $idOrder = $params['id_order'];
            $order = new Order($idOrder);
            $langId = $order->id_lang;
            $idShop = $order->id_shop;
        }
        if (isset($params['orderStatus'])) {
            $orderStatusId = $params['orderStatus']->id;
            $paid = $params['orderStatus']->logable;
        } else {
            $orderStatusId = $params['newOrderStatus']->id;
            $paid = $params['newOrderStatus']->logable;
        }
        $objectCardAllInfo = new GiftCardOrderDetails();
        $orderInfo = $objectCardAllInfo->getAllInfoByOrderId($idOrder, $idShop);
        $giftCardOrderStatus = json_decode(Configuration::get('WK_GIFT_CARD_ORDER_STATUS'));
        // if order status is not paid then disable/enable the voucher
        foreach ($orderInfo as $value) {
            $id_cart_rule = $value['id_card_rule'];
            $id = $value['id'];
            $cartRule = new CartRule($id_cart_rule);
            $objectCardAllInfo = new GiftCardOrderDetails($id);
            $cartRule->active = $paid;
            $objectCardAllInfo->active = $paid;
            $cartRule->save();
            $objectCardAllInfo->save();
        }
        // send mail if order status exist and again we are enable the voucher because we are sending mail and voucher should be enabled
        if (in_array($orderStatusId, $giftCardOrderStatus)) {
            foreach ($orderInfo as $value) {
                $id_cart_rule = $value['id_card_rule'];
                $id = $value['id'];
                $cartRule = new CartRule($id_cart_rule);
                $objectCardAllInfo = new GiftCardOrderDetails($id);
                $cartRule->active = 1;
                $objectCardAllInfo->active = 1;
                $cartRule->save();
                $objectCardAllInfo->save();
            }
            $this->sendMailToCustomer($idOrder, $idShop, $langId);
        }

        return true;
    }

    /**
     * Show Gift card product recipient details table
     *
     * @param array params
     *
     * @return string
     */
    public function hookDisplayAdminOrderMain($params)
    {
        $objGiftCardOrderDetails = new GiftCardOrderDetails();
        $idOrder = (isset($params['id_order']) && $params['id_order']) ? $params['id_order'] : false;
        if ($idOrder) {
            $giftCardProductDetails = $objGiftCardOrderDetails->getAllInfoByOrderId($idOrder, $this->context->shop->id);
            $giftCardRecipientData = [];
            if ($giftCardProductDetails && is_array($giftCardProductDetails)) {
                foreach ($giftCardProductDetails as $giftCardDetails) {
                    $giftCardRecipientData[] = [
                        'product_name' => Product::getProductName($giftCardDetails['id_product']),
                        'recipient_name' => $giftCardDetails['recipient_name'],
                        'recipient_email' => $giftCardDetails['recipient_email'],
                        'delivery_date' => $giftCardDetails['delivery_date'],
                        'message' => $giftCardDetails['message'],
                    ];
                }
            }

            $this->context->smarty->assign([
                'giftCardRecipientData' => $giftCardRecipientData,
                'wk_show_delivery_date' => Configuration::get('WK_GIFT_CARD_DISPLAY_DELIVERY_DATE'),
            ]);

            return $this->context->smarty->fetch('module:' . $this->name . '/views/templates/admin/wk_giftcard_product_recipient_details.tpl');
        }

        return '';
    }

    public function hookDisplayFooterProduct($params)
    {
        $selectedId = [];
        $productId = $params['product']->id;
        $psObjectGiftcard = new WkGiftCardObj();
        $selectedGiftCardId = $psObjectGiftcard->checkVoucherIdByProductId($productId);
        $selectedProductId = WkGiftCardProduct::getAllByGiftCardID($selectedGiftCardId);
        if (!empty($selectedProductId)) {
            foreach ($selectedProductId as $idSelect) {
                $selectedId[] = $idSelect['id_product'];
            }
            $productDetails = [];
            $productsForTemplate = [];
            if ($selectedId) {
                foreach ($selectedId as $idProduct) {
                    $objProduct = new Product((int) $idProduct);
                    if ($idProduct && Validate::isLoadedObject($objProduct)) {
                        $product = [
                            'id_product' => (int) $idProduct,
                        ];
                        array_push($productDetails, $product);
                    }
                }
                if (!empty($productDetails)) {
                    foreach ($productDetails as $key => $product) {
                        $assembler = new ProductAssembler($this->context);

                        $presenterFactory = new ProductPresenterFactory($this->context);
                        $presentationSettings = $presenterFactory->getPresentationSettings();
                        $presenter = new ProductListingPresenter(
                            new ImageRetriever(
                                $this->context->link
                            ),
                            $this->context->link,
                            new PriceFormatter(),
                            new ProductColorsRetriever(),
                            $this->context->getTranslator()
                        );

                        $productsForTemplate[] = $presenter->present(
                            $presentationSettings,
                            $assembler->assembleProduct(['id_product' => $product['id_product']]),
                            $this->context->language
                        );
                    }
                }
            }
            $this->context->smarty->assign('productDetails', $productsForTemplate);

            return $this->display(__FILE__, 'wk_gift_card.tpl');
        }
    }

    /**
     * Overridden front templates
     *
     * @param array $params
     *
     * @return void
     */
    public function hookDisplayOverrideTemplate($params)
    {
        if ($params['template_file'] == 'catalog/product') {
            $extendFilePath = 'catalog/product.tpl';
            $idProduct = $params['id'];
            if (!$idProduct) {
                $idProduct = Tools::getValue('id_product');
            }
            if ($idProduct) {
                $objProduct = new Product($idProduct);
                $objWkGiftCard = new WkGiftCardObj();
                $id_gift_card_obj = $objWkGiftCard->checkVoucherIdByProductId($idProduct);
                if ($id_gift_card_obj) {
                    $this->context->smarty->assign(
                        [
                            'extendFilePath' => $extendFilePath,
                            'allow_customization' => 0,
                        ]
                    );

                    return 'module:' . $this->name . '/views/templates/hook/product_override.tpl';
                }
            }

            return $extendFilePath;
        } elseif ($params['template_file'] == 'catalog/_partials/quickview') {
            $extendFilePath = 'catalog/_partials/quickview.tpl';
            $idProduct = $params['id'];
            if (!$idProduct) {
                $idProduct = Tools::getValue('id_product');
            }
            if ($idProduct) {
                $objProduct = new Product($idProduct);
                $objWkGiftCard = new WkGiftCardObj();
                $id_gift_card_obj = $objWkGiftCard->checkVoucherIdByProductId($idProduct);
                if ($id_gift_card_obj) {
                    $this->context->smarty->assign(
                        [
                            'extendFilePath' => $extendFilePath,
                        ]
                    );

                    return 'module:' . $this->name . '/views/templates/hook/product_quick_view.tpl';
                }
            }

            return $extendFilePath;
        } elseif ($params['template_file'] == 'checkout/_partials/cart-detailed'
        || $params['template_file'] == 'checkout/cart'
        ) {
            $extendFilePath = 'checkout/cart.tpl';
            if ($params['template_file'] == 'checkout/_partials/cart-detailed') {
                $extendFilePath = 'checkout/_partials/cart-detailed.tpl';
            }

            $this->context->smarty->assign(
                [
                    'extendFilePath' => $extendFilePath,
                ]
            );

            return 'module:' . $this->name . '/views/templates/hook/wk_gift_card_cart.tpl';

        // return $extendFilePath;
        } elseif ($params['template_file'] == 'customer/order-detail') {
            $extendFilePath = 'customer/order-detail.tpl';
            $idOrder = Tools::getValue('id_order');
            if ($idOrder) {
                $objGiftCardOrderDetails = new GiftCardOrderDetails();
                $giftCardInfo = $objGiftCardOrderDetails->getAllInfoByOrderId($idOrder, Context::getContext()->shop->id);

                if ($giftCardInfo && is_array($giftCardInfo)) {
                    $this->context->smarty->assign(
                        [
                            'extendFilePath' => $extendFilePath,
                            'giftCardInfo' => $giftCardInfo,
                            'idOrder' => $idOrder,
                            'objGiftCardOrderDetails' => $objGiftCardOrderDetails,
                        ]
                    );

                    return 'module:' . $this->name . '/views/templates/hook/customer_order_details.tpl';
                }
            }

            return $extendFilePath;
        } elseif ($params['template_file'] == 'catalog/_partials/product-customization') {
            // Handle the action of returning to the product page when the 'Continue Shipping' button is clicked.
            $idProduct = $params['id'];
            if (!$idProduct) {
                $idProduct = Tools::getValue('id_product');
            }
            if ($idProduct) {
                $objProduct = new Product($idProduct);
                $objWkGiftCard = new WkGiftCardObj();
                $id_gift_card_obj = $objWkGiftCard->checkVoucherIdByProductId($idProduct);
                if ($id_gift_card_obj) {
                    return 'module:' . $this->name . '/views/templates/hook/wk_product_customization.tpl';
                }
            }
        } elseif ($params['template_file'] == 'checkout/order-confirmation') {
            $extendFilePath = 'checkout/order-confirmation.tpl';
            $idOrder = $params['controller']->id_order;
            if (!$idOrder) {
                $idOrder = Tools::getValue('id_order');
            }
            $objGiftCardOrderDetails = new GiftCardOrderDetails();
            $giftCardInfo = $objGiftCardOrderDetails->getAllInfoByOrderId($idOrder, Context::getContext()->shop->id);
            if ($giftCardInfo && (is_array($giftCardInfo) && count($giftCardInfo) > 0)) {
                $this->context->smarty->assign(
                    [
                        'extendFilePath' => $extendFilePath,
                        'giftCardInfo' => $giftCardInfo,
                        'idOrder' => $idOrder,
                        'objGiftCardOrderDetails' => $objGiftCardOrderDetails,
                    ]
                );

                return 'module:' . $this->name . '/views/templates/hook/customer_order_confirmation.tpl';
            }
        } elseif ($params['template_file'] == 'customer/history') {
            $extendFilePath = 'customer/history.tpl';
            $objGiftCardOrderDetails = new GiftCardOrderDetails();
            $this->context->smarty->assign(
                [
                    'extendFilePath' => $extendFilePath,
                    'objGiftCardOrderDetails' => $objGiftCardOrderDetails,
                ]
            );

            return 'module:' . $this->name . '/views/templates/hook/wk_customer_order_history.tpl';
        }
    }

    public function getVoucherPdfImageLink($idProduct = false)
    {
        $context = Context::getContext();
        $logo = '';
        $id_shop = (int) $context->shop->id;
        $path = _PS_MODULE_DIR_ . 'wkgiftcard/views/img/' . $id_shop . '_voucher_pdf_img.jpg';

        if (Configuration::get('WK_VOUCHER_PDF_IMG', null, null, $id_shop) != false && file_exists($path)) {
            $logo = $path;
        }

        if (Configuration::get('WK_GIFT_CARD_PRODUCT_PDF_IMG') && $idProduct) {
            $prodObj = new Product((int) $idProduct);
            $linkRewrite = $prodObj->link_rewrite[$this->context->language->id];
            $img = $prodObj->getCover($prodObj->id);
            if (isset($img['id_image'])) {
                $logo = $this->context->link->getImageLink($linkRewrite, $img['id_image']);
            }
        }

        return $logo;
    }

    public function getPDFContent($recipientName, $message, $orderInformation, $productHtml, $fromName, $idProduct = false)
    {
        $context = Context::getContext();
        $pathLogo = $this->getVoucherPdfImageLink($idProduct);
        $width = 0;
        $height = 0;
        if (!empty($pathLogo)) {
            list($width, $height) = getimagesize($pathLogo);
        }
        // Limit the height of the logo for the PDF render
        $maximum_height = 100;
        if ($height > $maximum_height) {
            $ratio = $maximum_height / $height;
            $height *= $ratio;
            $width *= $ratio;
        }
        $context = Context::getContext();
        $context->smarty->assign([
            'logo_path' => $pathLogo,
            'width_logo' => $width,
            'height_logo' => $height,
            'recipient_name' => $recipientName,
            'message' => $message,
            'orderInformation' => $orderInformation,
            'product_html' => $productHtml,
            'from_name' => $fromName,
        ]);
        $context->smarty->assign(
            [
                'product_tab' => $context->smarty->fetch($this->getTemplate('voucher_pdf_body')),
            ]
        );

        return $context->smarty->fetch($this->getTemplate('voucher_content'));
    }

    /**
     * mail send by using this function
     *
     * @param int $idOrder
     * @param int $langId
     *
     * @return bool
     */
    public function sendMailToCustomer($idOrder, $idShop, $langId = null)
    {
        $order = '';
        if ($langId == null) {
            $order = new Order($idOrder);
            $langId = $order->id_lang;
        }
        $idCurrency = Configuration::get('PS_CURRENCY_DEFAULT');
        if (Validate::isLoadedObject($order) && isset($order->id_currency)) {
            $idCurrency = $order->id_currency;
        }
        $currencySign = new Currency($idCurrency);
        $shopName = Configuration::get('PS_SHOP_NAME');
        $shopLogo = Configuration::get('PS_LOGO');
        $shopUrl = $this->context->shop->getBaseURL();
        $objectCardAllInfo = new GiftCardOrderDetails();
        $objectGiftCard = new WkGiftCardObj();
        $orderRefernce = Order::getUniqReferenceOf($idOrder);
        $orderInfo = $objectCardAllInfo->getAllInfoByOrderId($idOrder, $idShop);
        if ($orderInfo) {
            foreach ($orderInfo as $orderDetails) {
                // entry for future delivery voucher
                if (strtotime(date('Y-m-d')) < strtotime($orderDetails['delivery_date'])) {
                    $idWkGiftCardDeliverySchedule = WkGiftCardDeliverySchedule::isAlreadyExistGiftCardOrderDetails($orderDetails['id']);
                    $giftCardDeliveryScheduleObj = new WkGiftCardDeliverySchedule((int) $idWkGiftCardDeliverySchedule);
                    $giftCardDeliveryScheduleObj->id_wk_gift_card_order_details = $orderDetails['id'];
                    $giftCardDeliveryScheduleObj->is_send = 0;
                    $giftCardDeliveryScheduleObj->save();
                    continue;
                }
                $orderInformation = [];
                $appliedProducts = [];
                $i = 0;
                $email = '';
                $j = 0;
                $cartRule = new CartRule((int) $orderDetails['id_card_rule']);
                $recipientName = $orderDetails['recipient_name'];
                $fromName = $orderDetails['from_name'];
                $message = $orderDetails['message'];
                $idOrder = $orderDetails['id_order'];
                $idProduct = $orderDetails['id_product'];
                $email = $orderDetails['recipient_email'];
                $giftCardId = $objectGiftCard->checkVoucherIdByProductId($idProduct);
                $attachProducts = WkGiftCardProduct::getAllByGiftCardID($giftCardId);
                $orderInformation[$i]['giftvoucher_id'] = $orderDetails['id_product'];
                $orderInformation[$i]['id_shop'] = $orderDetails['id_shop'];
                $orderRefernce = Order::getUniqReferenceOf($idOrder);
                $orderInformation[$i]['order_refernce'] = $orderRefernce;
                $orderInformation[$i]['selprod'] = [];
                if (!empty($attachProducts)) {
                    foreach ($attachProducts as $applyProduct) {
                        $selectedProduct = $objectGiftCard->getVoucherProductDetail($applyProduct['id_product'], $applyProduct['id_shop']);
                        if ($selectedProduct) {
                            $appliedProducts[$i][$orderDetails['genrate_code']][$j]['name'] = $selectedProduct;
                            $appliedProducts[$i][$orderDetails['genrate_code']][$j]['url'] = $this->context->link->getProductLink($applyProduct['id_product']);
                            ++$j;
                        }
                    }
                }
                $productName = $objectGiftCard->getVoucherProductDetail($idProduct, $idShop);
                $orderInformation[$i]['name'] = $productName;
                $orderInformation[$i]['startdate'] = $orderDetails['date_from'];
                $orderInformation[$i]['enddate'] = $orderDetails['date_to'];
                $orderInformation[$i]['code'] = $orderDetails['genrate_code'];
                $orderInformation[$i]['qty'] = $orderDetails['quantity'];

                $giftcard_information = $objectGiftCard->getProductAmountInfo(
                    $orderInformation[$i]['giftvoucher_id'],
                    $orderInformation[$i]['id_shop']
                );

                if ($giftcard_information) {
                    $orderInformation[$i]['amount'] = Tools::displayPrice($cartRule->reduction_amount, $currencySign);
                    $orderInformation[$i]['type'] = $giftcard_information['point_type'];
                }
                ++$i;
                $orderHtml = $objectCardAllInfo->getWkPsEmailTemplateContent(
                    'wk_voucher_details_mail.tpl',
                    Mail::TYPE_HTML,
                    $orderInformation
                );
                if (!empty($appliedProducts)) {
                    $productHtml = $objectCardAllInfo->getWkPsEmailTemplateContent(
                        'wk_voucher_product_mail.tpl',
                        Mail::TYPE_HTML,
                        $appliedProducts
                    );
                } else {
                    $productHtml = '';
                }
                $prodObj = new Product((int) $idProduct);
                $linkRewrite = $prodObj->link_rewrite[$langId];
                $img = $prodObj->getCover($prodObj->id);
                if (isset($img['id_image'])) {
                    $voucherProductImg = $this->context->link->getImageLink($linkRewrite, $img['id_image']);
                } else {
                    $voucherProductImg = '';
                }
                $mailVars = [
                    '{shop_logo}' => $shopLogo,
                    '{shop_url}' => $shopUrl,
                    '{shop_name}' => $shopName,
                    '{order_html}' => $orderHtml,
                    '{product_html}' => $productHtml,
                    '{recipient_name}' => $recipientName,
                    '{from_name}' => $fromName,
                    '{message}' => $message,
                    '{voucher_product_img}' => $voucherProductImg,
                ];

                $fileAttachement = [];
                if (Configuration::get('WK_GIFT_CARD_SEND_PDF_IN_EMAIL')) {
                    $fileName = $this->generateVoucherPDF($idOrder, $recipientName, $message, $orderInformation, $productHtml, $fromName, $idProduct);
                    $filePath = _PS_ROOT_DIR_ . '/modules/wkgiftcard/views/voucher/' . $fileName;
                    if (file_exists($filePath)) {
                        $fileAttachement[0]['content'] = Tools::file_get_contents($filePath);
                        $fileAttachement[0]['name'] = $fileName;
                        $fileAttachement[0]['invoice']['mime'] = 'application/pdf';
                        $fileAttachement[0]['mime'] = 'application/pdf';
                    }
                }
                $templatePath = _PS_MODULE_DIR_ . 'wkgiftcard/mails/';
                if ($email) {
                    $wkMailSend = Mail::Send(
                        (int) $langId,
                        'voucher_creation_mail_to_customer',
                        Mail::l('Votre carte cadeau de la part de ', (int) $langId) . $fromName,
                        $mailVars,
                        $email,
                        null,
                        null,
                        null,
                        $fileAttachement,
                        null,
                        $templatePath,
                        false,
                        null,
                        null,
                        null
                    );
                }
            }
        }
        if (isset($wkMailSend)) {
            return true;
        }

        return false;
    }

    public function generateVoucherPDF($idOrder, $recipientName, $message, $orderInformation, $productHtml, $fromName, $idProduct = false)
    {
        // start pdf code
        $orientation = 'L';
        $pdfRenderer = new WkPDFGeneratorVoucher((bool) Configuration::get('PS_PDF_USE_CACHE'), $orientation);
        $this->context->smarty->escape_html = false;
        $pdfRenderer->setFontForLang(Context::getContext()->language->iso_code);
        $pdfRenderer->createHeader('');
        $pdfRenderer->createContent($this->getPDFContent($recipientName, $message, $orderInformation, $productHtml, $fromName, $idProduct));
        $pdfRenderer->writePage();
        $fileName = 'voucher_' . $idOrder . '.pdf';
        $filePath = _PS_ROOT_DIR_ . '/modules/wkgiftcard/views/voucher/' . $fileName;
        $pdfFile = $pdfRenderer->render($fileName, false);
        file_put_contents($filePath, $pdfFile);

        return $fileName;
        // end
    }

    public function install()
    {
        $status = [2, 5];
        if (!parent::install()
            || !$this->giftCardDb->createTables()
            || !$this->callInstallTab()
            || !$this->callGiftCardMenu()
            || !$this->registerModuleHooks()
            || !Configuration::updateValue('WK_GIFT_CARD_PREFIX', 'GC-')
            || !Configuration::updateValue('WK_GIFT_CARD_ORDER_STATUS', json_encode($status))
        ) {
            return false;
        }

        return true;
    }

    /**
     * Register hooks for module
     *
     * @return bool
     */
    public function registerModuleHooks()
    {
        return $this->registerHook([
            'actionValidateOrder',
            'actionOrderStatusUpdate',
            'displayFooterProduct',
            'actionFrontControllerSetMedia',
            'displayHeader',
            'displayProductListReviews',
            'actionAdminControllerSetMedia',
            'actionMetaPageSave',
            'displayOverrideTemplate',
            'displayAfterProductVariant',
            'displayAfterCartProductLine',
            'displayAfterProductName',
            'displayProductPriceBlock',
            'actionCustomerAccountAdd',
            'displayAdminOrderMain',
        ]);
    }

    public function hookActionMetaPageSave($params)
    {
        WkGiftCardMenuHelper::removeTopMenuGiftCard();
        WkGiftCardMenuHelper::addTopMenuGiftCard();

        return true;
    }

    /**
     * For creating auction menu in front end.
     *
     * @return bool
     */
    public function callGiftCardMenu()
    {
        if (Module::isEnabled('ps_mainmenu')) {
            Configuration::updateValue('WK_GIFTCARD_MENU', 1);
            WkGiftCardMenuHelper::addTopMenuGiftCard();
        } else {
            Configuration::updateValue('WK_GIFTCARD_MENU', 0);
        }

        return true;
    }

    /**
     * Enable gift card Menu depending on Configuration variables
     *
     * @return bool
     */
    public function enable($force_all = false)
    {
        if ($force_all) {
        }
        Configuration::updateValue('WK_GIFT_CARD_MENU', 1);
        WkGiftCardMenuHelper::displayTopMenuGiftCard();

        if (parent::enable() == false) {
            return false;
        }

        return true;
    }

    /**
     * Disable gift card Menu depending on Configuration variables
     *
     * @return bool
     */
    public function disable($force_all = false)
    {
        if ($force_all) {
        }
        Configuration::updateValue('WK_GIFT_CARD_MENU', 0);
        WkGiftCardMenuHelper::displayTopMenuGiftCard();

        if (parent::disable() == false) {
            return false;
        }

        return true;
    }

    public function hookActionAdminControllerSetMedia()
    {
        Media::addJsDef(
            [
                'confirm_title' => $this->l('Confirm'),
                'confirm_description' => $this->l('Are you sure you want to delete image.'),
            ]
        );
        if ('AdminOrders' == Tools::getValue('controller')) {
            $idOrder = 0;
            $containsOption = 0;

            if (_PS_VERSION_ >= '1.7.7.0') {
                $uri = $_SERVER['REQUEST_URI']; // it will get full url
                $uriArray = explode('/', $uri); // convert string into array with explode
                $orderIndex = 0;
                foreach ($uriArray as $index => $string) {
                    if (strpos($string, '?_token') !== false) {
                        $orderIndex = $index;
                    }
                }
                if ($orderIndex) {
                    $idOrder = $uriArray[$orderIndex - 1];
                }
                $isSymfonyContext = 1;
            } else {
                $idOrder = Tools::getValue('id_order');
                $isSymfonyContext = 0;
            }
            if ($idOrder) {
                Media::addJsDef(
                    [
                        'wk_symfony_context' => $isSymfonyContext,
                        'id_order' => $idOrder,
                        'wk_gift_card_controller' => $this->context->link->getAdminLink(
                            'AdminGiftCard'
                        ),
                    ]
                );
                $this->context->controller->addJS(
                    [
                        $this->_path . 'views/js/admin/wk_admin_order_page.js',
                    ]
                );
            }
        }

        if (Tools::getValue('configure') == $this->name || Tools::getValue('controller') == 'AdminGiftCardInfo') {
            $this->context->controller->addJS($this->_path . 'views/js/wk_gift_card_config.js');
            $this->context->controller->addCSS($this->_path . 'views/css/wk_gift_card_config.css');
        }
    }

    /**
     * [hookDisplayProductListReviews -> Hide price for dynamic gift card product].
     *
     * @return [html] [Hide price for dynamic gift card]
     */
    public function hookDisplayProductListReviews($params)
    {
        if (isset($params['product']['id_product'])) {
            $idProduct = $params['product']['id_product'];
            $this->context->smarty->assign(
                [
                    'wk_dynamic_gift_pro_id' => $idProduct,
                ]
            );
            if (WkGiftCardObj::isDynamicPriceProduct($idProduct, $this->context->shop->id)) {
                return $this->fetch('module:wkgiftcard/views/templates/hook/hideprice.tpl') . $this->fetch('module:wkgiftcard/views/templates/hook/hidewishlist.tpl');
            } else {
                $wkGiftCardObj = new WkGiftCardObj();
                if ($wkGiftCardObj->checkVoucherIdByProductId($idProduct)) {
                    return $this->fetch('module:wkgiftcard/views/templates/hook/hidewishlist.tpl');
                }
            }
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        $controller = Tools::getValue('controller');
        if (empty($controller)) {
            $controller = $this->context->controller->php_self;
        }
        if ('product' == $controller
            || 'index' == $controller
            || 'category' == $controller
            || 'cart' == $controller
            || 'order' == $controller
            || 'giftcardlist' == $controller
        ) {
            $this->context->controller->addJqueryPlugin('growl', null, false);
            $this->context->controller->registerStylesheet('growl-css', 'js/jquery/plugins/growl/jquery.growl.css');
            Media::addJsDefL('invalidPriceErr', $this->l('Invalid price.'));
            Media::addJsDefL('nogiftcardavailable', $this->l('No gift card available.'));
            Media::addJsDefL('wk_enter_name', $this->l('Please enter to name.'));
            Media::addJsDefL('wk_enter_from_name', $this->l('Please enter from name.'));
            Media::addJsDefL('wk_enter_email', $this->l('Please enter to email.'));
            Media::addJsDefL('invalidmessagelength', $this->l('Only ') . $this->message_allowed_char . $this->l(' character are allowed in message.'));
            Media::addJsDefL('wk_gift_card_token', Tools::getToken(false));
            Media::addJsDefL('wkGiftCartLink', $this->context->link->getModuleLink('wkgiftcard', 'GiftCardProductCartActions'));
            Media::addJsDefL('wkGiftCartListLink', $this->context->link->getModuleLink('wkgiftcard', 'giftcardlist'));
            Media::addJsDefL('message_allowed_char', $this->message_allowed_char);
            if (Configuration::get('WK_GIFT_CARD_DISPLAY_DELIVERY_DATE')) {
                $this->context->controller->registerJavascript(
                    'datepicker-i18n.js',
                    'js/jquery/ui/i18n/jquery-ui-i18n.js',
                    ['position' => 'bottom', 'priority' => 999]
                );
            }

            $this->context->controller->registerJavascript(
                'wk-gift-dynamic-price',
                'modules/' . $this->name . '/views/js/wk_gift_card.js',
                ['position' => 'bottom', 'priority' => 999]
            );
        }

        if ('order' === $this->context->controller->php_self || 'orderopc' === $this->context->controller->php_self) {
            // On checkout page order final summary tab
            if (Configuration::get('PS_FINAL_SUMMARY_ENABLED')) {
                // If final summary is showing on checkout page (payment tab)
                Media::addJsDef([
                    'static_token' => Tools::getToken(false),
                    'giftCardAjaxURL' => $this->context->link->getModuleLink(
                        'wkgiftcard',
                        'GiftCardProductCartActions'
                    ),
                ]);
                $this->context->controller->registerJavascript(
                    'module-gift-card-wk-checkout-order-item-js',
                    'modules/' . $this->name . '/views/js/wk_checkout_order_summary_item.js',
                    ['position' => 'bottom', 'priority' => 999]
                );
            }
        }
    }

    public function callInstallTab()
    {
        $this->installTab('AdminGiftCardInfo', 'Gift Card Orders', 'AdminParentOrders');
        $this->installTab('AdminGiftCard', 'Gift Cards', 'AdminCatalog');

        return true;
    }

    public function installTab($class_name, $tab_name, $tab_parent_name = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = [];

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }

        if ($tab_parent_name) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tab_parent_name);
        } else {
            $tab->id_parent = 0;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->uninstallTab()
            || !$this->giftCardDb->deleteTables()
            || !$this->deleteConfigKeys()
        ) {
            return false;
        }

        return true;
    }

    public function deleteConfigKeys()
    {
        if (Module::isEnabled('ps_mainmenu')) {
            WkGiftCardMenuHelper::removeTopMenuGiftCard();
        }

        $var = [
            'WK_GIFT_CARD_PREFIX',
            'WK_GIFT_CARD_ORDER_STATUS',
            'WK_GIFT_CARD_DISPLAY_DELIVERY_DATE',
            'WK_GIFT_CARD_SEND_PDF_IN_EMAIL',
            'WK_GIFT_CARD_BANNER_IMAGE',
            'WK_GIFT_CARD_ASSOCIATE_CUSTOMER_EMAIL_TO_VOUCHER',
            'WK_GIFT_CARD_PRODUCT_PDF_IMG',
        ];
        foreach ($var as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }
}
