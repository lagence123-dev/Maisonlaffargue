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
 * Toolkit scripts class, implements all data retrieval and more
 *
 * @since 1.3.0
 * @author      zentria
 */
class TC_Toolkit
{
    /**
     * Get domain and shop type
     * @since 1.3.0
     *
     * @param $domain string compute domain, just hostname
     *
     * @return TC_Website
     */
    public static function getShopInfo($domain, $id_shop)
    {
        return TC_Website::createNewOne($domain, TC_Website_type::$WEBSITE_TYPE_PRESTASHOP);
    }

    /**
     * Creates a website data directly from a ps_configuration value
     *
     * @param string $domain
     * @param string $prestashopConfigurationName the name in ps_configuration
     * @param string $websiteDataName the name of the output TC_Website_data
     * @param string $websiteDataField the field of the output TC_Website_data
     * @param int $id_shop the shop's id
     * @param bool $globalValue in global context or not
     *
     * @return TC_Website_data
     */
    public static function getWebsiteDataFromPsConfiguration($domain, $prestashopConfigurationName, $websiteDataName, $websiteDataField, $id_shop, $globalValue = false)
    {
        $website_data = new TC_Website_data();

        if ($globalValue) {
            $value = Configuration::getGlobalValue($prestashopConfigurationName);
        } else {
            $value = Configuration::get($prestashopConfigurationName, null, null, $id_shop);
        }

        // Mandatory fields
        $website_data->name = $websiteDataName;
        $website_data->website_url = $domain;
        $website_data->$websiteDataField = $value;
        $website_data->id_shop = $id_shop;

        return $website_data;
    }

    /**
     * Get Prestashop version
     *
     * @since 1.3.0
     * @param $domain string compute domain, just hostname
     * @return TC_Website_data
     */
    public static function getPrestashopVersion($domain, $id_shop)
    {
        $props = [
            'value_string' => _PS_VERSION_,
        ];

        return TC_Website_data::createNewOne('prestashop_version', $domain, $props, $id_shop);
    }

    /**
     * Format module from Module::getModulesInstalled() to TC_Websitedata
     *
     * @since 1.3.0
     * @param $domain string compute domain, just hostname
     * @param $module array module informations
     */
    public static function getModuleInfo($domain, $module)
    {
        if (!is_object($module) && (!isset($module->name) || !$module->name)) {
            return false;
        }

        $default_iso = Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT'));
        $iso_langs = ['en', 'fr', 'es'];
        $name = 'module_' . $module->name;
        $lastModification = '';

        if (isset($module->last_modification)) {
            $lastModification = $module->last_modification;
        }

        $installed = 0;

        if (isset($module->installed)) {
            $installed = (int) $module->installed;
        }

        $names = [];
        $descriptions = [];

        foreach ($iso_langs as $iso) {
            $default_file = _PS_MODULE_DIR_ . $module->name . '/config.xml';
            $lang_file = _PS_MODULE_DIR_ . $module->name . "/config_$iso.xml";
            // If default config.xml file exist/not empty and its english, get data from it as en lang
            // Else if file config_XX.xml exist/not empty then get data from  it
            // Else get default data from prestashop for current shop lang context
            if ($iso == 'en' && is_file($default_file)) {
                $xml = simplexml_load_file($default_file);
                if ($xml && !empty($xml->displayName) && !empty($xml->description)) {
                    $names['en'] = trim(html_entity_decode((string) $xml->displayName));
                    $descriptions['en'] = trim(html_entity_decode((string) $xml->description));
                }
            } elseif (is_file($lang_file)) {
                $xml = simplexml_load_file($lang_file);
                if ($xml && !empty($xml->displayName) && !empty($xml->description)) {
                    $names[$iso] = trim(html_entity_decode((string) $xml->displayName));
                    $descriptions[$iso] = trim(html_entity_decode((string) $xml->description));
                }
            } elseif ($iso == $default_iso && !empty($module->displayName) && !empty($module->description)) {
                $names[$iso] = $module->displayName;
                $descriptions[$iso] = $module->description;
            }
        }

        $extra_data = [
            'tab' => $module->tab,
            'displayName' => $names,
            'description' => $descriptions,
            'description_full' => isset($module->description_full) ? $module->description_full : '',
            'additional_description' => isset($module->additional_description) ? $module->additional_description : '',
            'author' => isset($module->author) ? $module->author : '',
            'trusted' => isset($module->trusted) ? $module->trusted : '',
            'compatibility' => isset($module->compatibility) ? $module->compatibility : '',
            'nb_rates' => isset($module->nb_rates) ? $module->nb_rates : '',
            'avg_rate' => isset($module->avg_rate) ? $module->avg_rate : '',
            'badges' => isset($module->badges) ? $module->badges : '',
            'url' => isset($module->url) ? $module->url : '',
            'defaultIsoCodeSite' => $default_iso,
            'version_addons' => '',
        ];

        if (isset($module->version_addons)) {
            $extra_data['version_addons'] = (string) $module->version_addons;
        }

        $props = [
            'value_string' => $module->version,
            'value_string_2' => json_encode($extra_data),
            'value_boolean' => $module->active ? 1 : 0, // Avoid empty value
            'value_integer' => $installed, // Avoid empty value
            'value_date' => $lastModification,
        ];

        return TC_Website_data::createNewOne($name, $domain, $props);
    }

    /**
     * Sample test data
     *
     * @since 1.3.0
     * @return string
     */
    public static function sampleData()
    {
        return 'Non structured datas test';
    }

    public static function getWebsiteData_database_manager($domain, $id_shop)
    {
        $toSearch = ['phpmyadmin', 'adminer'];
        $found = [];
        if ($handle = opendir(_PS_ROOT_DIR_)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..') {
                    foreach ($toSearch as $currentToSearch) {
                        if (strpos($entry, $currentToSearch) === 0 || strpos($entry, $currentToSearch)) {
                            $found[] = $entry;
                        }
                    }
                }
            }
            closedir($handle);
        }
        return TC_Website_data::createNewOne('database_manager', $domain, 'value_string_2', json_encode($found), $id_shop);
    }

    public static function getInactive_modules($domain, $modulesOnDisk)
    {
        // Inactive modules
        $inactiveModules = [];
        $modulesOnDiskIds = [];
        foreach ($modulesOnDisk as $key => $module) {
            if (!$module->active && !$module->installed) {
                // Don't use addons cross selling
                unset($modulesOnDisk[$key]);
            } else {
                if ($module->id) {
                    $modulesOnDiskIds[] = $module->id;
                }
            }
        }

        // Make only one query for performances purposes
        $enabledinAShop = Db::getInstance()->executeS('
                    SELECT id_module, COUNT(*) enabledCountShops
                    FROM `' . _DB_PREFIX_ . 'module_shop`
                    WHERE id_module IN (\'' . implode('\', \'', $modulesOnDiskIds) . '\')
                    GROUP BY id_module');
        $enabledinAShopFormatted = [];

        foreach ($enabledinAShop as $currentModule) {
            $enabledinAShopFormatted[$currentModule['id_module']] = $currentModule['enabledCountShops'];
        }

        foreach ($modulesOnDisk as $key => $module) {
            if (isset($enabledinAShopFormatted[$module->id]) && $enabledinAShopFormatted[$module->id] == 0) {
                $inactiveModules[] = $module->name;
            }
        }

        return TC_Website_data::createNewOne('inactive_modules', $domain, 'value_string_2', json_encode($inactiveModules));
    }

    public static function getWebsiteData_vulnerable_files($domain, $id_shop)
    {
        $toSearch = [
            'modules/shoppingfluxexport/lastOrdersTreated.debug',
            'blm.php',
            'shell.php',
            'minishell.php',
            'databases.zip',
            '.elastic.env',
            '.nginx.env',
            'myconf.env',
            'backup-2020.zip',
            'backup-2021.zip',
            'backup-2022.zip',
            'backup-2023.zip',
            'backup.zip',
            'v1.zip',
            '.db.env',
            'main.env',
            'app.zip',
            '.env.new',
            'cronlab/.env',
            'install.zip',
            '.env.local',
            'secret.json',
            '.vscode/sftp.json',
            'configuration.zip',
            'code.zip',
            'db.zip',
            'config.ini',
            'config/prod.json',
            'web-variables.env',
            'wwwroot.zip',
            'env.ini',
            'conf.json',
            'config/config.json',
            'auth.json',
            'root.zip',
            'database.zip',
            'run.zip',
            'configuration.json',
            'include.zip',
            'system.zip',
            'main.zip',
            'module.zip',
            'uploads.zip',
            'upload.zip',
            'exports.zip',
            'htdocs.zip',
            'lib.zip',
            'database/.env',
            'old.zip',
        ];
        $found = [];
        foreach ($toSearch as $currentFile) {
            if (file_exists(_PS_ROOT_DIR_ . '/' . $currentFile)) {
                $found[] = $currentFile;
            }
        }
        return TC_Website_data::createNewOne('vulnerable_files', $domain, 'value_string_2', json_encode($found), $id_shop);
    }

    public static function getWebsiteData_php_unit_leaks($domain, $id_shop)
    {
        $found = [];
        $filesToTest = [
            '/vendor/phpunit/README.md',
            '/modules/vendor/phpunit/README.md',
            '/modules/autoupgrade/vendor/phpunit/README.md',
            '/modules/pscartabandonmentpro/vendor/phpunit/README.md',
            '/modules/ps_facetedsearch/vendor/phpunit/README.md',
            '/modules/ps_checkout/vendor/phpunit/README.md',
            '/modules/ps_facetedsearch/vendor/phpunit/README.md',
            '/modules/gamification/vendor/phpunit/README.md',
        ];
        $foldersToTest = [
            '/vendor/bin/phpunit',
            '/vendor/phpunit',
            '/modules/vendor/bin/phpunit',
            '/modules/vendor/phpunit',
            '/modules/autoupgrade/vendor/bin/phpunit',
            '/modules/autoupgrade/vendor/phpunit',
            '/modules/pscartabandonmentpro/vendor/bin/phpunit',
            '/modules/pscartabandonmentpro/vendor/phpunit',
            '/modules/ps_facetedsearch/vendor/bin/phpunit',
            '/modules/ps_facetedsearch/vendor/phpunit',
            '/modules/ps_checkout/vendor/bin/phpunit',
            '/modules/ps_checkout/vendor/phpunit',
            '/modules/ps_facetedsearch/vendor/bin/phpunit',
            '/modules/ps_facetedsearch/vendor/phpunit',
            '/modules/gamification/vendor/bin/phpunit',
            '/modules/gamification/vendor/phpunit',
        ];
        foreach ($filesToTest as $currentFileToTest) {
            if (file_exists($currentFileToTest)) {
                $found[] = $currentFileToTest;
            }
        }
        foreach ($foldersToTest as $currentFolderToTest) {
            if (is_dir($currentFolderToTest)) {
                $found[] = $currentFolderToTest;
            }
        }
        return TC_Website_data::createNewOne('php_unit_leaks', $domain, 'value_string_2', json_encode($found), $id_shop);
    }

    public static function getWebsiteData_no_carriers_zones($domain, $id_shop)
    {
        // Check Carriers availability for each zone
        $idLangDefault = (int) Configuration::get('PS_LANG_DEFAULT');
        $zones = Zone::getZones(true);
        $noCarrierZones = [];
        foreach ($zones as $currentZone) {
            $carriers = Carrier::getCarriers($idLangDefault, true, false, $currentZone['id_zone']);
            if (!sizeof($carriers)) {
                $noCarrierZones[] = $currentZone['name'];
            }
        }
        return TC_Website_data::createNewOne('no_carriers_zones', $domain, 'value_string_2', json_encode($noCarrierZones), $id_shop);
    }

    public static function getWebsiteData_payment_modules_availability($domain, $id_shop)
    {
        // Check Payment modules availability
        $paymentModulesAvailability = [
            'modules_with_currency' => [],
            'modules_no_currency' => [],
        ];
        $paymentModules = PaymentModule::getInstalledPaymentModules();
        foreach ($paymentModules as $currentModule) {
            if (sizeof(Currency::checkPaymentCurrencies($currentModule['id_module'], $id_shop))) {
                $paymentModulesAvailability['modules_with_currency'][] = $currentModule['name'];
            } else {
                $paymentModulesAvailability['modules_no_currency'][] = $currentModule['name'];
            }
        }
        return TC_Website_data::createNewOne('payment_modules_availability', $domain, 'value_string_2', json_encode($paymentModulesAvailability), $id_shop);
    }

    public static function getWebsiteData_shop_urls($domain, $id_shop)
    {
        // Get shop URLs (category, product, cart)
        $idLangDefault = (int) Configuration::get('PS_LANG_DEFAULT');
        $shopUrls = [
            'category' => false,
            'product' => false,
            'cart' => false,
        ];
        $sql = 'SELECT c.`id_category`
                FROM `' . _DB_PREFIX_ . 'category` c
                INNER JOIN `' . _DB_PREFIX_ . 'category_shop` cs on c.id_category = cs.id_category
                WHERE c.`active` = 1
                AND c.`id_category` != 0
                AND c.id_parent != 0
                AND c.level_depth > 1
                AND c.is_root_category = 0
                AND cs.id_shop = ' . (int) $id_shop . '
                ORDER BY c.date_upd ASC';
        $id_category = (int) Db::getInstance()->getValue($sql);

        // Set Link instance
        $link = new Link();

        if ($id_category !== false && $id_category > 0) {
            $category = new Category($id_category);
            $url = $link->getCategoryLink($id_category, $category->link_rewrite[$idLangDefault], $idLangDefault, null, $id_shop);
            $shopUrls['category'] = $url;
        }
        $sql = 'SELECT ps.`id_product`, ps.`cache_default_attribute`
                FROM `' . _DB_PREFIX_ . 'product_shop` ps
                INNER JOIN `' . _DB_PREFIX_ . 'product` p ON p.id_product = ps.id_product
                WHERE ps.`active` = 1
                AND ps.id_product != 0
                AND ps.id_shop = ' . (int) $id_shop . '
                AND ps.id_category_default != 0
                AND ps.available_for_order = 1
                AND ps.visibility != \'none\'
                ORDER BY ps.id_product ASC';
        $product = Db::getInstance()->getRow($sql);

        if ($product !== false && $product['id_product'] > 0) {
            $product = new Product($product['id_product']);
            $category = new Category($product->id_category_default);
            $url = $link->getProductLink((int) $product->id, $product->link_rewrite[$idLangDefault], $category->link_rewrite[$idLangDefault], $product->ean13, $idLangDefault, $id_shop, $product->cache_default_attribute);
            $shopUrls['product'] = $url;
        }
        $shopUrls['cart'] = $link->getPageLink('cart', null, $idLangDefault, null, false, $id_shop);
        return TC_Website_data::createNewOne('shop_urls', $domain, 'value_string_2', json_encode($shopUrls), $id_shop);
    }

    public static function getWebsiteData_no_root_sql($domain, $id_shop)
    {
        $toSearch = ['.sql', '.sql.gz', '.sql.zip'];
        $found = [];

        if ($handle = opendir(_PS_ROOT_DIR_)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..') {
                    foreach ($toSearch as $currentToSearch) {
                        if (strrpos(strtolower($entry), $currentToSearch) === strlen($currentToSearch)) {
                            $found[] = $entry;
                        }
                    }
                }
            }
            closedir($handle);
        }
        return TC_Website_data::createNewOne('no_root_sql', $domain, 'value_string_2', json_encode($found), $id_shop);
    }

    public static function getWebsiteData_modules_txt_exposed($domain, $id_shop)
    {
        if (file_exists(_PS_ROOT_DIR_ . '/modules.txt')) {
            $found = true;
        } else {
            $found = false;
        }

        return TC_Website_data::createNewOne('modules_txt_exposed', $domain, 'value_boolean', $found, $id_shop);
    }

    public static function getWebsiteData_install_dir_exposed($domain, $id_shop)
    {
        $found = false;

        if ($handle = opendir(_PS_ROOT_DIR_)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..') {
                    if (is_dir(_PS_ROOT_DIR_ . '/' . $entry) && (strpos($entry, 'install') || strpos($entry, 'install') === 0)) {
                        $found = _PS_ROOT_DIR_ . '/' . $entry;
                    }
                }
            }
            closedir($handle);
        }

        return TC_Website_data::createNewOne('install_dir_exposed', $domain, 'value_string_2', $found, $id_shop);
    }

    public static function getWebsiteData_inactive_employees($domain, $id_shop)
    {
        // Get the max id log two weeks old, for performances purpose of the next query
        $idLogTwoWeeks = Db::getInstance()->getValue('SELECT l.id_log
                                                        FROM ' . _DB_PREFIX_ . 'log l
                                                        WHERE l.date_add < (Now() - interval 2 week)
                                                        ORDER BY l.id_log DESC');

        $inactiveEmployees = Db::getInstance()->executeS('SELECT e.firstname, e.lastname, e.email, MAX(l.date_add) as lastAction
                                FROM `' . _DB_PREFIX_ . 'employee` e
                                LEFT JOIN ' . _DB_PREFIX_ . 'log l ON l.id_employee = e.id_employee AND l.id_log > \'' . $idLogTwoWeeks . '\'
                                WHERE e.active = 1
                                AND (l.date_add < (Now() - interval 2 month) OR l.date_add is NULL)
                                GROUP BY e.id_employee
                                ORDER BY lastAction ASC');

        return TC_Website_data::createNewOne('inactive_employees', $domain, 'value_string_2', json_encode($inactiveEmployees), $id_shop);
    }

    public static function getWebsiteData_conversion_rate($domain, $id_shop)
    {
        $sql = 'SELECT COUNT(*) as cartCount
                                FROM `' . _DB_PREFIX_ . 'cart` c
                                INNER JOIN `' . _DB_PREFIX_ . 'cart` cp ON cp.id_cart = c.id_cart
                                WHERE c.date_add > (Now() - interval 1 day)
                                AND c.id_shop = \'' . $id_shop . '\'';
        $cartCount = (int) Db::getInstance()->getValue($sql);

        $sql = 'SELECT COUNT(*) as orderCount
                                FROM `' . _DB_PREFIX_ . 'orders` o
                                INNER JOIN `' . _DB_PREFIX_ . 'order_state` os ON o.current_state = os.id_order_state
                                WHERE o.date_add > (Now() - interval 1 day)
                                AND os.logable = 1
                                AND o.id_shop = \'' . $id_shop . '\'';
        $orderCount = (int) Db::getInstance()->getValue($sql);

        if ($cartCount) {
            $ratePercentage = ($orderCount / $cartCount) * 100;
        } else {
            $ratePercentage = 0;
        }

        return TC_Website_data::createNewOne('conversion_rate', $domain, 'value_string_2', json_encode([
            'cartCount' => $cartCount,
            'orderCount' => $orderCount,
            'ratePercentage' => $ratePercentage,
        ]), $id_shop);
    }

    public static function getWebsiteData_cart_rules_expired($domain, $id_shop)
    {
        $sql = 'SELECT cr.id_cart_rule, cr.date_from, cr.date_to, cr.code
                    FROM `' . _DB_PREFIX_ . 'cart_rule` cr
                    WHERE cr.date_to < NOW()
                    ORDER BY cr.date_to ASC';

        // Get all the expired cart rules
        $nbExpiredRules = Db::getInstance()->getValue('SELECT count(cr.id_cart_rule)
                                                                FROM `' . _DB_PREFIX_ . 'cart_rule` cr
                                                                WHERE cr.date_to < NOW()
                                                                ORDER BY cr.date_to ASC');

        $expiredRulesDetails = Db::getInstance()->executeS($sql . ' LIMIT 10');

        $tcExpiredRules = TC_Website_data::createNewOne('cart_rules_expired', $domain, 'value_string_2', json_encode($expiredRulesDetails), $id_shop);
        $tcExpiredRules->value_integer = $nbExpiredRules;

        return $tcExpiredRules;
    }

    public static function getWebsiteData_product_meta_title_size($domain, $id_shop)
    {
        // Get all meta_title on active product where the length is lower than 50 caracters
        $sql = new DbQuery();
        $sql->select('ps.id_shop, pl.id_product, pl.meta_title, pl.name, l.name AS language_name');
        $sql->from('product_shop', 'ps');
        $sql->innerJoin('product_lang', 'pl', 'ps.id_product = pl.id_product AND pl.id_shop = ps.id_shop');
        $sql->innerJoin('lang', 'l', 'pl.id_lang = l.id_lang');
        $sql->where('ps.active = 1');
        $sql->where('l.active = 1');
        $sql->where('ps.id_shop = ' . (int) $id_shop);
        $sql->where('CHAR_LENGTH(pl.meta_title) < 50');
        $sql->orderBy('pl.id_product');
        $sql->limit(10);
        $metaTitleLowerDetails = Db::getInstance()->executeS($sql);

        // Count all meta_title on active product where the length is lower than 50 caracters
        $sql = new DbQuery();
        $sql->select('COUNT(*) as metaCount');
        $sql->from('product_shop', 'ps');
        $sql->innerJoin('product_lang', 'pl', 'ps.id_product = pl.id_product AND pl.id_shop = ps.id_shop');
        $sql->innerJoin('lang', 'l', 'pl.id_lang = l.id_lang');
        $sql->where('ps.active = 1');
        $sql->where('l.active = 1');
        $sql->where('ps.id_shop = ' . (int) $id_shop);
        $sql->where('CHAR_LENGTH(pl.meta_title) < 50');
        $nbMetaTitleLower = Db::getInstance()->getValue($sql);

        // Get all meta_title on active product where the length is upper than 70 caracters
        $sql = new DbQuery();
        $sql->select('ps.id_shop, pl.id_product, pl.meta_title, pl.name, l.name AS language_name');
        $sql->from('product_shop', 'ps');
        $sql->innerJoin('product_lang', 'pl', 'ps.id_product = pl.id_product AND pl.id_shop = ps.id_shop');
        $sql->innerJoin('lang', 'l', 'pl.id_lang = l.id_lang');
        $sql->where('ps.active = 1');
        $sql->where('l.active = 1');
        $sql->where('ps.id_shop = ' . (int) $id_shop);
        $sql->where('CHAR_LENGTH(pl.meta_title) > 70');
        $sql->orderBy('pl.id_product');
        $sql->limit(10);
        $metaTitleUpperDetails = Db::getInstance()->executeS($sql);

        // Count all meta_title on active product where the length is upper than 70 caracters
        $sql = new DbQuery();
        $sql->select('COUNT(*) as metaCount');
        $sql->from('product_shop', 'ps');
        $sql->innerJoin('product_lang', 'pl', 'ps.id_product = pl.id_product AND pl.id_shop = ps.id_shop');
        $sql->innerJoin('lang', 'l', 'pl.id_lang = l.id_lang');
        $sql->where('ps.active = 1');
        $sql->where('l.active = 1');
        $sql->where('ps.id_shop = ' . (int) $id_shop);
        $sql->where('CHAR_LENGTH(pl.meta_title) > 70');
        $nbMetaTitleUpper = Db::getInstance()->getValue($sql);

        $meta_titles = [
            'meta_lower_than' => $metaTitleLowerDetails,
            'meta_upper_than' => $metaTitleUpperDetails,
        ];

        $tcMetaTitleSize = TC_Website_data::createNewOne('product_meta_title_size', $domain, 'value_string_2', json_encode($meta_titles), $id_shop);
        $tcMetaTitleSize->value_integer = $nbMetaTitleLower + $nbMetaTitleUpper;

        return $tcMetaTitleSize;
    }

    public static function getWebsiteData_product_disable_redirect($domain, $id_shop)
    {
        // Get all disable products where redirect is not configured
        $sql = 'SELECT ps.id_product, ps.id_shop, ps.active, ps.redirect_type
                    FROM `' . _DB_PREFIX_ . 'product_shop` ps
                    WHERE ps.active = 0
                    AND ps.id_shop = ' . (int) $id_shop . '
                    AND (ps.redirect_type IS NULL OR ps.redirect_type = \'\' OR ps.redirect_type = \'404\')
                    ORDER BY ps.date_upd ASC';
        $productRedirectDetails = Db::getInstance()->executeS($sql . ' LIMIT 10');

        // Get all disable products numbers where redirect is not configured
        $nbProductRedirect = Db::getInstance()->getValue('SELECT COUNT(*) as productCount 
                                                                    FROM `' . _DB_PREFIX_ . 'product_shop` ps
                                                                    WHERE ps.active = 0
                                                                    AND ps.id_shop = ' . (int) $id_shop . '
                                                                    AND (ps.redirect_type IS NULL OR ps.redirect_type = \'\' OR ps.redirect_type = \'404\')');

        $tcProductRedirect = TC_Website_data::createNewOne('product_disable_redirect', $domain, 'value_string_2', json_encode($productRedirectDetails), $id_shop);
        $tcProductRedirect->value_integer = $nbProductRedirect;

        return $tcProductRedirect;
    }

    public static function getWebsiteData_customer_new_number($domain, $id_shop)
    {
        // Get number of new customers
        $nbNewCustomers = Db::getInstance()->getValue('SELECT COUNT(*) as customerCount 
                                                            FROM `' . _DB_PREFIX_ . 'customer`
                                                            WHERE date_add > DATE_SUB(NOW(), INTERVAL 1 DAY)
                                                            AND id_shop = ' . (int) $id_shop . '
                                                            ORDER BY date_add DESC');

        $tcCustomersNew = TC_Website_data::createNewOne('customer_new_number', $domain, 'value_integer', $nbNewCustomers, $id_shop);

        $initialized = Configuration::get('Z_init_customer_average', null, null, $id_shop);

        if (!$initialized) {
            $nbCustomersSql = 'SELECT COUNT(*) / 30 as customerCount 
                                FROM `' . _DB_PREFIX_ . 'customer`
                                WHERE date_add > DATE_SUB(NOW(), INTERVAL 30 DAY)
                                AND id_shop = ' . (int) $id_shop . '
                                ORDER BY date_add DESC';

            $average = (float) Db::getInstance()->getValue($nbCustomersSql);

            if (abs($average) > 0.1) {
                Configuration::updateValue('Z_init_customer_average', 1, false, null, $id_shop);
                $tcCustomersNew->value_float = $average;
            }
        }

        return $tcCustomersNew;
    }

    public static function getWebsiteData_shop_revenue($domain, $id_shop)
    {
        // Get the total revenue OF THE DAY
        $sql = 'SELECT SUM(op.amount) AS total_revenue
                FROM `' . _DB_PREFIX_ . 'orders` o
                INNER JOIN `' . _DB_PREFIX_ . 'order_payment` op ON op.order_reference = o.reference AND op.date_add >= DATE_SUB(NOW(), INTERVAL 1 DAY)
                WHERE o.id_shop = ' . (int) $id_shop . '
                -- This is an approximation, consider only the orders less than one month (few chances that an ordre older than 1 month gets a new payment
                -- this reduces the performances leaks, as order_payment does not indexes date_add, and without this approximation : query would parse all orders volume
                AND o.date_add >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';

        // Execute sql
        $totalRevenue = Db::getInstance()->getValue($sql);
        if (empty($totalRevenue)) {
            $totalRevenue = 0;
        }

        $initialized = Configuration::get('Z_init_shop_revenue_average', null, null, $id_shop);

        $tcTotalRevenue = TC_Website_data::createNewOne('shop_revenue', $domain, 'value_float', $totalRevenue, $id_shop);

        if (!$initialized) {
            $nbRevenueSql = 'SELECT SUM(op.amount) / 30 FROM `' . _DB_PREFIX_ . 'orders` as o
                        INNER JOIN `' . _DB_PREFIX_ . 'order_payment` op ON op.order_reference = o.reference AND op.date_add >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                        WHERE o.id_shop = ' . (int) $id_shop . '
                        AND o.date_add >= DATE_SUB(NOW(), INTERVAL 2 MONTH)';

            $average = (float) Db::getInstance()->getValue($nbRevenueSql);

            if (abs($average) > 0.1) {
                Configuration::updateValue('Z_init_shop_revenue_average', 1, false, null, $id_shop);
                $tcTotalRevenue->value_string = $average;
            }
        }

        return $tcTotalRevenue;
    }

    public static function getWebsiteData_orders_total($domain, $id_shop)
    {
        // Get the amount total of orders (valid orders) OF THE DAY
        $sql = 'SELECT COUNT(*) AS total_orders
                FROM `' . _DB_PREFIX_ . 'orders` o
                WHERE o.date_add >= DATE_SUB(NOW(), INTERVAL 1 DAY)
                  AND o.valid = 1
                  AND o.id_shop = ' . (int) $id_shop;

        // Execute sql
        $totalOrders = Db::getInstance()->getValue($sql);

        $initialized = Configuration::get('Z_init_orders_total_average', null, null, $id_shop);

        $tcTotalOrders = TC_Website_data::createNewOne('orders_total', $domain, 'value_integer', $totalOrders, $id_shop);

        if (!$initialized) {
            $nbOrdersSql = 'SELECT COUNT(*) / 30 FROM `' . _DB_PREFIX_ . 'orders` as o
                        WHERE o.date_add >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                        AND o.valid = 1
                        AND o.id_shop = ' . (int) $id_shop;

            $average = (float) Db::getInstance()->getValue($nbOrdersSql);

            if (abs($average) > 0.1) {
                Configuration::updateValue('Z_init_orders_total_average', 1, false, null, $id_shop);
                $tcTotalOrders->value_float = $average;
            }
        }

        return $tcTotalOrders;
    }

    public static function getWebsiteData_carts_total($domain, $id_shop)
    {
        // Get the amount total of carts (valid carts and updated in the last 30 days)
        $sql = 'SELECT COUNT(*) AS total_valid_carts
                FROM `' . _DB_PREFIX_ . 'cart`
                WHERE date_add >= DATE_SUB(NOW(), INTERVAL 1 DAY)
                  AND id_shop = \'' . $id_shop . '\'';

        // Execute sql
        $totalCarts = Db::getInstance()->getValue($sql);

        $initialized = Configuration::get('Z_init_cart_average', null, null, $id_shop);

        $tcTotalCarts = TC_Website_data::createNewOne('carts_total', $domain, 'value_integer', $totalCarts, $id_shop);

        if (!$initialized) {
            $nbCartsSql = 'SELECT COUNT(*) / 30 FROM `' . _DB_PREFIX_ . 'cart` as c
                        WHERE c.date_add >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                        AND c.id_shop = \'' . $id_shop . '\'';

            $average = (float) Db::getInstance()->getValue($nbCartsSql);

            if (abs($average) > 0.1) {
                Configuration::updateValue('Z_init_cart_average', 1, false, null, $id_shop);
                $tcTotalCarts->value_float = $average;
            }
        }

        return $tcTotalCarts;
    }

    public static function getWebsiteData_suspicious_customers($domain, $id_shop)
    {
        $pattern_regex_random = '^[^ ]*([a-z][A-Z].*){2,}';

        $sql = new DbQuery();
        $sql->select('firstname, lastname, email, date_add');
        $sql->from('customer');
        $sql->where('id_shop = ' . (int) $id_shop);
        $sql->where('date_add BETWEEN NOW() - INTERVAL 24 HOUR AND NOW()');
        $sql->where('CAST(firstname AS BINARY) REGEXP BINARY "' . $pattern_regex_random . '" OR CONVERT(firstname USING latin1) LIKE "%?%" OR CONVERT(lastname USING latin1) LIKE "%?%"');
        $sql->limit(50);

        $customers = Db::getInstance()->executeS($sql);

        $tcSuspiciousCustomers = TC_Website_data::createNewOne('suspicious_customers', $domain, 'value_string_2', json_encode($customers), $id_shop);
        $tcSuspiciousCustomers->value_integer = count($customers);

        return $tcSuspiciousCustomers;
    }

    /**
     * Get last record date of declared tables
     * @param string $domain
     * @param int $id_shop
     *
     * @return array of TC_Website_data
     */
    public static function getWebsiteData_garbage_data($domain, $id_shop)
    {
        $tcGarbageData = [];
        // Init our tables with their primary key
        $tables = [
            'id_connections' => 'connections',
            'id_cdc_gtm_order_log' => 'cdc_gtm_order_log',
            'id_mail' => 'mail',
            'id_log' => 'log',
            'id_payplug_logger' => 'payplug_logger',
            'id_statssearch' => 'statssearch',
            'id_cart' => 'cart',
        ];

        foreach ($tables as $primary => $table) {
            // Check if table exist, else continue
            $isTableExist = (bool) Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . $table . '"');

            if (!$isTableExist) {
                continue;
            }
            // Check if column exist, else continue
            $isColumnExist = (bool) Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . $table . '` LIKE "' . $primary . '"');

            if (!$isColumnExist) {
                continue;
            }

            // if cart table, we are doing left join, else same query for all others tables
            if ($table == 'cart') {
                $sqlRes = new DbQuery();
                $sqlRes->select('c.date_add');
                $sqlRes->from($table, 'c');
                $sqlRes->leftJoin('orders', 'o', 'c.' . $primary . ' = o.' . $primary);
                $sqlRes->where('o.id_order IS NULL');

                $sqlCount = new DbQuery();
                $sqlCount->select('COUNT(*)');
                $sqlCount->from($table, 'c');
                $sqlCount->leftJoin('orders', 'o', 'c.' . $primary . ' = o.' . $primary);
                $sqlCount->where('o.id_order IS NULL');
            } else {
                $sqlRes = new DbQuery();
                $sqlRes->select('date_add');
                $sqlRes->from($table);

                $sqlCount = new DbQuery();
                $sqlCount->select('COUNT(*)');
                $sqlCount->from($table);
            }

            $res = Db::getInstance()->getValue($sqlRes);
            $nbRes = Db::getInstance()->getValue($sqlCount);

            // Add to array website data with suffix table name
            $tcWebsiteData = TC_Website_data::createNewOne(sprintf('garbage_data_%s', $table), $domain, 'value_date', $res, $id_shop);
            $tcWebsiteData->value_integer = (int) $nbRes;
            $tcGarbageData[] = $tcWebsiteData;
        }

        return $tcGarbageData;
    }

    /**
     * Get server memory usage
     * @param string $domain
     * @return array of TC_Website_data
     */
    public static function getWebsiteData_server_memory($domain, $id_shop)
    {
        $tcMemoryData = [];
        try {
            // Both method are using shell_exec, so return if function not exist
            if (!function_exists('shell_exec')) {
                return [];
            }

            $proc_meminfo = @shell_exec('cat /proc/meminfo');

            if (empty($proc_meminfo)) {
                $proc_meminfo = @shell_exec('free -m');
            }
            if (empty($proc_meminfo)) {
                return [];
            }

            $proc_meminfo = explode("\n", $proc_meminfo);
            $meminfo = [];

            foreach ($proc_meminfo as $line) {
                $lineArr = explode(':', $line);
                if (isset($lineArr[1])) {
                    list($key, $val) = $lineArr;
                    $meminfo[$key] = trim($val);
                }
            }

            // Case if cat /proc/meminfo return nothing then we using free -m
            if (isset($meminfo['Mem'])) {
                $memory = preg_split('/\s+/', $meminfo['Mem']);
                $meminfo = [
                    'MemTotal' => (int) preg_replace('/[^0-9\.]/', '', $memory[0]),
                    'MemUsed' => (int) preg_replace('/[^0-9\.]/', '', $memory[1]),
                ];
            } else {
                $memTotal = (float) preg_replace('/[^0-9\.]/', '', $meminfo['MemTotal']);
                $memAvailable = (float) preg_replace('/[^0-9\.]/', '', $meminfo['MemAvailable']);
                $memUsed = $memTotal - $memAvailable;
                // Force to MB
                $meminfo = [
                    'MemTotal' => (int) round($memTotal / 1024),
                    'MemUsed' => (int) round($memUsed / 1024),
                ];
            }

            $tcMemoryData[] = TC_Website_data::createNewOne('server_memory_memtotal', $domain, 'value_integer', $meminfo['MemTotal'], $id_shop);
            $tcMemoryData[] = TC_Website_data::createNewOne('server_memory_memused', $domain, 'value_integer', $meminfo['MemUsed'], $id_shop);

            return $tcMemoryData;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get server disk usage
     *
     * @param string $domain
     * @return array of TC_Website_data
     */
    public static function getWebsiteData_disk_space($domain, $id_shop)
    {
        $tcDiskData = [];
        try {
            $totalSpace = (float) disk_total_space('/');
            $totalSpaceFree = (float) disk_free_space('/');
            $usedSpace = (int) $totalSpace - $totalSpaceFree;

            // Check if shell_function exist, and if result of command is not empty
            if (function_exists('shell_exec')) {
                $backup_disk_info = @shell_exec('cat /backup/size');

                // If $backup_disk_info has our data we need, return them
                if (!empty($backup_disk_info)) {
                    $backup_disk_info = explode("\n", $backup_disk_info);
                    // Return if index we need not exist
                    if (isset($backup_disk_info[1])) {
                        $usedSpace = (int) preg_replace('/[^0-9\.]/', '', $backup_disk_info[1]);
                    }
                }
            }

            // Convert $usedSpace & $totalSpace to MB
            // Check if not equal to 0 to avoid divid by 0 error
            if ($usedSpace !== 0 && $totalSpace !== 0) {
                $usedSpace = (int) $usedSpace / (1024 * 1024);
                $totalSpace = (int) $totalSpace / (1024 * 1024);
            }

            // Get total of used disk space
            $tcDiskData[] = TC_Website_data::createNewOne('server_disk_size', $domain, 'value_integer', round($usedSpace), $id_shop);
            // Get total disk space size
            $tcDiskData[] = TC_Website_data::createNewOne('server_disk_total_size', $domain, 'value_string_2', round($totalSpace), $id_shop);

            return $tcDiskData;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get server cpu usage
     *
     * @param string $domain
     */
    public static function getWebsiteData_server_cpus_number($domain, $id_shop)
    {
        try {
            $num_procs = false;

            // Check if shell_function exist, and if result of command is not empty
            if (function_exists('shell_exec')) {
                $nproc = @shell_exec('nproc');

                if (!empty($nproc)) {
                    $num_procs = json_encode((int) preg_replace('/[^0-9\.]/', '', $nproc));
                }
            }

            // Ge load average from php function
            $sys_load_avg = sys_getloadavg();

            // If not empty, return last item of array
            if (!empty($sys_load_avg)) {
                $sys_load_avg = end($sys_load_avg);
            }

            $server_cpus = TC_Website_data::createNewOne('server_cpu_info', $domain, 'value_float', $sys_load_avg, $id_shop);
            $server_cpus->value_integer = $num_procs;

            return $server_cpus;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get webservices data
     *
     * @param string $domain
     *
     * @return object TC_Website_data
     */
    public static function getWebsiteData_webservices($domain, $id_shop)
    {
        $sqlRes = new DbQuery();
        $sqlRes->select('
            wa.id_webservice_account,
            wa.active,
            wa.description name,
            CONCAT(LEFT(wa.key,4), "...", RIGHT(wa.key,4)) w_key,
            SUM(CASE WHEN wp.method IN ("GET", "HEAD") THEN 1 ELSE 0 END) AS count_read,
            SUM(CASE WHEN wp.method IN ("POST", "DELETE", "PUT") THEN 1 ELSE 0 END) AS count_write
        ');
        $sqlRes->from('webservice_account', 'wa');
        $sqlRes->innerJoin('webservice_account_shop', 'was', 'wa.id_webservice_account = was.id_webservice_account AND was.id_shop=' . (int) $id_shop);
        $sqlRes->leftJoin('webservice_permission', 'wp', 'wa.id_webservice_account = wp.id_webservice_account');
        $sqlRes->groupBy('wa.id_webservice_account, wa.active, wa.description, wa.key');
        $sqlRes->orderBy('wa.id_webservice_account');
        $webservices = Db::getInstance()->executeS($sqlRes);

        return TC_Website_data::createNewOne('webservices', $domain, 'value_string_2', json_encode($webservices), $id_shop);
    }

    /**
     * Function use to scan the override directory to get all file overridden
     *
     * @param $directory
     *
     * @return array
     */
    public static function scanOverrideDirectory($directory)
    {
        $result = [];

        if (!is_dir($directory)) {
            return ['error_access_directory' => $directory];
        }

        $files = scandir($directory);

        foreach ($files as $file) {
            // Ignore some special files
            if ($file == '.' || $file == '..') {
                continue;
            }

            $path = $directory . '/' . $file;

            if (is_dir($path)) {
                // Recursive call if it's a directory
                $subDirFiles = TC_Toolkit::scanOverrideDirectory($path);

                $result = array_merge($result, $subDirFiles);
            } elseif ($file !== 'index.php' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                // Add on result if it's not an index.php file
                $relativePath = str_replace(_PS_OVERRIDE_DIR_, '', $directory);
                $relativePath = str_replace(_PS_ROOT_DIR_ . '/override', '', $relativePath);
                $result[] = $relativePath . '/' . $file;
            }
        }

        return $result;
    }

    /**
     * Get all overridden files for domain
     *
     * @param $domain
     *
     * @return TC_Website_data
     */
    public static function getWebsiteData_overrides($domain, $id_shop)
    {
        $overrideDir = _PS_ROOT_DIR_ . '/override';
        $overrideFiles = TC_Toolkit::scanOverrideDirectory($overrideDir);

        return TC_Website_data::createNewOne('overrides', $domain, 'value_string_2', json_encode($overrideFiles), $id_shop);
    }

    /**
     * Get all information about the website
     *
     * @param $domain
     * @return TC_Website_data
     */
    public static function getWebsiteData_core_information($domain, $id_shop)
    {
        // Check SMTP state
        $smtp_state = 'unknown';
        $isMailSMTPMethod = Configuration::get('PS_MAIL_METHOD') == 2;

        if ($isMailSMTPMethod) {
            $fp = fsockopen(Configuration::get('PS_MAIL_SERVER'), (int) Configuration::get('PS_MAIL_SMTP_PORT'), $errno, $errstr, 10);
            if (!$fp) {
                $smtp_state = 'error';
            } else {
                $smtp_state = 'active';
            }
        }

        $ps_date_update_version_last = Db::getInstance()->getValue('
            SELECT date_upd 
            FROM ' . _DB_PREFIX_ . 'configuration 
            WHERE name = "PS_INSTALL_VERSION";
        ');

        // Fill all information
        $information = [
            'version' => [
                'ps' => _PS_VERSION_,
                'php' => phpversion(),
                'server' => $_SERVER['SERVER_SOFTWARE'],
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
            ],
            'database' => [
                'version' => Db::getInstance()->getVersion(),
                'server' => _DB_SERVER_,
                'name' => _DB_NAME_,
                'user' => _DB_USER_,
                'prefix' => _DB_PREFIX_,
                'engine' => _MYSQL_ENGINE_,
            ],
            'ps_date_update_version_last' => $ps_date_update_version_last,
            'uname' => function_exists('php_uname') ? php_uname('s') . ' ' . php_uname('v') . ' ' . php_uname('m') : '',
            'apache_instaweb' => Tools::apacheModExists('mod_instaweb'),
            'upload_max_size' => ini_get('upload_max_filesize'),
            'shop_path' => _PS_ROOT_DIR_,
            'mail' => (Configuration::get('PS_MAIL_METHOD') == 1 || Configuration::get('PS_MAIL_METHOD') == 2),
            'smtp_state' => $smtp_state,
            'smtp' => [
                'server' => Configuration::get('PS_MAIL_SERVER'),
                'user' => Configuration::get('PS_MAIL_USER'),
                'password' => Configuration::get('PS_MAIL_PASSWD'),
                'encryption' => Configuration::get('PS_MAIL_SMTP_ENCRYPTION'),
                'port' => Configuration::get('PS_MAIL_SMTP_PORT'),
            ],
        ];

        return TC_Website_data::createNewOne('core_information', $domain, 'value_string_2', json_encode($information), $id_shop);
    }

    /**
     * Get theme name
     *
     * @param $domain
     * @param $id_shop
     * @return TC_Website_data
     */
    public static function getWebsiteData_theme_name($domain, $id_shop)
    {
        $shop = new Shop($id_shop);
        $theme_name = Validate::isLoadedObject($shop) ? $shop->theme_name : '';

        return TC_Website_data::createNewOne('theme_name', $domain, 'value_string', $theme_name, $id_shop);
    }

    /**
     * Get list of modified files
     * @param string $domain
     */
    public static function getWebsiteData_modified_files($domain, $id_shop)
    {
        try {
            $files = false;
            $files_nb = 0;

            // Check if shell_function exist, and if result of command is not empty
            if (function_exists('shell_exec')) {
                $root_dir = _PS_ROOT_DIR_;
                $allowed_ext = ['html', 'js', 'css', 'scss', 'twig', 'tpl', 'php', 'txt', 'xml', 'yml', 'md'];
                $path_ignore = ['*/cache/*', '*/img/*'];

                $FIND_EXT_CONDITION = implode(' -o ', array_map(function ($ext) {
                    return "-iname '*.$ext'";
                }, $allowed_ext));

                $PATH_IGNORE_CONDITION = implode(' -o ', array_map(function ($path) {
                    return "-path '$path'";
                }, $path_ignore));

                $cmd = "TZ='Europe/Paris' timeout 7 find \"$root_dir\" -type f -mtime -1 \( $FIND_EXT_CONDITION \) -not \( $PATH_IGNORE_CONDITION \) -printf '%TY-%Tm-%Td %TH:%TM:%TS|%p\n' | sed 's/\.[0-9]*//' | sort -nr";

                $files_list = @shell_exec($cmd);

                if (!empty($files_list)) {
                    $files_list = str_replace($root_dir, '', $files_list);
                    $files = $files_list;
                    // Split text lines to array
                    $files = preg_split("/\r\n|\n|\r/", $files);
                    $files_nb = count($files);
                    // Keep 500 first items
                    $files = array_slice($files, 0, 500);
                    // Remove empty items
                    $files = array_filter($files);
                }
            }

            $modified_files = TC_Website_data::createNewOne('modified_files', $domain, 'value_string_2', json_encode($files), $id_shop);
            $modified_files->value_integer = $files_nb;

            return $modified_files;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get information about origin of module, if from addon or not
     *
     * @param $domain
     * @return TC_Website_data
     */
    public static function getWebsiteData_addons_downloaded_from_addons($domain, $id_shop)
    {
        return TC_Website_data::createNewOne('addons_downloaded_from_addons', $domain, 'value_boolean', Zentria::isForAddons(), $id_shop);
    }

    /**
     * Get information about UUID of user Addons account
     *
     * @param $domain
     * @return TC_Website_data
     */
    public static function getWebsiteData_addons_user_uuid($domain, $id_shop)
    {
        $owner_uuid_configuration = Configuration::getGlobalValue('ZENTRIA_OWNER_UUID');

        return TC_Website_data::createNewOne('addons_user_uuid', $domain, 'value_string_2', $owner_uuid_configuration, $id_shop);
    }

    /**
     * Get information about UUID of shop user Addons account
     *
     * @param $domain
     * @return TC_Website_data
     */
    public static function getWebsiteData_addons_shop_uuid($domain, $id_shop)
    {
        $shop_uuid_configuration = Configuration::getGlobalValue('ZENTRIA_SHOP_UUID');

        return TC_Website_data::createNewOne('addons_shop_uuid', $domain, 'value_string_2', $shop_uuid_configuration, $id_shop);
    }

    /**
     * Get duplicates in EAN13 codes, UPC codes, or product references, taking into account both products and their variations.
     * These identifiers must be unique to ensure structured management
     *
     * @param $domain
     * @param $id_shop
     *
     * @return TC_Website_data
     */
    public static function getWebsiteData_catalog_duplicate_detection($domain, $id_shop)
    {
        // Check for EAN13 duplicates across products and combinations
        $sql_ean_duplicates = 'SELECT COALESCE(p.ean13, pa.ean13) as value,
                COUNT(DISTINCT CONCAT(COALESCE(p.id_product, pa.id_product), \'-\', COALESCE(pa.id_product_attribute, 0))) as occurrence_count,
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        COALESCE(p.id_product, pa.id_product), \':\', COALESCE(pa.id_product_attribute, 0)
                    ) 
                    SEPARATOR \'||\'
                ) as affected_products
                FROM ' . _DB_PREFIX_ . 'product p
                INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON p.id_product = ps.id_product AND ps.id_shop = ' . $id_shop . '
                    LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON p.id_product = pa.id_product
                    LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_shop pas ON pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop = ' . $id_shop . '
                WHERE ps.active = 1
                    AND ((p.ean13 != \'\' AND p.ean13 IS NOT NULL) OR (pa.ean13 != \'\' AND pa.ean13 IS NOT NULL))
                    AND (pa.id_product_attribute IS NULL OR pas.id_product_attribute IS NOT NULL)
                GROUP BY COALESCE(p.ean13, pa.ean13)
                HAVING occurrence_count > 1';

        // Check for UPC duplicates across products and combinations
        $sql_upc_duplicates = 'SELECT COALESCE(p.upc, pa.upc) as value,
                COUNT(DISTINCT CONCAT(COALESCE(p.id_product, pa.id_product), \'-\', COALESCE(pa.id_product_attribute, 0))) as occurrence_count,
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        COALESCE(p.id_product, pa.id_product), \':\', COALESCE(pa.id_product_attribute, 0)
                    ) 
                    SEPARATOR \'||\'
                ) as affected_products
                FROM ' . _DB_PREFIX_ . 'product p
                INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON p.id_product = ps.id_product AND ps.id_shop = ' . $id_shop . '
                    LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON p.id_product = pa.id_product
                    LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_shop pas ON pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop = ' . $id_shop . '
                WHERE ps.active = 1
                    AND ((p.upc != \'\' AND p.upc IS NOT NULL) OR (pa.upc != \'\' AND pa.upc IS NOT NULL))
                    AND (pa.id_product_attribute IS NULL OR pas.id_product_attribute IS NOT NULL)
                GROUP BY COALESCE(p.upc, pa.upc)
                HAVING occurrence_count > 1';

        // Check for reference duplicates across products and combinations
        $sql_reference_duplicates = 'SELECT COALESCE(p.reference, pa.reference) as value,
                COUNT(DISTINCT CONCAT(COALESCE(p.id_product, pa.id_product), \'-\', COALESCE(pa.id_product_attribute, 0))) as occurrence_count,
                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        COALESCE(p.id_product, pa.id_product), \':\', COALESCE(pa.id_product_attribute, 0)
                    ) 
                    SEPARATOR \'||\'
                ) as affected_products
                FROM ' . _DB_PREFIX_ . 'product p
                INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON p.id_product = ps.id_product AND ps.id_shop = ' . $id_shop . '
                    LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa ON p.id_product = pa.id_product
                    LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_shop pas ON pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop = ' . $id_shop . '
                WHERE ps.active = 1
                    AND ((p.reference != \'\' AND p.reference IS NOT NULL) OR (pa.reference != \'\' AND pa.reference IS NOT NULL))
                    AND (pa.id_product_attribute IS NULL OR pas.id_product_attribute IS NOT NULL)
                GROUP BY COALESCE(p.reference, pa.reference)
                HAVING occurrence_count > 1';

        $result_ean_duplicates = Db::getInstance()->executeS($sql_ean_duplicates);
        $result_upc_duplicates = Db::getInstance()->executeS($sql_upc_duplicates);
        $result_reference_duplicates = Db::getInstance()->executeS($sql_reference_duplicates);

        // Init total values
        $total_ean_duplicates = 0;
        $total_upc_duplicates = 0;
        $total_reference_duplicates = 0;

        // Convert affected_products from concatenated string to structured array
        foreach ($result_ean_duplicates as &$row) {
            $row['affected_products'] = self::formatAffectedProducts($row['affected_products']);
            $total_ean_duplicates = $total_ean_duplicates + $row['occurrence_count'];
            unset($row['occurrence_count']);
        }
        foreach ($result_upc_duplicates as &$row) {
            $row['affected_products'] = self::formatAffectedProducts($row['affected_products']);
            $total_upc_duplicates = $total_upc_duplicates + $row['occurrence_count'];
            unset($row['occurrence_count']);
        }
        foreach ($result_reference_duplicates as &$row) {
            $row['affected_products'] = self::formatAffectedProducts($row['affected_products']);
            $total_reference_duplicates = $total_reference_duplicates + $row['occurrence_count'];
            unset($row['occurrence_count']);
        }

        // Execute requests in array
        $result = [
            'ean_duplicates' => $result_ean_duplicates,
            'upc_duplicates' => $result_upc_duplicates,
            'reference_duplicates' => $result_reference_duplicates,
        ];

        $catalog_duplicates = TC_Website_data::createNewOne('catalog_duplicate_detection', $domain, 'value_string_2', json_encode($result), $id_shop);
        $catalog_duplicates->value_integer = (int) $total_ean_duplicates + (int) $total_upc_duplicates + (int) $total_reference_duplicates;

        return $catalog_duplicates;
    }

    /**
     * Format affected products string into structured array
     *
     * @param string $affected_products_string Concatenated string with format "id_product:id_product_attribute||..."
     * @return array Array of products with structure ['id_product' => X, 'id_product_attribute' => Y]
     */
    private static function formatAffectedProducts($affected_products_string)
    {
        $formatted_products = [];
        $products = explode('||', $affected_products_string);

        foreach ($products as $product) {
            $parts = explode(':', $product);
            if (count($parts) == 2) {
                $formatted_products[] = [
                    'id_product' => (int) $parts[0],
                    'id_product_attribute' => (int) $parts[1],
                ];
            }
        }

        return $formatted_products;
    }

    public static function getWebsiteData_backoffice_leaked($domain, $id_shop)
    {
        $toSearch = [
            '/04dkwm8civzi0z6z/',
            '/0az87bktydagoexf/',
            '/0yvkfveen5kay8st/',
            '/1Ad7eA22sB/',
            '/1q2w3e/',
            '/2691admin/',
            '/26admin2045/',
            '/2yinflvcpot4mopj/',
            '/2zfvuvceq4qquflr/',
            '/321admin/',
            '/3hztlj2eybnwypjf/',
            '/44626783/',
            '/4admin/',
            '/4dm1nP4r4gu4y/',
            '/5kpvpn1v3pds5pck/',
            '/5oydqwoorxkmdz8r/',
            '/5svpuvhgsnqjgd98/',
            '/6admin-R375/',
            '/6btt7v0drycb9o9u/',
            '/7a3vvlthi90s7vcc/',
            '/7fhlvfztecutyxst/',
            '/7kur1oqlgfzlhv8w/',
            '/7u7qgep2k69uwvm9/',
            '/8bjMmV4G9/',
            '/8bthj2agfuookrdr/',
            '/8dm1nTex/',
            '/8mba8wfyzz4feuwr/',
            '/8rum7vjsrqcfu9ye/',
            '/8s5rip4gi94gsahl/',
            '/8tpivnvag5gq4vdd/',
            '/8xe2lkoee1idbj0q/',
            '/9a9zm4gw3szlmx8u/',
            '/9azcyxvuv0wlvpft/',
            '/9iywdp8q3fwuzef7/',
            '/9klesfetmk0dzovq/',
            '/aagxgfebyc62fwm8/',
            '/aaxD34Tamp/',
            '/abrx9aovtduco9bt/',
            '/accesadministration/',
            '/acceso-gestion/',
            '/acer342/',
            '/acp32/',
            '/ad123/',
            '/add/',
            '/adenrele/',
            '/adhava35/',
            '/adk3-91nvjkwp/',
            '/adliterie99hoteliere/',
            '/adm/',
            '/adm06/',
            '/adm1r3r/',
            '/adm20/',
            '/adm_23SAkv9/',
            '/adm81/',
            '/admicon/',
            '/admimm/',
            '/_admin/',
            '/admin@@@/',
            '/admin002/',
            '/admin005hudmhi/',
            '/admin007/',
            '/admin007007/',
            '/admin01/',
            '/admin015/',
            '/admin019/',
            '/admin061211/',
            '/admin062015/',
            '/admin067/',
            '/admin083/',
            '/admin099/',
            '/admin108iwxxmz/',
            '/admin11071995/',
            '/admin1120NK/',
            '/admin1120WS/',
            '/admin12@12/',
            '/admin123pl/',
            '/admin12kf6n9/',
            '/admin17127217/',
            '/admin1975am/',
            '/admin202088/',
            '/admin2020yupi/',
            '/admin20211/',
            '/admin20gkmdv5651v/',
            '/admin22543_SPZRLX/',
            '/admin241jfrwvv/',
            '/admin280121/',
            '/admin30!!/',
            '/admin32470!/',
            '/admin-32812/',
            '/admin_33/',
            '/admin353PV/',
            '/admin3apadan64jsyksn/',
            '/admin3oib/',
            '/admin436ucosr8/',
            '/admin4dg324s/',
            '/admin551kzysns/',
            '/admin646cemtr/',
            '/admin6df5gh1d3f51hd35fh1/',
            '/admin769b7/',
            '/admin771jvfphi/',
            '/admin7823sdrferk/',
            '/admin-789h6vmpg/',
            '/admin78amir/',
            '/admin832vdx2k3/',
            '/admin873pngy9/',
            '/admin88nikand9spw19n/',
            '/admin88sacaly/',
            '/admin996Pel/',
            '/admin_abn/',
            '/admin_adrefzw/',
            '/adminaeF3qu/',
            '/adminagrifer/',
            '/adminahlkjsdh6jhgdsa82987jhgsd863sdgk9872gd78iugaf27/',
            '/adminaj/',
            '/adminakshopgr/',
            '/admin_alma/',
            '/adminANALITICAMENTE/',
            '/adminanmapet/',
            '/adminanzalo/',
            '/adminaqua/',
            '/adminarion/',
            '/adminarke/',
            '/admin_arredamenti/',
            '/admin_ars/',
            '/admin_art4sense/',
            '/adminas/',
            '/adminasn123/',
            '/adminavto7605/',
            '/admin-aw/',
            '/adminb4M7l7G963/',
            '/adminbamb1n1/',
            '/adminbeallure/',
            '/adminben/',
            '/adminbh/',
            '/adminbiuromix/',
            '/adminbls05/',
            '/adminBMLb4M7l7G/',
            '/admin-bnidark1168/',
            '/adminbodinettezd/',
            '/adminboessa/',
            '/adminbooband/',
            '/adminbp/',
            '/adminbuv/',
            '/admincagnazzo/',
            '/admincarlton5984/',
            '/admincasar/',
            '/admincasual7605/',
            '/admincbd/',
            '/admincc/',
            '/adminCDB/',
            '/admincdm/',
            '/adminCH/',
            '/admin_charlette/',
            '/admin-cityfashion/',
            '/admincJHgjhlkjHldFGd78jhkg5463ghhghKfyitFJHfg/',
            '/adminCliChef/',
            '/adminclubaventura/',
            '/admin_cmtshop/',
            '/admincoco/',
            '/admin_collect454545/',
            '/adminconfort/',
            '/AdminConPdeParty/',
            '/admincontrol/',
            '/admin_control2020/',
            '/admincosme/',
            '/admin_cosval/',
            '/admin_cp/',
            '/admincp/',
            '/admincp-under/',
            '/admindami/',
            '/admindapro/',
            '/admindavid/',
            '/admindekor/',
            '/admin-dev/',
            '/admindev/',
            '/adminDEV/',
            '/adminDG0420/',
            '/admin_diaxiristiko/',
            '/admindiceglie/',
            '/admindichio/',
            '/admin-dm/',
            '/admin-dps/',
            '/adminds/',
            '/admin-duduligzdas/',
            '/admine/',
            '/admin_ecigloo/',
            '/adminelli/',
            '/admin-eloaove/',
            '/adminEM0220/',
            '/adminEmo/',
            '/adminEntona1982/',
            '/adminEP1119/',
            '/admin-eransa/',
            '/admines123/',
            '/adminevolve/',
            '/adminf/',
            '/adminFacilePrestaShop/',
            '/adminfide/',
            '/adminfinisterre/',
            '/admin-fKSAbJFxYB/',
            '/admin_flickerglass/',
            '/adminflx/',
            '/admin_foessel454545/',
            '/admin_fwc/',
            '/adming5/',
            '/admingajda/',
            '/admin_gas/',
            '/adminGF/',
            '/adminglob/',
            '/admin-gold/',
            '/admin_hair/',
            '/admin-helga/',
            '/admin_hermida/',
            '/admin_hiperfiestas/',
            '/adminhomy/',
            '/adminhot/',
            '/admin_ibrio/',
            '/admin_ideapreziosa/',
            '/admin_IM1020/',
            '/admin-imix/',
            '/admin_imprim454545/',
            '/admin-ina-1/',
            '/admining/',
            '/adminintima/',
            '/admin_intimouno/',
            '/adminiris/',
            '/adminISP/',
            '/administrace/',
            '/administracja/',
            '/administrador/',
            '/administrar/',
            '/administrare/',
            '/administratie/',
            '/administration/',
            '/administration_LBL_1.7.6.8/',
            '/administrator/',
            '/administravimas/',
            '/admin-izi/',
            '/adminJAL/',
            '/adminjunior/',
            '/adminka/',
            '/admin-kambala/',
            '/admin_kamome/',
            '/adminkasia/',
            '/adminkate/',
            '/admin-keyza/',
            '/AdminKonestilochic/',
            '/adminkonik85/',
            '/admin-kreling/',
            '/adminktr/',
            '/adminkurzachata/',
            '/adminkvetiny/',
            '/admin_LA0521/',
            '/adminlab/',
            '/adminLab/',
            '/admin_lacomed/',
            '/adminLav/',
            '/adminleo/',
            '/adminlk/',
            '/admin_locherber/',
            '/adminlogin/',
            '/adminlogin123/',
            '/adminloja/',
            '/adminltk/',
            '/adminluppolo/',
            '/adminlws/',
            '/adminmaisonbosc/',
            '/admin_majida/',
            '/adminmaqueta/',
            '/adminmarkez/',
            '/adminmb/',
            '/adminmdg8792/',
            '/admin-men/',
            '/adminmocla/',
            '/admin_montijo/',
            '/adminmooon/',
            '/admin_mop/',
            '/adminmotha/',
            '/adminMOVIL/',
            '/admin_moyo/',
            '/adminMP/',
            '/adminMR0221/',
            '/adminn/',
            '/adminnalaim/',
            '/admin_naturocare/',
            '/adminnb/',
            '/adminnbxasho/',
            '/admin_new/',
            '/adminnexmoto/',
            '/adminNico/',
            '/adminnj/',
            '/adminNJ/',
            '/adminnoluma/',
            '/adminnumeroventidue/',
            '/admin-official-ic/',
            '/adminoleama/',
            '/adminomar/',
            '/admin_onefit/',
            '/adminontime/',
            '/adminooy5Ke/',
            '/adminorgasmo/',
            '/admin-ovh19/',
            '/adminoyj/',
            '/adminp/',
            '/admin-pageart/',
            '/adminpanel/',
            '/adminpapieromat/',
            '/adminparafar/',
            '/adminparisac/',
            '/adminpb/',
            '/adminpdm/',
            '/adminpensobio/',
            '/adminpere/',
            '/adminPerfumy/',
            '/adminperlau/',
            '/adminPFacial/',
            '/admin_PI0321/',
            '/admin_piccoleghiande/',
            '/adminpippo/',
            '/admin_pixla/',
            '/adminpk/',
            '/adminprensiti/',
            '/admin-pro/',
            '/admin-ps/',
            '/admin_ps/',
            '/adminps/',
            '/adminpsl/',
            '/admin_puntera/',
            '/adminr/',
            '/adminraphael/',
            '/AdminRBL/',
            '/adminrdx/',
            '/admin_rebellerie/',
            '/adminRedpipe/',
            '/adminRep3ljuri2020/',
            '/adminROANJA/',
            '/admin_roma/',
            '/adminRoot/',
            '/adminrosa1974/',
            '/adminrp/',
            '/admin_rsalvador/',
            '/adminrta472bm/',
            '/adminSbike19/',
            '/adminscc/',
            '/adminsessi/',
            '/adminsexami/',
            '/adminShoes/',
            '/admin-shop/',
            '/admin_shop/',
            '/adminshop/',
            '/adminshoucangjie/',
            '/admin-site/',
            '/adminskjdhks9879sdyid876sdyf6sd65sftysdr564sdytsd6547/',
            '/adminsklep/',
            '/adminsmooke/',
            '/admin_solotudonna/',
            '/admin_special/',
            '/adminstock/',
            '/admin-store/',
            '/adminstores/',
            '/adminstyle/',
            '/adminsupplex/',
            '/admint1010/',
            '/admintamas/',
            '/admin_tangamania/',
            '/admintcm/',
            '/admintea/',
            '/admin-ti/',
            '/admin_tiendaonline/',
            '/admin-tiffany-private-access/',
            '/admin_time/',
            '/admintm/',
            '/admin_todoconfi/',
            '/admin_trs/',
            '/admintutu/',
            '/admin_TuVampi/',
            '/admintwenty/',
            '/admin_unocasa/',
            '/admin_urbe/',
            '/adminus/',
            '/adminvalugeecommerce/',
            '/adminvdv/',
            '/admin-viale/',
            '/adminvieux/',
            '/adminvino/',
            '/adminviolfed/',
            '/adminvova2019/',
            '/adminvtvbv1/',
            '/adminwe/',
            '/adminweb/',
            '/adminwgc/',
            '/adminwh/',
            '/adminxato/',
            '/adminxeral/',
            '/adminxl/',
            '/adminy/',
            '/adminyanxuanmall/',
            '/adminyucca/',
            '/adminyyy/',
            '/adminzanimal/',
            '/admin-zone/',
            '/adminzora/',
            '/admin_zsp/',
            '/admn26/',
            '/admorderca/',
            '/Adoze35/',
            '/Adpro35/',
            '/adsibon/',
            '/adumgpnegoyevlss/',
            '/agatadmin/',
            '/agvtejubiyuwkii2/',
            '/aljfaocaad4yesov/',
            '/amjzfccjfpcddy2q/',
            '/amministrazione/',
            '/annodd/',
            '/areariservata/',
            '/artimon/',
            '/aruhaz/',
            '/avataronadmin19/',
            '/ax4gykjzrgo0otny/',
            '/ayth0yifxuxtnwav/',
            '/b2b/',
            '/b4snjrqcfiov4gb6/',
            '/back/',
            '/backend/',
            '/backjuliooo566_67576/',
            '/backoffice/',
            '/Backoffice/',
            '/BackOffice/',
            '/backofficeadmin321/',
            '/backofficehenikma/',
            '/baglokalet/',
            '/baladmin/',
            '/bebe17032021x/',
            '/becad/',
            '/belfabvrp9yqo859/',
            '/bfbz1evs0xb0x8zl/',
            '/bgt8a6c5suc1yhx8/',
            '/bhmkoxx7jrcjlsou/',
            '/bhtgrti33sqakxdn/',
            '/bjnuefpw0wif7xci/',
            '/BL_TNS_Administration/',
            '/bms-admin/',
            '/bo/',
            '/bo_broom_81/',
            '/bocadmin/',
            '/boss/',
            '/botiga/',
            '/bo-val/',
            '/BOzaplecze20/',
            '/bs_admin/',
            '/bsz0xhtttjrb3rzz/',
            '/btctr9yemk7e27q6/',
            '/caansoft.com.admin/',
            '/cabiadmin/',
            '/callishop/',
            '/chezBogaToAdmin/',
            '/citizenadmin/',
            '/cktucecazceyp8gk/',
            '/cmrpfecjv0wzuulp/',
            '/cms/',
            '/cnytadmin/',
            '/connect/',
            '/connexion/',
            '/console-murpharm21/',
            '/contmanager/',
            '/control/',
            '/control6745/',
            '/control7490/',
            '/control7546/',
            '/control8495/',
            '/control8563/',
            '/control9108/',
            '/control9375/',
            '/control9390/',
            '/control9395/',
            '/control9597/',
            '/controle/',
            '/cowpvaojhcoudamq/',
            '/cpanel/',
            '/csiadmin/',
            '/cslc5vkd3s6xoxhj/',
            '/cubittadmin/',
            '/cvmept9op6yljqsw/',
            '/cz6j7gcnzwzjivth/',
            '/d44ffdwz4mnanbks/',
            '/d9u4evuoyuusdhiu/',
            '/dartvader/',
            '/dash/',
            '/dashboard/',
            '/dashboard7856Km_enteryes/',
            '/dashbord/',
            '/datos/',
            '/dbm1k6uxjsb2snxv/',
            '/DC_Store/',
            '/dealadmin/',
            '/dgg7qeq6fsfmuvvq/',
            '/diacon/',
            '/Dizeijhde5482/',
            '/djw8le7lgr6wmeq3/',
            '/@dmin/',
            '/_dmin57/',
            '/dmp8zs3/',
            '/doadmin/',
            '/DrgB14-tTr/',
            '/ds0M1EM2/',
            '/dw-backend/',
            '/dyamond/',
            '/e10xqe60pkw71h55/',
            '/eclisseadmin/',
            '/egv@k7P/',
            '/elboad123/',
            '/elegxos/',
            '/emback19922020/',
            '/emeritaf19922020/',
            '/enei0lbcgeq3zzov/',
            '/eoffice/',
            '/eppsqg3uucw07dn6/',
            '/eraseAdmin/',
            '/eshoped/',
            '/etf8mxz6e9qoxg29/',
            '/eth3hzfacxibah0y/',
            '/evte5baiutamzbm9/',
            '/eye22cnp0rqjbcmp/',
            '/f3rn4nd44r14s0nl1n3/',
            '/f88ngmk7zeeydati/',
            '/fam_admin/',
            '/fanamac_admin/',
            '/farmacialosaltoss21/',
            '/fbkgldmdg29kswdd/',
            '/fcijrtc9iog5zf26/',
            '/ffwxqccdc69ebs9a/',
            '/fhe3rvhrqwvdudod/',
            '/fj3kj6i7jvai1uo5/',
            '/flsltnh6ctxwwtib/',
            '/fuaoaa9wg5dmebcy/',
            '/fwnkhms/',
            '/gerencia/',
            '/gerenciar/',
            '/gestadmin/',
            '/gestao/',
            '/gestaomoda/',
            '/gestion/',
            '/gestion97420/',
            '/gestionclaus/',
            '/gestione/',
            '/gestione1212/',
            '/gestione_giralamoda/',
            '/gestion-horseshop/',
            '/gestionLSAC/',
            '/gestor/',
            '/gesyfar/',
            '/gjjkonp89brbjnla/',
            '/GLOVAL/',
            '/gmcth4we5uxpe33h/',
            '/gohost4u/',
            '/gourmetadminshelf2015/',
            '/grxxq6ww27n1ysem/',
            '/gst/',
            '/gtpanel/',
            '/gyphhw69kzy5qr7v/',
            '/h4zwbbzuwygrf6on/',
            '/haldus/',
            '/hardadmin/',
            '/hitechpoint/',
            '/hkdou3in4cbvjmls/',
            '/hpyadmin/',
            '/Hrundel681/',
            '/htaccessed/',
            '/htko0d1panwxbnaa/',
            '/iadmin/',
            '/ibetjg4wuzsvzgpz/',
            '/idbucdw1fqslmydf/',
            '/iehj3xuelzuchhne/',
            '/ih4sqfvk8gkmu0k2/',
            '/intranet/',
            '/ipuscfvde1hdzcgz/',
            '/ivecftkzpomacowd/',
            '/ivylql5u4cburxw4/',
            '/j5gtolzf0sobgbfq/',
            '/j91rw0lho2qh3ldy/',
            '/jaimecanfa/',
            '/japoadmin2015/',
            '/JbC6jS6Bu3/',
            '/jbkok0sogjlkxjqo/',
            '/jc8j1koihebt892o/',
            '/jesurfe/',
            '/jh6xlfvpqtm7s1va/',
            '/jlqn9sypxk7a0vtw/',
            '/jvh/',
            '/jy7qa3hfwxqowgyh/',
            '/k2nulyrckq1gqaoo/',
            '/k4ozho9eccucecl7/',
            '/k6na7ncqkevp0cw3/',
            '/kckuqa6uwtawfcli/',
            '/kesh_co_883Xez/',
            '/kishanhadmin@2020/',
            '/knotadm/',
            '/Kontrolmappa/',
            '/krjWB2dS99rpFR2X/',
            '/l1rnmzqjs8shy1bw/',
            '/l94ffpzxvcfnskjl/',
            '/lhxypugy4am6qkmn/',
            '/lifinadmin20/',
            '/lmi5d3sp0reewhl7/',
            '/logme/',
            '/logowanie-sklep-admin/',
            '/lr5fwbinldnwbype/',
            '/lsb0xgmue788b5fr/',
            '/ltxuqsmarzelphec/',
            '/lueli_admin_16/',
            '/m8l5ote67keq3d6k/',
            '/manage/',
            '/manager/',
            '/manager_swiss/',
            '/Master/',
            '/@mco_1/',
            '/MDVadminMDV/',
            '/miadmin/',
            '/mitienda/',
            '/mixeri2019/',
            '/mkadmin/',
            '/mksadmin/',
            '/mkt/',
            '/mngmt/',
            '/montel2018/',
            '/mshop/',
            '/muk8dnnrgkjpcdgv/',
            '/myadmin/',
            '/myfs2019admin/',
            '/n1glovesbg/',
            '/n2n95dzmrrg7ngyr/',
            '/nay_nay/',
            '/negozio/',
            '/new2020/',
            '/nimda/',
            '/noshi_boss/',
            '/nwlogin/',
            '/o5fdggcwu7bzhtlu/',
            '/o7g8u2ev0zcc7w3q/',
            '/o80aopsg1gnsflpc/',
            '/office/',
            '/office-101/',
            '/ofmczdq07bwncxte/',
            '/ofmxucfffckvemcj/',
            '/ojp8dorpi742mvcl/',
            '/onqj8bzv2snwqkdi/',
            '/onyva29/',
            '/operaadmin/',
            '/ougycuktrlama9zs/',
            '/oyg0s5xeqv1c0fwz/',
            '/ozpvwgaleuhnighx/',
            '/painel/',
            '/painelqvml/',
            '/paladmin/',
            '/panel/',
            '/panel0101/',
            '/panel0605/',
            '/panel-acceso/',
            '/panelbce/',
            '/paneldecontrol/',
            '/panell/',
            '/panelweb/',
            '/perun/',
            '/petalos/',
            '/pg1pbtnydhmnushj/',
            '/phenomenonadmin/',
            '/picadmin/',
            '/pn_admin/',
            '/pnva1ay0kydbtwbz/',
            '/ppqtfwekxu7zkdeq/',
            '/private/',
            '/ps-admin/',
            '/ps_admin/',
            '/psadmin/',
            '/ps_captain/',
            '/pshopadmin39/',
            '/pv3vdczmd56gvjpx/',
            '/pythgrl4strpuqdn/',
            '/q6jnni3bnl06ypej/',
            '/qaoih3ad4twh7fau/',
            '/qictnomm7lmpbhyn/',
            '/ql0deebgnkd5ibjp/',
            '/qqntqdfhuodkht2x/',
            '/qrfmudz5hd8kkodn/',
            '/qva1gj48jaielici/',
            '/qvwnv6ciachney5b/',
            '/qwerty1/',
            '/r0ey65lwtyntqmwp/',
            '/r1bd2caltscnuxbu/',
            '/rbo/',
            '/rel20/',
            '/rmihcita3frfrvig/',
            '/rmprknism6ys1rik/',
            '/rodgnkpoqo8dlzea/',
            '/Rs52234Adm98PLLLiY/',
            '/rxohn8gokzn9ebwo/',
            '/rzoquqqdfocpowkm/',
            '/sbwshopadmin/',
            '/sdipanel/',
            '/sevetsesomelj/',
            '/sfbingolxrhcq6ih/',
            '/sh0p4dmin/',
            '/shop-admin/',
            '/shoumanegueletpp/',
            '/sitefactory/',
            '/sjnpctwam8elgg9i/',
            '/slvd/',
            '/smak/',
            '/smartadmin/',
            '/sorloni/',
            '/sorloni2050/',
            '/souan-admin/',
            '/speadmin/',
            '/SPLXADMIN/',
            '/staff/',
            '/stats/',
            '/SUDO-SU-/',
            '/superadmin/',
            '/SurAdm12/',
            '/SVgshU8z48Nt/',
            '/sw0fnvoim5ezcyy3/',
            '/swadmin/',
            '/sxmmujkudfcwuxuf/',
            '/t2branndewzh0953/',
            '/t5s25friwhpusb8e/',
            '/t6oqdozfnjhwkc0i/',
            '/tak-entrar/',
            '/tatachu/',
            '/tj7v24swywapu6kc/',
            '/tlccejbpk8beoehy/',
            '/tngrm7vyg7qpeowg/',
            '/TopXXX/',
            '/trastienda/',
            '/tutrastienda/',
            '/txrfeu1wv2bqeiyv/',
            '/u3ty4ganlwicn8gs/',
            '/u6op2oftqq0dmxfy/',
            '/uc71yjdh5ebmoyo3/',
            '/uct8jzppankhqutf/',
            '/ujypgmq3evf82uid/',
            '/upravnik/',
            '/uugvtsphsttsskai/',
            '/uxeradm/',
            '/valdymas15/',
            '/vbkkbjyth/',
            '/vbxxutyoqs6noyxd/',
            '/vdrkfynoqpdri8tp/',
            '/vhfm7iqc5fknnkkd/',
            '/villanueva_van123/',
            '/vjyhrwnoo9gap5no/',
            '/vsexvdechoz8jcal/',
            '/vskgbnjq2aiw2p0d/',
            '/wacms/',
            '/warehouse/',
            '/webgest/',
            '/websystems/',
            '/WellBO/',
            '/wf0fch8oyzkswquj/',
            '/wguliyq2uhe0d7oj/',
            '/wnxeytg7i6tey1bb/',
            '/wo5a19fe7lnsqmxw/',
            '/wqggipdvfxwvqu43/',
            '/wqsopu8swdby3j9a/',
            '/wraj01izoeot9uf3/',
            '/wv55fzaeawabyfoa/',
            '/wwpsadmin/',
            '/wxycvk4buyx9ntiv/',
            '/wziso3n10vvx09w1/',
            '/x3zxmnt2sqpnrgtk/',
            '/xcu3nlgcbhvsa2lm/',
            '/xd9czpcdl4junmpr/',
            '/xdndjzl3c3egboii/',
            '/xgi24yrrmx6hwfo1/',
            '/xksl7kl07douzzqr/',
            '/xlt0fk6itcts3ftj/',
            '/xlxlen0anj1rjzwd/',
            '/xpmf74ge96hacwe7/',
            '/ybw48oi5897klxdj/',
            '/yolatadmin/',
            '/yonetici/',
            '/yonetim/',
            '/yonetimb/',
            '/yqeylsj1muaktwwp/',
            '/yrsbpskm2a1jgmnq/',
            '/ytu8nrbykk0pfbyd/',
            '/ywtiatw32rqjzo7c/',
            '/yxamk9okcrxufhbu/',
            '/yyadmin/',
            '/yzvsrjcif3jihc6i/',
            '/z27x9r8tdh86bgme/',
            '/z3vvmdgzgsmf3gwb/',
            '/Z4NqwpsW/',
            '/zao8ovjegkweje6u/',
            '/zaplecze/',
            '/zarzadzajsklepem/',
            '/zhk2ip36qotsowj4/',
            '/zkeqlsbbejnr16hh/',
            '/zknxpn0g6tw4aj5q/',
        ];
        $found = false;
        $extraData = '';
        foreach ($toSearch as $currentFolderToTest) {
            if (is_dir(_PS_ROOT_DIR_ . $currentFolderToTest) || is_link(_PS_ROOT_DIR_ . $currentFolderToTest)) {
                $found = true;
                $extraData = $currentFolderToTest;
                break;
            }
        }
        $tc_backoffice_leaked = TC_Website_data::createNewOne('backoffice_leaked', $domain, 'value_boolean', $found, $id_shop);
        $tc_backoffice_leaked->value_string_2 = $extraData;
        return $tc_backoffice_leaked;
    }

    public static function getWebsiteData_dataviz_stocks($domain, $id_shop)
    {
        // Query to return:
        // - Price
        // - Wholesale price
        $sql = new DbQuery();
        $sql->select('
            SUM(sa.quantity * IF(sa.id_product_attribute = 0, ps.price, ps.price + pa.price)) sell_price_wt,
            SUM(sa.quantity * IF(sa.id_product_attribute = 0, ps.wholesale_price, pa.wholesale_price)) wholesale_price
        ');
        $sql->from('product_shop', 'ps');
        $sql->innerJoin('stock_available', 'sa', 'sa.id_product = ps.id_product AND sa.id_shop = ps.id_shop');
        $sql->leftJoin('product_attribute', 'pa', 'pa.id_product = ps.id_product AND pa.id_product_attribute = sa.id_product_attribute');
        $sql->where('ps.active = 1 AND ps.id_shop = ' . (int) $id_shop);
        $product_prices_wholesale = Db::getInstance()->getRow($sql);

        // Query to return average price per product, including each combination as single product
        $sql = new DbQuery();
        $sql->select('AVG(COALESCE(ps.price + pas.price, ps.price)) AS average_price');
        $sql->from('product_shop', 'ps');
        $sql->leftJoin('product_attribute', 'pa', 'ps.id_product = pa.id_product');
        $sql->leftJoin('product_attribute_shop', 'pas', 'pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop = ' . (int) $id_shop);
        $sql->where('ps.active = 1 AND ps.id_shop = ' . (int) $id_shop);
        $average_price = Db::getInstance()->getValue($sql);

        // Query to return products out of stock, for combination product, its considered out of stock if all variations are out of stock
        $sql = new DbQuery();
        $sql->select('COUNT(DISTINCT ps.id_product) product_enabled_oos');
        $sql->from('product_shop', 'ps');
        $sql->where('ps.active = 1 AND ps.id_shop = ' . (int) $id_shop);
        $sql->where('NOT EXISTS (
            SELECT 1
            FROM ' . _DB_PREFIX_ . 'stock_available sa
            WHERE sa.id_product = ps.id_product AND sa.id_shop = ps.id_shop AND sa.quantity > 0
        )');
        $product_enabled_oos = Db::getInstance()->getValue($sql);

        // Assign our values to array
        $results_sql = [
            'sell_price_wt' => $product_prices_wholesale['sell_price_wt'],
            'wholesale_price' => $product_prices_wholesale['wholesale_price'],
            'average_price' => $average_price,
            'product_enabled_oos' => $product_enabled_oos,
        ];

        // Assign all our values to website data
        return TC_Website_data::createNewOne('dataviz_stocks', $domain, 'value_string_2', json_encode($results_sql), $id_shop);
    }

    public static function getWebsiteData_dataviz_catalog($domain, $id_shop)
    {
        // Query to return:
        // - Products availables
        $sql = new DbQuery();
        $sql->select('SUM(ps.active) products_availables');
        $sql->from('product_shop', 'ps');
        $sql->where('ps.id_shop = ' . (int) $id_shop);
        $product_availables = Db::getInstance()->getValue($sql);

        // Query to return products never ordered
        $sql = new DbQuery();
        $sql->select('COUNT(ps.id_product) never_ordered');
        $sql->from('product_shop', 'ps');
        $sql->leftJoin('order_detail', 'od', 'od.product_id = ps.id_product');
        $sql->where('od.product_id IS NULL AND ps.active = 1 AND ps.id_shop = ' . (int) $id_shop);
        $product_never_ordered = Db::getInstance()->getValue($sql);

        // Query to return products not ordered under 30 days
        $sql = new DbQuery();
        $sql->select('COUNT(*) not_ordered_last_30_days');
        $sql->from('product_shop', 'ps');
        $sql->where('ps.active = 1 AND ps.id_shop = ' . (int) $id_shop);
        $sql->where('NOT EXISTS (
            SELECT 1
            FROM ' . _DB_PREFIX_ . 'order_detail od
            JOIN ' . _DB_PREFIX_ . 'orders o ON od.id_order = o.id_order
            WHERE od.product_id = ps.id_product
              AND o.id_shop = ps.id_shop
              AND o.date_add >= NOW() - INTERVAL 30 DAY
        )');
        $product_not_ordered_last_30d = Db::getInstance()->getValue($sql);

        // Query to return products without categories or category active
        $sql = new DbQuery();
        $sql->select('COUNT(DISTINCT ps.id_product) product_wo_category');
        $sql->from('product_shop', 'ps');
        $sql->where('ps.active = 1 AND ps.id_shop = ' . (int) $id_shop);
        $sql->where('NOT EXISTS (
            SELECT 1
            FROM ' . _DB_PREFIX_ . 'category_product cp
            INNER JOIN ' . _DB_PREFIX_ . 'category c ON cp.id_category = c.id_category AND c.active = 1
            WHERE cp.id_product = ps.id_product
        )');
        $product_without_category = Db::getInstance()->getValue($sql);

        // Assign our values to array
        $results_sql = [
            'products_availables' => $product_availables,
            'products_disabled_no_redirect' => false, // Not implemented yet
            'products_no_bought_last_month' => $product_not_ordered_last_30d,
            'products_never_bought' => $product_never_ordered,
            'product_wo_category' => $product_without_category,
        ];

        // Assign all our values to website data
        return TC_Website_data::createNewOne('dataviz_catalog', $domain, 'value_string_2', json_encode($results_sql), $id_shop);
    }

    public static function getWebsiteData_dataviz_quality_score($domain, $id_shop)
    {
        // Products active description_short length average for all languages
        $sql = new DbQuery();
        $sql->select('AVG(CHAR_LENGTH(pl.description_short)) avg_length');
        $sql->from('product_shop', 'ps');
        $sql->innerJoin('product_lang', 'pl', 'ps.id_product = pl.id_product AND pl.id_shop = ps.id_shop');
        $sql->innerJoin('lang', 'l', 'pl.id_lang = l.id_lang AND l.active = 1');
        $sql->where('ps.active = 1 AND ps.id_shop = ' . (int) $id_shop);
        $qs_avg_product_length_short_desc = Db::getInstance()->getValue($sql);

        // Average all product image count
        $sql = new DbQuery();
        $sql->select('COUNT(pi.id_image) / COUNT(DISTINCT ps.id_product) product_avg_images_per_product');
        $sql->from('product_shop', 'ps');
        $sql->innerJoin('image', 'pi', 'ps.id_product = pi.id_product');
        $sql->where('ps.active = 1 AND ps.id_shop = ' . (int) $id_shop);
        $qs_avg_product_img_count = Db::getInstance()->getValue($sql);

        // Average all product feature count
        $sql = new DbQuery();
        $sql->select('COUNT(pf.id_feature) / COUNT(DISTINCT ps.id_product) avg_features_per_product');
        $sql->from('product_shop', 'ps');
        $sql->innerJoin('feature_product', 'pf', 'ps.id_product = pf.id_product');
        $sql->where('ps.active = 1 AND ps.id_shop = ' . (int) $id_shop);
        $qs_avg_features_per_product = Db::getInstance()->getValue($sql);

        // Percent of products active, combination include without any stock at all
        $sql = new DbQuery();
        $sql->select(' (SUM(IF(sa.quantity = 0, 1, 0)) * 100.0 / COUNT(DISTINCT ps.id_product)) AS oos_percent');
        $sql->from('product_shop', 'ps');
        $sql->innerJoin('stock_available', 'sa', 'sa.id_product = ps.id_product AND sa.id_product_attribute = 0');
        $sql->where('ps.active = 1 AND ps.id_shop = ' . (int) $id_shop);
        $qs_product_combinations_oos = Db::getInstance()->getValue($sql);

        // Percent of products active with no categories associated
        $sql = new DbQuery();
        $sql->select('
         ROUND(
            (COUNT(DISTINCT CASE 
                WHEN cp.id_product IS NULL THEN ps.id_product 
             END) * 100.0) / COUNT(DISTINCT ps.id_product),
          2) products_percent_wo_category
        ');
        $sql->from('product_shop', 'ps');
        $sql->leftJoin('category_product', 'cp', 'ps.id_product = cp.id_product');
        $sql->where('ps.active = 1 AND ps.id_shop = ' . (int) $id_shop);
        $qs_product_percent_wo_category = Db::getInstance()->getValue($sql);

        // Percent of products active with only home category associated
        $home_category = (int) Configuration::get('PS_HOME_CATEGORY');
        $sql = new DbQuery();
        $sql->select('
            ROUND(
                100 * SUM(
                    IF((cp_home.id_category = 2 AND cp_others.id_product IS NULL), 1, 0)) 
                    / COUNT(DISTINCT ps.id_product), 2
            ) AS product_percent_w_only_home
        ');
        $sql->from('product_shop', 'ps');
        $sql->leftJoin('category_product', 'cp_home', 'ps.id_product = cp_home.id_product AND cp_home.id_category = ' . $home_category);
        $sql->leftJoin('category_product', 'cp_others', 'ps.id_product = cp_others.id_product AND cp_others.id_category != ' . $home_category);
        $sql->where('ps.active = 1 AND ps.id_shop = ' . (int) $id_shop);
        $qs_product_percent_w_only_home = !empty($home_category) ? Db::getInstance()->getValue($sql) : false;

        // Assign our values to array
        $results_sql = [
            'qs_avg_product_length_short_desc' => $qs_avg_product_length_short_desc,
            'qs_avg_product_img_count' => $qs_avg_product_img_count,
            'qs_avg_features_per_product' => $qs_avg_features_per_product,
            'qs_product_combinations_oos' => $qs_product_combinations_oos,
            'qs_product_percent_root_wo_category' => $qs_product_percent_wo_category,
            'qs_product_percent_w_only_home' => $qs_product_percent_w_only_home,
        ];

        // Assign all our values to website data
        return TC_Website_data::createNewOne('dataviz_quality_score', $domain, 'value_string_2', json_encode($results_sql), $id_shop);
    }

    public static function getWebsiteData_dataviz_orders_time_slot($domain, $id_shop)
    {
        // Number of valid orders on 1 year
        // by day of the week and by hour
        $sql = new DbQuery();
        $sql->select('
            CASE
                WHEN DAYOFWEEK(o.`date_add`) = 1 THEN 7
                ELSE DAYOFWEEK(o.`date_add`) - 1
                END AS `day_of_week`,
            CASE
                WHEN DAYOFWEEK(o.`date_add`) = 1 THEN \'Sunday\'
                WHEN DAYOFWEEK(o.`date_add`) = 2 THEN \'Monday\'
                WHEN DAYOFWEEK(o.`date_add`) = 3 THEN \'Tuesday\'
                WHEN DAYOFWEEK(o.`date_add`) = 4 THEN \'Wednesday\'
                WHEN DAYOFWEEK(o.`date_add`) = 5 THEN \'Thursday\'
                WHEN DAYOFWEEK(o.`date_add`) = 6 THEN \'Friday\'
                WHEN DAYOFWEEK(o.`date_add`) = 7 THEN \'Saturday\'
                END AS `day_name`,
            HOUR(o.`date_add`) AS `hour`,
            COUNT(*) AS `order_count`
        ');
        $sql->from('orders', 'o');
        $sql->where('o.valid = 1 AND o.date_add >= DATE_SUB(NOW(), INTERVAL 2 WEEK)');
        $sql->where('o.id_shop = ' . (int) pSQL($id_shop));
        $sql->groupBy('day_of_week, hour');
        $sql->orderBy('day_of_week, hour');

        $ot_order_by_day = Db::getInstance()->executeS($sql);

        // Assign our values to array
        $results_sql = [
            $ot_order_by_day,
        ];

        // Assign all our values to website data
        return TC_Website_data::createNewOne('dataviz_orders_time_slot', $domain, 'value_string_2', json_encode($results_sql), $id_shop);
    }
}
