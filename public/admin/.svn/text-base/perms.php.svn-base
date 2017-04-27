<?php 
session_start();
include_once('header.php');
/*function __autoload($class_name) {

  defined('APPLICATION_PATH')  || define('CLASS_PATH', realpath(dirname(__FILE__) . '/../clases'));
  $class_path = realpath(CLASS_PATH);

  $file = $class_path.'/'.$class_name.'.php';
  if (file_exists($file)) require_once $file;
}
*/
$myACL = new ACL($_SESSION['SuserID']);
$query = new Query();
if (isset($_POST['action']))
{
	switch($_POST['action'])
	{
		case 'editPerm':
			$strSQL = sprintf("REPLACE INTO recurso SET recurso_id = %u, nombre = '%s', clave = '%s'",$_POST['permID'],$_POST['permName'],$_POST['permKey']);
			$query->executeQuery($strSQL);
		break;
		case 'deletePerm':
			$strSQL = sprintf("DELETE FROM recurso WHERE recurso_id = %u LIMIT 1",$_POST['permID']);
			$query->executeQuery($strSQL);
		break;
	}
	//header("location: index.php?page=perms");
}
if ($myACL->hasPermission('recursos') != true)
{
	//header("location: ./index.php");
}
 ?>
 
<div id="page">
	<? if ($_GET['action'] == ''): ?>
    	<h2>Seleccionar un recurso:</h2>
        <? 
		$roles = $myACL->getAllPerms('full'); ?>
		<?foreach ($roles as $k => $v): ?>
		
			<a href="index.php?page=perms&action=perm&permID=<?= $v['recurso_id']?>"> <?= $v['nombre']?></a><br />
		<? endforeach; ?>
		<?if (count($roles) < 1):?>
		
			echo "No hay recursos todavia.<br />";
		<? endif; ?>
        <input type="button" name="New" value="Nuevo Recurso" onclick="window.location='index.php?page=perms&action=perm'" />
    <? endif; ?>
    <? if ($_GET['action'] == 'perm') { 
		if ($_GET['permID'] == '') { 
		?>
    	<h2>Nuevo Recurso:</h2>
        <? } else { ?>
    	<h2>Administrar Recursos: (<?= $myACL->getPermNameFromID($_GET['permID']); ?>)</h2><? } ?>
        <form action="index.php?page=perms" method="post">
	<label for="permName">Nombre:</label><input type="text" name="permName" id="permName" value="<?= $myACL->getPermNameFromID($_GET['permID']); ?>" maxlength="128" /><br />
	<label for="permKey">Clave:</label><input type="text" name="permKey" id="permKey" value="<?= $myACL->getPermKeyFromID($_GET['permID']); ?>" maxlength="128" /><br />
    	<input type="hidden" name="action" value="editPerm" />
        <input type="hidden" name="permID" value="<?= $_GET['permID']; ?>" />
    	<input type="submit" name="Submit" value="Aceptar" />
    </form>
    <form action="index.php?page=perms" method="post">
         <input type="hidden" name="action" value="deletePerm" />
         <input type="hidden" name="permID" value="<?= $_GET['permID']; ?>" />
    	<input type="submit" name="Delete" value="Delete" />
    </form>
    <form action="index.php?page=perms" method="post">
    	<input type="submit" name="Cancel" value="Cancel" />
    </form>
    <? } ?>
</div>
