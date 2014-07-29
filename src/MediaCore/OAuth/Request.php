<?php
namespace MediaCore\OAuth;
use Zend\Uri\UriFactory;


/**
 * An Oauth Request
 *
 * @category    MediaCore
 * @package     MediaCore\OAuth\Request
 * @subpackage
 * @copyright   Copyright (c) 2014 MediaCore Technologies Inc. (http://www.mediacore.com)
 * @license
 * @version     Release:
 * @link        https://github.com/mediacore/mediacore-client-php
 */
class Request
{
    /**
     * The consumer
     *
     * @var null|OAuth\Consumer
     */
    private $consumer;

    /**
     * The uri
     *
     * @var null|Zend\Uri\Uri
     */
    private $baseUri;

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
     * @param OAuth\Consumer $consumer
     * @param string $url
     * @param string $method
     * @param array $params
     */
    public function __construct($consumer, $url, $method, $params)
    {
        $this->consumer = $consumer;
        $this->method = $method;

        // NOTE: strip off the url query parameters and store
        // them separately.
        // Query params in the url supercede those passed
        // in as args
        $this->baseUri = UriFactory::factory($url);
        $this->baseUri = $this->normalizeUri($this->baseUri);
        $queryParams = $this->baseUri->getQueryAsArray();
        $this->params = array_replace(
            $this->getOAuthParams(),
            $params,
            $queryParams
        );
    }

    /**
     * Create the oauth signature method and signature string
     *
     * @param MediaCore\OAuth\SignatureMethod\HMAC_SHA1 $signatureMethod
     * @return array
     */
    public function signRequest($signatureMethod)
    {
        $this->params['oauth_signature_method'] = $signatureMethod->getName();
        $uri = clone $this->baseUri;
        $uri->setQuery($this->params);

        // Add the oauth_signature after setting the query params so its
        // value isn't url encoded
        $this->params['oauth_signature'] = $signatureMethod->buildSignature(
            $this->consumer, $this->getBaseString());
        $url = $uri->toString() . '&oauth_signature=' . $this->params['oauth_signature'];

        return $url;
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
     * @return Zend\Uri\Uri
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * Get the parameters
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
        // remove the oauth_signature if it exists
        // in the params array
        if (isset($this->params['oauth_signature'])) {
            unset($this->params['oauth_signature']);
        }
        // build the percent encoded base string
        $baseStrings = array();
        $baseStrings[] = strtoupper($this->method);

        // The url has been normalized at this stage
        $baseStrings[] = rawurlencode($this->baseUri->toString());
        $encodedParams = $this->rawurlencodeKeyValuePairs($this->params);
        $baseStrings[] = rawurlencode(
            $this->toByteOrderedValueQueryString($encodedParams)
        );
        $queryStr = implode('&', $baseStrings);
        return $queryStr;
    }

    /**
     * Percent encode an associative array of parameters
     *
     * @param array $params
     * @return array
     */
    private function rawurlencodeKeyValuePairs($params)
    {
        $encodedParams = array();
        foreach ($this->params as $key => $value) {
            $encodedParams[rawurlencode($key)] =
                rawurlencode($value);
        }
        return $encodedParams;
    }

    /**
     * Normalize the base url
     * Remove any query parameters in the url and
     * return just its scheme://host:port/path
     * Borrowed from ZF1.12:
     * @link Zend_OAuth_Signature_SignatureAbstract
     *
     * @param Zend\Uri\Uri $uri
     * @return Zend\Uri\Uri
     */
    private function normalizeUri($uri)
    {
        if ($uri->getScheme() == 'http' && $uri->getPort() == '80') {
            $uri->setPort('');
        } elseif ($uri->getScheme() == 'https' && $uri->getPort() == '443') {
            $uri->setPort('');
        }
        $uri->setQuery('');
        $uri->setFragment('');
        $uri->setHost(strtolower($uri->getHost()));
        return $uri;
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
