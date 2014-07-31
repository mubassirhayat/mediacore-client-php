<?php
namespace MediaCore\Http;


class Response
{
    /**
     */
    public $body;

    /**
     */
    public $cookies = array();

    /**
     */
    public $headers = array();

    /**
     */
    public $statusCode;

    /**
     */
    public $success;

    /**
     */
    public $url;

    /**
     */
    private $response;

    /**
     */
    public function __construct(\Requests_Response $response)
    {
        $this->response = $response;
        $this->body = $response->body;
        $this->cookies = $response->cookies;
        $this->headers = $response->headers;
        $this->statusCode = $response->status_code;
        $this->success = $response->success;
        $this->url = $response->url;
    }
}
