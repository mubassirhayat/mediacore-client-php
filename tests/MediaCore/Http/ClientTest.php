<?php
namespace MediaCore\Http;

use MediaCore\Http\Adapter\Curl;

/**
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Consumer
     */
    protected $client;

    /**
     */
    protected function setUp()
    {
        $this->url = 'http://localhost:8080';
        $this->client = new Client($this->url);
    }

    /**
     */
    protected function tearDown()
    {
    }

    /**
     */
    public function testApiUrl()
    {
        $apiUrl = $this->client->apiUrl('api2', 'media');
        $expectedValue = $this->url . '/api2/media';
        $this->assertEquals($expectedValue, $apiUrl);
    }

    /**
     */
    public function testGetQuery()
    {
        $queryParams = array(
            'one' => 'firstvalue',
            'two' => 'secondcvlue',
        );
        $expectedValue = http_build_query($queryParams, $enc_type=PHP_QUERY_RFC3986);
        $this->assertEquals($expectedValue, $this->client->getQuery($queryParams));
    }

    /**
     */
    public function testGet()
    {

    }

    /**
     */
    public function testPost()
    {
    }
}
