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
{assign var=blockblog_is_snippets_comments value=0}

{foreach from=$comments item=comment name=myLoop}
    <div class="pl-animate {$blockblogblog_comp_effect|escape:'htmlall':'UTF-8'}" {if $blockblog_is_snippets_comments == 1}itemprop="review" itemscope itemtype="http://schema.org/Review"{/if}>
        {if $blockblog_is_snippets_comments == 1} <meta itemprop="itemReviewed" content="{$comment.post_title|escape:'htmlall':'UTF-8'}"/>{/if}
    <div class="row-custom">
        {if $blockblogava_list_displ_cpost == 1}
        <div class="col-md-2-custom col-sm-2-custom text-align-center">

            <figure class="thumbnail">
                {if $blockblogava_on == 1}
                    {if $comment.id_customer > 0 && isset($comment.is_show_ava)  && (isset($comment.avatar) && strlen($comment.avatar)>0)}

                    {if $comment.is_show_ava == 1 && ($blockblogava_on == 1 && $blockblogava_list_displ_cpost == 1)}
                                <a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$comment.author_id|escape:'htmlall':'UTF-8'}-{$comment.author|escape:'htmlall':'UTF-8'}"
                                                    title="{if strlen($comment.name)>70}{$comment.name|mb_substr:0:70}...{else}{$comment.name|escape:'htmlall':'UTF-8'}{/if}"
                                                    class="blog_post_author">{/if}
                        <img src="{$comment.avatar|escape:'htmlall':'UTF-8'}" class="img-responsive">
                        {if $comment.is_show_ava == 1 && ($blockblogava_on == 1 && $blockblogava_list_displ_cpost == 1)}</a>{/if}
                        <figcaption class="text-center image-name" {if $blockblog_is_snippets_comments == 1} itemprop="author" {/if}>
                            {if $comment.is_show_ava == 1 && ($blockblogava_on == 1 && $blockblogava_list_displ_cpost == 1)}
                            <a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$comment.author_id|escape:'htmlall':'UTF-8'}-{$comment.author|escape:'htmlall':'UTF-8'}"
                               title="{if strlen($comment.name)>70}{$comment.name|mb_substr:0:70}...{else}{$comment.name|escape:'htmlall':'UTF-8'}{/if}"
                               class="blog_post_author">{/if}
                            {if strlen($comment.name)>10}{$comment.name|mb_substr:0:10}...{else}{$comment.name|escape:'htmlall':'UTF-8'}{/if}
                                {if $comment.is_show_ava == 1 && ($blockblogava_on == 1 && $blockblogava_list_displ_cpost == 1)}</a>{/if}
                        </figcaption>
                    {else}
                        <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/logo_comments.png" class="img-responsive">
                        <figcaption class="text-center image-name" {if $blockblog_is_snippets_comments == 1}rop="author"{/if}>{if strlen($comment.name)>10}{$comment.name|mb_substr:0:10}...{else}{$comment.name|escape:'htmlall':'UTF-8'}{/if}</figcaption>
                    {/if}
                {/if}

            </figure>
        </div>
        {/if}
        <div class="col-md-{if $blockblogava_list_displ_cpost == 1}10{else}12{/if}-custom col-sm-{if $blockblogava_list_displ_cpost == 1}10{else}12{/if}-custom">
            <div class="panel panel-default left">
                <div class="panel-body">
                    <header class="text-left">
                        <div class="comment-user">

                            {if $comment.is_show_ava == 0 || ($blockblogava_on == 0 || $blockblogava_list_displ_cpost == 0)}<i class="fa fa-user"></i>{/if}&nbsp;{if $comment.is_show_ava == 1 && ($blockblogava_on == 1 && $blockblogava_list_displ_cpost == 1)}<span class="avatar-block-rev">
                                        <img alt="{if strlen($comment.name)>70}{$comment.name|mb_substr:0:70}...{else}{$comment.name|escape:'htmlall':'UTF-8'}{/if}" src="{$comment.avatar|escape:'htmlall':'UTF-8'}" />
                                    </span>&nbsp;<a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$comment.author_id|escape:'htmlall':'UTF-8'}-{$comment.author|escape:'htmlall':'UTF-8'}"
                                                    title="{if strlen($comment.name)>70}{$comment.name|mb_substr:0:70}...{else}{$comment.name|escape:'htmlall':'UTF-8'}{/if}"
                                                    class="blog_post_author">{/if}{if strlen($comment.name)>70}{$comment.name|mb_substr:0:70}...{else}{$comment.name|escape:'htmlall':'UTF-8'}{/if}{if $comment.is_show_ava == 1 && ($blockblogava_on == 1 && $blockblogava_list_displ_cpost == 1)}</a>{/if}
                        </div>
                        <div class="clear"></div>

                        {if $blockblograting_post == 1}
                        <div class="rating-input float-left margin-right-10" {if $blockblog_is_snippets_comments == 1} itemtype="http://schema.org/Rating" itemscope itemprop="reviewRating"{/if}>
                            {if $blockblog_is_snippets_comments == 1}
                            <meta itemprop="ratingValue" content="{$comment.rating|escape:'htmlall':'UTF-8'}"/>
                            <meta itemprop="bestRating" content="5"/>
                            {/if}
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
                        </div>
                        {/if}
                        <time datetime="{$comment.time_add|escape:'htmlall':'UTF-8'}" class="comment-date float-left"><i class="fa fa-clock-o"></i>&nbsp;{$comment.time_add|escape:'htmlall':'UTF-8'}</time>
                        {if $blockblog_is_snippets_comments == 1}<meta itemprop="datePublished" content="{$comment.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}"/>{/if}
                        <div class="clear"></div>
                    </header>
                    {if $blockblog_is_snippets_comments == 1}
                    <meta itemprop="name" content="{$comment.comment|mb_substr:0:70|escape:'htmlall':'UTF-8'}"/>
                    {/if}

                    <div class="comment-post" {if $blockblog_is_snippets_comments == 1}itemprop="description"{/if}>{$comment.comment|escape:'htmlall':'UTF-8'}</div>

                    {if strlen($comment.response)>0}
                        <div class="reponse-comment-post">
                            <div class="title-reponse-comment-post">
                                <b>{l s='Administrator' mod='blockblog'}:</b>
                            </div>

                            <p>{$comment.response|escape:'htmlall':'UTF-8'}</p>
                        </div>
                    {/if}

                </div>
            </div>

        </div>
        <div class="clear"></div>
    </div>
    </div>


{/foreach}



{if $blockblogblog_comp_effect != "disable_all_effects"}
{literal}
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function(){
                blockblog_init_effects();
            });
        });
    </script>
{/literal}
{/if}