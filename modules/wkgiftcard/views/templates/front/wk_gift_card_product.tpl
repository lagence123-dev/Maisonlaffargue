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
{if isset($total_items) && !empty($total_items)}
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
{else}
    <div class="wk_background_white wk_search_row my-2 p-1">
            <div class='alert alert-warning'>{l s='No gift card found' mod='wkgiftcard'}</div>
    </div>
{/if}