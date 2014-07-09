<?php
/**
 *
 * @category    MediaCore
 * @package     Http\Adapter
 * @copyright   Copyright (c) 2014 MediaCore Technologies Inc. (http://www.mediacore.com)
 * @license
 * @version     Release:
 * @link
 */

namespace MediaCore\Http\Adapter;

use MediaCore\Http\Adapter\AdapterInterface;

class Curl implements AdapterInterface
{
    private $defaults = array(
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 4,
        CURLOPT_POST => false,
        CURLOPT_SSL_VERIFYHOST => false,//TODO remove
        CURLOPT_SSL_VERIFYPEER => false,//TODO remove
    );

    const ENC_URLENCODED = 'application/x-www-form-urlencoded'; //TODO

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Send a curl request
     *
     * @param string $url
     * @param string $method
     * @param array|string $data
     * @param array $options
     * @param array $headers
     * @return string|boolean
     */
    public function send($url, $method='GET', $data=null,
            $options=array(), $headers=array()) {

        $options = array_replace($this->defaults, $options);

        // Add the CURLOPT_URL
        // NOTE: Disallow passing the url via the $options arg
        $options[CURLOPT_URL] = $url;

        // Set POST request opts if necessary
        $options[CURLOPT_POST] = false;
        unset($options[CURLOPT_POSTFIELDS]);
        if ($method == 'POST' && isset($data)) {
            $options[CURLOPT_POST] = true;
            if (is_string($data)) {
                $options[CURLOPT_POSTFIELDS] = $data;
            } else if (is_array($data)) {
                $options[CURLOPT_POSTFIELDS] = implode('&', $data);
            }
        }

        // Build the curl headers
        // NOTE: Disallow passing headers via the $options arg
        unset($options[CURLOPT_HTTPHEADER]);
        if (!empty($headers)) {
            $options[CURLOPT_HTTPHEADER] = (array)$headers;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
