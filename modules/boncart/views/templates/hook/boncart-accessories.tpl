{if $result}
  <div class="blockcart-accessories">
      <h4 class="blockcart-product-title revealOnScroll animated fadeInUp" data-animation="fadeInUp">{l s='You might also like' d='Shop.Theme.Catalog'}</h2>                             
      <div class="products row">
          {foreach from=$result item=item name=item}
              {if $smarty.foreach.item.iteration <= $limit}
                  {block name='product_miniature'}
                      {include file='catalog/_partials/miniatures/product.tpl' product=$item["info"]}
                  {/block}
              {/if}
          {/foreach}
      </div>
  </div>
{/if}
