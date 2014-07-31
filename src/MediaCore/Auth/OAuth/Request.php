<?php
namespace MediaCore\Auth\OAuth;


/**
 * An Oauth Request
 *
 * @category    MediaCore
 * @package     MediaCore\Auth\OAuth\Request
 * @copyright   Copyright (c) 2014 MediaCore Technologies Inc. (http://www.mediacore.com)
 * @license
 * @version     Release:
 * @link        https://github.com/mediacore/mediacore-client-php
 */
class Request
{
    /**
     * The OAuth version
     *
     * @var string
     */
    const OAUTH_VERSION = '1.0';

    /**
     * The consumer
     *
     * @var null|OAuth\Consumer
     */
    private $consumer = null;

    /**
     * The uri
     *
     * @var null|string
     */
    private $url = null;

    /**
     * The request method
     *
     * @var null|string
     */
    private $method = null;

    /**
     * Constructor
     *
     * @param OAuth\Consumer $consumer
     * @param string $url
     * @param string $method
     * @param array $params
     */
    public function __construct($consumer, $url, $method, $params)
    {
        $this->consumer = $consumer;
        $this->method = $method;

        // All params are appended, even duplicates
        // FIXME
        $this->url = $url;
        if (strpos($this->url, '?') === false) {
            $this->url .= '?';
        } else {
            $this->url .= '&';
        }
        $this->url .= http_build_query($this->getOAuthParams(), PHP_QUERY_RFC3986) . '&';
        $this->url .= http_build_query($params, PHP_QUERY_RFC3986);
    }

    /**
     * Create the oauth signature method and signature string
     * and return the signed request url
     *
     * @param MediaCore\OAuth\SignatureMethod\HMAC_SHA1 $signatureMethod
     * @return array
     */
    public function signRequest($signatureMethod)
    {
        $this->url .= '&oauth_signature_method='. $signatureMethod->getName();
        $this->url .= '&oauth_signature=' . $signatureMethod->buildSignature(
            $this->consumer, $this->getBaseString());

        return $this->url;
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
     * Get the uri
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Encode the HTTP method, base URL, and parameter string into a
     * single string
     *
     * @return string
     */
    public function getBaseString() {
        $baseStrings = array();
        $baseStrings[] = strtoupper($this->method);

        $pos = strpos($this->url, '?');
        $baseUrl = substr($this->url, 0, $pos);

        $baseStrings[] = rawurlencode($baseUrl);

        $queryStr = substr($this->url, $pos + 1, -1);
        $encodedParamArray = explode('&', $queryStr);
        $baseStrings[] = rawurlencode(
            $this->toByteOrderedValueQueryString($encodedParamArray)
        );

        return implode('&', $baseStrings);
    }

    /**
     * Sort the encoded parameters by a "natural order"
     * algorithm (lexicographical byte value ordering).
     * http://oauth.net/core/1.0/ (Section 9.1.1)
     * Borrowed from ZF1.12:
     * @link Zend_OAuth_Signature_SignatureAbstract
     *
     * @param array $params
     * @return string
     */
    private function toByteOrderedValueQueryString($params)
    {
        uksort($params, 'strnatcmp');

        $pairs = array();
        foreach ($params as $key=>$value) {
            if (is_array($value)) {
                natsort($value);
                foreach ($value as $keyduplicate) {
                    $pairs[] = $key . '=' . $keyduplicate;
                }
            } else {
                $pairs[] = $key . '=' . $value;
            }
        }
        return implode('&', $pairs);
    }

    /**
     * Get the OAuth parameter defaults
     *
     * @return array
     */
    private function getOAuthParams() {
        return array(
            'oauth_version' => self::OAUTH_VERSION,
            'oauth_nonce' => $this->generateNonce(),
            'oauth_timestamp' => $this->generateTimestamp(),
            'oauth_consumer_key' => $this->consumer->getKey(),
        );
    }

    /**
     * Generate the oauth_nonce string
     *
     * @return string
     */
    private function generateNonce()
    {
        $mtime = microtime();
        $rand = mt_rand();
        return md5($mtime.$rand);
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
