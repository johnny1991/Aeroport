<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define library directory
defined ('LIBRARY_PATH') || define('LIBRARY_PATH',
	realpath(dirname(__FILE__) . '/../../library'));

// Define models directory
defined ('MODELS_PATH') || define('MODELS_PATH',
		realpath(dirname(__FILE__) . '/models'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(realpath('LIBRARY_PATH'),realpath('MODELS_PATH'), get_include_path())));

/** Zend_Application */
require_once 'Zend/Application.php';

/** Zend_Session */
require_once 'Zend/Session.php';
Zend_Session::start();

/** Zend_Loader */
require_once "Zend/Loader.php";
Zend_Loader::registerAutoload();

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();
