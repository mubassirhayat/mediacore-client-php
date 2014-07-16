<?php
namespace MediaCore\Http\Adapter;


/**
 * The http adapter interface
 *
 * @category    MediaCore
 * @package     MediaCore\Http\Adapter\AdapterInterface
 * @copyright   Copyright (c) 2014 MediaCore Technologies Inc. (http://www.mediacore.com)
 * @license
 * @version     Release:
 * @link        https://github.com/mediacore/mediacore-client-php
 */
interface AdapterInterface
{
    /**
     * Send a request
     *
     * @param string $url
     * @param string $method
     * @param array|string $data
     * @param array $options
     * @param array $headers
     * @return string|boolean
     */
    public function send($url, $method='GET', $data=null,
            $options=array(), $headers=array());
}
