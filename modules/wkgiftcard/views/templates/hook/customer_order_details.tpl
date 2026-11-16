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

{block name='order_infos'}
    <div id="order-infos">
        <div class="box">
            <div class="row">
                <div class="col-xs-{if $order.details.reorder_url|escape:'htmlall':'UTF-8'}9{else}12{/if}">
                    <strong>
                        {l
                            s='Order Reference %reference% - placed on %date%'
                            d='Shop.Theme.Customeraccount'
                            sprintf=['%reference%' => $order.details.reference, '%date%' => $order.details.order_date]
                        }
                    </strong>
                </div>
                {if $order.details.reorder_url}
                    <div class="col-xs-3 text-xs-right">
                        {if !$objGiftCardOrderDetails->getAllInfoByOrderId(
                            $idOrder,
                            Context::getContext()->shop->id
                        )}
                            <a href="{$order.details.reorder_url|escape:'htmlall':'UTF-8'}" class="button-primary">{l s='Reorder' d='Shop.Theme.Actions'}</a>
                        {/if}
                    </div>
                {/if}
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="box">
            <ul>
                {if $order.carrier.name|escape:'htmlall':'UTF-8'}
                    <li><strong>{l s='Carrier' d='Shop.Theme.Checkout'}</strong> {$order.carrier.name|escape:'htmlall':'UTF-8'}</li>
                {/if}
                <li><strong>{l s='Payment method' d='Shop.Theme.Checkout'}</strong> {$order.details.payment|escape:'htmlall':'UTF-8'}</li>

                {if $order.details.invoice_url}
                    <li>
                        <a href="{$order.details.invoice_url|escape:'htmlall':'UTF-8'}">
                        {l s='Download your invoice as a PDF file.' mod='wkgiftcard'}
                        </a>
                    </li>
                {/if}

                {if $order.details.recyclable}
                    <li>
                        {l s='You have given permission to receive your order in recycled packaging.' mod='wkgiftcard'}
                    </li>
                {/if}

                {if $order.details.gift_message}
                    <li>{l s='You have requested gift wrapping for this order.' mod='wkgiftcard'}</li>
                    <li>{l s='Message' d='Shop.Theme.Customeraccount'} {$order.details.gift_message nofilter}</li>
                {/if}
            </ul>
        </div>
    </div>
{/block}

{block name='order_detail'}
    <div class="box hidden-sm-down">
        <table id="order-products" class="table table-bordered">
            <thead class="thead-default">
                <tr>
                    <th>{l s='Product' mod='wkgiftcard'}</th>
                    <th>{l s='Quantity' mod='wkgiftcard'}</th>
                    <th>{l s='Unit price' mod='wkgiftcard'}</th>
                    <th>{l s='Total price' mod='wkgiftcard'}</th>
                </tr>
            </thead>
            {foreach from=$order.products item=product}
                <tr>
                    <td>
                        <strong>
                            <a href="{$urls.pages.product|escape:'htmlall':'UTF-8'}&id_product={$product.id_product|escape:'htmlall':'UTF-8'}">
                                {$product.name|escape:'htmlall':'UTF-8'}
                            </a>
                        </strong><br />
                        {if $product.product_reference}
                            {l s='Reference' mod='wkgiftcard'}: {$product.product_reference|escape:'htmlall':'UTF-8'}<br />
                        {/if}
                        {if isset($product.download_link)}
                            <a href="{$product.download_link|escape:'htmlall':'UTF-8'}">{l s='Download' mod='wkgiftcard'}</a><br />
                        {/if}
                        {if $product.is_virtual}
                            {l s='Virtual products can\'t be returned.' d='Shop.Theme.Customeraccount'}</br>
                        {/if}
                        {if $product.customizations}
                            {foreach from=$product.customizations item="customization"}
                                {if $objGiftCardOrderDetails->getAllInfoByOrderId(
                                    $idOrder,
                                    Context::getContext()->shop->id,
                                    $product.id_customization
                                )}
                                    {*Start- custom hook for Gift card produt recipient details *}
                                    {hook h='displayAfterProductName' product=$product}
                                    {* End- custom hook for Gift card produt recipient details *}
                                {else}
                                    <div class="customization">
                                        <a href="#" data-toggle="modal"
                                            data-target="#product-customizations-modal-{$customization.id_customization|escape:'htmlall':'UTF-8'}">{l s='Product customization' mod='wkgiftcard'}
                                        </a>
                                    </div>
                                    <div id="_desktop_product_customization_modal_wrapper_{$customization.id_customization|escape:'htmlall':'UTF-8'}">
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
                                                        <h4 class="modal-title">{l s='Product customization' mod='wkgiftcard'}</h4>
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
                                    </div>
                                {/if}
                            {/foreach}
                        {/if}
                    </td>
                    <td>
                        {if $product.customizations}
                            {foreach $product.customizations as $customization}
                                {$customization.quantity|escape:'htmlall':'UTF-8'}
                            {/foreach}
                        {else}
                            {$product.quantity|escape:'htmlall':'UTF-8'}
                        {/if}
                    </td>
                    <td class="text-xs-right">{$product.price|escape:'htmlall':'UTF-8'}</td>
                    {if isset($product.id_customization)}
                        {if ($objGiftCardOrderDetails->getAllInfoByOrderId(
                            $idOrder,
                            Context::getContext()->shop->id,
                            $product.id_customization
                        )) }
                            <td class="text-xs-right">{Tools::displayPrice($product.total_price)|escape:'htmlall':'UTF-8'}</td>
                        {else}
                            <td class="text-xs-right">{$product.total|escape:'htmlall':'UTF-8'}</td>
                        {/if}
                    {else}
                        <td class="text-xs-right">{$product.total|escape:'htmlall':'UTF-8'}</td>
                    {/if}
                </tr>
            {/foreach}
            <tfoot>
                {foreach $order.subtotals as $line}
                    {if $line.value}
                        <tr class="text-xs-right line-{$line.type|escape:'htmlall':'UTF-8'}">
                            <td colspan="3">{$line.label|escape:'htmlall':'UTF-8'}</td>
                            <td>{$line.value|escape:'htmlall':'UTF-8'}</td>
                        </tr>
                    {/if}
                {/foreach}
                <tr class="text-xs-right line-{$order.totals.total.type|escape:'htmlall':'UTF-8'}">
                    <td colspan="3">{$order.totals.total.label|escape:'htmlall':'UTF-8'}</td>
                    <td>{$order.totals.total.value|escape:'htmlall':'UTF-8'}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="order-items hidden-md-up box">
        {foreach from=$order.products item=product}
            <div class="order-item">
                <div class="row">
                    <div class="col-sm-5 desc">
                        <div class="name">{$product.name|escape:'htmlall':'UTF-8'}</div>
                        {if $product.product_reference}
                            <div class="ref">{l s='Reference' mod='wkgiftcard'}: {$product.product_reference|escape:'htmlall':'UTF-8'}</div>
                        {/if}
                        {if isset($product.download_link)}
                            <a href="{$product.download_link|escape:'htmlall':'UTF-8'}">{l s='Download' mod='wkgiftcard'}</a><br />
                        {/if}
                        {if $product.customizations}
                            {foreach $product.customizations as $customization}
                                <div class="customization">
                                    <a href="#" data-toggle="modal"
                                        data-target="#product-customizations-modal-{$customization.id_customization|escape:'htmlall':'UTF-8'}">{l s='Product customization' mod='wkgiftcard'}</a>
                                </div>
                                <div id="_mobile_product_customization_modal_wrapper_{$customization.id_customization|escape:'htmlall':'UTF-8'}">
                                </div>
                            {/foreach}
                        {/if}
                    </div>
                    <div class="col-sm-7 qty">
                        <div class="row">
                            <div class="col-xs-4 text-sm-left text-xs-left">
                                {$product.price|escape:'htmlall':'UTF-8'}
                            </div>
                            <div class="col-xs-4">
                                {if $product.customizations}
                                    {foreach $product.customizations as $customization}
                                        {$customization.quantity|escape:'htmlall':'UTF-8'}
                                    {/foreach}
                                {else}
                                    {$product.quantity|escape:'htmlall':'UTF-8'}
                                {/if}
                            </div>
                            <div class="col-xs-4 text-xs-right">
                                {$product.total|escape:'htmlall':'UTF-8'}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/foreach}
    </div>
    <div class="order-totals hidden-md-up box">
        {foreach $order.subtotals as $line}
            {if $line.value}
                <div class="order-total row">
                    <div class="col-xs-8"><strong>{$line.label|escape:'htmlall':'UTF-8'}</strong></div>
                    <div class="col-xs-4 text-xs-right">{$line.value|escape:'htmlall':'UTF-8'}</div>
                </div>
            {/if}
        {/foreach}
        <div class="order-total row">
            <div class="col-xs-8"><strong>{$order.totals.total.label|escape:'htmlall':'UTF-8'}</strong></div>
            <div class="col-xs-4 text-xs-right">{$order.totals.total.value|escape:'htmlall':'UTF-8'}</div>
        </div>
    </div>
{/block}