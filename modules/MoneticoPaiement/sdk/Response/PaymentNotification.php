<?php

namespace MoneticoPaiement\sdk\Response;

use DateTime;
use MoneticoPaiement\Sdk\Api\PaymentSession\MerchantConfiguration;
use MoneticoPaiement\Sdk\Api\PaymentSession\Order;
use MoneticoPaiement\Sdk\Api\PaymentSession\PaymentMean;
use MoneticoPaiement\Sdk\Response\PaymentSession\Data;
use MoneticoPaiement\Sdk\Response\PaymentSession\Event;
use MoneticoPaiement\Sdk\Utilities\HmacComputer;
use MoneticoPaiement\Sdk\Utilities\Models\Payment\Amount;

class PaymentNotification extends PaymentResponse
{
    private MerchantConfiguration $merchant_configuration;
    private Order $order;
    private Event $event;

    public function __construct(array $response, string $cle_mac)
    {
        $this->setCodeRetour($response['event']['type']);
        parent::__construct($response, $cle_mac);
    }

    public function checkResponse(): array
    {
        $isSealValidated = false;
        $payment_result = [];
        if (!is_null($this->getMac()) && !is_null($this->getCleMac()) && is_array($this->getData())) {

            // check if reencoding is the same
            $isSealValidated = (new HmacComputer())->validateJsonSeal(json_encode($this->getData()), $this->getCleMac(), $this->getMac());

            if ($isSealValidated) {
                $order_data = $this->getData()['order'];
                $merchant_configuration_data = $this->getData()['merchant_configuration'];
                $event_data = $this->getData()['event'];

                // Date convert string to DateTime
                $date_format = "Y-m-d\TH:i:s";
                $date = DateTime::createFromFormat($date_format, $event_data['date']);

                // set Event
                $event = new Event();
                $event->setType($event_data['type']);
                $event->setDate($date);

                $data = new Data();
                $amount = new Amount();
                $amount->setValue($event_data['data']['amount']['value']);
                $amount->setCurrency($event_data['data']['amount']['currency']);
                $amount->setExponent($event_data['data']['amount']['exponent']);
                $data->setAmount($amount);

                $payment_mean = new PaymentMean();
                $payment_mean->setType($event_data['data']['payment_mean']['type']);
                $payment_mean->setTransferType($event_data['data']['payment_mean']['transfer_type']);
                $data->setPaymentMean($payment_mean);
                $event->setData($data);
                $this->setEvent($event);

                // set MerchantConfig
                $merchant_configuration = new MerchantConfiguration();
                $merchant_configuration->setConfiguration($merchant_configuration_data['configuration']);
                $merchant_configuration->setLanguage($merchant_configuration_data['language']);
                $merchant_configuration->setPointOfSale($merchant_configuration_data['point_of_sale']);
                $this->setMerchantConfiguration($merchant_configuration);

                // set Order
                $order = new Order();
                $order->setDate($date);
                $amount = new Amount();
                $amount->setValue($order_data['amount']['value']);
                $amount->setCurrency($order_data['amount']['currency']);
                $amount->setExponent($order_data['amount']['exponent']);
                $order->setAmount($amount);
                $order->setEnvironment($order_data['environment']);
                $order->setReference($order_data['reference']);
                $order->setComment($order_data['comment']);
                $this->setOrder($order);

                $payment_result = [
                    'is_payment_validated' => $this->isPaymentValidated(),
                    'is_payment_pending' => $this->isPaymentPending(),
                    'is_payment_refused' => $this->isPaymentRefused(),
                    'is_sandbox_payment' => $this->isSandboxPayment(),
                    'is_standard_transfer' => $this->isStandardTransfer(),
                    "is_instant_transfer" => $this->isInstantTransfer()
                ];
            }
        }

        $this->setIsSealValidated($isSealValidated);

        return $payment_result;
    }

    /**
     * Return if payment is validated
     * @return bool
     */
    public function isPaymentValidated(): bool
    {
        $isValidated = false;
        if (!is_null($this->getCodeRetour())) {
            $isValidated = $this->getCodeRetour() === "acceptance_successful";
        }
        return $isValidated;
    }

    /**
     * Return if payment is pending
     * @return bool
     */
    public function isPaymentPending(): bool
    {
        $isPending = false;
        if (!is_null($this->getCodeRetour())) {
            $isPending = $this->getCodeRetour() === 'acceptance_pending';
        }
        return $isPending;
    }

    /**
     * Return if payment is refused
     * @return bool
     */
    public function isPaymentRefused(): bool
    {
        $isRefused = false;
        if (!is_null($this->getCodeRetour())) {
            $isRefused = $this->getCodeRetour() === 'acceptance_refused';
        }
        return $isRefused;
    }

    /**
     * Return if payment is from a sandbox mode
     * @return bool
     */
    public function isSandboxPayment(): bool
    {
        $isSandBox = false;
        if ($this->getOrder()->getEnvironment() !== null) {
            $isSandBox = $this->getOrder()->getEnvironment() === "sandbox";
        }
        return $isSandBox;
    }

    /**
     * @return bool
     */
    public function isStandardTransfer(): bool
    {
        $isStandardTransfer = false;
        if ($this->getEvent()->getData()->getPaymentMean()->getTransferType() !== null) {
            $isStandardTransfer = $this->getEvent()->getData()->getPaymentMean()->getTransferType() === 'standard';
        }
        return $isStandardTransfer;
    }

    /**
     * @return bool
     */
    public function isInstantTransfer(): bool
    {
        $isInstantTransfer = false;
        if ($this->getEvent()->getData()->getPaymentMean()->getTransferType() !== null) {
            $isInstantTransfer = $this->getEvent()->getData()->getPaymentMean()->getTransferType() === 'instant';
        }
        return $isInstantTransfer;
    }

    /**
     * Return response
     * @param bool $notification_validated
     * @param string|null $reason
     * @return false|string
     */
    public function sendResponse(bool $notification_validated = null, string $reason = null): string
    {
        return self::sendNotificationResponse($notification_validated, $reason);
    }

    static public function sendNotificationResponse(bool $notification_validated = null, string $reason = null)
    {
        $body['notification_validated'] = $notification_validated;

        if ($reason !== null) {
            $body['reason'] = $reason;
        }

        header("Content-Type: application/json; charset=utf-8");
        return json_encode($body);
    }

    /**
     * @return MerchantConfiguration
     */
    public function getMerchantConfiguration(): MerchantConfiguration
    {
        return $this->merchant_configuration;
    }

    /**
     * @param MerchantConfiguration $merchant_configuration
     */
    public function setMerchantConfiguration(MerchantConfiguration $merchant_configuration): void
    {
        $this->merchant_configuration = $merchant_configuration;
    }

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

}
