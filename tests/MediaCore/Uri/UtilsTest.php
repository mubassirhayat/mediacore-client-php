<?php
namespace MediaCore;

/**
 */
class UtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Uri\Utils
     */
    protected $utils;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers MediaCore\Uri\Utils::appendParam
     */
    public function testAppendParam()
    {
        $url = 'http://example.com/path/to/directory';
        $utils = new Uri\Utils($url);
        $utils->appendParam('foo', 'bar');
        $expectedValue = array('foo'=>'bar');
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());
    }

    /**
     * @covers MediaCore\Uri\Utils::appendParams
     */
    public function testAppendParams()
    {
        $url = 'http://example.com/path/to/directory';
        $utils = new Uri\Utils($url);
        $utils->appendParams(array('foo'=>'bar'));
        $expectedValue = array('foo'=>'bar');
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());
    }

    /**
     * @covers MediaCore\Uri\Utils::appendPath
     */
    public function testAppendPath()
    {
        $url = 'http://example.com/path/to/directory';
        $utils = new Uri\Utils($url);
        $utils->appendPath('/subdirectory');
        $expectedValue = '/path/to/directory/subdirectory/';
        $this->assertEquals($expectedValue, $utils->getPath());
    }

    /**
     * @covers MediaCore\Uri\Utils::setParam
     */
    public function testSetParam()
    {
        $url = 'http://example.com/path/to/directory';
        $utils = new Uri\Utils($url);
        $utils->setParam('foo', 'bar');
        $expectedValue = array('foo'=>'bar');
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());

        $url = 'http://example.com/path/to/directory?foo=bar';
        $utils = new Uri\Utils($url);
        $utils->setParam('foo', 'bar');
        $expectedValue = array('foo'=>'bar');
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());

        $url = 'http://example.com/path/to/directory?foo=bar&foo=otherValue';
        $utils = new Uri\Utils($url);
        $utils->setParam('foo', 'bar');
        $expectedValue = array('foo'=>array('bar', 'bar'));
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());
    }

    /**
     * @covers MediaCore\Uri\Utils::setQuery
     */
    public function testSetQuery()
    {
        $url = 'http://example.com/path/to/directory';
        $query = 'foo=bar&baz=qux';
        $utils = new Uri\Utils($url);
        $utils->setQuery($query);
        $this->assertEquals($query, $utils->getQuery());

        $url = 'http://example.com/path/to/directory?foo=bar&baz=qux';
        $query = 'foo=bar&baz=qux&foo=bar&baz=qux';
        $utils = new Uri\Utils($url);
        $utils->setQuery($query);
        $this->assertEquals($query, $utils->getQuery());
    }

    /**
     * @covers MediaCore\Uri\Utils::removeParam
     */
    public function testRemoveParam()
    {
        $url = 'http://example.com/path/to/directory?foo=bar';
        $utils = new Uri\Utils($url);
        $utils->removeParam('foo');
        $expectedValue = array();
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());

        $url = 'http://example.com/path/to/directory?foo=bar&foo=otherValue';
        $utils = new Uri\Utils($url);
        $utils->removeParam('foo');
        $expectedValue = array();
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());
    }

    /**
     * @covers MediaCore\Uri\Utils::getScheme
     */
    public function testGetScheme()
    {
        $url = 'http://example.com/path/to/directory?foo=bar';
        $utils = new Uri\Utils($url);
        $this->assertEquals('http', $utils->getScheme());
    }

    /**
     * @covers MediaCore\Uri\Utils::getHost
     */
    public function testGetHost()
    {
        $url = 'http://example.com/path/to/directory?foo=bar';
        $utils = new Uri\Utils($url);
        $this->assertEquals('example.com', $utils->getHost());

        $url = 'http://example.com:8080/path/to/directory?foo=bar';
        $utils = new Uri\Utils($url);
        $this->assertEquals('example.com', $utils->getHost());
    }

    /**
     * @covers MediaCore\Uri\Utils::getPort
     */
    public function testGetPort()
    {
        $url = 'http://example.com:8080/path/to/directory?foo=bar';
        $utils = new Uri\Utils($url);
        $this->assertEquals('8080', $utils->getPort());

        $url = 'http://example.com/path/to/directory?foo=bar';
        $utils = new Uri\Utils($url);
        $this->assertNull($utils->getPort());
    }

    /**
     * @covers MediaCore\Uri\Utils::getPath
     */
    public function testGetPath()
    {
        $url = 'http://example.com:8080/path/to/directory?foo=bar';
        $utils = new Uri\Utils($url);
        $this->assertEquals('/path/to/directory', $utils->getPath());
    }

    /**
     * @covers MediaCore\Uri\Utils::getFragment
     */
    public function testGetFragment()
    {
        $url = 'http://www.example.org/foo.html#bar';
        $utils = new Uri\Utils($url);
        $this->assertEquals('bar', $utils->getFragment());
    }

    /**
     * @covers MediaCore\Uri\Utils::getQuery
     */
    public function testGetQuery()
    {
        $url = 'http://example.com/path/to/directory?foo=bar';
        $utils = new Uri\Utils($url);
        $expectedValue = 'foo=bar';
        $this->assertEquals($expectedValue, $utils->getQuery());

        $url = 'http://example.com/path/to/directory?foo=bar&foo=otherValue';
        $utils = new Uri\Utils($url);
        $expectedValue = 'foo=bar&foo=otherValue';
        $this->assertEquals($expectedValue, $utils->getQuery());
    }

    /**
     * @covers MediaCore\Uri\Utils::getQueryAsArray
     */
    public function testGetQueryAsArray()
    {
        //simple query string
        $url = 'http://example.com/path/to/directory?foo=bar';
        $utils = new Uri\Utils($url);
        $expectedValue = array('foo'=>'bar');
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());

        //multiple query params
        $url = 'http://example.com/path/to/directory?foo=bar&foo=baz';
        $utils = new Uri\Utils($url);
        $expectedValue = array('foo'=>array('bar','baz'));
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());

        //foo=bar=baz
        $url = 'http://example.com/path/to/directory?foo=bar%3Dbaz';
        $utils = new Uri\Utils($url);
        $expectedValue = array('foo'=>'bar=baz');
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());

        //empty query param value
        $url = 'http://example.com/path/to/directory?foo=bar&baz=';
        $utils = new Uri\Utils($url);
        $expectedValue = array('foo'=>'bar','baz'=>'');
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());
    }

    /**
     * @covers MediaCore\Uri\Utils::getParamValue
     */
    public function testGetParamValue()
    {
        $url = 'http://example.com/path/to/directory?foo=bar';
        $utils = new Uri\Utils($url);
        $expectedValue = 'bar';
        $this->assertEquals($expectedValue, $utils->getParamValue('foo'));

        $url = 'http://example.com/path/to/directory?foo=bar&foo=otherValue';
        $utils = new Uri\Utils($url);
        $expectedValue = array('bar','otherValue');
        $this->assertEquals($expectedValue, $utils->getParamValue('foo'));
    }

    /**
     * @covers MediaCore\Uri\Utils::buildQuery
     */
    public function testBuildQuery()
    {
        //test nested arrays
        $params = array(
            'foo'=>'bar',
            'myDuplicateKey'=>array('bar','myDupValue'),
        );
        $encodedQuery = Uri\Utils::buildQuery($params);
        $expectedValue = 'foo=bar&myDuplicateKey=bar&myDuplicateKey=myDupValue';
        $this->assertEquals($expectedValue, $encodedQuery);

        //test query encoding
        $params = array(
            'foo with spaces'=>'bar',
            'myDuplicateKey'=>array('bar with spaces','myDupValue'),
        );
        $encodedQuery = Uri\Utils::buildQuery($params);
        $expectedValue = 'foo%20with%20spaces=bar&myDuplicateKey=bar%20'
                       . 'with%20spaces&myDuplicateKey=myDupValue';
        $this->assertEquals($expectedValue, $encodedQuery);

    }

    /**
     * @covers MediaCore\Uri\Utils::hasParam
     */
    public function testHasParam()
    {
        $url = 'http://example.com/path/to/directory?foo=bar&foo=otherValue';
        $utils = new Uri\Utils($url);
        $this->assertTrue($utils->hasParam('foo'));
    }

    /**
     * @covers MediaCore\Uri\Utils::toString
     */
    public function testToString()
    {
        $url = 'http://example.com/path/to/directory?foo=bar&foo=otherValue';
        $utils = new Uri\Utils($url);
        $this->assertEquals($url, $utils->toString());
    }
}
