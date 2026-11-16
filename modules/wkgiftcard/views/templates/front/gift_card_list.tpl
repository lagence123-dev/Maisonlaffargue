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

{extends file=$layout}
{block name='content'}
    {if isset($banner_image_url_exist) && $banner_image_url_exist && isset($banner_image_url)}
        <div class='my-2 wk_search_row wk_overflow_hidden'>
            <img src='{$banner_image_url|escape:'htmlall':'UTF-8'}' class='wk_width_100' />
        </div>
    {/if}

    <div class='my-2 p-1 wk_search_row wk_background_white'>
        <h1>{l s='Gift cards' mod='wkgiftcard'}</h1>
    </div>

    {if isset($total_items) && !empty($total_items)}
        <div class="row wk_search_row wk_background_white p-1 my-2">
            <div id='js-product-list-top' class='row products-selection col-md-12'>
                <div class="col-md-7 hidden-sm-down total-products">
                    {if $total_items > 1}
                        <p>{l s='There are %product_count% products.' mod='wkgiftcard' sprintf=['%product_count%' => $total_items]}
                        </p>
                    {elseif $total_items > 0}
                        <p>{l s='There is 1 product.' mod='wkgiftcard'}</p>
                    {/if}
                </div>

                <div class="col-md-5">
                    <div class="row sort-by-row">
                        <span class="col-sm-3 col-md-3 hidden-sm-down sort-by">{l s='Sort by' mod='wkgiftcard'}:</span>
                        <div class="col-sm-12 col-xs-12 col-md-9 products-sort-order dropdown">
                            <button class="btn-unstyle select-title" rel="nofollow" data-toggle="dropdown"
                                aria-label="Sort by selection" aria-haspopup="true" aria-expanded="false">
                                {if ($order_selected == 1)}
                                    {l s='Name, A to Z' mod='wkgiftcard'}
                                {elseif ($order_selected == 2)}
                                    {l s='Name, Z to A' mod='wkgiftcard'}
                                {elseif ($order_selected == 3)}
                                    {l s='Price, low to high' mod='wkgiftcard'}
                                {elseif ($order_selected == 4)}
                                    {l s='Price, high to low' mod='wkgiftcard'}
                                {else}
                                    {l s='Select' mod='wkgiftcard'}
                                {/if}
                                <i class="material-icons float-xs-right"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a rel="nofollow"
                                    href="{$link->getModuleLink('wkgiftcard', 'giftcardlist', array('orderby' => 'name', 'orderway' => 'asc', 'q' => $search_string, 'type' => $search_type))|escape:'htmlall':'UTF-8'}"
                                    class="select-list">
                                    {l s='Name, A to Z' mod='wkgiftcard'}
                                </a>
                                <a rel="nofollow"
                                    href="{$link->getModuleLink('wkgiftcard', 'giftcardlist', array('orderby' => 'name', 'orderway' => 'desc', 'q' => $search_string, 'type' => $search_type))|escape:'htmlall':'UTF-8'}"
                                    class="select-list">
                                    {l s='Name, Z to A' mod='wkgiftcard'}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class='wk_display_flex'>
            <section class='col-md-3'>
                <div class='wk_background_white mb-2' style='p-4'>
                    <form method="get" action="{$link->getModuleLink('wkgiftcard', 'giftcardlist')|escape:'htmlall':'UTF-8'}">
                        <div class='wk_display_flex wk_flex_center wk_field_padding'>
                            <div class="form-group">
                                <input style="background: #FFF; outline: none !important;" type="text"
                                    value="{$search_string|escape:'htmlall':'UTF-8'}"
                                    placeholder="{l s='Search product' mod='wkgiftcard'}" name="q" class="form-control"
                                    autocomplete="off">
                            </div>
                            <div class="form-group wk-text-center">
                                <button type="submit" class="search_gift_cart_btn btn btn-primary">
                                    <i class="material-icons search" style="margin-right: unset;"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class='wk_background_white mb-2 wk_field_padding' style='p-4'>
                    <div class='mb-2'>
                        <h5>{l s='Price' mod='wkgiftcard'}</h5>
                    </div>
                    <a class="tag gift_card_price" data-price-name='0' href="#">
                        {l s='All' mod='wkgiftcard'}
                    </a>
                    <a class='tag gift_card_price' data-price-name='d' href="#">
                        {l s='Own price' mod='wkgiftcard'}
                    </a>

                    {if isset($staticPrices) && is_array($staticPrices)}
                        {foreach from=$staticPrices item=item}
                            <a class='tag gift_card_price' data-price-name='{$item.price|escape:'htmlall':'UTF-8'}' href="#">
                                {$item.formated_price|escape:'htmlall':'UTF-8'}
                            </a>
                        {/foreach}
                    {/if}
                </div>

                {if isset($available_tags) && count($available_tags) > 0}
                    <div class='wk_background_white mb-2 wk_field_padding' style='p-4'>
                        <div class='mb-2'>
                            <h5>{l s='Tags' mod='wkgiftcard'}</h5>
                        </div>
                        <div>
                            {foreach from=$available_tags item=item}
                                <a href='#' class='tag tag_filter_btn'
                                    data-tag-name='{$item|escape:'htmlall':'UTF-8'}'>{$item|escape:'htmlall':'UTF-8'}</a>
                            {/foreach}
                        </div>
                    </div>
                {/if}
            </section>
            <section id='giftCardProductDetails' class='col-md-9 wk_background_white wk_search_row mb-2'>
                <section class='featured-products clearfix '>
                    <div class="products py-1">
                        {if  count($products) > 0}
                            {foreach from=$products item="product"}
                                {include file="catalog/_partials/miniatures/product.tpl" product=$product productClasses="col-xs-12 col-lg-4 col-xl-4"}
                            {/foreach}
                            {block name="pagination_dustomer"}
                                {include file="module:wkgiftcard/views/templates/front/_partials/pagination.tpl"}
                            {/block}
                        {/if}
                    </div>
                </section>
            </section>

        </div>

    {else}
        <div class="wk_background_white wk_search_row my-2 p-1">
            <div class='alert alert-warning'>{l s='No gift card found' mod='wkgiftcard'}</div>
        </div>
    {/if}
{/block}