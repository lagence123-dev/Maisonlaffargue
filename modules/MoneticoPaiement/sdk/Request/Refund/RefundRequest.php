<?php

namespace MoneticoPaiement\Sdk\Request\Refund;

use MoneticoPaiement\Sdk\Utilities\HmacComputer;
use  MoneticoPaiement\Sdk\Collections\PaymentEnvironment;
use  MoneticoPaiement\Sdk\Collections\RefundResponseValue;
use MoneticoPaiement\Sdk\Utilities\Helper;
use MoneticoPaiement\Sdk\Utilities\MPRequest;

class RefundRequest
{
    use MPRequest;

    /**
     * @var string
     * Reference of the terminal
     */
    private $ept;
    /**
     * @var string
     *  Terminal version , default = 3.0
     */
    private $ept_version = "3.0";
    /**
     * @var \DateTime
     * Refund data in JJ/MM/AAAA:HH:MM:SS
     */
    private $date;
    /**
     * @var \DateTime
     * Order date in JJ/MM/AAAA
     */
    private $order_date;
    /**
     * @var \DateTime
     * Recovery date in JJ/MM/AAAA
     */
    private $recovery_date;
    /**
     * @var string
     * Authorisation number
     */
    private $authorization_number;
    /**
     * @var string
     * Reference of the terminal
     */
    private $amount;
    /**
     * @var float
     * Recredited  amount
     */
    private $recredited_amount;
    /**
     * @var float
     * Possible amount
     */
    private $possible_amount;
    /**
     * @var string
     * Currency
     */
    private $currency;
    /**
     * @var string
     * reference
     */
    private $reference;
    /**
     * @var string
     * language
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

    public function getFormFields()
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
        $formFields["date_remise"] = $this->getRecoveryDate()->format("d/m/Y");

        $formFields["num_autorisation"] = $this->getAuthorizationNumber();

        $formFields["montant"] = $this->getAmount();
        $formFields["montant_recredit"] = Helper::formatAmount($this->getRecreditedAmount(), $this->getCurrency());
        $formFields["montant_possible"] = Helper::formatAmount($this->getPossibleAmount(), $this->getCurrency());

        $formFields["reference"] = $this->getReference();
        $formFields["lgue"] = $this->getLangue();
        $formFields["societe"] = $this->getCodeSociete();

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
        $this->ept = $ept;
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
     * @return \DateTime
     */
    public function getRecoveryDate(): \DateTime
    {
        return $this->recovery_date;
    }

    /**
     * @param \DateTime $recovery_date
     */
    public function setRecoveryDate(\DateTime $recovery_date)
    {
        $this->recovery_date = $recovery_date;
    }

    /**
     * @return string
     */
    public function getAuthorizationNumber(): string
    {
        return $this->authorization_number;
    }

    /**
     * @param string $authorization_number
     */
    public function setAuthorizationNumber(string $authorization_number)
    {
        $this->authorization_number = $authorization_number;
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
    public function getRecreditedAmount(): float
    {
        return $this->recredited_amount;
    }

    /**
     * @param float $recredited_amount
     */
    public function setRecreditedAmount(float $recredited_amount)
    {
        $this->recredited_amount = round($recredited_amount, 2);
    }

    /**
     * @return float
     */
    public function getPossibleAmount(): float
    {
        return $this->possible_amount;
    }

    /**
     * @param float $possible_amount
     */
    public function setPossibleAmount(float $possible_amount)
    {
        $this->possible_amount = round($possible_amount, 2);
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
        $this->langue = $langue;
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
     * check the request
     *
     * @param bool $is_sandbox
     *
     * @return array|int[]
     */
    public function check(bool $is_sandbox)
    {
        $url = ($is_sandbox ? PaymentEnvironment::SANDBOX_API_URI : PaymentEnvironment::API_URI) . '/' . PaymentEnvironment::PAYMENT_RECREDIT;

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
            'success' => 0
        ];

        if (isset($data_response['cdr'])) {
            if ($this->getLangue() && isset(RefundResponseValue::RESPONSE[$data_response['cdr']][$this->getLangue()])) {
                $result = RefundResponseValue::RESPONSE[$data_response['cdr']][$this->getLangue()];
            } else {
                $result = RefundResponseValue::RESPONSE[$data_response['cdr']][RefundResponseValue::DEFAULT_LANGUAGE];
            }

            $response_content = array_merge([
                'success' => intval($data_response['cdr']) === 0,
                'cdr' => $data_response['cdr'],
            ], $result);
        }

        return $response_content;
    }
}