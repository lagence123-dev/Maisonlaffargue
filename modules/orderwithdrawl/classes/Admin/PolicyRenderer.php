<?php
/**
 * Single source of truth for the admin-configured withdrawal policy text
 * that the shop must expose to customers (EU distance-selling directive).
 *
 * The value is stored per language under
 * {@see \OrderWithdrawl\Config\OrderWithdrawlConfig::WITHDRAWAL_NOTE} and
 * has historically been surfaced in two places:
 *   1. The CMS page configured under `ORDERWITHDRAWL_WITHDRAWAL_PAGE` via
 *      the existing `hookDisplayCMSDisputeInformation` path.
 *   2. The withdrawal form (`views/templates/front/form.tpl`) which
 *      expects a Smarty variable named `orderwithdrawl_policy_text`.
 *
 * Only path (1) was actually implemented before Batch 6 (see UX-A-H1 /
 * "Admin-Widerrufsbelehrungsfeld ohne Rendering-Ziel"). This class gives
 * every consumer - the admin preview, the form controller and any future
 * hook - a common helper that returns the resolved policy text with the
 * same fallback chain (stored value -> default by ISO -> empty).
 *
 * Intentionally framework-light so it can be called from either a module
 * context or a standalone test harness.
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
use OrderWithdrawl\Settings\SettingsService;

final class PolicyRenderer
{
    /** @var SettingsService */
    private $settings;

    /** @var callable(string): string Returns the module-shipped default text for an ISO code. */
    private $defaultProvider;

    /**
     * The fallback callback receives the target ISO and returns the hard-coded
     * localized default. Inject a closure that delegates to
     * `OrderWithdrawl::getDefaultWithdrawalNoteByIso()` at runtime so the two
     * sources of default text stay in sync.
     *
     * @param SettingsService $settings Source of the admin-persisted text
     * @param callable(string): string $defaultProvider Fallback callback
     */
    public function __construct(SettingsService $settings, callable $defaultProvider)
    {
        $this->settings = $settings;
        $this->defaultProvider = $defaultProvider;
    }

    /**
     * Resolve the policy text for a given language id. Returns an empty
     * string when neither a stored value nor a default exists (for example
     * because the ISO is outside the module's whitelist).
     *
     * @param int $idLang PrestaShop language id
     * @param string $iso ISO-639-1 code used as fallback lookup key
     */
    public function getPolicyText(int $idLang, string $iso): string
    {
        $stored = $this->settings->getString(OrderWithdrawlConfig::WITHDRAWAL_NOTE, $idLang, '');
        $stored = trim($stored);
        if ($stored !== '') {
            return $stored;
        }

        $default = (string) ($this->defaultProvider)($iso);

        return trim($default);
    }

    /**
     * Convenience Smarty payload builder. Returned array keys are already
     * prefixed with `orderwithdrawl_` so callers can pass the array straight
     * to the Smarty assign call they already own.
     *
     * @return array{orderwithdrawl_policy_text: string}
     */
    public function getSmartyPayload(int $idLang, string $iso): array
    {
        return [
            'orderwithdrawl_policy_text' => $this->getPolicyText($idLang, $iso),
        ];
    }
}
