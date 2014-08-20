<?php
namespace MediaCore\Http;


class Client
{
    /**
     * GET method
     *
     * @var string
     */
    const GET = 'GET';

    /**
     * POST method
     *
     * @var string
     */
    const POST = 'POST';

    /**
     * WPUT
     *
     * @var string
     */
    const PUT = 'PUT';

    /**
     * DELETE method
     *
     * @var string
     */
    const DELETE = 'DELETE';

    /**
     * API base route
     *
     * @var string
     */
    const API_PATH = '/api2';

    /**
     *
     * @var string
     */
    private $url = '';

    /**
     *
     * @var Auth\AuthInterface
     */
    private $auth = null;

    /**
     *
     * @var Response
     */
    private $response = null;

    /**
     */
    public function __construct($url, $auth=null)
    {
        $this->url = rtrim($url, '/');
        $this->auth = $auth;
    }

    /**
     */
    public function getUrl()
    {
        $path = '';
        $args = func_get_args();
        if (is_array($args) && !empty($args)) {
            $path .= '/' . implode('/', $args);
        }
        return $this->url . $path;
    }

    /**
     */
    public function getApiUrl()
    {
        $path = self::API_PATH;
        $args = func_get_args();
        if (is_array($args) && !empty($args)) {
            $path .= '/' . implode('/', $args);
        }
        return $this->url . $path;
    }

    /**
     * @param array $params
     */
    public function getQuery($params)
    {
        return http_build_query($params, PHP_QUERY_RFC3986);
    }

    /**
     */
    public function get($url, $headers=array(), $options=array())
    {
        return $this->send($url, self::GET, null, $headers, $options);
    }

    /**
     */
    public function post($url, $data=array(), $headers=array(), $options=array())
    {
        return $this->send($url, self::POST, $data, $headers, $options);
    }

    /**
     */
    public function put($url, $data=array(), $headers=array(), $options=array())
    {
        return $this->send($url, self::PUT, $data, $headers, $options);
    }

    /**
     */
    public function delete($url, $headers=array(), $options=array())
    {
        return $this->send($url, self::DELETE, null, $headers, $options);
    }

    /**
     */
    public function send($url, $method=self::GET, $data=array(),
        $headers=array(), $options=array())
    {
        if (isset($this->auth)) {
            $options['auth'] = $this->auth;
        }
        $this->response = new Response(
            \Requests::request($url, $headers, $data, $method, $options)
        );
        return $this->response;
    }

    /**
     *
     * @param Response $rsponse
     * @param boolean $asArray
     * @return object|array|null
     */
    public function parseJson($response, $asArray=false)
    {
        if (!isset($response->body)) {
            throw new InvalidArgumentException(
                'The response object does not contain a body'
            );
        }
        return json_decode($response->body, $asArray);
    }
}
