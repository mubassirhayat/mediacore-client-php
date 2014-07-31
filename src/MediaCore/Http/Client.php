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
        $args = func_get_args();
        $url = $this->url;
        if (is_array($args) && !empty($args)) {
            $url .= '/' . implode('/', $args);
        }
        return $url;
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
        $this->send($url, self::GET, null, $headers, $options);
    }

    /**
     */
    public function post($url, $data=array(), $headers=array(), $options=array())
    {
        $this->send($url, self::POST, $data, $headers, $options);

    }

    /**
     */
    public function put($url, $data=array(), $headers=array(), $options=array())
    {
        $this->send($url, self::PUT, $data, $headers, $options);
    }

    /**
     */
    public function delete($url, $headers=array(), $options=array())
    {
        $this->send($url, self::DELETE, null, $headers, $options);
    }

    /**
     */
    public function send($url, $method=self::GET, $data=array(),
        $headers=array(), $options=array())
    {
        if (isset($this->auth)) {
            $options['auth'] = $this->auth;
        }
        $response = new Response(
            \Requests::request($url, $headers, $data, $method, $options)
        );

        \Psy\Shell::debug(get_defined_vars());

        $this->response = $response;
        return $this->response;
    }

    /**
     *
     * @return Response $rsponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     *
     * @param Response $rsponse
     * @param boolean $assoc
     * @return object|array|null
     */
    public function parseJson($response, $assoc=true)
    {
        if (!isset($response->body)) {
            throw new InvalidArgumentException(
                'The response object does not contain a body!'
            );
        }
        return json_decode($response->body, $assoc);
    }
}
