<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(
APPLICATION_PATH.'/../library'.PATH_SEPARATOR.
APPLICATION_PATH.'/../library/Zend'
);

/** Zend_Application */
require_once 'Zend/Application.php';

$_defaultConfig = '/configs/local.ini';

$application = new Zend_Application(
	APPLICATION_ENV,
		array(
			'config' => array(
				APPLICATION_PATH . '/configs/application.ini',
				APPLICATION_PATH . $_defaultConfig
		)
	)
);
Zend_Session::start();
$application->bootstrap()
            ->run();