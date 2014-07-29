<?php
namespace MediaCore\Http\Adapter;
use MediaCore\Http\Adapter\AdapterInterface;


/**
 * A Curl Adapter
 *
 * @category    MediaCore
 * @package     MediaCore\Http\Adapter\Curl
 * @copyright   Copyright (c) 2014 MediaCore Technologies Inc. (http://www.mediacore.com)
 * @license
 * @version     Release:
 * @link        https://github.com/mediacore/mediacore-client-php
 */
class Curl implements AdapterInterface
{
    /**
     * Default CURL_OPTS
     *
     * @type array
     */
    private $defaults = array(
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 4,
        CURLOPT_POST => false,
    );

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

        if (isset($options)) {
            $options = array_replace($this->defaults, (array)$options);
        }

        // Add the CURLOPT_URL
        // NOTE: Disallow passing the url via the $options arg
        $options[CURLOPT_URL] = $url;

        // Set POST request opts if necessary
        $options[CURLOPT_POST] = false;
        unset($options[CURLOPT_POSTFIELDS]);
        if (strtoupper($method) == 'POST' && isset($data)) {
            $options[CURLOPT_POST] = true;
            if (is_string($data)) {
                $options[CURLOPT_POSTFIELDS] = explode('&', $data);
            } else if (is_array($data)) {
                $options[CURLOPT_POSTFIELDS] = $data;
            } else {
                throw new InvalidArgumentException('"data" must be a string or an array');
            }
        }

        // Build the curl headers
        // NOTE: Disallow passing headers via the $options arg
        unset($options[CURLOPT_HTTPHEADER]);
        if (isset($headers)) {
            $options[CURLOPT_HTTPHEADER] = (array)$headers;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
