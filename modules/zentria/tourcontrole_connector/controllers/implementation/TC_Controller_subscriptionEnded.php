<?php
/**
 *  Copyright 2025 Zentria
 *
 *  @author    Zentria <zentria@profileo.com>
 *  @copyright 2025 Zentria
 *  @license   Commercial
 *  link      https://zentria.profileo.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Update global value about subscription active
 *
 * @author      zentria
 */
class TC_Controller_subscriptionEnded extends TC_Controller
{
    public function getContent()
    {
        Configuration::updateGlobalValue('ZENTRIA_SUBSCRIPTION_ACTIVE', '0');
    }
}
