<?php
/**
 * Repository wrapper that centralises customer-lookup with null-safety.
 *
 * @author    sjmedia GmbH
 * @copyright 2024-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace OrderWithdrawl\Repository;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CustomerRepository
{
    /**
     * Return a loaded {@see \Customer} instance or null when the id does not
     * resolve to a valid record. This is the single place where we must
     * combine `new Customer($id)` with `Validate::isLoadedObject`; callers
     * only have to null-check the return value.
     */
    public function find(int $id)
    {
        if ($id <= 0 || !class_exists('Customer', false)) {
            return null;
        }

        $customer = new \Customer($id);
        if (!class_exists('Validate', false)) {
            return null;
        }

        return \Validate::isLoadedObject($customer) ? $customer : null;
    }
}
