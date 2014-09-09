<?php
namespace MediaCore\Http;


/**
 * A basic HTTP Response based on Requests_Response
 *
 * @category    MediaCore
 * @package     MediaCore\Http\Response
 * @subpackage
 * @copyright   Copyright (c) 2014 MediaCore Technologies Inc.
 *              (http://www.mediacore.com)
 * @license
 * @version     Release:
 * @link        https://github.com/mediacore/mediacore-client-php
 */
class Response
{
    /**
     * The response body
     *
     * @type string
     */
    public $body;

    /**
     * The response cookies
     *
     * @type array
     */
    public $cookies = array();

    /**
     * The response headers
     *
     * @type array
     */
    public $headers = array();

    /**
     * The response status code
     *
     * @type number
     */
    public $statusCode;

    /**
     * Whether the response was a 201
     *
     * @type bookean
     */
    public $success;

    /**
     * The response url
     *
     * @type string
     */
    public $url;

    /**
     * The response json
     *
     * @type object
     */
    public $json;

    /**
     * Constructor
     *
     * @param Requests_Response $response
     */
    public function __construct(\Requests_Response $response)
    {
        $this->body = $response->body;
        $this->cookies = $response->cookies;
        $this->headers = $response->headers;
        $this->statusCode = $response->status_code;
        $this->success = $response->success;
        $this->url = $response->url;
        $this->json = $this->parseJson();
    }

    /**
     *
     * @return object|null
     */
    private function parseJson()
    {
        if (!isset($this->body)) {
            return null;
        }
        return json_decode($this->body);
    }
}
