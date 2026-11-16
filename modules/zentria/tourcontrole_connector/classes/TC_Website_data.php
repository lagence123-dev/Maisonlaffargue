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

class TC_Website_data extends TC_ObjectModel
{
    /**
     * Unique key
     */
    public $website_url;

    /**
     * Unique key
     */
    public $name;

    /**
     * Other fields
     */
    public $value_string;

    public $value_string_2;

    public $value_date;

    public $value_boolean;

    public $value_integer;

    public $value_float;

    // Only used for local filtering
    public $id_shop;

    public static $uniqueKeys = [
        'website_url',
        'name',
    ];

    /**
     * Create new TC_Website_data with custom data
     * @since 1.3.0
     *
     * @param $name string  TC_Website_data name, key identifier
     * @param $domain string  compute domain name, just hostname
     * @param $other_props array other custom properties value to add, key need to be property value
     *
     * @return TC_Website_data
     */
    public static function createNewOne($name, $domain, $other_props = null, $singleValue = null, $id_shop = false)
    {
        $website_data = new TC_Website_data();

        // Mandatory fields
        $website_data->name = $name;
        $website_data->website_url = $domain;

        // Other custom fields
        if (is_array($other_props)) {
            foreach ($other_props as $prop_name => $prop_value) {
                // Check property existence
                if (property_exists('TC_Website_Data', $prop_name)) {
                    $website_data->$prop_name = $prop_value;
                }
            }
        } else {
            $website_data->$other_props = $singleValue;
        }

        $website_data->id_shop = $id_shop;

        return $website_data;
    }
}
