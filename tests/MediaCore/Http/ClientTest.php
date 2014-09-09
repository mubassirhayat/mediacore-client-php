<?php
namespace MediaCore\Http;

use MediaCore\Auth\Lti;

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
        $this->url = null;
        $this->client = null;
    }

    public function testGetUrl()
    {
        $url = $this->client->getUrl('api2', 'media');
        $expectedValue = 'http://training.mediacore.tv/api2/media';
        $this->assertEquals($expectedValue, $url);
    }

    public function testSetAndGetAuth()
    {
        $auth = new Lti('key', 'secret');
        $this->client->setAuth($auth);
        $this->assertInstanceOf('Requests_Auth', $this->client->getAuth());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSetAuth()
    {
        $auth = new \stdClass();
        $this->client->setAuth($auth);
    }

    /**
     * @covers MediaCore\Http\Client::get
     */
    public function testGet()
    {
        $url = $this->client->getUrl('api2', 'media', '2751068');
        $response = $this->client->get($url);
        $this->assertObjectHasAttribute('id', $response->json);
        $this->assertEquals('2751068', $response->json->id);
    }
}
