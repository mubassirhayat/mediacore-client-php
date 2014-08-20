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
        $this->client = new Client($this->baseUrl);
    }

    /**
     */
    protected function tearDown()
    {
        $this->baseUrl = null;
        $this->client = null;
    }

    /**
     * @covers MediaCore\Http\Client::getApiUrl
     */
    public function testApiUrl()
    {
        $url = $this->client->getApiUrl('media');
        $expectedValue = $this->baseUrl . '/api2/media';
        $this->assertEquals($expectedValue, $url);
    }

    /**
     * @covers MediaCore\Http\Client::get
     */
    public function testGet()
    {
        $url = $this->client->getApiUrl('media', '2751068');
        $response = $this->client->get($url);
        $this->assertObjectHasAttribute('id', $response->json);
        $this->assertEquals('2751068', $response->json->id);
    }
}
