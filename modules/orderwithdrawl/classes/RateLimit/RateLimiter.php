<?php
/**
 * Lightweight, runtime-only rate limiter for the public withdrawal submit
 * endpoint.
 *
 * Design decision: we deliberately avoid creating a database table here to
 * stay out of Batch 7's upgrade-script territory. Instead we persist compact
 * per-identifier timestamp buckets in Configuration and keep an in-request
 * memory cache as a cheap read-through layer.
 *
 * @author    sjmedia GmbH
 * @copyright 2024-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace OrderWithdrawl\RateLimit;

if (!defined('_PS_VERSION_')) {
    exit;
}

use OrderWithdrawl\Config\OrderWithdrawlConfig;

class RateLimiter
{
    public const CONFIG_PREFIX = 'ORDERWITHDRAWL_RATE_LIMIT_';

    /** @var int */
    private $maxPerWindow;

    /** @var int */
    private $windowSeconds;

    /** @var array<string, list<int>> Local in-request fallback cache. */
    protected static $memory = [];

    public function __construct(int $maxPerWindow = OrderWithdrawlConfig::RATE_LIMIT_MAX_PER_WINDOW, int $windowSeconds = OrderWithdrawlConfig::RATE_LIMIT_WINDOW_SECONDS)
    {
        $this->maxPerWindow = $maxPerWindow;
        $this->windowSeconds = $windowSeconds;
    }

    /**
     * Record a new attempt for the given identifier (typically the client IP)
     * and return whether the request should be allowed through.
     */
    public function allow(string $identifier, $now = null): bool
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            // Unknown identifier - fail open but still track in a generic bucket
            // so a bad misconfiguration cannot block all legitimate traffic.
            $identifier = 'unknown';
        }

        $now = $now ?? time();
        $timestamps = $this->loadTimestamps($identifier);
        $cutoff = $now - $this->windowSeconds;
        $timestamps = array_values(array_filter($timestamps, static function ($ts) use ($cutoff): bool {
            return $ts >= $cutoff;
        }));

        if (count($timestamps) >= $this->maxPerWindow) {
            $this->storeTimestamps($identifier, $timestamps);

            return false;
        }

        $timestamps[] = $now;
        $this->storeTimestamps($identifier, $timestamps);

        return true;
    }

    /**
     * @return list<int>
     */
    protected function loadTimestamps(string $identifier): array
    {
        $cacheKey = $this->getCacheKey($identifier);
        if (isset(self::$memory[$cacheKey])) {
            return self::$memory[$cacheKey];
        }

        $timestamps = $this->loadPersistentTimestamps($identifier);
        self::$memory[$cacheKey] = $timestamps;

        return $timestamps;
    }

    /**
     * @param list<int> $timestamps
     */
    protected function storeTimestamps(string $identifier, array $timestamps)
    {
        $timestamps = array_values(array_map('intval', $timestamps));
        self::$memory[$this->getCacheKey($identifier)] = $timestamps;

        if (!class_exists('Configuration', false)) {
            return;
        }

        $storageKey = $this->getStorageKey($identifier);
        if ($timestamps === []) {
            \Configuration::deleteByName($storageKey);

            return;
        }

        $payload = json_encode($timestamps);
        if (!is_string($payload)) {
            return;
        }

        \Configuration::updateValue($storageKey, $payload);
    }

    protected function getCacheKey(string $identifier): string
    {
        return 'orderwithdrawl:ratelimit:' . hash('sha256', $identifier);
    }

    /**
     * @return list<int>
     */
    private function loadPersistentTimestamps(string $identifier): array
    {
        if (!class_exists('Configuration', false)) {
            return [];
        }

        $raw = \Configuration::get($this->getStorageKey($identifier), null, null, null, '');
        if (!is_string($raw) || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }

        $timestamps = [];
        foreach ($decoded as $timestamp) {
            if (is_numeric($timestamp)) {
                $timestamps[] = (int) $timestamp;
            }
        }

        return $timestamps;
    }

    private function getStorageKey(string $identifier): string
    {
        return self::CONFIG_PREFIX . substr(hash('sha256', $identifier), 0, 40);
    }

    /**
     * Reset state. Intended for test bootstrapping.
     */
    public static function reset()
    {
        self::$memory = [];
    }
}
