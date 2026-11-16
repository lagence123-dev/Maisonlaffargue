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
    {if isset($blockblogsidebar_posblog_alltags_alias) && $blockblogsidebar_posblog_alltags_alias == 1}
        <div id="left-column" class="col-xs-12 col-sm-4 col-md-3">
            {hook h="displayLeftColumn"}
        </div>
    {/if}
{/block}


<div id="content-wrapper" class="card card-block {if isset($blockblogsidebar_posblog_alltags_alias) && $blockblogsidebar_posblog_alltags_alias == 1}left-column col-xs-12 col-sm-8 col-md-9{elseif isset($blockblogsidebar_posblog_alltags_alias) && $blockblogsidebar_posblog_alltags_alias == 2}right-column col-xs-12 col-sm-8 col-md-9{/if}">
    {block name="page_content"}


        {if $blockblogis17 == 1}
            <nav data-depth="2" class="breadcrumb hidden-sm-down">
                <ol itemscope="" itemtype="http://schema.org/BreadcrumbList">
                    <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                        <a itemprop="item" href="{$blockblogtags_url|escape:'htmlall':'UTF-8'}">
                            <span itemprop="name">{l s='Blog Tags' mod='blockblog'}</span>
                        </a>
                        <meta itemprop="position" content="1">
                    </li>

                </ol>
            </nav>

        {/if}


        {capture name=path}
            {$meta_title|escape:'htmlall':'UTF-8'}
        {/capture}

    {if $blockblogis16 == 0}
        <h2 class="blog-category-title">{$meta_title|escape:'htmlall':'UTF-8'}</h2>
    {else}
        <h1 class="page-heading blog-category-title">{$meta_title|escape:'htmlall':'UTF-8'}</h1>
    {/if}


    <div class="blog-header-toolbar block-data-category">

        <div class="tagsBoxSearch">

            {foreach from=$blockblogtags_page item=tags name=myLoop}
                <a href="{$blockblogtag_url|escape:'htmlall':'UTF-8'}{$tags.query|escape:'htmlall':'UTF-8'}"
                   {if isset($tags.font) && strlen($tags.font)>0}style="font-size:{$tags.font|escape:'htmlall':'UTF-8'}"{/if}
                        >{$tags.query|escape:'htmlall':'UTF-8'}</a>

            {/foreach}

        </div>

    </div>


  </div>
        {/block}

    

    {block name="right_column"}
        {if isset($blockblogsidebar_posblog_alltags_alias) && $blockblogsidebar_posblog_alltags_alias == 2}
            <div id="right-column" class="col-xs-12 col-sm-4 col-md-3">
                {hook h="displayRightColumn"}
            </div>
        {/if}

    {/block}




{/block}
