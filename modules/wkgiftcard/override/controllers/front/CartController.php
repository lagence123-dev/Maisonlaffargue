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

class CartController extends CartControllerCore
{
    public function init()
    {
        parent::init();
        if (Module::isInstalled('wkgiftcard') && Module::isEnabled('wkgiftcard')) {
            if (Tools::getValue('add')) {
                include_once _PS_MODULE_DIR_ . 'wkgiftcard/classes/WkGiftCardRequiredClasses.php';
                $objGiftCardObj = new WkGiftCardObj();
                $isGiftCardProduct = $objGiftCardObj->checkVoucherIdByProductId($this->id_product);
                if ($isGiftCardProduct) {
                    $this->textRecord(new Product($this->id_product));
                    $customization_datas = $this->context->cart->getProductCustomization($this->id_product, null, true);
                    $this->customization_id = empty($customization_datas) ? null : $customization_datas[0]['id_customization'];
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

    public function displayAjaxUpdate()
    {
        if (Configuration::isCatalogMode()) {
            return;
        }

        $productsInCart = $this->context->cart->getProducts();
        $updatedProducts = array_filter($productsInCart, [$this, 'productInCartMatchesCriteria']);
        $updatedProduct = reset($updatedProducts);
        $productQuantity = $updatedProduct['quantity'] ?? 0;

        if (Module::isInstalled('wkgiftcard') && Module::isEnabled('wkgiftcard')) {
            $module = Module::getInstanceByName('wkgiftcard');
            $cart = $this->context->cart;
            $cartRules = $cart->getCartRules();
            if ($cartRules) {
                $id_customer = $cart->id_customer;
                $customerDetails = new Customer((int) $id_customer);
                if (Validate::isLoadedObject($customerDetails)) {
                    $objGiftCardOrderDetails = new GiftCardOrderDetails();
                    foreach ($cartRules as $cartRule) {
                        $cartRuleRecipientDetails = $objGiftCardOrderDetails->getCartRulesByCartId($cartRule['id_cart_rule'], $cart->id_shop);
                        if ($cartRuleRecipientDetails && isset($cartRuleRecipientDetails['recipient_email'])) {
                            if ($cartRuleRecipientDetails['recipient_email'] != $customerDetails->email) {
                                $this->errors[] = $module->l('This voucher is not belongs to you.');
                                $cart->removeCartRule($cartRule['id_cart_rule']);
                            }
                        }
                    }
                }
            }
        }

        if (!$this->errors) {
            $presentedCart = $this->cart_presenter->present($this->context->cart);

            // filter product output
            $presentedCart['products'] = $this->get('prestashop.core.filter.front_end_object.product_collection')
                ->filter($presentedCart['products']);

            $this->ajaxRender(json_encode([
                'success' => true,
                'id_product' => $this->id_product,
                'id_product_attribute' => $this->id_product_attribute,
                'id_customization' => $this->customization_id,
                'quantity' => $productQuantity,
                'cart' => $presentedCart,
                'errors' => empty($this->updateOperationError) ? '' : reset($this->updateOperationError),
            ]));

            return;
        } else {
            $this->ajaxRender(json_encode([
                'hasError' => true,
                'errors' => $this->errors,
                'quantity' => $productQuantity,
            ]));

            return;
        }
    }
}
