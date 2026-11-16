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

{if $blockblogisdisablebl == 0 || count($blockblogposts) > 0}
    <div id="blockblogsearch_block_left" class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if} search_blog" >
        <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">{l s='Search in Blog' mod='blockblog'}</h4>
        <form method="{if $blockblogurlrewrite_on == 1}get{else}post{/if}" action="{$blockblogposts_url|escape:'htmlall':'UTF-8'}">
            <div class="block_content">
                <input type="text" value="" class="search-blog {if $blockblogis17 == 1}search-blog17{/if}" name="search" {if $blockblogis_ps15 == 0}class="search_text"{/if}>
                <input type="submit" value="go" class="{if $blockblogis17 == 1}button-mini-blockblog{/if} button_mini {if $blockblogis_ps15 == 0}search_go{/if}"/>
                {if $blockblogis_ps15 == 0}<div class="clear"></div>{/if}
            </div>
        </form>
    </div>

{/if}
