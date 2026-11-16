{*
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 *
 * @author    StorePrestaModules SPM
 * @category content_management
 * @package blockblog
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */
*}

{foreach from=$blockbloggallerypage item=item name=myLoop}
    <li style="height:{$blockbloggallery_height|escape:'htmlall':'UTF-8'}px">

          <a class="gallery_item" title="{$item.content|escape:'htmlall':'UTF-8'}"
            rel="prettyPhotoGalleryPage[gallery]"
            href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img|escape:'htmlall':'UTF-8'}">
            <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img_thumb|escape:'htmlall':'UTF-8'}"
                title="{$item.title|escape:'htmlall':'UTF-8'}" alt="{$item.title|escape:'htmlall':'UTF-8'}"
                data-original="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img|escape:'htmlall':'UTF-8'}"
                class="lazyload" style="display: block;">
          </a>



    </li>
{/foreach}




{literal}
    <script type="text/javascript">
        {/literal}{if $blockblogis_ajax_gallery == 1}{literal}

                init_gallery_blockblog();

        {/literal}{/if}{literal}
    </script>
{/literal}

