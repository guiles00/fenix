<?php 
session_start();
include_once('header.php');
/*function __autoload($class_name) {

  defined('APPLICATION_PATH')  || define('CLASS_PATH', realpath(dirname(__FILE__) . '/../clases'));
  $class_path = realpath(CLASS_PATH);

  $file = $class_path.'/'.$class_name.'.php';
  if (file_exists($file)) require_once $file;
}*/
//echo "<pre>";


$myACL = new ACL($_SESSION['userID']);

$usuario = new Usuario();

//print_r($myACL);
//print_r($usuario);

//Tiene permiso para entrar a esta pagina?
if ($myACL->hasPermission('users') != true)
{
	//header("location: ./index.php");
}
?>

<?

if (isset($_POST['action']))
{
	switch($_POST['action'])
	{
		case 'editRoles':
			//$redir = "?action=user&userID=" . $_POST['userID'];
			
			
			//echo "<pre>";
			//print_r($_POST);
			foreach ($_POST as $k => $v)
			{
			  
				if (substr($k,0,4) == "rol_")
				{
					$roleID = str_replace("rol_","",$k);
					if ($v == '0' || $v == 'x') {
						$strSQL = sprintf("DELETE FROM usuario_rol WHERE usuario_id = %u AND rol_id = %u",$_POST['userID'],$roleID);
					} else {
						$strSQL = sprintf("REPLACE INTO usuario_rol SET usuario_id = %u, rol_id = %u, `addDate` = '%s'",$_POST['userID'],$roleID,date ("Y-m-d H:i:s"));
					}
				      //echo $strSQL."<br>";
				      $query = new Query();
				      $data = $query->executeQuery($strSQL);
				      //$row = mysql_fetch_array($data);

				}
			}
			
		break;
		
		case 'add':
			
		if( empty( $_POST['nombre'] ) ) exit("Username no puede ser vacio");
		$usuario = new Usuario();
		
		if($usuario->exists($_POST['nombre'])) exit('Error el nombre de usuario ya existe');
		
		$usuario->setUsername($_POST['nombre']);
		$usuario->setPassword($_POST['password']);
		$usuario->setTipoUsuario($_POST['tipo_usuario']);
		
		//Refactoring de este codigo
		if( $_POST['compania_id'] > 0  ){
		  $usuario->setUsuarioTipo($_POST['compania_id']);
		}elseif( $_POST['productor_id'] > 0){
		  $usuario->setUsuarioTipo($_POST['productor_id']);
		}elseif($_POST['cliente_id'] > 0){
		  $usuario->setUsuarioTipo($_POST['cliente_id']);
		}elseif( $_POST['ejecutivo_cuenta_id'] > 0){
		  $usuario->setUsuarioTipo($_POST['ejecutivo_cuenta_id']);
		}elseif( $_POST['operador_id'] > 0){
		  $usuario->setUsuarioTipo($_POST['operador_id']);
		}
		
		//$usuario->getUsuarioTipo($_POST['usuario_tipo_id']);
		//echo "<pre>";
		//print_r($_POST);
		//print_r($usuario);
		$usuario->save();
		//	$redir = "?action=nuevo"
		break;
	
		case 'edit':
		
		$nombre = $_POST['username'];
		
		$usuario_id = $_POST['userID'];
		
		$usuario = new Usuario($usuario_id);		
		$usuario->setUsername($nombre);
		//exit;
		$usuario->save();
		
		
		
		
		break;
	      
	      case 'editPassword':
		
		//$nombre = $_POST['username'];
		$password = $_POST['password'];
		$r_password = $_POST['r_password'];
		
		if( !($password === $r_password) ) exit('Password no coincide');
		
		$usuario_id = $_POST['userID'];
		
		$usuario = new Usuario($usuario_id);		
		//$usuario->setUsername($nombre);
		$usuario->setPassword($password);
		//exit;
		$usuario->save();
		
		
		
		
		break;
	      
	}
	//header("location: users.php" . $redir);
}
?>


<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
    <script type="text/javascript"
     src="../js/jquery.min.js"></script>
  <script type="text/javascript">
function confirmar(){
return confirm('Desea realizar la operacion?');
}
 $(document).ready(function() {
         $('#compania').hide();
	 $('#cliente').hide();
	 $('#productor').hide();
	 $('#ejecutivo_cuenta').hide();
	 
        
	$("#select_tipo_usuario").change(function () {
                var select = $("#select_tipo_usuario option:selected").text();
		
		
                switch(select){
		  case 'compania':
		  $('#compania').show();
		  $('#cliente').hide();
		  $('#productor').hide();
		  $('#ejecutivo_cuenta').hide();
		  break;
		case 'productor':
		  $('#compania').hide();
		  $('#cliente').hide();
		  $('#productor').show();
		  $('#ejecutivo_cuenta').hide();
		  break;
		case 'cliente':
		  $('#compania').hide();
		  $('#cliente').show();
		  $('#productor').hide();
		  $('#ejecutivo_cuenta').hide();
		  break;
		case 'ejecutivo_cuenta':
		  $('#compania').hide();
		  $('#cliente').hide();
		  $('#productor').hide();
		  $('#ejecutivo_cuenta').show();
		  break;
		case 'operador':
		  $('#compania').hide();
		  $('#cliente').hide();
		  $('#productor').hide();
		  $('#ejecutivo_cuenta').hide();
		  break;
		}
        });
	
});
</script>
</head>


<h2>Usuarios</h2>
<hr>
<div id="page">
	
	
	
	
	<? if ($_GET['action'] == '' ) { //Si no hay accion traeme el listado de los usuarios?>
    	
	<form action="index.php?page=users" method="POST">

		Buscar:<input type="text" name="criterio"></input>
		
	        <input type="hidden" name="busqueda" value="busqueda" />
	        <input type="submit" name="Submit" value="Buscar" />
		
	</form>
	<hr>

        <?      $criterio = $_POST['criterio'];
		$usuarios = $usuario->getUsuarios($criterio);?>
		<table>
		<tr><th>Username</th><th></th><th></th></tr>
		
		<?while($row = mysql_fetch_array($usuarios)):?>
		
		<tr>
		<td><a href="index.php?page=users&action=user&userID=<?=$row['usuario_id'] ?>"><?=$row['username']?></a><td/>
		<td><a href="index.php?page=users&action=editPassword&userID=<?=$row['usuario_id']?>"> Password </a><td/>
		<td><a href="index.php?page=users&action=delete&userID=<?=$row['usuario_id']?>" onclick='return confirmar()'> Eliminar </a><td/>
		</tr>
		
		<? endwhile;?>
		</table>
<input type="button" name="New" value="Nuevo Usuario" onclick="window.location='index.php?page=users&action=nuevo'" />		
	<?}//FIN Si no hay accion traeme todo el listado de los usuarios
    ?>

	<?
	if ($_GET['action'] == 'nuevo' ) { 

	?>
	<form action="index.php?page=users" method="POST">
	  <table>
	    
	<tr><td>Nombre:</td><td><input type="text" name="nombre"></input></td></tr>
	<tr><td>Password</td><td><input type="password" name="password"></input></td><td></tr>
	<tr><td>Tipo Usuario:</td>
		<td>
		<select id="select_tipo_usuario" name="tipo_usuario">
        <option value="0">tipo de usuario</option>
        <?
        $solicitud_helper = new SolicitudHelper();
        $solicitud_helpers = $solicitud_helper->getHelpers('tipo_usuario');
  
      while ($row = mysql_fetch_array($solicitud_helpers)){
   
	echo "<option value=".$row['helper_id'].">".$row['nombre']."</option> ";

      };
      ?>          
    </select></td></tr>
	<!--(aca va a salir solamente el usuario asociado, no puede estar vacio):-->
		<tr id="compania"><td>Compania</td>
		<td>
		<select  name="compania_id">
      <option value="0">Seleccione Compania</option>
      <?
        $compania = new Compania();
        $companias = $compania->getCompanias();
  
  while ($row = mysql_fetch_array($companias)){
   

    echo "<option value=".$row['compania_id'].">".$row['nombre']."</option> ";

    
  };
      ?>  
    </select>
		
		</td></tr>
	      <tr id="productor"><td>Productor</td>
		<td>
		<select  name="productor_id">
      <option value="0">Seleccione Productor</option>
      <?
        $productor = new Productor();
        $productores = $productor->getProductores();
  
  while ($row = mysql_fetch_array($productores)){
   
    echo "<option value=".$row['productor_id'].">".$row['nombre']."</option> "; 
    
  };
      ?>  
    </select></td></tr>
	      <tr id="cliente"><td>Cliente</td>
		<td>
		<select  name="cliente_id">
      <option value="0">Seleccione Cliente</option>
      <?
        $cliente = new Cliente();
        $clientes = $cliente->getClientes();
  
  while ($row = mysql_fetch_array($clientes)){
    echo "<option value=".$row['cliente_id'].">".$row['nombre']."</option> "; 
  };
      ?>  
    </select></td></tr>
	      
      <tr id="ejecutivo_cuenta" ><td>Ejecutivo Cuenta</td>
		<td>
		<select  name="ejecutivo_cuenta_id">
      <option value="0">Seleccione Ejecutivo Cuenta</option>
      <?
        $ejecutivo_cuenta = new EjecutivoCuenta();
        $ejecutivo_cuentas = $ejecutivo_cuenta->getEjecutivoCuentas();
  
  while ($row = mysql_fetch_array($ejecutivo_cuentas)){
    echo "<option value=".$row['ejecutivo_cuenta_id'].">".$row['nombre']."</option> ";
  };
      ?>  
    </select>
		
		</td></tr>
		
	        <input type="hidden" name="action" value="add" />
	        <tr><td><input type="button" name="Cancel" onclick="window.location='?page=users'" value="Cancel" /></td>
		<td><input type="submit" name="Submit" value="Aceptar" />
		</td></tr>
		
		</table>
	</form>	
<?	}	
?>
    <?
    if ($_GET['action'] == 'user' ) { 
		$userACL = new ACL($_GET['userID']);
		//$nombre = $userACL->getUsername($_GET['userID']);
		
		$usuario = new Usuario($_GET['userID']);
		
	
         //echo "<pre>";
       // print_r($usuario);
	//echo $usuario->getUsuario()->nombre;
	//Traigo el nombre del tipo de usuario
        //$solicitud_helper = new SolicitudHelper();
        //$tipo_usuario = $solicitud_helper->getName($usuario->getTipoUsuario(),'tipo_usuario');
  
	?>     
	<form action="index.php?page=users" method="post">
        <table border="0" cellpadding="5" cellspacing="0">
        <tr><th>Username</th><td><input type="text" name="username" value=<?=$usuario->getUsername();?> ></input></td></tr>
	<tr><th>Tipo</th><td><input readonly type="text" name="tipo_usuario" value=<?=$tipo_usuario;?> ></input></td></tr>
	<tr><th>Nombre</th><td><input readonly type="text" name="usuario_nombre" value='<?=$usuario->getUsuario()->nombre;?>' ></input></td></tr>
	
        </table>
        <input type="hidden" name="action" value="edit" />
        <input type="hidden" name="userID" value="<?= $_GET['userID']; ?>" />
        <input type="submit" name="Submit" value="Aceptar">
	
        <h3>Roles (<a href="index.php?page=users&action=roles&userID=<?= $_GET['userID']; ?>">Editar</a>)</h3>
        <ul>
        <? $roles = $userACL->getUserRoles();
	if(! empty($roles) ) :
		foreach ($roles as $k => $v):
		
			echo "<li>" . $userACL->getRoleNameFromID($v) . "</li>";
		endforeach;
	endif;	
		?>
        </ul>
    <input type="button" name="Cancel" onclick="window.location='index.php?page=users'" value="Cancel">
     <? } ?>
     
     
     <?
         if ($_GET['action'] == 'editPassword' ) :
	  //instancio el ACL para ver si tiene permisos
		$userACL = new ACL();
		$usuario = new Usuario($_GET['userID']);
		//echo "<pre>";
		//print_r($usuario);
		//exit;
		
		
	?>     
	<form action="index.php?page=users" method="post">
        <table border="0" cellpadding="5" cellspacing="0">
        <tr><td>Nombre:</td><td><input readonly type="text" name="username" value=<?=$usuario->getUsername();?> /> </td></tr>
        <tr><td>Password:</td><td><input type="password" name="password" value=''/></td></tr>
	<tr><td>Repetir Password:</td><td><input type="password" name="r_password" value=''/></td></tr>
        </table>
        <input type="hidden" name="action" value="editPassword" />
        <input type="hidden" name="userID" value="<?= $_GET['userID']; ?>" />
        <input type="submit" name="Submit" value="Aceptar" />
	<input type="button" name="Cancel" onclick="window.location='index.php?page=users'" value="Cancel" />
        
     <? endif; ?>

     <? if ($_GET['action'] == 'roles'): ?>
    

     <form action="index.php?page=users" method="post">
        <table border="0" cellpadding="5" cellspacing="0">
        <tr><th></th><th>Miembro</th><th>No Miembro</th></tr>
        <? 
		$roleACL = new ACL($_GET['userID']);
		//echo "<pre>";
		//print_r($roleACL);
		//exit;
		$roles = $roleACL->getAllRoles('full');
		
		//print_r($roles);
		//exit;
        foreach ($roles as $k => $v):
        ?>
         <tr><td><label><?=$v['nombre'] ?></label></td>
         <td><input type="radio" name=<? echo "rol_".$v['rol_id'] ?> id= <? echo "rol_".$v['rol_id'].'_1'?>value="1"
            <?if ($roleACL->userHasRole($v['rol_id'])) {?>  checked="checked" <?}?>/></td>
	 
            <td><input type="radio" name=<? echo "rol_".$v['rol_id']?> id= <? echo "rol_".$v['rol_id']."_0"?> value="0"
            <?if (!$roleACL->userHasRole($v['rol_id'])) {?>  checked="checked"<? }?> /></td>
            </tr>
        <? endforeach;?>
    
        </table>
        <input type="hidden" name="action" value="editRoles" />
        <input type="hidden" name="userID" value="<?= $_GET['userID']; ?>" />
        <input type="submit" name="Submit" value="Aceptar" />
    </form>
    <form action="index.php?page=users" method="post">
    	<input type="button" name="Cancel" onclick="window.location='index.php?page=users'" value="Cancel" />
    </form>
     <? endif; ?>
     
</div>
	<hr>
	
    <? if ($_GET['action'] == 'delete'): ?>
    <? $usuario = new Usuario($_GET['userID']);
       $usuario->delete();
      
    ?>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
    document.location.href='index.php?page=users';
</SCRIPT>
    <? endif;?>