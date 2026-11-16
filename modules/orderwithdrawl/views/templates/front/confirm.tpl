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
 * Confirmation view after a successful withdrawal submission.
 *
 * This template consumes the Smarty variable `orderwithdrawl_confirmation`, which is
 * populated by the submit controller via the POST-Redirect-GET pattern (Batch 2).
 * The controller is expected to persist a one-time confirmation payload (session or
 * short-lived token) and assign it as `orderwithdrawl_confirmation` when rendering
 * the confirm view, so reloads/back-navigation still show the receipt.
 *
 * Expected payload (all optional except `order_reference`, used as the submitted identifier):
 *   orderwithdrawl_confirmation.order_reference  (string)
 *   orderwithdrawl_confirmation.customer_name    (string)
 *   orderwithdrawl_confirmation.email            (string)
 *   orderwithdrawl_confirmation.submitted_at     (string, localised)
 *   orderwithdrawl_confirmation.message          (string, plain text)
 *
 * Legacy behaviour (inline confirmation after POST) is retained as fallback so that
 * current tests continue to pass until the controller side of the Redirect-GET flow
 * ships with Batch 2.
 *}

{extends file='page.tpl'}

{block name='page_title'}
    {l s='Confirmation of your withdrawal request' mod='orderwithdrawl'}
{/block}

{block name='page_content'}
    {if isset($orderwithdrawl_confirmation) && $orderwithdrawl_confirmation}
        <div class="alert alert-success orderwithdrawl-confirmation" role="status" aria-live="polite">
            <h2 class="orderwithdrawl-confirmation__title">
                {l s='Your withdrawal request has been received.' mod='orderwithdrawl'}
            </h2>

            {if isset($orderwithdrawl_confirmation.order_reference) && $orderwithdrawl_confirmation.order_reference}
                <p>
                    <strong>{l s='Order ID or order number:' mod='orderwithdrawl'}</strong>
                    {$orderwithdrawl_confirmation.order_reference|escape:'html':'UTF-8'}
                </p>
            {/if}

            {if isset($orderwithdrawl_confirmation.customer_name) && $orderwithdrawl_confirmation.customer_name}
                <p>
                    <strong>{l s='First and last name:' mod='orderwithdrawl'}</strong>
                    {$orderwithdrawl_confirmation.customer_name|escape:'html':'UTF-8'}
                </p>
            {/if}

            {if isset($orderwithdrawl_confirmation.email) && $orderwithdrawl_confirmation.email}
                <p>
                    <strong>{l s='Email:' mod='orderwithdrawl'}</strong>
                    {$orderwithdrawl_confirmation.email|escape:'html':'UTF-8'}
                </p>
            {/if}

            {if isset($orderwithdrawl_confirmation.submitted_at) && $orderwithdrawl_confirmation.submitted_at}
                <p>
                    <strong>{l s='Submitted at:' mod='orderwithdrawl'}</strong>
                    {$orderwithdrawl_confirmation.submitted_at|escape:'html':'UTF-8'}
                </p>
            {/if}

            {if isset($orderwithdrawl_confirmation.message) && $orderwithdrawl_confirmation.message}
                <div class="orderwithdrawl-confirmation__message">
                    {$orderwithdrawl_confirmation.message|escape:'html':'UTF-8'}
                </div>
            {/if}
        </div>
    {else}
        {* Legacy inline-confirm flow: re-submit gate before the withdrawal is finalised. *}
        <div class="alert alert-info" role="status" aria-live="polite">
            {l s='Please confirm that you want to withdraw from the contract for order' mod='orderwithdrawl'}
            <strong>{$order_reference|escape:'htmlall':'UTF-8'}</strong>.
        </div>
        <form method="post" action="{$module_link_submit|escape:'htmlall':'UTF-8'}" class="orderwithdrawl-form">
            <input type="hidden" name="orderwithdrawl_token" value="{Tools::getToken(false)|escape:'html':'UTF-8'}">
            <input type="hidden" name="customer_firstname" value="{if isset($customer_firstname)}{$customer_firstname|escape:'htmlall':'UTF-8'}{/if}">
            <input type="hidden" name="customer_lastname" value="{if isset($customer_lastname)}{$customer_lastname|escape:'htmlall':'UTF-8'}{/if}">
            <input type="hidden" name="customer_name" value="{if isset($customer_name)}{$customer_name|escape:'htmlall':'UTF-8'}{/if}">
            <input type="hidden" name="order_reference" value="{$order_reference|escape:'htmlall':'UTF-8'}">
            <input type="hidden" name="email" value="{$email|escape:'htmlall':'UTF-8'}">
            <button type="submit" class="btn btn-danger orderwithdrawl-btn">
                {l s='Confirm withdrawal' mod='orderwithdrawl'}
            </button>
        </form>
    {/if}
{/block}
