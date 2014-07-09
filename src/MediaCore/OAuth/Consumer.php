<?php
/**
 *
 * @category    MediaCore
 * @package     Consumer
 * @copyright   Copyright (c) 2014 MediaCore Technologies Inc. (http://www.mediacore.com)
 * @license
 * @version     Release:
 * @link
 */

namespace MediaCore\OAuth;

/**
 */
class Consumer
{
    public $key;
    public $secret;

    /**
     * TODO implement callback url
     */
    function __construct($key, $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    function getKey()
    {
        return $this->key;
    }

    function getSecret()
    {
        return $this->secret;
    }
}



