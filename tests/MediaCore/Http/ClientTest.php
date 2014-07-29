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
        $this->baseUrl = 'http://training.mediacore.tv';
        $adapter = new Curl();
        $this->client = new Client($this->baseUrl, $adapter);
    }

    /**
     */
    protected function tearDown()
    {
    }

    /**
     * @covers MediaCore\Http\Client::getUrl
     */
    public function testApiUrl()
    {
        $getUrl = $this->client->getUrl('api2', 'media');
        $expectedValue = $this->baseUrl . '/api2/media';
        $this->assertEquals($expectedValue, $getUrl);

        $getUrl = $this->client->getUrl('api2', 'media', 'get');
        $expectedValue = $this->baseUrl . '/api2/media/get';
        $this->assertEquals($expectedValue, $getUrl);
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
        $queryStr = http_build_query($queryParams);
        $expectedValue = str_replace('+', '%20', $queryStr);
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
        $url = $this->client->getUrl('api2', 'media') . '?' . $queryStr;
        $result = $this->client->get($url);
        $obj = json_decode($result);
        $this->assertObjectHasAttribute('items', (object)$obj);
    }
}
