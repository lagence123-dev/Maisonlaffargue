<?php
/**
 * 2007-2025 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

@ini_set('display_errors', '0');
@ini_set('display_startup_errors', '0');
error_reporting(0);

if (!headers_sent()) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains', false);
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Location: ../');
}
exit;
