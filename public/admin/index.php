<?php 
session_start();
function __autoload($class_name) {

  defined('APPLICATION_PATH')  || define('CLASS_PATH', realpath(dirname(__FILE__) ));
  $class_path = realpath(CLASS_PATH);

  $file = $class_path.'/'.$class_name.'.php';
  
  if (file_exists($file)) require_once $file;
}
//echo "<pre>";

$myACL = new ACL($_SESSION['userID']);


//print_r($myACL);
//print_r($usuario);


if ($myACL->hasPermission('index_admin') != true)
{
		//header("location: ../index.php");
}

 if (empty($_REQUEST['page'])) $_REQUEST['page'] = 'users';
        switch ($_REQUEST['page']) {
                case ('users'): require_once('users.php'); break;                
                case ('roles'): require_once('roles.php'); break;
		case ('perms'): require_once('perms.php'); break;
       }



?>

