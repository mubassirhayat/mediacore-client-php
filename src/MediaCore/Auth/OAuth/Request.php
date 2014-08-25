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
     * @var null|\Zend\Uri\Uri
     */
    private $uri = null;

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
    public function __construct($consumer, $url, $method, $params=array())
    {
        $this->consumer = $consumer;
        $this->method = $method;

        $this->uri = new \MediaCore\Uri($url);
        $this->queryParams = $this->uri->getQueryAsArray();
        $this->oAuthParams = $this->getOAuthParams();
        $this->params = $params;
        $this->uri->setQuery('');
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
        $this->oAuthParams['oauth_signature_method'] = $signatureMethod->getName();
        $this->oAuthParams['oauth_signature'] = $signatureMethod->buildSignature(
            $this->consumer, $this->getBaseString());
        $uri = clone $this->uri;
        $queryParams = $this->concatQueryParams();
        $uri->setQuery($queryParams);
        return $uri->toString();
    }

    /**
     */
    public function getBaseUrl() {
        $scheme = $this->uri->getScheme();
        $host = $this->uri->getHost();
        $port = $this->uri->getPort();
        $path = $this->uri->getPath();

        $baseUrl = $scheme;
        $baseUrl .= '://' . $host;
        if (($scheme == 'http' && $port != '80') ||
            ($scheme == 'https' && $port != '443')) {
            $baseUrl .= ':' . $port;
        }
        if (!empty($path)) {
            $baseUrl .= $path;
        }
        return $baseUrl;
    }

    /**
     * Get the url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->uri->toString();
    }

    /**
     * Get the query string
     *
     * @return string
     */
    public function getQueryStr()
    {
        return $this->concatQueryParams();
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
     * Encode the HTTP method, base URL, and parameter string into a
     * single string
     *
     * @return string
     */
    private function getBaseString() {
        $baseStrings = array();

        //method
        $baseStrings[] = strtoupper($this->method);

        //base url (scheme://host:port/path)
        $baseUrl = $this->getBaseUrl();
        $baseStrings[] = rawurlencode($baseUrl);

        //query str
        $encodedParamArray = array();
        parse_str($this->concatQueryParams(), $encodedParamArray);
        $baseStrings[] = rawurlencode(
            $this->toByteOrderedValueQueryString($encodedParamArray)
        );
        $ret = implode('&', $baseStrings);
        return $ret;
    }

    /**
     * Append all params to the query str
     */
    private function concatQueryParams() {
        return \MediaCore\Uri::buildQuery(
            $this->queryParams,
            $this->oAuthParams,
            $this->params
        );
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
                foreach ($value as $dup) {
                    $pairs[] = $key . '=' . $dup;
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
