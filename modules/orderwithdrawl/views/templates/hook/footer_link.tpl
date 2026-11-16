{*
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com/ for more information.
 *
 * @author    sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 *}

{if $orderwithdrawl_footer_mode == 'button'}
    <div class="orderwithdrawl-footer-wrapper orderwithdrawl-footer-wrapper--{$orderwithdrawl_button_align|default:'center'|escape:'htmlall':'UTF-8'}">
        <a href="{$orderwithdrawl_link|escape:'htmlall':'UTF-8'}"
           class="orderwithdrawl-footer-button orderwithdrawl-footer-button--{$orderwithdrawl_button_size|default:'medium'|escape:'htmlall':'UTF-8'}{if isset($orderwithdrawl_button_classes) && $orderwithdrawl_button_classes} {$orderwithdrawl_button_classes|escape:'htmlall':'UTF-8'}{/if}"
           style="background-color: {$orderwithdrawl_button_color|default:'#F44708'|escape:'htmlall':'UTF-8'};{if isset($orderwithdrawl_button_font_color) && $orderwithdrawl_button_font_color} color: {$orderwithdrawl_button_font_color|escape:'htmlall':'UTF-8'};{/if} --orderwithdrawl-button-color: {$orderwithdrawl_button_color|default:'#F44708'|escape:'htmlall':'UTF-8'};{if isset($orderwithdrawl_button_font_color) && $orderwithdrawl_button_font_color} --orderwithdrawl-button-font-color: {$orderwithdrawl_button_font_color|escape:'htmlall':'UTF-8'};{/if}"
           title="{$orderwithdrawl_label|escape:'htmlall':'UTF-8'}"
           rel="nofollow">
            {$orderwithdrawl_label|escape:'htmlall':'UTF-8'}
        </a>
    </div>
{else}
    <div class="orderwithdrawl-footer-link-wrapper text-center  col-md-3">
        <a href="{$orderwithdrawl_link|escape:'htmlall':'UTF-8'}"
           class="orderwithdrawl-footer-simple{if isset($orderwithdrawl_button_classes) && $orderwithdrawl_button_classes} {$orderwithdrawl_button_classes|escape:'htmlall':'UTF-8'}{/if}"
           {if isset($orderwithdrawl_link_color) && $orderwithdrawl_link_color}style="color: {$orderwithdrawl_link_color|escape:'htmlall':'UTF-8'}"{/if}
           title="{$orderwithdrawl_label|escape:'htmlall':'UTF-8'}"
           rel="nofollow">
            {$orderwithdrawl_label|escape:'htmlall':'UTF-8'}
        </a>
    </div>
{/if}
