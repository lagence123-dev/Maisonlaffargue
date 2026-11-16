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

{capture name=path}
    {$meta_title|escape:'htmlall':'UTF-8'}
{/capture}

{if $blockblogis16 == 0}
    <h2 class="background-none">{$meta_title|escape:'htmlall':'UTF-8'}</h2>
{else}
    <h1 class="page-heading">{$meta_title|escape:'htmlall':'UTF-8'}</h1>
{/if}


{if $blockblogis_search == 1}
    <div class="clear"></div>
    <h3 class="float-right">{l s='Results for' mod='blockblog'} "{$blockblogsearch|escape:'htmlall':'UTF-8'}"</h3>
    <div class="clear"></div>
{/if}


{if $blockblogis_arch == 1}
    {assign var=postsarch value=$posts}
    <div class="clear"></div>
    <h3 class="float-right">{l s='Results for' mod='blockblog'} "{$postsarch[0].time_add|date_format:"%B"} {$blockblogyear|escape:'htmlall':'UTF-8'}"</h3>
    <div class="clear"></div>
{/if}


{if $blockblogslider_b_on == 1}
    {if count($blockblogslides)>0}
        <div class="text-align-center">
            <div id="fancytr-blockblog">
                <div id="fancytr">
                    {foreach from=$blockblogslides item=slide name=myLoop}
                        <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$slide.img|escape:'htmlall':'UTF-8'}" alt="{$slide.title|escape:'htmlall':'UTF-8'}" />
                        <a href="{if $blockblogurlrewrite_on == 1}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$slide.seo_url|escape:'htmlall':'UTF-8'}
                          {else}
                            {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$slide.id|escape:'htmlall':'UTF-8'}
                          {/if}" title="{$slide.title|escape:'htmlall':'UTF-8'}"></a>
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="clear"></div>
    {/if}
{/if}




<div class="blog-header-toolbar">
    {if $count_all > 0}

        <div class="toolbar-top">

            <div class="{if $blockblogis16==1}sortTools sortTools16{else}sortTools{/if}">
                <ul class="actions">
                    <li class="frst">
                        <strong>{l s='Posts' mod='blockblog'}  ( {$count_all|escape:'htmlall':'UTF-8'} )</strong>
                    </li>
                </ul>

                {if $blockblogrsson == 1}
                    <ul class="sorter">
                        <li>
				<span>


					<a href="{$blockblogrss_url|escape:'htmlall':'UTF-8'}" title="{l s='RSS Feed' mod='blockblog'}" target="_blank">
                        <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/feed.png" alt="{l s='RSS Feed' mod='blockblog'}" />
                    </a>
				</span>
                        </li>

                    </ul>
                {/if}

            </div>

        </div>



        <ul class="blog-posts {if $blockblogblog_layout_typeblog_alias == 2}blockblog-grid-view{elseif $blockblogblog_layout_typeblog_alias == 3}blockblog-grid-view-second{else}blockblog-list-view{/if}" id="blog-items">
            {assign var=is_all_posts_page value=1}


            {assign var="list_posts" value="modules/blockblog/views/templates/front/list_posts.tpl"}

            {if file_exists("{$tpl_dir}{$list_posts}")}
                {include file="{$tpl_dir}{$list_posts}"}
            {else}
                {include file="{$list_posts}"}
            {/if}


        </ul>





        <div class="toolbar-paging">
            <div class="text-align-center" id="page_nav">
                {$paging nofilter}
            </div>
        </div>







    {else}
        <div class="block-no-items">
            {l s='There are not posts yet' mod='blockblog'}
        </div>
    {/if}

</div>

