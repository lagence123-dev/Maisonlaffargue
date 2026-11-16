<?php
/**
 * Back-office configuration controller for the OrderWithdrawl module.
 *
 * Owns every admin-only concern that historically lived inline in
 * `orderwithdrawl.php::getContent()`:
 *   - Render the configure.tpl form (with confirmation / error banners)
 *   - POST validation & persistence of all ORDERWITHDRAWL_* settings
 *   - Ajax endpoints (mail preview, send-test mail, reset-to-defaults)
 *   - Admin-only Smarty assignments (mail preview payload, pre-computed
 *     mode / period-start labels, CMS page dropdown feed, etc.)
 *
 * The class is deliberately coupled to the {@see \OrderWithdrawl} module
 * instance because we still rely on shared helpers (hex color validation,
 * display-location selections, language-aware default texts) that are used
 * both here AND from the hook surface. Those helpers stay in the module so
 * Hooks do not acquire a dependency on this class.
 *
 * @author    sjmedia GmbH
 * @copyright 2024-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace OrderWithdrawl\Admin;

if (!defined('_PS_VERSION_')) {
    exit;
}

use OrderWithdrawl\Config\OrderWithdrawlConfig;
use OrderWithdrawl\Mail\MailDispatcher;
use OrderWithdrawl\Order\OrderIdentifier;
use OrderWithdrawl\Settings\SettingsService;

final class AdminConfigController
{
    /** @var \OrderWithdrawl */
    private $module;

    /** @var SettingsService */
    private $settings;

    /** @var MailDispatcher */
    private $mailDispatcher;

    /** @var MailPreviewRenderer */
    private $mailPreview;

    public function __construct(\OrderWithdrawl $module, SettingsService $settings, MailDispatcher $mailDispatcher, MailPreviewRenderer $mailPreview)
    {
        $this->module = $module;
        $this->settings = $settings;
        $this->mailDispatcher = $mailDispatcher;
        $this->mailPreview = $mailPreview;
    }

    /**
     * Entry point mirroring the contract of the old `OrderWithdrawl::getContent()`.
     *
     * Handles back-office AJAX / reset actions first (each of those
     * terminates the request via die() when applicable) and then renders
     * the configure.tpl template.
     */
    public function render(): string
    {
        // UX-A-M1 + UX-A-L1: handle the back-office AJAX / reset actions
        // before the main render. Each handler terminates the request itself
        // (either via die() for JSON/HTML or by falling through to the
        // standard form flow for the reset-and-redisplay case).
        $this->handleAdminAsyncActions();

        $module = $this->module;
        $context = $module->getContext();
        $languages = \Language::getLanguages(false);
        $confirmation_message = '';
        $error_message = '';
        $buttonClasses = '';

        $isPs9 = version_compare(_PS_VERSION_, '9.0.0', '>=');

        $privacyPages = [];
        $privacyNotes = [];
        $withdrawalPages = [];
        $withdrawalNotes = [];
        $hasError = false;
        $displayLocationSelection = null;
        $footerHookSelection = null;
        $decisionApprovedNotes = [];
        $decisionRejectedNotes = [];
        $isFormSubmission = \Tools::isSubmit('submitOrderWithdrawlSettings');
        $cmsPagesByLanguage = $this->getCmsPagesByLanguage($languages);

        $resetNotice = isset($_POST['__orderwithdrawl_reset_notice']) ? (string) $_POST['__orderwithdrawl_reset_notice'] : '';
        if ($resetNotice === 'ok') {
            $confirmation_message = $module->l('Settings have been reset to the module defaults.');
        } elseif ($resetNotice === 'fail') {
            $error_message = $module->l('Reset failed. Check your shop logs for details.');
        }

        if ($isFormSubmission) {
            $days = (int) \Tools::getValue('ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS');
            $deliveryDays = (int) \Tools::getValue('ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS');
            $adminEmails = \Tools::getValue('ORDERWITHDRAWL_ADMIN_EMAILS');
            $footerDisplay = \Tools::getValue('ORDERWITHDRAWL_FOOTER_DISPLAY');
            $mode = \Tools::getValue('ORDERWITHDRAWL_MODE', 'period');
            $autoRejectExpired = (int) \Tools::getValue(OrderWithdrawlConfig::AUTO_REJECT_EXPIRED, 0) ? 1 : 0;
            $orderAssignmentMode = (string) \Tools::getValue('ORDERWITHDRAWL_ORDER_ASSIGNMENT_MODE', '');
            if ($orderAssignmentMode !== '') {
                list($customerVerificationMode, $orderIdentifierMode) = $this->parseOrderAssignmentMode($orderAssignmentMode);
            } else {
                $orderIdentifierMode = (string) \Tools::getValue(
                    OrderWithdrawlConfig::ORDER_IDENTIFIER_MODE,
                    OrderWithdrawlConfig::DEFAULT_ORDER_IDENTIFIER_MODE
                );
                $customerVerificationMode = (string) \Tools::getValue(
                    OrderWithdrawlConfig::CUSTOMER_VERIFICATION_MODE,
                    OrderWithdrawlConfig::DEFAULT_CUSTOMER_VERIFICATION_MODE
                );
            }
            $periodStartMode = (string) \Tools::getValue('ORDERWITHDRAWL_PERIOD_START_MODE', 'order_date');
            $triggerStateInputs = (array) \Tools::getValue('ORDERWITHDRAWL_TRIGGER_STATE_IDS', []);
            $displayLocationInput = \Tools::getValue('ORDERWITHDRAWL_DISPLAY_LOCATION', []);
            $buttonLabelInputs = (array) \Tools::getValue('ORDERWITHDRAWL_BUTTON_LABEL', []);
            $privacyPageInputs = (array) \Tools::getValue('ORDERWITHDRAWL_PRIVACY_PAGE', []);
            $privacyNoteInputs = (array) \Tools::getValue('ORDERWITHDRAWL_PRIVACY_NOTE', []);
            $withdrawalPageInputs = (array) \Tools::getValue('ORDERWITHDRAWL_WITHDRAWAL_PAGE', []);
            $withdrawalNoteInputs = (array) \Tools::getValue('ORDERWITHDRAWL_WITHDRAWAL_NOTE', []);
            $buttonClassesInput = \Tools::getValue('ORDERWITHDRAWL_BUTTON_CLASSES', '');
            $footerHooksInput = \Tools::getValue('ORDERWITHDRAWL_FOOTER_HOOKS', []);
            $decisionApprovedInputs = (array) \Tools::getValue('ORDERWITHDRAWL_DECISION_APPROVED', []);
            $decisionRejectedInputs = (array) \Tools::getValue('ORDERWITHDRAWL_DECISION_REJECTED', []);
            $enableCustomerNote = (int) \Tools::getValue('ORDERWITHDRAWL_ENABLE_CUSTOMER_NOTE', 0) ? 1 : 0;
            $productSelectionMode = (int) \Tools::getValue(OrderWithdrawlConfig::PRODUCT_SELECTION_MODE, 0) ? 1 : 0;
            $excludedProductIds = $this->normalizeIdListInput(\Tools::getValue(OrderWithdrawlConfig::EXCLUDED_PRODUCT_IDS, ''));
            $excludedCategoryIds = $this->normalizeIdListInput(\Tools::getValue(OrderWithdrawlConfig::EXCLUDED_CATEGORY_IDS, ''));
            $excludeCustomizedProducts = (int) \Tools::getValue(OrderWithdrawlConfig::EXCLUDE_CUSTOMIZED_PRODUCTS, 0) ? 1 : 0;
            $phpMailFallback = (int) \Tools::getValue(OrderWithdrawlConfig::PHP_MAIL_FALLBACK, 0) ? 1 : 0;
            $buttonSizeInput = \Tools::getValue('ORDERWITHDRAWL_BUTTON_SIZE', 'medium');
            $buttonAlignInput = \Tools::getValue('ORDERWITHDRAWL_BUTTON_ALIGN', 'center');
            $buttonLabels = [];
            foreach ($languages as $language) {
                $idLang = (int) $language['id_lang'];
                $value = isset($buttonLabelInputs[$idLang]) ? \Tools::substr(trim(strip_tags($buttonLabelInputs[$idLang])), 0, 255) : '';
                if ($value === '') {
                    $value = '';
                }
                $buttonLabels[$idLang] = $value;

                $pageValue = isset($privacyPageInputs[$idLang]) ? (int) $privacyPageInputs[$idLang] : 0;
                if ($pageValue < 0) {
                    $pageValue = 0;
                }
                $privacyPages[$idLang] = $pageValue;

                $noteValue = isset($privacyNoteInputs[$idLang]) ? (string) $privacyNoteInputs[$idLang] : '';
                $noteValue = str_replace(["\r\n", "\r"], "\n", $noteValue);
                $noteValue = \Tools::substr(trim(strip_tags($noteValue)), 0, 4000);
                $privacyNotes[$idLang] = $noteValue;

                $withdrawalPageValue = isset($withdrawalPageInputs[$idLang]) ? (int) $withdrawalPageInputs[$idLang] : 0;
                if ($withdrawalPageValue < 0) {
                    $withdrawalPageValue = 0;
                }
                $withdrawalPages[$idLang] = $withdrawalPageValue;

                $withdrawalNoteValue = isset($withdrawalNoteInputs[$idLang]) ? (string) $withdrawalNoteInputs[$idLang] : '';
                $withdrawalNoteValue = str_replace(["\r\n", "\r"], "\n", $withdrawalNoteValue);
                $withdrawalNoteValue = \Tools::substr(trim(strip_tags($withdrawalNoteValue)), 0, 4000);
                $withdrawalNotes[$idLang] = $withdrawalNoteValue;

                $approvedNoteValue = isset($decisionApprovedInputs[$idLang]) ? (string) $decisionApprovedInputs[$idLang] : '';
                $approvedNoteValue = str_replace(["\r\n", "\r"], "\n", $approvedNoteValue);
                $approvedNoteValue = \Tools::substr(trim(strip_tags($approvedNoteValue)), 0, 4000);
                $decisionApprovedNotes[$idLang] = $approvedNoteValue;

                $rejectedNoteValue = isset($decisionRejectedInputs[$idLang]) ? (string) $decisionRejectedInputs[$idLang] : '';
                $rejectedNoteValue = str_replace(["\r\n", "\r"], "\n", $rejectedNoteValue);
                $rejectedNoteValue = \Tools::substr(trim(strip_tags($rejectedNoteValue)), 0, 4000);
                $decisionRejectedNotes[$idLang] = $rejectedNoteValue;
            }
            $buttonClasses = \Tools::substr(trim((string) $buttonClassesInput), 0, 255);
            $buttonColor = trim(\Tools::getValue('ORDERWITHDRAWL_BUTTON_COLOR'));
            $buttonFontColor = trim(\Tools::getValue('ORDERWITHDRAWL_BUTTON_FONT_COLOR'));
            $linkColor = trim(\Tools::getValue('ORDERWITHDRAWL_LINK_COLOR'));
            $hasError = false;

            $allowedSizes = ['small', 'medium', 'large'];
            $allowedAlign = ['left', 'center', 'right'];
            $orderIdentifierMode = OrderIdentifier::normalizeMode($orderIdentifierMode);
            if (!in_array($customerVerificationMode, OrderWithdrawlConfig::ALLOWED_CUSTOMER_VERIFICATION_MODES, true)) {
                $customerVerificationMode = OrderWithdrawlConfig::DEFAULT_CUSTOMER_VERIFICATION_MODE;
            }
            $periodStartMode = $module->normalizeWithdrawalPeriodStartMode($periodStartMode);
            $triggerStateIds = $module->normalizeWithdrawalTriggerStateIds($triggerStateInputs);

            if ($mode === 'period') {
                if ($days < 1 || $days > 365) {
                    $error_message = $module->l('Please enter a valid number of days (1-365).');
                    $hasError = true;
                }

                if ($deliveryDays < 0 || $deliveryDays > 365) {
                    $error_message = $module->l('Please enter a valid delivery duration (0-365 days).');
                    $hasError = true;
                }

                if (!in_array($periodStartMode, $module->getWithdrawalPeriodStartModes(), true)) {
                    $error_message = $module->l('Please choose a valid legal period calculation mode.');
                    $hasError = true;
                }
            }

            if ($adminEmails) {
                $emails = explode(',', $adminEmails);
                foreach ($emails as $email) {
                    $email = trim($email);
                    if ($email && !\Validate::isEmail($email)) {
                        $error_message = $module->l('Please enter valid email addresses separated by commas.');
                        $hasError = true;
                        break;
                    }
                }
            }

            $allowedFooterDisplays = ['button', 'link'];
            if (!in_array($footerDisplay, $allowedFooterDisplays, true)) {
                $error_message = $module->l('Please choose a valid footer display option.');
                $hasError = true;
            }

            $displayLocation = $this->normalizeDisplayLocationInput($displayLocationInput);
            if (!$displayLocation) {
                $error_message = $module->l('Please choose where the withdrawal action should appear.');
                $hasError = true;
            }
            $displayLocationSelection = $displayLocation;

            $footerHooksSelection = $module->collectFooterHookValues($footerHooksInput);
            $footerHookSelection = $footerHooksSelection;

            foreach ($privacyPages as $selectedPage) {
                if ($selectedPage && (!$this->isCmsPagePublished((int) $selectedPage) || !\Validate::isUnsignedId($selectedPage))) {
                    $error_message = $module->l('Please select a published CMS page for the privacy notice.');
                    $hasError = true;
                    break;
                }
            }

            if (!$hasError) {
                foreach ($withdrawalPages as $selectedWithdrawalPage) {
                    if ($selectedWithdrawalPage && (!$this->isCmsPagePublished((int) $selectedWithdrawalPage) || !\Validate::isUnsignedId($selectedWithdrawalPage))) {
                        $error_message = $module->l('Please select a published CMS page for the withdrawal notice.');
                        $hasError = true;
                        break;
                    }
                }
            }

            $labelProvided = false;
            foreach ($languages as $language) {
                $idLang = (int) $language['id_lang'];
                if ($buttonLabels[$idLang] !== '') {
                    $labelProvided = true;
                    break;
                }
            }
            if (!$labelProvided) {
                $error_message = $module->l('Please provide a label for the withdrawal button.');
                $hasError = true;
            }

            if ($module->isDisplayLocationEnabled('footer', $displayLocation) && $footerDisplay === 'button' && !$module->isValidHexColor($buttonColor)) {
                $error_message = $module->l('Please provide a valid hex color (e.g. #FF6600).');
                $hasError = true;
            }

            if ($module->isDisplayLocationEnabled('footer', $displayLocation) && $footerDisplay === 'button' && $buttonFontColor !== '' && !$module->isValidHexColor($buttonFontColor)) {
                $error_message = $module->l('Please provide a valid font color in hex format (e.g. #FFFFFF).');
                $hasError = true;
            }

            if ($module->isDisplayLocationEnabled('footer', $displayLocation) && $footerDisplay === 'link' && $linkColor !== '' && !$module->isValidHexColor($linkColor)) {
                $error_message = $module->l('Please provide a valid link color in hex format (e.g. #2563EB).');
                $hasError = true;
            }

            if ($module->isDisplayLocationEnabled('footer', $displayLocation) && !$footerHooksSelection) {
                $error_message = $module->l('Please choose at least one footer hook position for the withdrawal CTA.');
                $hasError = true;
            }

            if (!$hasError && $buttonClasses !== '') {
                // allow only typical CSS class characters: letters, numbers, dash, underscore and spaces
                if (!preg_match('/^[A-Za-z0-9\-\s_]+$/', $buttonClasses)) {
                    $error_message = $module->l('Please use only letters, numbers, dashes, underscores and spaces for the Bootstrap classes.');
                    $hasError = true;
                }
            }

            if (!$hasError) {
                \Configuration::updateValue('ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS', $days);
                \Configuration::updateValue('ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS', $deliveryDays);
                \Configuration::updateValue('ORDERWITHDRAWL_PERIOD_START_MODE', $periodStartMode);
                \Configuration::updateValue('ORDERWITHDRAWL_TRIGGER_STATE_IDS', implode(',', $triggerStateIds));
                \Configuration::updateValue('ORDERWITHDRAWL_ADMIN_EMAILS', $adminEmails);
                \Configuration::updateValue('ORDERWITHDRAWL_FOOTER_DISPLAY', $footerDisplay);
                \Configuration::updateValue('ORDERWITHDRAWL_DISPLAY_LOCATION', $this->formatDisplayLocationStorage($displayLocation));
                \Configuration::updateValue('ORDERWITHDRAWL_BUTTON_CLASSES', $buttonClasses);
                \Configuration::updateValue('ORDERWITHDRAWL_FOOTER_HOOKS', $module->formatFooterHookStorage($footerHooksSelection));
                \Configuration::updateValue('ORDERWITHDRAWL_MODE', $mode);
                \Configuration::updateValue(OrderWithdrawlConfig::AUTO_REJECT_EXPIRED, $autoRejectExpired);
                \Configuration::updateValue(OrderWithdrawlConfig::ORDER_IDENTIFIER_MODE, $orderIdentifierMode);
                \Configuration::updateValue(OrderWithdrawlConfig::CUSTOMER_VERIFICATION_MODE, $customerVerificationMode);
                \Configuration::updateValue('ORDERWITHDRAWL_ENABLE_CUSTOMER_NOTE', $enableCustomerNote);
                \Configuration::updateValue(OrderWithdrawlConfig::PRODUCT_SELECTION_MODE, $productSelectionMode);
                \Configuration::updateValue(OrderWithdrawlConfig::EXCLUDED_PRODUCT_IDS, $excludedProductIds);
                \Configuration::updateValue(OrderWithdrawlConfig::EXCLUDED_CATEGORY_IDS, $excludedCategoryIds);
                \Configuration::updateValue(OrderWithdrawlConfig::EXCLUDE_CUSTOMIZED_PRODUCTS, $excludeCustomizedProducts);
                \Configuration::updateValue(OrderWithdrawlConfig::PHP_MAIL_FALLBACK, $phpMailFallback);
                if (!in_array($buttonSizeInput, $allowedSizes, true)) {
                    $buttonSizeInput = 'medium';
                }
                if (!in_array($buttonAlignInput, $allowedAlign, true)) {
                    $buttonAlignInput = 'center';
                }
                \Configuration::updateValue('ORDERWITHDRAWL_BUTTON_SIZE', $buttonSizeInput);
                \Configuration::updateValue('ORDERWITHDRAWL_BUTTON_ALIGN', $buttonAlignInput);

                $finalLabels = [];
                foreach ($languages as $language) {
                    $idLang = (int) $language['id_lang'];
                    $value = $buttonLabels[$idLang];
                    if ($value === '') {
                        $value = $module->getDefaultButtonLabelByIso($language['iso_code']);
                    }
                    $finalLabels[$idLang] = $value;
                }
                \Configuration::updateValue('ORDERWITHDRAWL_BUTTON_LABEL', $finalLabels, true);
                \Configuration::updateValue('ORDERWITHDRAWL_PRIVACY_PAGE', $privacyPages, true);
                \Configuration::updateValue('ORDERWITHDRAWL_PRIVACY_NOTE', $privacyNotes, true);
                \Configuration::updateValue('ORDERWITHDRAWL_WITHDRAWAL_PAGE', $withdrawalPages, true);
                \Configuration::updateValue('ORDERWITHDRAWL_WITHDRAWAL_NOTE', $withdrawalNotes, true);
                \Configuration::updateValue('ORDERWITHDRAWL_DECISION_APPROVED', $decisionApprovedNotes, true);
                \Configuration::updateValue('ORDERWITHDRAWL_DECISION_REJECTED', $decisionRejectedNotes, true);

                if ($module->isDisplayLocationEnabled('footer', $displayLocation)) {
                    if ($footerDisplay === 'button') {
                        \Configuration::updateValue('ORDERWITHDRAWL_BUTTON_COLOR', \Tools::strtoupper($buttonColor));
                        if ($buttonFontColor !== '') {
                            \Configuration::updateValue('ORDERWITHDRAWL_BUTTON_FONT_COLOR', \Tools::strtoupper($buttonFontColor));
                        }
                    } elseif ($footerDisplay === 'link') {
                        \Configuration::updateValue('ORDERWITHDRAWL_LINK_COLOR', \Tools::strtoupper($linkColor));
                    }
                }

                $this->clearCustomerAccountCaches();

                $confirmation_message = $module->l('Settings updated successfully.');
            }
        }

        if (!$isFormSubmission) {
            $privacyPages = $this->getStoredPrivacyPages($languages);
            $privacyNotes = $this->getStoredPrivacyNotes($languages);
            $withdrawalPages = $this->getStoredWithdrawalPages($languages);
            $withdrawalNotes = $this->getStoredWithdrawalNotes($languages);
            $buttonClasses = (string) \Configuration::get('ORDERWITHDRAWL_BUTTON_CLASSES', null, null, null, '');
            $footerHookSelection = $module->getFooterHookSelections();
            $decisionApprovedNotes = $this->getStoredDecisionApprovedNotes($languages);
            $decisionRejectedNotes = $this->getStoredDecisionRejectedNotes($languages);
        }

        $privacyNotePlaceholders = $this->getDefaultPrivacyNotePlaceholders($languages);
        $withdrawalNotePlaceholders = $this->getDefaultWithdrawalNotePlaceholders($languages);
        $decisionApprovedPlaceholders = $this->getDefaultDecisionApprovedPlaceholders($languages);
        $decisionRejectedPlaceholders = $this->getDefaultDecisionRejectedPlaceholders($languages);

        $displayLocationSelections = $displayLocationSelection !== null
            ? $displayLocationSelection
            : $module->getDisplayLocationSelections();
        $displayLocationShowFooter = $module->isDisplayLocationEnabled('footer', $displayLocationSelections);
        $displayLocationShowAccountBlock = $module->isDisplayLocationEnabled('account_block', $displayLocationSelections);
        $displayLocationShowAccountPage = $module->isDisplayLocationEnabled('account_page', $displayLocationSelections);
        $displayLocationShowCustomHook = $module->isDisplayLocationEnabled('custom_hook', $displayLocationSelections);

        // Get order state information
        $withdrawl_state_id = \Configuration::get('ORDERWITHDRAWL_STATE_ID');
        $send_email_enabled = false;

        if ($withdrawl_state_id) {
            $orderState = new \OrderState((int) $withdrawl_state_id);
            if (\Validate::isLoadedObject($orderState)) {
                $send_email_enabled = (bool) $orderState->send_email;
            }
        }

        $triggerStateIds = $module->getWithdrawalTriggerStateIds();
        $triggerStateLookup = array_fill_keys($triggerStateIds, true);
        $withdrawlPeriodDays = (int) \Configuration::get(
            'ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS',
            null,
            null,
            null,
            14
        );
        $withdrawlDeliveryDays = (int) \Configuration::get(
            'ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS',
            null,
            null,
            null,
            0
        );
        $withdrawlTotalWindowDays = $withdrawlPeriodDays + $withdrawlDeliveryDays;
        $adminEmailsValue = (string) \Configuration::get('ORDERWITHDRAWL_ADMIN_EMAILS', null, null, null, '');
        $activeLocationCount = 0;
        if ($displayLocationShowFooter) {
            ++$activeLocationCount;
        }
        if ($displayLocationShowAccountBlock) {
            ++$activeLocationCount;
        }
        if ($displayLocationShowAccountPage) {
            ++$activeLocationCount;
        }
        $additionalEmailCount = 0;
        if ($adminEmailsValue !== '') {
            $additionalEmailCount = count(array_filter(array_map('trim', explode(',', $adminEmailsValue))));
        }

        $withdrawlModeValue = (string) \Configuration::get('ORDERWITHDRAWL_MODE', null, null, null, 'period');
        $withdrawlPeriodStartModeValue = $module->getWithdrawalPeriodStartMode();
        $orderIdentifierModeValue = OrderIdentifier::normalizeMode((string) \Configuration::get(
            OrderWithdrawlConfig::ORDER_IDENTIFIER_MODE,
            null,
            null,
            null,
            OrderWithdrawlConfig::DEFAULT_ORDER_IDENTIFIER_MODE
        ));
        $customerVerificationModeValue = (string) \Configuration::get(
            OrderWithdrawlConfig::CUSTOMER_VERIFICATION_MODE,
            null,
            null,
            null,
            OrderWithdrawlConfig::DEFAULT_CUSTOMER_VERIFICATION_MODE
        );
        if (!in_array($customerVerificationModeValue, OrderWithdrawlConfig::ALLOWED_CUSTOMER_VERIFICATION_MODES, true)) {
            $customerVerificationModeValue = OrderWithdrawlConfig::DEFAULT_CUSTOMER_VERIFICATION_MODE;
        }

        // Pre-compute display labels (CQ-MN3: keep Smarty template free of business logic).
        $withdrawlModeLabel = $this->getWithdrawalModeLabel($withdrawlModeValue);
        $withdrawlPeriodStartLabel = $this->getWithdrawalPeriodStartLabel(
            $withdrawlPeriodStartModeValue,
            $withdrawlTotalWindowDays
        );

        $moduleName = $module->name;
        $moduleTab = $module->tab;
        $adminAjaxUrl = $context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $moduleName . '&tab_module=' . $moduleTab
            . '&module_name=' . $moduleName . '&token=' . \Tools::getAdminTokenLite('AdminModules');
        $excludedProductIds = (string) \Configuration::get(
            OrderWithdrawlConfig::EXCLUDED_PRODUCT_IDS,
            null,
            null,
            null,
            OrderWithdrawlConfig::DEFAULT_EXCLUDED_PRODUCT_IDS
        );
        $excludedCategoryIds = (string) \Configuration::get(
            OrderWithdrawlConfig::EXCLUDED_CATEGORY_IDS,
            null,
            null,
            null,
            OrderWithdrawlConfig::DEFAULT_EXCLUDED_CATEGORY_IDS
        );
        $context->smarty->assign([
            'form_action' => $adminAjaxUrl,
            'module_active' => (bool) $module->active,
            'withdrawl_period_days' => $withdrawlPeriodDays,
            'withdrawl_delivery_days' => $withdrawlDeliveryDays,
            'withdrawl_total_window_days' => $withdrawlTotalWindowDays,
            'active_location_count' => $activeLocationCount,
            'additional_email_count' => $additionalEmailCount,
            'withdrawl_period_start_mode' => $withdrawlPeriodStartModeValue,
            'withdrawl_mode_label' => $withdrawlModeLabel,
            'order_assignment_mode' => $this->buildOrderAssignmentMode($customerVerificationModeValue, $orderIdentifierModeValue),
            'order_assignment_mode_label' => $this->getOrderAssignmentModeLabel($customerVerificationModeValue, $orderIdentifierModeValue),
            'order_identifier_mode' => $orderIdentifierModeValue,
            'customer_verification_mode' => $customerVerificationModeValue,
            'withdrawl_period_start_label' => $withdrawlPeriodStartLabel,
            'withdrawl_trigger_state_ids' => $triggerStateIds,
            'withdrawl_trigger_state_lookup' => $triggerStateLookup,
            'available_order_states' => \OrderState::getOrderStates((int) $context->language->id),
            'admin_emails' => $adminEmailsValue,
            'withdrawl_state_id' => $withdrawl_state_id,
            'send_email_enabled' => $send_email_enabled,
            'footer_display_mode' => \Configuration::get('ORDERWITHDRAWL_FOOTER_DISPLAY', null, null, null, 'button'),
            'display_location_values' => $displayLocationSelections,
            'display_location_show_footer' => $displayLocationShowFooter,
            'display_location_show_account_block' => $displayLocationShowAccountBlock,
            'display_location_show_account_page' => $displayLocationShowAccountPage,
            'display_location_show_custom_hook' => $displayLocationShowCustomHook,
            'footer_hook_values' => $footerHookSelection,
            'footer_hook_displayFooter' => $module->isFooterHookEnabled('displayFooter', $footerHookSelection ?: $module->getFooterHookSelections()),
            'footer_hook_displayFooterBefore' => $module->isFooterHookEnabled('displayFooterBefore', $footerHookSelection ?: $module->getFooterHookSelections()),
            'footer_hook_displayFooterAfter' => $module->isFooterHookEnabled('displayFooterAfter', $footerHookSelection ?: $module->getFooterHookSelections()),
            'footer_hook_displayBeforeBodyClosingTag' => $module->isFooterHookEnabled('displayBeforeBodyClosingTag', $footerHookSelection ?: $module->getFooterHookSelections()),
            'privacy_cms_pages' => $cmsPagesByLanguage,
            'privacy_page_lang' => $privacyPages,
            'privacy_note_lang' => $privacyNotes,
            'privacy_note_placeholder' => $privacyNotePlaceholders,
            'withdrawal_page_lang' => $withdrawalPages,
            'withdrawal_note_lang' => $withdrawalNotes,
            'withdrawal_note_placeholder' => $withdrawalNotePlaceholders,
            'button_label_lang' => $this->getButtonLabelValues($languages),
            'button_label_placeholder' => $this->getDefaultButtonLabelPlaceholders($languages),
            'decision_approved_lang' => $decisionApprovedNotes,
            'decision_rejected_lang' => $decisionRejectedNotes,
            'decision_approved_placeholder' => $decisionApprovedPlaceholders,
            'decision_rejected_placeholder' => $decisionRejectedPlaceholders,
            'enable_customer_note' => (bool) \Configuration::get('ORDERWITHDRAWL_ENABLE_CUSTOMER_NOTE', null, null, null, 0),
            'product_selection_mode_enabled' => (bool) \Configuration::get(OrderWithdrawlConfig::PRODUCT_SELECTION_MODE, null, null, null, OrderWithdrawlConfig::DEFAULT_PRODUCT_SELECTION_MODE),
            'excluded_product_ids' => $excludedProductIds,
            'excluded_category_ids' => $excludedCategoryIds,
            'excluded_product_choices' => $this->resolveExcludedProductChoices($excludedProductIds),
            'excluded_category_choices' => $this->resolveExcludedCategoryChoices($excludedCategoryIds),
            'category_exclusion_tree' => $this->buildCategoryExclusionTree($excludedCategoryIds),
            'admin_ajax_url' => $adminAjaxUrl,
            'exclude_customized_products_enabled' => (bool) \Configuration::get(OrderWithdrawlConfig::EXCLUDE_CUSTOMIZED_PRODUCTS, null, null, null, OrderWithdrawlConfig::DEFAULT_EXCLUDE_CUSTOMIZED_PRODUCTS),
            'automatic_rejection_enabled' => (bool) \Configuration::get(OrderWithdrawlConfig::AUTO_REJECT_EXPIRED, null, null, null, OrderWithdrawlConfig::DEFAULT_AUTO_REJECT_EXPIRED),
            'button_font_color' => $module->getConfiguredButtonFontColor(),
            'link_color' => $module->getConfiguredLinkColor(),
            'button_classes' => $buttonClasses,
            'withdrawl_mode' => \Configuration::get('ORDERWITHDRAWL_MODE', null, null, null, 'period'),
            'button_size' => \Configuration::get('ORDERWITHDRAWL_BUTTON_SIZE', null, null, null, 'medium'),
            'button_align' => \Configuration::get('ORDERWITHDRAWL_BUTTON_ALIGN', null, null, null, 'center'),
            'languages' => $languages,
            'id_lang_default' => (int) $context->language->id,
            'button_color' => $module->getConfiguredButtonColor(),
            'confirmation_message' => $confirmation_message,
            'error_message' => $error_message,
            'module_dir' => $module->getPathUri(),
            'module_version' => (string) $module->version,
            'is_ps_9' => $isPs9,
            // UX-A-M1: mail preview dropdown feed.
            'mail_preview_templates' => MailPreviewRenderer::ALLOWED_TEMPLATES,
            'mail_preview_languages' => $this->getMailPreviewLanguages($languages),
            'mail_preview_endpoint_base' => $adminAjaxUrl,
            'ps_shop_email' => \Configuration::get('PS_SHOP_EMAIL'),
            'php_mail_fallback_enabled' => (bool) \Configuration::get(
                OrderWithdrawlConfig::PHP_MAIL_FALLBACK,
                null,
                null,
                null,
                OrderWithdrawlConfig::DEFAULT_PHP_MAIL_FALLBACK
            ),
        ]);

        return $module->displayConfigureTemplate('views/templates/admin/configure.tpl');
    }

    /**
     * Dispatch back-office AJAX / reset actions that run inside render().
     *
     * Supported actions (all guarded against CSRF via the PS admin token):
     *   - ?orderwithdrawl_action=preview_mail   => renders a single mail template.
     *   - ?orderwithdrawl_action=send_test_mail => sends a sample mail to the admin address.
     *   - submitOrderWithdrawlReset (form POST) => resets settings to defaults and redisplays.
     *
     * Preview / send-test handlers die() at the end to keep the response clean.
     * The reset handler simply populates a confirmation message and lets the
     * normal render() flow render the page with fresh values.
     */
    public function handleAdminAsyncActions()
    {
        $action = \Tools::getValue('orderwithdrawl_action', '');
        if ($action === 'preview_mail') {
            $this->ajaxProcessPreviewMail();

            return;
        }

        if ($action === 'send_test_mail') {
            $this->ajaxProcessSendTestMail();

            return;
        }

        if ($action === 'search_excluded_products') {
            $this->ajaxProcessSearchExcludedProducts();

            return;
        }

        if (\Tools::isSubmit('submitOrderWithdrawlReset')) {
            $this->handleResetToDefaults();
        }
    }

    /**
     * AJAX endpoint: render the HTML body of one mail template with sample
     * data so the admin can eyeball the markup before the first send.
     *
     * Responds with `text/html` and exits. Returns an empty body + 400 code
     * on validation failure.
     */
    public function ajaxProcessPreviewMail()
    {
        $this->enforceAdminAjaxAuthorisation();

        $template = $this->getMailTemplateFromRequest();
        $iso = $this->getMailIsoFromRequest();

        if ($template === '') {
            $this->sendPlainResponse(
                'text/html; charset=utf-8',
                '<p>' . $this->module->l('Unknown mail template.') . '</p>',
                400
            );
        }

        $idLang = $this->getMailPreviewLanguageId($iso);
        $html = $this->mailPreview->render(
            $template,
            $iso,
            $this->buildMailPreviewSampleVars($template, $idLang)
        );

        if ($html === '') {
            $this->sendPlainResponse(
                'text/html; charset=utf-8',
                '<p>' . $this->module->l('The requested language does not ship with this template yet.') . '</p>',
                404
            );
        }

        // Strip any stray script/iframe tags so the preview iframe itself
        // cannot be hijacked; the on-disk templates are trusted, but we
        // defence-in-depth here since the admin may paste custom placeholder
        // content in the future.
        $html = preg_replace('#<script\b[^>]*>.*?</script>#si', '', $html);
        $html = preg_replace('#<iframe\b[^>]*>.*?</iframe>#si', '', $html);

        $this->sendPlainResponse('text/html; charset=utf-8', (string) $html, 200);
    }

    /**
     * AJAX endpoint: send a sample mail to the admin address(es) using the
     * shared MailDispatcher so any routing / whitelist problems surface
     * identically to the real flow.
     *
     * Responds with JSON: `{success: bool, message: string}`.
     */
    public function ajaxProcessSendTestMail()
    {
        $this->enforceAdminAjaxAuthorisation();

        $ok = false;
        $recipient = '';

        try {
            $template = $this->getMailTemplateFromRequest();
            $iso = $this->getMailIsoFromRequest();

            if ($template === '') {
                $this->sendJsonResponse(['success' => false, 'message' => $this->module->l('Unknown mail template.')], 400);
            }

            $idLang = $this->getMailPreviewLanguageId($iso);

            $recipient = (string) \Configuration::get('PS_SHOP_EMAIL');
            if (!\Validate::isEmail($recipient)) {
                $this->sendJsonResponse(
                    ['success' => false, 'message' => $this->module->l('Shop email is not configured; cannot send a test mail.')],
                    500
                );
            }

            $vars = $this->buildMailPreviewSampleVars($template, $idLang);
            $subject = $this->module->l('[Preview] Order withdrawal test mail') . ' - ' . $template;

            $ok = $this->mailDispatcher->send($template, $idLang, $vars, $recipient, $subject);
        } catch (\Throwable $e) {
            $this->sendJsonResponse(
                ['success' => false, 'message' => $this->module->l('Mail dispatch failed. Check the shop logs for details.')],
                500
            );
        }

        if (!$ok) {
            $this->sendJsonResponse(
                ['success' => false, 'message' => $this->module->l('Mail::Send returned false; check the SMTP configuration.')],
                500
            );
        }

        $this->sendJsonResponse([
            'success' => true,
            'message' => sprintf($this->module->l('Test mail sent to %s.'), $recipient),
        ], 200);
    }

    /**
     * AJAX endpoint: find products that can be added to the exclusion list.
     */
    public function ajaxProcessSearchExcludedProducts()
    {
        $this->enforceAdminAjaxAuthorisation();

        $query = trim((string) \Tools::getValue('q', ''));
        if ($query === '' || strlen($query) < 2) {
            $this->sendJsonResponse(['success' => true, 'items' => []], 200);
        }

        $context = $this->module->getContext();
        $idLang = isset($context->language->id) ? (int) $context->language->id : (int) \Configuration::get('PS_LANG_DEFAULT');
        $idShop = isset($context->shop->id) ? (int) $context->shop->id : 0;
        $idQuery = (int) $query;
        $like = '%' . pSQL($query) . '%';
        $shopCondition = $idShop > 0 ? ' AND pl.id_shop = ' . $idShop : '';

        $sql = '
            SELECT p.id_product, p.reference, pl.name
            FROM `' . _DB_PREFIX_ . 'product` p
            INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (pl.id_product = p.id_product AND pl.id_lang = ' . $idLang . $shopCondition . ')
            WHERE pl.name LIKE "' . $like . '"
                OR p.reference LIKE "' . $like . '"
                OR p.id_product = ' . $idQuery . '
            ORDER BY pl.name ASC
            LIMIT 20';

        $rows = \Db::getInstance()->executeS($sql);
        $items = [];
        foreach (is_array($rows) ? $rows : [] as $row) {
            $idProduct = (int) $row['id_product'];
            $name = trim((string) $row['name']);
            $items[] = [
                'id' => $idProduct,
                'label' => '#' . $idProduct . ' ' . ($name !== '' ? $name : $this->module->l('Product not found')),
                'reference' => (string) $row['reference'],
            ];
        }

        $this->sendJsonResponse(['success' => true, 'items' => $items], 200);
    }

    /**
     * Build preview/test-mail placeholders from neutral sample data plus the
     * configured decision text for approved/rejected templates.
     *
     * @return array<string, mixed>
     */
    private function buildMailPreviewSampleVars(string $template, int $idLang): array
    {
        $vars = $this->mailPreview->getDefaultSampleVars($template, (string) \Language::getIsoById($idLang));
        $statusMessage = $this->getDecisionStatusMessageForTemplate($template, $idLang);

        if ($statusMessage !== null) {
            $vars['{status_message}'] = $statusMessage;
        }

        return $vars;
    }

    private function getMailTemplateFromRequest(): string
    {
        return $this->normalizeMailTemplate((string) \Tools::getValue('template', ''));
    }

    private function normalizeMailTemplate(string $template): string
    {
        switch ($template) {
            case 'withdrawl_confirm':
                return 'withdrawl_confirm';
            case 'withdrawl_admin':
                return 'withdrawl_admin';
            case 'withdrawl_approved':
                return 'withdrawl_approved';
            case 'withdrawl_rejected':
                return 'withdrawl_rejected';
        }

        return '';
    }

    private function getMailIsoFromRequest(): string
    {
        $iso = $this->normalizeMailIso((string) \Tools::getValue('iso', ''));
        if ($iso !== '') {
            return $iso;
        }

        $defaultIso = $this->normalizeMailIso((string) \Language::getIsoById((int) \Configuration::get('PS_LANG_DEFAULT')));

        return $defaultIso !== '' ? $defaultIso : 'de';
    }

    private function normalizeMailIso(string $iso): string
    {
        switch (strtolower(trim($iso))) {
            case 'de':
                return 'de';
            case 'en':
                return 'en';
            case 'es':
                return 'es';
            case 'fr':
                return 'fr';
            case 'it':
                return 'it';
            case 'nl':
                return 'nl';
            case 'pl':
                return 'pl';
            case 'ro':
                return 'ro';
        }

        return '';
    }

    private function getMailPreviewLanguageId(string $iso): int
    {
        $idLang = (int) \Language::getIdByIso($iso);
        if ($idLang <= 0) {
            $idLang = (int) \Configuration::get('PS_LANG_DEFAULT');
        }

        return $idLang > 0 ? $idLang : 1;
    }

    private function getDecisionStatusMessageForTemplate(string $template, int $idLang)
    {
        $key = null;
        if ($template === 'withdrawl_approved') {
            $key = OrderWithdrawlConfig::DECISION_APPROVED;
        } elseif ($template === 'withdrawl_rejected') {
            $key = OrderWithdrawlConfig::DECISION_REJECTED;
        }

        if ($key === null) {
            return null;
        }

        $statusMessage = $this->settings->get($key, $idLang);
        if ($statusMessage !== false && $statusMessage !== '') {
            return (string) $statusMessage;
        }

        $iso = (string) \Language::getIsoById($idLang);
        if ($template === 'withdrawl_approved') {
            return (string) $this->module->getDefaultDecisionApprovedByIso($iso);
        }

        return (string) $this->module->getDefaultDecisionRejectedByIso($iso);
    }

    /**
     * Reset all admin-configurable keys to the installer defaults. Routed
     * through {@see SettingsService::resetToDefaults} so the concrete default
     * list lives in the service layer (B2 territory) instead of being
     * duplicated here.
     */
    private function handleResetToDefaults()
    {
        // Minimal double-opt-in: require the hidden confirm flag that the
        // confirm modal sets to the sentinel value.
        $confirm = (string) \Tools::getValue('orderwithdrawl_reset_confirm', '');
        if ($confirm !== 'yes') {
            return;
        }

        $ok = $this->settings->resetToDefaults();

        // Piggy-back on the existing confirmation/error slots rendered by
        // configure.tpl. render() is about to render anyway.
        if ($ok) {
            $_POST['__orderwithdrawl_reset_notice'] = 'ok';
        } else {
            $_POST['__orderwithdrawl_reset_notice'] = 'fail';
        }
    }

    private function enforceAdminAjaxAuthorisation()
    {
        // Employees with module-configure access already cleared Prestashop's
        // admin-token gate to reach render(). We double-check here so a direct
        // POST to this URL without a valid employee cookie is rejected even
        // if the admin tab leaks the action string somehow.
        $context = $this->module->getContext();
        if (!$context->employee || !$context->employee->id) {
            $this->sendJsonResponse(['success' => false, 'message' => 'Forbidden'], 403);
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function sendJsonResponse(array $payload, int $httpStatus = 200)
    {
        if (!headers_sent()) {
            http_response_code($httpStatus);
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
        }
        echo json_encode($payload);
        exit;
    }

    private function sendPlainResponse(string $contentType, string $body, int $httpStatus = 200)
    {
        if (!headers_sent()) {
            http_response_code($httpStatus);
            header('Content-Type: ' . $contentType);
            header('X-Content-Type-Options: nosniff');
        }
        echo $body;
        exit;
    }

    /**
     * Filter the shop's language list down to those the module actually
     * ships mail templates for (UX-A-M1). Keeps the preview dropdown honest.
     *
     * @param array<int, array{id_lang?: int|string, iso_code?: string, name?: string}> $languages
     *
     * @return array<int, array{id_lang: int, iso_code: string, name: string}>
     */
    private function getMailPreviewLanguages(array $languages): array
    {
        $supported = MailPreviewRenderer::getAllowedIsoCodes();
        $filtered = [];
        foreach ($languages as $language) {
            $iso = \Tools::strtolower((string) ($language['iso_code'] ?? ''));
            if (!in_array($iso, $supported, true)) {
                continue;
            }
            $filtered[] = [
                'id_lang' => (int) ($language['id_lang'] ?? 0),
                'iso_code' => $iso,
                'name' => isset($language['name']) ? (string) $language['name'] : \Tools::strtoupper($iso),
            ];
        }

        return $filtered;
    }

    /**
     * Human-readable label for the evaluation mode (CQ-MN3).
     *
     * Keeps the Smarty template free of string comparisons so the TPL only
     * outputs pre-computed, translator-ready strings.
     *
     * @param string $mode Either `manual` or `period` (anything else is
     *                     treated as `period`)
     */
    private function getWithdrawalModeLabel(string $mode): string
    {
        if ($mode === 'manual') {
            return $this->module->l('Manual review');
        }

        return $this->module->l('Legal period');
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    private function getOrderAssignmentModeMap(): array
    {
        return [
            'name_reference' => ['name', 'reference'],
            'name_order_id' => ['name', 'order_id'],
            'name_both' => ['name', 'both'],
            'email_both' => ['email', 'both'],
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function parseOrderAssignmentMode(string $mode): array
    {
        $map = $this->getOrderAssignmentModeMap();

        return $map[$mode] ?? $map['name_reference'];
    }

    private function buildOrderAssignmentMode(string $customerVerificationMode, string $orderIdentifierMode): string
    {
        if (!in_array($customerVerificationMode, OrderWithdrawlConfig::ALLOWED_CUSTOMER_VERIFICATION_MODES, true)) {
            $customerVerificationMode = OrderWithdrawlConfig::DEFAULT_CUSTOMER_VERIFICATION_MODE;
        }
        $orderIdentifierMode = OrderIdentifier::normalizeMode($orderIdentifierMode);

        if ($customerVerificationMode === 'email') {
            return 'email_both';
        }

        foreach ($this->getOrderAssignmentModeMap() as $mode => $pair) {
            if ($pair[0] === $customerVerificationMode && $pair[1] === $orderIdentifierMode) {
                return $mode;
            }
        }

        return 'name_reference';
    }

    private function getOrderAssignmentModeLabel(string $customerVerificationMode, string $orderIdentifierMode): string
    {
        switch ($this->buildOrderAssignmentMode($customerVerificationMode, $orderIdentifierMode)) {
            case 'name_order_id':
                return $this->module->l('First and last name + order ID');
            case 'name_both':
                return $this->module->l('First and last name + order ID or order number');
            case 'email_both':
                return $this->module->l('Email + order ID or order number (legacy method, not recommended)');
            case 'name_reference':
            default:
                return $this->module->l('First and last name + order number (recommended)');
        }
    }

    /**
     * Pre-compute the snapshot label for the withdrawal period start (CQ-MN3).
     */
    private function getWithdrawalPeriodStartLabel(string $periodStartMode, int $totalWindowDays): string
    {
        if ($periodStartMode === 'shipping_status') {
            return $this->module->l('Legal period from shipping status');
        }

        return $this->module->l('Total withdrawal window');
    }

    /**
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, int>
     */
    private function getStoredPrivacyPages(array $languages): array
    {
        $values = [];
        foreach ($languages as $language) {
            $idLang = (int) $language['id_lang'];
            $stored = \Configuration::get('ORDERWITHDRAWL_PRIVACY_PAGE', $idLang);
            $values[$idLang] = $stored !== false ? (int) $stored : 0;
        }

        return $values;
    }

    /**
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, string>
     */
    private function getStoredPrivacyNotes(array $languages): array
    {
        $values = [];
        foreach ($languages as $language) {
            $idLang = (int) $language['id_lang'];
            $stored = \Configuration::get('ORDERWITHDRAWL_PRIVACY_NOTE', $idLang);
            if ($stored === false || $stored === '') {
                $stored = $this->module->getDefaultPrivacyNoteByIso((string) $language['iso_code']);
            }
            $values[$idLang] = (string) $stored;
        }

        return $values;
    }

    /**
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, int>
     */
    private function getStoredWithdrawalPages(array $languages): array
    {
        $values = [];
        foreach ($languages as $language) {
            $idLang = (int) $language['id_lang'];
            $stored = \Configuration::get('ORDERWITHDRAWL_WITHDRAWAL_PAGE', $idLang);
            $values[$idLang] = $stored !== false ? (int) $stored : 0;
        }

        return $values;
    }

    /**
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, string>
     */
    private function getStoredWithdrawalNotes(array $languages): array
    {
        $values = [];
        foreach ($languages as $language) {
            $idLang = (int) $language['id_lang'];
            $stored = \Configuration::get('ORDERWITHDRAWL_WITHDRAWAL_NOTE', $idLang);
            if ($stored === false || $stored === '') {
                $stored = $this->module->getDefaultWithdrawalNoteByIso((string) $language['iso_code']);
            }
            $values[$idLang] = (string) $stored;
        }

        return $values;
    }

    /**
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, string>
     */
    private function getStoredDecisionApprovedNotes(array $languages): array
    {
        $values = [];
        foreach ($languages as $language) {
            $idLang = (int) $language['id_lang'];
            $stored = \Configuration::get('ORDERWITHDRAWL_DECISION_APPROVED', $idLang);
            if ($stored === false || $stored === '') {
                $stored = $this->module->getDefaultDecisionApprovedByIso((string) $language['iso_code']);
            }
            $values[$idLang] = (string) $stored;
        }

        return $values;
    }

    /**
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, string>
     */
    private function getStoredDecisionRejectedNotes(array $languages): array
    {
        $values = [];
        foreach ($languages as $language) {
            $idLang = (int) $language['id_lang'];
            $stored = \Configuration::get('ORDERWITHDRAWL_DECISION_REJECTED', $idLang);
            if ($stored === false || $stored === '') {
                $stored = $this->module->getDefaultDecisionRejectedByIso((string) $language['iso_code']);
            }
            $values[$idLang] = (string) $stored;
        }

        return $values;
    }

    /**
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, string>
     */
    private function getDefaultPrivacyNotePlaceholders(array $languages): array
    {
        $placeholders = [];
        foreach ($languages as $language) {
            $placeholders[(int) $language['id_lang']] = $this->module->getDefaultPrivacyNoteByIso((string) $language['iso_code']);
        }

        return $placeholders;
    }

    /**
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, string>
     */
    private function getDefaultWithdrawalNotePlaceholders(array $languages): array
    {
        $placeholders = [];
        foreach ($languages as $language) {
            $placeholders[(int) $language['id_lang']] = $this->module->getDefaultWithdrawalNoteByIso((string) $language['iso_code']);
        }

        return $placeholders;
    }

    /**
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, string>
     */
    private function getDefaultDecisionApprovedPlaceholders(array $languages): array
    {
        $placeholders = [];
        foreach ($languages as $language) {
            $placeholders[(int) $language['id_lang']] = $this->module->getDefaultDecisionApprovedByIso((string) $language['iso_code']);
        }

        return $placeholders;
    }

    /**
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, string>
     */
    private function getDefaultDecisionRejectedPlaceholders(array $languages): array
    {
        $placeholders = [];
        foreach ($languages as $language) {
            $placeholders[(int) $language['id_lang']] = $this->module->getDefaultDecisionRejectedByIso((string) $language['iso_code']);
        }

        return $placeholders;
    }

    /**
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, string>
     */
    private function getDefaultButtonLabelPlaceholders(array $languages): array
    {
        $placeholders = [];
        foreach ($languages as $language) {
            $placeholders[(int) $language['id_lang']] = $this->module->getDefaultButtonLabelByIso((string) $language['iso_code']);
        }

        return $placeholders;
    }

    /**
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, string>
     */
    private function getButtonLabelValues(array $languages): array
    {
        $values = [];
        foreach ($languages as $language) {
            $idLang = (int) $language['id_lang'];
            $value = \Configuration::get('ORDERWITHDRAWL_BUTTON_LABEL', $idLang);
            if ($value === false || $value === '') {
                $value = $this->module->getDefaultButtonLabelByIso((string) $language['iso_code']);
            }
            $values[$idLang] = $value;
        }

        return $values;
    }

    /**
     * Build the CMS-page dropdown feed for the admin form.
     *
     * @param array<int, array{id_lang: int|string, iso_code: string}> $languages
     *
     * @return array<int, array<int, array{id_cms: int, meta_title: string}>>
     */
    private function getCmsPagesByLanguage(array $languages): array
    {
        $pages = [];

        foreach ($languages as $language) {
            $idLang = (int) $language['id_lang'];
            $pages[$idLang] = [];
            $cmsList = \CMS::listCms($idLang, false, true);
            if (!is_array($cmsList)) {
                continue;
            }
            foreach ($cmsList as $cms) {
                $pages[$idLang][] = [
                    'id_cms' => (int) $cms['id_cms'],
                    'meta_title' => isset($cms['meta_title']) ? (string) $cms['meta_title'] : '',
                ];
            }
        }

        return $pages;
    }

    /**
     * @return list<array{id: int, label: string, reference: string}>
     */
    private function resolveExcludedProductChoices(string $csv): array
    {
        $ids = $this->parseIdCsv($csv);
        if (!$ids) {
            return [];
        }

        $context = $this->module->getContext();
        $idLang = isset($context->language->id) ? (int) $context->language->id : (int) \Configuration::get('PS_LANG_DEFAULT');
        $idShop = isset($context->shop->id) ? (int) $context->shop->id : 0;
        $shopCondition = $idShop > 0 ? ' AND pl.id_shop = ' . $idShop : '';

        $sql = '
            SELECT p.id_product, p.reference, pl.name
            FROM `' . _DB_PREFIX_ . 'product` p
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (pl.id_product = p.id_product AND pl.id_lang = ' . $idLang . $shopCondition . ')
            WHERE p.id_product IN (' . implode(',', array_map('intval', $ids)) . ')';

        $rows = \Db::getInstance()->executeS($sql);
        $byId = [];
        foreach (is_array($rows) ? $rows : [] as $row) {
            $id = (int) $row['id_product'];
            $name = trim((string) $row['name']);
            $byId[$id] = [
                'id' => $id,
                'label' => '#' . $id . ' ' . ($name !== '' ? $name : $this->module->l('Product not found')),
                'reference' => (string) $row['reference'],
            ];
        }

        $choices = [];
        foreach ($ids as $id) {
            $choices[] = $byId[$id] ?? [
                'id' => $id,
                'label' => '#' . $id . ' ' . $this->module->l('Product not found'),
                'reference' => '',
            ];
        }

        return $choices;
    }

    /**
     * @return list<array{id: int, label: string, name: string}>
     */
    private function resolveExcludedCategoryChoices(string $csv): array
    {
        $ids = $this->parseIdCsv($csv);
        if (!$ids) {
            return [];
        }

        $context = $this->module->getContext();
        $idLang = isset($context->language->id) ? (int) $context->language->id : (int) \Configuration::get('PS_LANG_DEFAULT');
        $idShop = isset($context->shop->id) ? (int) $context->shop->id : 0;
        $shopCondition = $idShop > 0 ? ' AND cl.id_shop = ' . $idShop : '';

        $sql = '
            SELECT c.id_category, cl.name
            FROM `' . _DB_PREFIX_ . 'category` c
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
                ON (cl.id_category = c.id_category AND cl.id_lang = ' . $idLang . $shopCondition . ')
            WHERE c.id_category IN (' . implode(',', array_map('intval', $ids)) . ')';

        $rows = \Db::getInstance()->executeS($sql);
        $byId = [];
        foreach (is_array($rows) ? $rows : [] as $row) {
            $id = (int) $row['id_category'];
            $name = trim((string) $row['name']);
            $byId[$id] = [
                'id' => $id,
                'label' => '#' . $id . ' ' . ($name !== '' ? $name : $this->module->l('Category not found')),
                'name' => $name,
            ];
        }

        $choices = [];
        foreach ($ids as $id) {
            $choices[] = $byId[$id] ?? [
                'id' => $id,
                'label' => '#' . $id . ' ' . $this->module->l('Category not found'),
                'name' => '',
            ];
        }

        return $choices;
    }

    /**
     * @return list<array{id: int, label: string, level: int, selected: bool}>
     */
    private function buildCategoryExclusionTree(string $selectedCsv): array
    {
        $selected = array_flip($this->parseIdCsv($selectedCsv));
        $context = $this->module->getContext();
        $idLang = isset($context->language->id) ? (int) $context->language->id : (int) \Configuration::get('PS_LANG_DEFAULT');
        $idShop = isset($context->shop->id) ? (int) $context->shop->id : 0;
        $shopCondition = $idShop > 0 ? ' AND cl.id_shop = ' . $idShop : '';

        $sql = '
            SELECT c.id_category, c.level_depth, c.nleft, cl.name
            FROM `' . _DB_PREFIX_ . 'category` c
            INNER JOIN `' . _DB_PREFIX_ . 'category_lang` cl
                ON (cl.id_category = c.id_category AND cl.id_lang = ' . $idLang . $shopCondition . ')
            WHERE c.active = 1
            ORDER BY c.nleft ASC, cl.name ASC';

        $rows = \Db::getInstance()->executeS($sql);
        $tree = [];
        foreach (is_array($rows) ? $rows : [] as $row) {
            $id = (int) $row['id_category'];
            $name = trim((string) $row['name']);
            if ($id <= 0 || $name === '') {
                continue;
            }
            $tree[] = [
                'id' => $id,
                'label' => '#' . $id . ' ' . $name,
                'level' => max(0, (int) $row['level_depth'] - 1),
                'selected' => isset($selected[$id]),
            ];
        }

        return $tree;
    }

    /**
     * Verify that the submitted CMS page id is active. Zero means "no page"
     * and is always considered valid.
     */
    private function isCmsPagePublished(int $idCms): bool
    {
        if ($idCms <= 0) {
            return true;
        }

        $context = $this->module->getContext();
        $cms = new \CMS($idCms, (int) $context->language->id, (int) $context->shop->id);

        return \Validate::isLoadedObject($cms) && (bool) $cms->active;
    }

    /**
     * Normalise the raw POST input for the display-location checkbox group.
     *
     * @param mixed $input
     *
     * @return list<string>
     */
    private function normalizeDisplayLocationInput($input): array
    {
        if ($input === null || $input === '') {
            return [];
        }

        if (!is_array($input)) {
            $input = [$input];
        }

        return $this->module->collectDisplayLocationValues($input);
    }

    /**
     * @param mixed $input
     */
    private function normalizeIdListInput($input): string
    {
        if (is_array($input)) {
            $input = implode(',', $input);
        }

        $ids = [];
        foreach (preg_split('/[,\s]+/', (string) $input, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $rawId) {
            $id = (int) $rawId;
            if ($id > 0) {
                $ids[$id] = true;
            }
        }

        return implode(',', array_keys($ids));
    }

    /**
     * @return list<int>
     */
    private function parseIdCsv(string $csv): array
    {
        $ids = [];
        foreach (preg_split('/[,\s]+/', $csv, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $rawId) {
            $id = (int) $rawId;
            if ($id > 0) {
                $ids[$id] = $id;
            }
        }

        return array_values($ids);
    }

    /**
     * Serialise the normalised display-location selection for Configuration
     * persistence (comma-separated). Keeps the storage format in lockstep
     * with {@see OrderWithdrawl::getDisplayLocationSelections()}.
     *
     * @param list<string> $selections
     */
    private function formatDisplayLocationStorage(array $selections): string
    {
        $selections = $this->module->collectDisplayLocationValues($selections);

        return implode(',', $selections);
    }

    /**
     * Flush the neighbouring modules' template caches so a freshly saved
     * display-location change becomes visible without a manual cache clear
     * (notably ps_customeraccountlinks, which renders My-Account entries
     * right next to ours).
     */
    private function clearCustomerAccountCaches()
    {
        $modules = [
            'ps_customeraccountlinks',
        ];

        foreach ($modules as $moduleName) {
            try {
                $instance = \Module::getInstanceByName($moduleName);
                if (!$instance instanceof \Module || !$instance->active) {
                    continue;
                }

                if (is_callable([$instance, '_clearCache'])) {
                    call_user_func([$instance, '_clearCache'], '*');
                }
            } catch (\Throwable $e) {
                if (class_exists('PrestaShopLogger', false)) {
                    \PrestaShopLogger::addLog(
                        'Order withdrawal cache clear failed for module ' . $moduleName,
                        \PrestaShopLogger::LOG_SEVERITY_LEVEL_WARNING,
                        null,
                        'OrderWithdrawl',
                        null
                    );
                }
            }
        }
    }
}
