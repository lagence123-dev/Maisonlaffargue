{*
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
*}
{extends file=$extendFilePath}
{block name='cart_detailed_product_line'}
    <div class="product-line-grid">
        <!--  product line left content: image-->
        <div class="product-line-grid-left col-md-3 col-xs-4">
            <span class="product-image media-middle">
                {if $product.default_image}
                    <img src="{$product.default_image.bySize.cart_default.url|escape:'htmlall':'UTF-8'}"
                        alt="{$product.name|escape:'quotes':'UTF-8'}" loading="lazy">
                {else}
                    <img src="{$urls.no_picture_image.bySize.cart_default.url|escape:'htmlall':'UTF-8'}" loading="lazy" />
                {/if}
            </span>
        </div>

        <!--  product line body: label, discounts, price, attributes, customizations -->
        <div class="product-line-grid-body col-md-4 col-xs-8">
            <div class="product-line-info">
                <a class="label" href="{$product.url|escape:'htmlall':'UTF-8'}"
                    data-id_customization="{$product.id_customization|intval|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a>
            </div>

            <div class="product-line-info product-price h5 {if $product.has_discount}has-discount{/if}">
                {if $product.has_discount}
                    <div class="product-discount">
                        <span class="regular-price">{$product.regular_price|escape:'htmlall':'UTF-8'}</span>
                        {if $product.discount_type === 'percentage'}
                            <span class="discount discount-percentage">
                                -{$product.discount_percentage_absolute|escape:'htmlall':'UTF-8'}
                            </span>
                        {else}
                            <span class="discount discount-amount">
                                -{$product.discount_to_display|escape:'htmlall':'UTF-8'}
                            </span>
                        {/if}
                    </div>
                {/if}
                <div class="current-price">
                    <span class="price">{$product.price|escape:'htmlall':'UTF-8'}</span>
                    {if $product.unit_price_full}
                        <div class="unit-price-cart">{$product.unit_price_full|escape:'htmlall':'UTF-8'}</div>
                    {/if}
                </div>
                {hook h='displayProductPriceBlock' product=$product type="unit_price"}
            </div>

            <br />
            {foreach from=$product.attributes key="attribute" item="value"}
                <div class="product-line-info {$attribute|lower|escape:'html':'UTF-8'}">
                    <span class="label">{$attribute|escape:'htmlall':'UTF-8'}:</span>
                    <span class="value">{$value|escape:'htmlall':'UTF-8'}</span>
                </div>
            {/foreach}

            {*Start- custom hook for gift card recipient *}
            {hook h='displayAfterCartProductLine' product=$product type=1}
            {*End- custom hook for gift card recipient *}
            {if is_array($product.customizations) && $product.customizations|count}
                <br>
                {if WkGiftCardCart::getGiftCartProductCartInfo(
                            $product.id_product,
                            Context::getContext()->cart->id,
                            Context::getContext()->shop->id,
                            $product.id_customization
                        )}
                {block name='cart_detailed_product_line_customization'}

                {/block}
            {else}
                {block name='cart_detailed_product_line_customization'}
                    {foreach from=$product.customizations item="customization"}
                        <a href="#" data-toggle="modal"
                            data-target="#product-customizations-modal-{$customization.id_customization|escape:'htmlall':'UTF-8'}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                        <div class="modal fade customization-modal"
                            id="product-customizations-modal-{$customization.id_customization|escape:'htmlall':'UTF-8'}" tabindex="-1"
                            role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="{l s='Close' d='Shop.Theme.Global'}">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title">{l s='Product customization' d='Shop.Theme.Catalog'}</h4>
                                    </div>
                                    <div class="modal-body">
                                        {foreach from=$customization.fields item="field"}
                                            <div class="product-customization-line row">
                                                <div class="col-sm-3 col-xs-4 label">
                                                    {$field.label|escape:'htmlall':'UTF-8'}
                                                </div>
                                                <div class="col-sm-9 col-xs-8 value">
                                                    {if $field.type == 'text'}
                                                        {if (int)$field.id_module}
                                                            {$field.text|escape:'htmlall':'UTF-8'}
                                                        {else}
                                                            {$field.text|escape:'htmlall':'UTF-8'}
                                                        {/if}
                                                    {elseif $field.type == 'image'}
                                                        <img src="{$field.image.small.url|escape:'htmlall':'UTF-8'}" loading="lazy">
                                                    {/if}
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {/block}
            {/if}
        {/if}
    </div>

    <!--  product line right content: actions (quantity, delete), price -->
    <div class="product-line-grid-right product-line-actions col-md-5 col-xs-12">
        <div class="row">
            <div class="col-xs-4 hidden-md-up"></div>
            <div class="col-md-10 col-xs-6">
                <div class="row">
                    <div class="col-md-6 col-xs-6 qty">
                        {if !empty($product.is_gift)}
                            <span class="gift-quantity">{$product.quantity|escape:'htmlall':'UTF-8'}</span>
                        {else}
                            <input class="js-cart-line-product-quantity"
                                data-down-url="{$product.down_quantity_url|escape:'htmlall':'UTF-8'}"
                                data-up-url="{$product.up_quantity_url|escape:'htmlall':'UTF-8'}"
                                data-update-url="{$product.update_quantity_url|escape:'htmlall':'UTF-8'}"
                                data-product-id="{$product.id_product|escape:'htmlall':'UTF-8'}" type="number"
                                inputmode="numeric" pattern="[0-9]*" value="{$product.quantity|escape:'htmlall':'UTF-8'}"
                                name="product-quantity-spin"
                                aria-label="{l s='%productName% product quantity field' sprintf=['%productName%' => $product.name] d='Shop.Theme.Checkout'}" />
                        {/if}
                    </div>
                    <div class="col-md-6 col-xs-2 price">
                        <span class="product-price">
                            <strong>
                                {if !empty($product.is_gift)}
                                    <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
                                {else}
                                    {$product.total|escape:'htmlall':'UTF-8'}
                                {/if}
                            </strong>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-xs-2 text-xs-right">
                <div class="cart-line-product-actions">
                    <a class="remove-from-cart" rel="nofollow"
                        href="{$product.remove_from_cart_url|escape:'htmlall':'UTF-8'}"
                        data-link-action="delete-from-cart"
                        data-id-product="{$product.id_product|escape:'javascript':'UTF-8'}"
                        data-id-product-attribute="{$product.id_product_attribute|escape:'javascript':'UTF-8'}"
                        data-id-customization="{$product.id_customization|escape:'javascript':'UTF-8'}">
                        {if empty($product.is_gift)}
                            <i class="material-icons float-xs-left">delete</i>
                        {/if}
                    </a>

                    {block name='hook_cart_extra_product_actions'}
                        {hook h='displayCartExtraProductActions' product=$product}
                    {/block}

                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</div>
{/block}