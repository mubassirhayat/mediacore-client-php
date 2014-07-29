<?php
namespace MediaCore;

use MediaCore\Http\Adapter\Curl as CurlAdapter;
use MediaCore\Http\Client;
use MediaCore\OAuth\Consumer;
use MediaCore\OAuth\Request;
use MediaCore\OAuth\SignatureMethod\HMAC_SHA1;


/**
 * A basic LTI request builder
 *
 * @category    MediaCore
 * @package     MediaCore\Lti
 * @subpackage
 * @copyright   Copyright (c) 2014 MediaCore Technologies Inc. (http://www.mediacore.com)
 * @license
 * @version     Release:
 * @link        https://github.com/mediacore/mediacore-client-php
 */
class Lti
{
    /**
     * The API client
     *
     * @type Client
     */
    private $client;

    /**
     * The LTI consumer key
     *
     * @type string
     */
    private $key;

    /**
     * The LTI consumer secret
     *
     * @type string
     */
    private $secret;

    /**
     * Lti signature method
     *
     * @type null|HMAC_SHA1
     */
    private $signatureMethod;

    /**
     * The consumer
     *
     * @type null|Consumer
     */
    private $consumer;

    /**
     * The OAuth request
     *
     * @type null|Request
     */
    private $request;

    /**
     * The lti params
     *
     * @type null|array
     */
    private $params;

    /**
     * The request method to use
     *
     * @type null|string
     */
    private $method;

    /**
     * The LTI version
     *
     * @type null|string
     */
    const VERSION = 'LTI-1p0';

    /**
     * Constructor
     *
     * @param string $baseUrl
     * @param string $key
     * @param string $secret
     */
    public function __construct($baseUrl, $key, $secret)
    {
        $this->client = new Client($baseUrl, new CurlAdapter());
        $this->key = $key;
        $this->secret = $secret;
        $this->consumer = new Consumer($this->key, $this->secret);
        $this->signatureMethod = new HMAC_SHA1();
    }

    /**
     * Perform a GET request to an LTI-signed request url
     *
     * @return *
     */
    public function get($params, $endpoint='')
    {
        $requestUrl = $this->buildRequestUrl($params, $endpoint, 'GET');
        return $this->client->get($requestUrl);
    }

    /**
     * Perform a POST request to an LTI-signed request url
     *
     * @return *
     */
    public function post($params, $endpoint='')
    {
        $requestUrl = $this->buildRequestUrl($params, $endpoint, 'POST');
        $url = $this->request->getBaseUri()->toString();
        $params = $this->request->getParams();
        return $this->client->post($url, $params);
    }

    /**
     * Build the LTI request using LTI params passed in as arguments
     *
     * @param array $params
     * @param string $url
     * @param string $method
     * @param string $key
     * @param string $secret
     */
    public function buildRequestUrl($params, $endpoint='', $method='GET')
    {
        $this->request = new Request(
            $this->consumer,
            $this->client->getUrl($endpoint),
            $method,
            $params
        );
        return $this->request->signRequest($this->signatureMethod);
    }

    /**
     * Get the LTI version
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }
}
