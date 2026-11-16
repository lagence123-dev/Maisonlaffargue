<?php
/**
 * Central configuration keys and defaults for the orderwithdrawl module.
 *
 * This class consolidates the ~37 magic config string keys that were scattered
 * throughout the module. Every consumer (controllers, services, hooks, tests)
 * must reference the constants here instead of hard-coded strings.
 *
 * @author    sjmedia GmbH
 * @copyright 2024-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace OrderWithdrawl\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class OrderWithdrawlConfig
{
    // === Module identity ======================================================
    public const MODULE_NAME = 'orderwithdrawl';

    // === Withdrawal period ====================================================
    public const WITHDRAWL_PERIOD_DAYS = 'ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS';
    public const WITHDRAWL_DELIVERY_DAYS = 'ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS';
    public const PERIOD_START_MODE = 'ORDERWITHDRAWL_PERIOD_START_MODE';
    public const TRIGGER_STATE_IDS = 'ORDERWITHDRAWL_TRIGGER_STATE_IDS';
    public const MODE = 'ORDERWITHDRAWL_MODE';
    public const AUTO_REJECT_EXPIRED = 'ORDERWITHDRAWL_AUTO_REJECT_EXPIRED';
    public const ORDER_IDENTIFIER_MODE = 'ORDERWITHDRAWL_ORDER_IDENTIFIER_MODE';
    public const CUSTOMER_VERIFICATION_MODE = 'ORDERWITHDRAWL_CUSTOMER_VERIFICATION_MODE';

    // === Mail / admin =========================================================
    public const ADMIN_EMAILS = 'ORDERWITHDRAWL_ADMIN_EMAILS';
    public const PHP_MAIL_FALLBACK = 'ORDERWITHDRAWL_PHP_MAIL_FALLBACK';

    // === Display / UI =========================================================
    public const FOOTER_DISPLAY = 'ORDERWITHDRAWL_FOOTER_DISPLAY';
    public const DISPLAY_LOCATION = 'ORDERWITHDRAWL_DISPLAY_LOCATION';
    public const FOOTER_HOOKS = 'ORDERWITHDRAWL_FOOTER_HOOKS';
    public const BUTTON_LABEL = 'ORDERWITHDRAWL_BUTTON_LABEL';
    public const BUTTON_COLOR = 'ORDERWITHDRAWL_BUTTON_COLOR';
    public const BUTTON_FONT_COLOR = 'ORDERWITHDRAWL_BUTTON_FONT_COLOR';
    public const LINK_COLOR = 'ORDERWITHDRAWL_LINK_COLOR';
    public const BUTTON_CLASSES = 'ORDERWITHDRAWL_BUTTON_CLASSES';
    public const BUTTON_SIZE = 'ORDERWITHDRAWL_BUTTON_SIZE';
    public const BUTTON_ALIGN = 'ORDERWITHDRAWL_BUTTON_ALIGN';
    public const PRODUCT_SELECTION_MODE = 'ORDERWITHDRAWL_PRODUCT_SELECTION_MODE';
    public const EXCLUDED_PRODUCT_IDS = 'ORDERWITHDRAWL_EXCLUDED_PRODUCT_IDS';
    public const EXCLUDED_CATEGORY_IDS = 'ORDERWITHDRAWL_EXCLUDED_CATEGORY_IDS';
    public const EXCLUDE_CUSTOMIZED_PRODUCTS = 'ORDERWITHDRAWL_EXCLUDE_CUSTOMIZED_PRODUCTS';

    // === Customer note ========================================================
    public const ENABLE_CUSTOMER_NOTE = 'ORDERWITHDRAWL_ENABLE_CUSTOMER_NOTE';
    /**
     * Single, canonical maximum length for persisted customer note text.
     *
     * Previously the codebase truncated twice (4000 then 1600). The note is
     * now stored as a message/note on the order and that storage path is the
     * only one the value has to survive, hence the lower bound.
     *
     * @see controllers/front/submit.php
     * @see \OrderWithdrawl\Security\SecurityGuard::encodeCustomerNote
     */
    public const CUSTOMER_NOTE_MAX_LENGTH = 1600;

    // === CMS notices ==========================================================
    public const PRIVACY_PAGE = 'ORDERWITHDRAWL_PRIVACY_PAGE';
    public const PRIVACY_NOTE = 'ORDERWITHDRAWL_PRIVACY_NOTE';
    public const WITHDRAWAL_PAGE = 'ORDERWITHDRAWL_WITHDRAWAL_PAGE';
    public const WITHDRAWAL_NOTE = 'ORDERWITHDRAWL_WITHDRAWAL_NOTE';

    // === Decision status messages ============================================
    public const DECISION_APPROVED = 'ORDERWITHDRAWL_DECISION_APPROVED';
    public const DECISION_REJECTED = 'ORDERWITHDRAWL_DECISION_REJECTED';

    // === Order state ids ======================================================
    public const STATE_ID = 'ORDERWITHDRAWL_STATE_ID';
    public const STATE_APPROVED_ID = 'ORDERWITHDRAWL_STATE_APPROVED_ID';
    public const STATE_REJECTED_ID = 'ORDERWITHDRAWL_STATE_REJECTED_ID';

    // === PrestaShop native keys we read ======================================
    public const PS_LANG_DEFAULT = 'PS_LANG_DEFAULT';
    public const PS_SHOP_EMAIL = 'PS_SHOP_EMAIL';
    public const PS_SHOP_NAME = 'PS_SHOP_NAME';

    // === Defaults ============================================================
    public const DEFAULT_BUTTON_COLOR = '#F44708';
    public const DEFAULT_BUTTON_FONT_COLOR = '#FFFFFF';
    public const DEFAULT_LINK_COLOR = '';
    public const DEFAULT_BUTTON_SIZE = 'medium';
    public const DEFAULT_BUTTON_ALIGN = 'center';
    public const DEFAULT_FOOTER_DISPLAY = 'button';
    public const DEFAULT_FOOTER_HOOK = 'displayFooter';
    public const DEFAULT_DISPLAY_LOCATION = 'footer';
    public const DEFAULT_PERIOD_START_MODE = 'order_date';
    public const DEFAULT_MODE = 'manual';
    public const DEFAULT_AUTO_REJECT_EXPIRED = 0;
    public const DEFAULT_ORDER_IDENTIFIER_MODE = 'reference';
    public const DEFAULT_CUSTOMER_VERIFICATION_MODE = 'name';
    public const DEFAULT_PHP_MAIL_FALLBACK = 1;
    public const DEFAULT_PRODUCT_SELECTION_MODE = 0;
    public const DEFAULT_EXCLUDED_PRODUCT_IDS = '';
    public const DEFAULT_EXCLUDED_CATEGORY_IDS = '';
    public const DEFAULT_EXCLUDE_CUSTOMIZED_PRODUCTS = 0;
    public const DEFAULT_PERIOD_DAYS = 14;
    public const DEFAULT_DELIVERY_DAYS = 0;

    // === Whitelists ==========================================================
    /** @var list<string> */
    public const ALLOWED_BUTTON_SIZES = ['small', 'medium', 'large'];

    /** @var list<string> */
    public const ALLOWED_BUTTON_ALIGNS = ['left', 'center', 'right'];

    /** @var list<string> */
    public const ALLOWED_FOOTER_DISPLAYS = ['button', 'link'];

    /** @var list<string> */
    public const ALLOWED_FOOTER_HOOKS = [
        'displayFooter',
        'displayFooterBefore',
        'displayFooterAfter',
        'displayBeforeBodyClosingTag',
    ];

    /** @var list<string> */
    public const ALLOWED_DISPLAY_LOCATIONS = [
        'footer',
        'account_block',
        'account_page',
        'custom_hook',
    ];

    /** @var list<string> */
    public const ALLOWED_PERIOD_START_MODES = ['order_date', 'shipping_status'];

    /** @var list<string> */
    public const ALLOWED_ORDER_IDENTIFIER_MODES = ['order_id', 'reference', 'both'];

    /** @var list<string> */
    public const ALLOWED_CUSTOMER_VERIFICATION_MODES = ['name', 'email'];

    /** @var list<string> Whitelist of mail-template language folders shipped with the module. */
    public const ALLOWED_LANGUAGE_ISO = ['de', 'en', 'fr', 'it', 'es', 'nl', 'pl', 'ro'];

    // === Rate limit ==========================================================
    /** Max submissions a single IP may perform inside the window. */
    public const RATE_LIMIT_MAX_PER_WINDOW = 5;
    /** Window length in seconds. */
    public const RATE_LIMIT_WINDOW_SECONDS = 60;

    /** Form field names used for CSRF / honeypot. Keep in sync with B5 templates. */
    public const CSRF_TOKEN_FIELD = 'orderwithdrawl_token';
    public const HONEYPOT_FIELD = 'website_url';

    private function __construct()
    {
        // Utility class - not instantiable.
    }
}
