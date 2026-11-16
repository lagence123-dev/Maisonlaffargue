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
 * Invalidates the local token, so it will regenerates itself
 *
 * @author      zentria
 */
class TC_Controller_invalidateToken extends TC_Controller
{
    public function getContent()
    {
        Configuration::updateGlobalValue('ZENTRIA_TOKEN_SERV', 0);
        Configuration::updateGlobalValue('ZENTRIA_TOKEN_REASON', 'Call to invalidateToken received');

        // Regenerate a new token
        $zentria = Module::getInstanceByName('zentria');
        if ($zentria !== false && method_exists($zentria, 'generateCommunicationToken')) {
            $zentria->generateCommunicationToken();
        }

        exit('Token invalidated, and regeneration launched');
    }
}
