{*
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

<script src="{$js|escape:'htmlall':'UTF-8'}jquery-min.js"></script>
<script src="{$js|escape:'htmlall':'UTF-8'}bonmaintenance_front.js"></script>
<script src="{$js|escape:'htmlall':'UTF-8'}jquery.countdown.js"></script>
<link rel="stylesheet" href="{$css}bonmaintenance_front.css">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400&display=swap" rel="stylesheet">

<style>
    #bon-bonmaintenance::after {
        {if isset($maintenance_background_color) && $maintenance_background_color}
            background: {$maintenance_background_color|escape:'htmlall':'UTF-8'};
        {else}
            background: transparent;
        {/if}

        {if isset($maintenance_content_opacity) && $maintenance_content_opacity}
            opacity: {$maintenance_content_opacity|escape:'htmlall':'UTF-8'};
        {else}
            opacity: 1;
        {/if}
    }

    #bon-bonmaintenance .bonmaintenance-social a {
        {if isset($maintenance_social_color) && $maintenance_social_color}
            color: {$maintenance_social_color|escape:'htmlall':'UTF-8'};
        {else}
            color: #3a3a3a;
        {/if}
    }
</style>


{if $items && isset($items)}
    <div id="bon-bonmaintenance" {if $items[0].select_type == 'gradient'}
            style='background: linear-gradient(-45deg, {$items[0].colorhex|escape:'htmlall':'UTF-8'}, {$items[0].colorhex2|escape:'htmlall':'UTF-8'}, {$items[0].colorhex3|escape:'htmlall':'UTF-8'});'
        {else}
            style='background-image: url("{$image_baseurl|escape:'htmlall':'UTF-8'}{$items[0].image|escape:'htmlall':'UTF-8'}")'
        {/if}>
        <div class="logo"><img src="{$shop.logo|escape:'htmlall':'UTF-8'}" alt="logo" loading="lazy"></div>
        <div class="bon-bonmaintenance-wrapper">


            {if $items[0].title_text && isset($items[0].title_text)}
                <div class="bonmaintenance-title"
                    style="color:{if $maintenance_title_color}{$maintenance_title_color|escape:'htmlall':'UTF-8'}{else}inherit{/if}">
                    {$items[0].title_text|escape:'htmlall':'UTF-8' nofilter}
                </div>
            {/if}

            {if $items[0].description && isset($items[0].description)}
                <div class="bonmaintenance-description"
                    style="color:{if $maintenance_content_color}{$maintenance_content_color|escape:'htmlall':'UTF-8'}{else}inherit{/if}">
                    {$items[0].description|escape:'htmlall':'UTF-8' nofilter}
                </div>
            {/if}

            <div class="bonmaintenance-countdown"
                data-bonmaintenance="{$items[0].data_end|escape:'htmlall':'UTF-8'}"
                style="color:{if $maintenance_countdown_color}{$maintenance_countdown_color|escape:'htmlall':'UTF-8'}{else}inherit{/if}">
            </div>

            <div class="bonmaintenance-social">
                {if $maintenance_facebook}
                    <div class="bonmaintenance_facebook">
                        <a href="{$maintenance_facebook|escape:'htmlall':'UTF-8'}">

                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-facebook">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg>
                        </a>
                    </div>
                {/if}
                {if $maintenance_twitter}
                    <div class="bonmaintenance_twitter">
                        <a href="{$maintenance_twitter|escape:'htmlall':'UTF-8'}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none"
                                stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-twitter">
                                <path
                                    d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z">
                                </path>
                            </svg>
                        </a>
                    </div>
                {/if}
                {if $maintenance_rss}
                    <div class="bonmaintenance_rss">
                        <a href="{$maintenance_rss|escape:'htmlall':'UTF-8'}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none"
                                stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-rss">
                                <path d="M4 11a9 9 0 0 1 9 9"></path>
                                <path d="M4 4a16 16 0 0 1 16 16"></path>
                                <circle cx="5" cy="19" r="1"></circle>
                            </svg>
                        </a>
                    </div>
                {/if}
                {if $maintenance_youtube}
                    <div class="bonmaintenance_youtube">
                        <a href="{$maintenance_youtube|escape:'htmlall':'UTF-8'}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none"
                                stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-youtube">
                                <path
                                    d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z">
                                </path>
                                <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
                            </svg>
                        </a>

                    </div>
                {/if}
                {if $maintenance_pinterest}
                    <div class="bonmaintenance_pinterest">
                        <a href="{$maintenance_pinterest|escape:'htmlall':'UTF-8'}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50"
                                stroke="currentColor"
                                width="20px" height="20px">
                                <path fill="currentColor"
                                    d="M 25 2 C 12.309295 2 2 12.309295 2 25 C 2 37.690705 12.309295 48 25 48 C 37.690705 48 48 37.690705 48 25 C 48 12.309295 37.690705 2 25 2 z M 25 4 C 36.609825 4 46 13.390175 46 25 C 46 36.609825 36.609825 46 25 46 C 22.876355 46 20.82771 45.682142 18.896484 45.097656 C 19.75673 43.659418 20.867347 41.60359 21.308594 39.90625 C 21.570728 38.899887 22.648438 34.794922 22.648438 34.794922 C 23.348841 36.132057 25.395277 37.263672 27.574219 37.263672 C 34.058123 37.263672 38.732422 31.300682 38.732422 23.890625 C 38.732422 16.78653 32.935409 11.472656 25.476562 11.472656 C 16.196831 11.472656 11.271484 17.700825 11.271484 24.482422 C 11.271484 27.636307 12.94892 31.562193 15.634766 32.8125 C 16.041611 33.001865 16.260073 32.919834 16.353516 32.525391 C 16.425459 32.226044 16.788267 30.766792 16.951172 30.087891 C 17.003269 29.871239 16.978043 29.68405 16.802734 29.470703 C 15.913793 28.392399 15.201172 26.4118 15.201172 24.564453 C 15.201172 19.822048 18.791452 15.232422 24.908203 15.232422 C 30.18976 15.232422 33.888672 18.832872 33.888672 23.980469 C 33.888672 29.796219 30.95207 33.826172 27.130859 33.826172 C 25.020554 33.826172 23.440361 32.080359 23.947266 29.939453 C 24.555054 27.38426 25.728516 24.626944 25.728516 22.78125 C 25.728516 21.130713 24.842754 19.753906 23.007812 19.753906 C 20.850369 19.753906 19.117188 21.984457 19.117188 24.974609 C 19.117187 26.877359 19.761719 28.166016 19.761719 28.166016 C 19.761719 28.166016 17.630543 37.176514 17.240234 38.853516 C 16.849091 40.52931 16.953851 42.786365 17.115234 44.466797 C 9.421139 41.352465 4 33.819328 4 25 C 4 13.390175 13.390175 4 25 4 z" />
                            </svg>
                        </a>
                    </div>
                {/if}
                {if $maintenance_vimeo}
                    <div class="bonmaintenance_vimeo">
                        <a href="{$maintenance_vimeo|escape:'htmlall':'UTF-8'}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50"
                                stroke="currentColor"
                                width="20px" height="20px">
                                <path fill="currentColor"
                                    d="M 40.976563 7 C 42.636719 7.042969 43.828125 7.46875 44.613281 8.300781 C 45.632813 9.382813 46.097656 11.273438 46 13.902344 C 45.796875 18.144531 42.597656 24.378906 36.976563 31.480469 C 29.804688 40.886719 25.25 42.863281 22.691406 42.863281 C 21.007813 42.863281 19.277344 40.722656 17.8125 36.839844 C 17.214844 34.589844 16.582031 32.3125 15.96875 30.109375 C 15.351563 27.886719 14.714844 25.585938 14.105469 23.289063 C 12.851563 18.90625 11.121094 14.429688 7.800781 14.429688 C 7.539063 14.429688 7.046875 14.429688 5.8125 15.066406 C 5.953125 14.953125 6.09375 14.84375 6.234375 14.730469 C 7.425781 13.789063 8.65625 12.8125 9.878906 11.703125 C 11.546875 10.257813 14.070313 8.070313 15.183594 7.992188 C 15.3125 7.980469 15.441406 7.972656 15.566406 7.972656 C 18.609375 7.972656 19.617188 11.722656 19.949219 13.96875 C 20.800781 19.445313 21.546875 22.464844 21.90625 23.914063 L 21.945313 24.070313 C 22.800781 28.089844 23.898438 32 27 32 C 28.203125 32 30.625 31.386719 33.359375 27.238281 C 35.746094 23.484375 36.292969 20.433594 36.402344 18.917969 C 36.550781 17.660156 36.605469 15.871094 35.476563 14.59375 C 34.382813 13.355469 32.78125 13.214844 31.671875 13.214844 C 31.597656 13.214844 31.523438 13.214844 31.453125 13.21875 C 33.546875 8.847656 36.472656 7 40.976563 7 M 40.976563 5 C 34.203125 5 30.464844 8.804688 28.167969 16.210938 C 29.371094 15.765625 30.578125 15.214844 31.671875 15.214844 C 33.972656 15.214844 34.738281 16.070313 34.410156 18.726563 C 34.300781 20.386719 33.644531 23.066406 31.671875 26.164063 C 29.699219 29.152344 27.984375 30 27 30 C 25.796875 30 24.882813 28.269531 23.898438 23.621094 C 23.570313 22.292969 22.804688 19.304688 21.925781 13.664063 C 21.203125 8.742188 18.878906 5.972656 15.570313 5.972656 C 15.382813 5.972656 15.191406 5.980469 15 6 C 13.46875 6.109375 11.636719 7.535156 8.570313 10.191406 C 6.378906 12.183594 4.300781 13.621094 2 15.613281 L 4.191406 18.421875 C 6.269531 16.984375 7.476563 16.429688 7.804688 16.429688 C 9.335938 16.429688 10.757813 18.863281 12.183594 23.84375 C 13.386719 28.378906 14.699219 32.914063 15.90625 37.449219 C 17.765625 42.429688 20.066406 44.863281 22.691406 44.863281 C 27.074219 44.863281 32.328125 40.882813 38.570313 32.695313 C 44.699219 24.949219 47.78125 18.535156 48 14 C 48.21875 8.027344 45.816406 5.109375 41 5 C 40.992188 5 40.984375 5 40.976563 5 Z" />
                            </svg>
                        </a>
                    </div>
                {/if}
                {if $maintenance_instagram}
                    <div class="bonmaintenance_instagram">
                        <a href="{$maintenance_instagram|escape:'htmlall':'UTF-8'}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-instagram">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                        </a>
                    </div>
                {/if}
                {if $maintenance_linkedin}
                    <div class="bonmaintenance_linkedin">
                        <a href="{$maintenance_linkedin|escape:'htmlall':'UTF-8'}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none"
                                stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-linkedin">
                                <path
                                    d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z">
                                </path>
                                <rect x="2" y="9" width="4" height="12"></rect>
                                <circle cx="4" cy="4" r="2"></circle>
                            </svg>
                        </a>
                    </div>
                {/if}
            </div>
        </div>
        <div class="bonmaintenance-contacts" style="color:{$maintenance_address_color|escape:'htmlall':'UTF-8'}">
            {if isset($shop.address.address1) && $shop.address.address1}
                <span> {$shop.address.address1|escape:'htmlall':'UTF-8'}</span>
            {/if}

            {if isset($shop.address.address2) && $shop.address.address2}
                <span>{$shop.address.address2|escape:'htmlall':'UTF-8'}</span><br>
            {/if}
            {if isset($shop.address.city) && $shop.address.city}
                <span>{$shop.address.city|escape:'htmlall':'UTF-8'}</span>,
            {/if}

            {if isset($shop.address.state) && $shop.address.state}
                <span>{$shop.address.state|escape:'htmlall':'UTF-8'}</span>
            {/if}

            {if isset($shop.address.postcode) && $shop.address.postcode}
                <span>{$shop.address.postcode|escape:'htmlall':'UTF-8'}</span>
            {/if}
            {if isset($shop.address.country) && $shop.address.country}
                <br><span>{$shop.address.country|escape:'htmlall':'UTF-8'}</span><br>
            {/if}

            {if isset($shop.phone) && $shop.phone}
                <a href="tel:{$shop.phone}">{$shop.phone|escape:'htmlall':'UTF-8'}{if isset($shop.fax)},{/if}</a>
            {/if}
            {if isset($shop.fax) && $shop.fax}
                <a href="tel:{$shop.fax}">{$shop.fax|escape:'htmlall':'UTF-8'}</a>
            {/if}
        </div>
    </div>
{/if}