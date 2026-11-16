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

use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;

class WkGiftCardGiftCardProductCartActionsModuleFrontController extends ModuleFrontController
{
    public $display_column_left = '';
    public $display_column_right = '';

    public function initContent()
    {
        $this->display_column_left = false;
        $this->display_column_right = false;
        $action = Tools::getValue('action');
        $result = [];
        $m = $this->module;
        if (isset($action) && $action == 'add_gift_card_product_to_cart') {
            $name = trim(Tools::getValue('name'));
            $fromName = trim(Tools::getValue('from_name'));
            $email = trim(Tools::getValue('email'));
            $message = Tools::getValue('message');
            if (empty(Tools::getValue('date'))) {
                $date = date('Y-m-d');
            } else {
                $date = date('Y-m-d', strtotime(Tools::getValue('date')));
            }
            $price = Tools::getValue('price');
            $quantity = Tools::getValue('quantity');
            $idProduct = Tools::getValue('id_product');
            $isDynamicPriceGiftCard = WkGiftCardObj::isDynamicPriceProduct($idProduct, $this->context->shop->id);
            if ($isDynamicPriceGiftCard != Tools::getValue('is_dynamic_price_gift_card')) {
                $result['reload'] = 1;
            } else {
                $result['reload'] = 0;
            }
            if (!$idProduct) {
                $this->errors[] = $m->l('Product id is missing.', 'GiftCardProductCartActions');
            } elseif (empty($name)) {
                $this->errors[] = $m->l('To name is required.', 'GiftCardProductCartActions');
            } elseif (!Validate::isName($name)) {
                $this->errors[] = $m->l('To name is invalid.', 'GiftCardProductCartActions');
            } elseif (empty($email)) {
                $this->errors[] = $m->l('To email is required.', 'GiftCardProductCartActions');
            } elseif (!Validate::isEmail($email)) {
                $this->errors[] = $m->l('To email is invalid.', 'GiftCardProductCartActions');
            } elseif (empty($fromName)) {
                $this->errors[] = $m->l('From name is required.', 'GiftCardProductCartActions');
            } elseif (!Validate::isName($fromName)) {
                $this->errors[] = $m->l('From name is invalid.', 'GiftCardProductCartActions');
            } elseif (!empty($message) && !Validate::isMessage($message)) {
                $this->errors[] = $m->l('Your message is invalid.', 'GiftCardProductCartActions');
            } elseif (strlen(trim($message)) > $m->message_allowed_char) {
                $this->errors[] = $m->l('Only ', 'GiftCardProductCartActions') . $m->message_allowed_char . $m->l(' character are allowed in message.', 'GiftCardProductCartActions');
            } elseif ($date == '' || !Validate::isDate($date)) {
                $this->errors[] = $m->l('Delivery date is invalid.', 'GiftCardProductCartActions');
            } elseif (!Validate::isUnsignedInt($quantity) || !$quantity) {
                $this->errors[] = $m->l('Invalid quantity.', 'GiftCardProductCartActions');
            }
            if (!Validate::isPrice($price) && $isDynamicPriceGiftCard && !count($this->errors)) {
                $this->errors[] = $m->l('Invalid price.', 'GiftCardProductCartActions');
            }
            $product = new Product($idProduct, true, $this->context->language->id);
            if (!$product->id || !$product->active || !$product->checkAccess($this->context->cart->id_customer)) {
                $this->errors[] = $m->l('This product is no longer available.', 'GiftCardProductCartActions');
            }

            if (!Product::isAvailableWhenOutOfStock($product->out_of_stock)) {
                $availableProductQuantity = StockAvailable::getQuantityAvailableByProduct(
                    $idProduct
                );
                if ($availableProductQuantity < $quantity) {
                    $this->errors[] = $m->l('The available purchase order quantity for this product is ', 'GiftCardProductCartActions') . $availableProductQuantity;
                }
            }
            // if (WkGiftCardCart::getGiftCartProductCartInfo($idProduct, $this->context->cart->id, $this->context->shop->id)) {
            //     $this->errors[] = $m->l('This product is already exists into the cart.', 'GiftCardProductCartActions');
            // }

            if (!count($this->errors)) {
                if (!$this->context->cart->id) {
                    if (Context::getContext()->cookie->id_guest) {
                        $guest = new Guest(Context::getContext()->cookie->id_guest);
                        $this->context->cart->mobile_theme = $guest->mobile_theme;
                    }
                    $this->context->cart->add();
                    if ($this->context->cart->id) {
                        $this->context->cookie->id_cart = (int) $this->context->cart->id;
                    }
                }
                $this->textRecord(new Product($idProduct));
                $customization_datas = $this->context->cart->getProductCustomization($idProduct, null, true);
                $defaultCurrency = Configuration::get('PS_CURRENCY_DEFAULT');

                if ($defaultCurrency != $this->context->currency->id) {
                    $price = Tools::convertPriceFull($price, $this->context->currency);
                }

                $giftCardCartObj = new WkGiftCardCart();
                $giftCardCartObj->id_cart = $this->context->cart->id;
                $giftCardCartObj->id_shop = $this->context->shop->id;
                $giftCardCartObj->id_customization = (int) $customization_datas[0]['id_customization'];
                $giftCardCartObj->id_product = $idProduct;
                $giftCardCartObj->is_dynamic_price_type = $isDynamicPriceGiftCard;
                $giftCardCartObj->recipient_name = trim($name);
                $giftCardCartObj->from_name = trim($fromName);
                $giftCardCartObj->recipient_email = $email;
                $giftCardCartObj->message = trim($message);
                $giftCardCartObj->delivery_date = $date;
                $giftCardCartObj->dynamic_amount = $price;
                if ($giftCardCartObj->save()) {
                    $result['status'] = 'ok';
                } else {
                    $result['status'] = 'ko';
                    $result['errors'] = $m->l('Something went wrong.', 'GiftCardProductCartActions');
                }
            } else {
                $result['status'] = 'ko';
                $result['errors'] = $this->errors;
            }
            exit(json_encode($result));
        } elseif (isset($action) && $action == 'delete_gift_card_product_to_cart') {
            $idProduct = Tools::getValue('id_product');
            $idCart = $this->context->cart->id;
            if ($idProduct && $idCart) {
                $giftCardCartInfo = WkGiftCardCart::getGiftCartProductCartInfo($idProduct, $idCart, $this->context->shop->id);
                if ($giftCardCartInfo) {
                    $giftCardCartObj = new WkGiftCardCart((int) $giftCardCartInfo['id_wk_gift_card_cart']);
                    $giftCardCartObj->delete();
                    $result['status'] = 'ok';
                    exit(json_encode($result));
                }
            }
        }
    }

    /**
     * Custom function to add custonization data dynamically
     *
     * @param object $objProduct
     *
     * @return void
     */
    protected function textRecord($objProduct)
    {
        if (!$fieldIds = $objProduct->getCustomizationFieldIds()) {
            return false;
        }

        $authorizedTextFields = [];

        foreach ($fieldIds as $fieldId) {
            if ($fieldId['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                $authorizedTextFields[(int) $fieldId['id_customization_field']] = 'textField' .
                    (int) $fieldId['id_customization_field'];
            }
        }

        $indexes = array_flip($authorizedTextFields);
        foreach ($authorizedTextFields as $fieldName) {
            $value = 'Gift card Ref: ' . rand(10, 1000);
            if (in_array($fieldName, $authorizedTextFields) && $value != '') {
                $this->context->cart->addTextFieldToProduct(
                    $objProduct->id,
                    $indexes[$fieldName],
                    Product::CUSTOMIZE_TEXTFIELD,
                    $value
                );
            } elseif (in_array($fieldName, $authorizedTextFields) && $value == '') {
                $this->context->cart->deleteCustomizationToProduct((int) $objProduct->id, $indexes[$fieldName]);
            }
        }
    }

    public function displayAjaxGetOrderSummaryProductLine()
    {
        if ($this->context->cart->id) {
            $objCartPresenter = new CartPresenter();
            $presentedCart = $objCartPresenter->present($this->context->cart);
            $imageRetriever = new ImageRetriever($this->context->link);
            $urls['no_picture_image'] = $imageRetriever->getNoPictureImage($this->context->language);
            if ($products = $presentedCart['products']) {
                $this->context->smarty->assign([
                    'products' => $products,
                    'subtotals' => $presentedCart['subtotals'],
                    'totals' => $presentedCart['totals'],
                    'labels' => $presentedCart['labels'],
                    'add_product_link' => true,
                    'urls' => $urls,
                ]);
                exit(
                    $this->context->smarty->fetch(
                        'module:' . $this->module->name . '/views/templates/hook/_partials/order_final_summary.tpl'
                    )
                );
            } else {
                exit('0');
            }
        } else {
            exit('0');
        }
    }

    public function displayAjaxGetGiftCardCustomizableFields()
    {
        $idProduct = Tools::getValue('id_product');
        $token = Tools::getValue('token');
        $context = Context::getContext();
        $extendFilePath = 'catalog/product.tpl';
        if ($idProduct) {
            $objGiftCard = new WkGiftCardObj();
            $productObj = new Product((int) $idProduct);
            $quantity = StockAvailable::getQuantityAvailableByProduct($idProduct);
            if ($objGiftCard->getProductAllInfo($idProduct, $context->shop->id) && $quantity) {
                $this->context->smarty->assign(
                    [
                        'already_exist_in_cart' => 0,
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

                if ($this->context->customer->isLogged() && $this->context->customer->id) {
                    $objCustomer = new Customer((int) $this->context->customer->id);
                    if (Validate::isLoadedObject($objCustomer)) {
                        $this->context->smarty->assign([
                            'self_email' => $objCustomer->email,
                            'self_name' => $objCustomer->firstname . ' ' . $objCustomer->lastname,
                        ]);
                    }
                }

                $giftCardTpl = $this->context->smarty->fetch(
                    _PS_MODULE_DIR_ . $this->module->name . '/views/templates/hook/wk_gift_card_interface.tpl'
                );

                exit(json_encode(['giftCardTpl' => $giftCardTpl]));
            }
        }
    }
}
