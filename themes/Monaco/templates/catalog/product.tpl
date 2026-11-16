{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
    * @copyright 2007-2020 PrestaShop SA
    * @license https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}
{extends file=$layout}
{block name='head_seo' prepend}
    <link rel="canonical" href="{$product.canonical_url}">
{/block}
{block name='head' append}
    <meta property="og:type" content="product">
    <meta property="og:url" content="{$urls.current_url}">
    <meta property="og:title" content="{$page.meta.title}">
    <meta property="og:site_name" content="{$shop.name}">
    <meta property="og:description" content="{$page.meta.description}">
    <meta property="og:image" content="{$product.cover.large.url}">
    <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}">
    <meta property="product:pretax_price:currency" content="{$currency.iso_code}">
    <meta property="product:price:amount" content="{$product.price_amount}">
    <meta property="product:price:currency" content="{$currency.iso_code}">
    {if isset($product.weight) && ($product.weight != 0)}
        <meta property="product:weight:value" content="{$product.weight}">
        <meta property="product:weight:units" content="{$product.weight_unit}">
    {/if}
{/block}
{block name='head_microdata_special'}
    {include file='_partials/microdata/product-jsonld.tpl'}
{/block}

{block name='content'}
    <div class="revealOnScroll animated fadeInUp" data-animation="fadeInUp" id="main">
        <div class="row">
            <div class="col-md-6 mt-md-4">
                {block name='page_content_container'}
                    <section class="page-content" id="content">
                        {block name='page_content'}
                            {block name='product_cover_thumbnails'}
                                {include file='catalog/_partials/product-cover-thumbnails.tpl'}
                            {/block}
                        {/block}
                    </section>
                {/block}
            </div>
            <div class="col-md-6 product-page-right mt-md-4">
                {block name='breadcrumb'}
                    {include file='_partials/breadcrumb.tpl'}
                {/block}

                {block name='page_header_container'}
                    {block name='page_header'}
                        <h1 class="h1">{block name='page_title'}{$product.name}{/block}</h1>
                    {/block}
                {/block}
                {if $product.reference}
                    <div class="product-reference">
                        <label class="label">{l s='Reference:' d='Shop.Theme.Catalog'} </label>
                        <span>{$product.reference}</span>
                    </div>
                {/if}
                {block name='product_prices'}
                    {include file='catalog/_partials/product-prices.tpl'}
                    {hook h='displayProductPriceBlock' product=$product type="before_price"}
                {/block}
                {block name='product_reviews'}
                    {hook h='displayProductListReviews' product=$product}
                {/block}
                <div class="product-information">
                    {block name='product_description_short'}
                        <div class="product-description-short" id="product-description-short-{$product.id}">
                            {$product.description_short nofilter}
                        </div>
                    {/block}

                    <div class="product-actions">
                        {block name='product_buy'}
                            <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                                <div class="image-sticky-wrapper">
                                    <img class="js-qv-product-cover" src="{$product.cover.bySize.large_default.url}"
                                        alt="{$product.cover.legend}" title="{$product.cover.legend}" style="width:100%;">
                                </div>
                                <div class="bon-sticky-name">
                                    <h1 class="h1">
                                        {block name='page_title'}{$product.name|truncate:15:""}{/block}</h1>
                                    {block name='product_prices'}
                                        {include file='catalog/_partials/product-prices.tpl'}
                                        {hook h='displayProductPriceBlock' product=$product type="before_price"}
                                    {/block}
                                </div>
                                <input type="hidden" name="token" value="{$static_token}">
                                <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                                <input type="hidden" name="id_customization" value="{$product.id_customization}"
                                    id="product_customization_id">
                                {block name='product_pack'}
                                    {if $packItems}
                                        <section class="product-pack">
                                            <p class="h4">{l s='This pack contains' d='Shop.Theme.Catalog'}</p>
                                            {foreach from=$packItems item="product_pack"}
                                                {block name='product_miniature'}
                                                    {include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack}
                                                {/block}
                                            {/foreach}
                                        </section>
                                    {/if}
                                {/block}
                                {block name='product_discounts'}
                                    {include file='catalog/_partials/product-discounts.tpl'}
                                {/block}
                                {block name='product_variants'}
                                    {include file='catalog/_partials/product-variants.tpl'}

                                {/block}
                                {if $product.is_customizable && count($product.customizations.fields)}
                                    {block name='product_customization'}
                                        {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations form=false}
                                    {/block}
                                {/if}
                                {block name='product_add_to_cart'}
                                    {include file='catalog/_partials/product-add-to-cart.tpl'}
                                {/block}
                                {* Input to refresh product HTML removed, block kept for compatibility with themes *}
                                {block name='product_refresh'}{/block}
                            </form>
                            {if $product.is_customizable && count($product.customizations.fields)}
                                {block name='product_customization'}
                                    {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations form=true}
                                {/block}
                            {/if}
                        {/block}
                        {* {hook h='displayOneClick'} *}
                        {block name='product_additional_info'}
                            {include file='catalog/_partials/product-additional-info.tpl'}
                        {/block}
                    </div>

                    {hook h='CETemplate' id=15}

                    {block name='product_tabs'}
                        <div class="tabs product-tabs mb-5">
                            <div class="accordion" id="productAccordion" role="tablist" aria-multiselectable="true">

                                {if $product.description}
                                    <div class="tab" role="tab">
                                        <div id="heading-description" class="tab-header">
                                            <button
                                                class="btn btn-block text-left d-flex justify-content-between align-items-center collapsed"
                                                type="button" data-toggle="collapse" data-target="#description"
                                                aria-expanded="false" aria-controls="description">
                                                <span>{l s='Description' d='Shop.Theme.Catalog'}</span>
                                                <i class="material-icons add">add</i>
                                                <i class="material-icons remove">remove</i>
                                            </button>
                                        </div>
                                        <div id="description" class="tab-content collapse" role="tabpanel"
                                            aria-labelledby="heading-description" data-parent="#productAccordion">
                                            {block name='product_description'}
                                                <div class="product-description">{$product.description nofilter}</div>
                                            {/block}
                                        </div>
                                    </div>
                                {/if}

                                {if $product.grouped_features}
                                    {block name='product_details'}
                                        <div class="tab" role="tab">
                                            <div id="heading-product-details" class="tab-header">
                                                <button
                                                    class="btn btn-block text-left d-flex justify-content-between align-items-center collapsed"
                                                    type="button" data-toggle="collapse" data-target="#product-details"
                                                    aria-expanded="false" aria-controls="product-details">
                                                    <span>{l s='Product Details' d='Shop.Theme.Catalog'}</span>
                                                    <i class="material-icons add">add</i>
                                                    <i class="material-icons remove">remove</i>
                                                </button>
                                            </div>
                                            {include file='catalog/_partials/product-details.tpl'}
                                        </div>
                                    {/block}
                                {/if}

                                {block name='product_attachments'}
                                    {if $product.attachments}
                                        <div class="tab" role="tab">
                                            <div id="heading-attachments" class="tab-header">
                                                <button
                                                    class="btn btn-block text-left d-flex justify-content-between align-items-center collapsed"
                                                    type="button" data-toggle="collapse" data-target="#attachments"
                                                    aria-expanded="false" aria-controls="attachments">
                                                    <span>{l s='Attachments' d='Shop.Theme.Catalog'}</span>
                                                    <i class="material-icons add">add</i>
                                                    <i class="material-icons remove">remove</i>
                                                </button>
                                            </div>
                                            <div id="attachments" class="tab-content collapse" role="tabpanel"
                                                aria-labelledby="heading-attachments" data-parent="#productAccordion">
                                                <section class="product-attachments">
                                                    {foreach from=$product.attachments item=attachment}
                                                        <div class="attachment">
                                                            <h2><a
                                                                    href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">{$attachment.name}</a>
                                                            </h2>
                                                            <p>{$attachment.description}</p>
                                                            <a class="btn btn-primary btn-download"
                                                                href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                                                                {l s='Download' d='Shop.Theme.Actions'}
                                                                ({$attachment.file_size_formatted})
                                                            </a>
                                                        </div>
                                                    {/foreach}
                                                </section>
                                            </div>
                                        </div>
                                    {/if}
                                {/block}

                                {foreach from=$product.extraContent item=extra key=extraKey}
                                    <div class="tab" role="tab">
                                        <div id="heading-extra-{$extraKey}" class="tab-header">
                                            <button
                                                class="btn btn-block text-left d-flex justify-content-between align-items-center collapsed reviewtab"
                                                type="button" data-toggle="collapse" data-target="#extra-{$extraKey}"
                                                aria-expanded="false" aria-controls="extra-{$extraKey}">
                                                <span>{$extra.title}</span>
                                                <i class="material-icons add">add</i>
                                                <i class="material-icons remove">remove</i>
                                            </button>
                                        </div>
                                        <div id="extra-{$extraKey}" class="tab-content collapse {$extra.attr.class}" role="tabpanel"
                                            aria-labelledby="heading-extra-{$extraKey}" data-parent="#productAccordion">
                                            {$extra.content nofilter}
                                        </div>
                                    </div>
                                {/foreach}

                                {hook h="displayProductTab"}
                                {hook h="displayProductTabContent"}
                            </div>
                        </div>
                    {/block}

                    {block name='hook_display_reassurance'}
                        {hook h='displayReassurance'}
                    {/block}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {block name='product_accessories'}
                    {if $accessories}
                        <section class="product-accessories clearfix">
                            <h2 class="h2 products-section-title revealOnScroll animated fadeInUp" data-animation="fadeInUp">{l s='You might
                                also like' d='Shop.Theme.Catalog'}</h2>
                            <div class="products row">
                                {foreach from=$accessories item="product_accessory"}
                                    {block name='product_miniature'}
                                        {include file='catalog/_partials/miniatures/product.tpl' product=$product_accessory}
                                    {/block}
                                {/foreach}
                            </div>
                        </section>
                    {/if}
                {/block}
            </div>
        </div>
        {block name='product_footer'}
            {hook h='displayFooterProduct' product=$product}
        {/block}
        {block name='product_images_modal'}
            {include file='catalog/_partials/product-images-modal.tpl'}
        {/block}
        {block name='page_footer_container'}
            <footer class="page-footer">
                {block name='page_footer'}
                    <!-- Footer content -->
                {/block}
            </footer>
        {/block}
    </div>
{/block}

{block name='javascript_bottom'}
    {$smarty.block.parent}
    {if $product.is_customizable && count($product.customizations.fields)}
        {literal}
            <script>
                $(document).ready(function() {
                    // Fonction pour afficher ou masquer la section de personnalisation
                    function toggleCustomizationSection() {
                        const selectedSize = $("#group_14").val();
                        const customizationSection = $("#customizationSection");

                        // Affiche la section seulement si la valeur selectionnee est "608" (autre)
                        if (selectedSize === "608") {
                            customizationSection.show();
                        } else {
                            customizationSection.hide();
                        }
                    }

                    // Attacher l'événement de changement
        $("#group_14").change(toggleCustomizationSection);

        // Appel initial pour s'assurer que la section est bien masquée au démarrage
                    toggleCustomizationSection();

                    $(document).on('click', '#product-customization-form-div button', function() {
                        let productCustomizationForm = $('#product-customization-form');

                        productCustomizationForm.attr('action', $('#product-customization-form-div').attr(
                            'data-action'));
                        $('#product-customization-form-div textarea').each(function(index, field) {
                            productCustomizationForm.find('textarea[name="' + field.name + '"]').val(field
                                .value);
                        });

                        productCustomizationForm.find('button').trigger('click');
                    });
                });
            </script>
        {/literal}
    {/if}
{/block}