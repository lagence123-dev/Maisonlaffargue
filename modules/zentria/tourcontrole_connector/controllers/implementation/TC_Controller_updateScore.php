<?php
/**
 *  Copyright 2025 Zentria
 *
 *  @author    Zentria <zentria@profileo.com>
 *  @copyright 2025 Zentria
 *  @license   Commercial
 *  link      https://zentria.profileo.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Update configuration score values
 *
 * @author      zentria
 */
class TC_Controller_updateScore extends TC_Controller
{
    /**
     * Main method, call in connector.php
     */
    public function getContent()
    {
        // Check parameters
        if (!Tools::getIsset('encrypted_data')) {
            return $this->prepareData(true, 'No encrypted data found to update score configuration values');
        }

        // Decrypt and decode data score
        $contentDecrypted = Zentria_AesCipher::decrypt(
            Tools::getValue('encrypted_data'),
            Configuration::getGlobalValue('ZENTRIA_TOKEN_THIRD')
        );
        $decrypted_data = json_decode($contentDecrypted, true);

        // Update the token, the server has just updated it
        if (isset($decrypted_data['ZENTRIA_TOKEN_SERV']) && $decrypted_data['ZENTRIA_TOKEN_SERV']) {
            Configuration::updateGlobalValue('ZENTRIA_TOKEN_SERV', $decrypted_data['ZENTRIA_TOKEN_SERV']);
            unset($decrypted_data['ZENTRIA_TOKEN_SERV']);
        }

        // Update configuration values
        foreach ($decrypted_data as $config_key => $config_value) {
            // Skip when this config key is disallowed
            if (0 !== strpos($config_key, 'ZENTRIA_')) {
                continue;
            }

            Configuration::updateGlobalValue($config_key, $config_value);
        }

        // If we have a scan score, this means the subscription is active
        Configuration::updateGlobalValue('ZENTRIA_SUBSCRIPTION_ACTIVE', '1');

        // Format returned data
        return $this->prepareData();
    }

    /**
     * Prepare all data for return (true or false state)
     *
     * @since 1.3.0
     *
     * @param $error bool
     *
     * @return array all data to get
     */
    public function prepareData($error = false, $error_msg = '')
    {
        return [
            'error' => $error,
            'error_msg' => $error_msg,
            'message' => $error ?: 'Configuration values updated',
        ];
    }
}
