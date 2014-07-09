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
        $this->url = 'https://fakeurl.com';
        $this->getMethod = 'GET';
        $this->postMethod = 'POST';
        $this->key = 'myKey';
        $this->secret = 'mySecret';

        $this->lti = new Lti;
    }

    /**
     */
    protected function tearDown()
    {
        $this->url = null;
        $this->getMethod = null;
        $this->postMethod = null;
        $this->key = null;
        $this->secret = null;
        $this->lti = null;
    }

    /**
     * @covers MediaCore\Lti::buildRequest
     * TODO
     */
    public function testBuildRequest()
    {
        $params = array(
            'context_id' => '0001',
            'context_label' => 'test_course_label',
            'context_title' => 'test_course_title',
            'ext_lms' => 'moodle-2',
            'lis_person_name_family' => 'test_user',
            'lis_person_name_full' => 'test_name_full',
            'lis_person_name_given' => 'test_name_given',
            'lis_person_contact_email_primary' => 'test_email',
            'lti_message_type' => 'basic-lti-launch-request',
            'lti_version' => $this->lti->getVersion(),
            'roles' => 'Instructor',
            'tool_consumer_info_product_family_code' => 'moodle',
            'tool_consumer_info_version' => '1.0',
            'user_id' => 101,
        );

        $signedRequest = $this->lti->buildRequest($params, $this->url,
            $this->getMethod, $this->key, $this->secret);

    }
}
