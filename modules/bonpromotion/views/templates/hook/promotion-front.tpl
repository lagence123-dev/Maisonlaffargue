{*
 * 2015-2019 Bonpresta
 *
 * Promotion Discount Countdown Banner & Slider
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
 *  @author    Bonpresta
 *  @copyright 2015-2019 Bonpresta
 *  @license   http://opensource.org/licenses/GPL-2.0 General Public License (GPL 2.0)
*}

{if isset($items) && $items}
    <section id="bonpromotion">
        <ul>
            {foreach from=$items item=item name=item}
                {if $smarty.foreach.item.iteration <= $limit}
                    {if $item.type == 'video'}
                        <li class="video">
                            <a class="link-promotion" href="{$item.url|escape:'htmlall':'UTF-8'}">
                                <video loop="loop" autoplay muted class="bonpromotion-video"></video>

                                <div class="box-promotion">
                                    {if isset($item.description) && $item.description}
                                        <div class="box-promotion-desc revealOnScroll animated fadeInUp" data-animation="fadeInUp">
                                            {$item.description nofilter}
                                        </div>
                                    {/if}
                                    <div class="bonpromotion-countdown-btn-wrapper">
                                        <span class="bonpromotion-countdown-btn btn-primary revealOnScroll animated fadeInUp" data-animation="fadeInUp">{l s='Shop Now!' mod='bonpromotion'}<span class="arrow-right"></span></span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    {elseif $item.type == 'image'}
                        <li class="image">
                            <a class="link-promotion" href="{$item.url|escape:'htmlall':'UTF-8'}" style="background: url('{$image_baseurl}{$item.image}') no-repeat 100% 100% fixed">
                                <div class="box-promotion">
                                    {if isset($item.description) && $item.description}
                                        <div class="box-promotion-desc revealOnScroll animated fadeInUp" data-animation="fadeInUp">
                                            {$item.description nofilter}
                                        </div>
                                    {/if}
                                    <div class="bonpromotion-countdown-btn-wrapper">
                                        <span class="bonpromotion-countdown-btn btn-primary revealOnScroll animated fadeInUp" data-animation="fadeInUp">{l s='Shop Now!' mod='bonpromotion'}<span class="arrow-right"></span></span>
                                    </div>
                                </div>
                            </a>
                        </li>                     
                    {/if}
                {/if}   
            {/foreach}
        </ul>
    </section>
{/if}
