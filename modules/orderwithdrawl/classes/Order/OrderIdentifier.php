<?php
/**
 * Shared helpers for the customer-entered order identifier mode.
 *
 * @author    sjmedia GmbH
 * @copyright 2024-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace OrderWithdrawl\Order;

if (!defined('_PS_VERSION_')) {
    exit;
}

use OrderWithdrawl\Config\OrderWithdrawlConfig;

final class OrderIdentifier
{
    private function __construct()
    {
        // Utility class - not instantiable.
    }

    public static function normalizeMode(string $mode): string
    {
        $mode = trim($mode);

        return in_array($mode, OrderWithdrawlConfig::ALLOWED_ORDER_IDENTIFIER_MODES, true)
            ? $mode
            : OrderWithdrawlConfig::DEFAULT_ORDER_IDENTIFIER_MODE;
    }

    public static function format(int $orderId, string $orderReference, string $mode): string
    {
        $mode = self::normalizeMode($mode);
        $orderReference = trim($orderReference);

        if ($mode === 'order_id') {
            return $orderId > 0 ? (string) $orderId : '';
        }

        if ($mode === 'reference') {
            return $orderReference;
        }

        if ($orderId > 0 && $orderReference !== '') {
            return (string) $orderId . ' / ' . $orderReference;
        }

        if ($orderId > 0) {
            return (string) $orderId;
        }

        return $orderReference;
    }
}
