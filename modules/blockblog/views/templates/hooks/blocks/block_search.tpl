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

{if $blockblogisdisablebl == 0 || count($blockblogposts) > 0}

        {if $blockblogalias == "footer"}
            {if $blockblogis17 == 1}
                <div class="col-xs-12 col-sm-3 wrapper links block-search-in-blog">
            {else}
                <section class="blockblogasearch_block_footer footer-block col-xs-12 col-sm-3">
            {/if}
        {else}

            <div id="blockblogasearch_block_{$blockblogalias|escape:'htmlall':'UTF-8'}" class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if} search_blog" >
        {/if}
        <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if} {if $blockblogis17 == 1 && $blockblogalias == "footer"}h3 hidden-sm-down{/if}">{l s='Search in Blog' mod='blockblog'}</h4>

        {if $blockblogalias == "footer"}
            {if $blockblogis17 == 1}
                <div data-toggle="collapse" data-target="#blockblogasearch_block_footer" class="title clearfix hidden-md-up">
                    <span class="h3">{l s='Search in Blog' mod='blockblog'}</span>
                                <span class="pull-xs-right">
                                  <span class="navbar-toggler collapse-icons">
                                    <i class="material-icons add">&#xE313;</i>
                                    <i class="material-icons remove">&#xE316;</i>
                                  </span>
                                </span>
                </div>
            {/if}
            <div class="block-items-data toggle-footer {if $blockblogis17 == 1}collapse{/if}" {if $blockblogis17 == 1}id="blockblogasearch_block_footer"{/if}>
        {/if}


        <form method="{if $blockblogurlrewrite_on == 1}get{else}post{/if}" action="{$blockblogposts_url|escape:'htmlall':'UTF-8'}">
            <div class="block_content">
                <input type="text" value="" class="search-blog {if $blockblogis17 == 1}search-blog17{/if}" name="search" {if $blockblogis_ps15 == 0}class="search_text"{/if}>
                <input type="submit" value="go" class="{if $blockblogis17 == 1}button-mini-blockblog{/if} button_mini {if $blockblogis_ps15 == 0}search_go{/if}"/>
                {if $blockblogis_ps15 == 0}<div class="clear"></div>{/if}
            </div>
        </form>

    {if $blockblogalias == "footer"}
        </div>
        {if $blockblogis17 == 1}
            </div>
        {else}
            </section>
        {/if}
    {else}
	    </div>
    {/if}

{/if}