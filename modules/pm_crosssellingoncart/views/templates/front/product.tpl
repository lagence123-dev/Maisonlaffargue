{extends file="catalog/_partials/miniatures/product.tpl"}

{block name='product_thumbnail'}
    {if !empty($csoc_display['DISPLAY_IMG'])}
        <img src="{$product.cover.bySize[$csoc_display['IMAGE_SIZE']].url}" alt="{$product.cover.legend}" class="img-fluid mx-auto d-block mt-1">
    {/if}
{/block}

{block name='product_name'}
    {if !empty($csoc_display['DISPLAY_TITLE'])}
        {$smarty.block.parent}
    {/if}
{/block}

{block name='product_price_and_shipping'}
    {if !empty($csoc_display['DISPLAY_PRICE'])}
        {$smarty.block.parent}
    {/if}
{/block}

{if !empty($csoc_display['DISPLAY_BUTTON'])}
    {block name='product_flags'}
        {$smarty.block.parent}

        {block name='button_display'}
            <div class="button_display text-xs-center">
                <form action="{$urls.pages.cart}" method="POST">
                    <input type="hidden" name="token" value="{$static_token}">
                    <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id-{$product.id}">
                    {if isset($product.id_product_attribute)}
                        <input type="hidden" name="id_product_attribute" value="{$product.id_product_attribute}" id="idCombination-{$product.id_product_attribute}">
                    {/if}
                    <input type="hidden" name="id_customization" value="0" id="product_customization_id-{$product.id}">
                    <input type="hidden" name="qty" value="{if $product.minimal_quantity}{$product.minimal_quantity|intval}{else}1{/if}">

                    <button class="btn btn-primary add-to-cart" data-button-action="add-to-cart" type="submit">
                        <i class="material-icons shopping-cart">&#xE547;</i>
                        {l s='Add to cart' d='Shop.Theme.Actions'}
                    </button>
                </form>
            </div>
        {/block}
    {/block}
{/if}
