{*
**
 * 2011 - 2020 StorePrestaModules SPM LLC.
 *
 * MODULE blockblog
 *
 * @author    SPM <spm.presto@gmail.com>
 * @copyright Copyright (c) permanent, SPM
 * @license   Addons PrestaShop license limitation
 * @version   2.4.3
 * @link      http://addons.prestashop.com/en/2_community-developer?contributor=790166
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 *
*}

{if $blockbloggallery_on == 1}
    {if $blockbloggallery_home == 1 && count($blockbloggalleryblockhome)>0}

        {if $blockblogis17 == 1}<br/>{/if}

        {if $blockblogis16 == 1 && $blockblogis17 == 0}
            <div {if $blockblogis_ps15 !=0  && $blockblogis17 == 0}id="left_column"{/if}>
        {/if}

        <div id="blockblog_block_home_gallery"
             class="block
                    {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if} {if $blockblogis17 == 1}block-categories{/if}
                    ">
            <h4  class="title_block {if $blockblogis17 == 1}text-uppercase h6{/if}" {if $blockblogis16 != 1}align="center"{/if}>
                <a href="{$blockbloggallery_url|escape:'htmlall':'UTF-8'}"
                        >{l s='Blog Gallery' mod='blockblog'}</a>
            </h4>
            <div class="block_content gallery-block-blockblog-home">
                {if count($blockbloggalleryblockhome)>0}
                    <ul>

                        {foreach from=$blockbloggalleryblockhome item=item name=myLoop}

                            <li>

                                <a class="gallery_item" title="{$item.content|escape:'htmlall':'UTF-8'}"
                                   rel="prettyPhotoGalleryBlockHome[gallery]"
                                   href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img|escape:'htmlall':'UTF-8'}">
                                    <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img_thumb_block_home|escape:'htmlall':'UTF-8'}"
                                         title="{$item.title|escape:'htmlall':'UTF-8'}" alt="{$item.title|escape:'htmlall':'UTF-8'}"
                                         data-original="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img|escape:'htmlall':'UTF-8'}"
                                         class="lazyload">
                                </a>



                            </li>
                        {/foreach}

                    </ul>
                    <div class="clear"></div>




                    <p class="block-view-all float-right">
                        <a class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                           href="{$blockbloggallery_url|escape:'htmlall':'UTF-8'}" title="{l s='View all gallery' mod='blockblog'}">
                            <span>{l s='View all gallery' mod='blockblog'}</span>
                        </a>
                    </p>

                    <div class="clear"></div>
                {else}
                    <div class="padding-10">
                        {l s='There are not items yet.' mod='blockblog'}
                    </div>
                {/if}

            </div>
        </div>

        {if $blockblogis16 == 1 && $blockblogis17 == 0}
            </div>
        {/if}

    {/if}


{/if}