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

class TC_Contact extends TC_ObjectModel
{
    /**
     * Unique key
     */
    public $email;

    /**
     * Unique key
     */
    public $website_url;

    /**
     * Other fields
     */
    public $first_name;

    public $last_name;

    public $phone;

    public $phone_mobile;

    public $company;

    public $sponsor_mail;

    public $address1;

    public $address2;

    public $postcode;

    public $city;

    public $country;

    public $active_rum;

    public static $uniqueKeys = [
        'email',
        'website_url',
    ];
}
