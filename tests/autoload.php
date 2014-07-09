<?php
$paths = array(
    realpath(dirname(__FILE__) . '/../src'),
);
set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $paths));
require_once 'Zend/Loader/StandardAutoloader.php';
$loader = new Zend\Loader\StandardAutoloader(array('autoregister_zf' => true));
$loader->setFallbackAutoloader(true);
$loader->register();
