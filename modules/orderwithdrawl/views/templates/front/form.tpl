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

{extends file='page.tpl'}

{block name='page_title'}
    {$button_label|escape:'html':'UTF-8'}
{/block}

{block name='page_content'}
    {assign var='productSelectionEnabled' value=isset($orderwithdrawl_product_selection_enabled) && $orderwithdrawl_product_selection_enabled}
    {assign var='productSelectionReady' value=isset($orderwithdrawl_product_selection_ready) && $orderwithdrawl_product_selection_ready}

    {if isset($orderwithdrawl_notifications.error) && $orderwithdrawl_notifications.error}
        <div class="alert alert-danger orderwithdrawl-notification orderwithdrawl-notification--error" role="alert" aria-live="polite" aria-atomic="true">
            <ul class="mb-0">
                {foreach from=$orderwithdrawl_notifications.error item=message}
                    <li>{$message|escape:'html':'UTF-8'}</li>
                {/foreach}
            </ul>
        </div>
    {/if}

    {if isset($orderwithdrawl_notifications.success) && $orderwithdrawl_notifications.success}
        <div class="alert alert-success orderwithdrawl-notification orderwithdrawl-notification--success" role="status" aria-live="polite">
            <ul class="mb-0">
                {foreach from=$orderwithdrawl_notifications.success item=message}
                    <li>{$message|escape:'html':'UTF-8'}</li>
                {/foreach}
            </ul>
        </div>
    {/if}

    {if isset($orderwithdrawl_notifications.warning) && $orderwithdrawl_notifications.warning}
        <div class="alert alert-warning orderwithdrawl-notification orderwithdrawl-notification--warning" role="alert" aria-live="polite" aria-atomic="true">
            <ul class="mb-0">
                {foreach from=$orderwithdrawl_notifications.warning item=message}
                    <li>{$message|escape:'html':'UTF-8'}</li>
                {/foreach}
            </ul>
        </div>
    {/if}

    {if isset($orderwithdrawl_notifications.info) && $orderwithdrawl_notifications.info}
        <div class="alert alert-info orderwithdrawl-notification orderwithdrawl-notification--info" role="status" aria-live="polite">
            <ul class="mb-0">
                {foreach from=$orderwithdrawl_notifications.info item=message}
                    <li>{$message|escape:'html':'UTF-8'}</li>
                {/foreach}
            </ul>
        </div>
    {/if}

    {if isset($success) && $success}
        <div class="alert alert-success" role="status" aria-live="polite">
            {l s='Your withdrawal request for order %s has been received. A confirmation email has been sent.' sprintf=[$ref] mod='orderwithdrawl'}
        </div>
    {/if}

    {if isset($error_message) && $error_message}
        <div class="alert alert-danger orderwithdrawl-form-errors" role="alert" aria-live="polite" aria-atomic="true">
            {$error_message|escape:'html':'UTF-8'}
        </div>
    {/if}

    <form method="post"
          action="{$module_link_submit|escape:'htmlall':'UTF-8'}"
          class="orderwithdrawl-form"
          novalidate>
        {* CSRF token (consumed by submit controller in Batch 2) *}
        <input type="hidden" name="orderwithdrawl_token" value="{Tools::getToken(false)|escape:'html':'UTF-8'}">

        {* Honeypot field for bot protection (consumed by submit controller in Batch 2) *}
        <input type="text"
               name="website_url"
               value=""
               autocomplete="off"
               tabindex="-1"
               style="position:absolute;left:-9999px;"
               aria-hidden="true">

        {if !isset($show_customer_name_field) || $show_customer_name_field}
            <div class="form-group orderwithdrawl-form-group">
                <label for="customer_firstname">
                    {l s='First name' mod='orderwithdrawl'}
                    <span class="orderwithdrawl-required-marker" aria-label="{l s='Required field' mod='orderwithdrawl'}">*</span>
                </label>
                <input type="text"
                       id="customer_firstname"
                       name="customer_firstname"
                       class="form-control"
                       value="{if isset($form_values.customer_firstname)}{$form_values.customer_firstname|escape:'html':'UTF-8'}{/if}"
                       autocomplete="given-name"
                       aria-describedby="customer_firstname_help{if isset($errors.customer_firstname) && $errors.customer_firstname} customer_firstname_error{/if}"
                       {if isset($errors.customer_firstname) && $errors.customer_firstname}aria-invalid="true"{/if}
                       required>
                <small id="customer_firstname_help" class="form-text text-muted">
                    {l s='Enter the first name used for the order.' mod='orderwithdrawl'}
                </small>
                {if isset($errors.customer_firstname) && $errors.customer_firstname}
                    <span id="customer_firstname_error" class="orderwithdrawl-field-error" role="alert">
                        {$errors.customer_firstname|escape:'html':'UTF-8'}
                    </span>
                {/if}
            </div>

            <div class="form-group orderwithdrawl-form-group">
                <label for="customer_lastname">
                    {l s='Last name' mod='orderwithdrawl'}
                    <span class="orderwithdrawl-required-marker" aria-label="{l s='Required field' mod='orderwithdrawl'}">*</span>
                </label>
                <input type="text"
                       id="customer_lastname"
                       name="customer_lastname"
                       class="form-control"
                       value="{if isset($form_values.customer_lastname)}{$form_values.customer_lastname|escape:'html':'UTF-8'}{/if}"
                       autocomplete="family-name"
                       aria-describedby="customer_lastname_help{if isset($errors.customer_lastname) && $errors.customer_lastname} customer_lastname_error{/if}"
                       {if isset($errors.customer_lastname) && $errors.customer_lastname}aria-invalid="true"{/if}
                       required>
                <small id="customer_lastname_help" class="form-text text-muted">
                    {l s='Enter the last name used for the order.' mod='orderwithdrawl'}
                </small>
                {if isset($errors.customer_lastname) && $errors.customer_lastname}
                    <span id="customer_lastname_error" class="orderwithdrawl-field-error" role="alert">
                        {$errors.customer_lastname|escape:'html':'UTF-8'}
                    </span>
                {/if}
            </div>
        {/if}

        <div class="form-group orderwithdrawl-form-group">
            <label for="order_reference">
                {$order_identifier_label|escape:'html':'UTF-8'}
                <span class="orderwithdrawl-required-marker" aria-label="{l s='Required field' mod='orderwithdrawl'}">*</span>
            </label>
            <input type="text"
                   id="order_reference"
                   name="order_reference"
                   class="form-control"
                   value="{if isset($form_values.order_reference)}{$form_values.order_reference|escape:'html':'UTF-8'}{/if}"
                   placeholder="{$order_identifier_placeholder|escape:'html':'UTF-8'}"
                   aria-describedby="order_reference_help{if isset($errors.order_reference) && $errors.order_reference} order_reference_error{/if}"
                   {if isset($errors.order_reference) && $errors.order_reference}aria-invalid="true"{/if}
                   required>
            <small id="order_reference_help" class="form-text text-muted">
                {$order_identifier_help|escape:'html':'UTF-8'}
            </small>
            {if isset($errors.order_reference) && $errors.order_reference}
                <span id="order_reference_error" class="orderwithdrawl-field-error" role="alert">
                    {$errors.order_reference|escape:'html':'UTF-8'}
                </span>
            {/if}
        </div>

        <div class="form-group orderwithdrawl-form-group">
            <label for="email">
                {l s='Email for receipt confirmation' mod='orderwithdrawl'}
                <span class="orderwithdrawl-required-marker" aria-label="{l s='Required field' mod='orderwithdrawl'}">*</span>
            </label>
            <input type="email"
                   id="email"
                   name="email"
                   class="form-control"
                   value="{if isset($form_values.email)}{$form_values.email|escape:'html':'UTF-8'}{/if}"
                   aria-describedby="email_help{if isset($errors.email) && $errors.email} email_error{/if}"
                   {if isset($errors.email) && $errors.email}aria-invalid="true"{/if}
                   required>
            <small id="email_help" class="form-text text-muted">
                {l s='We will send the confirmation of receipt to this address.' mod='orderwithdrawl'}
            </small>
            {if isset($errors.email) && $errors.email}
                <span id="email_error" class="orderwithdrawl-field-error" role="alert">
                    {$errors.email|escape:'html':'UTF-8'}
                </span>
            {/if}
        </div>

        {if !$productSelectionEnabled || $productSelectionReady}
        <fieldset class="form-group orderwithdrawl-form-group orderwithdrawl-scope">
            <legend class="orderwithdrawl-scope__legend">
                {l s='What would you like to withdraw?' mod='orderwithdrawl'}
            </legend>
            <label class="orderwithdrawl-radio">
                <input type="radio"
	                       name="withdrawal_scope"
	                       value="full"
	                       data-orderwithdrawl-scope
	                       {if !$productSelectionEnabled || !$productSelectionReady}checked{/if}>
                <span>{l s='Withdraw the entire order' mod='orderwithdrawl'}</span>
            </label>
            <label class="orderwithdrawl-radio">
                <input type="radio"
	                       name="withdrawal_scope"
	                       value="partial"
	                       data-orderwithdrawl-scope
	                       {if $productSelectionEnabled && $productSelectionReady}checked{/if}>
                <span>{l s='Withdraw individual items or services' mod='orderwithdrawl'}</span>
            </label>
            <small class="form-text text-muted">
                {l s='These details are used only to identify the contract or the part of the contract you want to withdraw.' mod='orderwithdrawl'}
                {l s='A withdrawal reason is not required.' mod='orderwithdrawl'}
            </small>

            <div class="orderwithdrawl-contract-parts"
                 data-orderwithdrawl-contract-parts-block>
                {if $productSelectionEnabled}
                    <div class="orderwithdrawl-order-items">
                        {foreach from=$orderwithdrawl_order_items item=item}
                            <label class="orderwithdrawl-order-item">
                                <input type="checkbox"
                                       name="order_item_selection[]"
                                       value="{$item.id_order_detail|intval}"
                                       {if isset($orderwithdrawl_single_order_item) && $orderwithdrawl_single_order_item}checked{/if}>
                                <span class="orderwithdrawl-order-item__main">
                                    <span class="orderwithdrawl-order-item__name">
                                        {$item.name|escape:'html':'UTF-8'}
                                    </span>
                                    {if $item.reference}
                                        <span class="orderwithdrawl-order-item__reference">
                                            {$item.reference|escape:'html':'UTF-8'}
                                        </span>
                                    {/if}
                                </span>
                                <span class="orderwithdrawl-order-item__quantity">
                                    <span>{l s='Quantity' mod='orderwithdrawl'}</span>
                                    <input type="number"
                                           name="order_item_quantity[{$item.id_order_detail|intval}]"
                                           class="form-control"
                                           min="1"
                                           max="{$item.quantity|intval}"
                                           value="{$item.quantity|intval}">
                                </span>
                            </label>
                        {/foreach}
                    </div>
                    <small class="form-text text-muted">
                        {l s='Select the items or services you want to withdraw. A withdrawal reason is not required.' mod='orderwithdrawl'}
                    </small>
                {else}
                <div class="orderwithdrawl-contract-parts__rows"
                     data-orderwithdrawl-contract-parts>
                    <div class="orderwithdrawl-contract-part-row"
                         data-orderwithdrawl-contract-part-row>
                        <div class="orderwithdrawl-contract-part-field">
                            <label>
                                {l s='Item name' mod='orderwithdrawl'}
                                <input type="text"
                                       name="contract_part_name[]"
                                       class="form-control"
                                       autocomplete="off">
                            </label>
                        </div>
                        <div class="orderwithdrawl-contract-part-field">
                            <label>
                                {l s='Item number' mod='orderwithdrawl'}
                                <input type="text"
                                       name="contract_part_number[]"
                                       class="form-control"
                                       autocomplete="off">
                            </label>
                        </div>
                        <div class="orderwithdrawl-contract-part-field orderwithdrawl-contract-part-field--quantity">
                            <label>
                                {l s='Quantity' mod='orderwithdrawl'}
                                <input type="text"
                                       name="contract_part_quantity[]"
                                       class="form-control"
                                       inputmode="decimal"
                                       autocomplete="off">
                            </label>
                        </div>
                        <button type="button"
                                class="btn btn-outline-secondary orderwithdrawl-contract-part-remove"
                                data-orderwithdrawl-remove-contract-part
                                hidden>
                            {l s='Remove' mod='orderwithdrawl'}
                        </button>
                    </div>
                </div>
                <button type="button"
                        class="btn btn-secondary orderwithdrawl-contract-part-add"
                        data-orderwithdrawl-add-contract-part>
                    + {l s='Add another item' mod='orderwithdrawl'}
                </button>
                <small class="form-text text-muted">
                    {l s='For partial withdrawals, provide at least an item name or item number. The quantity helps identify the item more clearly.' mod='orderwithdrawl'}
                </small>
                {/if}
            </div>
        </fieldset>
        {/if}

        {if $orderwithdrawl_enable_customer_note && (!$productSelectionEnabled || $productSelectionReady)}
            <div class="form-group orderwithdrawl-form-group">
                <label for="customer_note">
                    {l s='Additional information for your withdrawal (optional)' mod='orderwithdrawl'}
                </label>
                <textarea id="customer_note"
                          name="customer_note"
                          class="form-control orderwithdrawl-note-textarea"
                          rows="4"
                          aria-describedby="customer_note_help{if isset($errors.customer_note) && $errors.customer_note} customer_note_error{/if}"
                          {if isset($errors.customer_note) && $errors.customer_note}aria-invalid="true"{/if}>{if isset($form_values.customer_note)}{$form_values.customer_note|escape:'html':'UTF-8'}{/if}</textarea>
                <small id="customer_note_help" class="form-text text-muted">
                    {l s='Use this field only for additional information that is not covered by the fields above. A withdrawal reason is not required.' mod='orderwithdrawl'}
                </small>
                {if isset($errors.customer_note) && $errors.customer_note}
                    <span id="customer_note_error" class="orderwithdrawl-field-error" role="alert">
                        {$errors.customer_note|escape:'html':'UTF-8'}
                    </span>
                {/if}
            </div>
        {/if}

        {if $productSelectionEnabled && !$productSelectionReady}
            <button type="submit"
                    name="orderwithdrawl_action"
                    value="preview_items"
                    class="btn btn-primary orderwithdrawl-btn">
                {l s='Check order' mod='orderwithdrawl'}
            </button>
        {elseif $productSelectionEnabled && $productSelectionReady}
            <button type="submit"
                    name="orderwithdrawl_action"
                    value="preview_items"
                    class="btn btn-secondary orderwithdrawl-btn orderwithdrawl-btn--secondary">
                {l s='Check order again' mod='orderwithdrawl'}
            </button>
            <button type="submit"
                    name="orderwithdrawl_action"
                    value="confirm_withdrawal"
                    class="btn btn-primary orderwithdrawl-btn">
                {l s='Confirm withdrawal' mod='orderwithdrawl'}
            </button>
        {else}
            <button type="submit" class="btn btn-primary orderwithdrawl-btn">
                {l s='Confirm withdrawal' mod='orderwithdrawl'}
            </button>
        {/if}
    </form>

    <script>
        (function () {
            var form = document.querySelector('.orderwithdrawl-form');
            if (!form) {
                return;
            }

            var scopeInputs = form.querySelectorAll('[data-orderwithdrawl-scope]');
            var partsBlock = form.querySelector('[data-orderwithdrawl-contract-parts-block]');
            var rowsContainer = form.querySelector('[data-orderwithdrawl-contract-parts]');
            var addButton = form.querySelector('[data-orderwithdrawl-add-contract-part]');

            function updateVisibility() {
                var selected = form.querySelector('[data-orderwithdrawl-scope]:checked');
                if (!partsBlock || !selected) {
                    return;
                }
                partsBlock.hidden = selected.value !== 'partial';
            }

            function updateRemoveButtons() {
                if (!rowsContainer) {
                    return;
                }
                var rows = rowsContainer.querySelectorAll('[data-orderwithdrawl-contract-part-row]');
                Array.prototype.forEach.call(rows, function (row) {
                    var removeButton = row.querySelector('[data-orderwithdrawl-remove-contract-part]');
                    if (removeButton) {
                        removeButton.hidden = rows.length <= 1;
                    }
                });
            }

            Array.prototype.forEach.call(scopeInputs, function (input) {
                input.addEventListener('change', updateVisibility);
            });

            if (addButton && rowsContainer) {
                addButton.addEventListener('click', function () {
                    var firstRow = rowsContainer.querySelector('[data-orderwithdrawl-contract-part-row]');
                    if (!firstRow) {
                        return;
                    }
                    var row = firstRow.cloneNode(true);
                    Array.prototype.forEach.call(row.querySelectorAll('input'), function (input) {
                        input.value = '';
                    });
                    rowsContainer.appendChild(row);
                    updateRemoveButtons();
                });

                rowsContainer.addEventListener('click', function (event) {
                    var target = event.target;
                    if (!target || !target.matches('[data-orderwithdrawl-remove-contract-part]')) {
                        return;
                    }
                    var row = target.closest('[data-orderwithdrawl-contract-part-row]');
                    if (row && rowsContainer.querySelectorAll('[data-orderwithdrawl-contract-part-row]').length > 1) {
                        row.parentNode.removeChild(row);
                        updateRemoveButtons();
                    }
                });
            }

            updateVisibility();
            updateRemoveButtons();
        })();
    </script>
{/block}
