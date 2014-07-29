<?php
namespace MediaCore\OAuth;

use MediaCore\OAuth\SignatureMethod\HMAC_SHA1;

/**
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    protected $request;
    protected $consumer;
    protected $signatureMethod;
    protected $url;
    protected $params;

    /**
     */
    protected function setUp()
    {
        $key = 'myKey';
        $secret = 'mySecret';
        $this->consumer = new Consumer($key, $secret);
        $this->signatureMethod = new HMAC_SHA1();

        $this->url = 'https://fakeurl.com';
        $this->method = 'GET';
        $this->oauthParams = array(
            'oauth_version' => '1.0',
            'oauth_nonce' => 'd41d8cd98f00b204e9800998ecf8427e',
            'oauth_timestamp' => '1405011060',
        );
    }

    /**
     */
    protected function tearDown()
    {
        $this->consumer = null;
        $this->signatureMethod = null;
        $this->url = null;
        $this->method = null;
        $this->oauthParams = null;
    }

    /**
     */
    public function testInvalidUrl()
    {
        $this->setExpectedException('InvalidArgumentException');
        $invalidUrl = 'ftp://invlidurl.com';
        $this->request = new Request($this->consumer, $invalidUrl,
            $this->method, array());
    }

    /**
     */
    public function testMinimumParamKeys()
    {
        $expectedValue = array(
            'oauth_version',
            'oauth_nonce',
            'oauth_timestamp',
            'oauth_consumer_key',
        );
        $this->request = new Request($this->consumer, $this->url, $this->method,
            $this->oauthParams);
        $this->assertEquals($expectedValue, array_keys($this->request->getParams()));
    }

    /**
     * @covers MediaCore\OAuth\Request::getBaseString
     */
    public function testBaseString()
    {
        $this->request = new Request($this->consumer, $this->url, $this->method,
            $this->oauthParams);
        $baseString = $this->request->getBaseString();
        $expectedValue = 'GET&https%3A%2F%2Ffakeurl.com&oauth_consumer_key%3D'
            . 'myKey%26oauth_nonce%3Dd41d8cd98f00b204e9800998ecf8427e%26'
            . 'oauth_timestamp%3D1405011060%26oauth_version%3D1.0';
        $this->assertEquals($expectedValue, $baseString);
    }

    /**
     * @covers MediaCore\OAuth\Request::buildRequestUrl
     */
    public function testSignRequest()
    {
        $this->request = new Request($this->consumer, $this->url, $this->method,
            $this->oauthParams);
        $signedRequestUrl = $this->request->signRequest($this->signatureMethod, $this->consumer);
        $expectedValue = 'https://fakeurl.com/?oauth_version=1.0&oauth_nonce='
            . 'd41d8cd98f00b204e9800998ecf8427e&oauth_timestamp=1405011060&'
            . 'oauth_consumer_key=myKey&oauth_signature_method=HMAC-SHA1&'
            . 'oauth_signature=KTUuSmPNLba77/p52pg5tFxLWWk=';
        $this->assertEquals($expectedValue, $signedRequestUrl);
    }
}
