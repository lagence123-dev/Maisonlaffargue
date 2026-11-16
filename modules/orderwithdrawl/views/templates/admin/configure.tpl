{**
 * Order Withdrawl Module - Admin Configuration Template
 *
 * @author    Mutayyab Usama
 * @copyright 2024
 * @license   http://www.gnu.org/licenses/gpl-3.0.html  GPL v3
 *}

<link rel="stylesheet" type="text/css" href="{$module_dir|escape:'html':'UTF-8'}views/css/admin.css?v={$module_version|escape:'url':'UTF-8'}" />
<script src="{$module_dir|escape:'html':'UTF-8'}views/js/admin.js?v={$module_version|escape:'url':'UTF-8'}" defer></script>

<div class="ow-admin">
    <header class="ow-header">
        <div class="ow-header__meta">
            <div class="ow-header__icon">
                <i class="icon icon-reply"></i>
            </div>
            <div>
                <h2>{l s='Order Withdrawal' mod='orderwithdrawl'}</h2>
                <p>{l s='Keep your storefront aligned with EU withdrawal requirements.' mod='orderwithdrawl'}</p>
            </div>
        </div>
        <div class="ow-header__cta">
            {if $module_active}
                <span class="ow-badge ow-badge--success">{l s='Module active' mod='orderwithdrawl'}</span>
            {else}
                <span class="ow-badge ow-badge--muted">{l s='Module disabled' mod='orderwithdrawl'}</span>
            {/if}
        </div>
    </header>

    {if isset($confirmation_message) && $confirmation_message}
        <div class="ow-alert ow-alert--success">
            <i class="icon icon-check-circle"></i>
            <span>{$confirmation_message|escape:'html':'UTF-8'}</span>
        </div>
    {/if}

    {if isset($error_message) && $error_message}
        <div class="ow-alert ow-alert--error">
            <i class="icon icon-exclamation-circle"></i>
            <span>{$error_message|escape:'html':'UTF-8'}</span>
        </div>
    {/if}

    <div class="ow-grid">
        <aside class="ow-panel ow-panel--secondary">
            <h3>{l s='Snapshot' mod='orderwithdrawl'}</h3>
            <div class="ow-stat">
                <div class="ow-stat__icon">
                    <i class="icon icon-cogs"></i>
                </div>
                <div>
                    <p class="ow-stat__label">{l s='Evaluation mode' mod='orderwithdrawl'}</p>
                    <p class="ow-stat__value">
                        {$withdrawl_mode_label|escape:'html':'UTF-8'}
                    </p>
                </div>
            </div>
            <div class="ow-stat">
                <div class="ow-stat__icon">
                    <i class="icon icon-search"></i>
                </div>
                <div>
                    <p class="ow-stat__label">{l s='Order matching information' mod='orderwithdrawl'}</p>
                    <p class="ow-stat__value">
                        {$order_assignment_mode_label|escape:'html':'UTF-8'}
                    </p>
                </div>
            </div>
            {if $withdrawl_mode != 'manual'}
            <div class="ow-stat">
                <div class="ow-stat__icon">
                    <i class="icon icon-calendar"></i>
                </div>
                <div>
                    <p class="ow-stat__label">
                        {$withdrawl_period_start_label|escape:'html':'UTF-8'}
                    </p>
                    <p class="ow-stat__value">
                        {$withdrawl_total_window_days|intval} <span>{l s='days' mod='orderwithdrawl'}</span>
                    </p>
                </div>
            </div>
            {/if}
            <div class="ow-stat">
                <div class="ow-stat__icon">
                    <i class="icon icon-eye"></i>
                </div>
                <div>
                    <p class="ow-stat__label">{l s='Display locations' mod='orderwithdrawl'}</p>
                    <p class="ow-stat__value">
                        {$active_location_count|intval} <span>{l s='active' mod='orderwithdrawl'}</span>
                    </p>
                </div>
            </div>
            <div class="ow-stat">
                <div class="ow-stat__icon">
                    <i class="icon icon-envelope"></i>
                </div>
                <div>
                    <p class="ow-stat__label">{l s='Notification emails' mod='orderwithdrawl'}</p>
                    <p class="ow-stat__value">
                        {if $admin_emails}
                            {$additional_email_count|intval} <span>{l s='additional' mod='orderwithdrawl'}</span>
                        {else}
                            {l s='Default only' mod='orderwithdrawl'}
                        {/if}
                    </p>
                </div>
            </div>
        </aside>

        <section class="ow-main">
            <form id="module_form" class="defaultForm ow-form" action="{$form_action|escape:'html':'UTF-8'}" method="post">
                <div class="ow-tabs" data-ow-tabs>
                    <div class="ow-tabs__nav" role="tablist" aria-label="{l s='Configuration' mod='orderwithdrawl'}">
                        <button type="button"
                                class="ow-tab is-active"
                                role="tab"
                                id="ow-tab-general-trigger"
                                aria-selected="true"
                                aria-controls="ow-tab-general"
                                tabindex="0"
                                data-ow-tab="general">
                            {l s='General Settings' mod='orderwithdrawl'}
                        </button>
                        <button type="button"
                                class="ow-tab"
                                role="tab"
                                id="ow-tab-formitems-trigger"
                                aria-selected="false"
                                aria-controls="ow-tab-formitems"
                                tabindex="-1"
                                data-ow-tab="formitems">
                            {l s='Form & item selection' mod='orderwithdrawl'}
                        </button>
                        <button type="button"
                                class="ow-tab"
                                role="tab"
                                id="ow-tab-experience-trigger"
                                aria-selected="false"
                                aria-controls="ow-tab-experience"
                                tabindex="-1"
                                data-ow-tab="experience">
                            {l s='Withdrawal Button & Display Settings' mod='orderwithdrawl'}
                        </button>
                        <button type="button"
                                class="ow-tab"
                                role="tab"
                                id="ow-tab-content-trigger"
                                aria-selected="false"
                                aria-controls="ow-tab-content"
                                tabindex="-1"
                                data-ow-tab="content">
                            {l s='Privacy & Withdrawal Settings' mod='orderwithdrawl'}
                        </button>
                        <button type="button"
                                class="ow-tab"
                                role="tab"
                                id="ow-tab-decision-trigger"
                                aria-selected="false"
                                aria-controls="ow-tab-decision"
                                tabindex="-1"
                                data-ow-tab="decision">
                            {l s='Withdrawal Decision Emails' mod='orderwithdrawl'}
                        </button>
                        <button type="button"
                                class="ow-tab"
                                role="tab"
                                id="ow-tab-mailpreview-trigger"
                                aria-selected="false"
                                aria-controls="ow-tab-mailpreview"
                                tabindex="-1"
                                data-ow-tab="mailpreview">
                            {l s='Mail Preview' mod='orderwithdrawl'}
                        </button>
                        <button type="button"
                                class="ow-tab"
                                role="tab"
                                id="ow-tab-docu-trigger"
                                aria-selected="false"
                                aria-controls="ow-tab-docu"
                                tabindex="-1"
                                data-ow-tab="docu">
                            {l s='Documentation' mod='orderwithdrawl'}
                        </button>
                    </div>

                    <div class="ow-tabs__panels">
                        <div class="ow-tab-pane is-active"
                             id="ow-tab-general"
                             role="tabpanel"
                             aria-labelledby="ow-tab-general-trigger"
                             data-ow-tab-panel="general">
                            <div class="ow-card">
                                <div class="ow-card__header">
                                    <h3>{l s='General settings' mod='orderwithdrawl'}</h3>
                                    <p>{l s='Control the legal timing and notifications.' mod='orderwithdrawl'}</p>
                                </div>
                                <div class="ow-card__content">
                                    <div class="ow-form__group">
                                        <label class="ow-label" for="ORDERWITHDRAWL_MODE">
                                            {l s='Mode' mod='orderwithdrawl'}
                                            <span class="ow-required" aria-hidden="true">*</span>
                                        </label>
                                        <select name="ORDERWITHDRAWL_MODE"
                                                id="ORDERWITHDRAWL_MODE"
                                                class="ow-input js-ow-required"
                                                required="required"
                                                aria-required="true"
                                                aria-describedby="ORDERWITHDRAWL_MODE_help">
                                            <option value="manual" {if $withdrawl_mode == 'manual'}selected="selected"{/if}>
                                                {l s='Manual review (recommended): accept the withdrawal request and decide in the Back Office' mod='orderwithdrawl'}
                                            </option>
                                            <option value="period" {if $withdrawl_mode == 'period'}selected="selected"{/if}>
                                                {l s='Automatic deadline check: X days + delivery time' mod='orderwithdrawl'}
                                            </option>
                                        </select>
                                        <span class="ow-field-error" data-ow-error-for="ORDERWITHDRAWL_MODE" role="alert" aria-live="polite"></span>
                                        <p class="ow-help" id="ORDERWITHDRAWL_MODE_help">
                                            {l s='Manual review is the recommended default setting: the customer can submit the withdrawal request, the order changes to withdrawal requested, and you decide in the Back Office.' mod='orderwithdrawl'}
                                            <br>
                                            {l s='The automatic deadline check is optional. Expired requests remain accepted for manual review unless automatic rejection is enabled below.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group js-mode-period-only">
                                        <label class="ow-label">
                                            {l s='Enable automatic review and rejection (at your own risk)' mod='orderwithdrawl'}
                                        </label>
                                        <label class="ow-checkbox">
                                            <input type="checkbox"
                                                   name="ORDERWITHDRAWL_AUTO_REJECT_EXPIRED"
                                                   id="ORDERWITHDRAWL_AUTO_REJECT_EXPIRED"
                                                   value="1"
                                                   class="ow-checkbox__control"
                                                   {if isset($automatic_rejection_enabled) && $automatic_rejection_enabled}checked="checked"{/if}>
                                            <span>{l s='Automatically reject withdrawal requests when the calculated deadline has expired.' mod='orderwithdrawl'}</span>
                                        </label>
                                        <p class="ow-help ow-risk-note">
                                            {l s='At your own risk: automatic rejection is based only on the data stored in the shop and does not replace a legal review. Please check manually if in doubt.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group">
                                        <label class="ow-label" for="ORDERWITHDRAWL_ORDER_ASSIGNMENT_MODE">
                                            {l s='Order matching information' mod='orderwithdrawl'}
                                            <span class="ow-required" aria-hidden="true">*</span>
                                        </label>
                                        <select name="ORDERWITHDRAWL_ORDER_ASSIGNMENT_MODE"
                                                id="ORDERWITHDRAWL_ORDER_ASSIGNMENT_MODE"
                                                class="ow-input js-ow-required"
                                                required="required"
                                                aria-required="true"
                                                aria-describedby="ORDERWITHDRAWL_ORDER_ASSIGNMENT_MODE_help">
                                            <option value="name_reference" {if $order_assignment_mode == 'name_reference'}selected="selected"{/if}>
                                                {l s='First and last name + order number (recommended)' mod='orderwithdrawl'}
                                            </option>
                                            <option value="name_order_id" {if $order_assignment_mode == 'name_order_id'}selected="selected"{/if}>
                                                {l s='First and last name + order ID' mod='orderwithdrawl'}
                                            </option>
                                            <option value="name_both" {if $order_assignment_mode == 'name_both'}selected="selected"{/if}>
                                                {l s='First and last name + order ID or order number' mod='orderwithdrawl'}
                                            </option>
                                            <option value="email_both" {if $order_assignment_mode == 'email_both'}selected="selected"{/if}>
                                                {l s='Email + order ID or order number (legacy method, not recommended)' mod='orderwithdrawl'}
                                            </option>
                                        </select>
                                        <span class="ow-field-error" data-ow-error-for="ORDERWITHDRAWL_ORDER_ASSIGNMENT_MODE" role="alert" aria-live="polite"></span>
                                        <p class="ow-help" id="ORDERWITHDRAWL_ORDER_ASSIGNMENT_MODE_help">
                                            {l s='Defines which information the customer must provide in the withdrawal form so the withdrawal request can be matched to an order. Order ID: the numeric PrestaShop order ID, e.g. 12345. Order number: the alphanumeric number from the order confirmation or customer account, e.g. ABCDEF123. First and last name + order number is recommended.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group js-mode-period-only">
                                        <label class="ow-label" for="ORDERWITHDRAWL_PERIOD_START_MODE">
                                            {l s='Calculation of the deadline' mod='orderwithdrawl'}
                                            <span class="ow-required" aria-hidden="true">*</span>
                                        </label>
                                        <select name="ORDERWITHDRAWL_PERIOD_START_MODE"
                                                id="ORDERWITHDRAWL_PERIOD_START_MODE"
                                                class="ow-input js-ow-required"
                                                required="required"
                                                aria-required="true">
                                            <option value="order_date" {if $withdrawl_period_start_mode == 'order_date'}selected="selected"{/if}>
                                                {l s='From the day of the order: withdrawal period + delivery time' mod='orderwithdrawl'}
                                            </option>
                                            <option value="shipping_status" {if $withdrawl_period_start_mode == 'shipping_status'}selected="selected"{/if}>
                                                {l s='From the day the status was changed to shipped: withdrawal period + delivery time' mod='orderwithdrawl'}
                                            </option>
                                        </select>
                                        <span class="ow-field-error" data-ow-error-for="ORDERWITHDRAWL_PERIOD_START_MODE" role="alert" aria-live="polite"></span>
                                        <p class="ow-help">
                                            {l s='If optional trigger statuses are selected, the first matching status in the order history is used. Otherwise, the first status marked as shipped is used.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group js-mode-period-only js-period-start-shipping-only">
                                        <label class="ow-label" for="ORDERWITHDRAWL_TRIGGER_STATE_IDS">
                                            {l s='Optional trigger statuses for shipping start' mod='orderwithdrawl'}
                                        </label>
                                        <select name="ORDERWITHDRAWL_TRIGGER_STATE_IDS[]"
                                                id="ORDERWITHDRAWL_TRIGGER_STATE_IDS"
                                                class="ow-input ow-input--multiselect"
                                                multiple="multiple"
                                                size="10">
                                            {foreach from=$available_order_states item=available_order_state}
                                                <option value="{$available_order_state.id_order_state|intval}"
                                                        {if isset($withdrawl_trigger_state_lookup[$available_order_state.id_order_state])}selected="selected"{/if}>
                                                    {$available_order_state.name|escape:'html':'UTF-8'}
                                                </option>
                                            {/foreach}
                                        </select>
                                        <p class="ow-help">
                                            {l s='Optional. If one or more statuses are selected, the withdrawal period starts with the first of these statuses in the order history. If left empty, the module uses the first status marked as shipped (delivery = 1).' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group js-mode-period-only">
                                        <label class="ow-label" for="ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS">
                                            {l s='Withdrawal period (days)' mod='orderwithdrawl'}
                                            <span class="ow-required" aria-hidden="true">*</span>
                                        </label>
                                        <input type="number"
                                               min="1"
                                               max="365"
                                               name="ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS"
                                               id="ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS"
                                               value="{$withdrawl_period_days|escape:'html':'UTF-8'}"
                                               class="ow-input js-ow-required"
                                               required="required"
                                               aria-required="true">
                                        <span class="ow-field-error" data-ow-error-for="ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS" role="alert" aria-live="polite"></span>
                                        <p class="ow-help">
                                            {l s='EU distance selling rules require a minimum of 14 days.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group js-mode-period-only">
                                        <label class="ow-label" for="ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS">
                                            {l s='Estimated delivery duration (days)' mod='orderwithdrawl'}
                                            <span class="ow-required" aria-hidden="true">*</span>
                                        </label>
                                        <input type="number"
                                               min="0"
                                               max="365"
                                               name="ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS"
                                               id="ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS"
                                               value="{$withdrawl_delivery_days|escape:'html':'UTF-8'}"
                                               class="ow-input js-ow-required"
                                               required="required"
                                               aria-required="true">
                                        <span class="ow-field-error" data-ow-error-for="ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS" role="alert" aria-live="polite"></span>
                                        <p class="ow-help">
                                            {l s='Used for the classic order date calculation and as a fallback if no shipping status exists yet.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group">
                                        <label class="ow-label">
                                            {l s='Additional admin email notifications' mod='orderwithdrawl'}
                                        </label>
                                        <input type="text"
                                               name="ORDERWITHDRAWL_ADMIN_EMAILS"
                                               id="ORDERWITHDRAWL_ADMIN_EMAILS"
                                               value="{$admin_emails|escape:'html':'UTF-8'}"
                                               class="ow-input"
                                               placeholder="admin@example.com, manager@example.com">
                                        <p class="ow-help">
                                            {l s='Add comma separated emails to notify besides the main shop address.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group">
                                        <label class="ow-label">
                                            {l s='Mail fallback' mod='orderwithdrawl'}
                                        </label>
                                        <label class="ow-checkbox">
                                            <input type="checkbox"
                                                   name="ORDERWITHDRAWL_PHP_MAIL_FALLBACK"
                                                   id="ORDERWITHDRAWL_PHP_MAIL_FALLBACK"
                                                   value="1"
                                                   class="ow-checkbox__control"
                                                   {if isset($php_mail_fallback_enabled) && $php_mail_fallback_enabled}checked="checked"{/if}>
                                            <span>{l s='Use PHP mail() as fallback if PrestaShop Mail::Send returns an error.' mod='orderwithdrawl'}</span>
                                        </label>
                                        <p class="ow-help">
                                            {l s='Recommended for compatibility with hosting environments where the native PrestaShop mail transport fails silently.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="ow-tab-pane"
                             id="ow-tab-formitems"
                             role="tabpanel"
                             aria-labelledby="ow-tab-formitems-trigger"
                             data-ow-tab-panel="formitems"
                             hidden>
                            <div class="ow-card">
                                <div class="ow-card__header">
                                    <h3>{l s='Form & item selection' mod='orderwithdrawl'}</h3>
                                    <p>{l s='Configure customer-facing form fields and selectable order items.' mod='orderwithdrawl'}</p>
                                </div>
                                <div class="ow-card__content">
                                    <div class="ow-form__group">
                                        <label class="ow-label">
                                            {l s='Customer comment field on withdrawal form' mod='orderwithdrawl'}
                                        </label>
                                        <label class="ow-checkbox">
                                            <input type="checkbox"
                                                   name="ORDERWITHDRAWL_ENABLE_CUSTOMER_NOTE"
                                                   id="ORDERWITHDRAWL_ENABLE_CUSTOMER_NOTE"
                                                   value="1"
                                                   class="ow-checkbox__control"
                                                   {if $enable_customer_note}checked="checked"{/if}>
                                            <span>{l s='Allow customers to add an optional note for additional information that is not already covered by the order or item fields.' mod='orderwithdrawl'}</span>
                                        </label>
                                        <p class="ow-help">
                                            {l s='If enabled, the note will be included in the admin notification email.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group">
                                        <label class="ow-label">
                                            {l s='Product selection from order' mod='orderwithdrawl'}
                                        </label>
                                        <label class="ow-checkbox">
                                            <input type="checkbox"
                                                   name="ORDERWITHDRAWL_PRODUCT_SELECTION_MODE"
                                                   id="ORDERWITHDRAWL_PRODUCT_SELECTION_MODE"
                                                   value="1"
                                                   class="ow-checkbox__control"
                                                   data-ow-product-selection-toggle
                                                   {if isset($product_selection_mode_enabled) && $product_selection_mode_enabled}checked="checked"{/if}>
                                            <span>{l s='After the customer details are verified, show selectable items from the order instead of manual item fields.' mod='orderwithdrawl'}</span>
                                        </label>
                                    </div>

                                    <div data-ow-product-selection-options{if !isset($product_selection_mode_enabled) || !$product_selection_mode_enabled} style="display:none"{/if}>
                                        <div class="ow-form__group" data-ow-exclusion-picker="products">
                                            <label class="ow-label" for="ORDERWITHDRAWL_EXCLUDED_PRODUCT_SEARCH">
                                                {l s='Excluded products' mod='orderwithdrawl'}
                                            </label>
                                            <div class="ow-picker">
                                                <input type="search"
                                                       id="ORDERWITHDRAWL_EXCLUDED_PRODUCT_SEARCH"
                                                       class="ow-input ow-picker__search"
                                                       placeholder="{l s='Search by product name, reference or ID' mod='orderwithdrawl'}"
                                                       autocomplete="off"
                                                       data-ow-product-search
                                                       data-endpoint="{$admin_ajax_url|escape:'html':'UTF-8'}">
                                                <div class="ow-picker__results" data-ow-product-results></div>
                                                <div class="ow-picker__chips" data-ow-product-chips>
                                                    {foreach from=$excluded_product_choices item=product}
                                                        <button type="button"
                                                                class="ow-chip"
                                                                data-ow-chip-id="{$product.id|intval}">
                                                            <span>{$product.label|escape:'html':'UTF-8'}</span>
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    {/foreach}
                                                </div>
                                            </div>
                                            <input type="hidden"
                                                   name="ORDERWITHDRAWL_EXCLUDED_PRODUCT_IDS"
                                                   id="ORDERWITHDRAWL_EXCLUDED_PRODUCT_IDS"
                                                   value="{$excluded_product_ids|escape:'html':'UTF-8'}"
                                                   data-ow-product-value>
                                            <p class="ow-help">
                                                {l s='Selected products cannot be chosen by customers for withdrawal.' mod='orderwithdrawl'}
                                            </p>
                                        </div>

                                        <div class="ow-form__group" data-ow-exclusion-picker="categories">
                                            <label class="ow-label" for="ORDERWITHDRAWL_EXCLUDED_CATEGORY_SEARCH">
                                                {l s='Excluded categories' mod='orderwithdrawl'}
                                            </label>
                                            <input type="search"
                                                   id="ORDERWITHDRAWL_EXCLUDED_CATEGORY_SEARCH"
                                                   class="ow-input ow-category-tree__filter"
                                                   placeholder="{l s='Search categories' mod='orderwithdrawl'}"
                                                   autocomplete="off"
                                                   data-ow-category-filter>
                                            <div class="ow-category-tree" data-ow-category-tree>
                                                {foreach from=$category_exclusion_tree item=category}
                                                    <label class="ow-category-tree__item" style="--ow-level: {$category.level|intval}">
                                                        <input type="checkbox"
                                                               value="{$category.id|intval}"
                                                               data-ow-category-checkbox
                                                               {if $category.selected}checked="checked"{/if}>
                                                        <span>{$category.label|escape:'html':'UTF-8'}</span>
                                                    </label>
                                                {/foreach}
                                            </div>
                                            <input type="hidden"
                                                   name="ORDERWITHDRAWL_EXCLUDED_CATEGORY_IDS"
                                                   id="ORDERWITHDRAWL_EXCLUDED_CATEGORY_IDS"
                                                   value="{$excluded_category_ids|escape:'html':'UTF-8'}"
                                                   data-ow-category-value>
                                            <p class="ow-help">
                                                {l s='Products assigned to selected categories cannot be chosen by customers for withdrawal.' mod='orderwithdrawl'}
                                            </p>
                                        </div>

                                        <details class="ow-advanced">
                                            <summary>{l s='Edit IDs manually' mod='orderwithdrawl'}</summary>
                                            <p class="ow-help">
                                                {l s='Use this only if a product or category cannot be selected through the picker.' mod='orderwithdrawl'}
                                            </p>
                                            <div class="ow-advanced__grid">
                                                <label>
                                                    <span>{l s='Product IDs' mod='orderwithdrawl'}</span>
                                                    <input type="text"
                                                           class="ow-input"
                                                           value="{$excluded_product_ids|escape:'html':'UTF-8'}"
                                                           data-ow-product-manual>
                                                </label>
                                                <label>
                                                    <span>{l s='Category IDs' mod='orderwithdrawl'}</span>
                                                    <input type="text"
                                                           class="ow-input"
                                                           value="{$excluded_category_ids|escape:'html':'UTF-8'}"
                                                           data-ow-category-manual>
                                                </label>
                                            </div>
                                        </details>

                                        <div class="ow-form__group">
                                            <label class="ow-label">
                                                {l s='Customized products' mod='orderwithdrawl'}
                                            </label>
                                            <label class="ow-checkbox">
                                                <input type="checkbox"
                                                       name="ORDERWITHDRAWL_EXCLUDE_CUSTOMIZED_PRODUCTS"
                                                       id="ORDERWITHDRAWL_EXCLUDE_CUSTOMIZED_PRODUCTS"
                                                       value="1"
                                                       class="ow-checkbox__control"
                                                       {if isset($exclude_customized_products_enabled) && $exclude_customized_products_enabled}checked="checked"{/if}>
                                                <span>{l s='Automatically exclude products with PrestaShop customizations from withdrawal selection.' mod='orderwithdrawl'}</span>
                                            </label>
                                            <p class="ow-help">
                                                {l s='Use this for personalized products when PrestaShop stores the customization on the order item.' mod='orderwithdrawl'}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ow-tab-pane"
                             id="ow-tab-experience"
                             role="tabpanel"
                             aria-labelledby="ow-tab-experience-trigger"
                             data-ow-tab-panel="experience"
                             hidden>
                            <div class="ow-card">
                                <div class="ow-card__header">
                                    <h3>{l s='Customer experience' mod='orderwithdrawl'}</h3>
                                    <p>{l s='Adjust wording and presentation of the withdrawal CTA.' mod='orderwithdrawl'}</p>
                                </div>
                                <div class="ow-card__content">
                                    <div class="ow-form__group">
                                        <label class="ow-label">
                                            {l s='Show withdrawal action in' mod='orderwithdrawl'}
                                            <span class="ow-required">*</span>
                                        </label>
                                        <div class="ow-option-list">
                                            <label class="ow-checkbox">
                                                <input type="checkbox"
                                                       name="ORDERWITHDRAWL_DISPLAY_LOCATION[]"
                                                       value="account_page"
                                                       class="ow-checkbox__control"
                                                       {if $display_location_show_account_page}checked="checked"{/if}>
                                                <span>{l s='Customer account page card' mod='orderwithdrawl'}</span>
                                            </label>
                                            <label class="ow-checkbox">
                                                <input type="checkbox"
                                                       name="ORDERWITHDRAWL_DISPLAY_LOCATION[]"
                                                       value="account_block"
                                                       class="ow-checkbox__control"
                                                       {if $display_location_show_account_block}checked="checked"{/if}>
                                                <span>{l s='My Account footer menu link' mod='orderwithdrawl'}</span>
                                            </label>
                                            <label class="ow-checkbox">
                                                <input type="checkbox"
                                                       name="ORDERWITHDRAWL_DISPLAY_LOCATION[]"
                                                       value="footer"
                                                       class="ow-checkbox__control js-footer-checkbox"
                                                       {if $display_location_show_footer}checked="checked"{/if}>
                                                <span>{l s='Footer button (recommended)' mod='orderwithdrawl'}</span>
                                            </label>
                                            <label class="ow-checkbox">
                                                <input type="checkbox"
                                                       name="ORDERWITHDRAWL_DISPLAY_LOCATION[]"
                                                       value="custom_hook"
                                                       class="ow-checkbox__control"
                                                       {if isset($display_location_show_custom_hook) && $display_location_show_custom_hook}checked="checked"{/if}>
                                                <span>{l s='Custom hook displayOrderWithdrawlCta' mod='orderwithdrawl'}</span>
                                            </label>
                                        </div>
                                        <p class="ow-help">
                                            {l s='Select at least one location to keep the withdrawal action visible for customers.' mod='orderwithdrawl'}
                                        </p>
                                        <p class="ow-help">
                                            {l s='Enable this option to place the withdrawal CTA manually in your theme.' mod='orderwithdrawl'}
                                            <code>{literal}{hook h='displayOrderWithdrawlCta'}{/literal}</code>
                                        </p>
                                    </div>

                                    <div class="ow-form__group">
                                        <label class="ow-label">
                                            {l s='Withdrawal action label' mod='orderwithdrawl'}
                                            <span class="ow-required">*</span>
                                        </label>
                                        <div class="ow-lang-tabs" data-ow-lang-tabs>
                                            <div class="ow-lang-tabs__nav">
                                                {foreach from=$languages item=language}
                                                    <button type="button"
                                                            class="ow-lang-tab {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                            data-ow-lang-tab="{$language.id_lang|intval}">
                                                        {$language.iso_code|escape:'html':'UTF-8'}
                                                    </button>
                                                {/foreach}
                                            </div>
                                            <div class="ow-lang-tabs__content">
                                                {foreach from=$languages item=language}
                                                    <div class="ow-lang-pane {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                         data-ow-lang-pane="{$language.id_lang|intval}">
                                                        <input type="text"
                                                               name="ORDERWITHDRAWL_BUTTON_LABEL[{$language.id_lang|intval}]"
                                                               value="{$button_label_lang[$language.id_lang]|escape:'html':'UTF-8'}"
                                                               placeholder="{$button_label_placeholder[$language.id_lang]|escape:'html':'UTF-8'}"
                                                               class="ow-input"
                                                               maxlength="255"
                                                               {if $language.id_lang == $id_lang_default}required="required"{/if}>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </div>
                                        <p class="ow-help">
                                            {l s='Appears wherever the withdrawal CTA is displayed.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group js-footer-style-group"{if !$display_location_show_footer} style="display:none"{/if}>
                                        <label class="ow-label">
                                            {l s='Footer display style' mod='orderwithdrawl'}
                                            <span class="ow-required">*</span>
                                        </label>
                                        <select name="ORDERWITHDRAWL_FOOTER_DISPLAY"
                                                id="ORDERWITHDRAWL_FOOTER_DISPLAY"
                                                class="ow-input"
                                                required="required">
                                            <option value="button" {if $footer_display_mode == 'button'}selected="selected"{/if}>
                                                {l s='Prominent button (recommended)' mod='orderwithdrawl'}
                                            </option>
                                            <option value="link" {if $footer_display_mode == 'link'}selected="selected"{/if}>
                                                {l s='Simple text link' mod='orderwithdrawl'}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="ow-form__group js-footer-style-group js-footer-color-group"{if !$display_location_show_footer} style="display:none"{/if}>
                                        <label class="ow-label">
                                            {l s='Button color' mod='orderwithdrawl'}
                                            <span class="ow-required">*</span>
                                        </label>
                                        <div class="ow-color-picker">
                                            <input type="text"
                                                   name="ORDERWITHDRAWL_BUTTON_COLOR"
                                                   id="ORDERWITHDRAWL_BUTTON_COLOR"
                                                   value="{$button_color|escape:'html':'UTF-8'}"
                                                   class="ow-input ow-input--color-text"
                                                   maxlength="7"
                                                   placeholder="#F44708"
                                                   data-invalid-message="{l s='Please use a valid hex color (e.g. #F1B218).' mod='orderwithdrawl'}">
                                            <input type="color"
                                                   id="ORDERWITHDRAWL_BUTTON_COLOR_PICKER"
                                                   value="{$button_color|escape:'html':'UTF-8'}"
                                                   class="ow-input ow-input--color">
                                        </div>
                                        <p class="ow-help">
                                            {l s='Primary tone used for the footer button background.' mod='orderwithdrawl'}
                                        </p>
                                    </div>
                                <div class="ow-form__group js-footer-style-group js-footer-color-group"{if !$display_location_show_footer} style="display:none"{/if}>
                                    <label class="ow-label">
                                        {l s='Button font color' mod='orderwithdrawl'}
                                    </label>
                                    <div class="ow-color-picker">
                                        <input type="text"
                                               name="ORDERWITHDRAWL_BUTTON_FONT_COLOR"
                                               id="ORDERWITHDRAWL_BUTTON_FONT_COLOR"
                                               value="{$button_font_color|escape:'html':'UTF-8'}"
                                               class="ow-input ow-input--color-text"
                                               maxlength="7"
                                               placeholder="#FFFFFF">
                                        <input type="color"
                                               id="ORDERWITHDRAWL_BUTTON_FONT_COLOR_PICKER"
                                               value="{$button_font_color|escape:'html':'UTF-8'}"
                                               class="ow-input ow-input--color">
                                </div>
                                    <p class="ow-help">
                                        {l s='Optional. Text color for the footer button label (hex value, e.g. #FFFFFF).' mod='orderwithdrawl'}
                                    </p>
                                </div>

                                    <div class="ow-form__group js-footer-style-group js-footer-button-only"{if !$display_location_show_footer || $footer_display_mode != 'button'} style="display:none"{/if}>
                                        <label class="ow-label">
                                            {l s='Button size' mod='orderwithdrawl'}
                                        </label>
                                        <select name="ORDERWITHDRAWL_BUTTON_SIZE"
                                                id="ORDERWITHDRAWL_BUTTON_SIZE"
                                                class="ow-input">
                                            <option value="small" {if $button_size == 'small'}selected="selected"{/if}>
                                                {l s='Small' mod='orderwithdrawl'}
                                            </option>
                                            <option value="medium" {if $button_size == 'medium'}selected="selected"{/if}>
                                                {l s='Medium (default)' mod='orderwithdrawl'}
                                            </option>
                                            <option value="large" {if $button_size == 'large'}selected="selected"{/if}>
                                                {l s='Large' mod='orderwithdrawl'}
                                            </option>
                                        </select>
                                        <p class="ow-help">
                                            {l s='Adjust the visual prominence of the footer button.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group js-footer-style-group js-footer-button-only"{if !$display_location_show_footer || $footer_display_mode != 'button'} style="display:none"{/if}>
                                        <label class="ow-label">
                                            {l s='Button alignment' mod='orderwithdrawl'}
                                        </label>
                                        <select name="ORDERWITHDRAWL_BUTTON_ALIGN"
                                                id="ORDERWITHDRAWL_BUTTON_ALIGN"
                                                class="ow-input">
                                            <option value="left" {if $button_align == 'left'}selected="selected"{/if}>
                                                {l s='Left aligned' mod='orderwithdrawl'}
                                            </option>
                                            <option value="center" {if $button_align == 'center'}selected="selected"{/if}>
                                                {l s='Centered' mod='orderwithdrawl'}
                                            </option>
                                            <option value="right" {if $button_align == 'right'}selected="selected"{/if}>
                                                {l s='Right aligned' mod='orderwithdrawl'}
                                            </option>
                                        </select>
                                        <p class="ow-help">
                                            {l s='Control where the footer button is placed within the footer area.' mod='orderwithdrawl'}
                                        </p>
                                    </div>



                                    <div class="ow-form__group js-footer-style-group js-footer-link-color-group"{if !$display_location_show_footer || $footer_display_mode != 'link'} style="display:none"{/if}>
                                        <label class="ow-label">
                                            {l s='Link text color (simple link mode)' mod='orderwithdrawl'}
                                        </label>
                                        <div class="ow-color-picker">
                                            <input type="text"
                                                   name="ORDERWITHDRAWL_LINK_COLOR"
                                                   id="ORDERWITHDRAWL_LINK_COLOR"
                                                   value="{$link_color|escape:'html':'UTF-8'}"
                                                   class="ow-input ow-input--color-text"
                                                   maxlength="7"
                                                   placeholder="#2563EB">
                                            <input type="color"
                                                   id="ORDERWITHDRAWL_LINK_COLOR_PICKER"
                                                   value="{$link_color|escape:'html':'UTF-8'}"
                                                   class="ow-input ow-input--color">
                                        </div>
                                        <p class="ow-help">
                                            {l s='Optional. Text color for the footer withdrawal link when using the simple link display.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group js-footer-style-group"{if !$display_location_show_footer} style="display:none"{/if}>
                                        <label class="ow-label">
                                            {l s='Bootstrap button classes (footer)' mod='orderwithdrawl'}
                                        </label>
                                        <input type="text"
                                               name="ORDERWITHDRAWL_BUTTON_CLASSES"
                                               id="ORDERWITHDRAWL_BUTTON_CLASSES"
                                               value="{$button_classes|escape:'html':'UTF-8'}"
                                               class="ow-input">
                                        <p class="ow-help">
                                            {l s='Optional. These CSS classes will be added to the footer withdrawal CTA (e.g. btn btn-primary). Intended for advanced styling.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group js-footer-style-group"{if !$display_location_show_footer} style="display:none"{/if}>
                                        <label class="ow-label">
                                            {l s='Footer hook position' mod='orderwithdrawl'}
                                            <span class="ow-required">*</span>
                                        </label>
                                        <div class="ow-option-list">
                                            {if !$is_ps_9}
                                            <label class="ow-checkbox">
                                                <input type="checkbox"
                                                       name="ORDERWITHDRAWL_FOOTER_HOOKS[]"
                                                       value="displayFooterBefore"
                                                       class="ow-checkbox__control"
                                                       {if $footer_hook_displayFooterBefore}checked="checked"{/if}>
                                                <span>{l s='displayFooterBefore (top of footer)' mod='orderwithdrawl'}</span>
                                            </label>
                                            {/if}
                                            <label class="ow-checkbox">
                                                <input type="checkbox"
                                                       name="ORDERWITHDRAWL_FOOTER_HOOKS[]"
                                                       value="displayFooter"
                                                       class="ow-checkbox__control"
                                                       {if $footer_hook_displayFooter}checked="checked"{/if}>
                                                <span>{l s='displayFooter (standard footer area)' mod='orderwithdrawl'}</span>
                                            </label>
                                            <label class="ow-checkbox">
                                                <input type="checkbox"
                                                       name="ORDERWITHDRAWL_FOOTER_HOOKS[]"
                                                       value="displayBeforeBodyClosingTag"
                                                       class="ow-checkbox__control"
                                                       {if $footer_hook_displayBeforeBodyClosingTag}checked="checked"{/if}>
                                                <span>{l s='displayBeforeBodyClosingTag (just before &lt;/body&gt;)' mod='orderwithdrawl'}</span>
                                            </label>
                                            {if !$is_ps_9}
                                            <label class="ow-checkbox">
                                                <input type="checkbox"
                                                       name="ORDERWITHDRAWL_FOOTER_HOOKS[]"
                                                       value="displayFooterAfter"
                                                       class="ow-checkbox__control"
                                                       {if $footer_hook_displayFooterAfter}checked="checked"{/if}>
                                                <span>{l s='displayFooterAfter (below footer)' mod='orderwithdrawl'}</span>
                                            </label>
                                            {/if}
                                        </div>
                                        <p class="ow-help">
                                            {if $is_ps_9}
                                                {l s='In PrestaShop 9+, use displayFooter for standard placement or displayBeforeBodyClosingTag for overlay/floating elements.' mod='orderwithdrawl'}
                                            {else}
                                                {l s='Select one or more hooks where the footer withdrawal CTA should be injected.' mod='orderwithdrawl'}
                                            {/if}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ow-tab-pane"
                             id="ow-tab-content"
                             role="tabpanel"
                             aria-labelledby="ow-tab-content-trigger"
                             data-ow-tab-panel="content"
                             hidden>
                            <div class="ow-card">
                                <div class="ow-card__header">
                                    <h3>{l s='Privacy settings' mod='orderwithdrawl'}</h3>
                                    <p>{l s='Manage the privacy notice and link to your data protection page used in the withdrawal form.' mod='orderwithdrawl'}</p>
                                </div>
                                <div class="ow-card__content">
                                    <div class="ow-form__group">
                                        <label class="ow-label">
                                            {l s='Privacy page' mod='orderwithdrawl'}
                                        </label>
                                        <div class="ow-lang-tabs" data-ow-lang-tabs>
                                            <div class="ow-lang-tabs__nav">
                                            {foreach from=$languages item=language}
                                                    <button type="button"
                                                            class="ow-lang-tab {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                            data-ow-lang-tab="{$language.id_lang|intval}">
                                                        {$language.iso_code|escape:'html':'UTF-8'}
                                                    </button>
                                                {/foreach}
                                            </div>
                                            <div class="ow-lang-tabs__content">
                                                {foreach from=$languages item=language}
                                                    <div class="ow-lang-pane {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                         data-ow-lang-pane="{$language.id_lang|intval}">
                                                    <select name="ORDERWITHDRAWL_PRIVACY_PAGE[{$language.id_lang|intval}]"
                                                            class="ow-input">
                                                        <option value="0">
                                                            {l s='-- Select a CMS page --' mod='orderwithdrawl'}
                                                        </option>
                                                        {if isset($privacy_cms_pages[$language.id_lang])}
                                                            {foreach from=$privacy_cms_pages[$language.id_lang] item=privacyPage}
                                                                <option value="{$privacyPage.id_cms|intval}"
                                                                    {if isset($privacy_page_lang[$language.id_lang]) && $privacy_page_lang[$language.id_lang] == $privacyPage.id_cms}selected="selected"{/if}>
                                                                    {$privacyPage.meta_title|escape:'html':'UTF-8'}
                                                                </option>
                                                            {/foreach}
                                                        {/if}
                                                    </select>
                                                </div>
                                            {/foreach}
                                            </div>
                                        </div>
                                        <p class="ow-help">
                                            {l s='Link the withdrawal form to your existing privacy policy page.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group">
                                        <label class="ow-label">
                                            {l s='Additional privacy note' mod='orderwithdrawl'}
                                        </label>
                                        <div class="ow-lang-tabs" data-ow-lang-tabs>
                                            <div class="ow-lang-tabs__nav">
                                            {foreach from=$languages item=language}
                                                    <button type="button"
                                                            class="ow-lang-tab {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                            data-ow-lang-tab="{$language.id_lang|intval}">
                                                        {$language.iso_code|escape:'html':'UTF-8'}
                                                    </button>
                                                {/foreach}
                                            </div>
                                            <div class="ow-lang-tabs__content">
                                                {foreach from=$languages item=language}
                                                    <div class="ow-lang-pane {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                         data-ow-lang-pane="{$language.id_lang|intval}">
                                                    <textarea name="ORDERWITHDRAWL_PRIVACY_NOTE[{$language.id_lang|intval}]"
                                                              class="ow-input ow-textarea"
                                                              rows="6"
                                                              placeholder="{$privacy_note_placeholder[$language.id_lang]|escape:'html':'UTF-8'}">{$privacy_note_lang[$language.id_lang]|escape:'html':'UTF-8'}</textarea>
                                                </div>
                                            {/foreach}
                                            </div>
                                        </div>
                                        <p class="ow-help">
                                            {l s='Optional text shown above the privacy link on the withdrawal form.' mod='orderwithdrawl'}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="ow-card">
                                <div class="ow-card__header">
                                    <h3>{l s='Withdrawal policy settings' mod='orderwithdrawl'}</h3>
                                    <p>{l s='Manage the withdrawal instruction that you place on your legal notice page.' mod='orderwithdrawl'}</p>
                                </div>
                                <div class="ow-card__content">
                                    <div class="ow-form__group">
                                        <label class="ow-label">
                                            {l s='Withdrawal policy page' mod='orderwithdrawl'}
                                        </label>
                                        <div class="ow-lang-tabs" data-ow-lang-tabs>
                                            <div class="ow-lang-tabs__nav">
                                            {foreach from=$languages item=language}
                                                    <button type="button"
                                                            class="ow-lang-tab {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                            data-ow-lang-tab="{$language.id_lang|intval}">
                                                        {$language.iso_code|escape:'html':'UTF-8'}
                                                    </button>
                                                {/foreach}
                                            </div>
                                            <div class="ow-lang-tabs__content">
                                                {foreach from=$languages item=language}
                                                    <div class="ow-lang-pane {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                         data-ow-lang-pane="{$language.id_lang|intval}">
                                                    <select name="ORDERWITHDRAWL_WITHDRAWAL_PAGE[{$language.id_lang|intval}]"
                                                            class="ow-input">
                                                        <option value="0">
                                                            {l s='-- Select a CMS page --' mod='orderwithdrawl'}
                                                        </option>
                                                        {if isset($privacy_cms_pages[$language.id_lang])}
                                                            {foreach from=$privacy_cms_pages[$language.id_lang] item=withdrawalPage}
                                                                <option value="{$withdrawalPage.id_cms|intval}"
                                                                    {if isset($withdrawal_page_lang[$language.id_lang]) && $withdrawal_page_lang[$language.id_lang] == $withdrawalPage.id_cms}selected="selected"{/if}>
                                                                    {$withdrawalPage.meta_title|escape:'html':'UTF-8'}
                                                                </option>
                                                            {/foreach}
                                                        {/if}
                                                    </select>
                                                </div>
                                            {/foreach}
                                            </div>
                                        </div>
                                        <p class="ow-help">
                                            {l s='Select the CMS page that contains your legal withdrawal information.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group">
                                        <label class="ow-label">
                                            {l s='Withdrawal policy text' mod='orderwithdrawl'}
                                        </label>
                                        <div class="ow-lang-tabs" data-ow-lang-tabs>
                                            <div class="ow-lang-tabs__nav">
                                            {foreach from=$languages item=language}
                                                    <button type="button"
                                                            class="ow-lang-tab {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                            data-ow-lang-tab="{$language.id_lang|intval}">
                                                        {$language.iso_code|escape:'html':'UTF-8'}
                                                    </button>
                                                {/foreach}
                                            </div>
                                            <div class="ow-lang-tabs__content">
                                                {foreach from=$languages item=language}
                                                    <div class="ow-lang-pane {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                         data-ow-lang-pane="{$language.id_lang|intval}">
                                                    <textarea name="ORDERWITHDRAWL_WITHDRAWAL_NOTE[{$language.id_lang|intval}]"
                                                              class="ow-input ow-textarea"
                                                              rows="6"
                                                              placeholder="{$withdrawal_note_placeholder[$language.id_lang]|escape:'html':'UTF-8'}">{$withdrawal_note_lang[$language.id_lang]|escape:'html':'UTF-8'}</textarea>
                                                </div>
                                            {/foreach}
                                            </div>
                                        </div>
                                        <p class="ow-help">
                                            {l s='Shown on the selected CMS page via the CMS dispute hook. Adjust the wording to match your legal requirements.' mod='orderwithdrawl'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ow-tab-pane"
                             id="ow-tab-mailpreview"
                             role="tabpanel"
                             aria-labelledby="ow-tab-mailpreview-trigger"
                             data-ow-tab-panel="mailpreview"
                             hidden>
                            <div class="ow-card">
                                <div class="ow-card__header">
                                    <h3>{l s='Mail template preview and test send' mod='orderwithdrawl'}</h3>
                                    <p>{l s='Review the rendered HTML of every withdrawal email template and optionally send a sample mail to the shop address before your customers see it.' mod='orderwithdrawl'}</p>
                                </div>
                                <div class="ow-card__content ow-mail-preview" data-ow-mail-preview>
                                    <div class="ow-mail-preview__controls">
                                        <div class="ow-form__group">
                                            <label class="ow-label" for="ow-mail-preview-template">
                                                {l s='Template' mod='orderwithdrawl'}
                                            </label>
                                            <select id="ow-mail-preview-template" class="ow-input" data-ow-mail-preview-template>
                                                {foreach from=$mail_preview_templates item=tpl}
                                                    <option value="{$tpl|escape:'html':'UTF-8'}">{$tpl|escape:'html':'UTF-8'}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <div class="ow-form__group">
                                            <label class="ow-label" for="ow-mail-preview-language">
                                                {l s='Language' mod='orderwithdrawl'}
                                            </label>
                                            <select id="ow-mail-preview-language" class="ow-input" data-ow-mail-preview-language>
                                                {foreach from=$mail_preview_languages item=mailLang}
                                                    <option value="{$mailLang.iso_code|escape:'html':'UTF-8'}">
                                                        {$mailLang.iso_code|upper|escape:'html':'UTF-8'} &mdash; {$mailLang.name|escape:'html':'UTF-8'}
                                                    </option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <div class="ow-form__group">
                                            <label class="ow-label">&nbsp;</label>
                                            <div>
                                                <button type="button" class="ow-button ow-button--secondary" data-ow-mail-preview-refresh>
                                                    <i class="icon icon-refresh"></i>
                                                    {l s='Reload preview' mod='orderwithdrawl'}
                                                </button>
                                                <button type="button" class="ow-button" data-ow-mail-preview-send>
                                                    <i class="icon icon-envelope"></i>
                                                    {l s='Send test mail' mod='orderwithdrawl'}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="ow-help">
                                        {l s='Test mails are sent to the shop email address configured in PrestaShop. Placeholders in the preview are substituted with neutral sample data.' mod='orderwithdrawl'}
                                        {if $ps_shop_email}
                                            <br>
                                            <strong>{l s='Current shop email:' mod='orderwithdrawl'}</strong> {$ps_shop_email|escape:'html':'UTF-8'}
                                        {/if}
                                    </p>
                                    <div class="ow-mail-preview__status" data-ow-mail-preview-status aria-live="polite"></div>
                                    <iframe class="ow-mail-preview__iframe"
                                            data-ow-mail-preview-frame
                                            sandbox
                                            title="{l s='Mail preview' mod='orderwithdrawl'}"
                                            src="about:blank">
                                    </iframe>
                                    <input type="hidden"
                                           data-ow-mail-preview-endpoint
                                           value="{$mail_preview_endpoint_base|escape:'html':'UTF-8'}">
                                </div>
                            </div>
                        </div>

                        <div class="ow-tab-pane"
                             id="ow-tab-docu"
                             role="tabpanel"
                             aria-labelledby="ow-tab-docu-trigger"
                             data-ow-tab-panel="docu"
                             hidden>
                            <div class="ow-card">
                                <div class="ow-card__header">
                                    <h3>{l s='Documentation' mod='orderwithdrawl'}</h3>
                                    <p>{l s='Download the module documentation in your preferred language.' mod='orderwithdrawl'}</p>
                                </div>
                                <div class="ow-card__content">
                                    <div class="ow-form__group">
                                        <div class="ow-docu-grid">
                                            {assign var=documentation_languages value=['de'=>'Deutsch','en'=>'English','fr'=>'Français','it'=>'Italiano','es'=>'Español','pl'=>'Polski','ro'=>'Română','nl'=>'Nederlands']}
                                            {assign var=documentation_links value=['de'=>'https://getapps4shops.com/?controller=attachment&id_attachment=1','en'=>'https://getapps4shops.com/?controller=attachment&id_attachment=5','fr'=>'https://getapps4shops.com/?controller=attachment&id_attachment=6','it'=>'https://getapps4shops.com/?controller=attachment&id_attachment=7','es'=>'https://getapps4shops.com/?controller=attachment&id_attachment=8','pl'=>'https://getapps4shops.com/?controller=attachment&id_attachment=9','ro'=>'https://getapps4shops.com/?controller=attachment&id_attachment=10','nl'=>'https://getapps4shops.com/?controller=attachment&id_attachment=11']}
                                            {foreach from=$documentation_languages key=lang_code item=lang_name}
                                                <div class="ow-docu-item">
                                                    <div class="ow-docu-item__icon">
                                                        <i class="icon icon-file-pdf-o"></i>
                                                    </div>
                                                    <div class="ow-docu-item__content">
                                                        <h4 class="ow-docu-item__title">{$lang_name|escape:'html':'UTF-8'}</h4>
                                                    </div>
                                                    <div class="ow-docu-item__action">
                                                        <a href="{$documentation_links[$lang_code]|escape:'html':'UTF-8'}"
                                                           class="ow-button ow-button--secondary" 
                                                           target="_blank" 
                                                           rel="noopener">
                                                            <i class="icon icon-download"></i>
                                                            PDF
                                                        </a>
                                                    </div>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="ow-tab-pane"
                             id="ow-tab-decision"
                             role="tabpanel"
                             aria-labelledby="ow-tab-decision-trigger"
                             data-ow-tab-panel="decision"
                             hidden>
                            <div class="ow-card">
                                <div class="ow-card__header">
                                    <h3>{l s='Withdrawal decision communication' mod='orderwithdrawl'}</h3>
                                    <p>{l s='Configure the texts used in approval and rejection emails when you change the withdrawal status in the order.' mod='orderwithdrawl'}</p>
                                </div>
                                <div class="ow-card__content">
                                    <div class="ow-form__group">
                                        <label class="ow-label">
                                            {l s='Approved status message' mod='orderwithdrawl'}
                                        </label>
                                        <div class="ow-lang-tabs" data-ow-lang-tabs>
                                            <div class="ow-lang-tabs__nav">
                                                {foreach from=$languages item=language}
                                                    <button type="button"
                                                            class="ow-lang-tab {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                            data-ow-lang-tab="{$language.id_lang|intval}">
                                                        {$language.iso_code|escape:'html':'UTF-8'}
                                                    </button>
                                                {/foreach}
                                            </div>
                                            <div class="ow-lang-tabs__content">
                                                {foreach from=$languages item=language}
                                                    <div class="ow-lang-pane {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                         data-ow-lang-pane="{$language.id_lang|intval}">
                                                        <textarea name="ORDERWITHDRAWL_DECISION_APPROVED[{$language.id_lang|intval}]"
                                                                  class="ow-input ow-textarea"
                                                                  rows="6"
                                                                  placeholder="{$decision_approved_placeholder[$language.id_lang]|escape:'html':'UTF-8'}">{$decision_approved_lang[$language.id_lang]|escape:'html':'UTF-8'}</textarea>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </div>
                                        <p class="ow-help">
                                            {l s='Optional text appended to the email when the withdrawal request is approved. It will be inserted as {status_message} in the template.' mod='orderwithdrawl'}
                                        </p>
                                    </div>

                                    <div class="ow-form__group">
                                        <label class="ow-label">
                                            {l s='Rejected status message' mod='orderwithdrawl'}
                                        </label>
                                        <div class="ow-lang-tabs" data-ow-lang-tabs>
                                            <div class="ow-lang-tabs__nav">
                                                {foreach from=$languages item=language}
                                                    <button type="button"
                                                            class="ow-lang-tab {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                            data-ow-lang-tab="{$language.id_lang|intval}">
                                                        {$language.iso_code|escape:'html':'UTF-8'}
                                                    </button>
                                                {/foreach}
                                            </div>
                                            <div class="ow-lang-tabs__content">
                                                {foreach from=$languages item=language}
                                                    <div class="ow-lang-pane {if $language.id_lang == $id_lang_default}is-active{/if}"
                                                         data-ow-lang-pane="{$language.id_lang|intval}">
                                                        <textarea name="ORDERWITHDRAWL_DECISION_REJECTED[{$language.id_lang|intval}]"
                                                                  class="ow-input ow-textarea"
                                                                  rows="6"
                                                                  placeholder="{$decision_rejected_placeholder[$language.id_lang]|escape:'html':'UTF-8'}">{$decision_rejected_lang[$language.id_lang]|escape:'html':'UTF-8'}</textarea>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </div>
                                        <p class="ow-help">
                                            {l s='Optional text appended to the email when the withdrawal request is rejected. It will be inserted as {status_message} in the template.' mod='orderwithdrawl'}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ow-form__actions ow-form__actions--split">
                    <button type="button"
                            class="ow-button ow-button--danger"
                            data-ow-reset-trigger
                            data-confirm-message="{l s='Reset all module settings to the installer defaults? Customer notes and configured texts will be replaced.' mod='orderwithdrawl'}">
                        <i class="icon icon-undo"></i>
                        {l s='Reset to defaults' mod='orderwithdrawl'}
                    </button>
                    <button type="submit" value="1" name="submitOrderWithdrawlSettings" class="ow-button">
                        <i class="process-icon-save"></i>
                        {l s='Save settings' mod='orderwithdrawl'}
                    </button>
                </div>
            </form>

            {* UX-A-L1: hidden form posted by the reset confirmation modal.
               Kept outside the main settings form so submitting the reset
               never accidentally persists pending edits. *}
            <form id="orderwithdrawl-reset-form"
                  method="post"
                  action="{$form_action|escape:'html':'UTF-8'}"
                  style="display:none">
                <input type="hidden" name="submitOrderWithdrawlReset" value="1">
                <input type="hidden" name="orderwithdrawl_reset_confirm" value="yes">
            </form>
        </section>
    </div>
</div>

{literal}
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var tabsContainer = document.querySelector('[data-ow-tabs]');
    if (tabsContainer) {
      var tabButtons = tabsContainer.querySelectorAll('[data-ow-tab]');
      var tabPanels = tabsContainer.querySelectorAll('[data-ow-tab-panel]');
      var tabButtonsArray = Array.prototype.slice.call(tabButtons);

      var activateTab = function (targetId) {
        if (!targetId) {
          return;
        }
        tabButtonsArray.forEach(function (button) {
          var isActive = button.dataset.owTab === targetId;
          button.classList.toggle('is-active', isActive);
          button.setAttribute('aria-selected', isActive ? 'true' : 'false');
          button.setAttribute('tabindex', isActive ? '0' : '-1');
        });
        tabPanels.forEach(function (panel) {
          var isActive = panel.dataset.owTabPanel === targetId;
          panel.classList.toggle('is-active', isActive);
          if (isActive) {
            panel.removeAttribute('hidden');
          } else {
            panel.setAttribute('hidden', '');
          }
        });
      };

      var initialButton = tabsContainer.querySelector('.ow-tab.is-active') || tabButtonsArray[0];
      if (initialButton) {
        activateTab(initialButton.dataset.owTab);
      }

      tabButtonsArray.forEach(function (button, index) {
        button.addEventListener('click', function () {
          activateTab(button.dataset.owTab);
          button.focus();
        });
        button.addEventListener('keydown', function (event) {
          if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') {
            return;
          }
          event.preventDefault();
          var delta = event.key === 'ArrowRight' ? 1 : -1;
          var nextIndex = (index + delta + tabButtonsArray.length) % tabButtonsArray.length;
          var nextButton = tabButtonsArray[nextIndex];
          if (nextButton) {
            activateTab(nextButton.dataset.owTab);
            nextButton.focus();
          }
        });
      });
    }

    var locationCheckboxes = document.querySelectorAll('input[name="ORDERWITHDRAWL_DISPLAY_LOCATION[]"]');
    var footerCheckbox = document.querySelector('input[name="ORDERWITHDRAWL_DISPLAY_LOCATION[]"][value="footer"]');
    var footerGroups = document.querySelectorAll('.js-footer-style-group');
    var footerDisplaySelect = document.getElementById('ORDERWITHDRAWL_FOOTER_DISPLAY');
    var footerColorGroups = document.querySelectorAll('.js-footer-color-group');
    var footerLinkColorGroups = document.querySelectorAll('.js-footer-link-color-group');
    var colorTextInput = document.getElementById('ORDERWITHDRAWL_BUTTON_COLOR');
    var colorPicker = document.getElementById('ORDERWITHDRAWL_BUTTON_COLOR_PICKER');
    var fontColorTextInput = document.getElementById('ORDERWITHDRAWL_BUTTON_FONT_COLOR');
    var fontColorPicker = document.getElementById('ORDERWITHDRAWL_BUTTON_FONT_COLOR_PICKER');
    var linkColorTextInput = document.getElementById('ORDERWITHDRAWL_LINK_COLOR');
    var linkColorPicker = document.getElementById('ORDERWITHDRAWL_LINK_COLOR_PICKER');
    var modeSelect = document.getElementById('ORDERWITHDRAWL_MODE');
    var periodStartModeSelect = document.getElementById('ORDERWITHDRAWL_PERIOD_START_MODE');
    var periodOnlyGroups = document.querySelectorAll('.js-mode-period-only');
    var shippingStatusOnlyGroups = document.querySelectorAll('.js-period-start-shipping-only');
    var footerButtonOnlyGroups = document.querySelectorAll('.js-footer-button-only');
    var moduleForm = document.getElementById('module_form');
    var productSelectionToggle = document.querySelector('[data-ow-product-selection-toggle]');
    var productSelectionOptions = document.querySelector('[data-ow-product-selection-options]');
    var invalidMessage = colorTextInput ? (colorTextInput.dataset.invalidMessage || 'Please use a valid hex color (e.g. #F1B218).') : 'Please use a valid hex color (e.g. #F1B218).';

    var normalizeHex = function (value) {
      if (typeof value !== 'string') {
        console.log('[ORDERWITHDRAWL] normalizeHex skipped non-string value', value);
        return null;
      }
      var trimmed = value.trim();
      if (!trimmed) {
        console.log('[ORDERWITHDRAWL] normalizeHex empty after trim');
        return null;
      }
      if (trimmed.charAt(0) !== '#') {
        trimmed = '#' + trimmed;
      }
      var match = /^#([0-9a-fA-F]{6}|[0-9a-fA-F]{3})$/.exec(trimmed);
      if (!match) {
        console.log('[ORDERWITHDRAWL] normalizeHex invalid format', trimmed);
        return null;
      }

      var hex = match[1].toUpperCase();
      if (hex.length === 3) {
        hex = hex.split('').map(function (char) {
          return char + char;
        }).join('');
        console.log('[ORDERWITHDRAWL] normalizeHex expanded shorthand', match[1], '->', hex);
      }

      console.log('[ORDERWITHDRAWL] normalizeHex normalized value', '#' + hex);
      return '#' + hex;
    };

    var syncPickerToText = function () {
      if (!colorTextInput || !colorPicker) {
        return;
      }
      var normalized = normalizeHex(colorPicker.value);
      if (normalized) {
        colorPicker.value = normalized;
        colorTextInput.value = normalized;
        colorTextInput.setCustomValidity('');
        console.log('[ORDERWITHDRAWL] syncPickerToText applied value', normalized);
      }
    };

    var syncTextToPicker = function () {
      if (!colorTextInput || !colorPicker) {
        return;
      }
      var normalized = normalizeHex(colorTextInput.value);
      if (normalized) {
        colorTextInput.value = normalized;
        colorPicker.value = normalized;
        colorTextInput.setCustomValidity('');
        console.log('[ORDERWITHDRAWL] syncTextToPicker applied value', normalized);
      } else {
        colorTextInput.setCustomValidity(invalidMessage);
        console.log('[ORDERWITHDRAWL] syncTextToPicker rejected value', colorTextInput.value);
      }
    };

    var syncFontPickerToText = function () {
      if (!fontColorTextInput || !fontColorPicker) {
        return;
      }
      var normalized = normalizeHex(fontColorPicker.value);
      if (normalized) {
        fontColorPicker.value = normalized;
        fontColorTextInput.value = normalized;
        console.log('[ORDERWITHDRAWL] syncFontPickerToText applied value', normalized);
      }
    };

    var syncFontTextToPicker = function () {
      if (!fontColorTextInput || !fontColorPicker) {
        return;
      }
      var normalized = normalizeHex(fontColorTextInput.value);
      if (normalized) {
        fontColorTextInput.value = normalized;
        fontColorPicker.value = normalized;
        console.log('[ORDERWITHDRAWL] syncFontTextToPicker applied value', normalized);
      } else {
        console.log('[ORDERWITHDRAWL] syncFontTextToPicker awaiting valid value', fontColorTextInput.value);
      }
    };

    var syncLinkPickerToText = function () {
      if (!linkColorTextInput || !linkColorPicker) {
        return;
      }
      var normalized = normalizeHex(linkColorPicker.value);
      if (normalized) {
        linkColorPicker.value = normalized;
        linkColorTextInput.value = normalized;
        console.log('[ORDERWITHDRAWL] syncLinkPickerToText applied value', normalized);
      }
    };

    var syncLinkTextToPicker = function () {
      if (!linkColorTextInput || !linkColorPicker) {
        return;
      }
      var normalized = normalizeHex(linkColorTextInput.value);
      if (normalized) {
        linkColorTextInput.value = normalized;
        linkColorPicker.value = normalized;
        console.log('[ORDERWITHDRAWL] syncLinkTextToPicker applied value', normalized);
      } else {
        console.log('[ORDERWITHDRAWL] syncLinkTextToPicker awaiting valid value', linkColorTextInput.value);
      }
    };

    if (colorTextInput && colorPicker) {
      var initial = normalizeHex(colorTextInput.value) || '#F44708';
      colorTextInput.value = initial;
      colorPicker.value = initial;
      colorTextInput.setCustomValidity('');
      console.log('[ORDERWITHDRAWL] initial color normalization', initial);

      colorPicker.addEventListener('input', syncPickerToText);
      colorTextInput.addEventListener('input', function () {
        var normalized = normalizeHex(colorTextInput.value);
        if (normalized) {
          colorTextInput.value = normalized;
          colorPicker.value = normalized;
          colorTextInput.setCustomValidity('');
          console.log('[ORDERWITHDRAWL] input event normalized value', normalized);
        } else {
          colorTextInput.setCustomValidity('');
          console.log('[ORDERWITHDRAWL] input event awaiting valid value', colorTextInput.value);
        }
      });
      colorTextInput.addEventListener('blur', syncTextToPicker);
      colorTextInput.addEventListener('change', syncTextToPicker);
    }

    if (fontColorTextInput && fontColorPicker) {
      var initialFont = normalizeHex(fontColorTextInput.value) || '#FFFFFF';
      fontColorTextInput.value = initialFont;
      fontColorPicker.value = initialFont;
      console.log('[ORDERWITHDRAWL] initial font color normalization', initialFont);

      fontColorPicker.addEventListener('input', syncFontPickerToText);
      fontColorTextInput.addEventListener('input', function () {
        var normalized = normalizeHex(fontColorTextInput.value);
        if (normalized) {
          fontColorTextInput.value = normalized;
          fontColorPicker.value = normalized;
          console.log('[ORDERWITHDRAWL] font input event normalized value', normalized);
        } else {
          console.log('[ORDERWITHDRAWL] font input event awaiting valid value', fontColorTextInput.value);
        }
      });
      fontColorTextInput.addEventListener('blur', syncFontTextToPicker);
      fontColorTextInput.addEventListener('change', syncFontTextToPicker);
    }

    if (linkColorTextInput && linkColorPicker) {
      var initialLink = normalizeHex(linkColorTextInput.value) || '#2563EB';
      linkColorTextInput.value = initialLink;
      linkColorPicker.value = initialLink;
      console.log('[ORDERWITHDRAWL] initial link color normalization', initialLink);

      linkColorPicker.addEventListener('input', syncLinkPickerToText);
      linkColorTextInput.addEventListener('input', function () {
        var normalized = normalizeHex(linkColorTextInput.value);
        if (normalized) {
          linkColorTextInput.value = normalized;
          linkColorPicker.value = normalized;
          console.log('[ORDERWITHDRAWL] link input event normalized value', normalized);
        } else {
          console.log('[ORDERWITHDRAWL] link input event awaiting valid value', linkColorTextInput.value);
        }
      });
      linkColorTextInput.addEventListener('blur', syncLinkTextToPicker);
      linkColorTextInput.addEventListener('change', syncLinkTextToPicker);
    }

    if (moduleForm && colorTextInput) {
      moduleForm.addEventListener('submit', function (event) {
        if (footerCheckbox && !footerCheckbox.checked) {
          colorTextInput.setCustomValidity('');
          console.log('[ORDERWITHDRAWL] submit skipping color validation (footer disabled)');
          return true;
        }
        syncTextToPicker();
        if (!colorTextInput.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
          console.log('[ORDERWITHDRAWL] submit prevented due to invalid color');
        }
      });
    }

    var toggleColorGroup = function () {
      if (!footerDisplaySelect) {
        return;
      }
      var showButtonColors = footerCheckbox && footerCheckbox.checked && footerDisplaySelect.value === 'button';
      var showLinkColors = footerCheckbox && footerCheckbox.checked && footerDisplaySelect.value === 'link';

      footerColorGroups.forEach(function (group) {
        group.style.display = showButtonColors ? '' : 'none';
      });
      footerLinkColorGroups.forEach(function (group) {
        group.style.display = showLinkColors ? '' : 'none';
      });

      footerButtonOnlyGroups.forEach(function (group) {
        group.style.display = showButtonColors ? '' : 'none';
      });

      if (showButtonColors) {
        syncTextToPicker();
      }
      if (showLinkColors) {
        syncLinkTextToPicker();
      }
    };

    var toggleFooterGroup = function () {
      var showFooter = footerCheckbox && footerCheckbox.checked;
      footerGroups.forEach(function (group) {
        group.style.display = showFooter ? '' : 'none';
      });
      if (!showFooter && colorTextInput) {
        colorTextInput.setCustomValidity('');
        console.log('[ORDERWITHDRAWL] footer disabled, clearing color validity');
      }
      toggleColorGroup();
    };

    var toggleModeGroups = function () {
      if (!modeSelect) {
        return;
      }
      var isPeriod = modeSelect.value === 'period';
      periodOnlyGroups.forEach(function (group) {
        group.style.display = isPeriod ? '' : 'none';
      });
      togglePeriodStartGroups();
    };

    var togglePeriodStartGroups = function () {
      if (!periodStartModeSelect) {
        return;
      }
      var showShippingStatusOptions = (!modeSelect || modeSelect.value === 'period') && periodStartModeSelect.value === 'shipping_status';
      shippingStatusOnlyGroups.forEach(function (group) {
        group.style.display = showShippingStatusOptions ? '' : 'none';
        var fields = group.querySelectorAll('select, input, textarea');
        fields.forEach(function (field) {
          field.disabled = !showShippingStatusOptions;
        });
      });
    };

    var toggleProductSelectionOptions = function () {
      if (!productSelectionToggle || !productSelectionOptions) {
        return;
      }
      productSelectionOptions.style.display = productSelectionToggle.checked ? '' : 'none';
    };

    locationCheckboxes.forEach(function (checkbox) {
      checkbox.addEventListener('change', toggleFooterGroup);
    });

    if (footerDisplaySelect) {
      footerDisplaySelect.addEventListener('change', toggleColorGroup);
    }

    if (modeSelect) {
      modeSelect.addEventListener('change', toggleModeGroups);
    }
    if (periodStartModeSelect) {
      periodStartModeSelect.addEventListener('change', togglePeriodStartGroups);
    }
    if (productSelectionToggle) {
      productSelectionToggle.addEventListener('change', toggleProductSelectionOptions);
    }

    toggleFooterGroup();
    toggleModeGroups();
    toggleProductSelectionOptions();

    // Language Tabs Logic
    var langTabsContainers = document.querySelectorAll('[data-ow-lang-tabs]');
    
    langTabsContainers.forEach(function(container) {
        var tabs = container.querySelectorAll('[data-ow-lang-tab]');
        var panes = container.querySelectorAll('[data-ow-lang-pane]');
        
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                var langId = tab.getAttribute('data-ow-lang-tab');
                
                // Deactivate all in this container
                tabs.forEach(function(t) { t.classList.remove('is-active'); });
                panes.forEach(function(p) { p.classList.remove('is-active'); });
                
                // Activate clicked
                tab.classList.add('is-active');
                var activePane = container.querySelector('[data-ow-lang-pane="' + langId + '"]');
                if (activePane) {
                    activePane.classList.add('is-active');
                }
            });
        });
    });
  });
</script>
{/literal}
