<?php
/**
 * Thin, lazy caching wrapper around PrestaShop's Configuration storage.
 *
 * Intentionally minimal: the module reads the same Configuration keys many
 * times per request. This service keeps a per-instance cache and acts as the
 * single point we swap out in tests to decouple the module from the
 * `Configuration::get` static.
 *
 * Config keys MUST be provided as {@see OrderWithdrawlConfig} constants.
 *
 * @author    sjmedia GmbH
 * @copyright 2024-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace OrderWithdrawl\Settings;

if (!defined('_PS_VERSION_')) {
    exit;
}

use OrderWithdrawl\Config\OrderWithdrawlConfig;

final class SettingsService
{
    /** @var array<string, mixed> */
    private $cache = [];

    /**
     * Default shop id applied to reads when the caller does not pass one
     * explicitly. Hooks SHOULD set this to the current context shop id so
     * Multishop overrides win over global defaults (PS-M1, PS-M2).
     *
     * @var int|null
     */
    private $defaultShopId;

    public function __construct($defaultShopId = null)
    {
        $this->defaultShopId = $defaultShopId;
    }

    /**
     * Update the default shop id used for subsequent reads. Passing null
     * restores global scope. Invalidates the internal cache so the change
     * actually takes effect.
     */
    public function setDefaultShopId($idShop)
    {
        if ($this->defaultShopId === $idShop) {
            return;
        }
        $this->defaultShopId = $idShop;
        $this->cache = [];
    }

    public function getDefaultShopId()
    {
        return $this->defaultShopId;
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, $idLang = null, $idShop = null, $default = false)
    {
        $effectiveShopId = $idShop ?? $this->defaultShopId;
        $cacheKey = $key . ':' . ($idLang ?? 'x') . ':' . ($effectiveShopId ?? 'x');
        if (array_key_exists($cacheKey, $this->cache)) {
            return $this->cache[$cacheKey];
        }
        if (!class_exists('Configuration', false)) {
            return $default;
        }
        $value = \Configuration::get($key, $idLang, null, $effectiveShopId, $default);
        $this->cache[$cacheKey] = $value;

        return $value;
    }

    public function getString(string $key, $idLang = null, string $default = ''): string
    {
        $value = $this->get($key, $idLang, null, $default);
        if ($value === false || $value === null) {
            return $default;
        }

        return (string) $value;
    }

    public function getInt(string $key, $idLang = null, int $default = 0, $idShop = null): int
    {
        $value = $this->get($key, $idLang, $idShop, $default);
        if ($value === false || $value === null || $value === '') {
            return $default;
        }

        return (int) $value;
    }

    public function getBool(string $key, $idLang = null, bool $default = false): bool
    {
        return (bool) $this->get($key, $idLang, null, $default);
    }

    public function invalidate()
    {
        $this->cache = [];
    }

    /**
     * Return the list of configuration keys the module persists, paired with
     * their canonical default value. Consumed by {@see self::resetToDefaults}
     * and by the admin "Reset to defaults" action. The list purposefully
     * omits the order-state ids (those are managed through the installer
     * and should not be wiped by a user-initiated reset).
     *
     * Multi-language keys are represented by `null` for the default and are
     * removed (`Configuration::deleteByName`) rather than re-seeded so the
     * module's `getDefault*ByIso` helpers can repopulate them on next read.
     *
     * @return array<string, mixed>
     */
    public static function getResettableDefaults(): array
    {
        return [
            OrderWithdrawlConfig::WITHDRAWL_PERIOD_DAYS => OrderWithdrawlConfig::DEFAULT_PERIOD_DAYS,
            OrderWithdrawlConfig::WITHDRAWL_DELIVERY_DAYS => OrderWithdrawlConfig::DEFAULT_DELIVERY_DAYS,
            OrderWithdrawlConfig::PERIOD_START_MODE => OrderWithdrawlConfig::DEFAULT_PERIOD_START_MODE,
            OrderWithdrawlConfig::TRIGGER_STATE_IDS => '',
            OrderWithdrawlConfig::MODE => OrderWithdrawlConfig::DEFAULT_MODE,
            OrderWithdrawlConfig::AUTO_REJECT_EXPIRED => OrderWithdrawlConfig::DEFAULT_AUTO_REJECT_EXPIRED,
            OrderWithdrawlConfig::ORDER_IDENTIFIER_MODE => OrderWithdrawlConfig::DEFAULT_ORDER_IDENTIFIER_MODE,
            OrderWithdrawlConfig::CUSTOMER_VERIFICATION_MODE => OrderWithdrawlConfig::DEFAULT_CUSTOMER_VERIFICATION_MODE,
            OrderWithdrawlConfig::ADMIN_EMAILS => '',
            OrderWithdrawlConfig::PHP_MAIL_FALLBACK => OrderWithdrawlConfig::DEFAULT_PHP_MAIL_FALLBACK,
            OrderWithdrawlConfig::FOOTER_DISPLAY => OrderWithdrawlConfig::DEFAULT_FOOTER_DISPLAY,
            OrderWithdrawlConfig::DISPLAY_LOCATION => OrderWithdrawlConfig::DEFAULT_DISPLAY_LOCATION,
            OrderWithdrawlConfig::FOOTER_HOOKS => OrderWithdrawlConfig::DEFAULT_FOOTER_HOOK,
            OrderWithdrawlConfig::BUTTON_CLASSES => '',
            OrderWithdrawlConfig::BUTTON_COLOR => OrderWithdrawlConfig::DEFAULT_BUTTON_COLOR,
            OrderWithdrawlConfig::BUTTON_FONT_COLOR => OrderWithdrawlConfig::DEFAULT_BUTTON_FONT_COLOR,
            OrderWithdrawlConfig::LINK_COLOR => OrderWithdrawlConfig::DEFAULT_LINK_COLOR,
            OrderWithdrawlConfig::BUTTON_SIZE => OrderWithdrawlConfig::DEFAULT_BUTTON_SIZE,
            OrderWithdrawlConfig::BUTTON_ALIGN => OrderWithdrawlConfig::DEFAULT_BUTTON_ALIGN,
            OrderWithdrawlConfig::ENABLE_CUSTOMER_NOTE => 0,
            OrderWithdrawlConfig::PRODUCT_SELECTION_MODE => OrderWithdrawlConfig::DEFAULT_PRODUCT_SELECTION_MODE,
            OrderWithdrawlConfig::EXCLUDED_PRODUCT_IDS => OrderWithdrawlConfig::DEFAULT_EXCLUDED_PRODUCT_IDS,
            OrderWithdrawlConfig::EXCLUDED_CATEGORY_IDS => OrderWithdrawlConfig::DEFAULT_EXCLUDED_CATEGORY_IDS,
            OrderWithdrawlConfig::EXCLUDE_CUSTOMIZED_PRODUCTS => OrderWithdrawlConfig::DEFAULT_EXCLUDE_CUSTOMIZED_PRODUCTS,
        ];
    }

    /**
     * Multi-language keys that should be deleted (not re-written) on reset
     * so the module falls back to its `getDefault*ByIso` helpers.
     *
     * @return list<string>
     */
    public static function getLanguageResetKeys(): array
    {
        return [
            OrderWithdrawlConfig::BUTTON_LABEL,
            OrderWithdrawlConfig::PRIVACY_PAGE,
            OrderWithdrawlConfig::PRIVACY_NOTE,
            OrderWithdrawlConfig::WITHDRAWAL_PAGE,
            OrderWithdrawlConfig::WITHDRAWAL_NOTE,
            OrderWithdrawlConfig::DECISION_APPROVED,
            OrderWithdrawlConfig::DECISION_REJECTED,
        ];
    }

    /**
     * Restore all admin-configurable module settings to their canonical
     * defaults. Order-state ids and the two history tables touched by the
     * installer are intentionally left alone.
     *
     * @return bool `true` if every write succeeded
     */
    public function resetToDefaults(): bool
    {
        if (!class_exists('Configuration', false)) {
            return false;
        }

        $ok = true;
        foreach (self::getResettableDefaults() as $key => $default) {
            $ok = (bool) \Configuration::updateValue($key, $default) && $ok;
        }

        foreach (self::getLanguageResetKeys() as $key) {
            $ok = (bool) \Configuration::deleteByName($key) && $ok;
        }

        $this->invalidate();

        return $ok;
    }
}
