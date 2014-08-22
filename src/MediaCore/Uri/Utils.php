<?php
namespace MediaCore\Uri;

use \Zend\Uri\Uri as Zend_Uri;

class Utils
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
     */
    public function appendParam($key, $value)
    {
        return $this->appendParams(array($key=>$value));
    }

    /**
     * Encode and append a list of params to the url
     */
    public function appendParams($params)
    {
        $queryStr = $this->uri->getQuery();
        if (!empty($queryStr)) {
            $queryStr .= '&';
        }
        $queryStr .= self::buildQuery($params);
        $this->uri->setQuery($queryStr);
        return $this->toString();
    }

    /**
     */
    public function appendPath($path)
    {
        $currPath = $this->uri->getPath();
        $currPath .= '/' . trim($path, '/') . '/';
        $this->uri->setPath($currPath);
        return $this->toString();
    }

    /**
     * Replace all existing parameters with its value
     * encoded
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
        return $this->toString();
    }

    /**
     */
    public function setQuery($query)
    {
        $this->uri->setQuery($query);
        return $this->toString();
    }

    /**
     * Remove all occurences of a parameter
     */
    public function removeParam($key)
    {
        $params = $this->getQueryAsArray();
        if (array_key_exists($key, $params)) {
            unset($params[$key]);
        }
        $queryStr = self::buildQuery($params);
        $this->uri->setQuery($queryStr);
        return $this->toString();
    }

    /**
     */
    public function getScheme()
    {
        return $this->uri->getScheme();
    }

    /**
     */
    public function getHost()
    {
        return $this->uri->getHost();
    }

    /**
     */
    public function getPort()
    {
        return $this->uri->getPort();
    }

    /**
     */
    public function getPath()
    {
        return $this->uri->getPath();
    }

    /**
     */
    public function getFragment()
    {
        return $this->uri->getFragment();
    }

    /**
     */
    public function getQuery()
    {
        return $this->uri->getQuery();
    }

    /**
     * Replacement for parse_str so that it doesn't use the square
     * bracket notation
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
     * @return string|array
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
     * Replacement for http_build_query so that it
     * doesn't use the square bracket notation for duplicate
     * query params and reliably percent-encodes params
     *
     * @param array $params An associative array of key/value pairs
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
     */
    public function hasParam($key)
    {
        $val = $this->getParamValue($key);
        return isset($val);
    }

    /**
     */
    public function toString()
    {
        return $this->uri->toString();
    }
}
