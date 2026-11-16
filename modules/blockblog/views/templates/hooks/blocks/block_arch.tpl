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

{if $blockblogisdisablebl == 0 || sizeof($blockblogarch)>0}

        {if $blockblogalias == "footer"}
            {if $blockblogis17 == 1}
                <div class="col-xs-12 col-sm-3 wrapper links block-archives17">
            {else}
                <section class="blockblogarch_block_footer footer-block col-xs-12 col-sm-3">
            {/if}
        {else}
            <div id="blockblogarch_block_{$blockblogalias|escape:'htmlall':'UTF-8'}" class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if} search_blog" >
        {/if}
        <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if} {if $blockblogis17 == 1 && $blockblogalias == "footer"}h3 hidden-sm-down{/if}">{l s='Blog Archives' mod='blockblog'}</h4>

        {if $blockblogalias == "footer"}
            {if $blockblogis17 == 1}
                <div data-toggle="collapse" data-target="#blockblogarch_block_footer" class="title clearfix hidden-md-up">
                    <span class="h3">{l s='Blog Archives' mod='blockblog'}</span>
                                <span class="pull-xs-right">
                                  <span class="navbar-toggler collapse-icons">
                                    <i class="material-icons add">&#xE313;</i>
                                    <i class="material-icons remove">&#xE316;</i>
                                  </span>
                                </span>
                </div>
            {/if}
        {/if}

        <div class="block_content {if $blockblogalias == "footer"}block-items-data toggle-footer {if $blockblogis17 == 1}collapse{/if}{/if}" {if $blockblogalias == "footer"}{if $blockblogis17 == 1}id="blockblogarch_block_footer"{/if}{/if}>
            {if sizeof($blockblogarch)>0}
                <ul class="bullet">
                    {foreach from=$blockblogarch item=items key=year name=myarch}
                        <li><a class="arch-category" href="javascript:void(0)"
                               onclick="show_arch({$smarty.foreach.myarch.index|escape:'htmlall':'UTF-8'},'left')">{$year|escape:'htmlall':'UTF-8'}</a></li>
                        <div id="arch{$smarty.foreach.myarch.index|escape:'htmlall':'UTF-8'}left"
                             {if $smarty.foreach.myarch.first}{else}class="display-none"{/if}
                                >
                            {foreach from=$items item=item name=myLoop1}
                                <li class="arch-subcat">
                                    <a class="arch-subitem" href="{$blockblogposts_url|escape:'htmlall':'UTF-8'}{if $blockblogurlrewrite_on == 1}?{else}&{/if}y={$year|escape:'htmlall':'UTF-8'}&m={$item.month|escape:'htmlall':'UTF-8'}">
                                        {$item.time_add|date_format:"%B"|escape:'htmlall':'UTF-8'}&nbsp;({$item.total|escape:'htmlall':'UTF-8'})
                                    </a>

                                </li>
                            {/foreach}
                        </div>
                    {/foreach}
                </ul>
            {else}
                {l s='There are not archives yet.' mod='blockblog'}
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