<?php
/**
 *  @author    Amazzing <modules@mirindevo.com>
 *  @copyright Amazzing
 *  @license   https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class OptimizerModel
{
    public $id = 0;
    public $active = 0;
    public $label = 'None';
    public $fields = [];
    public $supported_formats = [];
    public $errors = [];

    public function __construct($saved_data = [])
    {
        $this->applySavedData($saved_data);
    }

    public function applySavedData($saved_data)
    {
        if (isset($saved_data['settings'])) {
            $saved_data['settings'] = json_decode($saved_data['settings'], true);
            foreach (array_keys($this->fields) as $k) {
                if (isset($saved_data['settings'][$k])) {
                    $this->fields[$k]['value'] = $saved_data['settings'][$k];
                }
            }
        }
        $this->active = !empty($saved_data['active']) ? 1 : 0;
    }

    public function process($src_path, $dst_path)
    {
        if (in_array($this->getImageFormat($src_path), $this->supported_formats)
            && method_exists($this, 'processSupportedFormat')) {
            $ret = $this->processSupportedFormat($src_path, $dst_path);
        } else {
            $ret = (bool) Tools::copy($src_path, $dst_path);
        }

        return $ret ?: $this->label . ': ' . (!empty($this->errors) ? implode('<br>', $this->errors) : 'error');
    }

    public function getImageFormat($file_path)
    {
        return str_replace('image/', '', BannerImg::getMime($file_path));
    }

    public function curlRequest($data, $decode_response = true)
    {
        $response = $decode_response ? [] : '';
        if (function_exists('curl_init')) {
            if (!empty($data['get_fields'])) {
                if (is_array($data['get_fields'])) {
                    $data['get_fields'] = http_build_query($data['get_fields']);
                }
                $data['url'] .= '?' . $data['get_fields'];
            }
            $session = curl_init();
            curl_setopt($session, CURLOPT_URL, $data['url']);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0); // debug
            // curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0); // debug
            if (!empty($data['headers'])) {
                curl_setopt($session, CURLOPT_HTTPHEADER, $data['headers']);
            }
            if (!empty($data['post_fields'])) {
                curl_setopt($session, CURLOPT_POSTFIELDS, $data['post_fields']);
            }
            if (!empty($data['login:password'])) {
                curl_setopt($session, CURLOPT_USERPWD, $data['login:password']);
            }
            if (!empty($data['timeout'])) {
                curl_setopt($session, CURLOPT_CONNECTTIMEOUT, $data['timeout']);
            }
            $response = curl_exec($session);
            $possible_error = curl_error($session);
            curl_close($session);
            if ($possible_error) {
                $this->errors[] = $possible_error;
            } else {
                $response = $decode_response ? json_decode($response, true) : $response;
            }
        } else {
            $this->errors[] = 'no_curl';
        }

        return $response;
    }
}
