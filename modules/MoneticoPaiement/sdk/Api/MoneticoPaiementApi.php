<?php

namespace MoneticoPaiement\Sdk\Api;

class MoneticoPaiementApi
{
    /**
     * @var \CurlHandle|false
     */
    public $request;
    /**
     * @var bool
     */
    public $hasReturnTransfer = false;
    /**
     * @var bool|string Whatever curl_exec() has returned, so a string when the request was successful and returned data
     */
    public $response;

    public function __construct()
    {
        $this->setRequest(curl_init());

        curl_setopt_array($this->getRequest(), [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POST => true,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);
    }

    /**
     * Process and execute the request
     *
     * @param string $url
     * @param mixed  $fields
     * @param array  $header
     * @param string $authorization_value
     * @param string $method
     *
     * @return bool True when the response HTTP status is 200, false otherwise.
     */
    public function process(string $url, $fields, array $header, string $authorization_value = '', string $method = 'POST'): bool
    {
        $fields = is_array($fields) ? json_encode($fields) : $fields;
        // set options
        curl_setopt_array($this->getRequest(), [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $header,
        ]);

        if ($method === 'POST' || $method === 'PATCH') {
            $this->setCurlOpt(CURLOPT_POSTFIELDS, $fields);
        }

        if ($method === 'PATCH'){
            $this->setCurlOpt(CURLOPT_CUSTOMREQUEST, $method);
        }

        if ($authorization_value !== '') {
            $this->setCurlOpt(CURLOPT_USERPWD, $authorization_value);
        }
        // exec
        return $this->execute();
    }

    /**
     * Execute and close the request
     *
     * @return bool True when the response HTTP status is 200, false otherwise.
     */
    public function execute(): bool
    {
        $response = curl_exec($this->getRequest());
        $this->setResponse($response);
        if ($response === false) {
            $error_no = curl_errno($this->getRequest());
            $error_msg = curl_error($this->getRequest());
            throw new \UnexpectedValueException("Curl error $error_no - $error_msg");
        }
        return curl_getinfo($this->getRequest(), CURLINFO_HTTP_CODE) === 200;
    }

    /**
     * @param $option
     * @param $value
     *
     * @return void
     */
    public function setCurlOpt($option, $value)
    {
        curl_setopt($this->getRequest(), $option, $value);
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request): void
    {
        $this->request = $request;
    }

    /**
     * @return null|string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param bool|string $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function IsReturnTransfer(): bool
    {
        return $this->hasReturnTransfer;
    }

    /**
     * @param mixed $hasReturnTransfer
     */
    public function setIsReturnTransfer($hasReturnTransfer): void
    {
        $this->hasReturnTransfer = $hasReturnTransfer;
    }

    /**
     * @return void
     */
    public function close()
    {
        // close
        curl_close($this->getRequest());
    }
}