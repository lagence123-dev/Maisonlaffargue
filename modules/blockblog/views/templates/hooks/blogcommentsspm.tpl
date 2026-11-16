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

<div id="blockblogcomm_block_left_spm" class="last-comments-block block {if $blockblogis17 == 1}block-categories{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}" >
	<h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if}">{l s='Blog Last Comments' mod='blockblog'}</h4>
	
       	<div class="block_content">
               {if count($blockblogcomments) > 0}
                <div class="items-articles-block">
                    {foreach from=$blockblogcomments item=comment name=myLoop}

                        <div class="current-item-block">
                            <a class="item-comm" title="{$comment.comment|escape:'htmlall':'UTF-8'}"

                               href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$comment.post_seo_url|escape:'htmlall':'UTF-8'}{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$comment.post_id|escape:'htmlall':'UTF-8'}{/if}"
                                    >
                                {$comment.comment|strip_tags|mb_substr:0:$blockblogblog_com_tr|escape:'htmlall':'UTF-8'}{if strlen($comment.comment)>$blockblogblog_com_tr}...{/if}
                            </a>
                            {if $blockblograting_bllc == 1}
                                <div class="clr"></div>
                                <br/>
                                <span class="rating-input">
                                    {if $comment.rating != 0}
                                        {for $foo=0 to 4}
                                            {if $foo < $comment.rating}
                                                <i class="fa fa-star" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>

                                            {else}
                                                <i class="fa fa-star-o" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>
                                            {/if}

                                        {/for}

                                    {else}

                                        {for $foo=0 to 4}
                                        <i class="fa fa-star-o" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>
                                    {/for}
                                    {/if}
                                </span>
                            {/if}
                            <div class="clr"></div>
                            <small class="float-left block-blog-date"><i class="fa fa-clock-o fa-lg"></i>&nbsp;{$comment.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}</small>
                            <small class="float-right block-blog-date">

                                {if $comment.is_show_ava == 0 || ($blockblogava_on == 0)}<i class="fa fa-user"></i>{/if}&nbsp;{if $comment.is_show_ava == 1 && ($blockblogava_on == 1)}<span class="avatar-block-rev">
                                    <img alt="{$comment.name|escape:'htmlall':'UTF-8'}" src="{$comment.avatar|escape:'htmlall':'UTF-8'}" />
                                </span>&nbsp;<a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$comment.author_id|escape:'htmlall':'UTF-8'}-{$comment.author|escape:'htmlall':'UTF-8'}"
                                                title="{$comment.name|escape:'htmlall':'UTF-8'}"
                                                class="blog_post_author">{/if}{if strlen($comment.name)>10}{$comment.name|mb_substr:0:10}...{else}{$comment.name|escape:'htmlall':'UTF-8'}{/if}{if $comment.is_show_ava == 1 && ($blockblogava_on == 1)}</a>{/if}


                            </small>
                            <div class="clr"></div>
                        </div>
                    {/foreach}
                    <p class="block-view-all">
                        <a title="{l s='View all comments' mod='blockblog'}"  class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                           href="{$blockblogcomments_url|escape:'htmlall':'UTF-8'}"><b>{l s='View all comments' mod='blockblog'}</b></a>
                    </p>
               </div>

			    {else}
				<div class="block-no-items">
					{l s='There are not comments yet.' mod='blockblog'}
				</div>
				{/if}
     		</div>
</div>


