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

{if $blockblogtab_blog_pr == 1}

{if $blockblogbtabs_type == 1}

{if count($blockblogrelated_posts) > 0}
<h3 class="page-product-heading" id="#idTab2018">
	{l s='Related Blog Posts' mod='blockblog'}
</h3>
{/if}

{/if}

{/if}


{if $blockblogtab_blog_pr == 1}

{if count($blockblogrelated_posts) > 0}
<div id="idTab2018" {if $blockblogis17 == 1}class="{if $blockblogbtabs_type != 3}block-categories product-page-blockblog{else}tab-pane fade in{/if}"{else}class="tab-pane"{/if}>



        <div class="rel-posts-block {if $blockblogrelp_sliderp == 1}owl_blog_related_posts_type_carousel_product{/if}">


            <div class="other-posts">



                <ul class="{if $blockblogrelp_sliderp == 1}owl-carousel owl-theme{else}row-custom{/if}">
                    {foreach from=$blockblogrelated_posts item=relpost name=myLoop}
                        <li {if $blockblogrelp_sliderp == 0}class="col-sm-12-custom"{/if}>
                            {if strlen($relpost.img)>0}
                                <div class="block-top {if $blockblogrelp_sliderp == 0}float-left margin-right-10{/if}">
                                    <a title="{$relpost.title|escape:'htmlall':'UTF-8'}"
                                       href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$relpost.seo_url|escape:'htmlall':'UTF-8'}{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$relpost.id|escape:'htmlall':'UTF-8'}{/if}">
                                        <img alt="{$relpost.title|escape:'htmlall':'UTF-8'}"
                                             src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$blockblogpic|escape:'htmlall':'UTF-8'}{$relpost.img|escape:'htmlall':'UTF-8'}"
                                             {if $blockblogrelp_sliderp == 1}class="img-responsive"{/if}>
                                    </a>
                                </div>
                            {/if}


                            <div class="block-content {if $blockblogrelp_sliderp == 0}float-left{/if}">
                                <h4 class="block-heading">
                                    <a title="{$relpost.title|escape:'htmlall':'UTF-8'}"
                                       href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$relpost.seo_url|escape:'htmlall':'UTF-8'}{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$relpost.id|escape:'htmlall':'UTF-8'}{/if}"
                                            >{$relpost.title|escape:'htmlall':'UTF-8'}</a>
                                </h4>

                                {if $blockblograting_postrpp == 1}
                                    {if $relpost.avg_rating != 0}
                                        <span class="rating-input margin-right-10">
                            {for $foo=0 to 4}
                                {if $foo < $relpost.avg_rating}
                                    <i class="fa fa-star" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>

                                {else}
                                    <i class="fa fa-star-o" data-value="{$foo|escape:'htmlall':'UTF-8'}"></i>
                                {/if}

                            {/for}


                            </span>
                                    {/if}
                                {/if}
                            </div>
                            {if $blockblogrelp_sliderp == 0}<div class="clear"></div>{/if}
                            <div class="block-footer">
                                <p class="float-left">
                                    <time pubdate="pubdate" datetime="{$relpost.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}"
                                            ><i class="fa fa-clock-o fa-lg"></i>&nbsp;&nbsp;{$relpost.time_add|date_format:$blockblogblog_date|escape:'htmlall':'UTF-8'}
                                    </time>
                                </p>
                                <p class="float-right comment">

                                    {if $blockblogpostrel_viewsp}
                                        <i class="fa fa-eye fa-lg"></i>&nbsp;<span class="blockblog-views">({$relpost.count_views|escape:'htmlall':'UTF-8'})</span>&nbsp;&nbsp;
                                    {/if}

                                    {if $relpost.is_liked_post}
                                        <i class="fa fa-thumbs-up fa-lg"></i>&nbsp;(<span class="the-number">{$relpost.count_like|escape:'htmlall':'UTF-8'}</span>)
                                    {else}
                                        <span class="post-like-{$relpost.id|escape:'htmlall':'UTF-8'}">
                            <a onclick="blockblog_like_post({$relpost.id|escape:'htmlall':'UTF-8'},1)"
                               href="javascript:void(0)"><i class="fa fa-thumbs-o-up fa-lg"></i>&nbsp;(<span class="the-number">{$relpost.count_like|escape:'htmlall':'UTF-8'}</span>)</a>
                        </span>
                                        {* loyalty program *}
                                        {if $blockblogloyality_onl == 1}
                                            {if $blockblogloyality_like_blog_post_statusl == "loyality_like_blog_post"}
                                                {if $blockblogloyality_like_blog_post_timesul < $blockblogloyality_like_blog_post_timesl}
                                                    <span class="blockblog-loyalty-question"
                                                          onmouseover="blockblog_loyalty_question({$relpost.id|escape:'htmlall':'UTF-8'},'loyality_like_blog_post',1)"
                                                          onmouseout="blockblog_loyalty_question({$relpost.id|escape:'htmlall':'UTF-8'},'loyality_like_blog_post',0)">
                                                    <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/blockblog/views/img/question_white.png"
                                                         alt ="{$blockblogloyality_like_blog_post_pointsl|escape:'htmlall':'UTF-8'}"
                                                            />
                                                            <span class="blockblog-loyalty-tooltip" id="blockblog-loyalty-tooltip{$relpost.id|escape:'htmlall':'UTF-8'}loyality_like_blog_post" style="display: none;">
                                                                {$blockblogloyality_like_blog_post_descriptionl|replace:'[points]':$blockblogloyality_like_blog_post_pointsl nofilter}
                                                            </span>
                                            </span>
                                                {/if}
                                            {/if}
                                        {/if}
                                        {* loyalty program *}
                                    {/if}

                                    {if $relpost.is_comments || $relpost.count_comments > 0}
                                        &nbsp;
                                        <a href="{if $blockblogurlrewrite_on == 1}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$relpost.seo_url|escape:'htmlall':'UTF-8'}#blogcomments{else}{$blockblogpost_url|escape:'htmlall':'UTF-8'}{$relpost.id|escape:'htmlall':'UTF-8'}#blogcomments{/if}"
                                           title="{$relpost.title|escape:'htmlall':'UTF-8'}"
                                                ><i class="fa fa-comments-o fa-lg"></i>&nbsp;{$relpost.count_comments|escape:'htmlall':'UTF-8'}</a>
                                    {/if}


                                </p>

                                <div class="clear"></div>
                            </div>
                        </li>
                    {/foreach}

                </ul>

            </div>
            <div class="clear"></div>


        </div>

    {literal}
        <script type="text/javascript">

            {/literal}{if $blockblogrelp_sliderp == 1}{literal}
            var blockblog_number_posts_slider_product = {/literal}{$blockblognp_sliderp|escape:'htmlall':'UTF-8'}{literal};
            {/literal}{/if}{literal}

        </script>
    {/literal}


</div>

{/if}

{/if}

