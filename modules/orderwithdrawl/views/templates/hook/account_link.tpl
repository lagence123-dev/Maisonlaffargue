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

{assign var='accountMode' value=$orderwithdrawl_account_mode|default:'legacy'}
{assign var='accountButtonClasses' value=$orderwithdrawl_button_classes|default:''}

{if $accountMode == 'card'}
    <a class="col-lg-4 col-md-6 col-sm-6 col-xs-12 orderwithdrawl-account-card{if $accountButtonClasses} {$accountButtonClasses|escape:'htmlall':'UTF-8'}{/if}"
       id="returns-link"
       href="{$orderwithdrawl_link|escape:'htmlall':'UTF-8'}"
       title="{$orderwithdrawl_label|escape:'htmlall':'UTF-8'}">
        <span class="link-item">
            <i class="material-icons" aria-hidden="true">assignment_return</i>
            {$orderwithdrawl_label|escape:'htmlall':'UTF-8'}
        </span>
    </a>
{else}
    <li class="orderwithdrawl-account-legacy">
        <a href="{$orderwithdrawl_link|escape:'htmlall':'UTF-8'}"
           {if $accountButtonClasses}class="{$accountButtonClasses|escape:'htmlall':'UTF-8'}"{/if}
           title="{$orderwithdrawl_label|escape:'htmlall':'UTF-8'}"
           rel="nofollow">
            {$orderwithdrawl_label|escape:'htmlall':'UTF-8'}
        </a>
    </li>
{/if}
