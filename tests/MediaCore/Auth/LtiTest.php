<?php
namespace MediaCore;

/**
 */
class LtiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Lti
     */
    protected $lti;

    /**
     */
    protected function setUp()
    {
        $this->baseUrl = 'http://localhost:8080';
        $this->key = 'mykey';
        $this->secret = 'mysecret';
        $this->ltiParams = array(
            'context_id' => '0001',
            'context_label' => 'test_course_label',
            'context_title' => 'test_course_title',
            'ext_lms' => 'moodle-2',
            'lis_person_name_family' => 'test_user',
            'lis_person_name_full' => 'test_name_full',
            'lis_person_name_given' => 'test_name_given',
            'lis_person_contact_email_primary' => 'test_email',
            'lti_message_type' => 'basic-lti-launch-request',
            'roles' => 'Instructor',
            'tool_consumer_info_product_family_code' => 'moodle',
            'tool_consumer_info_version' => '1.0',
            'user_id' => 101,
        );
        $this->auth = new Auth\Lti($this->key, $this->secret);
        $this->client = new Http\Client($this->baseUrl, $this->auth);
    }

    /**
     */
    protected function tearDown()
    {
        $this->baseUrl = null;
        $this->key = null;
        $this->secret = null;
        $this->ltiParams = null;
        $this->lti = null;
    }

    /**
     * @covers MediaCore\Lti::get
     */
    public function testGet()
    {
        $queryStr = $this->client->getQuery($this->ltiParams);
        $url = $this->client->getUrl('chooser') . '?' . $queryStr;
        $response = $this->client->get($url);

        $dom = new \DOMDocument;
        $dom->loadHtml(mb_convert_encoding($response->body, 'HTML-ENTITIES', 'UTF-8'));
        $elem = $dom->getElementById('mcore-chooser');
        $this->assertInstanceOf('DOMElement', $elem);
    }

    /**
     * @covers MediaCore\Lti::post
     * TODO
     */
    public function testPost()
    {
        $url = $this->client->getUrl('chooser');
        $response = $this->client->post($url, $this->ltiParams);

        $dom = new \DOMDocument;
        $dom->loadHtml(mb_convert_encoding($response->body, 'HTML-ENTITIES', 'UTF-8'));
        $elem = $dom->getElementById('mcore-chooser');
        $this->assertInstanceOf('DOMElement', $elem);
    }

    /**
     * @covers MediaCore\Lti::getVersion
     */
    public function testGetVersion()
    {
        $expectedValue = 'LTI-1p0';
        $this->assertEquals($expectedValue, $this->auth->getVersion());
    }
}
