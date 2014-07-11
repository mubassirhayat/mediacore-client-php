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

```
<?php
require_once('vendor/autoload.php');
```


## Tests ##

The test libraries require PHPUnit 4.1.* that would have been installed via composer.

You can run the tests like this:

```
cd tests
phpunit --debug
```
