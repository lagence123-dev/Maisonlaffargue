{*
**
 * 2011 - 2020 StorePrestaModules SPM LLC.
 *
 * MODULE blockblog
 *
 * @author    SPM <spm.presto@gmail.com>
 * @copyright Copyright (c) permanent, SPM
 * @license   Addons PrestaShop license limitation
 * @version   2.4.3
 * @link      http://addons.prestashop.com/en/2_community-developer?contributor=790166
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 *
*}

{if $blockblogisdisablebl == 0 || count($blockblogcomments) > 0}

    {if $blockblogalias == "footer"}
        {if $blockblogis17 == 1}
            <div class="col-xs-12 col-sm-3 wrapper links {if $blockblogbcom_slider == 1}owl_blog_com_type_carousel{/if}">
        {else}
            <section class="blockblogcomm_block_footer footer-block col-xs-12 col-sm-3 {if $blockblogbcom_slider == 1}owl_blog_com_type_carousel{/if}">
        {/if}
    {else}

        <div id="blockblogcomm_block_{$blockblogalias|escape:'htmlall':'UTF-8'}"
             class="last-comments-block block
         {if $blockblogbcom_slider == 1}owl_blog_com_type_carousel{/if}
         {if $blockblogis17 == 1}block-categories hidden-sm-down{/if}
         {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if}" >

    {/if}

        <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if} {if $blockblogis17 == 1 && $blockblogalias == "footer"}h3 hidden-sm-down{/if}">{l s='Blog Last Comments' mod='blockblog'}</h4>

    {if $blockblogalias == "footer"}
        {if $blockblogis17 == 1}
            <div data-toggle="collapse" data-target="#blockblogcomm_block_footer" class="title clearfix hidden-md-up">
                <span class="h3">{l s='Blog Last Comments' mod='blockblog'}</span>
                            <span class="pull-xs-right">
                              <span class="navbar-toggler collapse-icons">
                                <i class="material-icons add">&#xE313;</i>
                                <i class="material-icons remove">&#xE316;</i>
                              </span>
                            </span>
            </div>
        {/if}
    {/if}

        <div class="block_content {if $blockblogalias == "footer"}block-items-data toggle-footer {if $blockblogis17 == 1}collapse{/if}{/if}" {if $blockblogalias == "footer"}{if $blockblogis17 == 1}id="blockblogcomm_block_footer"{/if}{/if}>
            {if count($blockblogcomments) > 0}
                <div class="items-articles-block">

                    {if $blockblogbcom_slider == 1 && (count($blockblogcomments) > $blockblogbcom_sl)}<ul class="owl-carousel owl-theme">{/if}

                        {foreach from=$blockblogcomments item=comment name=myLoop}

                            {if $blockblogbcom_slider == 1}

                                {if ($smarty.foreach.myLoop.index % $blockblogbcom_sl == 0) || $smarty.foreach.myLoop.first}
                                    <div>
                                {/if}

                            {/if}

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


                            {if $blockblogbcom_slider == 1}

                                {if ($smarty.foreach.myLoop.index % $blockblogbcom_sl == $blockblogbcom_sl - 1) || $smarty.foreach.myLoop.last}
                                    </div>
                                {/if}
                            {/if}

                        {/foreach}

                        {if $blockblogbcom_slider == 1 && (count($blockblogcomments) > $blockblogbcom_sl)}</ul>{/if}

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
    {if $blockblogalias == "footer"}
        {if $blockblogis17 == 1}
            </div>
        {else}
            </section>
        {/if}
    {else}
        </div>
    {/if}
{/if}