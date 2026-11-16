<?php
if (!defined('_PS_VERSION_')) { exit; }

class Datalog123Collector
{
    public static function collect()
    {
        $context = Context::getContext();
        $theme = self::theme($context);
        $modules = self::modules();
        $files = self::changedFiles();
        $overrides = self::overrides();
        $updates = array();
        foreach ($modules as $item) {
            if (!empty($item['update_available'])) {
                $updates[] = array('name' => $item['name'], 'version' => $item['version']);
            }
        }

        return array(
            // Legacy keys and names stay unchanged.
            'theme' => $theme,
            '_PS_VERSION_' => _PS_VERSION_,
            'PHP_VERSION' => PHP_VERSION,
            'shopname' => (string) Configuration::get('PS_SHOP_NAME'),
            'overrides' => $overrides,
            'files_changed' => $files,
            'list' => $modules,
            'webservice_keys_by_shop' => self::webserviceKeysByShop(),
            'trad' => self::translations(),
            // New keys never replace the historical ones.
            'email_config' => self::emailConfig(),
            'modules_updates' => $updates,
            'modules_updates_status' => 'cached_indicators_only',
            'files_deleted' => array(),
            'files_deleted_status' => 'not_available_without_reference_manifest',
            'files_changed_status' => $files === array() ? 'no_result_or_unavailable' : 'core_checker_result',
            'collected_at' => gmdate('c'),
        );
    }

    /**
     * Libelles des cles Webservice autorisees sur la boutique courante.
     * Ne jamais lire ni exposer webservice_account.key.
     */
    private static function webserviceKeysByShop()
    {
        $result = array();
        try {
            $context = Context::getContext();
            $idShop = isset($context->shop) && isset($context->shop->id)
                ? (int) $context->shop->id
                : 0;

            // Sans boutique identifiee, ne pas divulguer les cles des autres boutiques.
            if ($idShop <= 0) {
                return $result;
            }

            $db = Db::getInstance();
            $shopName = $db->getValue(
                'SELECT name FROM `' . _DB_PREFIX_ . 'shop` WHERE id_shop = ' . $idShop
            );
            if ($shopName === false) {
                return $result;
            }

            $keys = array();
            $sql = 'SELECT wa.description, wa.is_module, wa.module_name, wa.active
                FROM `' . _DB_PREFIX_ . 'webservice_account_shop` wsas
                INNER JOIN `' . _DB_PREFIX_ . 'webservice_account` wa
                    ON wa.id_webservice_account = wsas.id_webservice_account
                WHERE wsas.id_shop = ' . $idShop . '
                ORDER BY wa.id_webservice_account';
            $rows = $db->executeS($sql);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $keys[] = [
                        'description' => $row['description'],
                        'is_module' => $row['is_module'],
                        'module_name' => $row['module_name'],
                        'active' => $row['active'],
                    ];
                    //$keys[] = (string) $row['description'];
                }
            }

            $result[] = array(
                'id_shop' => $idShop,
                'shop_name' => (string) $shopName,
                'keys' => $keys,
            );
        } catch (\Throwable $e) {
            PrestaShopLogger::addLog('DATALOG123 - Webservice descriptions: ' . $e->getMessage(), 2);
        }

        return $result;
    }

    private static function theme($context)
    {
        $result = array('themeName' => '', 'themeDisplayName' => '', 'themeVersion' => '',
            'themeAuthorName' => '', 'themeAuthorEmail' => '', 'themeAuthorUrl' => '');
        try {
            $theme = isset($context->shop->theme) ? $context->shop->theme : null;
            if (!$theme) { return $result; }
            $result['themeName'] = (string) $theme->getName();
            $result['themeDisplayName'] = (string) $theme->get('display_name');
            $result['themeVersion'] = (string) $theme->get('version');
            $author = $theme->get('author');
            if (is_array($author)) {
                $result['themeAuthorName'] = isset($author['name']) ? (string) $author['name'] : '';
                $result['themeAuthorEmail'] = isset($author['email']) ? (string) $author['email'] : '';
                $result['themeAuthorUrl'] = isset($author['url']) ? (string) $author['url'] : '';
            }
        } catch (Throwable $e) { /* A missing theme must not break the entire diagnostic. */ }

        return $result;
    }

    private static function modules()
    {
        $result = array();
        $modules = array();
        $installedModules = array();
        $source = Configuration::get('DATALOG123_MODULES_SOURCE') === 'database' ? 'database' : 'disk';

        $context = Context::getContext();

        $iso = isset($context->language->iso_code)
            ? substr($context->language->iso_code, 0, 2)
            : 'fr';

        $domain = (string) Configuration::get('PS_SHOP_DOMAIN_SSL');

        if (!$domain) {
            $domain = (string) Configuration::get('PS_SHOP_DOMAIN');
        }

        $scheme = Configuration::get('PS_SSL_ENABLED')
            ? 'https://'
            : 'http://';

        $baseUrl = $scheme . $domain . __PS_BASE_URI__;

        /*
         * 1. RECUPERATION NATIVE DES MODULES
         */
        if ($source === 'disk') {
            try {
                $nativeModules = Module::getModulesOnDisk();

                if (is_array($nativeModules)) {
                    foreach ($nativeModules as $module) {
                        if (
                            is_object($module)
                            && isset($module->name)
                            && preg_match('/^[a-zA-Z0-9_-]+$/', $module->name)
                        ) {
                            $modules[$module->name] = $module;
                        }
                    }
                }

            } catch (\Throwable $e) {
                PrestaShopLogger::addLog(
                    'DATALOG123 - getModulesOnDisk: ' . $e->getMessage(),
                    2
                );
            }

            /*
             * 2. FALLBACK : SCAN DIRECT DES DOSSIERS
             */
            // Completer la liste native avec les modules presents physiquement,
            // meme s'ils ne sont pas installes ou omis par getModulesOnDisk().
            {

                $directories = @scandir(_PS_MODULE_DIR_);

                if (!is_array($directories)) {
                    $directories = array();
                }

                foreach ($directories as $name) {

                    if (isset($modules[$name])) {
                        continue;
                    }

                    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
                        continue;
                    }

                    $moduleDir = _PS_MODULE_DIR_ . $name . '/';

                    if (
                        !is_dir($moduleDir)
                        || !is_file($moduleDir . $name . '.php')
                    ) {
                        continue;
                    }

                    $module = new \stdClass();

                    $module->name = $name;
                    $module->displayName = $name;
                    $module->description = '';
                    $module->version = '';
                    $module->author = '';
                    $module->tab = 'others';

                    $configFile = $moduleDir . 'config.xml';

                    if (
                        is_file($configFile)
                        && filesize($configFile) < 512000
                        && function_exists('simplexml_load_file')
                    ) {
                        $xml = @simplexml_load_file(
                            $configFile,
                            'SimpleXMLElement',
                            LIBXML_NONET
                        );

                        if ($xml !== false) {
                            foreach (
                                array(
                                    'displayName',
                                    'description',
                                    'version',
                                    'author',
                                    'tab'
                                ) as $field
                            ) {
                                if (isset($xml->$field)) {
                                    $module->$field = (string) $xml->$field;
                                }
                            }
                        }
                    }

                    $modules[$name] = $module;
                }
            }

        } // Fin de la recuperation depuis le disque.

        /*
         * 3. MODULES INSTALLES EN BASE DE DONNEES
         */
        try {
            $idShop = isset($context->shop->id)
                ? (int) $context->shop->id
                : (int) Configuration::get('PS_SHOP_DEFAULT');

            $sql = '
            SELECT
                m.name,
                m.version,
                m.active,
                ms.id_module AS shop_active
            FROM `' . _DB_PREFIX_ . 'module` m
            LEFT JOIN `' . _DB_PREFIX_ . 'module_shop` ms
                ON m.id_module = ms.id_module
                AND ms.id_shop = ' . $idShop;

            $rows = Db::getInstance()->executeS($sql);

            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $installedModules[$row['name']] = $row;
                }
            }

        } catch (\Throwable $e) {
            PrestaShopLogger::addLog(
                'DATALOG123 - Modules SQL: ' . $e->getMessage(),
                2
            );
        }

        // En mode base de donnees, seuls les modules enregistres dans ps_module
        // alimentent la liste. Les metadonnees optionnelles restent lues sur disque.
        if ($source === 'database') {
            foreach ($installedModules as $name => $row) {
                if (!preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
                    continue;
                }
                $module = new \stdClass();
                $module->name = $name;
                $module->displayName = $name;
                $module->description = '';
                $module->version = isset($row['version']) ? (string) $row['version'] : '';
                $module->author = '';
                $module->tab = '';
                $modules[$name] = $module;
            }
        }

        /*
         * 4. RECUPERATION DU SERVICE MARKETPLACE
         *
         * ps_mbo : PrestaShop Marketplace in Back Office
         */
        $mboHelper = null;

        try {
            if (Module::isInstalled('ps_mbo')) {

                $mbo = Module::getInstanceByName('ps_mbo');

                if ($mbo) {

                    // API disponible selon la version de ps_mbo
                    if (method_exists($mbo, 'get')) {
                        $mboHelper = $mbo->get('mbo.modules.helper');

                    } elseif (method_exists($mbo, 'getService')) {
                        $mboHelper = $mbo->getService(
                            'mbo.modules.helper'
                        );
                    }
                }
            }

        } catch (\Throwable $e) {
            PrestaShopLogger::addLog(
                'DATALOG123 - ps_mbo: ' . $e->getMessage(),
                2
            );

            $mboHelper = null;
        }

        if (
            !is_object($mboHelper)
            || !method_exists($mboHelper, 'findForUpdates')
        ) {
            $mboHelper = null;
        }

        /*
         * 5. CONSTRUCTION DE LA LISTE DES MODULES
         */
        foreach ($modules as $name => $module) {

            $installed = isset($installedModules[$name]);

            $active = 0;

            if ($installed) {
                $active = !empty(
                $installedModules[$name]['shop_active']
                ) ? 1 : 0;
            }

            $version = isset($module->version)
                ? (string) $module->version
                : '';

            if (
                !$version
                && $installed
                && isset($installedModules[$name]['version'])
            ) {
                $version = (string) $installedModules[$name]['version'];
            }

            /*
             * 6. MISES A JOUR MARKETPLACE
             *
             * true  : mise à jour détectée
             * false : aucune mise à jour signalée
             * null  : information indisponible
             */
            $updateAvailable = null;
            $latestVersion = null;

            if ($installed && $mboHelper !== null) {

                try {
                    $updateInfo = $mboHelper->findForUpdates($name);

                    if (
                        is_array($updateInfo)
                        && array_key_exists(
                            'upgrade_available',
                            $updateInfo
                        )
                    ) {
                        $updateAvailable = filter_var(
                            $updateInfo['upgrade_available'],
                            FILTER_VALIDATE_BOOLEAN
                        );

                        // Version disponible si fournie par ps_mbo
                        foreach (
                            array(
                                'version_available',
                                'available_version',
                                'latest_version'
                            ) as $versionKey
                        ) {
                            if (
                                !empty($updateInfo[$versionKey])
                                && is_scalar($updateInfo[$versionKey])
                            ) {
                                $latestVersion = (string) $updateInfo[$versionKey];
                                break;
                            }
                        }
                    }

                } catch (\Throwable $e) {
                    PrestaShopLogger::addLog(
                        'DATALOG123 - Marketplace ' .
                        $name . ': ' . $e->getMessage(),
                        2
                    );
                }
            }

            /*
             * 7. CONSTRUCTION DU JSON
             */
            $row = array(
                'name' => $name,

                'displayName' => isset($module->displayName)
                    ? (string) $module->displayName
                    : $name,

                'description' => isset($module->description)
                    ? strip_tags((string) $module->description)
                    : '',

                'version' => $version,

                'author' => isset($module->author)
                    ? (string) $module->author
                    : '',

                'tab' => isset($module->tab)
                    ? (string) $module->tab
                    : '',

                'active' => $active,

                'installed' => $installed ? 1 : 0,

                // Statut explicite pour l'affichage dans le SaaS LOG123.
                'installation_status' => $installed ? 'installed' : 'not_installed',

                'logo' => $baseUrl
                    . 'modules/'
                    . rawurlencode($name)
                    . '/logo.png',

                'update_available' => $updateAvailable,

                'latest_version' => $latestVersion
            );

            /*
             * 8. INFORMATIONS XML COMPLEMENTAIRES
             */
            $configFiles = array(
                'config.xml' => 'config_tab',
                'config_' . $iso . '.xml' => 'config_iso_tab'
            );

            foreach ($configFiles as $filename => $key) {

                $path = _PS_MODULE_DIR_ . $name . '/' . $filename;

                if (
                    !is_file($path)
                    || filesize($path) >= 512000
                    || !function_exists('simplexml_load_file')
                ) {
                    continue;
                }

                $xml = @simplexml_load_file(
                    $path,
                    'SimpleXMLElement',
                    LIBXML_NONET
                );

                if ($xml !== false && isset($xml->tab)) {
                    $row[$key] = (string) $xml->tab;
                }
            }

            $result[] = $row;
        }

        return $result;
    }

    private static function changedFiles()
    {
        $class = '\\PrestaShop\\PrestaShop\\Adapter\\Requirement\\CheckMissingOrUpdatedFiles';
        if (!class_exists($class)) { return array(); }
        try {
            $checker = new $class();
            if (method_exists($checker, 'getListOfUpdatedFiles')) {
                $files = $checker->getListOfUpdatedFiles();
                return is_array($files) ? $files : array();
            }
        } catch (Throwable $e) { /* Unsupported core checker on some versions. */ }
        return array();
    }

    private static function overrides()
    {
        $result = array();
        if (!defined('_PS_OVERRIDE_DIR_') || !is_dir(_PS_OVERRIDE_DIR_)) { return $result; }
        try {
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(_PS_OVERRIDE_DIR_, FilesystemIterator::SKIP_DOTS));
            foreach ($it as $file) {
                if (!$file->isFile() || strtolower($file->getExtension()) !== 'php' || $file->getFilename() === 'index.php') { continue; }
                $result[] = str_replace('\\', '/', substr($file->getPathname(), strlen(_PS_OVERRIDE_DIR_)));
                if (count($result) >= 10000) { break; }
            }
        } catch (Throwable $e) { /* Don't disclose server paths or internal details. */ }
        sort($result);


        return $result;
    }

    private static function emailConfig()
    {
        $keys = array('PS_MAIL_METHOD', 'PS_MAIL_SERVER', 'PS_MAIL_SMTP_PORT', 'PS_MAIL_SMTP_ENCRYPTION',
            'PS_MAIL_TYPE', 'PS_MAIL_DOMAIN', 'PS_MAIL_USER', 'PS_MAIL_DKIM_ENABLE');
        $result = array();
        foreach ($keys as $key) {
            if ($key === 'PS_MAIL_USER') {
                $result['smtp_user_configured'] = (bool) Configuration::get($key);
            } else {
                $value = Configuration::get($key);
                $result[$key] = $value === false ? null : $value;
            }
        }
        // NEVER export PS_MAIL_PASSWD, secrets, or API keys.

        return $result;
    }

    private static function translations()
    {
        $labels = array(
            'administration' => 'Administration', 'advertising_marketing' => 'Advertising & Marketing',
            'analytics_stats' => 'Analytics & Stats', 'billing_invoicing' => 'Taxes & Invoicing',
            'checkout' => 'Checkout', 'content_management' => 'Content Management',
            'customer_reviews' => 'Customer Reviews', 'export' => 'Export',
            'front_office_features' => 'Front office Features', 'i18n_localization' => 'Internationalization & Localization',
            'merchandizing' => 'Merchandising', 'migration_tools' => 'Migration Tools',
            'payments_gateways' => 'Payments & Gateways', 'payment_security' => 'Site certification & Fraud prevention',
            'pricing_promotion' => 'Pricing & Promotion', 'quick_bulk_update' => 'Quick / Bulk update',
            'search_filter' => 'Search & Filter', 'seo' => 'SEO', 'shipping_logistics' => 'Shipping & Logistics',
            'slideshows' => 'Slideshows', 'smart_shopping' => 'Comparison site & Feed management',
            'market_place' => 'Marketplace', 'others' => 'Other Modules', 'mobile' => 'Mobile',
            'dashboard' => 'Dashboard', 'emailing' => 'Emailing & SMS',
            'social_networks' => 'Social Networks', 'social_community' => 'Social & Community',
        );
        try {
            $translator = Context::getContext()->getTranslator();
            foreach ($labels as $key => $label) {
                $labels[$key] = $translator->trans($label, array(), 'Admin.Modules.Feature');
            }
        } catch (Throwable $e) { /* Defaults are safe. */ }

        return $labels;
    }

    private static function getMarketplaceUpdates($modules)
    {
        $updates = array();

        try {
            if (!Module::isInstalled('ps_mbo')) {
                return $updates;
            }

            $mbo = Module::getInstanceByName('ps_mbo');

            if (!$mbo || !method_exists($mbo, 'get')) {
                return $updates;
            }

            $helper = $mbo->get('mbo.modules.helper');

            if (!$helper || !method_exists($helper, 'findForUpdates')) {
                return $updates;
            }

            foreach ($modules as $module) {

                $name = is_array($module)
                    ? ($module['name'] ?? '')
                    : ($module->name ?? '');

                if (!$name) {
                    continue;
                }

                try {
                    $info = $helper->findForUpdates($name);

                    if (!is_array($info)) {
                        continue;
                    }

                    if (!empty($info['upgrade_available'])) {
                        $updates[$name] = $info;
                    }

                } catch (\Throwable $e) {
                    continue;
                }
            }

        } catch (\Throwable $e) {
            PrestaShopLogger::addLog(
                'DATALOG123 - Marketplace updates: ' . $e->getMessage(),
                2
            );
        }

        return $updates;
    }
}