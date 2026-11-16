<?php

use MoneticoPaiement\Model\MoneticoPaiementEPT;
use MoneticoPaiement\Model\MoneticoPaiementCodeSite;
use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentMode;

require_once dirname(__FILE__) . '/../../utilities/autoload.utilities.php';

/**
 * @property MoneticoPaiement $module
 */
class AdminMoneticoPaiementDemoController extends \ModuleAdminController
{
    use LegacyLangCompatTrait;

    public $demo_data;
    /**
     * @var string comma-seperated list of the demo EPT numbers.
     */
    private string $demo_ept_numbers;
    private $payment_mode_constants;

    public function __construct()
    {
        parent::__construct();

        $this->setDemoEptNumbers();
        $this->setPmConstants();
    }

    public function init()
    {
        parent::init();
        if (!empty(Tools::getValue('action')) && Tools::getValue('action') === 'del_demo_orders' && !empty(Tools::getValue('ept_id'))) {

            $id = (int)Tools::getValue('ept_id');
            $ept = new MoneticoPaiementEPT($id);
            $params = [
                'configure' => $this->module->name,
                'token' => Tools::getAdminTokenLite('AdminModules')
            ];

            if (!empty($ept)) {
                if ($this->deleteDemoOrders($ept->getEptNumber())) {
                    $params = array_merge($params, [
                        'id' => $id,
                        'delete' . $this->module->name => '',
                        'type' => 'ept',
                    ]);
                    $message = sprintf($this->l('Demo orders created with EPT %s successfully deleted'), $ept->getEptNumber());

                } else {
                    $_SESSION['MP_ALERT']['MP_DEMO_MODE']['danger'][] = $this->l('An error occurred while deleting orders passed in demonstration mode.');
                    $message = sprintf($this->l('Error while deleting demo orders created with EPT %s.'), $ept->getEptNumber());
                }
                $this->createLog($message);
            }

            $link = $this->context->link->getAdminLink('AdminModules', true, $params, $params);
            Tools::redirect($link);
        }
    }

    public function createLog($message, $severity = 1)
    {
        $log_message = "[Monetico] " . $message;
        PrestaShopLogger::addLog($log_message, $severity);
    }

    /***
     * $demo_ept_numbers setter
     * @return void
     */
    public function setDemoEptNumbers(): void
    {
        $ept_demo_data = _PS_MODULE_DIR_ . $this->module->name . '/sdk/Utilities/Assets/demo_ept_data.json';
        $data = file_get_contents($ept_demo_data, true);
        $this->demo_data = json_decode($data, true);

        $demo_ept_numbers = [];
        foreach ($this->demo_data as $payment_data) {
            $demo_ept_numbers[] = $payment_data['ept_number'];
        }
        $this->demo_ept_numbers = implode(',', $demo_ept_numbers);
    }

    /***
     * $demo_ept_numbers getter
     * @return string
     */
    public function getDemoEptNumbers(): string
    {
        return $this->demo_ept_numbers;
    }

    /***
     * $payment_mode_constants setter
     * @return void
     */
    public function setPmConstants()
    {
        $payment_mode_class = new ReflectionClass(PaymentMode::class);
        $this->payment_mode_constants = $payment_mode_class->getConstants();
    }

    /***
     * $payment_mode_constants getter
     * @return array
     */
    public function getPmConstants()
    {
        return $this->payment_mode_constants;
    }

    /***
     * Manages demo mode configuration field
     * @return void
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function displayAjaxManageConfigDemoMode(): void
    {
        $mode_demo_active = (bool)Tools::getValue('bool_val');
        Configuration::updateValue('MP_DEMO_MODE_ACTIVE', (int)Tools::getValue('bool_val'));

        $this->module->recordSetupAuditLogs('demo_mode_enabled', !$mode_demo_active, $mode_demo_active);

        if (!$mode_demo_active) {

            unset($_SESSION['MP_DEMO_MODE']['warning']);
            $_SESSION['MP_DEMO_MODE']['success'][] = $this->l('Demonstration mode is now desactivated.');
            $this->createLog('Demo mode successfully deactivated');

            if (MoneticoPaiementHelper::getDemoOrders($this->getDemoEptNumbers())) {
                if ($this->deleteDemoOrders()) {
                    $_SESSION['MP_DEMO_MODE']['success'][] = $this->l('Orders passed in demonstration mode have been successfully deleted.');
                    $message = $this->l('Orders passed in demo mode successfully deleted via displayAjaxManageConfigDemoMode method');

                } else {
                    $_SESSION['MP_DEMO_MODE']['danger'][] = $this->l('Orders passed in demonstration mode haven\'t been deleted.');
                    $message = $this->l('Error while deleting Orders passed in demo mode via displayAjaxManageConfigDemoMode method');
                }

                $this->createLog($message);
            }

            $count_demo_epts = count(MoneticoPaiementHelper::demoEptCreated($this->getDemoEptNumbers()));

            if ($this->deleteDemoData()) {
                Configuration::updateValue('MP_DEMO_MODE_ORDERS', 0);

                if ($count_demo_epts) {
                    $_SESSION['MP_DEMO_MODE']['success'][] = $this->l('Demonstration data (EPTs and Company codes) have been successfully deleted.');
                }

                $message = $this->l('Demo data successfully deleted via displayAjaxManageConfigDemoMode method');

            } else {
                $_SESSION['MP_DEMO_MODE']['danger'][] = $this->l('Demonstration data (EPTs and Company codes) haven\'t been deleted.');
                $message = $this->l('Error while deleting demo data via displayAjaxManageConfigDemoMode method');
            }

            $this->createLog($message);

        } else {
            $_SESSION['MP_DEMO_MODE']['warning'] = [
                'always_display' => $this->l('Demonstration mode is activated'),
                'on_codesite_form_display' => $this->l('Some fields are disabled.'),
                'on_ept_form_display' => $this->l('Depending on the payment method, only some fields are editable.')
            ];
            $this->createLog('Demo mode successfully activated');
        }

        die(Configuration::get('MP_DEMO_MODE_ACTIVE'));
    }

    /***
     * Manages demo orders configuration field
     * @return void
     */
    public function displayAjaxManageConfigDemoOrders(): void
    {
        $orders_demo_active = (bool)Tools::getValue('bool_val');
        Configuration::updateValue('MP_DEMO_MODE_ORDERS', (int)Tools::getValue('bool_val'));

        if ($orders_demo_active) {
            $_SESSION['MP_DEMO_MODE']['success'][] = $this->l('An order will be created for each Monetico Paiement payment simulation.');
            $message = $this->l('Order creation successfully activated');

        } else {
            $_SESSION['MP_DEMO_MODE']['danger'][] = $this->l('No order will be created for each Monetico Paiement payment simulation.');
            $message = $this->l('Order creation successfully deactivated');
        }

        $this->createLog($message);

        die();
    }

    /***
     * Manages demo mode availability
     * @return void
     * @throws \PrestaShopDatabaseException
     */
    public function displayAjaxDemoModeActivation(): void
    {
        unset($_SESSION['MP_DEMO_MODE']['success']);
        unset($_SESSION['MP_DEMO_MODE']['danger']);

        $can_be_activated = MoneticoPaiementHelper::demoCanBeActivated($this->getDemoEptNumbers());

        if (!$can_be_activated) {
            $help_text = $this->l('To use demonstration mode, no EPT or Company code must be configured.');
        }

        $data = [
            'can_be_activated' => $can_be_activated,
            'help_text' => $help_text ?? null,
        ];

        die(json_encode($data));
    }


    /***
     * Displays Payment Modes selector modal
     * @return void
     * @throws \SmartyException|\PrestaShopDatabaseException
     */
    public function displayAjaxPaymentModeSelector(): void
    {
        $created_demo_ept = MoneticoPaiementHelper::demoEptCreated($this->getDemoEptNumbers());
        $nb_total_demo_ept = count(explode(',', $this->getDemoEptNumbers()));

        $created_ept = [];
        if (!empty($created_demo_ept)) {
            foreach ($created_demo_ept as $ept) {
                $created_ept[] = $ept['payment_mode'];
            }
        }
        $data_payment_mode = [];
        $pm_constants = $this->getPmConstants();
        $defaultLang = $this->context->language->iso_code;

        foreach ($pm_constants as $key => $payment_mode) {
            if ($key != 'DEFAULT_LANGUAGE' && $key != "TOUT_MODE") {
                $data_payment_mode[$key] = $payment_mode[$defaultLang] ?? $payment_mode[PaymentMode::DEFAULT_LANGUAGE];
            }
        }

        $this->context->smarty->assign([
            'modal_title' => $this->l('Demonstration data'),
            'modal_text' => $this->l('Select payment mode(s) you want to activate.'),
            'payment_modes' => $data_payment_mode,
            'modal_close_text' => $this->l('Close'),
            'modal_save_text' => $this->l('Activate'),
            'created_ept' => $created_ept,
            'can_add_other_demo_ept' => count($created_ept) < $nb_total_demo_ept,
            'dropdownlist_size' => $nb_total_demo_ept - count($created_ept),
            'modal_all_ept_created' => $this->l('You have already activated all the payment modes proposed by Monetico Paiement.'),
        ]);

        die($this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/payment_mode_selector_modal.tpl', null, null, null, true));
    }

    public function displayAjaxQuitDemoModal()
    {
        $demo_orders = MoneticoPaiementHelper::getDemoOrders($this->getDemoEptNumbers());
        $demo_epts = MoneticoPaiementHelper::demoEptCreated($this->getDemoEptNumbers());
        $modal_explanations = [
            $this->l('By clicking "Yes", demonstration data (EPTs and Company codes) will be deleted.'),
            $this->l('Orders passed in demonstration mode will be deleted too.'),
            $this->l('You will then be able to configure the module with the data from your Monetico Paiement contract.'),
        ];

        $this->context->smarty->assign([
            'modal_title' => $this->l('Quit demonstration mode'),
            'modal_text' => $this->l('Are you sure you want to quit demonstration mode ?'),
            'modal_explanation' => $modal_explanations,
            'modal_close_text' => $this->l('No'),
            'modal_confirm_text' => $this->l('Yes'),
            'count_demo_orders' => $demo_orders ? count($demo_orders) : 0,
            'count_demo_epts' => count($demo_epts)
        ]);

        die($this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/confirm_quit_demo_modal.tpl', null, null, null, true));
    }

    /***
     * Creates demo data (EPT and codesite) from JSON file in function of user's payment modes selection
     * @return void
     * @throws \PrestaShopException
     */
    public function displayAjaxCreateDemoData(): void
    {
        $selected_choices = Tools::getValue('payment_modes');
        $pm_constants = $this->getPmConstants();

        foreach ($selected_choices as $choice) {
            if (in_array($choice, array_keys($pm_constants)) && !MoneticoPaiementHelper::getEptFromEptNumber($this->demo_data[$choice]['ept_number'])) {
                $ept = new MoneticoPaiementEPT();
                $ept->setEptNumber($this->demo_data[$choice]['ept_number']);
                $ept->setEptMac($this->demo_data[$choice]['ept_mac']);
                $ept->setEptPaymentMode($choice);

                if ($choice === 'INSTALMENT') {
                    $ept->setEptInstalmentDefault(1);
                }

                if ($ept->save()) {
                    $this->createLog($ept->getEptPaymentMode() . ' demo TPE has been successfully created');

                    $defaultLang = $this->context->language->iso_code;
                    $pm_created = $pm_constants[$choice][$defaultLang];

                    $codesite = new MoneticoPaiementCodeSite();
                    $codesite->setCodeSiteEptId($ept->id);
                    $codesite->setCodeSiteName($this->demo_data[$choice]['code_site']);
                    $codesite->setCodeSiteDisplayName($this->l('Pay with Monetico Payment') . ' ' . $pm_created);
                    $codesite->setCodeSitePaymentMethod('cb');
                    $codesite->setCodeSiteLangue(strtoupper($this->context->language->iso_code));
                    $codesite->setCodeSiteActive(1);
                    $codesite->setCodeSiteSandbox(1);
                    $codesite->setCodesiteExpressPayment(false);
                    $codesite->setCodesiteAmountMin(null);
                    $codesite->setCodesiteAmountMax(null);
                    $codesite->set3dsv2(['no_preference']);

                    $options = [];
                    $options['code_site_shops_languages'] = 1;
                    $options['code_site_devises'] = ['EUR', 'USD'];
                    $options['code_site_additional_cards_logos'] = [];
                    $options['code_site_shop_ids'] = [1];
                    $options['code_site_countries'] = [];
                    $options['code_site_cards_logo_opt'] = 1;
                    $options['code_site_capture_event_opt'] = 0;
                    $codesite->setOpts($options);

                    if ($codesite->save()) {
                        $_SESSION['MP_DEMO_MODE']['success'][] = sprintf($this->l('%s mode activated.'), $pm_created);
                        $message = sprintf($this->l('%s demo company code has been successfully created'), $codesite->getCodeSiteName());
                    } else {
                        $message = sprintf($this->l('Error while %s demo company code creation'), $codesite->getCodeSiteName());
                    }

                    $this->createLog($message);

                } else {
                    $this->createLog('Error while ' . $ept->getEptPaymentMode() . ' demo EPT creation');
                }
            }
        }
        die();
    }

    /***
     * Deletes demo EPTs and codesites
     * @return bool
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function deleteDemoData(): bool
    {
        $result = true;
        $demo_ept = MoneticoPaiementHelper::demoEptCreated($this->getDemoEptNumbers());
        if (!empty($demo_ept)) {
            foreach ($demo_ept as $arr_ept) {
                $ept = new MoneticoPaiementEPT($arr_ept['id']);
                if (!($ept instanceof MoneticoPaiementEPT) || !$ept->delete()) {
                    $result &= false;
                    $this->createLog('Error while ' . $arr_ept['id'] . ' demo EPT deletion');
                }
            }
        }
        return $result;
    }

    /***
     * Delete demo orders and associated carts
     * @return int|string|true
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function deleteDemoOrders($ept_number = null)
    {
        $result = true;
        $demo_orders = MoneticoPaiementHelper::getDemoOrders($this->getDemoEptNumbers(), $ept_number);

        if (!empty($demo_orders)) {
            foreach ($demo_orders as $value) {
                $order = new Order($value['id_order']);
                $cart = new Cart($value['id_cart']);

                if (!$order instanceof Order || !$order->delete()) {
                    $result &= false;
                    $this->createLog('Error while order #' . $order->id . ' deletion');

                }
                if (!$cart instanceof Cart || !$cart->delete()) {
                    $result &= false;
                    $this->createLog('Error while cart #' . $cart->id . ' deletion');
                }
            }
        }
        return $result;
    }

}
