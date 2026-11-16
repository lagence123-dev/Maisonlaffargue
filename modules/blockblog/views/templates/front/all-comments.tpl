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



<div class="blog-header-toolbar">
{if $count_all > 0}

<div class="toolbar-top">
			
	<div class="{if $blockblogis16==1}sortTools sortTools16{else}sortTools{/if}">
		<ul class="actions">
			<li class="frst">
					<strong>{l s='Comments' mod='blockblog'}  ( {$count_all|escape:'htmlall':'UTF-8'} )</strong>
			</li>
		</ul>
		
	</div>

</div>



    <ul class="blog-posts {if $blockblogblog_layout_typeblog_allcom_alias == 2}blockblog-grid-view{else}blockblog-list-view{/if}" id="blog-items">


        {assign var="list_allcomments" value="modules/blockblog/views/templates/front/list_allcomments.tpl"}

        {if file_exists("{$tpl_dir}{$list_allcomments}")}
            {include file="{$tpl_dir}{$list_allcomments}"}
        {else}
            {include file="{$list_allcomments}"}
        {/if}



    </ul>




    <div class="toolbar-paging">
        <div class="text-align-center" id="page_nav">
            {$paging nofilter}
        </div>
    </div>

{else}
	<div class="block-no-items">
	{l s='There are not comments yet' mod='blockblog'}
	</div>
{/if}

</div>
