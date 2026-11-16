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

{*
 * Rendered via hookDisplayCMSDisputeInformation on the configured Privacy /
 * Withdrawal CMS pages. See orderwithdrawl.php::hookDisplayCMSDisputeInformation.
 *}
{if isset($orderwithdrawl_cms_notices) && $orderwithdrawl_cms_notices}
    {foreach $orderwithdrawl_cms_notices as $notice}
        <section class="orderwithdrawl-privacy" aria-label="{if isset($notice.title) && $notice.title}{$notice.title|escape:'htmlall':'UTF-8'}{else}{l s='Information' mod='orderwithdrawl'}{/if}">
            {if isset($notice.title) && $notice.title}
                <h3 class="orderwithdrawl-privacy__title">
                    {$notice.title|escape:'htmlall':'UTF-8'}
                </h3>
            {/if}
            {if isset($notice.content) && $notice.content}
                <p class="orderwithdrawl-privacy__note">
                    {$notice.content|escape:'htmlall':'UTF-8'}
                </p>
            {/if}
        </section>
    {/foreach}
{/if}
