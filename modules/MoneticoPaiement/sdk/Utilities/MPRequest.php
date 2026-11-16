<?php

namespace MoneticoPaiement\Sdk\Utilities;

trait MPRequest
{
    /**
     * @param $url
     *
     * @return array [bool/string]
     */
    public function process($url)
    {
        $request = curl_init();
        // set options
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, http_build_query($this->getFormFields()));
        // max time = 5000 ms
        curl_setopt($request, CURLOPT_TIMEOUT_MS, 5000);
        // exec
        $response = curl_exec($request);
        if (!$response) {
            return [
                'success' => $response,
                'response' => curl_error($request)
            ];
        }
        curl_close($request);
        return [
            'success' => true,
            'response' => $response
        ];
    }
}