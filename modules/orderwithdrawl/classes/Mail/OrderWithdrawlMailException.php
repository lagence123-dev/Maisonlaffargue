<?php
/**
 * Typed exception for mail-dispatch failures in the orderwithdrawl module.
 *
 * Raised by {@see MailDispatcher} when either the underlying PrestaShop
 * Mail::Send call fails or any lower-level error is thrown. Callers use the
 * specific type to decide whether to surface the problem to the customer in
 * addition to logging it.
 *
 * @author    sjmedia GmbH
 * @copyright 2024-2026 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */

declare(strict_types=1);

namespace OrderWithdrawl\Mail;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderWithdrawlMailException extends \RuntimeException
{
    /** @var string */
    private $template;

    public function __construct(string $template, string $message = '', $previous = null)
    {
        parent::__construct($message !== '' ? $message : 'Order withdrawal mail dispatch failed.', 0, $previous);
        $this->template = $template;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }
}
