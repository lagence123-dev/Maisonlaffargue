<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *
 * @version   release/2.1.0
 */
namespace Stripe_officialClasslib;

/**
 * Design pattern Registry
 */
class Registry
{
    /**
     * @var Stripe_officialClasslib\Registry Instance of this class
     */
    private static $_registry = null;

    /**
     * @var array
     */
    private $values = [];

    /**
     * Get instance of this class
     *
     * @return Stripe_officialClasslib\Registry
     */
    public static function getInstance()
    {
        if (null === self::$_registry) {
            self::$_registry = new Registry();
        }

        return self::$_registry;
    }

    /**
     * Get a variable in the Registry
     *
     * @param string $index
     *
     * @return bool
     */
    public static function get($index)
    {
        $instance = self::getInstance();
        if (!$instance->offsetExists($index)) {
            return false;
        }

        return $instance->values[$index];
    }

    /**
     * Set a variable in the Registry
     *
     * @param string $index
     * @param string $value
     */
    public static function set($index, $value)
    {
        $instance = self::getInstance();
        $instance->values[$index] = $value;
    }

    /**
     * Check if var exist in the registry
     *
     * @param string $index
     *
     * @return bool
     */
    public static function isRegistered($index)
    {
        if (null === self::$_registry) {
            return false;
        }

        return self::$_registry->offsetExists($index);
    }

    /**
     * Check if offsetExists
     *
     * @param string $index
     *
     * @return bool
     */
    public function offsetExists($index)
    {
        if (false === isset($this->values)) {
            return false;
        }

        return array_key_exists($index, $this->values);
    }

    /**
     * Increment a counter in the Registry
     *
     * @param string $index
     * @param int $value
     */
    public static function increment($index, $value = 1)
    {
        $instance = self::getInstance();
        if (self::isRegistered($index)) {
            $instance->values[$index] += $value;
        } else {
            $instance->values[$index] = $value;
        }
    }

    /**
     * Decrement a counter in the Registry
     *
     * @param string $index
     * @param int $value
     */
    public static function decrement($index, $value = 1)
    {
        $instance = self::getInstance();
        if (self::isRegistered($index)) {
            $instance->values[$index] -= $value;
        } else {
            $instance->values[$index] = $value * (-1);
        }
    }
}
