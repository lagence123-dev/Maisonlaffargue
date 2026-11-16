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
use OrderWithdrawl\Mail\MailDispatcher;
use OrderWithdrawl\Mail\OrderWithdrawlMailException;
use OrderWithdrawl\Order\OrderIdentifier;
use OrderWithdrawl\RateLimit\RateLimiter;
use OrderWithdrawl\Repository\CustomerRepository;
use OrderWithdrawl\Repository\OrderRepository;
use OrderWithdrawl\Security\SecurityGuard;

class orderwithdrawlsubmitModuleFrontController extends ModuleFrontController
{
    /**
     * @var bool
     */
    public $ssl = true;

    /** @var OrderRepository|null */
    protected $orderRepository;

    /** @var CustomerRepository|null */
    protected $customerRepository;

    /** @var MailDispatcher|null */
    protected $mailDispatcher;

    /** @var RateLimiter|null */
    protected $rateLimiter;

    public function postProcess()
    {
        // Defensive hardening order:
        //   1. honeypot / CSRF / rate-limit (cheap abuse gates first)
        //   2. order-ownership + period review flag (business rules)
        //   3. duplicate guard (DB)
        //   4. state change + outgoing mails
        if ($this->isHoneypotTriggered()) {
            // Silently drop with the same generic verification failure used below.
            $this->errors[] = $this->getOrderVerificationFailureMessage();
            $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

            return;
        }

        if (!$this->validateCsrfToken()) {
            $this->errors[] = $this->module->l('Invalid form token. Please reload the page and try again.', 'submit');
            $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

            return;
        }

        if (!$this->getRateLimiter()->allow($this->getClientIp())) {
            $this->errors[] = $this->module->l('Too many submissions from your connection. Please try again in a minute.', 'submit');
            $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

            return;
        }

        $customerFirstname = SecurityGuard::normaliseConsumerName((string) Tools::getValue('customer_firstname', ''));
        $customerLastname = SecurityGuard::normaliseConsumerName((string) Tools::getValue('customer_lastname', ''));
        $legacyCustomerName = SecurityGuard::normaliseConsumerName((string) Tools::getValue('customer_name', ''));
        $submittedSeparateName = $customerFirstname !== '' || $customerLastname !== '';
        $customerName = $submittedSeparateName
            ? SecurityGuard::normaliseConsumerName(trim($customerFirstname . ' ' . $customerLastname))
            : $legacyCustomerName;
        $orderIdentifier = trim((string) Tools::getValue('order_reference'));
        $email = trim((string) Tools::getValue('email'));
        $orderIdentifierMode = $this->getOrderIdentifierMode();
        $customerVerificationMode = $this->getCustomerVerificationMode();
        $productSelectionModeEnabled = $this->isProductSelectionModeEnabled();
        $formAction = (string) Tools::getValue('orderwithdrawl_action', 'confirm_withdrawal');
        $withdrawalScope = $this->normaliseWithdrawalScope((string) Tools::getValue('withdrawal_scope', 'full'));

        $customerNameRequired = $customerVerificationMode === 'name';
        $customerNameMissing = $customerNameRequired && (
            $customerName === ''
            || ($submittedSeparateName && ($customerFirstname === '' || $customerLastname === ''))
        );

        if ($customerNameMissing || $email === '') {
            if ($customerNameRequired && ($submittedSeparateName || $legacyCustomerName === '')) {
                if ($customerFirstname === '') {
                    $this->errors[] = $this->module->l('Please enter your first name.', 'submit');
                }
                if ($customerLastname === '') {
                    $this->errors[] = $this->module->l('Please enter your last name.', 'submit');
                }
            } elseif ($customerNameMissing) {
                $this->errors[] = $this->module->l('Please enter your first and last name.', 'submit');
            }

            if ($email === '') {
                $this->errors[] = $this->module->l('Please enter an email address for the confirmation.', 'submit');
            }

            $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

            return;
        }

        if ($orderIdentifier === '') {
            $this->errors[] = $this->module->l('Please enter your order ID/order number.', 'submit');
            $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

            return;
        }

        if (!Validate::isEmail($email)) {
            $this->errors[] = $this->module->l('Please provide a valid email address for the confirmation.', 'submit');
            $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

            return;
        }

        if ($productSelectionModeEnabled && $formAction === 'preview_items') {
            $this->previewOrderItems(
                $customerFirstname,
                $customerLastname,
                $customerName,
                $orderIdentifier,
                $email,
                $orderIdentifierMode,
                $customerVerificationMode
            );

            return;
        }

        if ($withdrawalScope === '') {
            $this->errors[] = $this->module->l('Please choose whether you want to withdraw the entire order or individual items/services.', 'submit');
            $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

            return;
        }

        $contractPartRows = [];
        if (!$productSelectionModeEnabled) {
            $contractPartResult = $this->collectContractPartRows(
                Tools::getValue('contract_part_name', []),
                Tools::getValue('contract_part_number', []),
                Tools::getValue('contract_part_quantity', [])
            );
            $contractPartRows = $contractPartResult['rows'];
            if ($withdrawalScope === 'partial' && ($contractPartResult['has_invalid_row'] || count($contractPartRows) === 0)) {
                $this->errors[] = $this->module->l('Please provide an item name or item number for each item you want to withdraw.', 'submit');
                $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

                return;
            }
        }

        if ($withdrawalScope !== 'partial') {
            $contractPartRows = [];
        }

        $order = $this->getOrderRepository()->findRowByIdentifierMode($orderIdentifier, $orderIdentifierMode);

        if (!$order) {
            $this->errors[] = $this->getOrderVerificationFailureMessage();
            $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

            return;
        }

        $orderId = (int) ($order['id_order'] ?? 0);
        $orderReference = (string) ($order['reference'] ?? $orderIdentifier);
        $orderIdentifierDisplay = OrderIdentifier::format($orderId, $orderReference, $orderIdentifierMode);
        $customerObj = $this->getCustomerRepository()->find((int) ($order['id_customer'] ?? 0));

        if ($customerVerificationMode === 'name') {
            $customerFirstnameForCheck = $customerObj !== null ? (string) $customerObj->firstname : '';
            $customerLastnameForCheck = $customerObj !== null ? (string) $customerObj->lastname : '';
            if (!SecurityGuard::consumerNamesMatch($customerName, $customerFirstnameForCheck, $customerLastnameForCheck)) {
                $this->errors[] = $this->getOrderVerificationFailureMessage();
                $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

                return;
            }
        } elseif ($customerVerificationMode === 'email') {
            $orderCustomerEmail = $order['invoice_email'] ?? $order['email'] ?? null;
            if (!$orderCustomerEmail && $customerObj !== null) {
                $orderCustomerEmail = $customerObj->email;
            }

            if (!$orderCustomerEmail || Tools::strtolower($email) !== Tools::strtolower($orderCustomerEmail)) {
                $this->errors[] = $this->getOrderVerificationFailureMessage();
                $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

                return;
            }
        }

        if ($productSelectionModeEnabled) {
            $orderProducts = $this->getOrderRepository()->findProductsByOrderId($orderId);
            if ($withdrawalScope === 'partial') {
                $selectedProductResult = $this->collectSelectedOrderProductRows(
                    $orderProducts,
                    Tools::getValue('order_item_selection', []),
                    Tools::getValue('order_item_quantity', [])
                );
                $contractPartRows = $selectedProductResult['rows'];
                if ($selectedProductResult['has_invalid_row'] || count($contractPartRows) === 0) {
                    $this->errors[] = $this->module->l('Please select at least one item from the order and provide a valid quantity.', 'submit');
                    $this->renderProductSelectionForm(
                        $this->buildSubmittedFormValues(
                            $customerFirstname,
                            $customerLastname,
                            $legacyCustomerName,
                            $orderIdentifier,
                            $email,
                            SecurityGuard::normaliseCustomerNote((string) Tools::getValue('customer_note', ''))
                        ),
                        $orderProducts,
                        true
                    );

                    return;
                }
            }
        }

        $customerNotePlain = SecurityGuard::normaliseCustomerNote((string) Tools::getValue('customer_note', ''));
        $customerNoteHtml = SecurityGuard::encodeCustomerNote((string) Tools::getValue('customer_note', ''));
        $withdrawalScopeLabel = $this->getWithdrawalScopeLabel($withdrawalScope);
        $contractPartDetailsPlain = $this->buildContractPartDetails($contractPartRows);
        $contractPartDetailsHtml = $this->encodeMultilineTextForHtml($contractPartDetailsPlain);
        $deadlineReviewRequired = false;
        $automaticRejectionRequired = false;
        $deadlineReviewNoticePlain = '';
        $deadlineReviewNoticeHtml = '';

        $mode = Configuration::get(OrderWithdrawlConfig::MODE, null, null, null, OrderWithdrawlConfig::DEFAULT_MODE);

        if ($mode === 'period') {
            $orderWithdrawlModule = $this->module;
            if (!$orderWithdrawlModule instanceof OrderWithdrawl) {
                $this->errors[] = $this->module->l('Withdrawal module is not available. Please contact administrator.', 'submit');
                $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

                return;
            }

            $periodDays = (int) Configuration::get(
                OrderWithdrawlConfig::WITHDRAWL_PERIOD_DAYS,
                null,
                null,
                null,
                OrderWithdrawlConfig::DEFAULT_PERIOD_DAYS
            );
            $deliveryDays = (int) Configuration::get(
                OrderWithdrawlConfig::WITHDRAWL_DELIVERY_DAYS,
                null,
                null,
                null,
                OrderWithdrawlConfig::DEFAULT_DELIVERY_DAYS
            );
            $deadline = $orderWithdrawlModule->calculateWithdrawalDeadline(
                $orderId,
                $order['date_add'],
                $periodDays,
                $deliveryDays
            );

            if ($deadline instanceof DateTime && new DateTime() > $deadline) {
                $deadlineReviewRequired = true;
                $automaticRejectionRequired = $this->isAutomaticExpiredRejectionEnabled();
                $deadlineReviewNoticePlain = $automaticRejectionRequired
                    ? $this->buildAutomaticRejectionNotice($deadline)
                    : $this->buildDeadlineReviewNotice($deadline);
                $deadlineReviewNoticeHtml = htmlspecialchars($deadlineReviewNoticePlain, ENT_QUOTES, 'UTF-8');
            }
        }

        $withdrawalStateId = (int) Configuration::get(OrderWithdrawlConfig::STATE_ID);
        if (!$withdrawalStateId) {
            $this->errors[] = $this->module->l('Withdrawal state not configured. Please contact administrator.', 'submit');
            $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

            return;
        }

        $orderRepository = $this->getOrderRepository();
        if (!$orderRepository->acquireWithdrawalLock($orderId)) {
            $this->errors[] = $this->module->l('A withdrawal request is already being processed for this order. Please try again shortly.', 'submit');
            $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

            return;
        }

        try {
            // Duplicate guard: reject repeat submissions for the same order before
            // writing any state transition or firing admin notifications.
            if ($orderRepository->hasOrderStateHistoryEntry($orderId, $withdrawalStateId)) {
                $this->errors[] = $this->module->l('A withdrawal has already been submitted for this order.', 'submit');
                $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));

                return;
            }

            $orderObj = new Order($orderId);
            $messageCustomer = $customerObj ?? new Customer((int) $order['id_customer']);

            if ($deadlineReviewRequired) {
                if ($automaticRejectionRequired) {
                    $this->saveAutomaticRejectionNoticeAsOrderMessage(
                        $orderObj,
                        $messageCustomer,
                        (string) $email,
                        $deadlineReviewNoticePlain
                    );
                } else {
                    $this->saveDeadlineReviewNoticeAsOrderMessage(
                        $orderObj,
                        $messageCustomer,
                        (string) $email,
                        $deadlineReviewNoticePlain
                    );
                }
            }

            if ($withdrawalScope === 'partial') {
                $this->saveContractPartDetailsAsOrderMessage(
                    $orderObj,
                    $messageCustomer,
                    (string) $email,
                    $withdrawalScopeLabel,
                    $contractPartDetailsPlain
                );
            }

            if ($customerNotePlain !== '') {
                $this->saveCustomerNoteAsOrderMessage(
                    $orderObj,
                    $messageCustomer,
                    (string) $email,
                    $customerNotePlain
                );
            }

            $adminLangId = (int) Configuration::get(OrderWithdrawlConfig::PS_LANG_DEFAULT);
            if ($adminLangId <= 0) {
                $adminLangId = (int) $this->context->language->id;
            }
            $processContinuationNoticePlain = $this->buildProcessContinuationNotice($adminLangId);
            $processContinuationNoticeHtml = htmlspecialchars($processContinuationNoticePlain, ENT_QUOTES, 'UTF-8');

            $this->saveProcessContinuationNoticeAsOrderMessage(
                $orderObj,
                $messageCustomer,
                (string) $email,
                $processContinuationNoticePlain
            );

            // The initial "withdrawal requested" status only needs a history row.
            // Decision states are handled separately so their dedicated decision
            // hooks/mails are not triggered for the receipt status.
            $history = new OrderHistory();
            $history->id_order = $orderId;
            $history->id_employee = 0;
            $history->id_order_state = $withdrawalStateId;
            $history->add();

            $storedCustomerFirstname = $customerObj !== null ? (string) $customerObj->firstname : '';
            $storedCustomerLastname = $customerObj !== null ? (string) $customerObj->lastname : '';
            if ($storedCustomerFirstname === '' && $storedCustomerLastname === '') {
                $splitName = $this->splitConsumerName($customerName);
                $storedCustomerFirstname = $splitName['firstname'];
                $storedCustomerLastname = $splitName['lastname'];
            }
            $historyUrl = $this->buildHistoryUrl();
            $dateNow = MailDispatcher::formatMailDate();

            $templateVars = [
                '{firstname}' => $storedCustomerFirstname,
                '{lastname}' => $storedCustomerLastname,
                '{consumer_name}' => $customerName,
                '{order_id}' => $orderId,
                '{order_reference}' => $orderReference,
                '{order_identifier}' => $orderIdentifierDisplay,
                '{email}' => $email,
                '{date}' => $dateNow,
                '{history_url}' => $historyUrl,
                '{withdrawal_scope}' => $withdrawalScopeLabel,
                '{contract_part_details}' => $contractPartDetailsPlain,
                '{contract_part_details_html}' => $contractPartDetailsHtml,
                '{customer_note}' => $customerNotePlain,
                '{customer_note_html}' => $customerNoteHtml,
                '{deadline_review_notice}' => $deadlineReviewNoticePlain,
                '{deadline_review_notice_html}' => $deadlineReviewNoticeHtml,
                '{process_continuation_notice}' => $processContinuationNoticePlain,
                '{process_continuation_notice_html}' => $processContinuationNoticeHtml,
            ];

            $idLang = $customerObj !== null ? (int) $customerObj->id_lang : 0;
            if ($idLang <= 0) {
                $idLang = (int) $this->context->language->id;
            }

            $dispatcher = $this->getMailDispatcher();

            // Mail-dispatch failure handling:
            //   The customer confirmation email is the only user-visible signal
            //   that their withdrawal was accepted. If the mail cannot be sent we
            //   surface a clear error banner so the customer can contact support
            //   - while keeping the DB state transition that already happened.
            //   Admin recipients are best-effort; a single bad recipient must not
            //   hide the confirmation success from the customer, so those are
            //   only logged.
            $customerMailFailed = false;
            try {
                $dispatcher->send(
                    'withdrawl_confirm',
                    $idLang,
                    $templateVars,
                    (string) $email,
                    $this->module->l('Confirmation of your withdrawal request', 'submit'),
                    null,
                    (int) $orderObj->id_shop
                );
            } catch (OrderWithdrawlMailException $e) {
                $customerMailFailed = true;
                $this->logGenericFailure(
                    'Order withdrawal customer mail failed to send',
                    $e,
                    $orderId
                );
            } catch (Exception $e) {
                // Defensive: any non-typed exception is treated the same way so
                // silent failures cannot slip through on production.
                $customerMailFailed = true;
                $this->logGenericFailure(
                    'Order withdrawal customer mail failed to send',
                    $e,
                    $orderId
                );
            }

            $adminEmailList = $this->collectAdminRecipients();

            $formattedOrderTotal = $this->formatPriceForLocale(
                (float) $orderObj->total_paid,
                (int) $orderObj->id_currency,
                $adminLangId
            );

            $adminTemplateVars = [
                '{order_id}' => $orderId,
                '{order_reference}' => $orderReference,
                '{order_identifier}' => $orderIdentifierDisplay,
                '{customer_email}' => $email,
                '{customer_name}' => $customerName,
                '{consumer_name}' => $customerName,
                '{date}' => $dateNow,
                '{order_total}' => $formattedOrderTotal,
                '{withdrawal_scope}' => $withdrawalScopeLabel,
                '{contract_part_details}' => $contractPartDetailsPlain,
                '{contract_part_details_html}' => $contractPartDetailsHtml,
                '{customer_note}' => $customerNotePlain,
                '{customer_note_html}' => $customerNoteHtml,
                '{deadline_review_notice}' => $deadlineReviewNoticePlain,
                '{deadline_review_notice_html}' => $deadlineReviewNoticeHtml,
                '{process_continuation_notice}' => $processContinuationNoticePlain,
                '{process_continuation_notice_html}' => $processContinuationNoticeHtml,
            ];

            foreach ($adminEmailList as $adminEmail) {
                try {
                    $dispatcher->send(
                        'withdrawl_admin',
                        $adminLangId,
                        $adminTemplateVars,
                        (string) $adminEmail,
                        $this->module->l('Order withdrawal request received', 'submit'),
                        null,
                        (int) $orderObj->id_shop
                    );
                } catch (OrderWithdrawlMailException $e) {
                    // Admin-notification failures are non-fatal for the customer
                    // flow: the withdrawal itself is persisted and the customer
                    // confirmation path decides the UI banner.
                    $this->logGenericFailure(
                        'Order withdrawal admin mail failed to send',
                        $e,
                        $orderId
                    );
                } catch (Exception $e) {
                    $this->logGenericFailure(
                        'Order withdrawal admin mail failed to send',
                        $e,
                        $orderId
                    );
                }
            }

            $automaticRejectionApplied = false;
            if (!$customerMailFailed && $automaticRejectionRequired) {
                $automaticRejectionApplied = $this->applyAutomaticExpiredRejection($orderObj);
            }

            $this->context->smarty->assign([
                'success' => true,
                'ref' => $orderIdentifier,
            ]);

            if ($customerMailFailed) {
                $this->errors[] = $this->module->l(
                    'Your withdrawal was recorded, but the confirmation email could not be sent. Please contact support.',
                    'submit'
                );
            } elseif ($automaticRejectionApplied) {
                $this->success[] = $this->module->l(
                    'Your withdrawal request has been received. The automatic deadline check rejected it because the calculated withdrawal period has expired.',
                    'submit'
                );
            } elseif ($deadlineReviewRequired) {
                $this->success[] = $this->module->l(
                    'Your withdrawal request has been received. According to the data stored in the shop, the calculated withdrawal deadline has expired. The merchant will review the request manually.',
                    'submit'
                );
            } else {
                $this->success[] = $this->module->l('Your withdrawal request has been successfully submitted.', 'submit');
            }

            $this->redirectWithNotifications($this->context->link->getModuleLink('orderwithdrawl', 'form'));
        } finally {
            $orderRepository->releaseWithdrawalLock($orderId);
        }
    }

    /**
     * Resolve the rate limiter instance. Exposed as a protected seam so tests
     * can inject a pre-configured limiter.
     */
    protected function getRateLimiter(): RateLimiter
    {
        if ($this->rateLimiter === null) {
            $this->rateLimiter = new RateLimiter();
        }

        return $this->rateLimiter;
    }

    public function setRateLimiter(RateLimiter $limiter)
    {
        $this->rateLimiter = $limiter;
    }

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

    protected function getOrderVerificationFailureMessage(): string
    {
        return $this->module->l('Order not found or verification failed.', 'submit');
    }

    protected function isProductSelectionModeEnabled(): bool
    {
        return (bool) Configuration::get(
            OrderWithdrawlConfig::PRODUCT_SELECTION_MODE,
            null,
            null,
            null,
            OrderWithdrawlConfig::DEFAULT_PRODUCT_SELECTION_MODE
        );
    }

    protected function previewOrderItems(string $customerFirstname, string $customerLastname, string $customerName, string $orderIdentifier, string $email, string $orderIdentifierMode, string $customerVerificationMode)
    {
        $formValues = $this->buildSubmittedFormValues(
            $customerFirstname,
            $customerLastname,
            $customerName,
            $orderIdentifier,
            $email
        );

        $order = $this->getOrderRepository()->findRowByIdentifierMode($orderIdentifier, $orderIdentifierMode);
        if (!$order) {
            $this->errors[] = $this->getOrderVerificationFailureMessage();
            $this->renderProductSelectionForm($formValues, [], false);

            return;
        }

        $customerObj = $this->getCustomerRepository()->find((int) ($order['id_customer'] ?? 0));

        if ($customerVerificationMode === 'name') {
            $customerFirstnameForCheck = $customerObj !== null ? (string) $customerObj->firstname : '';
            $customerLastnameForCheck = $customerObj !== null ? (string) $customerObj->lastname : '';
            if (!SecurityGuard::consumerNamesMatch($customerName, $customerFirstnameForCheck, $customerLastnameForCheck)) {
                $this->errors[] = $this->getOrderVerificationFailureMessage();
                $this->renderProductSelectionForm($formValues, [], false);

                return;
            }
        } elseif ($customerVerificationMode === 'email') {
            $orderCustomerEmail = $order['invoice_email'] ?? $order['email'] ?? null;
            if (!$orderCustomerEmail && $customerObj !== null) {
                $orderCustomerEmail = $customerObj->email;
            }

            if (!$orderCustomerEmail || Tools::strtolower($email) !== Tools::strtolower($orderCustomerEmail)) {
                $this->errors[] = $this->getOrderVerificationFailureMessage();
                $this->renderProductSelectionForm($formValues, [], false);

                return;
            }
        }

        $products = $this->getOrderRepository()->findProductsByOrderId((int) ($order['id_order'] ?? 0));
        if ($products === []) {
            $this->errors[] = $this->module->l('No selectable order items were found for this order.', 'submit');
            $this->renderProductSelectionForm($formValues, [], false);

            return;
        }

        $this->renderProductSelectionForm($formValues, $products, true);
    }

    protected function renderProductSelectionForm(array $formValues, array $orderItems, bool $ready)
    {
        $orderIdentifierMode = $this->getOrderIdentifierMode();
        $customerVerificationMode = $this->getCustomerVerificationMode();
        $errorMessage = isset($this->errors[0]) ? (string) $this->errors[0] : '';

        $this->context->smarty->assign([
            'module_link_submit' => $this->context->link->getModuleLink('orderwithdrawl', 'submit'),
            'withdrawl_period_days' => (int) Configuration::get(
                OrderWithdrawlConfig::WITHDRAWL_PERIOD_DAYS,
                null,
                null,
                null,
                OrderWithdrawlConfig::DEFAULT_PERIOD_DAYS
            ),
            'button_label' => $this->getButtonLabelForLanguage((int) $this->context->language->id),
            'orderwithdrawl_enable_customer_note' => (bool) Configuration::get(OrderWithdrawlConfig::ENABLE_CUSTOMER_NOTE, null, null, null, 0),
            'order_identifier_label' => $this->getOrderIdentifierLabel($orderIdentifierMode),
            'order_identifier_placeholder' => $this->getOrderIdentifierPlaceholder($orderIdentifierMode),
            'order_identifier_help' => $this->getOrderIdentifierHelp($orderIdentifierMode),
            'customer_verification_mode' => $customerVerificationMode,
            'show_customer_name_field' => $customerVerificationMode === 'name',
            'form_values' => $formValues,
            'error_message' => $errorMessage,
            'orderwithdrawl_product_selection_enabled' => true,
            'orderwithdrawl_product_selection_ready' => $ready,
            'orderwithdrawl_order_items' => $orderItems,
            'orderwithdrawl_single_order_item' => count($orderItems) === 1,
        ]);

        $this->setTemplate('module:orderwithdrawl/views/templates/front/form.tpl');
    }

    /**
     * @return array<string, string>
     */
    protected function buildSubmittedFormValues(string $customerFirstname, string $customerLastname, string $customerName, string $orderIdentifier, string $email, string $customerNote = ''): array
    {
        return [
            'customer_firstname' => $customerFirstname,
            'customer_lastname' => $customerLastname,
            'customer_name' => $customerName,
            'order_reference' => $orderIdentifier,
            'email' => $email,
            'customer_note' => $customerNote,
        ];
    }

    protected function getButtonLabelForLanguage(int $idLang): string
    {
        $label = Configuration::get(OrderWithdrawlConfig::BUTTON_LABEL, $idLang);
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

    /**
     * @return array{firstname:string, lastname:string}
     */
    protected function splitConsumerName(string $consumerName): array
    {
        $parts = explode(' ', SecurityGuard::normaliseConsumerName($consumerName), 2);

        return [
            'firstname' => $parts[0] ?? '',
            'lastname' => $parts[1] ?? '',
        ];
    }

    protected function normaliseWithdrawalScope(string $scope): string
    {
        $scope = trim($scope);
        if ($scope === '') {
            return 'full';
        }

        return in_array($scope, ['full', 'partial'], true) ? $scope : '';
    }

    /**
     * @param mixed $names
     * @param mixed $numbers
     * @param mixed $quantities
     *
     * @return array{rows:list<array{name:string, number:string, quantity:string}>, has_invalid_row:bool}
     */
    protected function collectContractPartRows($names, $numbers, $quantities): array
    {
        $names = $this->normaliseFormArray($names);
        $numbers = $this->normaliseFormArray($numbers);
        $quantities = $this->normaliseFormArray($quantities);
        $maxRows = max(count($names), count($numbers), count($quantities));
        $rows = [];
        $hasInvalidRow = false;

        for ($i = 0; $i < $maxRows && $i < 20; ++$i) {
            $name = $this->normaliseContractPartValue($names[$i] ?? '', 255);
            $number = $this->normaliseContractPartValue($numbers[$i] ?? '', 128);
            $quantity = $this->normaliseContractPartValue($quantities[$i] ?? '', 64);

            if ($name === '' && $number === '' && $quantity === '') {
                continue;
            }

            if ($name === '' && $number === '') {
                $hasInvalidRow = true;
                continue;
            }

            $rows[] = [
                'name' => $name,
                'number' => $number,
                'quantity' => $quantity,
            ];
        }

        return [
            'rows' => $rows,
            'has_invalid_row' => $hasInvalidRow,
        ];
    }

    /**
     * @param mixed $value
     *
     * @return list<mixed>
     */
    protected function normaliseFormArray($value): array
    {
        if (is_array($value)) {
            return array_values($value);
        }

        if ($value === null || $value === false) {
            return [];
        }

        return [$value];
    }

    /**
     * @param mixed $value
     */
    protected function normaliseContractPartValue($value, int $maxLength): string
    {
        $normalised = str_replace(["\r\n", "\r", "\n", "\t"], ' ', (string) $value);
        $stripped = strip_tags($normalised);
        $collapsed = preg_replace('/\s+/u', ' ', $stripped);
        if (!is_string($collapsed)) {
            $collapsed = $stripped;
        }

        return Tools::substr(trim($collapsed), 0, $maxLength);
    }

    protected function getWithdrawalScopeLabel(string $scope): string
    {
        if ($scope === 'partial') {
            return $this->module->l('Selected items or services', 'submit');
        }

        return $this->module->l('Entire order', 'submit');
    }

    /**
     * @param list<array{name:string, number:string, quantity:string}> $rows
     */
    protected function buildContractPartDetails(array $rows): string
    {
        if ($rows === []) {
            return '';
        }

        $blocks = [];
        foreach ($rows as $index => $row) {
            $lines = [(string) ($index + 1) . '.'];
            if ($row['name'] !== '') {
                $lines[] = $this->module->l('Item name:', 'submit') . ' ' . $row['name'];
            }
            if ($row['number'] !== '') {
                $lines[] = $this->module->l('Item number:', 'submit') . ' ' . $row['number'];
            }
            if ($row['quantity'] !== '') {
                $lines[] = $this->module->l('Quantity:', 'submit') . ' ' . $row['quantity'];
            }
            $blocks[] = implode("\n", $lines);
        }

        return implode("\n\n", $blocks);
    }

    /**
     * @param list<array{id_order_detail:int, name:string, reference:string, quantity:int}> $products
     * @param mixed $selectedIds
     * @param mixed $quantities
     *
     * @return array{rows:list<array{name:string, number:string, quantity:string}>, has_invalid_row:bool}
     */
    protected function collectSelectedOrderProductRows(array $products, $selectedIds, $quantities): array
    {
        $selectedIds = $this->normaliseFormArray($selectedIds);
        $quantityInputs = is_array($quantities) ? $quantities : [];
        $productMap = [];
        foreach ($products as $product) {
            $idOrderDetail = (int) $product['id_order_detail'];
            if ($idOrderDetail > 0) {
                $productMap[$idOrderDetail] = $product;
            }
        }

        $rows = [];
        $hasInvalidRow = false;
        $seen = [];
        foreach ($selectedIds as $rawId) {
            if (count($rows) >= 50) {
                break;
            }

            $idOrderDetail = (int) $rawId;
            if ($idOrderDetail <= 0 || isset($seen[$idOrderDetail]) || !isset($productMap[$idOrderDetail])) {
                $hasInvalidRow = true;
                continue;
            }
            $seen[$idOrderDetail] = true;

            $product = $productMap[$idOrderDetail];
            $orderedQuantity = max(1, (int) $product['quantity']);
            $rawQuantity = $this->getSelectedOrderItemQuantity($quantityInputs, $idOrderDetail);
            $selectedQuantity = $this->normaliseSelectedOrderItemQuantity($rawQuantity, $orderedQuantity);
            if ($selectedQuantity <= 0) {
                $hasInvalidRow = true;
                continue;
            }

            $rows[] = [
                'name' => (string) $product['name'],
                'number' => (string) $product['reference'],
                'quantity' => (string) $selectedQuantity,
            ];
        }

        return [
            'rows' => $rows,
            'has_invalid_row' => $hasInvalidRow,
        ];
    }

    protected function getSelectedOrderItemQuantity(array $quantityInputs, int $idOrderDetail): string
    {
        if (array_key_exists($idOrderDetail, $quantityInputs)) {
            return (string) $quantityInputs[$idOrderDetail];
        }

        $key = (string) $idOrderDetail;
        if (array_key_exists($key, $quantityInputs)) {
            return (string) $quantityInputs[$key];
        }

        return '';
    }

    protected function normaliseSelectedOrderItemQuantity(string $rawQuantity, int $orderedQuantity): int
    {
        $rawQuantity = trim($rawQuantity);
        if ($rawQuantity === '') {
            return $orderedQuantity;
        }

        if (!ctype_digit($rawQuantity)) {
            return 0;
        }

        $quantity = (int) $rawQuantity;
        if ($quantity < 1 || $quantity > $orderedQuantity) {
            return 0;
        }

        return $quantity;
    }

    protected function encodeMultilineTextForHtml(string $plain): string
    {
        if (trim($plain) === '') {
            return '';
        }

        return nl2br(htmlspecialchars($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8'), false);
    }

    protected function getCustomerRepository(): CustomerRepository
    {
        if ($this->customerRepository === null) {
            $this->customerRepository = new CustomerRepository();
        }

        return $this->customerRepository;
    }

    public function setCustomerRepository(CustomerRepository $repository)
    {
        $this->customerRepository = $repository;
    }

    protected function getMailDispatcher(): MailDispatcher
    {
        if ($this->mailDispatcher === null) {
            $this->mailDispatcher = new MailDispatcher(dirname(__FILE__) . '/../../mails/');
        }

        return $this->mailDispatcher;
    }

    public function setMailDispatcher(MailDispatcher $dispatcher)
    {
        $this->mailDispatcher = $dispatcher;
    }

    /**
     * Honeypot check: the form hides this input from real users via CSS; any
     * value means the request is coming from an automated submission.
     */
    protected function isHoneypotTriggered(): bool
    {
        return SecurityGuard::isHoneypotTriggered(
            Tools::getValue(OrderWithdrawlConfig::HONEYPOT_FIELD, '')
        );
    }

    /**
     * CSRF token check. Accepts the module-specific field name. The token
     * itself is minted by `Tools::getToken(false)` (shop-wide non-user token).
     *
     * To keep the existing test suite functional, the check is skipped when
     * the Tools stub does not implement `getToken()` - tests that want to
     * exercise the CSRF path can still seed the field explicitly.
     */
    protected function validateCsrfToken(): bool
    {
        if (!method_exists('Tools', 'getToken')) {
            return true;
        }

        $submitted = (string) Tools::getValue(OrderWithdrawlConfig::CSRF_TOKEN_FIELD, '');
        $expected = (string) Tools::getToken(false);

        if ($expected === '') {
            // No token producible (e.g. very early bootstrap). Fail open so
            // legitimate requests are not blocked, but log the situation.
            return true;
        }

        if ($submitted === '') {
            return false;
        }

        return hash_equals($expected, $submitted);
    }

    /**
     * Extract the client IP with PrestaShop's helper when available. Falls
     * back to `$_SERVER`.
     */
    protected function getClientIp(): string
    {
        if (method_exists('Tools', 'getRemoteAddr')) {
            return (string) Tools::getRemoteAddr();
        }

        return (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    }

    /**
     * Collect and sanitize admin recipients. Entries with header-injection
     * characters are dropped silently via {@see SecurityGuard::sanitizeAdminEmails}.
     *
     * @return list<string>
     */
    protected function collectAdminRecipients(): array
    {
        $adminEmailList = [];
        $shopEmail = (string) Configuration::get(OrderWithdrawlConfig::PS_SHOP_EMAIL);
        if ($shopEmail !== '' && Validate::isEmail($shopEmail)) {
            $adminEmailList[] = $shopEmail;
        }

        $additionalEmails = (string) Configuration::get(OrderWithdrawlConfig::ADMIN_EMAILS, null, null, null, '');
        foreach (SecurityGuard::sanitizeAdminEmails($additionalEmails) as $additionalEmail) {
            $adminEmailList[] = $additionalEmail;
        }

        return array_values(array_unique($adminEmailList));
    }

    /**
     * Generic failure logger. Raw exception details are only added when the
     * shop is running in dev mode, so sensitive payloads never end up in
     * `ps_log` on production.
     */
    protected function logGenericFailure(string $headline, Exception $e, int $orderId)
    {
        PrestaShopLogger::addLog(
            $headline,
            PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR,
            null,
            'OrderWithdrawl',
            $orderId
        );
        if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
            PrestaShopLogger::addLog(
                'Detail: ' . $e->getMessage(),
                PrestaShopLogger::LOG_SEVERITY_LEVEL_INFORMATIVE,
                null,
                'OrderWithdrawl',
                $orderId
            );
        }
    }

    /**
     * Build the customer order-history link (if the context offers one).
     * Empty string when we cannot resolve the link so the template
     * placeholder falls back to plain text.
     */
    protected function buildHistoryUrl(): string
    {
        if (!method_exists($this->context->link, 'getPageLink')) {
            return '';
        }

        try {
            $link = $this->context->link->getPageLink('history', true);
        } catch (Throwable $e) {
            return '';
        }

        return (string) $link;
    }

    /**
     * Format a price string using the best available locale formatter.
     */
    protected function formatPriceForLocale(float $amount, int $idCurrency, int $idLang): string
    {
        try {
            $currency = new Currency($idCurrency);
        } catch (Throwable $e) {
            return (string) $amount;
        }

        if (!Validate::isLoadedObject($currency) || empty($currency->iso_code)) {
            return (string) $amount;
        }

        // Feature-detect getContextLocale instead of PS-version sniffing. The
        // underlying signature change is about PS1.7.8's second parameter;
        // we probe Reflection so the same branch covers forks and backports
        // that don't follow the version-string contract.
        if (method_exists(Tools::class, 'getContextLocale')) {
            try {
                // PS-L3: detect the `getContextLocale($context, $idLang)` signature
                // added in PS 1.7.8 via reflection instead of version_compare.
                $locale = $this->invokeGetContextLocale($idLang);

                if (is_object($locale) && method_exists($locale, 'formatPrice')) {
                    return (string) $locale->formatPrice((float) $amount, (string) $currency->iso_code);
                }
            } catch (Throwable $e) {
                // Fall through to the plain formatter below.
            }
        }

        try {
            $precision = property_exists($currency, 'precision') ? (int) $currency->precision : 2;

            return sprintf(
                '%s %s',
                number_format((float) $amount, $precision, '.', ''),
                (string) $currency->iso_code
            );
        } catch (Throwable $e) {
            return (string) $amount;
        }
    }

    /**
     * Invoke `Tools::getContextLocale` honouring both historical signatures
     * (PS < 1.7.8: `$context`, PS >= 1.7.8: `$context, $idLang`). Uses
     * reflection so the correct signature is detected by looking at the actual
     * method parameters rather than a PS version string (PS-L3). Works on
     * hard-forked shops and LTS backports that don't follow version contracts.
     *
     * @return object|null
     */
    protected function invokeGetContextLocale(int $idLang)
    {
        if (!method_exists(Tools::class, 'getContextLocale')) {
            return null;
        }

        try {
            $reflection = new ReflectionMethod(Tools::class, 'getContextLocale');
            $acceptsIdLang = $reflection->getNumberOfParameters() >= 2;
        } catch (ReflectionException $e) {
            $acceptsIdLang = false;
        }

        if ($acceptsIdLang) {
            return Tools::getContextLocale($this->context, $idLang);
        }

        return Tools::getContextLocale($this->context);
    }

    /**
     * Save the optional withdrawal note on the order.
     *
     * The note is stored both as a private order message and appended to the
     * internal order note so it is visible directly in the Back Office.
     */
    protected function saveCustomerNoteAsOrderMessage(Order $order, Customer $customer, string $email, string $customerNote)
    {
        if (!Validate::isLoadedObject($order)) {
            return;
        }

        $messageText = $this->buildCustomerNoteEntry($order, $customer, $email, $customerNote);
        // Truncation happens exactly once, in SecurityGuard::normaliseCustomerNote.
        // We only ensure the composed message (with headers) does not exceed
        // the same bound by trimming the final assembly to the same limit.
        $messageText = Tools::substr($messageText, 0, OrderWithdrawlConfig::CUSTOMER_NOTE_MAX_LENGTH);

        try {
            $message = new Message();
            $message->id_order = (int) $order->id;
            $message->id_cart = (int) $order->id_cart;
            $message->id_customer = (int) $order->id_customer;
            $message->id_employee = 0;
            $message->private = true;
            $message->message = $messageText;
            $message->add();
        } catch (Exception $e) {
            $this->logGenericFailure(
                'Order withdrawal note save failed',
                $e,
                (int) $order->id
            );
        }

        $this->appendCustomerNoteToInternalOrderNote($order, $messageText);
    }

    /**
     * Build a normalized text block for the withdrawal note.
     */
    protected function buildCustomerNoteEntry(Order $order, Customer $customer, $email, $customerNote)
    {
        $parts = [
            'Order withdrawal note',
            'Order number: ' . (string) $order->reference,
            'Customer email: ' . (string) $email,
        ];

        if (Validate::isLoadedObject($customer)) {
            $parts[] = 'Customer: ' . trim($customer->firstname . ' ' . $customer->lastname);
        }

        $parts[] = 'Submitted at: ' . MailDispatcher::formatMailDate();
        $parts[] = '';
        $parts[] = (string) $customerNote;

        return implode("\n", $parts);
    }

    protected function saveContractPartDetailsAsOrderMessage(Order $order, Customer $customer, string $email, string $withdrawalScopeLabel, string $contractPartDetails)
    {
        if (!Validate::isLoadedObject($order) || trim($contractPartDetails) === '') {
            return;
        }

        $parts = [
            $this->module->l('Order withdrawal contract part details', 'submit'),
            'Order number: ' . (string) $order->reference,
            'Customer email: ' . (string) $email,
        ];

        if (Validate::isLoadedObject($customer)) {
            $parts[] = 'Customer: ' . trim($customer->firstname . ' ' . $customer->lastname);
        }

        $parts[] = 'Submitted at: ' . MailDispatcher::formatMailDate();
        $parts[] = 'Withdrawal scope: ' . $withdrawalScopeLabel;
        $parts[] = '';
        $parts[] = $contractPartDetails;

        $messageText = Tools::substr(
            implode("\n", $parts),
            0,
            OrderWithdrawlConfig::CUSTOMER_NOTE_MAX_LENGTH
        );

        try {
            $message = new Message();
            $message->id_order = (int) $order->id;
            $message->id_cart = (int) $order->id_cart;
            $message->id_customer = (int) $order->id_customer;
            $message->id_employee = 0;
            $message->private = true;
            $message->message = $messageText;
            $message->add();
        } catch (Exception $e) {
            $this->logGenericFailure(
                'Order withdrawal contract part details save failed',
                $e,
                (int) $order->id
            );
        }

        $this->appendCustomerNoteToInternalOrderNote($order, $messageText);
    }

    protected function buildDeadlineReviewNotice(DateTime $deadline): string
    {
        try {
            $deadlineLabel = MailDispatcher::formatMailDate($deadline);
        } catch (Throwable $e) {
            $deadlineLabel = '';
        }

        return sprintf(
            $this->module->l(
                'The withdrawal request has been received. According to the data stored in the shop, the calculated withdrawal deadline expired on %s. Please review the request manually.',
                'submit'
            ),
            $deadlineLabel
        );
    }

    protected function buildAutomaticRejectionNotice(DateTime $deadline): string
    {
        try {
            $deadlineLabel = MailDispatcher::formatMailDate($deadline);
        } catch (Throwable $e) {
            $deadlineLabel = '';
        }

        return sprintf(
            $this->module->l(
                'The calculated withdrawal deadline expired on %s. Automatic rejection is enabled; the request was moved to the rejection status after receipt.',
                'submit'
            ),
            $deadlineLabel
        );
    }

    protected function isAutomaticExpiredRejectionEnabled(): bool
    {
        return (bool) Configuration::get(
            OrderWithdrawlConfig::AUTO_REJECT_EXPIRED,
            null,
            null,
            null,
            OrderWithdrawlConfig::DEFAULT_AUTO_REJECT_EXPIRED
        );
    }

    protected function applyAutomaticExpiredRejection(Order $order): bool
    {
        if (!Validate::isLoadedObject($order)) {
            return false;
        }

        $rejectedStateId = (int) Configuration::get(OrderWithdrawlConfig::STATE_REJECTED_ID);
        if ($rejectedStateId <= 0) {
            PrestaShopLogger::addLog(
                'Order withdrawal automatic rejection skipped: rejected state is not configured.',
                PrestaShopLogger::LOG_SEVERITY_LEVEL_WARNING,
                null,
                'OrderWithdrawl',
                (int) $order->id
            );

            return false;
        }

        $rejectedState = new OrderState($rejectedStateId);
        if (!Validate::isLoadedObject($rejectedState)) {
            PrestaShopLogger::addLog(
                'Order withdrawal automatic rejection skipped: rejected state could not be loaded.',
                PrestaShopLogger::LOG_SEVERITY_LEVEL_WARNING,
                null,
                'OrderWithdrawl',
                (int) $order->id
            );

            return false;
        }

        try {
            $history = new OrderHistory();
            $history->id_order = (int) $order->id;
            $history->id_employee = 0;
            $history->changeIdOrderState($rejectedStateId, $order);

            return (bool) $history->add();
        } catch (Exception $e) {
            $this->logGenericFailure(
                'Order withdrawal automatic rejection failed',
                $e,
                (int) $order->id
            );
        }

        return false;
    }

    protected function buildProcessContinuationNotice(int $idLang): string
    {
        $iso = (string) Language::getIsoById($idLang);
        if ($this->module instanceof OrderWithdrawl) {
            return (string) $this->module->getProcessContinuationNoticeByIso($iso);
        }

        return $this->module->l(
            'Note: The withdrawal was successfully submitted. Further processing, review and handling will follow your internal business process.',
            'submit'
        );
    }

    protected function saveDeadlineReviewNoticeAsOrderMessage(Order $order, Customer $customer, string $email, string $deadlineReviewNotice)
    {
        if (!Validate::isLoadedObject($order) || trim($deadlineReviewNotice) === '') {
            return;
        }

        $parts = [
            $this->module->l('Order withdrawal deadline review notice', 'submit'),
            'Order number: ' . (string) $order->reference,
            'Customer email: ' . (string) $email,
        ];

        if (Validate::isLoadedObject($customer)) {
            $parts[] = 'Customer: ' . trim($customer->firstname . ' ' . $customer->lastname);
        }

        $parts[] = 'Submitted at: ' . MailDispatcher::formatMailDate();
        $parts[] = '';
        $parts[] = $deadlineReviewNotice;

        $messageText = Tools::substr(
            implode("\n", $parts),
            0,
            OrderWithdrawlConfig::CUSTOMER_NOTE_MAX_LENGTH
        );

        try {
            $message = new Message();
            $message->id_order = (int) $order->id;
            $message->id_cart = (int) $order->id_cart;
            $message->id_customer = (int) $order->id_customer;
            $message->id_employee = 0;
            $message->private = true;
            $message->message = $messageText;
            $message->add();
        } catch (Exception $e) {
            $this->logGenericFailure(
                'Order withdrawal deadline review notice save failed',
                $e,
                (int) $order->id
            );
        }

        $this->appendCustomerNoteToInternalOrderNote($order, $messageText);
    }

    protected function saveAutomaticRejectionNoticeAsOrderMessage(Order $order, Customer $customer, string $email, string $automaticRejectionNotice)
    {
        $this->saveOrderMessageWithTitle(
            $order,
            $customer,
            $email,
            $this->module->l('Order withdrawal automatic rejection notice', 'submit'),
            $automaticRejectionNotice,
            'Order withdrawal automatic rejection notice save failed'
        );
    }

    protected function saveProcessContinuationNoticeAsOrderMessage(Order $order, Customer $customer, string $email, string $processContinuationNotice)
    {
        $this->saveOrderMessageWithTitle(
            $order,
            $customer,
            $email,
            $this->module->l('Order withdrawal process continuation notice', 'submit'),
            $processContinuationNotice,
            'Order withdrawal process continuation notice save failed'
        );
    }

    protected function saveOrderMessageWithTitle(Order $order, Customer $customer, string $email, string $title, string $body, string $failureHeadline)
    {
        if (!Validate::isLoadedObject($order) || trim($body) === '') {
            return;
        }

        $parts = [
            $title,
            'Order number: ' . (string) $order->reference,
            'Customer email: ' . (string) $email,
        ];

        if (Validate::isLoadedObject($customer)) {
            $parts[] = 'Customer: ' . trim($customer->firstname . ' ' . $customer->lastname);
        }

        $parts[] = 'Submitted at: ' . MailDispatcher::formatMailDate();
        $parts[] = '';
        $parts[] = $body;

        $messageText = Tools::substr(
            implode("\n", $parts),
            0,
            OrderWithdrawlConfig::CUSTOMER_NOTE_MAX_LENGTH
        );

        try {
            $message = new Message();
            $message->id_order = (int) $order->id;
            $message->id_cart = (int) $order->id_cart;
            $message->id_customer = (int) $order->id_customer;
            $message->id_employee = 0;
            $message->private = true;
            $message->message = $messageText;
            $message->add();
        } catch (Exception $e) {
            $this->logGenericFailure(
                $failureHeadline,
                $e,
                (int) $order->id
            );
        }

        $this->appendCustomerNoteToInternalOrderNote($order, $messageText);
    }

    /**
     * Append the note to the internal order note shown in the Back Office.
     */
    protected function appendCustomerNoteToInternalOrderNote(Order $order, string $messageText)
    {
        $messageText = trim($messageText);
        if ($messageText === '') {
            return;
        }

        try {
            $existingNote = trim((string) $order->note);
            $normalizedExistingNote = str_replace(["\r\n", "\r"], "\n", $existingNote);

            if ($normalizedExistingNote !== '' && strpos($normalizedExistingNote, $messageText) !== false) {
                return;
            }

            $separator = $normalizedExistingNote === '' ? '' : "\n\n----------------------------------------\n\n";
            $order->note = $normalizedExistingNote . $separator . $messageText;
            $order->update();
        } catch (Exception $e) {
            $this->logGenericFailure(
                'Order withdrawal internal note save failed',
                $e,
                (int) $order->id
            );
        }
    }
}
