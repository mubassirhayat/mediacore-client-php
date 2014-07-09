<?php
/**
 * @category    MediaCore
 * @package     OAuth
 * @copyright   Copyright (c) 2014 MediaCore Technologies Inc. (http://www.mediacore.com)
 * @license
 * @version     Release:
 * @link        http://developers.mediacore.com
 */

namespace MediaCore\OAuth;

use Zend\Uri\UriFactory;


/**
 * An OAuth Request
 */
class Request
{
    /**
     * The consumer
     *
     * @var null|Oauth\Consumer
     */
    private $consumer;

    /**
     * The uri
     *
     * @var null|Zend\Uri\Uri
     */
    private $uri;

    /**
     * The request method
     *
     * @var null|string
     */
    private $method;

    /**
     * The request params
     *
     * @var null|array
     */
    private $params;

    /**
     * The OAuth version
     *
     * @var string
     */
    const OAUTH_VERSION = '1.0';

    /**
     * Constructor
     *
     * @param Oauth\Consumer $consumer
     * @param string $url
     * @param string $method
     * @param array $params
     */
    public function __construct($consumer, $url, $method, $params)
    {
        $this->consumer = $consumer;
        $this->uri = UriFactory::factory($url);
        $this->method = strtoupper($method);
        $this->params = array_replace($this->getDefaults(), $params);

        // TODO parse the url for extra params?
    }

    /**
     * Create the oauth signature method and signature string
     *
     * @param SignatureMethod_HMAC_SHA1
     * @return array
     */
    public function signRequest($signatureMethod)
    {
        $this->params['oauth_signature_method'] = $signatureMethod->getName();
        $this->params['oauth_signature'] =  $signatureMethod->buildSignature(
            $this->consumer, $this->getBaseString());
        return $this->params;
    }

    /**
     * Get the OAuth version
     *
     * @return string
     */
    public function getOAuthVersion()
    {
        return self::OAUTH_VERSION;
    }

    /**
     * Get the params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Encode the HTTP method, base URL, and parameter string into a
     * single string
     *
     * @return string
     */
    public function getBaseString() {

        // get the url parts (scheme://host:port/path)
        $scheme = strtolower($this->uri->getScheme());
        $port = $this->uri->getPort();
        $host = $this->uri->getHost();
        $path = $this->uri->getPath();

        // build the base url (scheme://host:port)
        if (($scheme == 'http' && $port != '80') ||
            ($scheme == 'https' && $port != '443')) {
            $host = "$host:$port";
        }
        $baseUrl = "$scheme://$host";
        if (!empty($path)) {
            $baseUrl .= "/$path";
        }
        // build the percent encoded base string
        // http://tools.ietf.org/html/rfc3986#section-2.1
        $queryStr = strtoupper($this->method) . '&';
        $queryStr .= rawurlencode($baseUrl) . '&';
        $queryStr .= http_build_query(
            $this->params,
            $enc_type=PHP_QUERY_RFC3986
        );
        return $queryStr;
    }

    /**
     * Get the OAuth parameter defaults
     *
     * @return array
     */
    private function getDefaults() {
        return array(
            'oauth_version' => self::OAUTH_VERSION,
            'oauth_nonce' => $this->generateNonce(),
            'oauth_timestamp' => $this->generateTimestamp(),
            'oauth_consumer_key' => $this->consumer->key,
        );
    }

    /**
     * Generate the oauth_nonce string
     *
     * @return string
     */
    private function generateNonce()
    {
        $mt = microtime();
        $rand = mt_rand();
        return md5($mt.$rand);
    }

    /**
     * Generate the current timestamp
     *
     * @return string
     */
    private function generateTimestamp()
    {
        return time();
    }
}
