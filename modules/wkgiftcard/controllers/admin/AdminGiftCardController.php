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

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;

class AdminGiftCardController extends ModuleAdminController
{
    public $defaultLang = '';

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->defaultLang = Configuration::get('PS_LANG_DEFAULT');
        $this->bootstrap = true;
        $this->className = 'WkGiftCardObj';
        $this->table = 'wk_gift_card';
        $this->identifier = 'id';

        parent::__construct();

        Shop::addTableAssociation('wk_gift_card', ['type' => 'shop']);
        $this->_join .= WkGiftCardObj::addSqlAssociationCustom(
            'wk_gift_card',
            'a',
            true,
            null,
            false,
            $this->identifier
        );
        $this->toolbar_title = $this->l('Gift Card');
        $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = a.`id_voucher_product`)';
        $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.`id_product` = a.`id_voucher_product` AND pl.`id_lang`=' .
        (int) $this->context->language->id . ' AND pl.`id_shop`=' .
        (int) $this->context->shop->id . ')';
        $this->_join .= '
		LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sav ON (sav.`id_product` = a.`id_voucher_product` AND sav.`id_product_attribute` = 0
		) ';
        $this->_select = 'pl.`link_rewrite` as link_rewrite, pl.`name` as name, pl.`id_product` as temp_ps_id, pl.`id_product` as temp_ps_id2, wk_gift_card_shop.`valid_upto`, wk_gift_card_shop.`active`, sav.`quantity` AS `sav_quantity`';
        $this->_select .= ',shp.`name` as wk_ps_shop_name, shp.`id_shop` as shopID';
        $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'shop` shp ON (shp.`id_shop` = wk_gift_card_shop.`id_shop`)';
        $this->_group = ' GROUP BY a.id';
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->addRowAction('edit');
        }
        $this->addRowAction('delete');
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash',
            ],
        ];

        $this->fields_list = [
            'id' => [
                'title' => $this->l('ID'),
                'align' => 'center',
            ],
            'temp_ps_id2' => [
                'title' => $this->l('Image'),
                'align' => 'center',
                'callback' => 'displayProductImage',
                'search' => false,
                'havingFilter' => true,
            ],
            'name' => [
                'title' => $this->l('Name'),
                'align' => 'center',
                'filter_key' => 'pl!name',
            ],
            'valid_upto' => [
                'title' => $this->l('Valid upto'),
                'align' => 'center',
                'filter_key' => 'wk_gift_card_shop!valid_upto',
            ],
            'sav_quantity' => [
                'title' => $this->l('Quantity'),
                'align' => 'center',
                'filter_key' => 'sav!quantity',
                'orderby' => true,
            ],
            'active' => [
                'title' => $this->l('Status'),
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'filter_key' => 'wk_gift_card_shop!active',
            ],
            'temp_ps_id' => [
                'title' => $this->l('Preview'),
                'align' => 'center',
                'search' => false,
                'remove_onclick' => true,
                'callback' => 'previewProduct',
                'orderby' => false,
            ],
        ];
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            unset($this->fields_list['active']);
            $this->list_no_link = true;
            $this->fields_list['wk_ps_shop_name'] = [
                'title' => $this->l('Shop'),
                'align' => 'center',
                'havingFilter' => true,
            ];
        }
    }

    public function displayProductImage($idPsProduct)
    {
        if ($idPsProduct) {
            $product = new Product($idPsProduct, false, $this->context->language->id);
            $cover = Product::getCover($idPsProduct);
            if ($cover) {
                $coverImage = $idPsProduct . '-' . $cover['id_image'];
                $imageTypeName = ImageType::getFormattedName('small');
                $imageLink = $this->context->link->getImageLink($product->link_rewrite, $coverImage, $imageTypeName);
                if ($imageLink) {
                    $this->context->smarty->assign('image_src', $imageLink);

                    return $this->context->smarty->fetch(
                        _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/display_product_image.tpl'
                    );
                }
            }

            return '';
        }
    }

    public function initContent()
    {
        if (($this->display == 'edit') && (Shop::getContext() != Shop::CONTEXT_SHOP)) {
            if (!$this->loadObject(true)) {
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
            }
        }

        return parent::initContent();
    }

    public function processDelete()
    {
        if (Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->errors[] = $this->l('You cannot delete in this shop context. Select a shop instead of group of shops.');
        } else {
            if (Validate::isLoadedObject($object = $this->loadObject())) {
                $object = $this->loadObject();
                if ($object->id) {
                    $objProduct = new Product($object->id_voucher_product);
                    if (!$objProduct->delete()) {
                        $this->errors[] = $this->l('An error occurred while deleting product.');
                    }
                }
            } else {
                // $this->errors[] = $this->l('An error occurred while deleting object.') .
                //     ' ' . $this->table . ' ' . $this->l('(can not load object)');
            }

            return parent::processDelete();
        }

        return false;
    }

    protected function processBulkDelete()
    {
        if (Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->errors[] = $this->l('You cannot delete in this shop context. Select a shop instead of group of shops.');
        } else {
            if (is_array($this->boxes) && !empty($this->boxes)) {
                foreach ($this->boxes as $value) {
                    $giftCardInfo = new WkGiftCardObj($value);
                    $objProduct = new Product($giftCardInfo->id_voucher_product);
                    if (!$objProduct->delete()) {
                        $this->errors[] = $this->l('An error occurred while deleting product.');
                    }
                }

                return parent::processBulkDelete();
            } else {
                $this->errors[] = $this->l('You must select at least one element to delete.');

                return false;
            }
        }

        return false;
    }

    public function processStatus()
    {
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            if ($object->id) {
                $objProduct = new Product($object->id_voucher_product);
                $objProduct->toggleStatus();
            }
        } else {
            // $this->errors[] = $this->l('An error occurred while updating status for an object.') .
            // ' ' . $this->table . ' ' . $this->l('(can not load object)');
        }

        return parent::processStatus();
    }

    protected function processBulkEnableSelection()
    {
        return $this->processBulkStatusSelection(1);
    }

    protected function processBulkDisableSelection()
    {
        return $this->processBulkStatusSelection(0);
    }

    protected function processBulkStatusSelection($status)
    {
        $result = true;
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                $giftCardInfo = new WkGiftCardObj($id);
                if (Validate::isLoadedObject($giftCardInfo)) {
                    $giftCardInfo->active = (int) $status;
                    $isUpdated = (bool) $giftCardInfo->update();
                    $result &= $isUpdated;
                    if (!$isUpdated) {
                        $this->errors[] = str_replace(
                            '%id%',
                            (int) $id,
                            $this->l(
                                'Can\'t update %id% status.'
                            )
                        );
                    } else {
                        $objProduct = new Product($giftCardInfo->id_voucher_product);
                        $objProduct->active = $status;
                        $objProduct->save();
                    }
                }
            }
            if ($result) {
                $this->redirect_after = self::$currentIndex . '&conf=5&token=' . $this->token;
            } else {
                $this->errors[] = $this->l('An error occurred while updating the status.');
            }
        } else {
            $this->errors[] = $this->l('You must select at least one item to perform a bulk action.');
        }

        return $result;
    }

    public function previewProduct($idProduct, $rowData)
    {
        if ($idProduct) {
            $idLang = $this->context->language->id;
            $product = new Product($idProduct);
            $isRewriteActive = (bool) Configuration::get('PS_REWRITING_SETTINGS');
            $idShop = (int) Context::getContext()->shop->id;
            if (isset($rowData['shopID']) && $rowData['shopID']) {
                $idShop = (int) $rowData['shopID'];
            }

            $productPreviewLink = $this->context->link->getProductLink(
                $product,
                $this->getFieldValue($product, 'link_rewrite', $idLang),
                Category::getLinkRewrite($this->getFieldValue($product, 'id_category_default'), $idLang),
                null,
                $idLang,
                (int) $idShop,
                0,
                $isRewriteActive
            );
            $idTab = Tab::getIdFromClassName('AdminProducts');
            $token = Tools::getAdminToken('AdminProducts' . (int) $idTab . (int) $this->context->employee->id);
            if (!$product->active) {
                $adminDir = dirname($_SERVER['PHP_SELF']);
                $adminDir = Tools::substr($adminDir, strrpos($adminDir, '/') + 1);
                $productPreviewLink .= ((strpos($productPreviewLink, '?') === false) ? '?' : '&') . 'adtoken=' . $token .
                '&ad=' . $adminDir . '&id_employee=' . (int) $this->context->employee->id;
            }
            $context = Context::getContext();
            $context->smarty->assign('productPreviewLink', $productPreviewLink);
            $tpl = $context->smarty->fetch(
                _PS_MODULE_DIR_ . 'wkgiftcard/views/templates/admin/preview.tpl'
            );

            return $tpl;
        }
    }

    /**
     * Show button in header (add new gift card)
     *
     * @return void
     */
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_btn['new'] = [
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->l('New gift card'),
            'icon' => 'process-icon-new',
        ];
        $this->page_header_toolbar_btn['view'] = [
            'href' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->module->name,
            'desc' => $this->l('Module configuration'),
            'icon' => 'process-icon-cogs',
        ];
    }

    public function renderForm()
    {
        if (($this->display == 'edit') && (Shop::getContext() != Shop::CONTEXT_SHOP) && $this->object->id) {
            return $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/shop_warning.tpl'
            );
        } else {
            $idTable = 0;
            if ($this->display == 'edit') {
                $giftCard = $this->object;
                $idTable = $this->object->id;
                if (Validate::isLoadedObject($giftCard)) {
                    $pack_items = [];
                    $idProduct = $giftCard->id_voucher_product;
                    $getProductInfoDifferentidArray = WkGiftCardProduct::getAllByGiftCardID($idTable);
                    if ($getProductInfoDifferentidArray) {
                        foreach ($getProductInfoDifferentidArray as $value) {
                            if ($value['id_product']) {
                                $cover_img = Image::getCover($value['id_product']);
                                $obj_product = new Product($value['id_product']);
                                $product_details = $giftCard->getProductDetailById($value['id_product'], $this->context->shop->id);
                                if ($product_details) {
                                    if (is_array($cover_img)) {
                                        $image = $this->context->link->getImageLink(
                                            $product_details['link_rewrite'],
                                            $cover_img['id_image'],
                                            ImageType::getFormattedName('home')
                                        );
                                    } else {
                                        $imageRetriever = new ImageRetriever($this->context->link);
                                        $image = $imageRetriever->getNoPictureImage($this->context->language)['bySize'][ImageType::getFormattedName('home')]['url'];
                                    }
                                    $product = [
                                        'id' => (int) $value['id_product'],
                                        'name' => $product_details['name'],
                                        'ref' => $obj_product->reference,
                                        'quantity' => 1,
                                        'image' => $image,
                                    ];
                                    array_push($pack_items, $product);
                                }
                            }
                        }
                    }
                    $objProduct = new Product($idProduct, true, null);
                    $giftCard->amount = number_format($giftCard->amount, 2);
                    $cover = Product::getCover($idProduct);
                    if ($cover) {
                        $coverImage = $idProduct . '-' . $cover['id_image'];
                        $imageTypeName = ImageType::getFormattedName('small');
                        $imageLink = $this->context->link->getImageLink($objProduct->link_rewrite[$this->context->language->id], $coverImage, $imageTypeName);
                        if ($imageLink) {
                            $this->context->smarty->assign('gift_image_src', $imageLink);
                        }
                    }
                    $defaultIdCategory = $objProduct->getDefaultCategory();
                    $idCategory = [];
                    $checkedProductCategory = Product::getProductCategories($idProduct);
                    if ($checkedProductCategory) {
                        // Default category
                        foreach ($checkedProductCategory as $checkIdCategory) {
                            $idCategory[] = $checkIdCategory;
                        }

                        $catIdsJoin = implode(',', $idCategory);
                        $this->context->smarty->assign('catIdsJoin', $catIdsJoin);
                    }
                    $defaultCategory = Category::getCategoryInformation($idCategory, $this->context->language->id);
                    $this->context->smarty->assign(
                        [
                            'defaultCategory' => $defaultCategory,
                            'defaultIdCategory' => $defaultIdCategory,
                            'giftCardObj' => $giftCard,
                            'giftCardProduct' => $objProduct,
                            'edit' => 1,
                            'pack_items' => $pack_items,
                            'product_quanity' => StockAvailable::getQuantityAvailableByProduct($idProduct),
                            'catConIdsJoin' => $giftCard->is_category_restriction ? $giftCard->allow_categories : '',
                        ]
                    );
                }
            }
            $currentLangId = Configuration::get('PS_LANG_DEFAULT');
            $adminCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
            $maxUpload = (int) ini_get('upload_max_filesize');
            $maxPost = (int) ini_get('post_max_size');
            $uploadMb = min($maxUpload, $maxPost);
            $imageSize = str_replace(
                '%upload_mb%',
                (int) $uploadMb,
                $this->l(
                    'maximum size can be %upload_mb% MB.'
                )
            );
            // show tax rule group on add product page
            $taxRuleGroups = TaxRulesGroup::getTaxRulesGroups(true);
            $this->context->smarty->assign(
                [
                    'idGiftCard' => $idTable,
                    'languages' => Language::getLanguages(false),
                    'currentLang' => Language::getLanguage((int) $currentLangId),
                    'total_languages' => count(Language::getLanguages(false)),
                    'imageSize' => $imageSize,
                    'adminCurrency' => $adminCurrency,
                    'tax_rules_groups' => $taxRuleGroups,
                ]
            );
            $this->fields_form = [
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ];

            return parent::renderForm();
        }
    }

    public function ajaxProcessProductCategory()
    {
        // Load Prestashop category with ajax load of plugin jstree
        WkGiftCardObj::getWkGiftCardProductCategory();
    }

    public function postProcess()
    {
        $id = Tools::getValue('idTable');
        if ($id) {
            $giftCardObj = new WkGiftCardObj($id);
            $idGiftCardProduct = $giftCardObj->id_voucher_product;
        } else {
            $giftCardObj = new WkGiftCardObj();
            $idGiftCardProduct = 0;
        }

        if (Tools::isSubmit('submitAddwk_gift_cardAndStay')
            || Tools::isSubmit('submitAddwk_gift_card')
        ) {
            $this->validateGiftCardDetails();

            if (!count($this->errors)) {
                if ($idProduct = $this->saveGiftCardProduct($idGiftCardProduct)) {
                    if (0 != $_FILES['gift_image']['size']) {
                        $source = $_FILES['gift_image']['tmp_name'];
                        WkGiftCardObj::updatePsProductImage($idProduct, $source);
                    }
                    $giftCardObj->id_voucher_product = (int) $idProduct;
                    if ($id) {
                        $oldDynamicPrice = $giftCardObj->dynamic_price;
                        if ($oldDynamicPrice != Tools::getValue('dynamic_price')) {
                            // delete cart on update dynamic price value because product price will occur issue if any customer is added in cart
                            WkGiftCardCart::deleteCurrentCustomerCarts($idProduct);
                        }
                    }
                    if (Tools::getValue('dynamic_price')) {
                        $giftCardObj->amount = 0;
                    } else {
                        $giftCardObj->amount = Tools::getValue('price');
                    }
                    $giftCardObj->dynamic_price = (int) Tools::getValue('dynamic_price');
                    $giftCardObj->together = (int) Tools::getValue('every_quantity');
                    $giftCardObj->free_shipping = (int) Tools::getValue('free_shipping');
                    $giftCardObj->active = (int) Tools::getValue('active');
                    $giftCardObj->point_type = 'amount';
                    $giftCardObj->valid_upto = (int) Tools::getValue('valid_upto');
                    $giftCardObj->choose_product = (int) Tools::getValue('is_select_product');
                    $giftCardObj->is_category_restriction = Tools::getValue('is_select_category');
                    $giftCardObj->allow_categories = trim(Tools::getValue('product_category_condition'));
                    if ($giftCardObj->save()) {
                        $giftCardProductAttach = new WkGiftCardProduct();
                        $giftCardProductAttach->deleteAllAttchedProduct($giftCardObj->id);
                        if (Tools::getValue('is_select_product')) {
                            $ps_wk_products = Tools::getValue('pspk_id_prod');
                            if ($ps_wk_products) {
                                $ps_wk_products = array_unique($ps_wk_products);
                                foreach ($ps_wk_products as $product_value) {
                                    $giftCardProductAttach = new WkGiftCardProduct();
                                    $giftCardProductAttach->id_wk_gift_card = (int) $giftCardObj->id;
                                    $giftCardProductAttach->id_product = (int) $product_value;
                                    $giftCardProductAttach->save();
                                }
                            }
                        }
                        if (Tools::isSubmit('submitAddwk_gift_cardAndStay')) {
                            if ($id) {
                                Tools::redirectAdmin(
                                    self::$currentIndex . '&conf=4&token=' . $this->token . '&id=' . $giftCardObj->id .
                                    '&updatewk_gift_card'
                                );
                            } else {
                                Tools::redirectAdmin(
                                    self::$currentIndex . '&conf=3&token=' . $this->token . '&id=' . $giftCardObj->id .
                                    '&updatewk_gift_card'
                                );
                            }
                        } else {
                            if ($id) {
                                Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
                            } else {
                                Tools::redirectAdmin(self::$currentIndex . '&conf=3&token=' . $this->token);
                            }
                        }
                    } else {
                        $this->errors[] = $this->l('Some error has been occurred while saving gift card details info.');
                    }
                }
            } else {
                $this->display = 'edit';
            }
        }
        parent::postProcess();
    }

    public function saveGiftCardProduct($id = 0)
    {
        if ($id) {
            $product = new Product($id);
        } else {
            $product = new Product();
        }

        $giftCardName = [];
        $groupShops = [];
        // Add Product
        $product->name = [];
        $product->description_short = [];
        $product->link_rewrite = [];
        $selectedLangId = Configuration::get('PS_LANG_DEFAULT');
        foreach (Language::getLanguages(false) as $lang) {
            $product->name[$lang['id_lang']] = trim(Tools::getValue(
                'gift_card_name_' . $lang['id_lang']
            ));
            if (empty($product->name[$lang['id_lang']])) {
                $product->name[$lang['id_lang']] = trim(Tools::getValue(
                    'gift_card_name_' . $selectedLangId
                ));
            }
            $product->description_short[$lang['id_lang']] = trim(Tools::getValue(
                'description_short' . $lang['id_lang']
            ));
            if (Tools::getValue(
                'gift_card_name_' . $lang['id_lang']
            )) {
                $product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite(Tools::getValue(
                    'gift_card_name_' . $lang['id_lang']
                ));
            } else {
                $product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite(Tools::getValue(
                    'gift_card_name_' . $selectedLangId
                ));
            }

            $giftCardName[$lang['id_lang']] = $this->module->l('Gift card');
        }

        $quantity = Tools::getValue('gift_card_quantity');
        $product->id_shop_default = Context::getContext()->shop->id;
        $product->id_category_default = Tools::getValue('default_category');
        if (Tools::getValue('dynamic_price')) {
            $product->price = 0;
        } else {
            $product->price = Tools::getValue('price');
        }
        $product->active = Tools::getValue('active');
        $product->indexed = 1;
        $product->is_virtual = 1;
        $product->quantity = $quantity;
        $product->id_tax_rules_group = Tools::getValue('id_tax_rules_group');
        $product->reference = trim(Tools::getValue('gift_card_reference'));
        $product->ean13 = trim(Tools::getValue('gift_card_ean_13'));
        $product->upc = trim(Tools::getValue('gift_card_upc_barcode'));
        $product->customizable = 1;
        $product->text_fields = 1;

        if ($product->save()) {
            if (!$product->getCustomizationFieldIds()) {
                $objCustomizationField = new CustomizationFieldCore();
                $objCustomizationField->id_product = $product->id;
                $objCustomizationField->type = 1;
                $objCustomizationField->required = 0;
                $objCustomizationField->name = $giftCardName;
                $objCustomizationField->save();
            }

            $categoryIds = Tools::getValue('product_category');
            $categoryIds = explode(',', $categoryIds);
            $categoryIds = array_unique($categoryIds);
            if ($id) {
                $product->updateCategories($categoryIds);
            } else {
                $product->addToCategories($categoryIds);
                // set default Use default behavior (Deny orders) for product
                StockAvailable::setProductOutOfStock((int) $product->id, 2, $this->context->shop->id);
            }
            if ($quantity >= 0) {
                if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                    StockAvailable::setQuantity($product->id, null, $quantity);
                } else {
                    // if group then update in current group shop only
                    if (Shop::getContext() == Shop::CONTEXT_GROUP) {
                        $groupShops = Shop::getShops(true, $this->context->shop->id_shop_group);
                    }
                    // if all then update in all shop
                    if (Shop::getContext() == Shop::CONTEXT_ALL) {
                        $groupShops = Shop::getShops(true);
                    }
                    if ($groupShops) {
                        $oldContext = Shop::getContext();
                        $currentShop = Context::getContext()->shop;
                        foreach ($groupShops as $groupShop) {
                            $shop = Context::getContext()->shop;
                            // Important to setContext
                            Shop::setContext($shop::CONTEXT_SHOP, $shop->id);
                            $this->context->shop = $shop;
                            $this->context->cookie->id_shop = $shop->id;
                            StockAvailable::setQuantity($product->id, null, $quantity, $groupShop['id_shop']);
                        }
                        // Important to setContext
                        Shop::setContext($oldContext, $currentShop->id);
                        $this->context->shop = $currentShop;
                        $this->context->cookie->id_shop = $currentShop->id;
                    }
                }
            }
            Tag::deleteTagsForProduct($product->id);
            foreach (Language::getLanguages(false) as $lang) {
                Search::indexation($product->link_rewrite[$lang['id_lang']], $product->id);
                $tagList = Tools::getValue('wkgift_tags_' . $lang['id_lang']);
                if (!empty($tagList)) {
                    Tag::addTags($lang['id_lang'], $product->id, $tagList);
                }
            }

            return $product->id;
        }

        return false;
    }

    public function validateGiftCardDetails()
    {
        $nameMultiLang = [];
        $shortdescriptionMultiLang = [];
        $langulages = Language::getLanguages(false);
        $currentLangId = Configuration::get('PS_LANG_DEFAULT');
        foreach ($langulages as $langulage) {
            $nameMultiLang['name' . $langulage['id_lang']] = trim(Tools::getValue(
                'gift_card_name_' . $langulage['id_lang']
            ));
            $shortdescriptionMultiLang['description_short' . $langulage['id_lang']] = trim(Tools::getValue(
                'description_short' . $langulage['id_lang']
            ));
            if ($langulage['id_lang'] == $currentLangId) {
                if (empty(trim($nameMultiLang['name' . $langulage['id_lang']]))) {
                    $this->errors[] = $this->l('Name is required in default language ') . $langulage['name'];
                }
            }
            if (!Validate::isCatalogName($nameMultiLang['name' . $langulage['id_lang']])) {
                $this->errors[] = sprintf(
                    $this->l('Name must not have invalid characters <>;=#{} in %s language.'),
                    $langulage['name']
                );
            } elseif (strlen($nameMultiLang['name' . $langulage['id_lang']]) > 128) {
                $this->errors[] = sprintf(
                    $this->l('Name should have 128 character or less in %s language.'),
                    $langulage['name']
                );
            }
            if (!Validate::isTagsList(Tools::getValue('wkgift_tags_' . $langulage['id_lang']))) {
                $this->errors[] = sprintf(
                    $this->l('Tags is invalid in %s language.'),
                    $langulage['name']
                );
            }
        }
        $dynamicPrice = Tools::getValue('dynamic_price');
        $productPrice = Tools::getValue('price');
        $validUpto = Tools::getValue('valid_upto');
        $productQuantity = Tools::getValue('gift_card_quantity');
        $productCategory = Tools::getValue('product_category');
        $productReference = trim(Tools::getValue('gift_card_reference'));
        $productUPCBarcode = trim(Tools::getValue('gift_card_upc_barcode'));
        $productEAN13 = trim(Tools::getValue('gift_card_ean_13'));
        $is_select_category = Tools::getValue('is_select_category');
        $allowed_product_category = Tools::getValue('product_category_condition');

        // check category exist or not before save
        $categoryIds = explode(',', $productCategory);
        foreach ($categoryIds as $key => $categoryId) {
            $categoryObj = new Category((int) $categoryId);
            if (!Validate::isLoadedObject($categoryObj)) {
                unset($categoryIds[$key]);
            }
        }
        $productCategory = implode(',', $categoryIds);
        if ($shortdescriptionMultiLang) {
            $limit = (int) Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
            if ($limit <= 0) {
                $limit = 400;
            }
            foreach ($shortdescriptionMultiLang as $shortDesc) {
                if (!Validate::isCleanHtml($shortDesc)) {
                    $this->errors[] = $this->l($this->l('Short description is invalid.'));
                }

                if (Tools::strlen(strip_tags($shortDesc)) > $limit) {
                    $this->errors[] = sprintf(
                        $this->l('Short description is too long: %1$d chars max (current count %2$d).'),
                        $limit,
                        Tools::strlen(strip_tags($shortDesc))
                    );
                }
            }
        }

        if ($_FILES['gift_image']['size'] != 0) {
            if (0 == $_FILES['gift_image']['error']) {
                if (!ImageManager::isCorrectImageFileExt($_FILES['gift_image']['name'])) {
                    $this->errors[] = $this->l('Image is invalid. Only jpg, png, jpeg, gif and webp allowed.');
                }
            }
        }

        if ($dynamicPrice != 1) {
            if ($productPrice == '') {
                $this->errors[] = $this->l('Price is required.');
            } elseif (!Validate::isPrice($productPrice)) {
                $this->errors[] = $this->l('Price is invalid.');
            }
        }
        if ($productQuantity == '') {
            $this->errors[] = $this->l('Quantity is required.');
        } elseif (!Validate::isPrice($productQuantity)) {
            $this->errors[] = $this->l('Quantity is invalid.');
        }

        if ($productReference && ($productReference != strip_tags($productReference))) {
            $this->errors[] = $this->l('Reference is invalid.');
        } elseif ($productReference && !preg_match('/^[a-zA-Z0-9_#.-]+$/', $productReference)) {
            $this->errors[] = $this->l('Reference is invalid.');
        }

        if ($productUPCBarcode && ($productUPCBarcode != strip_tags($productUPCBarcode))) {
            $this->errors[] = $this->l('UPC barcode is invalid.');
        } elseif ($productUPCBarcode && !Validate::isInt($productUPCBarcode)) {
            $this->errors[] = $this->l('UPC barcode is invalid.');
        }

        if ($productEAN13 && ($productEAN13 != strip_tags($productEAN13))) {
            $this->errors[] = $this->l('EAN 13 in invalid.');
        } elseif ($productEAN13 && !Validate::isInt($productEAN13)) {
            $this->errors[] = $this->l('EAN 13 is invalid.');
        }

        if ($validUpto == '') {
            $this->errors[] = $this->l('Valid upto is required.');
        } elseif (!Validate::isUnsignedInt($validUpto)) {
            $this->errors[] = $this->l('Valid upto is invalid.');
        }
        if (!$productCategory) {
            $this->errors[] = $this->l('Please select at least one category.');
        }

        if ($is_select_category && !$allowed_product_category) {
            $this->errors[] = $this->l('Please select at least one allowed category.');
        }

        if (Tools::getValue('is_select_product')) {
            $ps_wk_products = Tools::getValue('pspk_id_prod');
            if ($ps_wk_products) {
                $ps_wk_products = array_unique($ps_wk_products);
                foreach ($ps_wk_products as $product_value) {
                    $objProduct = new Product((int) $product_value);
                    if (!Validate::isLoadedObject($objProduct)) {
                        $this->errors[] = $this->l('Product ID') . $product_value . $this->l(' is deleted or unavailable.');
                    }
                }
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addjQueryPlugin('tagify', null, false);
        // tinymce
        Media::addJsDef([
            'ajax_link' => $this->context->link->getAdminLink('AdminGiftCardInfo'),
            'wk_gift_card_token' => $this->module->secure_key,
            'prod_err' => $this->l('Please enter valid product name.'),
            'maxSizeAllowed' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
            'filesizeError' => $this->l('File exceeds maximum size.'),
            'admin_gift_card_url' => $this->context->link->getAdminLink('AdminGiftCard'),
            'current_config_tab' => 'information',
        ]);
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/categorytree/themes/default/style.min.css');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/categorytree/jstree.min.js');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/gift_product.css');
        $this->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
        $this->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/wkgiftsearchproduct.js');
    }

    /**
     * Append product options admin order details product line row
     *
     * @return void
     */
    public function ajaxProcessAppendInProductLine()
    {
        $idOrderDetail = Tools::getValue('id_order_detail');
        $idOrder = Tools::getValue('id_order');
        $id_product = Tools::getValue('id_product');

        if ($idOrder && $idOrderDetail) {
            $order = new Order($idOrder);
            $cart_products = $order->getCartProducts();

            $index = array_search($id_product, array_column($cart_products, 'id_product'));
            if ($index === false) {
                exit;
            }

            $recipient_info = [];
            if ($cart_products) {
                $objGiftCardOrderDetails = new GiftCardOrderDetails();
                $recipient_info = $objGiftCardOrderDetails->getAllInfoByOrderId(
                    $order->id,
                    $this->context->shop->id,
                    $cart_products[$index]['id_customization'],
                );
            }

            if (!empty($recipient_info) && (isset($recipient_info['id_product']) && $recipient_info['id_product'] == $id_product)) {
                exit(json_encode(['data' => true]));
            }
        }
    }
}
