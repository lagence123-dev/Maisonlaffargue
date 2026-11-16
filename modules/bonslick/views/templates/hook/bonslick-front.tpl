{*
* 2015-2020 Bonpresta
*
* Bonpresta Awesome Image Slider
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
* @copyright 2015-2020 Bonpresta
* @license http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
*}

{if isset($items) && $items}
<div class="clearfix"></div>
{*<section id="bonslick">*}
<section id="bonswiper">
{*    <ul class="bonslick-slider">*}
    <ul class="bonswiper-wrapper">
        {foreach from=$items item=item name=item}
        {if $item.type == 'image'}
        <li class="swiper-slide">
            <a class="link-bonslick" href="{$item.url|escape:'htmlall':'UTF-8'}">
                {if isset($item.image) && $item.image}
                <img class="img-responsive" data-swiper-parallax-scale="1.2" src="{$image_baseurl|escape:'htmlall':'UTF-8'}{$item.image|escape:'htmlall':'UTF-8'}" alt="{$item.title|escape:'htmlall':'UTF-8'}" />
                {/if}
                <section class="box-bonslick">
                    {if isset($item.description) && $item.description}
                    <div class="container">
                        <div class="bonslick-caption col-12 col-md-12" data-swiper-parallax-x="-200%">
                            {$item.description nofilter}
                        </div>
                    </div>
                    {/if}
                </section>
            </a>
        </li>
        {else}
        <li class="swiper-slide">
            <div id="video-container" class="video-container">
                <a class="link-bonslick" href="{$item.url|escape:'htmlall':'UTF-8'}">
                    <video id="video-element" class="bonslick-video" loop="loop" autoplay muted>
                        <source src="{$image_baseurl|escape:'htmlall':'UTF-8'}{$item.image|escape:'htmlall':'UTF-8'}" type="video/mp4">
                    </video>
                    <section class="box-bonslick">
                        {if isset($item.description) && $item.description}
                        <div class="container">
                            <div class="bonslick-caption col-7 col-md-8">
                                {$item.description nofilter}
                            </div>
                        </div>
                        {/if}
                    </section>
                </a>
                <div id="controls">
                    <button id='btnPlayPause' class='play' accesskey="P" onclick='playPauseVideo();'></button>
                    <button id='btnMute' class='unmute' onclick='muteVolume();'></button>
                </div>
            </div>
        </li>
        {/if}
        {/foreach}
    </ul>
    {if $bon_nav}
        <div class="bonswiper-button-next"></div>
        <div class="bonswiper-button-prev"></div>
    {/if}
    {if $bon_dots}
    <div class="bonswiper-pagination"></div>
    {/if}
</section>
{/if}