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
    public $json;

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
        $this->json = $this->parseJson();
    }

    /**
     *
     * @param Response $rsponse
     * @param boolean $asArray
     * @return object|array|null
     */
    public function parseJson($asArray=false)
    {
        if (!isset($this->body)) {
            return null;
        }
        return json_decode($this->body, $asArray);
    }
}
