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

{if $blockblogisdisablebl == 0 || sizeof($blockbloggalleryblockhook)>0}
        <div id="blockbloggallery_block_left" class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}" >
            <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">{l s='Blog Gallery' mod='blockblog'}</h4>
            <div class="block_content gallery-block-blockblog">

                {if sizeof($blockbloggalleryblockhook)>0}
                    <ul>
                        {foreach from=$blockbloggalleryblockhook item=item name=myLoop}
                            <li>

                                <a class="gallery_item" title="{$item.content|escape:'htmlall':'UTF-8'}"
                                   rel="prettyPhotoGalleryBlockLeft[gallery]"
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

                    <p class="block-view-all">
                        <a href="{$blockbloggallery_url|escape:'htmlall':'UTF-8'}" title="{l s='View all gallery' mod='blockblog'}" class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                                ><b>{l s='View all gallery' mod='blockblog'}</b></a>
                    </p>
                {else}
                    {l s='There are not items yet.' mod='blockblog'}
                {/if}
            </div>



        </div>

    {/if}
