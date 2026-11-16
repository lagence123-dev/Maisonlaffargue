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

include_once dirname(__FILE__) . '/classes/Zentria_AesCipher.php';

$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

class Zentria extends Module
{
    protected $config_form = false;

    private $container;

    public static $distantServerUrl = 'https://zentria.profileo.com/';

    public static $distantTreatmentServerDomain = 'api.zentriaapp.com';

    public static $forAddons = false;

    /** @var string[] all hexadecimal color code for tpl */
    const CODES_HEX = [
        'success' => '#20D9AD',
        'warning' => '#F4AC00',
        'critical' => '#F87071',
        'default' => '#000046',
    ];

    public function __construct()
    {
        $this->name = 'zentria';
        $this->author = 'Zentria';
        $this->tab = 'administration';
        $this->version = '1.3.4';
        $this->need_instance = 1;
        $this->bootstrap = true;
        $this->module_key = 'da538776c24df7ee5b7505e6c29395af';

        parent::__construct();

        $this->displayName = $this->l('Zentria');
        $this->description = $this->l('Zentria Services Wrapper');

        $this->confirmUninstall = $this->l('Are you sure ?');

        $this->ps_versions_compliancy = [
            'min' => '1.6.0.0',
            'max' => '9.0.99',
        ];

        // Check last version each 6 hours
        if (!Configuration::getGlobalValue('VERS_' . strtoupper($this->name) . '_CHECK') || (int) Configuration::getGlobalValue('VERS_' . strtoupper($this->name) . '_CHECK', time()) < time() - 60 * 60 * 6 || !Configuration::getGlobalValue('VERS_' . strtoupper($this->name))) {
            $this->updateLastVersionKnownFromServer();
        }

        // If we just upgraded the module version
        if ((int) Tools::getValue($this->name . 'DoUpgrade') == 1) {
            $this->upgrade();

            // redirect to after upgrade
            $redirectLink = $this->context->link->getAdminLink(Tools::getValue('controller')) . '&' . $this->name . 'Upgraded=1';
            Tools::redirectAdmin($redirectLink);
        }

        // Check last version
        if ((int) Tools::getValue($this->name . 'CheckUpgradeUrl') == 1) {
            $this->updateLastVersionKnownFromServer();
            echo json_encode(['needUpgrade' => $this->version != Configuration::getGlobalValue('VERS_' . strtoupper($this->name))]);
            exit;
        }

        // If we just upgraded the module version
        if ((int) Tools::getValue($this->name . 'Upgraded') == 1) {
            $this->afterUpgrade();

            // Redirect with confirmation message
            $redirectLink = $this->context->link->getAdminLink(Tools::getValue('controller')) . '&conf=4';
            Tools::redirectAdmin($redirectLink);
        }

        // Check if Prestashop Addons integration can be done
        if ($this->loadAddonsIntegration() && $this->container === null) {
            $this->container = new \PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer(
                $this->name,
                $this->getLocalPath()
            );
        }
    }

    public function install()
    {
        $result = parent::install() && $this->registerHooks();

        Configuration::updateGlobalValue('ZENTRIA_TOKEN_REASON', 'Module fresh installation');

        // Check if module is for AddOns marketplace, checking function will save configuration if not exist
        self::isForAddons();

        $this->generateCommunicationToken();

        return $result;
    }

    public function uninstall()
    {
        $keys = [
            'ZENTRIA_GLOBAL_SCORE',
            'ZENTRIA_GLOBAL_COLOR',
            'ZENTRIA_SPEED_SCORE',
            'ZENTRIA_SPEED_COLOR',
            'ZENTRIA_SECURITY_SCORE',
            'ZENTRIA_SECURITY_COLOR',
            'ZENTRIA_NB_ACTIONS_REQUIRED',
            'ZENTRIA_SUBSCRIPTION_ACTIVE',
            'ZENTRIA_TOKEN_SERV',
            'ZENTRIA_TOKEN_THIRD',
            'ZENTRIA_TOKEN_TIMESTAMP',
            'ZENTRIA_TUTORIAL_DONE',
            'ZENTRIA_WIDGET',
            'ZENTRIA_NOTIFY_ACCOUNT_DETAILS',
            'VERS_ZENTRIA',
            'VERS_ZENTRIA_CHECK',
            'ZENTRIA_SUBSCRIPTION_LOCK',
            'ZENTRIA_TOKEN_REASON',
        ];

        foreach ($keys as $key) {
            Configuration::deleteByName($key);
        }
        return parent::uninstall();
    }

    /**
     * Return if module is distributed for addOns or not
     * Function will save value from $forAddons if configuration not exist yet
     *
     * @return bool
     */
    public static function isForAddons()
    {
        // If key not exist yet(due to and upgrade manual) then save it
        // We cast to int because prestashop not allow to save in case of false value
        if (!Configuration::hasKey('ZENTRIA_FOR_ADDONS')) {
            Configuration::updateGlobalValue('ZENTRIA_FOR_ADDONS', (int) self::$forAddons);
        }

        return (bool) Configuration::getGlobalValue('ZENTRIA_FOR_ADDONS');
    }

    public static function getDistantTreatmentUrl($withProtocol = true)
    {
        $distantTreatmentServerUrl = Zentria::$distantTreatmentServerDomain;
        if (strpos($_SERVER['SERVER_NAME'], '.lan')) {
            $distantTreatmentServerUrl = str_replace('.com', '.lan', $distantTreatmentServerUrl);
        }
        if ($withProtocol) {
            $distantTreatmentServerUrl = 'https://' . $distantTreatmentServerUrl . '/';
        }
        return $distantTreatmentServerUrl;
    }

    /**
     * Retrieve the service
     *
     * @param string $serviceName
     *
     * @return false|object|\PrestaShop\PsAccountsInstaller\Installer\Facade\PsAccounts|null
     */
    public function getService($serviceName = null)
    {
        // If Prestashop version is not between 1.6.1.0 & 1.6.1.99 then SF method $this->getService() not exist yet
        if ($this->loadAddonsIntegration('1.6.1.0') && !$this->loadAddonsIntegration('1.6.1.99')) {
            try {
                $accountsInstaller = new PrestaShop\PsAccountsInstaller\Installer\Installer('5.0');

                return new PrestaShop\PsAccountsInstaller\Installer\Facade\PsAccounts($accountsInstaller);
            } catch (Exception $e) {
                return false;
            }
        } else {
            return $this->container->getService($serviceName);
        }
    }

    protected function registerHooks()
    {
        return $this->registerHook([
            'actionAdminControllerSetMedia',
            'displayBackOfficeFooter',
            'actionModuleUnRegisterHookBefore',
            'actionModuleInstallAfter',
        ]);
    }

    public function getLocalPath()
    {
        return $this->local_path;
    }

    public function getPath()
    {
        return _PS_ROOT_DIR_ . $this->_path;
    }

    public function getName()
    {
        return $this->name;
    }

    public function hookActionAdminControllerSetMedia()
    {
        // Load our assets only if we are on admin modules & we are on configuration of this module name
        if (Tools::getValue('controller') == 'AdminModules' && Tools::getValue('configure') == $this->name) {
            // Mains assets for configuration module page
            $this->context->controller->addCSS($this->_path . 'views/css/configuration.css');
            $this->context->controller->addJS($this->_path . 'views/js/back.js?v=' . $this->version, false);
            // Assets for upgrade modal
            $this->context->controller->addCSS($this->_path . 'views/css/upgrade_modal.css');
            $this->context->controller->addJS($this->_path . 'views/js/upgrade_modal.js?v=' . $this->version, false);
        }

        if ($this->doShowOnboarding() && Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/onboarding.css');
            $this->context->controller->addJS($this->_path . 'views/js/onboarding.js?v=' . $this->version, false);
        }

        $this->context->controller->addJS($this->_path . 'views/js/jquery.growl.js?v=' . $this->version, false);
        $this->context->controller->addCSS($this->_path . 'views/css/jquery.growl.css');

        if (Configuration::getGlobalValue('ZENTRIA_WIDGET')) {
            $this->context->controller->addCSS($this->_path . 'views/css/zentria_widget.css');
            $this->context->controller->addJS($this->_path . 'views/js/zentria_widget.js?v=' . $this->version, false);
        }

        // compatibility with ochat
        $ochat = Module::getInstanceByName('onlinechat');
        $seoprestamenu = Module::getInstanceByName('seoprestamenu');

        if (!$ochat || !$ochat->active || !$seoprestamenu || !$seoprestamenu->active) {
            $this->context->controller->addJS($this->_path . 'views/js/sweetalert2.min.js?v=' . $this->version);
            $this->context->controller->addCSS($this->_path . 'views/css/sweetalert2.min.css');
        }
        $this->context->controller->addJqueryPlugin([
            'fancybox',
        ]);
    }

    public function hookActionModuleUnRegisterHookBefore($module)
    {
        $this->hookActionModuleInstallAfter($module);
    }

    public function hookActionModuleInstallAfter($module)
    {
    }

    protected function isSecure()
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTP_HTTPS']) && $_SERVER['HTTP_HTTPS'] !== 'off') || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || (!empty($_SERVER['HTTP_REQUEST_SCHEME']) && $_SERVER['HTTP_REQUEST_SCHEME'] == 'https');
    }

    protected function updateLastVersionKnownFromServer()
    {
        if (self::isForAddons()) {
            // If addons redistributable, only use addons native upgrade system
        } else {
            $lastVersion = file_get_contents(self::$distantServerUrl . 'release/' . $this->name . '.version?timestamp=' . time());
            Configuration::updateGlobalValue('VERS_ZENTRIA', $lastVersion);
        }
        Configuration::updateGlobalValue('VERS_ZENTRIA_CHECK', time());
    }

    protected function afterUpgrade()
    {
        // Check hooks and register if needed
        $this->registerHooks();

        // Generate communication token for "sondes"
        $this->generateCommunicationToken();
    }

    public function generateCommunicationToken()
    {
        // Check if lock is older than 20 seconds, and if so: then release it
        if (Configuration::getGlobalValue('ZENTRIA_SUBSCRIPTION_LOCK') && Configuration::getGlobalValue('ZENTRIA_SUBSCRIPTION_LOCK') < (time() - 20)) {
            Configuration::updateGlobalValue('ZENTRIA_SUBSCRIPTION_LOCK', '');
        }

        // Generate communication token for "sondes"
        if (!Configuration::getGlobalValue('ZENTRIA_TOKEN_SERV') && !Configuration::getGlobalValue('ZENTRIA_SUBSCRIPTION_LOCK')) {
            // Put lock to avoid concurrent accesses
            Configuration::updateGlobalValue('ZENTRIA_SUBSCRIPTION_LOCK', time());
            $tokenThird = md5(openssl_random_pseudo_bytes(16)) . '-' . md5(openssl_random_pseudo_bytes(16));
            Configuration::updateGlobalValue('ZENTRIA_TOKEN_THIRD', $tokenThird);

            $allShops = Shop::getShops(true);
            $allShopsUrls = [];
            foreach ($allShops as $currentShop) {
                $allShopsUrls[] = $currentShop['domain_ssl'];
            }

            // Get the token
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, self::getDistantTreatmentUrl() . 'eo_scripts/token_subscription.php');
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, [
                'tokenThird' => $tokenThird,
                'domains' => json_encode($allShopsUrls),
                'moduleName' => 'zentria',
                'moduleVersion' => $this->version,
                'reason' => urlencode(Configuration::getGlobalValue('ZENTRIA_TOKEN_REASON')),
            ]);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($curl, CURLOPT_TIMEOUT_MS, 2000);
            curl_setopt($curl, CURLOPT_VERBOSE, true);

            $result = curl_exec($curl);
            curl_close($curl);

            if ($result != 'ERROR') {
                $serverToken = Zentria_AesCipher::decrypt($result, $tokenThird);
                Configuration::updateGlobalValue('ZENTRIA_TOKEN_SERV', $serverToken);
            } else {
                Configuration::updateGlobalValue('ZENTRIA_TOKEN_SERV', 0);
                Configuration::updateGlobalValue('ZENTRIA_TOKEN_REASON', 'Token_subscription error');
            }

            // Release lock
            Configuration::updateGlobalValue('ZENTRIA_SUBSCRIPTION_LOCK', '');
        }
    }

    protected function upgrade()
    {
        if (self::isForAddons()) {
            // If addons redistributable, only use addons native upgrade system
            return;
        }

        /**
         * download file
         */
        $distantPath = self::$distantServerUrl . 'release/' . $this->name . '.zip?timestamp=' . time();
        $newVersionDir = $this->getPath() . '/new_version/';
        $data = Tools::file_get_contents($distantPath);
        $dest = $this->getPath() . $this->name . '.zip';
        $fp = fopen($dest, 'wb');
        if ($fp === false) {
            throw new PrestaShopException('Downloading new version error');
        }
        $result = fwrite($fp, $data);
        if ($result === false) {
            throw new PrestaShopException('Writing to local file system error');
        }
        $result = fclose($fp);

        // if dir /new_version/ exists, delete it
        if (is_dir($newVersionDir)) {
            $this->deleteDir($newVersionDir);
        }

        // extract file
        $zip = new ZipArchive();
        if ($zip->open($dest) === true) {
            $zip->extractTo($newVersionDir);
            $zip->close();
        } else {
            throw new PrestaShopException('Extracting ZIP file in local file system error');
        }

        // Clean directory existing
        if ($handle = opendir($this->getPath())) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..' && $entry != 'new_version') {
                    if (is_dir($this->getPath() . $entry)) {
                        $this->deleteDir($this->getPath() . $entry);
                    } else {
                        unlink($this->getPath() . $entry);
                    }
                }
            }

            closedir($handle);
        }

        /**
         * reinject new files
         */
        $pathNewVersion = $newVersionDir . $this->name . '/';

        if ($handle = opendir($pathNewVersion)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != '.' && $entry != '..') {
                    $oldName = $pathNewVersion . $entry;
                    $newName = $this->getPath() . $entry;
                    rename($oldName, $newName);
                }
            }
            closedir($handle);
        }

        // remove dir /new_version/ and the zip
        $this->deleteDir($newVersionDir);
        unlink($dest);

        // Update version in configuration & DB from server downloaded latest update
        $this->updateLastVersionKnownFromServer();
        $last_version = Configuration::get('VERS_' . strtoupper($this->name));
        Db::getInstance()->update('module', [
            'version' => pSQL($last_version),
        ], 'name = "' . pSQL($this->name) . '"');
    }

    protected function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException($dirPath . ' must be a directory');
        }

        if (Tools::substr($dirPath, Tools::strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }

        $files = glob($dirPath . '*', GLOB_MARK);

        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    public function hookDisplayBackOfficeFooter()
    {
        if ($this->isEnabledForShopContext()) {
            // Generate a communication token if not already existing
            $this->generateCommunicationToken();
        }

        $this->checkAndNotifyAccountDetails();

        // Check version in DB vs version of files
        $moduleVersion = Db::getInstance()->getValue('SELECT version FROM ' . _DB_PREFIX_ . 'module WHERE name = \'' . $this->name . '\'');
        if ($moduleVersion != $this->version) {
            Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'module SET version = \'' . $this->version . '\' WHERE name = \'' . $this->name . '\'');
        }

        if (self::isForAddons()) {
            // If addons redistributable, only use addons native upgrade system
            $needUpgrade = false;
        } else {
            $needUpgrade = version_compare($this->version, Configuration::getGlobalValue('VERS_' . strtoupper($this->name)), '<');
        }

        $this->context->smarty->assign([
            'needUpgrade' => $needUpgrade,
            'upgradeUrl' => $this->context->link->getAdminLink(Tools::getValue('controller')) . '&' . $this->name . 'DoUpgrade=1',
            'checkUpgradeUrl' => $this->context->link->getAdminLink(Tools::getValue('controller')) . '&' . $this->name . 'CheckUpgradeUrl=1',
            'displayName' => $this->displayName,
        ]);

        // Check upgrade & display modal to upgrade if upgrade is available
        $this->updateLastVersionKnownFromServer();

        // Check if we are in module configuration page and if module upgrade modal should be displayed
        $upgradeModalTemplate = false;
        if (Tools::getValue('controller') == 'AdminModules' && Tools::getValue('configure') == $this->name) {
            if ($needUpgrade) {
                $this->context->smarty->assign([
                    'need_upgrade' => $needUpgrade,
                ]);
            }

            $upgradeModalTemplate = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/upgrade_modal.tpl');
        }

        if (!Configuration::getGlobalValue('ZENTRIA_WIDGET')) {
            return $upgradeModalTemplate;
        }

        $globalScore = (int) Configuration::getGlobalValue('ZENTRIA_GLOBAL_SCORE');
        $speedScore = (int) Configuration::getGlobalValue('ZENTRIA_SPEED_SCORE');
        $securityScore = (int) Configuration::getGlobalValue('ZENTRIA_SECURITY_SCORE');

        $this->context->smarty->assign([
            'isScoring' => $speedScore || $securityScore,
            'globalScore' => $globalScore,
            'speedScore' => $speedScore,
            'securityScore' => $securityScore,
            'globalColor' => self::getColorCodeHex('ZENTRIA_GLOBAL_COLOR'),
            'speedColor' => self::getColorCodeHex('ZENTRIA_SPEED_COLOR'),
            'securityColor' => self::getColorCodeHex('ZENTRIA_SECURITY_COLOR'),
            'ctaUrl' => 'https://zentria.profileo.com/' . $this->context->language->iso_code . '/index.php?controller=zentriadashboard',
        ]);

        $widgetTemplate = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/zentria_widget.tpl');

        // If we should display upgrade modal, return it with widget
        if ($upgradeModalTemplate) {
            return $widgetTemplate . $upgradeModalTemplate;
        } else {
            return $widgetTemplate;
        }
    }

    /**
     * Init PS_mbo
     *
     * @return bool|void
     */
    public function initPsMBO()
    {
        // Check if Prestashop Addons integration can be done
        if (!$this->loadAddonsIntegration()) {
            return false;
        }

        // Load dependencies manager
        $mboInstaller = new \Prestashop\ModuleLibMboInstaller\DependencyBuilder($this);

        if (!$mboInstaller->areDependenciesMet()) {
            $dependencies = $mboInstaller->handleDependencies();
            $this->context->smarty->assign('dependencies', $dependencies);

            return $this->display(__FILE__, '/views/templates/admin/dependency_builder.tpl');
        }

        return false;
    }

    /**
     * Init PS_Accounts
     *
     * @return string
     * @throws SmartyException
     */
    public function initPsAccounts()
    {
        // Check if Prestashop Addons integration can be done
        if (!$this->loadAddonsIntegration('1.6.1.0')) {
            return '';
        }

        // Check if ps_accounts module exist
        if (!is_dir(_PS_MODULE_DIR_ . 'ps_accounts')) {
            return '';
        }

        // Init ps_account service
        try {
            // install() work only on PS 1.7+, see:
            // zentria/vendor/prestashop/prestashop-accounts-installer/src/Installer/Installer.php - install()
            if (!Module::isInstalled('ps_accounts') && $this->loadAddonsIntegration()) {
                $accountsInstaller = $this->getService('zentria.ps_accounts_installer');
                // Check if method exist
                if (method_exists($accountsInstaller, 'install')) {
                    $accountsInstaller->install();
                }
            }

            $accountsFacade = $this->getService('zentria.ps_accounts_facade');
            // Check if method exist
            if (!method_exists($accountsFacade, 'getPsAccountsService')) {
                return '';
            }

            $accountsService = $accountsFacade->getPsAccountsService();
        } catch (Exception $e) {
            $this->context->controller->errors[] = $e->getMessage();

            return '';
        }

        // Return if not exist
        if ($accountsService) {
            // Assign to JS/Smarty
            try {
                // Check if method exist
                if (method_exists($accountsFacade, 'getPsAccountsPresenter')) {
                    Media::addJsDef([
                        'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()->present($this->name),
                    ]);
                }

                // Retrieve the PrestaShop Account CDN
                $this->context->smarty->assign('urlAccountsCdn', $accountsService->getAccountsCdn());
            } catch (Exception $e) {
                $this->context->controller->errors[] = $e->getMessage();

                return '';
            }
        }

        return $this->context->smarty->fetch($this->local_path . 'views/templates/admin/addons_integration.tpl');
    }

    /**
     * Configuration form to allow or not the widget to be displayed in the backoffice footer
     */
    public function getContent()
    {
        // Check if Prestashop Addons integration can be done
        if ($this->loadAddonsIntegration()) {
            // Init ps_mbo requirement dependencies
            $ps_mbo_depencies = $this->initPsMBO();
            if ($ps_mbo_depencies !== false) {
                return $ps_mbo_depencies;
            }
        }

        if ($this->isEnabledForShopContext()) {
            // Generate a communication token if not already existing
            $this->generateCommunicationToken();
        }

        if (Tools::isSubmit('submit' . $this->name)) {
            Configuration::updateGlobalValue('ZENTRIA_WIDGET', Tools::getValue('ZENTRIA_WIDGET'));

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&conf=4');
        } elseif (Tools::isSubmit('test_communication')) {
            $test_communication = $this->getCommunicationTester();

            $this->context->smarty->assign([
                'test_communication_resp' => $test_communication,
            ]);
        } elseif (Tools::isSubmit('invalidate_token_zentria')) {
            Configuration::deleteByName('ZENTRIA_TOKEN_SERV');
            Configuration::deleteByName('ZENTRIA_TOKEN_THIRD');
            Configuration::updateGlobalValue('ZENTRIA_TOKEN_REASON', 'Backoffice invalidation');
        }

        // Onboarding
        if ($this->doShowOnboarding()) {
            $this->context->smarty->assign([
                'iso_lang' => $this->context->language->iso_code,
                'is_module_enabled' => $this->active,
                'domain_name' => preg_replace('#^https?://|www.|\/#', '', $this->context->shop->getBaseURL(true)),
                'zentria_configuration' => sprintf('%s&configure=%s', $this->context->link->getAdminLink('AdminModules'), $this->name),
            ]);

            return $this->display(__FILE__, '/views/templates/admin/onboarding.tpl');
        }

        return $this->displayForm('ZENTRIA_WIDGET');
    }

    public function displayForm($usersConfigKey)
    {
        $domain_name = preg_replace('#^https?://|www.|\/#', '', $this->context->shop->getBaseURL(true));
        $this->context->smarty->assign([
            'iso_lang' => $this->context->language->iso_code,
            'is_module_enabled' => $this->active,
            'module_version' => $this->version,
            'is_communication_token_set' => Configuration::getGlobalValue('ZENTRIA_TOKEN_SERV') && Configuration::getGlobalValue('ZENTRIA_TOKEN_THIRD'),
            'domain_name' => urlencode($domain_name),
        ]);

        $zentria_settings = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/configuration.tpl');
        $widget_settings = $this->displayForm_widgetConfig($usersConfigKey);

        $ps_addons_configure = $this->initPsAccounts();

        return $ps_addons_configure . $zentria_settings . $widget_settings;
    }

    protected function getHelperForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;        // false -> remove toolbar
        $helper->toolbar_scroll = false;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
        ];

        return $helper;
    }

    /**
     * Configuration form to allow or not the widget to be displayed in the backoffice footer
     *
     * @param string $usersConfigKey
     */
    public function displayForm_widgetConfig($usersConfigKey)
    {
        // Init Fields form array
        $fields_form = [];
        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
            ],
            'description' => $this->l('Here you can configure if you want the widget to be displayed in the backoffice\'s footer or not'),
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('Display the widget'),
                    'name' => $usersConfigKey,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
            ],
        ];

        $helper = $this->getHelperForm();

        // Load DB values
        $helper->fields_value[$usersConfigKey] = Configuration::getGlobalValue($usersConfigKey);

        return $helper->generateForm($fields_form);
    }

    /**
     * Check if the sub is active or inactive
     * @return bool
     */
    protected function isSubscriptionActive()
    {
        return (bool) Configuration::getGlobalValue('ZENTRIA_SUBSCRIPTION_ACTIVE');
    }

    /**
     * Get hexa color for a given configuration value score
     *
     * @param string $key
     *
     * @return string
     */
    public static function getColorCodeHex($key)
    {
        $config_color = Configuration::get($key);

        if ($config_color !== false && array_key_exists($config_color, self::CODES_HEX)) {
            return self::CODES_HEX[$config_color];
        }

        // If key not exist return default value
        return self::CODES_HEX['default'];
    }

    /**
     * Check if we can etablish a connection with Zentria Server
     *
     * @return array
     */
    public function getCommunicationTester()
    {
        // Get token third
        $token_third = Configuration::getGlobalValue('ZENTRIA_TOKEN_THIRD');
        // Check if a test was made recently
        $lastTestTimestamp = (int) Configuration::getGlobalValue('ZENTRIA_TOKEN_TIMESTAMP');
        $currentTimestamp = time();

        if ($lastTestTimestamp > 0 && ($currentTimestamp - $lastTestTimestamp) < 60) {
            // Less than one minute since last test
            return [
              'err' => true,
              'msg' => $this->l('Too many attempts, please try again in one minute'),
            ];
        }

        // Update timestamp of last test
        Configuration::updateGlobalValue('ZENTRIA_TOKEN_TIMESTAMP', $currentTimestamp);

        // Prepare URL
        $url = Zentria::getDistantTreatmentUrl() . 'tourcontrole_connector/connector.php?action=communicationTester&tokenThird=' . $token_third;

        // Call with curl and get answer
        $output = '';
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            if (strpos($_SERVER['SERVER_NAME'], '.lan')) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            }
            $output = curl_exec($ch);
            curl_close($ch);
        } else {
            // Communication failed curl not available
            return [
                'err' => true,
                'msg' => $this->l('Communication with Zentria server failed'),
                'url_controller' => 'contact',
                'url_controller_label' => $this->l('Contact us'),
            ];
        }

        // Decrypt answer
        $output = Zentria_AesCipher::decrypt($output, Configuration::getGlobalValue('ZENTRIA_TOKEN_SERV'));
        if ($output && strpos($output, 'ok') !== false) {
            // Communication succeed
            return [
                'err' => false,
                'msg' => $this->l('Communication test with Zentria server successful'),
            ];
        } else {
            // Communication failed
            return [
                'err' => true,
                'msg' => $this->l('Communication with Zentria server failed'),
                'url_controller' => 'contact',
                'url_controller_label' => $this->l('Contact us'),
            ];
        }
    }

    public function ajaxProcessValidateOnboarding()
    {
        Configuration::updateGlobalValue('ZENTRIA_TUTORIAL_DONE', 1);
        exit;
    }

    /**
     * Check if we need to display the onboarding or not
     * @return bool
     */
    protected function doShowOnboarding()
    {
        return !$this->isSubscriptionActive() && !Configuration::getGlobalValue('ZENTRIA_TUTORIAL_DONE');
    }

    /**
     * Return if AddOns integration can be loaded
     *
     * @return bool
     */
    private function loadAddonsIntegration($min_version = '1.7.0.0')
    {
        // Check if Prestashop running version under $min_version, ps_accounts and other things work only on Prestashop greater than $min_version
        if (!self::isForAddons() || version_compare(_PS_VERSION_, $min_version, '<')) {
            return false;
        }

        return true;
    }

    /**
     * Return informations about AddOns user
     *
     * @return array
     */
    private function getAddonsAccountDetails()
    {
        $domains = array_column(Shop::getShops(true), 'domain_ssl');
        // Check if Prestashop Addons integration can be done
        if (!$this->loadAddonsIntegration('1.6.1.0')) {
            return [
                'ownerUuid' => 'not_compatible_1.6.0.x',
                'shopUuid' => 'not_compatible_1.6.0',
                'domains' => $domains,
            ];
        }

        // Check if module ps_accounts is installed to continue
        if (!Module::isInstalled('ps_accounts')) {
            return [
                'ownerUuid' => 'ps_accounts_not_installed',
                'shopUuid' => 'ps_accounts_not_installed',
                'domains' => $domains,
            ];
        }

        $accountsFacade = $this->getService('zentria.ps_accounts_facade');

        // Check if method getPsAccountsService() exist
        if (!method_exists($accountsFacade, 'getPsAccountsService')) {
            return [
                'ownerUuid' => 'ps_accounts_no_account_facade',
                'shopUuid' => 'ps_accounts_no_account_facade',
                'domains' => $domains,
            ];
        }

        $psAccounts = $accountsFacade->getPsAccountsService();

        // Check if function exists
        if (!method_exists($psAccounts, 'isAccountLinked')) {
            return [
                'ownerUuid' => 'ps_accounts_no_method_isAccountLinked',
                'shopUuid' => 'ps_accounts_no_method_isAccountLinked',
                'domains' => $domains,
            ];
        }

        // Check if function exist
        if (method_exists($psAccounts, 'isAccountLinked') && $psAccounts->isAccountLinked()) {
            $ownerUuid = 'not_logged';
            $shopUuid = 'not_logged';

            // If function exist, return user UUID
            if (method_exists($psAccounts, 'getUserUuid')) {
                $ownerUuid = $psAccounts->getUserUuid();
            }

            // If function exist, return shop UUID
            if (method_exists($psAccounts, 'getShopUuid')) {
                $shopUuid = $psAccounts->getShopUuid();
            }

            return [
                'ownerUuid' => $ownerUuid,
                'shopUuid' => $shopUuid,
                'domains' => $domains,
            ];
        } else {
            return [
                'ownerUuid' => 'not_logged',
                'shopUuid' => 'not_logged',
                'domains' => $domains,
            ];
        }
    }

    /**
     * Check if account linking with addons has changed, and if so: then notify the datas
     */
    private function checkAndNotifyAccountDetails()
    {
        // Notify only if : module was downloaded from addons and we did not notify last hour
        if (self::isForAddons() && $this->shouldNotifyAccountDetails()) {
            $accountDetails = $this->getAddonsAccountDetails();
            $owner_uuid_configuration = Configuration::getGlobalValue('ZENTRIA_OWNER_UUID');
            $shop_uuid_configuration = Configuration::getGlobalValue('ZENTRIA_SHOP_UUID');

            // If configurations not exists, or exist but different from current one, send them
            $ownerUuid = $accountDetails['ownerUuid'];
            $shopUuid = $accountDetails['shopUuid'];
            $domains = $accountDetails['domains'];
            if ((!$owner_uuid_configuration && !$shop_uuid_configuration)
                || $owner_uuid_configuration != $ownerUuid
                || $shop_uuid_configuration != $shopUuid) {
                $token_third = Configuration::getGlobalValue('ZENTRIA_TOKEN_THIRD');

                // Prepare URL
                $url = Zentria::getDistantTreatmentUrl() . 'eo_scripts/zentria_addons_accounts.php';
                // User agent
                $user_agent = 'ZentriaModule/' . $this->version . ' (source; ' . $this->context->shop->getBaseURL(true) . ')';

                $post_fields = [
                    'tokenThird' => $token_third,
                    'domains' => json_encode($domains),
                    'ownerUuid' => $ownerUuid,
                    'shopUuid' => $shopUuid,
                    'loggedAdminEmail' => isset($this->context->employee->email) ? $this->context->employee->email : 'unknown_admin_email',
                ];

                // Get the token
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($curl, CURLOPT_TIMEOUT_MS, 2000);
                if (strpos($_SERVER['SERVER_NAME'], '.lan')) {
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                }

                $result = curl_exec($curl);
                curl_close($curl);

                if ($result != 'ERROR') {
                    Configuration::updateGlobalValue('ZENTRIA_OWNER_UUID', $ownerUuid);
                    Configuration::updateGlobalValue('ZENTRIA_SHOP_UUID', $shopUuid);
                } else {
                    Configuration::updateGlobalValue('ZENTRIA_OWNER_UUID', 'error_distant_call');
                    Configuration::updateGlobalValue('ZENTRIA_SHOP_UUID', 'error_distant_call');
                }
            }
        }
    }

    /**
     * Return if account details can be send, this is a securization to not send more than 1 time per hour
     *
     * @return bool
     */
    private function shouldNotifyAccountDetails()
    {
        $last_update = Configuration::getGlobalValue('ZENTRIA_NOTIFY_ACCOUNT_DETAILS');

        // Check if configuration exist or if last update is older than past hour
        if (!$last_update || (time() - (int) $last_update) > 3600) {
            Configuration::updateGlobalValue('ZENTRIA_NOTIFY_ACCOUNT_DETAILS', time());

            return true;
        }

        return false;
    }
}
