<?php
/**
 * Upgrade script for orderwithdrawl v1.3.0
 *
 * 1.3.0 is the "bugfix-welle" release. Code/UX/i18n/security fixes only;
 * no database schema changes. Key deltas for shops upgrading from 1.2.x:
 *
 * - New PSR-4 service layer under `classes/` (MailDispatcher, SecurityGuard,
 *   SettingsService, RateLimiter, OrderWithdrawlConfig, repositories,
 *   PolicyRenderer, MailPreviewRenderer, OrderWithdrawlMailException).
 *   Loaded via `composer.json` autoload; nothing to migrate server-side if
 *   `composer install --no-dev` has been executed during deployment.
 * - Withdrawal form now issues a CSRF token, honeypot and IP-rate-limit.
 *   Runtime-only - no DB tables added.
 * - withdrawl_admin.html/.txt shipped for ALL seven locales (previously only
 *   de/en). Existing shops with customised templates under themes/<theme>/
 *   mails/<iso>/modules/orderwithdrawl/ are unaffected.
 * - Customer note is now HTML-encoded via SecurityGuard before hitting any
 *   mail template. Placeholder `{customer_note_html}` is the HTML-safe variant;
 *   `{customer_note}` remains the plain-text one.
 * - Hook `actionGetExtraMailTemplateVars` now filters on module templates
 *   before loading Order, avoiding needless work on every shop mail.
 * - OrderHistory state transitions are no longer double-dispatched.
 * - Admin config page gained mail preview, test-send and reset-to-defaults.
 * - translations/en.php added as explicit fallback locale.
 * - PHP 8.5 compatibility: implicit-nullable deprecations resolved.
 *
 * This is also the first public 1.3.x release. Customers were only shipped up
 * to 1.2.4, so configuration defaults introduced during internal 1.3.x work
 * are consolidated here. Existing merchant choices are never overwritten.
 *
 * @author    sjmedia GmbH
 * @copyright 2007-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_0($module)
{
    unset($module);

    $ok = true;

    if (!Configuration::hasKey('ORDERWITHDRAWL_MODE') && !Configuration::updateValue('ORDERWITHDRAWL_MODE', 'manual')) {
        $ok = false;
    }

    if (!Configuration::hasKey('ORDERWITHDRAWL_ORDER_IDENTIFIER_MODE') && !Configuration::updateValue('ORDERWITHDRAWL_ORDER_IDENTIFIER_MODE', 'reference')) {
        $ok = false;
    }

    if (!Configuration::hasKey('ORDERWITHDRAWL_CUSTOMER_VERIFICATION_MODE') && !Configuration::updateValue('ORDERWITHDRAWL_CUSTOMER_VERIFICATION_MODE', 'name')) {
        $ok = false;
    }

    if (!Configuration::hasKey('ORDERWITHDRAWL_PHP_MAIL_FALLBACK') && !Configuration::updateValue('ORDERWITHDRAWL_PHP_MAIL_FALLBACK', 1)) {
        $ok = false;
    }

    return $ok;
}
