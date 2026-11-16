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

class Cart extends CartCore
{
    public function getOrderTotal(
        $with_taxes = true,
        $type = Cart::BOTH,
        $products = null,
        $id_carrier = null,
        $use_cache = true,
        bool $keepOrderPrices = false
    ) {
        $price_calculator = PrestaShop\PrestaShop\Adapter\ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Product\\PriceCalculator');
        $ps_use_ecotax = $this->configuration->get('PS_USE_ECOTAX');
        $ps_round_type = $this->configuration->get('PS_ROUND_TYPE');
        $compute_precision = $this->configuration->get('_PS_PRICE_COMPUTE_PRECISION_');

        if (!$this->id) {
            return 0;
        }
        $type = (int) $type;
        $array_type = [
            Cart::ONLY_PRODUCTS,
            Cart::ONLY_DISCOUNTS,
            Cart::BOTH,
            Cart::BOTH_WITHOUT_SHIPPING,
            Cart::ONLY_SHIPPING,
            Cart::ONLY_WRAPPING,
            Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING,
            Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
        ];
        $virtual_context = Context::getContext()->cloneContext();
        $virtual_context->cart = $this;
        if (!in_array($type, $array_type)) {
            exit(Tools::displayError());
        }
        $with_shipping = in_array($type, [Cart::BOTH, Cart::ONLY_SHIPPING]);
        if ($type == Cart::ONLY_DISCOUNTS && !CartRule::isFeatureActive()) {
            return 0;
        }
        $virtual = $this->isVirtualCart();
        if ($virtual && $type == Cart::ONLY_SHIPPING) {
            return 0;
        }
        if ($virtual && $type == Cart::BOTH) {
            $type = Cart::BOTH_WITHOUT_SHIPPING;
        }
        if ($with_shipping || $type == Cart::ONLY_DISCOUNTS) {
            if (is_null($products) && is_null($id_carrier)) {
                $shipping_fees = $this->getTotalShippingCost(null, (bool) $with_taxes);
            } else {
                $shipping_fees = $this->getPackageShippingCost((int) $id_carrier, (bool) $with_taxes, null, $products);
            }
        } else {
            $shipping_fees = 0;
        }
        if ($type == Cart::ONLY_SHIPPING) {
            return $shipping_fees;
        }
        if ($type == Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING) {
            $type = Cart::ONLY_PRODUCTS;
        }
        $param_product = true;
        if (is_null($products)) {
            $param_product = false;
            $products = $this->getProducts();
        }
        if ($type == Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING) {
            foreach ($products as $key => $product) {
                if ($product['is_virtual']) {
                    unset($products[$key]);
                }
            }
            $type = Cart::ONLY_PRODUCTS;
        }
        $order_total = 0;
        if (Tax::excludeTaxeOption()) {
            $with_taxes = false;
        }
        $products_total = [];
        $productLines = $this->countProductLines($products);
        foreach ($products as $product) {
            if (array_key_exists('is_gift', $product) && $product['is_gift']) {
                $productIndex = $product['id_product'] . '-' . $product['id_product_attribute'];
                if ($productLines[$productIndex] > 1) {
                    continue;
                }
            }
            if ($virtual_context->shop->id != $product['id_shop']) {
                $virtual_context->shop = new Shop((int) $product['id_shop']);
            }
            $id_address = $this->getProductAddressId($product);
            $null = null;
            $price = $price_calculator->getProductPrice(
                (int) $product['id_product'],
                $with_taxes,
                (int) $product['id_product_attribute'],
                6,
                null,
                false,
                true,
                $product['cart_quantity'],
                false,
                (int) $this->id_customer ? (int) $this->id_customer : null,
                (int) $this->id,
                $id_address,
                $null,
                $ps_use_ecotax,
                true,
                $virtual_context,
                true,
                (int) $product['id_customization']
            );
            $id_tax_rules_group = $this->findTaxRulesGroupId($with_taxes, $product, $virtual_context);
            if (in_array($ps_round_type, [Order::ROUND_ITEM, Order::ROUND_LINE])) {
                if (!isset($products_total[$id_tax_rules_group])) {
                    $products_total[$id_tax_rules_group] = 0;
                }
            } elseif (!isset($products_total[$id_tax_rules_group . '_' . $id_address])) {
                $products_total[$id_tax_rules_group . '_' . $id_address] = 0;
            }
            $totalPriceGiftCardProduct = 0;
            if (Module::isInstalled('wkgiftcard') && Module::isEnabled('wkgiftcard')) {
                include_once dirname(__FILE__) . '/../../modules/wkgiftcard/classes/WkGiftCardRequiredClasses.php';
                $isGiftCardProduct = WkGiftCardObj::isDynamicPriceProduct($product['id_product'], Context::getContext()->shop->id);
                $context = Context::getContext();
                if ($isGiftCardProduct) {
                    $giftCardProductCartInfo = WkGiftCardCart::getGiftCartProductCartInfo(
                        $product['id_product'],
                        $this->id,
                        $context->shop->id,
                        $product['id_customization']
                    );
                    if ($giftCardProductCartInfo) {
                        $productCost = $giftCardProductCartInfo['dynamic_amount'];
                        $defaultCurrency = Configuration::get('PS_CURRENCY_DEFAULT');
                        if ($defaultCurrency != $context->currency->id) {
                            $productCost = Tools::convertPriceFull($giftCardProductCartInfo['dynamic_amount'], null, $context->currency);
                        }
                        $totalPriceGiftCardProduct += (float) ($product['cart_quantity'] * $productCost);
                    }
                }
            }
            if (isset($isGiftCardProduct) && $isGiftCardProduct) {
                switch ($ps_round_type) {
                    case Order::ROUND_TOTAL:
                        $products_total[$id_tax_rules_group . '_' . $id_address] += $totalPriceGiftCardProduct;
                        break;
                    case Order::ROUND_LINE:
                        $product_price = $totalPriceGiftCardProduct;
                        $products_total[$id_tax_rules_group] += Tools::ps_round(
                            $product_price,
                            $compute_precision
                        );
                        break;
                    case Order::ROUND_ITEM:
                    default:
                        $products_total[$id_tax_rules_group] += Tools::ps_round(
                            $totalPriceGiftCardProduct,
                            $compute_precision
                        );
                        break;
                }
            } else {
                switch ($ps_round_type) {
                    case Order::ROUND_TOTAL:
                        $products_total[$id_tax_rules_group . '_' . $id_address] += $price * (int) $product['cart_quantity'];
                        break;
                    case Order::ROUND_LINE:
                        $product_price = $price * $product['cart_quantity'];
                        $products_total[$id_tax_rules_group] += Tools::ps_round(
                            $product_price,
                            $compute_precision
                        );
                        break;
                    case Order::ROUND_ITEM:
                    default:
                        $product_price = $price;
                        $products_total[$id_tax_rules_group] += Tools::ps_round(
                            $product_price,
                            $compute_precision
                        ) * (int) $product['cart_quantity'];
                        break;
                }
            }
        }
        foreach ($products_total as $key => $price) {
            $order_total += $price;
        }
        $order_total_products = $order_total;
        if ($type == Cart::ONLY_DISCOUNTS) {
            $order_total = 0;
        }
        $wrappingFees = $this->calculateWrappingFees($with_taxes, $type);
        if ($type == Cart::ONLY_WRAPPING) {
            return $wrappingFees;
        }
        $order_total_discount = 0;
        $order_shipping_discount = 0;
        if (!in_array($type, [Cart::ONLY_SHIPPING, Cart::ONLY_PRODUCTS]) && CartRule::isFeatureActive()) {
            $cart_rules = $this->getTotalCalculationCartRules($type, $with_shipping);
            $package = [
                'id_carrier' => $id_carrier,
                'id_address' => $this->getDeliveryAddressId($products),
                'products' => $products,
            ];
            $flag = false;
            foreach ($cart_rules as $cart_rule) {
                if (($with_shipping || $type == Cart::ONLY_DISCOUNTS) && $cart_rule['obj']->free_shipping && !$flag) {
                    $order_shipping_discount = (float) Tools::ps_round(
                        $cart_rule['obj']->getContextualValue(
                            $with_taxes,
                            $virtual_context,
                            CartRule::FILTER_ACTION_SHIPPING,
                            $param_product ? $package : null,
                            $use_cache
                        ),
                        $compute_precision
                    );
                    $flag = true;
                }
                if (!$this->shouldExcludeGiftsDiscount && (int) $cart_rule['obj']->gift_product) {
                    $in_order = false;
                    if (is_null($products)) {
                        $in_order = true;
                    } else {
                        foreach ($products as $product) {
                            if ($cart_rule['obj']->gift_product == $product['id_product']
                            && $cart_rule['obj']->gift_product_attribute == $product['id_product_attribute']) {
                                $in_order = true;
                            }
                        }
                    }
                    if ($in_order) {
                        $order_total_discount += $cart_rule['obj']->getContextualValue(
                            $with_taxes,
                            $virtual_context,
                            CartRule::FILTER_ACTION_GIFT,
                            $package,
                            $use_cache
                        );
                    }
                }
                if ($cart_rule['obj']->reduction_percent > 0 || $cart_rule['obj']->reduction_amount > 0) {
                    $order_total_discount += Tools::ps_round(
                        $cart_rule['obj']->getContextualValue(
                            $with_taxes,
                            $virtual_context,
                            CartRule::FILTER_ACTION_REDUCTION,
                            $package,
                            $use_cache
                        ),
                        $compute_precision
                    );
                }
            }
            $order_total_discount = min(
                Tools::ps_round(
                    $order_total_discount,
                    2
                ),
                (float) $order_total_products
            ) + (float) $order_shipping_discount;
            $order_total -= $order_total_discount;
        }
        if ($type == Cart::BOTH) {
            $order_total += $shipping_fees + $wrappingFees;
        }
        if ($order_total < 0 && $type != Cart::ONLY_DISCOUNTS) {
            return 0;
        }
        if ($type == Cart::ONLY_DISCOUNTS) {
            return $order_total_discount;
        }

        return Tools::ps_round((float) $order_total, $compute_precision);
    }
}
