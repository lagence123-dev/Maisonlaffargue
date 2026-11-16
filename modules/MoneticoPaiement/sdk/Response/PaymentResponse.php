<?php

namespace MoneticoPaiement\Sdk\Response;

use MoneticoPaiement\Sdk\Utilities\HmacComputer;

class PaymentResponse
{
    /**
     * @var string
     */
    private string $mac;
    /**
     * @var string
     */
    private string $cle_mac;
    /**
     * @var string
     */
    private string $code_retour;
    /**
     * @var array
     */
    private array $data;
    /**
     * @var array
     */
    private array $response;
    /**
     * @var bool
     */
    private bool $is_seal_validated;
    /**
     * @var array
     */
    private array $error_data = [];
    /**
     * @var int
     */
    private int $cdr_return_code;

    /**
     * PaymentResponse constructor.
     *
     * @param array $response
     * @param string $cle_mac
     */
    public function __construct(array $response, string $cle_mac)
    {
        if (isset($response['MAC'])) {
            $this->setMac($response['MAC']);
            unset($response['MAC']);
        }

        if (isset($response['code-retour'])) {
            $this->setCodeRetour($response['code-retour']);
        }

        $rawurldecodeIfString = function ($value) {
            if (!is_string($value)) { // typically array
                return $value;
            }

            return rawurldecode($value);
        };

        $this->setData(array_map($rawurldecodeIfString, $response));
        $this->setCleMac($cle_mac);
        $this->setResponse($this->checkResponse());
    }

    /**
     * Return if payment is from a sandbox mode
     * @return bool
     */
    public function isSandboxPayment(): bool
    {
        $isSandBox = false;
        if (isset($this->getData()['code-retour'])) {
            $isSandBox = $this->getData()['code-retour'] === "payetest";
        }
        return $isSandBox;
    }

    /**
     * Return if payment is validated
     * @return bool
     */
    public function isPaymentValidated(): bool
    {
        $isValidated = false;
        if (!is_null($this->getCodeRetour())) {
            $isValidated = mb_substr(strtolower($this->getCodeRetour()), 0, strlen("paiement")) === "paiement";
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
            $isPending = $this->getCodeRetour() === 'attente_partenaire';
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
            $isRefused = $this->getData()['modepaiement'] === 'loan' &&
                $this->getCodeRetour() === 'Annulation';
        }
        return $isRefused;
    }

    /**
     * Return if split payment
     * @return false|int
     */
    public function isSplitPayment()
    {
        $position = 0;
        if (!is_null($this->getCodeRetour())) {
            $position = strpos(strtolower($this->getCodeRetour()), "_pf");
        }
        return $position;
    }

    /**
     * Get the response data
     * @return array
     */
    public function checkResponse(): array
    {
        $isSealValidated = false;
        $payment_result = [];

        if (!is_null($this->getMac()) && !is_null($this->getCleMac()) && is_array($this->getData())) {
            // check if reencoding is the same
            $isSealValidated = (new HmacComputer())->validateSeal($this->getData(), $this->getCleMac(), $this->getMac());

            /* code retour values =>
             * payetest
             * paiement
             * Annulation
             * paiement_pf[N]
             * Annulation_pf[N]
             * N = { 2;3;4 }
             * attente_partenaire
             * acceptance_successful
             * acceptance_pending
             * acceptance_refused
             */
            if ($isSealValidated) {
                $instalmentNumber = 1;
                if ($this->isSplitPayment() && !is_null($this->getCodeRetour())) {
                    $code_retour = $this->getCodeRetour();
                    $instalmentNumber = mb_substr($code_retour, strpos($code_retour, '_pf') + 3);
                }

                $payment_result = [
                    'is_payment_validated' => $this->isPaymentValidated(),
                    'is_payment_pending' => $this->isPaymentPending(),
                    'is_payment_refused' => $this->isPaymentRefused(),
                    'is_sandbox_payment' => $this->isSandboxPayment(),
                    'is_split_payment' => $this->isSplitPayment(),
                    'split_payment_number' => $instalmentNumber
                ];
            }
        }

        $this->setIsSealValidated($isSealValidated);

        return $payment_result;
    }

    /**
     * The output has to exactly be "version=2\ncdr=0" to be OK. Anything else will be interpreted as KO.
     * @return string
     */
    public function sendResponse(): string
    {
        header("Content-Type: text/plain");

        $response = "version=2\n";
        if (!$this->getIsSealValidated()) {
            return $response . "cdr=1\nerror=invalid seal";
        }

        $cdr = 0;
        if (isset($cdr_return_code)) {
            $cdr = $this->cdr_return_code;
        } else if ($this->error_data !== []) {
            $cdr = 1;
        }

        $response .= "cdr=$cdr";

        if ($cdr === 0) {
            return $response;
        }

        if ($this->error_data !== []) {
            foreach ($this->error_data as $key => $value) {
                $response .= "\n$key=$value";
            }
        }

        return $response;
    }

    /**
     * @return string|null
     */
    public function getMac(): ?string
    {
        return $this->mac;
    }

    /**
     * @param string $mac
     */
    public function setMac(string $mac)
    {
        $this->mac = $mac;
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
    public function getCodeRetour(): string
    {
        return $this->code_retour;
    }

    /**
     * @param string $code_retour
     */
    public function setCodeRetour(string $code_retour)
    {
        $this->code_retour = $code_retour;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * @param array $response
     */
    public function setResponse(array $response): void
    {
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function getIsSealValidated(): bool
    {
        return $this->is_seal_validated;
    }

    /**
     * @param bool $is_seal_validated
     */
    public function setIsSealValidated(bool $is_seal_validated): void
    {
        $this->is_seal_validated = $is_seal_validated;
    }

    public function setErrorData(array $error_data): void
    {
        $this->error_data = $error_data;
    }

    public function setCdrReturnCode(int $cdr_return_code): void
    {
        $this->cdr_return_code = $cdr_return_code;
    }
}