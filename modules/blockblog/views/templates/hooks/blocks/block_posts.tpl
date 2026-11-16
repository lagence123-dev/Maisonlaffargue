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

{if $blockblogisdisablebl == 0 || count($blockblogposts) > 0}

    {if $blockblogalias == "footer"}
        {if $blockblogis17 == 1}
            <div class="col-xs-12 col-sm-3 wrapper links {if $blockblogbposts_slider == 1}owl_blog_recents_posts_type_carousel{/if}">
        {else}
            <section class="blockblogposts_block_footer footer-block col-xs-12 col-sm-3 {if $blockblogbposts_slider == 1}owl_blog_recents_posts_type_carousel{/if}">
        {/if}
    {else}

    <div id="blockblogposts_block_{$blockblogalias|escape:'htmlall':'UTF-8'}"
         class="block
         {if $blockblogbposts_slider == 1}owl_blog_recents_posts_type_carousel{/if}
         {if $blockblogis17 == 1}block-categories hidden-sm-down{/if}
         {if $blockblogis16 == 1}blockmanufacturer16{else}blockmanufacturer{/if} blockblog-block" >
    {/if}

        <h4 class="title_block {if $blockblogis17 == 1}text-uppercase{/if} {if $blockblogis17 == 1 && $blockblogalias == "footer"}h3 hidden-sm-down{/if}">

            {l s='Blog Posts recents' mod='blockblog'}

            {if $blockblogrsson == 1}
                <a  class="margin-left-left-10" href="{$blockblogrss_url|escape:'htmlall':'UTF-8'}" title="{l s='RSS Feed' mod='blockblog'}" target="_blank">
                    <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/feed.png" alt="{l s='RSS Feed' mod='blockblog'}" />
                </a>
            {/if}

        </h4>

        {if $blockblogalias == "footer"}
        {if $blockblogis17 == 1}
            <div data-toggle="collapse" data-target="#blockblogposts_block_footer" class="title clearfix hidden-md-up">
                <span class="h3">{l s='Blog Posts recents' mod='blockblog'}</span>
                        <span class="pull-xs-right">
                          <span class="navbar-toggler collapse-icons">
                            <i class="material-icons add">&#xE313;</i>
                            <i class="material-icons remove">&#xE316;</i>
                          </span>
                        </span>
            </div>
        {/if}
        {/if}


        <div class="block_content {if $blockblogalias == "footer"}block-items-data toggle-footer {if $blockblogis17 == 1}collapse{/if}{/if}" {if $blockblogalias == "footer"}{if $blockblogis17 == 1}id="blockblogposts_block_footer"{/if}{/if}>

            {if count($blockblogposts) > 0}
                <div class="items-articles-block">


                    {if $blockblogbposts_slider == 1 && (count($blockblogposts) > $blockblogbposts_sl)}<ul class="owl-carousel owl-theme">{/if}



                        {foreach from=$blockblogposts item=items name=myLoop1}
                            {foreach from=$items.data item=blog name=myLoop}

                                {if $blockblogbposts_slider == 1}

                                    {if ($smarty.foreach.myLoop1.index % $blockblogbposts_sl == 0) || $smarty.foreach.myLoop1.first}
                                        <div>
                                    {/if}

                                {/if}

                                <div class="current-item-block">


                                {if $blockblogblock_display_img == 1}
                                    {if strlen($blog.img)>0}
                                        <div class="block-side">
                                            <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$blog.img|escape:'htmlall':'UTF-8'}"
                                                 title="{$blog.title|escape:'htmlall':'UTF-8'}" alt="{$blog.title|escape:'htmlall':'UTF-8'}"  />
                                        </div>
                                    {/if}
                                {/if}

                                <div class="block-content">
                                    <a class="item-article" title="{$blog.title|escape:'htmlall':'UTF-8'}"
                                       href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}{/if}"
                                            >{$blog.title|escape:'htmlall':'UTF-8'}</a>

                                    <div class="blog-block-content">{$blog.content|strip_tags|mb_substr:0:$blockblogblog_tr_b nofilter}{if strlen($blog.content)>$blockblogblog_tr_b}...{/if}</div>

                                    {if $blockblograting_bl == 1}
                                        {if $blog.avg_rating != 0}
                                            <span class="rating-input">

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

                                    <div class="clr"></div>
                                    {if $blockblogblock_display_date == 1}
                                        <span class="float-left block-blog-date"><i class="fa fa-clock-o fa-lg"></i>&nbsp;{$blog.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}</span>
                                    {/if}
                                    <span class="float-right comment block-blog-like">
                            {if $blockblogpostbl_views}
                                <i class="fa fa-eye fa-lg"></i>&nbsp;<span class="blockblog-views">({$blog.count_views|escape:'htmlall':'UTF-8'})</span>&nbsp;&nbsp;
                            {/if}

                                        {if $blog.is_liked_post}
                                            <i class="fa fa-thumbs-up fa-lg"></i>&nbsp;(<span class="the-number">{$blog.count_like|escape:'htmlall':'UTF-8'}</span>)
                            {else}
                                <span class="post-like-{$blog.id|escape:'htmlall':'UTF-8'}">
                                <a onclick="blockblog_like_post({$blog.id|escape:'htmlall':'UTF-8'},1)"
                                   href="javascript:void(0)"><i class="fa fa-thumbs-o-up fa-lg"></i>&nbsp;(<span class="the-number">{$blog.count_like|escape:'htmlall':'UTF-8'}</span>)</a>
                                </span>

                                            {* loyalty program *}
                                            {if $blockblogloyality_onl == 1}
                                                {if $blockblogloyality_like_blog_post_statusl == "loyality_like_blog_post"}
                                                    {if $blockblogloyality_like_blog_post_timesul < $blockblogloyality_like_blog_post_timesl}
                                                        <span class="blockblog-loyalty-question"
                                                              onmouseover="blockblog_loyalty_question({$blog.id|escape:'htmlall':'UTF-8'},'loyality_like_blog_post{$blockblogalias|escape:'htmlall':'UTF-8'}',1)"
                                                              onmouseout="blockblog_loyalty_question({$blog.id|escape:'htmlall':'UTF-8'},'loyality_like_blog_post{$blockblogalias|escape:'htmlall':'UTF-8'}',0)">
                                                    <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/question_white.png"
                                                         alt ="{$blockblogloyality_like_blog_post_pointsl|escape:'htmlall':'UTF-8'}"
                                                            />
                                                            <span class="blockblog-loyalty-tooltip" id="blockblog-loyalty-tooltip{$blog.id|escape:'htmlall':'UTF-8'}loyality_like_blog_post{$blockblogalias|escape:'htmlall':'UTF-8'}" style="display: none;">
                                                                {$blockblogloyality_like_blog_post_descriptionl|replace:'[points]':$blockblogloyality_like_blog_post_pointsl nofilter}
                                                            </span>
                                            </span>
                                                    {/if}
                                                {/if}
                                            {/if}
                                            {* loyalty program *}

                                        {/if}


                                        {if $blog.is_comments || $blog.count_comments > 0}
                                            &nbsp;
                                            <a title="{$blog.title|escape:'htmlall':'UTF-8'}"
                                               href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.seo_url|escape:'htmlall':'UTF-8'}#blogcomments{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$blog.id|escape:'htmlall':'UTF-8'}#blogcomments{/if}">
                                                <i class="fa fa-comments-o fa-lg"></i>&nbsp;(<span class="the-number">{$blog.count_comments|escape:'htmlall':'UTF-8'}</span>)</a>
                                        {/if}

                        </span>

                                    <div class="clr"></div>
                                </div>


                            {if $blockblogbposts_slider == 1}

                            {if ($smarty.foreach.myLoop1.index % $blockblogbposts_sl == $blockblogbposts_sl - 1) || $smarty.foreach.myLoop1.last}
                                </div>
                            {/if}

                            {/if}

                                </div>



                            {/foreach}
                        {/foreach}


                        {if $blockblogbposts_slider == 1  && (count($blockblogposts) > $blockblogbposts_sl)}</ul>{/if}



                    <p class="block-view-all">
                        <a href="{$blockblogposts_url|escape:'htmlall':'UTF-8'}" title="{l s='View all posts' mod='blockblog'}" class="{if $blockblogis17 == 1}btn btn-default button button-small-blockblog{/if} button"
                                ><b>{l s='View all posts' mod='blockblog'}</b></a>
                    </p>

                </div>
            {else}
                <div class="block-no-items">
                    {l s='There are not posts yet.' mod='blockblog'}
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