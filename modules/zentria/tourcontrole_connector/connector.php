<?php
/**
 *  Copyright 2025 Zentria
 *
 *  @author    Zentria <zentria@profileo.com>
 *  @copyright 2025 Zentria
 *  @license   Commercial
 *  link      https://zentria.profileo.com
 */

// Framework
require_once dirname(__FILE__) . '/../../../config/config.inc.php';
require_once dirname(__FILE__) . '/../../../init.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

// Models
include_once './classes/TC_ObjectModel.php';
include_once './classes/TC_Contact.php';
include_once './classes/TC_Service.php';
include_once './classes/TC_Service_type.php';
include_once './classes/TC_Website_data.php';
include_once './classes/TC_Website_type.php';
include_once './classes/TC_Website.php';
// Utilities
include_once './utilities/TC_Toolkit.php';
include_once './utilities/TC_Toolkit_data_viz.php';
// Encoding
include_once './../classes/Zentria_AesCipher.php';
// Controllers
include_once './controllers/interface/TC_Controller.php';
include_once './controllers/implementation/TC_Controller_getData.php';
include_once './controllers/implementation/TC_Controller_invalidateToken.php';
include_once './controllers/implementation/TC_Controller_updateScore.php';
include_once './controllers/implementation/TC_Controller_subscriptionEnded.php';
include_once './controllers/implementation/TC_Controller_communicationTester.php';
include_once './controllers/implementation/TC_Controller_getData_for_data_viz.php';

/**
 * Entry point for the external calls
 *
 * @author zentria
 */
class TCConnector
{
    /** @var object Module */
    private $module;

    /** @var string Action get */
    private $action;

    /** @var bool token_server */
    private $token_server;

    /** @var bool isTokenInvalidation */
    private $isTokenInvalidation;

    /** @var bool no_encryption */
    private $no_encryption;

    /** @var bool display_errors */
    private $display_errors;

    /** @var bool notoken_check */
    private $notoken_check;

    public function __construct()
    {
        // To load other resources
        $this->module = Module::getInstanceByName('zentria');

        if (!Validate::isLoadedObject($this->module) || !Module::isEnabled($this->module->name)) {
            exit('Module not active');
        }
        $this->action = Tools::getValue('action');
        $this->token_server = Tools::getValue('tokenServer');

        $this->no_encryption = (bool) Tools::getValue('no_encryption') && $this->action == 'communicationTester'; // Only for test controller
        $this->display_errors = (bool) Tools::getValue('display_errors') && $this->action == 'communicationTester'; // Only for test controller
        $this->notoken_check = (bool) Tools::getValue('notoken_check') && $this->action == 'communicationTester'; // Only for test controller

        $this->isTokenInvalidation = $this->action == 'invalidateToken';

        if ($this->display_errors) {
            @ini_set('display_errors', 'on');
            @error_reporting(E_ALL | E_STRICT);

            if (!defined('_PS_DEBUG_SQL_')) {
                define('_PS_DEBUG_SQL_', true);
            }
        } else {
            @ini_set('display_errors', 'off');
        }

        if (version_compare(_PS_VERSION_, '9.0', '<')) {
            // Set employee to context if there aren't as expected to avoid errors
            $context = Context::getContext();
            if (!isset($context->employee->id)) {
                $cookie = new Cookie('psAdmin', '', (int) Configuration::get('PS_COOKIE_LIFETIME_BO'));
                $context->employee = new Employee((int) $cookie->id_employee);
            }
        }

        $this->checkProvenance();
    }

    /**
     * Check caller's IP
     * Check communication token
     */
    protected function checkProvenance()
    {
        $serverVars = [
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
            'HTTP_X_FORWARDED_FOR' => 'n/a',
            'HTTP_X_REAL_IP' => false,
            'HTTP_CF_CONNECTING_IP' => false,
        ];

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $serverVars['HTTP_X_FORWARDED_FOR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            $serverVars['HTTP_X_REAL_IP'] = $_SERVER['HTTP_X_REAL_IP'];
        }

        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $serverVars['HTTP_CF_CONNECTING_IP'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        $allowedIp = gethostbyname(Zentria::getDistantTreatmentUrl(false));

        if (!in_array($allowedIp, $serverVars)) {
            $securityError = 'Security error, expected IP :' . $allowedIp;
            $securityError .= chr(10) . '<br>But we got: ';
            $securityError .= chr(10) . '<pre>';
            $securityError .= chr(10) . print_r($serverVars, true);
            $securityError .= chr(10) . '</pre>';

            // Reset token
            Configuration::updateGlobalValue('ZENTRIA_TOKEN_SERV', 0);
            Configuration::updateGlobalValue('ZENTRIA_TOKEN_REASON', 'Security error - ip matching');

            exit($securityError);
        }

        if (Configuration::getGlobalValue('ZENTRIA_TOKEN_SERV') != $this->token_server && !$this->isTokenInvalidation && !$this->notoken_check) {
            // Reset token
            Configuration::updateGlobalValue('ZENTRIA_TOKEN_SERV', 0);
            Configuration::updateGlobalValue('ZENTRIA_TOKEN_REASON', 'Security error - token match error');

            exit('Token match error');
        }
    }

    /**
     * Return controller content from getContent() function
     *
     * @return string $output
     */
    public function getContentController($encryptionToken)
    {
        $className = sprintf('TC_Controller_%s', $this->action);

        if (class_exists($className)) {
            $controller = new $className();
            $output = $controller->getContent();

            if (!$this->no_encryption) {
                $output = Zentria_AesCipher::encrypt(serialize($output), $encryptionToken);
            }

            exit($output);
        } else {
            exit('Controller not found');
        }
    }
}

// Keep in memory current token, because getData will update the communication token
$encryptionToken = Configuration::getGlobalValue('ZENTRIA_TOKEN_THIRD');
$t = new TCConnector();

return $t->getContentController($encryptionToken);
