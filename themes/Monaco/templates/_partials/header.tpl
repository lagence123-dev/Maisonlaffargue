{**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License 3.0 (AFL-3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
    * @copyright 2007-2020 PrestaShop SA
    * @license https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
    * International Registered Trademark & Property of PrestaShop SA
    *}
    {block name='header_banner'}
        <div class="header-banner">
            <div class="container header-contact-info">
                <div id="_mobile_logo">
                    <a href="{$urls.base_url}">
                        <img class="logo" src="{$shop.logo}" alt="{$shop.name}" loading="lazy" width="{$shop.logo_details.width}" height="{$shop.logo_details.height}">
                    </a>
                </div>
                <div class="header-contact left-block">
                    {hook h='displayBanner'}
                </div>
                <div class="header-contact right-block">
                    {if $shop.phone}
                        <div class="shop_phone">
                            <i class="fl-outicons-phone14 header_phone_icon"></i>
                            <a href="tel:{$shop.phone}" class="header_phone">{$shop.phone}</a>
                        </div>
                    {/if}
                    {if $shop.address.country}
                        <div class="shop_address_country d-flex">
                            <i class="fl-outicons-pin12 mr-1 header_address_icon"></i>
                            <div class="header_address">{$shop.name} {$shop.address.country}</div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    {/block}
    {block name='header_top'}
        <div class="header-top revealOnScroll animated fadeInUp" data-animation="fadeInUp">
            <div class="container">
                <div class="row">
                    <div class="col-12 position-static">
                        {if !Module::isEnabled('bonmegamenu')}
                            <div class="d-lg-none text-sm-center mobile">
                                <div class="float-xs-left" id="menu-icon">
                                    <i class="fl-outicons-lines7"></i>
                                </div>
                            </div>
                        {/if}
                        <div id="_desktop_logo">
                            <a href="{$urls.base_url}">
                                <img class="logo" src="{$shop.logo}" alt="{$shop.name}" loading="lazy" width="{$shop.logo_details.width}" height="{$shop.logo_details.height}">
                            </a>
                        </div>
                        {hook h='displayBonSearch'}
                        {if Module::isEnabled('bonmegamenu')}
                            {widget name='bonmegamenu' hook='displayCustomBonmegamenuMobile'}
                            {widget name='bonmegamenu' hook='displayCustomBonmegamenu'}
                        {else}
                            {hook h='displayNav'}
                        {/if}
                        <div class="bon-nav-bar">
                            {hook h='displayTop'}
                        </div>
                    </div>
                </div>
                {if !Module::isEnabled('bonmegamenu')}
                    <div id="mobile_top_menu_wrapper" class="d-block d-lg-none">
                        <div class="js-top-menu mobile" id="_mobile_top_menu"></div>
                        <div class="js-top-menu-bottom">
                            <div id="_mobile_currency_selector"></div>
                            <div id="_mobile_language_selector"></div>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
        {if $modules.bontheme.theme_header_type !== 'header-3'}
            {hook h='displayNavFullWidth'}
        {/if}
    {/block}
    