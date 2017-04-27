<?php

if (isset($_GET['PHPSESSID']))
{
	$_COOKIE['PHPSESSID']=$_GET['PHPSESSID'];
}else if (isset($_POST['PHPSESSID'])){
	$_COOKIE['PHPSESSID']=$_POST['PHPSESSID'];
}

//error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED); 
//ini_set("display_errors", 1);

		
/* use custom error handling */
set_error_handler("my_error_handler");
function my_error_handler($errno, $errstr, $errfile, $errline) 
{
	$errLogLine = date("n/j/y H:i:s")." - ".$errfile.":".$errline.": ".$errstr."\n";
	//error_log ($errLogLine, 3, "/tmp/app-errors.log");
}  

// Set the initial include_path. You may need to change this to ensure that 
// Zend Framework is in the include_path; additionally, for performance 
// reasons, it's best to move this to your web server configuration or php.ini 
// for production.
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__) . '/../library'),
    get_include_path(),
)));

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
// por algo no toma el entorno mio, hay que ver como lo maneja el fastcgi, asi con esa variable anda.
if (isset($_SERVER['APPLICATION_ENV']))
{
	if ($_SERVER['APPLICATION_ENV']=='development') define('APPLICATION_ENV','development');
}
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

/** Zend_Application */
require_once 'Zend/Application.php';  

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();


try{
	$application->run();
}catch(Doctrine_Query_Exception $e){
	echo "ERROR DOCTRINE! (Doctrine_Query_Exception)<br />";
	var_dump ($e);
}
