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
<div class="order-confirmation-table">
    {foreach from=$products item=product}
        <div class="order-line row">
            <div class="col-sm-2 col-xs-3">
                <span class="image">
                    {if isset($product.default_image) && isset($product.default_image.bySize)}

                        <img src="{$product.default_image.bySize.cart_default.url|escape:'htmlall':'UTF-8'}"
                            alt="{$product.name|escape:'htmlall':'UTF-8'}" loading="lazy">
                    {else}
                        {if isset($urls.no_picture_image)}
                            <img src="{$urls.no_picture_image.bySize.cart_default.url|escape:'htmlall':'UTF-8'}" loading="lazy" />
                        {/if}
                    {/if}
                </span>
            </div>
            <div class="col-sm-4 col-xs-9 details">
                {if $add_product_link}<a href="{$product.url|escape:'htmlall':'UTF-8'}" target="_blank">{/if}
                    <span>{$product.name|escape:'htmlall':'UTF-8'}</span>
                    {if $add_product_link}</a>{/if}
                {*Start- custom hook for gift card recipient *}
                {hook h='displayAfterCartProductLine' product=$product type=1}
                {*End- custom hook for gift card recipient *}
                {if is_array($product.customizations) && $product.customizations|count}
                    {if WkGiftCardCart::getGiftCartProductCartInfo(
                        $product.id_product,
                        Context::getContext()->cart->id,
                        Context::getContext()->shop->id,
                        $product.id_customization
                    )}
                    <div class="customizations">
                        {* don't display customization*}
                    </div>

                {else}
                    {foreach from=$product.customizations item="customization"}
                        <div class="customizations">
                            <a href="#" data-toggle="modal"
                                data-target="#product-customizations-modal-{$customization.id_customization|escape:'htmlall':'UTF-8'}">{l s='Product customization' d='Shop.Theme.Catalog'}</a>
                        </div>
                        <div class="modal fade customization-modal"
                            id="product-customizations-modal-{$customization.id_customization|escape:'htmlall':'UTF-8'}"
                            tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                                            {$field.text nofilter}
                                                        {else}
                                                            {$field.text|escape:'htmlall':'UTF-8'}
                                                        {/if}
                                                    {elseif $field.type == 'image'}
                                                        <img src="{$field.image.small.url|escape:'htmlall':'UTF-8'}">
                                                    {/if}
                                                </div>
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                {/if}
            {/if}

            {hook h='displayProductPriceBlock' product=$product type="unit_price"}

        </div>
        <div class="col-sm-6 col-xs-12 qty">
            <div class="row">
                <div class="col-xs-5 text-sm-right text-xs-left">{$product.price|escape:'htmlall':'UTF-8'}</div>
                <div class="col-xs-2"> {$product.quantity|escape:'htmlall':'UTF-8'}</div>
                <div class="col-xs-5 text-xs-right bold">{$product.total|escape:'htmlall':'UTF-8'}</div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    {*Start- custom hook for gift card recipient *}
    {hook h='displayAfterCartProductLine' product=$product type=1}
    {*End- custom hook for gift card recipient*}
    {/foreach}
    <hr>
    <table>
        {foreach $subtotals as $subtotal}
            {if $subtotal !== null && $subtotal.type !== 'tax' && $subtotal.label !== null}
                <tr>
                    <td>{$subtotal.label|escape:'htmlall':'UTF-8'}</td>
                    <td>{if 'discount' == $subtotal.type}-&nbsp;{/if}{$subtotal.value|escape:'htmlall':'UTF-8'}</td>
                </tr>
            {/if}
        {/foreach}
        {if $subtotals.tax !== null && $subtotals.tax.label !== null}
            <tr class="sub">
                <td>{$subtotals.tax.label|escape:'htmlall':'UTF-8'}</td>
                <td>{$subtotals.tax.value|escape:'htmlall':'UTF-8'}</td>
            </tr>
        {/if}
        <tr class="font-weight-bold">
            <td><span class="text-uppercase">{$totals.total.label|escape:'htmlall':'UTF-8'}</span>
                {$labels.tax_short|escape:'htmlall':'UTF-8'}</td>
            <td>{$totals.total.value|escape:'htmlall':'UTF-8'}</td>
        </tr>
    </table>
</div>
