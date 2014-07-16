<?php
namespace MediaCore\OAuth;


/**
 * A basic oauth consumer
 *
 * @category    MediaCore
 * @package     MediaCore\OAuth\Consumer
 * @copyright   Copyright (c) 2014 MediaCore Technologies Inc. (http://www.mediacore.com)
 * @license
 * @version     Release:
 * @link        https://github.com/mediacore/mediacore-client-php
 */
class Consumer
{
    /**
     * The consumer key
     *
     * @type string
     */
    public $key;

    /**
     * The consumer secret
     *
     * @type string
     */
    public $secret;

    /**
     * Constructor
     *
     * @param string $key
     * @param string $secret
     */
    function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Get the consumer key
     *
     * @return string
     */
    function getKey()
    {
        return $this->key;
    }

    /**
     * Get the consumer secret
     *
     * @return string
     */
    function getSecret()
    {
        return $this->secret;
    }
}
