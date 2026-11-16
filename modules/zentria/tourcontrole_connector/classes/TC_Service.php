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

class TC_Service extends TC_ObjectModel
{
    /**
     * Unique key
     */
    public $email;

    /**
     * Unique key
     */
    public $service_name;

    public static $uniqueKeys = [
        'email',
        'service_name',
    ];
}
