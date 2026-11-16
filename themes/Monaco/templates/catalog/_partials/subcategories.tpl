{if !empty($subcategories)}
  {if (isset($display_subcategories) && $display_subcategories eq 1) || !isset($display_subcategories) }
    <div id="subcategories">
      <ul class="subcategories-list row">
        {foreach from=$subcategories item=subcategory}
          <li class="col-auto">
            <div class="subcategory-image">
              <a href="{$subcategory.url}" title="{$subcategory.name|escape:'html':'UTF-8'}" class="img">
                {if !empty($subcategory.image.small.url)}
                  <img
                    class="img-fluid"
                    src="{$subcategory.image.small.url}"
                    alt="{$subcategory.name|escape:'html':'UTF-8'}"
                    loading="lazy"
                    width="{$subcategory.image.small.width}"
                    height="{$subcategory.image.small.height}" />
                {/if}
              </a>
            </div>
            <p class="mt-2 mb-3 text-center">
              <a class="subcategory-name" href="{$subcategory.url}">
                {$subcategory.name|truncate:25:'...'|escape:'html':'UTF-8'}
              </a>
            </p>
          </li>
        {/foreach}
      </ul>
    </div>
  {/if}
{/if}