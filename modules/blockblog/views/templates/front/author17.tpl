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

{extends file='page.tpl'}






{block name="content_wrapper"}

{block name="left_column"}
    {if isset($blockblogsidebar_posblog_author_alias) && $blockblogsidebar_posblog_author_alias == 1}
        <div id="left-column" class="col-xs-12 col-sm-4 col-md-3">
            {hook h="displayLeftColumn"}
        </div>
    {/if}
{/block}


<div id="content-wrapper" class="card card-block {if isset($blockblogsidebar_posblog_author_alias) && $blockblogsidebar_posblog_author_alias == 1}left-column col-xs-12 col-sm-8 col-md-9{elseif isset($blockblogsidebar_posblog_author_alias) && $blockblogsidebar_posblog_author_alias == 2}right-column col-xs-12 col-sm-8 col-md-9{/if}">
    {block name="page_content"}

        {if $blockblogis17 == 1}
            <nav data-depth="2" class="breadcrumb hidden-sm-down">
                <ol itemscope="" itemtype="http://schema.org/BreadcrumbList">
                    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                        <a itemprop="item" href="{$blockblogauthors_url|escape:'htmlall':'UTF-8'}">
                            <span itemprop="name">{l s='Blog Authors' mod='blockblog'}</span>
                        </a>
                        <meta itemprop="position" content="1">
                    </li>

                    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                        <a itemprop="item" href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$blockblogauthor_id|escape:'htmlall':'UTF-8'}">
                            <span itemprop="name">{$blockblogauthor_name|escape:'htmlall':'UTF-8'}</span>
                        </a>
                        <meta itemprop="position" content="2">
                    </li>
                </ol>
            </nav>

        {/if}


        {capture name=path}
            <a href="{$blockblogauthors_url|escape:'htmlall':'UTF-8'}">
                <span>{l s='Blog Authors' mod='blockblog'}</span>
            </a>
            <span class="navigation-pipe">&gt;</span>
            {$blockblogauthor_name|escape:'htmlall':'UTF-8'}
        {/capture}

        <div class="row-custom blog-author-intro">
            <div class="col-sm-2-custom text-align-center">

                <span class="avatar-block-info">
                    <img alt="{$blockblogauthor_name|escape:'htmlall':'UTF-8'}" src="{$blockblogavatar|escape:'htmlall':'UTF-8'}" />
                </span>

            </div>
            <div class="col-sm-10-custom">
                <h1 class="page-heading blog-author-title">
                {l s='Author' mod='blockblog'}: {$blockblogauthor_name|escape:'htmlall':'UTF-8'}
                </h1>
                {if strlen($blockblogauthor_info)>0}
                    <div class="blog-author-info">
                        <p>{$blockblogauthor_info|escape:'htmlall':'UTF-8'}</p>
                    </div>
                {/if}
            </div>
            <div class="clear"></div>
        </div>



    <div class="blog-header-toolbar block-data-category">
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


            <ul class="blog-posts {if $blockblogblog_layout_typeblog_author_alias == 2}blockblog-grid-view{elseif $blockblogblog_layout_typeblog_author_alias == 3}blockblog-grid-view-second{else}blockblog-list-view{/if}" id="blog-items">
                {assign var=is_author_page value=1}
                {include file="module:blockblog/views/templates/front/list_posts.tpl"}



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

        </div>
    {/block}



    {block name="right_column"}
        {if isset($blockblogsidebar_posblog_author_alias) && $blockblogsidebar_posblog_author_alias == 2}
            <div id="right-column" class="col-xs-12 col-sm-4 col-md-3">
                {hook h="displayRightColumn"}
            </div>
        {/if}

    {/block}




{/block}
