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

class TC_Website extends TC_ObjectModel
{
    /**
     * Unique key
     */
    public $website_url;

    /**
     * Secondary key
     */
    public $website_type_name;

    public static $uniqueKeys = [
        'website_url',
    ];

    /**
     * Create new TC_Website_data with custom data
     * @since 1.3.0
     *
     * @param $domain string  compute url, hostname or domain name
     * @param $type_name string  website type name
     *
     * @return TC_Website
     */
    public static function createNewOne($domain, $type_name)
    {
        $website = new TC_Website();

        // Mandatory fields
        $website->website_url = $domain;
        $website->website_type_name = $type_name;

        return $website;
    }

    public static function computeDomainName($url)
    {
        $domainName = strtolower($url);
        $domainName = str_replace('http://', '', $domainName);
        $domainName = str_replace('https://', '', $domainName);

        if (strpos($domainName, '/')) {
            $domainNameExploded = explode('/', $domainName);
            $domainName = $domainNameExploded[0];
        }
        return $domainName;
    }
}
