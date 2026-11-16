<?php


namespace MoneticoPaiement\Sdk\Request\Payment;


use MoneticoPaiement\Sdk\Collections\PaymentEnvironment;
use MoneticoPaiement\Sdk\Utilities\Helper;
use MoneticoPaiement\Sdk\Utilities\HmacComputer;
use MoneticoPaiement\Sdk\Collections\PaymentStateResponseValue;
use MoneticoPaiement\Sdk\Utilities\MPRequest;

class PaymentState
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
     * @var string
     * Reference of the societe number
     */
    private $code_societe;
    /**
     * @var string
     * Etp mac used to seal fields
     */
    private $cle_mac;
    /**
     * @var string
     * Reference of the payment
     */
    private $reference;

    /**
     * @var \DateTime
     * Date of the payment
     */
    private $date;

    /**
     * @var float
     * Amount of the payment
     */
    private $amount;

    /**
     * @var string
     * Currency of the payment
     * Must be a string from the Currency class
     */
    private $currency;

    /**
     * @var string
     * Ept Mac for create request mac
     */
    private $ept_mac;
    /**
     * @var string
     */
    private $response_langue;

    /**
     * Helper function to decode the xml to array
     */
    public function xmlToArray($response)
    {
        $xml = new \SimpleXMLElement($response);
        $objJsonDocument = json_encode($xml);
        return json_decode($objJsonDocument, true);
    }

    /**
     * Get form fields with MAC for request
     * @return array+
     */
    public function getFormFields()
    {
        $formFields = $this->getFormFieldsWithoutMac();

        $hmacComputer = new HmacComputer();
        $seal = $hmacComputer->sealFields($formFields, $this->getEptMac());
        $formFields['MAC'] = $seal;
        return $formFields;
    }

    /**
     * Get form fields without MAC for request
     * @return array
     */
    public function getFormFieldsWithoutMac()
    {
        $formFields = [];

        $formFields["TPE"] = $this->getEpt();
        $formFields["societe"] = $this->getCodeSociete();
        $formFields["version"] = $this->getEptVersion();
        $formFields["reference"] = $this->getReference();
        $formFields["date"] = $this->getDate()->format("d/m/Y");
        $formFields["montant"] = Helper::formatAmount($this->getAmount(), $this->getCurrency());

        return $formFields;
    }

    /**
     * check the request
     *
     * @param bool $is_sandbox
     *
     * @return array
     */
    public function check(bool $is_sandbox)
    {
        $url = ($is_sandbox ? PaymentEnvironment::SANDBOX_API_URI : PaymentEnvironment::API_URI) . '/' . PaymentEnvironment::PAYMENT_STATE;
        $response = $this->process($url);
        if (!$response['success']) {
            return [
                'success' => $response['success'],
                'request_value' => "",
                'response_value' => [
                    'desc' => $response['response'],
                    'rmk' => '( URL= ' . $url . ')',
                ]
            ];
        }

        try {
            $array_response = $this->xmlToArray($response['response']);
        } catch (\Exception  $e) {
            return [
                'success' => false,
                'request_value' => "",
                'response_value' => [
                    'desc' => '',
                    'rmk' => '',
                ]
            ];
        }
        $success = !isset($array_response['cdr']);
        $response_value = [];

        if (!$success) {
            if ($this->getResponseLangue() && isset(PaymentStateResponseValue::RESPONSE[$array_response['cdr']][$this->getResponseLangue()])) {
                $response_value = PaymentStateResponseValue::RESPONSE[$array_response['cdr']][$this->getResponseLangue()];
            } else {
                $response_value = PaymentStateResponseValue::RESPONSE[$array_response['cdr']][PaymentStateResponseValue::DEFAULT_LANGUAGE];
            }
        }

        $response_content = [
            'success' => $success,
            'request_value' => $array_response,
            'response_value' => $response_value,
        ];

        return $response_content;
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
        $this->ept = mb_substr($ept, 0, 7);
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
    public function getCleMac(): string
    {
        return $this->cle_mac;
    }

    /**
     * @param string $cle_mac
     */
    public function setCleMac(string $cle_mac)
    {
        $this->cle_mac = $cle_mac;
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
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $montant
     */
    public function setAmount(float $amount)
    {
        $this->amount = number_format($amount, 2, '.', '');
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $devise
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
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
        $this->response_langue = $response_langue;
    }

    /**
     * @return string
     */
    public function getEptMac(): string
    {
        return $this->ept_mac;
    }

    /**
     * @param string $ept_mac
     */
    public function setEptMac(string $ept_mac)
    {
        $this->ept_mac = mb_substr($ept_mac, 0, 40);
    }


}
