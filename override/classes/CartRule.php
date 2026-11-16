<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class CartRule extends CartRuleCore
{
    /**
     * CartRules compare function to use the Product and the rules.
     *
     * @param array $products List of Products from the cart,
     * @param array $eligibleProducts List of Product eligible for rules,
     * @param string $ruleType name of the rule,
     *
     * @return array Product selected who are eligible
     */
    /*
    * module: wkgiftcard
    * date: 2025-03-25 20:24:06
    * version: 5.2.1
    */
    protected function filterProducts($products, $eligibleProducts, $ruleType)
    {
        if (Module::isEnabled('wkgiftcard')) {
            $cartRuleId = $this->id;
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_order_details` WHERE `id_card_rule` = ' . (int) $cartRuleId;
            if (Db::getInstance()->getRow($sql) && $ruleType == 'products' || $ruleType == 'categories') {
                $return = [];
                $productsList = explode(':', preg_replace("#\-[0-9]+#", '-0', implode(':', $products)));
                $allMatch = true;
                foreach ($productsList as $k => $product) {
                    if (in_array($product, $eligibleProducts)) {
                        $return[] = $products[$k];
                    } else {
                        $allMatch = false;
                    }
                }
                if (!$allMatch) {
                    $context = Context::getContext();
                    if (isset($context->cart->id)) {
                        $cartTotalAmount = $context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
                        $reductionAmount = (float) $this->reduction_amount;
                        if ($reductionAmount > $cartTotalAmount) {
                            return [-1];
                        }
                    }
                }
                return $return;
            }
            return parent::filterProducts($products, $eligibleProducts, $ruleType);
        } else {
            return parent::filterProducts($products, $eligibleProducts, $ruleType);
        }
    }
    /**
     * Checks if the products chosen by the customer are usable with the cart rule.
     *
     * @param Cart $cart
     * @param bool $returnProducts [default=false]
     *                             If true, this method will return an array of eligible products.
     *                             Otherwise, it returns TRUE on success and string|false on errors (depending on the value of $displayError)
     * @param bool $displayError [default=false]
     *                           If true, this method will return an error message instead of FALSE on errors.
     *                           Otherwise, it returns FALSE on errors
     * @param bool $alreadyInCart
     *
     * @return array|bool|string
     *
     * @throws PrestaShopDatabaseException
     */
    /*
    * module: wkgiftcard
    * date: 2025-03-25 20:24:06
    * version: 5.2.1
    */
    public function checkProductRestrictionsFromCart(Cart $cart, $returnProducts = false, $displayError = true, $alreadyInCart = false)
    {
        $selected_products = [];
        if ($this->product_restriction) {
            $products = $cart->getProducts();
            if (empty($products)) {
                return (!$displayError) ? false : $this->trans('You cannot use this voucher in an empty cart', [], 'Shop.Notifications.Error');
            }
            $product_rule_groups = $this->getProductRuleGroups();
            foreach ($product_rule_groups as $id_product_rule_group => $product_rule_group) {
                /*
                 * Rule group is a set of rules that the cart must meet for this cart rule to be applied.
                 * These groups have an AND relationship. If you create two groups for given cart rule,
                 * the cart must meet the conditions of both of them to be applied.
                 *
                 * Also, at least $product_rule_group['quantity'] must meet these rules.
                 */
                $eligible_products_list = [];
                foreach ($products as $product) {
                    $eligible_products_list[] = (int) $product['id_product'] . '-' . (int) $product['id_product_attribute'];
                }
                $product_rules = $this->getProductRules($id_product_rule_group);
                $countRulesProduct = count($product_rules);
                $condition = 0;
                foreach ($product_rules as $product_rule) {
                    /*
                     * For the cart RULE GROUP to be validated, at least on of the RULES inside the RULE GROUP
                     * must meet the conditions.
                     */
                    switch ($product_rule['type']) {
                        case 'attributes':
                            $cart_attributes = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, pac.`id_attribute`, cp.`id_product_attribute`
							FROM `' . _DB_PREFIX_ . 'cart_product` cp
							LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON cp.id_product_attribute = pac.id_product_attribute
							WHERE cp.`id_cart` = ' . (int) $cart->id . '
							AND cp.`id_product` IN (' . implode(',', array_map('intval', $eligible_products_list)) . ')
							AND cp.id_product_attribute > 0');
                            $count_matching_products = 0;
                            $matching_products_list = [];
                            foreach ($cart_attributes as $cart_attribute) {
                                if (in_array($cart_attribute['id_attribute'], $product_rule['values'])) {
                                    $count_matching_products += $cart_attribute['quantity'];
                                    if (
                                        $alreadyInCart
                                        && $this->gift_product == $cart_attribute['id_product']
                                        && $this->gift_product_attribute == $cart_attribute['id_product_attribute']) {
                                        --$count_matching_products;
                                    }
                                    $matching_products_list[] = $cart_attribute['id_product'] . '-' . $cart_attribute['id_product_attribute'];
                                }
                            }
                            if ($count_matching_products < $product_rule_group['quantity']) {
                                if ($countRulesProduct === 1) {
                                    return (!$displayError) ? false : $this->trans('You cannot use this voucher with these products', [], 'Shop.Notifications.Error');
                                } else {
                                    ++$condition;
                                    break;
                                }
                            }
                            $eligible_products_list = $this->filterProducts($eligible_products_list, $matching_products_list, $product_rule['type']);
                            break;
                        case 'products':
                            $cart_products = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`
							FROM `' . _DB_PREFIX_ . 'cart_product` cp
							WHERE cp.`id_cart` = ' . (int) $cart->id . '
							AND cp.`id_product` IN (' . implode(',', array_map('intval', $eligible_products_list)) . ')');
                            $count_matching_products = 0;
                            $matching_products_list = [];
                            foreach ($cart_products as $cart_product) {
                                if (in_array($cart_product['id_product'], $product_rule['values'])) {
                                    $count_matching_products += $cart_product['quantity'];
                                    if ($alreadyInCart && $this->gift_product == $cart_product['id_product']) {
                                        --$count_matching_products;
                                    }
                                    $matching_products_list[] = $cart_product['id_product'] . '-0';
                                }
                            }
                            if ($count_matching_products < $product_rule_group['quantity']) {
                                if ($countRulesProduct === 1) {
                                    return (!$displayError) ? false : $this->trans('You cannot use this voucher with these products', [], 'Shop.Notifications.Error');
                                } else {
                                    ++$condition;
                                    break;
                                }
                            }
                            $eligible_products_list = $this->filterProducts($eligible_products_list, $matching_products_list, $product_rule['type']);
                            if (Module::isEnabled('wkgiftcard')) {
                                $cartRuleId = $this->id;
                                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_order_details` WHERE `id_card_rule` = ' . (int) $cartRuleId;
                                if (Db::getInstance()->getRow($sql)) {
                                    if (isset($eligible_products_list['0']) && $eligible_products_list[0] == -1) {
                                        return $this->trans('You cannot use this voucher with these products', [], 'Shop.Notifications.Error');
                                    }
                                }
                            }
                            break;
                        case 'categories':
                            if (Module::isEnabled('wkgiftcard')) {
                                $cartRuleId = $this->id;
                                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_order_details` WHERE `id_card_rule` = ' . (int) $cartRuleId;
                                if (Db::getInstance()->getRow($sql)) {
                                    if (isset($eligible_products_list['0']) && $eligible_products_list[0] == -1) {
                                        return $this->trans('You cannot use this voucher with these products', [], 'Shop.Notifications.Error');
                                    }
                                }
                            }
                            $cart_categories = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, cp.`id_product_attribute`, catp.`id_category`
							FROM `' . _DB_PREFIX_ . 'cart_product` cp
							LEFT JOIN `' . _DB_PREFIX_ . 'category_product` catp ON cp.id_product = catp.id_product
							WHERE cp.`id_cart` = ' . (int) $cart->id . '
							AND cp.`id_product` IN (' . implode(',', array_map('intval', $eligible_products_list)) . ')
							AND cp.`id_product` <> ' . (int) $this->gift_product);
                            $count_matching_products = 0;
                            $matching_products_list = [];
                            foreach ($cart_categories as $cart_category) {
                                if (in_array($cart_category['id_category'], $product_rule['values'])
                                    /*
                                     * We also check that the product is not already in the matching product list,
                                     * because there are doubles in the query results (when the product is in multiple categories)
                                     */
                                    && !in_array($cart_category['id_product'] . '-' . $cart_category['id_product_attribute'], $matching_products_list)) {
                                    $count_matching_products += $cart_category['quantity'];
                                    $matching_products_list[] = $cart_category['id_product'] . '-' . $cart_category['id_product_attribute'];
                                }
                            }
                            if (Module::isEnabled('wkgiftcard')) {
                                $cartRuleId = $this->id;
                                $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_gift_card_order_details` WHERE `id_card_rule` = ' . (int) $cartRuleId;
                                if (Db::getInstance()->getRow($sql)) {
                                    $productsList = $eligible_products_list;
                                    $eligibleProducts = $matching_products_list;
                                    $allMatch = true;
                                    foreach ($productsList as $k => $product) {
                                        if (in_array($product, $eligibleProducts)) {
                                            $return[] = $product;
                                        } else {
                                            $allMatch = false;
                                        }
                                    }
                                    if (!$allMatch) {
                                        $context = Context::getContext();
                                        if (isset($context->cart->id)) {
                                            $cartTotalAmount = $context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
                                            $reductionAmount = (float) $this->reduction_amount;
                                            if ($reductionAmount > $cartTotalAmount) {
                                                return $this->trans('You cannot use this voucher with these products', [], 'Shop.Notifications.Error');
                                            }
                                        }
                                    }
                                }
                            }
                            if ($count_matching_products < $product_rule_group['quantity']) {
                                if ($countRulesProduct === 1) {
                                    return (!$displayError) ? false : $this->trans('You cannot use this voucher with these products', [], 'Shop.Notifications.Error');
                                } else {
                                    ++$condition;
                                    break;
                                }
                            }
                            foreach ($matching_products_list as &$matching_product) {
                                $matching_product = preg_replace('/^([0-9]+)-[0-9]+$/', '$1-0', $matching_product);
                            }
                            $eligible_products_list = $this->filterProducts($eligible_products_list, $matching_products_list, $product_rule['type']);
                            break;
                        case 'manufacturers':
                            $cart_manufacturers = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, p.`id_manufacturer`
							FROM `' . _DB_PREFIX_ . 'cart_product` cp
							LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON cp.id_product = p.id_product
							WHERE cp.`id_cart` = ' . (int) $cart->id . '
							AND cp.`id_product` IN (' . implode(',', array_map('intval', $eligible_products_list)) . ')');
                            $count_matching_products = 0;
                            $matching_products_list = [];
                            foreach ($cart_manufacturers as $cart_manufacturer) {
                                if (in_array($cart_manufacturer['id_manufacturer'], $product_rule['values'])) {
                                    $count_matching_products += $cart_manufacturer['quantity'];
                                    $matching_products_list[] = $cart_manufacturer['id_product'] . '-0';
                                }
                            }
                            if ($count_matching_products < $product_rule_group['quantity']) {
                                if ($countRulesProduct === 1) {
                                    return (!$displayError) ? false : $this->trans('You cannot use this voucher with these products', [], 'Shop.Notifications.Error');
                                } else {
                                    ++$condition;
                                    break;
                                }
                            }
                            $eligible_products_list = $this->filterProducts($eligible_products_list, $matching_products_list, $product_rule['type']);
                            break;
                        case 'suppliers':
                            $cart_suppliers = Db::getInstance()->executeS('
							SELECT cp.quantity, cp.`id_product`, p.`id_supplier`
							FROM `' . _DB_PREFIX_ . 'cart_product` cp
							LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON cp.id_product = p.id_product
							WHERE cp.`id_cart` = ' . (int) $cart->id . '
							AND cp.`id_product` IN (' . implode(',', array_map('intval', $eligible_products_list)) . ')');
                            $count_matching_products = 0;
                            $matching_products_list = [];
                            foreach ($cart_suppliers as $cart_supplier) {
                                if (in_array($cart_supplier['id_supplier'], $product_rule['values'])) {
                                    $count_matching_products += $cart_supplier['quantity'];
                                    $matching_products_list[] = $cart_supplier['id_product'] . '-0';
                                }
                            }
                            if ($count_matching_products < $product_rule_group['quantity']) {
                                if ($countRulesProduct === 1) {
                                    return (!$displayError) ? false : $this->trans('You cannot use this voucher with these products', [], 'Shop.Notifications.Error');
                                } else {
                                    ++$condition;
                                    break;
                                }
                            }
                            $eligible_products_list = $this->filterProducts($eligible_products_list, $matching_products_list, $product_rule['type']);
                            break;
                    }
                    if (!count($eligible_products_list)) {
                        if ($countRulesProduct === 1) {
                            return (!$displayError) ? false : $this->trans('You cannot use this voucher with these products', [], 'Shop.Notifications.Error');
                        }
                    }
                }
                if ($countRulesProduct !== 1 && $condition == $countRulesProduct) {
                    return (!$displayError) ? false : $this->trans('You cannot use this voucher with these products', [], 'Shop.Notifications.Error');
                }
                $selected_products = array_merge($selected_products, $eligible_products_list);
            }
        }
        if ($returnProducts) {
            return $selected_products;
        }
        return (!$displayError) ? true : false;
    }
}
