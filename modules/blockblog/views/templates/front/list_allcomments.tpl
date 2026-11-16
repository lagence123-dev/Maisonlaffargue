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

{foreach from=$comments item=comment name=myLoop}
    <li class="pl-animate {$blockblogblog_com_effect|escape:'htmlall':'UTF-8'}">
        <div class="top-blog">

            <div class="comment-post">
            {$comment.comment|strip_tags|escape:'htmlall':'UTF-8'}
            </div>

            {if strlen($comment.response)>0}
                <div class="reponse-comment-post">
                    <div class="title-reponse-comment-post">
                        <b>{l s='Administrator' mod='blockblog'}:</b>
                    </div>

                    <span>{$comment.response|escape:'htmlall':'UTF-8'}</span>
                </div>
            {/if}


            <div class="clear"></div>
            <br/>

            {if $blockblograting_acom == 1}
                <span class="rating-input float-left margin-right-10">
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

            <small class="float-right">
                {if $comment.is_show_ava == 0  || ($blockblogava_on == 0 || $blockblogava_list_displ_call == 0)}<i class="fa fa-user"></i>{/if}&nbsp;{if $comment.is_show_ava == 1 && ($blockblogava_on == 1 && $blockblogava_list_displ_call == 1)}<span class="avatar-block-rev">
                                        <img alt="{$comment.name|escape:'htmlall':'UTF-8'}" src="{$comment.avatar|escape:'htmlall':'UTF-8'}" />
                                    </span>&nbsp;<a href="{$blockblogauthor_url|escape:'htmlall':'UTF-8'}{$comment.author_id|escape:'htmlall':'UTF-8'}-{$comment.author|escape:'htmlall':'UTF-8'}"
                                                    title="{$comment.name|escape:'htmlall':'UTF-8'}"
                                                    class="blog_post_author">{/if}{$comment.name|escape:'htmlall':'UTF-8'}{if $comment.is_show_ava == 1 && ($blockblogava_on == 1 && $blockblogava_list_displ_call == 1)}</a>{/if}
            </small>

            <div class="clear"></div>

            <p class="float-left">

                <time datetime="{$comment.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}" pubdate="pubdate"
                        ><i class="fa fa-clock-o fa-lg"></i>&nbsp;{$comment.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}</time>

            </p>
            <p class="float-right comment">
                <i class="fa fa-list-alt fa-lg"></i>&nbsp; <a href="{if $blockblogurlrewrite_on == 1}
                                                                        {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$comment.post_seo_url|escape:'htmlall':'UTF-8'}#blogcomments
                                                                      {else}
                                                                        {$blockblogpost_url|escape:'htmlall':'UTF-8'}{$comment.post_seo_url|escape:'htmlall':'UTF-8'}#blogcomments
                                                                      {/if}"
                                                              title="{$comment.post_title|escape:'htmlall':'UTF-8'}"
                        >{$comment.post_title|escape:'htmlall':'UTF-8'}</a>
            </p>

            <div class="clear"></div>
        </div>


    </li>
{/foreach}



{if $blockblogblog_com_effect != "disable_all_effects"}
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