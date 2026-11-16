{if isset($csoc_product_selection) && count($csoc_product_selection) > 0}
    {if isset($on_product_page) && $on_product_page}
        <div class="pm-csoc page-product-box product-accessories clearfix">
            {if $csoc_bloc_title}
                <h3 class="h5 text-uppercase">{$csoc_bloc_title}</h3>
            {/if}

            <div class="{$carouselCssClass} bx-wrapper block products_block csoc-block products clearfix">
                {foreach from=$csoc_product_selection item='cartProduct' name=cartProduct}
                    {block name='product_miniature'}
                        {include 'module:pm_crosssellingoncart/views/templates/front/product.tpl' product=$cartProduct}
                    {/block}
                {/foreach}
            </div>
        </div>
    {else}
        <div class="product-accessories pm-csoc">
            {if $csoc_bloc_title}
                <div class="card-block">
                    <h3 class="step-title h3">{$csoc_bloc_title}</h3>
                </div>
            {/if}
            <div class="carousel clearfix">
                {foreach from=$csoc_product_selection item='cartProduct' name=cartProduct}
                    {block name='product_miniature'}
                        {include 'module:pm_crosssellingoncart/views/templates/front/product.tpl' product=$cartProduct}
                    {/block}
                {/foreach}
            </div>
        </div>
    {/if}
{/if}
