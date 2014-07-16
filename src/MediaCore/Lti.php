<?php
namespace MediaCore;
use MediaCore\OAuth\SignatureMethod\HMAC_SHA1;
use MediaCore\OAuth\Consumer;
use MediaCore\OAuth\Request;


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
     */
    public function __construct()
    {
        $this->signatureMethod = new HMAC_SHA1();
    }

    /**
     * Build the LTI request including the OAuth parameters
     *
     * @param array $params
     * @param string $url
     * @param string $method
     * @param string $key
     * @param string $secret
     */
    public function buildRequestUrl($params, $url, $method='GET', $key, $secret)
    {
        $this->params = $params;
        $this->url = $url;
        $this->method = $method;
        $this->consumer = new Consumer($key, $secret);
        $this->request = new Request(
            $this->consumer,
            $this->url,
            $this->method,
            $this->params
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
