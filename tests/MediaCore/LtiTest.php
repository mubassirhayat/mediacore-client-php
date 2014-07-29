<?php

namespace MediaCore;

require_once(realpath(dirname(__FILE__) . '/../../src/MediaCore/Lti.php'));

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
        $this->getMethod = 'GET';
        $this->postMethod = 'POST';
        $this->key = 'mykey';
        $this->secret = 'mysecret';
        $this->oauthParams = array(
            'oauth_version' => '1.0',
            'oauth_nonce' => 'd41d8cd98f00b204e9800998ecf8427e',
            'oauth_timestamp' => '1405011060',
        );
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
            'lti_version' => 'LTI-1p0',
            'roles' => 'Instructor',
            'tool_consumer_info_product_family_code' => 'moodle',
            'tool_consumer_info_version' => '1.0',
            'user_id' => 101,
        );
        $this->lti = new Lti($this->baseUrl, $this->key, $this->secret);
    }

    /**
     */
    protected function tearDown()
    {
        $this->baseUrl = null;
        $this->getMethod = null;
        $this->postMethod = null;
        $this->key = null;
        $this->secret = null;
        $this->lti = null;
        $this->oauthParams = null;
    }

    /**
     * @covers MediaCore\Lti::buildRequestUrl
     */
    public function testBuildRequestUrl()
    {
        $params = array_merge($this->ltiParams, $this->oauthParams);
        $signedRequestUrl = $this->lti->buildRequestUrl($params, 'chooser',
            $this->getMethod);

        $expectedValue = 'http://localhost:8080/chooser?oauth_version=1.0&'
            . 'oauth_nonce=d41d8cd98f00b204e9800998ecf8427e&'
            . 'oauth_timestamp=1405011060&oauth_consumer_key=moodlekey&'
            . 'context_id=0001&context_label=test_course_label&'
            . 'context_title=test_course_title&ext_lms=moodle-2&'
            . 'lis_person_name_family=test_user&lis_person_name_full=test_name_full&'
            . 'lis_person_name_given=test_name_given&lis_person_contact_email_primary='
            . 'test_email&lti_message_type=basic-lti-launch-request&lti_version='
            . 'LTI-1p0&roles=Instructor&tool_consumer_info_product_family_code=moodle&'
            . 'tool_consumer_info_version=1.0&user_id=101&oauth_signature_method=HMAC-SHA1&'
            . 'oauth_signature=/bJ6YT1ON+QbLtC56OCk7Er2IMw=';

        $this->assertEquals($expectedValue, $signedRequestUrl);
    }

    /**
     * @covers MediaCore\Lti::get
     */
    public function testGet()
    {
        $response = $this->lti->get($this->ltiParams, 'chooser');
        $dom = new \DOMDocument;
        $dom->loadHtml(mb_convert_encoding($response, 'HTML-ENTITIES', 'UTF-8'));
        $elem = $dom->getElementById('mcore-chooser');
        $this->assertInstanceOf('DOMElement', $elem);
    }

    /**
     * @covers MediaCore\Lti::post
     */
    public function testPost()
    {
        $response = $this->lti->post($this->ltiParams, 'chooser');
        $dom = new \DOMDocument;
        $dom->loadHtml(mb_convert_encoding($response, 'HTML-ENTITIES', 'UTF-8'));
        $elem = $dom->getElementById('mcore-chooser');
        $this->assertInstanceOf('DOMElement', $elem);
    }

    /**
     * @covers MediaCore\Lti::getVersion
     */
    public function testGetVersion()
    {
        $expectedValue = 'LTI-1p0';
        $this->assertEquals($expectedValue, $this->lti->getVersion());
    }
}
