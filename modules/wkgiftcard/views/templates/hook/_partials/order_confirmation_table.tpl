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

<div id="order-items" class="col-md-12">
    <div class="row">
        {block name='order_items_table_head'}
            <h3 class="card-title h3 col-md-6 col-12">{l s='Order items' d='wkgiftcard'}</h3>
            <h3 class="card-title h3 col-md-2 text-md-center _desktop-title">{l s='Unit price' mod='wkgiftcard'}</h3>
            <h3 class="card-title h3 col-md-2 text-md-center _desktop-title">{l s='Quantity' mod='wkgiftcard'}</h3>
            <h3 class="card-title h3 col-md-2 text-md-center _desktop-title">{l s='Total products' mod='wkgiftcard'}
            </h3>
        {/block}
    </div>

    <div class="order-confirmation-table">
        {foreach from=$products item=product}
            <div class="order-line row">
                <div class="col-sm-2 col-xs-3">
                    <span class="image">
                        {if !empty($product.default_image)}
                            <picture>
                                {if !empty($product.default_image.medium.sources.avif)}
                                <source srcset="{$product.default_image.medium.sources.avif}" type="image/avif">{/if}
                                {if !empty($product.default_image.medium.sources.webp)}
                                <source srcset="{$product.default_image.medium.sources.webp}" type="image/webp">{/if}
                                <img src="{$product.default_image.medium.url|escape:'htmlall':'UTF-8'}" loading="lazy" />
                            </picture>
                        {else}
                            <picture>
                                {if !empty($urls.no_picture_image.bySize.medium_default.sources.avif)}
                                    <source srcset="{$urls.no_picture_image.bySize.medium_default.sources.avif|escape:'htmlall':'UTF-8'}" type="image/avif">
                                {/if}
                                {if !empty($urls.no_picture_image.bySize.medium_default.sources.webp)}
                                    <source srcset="{$urls.no_picture_image.bySize.medium_default.sources.webp|escape:'htmlall':'UTF-8'}" type="image/webp">
                                {/if}
                                <img src="{$urls.no_picture_image.bySize.medium_default.url|escape:'htmlall':'UTF-8'}" loading="lazy" />
                            </picture>
                        {/if}
                    </span>
                </div>
                <div class="col-sm-4 col-xs-9 details">
                    {if $add_product_link}<a href="{$product.url|escape:'htmlall':'UTF-8'}" target="_blank">{/if}
                        <span>{$product.name|escape:'htmlall':'UTF-8'}</span>
                        {if $add_product_link}</a>{/if}
                    {if is_array($product.customizations) && $product.customizations|count}
                        {foreach from=$product.customizations item="customization"}
                            <div class="customizations">
                                <a href="#" data-toggle="modal"
                                    data-target="#product-customizations-modal-{$customization.id_customization|escape:'htmlall':'UTF-8'}">{l s='Product customization' mod='wkgiftcard'}</a>
                            </div>
                            <div class="modal fade customization-modal"
                                id="product-customizations-modal-{$customization.id_customization|escape:'htmlall':'UTF-8'}" tabindex="-1" role="dialog"
                                aria-hidden="true">
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
                                                                {$field.text nofilter}
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
                    {/if}
                    {hook h='displayProductPriceBlock' product=$product type="unit_price"}
                </div>
                <div class="col-sm-6 col-xs-12 qty">
                    <div class="row">
                        <div class="col-xs-4 text-sm-center text-xs-left">{$product.price|escape:'htmlall':'UTF-8'}</div>
                        <div class="col-xs-4 text-sm-center">{$product.quantity|escape:'htmlall':'UTF-8'}</div>
                        {if isset($product.id_customization)}
                            {if ($objGiftCardOrderDetails->getAllInfoByOrderId(
                                $idOrder,
                                Context::getContext()->shop->id,
                                $product.id_customization
                            ))}
                                <div class="col-xs-4 text-sm-center text-xs-right bold">{Tools::displayPrice($product.price)|escape:'htmlall':'UTF-8'}</div>
                            {else}
                                <div class="col-xs-4 text-sm-center text-xs-right bold">{$product.total|escape:'htmlall':'UTF-8'}</div>
                            {/if}
                        {else}
                            <div class="col-xs-4 text-sm-center text-xs-right bold">{$product.total|escape:'htmlall':'UTF-8'}</div>
                        {/if}
                    </div>
                </div>
            </div>
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

            {if !$configuration.display_prices_tax_incl && $configuration.taxes_enabled}
                <tr>
                    <td><span class="text-uppercase">{$totals.total.label|escape:'htmlall':'UTF-8'}&nbsp;{$labels.tax_short|escape:'htmlall':'UTF-8'}</span></td>
                    <td>{$totals.total.value|escape:'htmlall':'UTF-8'}</td>
                </tr>
                <tr class="total-value font-weight-bold">
                    <td><span class="text-uppercase">{$totals.total_including_tax.label|escape:'htmlall':'UTF-8'}</span></td>
                    <td>{$totals.total_including_tax.value|escape:'htmlall':'UTF-8'}</td>
                </tr>
            {else}
                <tr class="total-value font-weight-bold">
                    <td><span
                            class="text-uppercase">{$totals.total.label|escape:'htmlall':'UTF-8'}&nbsp;{if $configuration.taxes_enabled && $configuration.display_taxes_label}{$labels.tax_short|escape:'htmlall':'UTF-8'}{/if}</span>
                    </td>
                    <td>{$totals.total.value|escape:'htmlall':'UTF-8'}</td>
                </tr>
            {/if}
            {if $subtotals.tax !== null && $subtotals.tax.label !== null}
                <tr class="sub taxes">
                    <td colspan="2"><span
                    class="label">{l s='%label%:' sprintf=['%label%' => $subtotals.tax.label] mod='wkgiftcard'}</span>&nbsp;<span
                    class="value">{$subtotals.tax.value|escape:'htmlall':'UTF-8'}</span></td>
                </tr>
            {/if}
        </table>
    </div>
</div>