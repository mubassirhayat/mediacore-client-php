<?php
namespace MediaCore\Http;
use MediaCore\Http\Adapter\AdapterInterface as AdapterInterface;


/**
 * HTTP Client
 *
 * @category    MediaCore
 * @package     MediaCore\Http\Client
 * @subpackage
 * @copyright   Copyright (c) 2014 MediaCore Technologies Inc. (http://www.mediacore.com)
 * @license
 * @version     Release:
 * @link        https://github.com/mediacore/mediacore-client-php
 */
class Client
{
    /**
     * The base url
     *
     * @type string
     */
    private $baseUrl;

    /**
     * An adapter that will handle requests to url endpoints
     *
     * @type AdapterInterface
     */
    private $adapter;

    /**
     * Constructor
     *
     * @param string $baseUrl
     * @param AdapterInterface $adapter
     */
    public function __construct($baseUrl, $adapter)
    {
        $this->baseUrl = $baseUrl;
        $this->adapter = $adapter;
    }

    /**
     * Get a constructed path from supplied
     * path segments
     *
     * @param string ...
     * @return string
     */
    public function getUrl()
    {
        $args = func_get_args();
        $url = $this->baseUrl;
        if (is_array($args) && !empty($args)) {
            $url .= '/' . implode('/', $args);
        }
        return $url;
    }

    /**
     * Percent encode (RFC3986) the query params values
     *
     * @param string $params
     * @return array
     */
    public function getQuery($params)
    {
        $encodedParams = '';
        foreach ($params as $k => $v) {
            $encodedParams .= rawurlencode($k) . '=';
            $encodedParams .= rawurlencode($v) . "&";
        }
        return substr($encodedParams, 0, -1);
    }

    /**
     * Send a GET curl request
     *
     * @param string $url
     * @param array $options
     * @param array $headers
     * @return mixed
     */
    public function get($url, $options=array(), $headers=array()) {
        return $this->send($url, 'GET', null, $options, $headers);
    }

    /**
     * Send a POST curl request
     *
     * @param string $url
     * @param array $data
     * @param array $options
     * @param array $headers
     * @return mixed
     */
    public function post($url, $data, $options=array(), $headers=array()) {
        return $this->send($url, 'POST', $data, $options, $headers);
    }

    /**
     * Send an adapter request
     *
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $options
     * @param array $headers
     * @return string|boolean
     */
    private function send($url, $method, $data=null,
            $options=array(), $headers=array()) {

        return $this->adapter->send($url, $method, $data,
            $options, $headers);
    }
}
