<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com/ for more information.
 *
 * @author    sjmedia GmbH
 * @copyright 2024 sjmedia GmbH
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use OrderWithdrawl\Admin\AdminConfigController;
use OrderWithdrawl\Admin\MailPreviewRenderer;
use OrderWithdrawl\Config\OrderWithdrawlConfig;
use OrderWithdrawl\Mail\MailDispatcher;
use OrderWithdrawl\Order\OrderIdentifier;
use OrderWithdrawl\Repository\OrderRepository;
use OrderWithdrawl\Settings\SettingsService;

$orderWithdrawlAutoload = __DIR__ . '/vendor/autoload.php';
if (is_file($orderWithdrawlAutoload)) {
    require_once $orderWithdrawlAutoload;
}

class OrderWithdrawl extends Module
{
    /** @var OrderRepository|null Lazy-loaded, test-injectable. */
    protected $orderRepository;

    /** @var MailDispatcher|null Lazy-loaded, test-injectable. */
    protected $mailDispatcher;

    /** @var SettingsService|null Lazy-loaded, test-injectable. */
    protected $settingsService;

    public function __construct()
    {
        $this->name = 'orderwithdrawl';
        $this->tab = 'administration';
        $this->version = '1.4.3';
        $this->author = 'sjmedia GmbH';
        $this->need_instance = 0;

        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '9.99.99',
        ];
        $this->module_key = '98ca0a2508c2fbb8fb43e397d6d5062f';

        parent::__construct();

        $this->displayName = $this->l('European Legal Withdrawal Button');
        $this->description = $this->l('Adds a withdrawal button for orders to comply with EU law.');
    }

    /**
     * Current shop id for Multishop-scoped Configuration reads.
     */
    protected function getCurrentShopId()
    {
        $id = isset($this->context->shop->id) ? (int) $this->context->shop->id : 0;

        return $id > 0 ? $id : null;
    }

    /**
     * Lazy-loaded OrderRepository. Protected seam for tests.
     */
    protected function getOrderRepository(): OrderRepository
    {
        if ($this->orderRepository === null) {
            $this->orderRepository = new OrderRepository();
        }

        return $this->orderRepository;
    }

    public function setOrderRepository(OrderRepository $repository)
    {
        $this->orderRepository = $repository;
    }

    /**
     * Lazy-loaded SettingsService. Centralises Configuration reads so tests
     * can swap the implementation and the hook layer passes a shop id
     * without threading it through every call site.
     *
     * PS-M1 / PS-M2: the returned service is seeded with the current context
     * shop id (falls back to global scope when unavailable) so any hook read
     * transparently respects Multishop overrides.
     */
    protected function getSettingsService(): SettingsService
    {
        if ($this->settingsService === null) {
            $this->settingsService = new SettingsService($this->getCurrentShopId());
        } else {
            $this->settingsService->setDefaultShopId($this->getCurrentShopId());
        }

        return $this->settingsService;
    }

    public function setSettingsService(SettingsService $service)
    {
        $this->settingsService = $service;
    }

    /**
     * Lazy-loaded MailDispatcher. Points at the module's own mails directory
     * so every decision/confirmation mail goes through one template path
     * resolver (PS-H5).
     */
    protected function getMailDispatcher(): MailDispatcher
    {
        if ($this->mailDispatcher === null) {
            $this->mailDispatcher = new MailDispatcher(dirname(__FILE__) . '/mails/');
        }

        return $this->mailDispatcher;
    }

    public function setMailDispatcher(MailDispatcher $dispatcher)
    {
        $this->mailDispatcher = $dispatcher;
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // Common hooks for all versions
        $hooks = [
            'displayMyAccountBlock',
            'displayCustomerAccount',
            'displayFooter',
            'displayHeader',
            'displayCMSDisputeInformation',
            'displayOrderWithdrawlCta',
            'actionOrderStatusPostUpdate',
            'moduleRoutes',
        ];

        $hooks[] = 'displayBeforeBodyClosingTag';
        $hooks[] = 'displayFooterBefore';
        $hooks[] = 'displayFooterAfter';

        // PS-M5: `actionGetExtraMailTemplateVars` is registered both here
        // (fresh installs) AND in upgrade/upgrade-1.2.1.php (for shops
        // upgrading from < 1.2.1, where install() does not re-run).
        // `registerHook` is idempotent, so the apparent duplication is safe
        // and intentional - do NOT remove either side.
        $hooks[] = 'actionGetExtraMailTemplateVars';

        foreach ($hooks as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        return Configuration::updateValue('ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS', 14)
            && Configuration::updateValue('ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS', 0)
            && Configuration::updateValue('ORDERWITHDRAWL_PERIOD_START_MODE', 'order_date')
            && Configuration::updateValue('ORDERWITHDRAWL_TRIGGER_STATE_IDS', '')
            && Configuration::updateValue(OrderWithdrawlConfig::ORDER_IDENTIFIER_MODE, OrderWithdrawlConfig::DEFAULT_ORDER_IDENTIFIER_MODE)
            && Configuration::updateValue(OrderWithdrawlConfig::CUSTOMER_VERIFICATION_MODE, OrderWithdrawlConfig::DEFAULT_CUSTOMER_VERIFICATION_MODE)
            && Configuration::updateValue('ORDERWITHDRAWL_BUTTON_CLASSES', '')
            && Configuration::updateValue('ORDERWITHDRAWL_ADMIN_EMAILS', '')
            && Configuration::updateValue(OrderWithdrawlConfig::PHP_MAIL_FALLBACK, OrderWithdrawlConfig::DEFAULT_PHP_MAIL_FALLBACK)
            && Configuration::updateValue('ORDERWITHDRAWL_FOOTER_DISPLAY', 'button')
            && Configuration::updateValue('ORDERWITHDRAWL_DISPLAY_LOCATION', 'footer')
            && Configuration::updateValue('ORDERWITHDRAWL_FOOTER_HOOKS', 'displayFooter')
            && Configuration::updateValue('ORDERWITHDRAWL_BUTTON_LABEL', $this->getDefaultButtonLabels(), true)
            && Configuration::updateValue('ORDERWITHDRAWL_BUTTON_COLOR', '#F44708')
            && Configuration::updateValue('ORDERWITHDRAWL_BUTTON_FONT_COLOR', '#FFFFFF')
            && Configuration::updateValue('ORDERWITHDRAWL_LINK_COLOR', '')
            && Configuration::updateValue('ORDERWITHDRAWL_MODE', OrderWithdrawlConfig::DEFAULT_MODE)
            && Configuration::updateValue(OrderWithdrawlConfig::AUTO_REJECT_EXPIRED, OrderWithdrawlConfig::DEFAULT_AUTO_REJECT_EXPIRED)
            && Configuration::updateValue('ORDERWITHDRAWL_ENABLE_CUSTOMER_NOTE', 0)
            && Configuration::updateValue(OrderWithdrawlConfig::PRODUCT_SELECTION_MODE, OrderWithdrawlConfig::DEFAULT_PRODUCT_SELECTION_MODE)
            && Configuration::updateValue(OrderWithdrawlConfig::EXCLUDED_PRODUCT_IDS, OrderWithdrawlConfig::DEFAULT_EXCLUDED_PRODUCT_IDS)
            && Configuration::updateValue(OrderWithdrawlConfig::EXCLUDED_CATEGORY_IDS, OrderWithdrawlConfig::DEFAULT_EXCLUDED_CATEGORY_IDS)
            && Configuration::updateValue(OrderWithdrawlConfig::EXCLUDE_CUSTOMIZED_PRODUCTS, OrderWithdrawlConfig::DEFAULT_EXCLUDE_CUSTOMIZED_PRODUCTS)
            && Configuration::updateValue('ORDERWITHDRAWL_BUTTON_SIZE', 'medium')
            && Configuration::updateValue('ORDERWITHDRAWL_BUTTON_ALIGN', 'center')
            && Configuration::updateValue('ORDERWITHDRAWL_PRIVACY_PAGE', $this->getDefaultPrivacyPages(), true)
            && Configuration::updateValue('ORDERWITHDRAWL_PRIVACY_NOTE', $this->getDefaultPrivacyNotes(), true)
            && Configuration::updateValue('ORDERWITHDRAWL_WITHDRAWAL_PAGE', $this->getDefaultWithdrawalPages(), true)
            && Configuration::updateValue('ORDERWITHDRAWL_WITHDRAWAL_NOTE', $this->getDefaultWithdrawalNotes(), true)
            && $this->installOrderStates();
    }

    public function installOrderState()
    {
        // No email on status change – the withdrawl_confirm mail is sent directly
        // by the form controller when the customer submits the withdrawal request.
        return $this->installSingleOrderState(
            'ORDERWITHDRAWL_STATE_ID',
            [
                'en' => 'Withdrawal Requested',
                'de' => 'Widerruf beantragt',
            ],
            '#FFA500',
            '',
            false
        );
    }

    public function uninstall()
    {
        return parent::uninstall()
            && $this->uninstallOrderState()
            && Configuration::deleteByName('ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS')
            && Configuration::deleteByName('ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS')
            && Configuration::deleteByName('ORDERWITHDRAWL_PERIOD_START_MODE')
            && Configuration::deleteByName('ORDERWITHDRAWL_TRIGGER_STATE_IDS')
            && Configuration::deleteByName(OrderWithdrawlConfig::ORDER_IDENTIFIER_MODE)
            && Configuration::deleteByName(OrderWithdrawlConfig::CUSTOMER_VERIFICATION_MODE)
            && Configuration::deleteByName('ORDERWITHDRAWL_ADMIN_EMAILS')
            && Configuration::deleteByName(OrderWithdrawlConfig::PHP_MAIL_FALLBACK)
            && Configuration::deleteByName('ORDERWITHDRAWL_STATE_ID')
            && Configuration::deleteByName('ORDERWITHDRAWL_STATE_APPROVED_ID')
            && Configuration::deleteByName('ORDERWITHDRAWL_STATE_REJECTED_ID')
            && Configuration::deleteByName('ORDERWITHDRAWL_FOOTER_DISPLAY')
            && Configuration::deleteByName('ORDERWITHDRAWL_FOOTER_HOOKS')
            && Configuration::deleteByName('ORDERWITHDRAWL_BUTTON_CLASSES')
            && Configuration::deleteByName('ORDERWITHDRAWL_DISPLAY_LOCATION')
            && Configuration::deleteByName('ORDERWITHDRAWL_BUTTON_LABEL')
            && Configuration::deleteByName('ORDERWITHDRAWL_BUTTON_COLOR')
            && Configuration::deleteByName('ORDERWITHDRAWL_BUTTON_FONT_COLOR')
            && Configuration::deleteByName('ORDERWITHDRAWL_LINK_COLOR')
            && Configuration::deleteByName('ORDERWITHDRAWL_MODE')
            && Configuration::deleteByName(OrderWithdrawlConfig::AUTO_REJECT_EXPIRED)
            && Configuration::deleteByName('ORDERWITHDRAWL_ENABLE_CUSTOMER_NOTE')
            && Configuration::deleteByName(OrderWithdrawlConfig::PRODUCT_SELECTION_MODE)
            && Configuration::deleteByName(OrderWithdrawlConfig::EXCLUDED_PRODUCT_IDS)
            && Configuration::deleteByName(OrderWithdrawlConfig::EXCLUDED_CATEGORY_IDS)
            && Configuration::deleteByName(OrderWithdrawlConfig::EXCLUDE_CUSTOMIZED_PRODUCTS)
            && Configuration::deleteByName('ORDERWITHDRAWL_BUTTON_SIZE')
            && Configuration::deleteByName('ORDERWITHDRAWL_BUTTON_ALIGN')
            && Configuration::deleteByName('ORDERWITHDRAWL_PRIVACY_PAGE')
            && Configuration::deleteByName('ORDERWITHDRAWL_PRIVACY_NOTE')
            && Configuration::deleteByName('ORDERWITHDRAWL_WITHDRAWAL_PAGE')
            && Configuration::deleteByName('ORDERWITHDRAWL_WITHDRAWAL_NOTE');
    }

    protected function uninstallOrderState()
    {
        // Remove all order states associated with this module
        $rows = Db::getInstance()->executeS(
            'SELECT id_order_state FROM ' . _DB_PREFIX_ . 'order_state WHERE module_name = \'' . pSQL($this->name) . '\''
        );

        if (!is_array($rows) || !$rows) {
            return true;
        }

        $result = true;
        foreach ($rows as $row) {
            $stateId = (int) $row['id_order_state'];
            if ($stateId <= 0) {
                continue;
            }

            $orderState = new OrderState($stateId);
            if (!Validate::isLoadedObject($orderState)) {
                continue;
            }

            $orderState->deleted = true;
            $deleted = (bool) $orderState->save();
            $result = $deleted && $result;
        }

        return $result;
    }

    /**
     * Public bridge so {@see AdminConfigController} can reach `$this->context`
     * without exposing other protected state. Hooks keep using `$this->context`
     * directly; this getter exists purely for the Admin namespace extraction.
     *
     * @internal Used by {@see AdminConfigController}
     *
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Public bridge for {@see AdminConfigController::render()} so it can ask
     * the module to render its own `display()` call - `display()` is protected
     * on the Module base class, which means only the module itself can invoke
     * it. Keeps the tpl path relative to this file on purpose (PrestaShop uses
     * __FILE__ to compute the tpl search root).
     *
     * @internal Used by {@see AdminConfigController}
     */
    public function displayConfigureTemplate(string $tpl): string
    {
        return $this->display(__FILE__, $tpl);
    }

    public function getContent()
    {
        $controller = new AdminConfigController(
            $this,
            $this->getSettingsService(),
            $this->getMailDispatcher(),
            new MailPreviewRenderer(dirname(__FILE__) . '/mails/')
        );

        return $controller->render();
    }

    public function hookModuleRoutes($params)
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '<')) {
            // PS 1.7 registers broad one-segment routes such as category_rule
            // before module routes. /order-withdrawl can be interpreted as a
            // category URL and return 404, so keep the native module URL there.
            return [];
        }

        return [
            'module-orderwithdrawl-form' => [
                'controller' => 'form',
                'rule' => 'order-withdrawl',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                ],
            ],
            'module-orderwithdrawl-submit' => [
                'controller' => 'submit',
                'rule' => 'order-withdrawl-submit',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => $this->name,
                ],
            ],
        ];
    }

    public function hookDisplayMyAccountBlock($params)
    {
        if (!$this->isDisplayLocationEnabled('account_block')) {
            return '';
        }

        return $this->renderAccountEntry('legacy');
    }

    public function hookDisplayCustomerAccount($params)
    {
        if (!$this->isDisplayLocationEnabled('account_page')) {
            return '';
        }

        return $this->renderAccountEntry('card');
    }

    public function hookDisplayFooter($params)
    {
        return $this->renderFooterHook('displayFooter');
    }

    public function hookDisplayFooterBefore($params)
    {
        return $this->renderFooterHook('displayFooterBefore');
    }

    public function hookDisplayFooterAfter($params)
    {
        return $this->renderFooterHook('displayFooterAfter');
    }

    public function hookDisplayBeforeBodyClosingTag($params)
    {
        return $this->renderFooterHook('displayBeforeBodyClosingTag');
    }

    public function hookDisplayOrderWithdrawlCta($params)
    {
        if (!$this->isDisplayLocationEnabled('custom_hook')) {
            return '';
        }

        return $this->renderCtaLink();
    }

    protected function renderFooterHook($hookName)
    {
        $locations = $this->getDisplayLocationSelections();
        if (!$this->isDisplayLocationEnabled('footer', $locations)) {
            return '';
        }

        if (!$this->isFooterHookEnabled($hookName)) {
            return '';
        }

        return $this->renderCtaLink();
    }

    protected function renderCtaLink()
    {
        // PS-M2: hook reads honour the current-shop context via SettingsService.
        $settings = $this->getSettingsService();
        $link = $this->context->link->getModuleLink($this->name, 'form');
        $displayMode = $settings->getString(
            OrderWithdrawlConfig::FOOTER_DISPLAY,
            null,
            OrderWithdrawlConfig::DEFAULT_FOOTER_DISPLAY
        );
        if (!in_array($displayMode, OrderWithdrawlConfig::ALLOWED_FOOTER_DISPLAYS, true)) {
            $displayMode = OrderWithdrawlConfig::DEFAULT_FOOTER_DISPLAY;
        }
        $label = $this->getButtonLabelByLanguageId((int) $this->context->language->id);
        $buttonColor = $this->getConfiguredButtonColor();
        $buttonFontColor = $this->getConfiguredButtonFontColor();
        $linkColor = $this->getConfiguredLinkColor();
        $buttonClasses = $this->getConfiguredButtonClasses();
        $buttonSize = $settings->getString(
            OrderWithdrawlConfig::BUTTON_SIZE,
            null,
            OrderWithdrawlConfig::DEFAULT_BUTTON_SIZE
        );
        $buttonAlign = $settings->getString(
            OrderWithdrawlConfig::BUTTON_ALIGN,
            null,
            OrderWithdrawlConfig::DEFAULT_BUTTON_ALIGN
        );

        $this->context->smarty->assign([
            'orderwithdrawl_link' => $link,
            'orderwithdrawl_label' => $label,
            'orderwithdrawl_footer_mode' => $displayMode,
            'orderwithdrawl_button_color' => Tools::htmlentitiesUTF8($buttonColor),
            'orderwithdrawl_button_font_color' => Tools::htmlentitiesUTF8($buttonFontColor),
            'orderwithdrawl_link_color' => Tools::htmlentitiesUTF8($linkColor),
            'orderwithdrawl_button_classes' => $buttonClasses,
            'orderwithdrawl_button_size' => $buttonSize,
            'orderwithdrawl_button_align' => $buttonAlign,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/footer_link.tpl');
    }

    public function hookDisplayHeader($params)
    {
        // PS-M2: shop-scoped read via SettingsService.
        $displayMode = $this->getSettingsService()->getString(
            OrderWithdrawlConfig::FOOTER_DISPLAY,
            null,
            OrderWithdrawlConfig::DEFAULT_FOOTER_DISPLAY
        );
        if (!in_array($displayMode, OrderWithdrawlConfig::ALLOWED_FOOTER_DISPLAYS, true)) {
            $displayMode = OrderWithdrawlConfig::DEFAULT_FOOTER_DISPLAY;
        }

        $locations = $this->getDisplayLocationSelections();

        $shouldRegisterStyles = false;
        if (
            $displayMode === 'button'
            && (
                $this->isDisplayLocationEnabled('footer', $locations)
                || $this->isDisplayLocationEnabled('custom_hook', $locations)
            )
        ) {
            $shouldRegisterStyles = true;
        }

        if ($this->isDisplayLocationEnabled('account_page', $locations)) {
            $shouldRegisterStyles = true;
        }

        if ($this->shouldDisplayCmsNotice()) {
            $shouldRegisterStyles = true;
        }

        if ($shouldRegisterStyles
            && isset($this->context->controller)
            && $this->context->controller instanceof FrontController) {
            $this->context->controller->registerStylesheet(
                $this->name . '-front',
                'modules/' . $this->name . '/views/css/front.css',
                [
                    'media' => 'all',
                    'priority' => 150,
                ]
            );
        }
    }

    public function isValidHexColor($color)
    {
        return is_string($color) && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color);
    }

    public function getConfiguredButtonColor()
    {
        $color = Configuration::get('ORDERWITHDRAWL_BUTTON_COLOR');
        if (!$this->isValidHexColor($color)) {
            return '#F44708';
        }

        return Tools::strtoupper($color);
    }

    public function getConfiguredButtonFontColor()
    {
        $color = Configuration::get('ORDERWITHDRAWL_BUTTON_FONT_COLOR');
        if (!$this->isValidHexColor($color)) {
            return '#FFFFFF';
        }

        return Tools::strtoupper($color);
    }

    protected function getConfiguredButtonClasses()
    {
        $classes = (string) Configuration::get('ORDERWITHDRAWL_BUTTON_CLASSES');
        $classes = trim($classes);
        if ($classes === '') {
            return '';
        }

        // Normalize whitespace between class names
        $classes = preg_replace('/\s+/', ' ', $classes);

        return $classes;
    }

    public function getConfiguredLinkColor()
    {
        $color = Configuration::get('ORDERWITHDRAWL_LINK_COLOR');
        if (!$this->isValidHexColor($color)) {
            return '';
        }

        return Tools::strtoupper($color);
    }

    public function getWithdrawalPeriodStartModes()
    {
        return ['order_date', 'shipping_status'];
    }

    public function normalizeWithdrawalPeriodStartMode($mode)
    {
        if (!is_string($mode) || !in_array($mode, $this->getWithdrawalPeriodStartModes(), true)) {
            return 'order_date';
        }

        return $mode;
    }

    public function getWithdrawalPeriodStartMode()
    {
        return $this->normalizeWithdrawalPeriodStartMode(
            (string) Configuration::get('ORDERWITHDRAWL_PERIOD_START_MODE', null, null, null, 'order_date')
        );
    }

    public function normalizeWithdrawalTriggerStateIds(array $stateIds)
    {
        $normalized = [];

        foreach ($stateIds as $stateId) {
            $stateId = (int) $stateId;
            if ($stateId > 0) {
                $normalized[$stateId] = $stateId;
            }
        }

        return array_values($normalized);
    }

    public function getWithdrawalTriggerStateIds()
    {
        $stored = (string) Configuration::get('ORDERWITHDRAWL_TRIGGER_STATE_IDS', null, null, null, '');
        if ($stored === '') {
            return [];
        }

        return $this->normalizeWithdrawalTriggerStateIds(explode(',', $stored));
    }

    protected function getFirstOrderHistoryDateForStates($orderId, array $stateIds)
    {
        $orderId = (int) $orderId;
        $stateIds = $this->normalizeWithdrawalTriggerStateIds($stateIds);

        if ($orderId <= 0 || !$stateIds) {
            return null;
        }

        $date = Db::getInstance()->getValue(
            'SELECT oh.`date_add`
             FROM `' . _DB_PREFIX_ . 'order_history` oh
             WHERE oh.`id_order` = ' . $orderId . '
               AND oh.`id_order_state` IN (' . implode(',', array_map('intval', $stateIds)) . ')
             ORDER BY oh.`date_add` ASC, oh.`id_order_history` ASC'
        );

        if (!$date) {
            return null;
        }

        try {
            return new DateTime($date);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getFirstShippedStatusDate($orderId)
    {
        $orderId = (int) $orderId;
        if ($orderId <= 0) {
            return null;
        }

        $date = Db::getInstance()->getValue(
            'SELECT oh.`date_add`
             FROM `' . _DB_PREFIX_ . 'order_history` oh
             INNER JOIN `' . _DB_PREFIX_ . 'order_state` os
                 ON (os.`id_order_state` = oh.`id_order_state`)
             WHERE oh.`id_order` = ' . $orderId . '
               AND os.`delivery` = 1
             ORDER BY oh.`date_add` ASC, oh.`id_order_history` ASC'
        );

        if (!$date) {
            return null;
        }

        try {
            return new DateTime($date);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getWithdrawalStartStatusDate($orderId)
    {
        $customTriggerDate = $this->getFirstOrderHistoryDateForStates(
            (int) $orderId,
            $this->getWithdrawalTriggerStateIds()
        );

        if ($customTriggerDate instanceof DateTime) {
            return $customTriggerDate;
        }

        return $this->getFirstShippedStatusDate((int) $orderId);
    }

    public function calculateWithdrawalDeadline($orderId, $orderDate, $periodDays = null, $deliveryDays = null)
    {
        if (empty($orderDate)) {
            return null;
        }

        try {
            $orderDateTime = new DateTime($orderDate);
        } catch (Exception $e) {
            return null;
        }

        if ($periodDays === null) {
            $periodDays = (int) Configuration::get(
                'ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS',
                null,
                null,
                null,
                14
            );
        } else {
            $periodDays = (int) $periodDays;
        }

        if ($deliveryDays === null) {
            $deliveryDays = (int) Configuration::get(
                'ORDERWITHDRAWL_WITHDRAWL_DELIVERY_DAYS',
                null,
                null,
                null,
                0
            );
        } else {
            $deliveryDays = (int) $deliveryDays;
        }

        $deadline = clone $orderDateTime;
        $periodStartMode = $this->getWithdrawalPeriodStartMode();

        if ($periodStartMode === 'shipping_status') {
            $shippingDate = $this->getWithdrawalStartStatusDate((int) $orderId);
            if ($shippingDate instanceof DateTime) {
                $deadline = clone $shippingDate;
                $deadline->modify('+' . ($periodDays + $deliveryDays) . ' days');

                return $deadline;
            }
        }

        $deadline->modify('+' . ($periodDays + $deliveryDays) . ' days');

        return $deadline;
    }

    /**
     * Install all order states used by this module.
     *
     * @return bool
     */
    public function installOrderStates()
    {
        return $this->installOrderState()
            && $this->installAdditionalOrderStates();
    }

    /**
     * Install additional order states:
     * - Withdrawal request approved
     * - Withdrawal request rejected
     *
     * @return bool
     */
    protected function installAdditionalOrderStates()
    {
        $approvedInstalled = $this->installSingleOrderState(
            'ORDERWITHDRAWL_STATE_APPROVED_ID',
            [
                'en' => 'Withdrawal request approved',
                'de' => 'Widerrufsanfrage bewilligt',
            ],
            '#28A745',
            'withdrawl_approved',
            false
        );

        $rejectedInstalled = $this->installSingleOrderState(
            'ORDERWITHDRAWL_STATE_REJECTED_ID',
            [
                'en' => 'Withdrawal request rejected',
                'de' => 'Widerrufsanfrage abgelehnt',
            ],
            '#DC3545',
            'withdrawl_rejected',
            false
        );

        return $approvedInstalled && $rejectedInstalled;
    }

    /**
     * Create or update a single order state for this module.
     *
     * @param string $configKey
     * @param string|array $stateLabel String or array ['iso_code' => 'Label']
     * @param string $color
     * @param string $template Mail template name (e.g. 'withdrawl_approved'). PS will find it
     *                         automatically in modules/orderwithdrawl/mails/ via its fallback.
     *                         Pass '' to disable email sending for this state.
     * @param bool $sendEmail Whether PS should send an email on this status change
     *
     * @return bool
     */
    protected function installSingleOrderState($configKey, $stateLabel, $color, $template = '', $sendEmail = false)
    {
        $existingId = (int) Configuration::get($configKey);

        // Prepare labels for all languages
        $labels = [];
        $templates = [];
        foreach (Language::getLanguages() as $language) {
            $iso = $language['iso_code'];
            $text = '';

            if (is_array($stateLabel)) {
                if (isset($stateLabel[$iso])) {
                    $text = $stateLabel[$iso];
                } elseif (isset($stateLabel['en'])) {
                    $text = $stateLabel['en'];
                } else {
                    $text = reset($stateLabel);
                }
            } else {
                $text = $this->l($stateLabel);
            }

            $labels[$language['id_lang']] = Tools::substr(strip_tags($text), 0, 255);
            $templates[$language['id_lang']] = $template;
        }

        // Search by name in default language to avoid duplicates if config is lost but state exists
        $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
        $defaultIso = Language::getIsoById($defaultLangId);
        $searchLabel = '';

        if (is_array($stateLabel)) {
            $searchLabel = isset($stateLabel[$defaultIso]) ? $stateLabel[$defaultIso] : (isset($stateLabel['en']) ? $stateLabel['en'] : reset($stateLabel));
        } else {
            $searchLabel = $stateLabel; // Fallback
        }

        if ($existingId) {
            $orderState = new OrderState($existingId);
            if (Validate::isLoadedObject($orderState)) {
                $orderState->name = $labels;
                $orderState->template = $templates;
                $orderState->send_email = $sendEmail;
                $orderState->color = $color;
                $orderState->hidden = false;
                $orderState->delivery = false;
                $orderState->logable = false;
                $orderState->invoice = false;
                $orderState->unremovable = false;
                $orderState->module_name = pSQL($this->name);

                if ($orderState->update()) {
                    $this->copyOrderStateIcon((int) $orderState->id);

                    return true;
                }
            }
        }

        // Try to locate an existing state by name (default language)
        $orderStates = OrderState::getOrderStates($defaultLangId);
        foreach ($orderStates as $state) {
            if ($state['name'] === $searchLabel) {
                $orderState = new OrderState((int) $state['id_order_state']);
                if (Validate::isLoadedObject($orderState)) {
                    $orderState->name = $labels;
                    $orderState->template = $templates;
                    $orderState->send_email = $sendEmail;
                    $orderState->color = $color;
                    $orderState->module_name = pSQL($this->name);
                    if ($orderState->update()) {
                        Configuration::updateValue($configKey, (int) $orderState->id);
                        $this->copyOrderStateIcon((int) $orderState->id);

                        return true;
                    }
                }
            }
        }

        // Create a brand new order state
        $orderState = new OrderState();
        $orderState->name = $labels;
        $orderState->template = $templates;
        $orderState->send_email = $sendEmail;
        $orderState->color = $color;
        $orderState->hidden = false;
        $orderState->delivery = false;
        $orderState->logable = false;
        $orderState->invoice = false;
        $orderState->unremovable = false;
        $orderState->module_name = pSQL($this->name);

        if ($orderState->add()) {
            Configuration::updateValue($configKey, (int) $orderState->id);
            $this->copyOrderStateIcon((int) $orderState->id);

            return true;
        }

        return false;
    }

    /**
     * Copy the module logo as order state icon.
     *
     * @param int $orderStateId
     */
    protected function copyOrderStateIcon($orderStateId)
    {
        $source = _PS_MODULE_DIR_ . pSQL($this->name) . '/logo.png';
        $destination = _PS_ORDER_STATE_IMG_DIR_ . (int) $orderStateId . '.gif';

        if (file_exists($source)) {
            @copy($source, $destination);
        }
    }

    public function getDefaultDecisionApprovedByIso($isoCode)
    {
        $iso = Tools::strtolower((string) $isoCode);
        $defaults = [
            'de' => 'Ihre Widerrufsanfrage wurde bewilligt. Wir informieren Sie separat über die nächsten Schritte zur Rückabwicklung (z. B. Rücksendung der Ware und Erstattung).',
            'en' => 'Your withdrawal request has been approved. We will inform you separately about the next steps for the refund process (e.g. return of goods and reimbursement).',
        ];

        if (isset($defaults[$iso])) {
            return $defaults[$iso];
        }

        return $defaults['en'];
    }

    public function getDefaultDecisionRejectedByIso($isoCode)
    {
        $iso = Tools::strtolower((string) $isoCode);
        $defaults = [
            'de' => 'Ihre Widerrufsanfrage konnte leider nicht bewilligt werden. Bitte wenden Sie sich bei Fragen zu den Gründen direkt an unseren Kundenservice.',
            'en' => 'Unfortunately, your withdrawal request could not be approved. If you have any questions regarding the reasons, please contact our customer service.',
        ];

        if (isset($defaults[$iso])) {
            return $defaults[$iso];
        }

        return $defaults['en'];
    }

    public function getProcessContinuationNoticeByIso($isoCode)
    {
        $iso = Tools::strtolower((string) $isoCode);
        $defaults = [
            'de' => 'Hinweis: Der Widerruf wurde erfolgreich übermittelt. Die weitere Bearbeitung, Bewertung und Abwicklung erfolgt nach Ihrem internen Geschäftsprozess.',
            'en' => 'Note: The withdrawal was successfully submitted. Further processing, review and handling will follow your internal business process.',
            'es' => 'Nota: El desistimiento se ha enviado correctamente. El procesamiento, la revisión y la gestión posteriores se realizarán según su proceso interno de negocio.',
            'fr' => 'Remarque : la rétractation a été transmise avec succès. Le traitement, l’examen et le suivi ultérieurs se feront selon votre processus interne.',
            'it' => 'Nota: il recesso è stato inviato correttamente. L’ulteriore elaborazione, valutazione e gestione seguirà il vostro processo interno.',
            'nl' => 'Opmerking: de herroeping is succesvol verzonden. De verdere verwerking, beoordeling en afhandeling verloopt volgens uw interne bedrijfsproces.',
            'pl' => 'Uwaga: odstąpienie zostało pomyślnie przesłane. Dalsze przetwarzanie, ocena i obsługa przebiegają zgodnie z wewnętrznym procesem biznesowym.',
            'ro' => 'Notă: retragerea a fost transmisă cu succes. Procesarea, evaluarea și gestionarea ulterioară vor urma procesul dvs. intern de afaceri.',
        ];

        if (isset($defaults[$iso])) {
            return $defaults[$iso];
        }

        return $defaults['en'];
    }

    public function getFooterHookSelections()
    {
        $stored = Configuration::get('ORDERWITHDRAWL_FOOTER_HOOKS', null, null, null, 'displayFooter');
        $selections = $this->collectFooterHookValues($stored);

        if (!$selections) {
            $selections = ['displayFooter'];
        }

        return $selections;
    }

    public function collectFooterHookValues($values)
    {
        if (!is_array($values)) {
            if (!is_string($values) || $values === '') {
                $values = [];
            } else {
                $values = explode(',', $values);
            }
        }

        $allowed = [
            'displayFooter',
            'displayFooterBefore',
            'displayFooterAfter',
            'displayBeforeBodyClosingTag',
        ];

        $hooks = [];
        foreach ($values as $value) {
            $key = trim((string) $value);
            if ($key === '') {
                continue;
            }
            if (in_array($key, $allowed, true)) {
                $hooks[$key] = true;
            }
        }

        return array_keys($hooks);
    }

    public function formatFooterHookStorage(array $hooks)
    {
        $hooks = $this->collectFooterHookValues($hooks);

        return implode(',', $hooks);
    }

    public function isFooterHookEnabled(string $hookName, $selections = null): bool
    {
        if ($selections === null) {
            $selections = $this->getFooterHookSelections();
        }

        return in_array($hookName, $selections, true);
    }

    public function getButtonLabelByLanguageId($idLang)
    {
        $label = Configuration::get('ORDERWITHDRAWL_BUTTON_LABEL', (int) $idLang);
        if ($label === false || $label === '') {
            $iso = Language::getIsoById((int) $idLang);
            $label = $this->getDefaultButtonLabelByIso($iso);
        }

        return $label;
    }

    protected function getDefaultButtonLabels()
    {
        $labels = [];
        foreach (Language::getLanguages(false) as $language) {
            $labels[(int) $language['id_lang']] = $this->getDefaultButtonLabelByIso($language['iso_code']);
        }

        return $labels;
    }

    protected function getDefaultPrivacyPages()
    {
        $pages = [];
        foreach (Language::getLanguages(false) as $language) {
            $pages[(int) $language['id_lang']] = 0;
        }

        return $pages;
    }

    protected function getDefaultWithdrawalPages()
    {
        $pages = [];
        foreach (Language::getLanguages(false) as $language) {
            $pages[(int) $language['id_lang']] = 0;
        }

        return $pages;
    }

    protected function getDefaultPrivacyNotes()
    {
        $notes = [];
        foreach (Language::getLanguages(false) as $language) {
            $notes[(int) $language['id_lang']] = $this->getDefaultPrivacyNoteByIso($language['iso_code']);
        }

        return $notes;
    }

    public function getDefaultPrivacyNote()
    {
        return $this->getDefaultPrivacyNoteByIso('en');
    }

    public function getDefaultPrivacyNoteByIso($isoCode)
    {
        $iso = Tools::strtolower((string) $isoCode);
        $defaults = [
            'de' => 'Ihre im Rahmen des Widerrufs angegebenen Daten werden ausschließlich zur Bearbeitung Ihrer Anfrage verwendet. Weitere Informationen finden Sie in unserer Datenschutzerklärung.',
            'en' => 'The personal data you submit with this withdrawal will be used solely to process your request. For more details, please consult our privacy policy.',
            'es' => 'Los datos personales facilitados con este desistimiento se utilizarán únicamente para tramitar su solicitud. Para más información, consulte nuestra política de privacidad.',
            'fr' => 'Les données personnelles communiquées dans le cadre de ce retrait sont utilisées uniquement pour traiter votre demande. Pour plus d’informations, consultez notre politique de confidentialité.',
            'it' => 'I dati personali forniti con questa richiesta di recesso saranno utilizzati esclusivamente per gestire la richiesta. Per maggiori dettagli, consulti la nostra informativa sulla privacy.',
            'nl' => 'De persoonsgegevens die u via dit herroepingsverzoek verstrekt, worden uitsluitend gebruikt om uw aanvraag te verwerken. Raadpleeg voor meer informatie ons privacybeleid.',
            'pl' => 'Dane osobowe podane w formularzu odstąpienia wykorzystujemy wyłącznie do obsługi Państwa żądania. Więcej informacji znajdą Państwo w naszej polityce prywatności.',
            'ro' => 'Datele personale furnizate în cadrul cererii de retragere sunt folosite exclusiv pentru procesarea solicitării. Pentru detalii suplimentare consultați politica noastră de confidențialitate.',
        ];

        if (isset($defaults[$iso])) {
            return $defaults[$iso];
        }

        return $defaults['en'];
    }

    public function getDefaultButtonLabelByIso($isoCode)
    {
        $iso = Tools::strtolower((string) $isoCode);
        $defaults = [
            'de' => 'Vertrag widerrufen',
            'fr' => 'Demander une rétractation',
            'it' => 'Richiedi revoca',
            'es' => 'Solicitar desistimiento',
            'nl' => 'Herroeping aanvragen',
            'pl' => 'Złóż wniosek o odstąpienie',
            'ro' => 'Solicitare retragere',
            'en' => 'Withdraw from contract',
        ];

        if (isset($defaults[$iso])) {
            return $defaults[$iso];
        }

        return 'Withdraw from contract';
    }

    protected function getDefaultWithdrawalNotes()
    {
        $notes = [];
        foreach (Language::getLanguages(false) as $language) {
            $notes[(int) $language['id_lang']] = $this->getDefaultWithdrawalNoteByIso($language['iso_code']);
        }

        return $notes;
    }

    public function getDefaultWithdrawalNoteByIso($isoCode)
    {
        $iso = Tools::strtolower((string) $isoCode);
        $defaults = [
            'de' => 'Zusätzlich zu den herkömmlichen Möglichkeiten, den Widerruf zu erklären (z. B. per E-Mail, Post oder Fax), steht Ihnen auf dieser Website auch eine elektronische Widerrufsfunktion zur Verfügung. Diese Funktion („Widerrufsbutton") finden Sie im Kundenbereich sowie im Footer dieser Website. Über diese Funktion können Sie Ihren Vertrag online widerrufen. Nach Absenden Ihres Widerrufs erhalten Sie automatisch eine Eingangsbestätigung auf einem dauerhaften Datenträger (z. B. per E-Mail).',
            'en' => 'In addition to the conventional methods of declaring a withdrawal (e.g. by email, post or fax), this website also offers an electronic withdrawal function. This function ("withdrawal button") can be found in the customer area and in the footer of this website. You can use this function to withdraw from your contract online. After submitting your withdrawal, you will automatically receive an acknowledgment of receipt on a durable medium (e.g. by email).',
            'es' => 'Además de los métodos tradicionales para declarar su desistimiento (por ejemplo, por correo electrónico, correo postal o fax), este sitio web también ofrece una función electrónica de desistimiento. Esta función ("botón de desistimiento") se encuentra en el área de clientes y en el pie de página de este sitio web. Puede utilizar esta función para desistir de su contrato en línea. Tras enviar su desistimiento, recibirá automáticamente una confirmación de recepción en un soporte duradero (por ejemplo, por correo electrónico).',
            'fr' => 'En plus des moyens habituels de déclaration de rétractation (par exemple par e-mail, courrier ou fax), ce site web propose également une fonction de rétractation électronique. Cette fonction (« bouton de rétractation ») se trouve dans l\'espace client ainsi qu\'en bas de page de ce site. Vous pouvez utiliser cette fonction pour vous rétracter de votre contrat en ligne. Après l\'envoi de votre rétractation, vous recevrez automatiquement un accusé de réception sur un support durable (par exemple par e-mail).',
            'it' => 'Oltre ai metodi tradizionali per dichiarare il recesso (ad esempio via e-mail, posta o fax), questo sito web offre anche una funzione elettronica di recesso. Questa funzione ("pulsante di recesso") si trova nell\'area clienti e nel footer di questo sito. È possibile utilizzare questa funzione per recedere dal contratto online. Dopo l\'invio del recesso, riceverete automaticamente una conferma di ricezione su un supporto durevole (ad esempio via e-mail).',
            'nl' => 'Naast de gebruikelijke manieren om uw herroeping te verklaren (bijvoorbeeld per e-mail, post of fax), biedt deze website ook een elektronische herroepingsfunctie. Deze functie ("herroepingsknop") vindt u in het klantenportaal en in de voettekst van deze website. Via deze functie kunt u uw contract online herroepen. Na het verzenden van uw herroeping ontvangt u automatisch een ontvangstbevestiging op een duurzame gegevensdrager (bijvoorbeeld per e-mail).',
            'pl' => 'Oprócz tradycyjnych sposobów złożenia oświadczenia o odstąpieniu od umowy (np. pocztą elektroniczną, listownie lub faksem), na tej stronie internetowej dostępna jest również funkcja elektronicznego odstąpienia od umowy. Funkcję tę („przycisk odstąpienia") można znaleźć w strefie klienta oraz w stopce tej strony. Za pomocą tej funkcji można odstąpić od umowy online. Po wysłaniu oświadczenia o odstąpieniu od umowy automatycznie otrzymają Państwo potwierdzenie jego otrzymania na trwałym nośniku (np. pocztą elektroniczną).',
            'ro' => 'Pe lângă metodele tradiționale de a declara retragerea (de exemplu, prin e-mail, poștă sau fax), acest site web oferă și o funcție electronică de retragere. Această funcție („buton de retragere") se găsește în zona de clienți și în subsolul acestui site. Puteți utiliza această funcție pentru a vă retrage din contract online. După trimiterea retragerii, veți primi automat o confirmare de primire pe un suport durabil (de exemplu, prin e-mail).',
        ];

        if (isset($defaults[$iso])) {
            return $defaults[$iso];
        }

        return $defaults['en'];
    }

    public function getDisplayLocationSelections()
    {
        $stored = Configuration::get('ORDERWITHDRAWL_DISPLAY_LOCATION', null, null, null, '');
        $selections = $this->collectDisplayLocationValues($stored);

        if (!$selections) {
            $selections = ['footer'];
        }

        return $selections;
    }

    public function collectDisplayLocationValues($values)
    {
        if (!is_array($values)) {
            if (!is_string($values) || $values === '') {
                $values = [];
            } else {
                $values = explode(',', $values);
            }
        }

        $locations = [];
        foreach ($values as $value) {
            $key = Tools::strtolower(trim((string) $value));
            if ($key === '') {
                continue;
            }

            if ($key === 'both') {
                $locations['footer'] = true;
                $locations['account_block'] = true;
                $locations['account_page'] = true;
                continue;
            }

            if ($key === 'account') {
                $locations['account_block'] = true;
                $locations['account_page'] = true;
                continue;
            }

            if (in_array($key, OrderWithdrawlConfig::ALLOWED_DISPLAY_LOCATIONS, true)) {
                $locations[$key] = true;
            }
        }

        $ordered = ['footer', 'account_block', 'account_page', 'custom_hook'];
        $result = array_keys($locations);
        usort($result, function ($a, $b) use ($ordered) {
            $posA = array_search($a, $ordered, true);
            $posB = array_search($b, $ordered, true);

            if ($posA === false) {
                $posA = PHP_INT_MAX;
            }
            if ($posB === false) {
                $posB = PHP_INT_MAX;
            }

            return $posA <=> $posB;
        });

        return $result;
    }

    public function isDisplayLocationEnabled(string $zone, $selections = null): bool
    {
        if ($selections === null) {
            $selections = $this->getDisplayLocationSelections();
        }

        return in_array($zone, $selections, true);
    }

    protected function shouldDisplayCmsNotice()
    {
        $controller = $this->context->controller;
        if (!$controller instanceof FrontController) {
            return false;
        }

        if ($controller->php_self !== 'cms') {
            return false;
        }

        $idLang = (int) $this->context->language->id;
        $currentCmsId = 0;
        if (isset($controller->cms) && $controller->cms instanceof CMS) {
            $currentCmsId = (int) $controller->cms->id;
        } else {
            $currentCmsId = (int) Tools::getValue('id_cms');
        }

        $privacyPageId = (int) Configuration::get('ORDERWITHDRAWL_PRIVACY_PAGE', $idLang);
        if ($privacyPageId && $privacyPageId === $currentCmsId) {
            return true;
        }

        $withdrawalPageId = (int) Configuration::get('ORDERWITHDRAWL_WITHDRAWAL_PAGE', $idLang);
        if ($withdrawalPageId && $withdrawalPageId === $currentCmsId) {
            return true;
        }

        return false;
    }

    protected function renderAccountEntry($mode)
    {
        $link = $this->context->link->getModuleLink($this->name, 'form');
        $label = $this->getButtonLabelByLanguageId((int) $this->context->language->id);
        $buttonClasses = $this->getConfiguredButtonClasses();

        $this->context->smarty->assign([
            'orderwithdrawl_link' => $link,
            'orderwithdrawl_label' => $label,
            'orderwithdrawl_account_mode' => $mode,
            'orderwithdrawl_button_classes' => $buttonClasses,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/account_link.tpl');
    }

    public function hookDisplayCMSDisputeInformation($params)
    {
        $idLang = (int) $this->context->language->id;
        $currentCmsId = 0;
        if (isset($params['cms']) && $params['cms'] instanceof CMS) {
            $currentCmsId = (int) $params['cms']->id;
        } else {
            $currentCmsId = (int) Tools::getValue('id_cms');
        }

        $blocks = [];
        // PS-M1: SettingsService is already seeded with the current shop id,
        // so Configuration::get transparently returns the multishop override
        // when one exists, otherwise the global default.
        $settings = $this->getSettingsService();

        $privacyPageId = $settings->getInt(OrderWithdrawlConfig::PRIVACY_PAGE, $idLang);
        if ($privacyPageId && $privacyPageId === $currentCmsId) {
            $note = $settings->get(OrderWithdrawlConfig::PRIVACY_NOTE, $idLang);
            if ($note === false || $note === '') {
                $note = $this->getDefaultPrivacyNoteByIso(Language::getIsoById($idLang));
            }
            $note = trim((string) $note);
            if ($note !== '') {
                $blocks[] = [
                    'title' => $this->l('Privacy notice'),
                    'content' => $note,
                ];
            }
        }

        $withdrawalPageId = $settings->getInt(OrderWithdrawlConfig::WITHDRAWAL_PAGE, $idLang);
        if ($withdrawalPageId && $withdrawalPageId === $currentCmsId) {
            $note = $settings->get(OrderWithdrawlConfig::WITHDRAWAL_NOTE, $idLang);
            if ($note === false || $note === '') {
                $note = $this->getDefaultWithdrawalNoteByIso(Language::getIsoById($idLang));
            }
            $note = trim((string) $note);
            if ($note !== '') {
                $blocks[] = [
                    'title' => $this->l('Withdrawal notice'),
                    'content' => $note,
                ];
            }
        }

        if (!$blocks) {
            return '';
        }

        $this->context->smarty->assign([
            'orderwithdrawl_cms_notices' => $blocks,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/privacy_notice.tpl');
    }

    /**
     * React to order status changes for withdrawal decision states.
     *
     * We send approved/rejected emails directly from the module instead of
     * relying on the generic order-state mail resolution. This avoids template
     * lookup issues in some PrestaShop 1.7 setups and keeps the delivery path
     * consistent with the module's own mail templates.
     *
     * @param array $params
     */
    public function hookActionOrderStatusPostUpdate($params)
    {
        $orderId = (int) ($params['id_order'] ?? 0);
        if ($orderId <= 0) {
            return;
        }

        $newOrderStatus = isset($params['newOrderStatus']) ? $params['newOrderStatus'] : null;
        if (!$newOrderStatus instanceof OrderState) {
            return;
        }

        $order = $this->getOrderRepository()->findById($orderId);
        if ($order === null) {
            return;
        }

        $settings = $this->getSettingsService();
        $shopId = (int) $order->id_shop > 0 ? (int) $order->id_shop : null;
        $approvedStateId = $settings->getInt(OrderWithdrawlConfig::STATE_APPROVED_ID, null, 0, $shopId);
        $rejectedStateId = $settings->getInt(OrderWithdrawlConfig::STATE_REJECTED_ID, null, 0, $shopId);

        try {
            if ($approvedStateId > 0 && (int) $newOrderStatus->id === $approvedStateId) {
                $this->sendWithdrawalDecisionEmail($order, 'withdrawl_approved');
            } elseif ($rejectedStateId > 0 && (int) $newOrderStatus->id === $rejectedStateId) {
                $this->sendWithdrawalDecisionEmail($order, 'withdrawl_rejected');
            }
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                'Order withdrawal decision email error: ' . $e->getMessage(),
                PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR,
                null,
                'OrderWithdrawl',
                $orderId
            );
        }
    }

    /**
     * Inject custom template variables into withdrawal decision emails sent via
     * PS standard mechanism (OrderHistory → Mail::Send).
     *
     * PS fires this hook inside Mail::Send() before variable substitution,
     * allowing the module to enrich template vars without replacing the delivery flow.
     * Provides: {order_id}, {order_reference}, {order_identifier}, {date}, {status_message}, {email}.
     *
     * PS-H4: the template whitelist MUST run before any Order/Customer load so
     * the hook stays cheap for unrelated mails (the hook fires on every
     * outgoing mail in the shop).
     *
     * @param array $params Keys: template, template_vars, extra_template_vars (by ref), id_lang
     */
    public function hookActionGetExtraMailTemplateVars($params)
    {
        $template = (string) ($params['template'] ?? '');
        $allowedTemplates = [
            'withdrawl_confirm',
            'withdrawl_admin',
            'withdrawl_approved',
            'withdrawl_rejected',
        ];
        if (!in_array($template, $allowedTemplates, true)) {
            return;
        }

        $orderId = (int) ($params['template_vars']['{id_order}'] ?? 0);
        if ($orderId <= 0) {
            return;
        }

        $order = $this->getOrderRepository()->findById($orderId);
        if ($order === null) {
            return;
        }

        $customer = new Customer((int) $order->id_customer);
        $settings = $this->getSettingsService();
        $shopId = (int) $order->id_shop > 0 ? (int) $order->id_shop : null;
        $idLang = (int) ($params['id_lang'] ?? $settings->getInt(OrderWithdrawlConfig::PS_LANG_DEFAULT, null, 0, $shopId));

        if ($template === 'withdrawl_approved') {
            $statusMessage = $settings->get(OrderWithdrawlConfig::DECISION_APPROVED, $idLang, $shopId);
            if ($statusMessage === false || $statusMessage === '') {
                $statusMessage = $this->getDefaultDecisionApprovedByIso(Language::getIsoById($idLang));
            }
        } elseif ($template === 'withdrawl_rejected') {
            $statusMessage = $settings->get(OrderWithdrawlConfig::DECISION_REJECTED, $idLang, $shopId);
            if ($statusMessage === false || $statusMessage === '') {
                $statusMessage = $this->getDefaultDecisionRejectedByIso(Language::getIsoById($idLang));
            }
        } else {
            $statusMessage = '';
        }

        $orderIdentifierMode = OrderIdentifier::normalizeMode((string) $settings->get(
            OrderWithdrawlConfig::ORDER_IDENTIFIER_MODE,
            null,
            $shopId,
            OrderWithdrawlConfig::DEFAULT_ORDER_IDENTIFIER_MODE
        ));

        $params['extra_template_vars']['{order_id}'] = (int) $order->id;
        $params['extra_template_vars']['{order_reference}'] = $order->reference;
        $params['extra_template_vars']['{order_identifier}'] = OrderIdentifier::format(
            (int) $order->id,
            (string) $order->reference,
            $orderIdentifierMode
        );
        $params['extra_template_vars']['{date}'] = MailDispatcher::formatMailDate();
        $params['extra_template_vars']['{status_message}'] = $statusMessage;
        $params['extra_template_vars']['{email}'] = Validate::isLoadedObject($customer) ? $customer->email : '';
    }

    /**
     * Send a decision email (approved / rejected) to the customer.
     *
     * Delegates template-path resolution and language fallback to the
     * {@see MailDispatcher} so the mail directory is computed in exactly one
     * place in the module (PS-H5) and the "which language if unknown"
     * question has a single source of truth (CQ-MN4).
     *
     * @param Order $order
     * @param string $template
     *
     * @return void
     */
    protected function sendWithdrawalDecisionEmail(Order $order, $template)
    {
        $customer = new Customer((int) $order->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            return;
        }

        $settings = $this->getSettingsService();
        $shopId = (int) $order->id_shop > 0 ? (int) $order->id_shop : null;
        $idLang = (int) $customer->id_lang ?: $settings->getInt(OrderWithdrawlConfig::PS_LANG_DEFAULT, null, 0, $shopId);
        $dispatcher = $this->getMailDispatcher();
        $resolvedLangId = $dispatcher->resolveLanguageId((string) $template, $idLang);

        // Decide which configured message to use; read with the resolved
        // language id so placeholder text matches the template actually used.
        $statusMessage = '';
        if ($template === 'withdrawl_approved') {
            $statusMessage = $settings->get(OrderWithdrawlConfig::DECISION_APPROVED, $resolvedLangId, $shopId);
            if ($statusMessage === false || $statusMessage === '') {
                $statusMessage = $this->getDefaultDecisionApprovedByIso(Language::getIsoById($resolvedLangId));
            }
        } elseif ($template === 'withdrawl_rejected') {
            $statusMessage = $settings->get(OrderWithdrawlConfig::DECISION_REJECTED, $resolvedLangId, $shopId);
            if ($statusMessage === false || $statusMessage === '') {
                $statusMessage = $this->getDefaultDecisionRejectedByIso(Language::getIsoById($resolvedLangId));
            }
        }

        $orderIdentifierMode = OrderIdentifier::normalizeMode((string) $settings->get(
            OrderWithdrawlConfig::ORDER_IDENTIFIER_MODE,
            null,
            $shopId,
            OrderWithdrawlConfig::DEFAULT_ORDER_IDENTIFIER_MODE
        ));

        $templateVars = [
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{order_id}' => (int) $order->id,
            '{order_reference}' => $order->reference,
            '{order_identifier}' => OrderIdentifier::format(
                (int) $order->id,
                (string) $order->reference,
                $orderIdentifierMode
            ),
            '{email}' => $customer->email,
            '{date}' => MailDispatcher::formatMailDate(),
            '{status_message}' => $statusMessage,
        ];

        $dispatcher->send(
            (string) $template,
            $resolvedLangId,
            $templateVars,
            (string) $customer->email,
            $this->getDecisionMailSubjectByTemplate($template, $resolvedLangId),
            trim($customer->firstname . ' ' . $customer->lastname),
            (int) $order->id_shop
        );
    }

    /**
     * Resolve the mail subject for a decision template in the target language.
     *
     * @param string $template
     * @param int $idLang
     *
     * @return string
     */
    protected function getDecisionMailSubjectByTemplate($template, $idLang)
    {
        $iso = Tools::strtolower((string) Language::getIsoById((int) $idLang));

        $subjects = [
            'withdrawl_approved' => [
                'de' => 'Widerrufsanfrage genehmigt',
                'en' => 'Withdrawal request approved',
                'fr' => 'Demande de retractation approuvee',
                'it' => 'Richiesta di recesso approvata',
                'es' => 'Solicitud de desistimiento aprobada',
                'nl' => 'Herroepingsverzoek goedgekeurd',
                'pl' => 'Wniosek o odstapienie zatwierdzony',
                'ro' => 'Cererea de retragere a fost aprobata',
            ],
            'withdrawl_rejected' => [
                'de' => 'Widerrufsanfrage abgelehnt',
                'en' => 'Withdrawal request rejected',
                'fr' => 'Demande de retractation refusee',
                'it' => 'Richiesta di recesso respinta',
                'es' => 'Solicitud de desistimiento rechazada',
                'nl' => 'Herroepingsverzoek afgewezen',
                'pl' => 'Wniosek o odstapienie odrzucony',
                'ro' => 'Cererea de retragere a fost respinsa',
            ],
        ];

        if (isset($subjects[$template][$iso])) {
            return $subjects[$template][$iso];
        }

        if (isset($subjects[$template]['en'])) {
            return $subjects[$template]['en'];
        }

        return '';
    }
}
