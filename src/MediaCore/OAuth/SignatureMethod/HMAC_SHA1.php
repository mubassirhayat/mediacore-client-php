<?php
/**
 * @category    MediaCore
 * @package     OAuth\SignatureMethod
 * @subpackage
 * @copyright   Copyright (c) 2014 MediaCore Technologies Inc. (http://www.mediacore.com)
 * @license
 * @version     Release:
 * @link        https://github.com/mediacore/mediacore-client-php
 */

namespace MediaCore\OAuth\SignatureMethod;

use MediaCore\OAuth\Consumer;
use MediaCore\OAuth\SignatureMethod\SignatureMethodInterface;

/**
 * A Signature Method that uses HMAC_SHA1
 * to encode a OAuth request base string
 * and a signing key.
 */
class HMAC_SHA1 implements SignatureMethodInterface
{
    /**
     * The signature name
     *
     * @var null|string
     */
    private $name;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'HMAC-SHA1';
    }

    /**
     * Get the signature name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the base64 encoded OAuth signature
     *
     * @param Consumer $consumer
     * @param string $baseString
     * @return string
     */
    public function buildSignature($consumer, $baseString)
    {
        $signingKey = rawurlencode($consumer->secret) . '&';
        return base64_encode(hash_hmac('sha1', $baseString, $signingKey,
            /* raw_output */ true));
    }
}
