<?php

namespace MoneticoPaiement\Sdk\Utilities;

class HmacComputer
{
    /**
     * Seal fields
     *
     * @param array  $fields
     * @param string $key
     *
     * @return string
     *
     */
    public function sealFields(array $fields, string $key)
    {
        $stringToSeal = $this->getStringToSeal($fields);
        return $this->sealString($stringToSeal, $this->getUsableKey($key));
    }

    /**
     * Check whether the expected seal fields value is the same with fields passed in the parameters
     *
     * @param array  $fields
     * @param string $key
     * @param string $expectedSeal
     *
     * @return bool
     */
    public function validateSeal(array $fields, string $key, string $expectedSeal): bool
    {
        if (count($fields) > 0) {
            $computedSeal = $this->sealFields($fields, $key);
            return strtoupper($computedSeal) === strtoupper($expectedSeal);
        }
        return false;
    }

    /**
     * Check whether the expected seal value is the same with json passed in the parameters
     *
     * @param string $json
     * @param string $key
     * @param string $expectedSeal
     *
     * @return bool
     */
    public function validateJsonSeal(string $json, string $key, string $expectedSeal): bool
    {
        if (!empty($json)) {
            $computedSeal = $this->sealString($json, $key);
            return strtoupper($computedSeal) === strtoupper($expectedSeal);
        }
        return false;
    }

    /**
     * @param array $formFields
     *
     * @return string
     */
    public function getStringToSeal(array $formFields)
    {
        // The string to be sealed is composed of all the form fields sent
        // 1. ordered alphabetically (numbers first, then capitalized letter, then other letters)
        // 2. represented using the format key=value
        // 3. separated by "*" character
        // Please refer to technical documentation for more details
        ksort($formFields);
        array_walk($formFields, function (&$item, $key) {
            $item = "$key=$item";
        });
        return implode('*', $formFields);
    }

    /**
     * @param string $stringToSeal
     * @param string $key
     *
     * @return false|string
     */
    public function sealString(string $stringToSeal, string $key)
    {
        $usableKey = $this->getUsableKey($key);
        $MAC = hash_hmac(
            "sha1",
            $stringToSeal,
            hex2bin($usableKey)
        );

        return $MAC;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    private function getUsableKey(string $key)
    {
        $hexStrKey = mb_substr($key, 0, 38);
        $hexFinal = "" . mb_substr($key, 38, 2) . "00";

        $cca0 = ord($hexFinal[0]);

        if ($cca0 > 70 && $cca0 < 97) {
            $hexStrKey .= chr($cca0 - 23) . mb_substr($hexFinal, 1, 1);
        } else {
            if (mb_substr($hexFinal, 1, 1) === "M") {
                $hexStrKey .= mb_substr($hexFinal, 0, 1) . "0";
            } else {
                $hexStrKey .= mb_substr($hexFinal, 0, 2);
            }
        }

        return $hexStrKey;
    }
}

?>