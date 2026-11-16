{*
* 2015-2020 Bonpresta
*
* Bonpresta Theme
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

<style>
    #header .position-static>#_desktop_search_widget {
        display: none;
    }
</style>

{if !Module::isEnabled('bonmegamenu')}
    <style>
        @media screen and (min-width: 992px) {
            body:not(.header-4) #header #_mobile_logo {
                display: none;
            }

            body.header-3 #header .position-static > #_desktop_search_widget,
            body.header-4 #header .position-static > #_desktop_search_widget {
                display: flex;
            }
            
            body.header-3 #header .position-static .bon-nav-bar #_desktop_search_widget,
            body.header-4 #header .position-static .bon-nav-bar #_desktop_search_widget {
                display: none;
            }

            body.header-3 #header .header-top .bonmmenu {
                display: none !important;
            }

            body.header-3 #header .header-top {
                margin-bottom: 20px;
            }

            body.header-3 #header {
                padding: 20px 0 0;
                border-bottom: none;
            }

            body.header-4 #header #_desktop_logo {
                display: none;
            }

            body.header-4 #header .header-banner {
                margin-bottom: 15px;
            }

            body.header-3 #header #header-nav {
                display: block;
            }
        }

        @media screen and (max-width: 991px) {
            #header #_desktop_logo,
            body.header-4 #header .header-banner .header-contact {
                display: none;
            }

            #header .header-banner .header-contact-info {
                justify-content: center;
            }

            #header {
                padding: 0;
            }

            #header .header-banner {
                padding: 20px 0;
            }
            
            #header .bon-nav-bar {
                justify-content: space-between;
            }

            body.header-1 #header .header-top .position-static {
                justify-content: space-between;
            }
        }
    </style>    
{/if}

{if isset($theme_color_demo) && $theme_color_demo}
<button id="custom-toggler"></button>
{/if}

<div class="bon-custom-menu">
    {if isset($theme_enable_setting) && $theme_enable_setting}
        <button id="custom-menu-open"></button>
    {/if}
    <div class="boxed-setting">
        <p>Boxed:</p>
        <span class="toggle-bg ">
            <input class="input-boxed " type="radio" value="on">
            <span class="switch-boxed "></span>
        </span>
    </div>
    <div class="Sticky-header">
        <p>Sticky Header:</p>
        <span class="toggle-bg active">
            <input class="input-sticky-header" type="radio" value="on">
            <span class="switch-header active"></span>
        </span>
    </div>
    <div class="sticky-addcart">
        <p>Sticky Add To Cart</p>
        <span class="toggle-bg active">
            <input class="input-sticky-cart" type="radio" value="on">
            <span class="switch-cart active"></span>
        </span>
    </div>
    <div class="Sticky-footer">
        <p>Sticky Footer:</p>
        <span class="toggle-bg active">
            <input class="input-sticky-footer" type="radio" value="on">
            <span class="switch-footer active"></span>
        </span>
    </div>
    <div class="bon-select-language">
        <p>Font:</p>
        <form id="bon-select">
            <select class="bon-select-form" name="language-select" form="bon-select">
                <option value="Lato">Lato</option>
                <option value="Raleway">Raleway</option>
                <option value="OpenSans">OpenSans</option>
                <option value="Roboto">Roboto</option>
                <option value="Oswald">Oswald</option>
                <option value="Ubuntu">Ubuntu</option>
                <option value="Playfair">Playfair</option>
                <option value="Lora">Lora</option>
                <option value="Indie">Indie</option>
                <option value="Hind">Hind</option>
            </select>
        </form>
    </div>
</div>
{if isset($theme_enable_header) && $theme_enable_header}
    <div class="bon-header-menu">
        <button id="bon-header-open">
            {* <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                <g id="Layer_41" data-name="Layer 41">
                    <path
                        d="M54.83,5.67H9.17a3.5,3.5,0,0,0-3.5,3.5V54.83a3.5,3.5,0,0,0,3.5,3.5H54.83a3.5,3.5,0,0,0,3.5-3.5V9.17A3.5,3.5,0,0,0,54.83,5.67Zm.5,3.5V30.5h-36V8.67H54.83A.5.5,0,0,1,55.33,9.17ZM8.67,54.83V9.17a.5.5,0,0,1,.5-.5h7.18V55.33H9.17A.5.5,0,0,1,8.67,54.83Zm10.68.5V33.5H41.18V55.33Zm35.48,0H44.18V33.5H55.33V54.83A.5.5,0,0,1,54.83,55.33Z" />
                </g>
            </svg> *}

            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px"
                viewBox="0 0 24 24" xml:space="preserve">
                <path
                    d="M21.5,1h-19C1.122,1,0,2.122,0,3.5v17C0,21.878,1.122,23,2.5,23h19c1.378,0,2.5-1.122,2.5-2.5v-17C24,2.122,22.878,1,21.5,1z M4.5,3C4.776,3,5,3.224,5,3.5S4.776,4,4.5,4S4,3.776,4,3.5S4.224,3,4.5,3z M2.5,3C2.776,3,3,3.224,3,3.5S2.776,4,2.5,4S2,3.776,2,3.5S2.224,3,2.5,3z M7,22H2.5C1.673,22,1,21.327,1,20.5V6h6V22zM6.5,4C6.224,4,6,3.776,6,3.5S6.224,3,6.5,3S7,3.224,7,3.5S6.776,4,6.5,4z M23,20.5c0,0.827-0.673,1.5-1.5,1.5H8V12h15V20.5z M23,11H8V6h15V11z" />
            </svg>
        </button>
        <div class="header-settings">
            {assign var="imgDir" value="{_MODULE_DIR_}/bontheme/views/img"}
            <div class="header-type" data-type="1">
                <img src="{$imgDir}/header-1.png" alt="header img" class="header-img">
                <p class="header-name">Header 1</p>
            </div>
            <div class="header-type" data-type="2">
                <img src="{$imgDir}/header-2.png" alt="header img" class="header-img">
                <p class="header-name">Header 2</p>
            </div>
            <div class="header-type" data-type="3">
                <img src="{$imgDir}/header-3.png" alt="header img" class="header-img">
                <p class="header-name">Header 3</p>
            </div>
            <div class="header-type" data-type="4">
                <img src="{$imgDir}/header-4.png" alt="header img" class="header-img">
                <p class="header-name">Header 4</p>
            </div>
        </div>
    </div>
{/if}

{if isset($theme_promo) && $theme_promo && $theme_enable_promo && $theme_enable_promo && $page.page_name === "index"}
    <div class="promo-container active-list">
        <ul class="promo-list">
            <li class="promo-item active-item">
                <a class="promo-item-inner" target="_blank" href="{$theme_promo_link}">
                    <div class="item-description">
                        <span class="name">{l s='Promotion Code -30%' mod='bontheme'}</span>
                        <div class="promo-code-value">
                            <span class="value">{$theme_promo}</span>
                        </div>
                        <span class="btn btn-primary">{l s='Buy now' mod='bontheme'}</span>
                    </div>
                </a>
                <button class="close-promo-popup"></button>
            </li>
        </ul>
    </div>
{/if}


{if isset($theme_color_enable) && $theme_color_enable}
<style>
    ::selection, #boncompare .compare-count, #header #_desktop_cart .blockcart .cart-products-count, #navigation #_desktop_cart .blockcart .cart-products-count,
    #_desktop_top_menu ul[data-depth='0']>li>a:after,
    .tabs .nav-tabs .nav-item .nav-link:after,
    .custom-radio input[type='radio']+span:before,
    .product-actions .add-to-cart:hover,
    .featured-products .thumbnail-container .ajax_add_to_cart_button:hover,
    .product-actions .add-to-cart {
        background-color: {$theme_color|escape: 'html':'UTF-8'};
    }
    #bonbanners a .banner-inner span,
    #bonwishlist .wishlist-count,
    #header .blockcart .cart-products-count,
    #bonslick .slick-slide .box-bonslick span,
    #bonslick .slick-slide .box-bonslick span:hover,
    li.product-flag.new,
    .meshim_widget_components_chatButton_Button .button_bar,
    body .bon-shipping,
    .btn-primary,
    .btn-primary:hover,
    .custom-checkbox input[type=checkbox]+span .checkbox-checked,
    .bonpromotion-countdown-btn,
    .bonsearch .bonsearch_btn,
    .toggle-bg.active {
        background: none;
               background-color: {$theme_color|escape: 'html':'UTF-8'};
               border-color: {$theme_color|escape: 'html':'UTF-8'};
        box-shadow: none;
    }

    nav#header-nav .navigation #_desktop_top_menu .top-menu .nav-arrows i,
    #product-availability .product-available,
    .pagination .current a,
    .product-page-right .product-price .current-price,
    #_desktop_top_menu>.top-menu>li.sfHover>a,a:hover,
    #header #_desktop_cart .blockcart i:hover, #navigation #_desktop_cart .blockcart i:hover,
    #header #_desktop_setting-header:focus i, #header #_desktop_setting-header:hover i,
    #header #_desktop_user_info:focus i, #header #_desktop_user_info:hover i,
    #navigation #_desktop_setting-header:focus i, #navigation #_desktop_setting-header:hover i,
    #navigation #_desktop_user_info:focus i, #navigation #_desktop_user_info:hover i,
    #main .product-information .product-actions #group_1 .input-container label span.check,
    .product-container .product-list .product-item .item-description .product-item-name,
    .product-container .product-list .product-item .item-description .product-item-name:hover,
    #bonwishlist .wishlist_add_to_cart_button:hover i,
    #bonwishlist .wishlist-summary-product-name .product-title:hover span,
    #header .header-top .position-static #_desktop_setting-header i.active {
              color: {$theme_color|escape: 'html':'UTF-8'};
    }

    li.product-flag.new:after,
    #productCommentsBlock .pull-right .open-comment-form {
               border-color: {$theme_color|escape: 'html':'UTF-8'};
        border-right-color: transparent;
        border-bottom-color: transparent;
    }

    .footer-container .links li a:hover:before,
    #bonwishlist .wishlist-tooltip:hover i,
    .products-sort-order .select-title:after,
    #video-container #controls .play:hover:before,
    #video-container #controls .pause:hover:before,
    #video-container #controls .mute:hover:before,
    #video-container #controls .unmute:hover:before,
    #bonslick .slick-prev:hover:before,
    #bonslick .slick-next:hover:before,
    #main .images-container .js-qv-mask .slick-slider .slick-arrow.slick-next:hover:before,
    #main .images-container .js-qv-mask .slick-slider .slick-arrow.slick-prev:hover:before,
    .bonsearch:focus,
    .bonsearch_button.active,
    #header .header-top .position-static #_desktop_setting-header i.active,
    .quickview .modal-content .modal-body .product-price .current-price,
    .comments_note a span:hover,
    .product-quantity .bon-product-popup .title-popup-1:hover,
    .product-quantity .bon-product-popup .title-popup-2:hover,
    .product-add-to-cart .product-quantity .bon-review-inner a:hover,
    .product-quantity .bon-product-popup .title-popup-1:hover:before,
    .product-quantity .bon-product-popup .title-popup-2:hover:before,
    .product-add-to-cart .product-quantity .bon-review-inner a:hover:before {
               color: {$theme_color|escape: 'html':'UTF-8'};
    }

    #header .top-menu a[data-depth="0"]:hover,
    #header .header-top .position-static .bon-nav-bar #_desktop_setting-header i:hover,
    .bonsearch:hover,
    .bonsearch_button.active,
    #header .header-top .position-static .bon-nav-bar #_desktop_user_info i:hover,
    #header .header-top .position-static .bon-nav-bar #_desktop_cart .blockcart i:hover,
    #header .header-top .position-static .bon-nav-bar #_desktop_setting-header i.active,
    .bonthumbnails li a:hover,
    .footer-container .links li a:hover,
    #wrapper .breadcrumb li a:hover,
    .pagination a:not(.previous):not(.next):hover,
    .pagination .next:hover,
    .pagination .previous:hover,
    .featured-products .product-title a:hover,
    .product-accessories .product-title a:hover,
    .product-miniature .product-title a:hover,
    .footer-container-bottom a:hover,
    #search_filters .facet .facet-label a:hover,
    #_desktop_top_menu .sub-menu ul[data-depth="1"]>li a:hover,
    #_desktop_top_menu .sub-menu ul[data-depth="2"]>li a:hover,
    .footer-container .product-container .product-list .product-item .item-description .product-item-name:hover,
    #header #_desktop_currency_selector .currency-selector ul li a:hover,
    #back-to-top:hover,
    .bonsearch #search_popup .wrap_item .product_image h5:hover,
    #bon_manufacturers_block .owl-nav .owl-next:hover,
    #bon_manufacturers_block .owl-nav .owl-prev:hover,
    .bon_manufacture_list h4 a:hover,
    .bon-newsletter .bon-newsletter-close>i:hover,
    .product-add-to-cart .product-quantity .bon-product-popup .bon-product-delivery a:hover:before,
    .product-add-to-cart .product-quantity .bon-product-popup .bon-product-delivery a:hover,
    #main .product-information .product-actions #group_1 .input-container label:hover span.radio-label,
    .product-add-to-cart .product-quantity .bon-product-popup .bon-product-size a:hover,
    .product-add-to-cart .product-quantity .bon-product-popup .bon-product-size a:hover:before,
    .product-add-to-cart .product-quantity .bon-product-popup .bon-product-size a:hover,
    .product-add-to-cart .product-quantity .bon-product-popup .bon-product-size a:hover:before,
    #bonwishlist .wishlist-tooltip:hover i {
               color: {$theme_color|escape: 'html':'UTF-8'};
    }

    .product-actions .add-to-cart:hover,
    .featured-products .thumbnail-container .ajax_add_to_cart_button:hover,
    #main .product-information .product-actions #group_1 .input-container label span.check,
    #main .product-information .product-actions #group_1 .input-container label:hover span.radio-label {
               border-color: {$theme_color|escape: 'html':'UTF-8'};
        ;
    }

    .bonthumbnails li a:hover,
    #main .images-container .js-qv-mask .slick-slider .slick-slide:hover,
    #main .images-container .js-qv-mask .slick-slider .slick-slide.selected,
    body .bonthumbnails li.active,
    body .bonthumbnails li:focus {
               box-shadow: inset 0 0 0 2px {$theme_color|escape: 'html':'UTF-8'};
    }
</style>
{/if}