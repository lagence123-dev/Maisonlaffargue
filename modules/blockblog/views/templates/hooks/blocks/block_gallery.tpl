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

{if $blockblogisdisablebl == 0 || sizeof($blockbloggalleryblock)>0}
        {if $blockblogalias == "footer"}
            {if $blockblogis17 == 1}
                <div class="col-xs-12 col-sm-3 wrapper links block-gallery-blog">
            {else}
                <section class="blockblogagallery_block_footer footer-block col-xs-12 col-sm-3">
            {/if}
        {else}
            <div id="blockbloggallery_block_{$blockblogalias|escape:'htmlall':'UTF-8'}" class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}" >
        {/if}

        <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if} {if $blockblogis17 == 1 && $blockblogalias == "footer"}h3 hidden-sm-down{/if}">{l s='Blog Gallery' mod='blockblog'}</h4>

        {if $blockblogalias == "footer"}
            {if $blockblogis17 == 1}
                <div data-toggle="collapse" data-target="#blockblogagallery_block_footer" class="title clearfix hidden-md-up">
                    <span class="h3">{l s='Blog Gallery' mod='blockblog'}</span>
                                <span class="pull-xs-right">
                                  <span class="navbar-toggler collapse-icons">
                                    <i class="material-icons add">&#xE313;</i>
                                    <i class="material-icons remove">&#xE316;</i>
                                  </span>
                                </span>
                </div>
            {/if}
        {/if}

        <div class="block_content gallery-block-blockblog {if $blockblogalias == "footer"}block-items-data toggle-footer {if $blockblogis17 == 1}collapse{/if}{/if}" {if $blockblogalias == "footer"}{if $blockblogis17 == 1}id="blockblogagallery_block_footer"{/if}{/if}>

            {if sizeof($blockbloggalleryblock)>0}
                <ul>
                    {foreach from=$blockbloggalleryblock item=item name=myLoop}
                        <li>

                            <a class="gallery_item" title="{$item.content|escape:'htmlall':'UTF-8'}"
                               rel="prettyPhotoGalleryBlockLeft[gallery]"
                               href="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img|escape:'htmlall':'UTF-8'}">
                                <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}gallery/{$item.img_thumb_block|escape:'htmlall':'UTF-8'}"
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



    {if $blockblogalias == "footer"}
        {if $blockblogis17 == 1}
            </div>
        {else}
            </section>
        {/if}
    {else}
        </div>
    {/if}

{/if}