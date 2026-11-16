{*
* 2015-2022 Bonpresta
*
* Bonpresta Instagram Gallery Feed Photos & Videos User
*
* NOTICE OF LICENSE
*
* This source file is subject to the General Public License (GPL 2.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/GPL-2.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future.
*
* @author Bonpresta
* @copyright 2015-2022 Bonpresta
* @license http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
*}
<section id="boninstagram">
    <div class="boninstagram-container clearfix block">
        <div class="instagram-home-title revealOnScroll animated fadeInUp" data-animation="fadeInUp">
        <p class="h2 products-section-title">
                {l s='Instagram' mod='boninstagram'}
            </p>
        </div>
        <div class="block_content">
            <ul class="{if $display_carousel}slick-carousel-instagram {else}clearfix{/if}">
                {if isset($images) && $images}
                    {foreach from=$images item=item name=item}
                        {if $smarty.foreach.item.iteration <= $limit}
                            <li class="instagram-item {if !$display_carousel}instagram-item-gallery{/if} revealOnScroll animated fadeInUp"
                                style="{if $limit < 11}width: calc(100% / {$limit});{else}width: 12.5%;{/if};"
                                data-animation="fadeInUp">
                                <a href="https://www.instagram.com/{$item['username']|escape:'htmlall':'UTF-8'}/"
                                    class="{if $item['is_video']}boninstagram-item_vid{else}boninstagram-item_img{/if}"
                                    target="_blank"
                                    rel="nofollow">
                                    {if $item['is_video']}
                                        <video class="boninstagram-video" loop="loop" autoplay muted>
                                            <source src="{$baseurl}img/sample-{$smarty.foreach.item.iteration}.mp4" type="video/mp4">
                                        </video>
                                    {else}
                                        <img src="{$baseurl}img/sample-{$smarty.foreach.item.iteration}.jpg"
                                            alt="boninstagram-{$smarty.foreach.item.iteration|escape:'htmlall':'UTF-8'}">
                                    {/if}
                                    <span class="instagram_cover"></span>
                                    {if $show_icon}
                                        <button class="instagram-menu-open">
                                            <div>
                                                <div></div>
                                                <div></div>
                                            </div>
                                        </button>
                                    {/if}
                                    {if $show_user}
                                        <div class="boninstagram-author">
                                            <span>@{$item['username']|escape:'htmlall':'UTF-8'}</span>
                                        </div>
                                    {/if}
                                    {if $show_date}
                                        <div class="boninstagram-date">
                                            <span>{$item['posted_date']|escape:'htmlall':'UTF-8'}</span>
                                        </div>
                                    {/if}
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                {else}
                    {for $img = 1 to 8}
                        <li class="instagram-item {if !$display_carousel}instagram-item-gallery{/if} revealOnScroll animated fadeInUp"
                            style="{if $limit < 11}width: calc(100% / {$limit});{else}width: 12.5%;{/if};"
                            data-animation="fadeInUp">
                            <a href="#"
                                class="boninstagram-item_img"
                                target="_blank"
                                rel="nofollow">
                                    <img src="{$baseurl}img/sample-{$img}.jpg"
                                        alt="boninstagram-{$img}">
                                <span class="instagram_cover"></span>
                                {if $show_icon}
                                    <button class="instagram-menu-open">
                                        <div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                    </button>
                                {/if}
                                {if $show_user}
                                    <div class="boninstagram-author">
                                        <span>@username</span>
                                    </div>
                                {/if}
                                {if $show_date}
                                    <div class="boninstagram-date">
                                        <span>posted_date</span>
                                    </div>
                                {/if}
                            </a>
                        </li>
                    {/for}
                {/if}
            </ul>
        </div>
    </div>
</section>