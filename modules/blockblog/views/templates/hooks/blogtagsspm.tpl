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

{if $blockblogisdisablebl == 0 || sizeof($blockblogtags)>0}
    <div id="blockblogtag_block_left" class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}" >
        <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">{l s='Blog Tags' mod='blockblog'}</h4>
        <div class="block_content tags-block-blockblog">

            {if sizeof($blockblogtags)>0}

                {foreach from=$blockblogtags item=tags name=myLoop}
                    <a href="{$blockblogtag_url|escape:'htmlall':'UTF-8'}{$tags.query|escape:'htmlall':'UTF-8'}"
                       class="tag-blockblog"
                            >{$tags.query|escape:'htmlall':'UTF-8'}</a>

                {/foreach}

                <div class="clear"></div>

                <p class="block-view-all">
                    <a href="{$blockblogtags_url|escape:'htmlall':'UTF-8'}" title="{l s='View all tags' mod='blockblog'}" class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                            ><b>{l s='View all tags' mod='blockblog'}</b></a>
                </p>
            {else}
                {l s='There are not tags yet.' mod='blockblog'}
            {/if}
        </div>



    </div>

{/if}
