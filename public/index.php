<?php
// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

defined('LIBRARY_PATH')
|| define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));

defined('VIEW_PATH')
|| define('VIEW_PATH', APPLICATION_PATH . '/views/');

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

defined('NAMESPACE_MAP_APP')
|| define('NAMESPACE_MAP_APP', "MAP_APPLICATION");

date_default_timezone_set('Asia/Ho_Chi_Minh');

define('QUES_MULTICHOICE',2);
define('QUES_TRUE_FALSE',1);
define('QUES_MATCHING',3);
define('QUES_COMPLETION',4);
define('QUES_ESSAYTEST',5);
define('QUES_SHORTANSWER',6);


static  $JQUERYUI_CSS = "cupertino";

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
realpath(APPLICATION_PATH . '/../library'),
get_include_path(),
)));

// Define base url
require_once 'Zend/Controller/Request/Http.php';
$request = new Zend_Controller_Request_Http();
defined('BASE_URL')
|| define('BASE_URL', $request->getBaseUrl());
/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'Zend/Auth.php';
$auth = Zend_Auth::getInstance();
if ($auth->hasIdentity()){
	$userhaslogin = $auth->getStorage()->read();
	defined('ROLE_IN_GROUPID')
	|| define('ROLE_IN_GROUPID', $userhaslogin->group_id);
}

// Create application, bootstrap, and run
$application = new Zend_Application(
APPLICATION_ENV,
APPLICATION_PATH . '/configs/application.ini'
);

// permission plugin before predispatch
require_once APPLICATION_PATH.'/Permission.php';
$front = Zend_Controller_Front::getInstance();
$front->registerPlugin(new Zend_Controller_Plugin_Permission());

$application->bootstrap()
->run();