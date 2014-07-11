<?php
namespace MediaCore\Http\Adapter;

/**
 */
class CurlTest extends \PHPUnit_Framework_TestCase
{
    protected $curl;

    /**
     */
    protected function setUp()
    {
        $this->curl = new Curl();
    }

    /**
     */
    protected function tearDown()
    {
        $this->curl = null;
    }

    /**
     */
    public function testSendFailure()
    {
        $url = 'http://localhost:9999/api2/media';
        $result = $this->curl->send($url, 'GET');
        $this->assertFalse($result);
    }

    /**
     */
    public function testSendSuccess()
    {
        $url = 'http://training.mediacore.tv/api2/media';
        $result = $this->curl->send($url, 'GET');
        $this->assertObjectHasAttribute('items', json_decode($result));
    }
}
