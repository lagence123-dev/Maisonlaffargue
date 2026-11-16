<?php

use MoneticoPaiement\Model\MoneticoPaiementAliasBC;
use MoneticoPaiement\Model\MoneticoPaiementHelper;
use MoneticoPaiement\Model\MoneticoPaiementTransaction;
use MoneticoPaiement\Prestashop\utilities\PaymentStatus;
use MoneticoPaiement\Sdk\Response\PaymentNotification;
use MoneticoPaiement\Sdk\Response\PaymentResponse;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentProtocol;
use MoneticoPaiement\Sdk\Utilities\Traits\WhiteList;
use MoneticoPaiement\utilities\PaymentTax;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopException;

/**
 * class MoneticoPaiementCallbackModuleFrontController
 *
 * This class will be used for Monetico Paiement to call PrestaShop
 */
class MoneticoPaiementCallbackModuleFrontController extends ModuleFrontController
{
    use WhiteList;
    use PaymentTax;

    public bool $is_notification_callback = false;

    /**
     * Return if the callback type is Notification
     * @return bool
     */
    public function isNotificationCallback(): bool
    {
        return $this->is_notification_callback;
    }

    /**
     * @param bool $is_notification_callback
     * @return void
     */
    public function setNotificationCallback(bool $is_notification_callback){
        $this->is_notification_callback = $is_notification_callback;
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * Return
     */
    public function postProcess()
    {
        // We set ajax to true for return text/plain because in debug mode  we have html comments
        $this->ajax = true;
        $response_failed_reason = null;

        // Check if the request sent by Monetico is in JSON : in this case, it is a notification callback
        // In other cases, it is a CGI2 callback, and we check if the request is POST or GET type, to get data
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'json')) {
            $received_data = json_decode(file_get_contents('php://input'), true);
            $received_data['MAC'] = $_SERVER['HTTP_MAC'] ?? null;
            $this->setNotificationCallback(true);
        } else {
            $received_data = $_SERVER['REQUEST_METHOD'] === 'GET' ? $_GET : $_POST;
            $this->unsetUnusedData($received_data);
        }

        if ($this->checkEntries($received_data)) {
            $unified_data = $this->getUnifiedData($received_data);
            $security_key = MoneticoPaiementHelper::getEptMacFromEptNumber($unified_data['TPE']);
            if ($this->isNotificationCallback()) {
                $response = new PaymentNotification($received_data, $security_key);
            } else {
                $response = new PaymentResponse($received_data, $security_key);
            }
            $response_result = $response->getResponse();
            $isSealValidated = $response->getIsSealValidated();
            $notification_validated = $this->isNotificationCallback() ? $isSealValidated : null;

            if ($isSealValidated) {
                // check the current transaction with reference
                $transaction = MoneticoPaiementHelper::getTransactionByOrderRef($unified_data['reference']);
                $got_by_ref = true;

                // if not exist search transaction ID in texte-libre field
                if (!isset($transaction['id'])) {
                    $texte_libre = explode(' ', $unified_data['texte-libre']);
                    $cart_id = $texte_libre[1];
                    $transaction['id'] = MoneticoPaiementHelper::getTransactionIdByCartId($cart_id);
                    $got_by_ref = false;
                    $this->createLog($this->l('The Monetico transaction was not found by reference. Verification by the text-libre field.', 'Callback') .
                        " [reference=" . $received_data['reference'] .
                        " , cartId=" . $cart_id .
                        " , result=" . isset($transaction['id']) . "]", 2);
                }

                if (isset($transaction['id'])) {
                    $transaction = new MoneticoPaiementTransaction($transaction['id']);
                    $transaction->setPaymentResponseContext(json_encode($received_data));
                    $transaction->setOrderRef($unified_data['reference']);
                    $order_context = json_decode($transaction->getOrderContext(), true);
                    $order_context['reference'] = $unified_data['reference'];
                    $transaction->setOrderContext(json_encode($order_context));
                    $transaction->update();

                    if ($transaction->getCartId()) {
                        $cart = new Cart($transaction->getCartId());
                        if (!empty($cart->id)) {
                            $customer = new Customer($cart->id_customer);
                            $currency = new Currency($cart->id_currency);

                            // /!\ very important to do that, especially for the currency
                            // because this is the currency setup here (and not the one passed to validateOrder() below)
                            // that will be used to calculate product price-related things
                            // in \ProductCore::getPriceStatic().
                            // This is essentially what is done in \PsCheckout\Infrastructure\Adapter\Context::setCurrentCart().
                            $this->context->cart = $cart;
                            $this->context->customer = $customer;
                            $this->context->currency = $currency;
                            if ($cart->id_address_invoice !== null) {
                                $this->context->country = new \Country((new \Address($cart->id_address_invoice))->id_country);
                            }

                            // To verify corresponding amount
                            $response_currency = (int)Currency::getIdByIsoCode($unified_data['response_currency']);
                            $response_amount = floatval($unified_data['response_amount']);

                            [$total, $taxed] = $this->getOrderTotalAndTaxStatus($cart, $transaction);

                            if ($total === $response_amount && $currency->id === $response_currency) {
                                $status = $this->getOrderStatus($response_result);
                            } else {
                                $status = PaymentStatus::MP_ERROR_PAYMENT_ORDER_TOTAL;
                                $this->createLog($this->l('Order payment total error.', 'Callback') .
                                    " [reference=" . $transaction->getOrderRef() .
                                    ", expectedTotal=" . $total . " , expectedCurrency=" . $currency->id .
                                    ", total=" . $response_amount . " , currency=" . $response_currency .
                                    "  ]", 3);
                            }
                            // To verify that it's an order update or not
                            $order_id = Order::getIdByCartId($cart->id);
                            if (!empty($order_id)) {
                                $order = new Order($order_id);
                                if (!$got_by_ref) {
                                    $status = PaymentStatus::MP_WARNING;
                                    $order->note = $order->note . $this->l('Another payment has been received for an order already paid', 'Callback') . ' : ' . $transaction->getOrderRef();
                                    $this->createLog($this->l('Another payment has been received for an order already paid', 'Callback') .
                                        " [reference=" . $transaction->getOrderRef() . " , orderId=" . $order_id .
                                        "]", 3);
                                    $response->setErrorData(['error' => "Transaction '{$transaction->getOrderRef()}' has already been processed, its related to order '$order_id'"]);
                                }
                                $new_order_status_id = Configuration::get($status);
                                // if the new state  is different of current state
                                if ($order->current_state != $new_order_status_id) {
                                    //update the real amount
                                    $order->total_paid_real = $response_amount;
                                    $order->update();
                                    // we add a new history with the new state
                                    $order_history = new OrderHistory();
                                    $order_history->id_order = $order->id;
                                    $order_history->changeIdOrderState($new_order_status_id, $order->id);
                                    $order_history->save();
                                }
                            } else {
                                //validate the order if it's not a payment error
                                if (!in_array(Configuration::get($status), $this->getErrorsStatusConfIds())) {
                                    $payment_method = $this->module->displayName;
                                    if (array_key_exists($unified_data['modepaiement'], PaymentProtocol::PROTOCOL)
                                        && $unified_data['modepaiement'] !== "CB") {
                                        $payment_protocol_value = PaymentProtocol::PROTOCOL[$unified_data['modepaiement']];
                                        $payment_method = $this->module->displayName . ' (' . ($payment_protocol_value[$this->context->language->iso_code] ??
                                                $payment_protocol_value[PaymentProtocol::DEFAULT_LANGUAGE]) . ')';
                                    }
                                    try {
                                        $tmp_tax = Configuration::get('PS_TAX');
                                        Configuration::set('PS_TAX', $taxed);
                                        $this->module->validateOrder(
                                            $cart->id,
                                            Configuration::get($status),
                                            $response_amount,
                                            $payment_method,
                                            null,
                                            ['transaction_id' => $transaction->getOrderRef()],
                                            $response_currency,
                                            true,
                                            $customer->secure_key
                                        );
                                        Configuration::set('PS_TAX', $tmp_tax);
                                    } catch (PrestaShopException $e) {
                                        $this->createLog($this->l('Command validation failed.', 'Callback') .
                                            " [reference=" . $transaction->getOrderRef() .
                                            " , cartId=" . $cart->id .
                                            " , cause=" . $e->getMessage() .
                                            "]", 3);

                                        $response->setErrorData(['error' => $e->getMessage()]);
                                    }

                                } else {
                                    $this->createLog($this->l('You received a callback with an error status.', 'Callback') .
                                        " [reference=" . $transaction->getOrderRef() .
                                        " , orderId=" . $order_id .
                                        " , status=" . $status .
                                        "]", 2);
                                }
                            }

                            if ($transaction->getAliasId() > 0) {
                                $alias_id = $transaction->getAliasId();
                                $alias = new MoneticoPaiementAliasBC($alias_id);
                                if (isset($received_data['cbenregistree']) && intval($received_data['cbenregistree']) === 1 && !$alias->isCertified()) {
                                    $alias->setCertified(true);
                                    $alias->save();
                                }
                                MoneticoPaiementHelper::removeOthersAlias($alias->getUserId());
                            }
                        }
                    }
                } else {
                    $response->setErrorData([
                        'error' => "The Monetico transaction with reference '{$unified_data['reference']}' was not found.",
                    ]);

                    $response_failed_reason = $this->isNotificationCallback() ? 'order_not_found' : null;
                    $this->createLog($this->l('The Monetico transaction was not found.', 'Callback') .
                        " [reference=" . $received_data['reference'] . "]", 3);
                }
            } else {
                $response->setErrorData(['error' => 'invalid seal']); // response with "cdr=1"

                $response_failed_reason = $this->isNotificationCallback() ? 'hmac_authentication_failed' : null;
                $this->createLog($this->l('The data received is invalid.', 'Callback') .
                    " [data=" . json_encode($received_data) . "]", 2);
            }

            if (in_array($response_failed_reason, ['order_not_found', 'hmac_authentication_failed'])) {
                $notification_validated = false;
            }

            $callback_response = $response->sendResponse($notification_validated, $response_failed_reason);

        } else {
            $callback_response = $this->isNotificationCallback() ? PaymentNotification::sendNotificationResponse(false, 'technical_error') : '';
        }
        // Finally, display callback response
        echo $callback_response;
        exit(0);
    }

    /**
     * Unset prestashop variable when it's Get request to avoid hmac seal errors
     * @param $received_data
     */
    public function unsetUnusedData(&$received_data): void
    {
        if (isset($received_data['module'])) {
            unset($received_data['module']);
        }
        if (isset($received_data['controller'])) {
            unset($received_data['controller']);
        }
        if (isset($received_data['fc'])) {
            unset($received_data['fc']);
        }
        // unset isolang and id_lang if prestashop multi language is available
        if (isset($received_data['isolang'])) {
            unset($received_data['isolang']);
        }
        if (isset($received_data['id_lang'])) {
            unset($received_data['id_lang']);
        }
    }

    /**
     * Check received data from Monetico
     * @param $data
     * @return bool
     */
    public function checkEntries($data): bool
    {
        if (empty($data)) {
            return false;
        }

        // MAC
        if (empty($data['MAC']) || mb_strlen($data['MAC']) !== 40) {
            return false;
        }

        if ($this->isNotificationCallback()) {
            // TPE
            if (empty($data['merchant_configuration']['point_of_sale']) || mb_strlen($data['merchant_configuration']['point_of_sale']) !== 7) {
                return false;
            }
            // code-retour
            if (empty($data['event']['type']) || !in_array($data['event']['type'], $this->getCodeRetourWhiteList())) {
                return false;
            }
            // reference
            if (empty($data['order']['reference']) || mb_strlen($data['order']['reference']) > 50 || !ctype_alnum($data['order']['reference'])) {
                return false;
            }
            // order amount
            if (empty($data['order']['amount']['currency']) || empty($data['order']['amount']['exponent']) || empty($data['order']['amount']['value'])) {
                return false;
            } else {
                $response_currency = $data['order']['amount']['currency'];
                $response_amount = $data['order']['amount']['value'];
                $check_amount = Validate::isFloat($response_amount * pow(10, $data['order']['amount']['exponent'])) &&
                    in_array($response_currency, $this->getCurrencyWhiteList());
                if (!$check_amount) {
                    return false;
                }
            }
            // event amount
            if (empty($data['event']['data']['amount']['currency']) || empty($data['event']['data']['amount']['exponent']) ||
                empty($data['event']['data']['amount']['value'])) {
                return false;
            } else {
                $response_currency = $data['event']['data']['amount']['currency'];
                $response_amount = $data['event']['data']['amount']['value'];
                $check_amount = Validate::isFloat($response_amount * pow(10, $data['event']['data']['amount']['exponent'])) &&
                    in_array($response_currency, $this->getCurrencyWhiteList());
                if (!$check_amount) {
                    return false;
                }
            }
            // compare order amount and event amount
            if (isset($data['event']['data']['amount']) && isset($data['order']['amount'])) {
                $check_amount = $data['event']['data']['amount'] === $data['order']['amount'];
                if (!$check_amount) {
                    return false;
                }
            }

        } else {
            // TPE
            if (empty($data['TPE']) || mb_strlen($data['TPE']) !== 7) {
                return false;
            }
            // code-retour
            if (empty($data['code-retour']) || !in_array($data['code-retour'], $this->getCodeRetourWhiteList())) {
                return false;
            }
            // reference
            if (empty($data['reference']) || mb_strlen($data['reference']) > 50 || !ctype_alnum($data['reference'])) {
                return false;
            }
            // amount
            if (empty($data['montant'])) {
                return false;
            } else {
                $response_currency = Tools::substr($data['montant'], -3);
                $response_amount = Tools::substr($data['montant'], 0, -3);
                $check_amount = Validate::isFloat($response_amount) && in_array($response_currency, $this->getCurrencyWhiteList());
                if (!$check_amount) {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * Get usable values for the callback treatment
     * As we have 2 structures of data, depending on the callback type, wa have to unify keys to use same names,
     * regardless of the type of the callback.
     * @param $received_data
     * @return array
     */
    public function getUnifiedData($received_data): array
    {
        return [
            'MAC' => $received_data['MAC'],
            'TPE' => $received_data['merchant_configuration']['point_of_sale'] ?? $received_data['TPE'],
            'code-retour' => $received_data['event']['type'] ?? $received_data['code-retour'],
            'reference' => $received_data['order']['reference'] ?? $received_data['reference'],
            'response_currency' => $received_data['order']['amount']['currency'] ?? Tools::substr($received_data['montant'], -3),
            'response_amount' => isset($received_data['order']['amount']['value']) ?
                $received_data['order']['amount']['value'] / pow(10, $received_data['order']['amount']['exponent']) :
                Tools::substr($received_data['montant'], 0, -3),
            'texte-libre' => $received_data['order']['comment'] ?? $received_data['texte-libre'],
            'modepaiement' => $received_data['modepaiement'] ?? 'ipa'
        ];
    }

    /**
     * Get all error status, to check if the order status is one of them.
     * If the status of the order is not in this array, we can use ValidateOrder (create order from Cart).
     * @return array
     */
    public function getErrorsStatusConfIds(): array
    {
        return [
            Configuration::get(PaymentStatus::MP_ERROR_PAYMENT),
            Configuration::get(PaymentStatus::MP_REFUSED_PAYMENT),
            Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT[1]),
            Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT[2]),
            Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT[3]),
            Configuration::get(PaymentStatus::MP_ERROR_SPLIT_PAYMENT[4]),
        ];
    }

    /**
     * Get ths status to set to the order
     * @param $payment_result
     * @return string
     */
    public function getOrderStatus($payment_result): string
    {
        $status = PaymentStatus::MP_ERROR_PAYMENT;
        $is_notification_callback = $this->isNotificationCallback();
        $is_split_payment = !empty($payment_result['split_payment']);

        $is_validated_payment = (!$is_notification_callback && $payment_result['is_payment_validated']) ||
            ($is_notification_callback && $payment_result['is_payment_validated'] && !$payment_result['is_sandbox_payment']);

        $is_validated_sandbox_payment = (!$is_notification_callback && $payment_result['is_sandbox_payment']) ||
            ($is_notification_callback && $payment_result['is_sandbox_payment'] && $payment_result['is_payment_validated']);

        if ($payment_result['is_payment_pending']) {
            $status = PaymentStatus::MP_PENDING;

        } else if ($payment_result['is_payment_refused']) {
            $status = PaymentStatus::MP_REFUSED_PAYMENT;

        } else if ($is_split_payment && !$is_validated_payment && !$is_validated_sandbox_payment) {
            $status = PaymentStatus::MP_ERROR_SPLIT_PAYMENT[$payment_result['split_payment_number']];

        } else if ($is_validated_payment) {

            if ($is_split_payment) {
                $status = PaymentStatus::MP_RECEIVED_SPLIT_PAYMENT[$payment_result['split_payment_number']];
            } else {
                $status = PaymentStatus::MP_RECEIVED_PAYMENT;
            }

        } else if ($is_validated_sandbox_payment && !$is_split_payment) {
            $status = PaymentStatus::MP_SANDBOX_PAYMENT;
        }

        return $status;
    }

    /**
     * Add a PrestaShop log
     *
     * @param     $message
     * @param int $severity
     */
    public function createLog($message, $severity)
    {
        $log_message = "[" . $this->module->displayName . "] " . $message;
        PrestaShopLogger::addLog($log_message, $severity);
    }

}