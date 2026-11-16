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

use OrderWithdrawl\Config\OrderWithdrawlConfig;
use OrderWithdrawl\Order\OrderIdentifier;

class orderwithdrawlformModuleFrontController extends ModuleFrontController
{
    /**
     * @var bool
     */
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();

        $orderIdentifierMode = $this->getOrderIdentifierMode();
        $customerVerificationMode = $this->getCustomerVerificationMode();
        $formValues = $this->getDefaultFormValues();
        $notifications = $this->getOrderWithdrawlNotifications();

        $this->context->smarty->assign([
            'module_link_submit' => $this->context->link->getModuleLink('orderwithdrawl', 'submit'),
            'withdrawl_period_days' => (int) Configuration::get(
                'ORDERWITHDRAWL_WITHDRAWL_PERIOD_DAYS',
                null,
                null,
                null,
                14
            ),
            'button_label' => $this->getButtonLabelForLanguage((int) $this->context->language->id),
            'orderwithdrawl_enable_customer_note' => (bool) Configuration::get('ORDERWITHDRAWL_ENABLE_CUSTOMER_NOTE', null, null, null, 0),
            'order_identifier_label' => $this->getOrderIdentifierLabel($orderIdentifierMode),
            'order_identifier_placeholder' => $this->getOrderIdentifierPlaceholder($orderIdentifierMode),
            'order_identifier_help' => $this->getOrderIdentifierHelp($orderIdentifierMode),
            'customer_verification_mode' => $customerVerificationMode,
            'show_customer_name_field' => $customerVerificationMode === 'name',
            'form_values' => $formValues,
            'orderwithdrawl_notifications' => $notifications,
            'orderwithdrawl_product_selection_enabled' => (bool) Configuration::get(
                OrderWithdrawlConfig::PRODUCT_SELECTION_MODE,
                null,
                null,
                null,
                OrderWithdrawlConfig::DEFAULT_PRODUCT_SELECTION_MODE
            ),
            'orderwithdrawl_product_selection_ready' => false,
            'orderwithdrawl_order_items' => [],
        ]);

        $this->setTemplate('module:orderwithdrawl/views/templates/front/form.tpl');
    }

    /**
     * @return array{error:list<string>, warning:list<string>, success:list<string>, info:list<string>}
     */
    protected function getOrderWithdrawlNotifications(): array
    {
        $notifications = $this->prepareNotifications();
        $normalised = [
            'error' => [],
            'warning' => [],
            'success' => [],
            'info' => [],
        ];

        foreach (array_keys($normalised) as $type) {
            if (isset($notifications[$type])) {
                $normalised[$type] = $this->normaliseNotificationMessages($notifications[$type]);
            }
        }

        return $normalised;
    }

    /**
     * @param mixed $messages
     *
     * @return list<string>
     */
    protected function normaliseNotificationMessages($messages): array
    {
        if ($messages === null || $messages === false || $messages === '') {
            return [];
        }

        if (!is_array($messages)) {
            $message = trim((string) $messages);

            return $message === '' ? [] : [$message];
        }

        $normalised = [];
        foreach ($messages as $message) {
            if (is_array($message)) {
                foreach ($this->normaliseNotificationMessages($message) as $nestedMessage) {
                    $normalised[] = $nestedMessage;
                }

                continue;
            }

            $message = trim((string) $message);
            if ($message !== '') {
                $normalised[] = $message;
            }
        }

        return $normalised;
    }

    /**
     * @return array<string, string>
     */
    protected function getDefaultFormValues(): array
    {
        $values = [
            'customer_firstname' => '',
            'customer_lastname' => '',
            'customer_name' => '',
            'email' => '',
            'order_reference' => '',
            'customer_note' => '',
        ];

        $customer = isset($this->context->customer) ? $this->context->customer : null;
        if (!$this->isLoadedCustomer($customer)) {
            return $values;
        }

        $firstname = $this->normalisePrefillText((string) $customer->firstname);
        $lastname = $this->normalisePrefillText((string) $customer->lastname);

        $values['customer_firstname'] = $firstname;
        $values['customer_lastname'] = $lastname;
        $values['customer_name'] = trim($firstname . ' ' . $lastname);

        return $values;
    }

    protected function isLoadedCustomer($customer): bool
    {
        if (!is_object($customer)) {
            return false;
        }

        if (class_exists('Validate', false) && Validate::isLoadedObject($customer)) {
            return true;
        }

        return isset($customer->id) && (int) $customer->id > 0;
    }

    protected function normalisePrefillText(string $value): string
    {
        return trim(strip_tags(str_replace(["\r\n", "\r", "\n", "\t"], ' ', $value)));
    }

    protected function getButtonLabelForLanguage(int $idLang): string
    {
        $label = Configuration::get('ORDERWITHDRAWL_BUTTON_LABEL', $idLang);
        if ($label === false || $label === '') {
            $iso = Language::getIsoById($idLang);
            $iso = Tools::strtolower((string) $iso);
            $defaults = $this->getDefaultButtonLabelMap();
            if (isset($defaults[$iso])) {
                return $defaults[$iso];
            }

            return 'Withdraw from contract';
        }

        return (string) $label;
    }

    protected function getOrderIdentifierMode(): string
    {
        return OrderIdentifier::normalizeMode((string) Configuration::get(
            OrderWithdrawlConfig::ORDER_IDENTIFIER_MODE,
            null,
            null,
            null,
            OrderWithdrawlConfig::DEFAULT_ORDER_IDENTIFIER_MODE
        ));
    }

    protected function getCustomerVerificationMode(): string
    {
        $mode = (string) Configuration::get(
            OrderWithdrawlConfig::CUSTOMER_VERIFICATION_MODE,
            null,
            null,
            null,
            OrderWithdrawlConfig::DEFAULT_CUSTOMER_VERIFICATION_MODE
        );

        return in_array($mode, OrderWithdrawlConfig::ALLOWED_CUSTOMER_VERIFICATION_MODES, true)
            ? $mode
            : OrderWithdrawlConfig::DEFAULT_CUSTOMER_VERIFICATION_MODE;
    }

    protected function getOrderIdentifierLabel(string $mode): string
    {
        $mode = OrderIdentifier::normalizeMode($mode);
        if ($mode === 'order_id') {
            return $this->module->l('Order ID', 'form');
        }

        if ($mode === 'reference') {
            return $this->module->l('Order number', 'form');
        }

        return $this->module->l('Order ID or order number', 'form');
    }

    protected function getOrderIdentifierPlaceholder(string $mode): string
    {
        $mode = OrderIdentifier::normalizeMode($mode);
        if ($mode === 'order_id') {
            return $this->module->l('e.g. 12345', 'form');
        }

        if ($mode === 'reference') {
            return $this->module->l('e.g. ABC123456', 'form');
        }

        return $this->module->l('e.g. 12345 or ABC123456', 'form');
    }

    protected function getOrderIdentifierHelp(string $mode): string
    {
        $mode = OrderIdentifier::normalizeMode($mode);
        if ($mode === 'order_id') {
            return $this->module->l('Use the numeric order ID from your order confirmation or customer account.', 'form');
        }

        if ($mode === 'reference') {
            return $this->module->l('Use the order number from your order confirmation email.', 'form');
        }

        return $this->module->l('You will find the order ID or order number in your order confirmation email or customer account.', 'form');
    }

    /**
     * @return array<string, string>
     */
    protected function getDefaultButtonLabelMap(): array
    {
        return [
            'de' => 'Vertrag widerrufen',
            'fr' => 'Demander une rétractation',
            'it' => 'Richiedi revoca',
            'es' => 'Solicitar desistimiento',
            'nl' => 'Herroeping aanvragen',
            'pl' => 'Złóż wniosek o odstąpienie',
            'ro' => 'Solicitare retragere',
            'en' => 'Withdraw from contract',
        ];
    }
}
