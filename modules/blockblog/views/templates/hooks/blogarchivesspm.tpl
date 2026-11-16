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

{if $blockblogisdisablebl == 0 || sizeof($blockblogarch)>0}

        <div id="blockblogarch_block_left" class="block {if $blockblogis17 == 1}block-categories hidden-sm-down{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if} search_blog" >
            <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">{l s='Blog Archives' mod='blockblog'}</h4>

            <div class="block_content">
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

        </div>

    {/if}
