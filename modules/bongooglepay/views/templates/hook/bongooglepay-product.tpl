{*
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
 
{if $product.add_to_cart_url}
    <input type="hidden" id="price_bongooglepay" data-price="{math equation=$product.quantity_wanted*$product.price_amount}"
        value="{$product.quantity_wanted|escape:'htmlall':'UTF-8'}" min="{$product.minimal_quantity|escape:'htmlall':'UTF-8'}">
    <input type="hidden" id="currency_bongooglepay" data-currency="{$currency.iso_code|escape:'htmlall':'UTF-8'}">
    <div id="bon-google-checkout" style="display: block!important;">
    </div>
{/if}

