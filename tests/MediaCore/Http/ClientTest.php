<?php
namespace MediaCore\Http;

use MediaCore\Http\Adapter\Curl;

/**
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $client;

    /**
     */
    protected function setUp()
    {
        $this->url = 'http://training.mediacore.tv';
        $this->client = new Client($this->url);
    }

    /**
     */
    protected function tearDown()
    {
    }

    /**
     * @covers MediaCore\Http\Client::apiUrl
     */
    public function testApiUrl()
    {
        $apiUrl = $this->client->apiUrl('api2', 'media');
        $expectedValue = $this->url . '/api2/media';
        $this->assertEquals($expectedValue, $apiUrl);

        $apiUrl = $this->client->apiUrl('api2', 'media', 'get');
        $expectedValue = $this->url . '/api2/media/get';
        $this->assertEquals($expectedValue, $apiUrl);
    }

    /**
     * @covers MediaCore\Http\Client::getQuery
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
     * @covers MediaCore\Http\Client::get
     */
    public function testGet()
    {
        $queryParams = array(
            'joins' => 'files',
        );
        $queryStr = $this->client->get($this->client->getQuery($queryParams));
        $url = $this->client->apiUrl('api2', 'media') . '?' . $queryStr;
        $result = $this->client->get($url);
        $obj = json_decode($result);
        $this->assertObjectHasAttribute('items', (object)$obj);
    }
}
