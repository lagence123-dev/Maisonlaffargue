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

class TC_Website_type extends TC_ObjectModel
{
    public static $WEBSITE_TYPE_PRESTASHOP = 'PrestaShop';

    public static $WEBSITE_TYPE_WORDPRESS = 'WordPress';

    public static $WEBSITE_TYPE_MAGENTO = 'Magento';

    /**
     * Unique key
     */
    public $website_type_name;

    public static $uniqueKeys = [
        'website_type_name',
    ];
}
