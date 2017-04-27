<?php 
session_start();
include_once('header.php');
/*function __autoload($class_name) {

  defined('APPLICATION_PATH')  || define('CLASS_PATH', realpath(dirname(__FILE__) . '/../clases'));
  $class_path = realpath(CLASS_PATH);

  $file = $class_path.'/'.$class_name.'.php';
  if (file_exists($file)) require_once $file;
}*/

$myACL = new ACL($_SESSION['userID']);
if (isset($_POST['action']))
{
	$query = new Query();
	switch($_POST['action'])
	{
		case 'editRole':
			
			$strSQL = sprintf("REPLACE INTO rol SET rol_id = %u, nombre = '%s'",$_POST['roleID'],$_POST['roleName']);
			
			$query->executeQuery($strSQL);
			
			if (mysql_affected_rows() > 1)
			{
				$roleID = $_POST['roleID'];
			} else {
				$roleID = mysql_insert_id();
			}
			
			
			foreach ($_POST as $k => $v)
			{
				if (substr($k,0,8) == "recurso_")
				{
					
					$permID = str_replace("recurso_","",$k);
					
					if ($v == 'X')
					{
						$strSQL = sprintf("DELETE FROM rol_recurso WHERE rol_id = %u AND recurso_id = %u",$roleID,$permID);
						
						$query->executeQuery($strSQL);
						
						continue;
					}
					$strSQL = sprintf("REPLACE INTO rol_recurso SET rol_id = %u, recurso_id = %u, valor = %u, addDate = '%s'",$roleID,$permID,$v,date ("Y-m-d H:i:s"));
					
					$query->executeQuery($strSQL);
				}
			}
			//header("location: roles.php");
		break;
		case 'deleteRol':
			
			
			$strSQL = sprintf("DELETE FROM rol WHERE rol_id = %u LIMIT 1",$_POST['roleID']);
			$query->executeQuery($strSQL);
			$strSQL = sprintf("DELETE FROM usuario_rol WHERE rol_id = %u",$_POST['roleID']);
			$query->executeQuery($strSQL);
			$strSQL = sprintf("DELETE FROM rol_recurso WHERE rol_id = %u",$_POST['roleID']);
			$query->executeQuery($strSQL);
			//header("location: roles.php");
		break;
	}
}
if ($myACL->hasPermission('roles') != true)
{
	//header("location: ./index.php");
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

</head>

<div id="page">
	<? if ($_GET['action'] == '') { ?>
    	<h2>Seleccionar un Rol para administrar:</h2>
        <? 
		$roles = $myACL->getAllRoles('full');
		foreach ($roles as $k => $v):?>
			<a href="index.php?page=roles&action=role&roleID=<?=$v['rol_id']?>"><?=$v['nombre']?></a><br />
		<? endforeach;?>
		<?if (count($roles) < 1):?>
		
			No tiene roles<br/>
		<? endif; ?>
        <input type="button" name="New" value="Nuevo Rol" onclick="window.location='index.php?page=roles&action=role'" />
    <? } 
    if ($_GET['action'] == 'role') { 
		if ($_GET['roleID'] == '') { 
		?>
    	<h2>Nuevo Rol:</h2>
        <? } else { ?>
    	<h2>Administrar Rol: (<?= $myACL->getRoleNameFromID($_GET['roleID']); ?>)</h2><? } ?>
        <form action="index.php?page=roles" method="post">
        	<label for="roleName">Nombre:</label><input type="text" name="roleName" id="roleName" value="<?= $myACL->getRoleNameFromID($_GET['roleID']); ?>" />
            <table border="0" cellpadding="5" cellspacing="0">
            <tr><th></th><th>Permitir</th><th>Denegar</th><th>Ignorar</th></tr>
            <? 
            $rPerms = $myACL->getRolePerms($_GET['roleID']);
            $aPerms = $myACL->getAllPerms('full');
	    
	    //echo "<pre>";
	    //print_r($rPerms);
	    //exit;
	    //print_r($aPerms);
	    //exit;
            foreach ($aPerms as $k => $v)
            {
		//echo "<pre>";
		///e///cho "Clave:";
		///print_r($rPerms[$v['clave']]);
		///print_r($aPerms[$v['clave']]);
		
		//echo "Valor:";
		//print_r($rPerms[$v['clave']]['value']);
		//print_r($aPerms[$v['clave']]['value']);
		?>
                <tr><td><label> <?=$v['nombre']?> </label></td>
                <td><input type="radio" name= <? echo "recurso_".$v['recurso_id'] ?>  id=<? echo "recurso_".$v['recurso_id']."_1"?> value="1"
                
		<?if ($rPerms[$v['clave']]['value'] === true && $_GET['roleID'] != '') {?>  checked="checked" <? }?>/></td>
                
		<td><input type="radio" name=<? echo "recurso_".$v['recurso_id']?> id=<? echo "recurso_".$v['recurso_id']."_0"?> value="0"
                <?if ($rPerms[$v['clave']]['value'] != true && $_GET['roleID'] != '') {?>  checked="checked" <? } ?>/></td>
				
		<td><input type="radio" name=<? echo "recurso_".$v['recurso_id']?> id=<? echo "recurso_".$v['ID']."_X"?> value="X"
                <? if ($_GET['roleID'] == '' || !array_key_exists($v['clave'],$rPerms)) {?>  checked="checked" <?}?> /></td>
                </tr>
         <?   }
        ?>
    	</table>
    	<input type="hidden" name="action" value="editRole" />
        <input type="hidden" name="roleID" value="<?= $_GET['roleID']; ?>" />
    	<input type="submit" name="Submit" value="Aceptar" />
    </form>
    <form action="index.php?page=roles" method="post">
         <input type="hidden" name="action" value="deleteRol" />
         <input type="hidden" name="roleID" value="<?= $_GET['roleID']; ?>" />
    	<input type="submit" name="Delete" value="Delete" />
    </form>
    <form action="index.php?page=roles" method="post">
    	<input type="submit" name="Cancel" value="Cancel" />
    </form>
    <? } ?>
</div>
