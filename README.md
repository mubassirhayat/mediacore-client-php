# MediaCore Client PHP #

MediaCore PHP LTI, OAuth, and Client libraries

## Dependencies ##

- PHP 5.3.3 or greater.
- [Zend\Uri](http://framework.zend.com/manual/2.3/en/modules/zend.uri.html) Zend Framework 2 package


## Installation ##

You can install dependencies using [composer](https://getcomposer.org/):

```
brew install composer
composer install
```

Composer contains an autoloader for the required php libraries that can be included in your project like so:

``` php
require_once('vendor/autoload.php');

$params = array(
	'context_id' => 'my_context_id',
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
	'user_id' => '101',
);
$url = 'http://somedomain.com/chooser';
$key = 'key';
$secret = 'secret';

$lti = new MediaCore\Lti();
$signedUrl = $lti->buildRequestUrl($params, $url, 'GET', $key, $secret);
echo $signedUrl;
```


## Tests ##

The test libraries require PHPUnit 4.1.* installed via composer.

You can run the tests like this:

```
cd tests
phpunit --debug
```


## Documentation ##

Documentation can be created using Phpdocumentor, installed via composer.

```
mkdir -p docs/api
phpdoc -d "./src/" -t "./docs/api/" --template="zend"
```
