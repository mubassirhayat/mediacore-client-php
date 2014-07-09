<?php
namespace MediaCore\OAuth;

use MediaCore\OAuth\SignatureMethod\HMAC_SHA1;

/**
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
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
        $this->params = array();
    }

    /**
     */
    protected function tearDown()
    {
        $this->consumer = null;
        $this->signatureMethod = null;
        $this->url = null;
        $this->method = null;
        $this->params = null;
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
    public function testParamKeys()
    {
        $expectedValue = array(
            'oauth_version',
            'oauth_nonce',
            'oauth_timestamp',
            'oauth_consumer_key',
        );
        $this->request = new Request($this->consumer, $this->url, $this->method,
            $this->params);
        $this->assertEquals($expectedValue, array_keys($this->request->getParams()));
    }

    /**
     */
    public function testBaseString()
    {
        $this->request = new Request($this->consumer, $this->url, $this->method,
            $this->params);
        $baseString = $this->request->getBaseString();
        $expectedValue = 'GET&https%3A%2F%2Ffakeurl.com&oauth_version=&oauth_nonce=&'
                       . 'oauth_timestamp=&oauth_consumer_key='
                       . $this->consumer->getKey();
        $this->assertEquals($expectedValue, $baseString);
    }

    /**
     */
    public function testSignRequest()
    {
        $this->request = new Request($this->consumer, $this->url, $this->method,
            $this->params);
        $params = $this->request->signRequest($this->signatureMethod, $this->consumer);

        // test thesignature names match
        $signatureName = $this->signatureMethod->getName();
        $this->assertEquals($signatureName, $params['oauth_signature_method']);

        // TODO test the oauth signature result
    }
}
