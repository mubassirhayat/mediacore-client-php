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
        $utils->appendParam('newKey', 'newValue');
        $expectedValue = array('newKey'=>'newValue');
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());
    }

    /**
     * @covers MediaCore\Uri\Utils::appendParams
     */
    public function testAppendParams()
    {
        $url = 'http://example.com/path/to/directory';
        $utils = new Uri\Utils($url);
        $utils->appendParams(array('newKey'=>'newValue'));
        $expectedValue = array('newKey'=>'newValue');
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
        $utils->setParam('myKey', 'newValue');
        $expectedValue = array('myKey'=>'newValue');
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());

        $url = 'http://example.com/path/to/directory?myKey=myValue';
        $utils = new Uri\Utils($url);
        $utils->setParam('myKey', 'newValue');
        $expectedValue = array('myKey'=>'newValue');
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());

        $url = 'http://example.com/path/to/directory?myKey=myValue&myKey=otherValue';
        $utils = new Uri\Utils($url);
        $utils->setParam('myKey', 'newValue');
        $expectedValue = array('myKey'=>array('newValue', 'newValue'));
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());
    }

    /**
     * @covers MediaCore\Uri\Utils::removeParam
     */
    public function testRemoveParam()
    {
        $url = 'http://example.com/path/to/directory?myKey=myValue';
        $utils = new Uri\Utils($url);
        $utils->removeParam('myKey');
        $expectedValue = array();
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());

        $url = 'http://example.com/path/to/directory?myKey=myValue&myKey=otherValue';
        $utils = new Uri\Utils($url);
        $utils->removeParam('myKey');
        $expectedValue = array();
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());
    }

    /**
     * @covers MediaCore\Uri\Utils::getScheme
     */
    public function testGetScheme()
    {
        $url = 'http://example.com/path/to/directory?myKey=myValue';
        $utils = new Uri\Utils($url);
        $this->assertEquals('http', $utils->getScheme());
    }

    /**
     * @covers MediaCore\Uri\Utils::getHost
     */
    public function testGetHost()
    {
        $url = 'http://example.com/path/to/directory?myKey=myValue';
        $utils = new Uri\Utils($url);
        $this->assertEquals('example.com', $utils->getHost());

        $url = 'http://example.com:8080/path/to/directory?myKey=myValue';
        $utils = new Uri\Utils($url);
        $this->assertEquals('example.com', $utils->getHost());
    }

    /**
     * @covers MediaCore\Uri\Utils::getPort
     */
    public function testGetPort()
    {
        $url = 'http://example.com:8080/path/to/directory?myKey=myValue';
        $utils = new Uri\Utils($url);
        $this->assertEquals('8080', $utils->getPort());

        $url = 'http://example.com/path/to/directory?myKey=myValue';
        $utils = new Uri\Utils($url);
        $this->assertNull($utils->getPort());
    }

    /**
     * @covers MediaCore\Uri\Utils::getPath
     */
    public function testGetPath()
    {
        $url = 'http://example.com:8080/path/to/directory?myKey=myValue';
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
        $url = 'http://example.com/path/to/directory?myKey=myValue';
        $utils = new Uri\Utils($url);
        $expectedValue = 'myKey=myValue';
        $this->assertEquals($expectedValue, $utils->getQuery());

        $url = 'http://example.com/path/to/directory?myKey=myValue&myKey=otherValue';
        $utils = new Uri\Utils($url);
        $expectedValue = 'myKey=myValue&myKey=otherValue';
        $this->assertEquals($expectedValue, $utils->getQuery());
    }

    /**
     * @covers MediaCore\Uri\Utils::getQueryAsArray
     */
    public function testGetQueryAsArray()
    {
        $url = 'http://example.com/path/to/directory?myKey=myValue';
        $utils = new Uri\Utils($url);
        $expectedValue = array('myKey'=>'myValue');
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());

        $url = 'http://example.com/path/to/directory?myKey=myValue&myKey=otherValue';
        $utils = new Uri\Utils($url);
        $expectedValue = array('myKey'=>array('myValue','otherValue'));
        $this->assertEquals($expectedValue, $utils->getQueryAsArray());
    }

    /**
     * @covers MediaCore\Uri\Utils::getParamValue
     */
    public function testGetParamValue()
    {
        $url = 'http://example.com/path/to/directory?myKey=myValue';
        $utils = new Uri\Utils($url);
        $expectedValue = 'myValue';
        $this->assertEquals($expectedValue, $utils->getParamValue('myKey'));

        $url = 'http://example.com/path/to/directory?myKey=myValue&myKey=otherValue';
        $utils = new Uri\Utils($url);
        $expectedValue = array('myValue','otherValue');
        $this->assertEquals($expectedValue, $utils->getParamValue('myKey'));
    }

    /**
     * @covers MediaCore\Uri\Utils::buildQuery
     */
    public function testBuildQuery()
    {
        $params = array(
            'myKey'=>'myValue',
            'myDuplicateKey'=>array('myValue','myDupValue'),
        );
        $encodedQuery = Uri\Utils::buildQuery($params);
        $expectedValue = 'myKey=myValue&myDuplicateKey=myValue&myDuplicateKey=myDupValue';
        $this->assertEquals($expectedValue, $encodedQuery);

        $params = array(
            'myKey with spaces'=>'myValue',
            'myDuplicateKey'=>array('myValue with spaces','myDupValue'),
        );
        $encodedQuery = Uri\Utils::buildQuery($params);
        $expectedValue = 'myKey%20with%20spaces=myValue&myDuplicateKey=myValue%20'
                       . 'with%20spaces&myDuplicateKey=myDupValue';
        $this->assertEquals($expectedValue, $encodedQuery);

    }

    /**
     * @covers MediaCore\Uri\Utils::hasParam
     */
    public function testHasParam()
    {
        $url = 'http://example.com/path/to/directory?myKey=myValue&myKey=otherValue';
        $utils = new Uri\Utils($url);
        $this->assertTrue($utils->hasParam('myKey'));
    }

    /**
     * @covers MediaCore\Uri\Utils::toString
     */
    public function testToString()
    {
        $url = 'http://example.com/path/to/directory?myKey=myValue&myKey=otherValue';
        $utils = new Uri\Utils($url);
        $this->assertEquals($url, $utils->toString());
    }
}
