<?php
namespace MediaCore\OAuth\SignatureMethod;

use MediaCore\OAuth\Consumer;

/**
 */
class HMAC_SHA1Test extends \PHPUnit_Framework_TestCase
{
    /**
     */
    protected $service;
    protected $name;
    protected $consumer;

    /**
     */
    protected function setUp()
    {
        $this->name = 'HMAC-SHA1';
        $this->service = new HMAC_SHA1();
        $key = 'myKey';
        $secret = 'mySecret';
        $this->consumer = new Consumer($key, $secret);
    }

    /**
     */
    protected function tearDown()
    {
        $this->name = null;
        $this->service = null;
        $this->consumer = null;
    }

    /**
     */
    public function testGetName()
    {
        $expectedValue = $this->name;
        $this->assertEquals($expectedValue, $this->service->getName());
    }

    /**
     */
    public function testBuildSignature()
    {
    }
}
