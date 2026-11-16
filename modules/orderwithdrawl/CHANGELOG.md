# Changelog

## Version 1.4.3 - June 2026

Version 1.4.3 tightens the Front Office wording and international fallback handling.

### Front Office

- The withdrawal form page title now uses the configured button label for the current language.
- This keeps the visible page title aligned with the Back Office button-label setting.

### Translations and Compatibility

- Replaced remaining German source fallback strings in PHP/Smarty translation calls with English source strings.
- Added matching translations for the supported module languages.
- Added `upgrade-1.4.3.php` as a no-op upgrade script because no database schema or configuration migration is required.

## Version 1.4.2 - June 2026

Version 1.4.2 improves the Back Office configuration for product and category exclusions in the product-selection mode.

### Back Office Exclusion Picker

- Replaced raw product/category ID entry with a product search picker, selected product chips and a searchable category tree.
- Kept existing `ORDERWITHDRAWL_EXCLUDED_PRODUCT_IDS` and `ORDERWITHDRAWL_EXCLUDED_CATEGORY_IDS` storage unchanged for backwards compatibility.
- Added an advanced manual ID editor as a fallback for direct ID entry.
- Added translations and regression coverage for the new picker UI.

### Compatibility and Upgrade

- Module version raised to 1.4.2.
- Removed nullable return-type syntax from the main module class so older PrestaShop 1.7.5.x module scanners do not falsely flag the module as a parse error.
- Disabled the custom one-segment pretty module route on PrestaShop 1.7 because `category_rule` can claim `/order-withdrawl` before the module route; 1.7 now uses the native `/module/orderwithdrawl/form` URL for stable redirects and notifications.
- Added `upgrade-1.4.2.php` as a no-op upgrade script because no database schema or configuration migration is required.
- Compatibility target remains PrestaShop 1.7.8, PrestaShop 8 and PrestaShop 9 with PHP 7.4+.

## Version 1.4.1 - June 2026

Version 1.4.1 adds the optional automatic rejection flow for expired withdrawal requests and tightens the front-office required-field handling.

### Automatic Rejection

- Added a Back Office checkbox for automatic deadline-based rejection. The option is disabled by default and marked as "at your own risk".
- When enabled, expired requests are first recorded as received, the receipt/admin mails are sent, and the order is then moved to the configured "Withdrawal request rejected" status.
- The existing `withdrawl_rejected` decision mail is used for the customer rejection notice.
- Admin notification mails and private order notes now include a translated notice that the normal order process can continue after receipt of the withdrawal.

### Form and Translations

- First name, last name and confirmation email are visibly marked as required fields.
- Missing first name, last name and email now produce separate server-side error messages.
- Added German, English, Spanish, French, Italian, Dutch, Polish and Romanian translations for the new texts.

### Compatibility and Upgrade

- Module version raised to 1.4.1.
- Added `upgrade-1.4.1.php` to initialise automatic rejection as disabled while preserving existing configurations.
- No database schema change is required.

## Version 1.4.0 - June 2026

Version 1.4.0 adds an optional product-selection mode for partial withdrawals. The mode is disabled by default so existing shops keep the stable 1.3.x flow unless merchants explicitly enable the new behavior in the Back Office.

### Product Selection Mode

- Added a Back Office option to show order products after the customer details have been verified.
- The public form now supports a server-side two-step flow without AJAX: first check the order details, then select the full order or individual order items.
- Partial withdrawals can be submitted by selecting order items and a valid quantity instead of manually entering item data.
- Product data is loaded only after successful order assignment and customer verification.
- Final submissions reload the order products server-side and ignore forged product names or references from the browser.

### Compatibility and Upgrade

- Module version raised to 1.4.0.
- Added `upgrade-1.4.0.php` to initialise the product-selection setting as disabled while preserving existing configurations.
- No database schema change is required.
- Compatibility target remains PrestaShop 1.7.8, PrestaShop 8 and PrestaShop 9 with PHP 7.4+.

## Version 1.3.1 - May 2026

Version 1.3.1 adds structured support for full and partial withdrawal requests while preserving the stability and compatibility goals of the 1.3.x release line.

### Structured Partial Withdrawal

- Added a withdrawal scope selection to the public form: entire order or selected items/services.
- Added repeatable item rows for partial withdrawals with item name, item number and quantity.
- No withdrawal reason is requested; the new fields are used only to identify the contract or contract part.
- Partial submissions require at least an item name or item number. Blank rows are ignored.
- Input is sanitized, length-limited and validated before any order state change or email dispatch.

### Emails and Back Office Review

- Customer and admin receipt emails now include the withdrawal scope.
- Receipt emails include submitted item/service details for partial withdrawals.
- Partial-withdrawal details are added to order messages for Back Office review.
- Mail preview/sample variables were updated for the new placeholders.
- Added optional custom CTA hook `displayOrderWithdrawlCta` for theme-level placement.

### Compatibility and Upgrade

- Module version raised to 1.3.1.
- Added `upgrade-1.3.1.php` to register the custom CTA hook while preserving existing settings.
- No database schema change is required.
- Compatibility target remains PrestaShop 1.7.8, PrestaShop 8 and PrestaShop 9 with PHP 7.4+.

## Version 1.3.0 - May 2026

Version 1.3.0 is a major stability, compliance and compatibility release for the Order Withdrawal module. It consolidated earlier internal 1.3.x work into one public release and is intended as the direct upgrade path for customers running version 1.2.4.

### Compatibility and Upgrade

- Direct upgrade path from version 1.2.4 to version 1.3.0.
- Earlier internal 1.3.x development work was consolidated into the public 1.3.0 release.
- No database schema change is required.
- Existing merchant settings are preserved during the upgrade.
- New configuration defaults are only written when the corresponding setting does not already exist.
- Compatibility focus for PrestaShop 1.7.8, PrestaShop 8 and PrestaShop 9.
- PHP 7.4+ support is preserved for PrestaShop 1.7.8 installations.

### Legal and Withdrawal Flow

- The public withdrawal flow now uses first and last name plus order number as the recommended verification method.
- The Back Office can switch between:
  - first and last name plus order number, recommended,
  - first and last name plus order ID,
  - first and last name plus order ID or order number,
  - email plus order ID or order number (older method, not recommended).
- Fresh installations now default to manual review.
- In manual review mode, withdrawal requests are recorded first and can then be assessed in the Back Office.
- In period mode, requests after the calculated deadline are no longer blocked. They are recorded, notification emails are sent and the order receives a manual-review notice.
- If the automatic deadline check indicates that the deadline may have expired, the customer sees a cautious success notice that the withdrawal declaration was still submitted and will be reviewed by the merchant.
- Customer receipt emails now confirm receipt of a withdrawal request and list the submitted details instead of implying a final merchant decision.
- The public form no longer displays the withdrawal policy details block or a fixed 14-day period notice directly; legal withdrawal and privacy information remain configurable through the module settings and CMS/legal content.
- The Front Office withdrawal page title now uses the legal withdrawal wording "Withdraw from contract" / "Vertrag widerrufen".

### Back Office

- Merged customer verification and order lookup into one "Order assignment details" setting.
- Documentation buttons now link to the hosted GetApps4Shops attachment downloads per language instead of local PDF files.
- Added PHP mail fallback setting.
- Added mail template preview in the module configuration.
- Added test mail sending from the module configuration.
- Added reset-to-defaults action for module settings.
- Improved Back Office help texts and tooltip placement.
- Improved required-field markers and form validation behavior.

### Email and Notifications

- Added a shared MailDispatcher for all module emails.
- Added SMTP/sendmail diagnostics when PrestaShop mail sending fails.
- Added optional PHP mail() fallback when PrestaShop Mail::Send returns an error.
- Completed localized email templates and translations for all shipped languages.
- Admin withdrawal notification templates are now available as HTML and plain text in all shipped locales.
- Customer and admin notification templates now support a unified order identifier placeholder.
- The submitted first and last name is included in confirmation emails.
- Decision emails use the shared mail handling logic for more consistent behavior.

### Security and Stability

- Added CSRF protection to the public withdrawal form.
- Added honeypot protection against automated submissions.
- Added IP-based rate limiting for public submissions.
- Added duplicate submission prevention before order state changes.
- Customer notes are sanitized and HTML-encoded before being inserted into HTML email templates.
- Admin notification recipients are sanitized before validation.
- Mail template path resolution is restricted to known language folders.
- Sensitive module folders are protected with deny-all `.htaccess` guards.
- Order state transitions are dispatched only once per withdrawal submission.

### PrestaShop Integration

- Improved compatibility between PrestaShop 8 and PrestaShop 9.
- Improved multishop configuration handling by reading module settings in shop context.
- Improved mail template variable handling by filtering only module-specific templates before loading order data.
- `Tools::getContextLocale` support is detected safely for different PrestaShop versions.

### Code Quality

- Added a PSR-4 service layer under `classes/`.
- Extracted configuration constants and defaults into a dedicated config class.
- Added dedicated helpers for security, mail dispatching, rate limiting, settings and repositories.
- Improved null-safe order and customer lookups.
- Added and extended automated regression tests for upgrade behavior, mail templates, hooks, duplicate submissions, localization coverage and front-office layout.
