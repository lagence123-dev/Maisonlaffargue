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

class Product extends ProductCore
{
    public static function priceCalculation(
        $id_shop,
        $id_product,
        $id_product_attribute,
        $id_country,
        $id_state,
        $zipcode,
        $id_currency,
        $id_group,
        $quantity,
        $use_tax,
        $decimals,
        $only_reduc,
        $use_reduc,
        $with_ecotax,
        &$specific_price,
        $use_group_reduction,
        $id_customer = 0,
        $use_customer_price = true,
        $id_cart = 0,
        $real_quantity = 0,
        $id_customization = 0
    ) {
        if (Module::isInstalled('wkgiftcard') && Module::isEnabled('wkgiftcard')) {
            include_once dirname(__FILE__) . '/../../modules/wkgiftcard/classes/WkGiftCardRequiredClasses.php';
            $isGiftCardProduct = WkGiftCardObj::isDynamicPriceProduct($id_product, Context::getContext()->shop->id);
            if ($isGiftCardProduct) {
                $context = Context::getContext();
                if (isset($context->cart->id)) {
                    $giftCardProductCartInfo = WkGiftCardCart::getGiftCartProductCartInfo(
                        $id_product,
                        $context->cart->id,
                        $context->shop->id,
                        $id_customization
                    );
                    if ($giftCardProductCartInfo) {
                        $productCost = $giftCardProductCartInfo['dynamic_amount'];
                        $defaultCurrency = Configuration::get('PS_CURRENCY_DEFAULT');
                        if ($defaultCurrency != $context->currency->id) {
                            $productCost = Tools::convertPriceFull($giftCardProductCartInfo['dynamic_amount'], null, $context->currency);
                        }

                        return Tools::ps_round($productCost, $decimals);
                    }
                }
            }
        }

        return parent::priceCalculation(
            $id_shop,
            $id_product,
            $id_product_attribute,
            $id_country,
            $id_state,
            $zipcode,
            $id_currency,
            $id_group,
            $quantity,
            $use_tax,
            $decimals,
            $only_reduc,
            $use_reduc,
            $with_ecotax,
            $specific_price,
            $use_group_reduction,
            $id_customer,
            $use_customer_price,
            $id_cart,
            $real_quantity,
            $id_customization
        );
    }
}
