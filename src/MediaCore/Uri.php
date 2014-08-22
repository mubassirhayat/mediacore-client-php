<?php
namespace MediaCore;

use \Zend\Uri\Uri as Zend_Uri;

class Uri
{
    /**
     * The uri object
     *
     * @type null|Zend\Uri\Uri
     */
    private $uri = null;

    /**
     * Constructor
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->uri = new Zend_Uri($url);
        $this->uri->normalize();
    }

    /**
     * Encode and append a param to the url
     *
     * @param string $key
     * @param string $value
     * @return Uri
     */
    public function appendParam($key, $value)
    {
        return $this->appendParams(array($key=>$value));
    }

    /**
     * Encode and append a list of params to the url
     *
     * @param array $params Key/Value pairs
     * @return Uri
     */
    public function appendParams($params)
    {
        $queryStr = $this->uri->getQuery();
        if (!empty($queryStr)) {
            $queryStr .= '&';
        }
        $queryStr .= self::buildQuery($params);
        $this->uri->setQuery($queryStr);
        return $this;
    }

    /**
     * Append a path to the url
     *
     * @param string $path
     * @return Uri
     */
    public function appendPath($path)
    {
        $currPath = $this->uri->getPath();
        $currPath .= '/' . trim($path, '/') . '/';
        $this->uri->setPath($currPath);
        return $this;
    }

    /**
     * Replace all existing parameters with its value
     * encoded
     *
     * @param string $key
     * @param string $value
     * @return Uri
     */
    public function setParam($key, $value)
    {
        $params = $this->getQueryAsArray();
        if (array_key_exists($key, $params)) {
            if (is_array($params[$key])) {
                //replace all occurences
                foreach ($params[$key] as &$val) {
                    $val = $value;
                }
            } else {
                $params[$key] = $value;
            }
        } else {
            $params[$key] = $value;
        }
        $queryStr = self::buildQuery($params);
        $this->uri->setQuery($queryStr);
        return $this;
    }

    /**
     * Set the url query
     *
     * @param string $query
     */
    public function setQuery($query)
    {
        $this->uri->setQuery($query);
        return $this;
    }

    /**
     * Remove all occurences of a parameter
     *
     * @param string $key
     * @return Uri
     */
    public function removeParam($key)
    {
        $params = $this->getQueryAsArray();
        if (array_key_exists($key, $params)) {
            unset($params[$key]);
        }
        $queryStr = self::buildQuery($params);
        $this->uri->setQuery($queryStr);
        return $this;
    }

    /**
     * Get the url scheme
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->uri->getScheme();
    }

    /**
     * Get the url host (no port)
     *
     * @return string
     */
    public function getHost()
    {
        return $this->uri->getHost();
    }

    /**
     * Get the url port
     *
     * @return null|string
     */
    public function getPort()
    {
        return $this->uri->getPort();
    }

    /**
     * Get the url path
     *
     * @return null|string
     */
    public function getPath()
    {
        return $this->uri->getPath();
    }

    /**
     * Get the url fragment
     *
     * @return null|string
     */
    public function getFragment()
    {
        return $this->uri->getFragment();
    }

    /**
     * Get the url query
     *
     * @return null|string
     */
    public function getQuery()
    {
        return $this->uri->getQuery();
    }

    /**
     * Replacement for parse_str so that it doesn't use square
     * bracket notation for duplicate query params
     *
     * @return array
     */
    public function getQueryAsArray()
    {
        $queryStr = $this->getQuery();
        if (empty($queryStr)) {
            return array();
        }
        $pairs = explode('&', $queryStr);
        $result = array();
        foreach ($pairs as $p) {
            $kv = explode('=', $p, 2);
            $key = rawurldecode($kv[0]);
            $val = rawurldecode($kv[1]);
            if (array_key_exists($key, $result)) {
                if (is_array($result[$key])) {
                    array_push($result[$key], $val);
                } else {
                    $currVal = $result[$key];
                    $result[$key] = array($currVal,$val);
                }
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
    }

    /**
     * Get a query param's value(s)
     *
     * @param string $key
     * @return null|string|array
     */
    public function getParamValue($key)
    {
        $params = $this->getQueryAsArray();
        if (array_key_exists($key, $params)) {
            return $params[$key];
        }
        return null;
    }

    /**
     * Replacement for http_build_query so that it reliably percent-encodes
     * params and doesn't use the square bracket notation for duplicate
     *
     * @param array $params Array of key/value pairs
     * @return string
     */
    public static function buildQuery($params)
    {
        $result = array();
        foreach ($params as $key=>$values) {
            $encodedKey = rawurlencode($key);
            if (is_array($values)) {
                foreach ($values as $v) {
                    $encodedVal = rawurlencode($v);
                    $encodedPair = $encodedKey . '=' . $encodedVal;
                    array_push($result, $encodedPair);
                }
            } else {
                $encodedVal = rawurlencode($values);
                $encodedPair = $encodedKey . '=' . $encodedVal;
                array_push($result, $encodedPair);
            }
        }
        return implode('&', $result);
    }


    /**
     * Check if a url query param existso
     *
     * @param string $key
     */
    public function hasParam($key)
    {
        $val = $this->getParamValue($key);
        return isset($val);
    }

    /**
     * Compose the URI into a string
     *
     * @return string
     */
    public function toString()
    {
        return $this->uri->toString();
    }

    /**
     * Magic method to convert the URI to a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->uri->__toString();
    }
}
