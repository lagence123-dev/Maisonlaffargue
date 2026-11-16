<?php

namespace MoneticoPaiement\Sdk\Api\Payment;

use MoneticoPaiement\Sdk\Utilities\Collections\PaymentEnvironment;
use MoneticoPaiement\Sdk\Utilities\Collections\PaymentCaptureResponseValue;
use MoneticoPaiement\Sdk\Utilities\Helper;
use MoneticoPaiement\Sdk\Utilities\HmacComputer;

/**
 * Class PaymentCapture
 * @package MoneticoPaiement\Sdk\Request\Payment
 *          For Partial Payment, recurring Payment or deferred Payment
 */
class Capture extends MPRequestAbstract
{
    /**
     * @var string
     * Reference of the terminal
     */
    private $ept;

    /**
     * @var string
     * Reference of the terminal
     */
    private $ept_version = "2.0";

    /**
     * @var \DateTime
     *  Capture date of the payment
     */
    private $date;

    /**
     * @var \DateTime
     * Order Date of the payment
     */
    private $order_date;

    /**
     * @var float
     * Recredited amount
     */
    private $amount;
    /**
     * @var float
     * Captured amount
     */
    private $captured_amount;
    /**
     * @var float
     * Already Captured amount
     */
    private $captured_already_amount;
    /**
     * @var float
     * Possible amount
     */
    private $remaining_amount;
    /**
     * @var string
     * reference
     */
    private $reference;
    /**
     * @var string
     * Display language of the payment page
     * Must be a string from the Language class
     */
    private $langue;
    /**
     * @var string
     * code societe
     */
    private $code_societe;
    /**
     * @var string
     * MAC
     */
    private $MAC;
    /**
     * @var string
     * Ept MAC
     */
    private $Ept_MAC;

    /**
     * @var string
     * Response Language
     */
    private $response_language;

    /**
     * @var string
     * Currency of the payment
     * Must be a string from the Currency class
     */
    private $currency;

    /**
     * @return array
     * @var bool
     */
    private $stoprecurrence = false;

    protected function getFormFields()
    {
        $formFields = $this->getFormFieldsWithoutMac();

        $hmacComputer = new HmacComputer();
        $seal = $hmacComputer->sealFields($formFields, $this->getEptMAC());
        $formFields['MAC'] = $seal;
        return $formFields;
    }

    public function getFormFieldsWithoutMac()
    {
        $formFields = [];

        // Payment terminal
        $formFields["TPE"] = $this->getEpt();
        $formFields["version"] = $this->getEptVersion();

        // Payment informations
        $formFields["date"] = $this->getDate()->format("d/m/Y:H:i:s");
        $formFields["date_commande"] = $this->getOrderDate()->format("d/m/Y");

        $formFields["montant"] = Helper::formatAmount($this->getAmount(), $this->getCurrency());
        $formFields["montant_a_capturer"] = Helper::formatAmount($this->getCapturedAmount(), $this->getCurrency());
        $formFields["montant_deja_capture"] = Helper::formatAmount($this->getCapturedAlreadyAmount(), $this->getCurrency());
        $formFields["montant_restant"] = Helper::formatAmount($this->getRemainingAmount(), $this->getCurrency());

        $formFields["reference"] = $this->getReference();
        $formFields["lgue"] = $this->getLangue();
        $formFields["societe"] = $this->getCodeSociete();

        if ($this->isStoprecurrence()) {
            $formFields["stoprecurrence"] = 'OUI';
        }
        return $formFields;
    }

    /**
     * @return string
     */
    public function getEpt(): string
    {
        return $this->ept;
    }

    /**
     * @param string $ept
     */
    public function setEpt(string $ept)
    {
        if (mb_strlen($ept) === 7) {
            $this->ept = $ept;
        }
    }

    /**
     * @return string
     */
    public function getEptVersion(): string
    {
        return $this->ept_version;
    }

    /**
     * @param string $ept_version
     */
    public function setEptVersion(string $ept_version)
    {
        $this->ept_version = $ept_version;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }


    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getResponseLangue(): string
    {
        return $this->response_langue;
    }

    /**
     * @param string $response_langue
     */
    public function setResponseLangue(string $response_langue)
    {
        if (mb_strlen($response_langue) === 2) {
            $this->response_langue = $response_langue;
        }
    }

    /**
     * @return \DateTime
     */
    public function getOrderDate(): \DateTime
    {
        return $this->order_date;
    }

    /**
     * @param \DateTime $order_date
     */
    public function setOrderDate(\DateTime $order_date)
    {
        $this->order_date = $order_date;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount)
    {
        $this->amount = round($amount, 2);
    }

    /**
     * @return string
     */
    public function getCapturedAmount(): float
    {
        return $this->captured_amount;
    }

    /**
     * @param float $captured_amount
     */
    public function setCapturedAmount(float $captured_amount)
    {
        $this->captured_amount = round($captured_amount, 2);
    }

    /**
     * @return float
     */
    public function getCapturedAlreadyAmount(): float
    {
        return $this->captured_already_amount;
    }

    /**
     * @param float $captured_already_amount
     */
    public function setCapturedAlreadyAmount(float $captured_already_amount)
    {
        $this->captured_already_amount = round($captured_already_amount, 2);
    }

    /**
     * @return float
     */
    public function getRemainingAmount(): float
    {
        return $this->remaining_amount;
    }

    /**
     * @param float $remaining_amount
     */
    public function setRemainingAmount(float $remaining_amount)
    {
        $this->remaining_amount = round($remaining_amount, 2);
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     */
    public function setReference(string $reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getLangue(): string
    {
        return $this->langue;
    }

    /**
     * @param string $langue
     */
    public function setLangue(string $langue)
    {
        if (mb_strlen($langue) === 2) {
            $this->langue = $langue;
        }
    }

    /**
     * @return string
     */
    public function getCodeSociete(): string
    {
        return $this->code_societe;
    }

    /**
     * @param string $code_societe
     */
    public function setCodeSociete(string $code_societe)
    {
        $this->code_societe = $code_societe;
    }

    /**
     * @return string
     */
    public function getMAC(): string
    {
        return $this->MAC;
    }

    /**
     * @param string $MAC
     */
    public function setMAC(string $MAC)
    {
        $this->MAC = $MAC;
    }

    /**
     * @return string
     */
    public function getEptMAC(): string
    {
        return $this->Ept_MAC;
    }

    /**
     * @param string $Ept_MAC
     */
    public function setEptMAC(string $Ept_MAC)
    {
        $this->Ept_MAC = $Ept_MAC;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return bool
     */
    public function isStoprecurrence(): bool
    {
        return $this->stoprecurrence;
    }

    /**
     * @param bool $stoprecurrence
     */
    public function setStoprecurrence(bool $stoprecurrence): void
    {
        $this->stoprecurrence = $stoprecurrence;
    }

    /**
     * check the request
     *
     * @param bool $is_sandbox
     *
     * @return array|int[]
     */
    public function check(bool $is_sandbox)
    {
        $url = ($is_sandbox ? PaymentEnvironment::API_PAYMENT_SANDBOX_URI : PaymentEnvironment::API_PAYMENT_PROD_URI)
            . PaymentEnvironment::PAYMENT_CAPTURE;

        $response = $this->process($url);
        // If it's a curl error
        if (!$response['success']) {
            return [
                'success' => $response['success'],
                'cdr' => -1,
                'desc' => $response['response'],
                'rmk' => '( URL= ' . $url . ')',
            ];
        }

        $array_response = explode("\n", $response['response']);
        $data_response = [];
        foreach ($array_response as $row_response) {
            if ($row_response != "") {
                $row = explode('=', $row_response);
                $data_response[$row[0]] = $row[1];
            }
        }

        $response_content = [
            'success' => 0,
            'cdr' => null,
            'desc' => '',
            'rmk' => '',
        ];

        if ($this->getLangue() && isset($data_response['cdr']) &&  isset($data_response['lib']) ) {
            if (isset(PaymentCaptureResponseValue::RESPONSE[$data_response['cdr']][$data_response['lib']][$this->getLangue()])) {
                $response_value = PaymentCaptureResponseValue::RESPONSE[$data_response['cdr']][$data_response['lib']][$this->getLangue()];
            } else {
                $response_value = PaymentCaptureResponseValue::RESPONSE[$data_response['cdr']][$data_response['lib']][PaymentCaptureResponseValue::DEFAULT_LANGUAGE];
            }
        }

        if (isset($data_response['cdr'])) {
            $response_content = [
                'success' => intval($data_response['cdr']) === 1,
                'cdr' => $data_response['cdr'],
                'desc' => $response_value['desc'] ?? '',
                'rmk' => $response_value['rmk'] ?? ''
            ];
        }

        return $response_content;
    }


}