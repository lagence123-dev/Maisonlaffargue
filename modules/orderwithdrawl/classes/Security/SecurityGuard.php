<?php
/**
 * Security helpers for the orderwithdrawl module.
 *
 * Stateless utilities that centralise encoding, header-filtering and
 * whitelist defences. All methods are pure and side-effect-free so they can
 * be unit-tested without a PrestaShop bootstrap.
 *
 * @author    sjmedia GmbH
 * @copyright 2024-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace OrderWithdrawl\Security;

if (!defined('_PS_VERSION_')) {
    exit;
}

use OrderWithdrawl\Config\OrderWithdrawlConfig;

final class SecurityGuard
{
    private function __construct()
    {
        // Utility class - not instantiable.
    }

    /**
     * Normalise, trim and truncate a raw customer note, then HTML-encode it for
     * safe placement inside HTML mail bodies.
     *
     * Use the return value in `{customer_note_html}`. For `{customer_note}`
     * (plain-text mails) use `normaliseCustomerNote()` instead.
     */
    public static function encodeCustomerNote(string $raw): string
    {
        $plain = self::normaliseCustomerNote($raw);

        return htmlspecialchars($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Plain-text normalisation used before encoding: unify line endings,
     * strip tags, trim and truncate to the single canonical limit.
     */
    public static function normaliseCustomerNote(string $raw): string
    {
        $normalised = str_replace(["\r\n", "\r"], "\n", $raw);
        $stripped = strip_tags($normalised);
        $trimmed = trim($stripped);

        return mb_substr($trimmed, 0, OrderWithdrawlConfig::CUSTOMER_NOTE_MAX_LENGTH, 'UTF-8');
    }

    /**
     * Normalise the consumer name submitted with the legal withdrawal
     * declaration. This value is part of the receipt content, so it stays
     * human-readable; comparison-specific lowercasing happens separately.
     */
    public static function normaliseConsumerName(string $raw): string
    {
        $normalised = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $raw);
        $stripped = strip_tags($normalised);
        $collapsed = preg_replace('/\s+/u', ' ', $stripped);
        if (!is_string($collapsed)) {
            $collapsed = $stripped;
        }

        return mb_substr(trim($collapsed), 0, 255, 'UTF-8');
    }

    public static function consumerNamesMatch(string $submittedName, string $firstname, string $lastname): bool
    {
        $submitted = self::normaliseNameForComparison($submittedName);
        if ($submitted === '') {
            return false;
        }

        $forward = self::normaliseNameForComparison(trim($firstname . ' ' . $lastname));
        $reverse = self::normaliseNameForComparison(trim($lastname . ' ' . $firstname));

        if ($forward === '' && $reverse === '') {
            return false;
        }

        return $submitted === $forward || $submitted === $reverse;
    }

    private static function normaliseNameForComparison(string $name): string
    {
        $name = self::normaliseConsumerName($name);

        return mb_strtolower($name, 'UTF-8');
    }

    /**
     * Parse an admin email CSV config value into a sanitized list of
     * deliverable email addresses. Entries containing header-injection
     * characters (`\r`, `\n`, `\0`) are discarded *before* the semantic email
     * check, so crafted values can never make it into the recipient list.
     *
     * @return list<string>
     */
    public static function sanitizeAdminEmails(string $csv): array
    {
        if ($csv === '') {
            return [];
        }

        $result = [];
        foreach (explode(',', $csv) as $candidate) {
            $candidate = trim((string) $candidate);
            if ($candidate === '') {
                continue;
            }
            // Reject any header-injection control chars up front.
            if (preg_match('/[\r\n\0]/', $candidate) === 1) {
                continue;
            }
            if (!filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                continue;
            }
            $result[] = $candidate;
        }

        return array_values(array_unique($result));
    }

    /**
     * Validate a language ISO code against the module's shipped mail template
     * whitelist. If the supplied code is unknown or contains path-traversal
     * artifacts, `en` is returned so the resolver still lands in the module's
     * own mail directory.
     */
    public static function validateLanguageIso(string $iso): string
    {
        $normalised = strtolower(trim($iso));
        if ($normalised === '') {
            return 'en';
        }
        // Reject any non-letter characters. Guards against
        // `../../etc` style inputs even before the whitelist check runs.
        if (preg_match('/^[a-z]{2}$/', $normalised) !== 1) {
            return 'en';
        }
        if (!in_array($normalised, OrderWithdrawlConfig::ALLOWED_LANGUAGE_ISO, true)) {
            return 'en';
        }

        return $normalised;
    }

    /**
     * Validate a hex color value (short or long form) with stricter semantics
     * than a loose regex: no surrounding whitespace, no injection-worthy
     * characters, case-insensitive.
     */
    public static function isValidHexColor($color): bool
    {
        if (!is_string($color)) {
            return false;
        }
        if (strlen($color) > 7) {
            return false;
        }

        return preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color) === 1;
    }

    /**
     * Return a CSS-safe representation of a hex color string, or empty string
     * when the input is invalid. Used for inline styles where we cannot
     * rely on downstream Smarty escaping remaining in place forever.
     */
    public static function sanitizeHexColor($color): string
    {
        if (!self::isValidHexColor($color)) {
            return '';
        }

        return strtoupper((string) $color);
    }

    /**
     * Validate the honeypot field: a bot will typically fill any visible-like
     * input, so a non-empty value indicates automated abuse.
     */
    public static function isHoneypotTriggered($value): bool
    {
        if ($value === null) {
            return false;
        }

        return trim((string) $value) !== '';
    }
}
