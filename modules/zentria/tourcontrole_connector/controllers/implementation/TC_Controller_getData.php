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
 * Get and retrieve all specific data
 *
 * @author      zentria
 */
class TC_Controller_getData extends TC_Controller
{
    /**
     * @since 1.3.0
     * @var TC_Contact[] all contact to sync
     */
    protected $_contacts = [];

    /**
     * @since 1.3.0
     * @var TC_Website[] all websites to sync
     */
    protected $_websites = [];

    /**
     * @since 1.3.0
     * @var TC_Service[] all services to sync
     */
    protected $_services = [];

    /**
     * @since 1.3.0
     * @var TC_Website_data[] all websites_data to sync
     */
    protected $_website_datas = [];

    /**
     * @since 1.3.0
     * @var array all data to process
     */
    protected $_process_datas = [];

    /**
     * Main method, call in connector.php
     */
    public function getContent()
    {
        // By default don't display errors to avoid warnings, errors can be forced by passing in param
        if ((bool) Tools::getValue('display_errors')) {
            @ini_set('display_errors', 'on');
            @error_reporting(E_ALL | E_STRICT);
        } else {
            @ini_set('display_errors', 'off');
            if (!defined('_PS_DEBUG_SQL_')) {
                define('_PS_DEBUG_SQL_', false);
            }
            if (!defined('_PS_MODE_DEV_')) {
                define('_PS_MODE_DEV_', false);
            }
        }

        // Browse all shops from the store
        $shops = Shop::getShops(true);

        foreach ($shops as $currentShop) {
            // For now disallow sub folders
            if ($currentShop['uri'] && sizeof(explode('/', $currentShop['uri'])) > 2) {
                continue;
            }

            // SSL domain mandatory
            if (isset($currentShop['domain_ssl']) && $currentShop['domain_ssl']) {
                // Keep only hostname
                $domain = TC_Website::computeDomainName($currentShop['domain_ssl']);

                // Retrieve ID shop if exist
                $id_shop = isset($currentShop['id_shop']) ? (int) $currentShop['id_shop'] : null;

                // Prepare and add to the specific list all data to sync
                $this->addDataToSync($domain, $id_shop);

                // Prepare and add to the specific list all data to process
                $this->addDataToProcess();
            }
        }

        // Format returned data
        return $this->prepareData();
    }

    public static $cache_addDataToSync = [];

    /**
     * Get all data for synchronizing
     * @since 1.3.0
     *
     * @param $domain string compute domain, just hostname
     * @param $id_shop int|string id of current shop
     */
    public function addDataToSync($domain, $id_shop)
    {
        // Domain and shop type
        $this->addWebsite(TC_Toolkit::getShopInfo($domain, $id_shop));

        // Prestashop version
        $this->addWebsiteData(TC_Toolkit::getPrestashopVersion($domain, $id_shop));

        // Maintenance mode
        $this->addWebsiteData(TC_Toolkit::getWebsiteDataFromPsConfiguration($domain, 'PS_SHOP_ENABLE', 'maintenance_mode', 'value_integer', $id_shop));

        // Smarty cache status
        $this->addWebsiteData(TC_Toolkit::getWebsiteDataFromPsConfiguration($domain, 'PS_SMARTY_CACHE', 'smarty_cache', 'value_integer', $id_shop));

        // Smarty Caching type configuration
        $this->addWebsiteData(TC_Toolkit::getWebsiteDataFromPsConfiguration($domain, 'PS_SMARTY_CACHING_TYPE', 'smarty_caching_type', 'value_string', $id_shop));

        // Smarty compile status
        $this->addWebsiteData(TC_Toolkit::getWebsiteDataFromPsConfiguration($domain, 'PS_SMARTY_FORCE_COMPILE', 'smarty_force_compile', 'value_integer', $id_shop));

        // Varnish configured
        $this->addWebsiteData(TC_Toolkit::getWebsiteDataFromPsConfiguration($domain, 'main_server', 'varnish_configured', 'value_string', $id_shop, true));

        // Varnish enabled
        $this->addWebsiteData(TC_Toolkit::getWebsiteDataFromPsConfiguration($domain, 'varnish_enable', 'varnish_enabled', 'value_string', $id_shop, true));

        // CCC CSS and JS
        $this->addWebsiteData(TC_Toolkit::getWebsiteDataFromPsConfiguration($domain, 'PS_CSS_THEME_CACHE', 'css_theme_cache', 'value_integer', $id_shop));
        $this->addWebsiteData(TC_Toolkit::getWebsiteDataFromPsConfiguration($domain, 'PS_JS_THEME_CACHE', 'js_theme_cache', 'value_integer', $id_shop));

        // JS compression inside the HTML
        $this->addWebsiteData(TC_Toolkit::getWebsiteDataFromPsConfiguration($domain, 'PS_JS_HTML_THEME_COMPRESSION', 'js_in_html_compression', 'value_integer', $id_shop));

        // On the fly search indexation on product update
        $this->addWebsiteData(TC_Toolkit::getWebsiteDataFromPsConfiguration($domain, 'PS_SEARCH_INDEXATION', 'search_indexation', 'value_integer', $id_shop));

        // Static subdomain
        if (!defined('_MEDIA_SERVER_1_')) {
            define('_MEDIA_SERVER_1_', '');
        }

        $this->addWebsiteData(TC_Website_data::createNewOne('static_subdomain', $domain, 'value_string', _MEDIA_SERVER_1_, $id_shop));

        // Dev mode
        $this->addWebsiteData(TC_Website_data::createNewOne('dev_mode', $domain, 'value_boolean', _PS_MODE_DEV_, $id_shop));

        // robots.txt file existence
        $this->addWebsiteData(TC_Website_data::createNewOne('seo_robots_txt', $domain, 'value_boolean', file_exists(_PS_ROOT_DIR_ . '/robots.txt'), $id_shop));

        // IP address of the cookie
        $this->addWebsiteData(TC_Toolkit::getWebsiteDataFromPsConfiguration($domain, 'PS_COOKIE_CHECKIP', 'cookie_check_ip', 'value_boolean', $id_shop));

        // Check version for new params in recent version
        if (version_compare(_PS_VERSION_, '1.7.8.8', '>=') && defined('_PS_ALLOW_MULTI_STATEMENTS_QUERIES_')) {
            // Allow multi statements queries
            $this->addWebsiteData(TC_Website_data::createNewOne('allow_multi_statements_queries', $domain, 'value_boolean', _PS_ALLOW_MULTI_STATEMENTS_QUERIES_, $id_shop));
        } else {
            // Old version, not implemented
            $this->addWebsiteData(TC_Website_data::createNewOne('allow_multi_statements_queries', $domain, 'value_string', 'null', $id_shop));
        }

        // Order frequency
        $orderFrequency = $this->getOrderFrequency($id_shop);
        $this->addWebsiteData(TC_Website_data::createNewOne('order_frequency', $domain, 'value_string', json_encode($orderFrequency), $id_shop));

        // Get modules list on disk for real and trusted
        $modulesOnDisk = $this->getModulesOnDiskReallyOnDisk();

        $inactive_modules = TC_Toolkit::getInactive_modules($domain, $modulesOnDisk);
        $this->addWebsiteData($inactive_modules);

        foreach ($modulesOnDisk as $module) {
            $base_module_file = sprintf('%s%s/%s.php', _PS_MODULE_DIR_, $module->name, $module->name);
            // If module is really on disk
            if (file_exists(_PS_MODULE_DIR_ . $module->name . '/' . $module->name . '.php')) {
                // Set new key last_modification with last date of file modification
                $module->last_modification = date('Y-m-d H:i:s', filemtime($base_module_file));
                // Installed module information

                $moduleData = TC_Toolkit::getModuleInfo($domain, $module);
                if ($moduleData instanceof TC_Website_data) {
                    $this->addWebsiteData($moduleData);
                }
            }
        }

        // Complex sondes with callbacks on TC_Toolkit
        // Second boolean is for if we need to execute foreachshop or not
        $generationFunctions = [
            'database_manager' => false,
            'vulnerable_files' => false,
            'php_unit_leaks' => false,
            'no_carriers_zones' => false,
            'payment_modules_availability' => true,
            'shop_urls' => true,
            'no_root_sql' => false,
            'backoffice_leaked' => false,
            'modules_txt_exposed' => false,
            'install_dir_exposed' => false,
            'inactive_employees' => false,
            'conversion_rate' => true,
            'cart_rules_expired' => false,
            'product_meta_title_size' => true,
            'product_disable_redirect' => true,
            'customer_new_number' => true,
            'shop_revenue' => true,
            'orders_total' => true,
            'carts_total' => true,
            'suspicious_customers' => true,
            'dataviz_stocks' => true,
            'dataviz_catalog' => true,
            'dataviz_quality_score' => true,
            'dataviz_orders_time_slot' => true,
            'garbage_data' => false,
            'server_memory' => false,
            'disk_space' => false,
            'server_cpus_number' => false,
            'webservices' => true,
            'overrides' => false,
            'core_information' => false,
            'theme_name' => true,
            'modified_files' => false,
            'addons_downloaded_from_addons' => false,
            'addons_user_uuid' => false,
            'addons_shop_uuid' => false,
            'catalog_duplicate_detection' => true,
        ];

        foreach ($generationFunctions as $currentFunction => $forEachShop) {
            $functionName = 'getWebsiteData_' . $currentFunction;
            $websiteDataArr = [];

            if (!$forEachShop) {
                if (!isset(TC_Controller_getData::$cache_addDataToSync[$currentFunction])) {
                    $data = TC_Toolkit::$functionName($domain, $id_shop);
                } else {
                    $data = TC_Controller_getData::$cache_addDataToSync[$currentFunction];
                }
                $websiteDataArr = is_array($data) ? $data : [$data];

                foreach ($websiteDataArr as $websiteData) {
                    $websiteData = clone $websiteData;
                    $websiteData->website_url = $domain;
                    $this->addWebsiteData($websiteData);
                }
            } else {
                $data = TC_Toolkit::$functionName($domain, $id_shop);
                $websiteDataArr = is_array($data) ? $data : [$data];

                foreach ($websiteDataArr as $websiteData) {
                    $this->addWebsiteData($websiteData);
                }
            }
        }
    }

    /**
     * getModulesOnDisk gives us also the addons pushed one, this method cleans them
     * it also bypass by a hack the call to addons that can be slow
     *
     * @return stdClass[]
     */
    protected function getModulesOnDiskReallyOnDisk()
    {
        // START BYPASS GetModulesOnDisk trusted_modules_list verification
        // The getModulesOnDisk function checks if the trusted_modules_list file is older than 3 days.
        // If the file is outdated, the process of verifying the trusted modules list is triggered,
        // which causes slowdowns in the scanning process and sometimes results in a timeout.
        // To bypass this process, we need to update the trusted_modules_list.xml file with the current date and time.

        // If admin dir not defined, the module 'gauthenticator' would die stopping the script execution
        if (!defined('_PS_ADMIN_DIR_')) {
            define('_PS_ADMIN_DIR_', 'fake_admin_dir');
        }

        // Save current response code
        $responseCode = http_response_code();

        $file_path = _PS_ROOT_DIR_ . '/config/xml/trusted_modules_list.xml';
        $copy_path = str_replace('.xml', '_copy.xml', $file_path);
        $file_path_untrusted = _PS_ROOT_DIR_ . '/config/xml/untrusted_modules_list.xml';
        if (!file_exists($file_path) || !file_exists($file_path_untrusted)) {
            // Some XML missing, force regeneration
            // If not, due to a bug un 1.7, the deleteTrustedXmlCache method would be called
            // and $untrusted_modules_list_content will never be filled again due to condition $untrusted_modules_list_content === null
            // which won't be reached because $untrusted_modules_list_content is false and not null
            // creating surnumerous calls to generateTrustedXml
            if (method_exists(Module::class, 'generateTrustedXml')) {
                Module::generateTrustedXml();
            }
        }

        if (file_exists($file_path)) {
            // Rename the original file to avoid losing it and create a copy with the original name to trick the system.
            rename($file_path, $copy_path);
            copy($copy_path, $file_path);
            // Add an empty line in file to change date of document on today
            file_put_contents($file_path, PHP_EOL, FILE_APPEND);
        }

        // Modules on disk
        if (_PS_VERSION_ >= '8.0') {
            // The second parameter, $logged_on_addons, has been removed in PS 8
            $modulesOnDisk = Module::getModulesOnDisk(true);
        } else {
            $modulesOnDisk = Module::getModulesOnDisk(true, false);
        }

        if (file_exists($copy_path)) {
            // Delete the file copied and renamed
            unlink($file_path);
            // Rename the original file with his original name
            rename($copy_path, $file_path);
        }

        // Restore previous response code because getModulesOnDisk can affect it
        http_response_code($responseCode);
        // END BYPASS GetModulesOnDisk trusted_modules_list verification

        // Remove all modules not really on disk
        foreach ($modulesOnDisk as $key => $module) {
            if (isset($module->type) && ($module->type == 'addonsNative' || $module->type == 'addonsMustHave') && $module->not_on_disk) {
                unset($modulesOnDisk[$key]);
            }

            // Installed boolean is not correct, module is installed if
            $module->installed = isset($module->id) && $module->id > 0;
            $modulesOnDisk[$key] = $module;
        }

        return $modulesOnDisk;
    }

    /**
     * Calculate last 2 weeks order frequency
     *
     * @param int $id_shop
     *
     * @return array[]
     */
    protected function getOrderFrequency($id_shop)
    {
        $result = [];
        for ($i = 0; $i < 14; ++$i) {
            $date = date('Y-m-d', time() - $i * 60 * 60 * 24);
            $result[$date] = [];

            for ($j = 0; $j < 24; ++$j) {
                $hours = $j;
                if ($j < 10) {
                    $j = '0' . $j;
                }
                $dateMySql = $date . ' ' . $hours;
                $nbOrdersSql = 'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'orders o
                                INNER JOIN ' . _DB_PREFIX_ . 'order_state os ON o.current_state = os.id_order_state
                                WHERE os.logable = 1
                                AND o.date_add > \'' . $dateMySql . '\'
                                AND o.date_add < (\'' . $dateMySql . '\' + interval 1 hour)
                                AND o.id_shop = \'' . $id_shop . '\'';
                $result[$date][$j] = Db::getInstance()->getValue($nbOrdersSql);
            }
        }
        return $result;
    }

    /**
     * Get and retrieve all data for processing
     *
     * @since 1.3.0
     */
    public function addDataToProcess()
    {
        // Domain and shop type
        $this->addProcessData(TC_Toolkit::sampleData(), 'test_data');
    }

    /**
     * Prepare all data for return
     *
     * @since 1.3.0
     * @return array all data to get
     */
    public function prepareData()
    {
        $toReturn = [];

        // Update token each communication OK
        $tokenThird = md5(openssl_random_pseudo_bytes(16)) . '-' . md5(openssl_random_pseudo_bytes(16));
        Configuration::updateGlobalValue('ZENTRIA_TOKEN_THIRD', $tokenThird);
        $toReturn['ZENTRIA_TOKEN_THIRD'] = $tokenThird;

        $toReturn['data_to_sync'] = [
            'TC_Contact' => $this->getContacts(),
            'TC_Website' => $this->getWebsites(),
            'TC_Service' => $this->getServices(),
            'TC_Website_data' => $this->getWebsiteDatas(),
        ];

        $toReturn['data_to_process'] = $this->getProcessDatas();

        return $toReturn;
    }

    /**
     * Get all TC_Contact data in the list
     *
     * @since 1.3.0
     *
     * @return TC_Contact[]
     */
    public function getContacts()
    {
        return $this->_contacts;
    }

    /**
     * Add new contact to the list
     * @since 1.3.0
     *
     * @param $contact TC_Contact contact to add
     *
     * @throws InvalidArgumentException
     */
    public function addContact($contact)
    {
        // Check object to add
        if (!$contact instanceof TC_Contact) {
            throw new InvalidArgumentException('addContact parameter must be a TC_Contact instance');
        }
        // Add it to the list
        $this->_contacts[] = $contact;
    }

    /**
     * Get all TC_Website data in the list
     *
     * @since 1.3.0
     * @return TC_Website[]
     */
    public function getWebsites()
    {
        return $this->_websites;
    }

    /**
     * Add new website to the list
     * @since 1.3.0
     *
     * @param $website TC_Website website to add
     *
     * @throws InvalidArgumentException
     */
    public function addWebsite($website)
    {
        // Check object to add
        if (!$website instanceof TC_Website) {
            throw new InvalidArgumentException('addWebsite parameter must be a TC_Website instance');
        }

        // Add it to the list
        $this->_websites[] = $website;
    }

    /**
     * Get all TC_Service data in the list
     *
     * @since 1.3.0
     * @return TC_Service[]
     */
    public function getServices()
    {
        return $this->_services;
    }

    /**
     * Add new service to the list
     * @since 1.3.0
     *
     * @param $service TC_Service service to add
     *
     * @throws InvalidArgumentException
     */
    public function addService($service)
    {
        // Check object to add
        if (!$service instanceof TC_Service) {
            throw new InvalidArgumentException('addService parameter must be a TC_Service instance');
        }

        // Add it to the list
        $this->_services[] = $service;
    }

    /**
     * Get all TC_Website_data data in the list
     *
     * @since 1.3.0
     * @return TC_Website_data[]
     */
    public function getWebsiteDatas()
    {
        return $this->_website_datas;
    }

    /**
     * Add new website_data to the list
     * @since 1.3.0
     *
     * @param $website_data TC_Website_data website_data to add
     *
     * @throws InvalidArgumentException
     */
    public function addWebsiteData($website_data)
    {
        // Check object to add
        if (!$website_data instanceof TC_Website_data) {
            throw new InvalidArgumentException('addWebsite_data parameter must be a TC_Website_data instance');
        }
        // Add it to the list, replace it if allready existing
        $found = false;

        foreach ($this->_website_datas as $key => $currentWebsiteData) {
            if ($website_data->id_shop) {
                if ($website_data->name == $currentWebsiteData->name && $website_data->website_url == $currentWebsiteData->website_url && $website_data->id_shop == $currentWebsiteData->id_shop) {
                    $this->_website_datas[$key] = $website_data;
                    $found = true;
                }
            } else {
                if ($website_data->name == $currentWebsiteData->name && $website_data->website_url == $currentWebsiteData->website_url) {
                    $this->_website_datas[$key] = $website_data;
                    $found = true;
                }
            }
        }

        if (!$found) {
            $this->_website_datas[] = $website_data;
        }
    }

    /**
     * Get all process data in the list
     *
     * @since 1.3.0
     * @return array
     */
    public function getProcessDatas()
    {
        return $this->_process_datas;
    }

    /**
     * Add new process data to the list
     * @since 1.3.0
     *
     * @param $process_data mixed var
     * @param $key string  key identifier
     */
    public function addProcessData($process_data, $key)
    {
        // Add it to the list
        $this->_process_datas[$key] = $process_data;
    }
}
