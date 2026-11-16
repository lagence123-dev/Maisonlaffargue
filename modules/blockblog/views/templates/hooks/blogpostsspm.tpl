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

<div id="blockblogposts_block_left_spm" class="block {if $blockblogis17 == 1}block-categories{/if} {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if} {if $blockblogbposts_slider == 1}owl_blog_recents_posts_type_carousel{/if} blockblog-block">

    <div class="block_content">

        {if count($blockblogposts) > 0}
            {* <div class="items-articles-block"> *}
            <ul class="items-articles-block {if $blockblogbposts_slider == 1 && (count($blockblogposts) > $blockblogbposts_sl)}owl-carousel owl-theme{/if}">

                {foreach from=$blockblogposts item=items name=myLoop1}
                    {foreach from=$items.data item=blog name=myLoop}
                        <li class="current-item-block">

                            {if $blockblogblock_display_img == 1}
                                {if strlen($blog.img)>0}
                                    <div class="block-side">
                                        <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$blog.img|escape:'htmlall':'UTF-8'}" title="{$blog.title|escape:'htmlall':'UTF-8'}" alt="{$blog.title|escape:'htmlall':'UTF-8'}" />
                                    </div>
                                {/if}
                            {/if}

                            <div class="block-content">
                                <a class="item-article d-block" title="{$blog.title|escape:'htmlall':'UTF-8'}" href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}{/if}">{$blog.title|escape:'htmlall':'UTF-8'}</a>
                                <div class="clr"></div>
                                {if $blockblogblock_display_date == 1}
                                    <span class="block-blog-date"><i class="fa fa-clock-o fa-lg"></i>&nbsp;{$blog.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}</span>
                                {/if}

                                <p class="mb-2">{$blog.content|strip_tags|nl2br nofilter}</p>
                                
                                {* <span class="comment block-blog-like">
                                    {if $blockblograting_bl == 1}
                                        {if $blog.avg_rating != 0}
                                            <span class="rating-input margin-right-10">
                                                {for $foo=0 to 4}
                                                    {if $foo < $blog.avg_rating}
                                                        <i class="fa fa-star" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>
                                                    {else}
                                                        <i class="fa fa-star-o" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>
                                                    {/if}
                                                {/for}
                                            </span>
                                        {/if}
                                    {/if}

                                    {if $blockblogpostbl_views}
                                        <i class="fa fa-eye fa-lg"></i>&nbsp;<span class="blockblog-views">({$blog.count_views|escape:'htmlall':'UTF-8'})</span>&nbsp;&nbsp;
                                    {/if}


                                    {if $blog.is_liked_post}
                                        <i class="fa fa-thumbs-up fa-lg"></i>&nbsp;(<span class="the-number">{$blog.count_like|escape:'htmlall':'UTF-8'}</span>)
                                    {else}
                                        <span class="post-like-{$blog.id|escape:'htmlall':'UTF-8'}">
                                            <a onclick="blockblog_like_post({$blog.id|escape:'htmlall':'UTF-8'},1)" href="javascript:void(0)"><i class="fa fa-thumbs-o-up fa-lg"></i>&nbsp;(<span class="the-number">{$blog.count_like|escape:'htmlall':'UTF-8'}</span>)</a>
                                        </span>

                                        {if $blockblogloyality_onl == 1}
                                            {if $blockblogloyality_like_blog_post_statusl == "loyality_like_blog_post"}
                                                {if $blockblogloyality_like_blog_post_timesul < $blockblogloyality_like_blog_post_timesl}
                                                    <span class="blockblog-loyalty-question"
                                                        onmouseover="blockblog_loyalty_question({$blog.id|escape:'htmlall':'UTF-8'},'loyality_like_blog_post{$blockblogalias|escape:'htmlall':'UTF-8'}',1)"
                                                        onmouseout="blockblog_loyalty_question({$blog.id|escape:'htmlall':'UTF-8'},'loyality_like_blog_post{$blockblogalias|escape:'htmlall':'UTF-8'}',0)">
                                                        <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/question_white.png"
                                                            alt="{$blockblogloyality_like_blog_post_pointsl|escape:'htmlall':'UTF-8'}" />
                                                        <span class="blockblog-loyalty-tooltip"
                                                            id="blockblog-loyalty-tooltip{$blog.id|escape:'htmlall':'UTF-8'}loyality_like_blog_post{$blockblogalias|escape:'htmlall':'UTF-8'}"
                                                            style="display: none;">
                                                            {$blockblogloyality_like_blog_post_descriptionl|replace:'[points]':$blockblogloyality_like_blog_post_pointsl nofilter}
                                                        </span>
                                                    </span>
                                                {/if}
                                            {/if}
                                        {/if}
                                    {/if}

                                    {if $blog.is_comments || $blog.count_comments > 0}
                                        &nbsp;
                                        <a title="{$blog.title|escape:'htmlall':'UTF-8'}" href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}#blogcomments{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}#blogcomments{/if}">
                                            <i class="fa fa-comments-o fa-lg"></i>&nbsp;(<span class="the-number">{$blog.count_comments|escape:'htmlall':'UTF-8'}</span>)
                                        </a>
                                    {/if}
                                </span> *}

                                <a class="item-link d-block" title="{$blog.title|escape:'htmlall':'UTF-8'}" href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}{/if}">Lire la suite</a>

                                <div class="clr"></div>
                            </div>
                        </li>

                    {/foreach}
                {/foreach}

            </ul>
            {* <p class="block-view-all">
                <a href="{$blockblogposts_url|escape:'htmlall':'UTF-8'}" title="{l s='View all posts' mod='blockblog'}" class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"><b>{l s='View all posts' mod='blockblog'}</b></a>
            </p> *}
        {else}
            <div class="block-no-items">
                {l s='There are not Posts yet.' mod='blockblog'}
            </div>
        {/if}

    </div>
</div>