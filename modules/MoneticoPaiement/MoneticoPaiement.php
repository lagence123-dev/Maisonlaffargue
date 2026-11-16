<?php

/*
 * On 27/01/2026 tested on:
 * - Prestashop 9.0.2
 * - PHP 8.4.16
 *
 * On 27/01/2026 tested on:
 * - Prestashop 8.2.0
 * - PHP 8.2.12
 *
 * On 27/01/2026 tested on:
 * - Prestashop 1.7.8
 * - PHP 7.4.30
 */

use MoneticoPaiement\Model\MoneticoPaiementCodeSite;
use MoneticoPaiement\Model\MoneticoPaiementEPT;
use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Model\MoneticoPaiementTransaction;
use MoneticoPaiement\Prestashop\SupportDataFile\CMSDataCollector;
use MoneticoPaiement\Prestashop\SupportDataFile\MoneticoDataCollector;
use MoneticoPaiement\Prestashop\utilities\PaymentStatus;
use MoneticoPaiement\Sdk\Api\HostedFields\PaymentMeanTokenGenerator;
use MoneticoPaiement\Sdk\Api\HostedFields\PaymentMeanTokenGenerator\{Context, Response};
use MoneticoPaiement\Sdk\Api\MoneticoPaiementApi;
use MoneticoPaiement\Sdk\Api\Payment\Capture;
use MoneticoPaiement\Sdk\Api\Payment\State;
use MoneticoPaiement\Sdk\Api\PaymentSession\PaymentSessionInitiation;
use MoneticoPaiement\Sdk\Utilities\Collections\Currency;
use MoneticoPaiement\Sdk\Utilities\Collections\Language;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentDisplayMethod;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentEnvironment;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentMode;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentProtocol;
use MoneticoPaiement\Sdk\Utilities\Collections\ThreeDSecureChallengeValue;
use MoneticoPaiement\Sdk\Utilities\Helper;
use MoneticoPaiement\Sdk\Utilities\HmacComputer;
use MoneticoPaiement\Sdk\Utilities\Models\MerchantConfiguration;
use MoneticoPaiement\Sdk\Utilities\SupportDataFile\LogFileCollector;
use MoneticoPaiement\Sdk\Utilities\SupportDataFile\MainInfoFileCollector;
use MoneticoPaiement\Sdk\Utilities\SupportDataFile\TechDataCollector;
use MoneticoPaiement\Sdk\Utilities\SupportDataFile\ZipFileBuilder;
use MoneticoPaiement\Sdk\Utilities\Traits\LogoWithAdditionalCards;
use MoneticoPaiement\Sdk\Utilities\Traits\WhiteList;
use MoneticoPaiement\utilities\PaymentTax;
use PrestaShop\PrestaShop\Adapter\Configuration as ConfigurationService;
use PrestaShop\PrestaShop\Adapter\Entity\Language as PrestashopLanguage;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopLogger;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use Symfony\Component\Form\Extension\Core\Type\TextType;

if (!defined('_PS_VERSION_')) {
    exit;
}

// Define ActionsBarButton class to use for PS >= 9
if (class_exists(PrestaShop\PrestaShop\Core\Action\ActionsBarButton::class)) {
    class_alias(PrestaShop\PrestaShop\Core\Action\ActionsBarButton::class, 'ActionsBarButton');

// Define ActionsBarButton class to use for PS < 9
} elseif (class_exists(PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton::class)) {
    class_alias(PrestaShopBundle\Controller\Admin\Sell\Order\ActionsBarButton::class, 'ActionsBarButton');

} else{
    throw new RuntimeException('ActionsBarButton class not found for this version');
}

//load SDK
include_once(dirname(__FILE__) . '/sdk/autoload.php');
//load class
require_once(dirname(__FILE__) . '/class/autoload.class.php');
//load utilities
require_once dirname(__FILE__) . '/utilities/autoload.utilities.php';

require_once dirname(__FILE__) . '/functions-polyfill.php';

class MoneticoPaiement extends \PaymentModule
{
    use WhiteList, LogoWithAdditionalCards, Apis, PaymentTax;

    public const MODULE_NAME = 'MoneticoPaiement';

    /**
     * @var string[]
     * Hook after PS 1.7.7.0
     */
    public $hookAfter1770 = [
        'paymentOptions',
        'actionGetAdminOrderButtons',
        'displayAdminOrderMainBottom',
        'displayAdminOrderTop',
        'displayAdminCustomers',
        'actionOrderGridQueryBuilderModifier',
        'actionOrderGridDefinitionModifier',
        'actionOrderStatusUpdate',
        'actionAdminControllerSetMedia',
        'actionAdminControllerInitAfter',
        'displayDashboardTop',
        'actionPresentPaymentOptions',
        'actionFrontControllerSetMedia',
        'displayPaymentByBinaries',
        'displayOrderDetail'
    ];

    /**
     * @var string[]
     * Hook before 1.7.7.0
     */
    public $hookBefore1770 = [
        'paymentOptions',
        'displayBackOfficeOrderActions',
        'displayAdminOrderLeft',
        'displayAdminCustomers',
        'actionOrderGridQueryBuilderModifier',
        'actionOrderGridDefinitionModifier',
        'actionOrderStatusUpdate',
        'actionFrontControllerSetMedia',
        'displayPaymentByBinaries',
        'actionAdminControllerSetMedia'
    ];

    private $demo_ept_numbers;

    private ConfigurationService $configuration;

    public function __construct(ConfigurationService $configuration)
    {
        $this->configuration = $configuration;

        $this->name = self::MODULE_NAME;
        $this->tab = 'payments_gateways';
        $this->version = '1.5.2'; // Actual version
        $this->author = 'Monetico Paiement';
        $this->ps_versions_compliancy = [ // Compatibility
            'min' => '1.7.8.0',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = 'Monetico Paiement';
        $this->description = $this->l('Payment module for Monetico Paiement solution');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this plugin ?');

        $this->setDemoEptNumbers();
    }

    public function mode_demo_active(): bool
    {
        return boolval(Configuration::get('MP_DEMO_MODE_ACTIVE'));
    }

    public function setDemoEptNumbers()
    {
        $demo_data = $this->getDemoData();

        $demo_ept_numbers = [];
        foreach ($demo_data as $payment_data) {
            $demo_ept_numbers[] = $payment_data['ept_number'];
        }
        $this->demo_ept_numbers = implode(',', $demo_ept_numbers);
    }

    public function getDemoEptNumbers()
    {
        return $this->demo_ept_numbers;
    }

    /**
     * @return array<string, array{ept_number: string, ept_mac: string, code_site: string, code_site_emu: string}> Top level key is uppercase payment mode.
     */
    public function getDemoData(): array
    {
        $ept_demo_data = _PS_MODULE_DIR_ . $this->name . '/sdk/Utilities/Assets/demo_ept_data.json';
        $data = file_get_contents($ept_demo_data, true);

        return json_decode($data, true);
    }

    /**
     * Install instructions
     * @return bool
     */
    public function install()
    {
        // Parent installation
        $parent_installation = parent::install();
        if (!$parent_installation) {
            $this->createLog($this->l('The parent installation has failed'), 3);
            return false;
        }
        // Database installation
        $database_installation = $this->installDB();
        if (!$database_installation) {
            $this->createLog($this->l('The database installation has failed'), 3);
            return false;
        }

        // Order states installation
        $order_states_installation = $this->installOrderstates();
        if (!$order_states_installation) {
            $this->createLog($this->l('The database installation has failed'), 3);
            return false;
        }

        // Tabs installation
        $tabs_installation = $this->installTabs();
        if (!$tabs_installation) {
            $this->createLog($this->l('The tabs installation has failed'), 3);
            return false;
        }

        $update_version = Configuration::updateValue('MP_UpdateVersion', $this->version);
        if (!$update_version) {
            $this->createLog($this->l('The update version has not be updated'), 3);
            return false;
        }

        $this->recordNewVersionInstallation();

        // Hooks installation
        foreach (array_merge($this->hookAfter1770, $this->hookBefore1770) as $hook) {
            if (!$this->registerHook($hook)) {
                $this->createLog(sprintf($this->l('The hook %s installation has failed'), $hook), 3);
                return false;
            }
        }

        $this->recordSetupAuditLogs('module_installed', false, true);
        // Note that installation comes just *after* enabling

        return true;
    }

    /**
     * Install database
     * @return bool
     */
    public function installDB(): bool
    {
        $return = true;

        // install ept
        $return &= \Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . pSQL(_DB_PREFIX_) . 'monetico_ept` (
                `id_ept` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `ept_number` VARCHAR(64) NOT NULL,
                `ept_mac` VARCHAR(64) NOT NULL,
                `ept_payment_mode` VARCHAR(64) NOT NULL,
                `ept_instalment_default` BOOLEAN NULL,
                `ept_instalment_term_nb` INT(1) NULL,
                `ept_instalment_pct_1` INT(2) NULL,
                `ept_instalment_pct_2` INT(2) NULL,
                `ept_instalment_pct_3` INT(2) NULL,
                `ept_instalment_pct_4` INT(2) NULL,
                PRIMARY KEY (`id_ept`)
            ) ENGINE=' . pSQL(_MYSQL_ENGINE_) . ' DEFAULT CHARSET=utf8 ;');

        if (!$return) {
            $this->createLog($this->l('The monetico ETP database creation has  failed'), 3);
            return $return;
        }
        // install code site
        $return &= \Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . pSQL(_DB_PREFIX_) . 'monetico_code_site` (
                `id_code_site` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `code_site_ept_id` INT NOT NULL,
                `code_site_name` VARCHAR(64) NOT NULL,
                `code_site_payment_method` VARCHAR(64) NOT NULL,
                `code_site_display_name` VARCHAR(64) NOT NULL,
                `code_site_langue` VARCHAR(64) NOT NULL,
                `code_site_express_payment`BOOLEAN NOT NULL,
                `code_site_active` BOOLEAN NOT NULL,
                `code_site_sandbox` BOOLEAN NOT NULL,
                `code_site_amount_min`  FLOAT unsigned,
                `code_site_amount_max`  FLOAT unsigned,
                PRIMARY KEY (`id_code_site`)
            ) ENGINE=' . pSQL(_MYSQL_ENGINE_) . ' DEFAULT CHARSET=utf8 ;');

        if (!$return) {
            $this->createLog($this->l('The monetico company code table creation has failed'), 3);
            return $return;
        }
        // install code site 3dsv2
        $return &= \Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . pSQL(_DB_PREFIX_) . 'monetico_code_site_3dsv2` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `code_site_id` INT NOT NULL,
                `amount`  FLOAT NOT NULL,
                `state` VARCHAR(64) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=' . pSQL(_MYSQL_ENGINE_) . ' DEFAULT CHARSET=utf8 ;');

        if (!$return) {
            $this->createLog($this->l('The monetico company code 3dsv2 table creation has failed'), 3);
            return $return;
        }
        // install code site options
        $return &= \Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . pSQL(_DB_PREFIX_) . 'monetico_code_site_opts` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `code_site_id` INT NOT NULL,
                `opt` VARCHAR(64) NOT NULL,
                `value` VARCHAR(64) NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=' . pSQL(_MYSQL_ENGINE_) . ' DEFAULT CHARSET=utf8 ;');

        if (!$return) {
            $this->createLog($this->l('The monetico company code opts table creation has failed'), 3);
            return $return;
        }


        // install transaction
        $return &= \Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . pSQL(_DB_PREFIX_) . 'monetico_transaction` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_ref` VARCHAR(64) NOT NULL,
                `cart_id` INT NOT NULL,
                `code_site_id` INT NOT NULL,
                `MAC` VARCHAR(40) NOT NULL,
                `order_context` TEXT NOT NULL,
                `payment_response_context` TEXT NOT NULL,
                `sandbox_transaction` BOOLEAN NOT NULL,
                `alias_id` INT UNSIGNED DEFAULT 0,
                `has_taxes` BOOLEAN DEFAULT 1, 
                PRIMARY KEY (`id`)
            ) ENGINE=' . pSQL(_MYSQL_ENGINE_) . ' DEFAULT CHARSET=utf8 ;');


        if (!$return) {
            $this->createLog($this->l('The monetico transaction table creation has failed'), 3);
            return $return;
        }


        // install refund
        $return &= \Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `' . pSQL(_DB_PREFIX_) . 'monetico_refund` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` INT NOT NULL,
                `amount` FLOAT NOT NULL,
                `refund_context` TEXT NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=' . pSQL(_MYSQL_ENGINE_) . ' DEFAULT CHARSET=utf8 ;');

        if (!$return) {
            $this->createLog($this->l('The monetico refund table creation has  failed'), 3);
            return $return;
        }

        $return &= \Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . pSQL(_DB_PREFIX_) . 'monetico_alias_bc` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `code_site_id` int(11) NOT NULL,
        `user_id` int(11) NOT NULL,
        `alias` varchar(64) NOT NULL,
        `certified` BOOLEAN NOT NULL,
         PRIMARY KEY (`id`)
        ) ENGINE=' . pSQL(_MYSQL_ENGINE_) . ' DEFAULT CHARSET=utf8;'
        );

        if (!$return) {
            $this->createLog($this->l('The monetico alias bank Card\'s table creation has  failed'), 3);
            return $return;
        }

        $this->createLog($this->l('The Monetico Paiement tables have been successfully added'));
        return $return;
    }

    /**
     * Add state picture for prestashop < 1.7.7.0
     *
     * @param $id
     */
    public function copyStatusGif($id)
    {
        $return = true;
        if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
            $source = _PS_MODULE_DIR_ . $this->name . '/views/img/os_mp.png';
            $destination = _PS_ROOT_DIR_ . '/img/os/' . (int)$id . '.gif';
            $return = copy($source, $destination);

            if ($return) {
                $this->createLog($this->l('The status gif has been successfully copied'));
            } else {
                $this->createLog($this->l('The status gif has not been copied'), 3);
            }
        }

        return $return;
    }

    public function deleteExistingOrderStateTranslation($config_value)
    {
        if (!Db::getInstance()->delete('order_state_lang', 'id_order_state = ' . (int)$config_value)) {
            $this->createLog(sprintf($this->l('Error while deleting translations of order state « %s ».'), $config_value), 3);
        }
    }

    /**
     * Install Options
     */
    public function installOrderstates(): bool
    {
        $return = true;
        $languages = $this->context->controller->getLanguages();

        // PENDING STATE
        if (!Configuration::get(PaymentStatus::MP_PENDING) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_PENDING), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_PENDING))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_PENDING));
            }

            $mp_pending = new OrderState();
            $mp_pending->name = [];
            PaymentStatus::setMultilingualName($mp_pending, PaymentStatus::MP_PENDING, $languages);
            $mp_pending->invoice = false;
            $mp_pending->send_email = false;
            $mp_pending->module_name = $this->name;
            $mp_pending->color = '#34209E';
            $mp_pending->unremovable = true;
            $mp_pending->hidden = false;
            $mp_pending->logable = true;
            $mp_pending->delivery = false;
            $mp_pending->shipped = false;
            $mp_pending->paid = true;

            if (!$mp_pending->save() || !Configuration::updateValue(PaymentStatus::MP_PENDING, $mp_pending->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Pending payement')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_pending->id);
            }
        }
        // REFUSED STATE
        if (!Configuration::get(PaymentStatus::MP_REFUSED_PAYMENT) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_REFUSED_PAYMENT), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_REFUSED_PAYMENT))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_REFUSED_PAYMENT));
            }

            $mp_refused_payment_state = new OrderState();
            $mp_refused_payment_state->name = [];
            PaymentStatus::setMultilingualName($mp_refused_payment_state, PaymentStatus::MP_REFUSED_PAYMENT, $languages);
            $mp_refused_payment_state->invoice = false;
            $mp_refused_payment_state->send_email = false;
            $mp_refused_payment_state->module_name = $this->name;
            $mp_refused_payment_state->color = '#2C3E50';
            $mp_refused_payment_state->unremovable = true;
            $mp_refused_payment_state->hidden = false;
            $mp_refused_payment_state->logable = true;
            $mp_refused_payment_state->delivery = false;
            $mp_refused_payment_state->shipped = false;
            $mp_refused_payment_state->paid = false;
            if (!$mp_refused_payment_state->save() || !Configuration::updateValue(PaymentStatus::MP_REFUSED_PAYMENT, $mp_refused_payment_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Refused Payment')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_refused_payment_state->id);
            }
        }
        // RECEIVED STATES
        if (!Configuration::get(PaymentStatus::MP_RECEIVED_PAYMENT)) {
            if (!Configuration::get('PS_OS_PAYMENT')) {

                $mp_received_payment_state = new OrderState();
                $mp_received_payment_state->name = [];
                PaymentStatus::setMultilingualName($mp_received_payment_state, PaymentStatus::MP_RECEIVED_PAYMENT, $languages);
                $mp_received_payment_state->invoice = false;
                $mp_received_payment_state->send_email = false;
                $mp_received_payment_state->module_name = $this->name;
                $mp_received_payment_state->color = '#3498D8';
                $mp_received_payment_state->unremovable = true;
                $mp_received_payment_state->hidden = false;
                $mp_received_payment_state->logable = true;
                $mp_received_payment_state->delivery = false;
                $mp_received_payment_state->shipped = false;
                $mp_received_payment_state->paid = true;


                if (!$mp_received_payment_state->save() || !Configuration::updateValue(PaymentStatus::MP_RECEIVED_PAYMENT, $mp_received_payment_state->id)) {
                    $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Payment accepted')), 3);
                    return false;
                } else {
                    $return &= $this->copyStatusGif($mp_received_payment_state->id);
                }

            } else if (!Configuration::updateValue(PaymentStatus::MP_RECEIVED_PAYMENT, Configuration::get('PS_OS_PAYMENT'))) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Payment accepted')), 3);
                return false;
            }
        }

        if (!Configuration::get(PaymentStatus::MP_RECEIVED_PARTIAL_PAYMENT) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_RECEIVED_PARTIAL_PAYMENT), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_RECEIVED_PARTIAL_PAYMENT))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_RECEIVED_PARTIAL_PAYMENT));
            }

            $mp_received_partial_payment_state = new OrderState();
            $mp_received_partial_payment_state->name = [];
            PaymentStatus::setMultilingualName($mp_received_partial_payment_state, PaymentStatus::MP_RECEIVED_PARTIAL_PAYMENT, $languages);
            $mp_received_partial_payment_state->invoice = false;
            $mp_received_partial_payment_state->send_email = false;
            $mp_received_partial_payment_state->module_name = $this->name;
            $mp_received_partial_payment_state->color = '#3498D8';
            $mp_received_partial_payment_state->unremovable = true;
            $mp_received_partial_payment_state->hidden = false;
            $mp_received_partial_payment_state->logable = true;
            $mp_received_partial_payment_state->delivery = false;
            $mp_received_partial_payment_state->shipped = false;
            $mp_received_partial_payment_state->paid = true;

            if (!$mp_received_partial_payment_state->save() || !Configuration::updateValue(PaymentStatus::MP_RECEIVED_PARTIAL_PAYMENT, $mp_received_partial_payment_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Partial recovery')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_received_partial_payment_state->id);
            }
        }


        if (!Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["1"]) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["1"]), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["1"]))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["1"]));
            }

            $mp_received_payment_1_state = new OrderState();
            $mp_received_payment_1_state->name = [];
            PaymentStatus::setMultilingualName($mp_received_payment_1_state, PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["1"], $languages);
            $mp_received_payment_1_state->invoice = false;
            $mp_received_payment_1_state->send_email = false;
            $mp_received_payment_1_state->module_name = $this->name;
            $mp_received_payment_1_state->color = '#3498D8';
            $mp_received_payment_1_state->unremovable = true;
            $mp_received_payment_1_state->hidden = false;
            $mp_received_payment_1_state->logable = true;
            $mp_received_payment_1_state->delivery = false;
            $mp_received_payment_1_state->shipped = false;
            $mp_received_payment_1_state->paid = true;

            if (!$mp_received_payment_1_state->save() || !Configuration::updateValue(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["1"], $mp_received_payment_1_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Received instalment number 1')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_received_payment_1_state->id);
            }
        }


        if (!Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["2"]) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["2"]), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["2"]))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["2"]));
            }

            $mp_received_payment_2_state = new OrderState();
            $mp_received_payment_2_state->name = [];
            PaymentStatus::setMultilingualName($mp_received_payment_2_state, PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["2"], $languages);
            $mp_received_payment_2_state->invoice = false;
            $mp_received_payment_2_state->send_email = false;
            $mp_received_payment_2_state->module_name = $this->name;
            $mp_received_payment_2_state->color = '#3498D8';
            $mp_received_payment_2_state->unremovable = true;
            $mp_received_payment_2_state->hidden = false;
            $mp_received_payment_2_state->logable = true;
            $mp_received_payment_2_state->delivery = false;
            $mp_received_payment_2_state->shipped = false;
            $mp_received_payment_2_state->paid = true;

            if (!$mp_received_payment_2_state->save() || !Configuration::updateValue(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["2"], $mp_received_payment_2_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Received instalment number 2')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_received_payment_2_state->id);
            }
        }

        if (!Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["3"]) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["3"]), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["3"]))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["3"]));
            }

            $mp_received_payment_3_state = new OrderState();
            $mp_received_payment_3_state->name = [];
            PaymentStatus::setMultilingualName($mp_received_payment_3_state, PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["3"], $languages);
            $mp_received_payment_3_state->invoice = false;
            $mp_received_payment_3_state->send_email = false;
            $mp_received_payment_3_state->module_name = $this->name;
            $mp_received_payment_3_state->color = '#3498D8';
            $mp_received_payment_3_state->unremovable = true;
            $mp_received_payment_3_state->hidden = false;
            $mp_received_payment_3_state->logable = true;
            $mp_received_payment_3_state->delivery = false;
            $mp_received_payment_3_state->shipped = false;
            $mp_received_payment_3_state->paid = true;

            if (!$mp_received_payment_3_state->save() || !Configuration::updateValue(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["3"], $mp_received_payment_3_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Received instalment number 3')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_received_payment_3_state->id);
            }
        }

        if (!Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["4"]) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["4"]), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["4"]))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["4"]));
            }

            $mp_received_payment_4_state = new OrderState();
            $mp_received_payment_4_state->name = [];
            PaymentStatus::setMultilingualName($mp_received_payment_4_state, PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["4"], $languages);
            $mp_received_payment_4_state->invoice = false;
            $mp_received_payment_4_state->send_email = false;
            $mp_received_payment_4_state->module_name = $this->name;
            $mp_received_payment_4_state->color = '#3498D8';
            $mp_received_payment_4_state->unremovable = true;
            $mp_received_payment_4_state->hidden = false;
            $mp_received_payment_4_state->logable = true;
            $mp_received_payment_4_state->delivery = false;
            $mp_received_payment_4_state->shipped = false;
            $mp_received_payment_4_state->paid = true;

            if (!$mp_received_payment_4_state->save() || !Configuration::updateValue(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["4"], $mp_received_payment_4_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Payment completed')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_received_payment_4_state->id);
            }
        }
        // SANDBOX STATES
        if (!Configuration::get(PaymentStatus::MP_SANDBOX_PAYMENT) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_SANDBOX_PAYMENT), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_SANDBOX_PAYMENT))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_SANDBOX_PAYMENT));
            }

            $mp_sanbox_payment_state = new OrderState();
            $mp_sanbox_payment_state->name = [];
            PaymentStatus::setMultilingualName($mp_sanbox_payment_state, PaymentStatus::MP_RECEIVED_PAYMENT, $languages, '[SANDBOX]');
            $mp_sanbox_payment_state->invoice = false;
            $mp_sanbox_payment_state->send_email = false;
            $mp_sanbox_payment_state->module_name = $this->name;
            $mp_sanbox_payment_state->color = '#3498D8';
            $mp_sanbox_payment_state->unremovable = true;
            $mp_sanbox_payment_state->hidden = false;
            $mp_sanbox_payment_state->logable = true;
            $mp_sanbox_payment_state->delivery = false;
            $mp_sanbox_payment_state->shipped = false;
            $mp_sanbox_payment_state->paid = true;

            if (!$mp_sanbox_payment_state->save() || !Configuration::updateValue(PaymentStatus::MP_SANDBOX_PAYMENT, $mp_sanbox_payment_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Received sandbox Payment')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_sanbox_payment_state->id);
            }
        }

        // DEMO STATES
        if (!Configuration::get(PaymentStatus::MP_DEMO_PAYMENT) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_DEMO_PAYMENT), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_DEMO_PAYMENT))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_DEMO_PAYMENT));
            }

            $mp_demo_payment_state = new OrderState();
            $mp_demo_payment_state->name = [];
            PaymentStatus::setMultilingualName($mp_demo_payment_state, PaymentStatus::MP_RECEIVED_PAYMENT, $languages, '[DEMO]');
            $mp_demo_payment_state->invoice = false;
            $mp_demo_payment_state->send_email = false;
            $mp_demo_payment_state->module_name = $this->name;
            $mp_demo_payment_state->color = '#3498D8';
            $mp_demo_payment_state->unremovable = true;
            $mp_demo_payment_state->hidden = false;
            $mp_demo_payment_state->logable = true;
            $mp_demo_payment_state->delivery = false;
            $mp_demo_payment_state->shipped = false;
            $mp_demo_payment_state->paid = true;

            if (!$mp_demo_payment_state->save() || !Configuration::updateValue(PaymentStatus::MP_DEMO_PAYMENT, $mp_demo_payment_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Received demo Payment')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_demo_payment_state->id);
            }
        }

        if (!Configuration::get(PaymentStatus::MP_DEMO_RECEIVED_PARTIAL_PAYMENT) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_DEMO_RECEIVED_PARTIAL_PAYMENT), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_DEMO_RECEIVED_PARTIAL_PAYMENT))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_DEMO_RECEIVED_PARTIAL_PAYMENT));
            }

            $mp_demo_received_partial_payment_state = new OrderState();
            $mp_demo_received_partial_payment_state->name = [];
            PaymentStatus::setMultilingualName($mp_demo_received_partial_payment_state, PaymentStatus::MP_RECEIVED_PARTIAL_PAYMENT, $languages, '[DEMO]');
            $mp_demo_received_partial_payment_state->invoice = false;
            $mp_demo_received_partial_payment_state->send_email = false;
            $mp_demo_received_partial_payment_state->module_name = $this->name;
            $mp_demo_received_partial_payment_state->color = '#3498D8';
            $mp_demo_received_partial_payment_state->unremovable = true;
            $mp_demo_received_partial_payment_state->hidden = false;
            $mp_demo_received_partial_payment_state->logable = true;
            $mp_demo_received_partial_payment_state->delivery = false;
            $mp_demo_received_partial_payment_state->shipped = false;
            $mp_demo_received_partial_payment_state->paid = true;

            if (!$mp_demo_received_partial_payment_state->save() || !Configuration::updateValue(PaymentStatus::MP_DEMO_RECEIVED_PARTIAL_PAYMENT, $mp_demo_received_partial_payment_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Demo Partial recovery')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_demo_received_partial_payment_state->id);
            }
        }

        if (!Configuration::get(PaymentStatus::MP_DEMO_RECEIVED_SPLIT_PAYMENT) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_DEMO_RECEIVED_SPLIT_PAYMENT), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_DEMO_RECEIVED_SPLIT_PAYMENT))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_DEMO_RECEIVED_SPLIT_PAYMENT));
            }

            $mp_demo_received_payment_1_state = new OrderState();
            $mp_demo_received_payment_1_state->name = [];
            PaymentStatus::setMultilingualName($mp_demo_received_payment_1_state, PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT["1"], $languages, '[DEMO]');
            $mp_demo_received_payment_1_state->invoice = false;
            $mp_demo_received_payment_1_state->send_email = false;
            $mp_demo_received_payment_1_state->module_name = $this->name;
            $mp_demo_received_payment_1_state->color = '#3498D8';
            $mp_demo_received_payment_1_state->unremovable = true;
            $mp_demo_received_payment_1_state->hidden = false;
            $mp_demo_received_payment_1_state->logable = true;
            $mp_demo_received_payment_1_state->delivery = false;
            $mp_demo_received_payment_1_state->shipped = false;
            $mp_demo_received_payment_1_state->paid = true;

            if (!$mp_demo_received_payment_1_state->save() || !Configuration::updateValue(PaymentStatus::MP_DEMO_RECEIVED_SPLIT_PAYMENT, $mp_demo_received_payment_1_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Received demo instalment number 1')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_demo_received_payment_1_state->id);
            }
        }

        if (!Configuration::get(PaymentStatus::MP_DEMO_CANCELED) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_DEMO_CANCELED), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_DEMO_CANCELED))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_DEMO_CANCELED));
            }

            $mp_demo_canceled = new OrderState();
            $mp_demo_canceled->name = [];
            PaymentStatus::setMultilingualName($mp_demo_canceled, PaymentStatus::MP_DEMO_CANCELED, $languages, '[DEMO]');
            $mp_demo_canceled->invoice = false;
            $mp_demo_canceled->send_email = false;
            $mp_demo_canceled->module_name = $this->name;
            $mp_demo_canceled->color = '#2C3E50';
            $mp_demo_canceled->unremovable = true;
            $mp_demo_canceled->hidden = false;
            $mp_demo_canceled->logable = true;
            $mp_demo_canceled->delivery = false;
            $mp_demo_canceled->shipped = false;
            $mp_demo_canceled->paid = true;

            if (!$mp_demo_canceled->save() || !Configuration::updateValue(PaymentStatus::MP_DEMO_CANCELED, $mp_demo_canceled->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Canceled demo Order')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_demo_canceled->id);
            }
        }

        // ERROR STATES
        if (!Configuration::get(PaymentStatus::MP_WARNING) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_WARNING), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_WARNING))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_WARNING));
            }

            $mp_warning_state = new OrderState();
            $mp_warning_state->name = [];
            PaymentStatus::setMultilingualName($mp_warning_state, PaymentStatus::MP_WARNING, $languages);
            $mp_warning_state->invoice = false;
            $mp_warning_state->send_email = false;
            $mp_warning_state->module_name = $this->name;
            $mp_warning_state->color = '#f8dda7';
            $mp_warning_state->unremovable = true;
            $mp_warning_state->hidden = false;
            $mp_warning_state->logable = true;
            $mp_warning_state->delivery = false;
            $mp_warning_state->shipped = false;
            $mp_warning_state->paid = false;
            if (!$mp_warning_state->save() || !Configuration::updateValue(PaymentStatus::MP_WARNING, $mp_warning_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Warning')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_warning_state->id);
            }
        }

        if (!Configuration::get(PaymentStatus::MP_ERROR_PAYMENT)) {
            if (!Configuration::get('PS_OS_ERROR')) {

                $mp_error_payment_state = new OrderState();
                $mp_error_payment_state->name = [];
                PaymentStatus::setMultilingualName($mp_error_payment_state, PaymentStatus::MP_ERROR_PAYMENT, $languages);
                $mp_error_payment_state->invoice = false;
                $mp_error_payment_state->send_email = false;
                $mp_error_payment_state->module_name = $this->name;
                $mp_error_payment_state->color = '#E74C3C';
                $mp_error_payment_state->unremovable = true;
                $mp_error_payment_state->hidden = false;
                $mp_error_payment_state->logable = true;
                $mp_error_payment_state->delivery = false;
                $mp_error_payment_state->shipped = false;
                $mp_error_payment_state->paid = false;
                if (!$mp_error_payment_state->save() || !Configuration::updateValue(PaymentStatus::MP_ERROR_PAYMENT, Configuration::get('PS_OS_ERROR'))) {
                    $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Error Payment')), 3);
                    return false;
                } else {
                    $return &= $this->copyStatusGif($mp_error_payment_state->id);
                }
            } else if (!Configuration::updateValue('MP_ERROR_PAYMENT', Configuration::get('PS_OS_ERROR'))) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Error Payment')), 3);
                return false;
            }
        }

        if (!Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["1"]) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["1"]), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["1"]))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["1"]));
            }

            $mp_error_payment_1_state = new OrderState();
            $mp_error_payment_1_state->name = [];
            PaymentStatus::setMultilingualName($mp_error_payment_1_state, PaymentStatus::MP_ERROR_SPLIT_PAYMENT["1"], $languages);
            $mp_error_payment_1_state->invoice = false;
            $mp_error_payment_1_state->send_email = false;
            $mp_error_payment_1_state->module_name = $this->name;
            $mp_error_payment_1_state->color = '#E74C3C';
            $mp_error_payment_1_state->unremovable = true;
            $mp_error_payment_1_state->hidden = false;
            $mp_error_payment_1_state->logable = true;
            $mp_error_payment_1_state->delivery = false;
            $mp_error_payment_1_state->shipped = false;
            $mp_error_payment_1_state->paid = false;
            if (!$mp_error_payment_1_state->save() || !Configuration::updateValue(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["1"], $mp_error_payment_1_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Error first Payment')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_error_payment_1_state->id);
            }
        }
        if (!Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["2"]) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["2"]), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["2"]))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["2"]));
            }

            $mp_error_payment_2_state = new OrderState();
            $mp_error_payment_2_state->name = [];
            PaymentStatus::setMultilingualName($mp_error_payment_2_state, PaymentStatus::MP_ERROR_SPLIT_PAYMENT["2"], $languages);
            $mp_error_payment_2_state->invoice = false;
            $mp_error_payment_2_state->send_email = false;
            $mp_error_payment_2_state->module_name = $this->name;
            $mp_error_payment_2_state->color = '#E74C3C';
            $mp_error_payment_2_state->unremovable = true;
            $mp_error_payment_2_state->hidden = false;
            $mp_error_payment_2_state->logable = true;
            $mp_error_payment_2_state->delivery = false;
            $mp_error_payment_2_state->shipped = false;
            $mp_error_payment_2_state->paid = false;

            if (!$mp_error_payment_2_state->save() || !Configuration::updateValue(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["2"], $mp_error_payment_2_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Error second Payment')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_error_payment_2_state->id);
            }
        }
        if (!Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["3"]) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["3"]), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["3"]))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["3"]));
            }

            $mp_error_payment_3_state = new OrderState();
            $mp_error_payment_3_state->name = [];
            PaymentStatus::setMultilingualName($mp_error_payment_3_state, PaymentStatus::MP_ERROR_SPLIT_PAYMENT["3"], $languages);
            $mp_error_payment_3_state->invoice = false;
            $mp_error_payment_3_state->send_email = false;
            $mp_error_payment_3_state->module_name = $this->name;
            $mp_error_payment_3_state->color = '#E74C3C';
            $mp_error_payment_3_state->unremovable = true;
            $mp_error_payment_3_state->hidden = false;
            $mp_error_payment_3_state->logable = true;
            $mp_error_payment_3_state->delivery = false;
            $mp_error_payment_3_state->shipped = false;
            $mp_error_payment_3_state->paid = false;

            if (!$mp_error_payment_3_state->save() || !Configuration::updateValue(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["3"], $mp_error_payment_3_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Error third Payment')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_error_payment_3_state->id);
            }
        }
        if (!Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["4"]) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["4"]), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["4"]))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["4"]));
            }

            $mp_error_payment_4_state = new OrderState();
            $mp_error_payment_4_state->name = [];
            PaymentStatus::setMultilingualName($mp_error_payment_4_state, PaymentStatus::MP_ERROR_SPLIT_PAYMENT["4"], $languages);
            $mp_error_payment_4_state->invoice = false;
            $mp_error_payment_4_state->send_email = false;
            $mp_error_payment_4_state->module_name = $this->name;
            $mp_error_payment_4_state->color = '#E74C3C';
            $mp_error_payment_4_state->unremovable = true;
            $mp_error_payment_4_state->hidden = false;
            $mp_error_payment_4_state->logable = true;
            $mp_error_payment_4_state->delivery = false;
            $mp_error_payment_4_state->shipped = false;
            $mp_error_payment_4_state->paid = false;

            if (!$mp_error_payment_4_state->save() || !Configuration::updateValue(PaymentStatus::MP_ERROR_SPLIT_PAYMENT["4"], $mp_error_payment_4_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Received fourth Payment')), 3);
                return false;
            } else {
                $return = $this->copyStatusGif($mp_error_payment_4_state->id);
            }
        }
        if (!Configuration::get(PaymentStatus::MP_ERROR_PAYMENT_ORDER_TOTAL) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_ERROR_PAYMENT_ORDER_TOTAL), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_ERROR_PAYMENT_ORDER_TOTAL))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_ERROR_PAYMENT_ORDER_TOTAL));
            }

            $mp_error_payment_order_total_state = new OrderState();
            $mp_error_payment_order_total_state->name = [];
            PaymentStatus::setMultilingualName($mp_error_payment_order_total_state, PaymentStatus::MP_ERROR_PAYMENT_ORDER_TOTAL, $languages);
            $mp_error_payment_order_total_state->invoice = false;
            $mp_error_payment_order_total_state->send_email = false;
            $mp_error_payment_order_total_state->module_name = $this->name;
            $mp_error_payment_order_total_state->color = '#E74C3C';
            $mp_error_payment_order_total_state->unremovable = true;
            $mp_error_payment_order_total_state->hidden = false;
            $mp_error_payment_order_total_state->logable = true;
            $mp_error_payment_order_total_state->delivery = false;
            $mp_error_payment_order_total_state->shipped = false;
            $mp_error_payment_order_total_state->paid = false;

            if (!$mp_error_payment_order_total_state->save() || !Configuration::updateValue(PaymentStatus::MP_ERROR_PAYMENT_ORDER_TOTAL, $mp_error_payment_order_total_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Error Payment Order Total')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_error_payment_order_total_state->id);
            }
        }

        // REFUND STATES
        if (!Configuration::get(PaymentStatus::MP_PARTIAL_REFUND) ||
            !OrderState::existsInDatabase(Configuration::get(PaymentStatus::MP_PARTIAL_REFUND), 'order_state')) {

            if (!empty(Configuration::get(PaymentStatus::MP_PARTIAL_REFUND))) {
                $this->deleteExistingOrderStateTranslation(Configuration::get(PaymentStatus::MP_PARTIAL_REFUND));
            }

            $mp_partial_refund_state = new OrderState();
            $mp_partial_refund_state->name = [];
            PaymentStatus::setMultilingualName($mp_partial_refund_state, PaymentStatus::MP_PARTIAL_REFUND, $languages);
            $mp_partial_refund_state->invoice = false;
            $mp_partial_refund_state->send_email = false;
            $mp_partial_refund_state->module_name = $this->name;
            $mp_partial_refund_state->color = '#01B887';
            $mp_partial_refund_state->unremovable = true;
            $mp_partial_refund_state->hidden = false;
            $mp_partial_refund_state->logable = true;
            $mp_partial_refund_state->delivery = false;
            $mp_partial_refund_state->shipped = false;
            $mp_partial_refund_state->paid = false;

            if (!$mp_partial_refund_state->save() || !Configuration::updateValue(PaymentStatus::MP_PARTIAL_REFUND, $mp_partial_refund_state->id)) {
                $this->createLog(sprintf($this->l('Error while creating order status « %s ».'), $this->l('Partial Refund')), 3);
                return false;
            } else {
                $return &= $this->copyStatusGif($mp_partial_refund_state->id);
            }
        }

        $this->createLog($this->l('Order states have been successfully added'));
        return $return;
    }

    /**
     * Install Controller Tabs
     * @return bool
     */
    public function installTabs(): bool
    {
        $languages = $this->context->controller->getLanguages();

        $refund_tab = new Tab();
        $refund_tab->name = [];
        foreach ($languages as $language) {
            $refund_tab->name[$language['id_lang']] = $this->l('Process to refund', false, $language['locale']);
        }
        $refund_tab->class_name = 'AdminMoneticoPaiementRefund';
        $refund_tab->id_parent = 0;
        $refund_tab->module = $this->name;

        if (!$refund_tab->add()) {
            $this->createLog($this->l('An error has occurred during the process to create the refund tab'), 3);
            return false;
        }

        $capture_tab = new Tab();
        $capture_tab->name = [];
        foreach ($languages as $language) {
            $capture_tab->name[$language['id_lang']] = $this->l('Process to capture', false, $language['locale']);
        }
        $capture_tab->class_name = 'AdminMoneticoPaiementCapture';
        $capture_tab->id_parent = 0;
        $capture_tab->module = $this->name;
        if (!$capture_tab->add()) {
            $this->createLog($this->l('An error has occurred during the process to create the capture tab'), 3);
            return false;
        }

        $demo_tab = new Tab();
        $demo_tab->name = [];
        foreach ($languages as $language) {
            $demo_tab->name[$language['id_lang']] = $this->l('Demo mode', false, $language['locale']);
        }
        $demo_tab->class_name = 'AdminMoneticoPaiementDemo';
        $demo_tab->id_parent = 0;
        $demo_tab->module = $this->name;
        if (!$demo_tab->add()) {
            $this->createLog($this->l('An error has occurred during the process to create the demo mode tab'), 3);
            return false;
        }

        MoneticoPaiementHelper::updateTabsName(pSQL(_DB_PREFIX_));
        $this->createLog($this->l('The tabs have been successfully added'));
        return true;
    }

    public function uninstall(): bool
    {
        $success = parent::uninstall();

        if ($success) {
            $this->recordSetupAuditLogs('module_installed', true, false);
        }

        return $success;
    }

    public function enable($force_all = false): bool
    {
        $success = parent::enable($force_all);

        if ($success) {
            $this->recordSetupAuditLogs('module_enabled', false, true);
        }

        return $success;
    }

    public function disable($force_all = false): bool
    {
        $success = parent::disable($force_all);

        if ($success) {
            $this->recordSetupAuditLogs('module_enabled', true, false);
        }

        return $success;
    }

    /**
     * Compare the hook used before and after 1.7.7.0
     */
    public function hookVersionCompare()
    {
        return version_compare(_PS_VERSION_, '1.7.7.0', '>=');
    }

    /**
     * Add a PrestaShop log
     *
     * @param     $message
     * @param int $severity
     */
    public function createLog($message, $severity = 1)
    {
        $log_message = "[" . $this->displayName . "] " . $message;
        if ($severity > 1) {
            $this->_errors[] = $log_message;
        }
        PrestaShopLogger::addLog($log_message, $severity);
    }

    /**
     * print the render
     *
     * @param $errors
     *
     * @return string
     */
    public function getErrorFormat($errors): string
    {
        $response_errors = '';
        if (count($errors) > 0) {
            $response_errors .= '<ul>';
            foreach ($errors as $error) {
                $response_errors .= '<li>' . $error . '</li>';
            }
            $response_errors .= '</ul>';
        }
        return $response_errors;
    }

    /**
     * @return string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getContent(): string
    {
        $output = $this->displayHeader();

        if (Tools::isSubmit('order_id')) {
            $this->context->controller->addJS($this->_path . '/views/js/amount_format.js');

            return Tools::isSubmit('partial_capture') ?
                $this->displayPartialCaptureForm() :
                $this->displayRefundForm();
        }

        if (!Tools::isSubmit('type')) {
            $output .= $this->displayHome();

            return $output;
        }

        // there is a 'type' query string in the URL that tell the action to perform
        $type = Tools::getValue('type'); // "ept", "codesite", "config" or "monetico-download-support-data"

        if ($type === 'monetico-download-support-data') {
            $this->downloadSupportData();
            // the method above always terminates the current request
        }

        $id = (int)Tools::getValue('id'); // EPT or codesite database id

        if (Tools::isSubmit('add' . $this->name)) {
            // display the form to add a new EPT or code site or general config
            $output .= $this->ReadContent($type);
        } else if (Tools::isSubmit('create' . $this->name)) {
            // save a new EPT, code site or the general config
            $this->context->controller->addJS($this->_path . '/views/js/amount_format.js');
            $output .= $this->UpdateContent($type);
        } else if (Tools::isSubmit('update' . $this->name)) {
            // display the update form for an existing EPT or code site
            $this->context->controller->addJS($this->_path . '/views/js/amount_format.js');
            $output .= $this->ReadContent($type, $id);
        } else if (Tools::isSubmit('submit' . $this->name)) {
            // save an updated EPT or code site
            $output .= $this->UpdateContent($type);
        } else if (Tools::isSubmit('delete' . $this->name)) {
            // delete an existing EPT or codesite
            $output .= $this->DeleteContent($type, $id);
        } else if (Tools::isSubmit('duplicate' . $this->name)) {
            // display the form to create a new EPT or codesite, but pre-filled with the data of an existing one
            $output .= $this->ReadContent($type, 0, $id);
        }

        return $output;
    }

    /***
     * Display the admin header home page
     * @return string
     */
    public function displayHeader(): string
    {
        $this->smarty->assign('actual_version', $this->version);
        return $this->display(__FILE__, 'views/templates/admin/_partial/header.tpl');
    }

    /**
     * Display the admin home page
     * @return string
     * @throws \PrestaShopDatabaseException
     */
    public function displayHome(): string
    {
        $this->context->controller->addCSS($this->_path . '/views/css/style.css');
        // we assign link to config value
        $this->smarty->assign('link_to_config', AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&update' . $this->name . '=&type=config');
        // and we output the page
        return $this->display(__FILE__, 'views/templates/admin/config.tpl') .
            $this->displayConfigForm() .
            $this->displayInstructions() .
            $this->renderEPTList() .
            $this->renderCodeSiteList() .
            $this->renderSupportSection();
    }

    /**
     * Display the instructions when ept count was 0
     * @return string
     * @throws \PrestaShopDatabaseException
     */
    public function displayInstructions(): string
    {
        $output = "";
        if (MoneticoPaiementHelper::getEptCount() === 0) {
            $output = $this->display(__FILE__, 'views/templates/admin/_partial/instructions.tpl');
        }
        return $output;
    }

    /**
     * Read Content / Display the form to create or update
     *
     * @param     $type
     * @param int $id
     * @param int $duplicateId
     *
     * @return string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function ReadContent($type, $id = 0, $duplicateId = 0): string
    {
        $this->context->controller->addJS($this->_path . '/views/js/click_limiter.js');

        switch ($type) {
            case 'ept':
                $this->context->controller->addJS($this->_path . '/views/js/display_instalment_input.js');
                return $this->displayEPTForm($id);
                break;
            case 'codesite':
                $this->context->controller->addJS($this->_path . '/views/js/display_code_site_inputs.js');
                return $this->displayCodeSiteForm($id, $duplicateId);
                break;
            case 'config':
                return $this->displayConfigForm();
                break;
            default:
        }
        return "";
    }

    /**
     * Update Content
     *
     * @param 'ept'|'codesite'|'config' $type
     *
     * @return string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function UpdateContent(string $type)
    {
        $this->context->controller->addJS($this->_path . '/views/js/click_limiter.js');

        $output = "";
        switch ($type) {
            case 'ept':
                $id_ept = Tools::getValue('id_ept');
                $ept = new MoneticoPaiementEPT($id_ept);
                $oldEpt = clone $ept;

                $ept->setEptNumber(trim(Tools::getValue('ept_number')));
                $ept->setEptMac(trim(Tools::getValue('ept_mac')));
                $ept->setEptPaymentMode(Tools::getValue('ept_payment_mode'));

                if (Tools::getValue('ept_payment_mode') === 'INSTALMENT') {

                    $ept->setEptInstalmentDefault(Tools::getValue('ept_instalment_default'));
                    $ept->setEptInstalmentTermNb(Tools::getValue('ept_instalment_default') ? 0 : Tools::getValue('ept_instalment_term_nb'));
                    $ept->setEptInstalmentPct1(Tools::getValue('ept_instalment_default') ? 0 : intval(Tools::getValue('ept_instalment_pct_1')));
                    $ept->setEptInstalmentPct2(Tools::getValue('ept_instalment_default') ? 0 : intval(Tools::getValue('ept_instalment_pct_2')));
                    $ept->setEptInstalmentPct3((Tools::getValue('ept_instalment_default') || $ept->getEptInstalmentTermNb() < 3) ? 0 : intval(Tools::getValue('ept_instalment_pct_3')));
                    $ept->setEptInstalmentPct4((Tools::getValue('ept_instalment_default') || $ept->getEptInstalmentTermNb() < 4) ? 0 : intval(Tools::getValue('ept_instalment_pct_4')));
                }

                $array_errors = $this->validateEPT($ept->toArray());
                // if no error and save is correctly
                if (empty($array_errors) && $ept->save()) {
                    if ($id_ept) {
                        $output .= $this->displayConfirmation($this->l('Ept has been updated'));
                        $this->recordSetupAuditLogs('ept_update', $oldEpt->toArray(), $ept->toArray());
                    } else {
                        $output .= $this->displayConfirmation($this->l('Ept has been created'));
                        $this->recordSetupAuditLogs('ept_create', null, $ept->toArray());
                    }
                    $output .= $this->displayHome();
                } else {
                    $this->context->controller->addJS($this->_path . '/views/js/display_instalment_input.js');
                    $output .= $this->displayError($this->l('An error has been encountered') . $this->getErrorFormat($array_errors));
                    $output .= $this->displayOldEPTForm($ept->toArray());
                }

                break;
            case 'codesite':

                $id_code_site = Tools::getValue('id_code_site');
                $codesite = new MoneticoPaiementCodeSite($id_code_site);
                $oldCodesite = clone $codesite;

                $codesite->setCodeSiteEptId(Tools::getValue('code_site_ept_id'));
                $codesite->setCodeSiteName(trim(Tools::getValue('code_site_name')));
                $codesite->setCodeSiteDisplayName(trim(Tools::getValue('code_site_display_name')));
                $codesite->setCodeSitePaymentMethod(trim(Tools::getValue('code_site_payment_method')));
                $codesite->setCodeSiteLangue(Tools::getValue('code_site_langue'));
                $codesite->setCodeSiteActive((int)Tools::getValue('code_site_active'));
                $codesite->setCodeSiteSandbox((int)Tools::getValue('code_site_sandbox'));

                if ($codesite->getCodeSitePaymentMethod() === 'cb') {
                    $codesite->setCodesiteExpressPayment((bool)Tools::getValue('code_site_express_payment'));
                } else {
                    $codesite->setCodesiteExpressPayment(false);
                }

                $codesite->setCodeSiteAmountOption(Tools::getValue('code_site_amount_option'));
                $code_site_amount_option_is_false = !$codesite->isCodeSiteAmountOption();

                if (Tools::getValue('code_site_amount_min') === '' || $code_site_amount_option_is_false) {

                    $codesite->setCodesiteAmountMin(null);
                } else {
                    $codesite->setCodesiteAmountMin(floatval(Tools::getValue('code_site_amount_min')));
                }

                if (Tools::getValue('code_site_amount_max') === '' || $code_site_amount_option_is_false) {
                    $codesite->setCodesiteAmountMax(null);
                } else {
                    $codesite->setCodesiteAmountMax(floatval(Tools::getValue('code_site_amount_max')));
                }

                $array_codesite = $codesite->toArray();

                $array_errors = $this->validateCodeSite($array_codesite);

                // options
                $options = [];
                $options['code_site_shops_languages'] = Tools::getValue('code_site_shops_languages');
                $options['code_site_devises'] = Tools::getValue('code_site_devises');
                $options['code_site_payment_display_method'] = Tools::getValue('code_site_payment_display_method');
                $options['code_site_additional_cards_logos'] = Tools::getValue('code_site_additional_cards_logos');
                $options['code_site_shop_ids'] = Tools::getValue('code_site_shop_ids');
                $options['code_site_countries'] = Tools::getValue('code_site_countries');
                $options['code_site_cards_logo_opt'] = Tools::getValue('code_site_cards_logo_opt');
                $options['code_site_capture_event_opt'] = Tools::getValue('code_site_capture_event_opt');
                // 3dsv2
                $codesite->set3dsv2(Tools::getValue('cs_3dsv2_otps'));
                //add to opts
                $codesite->setOpts($options);
                $array_options_errors = $this->validateCodeSiteOptions($options);

                $array_errors = array_merge($array_errors, $array_options_errors);

                if (empty($array_errors) && $codesite->save()) {

                    if ($id_code_site) {
                        $output .= $this->displayConfirmation($this->l('Company code has been updated'));
                        $this->recordSetupAuditLogs('codesite_update', $oldCodesite->toArray(), $codesite->toArray());
                    } else {
                        $output .= $this->displayConfirmation($this->l('Company code has been created'));
                        $this->recordSetupAuditLogs('codesite_create', null, $codesite->toArray());
                    }
                    $output .= $this->displayHome();
                } else {
                    $this->context->controller->addJS($this->_path . '/views/js/display_code_site_inputs.js');
                    $output .= $this->displayError($this->l('An error has been encountered') . $this->getErrorFormat($array_errors));
                    $output .= $this->displayOldCodeSiteForm($codesite->toArray());
                }
                break;
            case 'config':
                Configuration::updateValue('MP_OPTS_1', Tools::getValue('opts_1'));
                $output .= $this->displayConfirmation($this->l('Settings updated')) . $this->displayHome();

                break;
            default:
        }
        return $output;
    }

    /**
     * Delete Content
     *
     * @param $type
     * @param $id
     *
     * @return string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function DeleteContent($type, $id)
    {
        $output = '';
        switch ($type) {
            case 'ept':
                $ept = new MoneticoPaiementEPT($id);
                if ($ept instanceof MoneticoPaiementEPT && $ept->delete()) {
                    $output .= $this->displayConfirmation($this->l('Ept has been deleted'));
                    $output .= $this->displayHome();

                    $this->recordSetupAuditLogs('ept_delete', $ept->toArray(), null);
                } else {
                    $output .= $this->displayError($this->l('An error has been encountered'));
                }
                break;
            case 'codesite':
                $codesite = new MoneticoPaiementCodeSite($id);
                if ($codesite instanceof MoneticoPaiementCodeSite && $codesite->delete()) {
                    $output .= $this->displayConfirmation($this->l('Company code has been deleted'));
                    $output .= $this->displayHome();

                    $this->recordSetupAuditLogs('codesite_delete', $codesite->toArray(), null);
                } else {
                    $output .= $this->displayError($this->l('An error has been encountered'));
                }
                break;
            default:
        }

        return $output;
    }

    /**
     * Validate the input fields of EPT
     *
     * @param $ept
     *
     * @return array
     */
    public function validateEPT($ept)
    {

        $errors = [];

        if (!Validate::isString($ept['ept_number'])) {
            $errors[] = $this->l('Please fill a valid ept number');
        } else if (MoneticoPaiementHelper::getEptMacFromEptNumber($ept['ept_number'], $ept['id_ept'] ?? null) != '') {
            // ept number will be unique
            $errors[] = $this->l('Please fill a new ept number');
        }

        if (!Validate::isString($ept['ept_mac'])) {
            $errors[] = $this->l('Please fill a valid security key');
        }

        $ept_payment_mode_exist = Validate::isString($ept['ept_payment_mode']) && in_array($ept['ept_payment_mode'], $this->getPaymentModeWhiteList());
        if (!$ept_payment_mode_exist) {
            $errors[] = $this->l('Please fill a valid payment mode');
        } else if ($ept['ept_payment_mode'] === 'INSTALMENT' && Validate::isBool($ept['ept_instalment_default']) && $ept['ept_instalment_default'] === 0
            && Validate::isUnsignedInt($ept['ept_instalment_term_nb']) && $ept['ept_instalment_term_nb'] > 0 && $ept['ept_instalment_term_nb'] < 5
        ) {
            $pc_sum = 0;
            if (!(Validate::isUnsignedInt($ept['ept_instalment_pct_1']) && Validate::isUnsignedInt($ept['ept_instalment_pct_2'])
                && Validate::isUnsignedInt($ept['ept_instalment_pct_3']) && Validate::isUnsignedInt($ept['ept_instalment_pct_4']))) {
                $errors[] = $this->l('Please fill a valid instalment percentage number between 1 and 100');
            } else {
                for ($i = 1; $i <= $ept['ept_instalment_term_nb']; $i++) {
                    if (!Validate::isUnsignedInt($ept['ept_instalment_pct_' . $i]) || (intval($ept['ept_instalment_pct_' . $i]) === 0)) {
                        $errors[] = $this->l('Please fill a valid instalment percentage number between 1 and 100');
                        break;
                    }

                    $pc_sum += intval($ept['ept_instalment_pct_' . $i]);
                }

                if ($pc_sum !== 100) {
                    $errors[] = $this->l('Instalment percentage sum cannot be different from 100');
                }
            }
        }

        return $errors;
    }

    /**
     * Validate the input fields of code site
     *
     * @param $code_site
     *
     * @return array
     */
    public function validateCodeSite($code_site)
    {
        $errors = [];

        if (!Validate::isUnsignedInt($code_site['code_site_ept_id']) ||
            !(new MoneticoPaiementEPT($code_site['code_site_ept_id']) instanceof MoneticoPaiementEPT)) {
            $errors[] = $this->l('Please fill a valid ept');
        }

        if (!Validate::isString($code_site['code_site_name']) || $code_site['code_site_name'] === "" ||
            mb_strlen($code_site['code_site_name']) > 64) {
            $errors[] = $this->l('Please fill a valid name');
        }

        if (!Validate::isString($code_site['code_site_display_name']) || $code_site['code_site_display_name'] === "" ||
            mb_strlen($code_site['code_site_display_name']) > 64) {
            $errors[] = $this->l('Please fill a valid display name');
        }

        if (!Validate::isString($code_site['code_site_payment_method']) || $code_site['code_site_payment_method'] === "" ||
            !in_array($code_site['code_site_payment_method'], $this->getPaymentProtocolWhiteList())) {
            $errors[] = $this->l('Please fill a valid payment method');
        }

        if (!Validate::isString($code_site['code_site_langue']) || !in_array($code_site['code_site_langue'], $this->getLanguageWhiteList())) {
            $errors[] = $this->l('Please fill a valid language');
        }

        if (!Validate::isBool($code_site['code_site_active'])) {
            $errors[] = $this->l('Please fill a valid active mode value');
        }

        if (!Validate::isBool($code_site['code_site_sandbox'])) {
            $errors[] = $this->l('Please fill a valid sandbox mode value');
        }

        if (!Validate::isBool($code_site['code_site_express_payment'])) {
            $errors[] = $this->l('Please fill a valid express payment value');
        }

        if (!is_null($code_site['code_site_amount_min']) && !Validate::isFloat($code_site['code_site_amount_min'])) {
            $errors[] = $this->l('Please fill a valid minimum amount');
        }
        if (!is_null($code_site['code_site_amount_max']) && !Validate::isFloat($code_site['code_site_amount_max'])) {
            $errors[] = $this->l('Please fill a valid maximum amount');
        }

        return $errors;
    }

    /**
     * Validate the input fields of code site options
     *
     * @param $code_site_opts
     *
     * @return array
     */
    public function validateCodeSiteOptions($code_site_opts)
    {
        $errors = [];
        if (!is_array($code_site_opts['code_site_devises'])) {
            $errors[] = $this->l('Please select at least one currency');
        } else if (count($code_site_opts['code_site_devises']) > 0) {
            $currency_white_list = $this->getCurrencyWhiteList();
            foreach ($code_site_opts['code_site_devises'] as $currency) {
                if (!in_array($currency, $currency_white_list)) {
                    $errors[] = $this->l('Please select a valid currency');
                    break;
                }
            }
        }

        if (!is_array($code_site_opts['code_site_shop_ids'])) {
            $errors[] = $this->l('Please select at least one shop');
        } else if (count($code_site_opts['code_site_shop_ids']) > 0) {
            $current_shop_ids = Shop::getShops(true, null, true);
            foreach ($code_site_opts['code_site_shop_ids'] as $shop_id) {
                if (!in_array($shop_id, $current_shop_ids)) {
                    $errors[] = $this->l('Please select a valid shop');
                    break;
                }
            }
        }

        if (is_array($code_site_opts['code_site_shops_languages']) && count($code_site_opts['code_site_shops_languages']) > 0) {
            $shop_language_ids = PrestashopLanguage::getLanguages(true, false, true);
            foreach ($code_site_opts['code_site_shops_languages'] as $language_id) {
                if (!in_array($language_id, $shop_language_ids)) {
                    $errors[] = $this->l('Please select a valid country');
                    break;
                }
            }
        }


        if (is_array($code_site_opts['code_site_countries']) && count($code_site_opts['code_site_countries']) > 0) {
            $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
            foreach ($code_site_opts['code_site_countries'] as $country_id) {
                if (is_null(Country::getNameById($defaultLang, $country_id))) {
                    $errors[] = $this->l('Please select a valid country');
                    break;
                }
            }
        }


        if (is_array($code_site_opts['code_site_additional_cards_logos']) && count($code_site_opts['code_site_additional_cards_logos']) > 0) {
            $additional_cards_whitelist = $this->getPaymentAdditionalNetworkWhitelist();
            foreach ($code_site_opts['code_site_additional_cards_logos'] as $additional_card) {
                if (!in_array($additional_card, $additional_cards_whitelist)) {
                    $errors[] = $this->l('Please select a valid additional network');
                    break;
                }
            }

            $additional_cards_string = implode("_", $code_site_opts['code_site_additional_cards_logos']);
            $logo_url = _MODULE_DIR_ . $this->name . "/views/img/monetico_paiement_checkout_logo_" . mb_strtolower($additional_cards_string) . ".png";

            $logo_exist_or_successfully_created = (file_exists($logo_url) ||
                $this->createLogoWithAdditionalCards($code_site_opts['code_site_additional_cards_logos'],
                    dirname(__FILE__) . '/views/img/monetico_paiement_checkout_logo.png',
                    dirname(__FILE__) . "/views/img/monetico_paiement_checkout_logo_" . mb_strtolower($additional_cards_string) . ".png"
                ));

            if (!$logo_exist_or_successfully_created) {
                $errors[] = $this->l('Logo cards has not been generated');
            }
        }

        if (!Validate::isBool($code_site_opts['code_site_cards_logo_opt'])) {
            $errors[] = $this->l('Please fill a valid cards logo option');
        }

        if (!Validate::isUnsignedInt($code_site_opts['code_site_capture_event_opt']) || !in_array($code_site_opts['code_site_capture_event_opt'], [0, Configuration::get('PS_OS_SHIPPING'), Configuration::get('PS_OS_DELIVERED')])) {
            $errors[] = $this->l('Please fill a valid capture event option');
        }

        if (!Validate::isString($code_site_opts['code_site_payment_display_method']) || $code_site_opts['code_site_payment_display_method'] == ""
            || !in_array($code_site_opts['code_site_payment_display_method'], $this->getPaymentDisplayMethodWhitelist())) {
            $errors[] = $this->l('Please fill a valid payment display method option');
        }

        return $errors;
    }

    /**
     * Get field of ept list render
     * @return array[]
     */
    public function getEPTFieldList()
    {
        return [
            'id' => [
                'title' => $this->l('ID'),
                'type' => 'hidden',
                'search' => false,
            ],
            'number' => [
                'title' => $this->l('Number'),
                'type' => 'text',
                'search' => false,
            ],
            'mac' => [
                'title' => $this->l('Security key'),
                'type' => 'text',
                'search' => false,
            ],
            'payment_mode' => [
                'title' => $this->l('Payment mode'),
                'type' => 'text',
                'search' => false,
            ],
        ];
    }

    /**
     * Get the ept list render
     * @return false|string
     * @throws \PrestaShopDatabaseException
     */
    public function renderEPTList()
    {
        $fields_list = $this->getEPTFieldList();

        $helper = new HelperList();
        $helper->list_id = 'form-ept-list';
        $helper->shopLinkType = '';
        $helper->simple_header = false;

        $helper->toolbar_btn['new'] = ['href' => AdminController::$currentIndex . '&configure=' . $this->name . '&add' . $this->name . '&type=ept' . '&token=' . Tools::getAdminTokenLite('AdminModules'), 'desc' => $this->l('Add new')];
        $helper->actions = ['edit', 'deleteEpt'];
        $helper->show_toolbar = true;
        $helper->module = $this;
        $helper->identifier = 'id';
        $helper->title = $this->l('EPT');
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&type=ept';
        $helper->no_link = true;

        $helper->listTotal = MoneticoPaiementHelper::getEptCount();
        $data_epts = MoneticoPaiementHelper::getAllEpt();

        return $helper->generateList($data_epts, $fields_list);
    }

    /**
     * Custom action link for delete EPT.
     *
     * @param $token
     * @param $id
     * @param $name
     *
     * @return false|string
     */
    public function displayDeleteEptLink($token = null, $id = null, $name = null)
    {
        $confirm_text = $this->l('Are you sure you want to delete this EPT ?');

        if ($this->mode_demo_active()) {
            $confirm_text .= '<br>' . $this->l('By clicking Yes, all orders created with this EPT in demonstration mode will be deleted too.');
            $params = [
                'action' => 'del_demo_orders',
                'ept_id' => (int)$id,
            ];
            $href = $this->context->link->getAdminLink('AdminMoneticoPaiementDemo', true, $params, $params);
        } else {
            $confirm_text .= '<br>' . $this->l('By clicking on Yes, this will have repercussions on the details of orders placed with this EPT.');
            $href = AdminController::$currentIndex . '&configure=' . $this->name . '&id=' . (int)$id . '&delete' . $this->name . '&type=ept' . '&token=' . Tools::getAdminTokenLite('AdminModules');
        }

        $this->smarty->assign([
            'href' => $href,
            'action' => $this->l('Delete'),
            'header_text' => '',
            'display_text' => $confirm_text,
            'confirm_text' => $this->l('Yes'),
            'cancel_text' => $this->l('No'),
            'ept_id' => (int)$id,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/list_action_delete_ept.tpl');
    }

    /**
     * Get Field of Code Site Form
     * @return array[]
     */
    public function getCodeSiteFieldList()
    {
        return [
            'id' => [
                'title' => $this->l('ID'),
                'type' => 'text',
                'search' => false,
            ],
            'code_site_name' => [
                'title' => $this->l('Company code Name'),
                'type' => 'text',
                'search' => false,
            ],
            'code_site_ept' => [
                'title' => $this->l('EPT'),
                'type' => 'text',
                'search' => false,
            ],
            'code_site_payment_method' => [
                'title' => $this->l('Payment Method'),
                'width' => 64,
                'type' => 'text',
                'search' => false,
            ],
            'code_site_payment_display_method' => [
                'title' => $this->l('Purchase route'),
                'width' => 64,
                'type' => 'text',
                'search' => false,
            ],
            'code_site_langue' => [
                'title' => $this->l('Language'),
                'type' => 'text',
                'search' => false,
            ],
            'code_site_active' => [
                'title' => $this->l('Enabled'),
                'type' => 'bool',
                'ajax' => true,
                'search' => false,
            ],
            'code_site_sandbox' => [
                'title' => $this->l('Sandbox Mode'),
                'type' => 'bool',
                'ajax' => true,
                'search' => false,
            ],
            'code_site_express_payment' => [
                'title' => $this->l('Express Payment'),
                'type' => 'bool',
                'ajax' => true,
                'search' => false,
            ],
        ];
    }

    /**
     * Get the code site list render
     * @return false|string
     * @throws \PrestaShopDatabaseException
     */
    public function renderCodeSiteList()
    {
        $fields_list = $this->getCodeSiteFieldList();

        $helper = new HelperList();
        $helper->list_id = 'form-code-site-list';
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        // if one tpe exist , we can add one code Site
        if (MoneticoPaiementHelper::getEptCount() > 0) {
            $helper->toolbar_btn['new'] = ['href' => AdminController::$currentIndex . '&configure=' . $this->name . '&add' . $this->name . '&type=codesite' . '&token=' . Tools::getAdminTokenLite('AdminModules'), 'desc' => $this->l('Add new')];
        }

        $helper->actions = ['edit', 'duplicateCodeSite'];
        if (!$this->mode_demo_active()) {
            $helper->actions[] = 'delete';
        }
        $helper->show_toolbar = false;
        $helper->module = $this;
        $helper->identifier = 'id';
        $helper->title = $this->l('Company code');
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&type=codesite';
        $helper->no_link = true;
        // add total of code site
        $helper->listTotal = MoneticoPaiementHelper::getCodeSiteCount();
        // get all code site
        $data_code_sites = MoneticoPaiementHelper::getAllCodeSites();

        return $helper->generateList($data_code_sites, $fields_list);
    }

    private function renderSupportSection(): string
    {
        $output = '';

        $supportFileError = $_SESSION['monetico_support_file_error'] ?? null;
        if ($supportFileError !== null) {
            unset($_SESSION['monetico_support_file_error']);

            $errorMessage = $this->l('There has been a problem generating the file with support data') . '. Error: ' . $supportFileError;
            $this->smarty->assign([
                'supportFileError' => $errorMessage,
            ]);

            $output .= $this->displayError($errorMessage); // alert/toast
        }

        return $output . $this->display(__FILE__, 'views/templates/admin/support-section.tpl');
    }

    /**
     * Called when clicking on the "download" button in the support section.
     *
     * @return never Always terminates the request, either by giving a .zip file to the client, or with a 302 response going back to the previous page
     */
    private function downloadSupportData(): void
    {
        try {
            $zipFileBuilder = new ZipFileBuilder();

            // setup all the non-default collectors
            $infoFileCollector = new MainInfoFileCollector();

            /** @var array<string, mixed> $parameters */
            $parameters = require_once _PS_ROOT_DIR_ . '/app/config/parameters.php';
            $dbHost = '';
            if (is_array($parameters)) {
                $dbHost = $parameters['parameters']['database_host'] ?? '';
            }
            $infoFileCollector->addDataCollector(new TechDataCollector([$dbHost]));

            $infoFileCollector->addDataCollector(new CMSDataCollector());
            $infoFileCollector->addDataCollector(new MoneticoDataCollector($this->configuration));

            $zipFileBuilder->addFileCollector($infoFileCollector);

            // add recent (last month max) log files from the prestashop /var/logs folder
            $logFilePaths = glob(_PS_ROOT_DIR_ . '/var/logs/*.log') ?: [];

            $now = new DateTimeImmutable();
            foreach ($logFilePaths as $path) {
                $dirSeparatorPosition = max(strrpos($path, '/'), strrpos($path, '\\')); // because of windows, we have to try both
                $filename = substr($path, $dirSeparatorPosition + 1);

                if ($filename === 'dev.log' || $filename === 'prod.log') {
                    $zipFileBuilder->addFileCollector(new LogFileCollector(
                        $path,
                        $filename,
                    ));

                    continue;
                }

                // The file name is like "(dev|prod)-{Y}-{m}-{d}.log" (ie: "prod-2025-12-16.log")
                // only consider files from the last 30 days
                try {
                    $date = new DateTimeImmutable(substr($path, -14, 10));
                } catch (Throwable $e) {
                    continue;
                }

                $interval = $now->diff($date);
                if ($interval->days > 31) {
                    continue;
                }

                $zipFileBuilder->addFileCollector(new LogFileCollector(
                    $path,
                    $filename,
                ));
            }

            $zipFileBuilder->addFileCollector(new LogFileCollector(
                _PS_ROOT_DIR_ . '/modules/MoneticoPaiement/class/SupportDataFile/instructions.md',
                'instructions.md',
                false, // filter lines
            ));

            // collect all data and files
            $zipFileBuilder->build();

            // make the client download the file (which terminates the request here)
            $zipFileBuilder->sendToClient();
        } catch (Throwable $e) {
            $message = "Error generating the support data file: " . $e->getMessage();
            $this->createLog($message);

            $_SESSION['monetico_support_file_error'] = $e->getMessage();

            $url = AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules');
            header('Location: ' . $url);
            http_response_code(302);
            exit;
        }
    }

    /**
     * Custom action link for duplicate code site.
     *
     * @param $token
     * @param $id
     * @param $name
     *
     * @return false|string
     */
    public function displayDuplicateCodeSiteLink($token = null, $id = null, $name = null)
    {
        $this->smarty->assign([
            'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&id=' . (int)$id . '&duplicate' . $this->name . '&type=codesite' . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            'action' => $this->trans('Duplicate', [], 'Admin.Actions'),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/list_action_duplicate_code_site.tpl');
    }

    /**
     * Get the helper form of ept with empty data
     *
     * @param $ept_id
     *
     * @return \HelperForm
     */
    public function getEPTHelperForm($ept_id)
    {
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper = new HelperForm();
        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = false;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = ($ept_id > 0 ? 'submit' : 'create') . $this->name;
        // add value;
        $helper->fields_value['type'] = 'ept';
        $helper->fields_value['id_ept'] = '';
        $helper->fields_value['ept_number'] = '';
        $helper->fields_value['ept_mac'] = '';
        $helper->fields_value['ept_payment_mode'] = '';
        $helper->fields_value['ept_instalment_default'] = '';
        $helper->fields_value['ept_instalment_pct_1'] = '';
        $helper->fields_value['ept_instalment_pct_2'] = '';
        $helper->fields_value['ept_instalment_pct_3'] = '';
        $helper->fields_value['ept_instalment_pct_4'] = '';

        return $helper;
    }

    /**
     * Get Ept form
     *
     * @param $ept_id
     *
     * @return array
     */
    public function getEPTForm($ept_id)
    {
        $data_payment_mode = [];
        $payment_mode_class = new ReflectionClass('MoneticoPaiement\Sdk\Utilities\Collections\PaymentMode');
        $pm_constants = $payment_mode_class->getConstants();
        $defaultLang = $this->context->language->iso_code;

        $data_payment_mode[] = [
            'id' => '',
            'name' => '-- ' . $this->l('select a payment mode') . ' --',
        ];

        foreach ($pm_constants as $key => $payment_mode) {
            if ($key != 'DEFAULT_LANGUAGE') {
                $data_payment_mode[] = [
                    'id' => $key,
                    'name' => $payment_mode[$defaultLang] ?? $payment_mode[PaymentMode::DEFAULT_LANGUAGE]
                ];
            }
        }

        if ($ept_id > 0) {
            $form_title = $this->l('Edit payment terminal');
        } else {
            $form_title = $this->l('Creation of the payment terminal');
        }

        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $form_title,
                'icon' => 'icon-cogs'
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'type',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'id_ept',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'ept_payment_mode',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('EPT Number'),
                    'name' => 'ept_number',
                    'hint' => $this->l('This information is provided to you when you subscribe to your Monetico Paiement contract'),
                    'required' => true,
                    'maxlength' => 64,
                    'readonly' => $this->mode_demo_active(),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Security key'),
                    'name' => 'ept_mac',
                    'hint' => $this->l('This information is provided to you when you subscribe to your Monetico Paiement contract'),
                    'required' => true,
                    'maxlength' => 64,
                    'readonly' => $this->mode_demo_active(),
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Payment mode'),
                    'name' => 'ept_payment_mode',
                    'hint' => $this->l('This information is provided to you when you subscribe to your Monetico Paiement contract'),
                    'required' => true,
                    'options' => [
                        'query' => $data_payment_mode,
                        'id' => 'id',
                        'name' => "name"
                    ],
                    'disabled' => $this->mode_demo_active(),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Instalment Default Value'),
                    'name' => 'ept_instalment_default',
                    'hint' => $this->l('Choose whether the instalment payments will be distributed according to the default terms of your Monetico Paiement contract'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ],
                    ],
                    'is_bool' => false,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Instalment Term Number'),
                    'name' => 'ept_instalment_term_nb',
                    'hint' => $this->l('This information is provided to you when you subscribe to your Monetico Paiement contract'),
                    'required' => true,
                    'options' => [
                        'query' => [
                            [
                                'id' => 2,
                                'name' => 2,
                            ],
                            [
                                'id' => 3,
                                'name' => 3,
                            ],
                            [
                                'id' => 4,
                                'name' => 4,
                            ],
                        ],
                        'id' => 'id',
                        'name' => "name"
                    ]
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Instalment Percentage 1'),
                    'name' => 'ept_instalment_pct_1',
                    'cast' => 'intval',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Instalment Percentage 2'),
                    'name' => 'ept_instalment_pct_2',
                    'cast' => 'intval',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Instalment Percentage 3'),
                    'name' => 'ept_instalment_pct_3',
                    'cast' => 'intval',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Instalment Percentage 4'),
                    'name' => 'ept_instalment_pct_4',
                    'cast' => 'intval',
                ]
            ],
            'buttons' => [
                [
                    'href' => $this->context->link->getAdminLink('AdminModules', true, ['configure' => self::MODULE_NAME], ['configure' => self::MODULE_NAME]),
                    'title' => $this->l('Cancel'),
                    'class' => 'btn btn-default pull-left',
                    'icon' => 'process-icon-cancel',
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ],
        ];

        return $fieldsForm;
    }

    /**
     * Display the ept form
     *
     * @param int $ept_id
     *
     * @return string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function displayEPTForm($ept_id = 0)
    {
        $helper = $this->getEPTHelperForm($ept_id);

        // add value;
        $helper->fields_value['type'] = 'ept';
        $helper->fields_value['id_ept'] = '';
        $helper->fields_value['ept_number'] = '';
        $helper->fields_value['ept_mac'] = '';
        $helper->fields_value['ept_payment_mode'] = '';
        $helper->fields_value['ept_instalment_default'] = true;
        $helper->fields_value['ept_instalment_term_nb'] = '';
        $helper->fields_value['ept_instalment_pct_1'] = '';
        $helper->fields_value['ept_instalment_pct_2'] = '';
        $helper->fields_value['ept_instalment_pct_3'] = '';
        $helper->fields_value['ept_instalment_pct_4'] = '';
        // get ept field form
        $fieldsForm = $this->getEPTForm($ept_id);


        // Load current value
        if ($ept_id > 0) {
            $ept = new MoneticoPaiementEPT($ept_id);
            if ($ept instanceof MoneticoPaiementEPT) {
                $helper->fields_value['id_ept'] = $ept->getIdEpt();
                $helper->fields_value['ept_number'] = $ept->getEptNumber();
                $helper->fields_value['ept_mac'] = $this->mode_demo_active() ? MoneticoPaiementHelper::displayMacSecurity($ept->getEptMac()) : $ept->getEptMac();
                $helper->fields_value['ept_payment_mode'] = $ept->getEptPaymentMode();
                $helper->fields_value['ept_instalment_default'] = $ept->isEptInstalmentDefault();
                $helper->fields_value['ept_instalment_term_nb'] = $ept->getEptInstalmentTermNb();
                $helper->fields_value['ept_instalment_pct_1'] = $ept->getEptInstalmentPct1() === 0 ? '' : $ept->getEptInstalmentPct1();
                $helper->fields_value['ept_instalment_pct_2'] = $ept->getEptInstalmentPct2() === 0 ? '' : $ept->getEptInstalmentPct2();
                $helper->fields_value['ept_instalment_pct_3'] = $ept->getEptInstalmentPct3() === 0 ? '' : $ept->getEptInstalmentPct3();
                $helper->fields_value['ept_instalment_pct_4'] = $ept->getEptInstalmentPct4() === 0 ? '' : $ept->getEptInstalmentPct4();
            }
        }

        return $helper->generateForm($fieldsForm);
    }

    /**
     * Display the old ept form
     *
     * @param $old_ept
     *
     * @return string
     */
    public function displayOldEPTForm($old_ept)
    {
        // get ept field form
        $fieldsForm = $this->getEPTForm($old_ept['id_ept'] ?? 0);
        $helper = $this->getEPTHelperForm($old_ept['id_ept'] ?? 0);

        $helper->fields_value['type'] = 'ept';
        $helper->fields_value['id_ept'] = $old_ept['id_ept'] ?? '';
        $helper->fields_value['ept_number'] = $old_ept['ept_number'];
        $helper->fields_value['ept_mac'] = $old_ept['ept_mac'];
        $helper->fields_value['ept_payment_mode'] = $old_ept['ept_payment_mode'];
        $helper->fields_value['ept_instalment_default'] = $old_ept['ept_instalment_default'];
        $helper->fields_value['ept_instalment_term_nb'] = $old_ept['ept_instalment_term_nb'];
        $helper->fields_value['ept_instalment_pct_1'] = $old_ept['ept_instalment_pct_1'];
        $helper->fields_value['ept_instalment_pct_2'] = $old_ept['ept_instalment_pct_2'];
        $helper->fields_value['ept_instalment_pct_3'] = $old_ept['ept_instalment_pct_3'];
        $helper->fields_value['ept_instalment_pct_4'] = $old_ept['ept_instalment_pct_4'];

        return $helper->generateForm($fieldsForm);
    }

    /**
     * Get the helper form with code site empty data
     *
     * @param $code_site_id
     *
     * @return \HelperForm
     */
    public function getCodeSiteHelperForm($code_site_id)
    {
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = ($code_site_id > 0 ? 'submit' : 'create') . $this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        $helper->fields_value['type'] = 'codesite';
        $helper->fields_value['id_code_site'] = '';
        $helper->fields_value['code_site_ept_id'] = '';
        $helper->fields_value['code_site_name'] = '';
        $helper->fields_value['code_site_payment_method'] = '';
        $helper->fields_value['code_site_payment_display_method'] = '';
        $helper->fields_value['code_site_display_name'] = '';
        $helper->fields_value['code_site_cards_logo_opt'] = true;
        $helper->fields_value['code_site_capture_event_opt'] = '';
        $helper->fields_value['code_site_langue'] = 'FR';
        $helper->fields_value['code_site_3dsv2_opts'] = '';
        $helper->fields_value['code_site_express_payment'] = false;
        $helper->fields_value['code_site_active'] = true;
        $helper->fields_value['code_site_sandbox'] = true;
        // opts
        $helper->fields_value['code_site_shops_languages[]'] = '';
        $helper->fields_value['code_site_devises[]'] = '';
        $helper->fields_value['code_site_shop_ids[]'] = '';
        $helper->fields_value['code_site_countries[]'] = '';
        $helper->fields_value['code_site_additional_cards_logos[]'] = '';
        $helper->fields_value['code_site_amount_option'] = false;
        $helper->fields_value['code_site_amount_min'] = '';
        $helper->fields_value['code_site_amount_max'] = '';

        return $helper;
    }

    /**
     * Get Languages for code site form
     * @return array
     */
    public function getCodeSiteLanguages()
    {
        $data_language = [];
        $language_class = new ReflectionClass('MoneticoPaiement\Sdk\Utilities\Collections\Language');
        $lc_constants = $language_class->getConstants();
        $defaultLang = $this->context->language->iso_code;

        foreach ($lc_constants as $key => $language) {
            if ($key != 'DEFAULT_LANGUAGE') {
                $data_language[] = [
                    'id' => $key,
                    'name' => $language[$defaultLang] ?? $language[Language::DEFAULT_LANGUAGE]
                ];
            }
        }
        return $data_language;
    }

    /**
     * Get Currencies for code site form
     * @return array
     */
    public function getCodeSiteCurrencies()
    {
        $data_devise = [];
        $array_currencies = array_merge(Currency::PRINCIPAL_CURRENCIES, Currency::OTHER_CURRENCIES);
        foreach ($array_currencies as $currency) {
            $data_devise[] = [
                'id' => $currency,
                'name' => $currency
            ];
        }
        return $data_devise;
    }

    /**
     * Get 3dsv2 types for code site form
     * @return array
     */
    public function getCodeSite3dsv2()
    {
        $data_3dsv2 = [];
        $data_3dsv2[] = [
            'value' => ThreeDSecureChallengeValue::NO_PREFERENCE,
            'name' => $this->l('No preference'),
        ];
        $data_3dsv2[] = [
            'value' => ThreeDSecureChallengeValue::CHALLENGE_PREFERRED,
            'name' => $this->l('Authentication desired'),
        ];
        $data_3dsv2[] = [
            'value' => ThreeDSecureChallengeValue::CHALLENGE_MANDATED,
            'name' => $this->l('Systematic authentication requested'),
        ];
        $data_3dsv2[] = [
            'value' => ThreeDSecureChallengeValue::NO_CHALLENGE_REQUESTED,
            'name' => $this->l('No authentication required'),
        ];
        $data_3dsv2[] = [
            'value' => ThreeDSecureChallengeValue::NO_CHALLENGE_REQUESTED_STRONG_AUTHENTICATION,
            'name' => $this->l('No authentication requested type of exemption strong authentication'),
        ];
        $data_3dsv2[] = [
            'value' => ThreeDSecureChallengeValue::NO_CHALLENGE_REQUESTED_TRUSTED_THIRD_PARTY,
            'name' => $this->l('No authentication requested type of trusted third party exemption'),
        ];
        $data_3dsv2[] = [
            'value' => ThreeDSecureChallengeValue::NO_CHALLENGE_REQUESTED_RISK_ANALYSIS,
            'name' => $this->l('No authentication required type of exemption prior risk analysis done'),
        ];

        return $data_3dsv2;
    }

    /**
     * Get Shops for code site form
     * @return array
     */
    public function getCodeSiteShops()
    {
        $data_shops = [];
        $shops = Shop::getShops();
        foreach ($shops as $shop) {
            $data_shops[] = [
                'id' => $shop['id_shop'],
                'name' => $shop['name']
            ];
        }
        return $data_shops;
    }

    /**
     * Get Countries for code site form
     * @return array
     */
    public function getCodeSiteCountries()
    {
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
        $data_countries = [];
        $countries = Country::getCountries($defaultLang, true);
        foreach ($countries as $country) {
            $data_countries[] = [
                'id' => $country['id_country'],
                'name' => $country['name']
            ];
        }
        return $data_countries;
    }

    /**
     * Get Protocols for code site form
     * @return array
     */
    public function getProtocols()
    {
        $data_protocols = [];
        $defaultLang = $this->context->language->iso_code;
        foreach (PaymentProtocol::PROTOCOL as $key => $protocol) {
            $data_protocols[] = [
                'id' => $key,
                'name' => $protocol[$defaultLang] ?? $protocol[PaymentProtocol::DEFAULT_LANGUAGE]
            ];
        }

        return $data_protocols;
    }

    /**
     * Get Additional Logo Cards for code site form
     * @return array
     */
    public function getAddtionalLogos()
    {
        $data_additional_cards = [];
        $additional_card_class = new ReflectionClass('MoneticoPaiement\Sdk\Utilities\Collections\PaymentAdditionalNetwork');
        $ac_constants = $additional_card_class->getConstants();

        foreach ($ac_constants as $key => $additional_card) {
            $data_additional_cards[] = [
                'id' => $key,
                'name' => $additional_card
            ];
        }
        return $data_additional_cards;
    }

    /**
     * Get Payment Display Method for code site form
     * @return array
     */
    public function getPaymentDisplayMethod()
    {
        $display_methods = new \ReflectionClass(PaymentDisplayMethod::class);
        $dm_constants = $display_methods->getConstants();
        $data_display_methods = [];
        $defaultLang = $this->context->language->iso_code;

        foreach ($dm_constants as $key => $display_methods) {
            if ($key != 'DEFAULT_LANGUAGE') {
                $data_display_methods[] = [
                    'id' => $key,
                    'name' => $display_methods[$defaultLang] ?? $display_methods[PaymentDisplayMethod::DEFAULT_LANGUAGE]
                ];
            }
        }
        return $data_display_methods;
    }

    /**
     * Display description for purchase route
     * @return string
     */
    public function getPaymentDisplayMethodDesc()
    {
        return '<span id="deported_desc" class="payment_display_method_desc" style="display: none">' . $this->l('Your customer will be redirected to the Monetico payment page to make the payment') . '</span>' .
            '<span id="hostedfields_desc" class="payment_display_method_desc" style="display: none">' . $this->l('Your customer will be able to enter their card information directly on your order page') . '<br>' .
            '<span style="color:red">' . $this->l('Be careful, the choice of the integrated purchasing route requires the activation of this option on your Monetico payment contract. A dedicated company code name will be provided to you. You\'ll have to fill it in in the Company code Name field.') . '</span></span>';
    }

    /**
     * Get the code site form
     *
     * @param $code_site_id
     *
     * @return array
     * @throws \PrestaShopDatabaseException
     */
    public function getCodeSiteForm($code_site_id)
    {

        // get ept
        $data_ept = MoneticoPaiementHelper::getAllEpt();
        // get languages
        $data_language = $this->getCodeSiteLanguages();
        // get currency
        $data_devise = $this->getCodeSiteCurrencies();
        // get shops
        $data_shops = $this->getCodeSiteShops();
        // get countries
        $data_countries = $this->getCodeSiteCountries();

        if ($code_site_id > 0) {
            $form_title = $this->l('Company code edition');
        } else {
            $form_title = $this->l('Company code creation');
        }

        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $form_title,
                'icon' => 'icon-cogs'
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'type',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'id_code_site',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'code_site_sandbox',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'code_site_ept_id',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'ajax_url',
                    'id' => 'ajax_url',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'code_site_active',
                    'hint' => $this->l('Choose if your company code is activated or not'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ],
                    ],
                    'is_bool' => false,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('SandBox Mode'),
                    'name' => 'code_site_sandbox',
                    'hint' => $this->l('Allows transactions to be carried out on the SANDBOX Monetico Paiement platform which simulates transactions'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ],
                    ],
                    'is_bool' => false,
                    'disabled' => $this->mode_demo_active(),
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Ept number'),
                    'name' => 'code_site_ept_id',
                    'hint' => $this->l('Choose the EPT to use with this company code'),
                    'required' => true,
                    'options' => [
                        'query' => $data_ept,
                        'id' => 'id',
                        'name' => 'number'
                    ],
                    'disabled' => $this->mode_demo_active(),
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Payment Method'),
                    'name' => 'code_site_payment_method',
                    'hint' => $this->l('Choose the payment method offered during the payment process. These payment methods must first be activated on your electronic payment terminal contract.'),
                    'options' => [
                        'query' => $this->getProtocols(),
                        'id' => 'id',
                        'name' => 'name'
                    ]
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Purchase route'),
                    'name' => 'code_site_payment_display_method',
                    'hint' => $this->l('Choose the display payment method offered during the payment process. The display payment method must first be activated on your electronic payment terminal contract.'),
                    'options' => [
                        'query' => $this->getPaymentDisplayMethod(),
                        'id' => 'id',
                        'name' => 'name'
                    ],
                    'desc' => $this->getPaymentDisplayMethodDesc(),
                    'onchange' => "$('.payment_display_method_desc').css('display','none');$('#'+this.value+'_desc').css('display','block');",
                    'disabled' => $this->mode_demo_active(),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Company code Name'),
                    'name' => 'code_site_name',
                    'hint' => $this->l('This information is provided to you when you subscribe to your Monetico Paiement contract'),
                    'required' => true,
                    'maxlength' => 64,
                    'readonly' => $this->mode_demo_active(),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Display name'),
                    'name' => 'code_site_display_name',
                    'hint' => $this->l('Indicate the caption that will be displayed during the payment process'),
                    'required' => true,
                    'maxlength' => 64
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Express Payment'),
                    'name' => 'code_site_express_payment',
                    'hint' => $this->l('Choose if your company code uses express payment option or not'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ],
                    ],
                    'is_bool' => false,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display payment method logo'),
                    'name' => 'code_site_cards_logo_opt',
                    'hint' => $this->l('Choose if payment method logo is visible on checkout page or not'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ],
                    ],
                    'is_bool' => false,
                ],
                [
                    'type' => 'select',
                    'multiple' => true,
                    'label' => $this->l('Display additional network logos'),
                    'name' => 'code_site_additional_cards_logos[]',
                    'hint' => $this->l('Choose if additional network logos are visible on checkout page or not'),
                    'options' => [
                        'query' => $this->getAddtionalLogos(),
                        'id' => 'id',
                        'name' => 'name'
                    ]
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Language'),
                    'name' => 'code_site_langue',
                    'hint' => $this->l('Choose language of Monetico Paiement Page'),
                    'required' => true,
                    'options' => [
                        'query' => $data_language,
                        'id' => 'id',
                        'name' => 'name'
                    ]
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Capture event'),
                    'name' => 'code_site_capture_event_opt',
                    'hint' => $this->l('Choose the event that will automatically recover the payment if payment mode is differed or instalment (only the first instalment will be recovered)'),
                    'required' => true,
                    'options' => [
                        'query' => [
                            [
                                'id' => 0,
                                'name' => $this->l('Nothing'),
                            ],
                            [
                                'id' => Configuration::get('PS_OS_SHIPPING'),
                                'name' => $this->l('Shipped')
                            ],
                            [
                                'id' => Configuration::get('PS_OS_DELIVERED'),
                                'name' => $this->l('Delivered')
                            ]],
                        'id' => 'id',
                        'name' => 'name'
                    ]
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('3DSV2 Options'),
                    'id' => 'tableRender',
                    'class' => 'hidden',
                    'name' => 'code_site_3dsv2_opts',
                    'hint' => $this->l('Used to express a wish related to the card holder\'s 3D Secure authentication during the payment phase. Different stages can be configured'),
                ],
            ],
        ];

        $form_title = $this->l('Display options');

        $fieldsForm[1]['form'] = [
            'legend' => [
                'title' => $form_title,
                'icon' => 'icon-cogs'
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('Manage the display according to the cart amount'),
                    'name' => 'code_site_amount_option',
                    'hint' => $this->l('Allows you to manage the display of the payment method according to the cart amount , regardless of the accepted currency'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ],
                    ],
                    'is_bool' => false,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Minimum amount'),
                    'name' => 'code_site_amount_min',
                    'hint' => $this->l('Indicates the minimum cart amount to display the payment method. If the field is empty, there will be no minimum.'),
                    'cast' => 'floatval',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Maximum amount'),
                    'name' => 'code_site_amount_max',
                    'hint' => $this->l('Indicates the maximum cart amount to display the payment method. If the field is empty, there will be no maximum.'),
                    'cast' => 'floatval',
                ],
                [
                    'type' => 'select',
                    'multiple' => true,
                    'label' => $this->l('Accepted shops languages'),
                    'name' => 'code_site_shops_languages[]',
                    'hint' => $this->l('Choose the accepted shops languages'),
                    'options' => [
                        'query' => PrestashopLanguage::getLanguages(true),
                        'id' => 'id_lang',
                        'name' => 'name'
                    ]
                ],
                [
                    'type' => 'select',
                    'multiple' => true,
                    'label' => $this->l('Accepted currencies'),
                    'name' => 'code_site_devises[]',
                    'hint' => $this->l('Choose the accepted currencies'),
                    'required' => true,
                    'options' => [
                        'query' => $data_devise,
                        'id' => 'id',
                        'name' => 'name'
                    ]
                ],
                [
                    'type' => 'select',
                    'multiple' => true,
                    'label' => $this->l('Accepted shops'),
                    'name' => 'code_site_shop_ids[]',
                    'hint' => $this->l('Choose the accepted shops'),
                    'required' => true,
                    'options' => [
                        'query' => $data_shops,
                        'id' => 'id',
                        'name' => 'name'
                    ]
                ],
                [
                    'type' => 'select',
                    'multiple' => true,
                    'label' => $this->l('Restricted countries'),
                    'name' => 'code_site_countries[]',
                    'hint' => $this->l('Choose the restricted billing countries'),
                    //'required' => true,
                    'options' => [
                        'query' => $data_countries,
                        'id' => 'id',
                        'name' => 'name'
                    ]
                ],
            ],
            'buttons' => [
                [
                    'href' => $this->context->link->getAdminLink('AdminModules', true, ['configure' => self::MODULE_NAME], ['configure' => self::MODULE_NAME]),
                    'title' => $this->l('Cancel'),
                    'class' => 'btn btn-default pull-left',
                    'icon' => 'process-icon-cancel',
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        return $fieldsForm;
    }

    public function addJsDefCofidisProtocol()
    {
        // Get cofidis protocols
        $cofidis_protocols = PaymentProtocol::getCofidisProtocols();
        // Get Cofidis Protocols translated names
        $names_to_display = PaymentProtocol::getCofidisProtocolsTranslatedNames();
        // Set Cofidis Protocols and Cofidis Protocols translated names as JS variables
        Media::addJsDef([
            'cofidis_protocol_names' => $names_to_display,
            'cofidis_protocols' => $cofidis_protocols
        ]);
    }

    /**
     * Display the code site form
     *
     * @param int $code_site_id
     * @param int $duplicate_code_site_id
     *
     * @return string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function displayCodeSiteForm($code_site_id = 0, $duplicate_code_site_id = 0)
    {
        $helper = $this->getCodeSiteHelperForm($code_site_id);
        $fieldsForm = $this->getCodeSiteForm($code_site_id);

        $this->addJsDefCofidisProtocol();

        if ($code_site_id > 0 || $duplicate_code_site_id > 0) {
            if ($code_site_id > 0) {
                $code_site = new MoneticoPaiementCodeSite($code_site_id);
            } else if ($duplicate_code_site_id > 0) {
                $code_site = new MoneticoPaiementCodeSite($duplicate_code_site_id);
            }

            if ($code_site instanceof MoneticoPaiementCodeSite) {
                $helper->fields_value['id_code_site'] = $code_site_id;
                $helper->fields_value['code_site_ept_id'] = $code_site->getCodeSiteEptId();
                $helper->fields_value['code_site_name'] = $code_site->getCodeSiteName();
                $helper->fields_value['code_site_payment_method'] = $code_site->getCodeSitePaymentMethod();
                $helper->fields_value['code_site_display_name'] = $code_site->getCodeSiteDisplayName();
                $helper->fields_value['code_site_payment_display_method'] = $code_site->getOption('code_site_payment_display_method');
                $helper->fields_value['code_site_cards_logo_opt'] = $code_site->getOption('code_site_cards_logo_opt');
                $helper->fields_value['code_site_additional_cards_logos[]'] = $code_site->getOption('code_site_additional_cards_logos');
                $helper->fields_value['code_site_capture_event_opt'] = $code_site->getOption('code_site_capture_event_opt');
                $helper->fields_value['code_site_langue'] = $code_site->getCodeSiteLangue();
                $helper->fields_value['code_site_3dsv2_opts'] = json_encode([
                    'header' => ['#', $this->l('Constraint from the amount of'), $this->l('State'), ''],
                    'states' => $this->getCodeSite3dsv2(),
                    'body' => $code_site->get3DSV2()]);
                $helper->fields_value['code_site_active'] = $code_site->isCodeSiteActive();
                $helper->fields_value['code_site_sandbox'] = $code_site->isCodeSiteSandbox();
                $helper->fields_value['code_site_express_payment'] = $code_site->isCodeSiteExpressPayment();
                // code site options
                $helper->fields_value['code_site_shops_languages[]'] = $code_site->getOption('code_site_shops_languages');
                $helper->fields_value['code_site_devises[]'] = $code_site->getOption('code_site_devises');

                $helper->fields_value['code_site_shop_ids[]'] = $code_site->getOption('code_site_shop_ids');
                $helper->fields_value['code_site_countries[]'] = $code_site->getOption('code_site_countries');

                $helper->fields_value['code_site_amount_option'] = $code_site->getCodeSiteAmountMin() !== null || $code_site->getCodeSiteAmountMax() != null;
                $helper->fields_value['code_site_amount_min'] = $code_site->getCodeSiteAmountMin();
                $helper->fields_value['code_site_amount_max'] = $code_site->getCodeSiteAmountMax();
            }
        } else {
            $helper->fields_value['code_site_3dsv2_opts'] = json_encode([
                'header' => ['#', $this->l('Constraint from the amount of'), $this->l('State'), ''],
                'states' => $this->getCodeSite3dsv2(),
                'body' => []]);
        }


        return $helper->generateForm($fieldsForm);
    }

    /**
     * Display the old code site form (if error)
     *
     * @param $old_code_site
     *
     * @return string
     */
    public function displayOldCodeSiteForm($old_code_site)
    {
        $this->addJsDefCofidisProtocol();

        $helper = $this->getCodeSiteHelperForm($old_code_site['id_code_site'] ?? 0);
        $fieldsForm = $this->getCodeSiteForm($old_code_site['id_code_site'] ?? 0);
        // valeur old
        $helper->fields_value['type'] = 'codesite';
        $helper->fields_value['id_code_site'] = $old_code_site['id_code_site'] ?? 0;
        $helper->fields_value['code_site_ept_id'] = $old_code_site['code_site_ept_id'];
        $helper->fields_value['code_site_name'] = $old_code_site['code_site_name'];
        $helper->fields_value['code_site_payment_method'] = $old_code_site['code_site_payment_method'];
        $helper->fields_value['code_site_display_name'] = $old_code_site['code_site_display_name'];
        $helper->fields_value['code_site_payment_display_method'] = $old_code_site['code_site_payment_display_method'];
        $helper->fields_value['code_site_cards_logo_opt'] = $old_code_site['code_site_cards_logo_opt'];
        $helper->fields_value['code_site_additional_cards_logos[]'] = $old_code_site['code_site_additional_cards_logos'];
        $helper->fields_value['code_site_capture_event_opt'] = $old_code_site['code_site_capture_event_opt'];
        $helper->fields_value['code_site_langue'] = $old_code_site['code_site_langue'];
        $helper->fields_value['code_site_3dsv2_opts'] = json_encode([
            'header' => ['#', $this->l('Constraint from the amount of'), $this->l('State'), ''],
            'states' => $this->getCodeSite3dsv2(),
            'body' => array_values($old_code_site['3dsv2'])]);

        $helper->fields_value['code_site_active'] = $old_code_site['code_site_active'];
        $helper->fields_value['code_site_sandbox'] = $old_code_site['code_site_sandbox'];
        $helper->fields_value['code_site_express_payment'] = $old_code_site['code_site_express_payment'];
        // valeur old opts
        $helper->fields_value['code_site_shops_languages[]'] = $old_code_site['code_site_shops_languages'];
        $helper->fields_value['code_site_devises[]'] = $old_code_site['code_site_devises'];

        $helper->fields_value['code_site_shop_ids[]'] = $old_code_site['code_site_shop_ids'];
        $helper->fields_value['code_site_countries[]'] = $old_code_site['code_site_countries'];
        $helper->fields_value['code_site_amount_option'] = $old_code_site['code_site_amount_option'];
        $helper->fields_value['code_site_amount_min'] = $old_code_site['code_site_amount_min'];
        $helper->fields_value['code_site_amount_max'] = $old_code_site['code_site_amount_max'];

        return $helper->generateForm($fieldsForm);
    }

    // CONFIG FORM

    /**
     * Display the config form
     * @return string
     */
    public function displayConfigForm()
    {
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/demo_mode.js');

        $demo_mode_desc = !$this->mode_demo_active() ?
            $this->l('The demonstration mode allows you to test the module and carry out a Monetico Paiement payment process without having to enter contract information.') :
            $this->l('Exit demonstration mode so you can configure the module with your contract information.');

        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Demonstration mode'),
                'icon' => 'icon-cogs'
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'module_url',
                    'id' => 'module_url',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'ajax_url',
                    'id' => 'ajax_url',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Demonstration mode'),
                    'name' => 'demo_mode',
                    'desc' => $demo_mode_desc,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Activated'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Desactivated'),
                        ],
                    ],
                    'is_bool' => true,
                    'disabled' => !$this->mode_demo_active(),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Order creation'),
                    'name' => 'create_test_order',
                    'desc' => $this->l('Create an order for each Monetico Paiement payment simulation. Orders passed in demonstration mode are identifiable by the suffix [DEMO] in their status.'),
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Activated'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Desactivated'),
                        ],
                    ],
                    'is_bool' => true,
                    'disabled' => !$this->mode_demo_active(),
                ],
            ],
        ];

        $fieldsForm[1]['form'] = [
            'legend' => [
                'title' => $this->l('General configuration'),
                'icon' => 'icon-cogs'
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'type',
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Return interface URL'),
                    'name' => 'cgi2_url',
                    'disabled' => 'disabled',
                    'desc' => $this->l('This url should be shared to your bank advisor')
                ]
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->fields_value['type'] = 'config';
        $helper->fields_value['cgi2_url'] = $this->context->link->getModuleLink($this->name, 'callback', []);
        $helper->fields_value['ajax_url'] = $this->context->link->getAdminLink('AdminMoneticoPaiementDemo', true, [], []);
        $helper->fields_value['module_url'] = $this->context->link->getAdminLink('AdminModules', true, ['configure' => self::MODULE_NAME], ['configure' => self::MODULE_NAME]);
        $helper->fields_value['demo_mode'] = $this->mode_demo_active();
        $helper->fields_value['create_test_order'] = boolval(Configuration::get('MP_DEMO_MODE_ORDERS'));

        return $helper->generateForm($fieldsForm);
    }


    /**
     * Docs : https://devdocs.prestashop-project.org/8/modules/concepts/hooks/list-of-hooks/actionfrontcontrollersetmedia
     */
    public function hookActionFrontControllerSetMedia()
    {
        $has_hf_prod_code_site = MoneticoPaiementHelper::hasCodeSiteWithHostedFieldProd();
        $has_ipa_prod_code_site = MoneticoPaiementHelper::hasCodeSiteWithIpaProd();
        $path_sdk_hf = ($has_hf_prod_code_site ? PaymentEnvironment::URI : PaymentEnvironment::SANDBOX_URI) . PaymentEnvironment::SDK_HOSTEDFIELDS;
        $path_sdk_ipa = ($has_ipa_prod_code_site ? PaymentEnvironment::URI : PaymentEnvironment::SANDBOX_URI) . PaymentEnvironment::SDK_IPA;

        // include ipa js
        $this->context->controller->registerJavascript(
            'moneticopaiement-ipa-js', $path_sdk_ipa, ['server' => 'remote']
        );
        // include hostedfields JS
        $this->context->controller->registerJavascript(
            'moneticopaiement-hostedfields-js', $path_sdk_hf, ['server' => 'remote']
        );
    }

    public function hookDisplayPaymentByBinaries(array $params)
    {
        /** @var Cart $cart */
        $cart = $params['cart'];

        if (false === Validate::isLoadedObject($cart)) {
            return '';
        }
        $currency = new \Currency($cart->id_currency);
        $invoice_address = new Address($cart->id_address_invoice);

        $code_sites_ids = MoneticoPaiementHelper::getCodeSiteIdsByAmount($cart->getOrderTotal(true, Cart::BOTH));
        // Code site filter by required elements
        $code_sites_ids = MoneticoPaiementHelper::codeSiteFilter($code_sites_ids, [
            'code_site_devises' => $currency->iso_code,
            'code_site_shop_ids' => $cart->id_shop,
        ]);
        // Code site filter by optional elements
        $code_sites_ids = MoneticoPaiementHelper::codeSiteFilterOptional($code_sites_ids, [
            'code_site_countries' => $invoice_address->id_country,
            'code_site_languages' => $this->context->language->id,
        ]);

        $paymentOptions = [];
        foreach ($code_sites_ids as $code_site_id) {
            $code_site = new MoneticoPaiementCodeSite($code_site_id);
            if ($code_site->getOption('code_site_payment_display_method') == 'hostedfields') {
                [$total_to_pay] = $this->getOrderTotalAndTaxStatus($cart);

                $paymentOptions[] = [
                    'MoneticoPaymentMethodNumber' => (int)$code_site_id,
                    'payment_method' => $code_site->getCodeSitePaymentMethod(),
                    'btn_label' => sprintf($this->l('PLACE ORDER AND PAY %s'), $total_to_pay . $currency->getSymbol()),
                    'paymentType' => 'Hf',
                ];
            }
            if ($code_site->getCodeSitePaymentMethod() === 'ipa') {
                $paymentOptions[] = [
                    'MoneticoPaymentMethodNumber' => (int)$code_site_id,
                    'payment_method' => $code_site->getCodeSitePaymentMethod(),
                    'btn_label' => $this->l('PAY BY BANK TRANSFER'),
                    'paymentType' => 'Ipa',
                ];
            }
        }

        $this->context->smarty->assign([
            'paymentOptions' => $paymentOptions,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/displayPaymentByBinaries.tpl');
    }

    /**
     * Docs : https://devdocs.prestashop-project.org/8/modules/concepts/hooks/list-of-hooks/actionpresentpaymentoptions/
     *
     * @param $params
     */
    public function hookPaymentOptions($params)
    {
        $cart = $params['cart'];
        // while the delivery state is not ready
        if ($cart->id_address_delivery > 0 && $cart->id_address_invoice > 0) {

            $currency = new \Currency($cart->id_currency);
            $invoice_address = new Address($cart->id_address_invoice);
            // code site filter by amount
            $code_sites_ids = MoneticoPaiementHelper::getCodeSiteIdsByAmount($cart->getOrderTotal(true, Cart::BOTH));
            // Code site filter by required elements
            $code_sites_ids = MoneticoPaiementHelper::codeSiteFilter($code_sites_ids, [
                'code_site_devises' => $currency->iso_code,
                'code_site_shop_ids' => $cart->id_shop,
            ]);
            // Code site filter by optional elements
            $code_sites_ids = MoneticoPaiementHelper::codeSiteFilterOptional($code_sites_ids, [
                'code_site_countries' => $invoice_address->id_country,
                'code_site_shops_languages' => $this->context->language->id,
            ]);

            $customerGroups = $this->context->customer->getGroups();
            $moneticoOptions = [];
            foreach ($code_sites_ids as $code_site_id) {

                $newOption = new PaymentOption();
                $code_site = new MoneticoPaiementCodeSite($code_site_id);
                // form for pass data during checkout process
                $inputs = [];
                $inputs['MoneticoPaymentMethodNumber'] = [
                    'name' => 'MoneticoPaymentMethodNumber',
                    'type' => 'hidden',
                    'value' => (int)$code_site_id,
                ];

                if ($code_site->getOption('code_site_cards_logo_opt') && $code_site->getCodeSitePaymentMethod() !== 'ipa') {
                    $newOption->setLogo(_MODULE_DIR_ . $this->name . '/views/img/monetico_paiement_checkout_logo.png');
                    if (count($code_site->getOption('code_site_additional_cards_logos'))) {
                        $code_site_additional_cards_logos = $code_site->getOption('code_site_additional_cards_logos');
                        sort($code_site_additional_cards_logos);
                        $additional_cards_string = implode("_", $code_site_additional_cards_logos);
                        $logo_url = _MODULE_DIR_ . $this->name . "/views/img/monetico_paiement_checkout_logo_" . mb_strtolower($additional_cards_string) . ".png";
                        if (file_exists($logo_url) || $this->createLogoWithAdditionalCards($code_site_additional_cards_logos,
                                dirname(__FILE__) . '/views/img/monetico_paiement_checkout_logo.png',
                                dirname(__FILE__) . "/views/img/monetico_paiement_checkout_logo_" . mb_strtolower($additional_cards_string) . ".png"
                            )) {
                            $newOption->setLogo($logo_url);
                        }
                    }
                }

                // set and add information for option
                $newOption->setModuleName($this->name . '-' . $code_site_id)
                    ->setCallToActionText($code_site->getCodeSiteDisplayName())
                    ->setAction($this->context->link->getModuleLink($this->name, 'transaction'))
                    ->setInputs($inputs);

                if ($code_site->getCodeSitePaymentMethod() && !in_array($code_site->getCodeSitePaymentMethod(), ['cb', 'ipa'])) {
                    if ($code_site->getOption('code_site_cards_logo_opt')) {
                        if ($code_site->getCodeSitePaymentMethod() === '5-10-12xcb') {
                            $img_name = 'nxcb';
                        } else {
                            $img_name = $code_site->getCodeSitePaymentMethod();
                        }
                        $newOption->setLogo(PaymentEnvironment::MONETICO_TDB_LOGO_URL . $img_name . '.png');
                    }
                } else if ($code_site->getOption('code_site_payment_display_method') == 'hostedfields') {
                    $ept = new MoneticoPaiementEPT($code_site->getCodeSiteEptId());
                    $paymentMeanTokenGenerator = new PaymentMeanTokenGenerator();
                    $paymentMeanTokenGenerator->setDatetime(new \DateTime('now'));

                    $paymentMeanTokenGeneratorMerchantConfiguration = new MerchantConfiguration();
                    $paymentMeanTokenGeneratorMerchantConfiguration->setPointOfSale($ept->getEptNumber());
                    $paymentMeanTokenGeneratorMerchantConfiguration->setLanguage($code_site->getCodeSiteLangue());
                    $paymentMeanTokenGeneratorMerchantConfiguration->setConfiguration($code_site->getCodeSiteName());
                    $paymentMeanTokenGenerator->setMerchantConfiguration($paymentMeanTokenGeneratorMerchantConfiguration);

                    $payment_means_token_generator_context = new Context();
                    $payment_means_token_generator_context->setUseCase("Payment");
                    $paymentMeanTokenGenerator->setContext($payment_means_token_generator_context);

                    $request = new MoneticoPaiementApi();
                    $hmacComputer = new HmacComputer();
                    $fields = json_encode($paymentMeanTokenGenerator->getFields());
                    $seal = $hmacComputer->sealString($fields, $ept->getEptMac());

                    $header = [
                        'Content-Type:application/json',
                        'MAC:' . $seal
                    ];

                    $request->process($paymentMeanTokenGenerator->getUrl($code_site->isCodeSiteSandbox()), $paymentMeanTokenGenerator->getFields(), $header);
                    $paymentMeanTokenGeneratorResponse = new Response(json_decode($request->getResponse(), true));

                    if ($paymentMeanTokenGeneratorResponse->getReturnCode() == 0) {

                        $hmacComputer = new HmacComputer();
                        $seal = $hmacComputer->sealFields(
                            [$ept->getEptNumber(), (int)$code_site_id, $this->context->cart->id, $paymentMeanTokenGeneratorResponse->getToken()],
                            $ept->getEptMac());

                        $this->context->smarty->assign([
                            'seal' => $seal,
                            'ept' => $ept->getEptNumber(),
                            'token' => $paymentMeanTokenGeneratorResponse->getToken(),
                            'codeSiteId' => (int)$code_site_id,
                            'cartId' => $this->context->cart->id,
                            'paymentServiceUrl' => $this->context->link->getModuleLink($this->name, 'payment_service', [
                                'stepName' => 'init',
                            ], true)
                        ]);

                        $newOption->setInputs($inputs)
                            ->setAdditionalInformation($this->context->smarty->fetch('module:MoneticoPaiement/views/templates/iframe.tpl'))
                            ->setBinary(true)
                            ->setAction($this->context->link->getModuleLink($this->name, 'hf_transaction', ['MoneticoPaymentMethodNumber' => (int)$code_site_id], true));
                    }

                } else if ($code_site->getCodeSitePaymentMethod() === 'ipa') {
                    $ept = new MoneticoPaiementEPT($code_site->getCodeSiteEptId());
                    $session_id = $_SESSION['payment_session_data_' . $code_site_id]['id'] ?? null;

                    $hmacComputer = new HmacComputer();
                    $init_seal = $hmacComputer->sealFields(
                        [$code_site_id],
                        $ept->getEptMac());

                    if (isset($session_id)) {
                        try {
                            $can_restart = PaymentSessionInitiation::canRestart($session_id, $code_site->isCodeSiteSandbox());
                        } catch (Exception $exception) {
                            $can_restart = false;
                            PrestaShopLogger::addLog("[Monetico] " . $exception->getMessage() . (_PS_MODE_DEV_ ? ($exception->getTraceAsString()) : ''), 3);
                        }
                        $reference = $_SESSION['payment_session_data_' . $code_site_id]['reference'] ?? null;
                        $date = $_SESSION['payment_session_date_transaction_' . $code_site_id] ?? null;

                        $transaction_seal = $hmacComputer->sealFields(
                            [$code_site_id, $reference, $date->format("d/m/Y:H:i:s") ?? null],
                            $ept->getEptMac());
                    }

                    $this->context->smarty->assign([
                        'session_id_' . $code_site->getIdCodeSite() => $session_id,
                        'module_name' => $this->name,
                        'code_site_id' => (int)$code_site_id,
                        'init_seal' => $init_seal,
                        'transaction_seal' => $transaction_seal ?? null,
                        'reference' => $reference ?? null,
                        'can_restart' => $can_restart ?? null,
                        'date' => isset($date) ? $date->format("d/m/Y:H:i:s") : '',
                        'paymentSessionUrl' => $this->context->link->getModuleLink($this->name, 'payment_session'),
                    ]);

                    $newOption->setAdditionalInformation($this->context->smarty->fetch('module:MoneticoPaiement/views/templates/ipa.tpl'))
                        ->setBinary(true);
                }
                $moneticoOptions[] = $newOption;
            }
            return $moneticoOptions;
        }
    }

    /**
     * Check if refund is possible
     *
     * @param array $payment_state_result
     *
     * @return bool
     */
    public function checkIfRefundIsPossible(array $payment_state_result)
    {
        $possible_amount_without_currency = Helper::getAmountWithoutCurrency($payment_state_result['montantrecouvre']);
        $refund_already_amount = 0;

        if (isset($payment_state_result['recredits']['total'])) {
            $refund_already_amount = Helper::getAmountWithoutCurrency($payment_state_result['recredits']['total']);
        }

        $possible_amount = round($possible_amount_without_currency - $refund_already_amount, 2);

        return $possible_amount > 0;
    }

    /**
     * Doc https://devdocs.prestashop-project.org/8/modules/concepts/hooks/list-of-hooks/actiongetadminorderbuttons/
     * Display button action in order page (PS >= 1.7.7.0)
     *
     * @param array $params
     *
     * @throws \PrestaShopDatabaseException
     *
     * @throws \PrestaShopException
     * @throws \PrestaShop\PrestaShop\Core\Exception\TypeException
     */
    public function hookActionGetAdminOrderButtons(array $params)
    {
        if ($this->hookVersionCompare()) {
            $order_id = $params['id_order'];
            $order = new Order($order_id);

            $demo_orders = MoneticoPaiementHelper::getDemoOrders($this->getDemoEptNumbers());
            $demo_order_ids = [];
            if (!empty($demo_orders)) {
                foreach ($demo_orders as $demo_order) {
                    $demo_order_ids[] = $demo_order['id_order'];
                }
            }
            $is_demo_order = in_array($order->id, $demo_order_ids);

            if ($order->payment === $this->displayName) {
                // get actions available
                $payment_actions = $this->getPaymentActions($order_id);

                if ($payment_actions['success']) {
                    $is_partial_payment = $payment_actions['request_value']['etat'] === 'PP';
                    $instalment_url_property = $payment_actions['request_value']['etat'] === 'PF' ? "&instalment_payment=1" : "";
                    $payment_actions_values = $payment_actions['actions'];
                    // if refund is available
                    if ($payment_actions_values['can_refund']) {
                        if ($is_demo_order) {
                            $url = '#';
                            $message = '<em>' . $this->l('To make a Monetico Paiement refund, your server must be authorized by our services.') . "</em>";
                            $message .= '\n<strong>' . $this->l('This action is therefore not available in demonstration mode.') . '</strong>';
                        } else {
                            $url = $this->context->link->getAdminLink('AdminModules') . "&order_id=" . $order_id . '&configure=' . $this->name;
                        }
                        $name = $this->l('Refund with Monetico Paiement');

                        $bar = $params['actions_bar_buttons_collection'];
                        if ($is_demo_order) {
                            $bar->add(
                                new ActionsBarButton(
                                    'btn-primary', ['style' => '#popup_ok{display:none}', 'href' => '#', 'onclick' => 'confirm_link(\'\', \'' . $message . '\', \'' . $this->l("Ok") . '\', \'' . $this->l("Cancel") . '\', \'' . $url . '\', \'#\')'
                                ], $name
                                )
                            );
                        } else {
                            $bar->add(
                                new ActionsBarButton(
                                    'btn-primary', ['href' => $url
                                ], $name
                                )
                            );
                        }

                    }

                    // if capture is available
                    if ($payment_actions_values['can_capture']) {
                        $name = $this->l('Recovery');
                        $bar = $params['actions_bar_buttons_collection'];

                        $message = $this->l('Do you confirm recovery ?');

                        if ($is_partial_payment) {
                            $url = $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . "&order_id=" . $order_id . "&partial_capture=1";
                            if ($is_demo_order) {
                                $message = $this->l('In demonstration mode, no amount will actually be recovered.');
                                $bar->add(
                                    new ActionsBarButton(
                                        'btn-primary', ['href' => '#', 'onclick' => 'confirm_link(\'\', \'' . $message . '\', \'' . $this->l("Ok") . '\', \'' . $this->l("Cancel") . '\', \'' . $url . '\', \'#\')'
                                    ], $name
                                    )
                                );
                            } else {
                                $bar->add(
                                    new ActionsBarButton(
                                        'btn-primary', ['href' => $url], $name
                                    )
                                );
                            }
                        } else {
                            $url = $this->context->link->getAdminLink('AdminMoneticoPaiementCapture') . "&order_id=" . $order_id . "&capture=1" . $instalment_url_property;
                            if ($is_demo_order) {
                                $message .= '</br><em>' . $this->l('In demonstration mode, no amount will actually be recovered.') . "</em>";
                            }
                            $bar->add(
                                new ActionsBarButton(
                                    'btn-primary', ['href' => '#', 'onclick' => 'confirm_link(\'\', \'' . $message . '\', \'' . $this->l("Yes") . '\', \'' . $this->l("No") . '\', \'' . $url . '\', \'#\')'
                                ], $name
                                )
                            );
                        }
                    }

                    // if cancel is available
                    if ($payment_actions_values['can_cancel']) {
                        $url = $this->context->link->getAdminLink('AdminMoneticoPaiementCapture') . "&order_id=" . $order_id . "&capture=0" . $instalment_url_property;
                        if (count($payment_actions['request_value']['recouvrements']) === 0 || $payment_actions['request_value']['modalitepaiement'] === 'PP') {
                            $name = $this->l('Cancelation');
                        } else {
                            $name = $this->l('Next Instalments Cancelation');
                        }
                        $message = $this->l('Do you confirm cancelation ?');
                        $bar = $params['actions_bar_buttons_collection'];
                        $bar->add(
                            new ActionsBarButton(
                                'btn-primary', ['href' => '#', 'onclick' => 'confirm_link(\'\', \'' . $message . '\', \'' . $this->l("Yes") . '\', \'' . $this->l("No") . '\', \'' . $url . '\', \'#\')'
                            ], $name
                            )
                        );
                    }
                }
            }
        }
    }

    /**
     * Hook for adding required jquery CSS and JS for confirm_link popup
     *
     * @return void
     */
    public function hookActionAdminControllerSetMedia()
    {
        $this->context->controller->addCSS(_PS_JS_DIR_ . 'jquery/plugins/alerts/jquery.alerts.css');
        $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/plugins/alerts/jquery.alerts.js');
        $this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/jquery.browser-0.1.0.min.js');
    }

    /**
     * Hook for display the button action (ONLY for PS < 1.7.7.0)
     *
     * @param $params
     *
     * @return false|string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function hookDisplayBackOfficeOrderActions($params)
    {
        if (!$this->hookVersionCompare()) {
            $order_id = $params['id_order'];
            $order = new Order($order_id);
            $display = "";
            if ($order->payment === $this->displayName) {
                // get actions available
                $payment_actions = $this->getPaymentActions($order_id);

                if ($payment_actions['success']) {

                    $instalment_url_property = $payment_actions['request_value']['etat'] === 'PF' ? "&instalment_payment=1" : "";
                    $payment_actions_values = $payment_actions['actions'];
                    // if refund is available
                    if ($payment_actions_values['can_refund']) {
                        $url = $this->context->link->getAdminLink('AdminModules') . "&order_id=" . $order_id . '&configure=' . $this->name;
                        $name = $this->l('Refund with Monetico Paiement');

                        $this->context->smarty->assign([
                            'name' => $name,
                            'url' => $url
                        ]);

                        $display .= $this->display(__FILE__, 'views/templates/hook/display_btn.tpl');
                    }
                    // if capture is available
                    if ($payment_actions_values['can_capture']) {

                        $url = $this->context->link->getAdminLink('AdminMoneticoPaiementCapture') . "&order_id=" . $order_id . "&capture=1" . $instalment_url_property;
                        $name = $this->l('Recovery');
                        $this->context->smarty->assign([
                            'name' => $name,
                            'url' => $url
                        ]);
                        $display .= $this->display(__FILE__, 'views/templates/hook/display_btn.tpl');
                    }
                    // if can_cancel is available
                    if ($payment_actions_values['can_cancel']) {
                        $url = $this->context->link->getAdminLink('AdminMoneticoPaiementCapture') . "&order_id=" . $order_id . "&capture=0" . $instalment_url_property;
                        if (count($payment_actions['request_value']['recouvrements']) === 0) {
                            $name = $this->l('Cancelation');
                        } else {
                            $name = $this->l('Next Instalments Cancelation');
                        }

                        $this->context->smarty->assign([
                            'name' => $name,
                            'url' => $url
                        ]);
                        $display .= $this->display(__FILE__, 'views/templates/hook/display_btn.tpl');
                    }
                }
            }
            return $display;
        }
    }


    /**
     * get values to display on the order page
     *
     * @param \Order $order
     *
     * @return void
     */
    public function getRefundAndInstalmentSectionValues(Order $order)
    {
        $response_current_state = $this->getPaymentState($order->id);
        $refund_smarty_render = [];

        $this->context->smarty->assign([
            'nb_instalment' => 0,
            'instalments' => [],
            'nb_refund' => 0,
            'refunds' => [],
            'nb_recoveries' => 0,
            'recoveries' => []

        ]);


        if ($response_current_state['success']) {
            $request_values = $response_current_state['request_value'];

            if (isset($request_values['recredits']['recredit'])) {
                $responseCashbacks = $request_values['recredits']['recredit'];

                if (array_key_exists('date_GMT', $responseCashbacks)) {
                    $responseCashbacks = [0 => $responseCashbacks];
                }

                foreach ($responseCashbacks as $refund_value) {
                    $refund_smarty_render[] = [
                        'date' => \DateTime::createFromFormat("Y-m-d", $refund_value["date_GMT"])->format("d/m/Y") . ' ' . $refund_value["heure_GMT"],
                        'amount' => $refund_value['montant_recredite']
                    ];
                }


                $this->context->smarty->assign([
                    'nb_refund' => count($refund_smarty_render),
                    'refunds' => $refund_smarty_render,
                ]);

            }

            if (isset($request_values['recouvrements']['recouvrement']) && $request_values['modalitepaiement'] === 'PP') {
                $responseCashbacks = $request_values['recouvrements']['recouvrement'];
                $recovery_smarty_render = [];

                if (array_key_exists('dateheure_gmt', $responseCashbacks)) {
                    $responseCashbacks = [0 => $responseCashbacks];
                }

                foreach ($responseCashbacks as $capture_value) {
                    $recovery_smarty_render[] = [
                        'date' => \DateTime::createFromFormat("Y-m-d H:i:s", $capture_value['dateheure_gmt'])->format("d/m/Y H:i:s"),
                        'amount' => $capture_value['montant']
                    ];
                }

                $this->context->smarty->assign([
                    'nb_recoveries' => count($recovery_smarty_render),
                    'recoveries' => array_reverse($recovery_smarty_render),
                ]);

            }

            $multiple_smarty_render = [];
            if (isset($request_values['echeancier']["echeance"])) {
                $instalments = $request_values['echeancier']["echeance"];

                foreach ($instalments as $instalment) {
                    $succesfull_state = 0;
                    switch ($instalment["resultat"]) {
                        case 'I':
                            $instalment_status = $this->l('Pending');
                            break;
                        case "P":
                            $instalment_status = $this->l('Paid');
                            $succesfull_state = 1;
                            break;
                        case "X":
                            $instalment_status = $this->l('Canceled');
                            break;
                        default:
                            $instalment_status = $this->l('Error');
                            $succesfull_state = -1;
                    }

                    $multiple_smarty_render[] = [
                        'number' => '#' . $instalment['numero'],
                        'date' => \DateTime::createFromFormat("Y-m-d", $instalment["date"])->format("d/m/Y"),
                        'amount' => $instalment["montant"],
                        'state' => $instalment_status,
                        'succesfull_state' => $succesfull_state
                    ];
                }

                $this->context->smarty->assign([
                    'nb_instalment' => count($multiple_smarty_render),
                    'instalments' => array_reverse($multiple_smarty_render),
                ]);

            }
        }
    }

    /**
     * Docs : https://devdocs.prestashop-project.org/8/modules/concepts/hooks/list-of-hooks/displayadminordermainbottom/
     * Hook for display the payment refund table (PS >= 1.7.7.0)
     *
     * @param $params
     *
     * @return false|string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function hookDisplayAdminOrderMainBottom($params)
    {
        if ($this->hookVersionCompare()) {
            $order = new Order($params['id_order']);
            // check if payment with MP
            if ($order->payment === $this->displayName) {
                $this->getRefundAndInstalmentSectionValues($order);
                return $this->display(__FILE__, 'views/templates/hook/display_order_main_bottom.tpl');
            }
        }
    }


    /**
     * Hook for display the payment refund table (ONLY for PS < 1.7.7.0)
     *
     * @param $params
     *
     * @return false|string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function hookDisplayAdminOrderLeft($params)
    {
        if (!$this->hookVersionCompare()) {
            $order = new Order($params['id_order']);
            if ($order->payment === $this->displayName) {
                $this->getRefundAndInstalmentSectionValues($order);
                return $this->display(__FILE__, 'views/templates/hook/display_admin_order_left.tpl');
            }
        }
    }

    /**
     * Display the refund form
     * @return string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function displayRefundForm()
    {

        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        $order_id = Tools::getValue('order_id');
        $order = new Order($order_id);

        $currency = new \Currency($order->id_currency);
        $currency_iso_code = $currency->iso_code;

        $payment_state = $this->getPaymentState($order_id);

        $payment_state_result = $payment_state['request_value'];

        $amount_without_currency = Helper::getAmountWithoutCurrency($payment_state_result['montantcommande']);
        $possible_amount_without_currency = Helper::getAmountWithoutCurrency($payment_state_result['montantrecouvre']);

        $refund_already_amount = 0;

        if (isset($payment_state_result['recredits']['total'])) {
            $refund_already_amount = Helper::getAmountWithoutCurrency($payment_state_result['recredits']['total']);
        }

        $possible_amount = round($possible_amount_without_currency - $refund_already_amount, 2);

        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Payment Refund with Monetico Paiement'),
                'icon' => 'icon-cogs'
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'order_id',
                    'value' => $order_id
                ],
                [
                    'type' => 'text',
                    'name' => 'order_amount',
                    'value' => $amount_without_currency . $currency_iso_code,
                    'label' => $this->l('Order amount'),
                    'readonly' => true,
                ],
                [
                    'type' => 'text',
                    'name' => 'order_already_refund_amount',
                    'value' => $refund_already_amount . $currency_iso_code,
                    'label' => $this->l('Already refunded amount'),
                    'readonly' => true,
                ],
                [
                    'type' => 'hidden',
                    'name' => 'hidden_possible_amount',
                    'value' => $possible_amount,
                ],
                [
                    'type' => 'text',
                    'name' => 'possible_amount',
                    'value' => $possible_amount . $currency_iso_code,
                    'label' => $this->l('Refundable amount'),
                    'readonly' => true
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Total refund'),
                    'desc' => $this->l('Total refund amount will be the refundable amount'),
                    'name' => 'refund_type',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ],
                    ],
                    'is_bool' => false,
                ],
                [
                    'type' => 'text',
                    'name' => 'amount',
                    'cast' => 'intval',
                    'label' => $this->l('Refund amount'),
                    'size' => 10,
                ],
            ],
            'buttons' => [
                [
                    'href' => $this->context->link->getAdminLink('AdminOrders', true, ['id_order' => $order_id, "vieworder" => ""], ['id_order' => $order_id, 'vieworder' => ""]),
                    'title' => $this->l('Back to order view'),
                    'class' => 'btn btn-default pull-left',
                    'icon' => 'icon-reply',
                ]
            ],
            'submit' => [
                'title' => $this->l('Process to refund'),
                'class' => 'btn btn-primary pull-right',
                'icon' => 'icon-money',
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = "AdminMoneticoPaiementRefund";
        $helper->token = Tools::getAdminTokenLite('AdminMoneticoPaiementRefund');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminMoneticoPaiementRefund', true, ['order_id' => $order_id], ['order_id' => $order_id]);
        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->l('Payment Refund with Monetico Paiement');
        $helper->submit_action = 'submitRefund';
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&saveRefund' .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'icon' => 'icon-cogs'
            ],
            'cancel' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to order view')
            ]
        ];
        // assign value
        $helper->fields_value['order_id'] = $order_id;
        $helper->fields_value['refund_type'] = 0;
        $helper->fields_value['hidden_possible_amount'] = round($possible_amount, 2);
        $helper->fields_value['order_amount'] = $amount_without_currency . $currency_iso_code;
        $helper->fields_value['order_already_refund_amount'] = round($refund_already_amount, 2) . $currency_iso_code;
        $helper->fields_value['possible_amount'] = round($possible_amount, 2) . $currency_iso_code;
        $helper->fields_value['amount'] = "0.00";

        return $helper->generateForm($fieldsForm);
    }

    /**
     * Display partial capture form
     * @return string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function displayPartialCaptureForm(): string
    {
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
        $order_id = (int)Tools::getValue('order_id');
        $order = new Order($order_id);

        $currency = new \Currency($order->id_currency);
        $currency_iso_code = $currency->iso_code;

        $payment_state = $this->getPaymentState($order_id);

        $payment_state_result = $payment_state['request_value'];

        $amount_without_currency = Helper::getAmountWithoutCurrency($payment_state_result['montantcommande']);
        $already_capture_amount_without_currency = Helper::getAmountWithoutCurrency($payment_state_result['montantrecouvre']) ?? 0;

        $possible_amount = round($amount_without_currency - $already_capture_amount_without_currency, 2);

        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Payment Capture with Monetico Paiement'),
                'icon' => 'icon-cogs'
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'order_id',
                    'value' => $order_id
                ],
                [
                    'type' => 'text',
                    'name' => 'order_amount',
                    'value' => $amount_without_currency . $currency_iso_code,
                    'label' => $this->l('Order amount'),
                    'readonly' => true,
                ],
                [
                    'type' => 'text',
                    'name' => 'order_already_capture_amount',
                    'value' => $already_capture_amount_without_currency . $currency_iso_code,
                    'label' => $this->l('Already captured amount'),
                    'readonly' => true,
                ],
                [
                    'type' => 'hidden',
                    'name' => 'hidden_possible_amount',
                    'value' => $possible_amount,
                ],
                [
                    'type' => 'text',
                    'name' => 'possible_amount',
                    'value' => $possible_amount . $currency_iso_code,
                    'label' => $this->l('Recoverable amount'),
                    'readonly' => true
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Full recovery'),
                    'desc' => $this->l('Full recovery amount will be the recoverable amount'),
                    'name' => 'capture_type',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        ],
                    ],
                    'is_bool' => false,
                ],
                [
                    'type' => 'text',
                    'name' => 'amount',
                    'cast' => 'intval',
                    'label' => $this->l('Recovery amount'),
                    'size' => 10,
                ],
            ],
            'buttons' => [
                [
                    'href' => $this->context->link->getAdminLink('AdminOrders', true, ['id_order' => $order_id, 'vieworder' => ''], ['id_order' => $order_id, 'vieworder' => '']),
                    'title' => $this->l('Back to order view'),
                    'class' => 'btn btn-default pull-left',
                    'icon' => 'icon-reply',
                ]
            ],
            'submit' => [
                'title' => $this->l('Recover'),
                'class' => 'btn btn-primary pull-right',
                'icon' => 'icon-money',
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = 'AdminMoneticoPaiementCapture';
        $helper->token = Tools::getAdminTokenLite('AdminMoneticoPaiementCapture');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminMoneticoPaiementCapture', true, ['order_id' => $order_id, 'capture' => 1], ['order_id' => $order_id, 'capture' => 1]);
        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->l('Payment capture with Monetico Paiement');
        $helper->submit_action = 'submitPartialCapture';

        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Process to capture'),
                'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&savePartialCapture' .
                    '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'icon' => 'icon-cogs'
            ],
            'cancel' => [
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to order view')
            ]
        ];
        // assign value
        $helper->fields_value['order_id'] = $order_id;
        $helper->fields_value['capture_type'] = 0;
        $helper->fields_value['hidden_possible_amount'] = round($possible_amount, 2);
        $helper->fields_value['order_amount'] = $amount_without_currency . $currency_iso_code;
        $helper->fields_value['order_already_capture_amount'] = round($already_capture_amount_without_currency, 2) . $currency_iso_code;
        $helper->fields_value['possible_amount'] = round($possible_amount, 2) . $currency_iso_code;
        $helper->fields_value['amount'] = "0.00";

        return $helper->generateForm($fieldsForm);
    }


    /**
     * Display the refund success/error (PS >= 1.7.7.0)
     *
     * @param array $params
     *
     * @return false|string
     */
    public function hookDisplayAdminOrderTop(array $params)
    {
        $order_id = $params['id_order'];
        $order = new Order($order_id);

        if ($order->module === $this->name) {

            if (isset($_SESSION['MP_ALERT'])) {
                $this->context->smarty->assign([
                    'success' => $_SESSION['MP_ALERT']['MP_STATUS'],
                    'message' => $_SESSION['MP_ALERT']['MP_MESSAGE'] ?? '',
                    'content' => $_SESSION['MP_ALERT']['MP_DESC'] ?? '',
                ]);
                unset($_SESSION['MP_ALERT']);
                return $this->display(__FILE__, 'views/templates/hook/display_alert.tpl');
            }

            $transaction_id = MoneticoPaiementHelper::getTransactionIdByCartId($order->id_cart);
            if (!empty($transaction_id)) {
                $transaction = new MoneticoPaiementTransaction($transaction_id);
                $order_context = json_decode($transaction->getOrderContext(), true) ?? null;
            }

            $payment_protocol = $order_context['protocole'] ?? '';
            $is_loan_payment = $payment_protocol === 'loan';
            $is_ipa_payment = $payment_protocol === 'ipa';
            $is_pending = $order->getCurrentState() == Configuration::get(PaymentStatus::MP_PENDING);

            if ($order->getCurrentState() == Configuration::get(PaymentStatus::MP_WARNING)) {
                $message = sprintf($this->l('Order %s is already paid. Check your Monetico Paiement board.'), $order_id);
                return $this->displayError($message);

            } elseif ($is_pending && ($is_loan_payment || $is_ipa_payment)) {
                $msg = $this->l('Payment awaiting validation by %s');
                $str = $is_loan_payment ? 'Cofidis' : $this->l('the bank of the customer');
                $message = sprintf($msg, $str);
                return $this->displayInformation($message);
            }

            if ($order->payment === $this->displayName) {
                $payment_status = $this->getPaymentState($order_id);
                if (!$payment_status['success']) {
                    $this->createLog($this->l('An error was returned during the Monetico Paiement request') . ' : ' . $payment_status['response_value']['desc'], 3);
                    $this->context->smarty->assign([
                        'success' => $payment_status['success'],
                        'message' => $this->l('An error was returned during the Monetico Paiement request'),
                        'content' => $payment_status['response_value'],
                    ]);
                    return $this->display(__FILE__, 'views/templates/hook/display_alert.tpl');
                }
            }
        }
    }

    /**
     * Docs : https://devdocs.prestashop-project.org/8/modules/concepts/hooks/list-of-hooks/displayadminafterheader/
     * Display the refund success/error (ONLY for PS < 1.7.7.0)
     * @return false|string
     */
    public function hookDisplayAdminAfterHeader()
    {
        if (!$this->hookVersionCompare() && ($id_order = Tools::getValue('id_order'))) {
            return $this->hookDisplayAdminOrderTop(['id_order' => $id_order]);

        }
    }


    /**
     * Docs : https://devdocs.prestashop-project.org/8/modules/concepts/hooks/list-of-hooks/displayadmincustomers/
     * Display the bank cardsto customer view
     *
     * @param $customer
     *
     * @return false|string
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function hookDisplayAdminCustomers($customer)
    {

        $user_alias = MoneticoPaiementHelper::getUsersAlias($customer['id_customer']);
        $data_alias = [];

        foreach ($user_alias as $alias) {
            $code_site = new MoneticoPaiementCodeSite($alias['code_site_id']);
            $data_alias[] = [
                'code_site_id' => $alias['code_site_id'],
                'code_site_name' => $code_site->getCodeSiteName(),
                'value' => $alias['alias']
            ];
        }
        $this->smarty->assign('all_alias', $data_alias);
        return $this->display(__FILE__, 'views/templates/hook/display_admin_customers.tpl');
    }

    /**
     * Docs : https://devdocs.prestashop-project.org/8/modules/concepts/hooks/list-of-hooks/actiondefinitionidgriddefinitionmodifier/
     * Add column to orders grid
     *
     * @param array $params
     *
     * @return void
     */
    public function hookActionOrderGridDefinitionModifier(array $params)
    {
        /** @var $definition \PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinition */
        $definition = $params['definition'];
        // columns
        $columns = $definition->getColumns();
        $columns->addAfter('payment', (new DataColumn('moneticopaiement_reference'))
            ->setName($this->l('Monetico Paiement Reference'))
            ->setOptions([
                'field' => 'moneticopaiement_reference',
            ]));
        // filters
        $filters = $definition->getFilters();
        $filters->add((new Filter('moneticopaiement_reference', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->l('Search Reference'),
                ],
            ])
            ->setAssociatedColumn('moneticopaiement_reference'));

        // `moneticopaiement_reference` is the alias of the `order_payment.transaction_id` field in the filter SQL request
        // See in the hookActionOrderGridQueryBuilderModifier() hook below.
    }

    /**
     * Docs : https://devdocs.prestashop-project.org/8/modules/concepts/hooks/list-of-hooks/actiondefinitionidgridquerybuildermodifier/
     * Get the value , sorter , and filter the orders grid
     *
     * @param array $params
     *
     * @return void
     */
    public function hookActionOrderGridQueryBuilderModifier(array $params)
    {
        /** @var $searchQueryBuilder \Doctrine\ORM\QueryBuilder */
        $searchQueryBuilder = $params['search_query_builder'];
        /** @var $searchCriteria \PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface */
        $searchCriteria = $params['search_criteria'];

        $searchQueryBuilder->addSelect('op.transaction_id as moneticopaiement_reference');
        $searchQueryBuilder->leftjoin('o', pSQL(_DB_PREFIX_) . 'order_payment', 'op', 'op.order_reference = o.reference ');

        if ('moneticopaiement_reference' === $searchCriteria->getOrderBy()) {
            $searchQueryBuilder->orderBy('op.transaction_id', $searchCriteria->getOrderWay());
        }

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('moneticopaiement_reference' === $filterName) {
                $searchQueryBuilder->andWhere('op.transaction_id = :transaction_id');
                $searchQueryBuilder->setParameter('transaction_id', $filterValue);
                if (!$filterValue) {
                    $searchQueryBuilder->orWhere('op.transaction_id IS NULL');
                }
            }
        }
    }

    /**
     * Docs : https://devdocs.prestashop-project.org/8/modules/concepts/hooks/list-of-hooks/actionorderstatusupdate/
     *
     * @param $params
     *
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function hookActionOrderStatusUpdate($params)
    {
        $order_id = $params['id_order'];
        $order = new Order($order_id);
        if ($order->payment === $this->displayName) {

            $transaction_id = MoneticoPaiementHelper::getTransactionIdByCartId($order->id_cart);
            if (!$transaction_id)
                return;

            $transaction = new MoneticoPaiementTransaction($transaction_id);
            if (!$transaction)
                return;
            $code_site = new MoneticoPaiementCodeSite($transaction->getCodeSiteId());
            if (!$code_site)
                return;

            $event_id = intval($code_site->getOption('code_site_capture_event_opt'));
            if ($params['newOrderStatus']->id === $event_id) {
                $payment_actions = $this->getPaymentActions($order_id);
                if ($payment_actions['actions']['can_capture']) {
                    $this->captureRequest($order, $payment_actions['request_value']);
                }
            }
        }

        return;
    }

    /**
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    public function hookDisplayOrderDetail($params)
    {
        $order = $params['order'];
        $notice = null;
        if ($order->module === $this->name) {
            $transaction_id = MoneticoPaiementHelper::getTransactionIdByCartId($order->id_cart);

            if (!empty($transaction_id)) {
                $transaction = new MoneticoPaiementTransaction($transaction_id);

                if ($transaction instanceof MoneticoPaiementTransaction) {
                    $order_context = json_decode($transaction->getOrderContext(), true);
                    $payment_protocol = $order_context['protocole'] ?? '';
                    $is_loan_payment = $payment_protocol === 'loan';
                    $is_ipa_payment = $payment_protocol === 'ipa';
                    $is_pending = $order->getCurrentState() == Configuration::get(PaymentStatus::MP_PENDING);

                    if (($is_loan_payment || $is_ipa_payment) && $is_pending) {
                        $str = $is_loan_payment ? 'Cofidis' : $this->l('your bank');
                        $msg = $this->l('Your payment has been placed on hold for a response from %s. You will be informed as soon as a reply is given.');
                        $message = sprintf($msg, $str);
                        $notice = $this->displayInformation($message);
                    }
                }
            }
        }
        return $notice;
    }

    /**
     * @param Order $order
     * @param array $payment_state_result
     * @return void
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function captureRequest(Order $order, array $payment_state_result)
    {
        $transaction_id = MoneticoPaiementHelper::getTransactionIdByCartId($order->id_cart);
        $transaction = new MoneticoPaiementTransaction($transaction_id);
        $transaction_commande = json_decode($transaction->getOrderContext(), true);

        $ept = MoneticoPaiementHelper::getEptFromEptNumber($transaction_commande['TPE']);

        $capture_request = new Capture();
        $capture_request->setEpt($transaction_commande['TPE']);
        $capture_request->setDate(new \DateTime('now'));
        $capture_request->setOrderDate(\DateTime::createFromFormat("d/m/Y:H:i:s", $transaction_commande['date']));

        $order_id = $order->id;
        $order_amount = Helper::getAmountWithoutCurrency($payment_state_result['montantcommande']);
        $already_recovered_amount = Helper::getAmountWithoutCurrency($payment_state_result['montantrecouvre']);

        // Set amount to capture
        if (isset($payment_state_result['echeancier']["echeance"])) {
            $amount_without_currency = Helper::getAmountWithoutCurrency($payment_state_result['echeancier']["echeance"][0]["montant"]);

        } else if ($payment_state_result['modalitepaiement'] === 'PP') {
            $amount_without_currency = round($order_amount - $already_recovered_amount, 2);

        } else {
            $amount_without_currency = $order_amount;
        }

        $capture_request->setCapturedAmount($amount_without_currency);

        // Set remaining amount
        if ($payment_state_result['modalitepaiement'] === 'PP') {
            $captured_amount = round($order_amount - $already_recovered_amount, 2);
            $remaining_amount = round($order_amount - $already_recovered_amount - $captured_amount, 2);

        } else {
            $transaction_commande_amount_without_currency = Helper::getAmountWithoutCurrency($transaction_commande['montant']);
            $remaining_amount = round($transaction_commande_amount_without_currency - $amount_without_currency, 2);
        }

        $capture_request->setRemainingAmount($remaining_amount);

        $capture_request->setAmount($order_amount);
        $capture_request->setCapturedAlreadyAmount($already_recovered_amount);

        $currency = new \Currency($order->id_currency);
        $capture_request->setCurrency($currency->iso_code);

        $capture_request->setReference($transaction_commande['reference']);
        $capture_request->setLangue($transaction_commande['lgue']);
        $capture_request->setCodeSociete($transaction_commande['societe']);
        $capture_request->setEptMAC($ept['ept_mac']);

        $capture_request_response = $capture_request->check($transaction->isSandboxTransaction());
        $_SESSION['MP_ALERT']['MP_STATUS'] = 1;
        $_SESSION['MP_ALERT']['MP_DESC'] = $capture_request_response;

        if ($capture_request_response['success']) {
            $_SESSION['MP_ALERT']['MP_MESSAGE'] = $this->l('Capture has been successfully executed');

            if ($payment_state_result['etat'] === 'PF') {
                $order_history = new OrderHistory();
                $order_history->id_order = $order->id;
                $order_history->changeIdOrderState(Configuration::get(PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT[1]), $order_id);
                $order_history->save();
            }
        } else {
            $this->createLog($_SESSION['MP_ALERT']['MP_MESSAGE'] . ' : ' . htmlspecialchars($capture_request_response['desc']) . ' ' . htmlspecialchars($capture_request_response['rmk']));
        }
    }

    /**
     * This is needed because otherwise, loading the order details page
     * would do 3 requests to the state API within 1 or 2 seconds.
     */
    private $paymentStateCache = [
        'last_call_timestamp' => 0,
        'data' => null,
    ];

    /**
     * Get the payment state
     *
     * @param $order_id
     *
     * @return array
     */
    public function getPaymentState($order_id)
    {
        if (
            $this->paymentStateCache['data'] !== null &&
            time() - $this->paymentStateCache['last_call_timestamp'] < 5 // last call less than 5 seconds
        ) {
            return $this->paymentStateCache['data'];
        }

        $order = new Order($order_id);

        $payment_state_request = new State();
        $transaction_id = MoneticoPaiementHelper::getTransactionIdByCartId($order->id_cart);
        if (empty($transaction_id)) {
            $message = sprintf($this->l('The Monetico transaction was not found for the order %s.'), $order->id_cart);
            $this->createLog($message, 3);
            return [
                'success' => 0,
                'response_value' => [
                    'desc' => $message
                ]
            ];
        }

        $transaction = new MoneticoPaiementTransaction($transaction_id);
        $transaction_commande = json_decode($transaction->getOrderContext(), true);

        $payment_state_request->setEpt($transaction_commande['TPE']);
        $payment_state_request->setDate(\DateTime::createFromFormat("d/m/Y:H:i:s", $transaction_commande['date']));
        $currency = new \Currency($order->id_currency);

        $payment_state_request->setCurrency($currency->iso_code);
        $amount_without_currency = Helper::getAmountWithoutCurrency($transaction_commande['montant']);
        $payment_state_request->setAmount($amount_without_currency);

        $payment_state_request->setReference($transaction_commande['reference']);
        $payment_state_request->setCodeSociete($transaction_commande['societe']);
        $payment_state_request->setResponseLangue($this->context->language->iso_code);

        $ept = MoneticoPaiementHelper::getEptFromEptNumber($transaction_commande['TPE']);
        if (!$ept) {
            return [
                'success' => 0,
                'response_value' => [
                    'desc' => sprintf($this->l('EPT used for this order was not found. Check if the EPT %s exist.'), $transaction_commande['TPE'])
                ]
            ];
        }

        $payment_state_request->setEptMAC($ept['ept_mac']);

        $data = $payment_state_request->check($transaction->isSandboxTransaction());

        $this->paymentStateCache = [
            'last_call_timestamp' => time(),
            'data' => $data,
        ];

        return $data;
    }

    /**
     * Get payment actions available ( return actions and state )
     *
     * @param $order_id
     *
     * @return array|\false[][]
     */
    public function getPaymentActions($order_id)
    {
        $order_actions = [
            'can_capture' => false,
            'can_cancel' => false,
            'can_refund' => false,
        ];

        $payment_status = $this->getPaymentState($order_id);

        if ($payment_status['success']) {
            $payment_state_result = $payment_status['request_value'];
            switch ($payment_state_result['etat']) {
                case 'PA':
                    $order_actions['can_refund'] = $this->checkIfRefundIsPossible($payment_state_result);
                    break;
                case 'AU':
                    $order_actions['can_capture'] = true;
                    $order_actions['can_cancel'] = true;
                    break;
                case 'PF':
                    $order_actions['can_capture'] = (count($payment_status['request_value']['recouvrements']) === 0);
                    $order_actions['can_cancel'] = true;
                    $order_actions['can_refund'] = $this->checkIfRefundIsPossible($payment_state_result);
                    break;
                case 'PP':
                    $order_actions['can_capture'] = Helper::getAmountWithoutCurrency($payment_status['request_value']['montantrecouvre']) <
                        Helper::getAmountWithoutCurrency($payment_status['request_value']['montantcommande']);
                    $order_actions['can_cancel'] = true;
                    $order_actions['can_refund'] = $this->checkIfRefundIsPossible($payment_state_result);
                    break;
                default:
            }
        }

        return array_merge($payment_status, [
            'actions' => $order_actions
        ]);
    }

    /**
     * Request Themis if necessary (to display them on the module config page))
     *
     * @return void
     * @throws \PrestaShopException
     */
    public function hookActionAdminControllerInitAfter()
    {
        $is_module_config_page = isset($_GET['configure']) && $_GET['configure'] === "MoneticoPaiement";

        if ($is_module_config_page && $this->shouldSendRequest()) {
            $this->sendThemisRequest();
        }
    }

    /**
     * Request Themis if necessary, and display notices in Admin Dashboard
     * @return void
     * @throws PrestaShopDatabaseException
     */
    public function hookDisplayDashboardTop()
    {
        if ($this->shouldSendRequest()) {
            $this->sendThemisRequest();
        }

        // Check if data to display exist in session
        $session_data_exist = !empty($_SESSION['MP_THEMIS_DATA']['general_data']) &&
            !empty($_SESSION['MP_THEMIS_DATA']['current_status']['status']);

        // Check if version status is deprecated or obsolete
        $is_not_active_version = $session_data_exist &&
            $_SESSION['MP_THEMIS_DATA']['current_status']['status'] !== 'active';

        // Check if is dashboard page
        $is_dashboard_page = isset($_GET['controller']) && $_GET['controller'] === 'AdminDashboard';

        if ($session_data_exist && $is_not_active_version && $is_dashboard_page) {

            $status = $_SESSION['MP_THEMIS_DATA']['current_status']['status'];
            $status_desc = $_SESSION['MP_THEMIS_DATA']['current_status']['statusDescription'] . ' ';
            $href = $_SESSION['MP_THEMIS_DATA']['general_data']['dl_module_href'];
            $link_text = $_SESSION['MP_THEMIS_DATA']['general_data']['dl_module_text'];

            if (!empty($_SESSION['MP_THEMIS_DATA']['future_status']['formated_date_next_status'])) {
                $status_desc .= '<em>(' . $_SESSION['MP_THEMIS_DATA']['future_status']['formated_date_next_status'] . ')</em>';
            }

            $status_desc .= sprintf('<br><a href="%s" target="_blank">%s</a>', $href, $link_text);
            $prefix = '<b>' . $this->displayName . '</b><br>';

            if ($status === 'deprecated') {
                echo $this->displayWarning($prefix . $status_desc);
            } else if ($status === 'obsolete') {
                echo $this->displayError($prefix . $status_desc);
            }
        }
    }

    /**
     * Check if a request must be sent to Themis API
     */
    public function shouldSendRequest(): bool
    {
        // Check if general data exist in session
        $lastRequestDate = $_SESSION['MP_THEMIS_DATA']['general_data']['last_request_date'] ?? null;
        $has_general_data = $lastRequestDate !== null;
        // Check if current_status data exist in session
        $has_current_version_data = !empty($_SESSION['MP_THEMIS_DATA']['current_status']['version']);
        // Check if version data in session is different from installed module version
        $other_version_installed = $has_current_version_data &&
            $_SESSION['MP_THEMIS_DATA']['current_status']['version'] !== $this->version;

        // If no info about current version in session or if other version has been installed, unset session data
        if (($has_general_data && !$has_current_version_data) || $other_version_installed) {
            unset($_SESSION['MP_THEMIS_DATA']);
        }

        return empty($_SESSION['MP_THEMIS_DATA']) || $lastRequestDate !== date('Y-m-d');
    }

    /**
     * Call Themis API and store data in session.
     *
     * @return void
     * @throws \PrestaShopDatabaseException
     */
    public function sendThemisRequest()
    {
        $_SESSION['MP_THEMIS_DATA']['general_data'] = [
            'module_name' => $this->displayName,
            'dl_module_href' => PaymentEnvironment::DOWNLOAD_PAGE,
            'dl_module_text' => $this->l('Download the latest version now.'),
            'last_request_date' => date('Y-m-d')
        ];

        // Get all configured EPT
        $epts = MoneticoPaiementHelper::getAllEpt(false);

        if (!empty($epts)) {
            // Select the first configured EPT (to authenticate to Themis API)
            $ept_to_use = $epts[0];

            // Send request to Themis API to get current status of the module version
            $current_status_response = $this->ApiThemisSend($ept_to_use);

            if (!empty($current_status_response)) {
                $arr_response = json_decode($current_status_response, true);

                if (!empty($arr_response)) {
                    $_SESSION['MP_THEMIS_DATA']['current_status'] = $arr_response;
                }
            }

            // Send request to Themis API to get future status of the module version
            $future_status_response = $this->ApiThemisSend($ept_to_use, true);
            if (!empty($future_status_response)) {
                $arr_response = json_decode($future_status_response, true);

                // If version exists in API and has planning, API returns {"statuses":[...]}
                if (!empty($arr_response) && key_exists("statuses", $arr_response) && !empty($arr_response["statuses"][0])) {
                    $formated_date_next_status = date('d/m/Y', strtotime($arr_response["statuses"][0]['applicationDateUtc']));
                    $_SESSION['MP_THEMIS_DATA']['future_status'] = $arr_response["statuses"][0];
                    $_SESSION['MP_THEMIS_DATA']['future_status']['formated_date_next_status'] = $formated_date_next_status;
                }
            }
        }
    }

    /**
     * Manage Monetico Paiement payment modes visibility on the checkout page (FO))
     *
     * @param $params
     *
     * @return void
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function hookActionPresentPaymentOptions($params)
    {
        // Get all payment options
        $paymentOptions = $params['paymentOptions'] ?? [];
        // Get all configured epts
        $epts = MoneticoPaiementHelper::getAllEpt(false);

        if (!empty($epts) && !empty($paymentOptions)) {
            // Select the first one and send request to Themis API to check version status
            $ept_to_use = $epts[0];
            $response = $this->ApiThemisSend($ept_to_use);

            // If API response ok, check if version status is not obsolete
            if (!empty($response)) {
                $api_data = json_decode($response, true);
                $hide_payment_modes = isset($api_data['status']) && $api_data['status'] === "obsolete";
            }

            // If $hide_payment_modes exists and is true
            if (!empty($hide_payment_modes)) {
                foreach ($paymentOptions as $key => $paymentOption) {
                    // Remove MP payment modes
                    if ($key === self::MODULE_NAME) {
                        unset($paymentOptions[$key]);
                    }
                }
                // Set new payment modes list to display on checkout page
                $params['paymentOptions'] = $paymentOptions;
            }
        }
    }

    // --------------------------------------------------
    // Version history

    private const VERSION_HISTORY_CONFIG_KEY = 'MONETICO_VERSION_HISTORY';

    /**
     * @return array<string, string> Keys are dates in "Y-m-d H:i:s", values are versions strings.
     */
    public function getVersionHistory(): array
    {
        $raw = $this->configuration->get(self::VERSION_HISTORY_CONFIG_KEY);
        if ($raw === null) {
            return [];
        }

        $versionHistory = json_decode($raw, true);
        if (!is_array($versionHistory)) {
            $this->configuration->remove(self::VERSION_HISTORY_CONFIG_KEY);
            return [];
        }

        return $versionHistory;
    }

    private function recordNewVersionInstallation(): void
    {
        $versionHistory = $this->getVersionHistory();
        $versionHistory[date('Y-m-d H:i:s')] = $this->version;
        $this->configuration->set(self::VERSION_HISTORY_CONFIG_KEY, json_encode($versionHistory));
    }

    // --------------------------------------------------
    // Setup audit logs.
    // Here we use the term "setup" instead of config because in Prestashop most of the module's "configuration/settings/setup"
    // is stored in dedicated tables instead of the general configuration table like in Wordpress.

    private const SETUP_AUDIT_LOGS_CONFIG_KEY = 'MONETICO_SETUP_AUDIT_LOGS';

    /**
     * @return array<array{date: string, user_id: string, key: string, old_value: string, new_value: string}>
     */
    public function getSetupAuditLogs(): array
    {
        $raw = $this->configuration->get(self::SETUP_AUDIT_LOGS_CONFIG_KEY);
        if ($raw === null) {
            return [];
        }

        $logs = json_decode($raw, true);
        if (!is_array($logs)) {
            $this->configuration->remove(self::SETUP_AUDIT_LOGS_CONFIG_KEY);
            return [];
        }

        return $logs;
    }

    /**
     * @param scalar|array<string, scalar> $oldValue
     * @param scalar|array<string, scalar> $newValue
     * @param bool $trimArraysOfSameValues When $oldValue and $newValues are arrays, remove from them the keys that have the same value, so that we only store the "diff", the keys that changed.
     */
    public function recordSetupAuditLogs(string $key, $oldValue, $newValue, bool $trimArraysOfSameValues = true): void
    {
        $auditLogs = $this->getSetupAuditLogs();

        if ($trimArraysOfSameValues && is_array($oldValue) && is_array($newValue)) {
            $alwaysKeptKeys = ['ept_number', 'code_site_name']; // we do not remove these keys so that we always know which EPT/Codesite is updated
            foreach (array_keys($newValue) as $dataKey) {
                if (in_array($dataKey, $alwaysKeptKeys, true)) {
                    continue;
                }

                if ($newValue[$dataKey] === ($oldValue[$dataKey] ?? null)) {
                    unset($oldValue[$dataKey]);
                    unset($newValue[$dataKey]);
                }
            }
        }

        if (is_array($oldValue) && isset($oldValue['ept_mac'])) {
            $oldValue['ept_mac'] = MoneticoPaiementHelper::displayMacSecurity($oldValue['ept_mac']);
        }
        if (is_array($newValue) && isset($newValue['ept_mac'])) {
            $newValue['ept_mac'] = MoneticoPaiementHelper::displayMacSecurity($newValue['ept_mac']);
        }

        $auditLogs[] = [
            'date' => date('Y-m-d H:i:s'),
            'user_id' => $this->context->employee->id ?? null,
            'key' => $key,
            'old_value' => $oldValue,
            'new_value' => $newValue,
        ];

        $this->configuration->set(self::SETUP_AUDIT_LOGS_CONFIG_KEY, json_encode($auditLogs, JSON_PRETTY_PRINT));
    }
}
