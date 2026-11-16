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

{if $blockblogis17 == 1}
        <h2>{l s='Blog Authors' mod='blockblog'}</h2>
    {else}
        {capture name=path}
            {l s='Blog Authors' mod='blockblog'}
        {/capture}
    {/if}





    <div class="b-inside-pages {if $blockblogis17 == 1}block-categories{/if}">

        <div id="top" class="b-column-c">

            <div class="b-wrapper">

                <div class="b-tab {if $blockblogis16 == 1}b-tab-16-profile-page{/if}">
                    <ul>
                        <li class="current"><a href="#">{l s='Authors' mod='blockblog'} ({$blockblogdata_count_customers|escape:'htmlall':'UTF-8'})</a></li>
                    </ul>
                </div>

                <div class="b-search-friends">



                    <form onsubmit="return false;" method="post" action="#">

                        <fieldset>
                            <input type="submit" value="go" class="button_mini_custom" onclick="go_page_blockblog(0,'authors','search',ajax_url_blockblog)">
                            <input type="text" class="txt"  name="search" id="search"
                                   onfocus="{literal}if(this.value == '{/literal}{l s='Find in Authors List' mod='blockblog'}{literal}') {this.value='';};{/literal}" onblur="{literal}if(this.value == '') {this.value='{/literal}{l s='Find in Authors List' mod='blockblog'}{literal}';};{/literal}"
                                   onblur="{literal}if(this.value == '{/literal}{l s='Find in Authors List' mod='blockblog'}{literal}') {this.value='';};{/literal}" onblur="{literal}if(this.value == '') {this.value='{/literal}{l s='Find in Authors List' mod='blockblog'}{literal}';};{/literal}"
                                   value="{l s='Find in Authors List' mod='blockblog'}" />

                            <a rel="nofollow" href="javascript:void(0)" id="clear-search-users" class="clear-search-items display-none"
                               onclick="go_page_blockblog(0,'authors',ajax_url_blockblog)">
                                {l s='Clear search' mod='blockblog'}
                            </a>


                        </fieldset>
                    </form>

                </div>

                {if $blockblogis_search == 1}
                    <h3 class="search-result-item-user">{l s='Results for' mod='blockblog'} <b>"{$blockblogsearch|escape:'quotes':'UTF-8'}"</b></h3>
                    <br/>
                {/if}

                <div class="b-friends-list">
                    {if count($blockblogcustomers)>0}
                        <ul id="blog-items" class="{if $blockblogblog_layout_typeblog_authors_alias == 1}blockblog-list-view-authors{else}blockblog-grid-view-authors{/if}">


                            {assign var="list_authors" value="modules/blockblog/views/templates/front/list_authors.tpl"}

                            {if file_exists("{$tpl_dir}{$list_authors}")}
                                {include file="{$tpl_dir}{$list_authors}"}
                            {else}
                                {include file="{$list_authors}"}
                            {/if}



                        </ul>
                    {else}
                        <div class="blockblog-not-found">
                            {l s='Blog Authors not found' mod='blockblog'}
                        </div>
                    {/if}

                </div>

                {if count($blockblogcustomers)>0}
                    <div class="paging-users-custom" id="page_nav">
                        {$blockblogpaging nofilter}
                    </div>
                {/if}


            </div>


        </div>

    </div>


