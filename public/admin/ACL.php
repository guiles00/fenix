<?

	class ACL
	{
		var $perms = array();
		var $perfil = array();		//Array : Stores the permissions for the user
		var $userID = null;			//Integer : Stores the ID of the current user
		var $userRoles = array();
		var $userPerfil = array();
		var $query = null;
		
		function __construct($userID=null)
		{
			
			$this->query = new Query();
			//Si esta vacio instancia el ACL del usuario que ingreso, si no, instancia el ACL seleccionado
			(empty($userID))? $this->userID = $_SESSION['userInfo']['usuario_id']:$this->userID = $userID;
			
			//Trame los roles que tiene
			$this->userRoles = $this->getUserRoles();
			$this->userPerfil = $this->getUserPerfil();
			//print_r($this->userRoles);
			//Construye el ACL que tiene el usuario
			$this->buildACL();
		}
		
		function buildACL()
		{
			//first, get the rules for the user's role
			if (count($this->userRoles) > 0)
			{
				$this->perms = array_merge($this->perms,$this->getRolePerms($this->userRoles));
			}
			//then, get the individual user permissions
			//$this->perms = array_merge($this->perms,$this->getUserPerms($this->userID));
			
	/*	if (count($this->userPerfil) > 0)
			{
				$this->perfil = array_merge($this->perms,$this->getRolePerms($this->userPerfil));
			}*/
		}
		
		/*Trae los perfiles que tiene el usuario*/
		function getUserPerfil()
		{
			
			$strSQL = "SELECT * FROM usuario_perfil WHERE usuario_id = " . floatval($this->userID) . " ORDER BY addDate ASC";
			$data = $this->query->executeQuery($strSQL);
			
			
			if(empty($data)) return null;  
			
			$resp = array();
			while($row = mysql_fetch_array($data))
			{
				$resp[] = $row['perfil_id'];
			}
			return $resp;
		}
		
		
		/*Trae los roles que tiene el usuario*/
		function getUserRoles()
		{
			
			$strSQL = "SELECT * FROM usuario_rol WHERE usuario_id = " . floatval($this->userID) . " ORDER BY addDate ASC";
			$data = $this->query->executeQuery($strSQL);
			
			
			if(empty($data)) return null;  
			
			$resp = array();
			while($row = mysql_fetch_array($data))
			{
				$resp[] = $row['rol_id'];
			}
			return $resp;
		}
		function getRoleNameFromID($roleID)
		{
			$strSQL = "SELECT nombre FROM rol WHERE rol_id = " . floatval($roleID) . " LIMIT 1";
			$data = $this->query->executeQuery($strSQL);
			
			$row = mysql_fetch_array($data);
			return $row[0];
		}
		
		
		function hasPermission($permKey)
		{
			
			//echo"<echo>";
			//print_r($this->perms);
			$permKey = strtolower($permKey);
			if (array_key_exists($permKey,$this->perms))
			{
				if ($this->perms[$permKey]['value'] === '1' || $this->perms[$permKey]['value'] === true)
				{
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		
		
		function getPermKeyFromID($permID)
		{
			$strSQL = "SELECT clave FROM recurso WHERE recurso_id = " . floatval($permID) . " LIMIT 1";
			$data = $this->query->executeQuery($strSQL);
//	echo $strSQL;
//exit;
			$row = mysql_fetch_array($data);
			return $row[0];
		}
		
		function getPermNameFromID($permID)
		{
			$strSQL = "SELECT nombre FROM recurso WHERE recurso_id = " . floatval($permID) . " LIMIT 1";
			$data = $this->query->executeQuery($strSQL);
			$row = mysql_fetch_array($data);
			return $row[0];
		}
		
		
		
		
		
		function getAllRoles($format='ids')
		{
			$format = strtolower($format);
			$strSQL = "SELECT * FROM rol ORDER BY nombre ASC";
			$data = $this->query->executeQuery($strSQL);
			
			
			$resp = array();
			while($row = mysql_fetch_array($data))
			{
				if ($format == 'full')
				{
					$resp[] = array("rol_id" => $row['rol_id'],"nombre" => $row['nombre']);
				} else {
					$resp[] = $row['rol_id'];
				}
			}
			return $resp;
		}
		
		function getAllPerms($format='ids')
		{
			$format = strtolower($format);
			$strSQL = "SELECT * FROM recurso ORDER BY nombre ASC";
			$data = $this->query->executeQuery($strSQL);
			$resp = array();
			
			while($row = mysql_fetch_assoc($data))
			{
				//echo "<pre>";
				///print_r($row);
				if ($format == 'full')
				{
				$resp[$row['clave']] =
				array('recurso_id' => $row['recurso_id'], 'nombre' => $row['nombre'],
				      'clave' => $row['clave']);
				} else {
					$resp[] = $row['recurso_id'];
				}
			}
			return $resp;
		}

		function getRolePerms($role)
		{
			if (is_array($role))
			{
				$roleSQL = "SELECT * FROM rol_recurso WHERE rol_id IN (" . implode(",",$role) . ") ORDER BY rol_id ASC";
			} else {
				$roleSQL = "SELECT * FROM rol_recurso WHERE rol_id = " . floatval($role) . " ORDER BY rol_id ASC";
			}
			$data = $this->query->executeQuery($roleSQL);
			
			$perms = array();
			while($row = mysql_fetch_assoc($data))
			{
				$pK = strtolower($this->getPermKeyFromID($row['recurso_id']));
				if ($pK == '') { continue; }
				if ($row['valor'] === '1') {
					$hP = true;
				} else {
					$hP = false;
				}
				$perms[$pK] = array('perm' => $pK,'inheritted' => true,'value' => $hP,'Name' => $this->getPermNameFromID($row['recurso_id']),'ID' => $row['recurso_id']);
			}
			return $perms;
		}
		
		function getUserPerms($userID)
		{
			$strSQL = "SELECT * FROM usuario WHERE `userID` = " . floatval($userID) . " ORDER BY `addDate` ASC";
			$data = $this->query->executeQuery($strSQL);
			$perms = array();
			while($row = mysql_fetch_assoc($data))
			{
				$pK = strtolower($this->getPermKeyFromID($row['permID']));
				if ($pK == '') { continue; }
				if ($row['value'] == '1') {
					$hP = true;
				} else {
					$hP = false;
				}
				$perms[$pK] = array('perm' => $pK,'inheritted' => false,'value' => $hP,'Name' => $this->getPermNameFromID($row['permID']),'ID' => $row['permID']);
			}
			return $perms;
		}
		
		function userHasRole($roleID)
		{
			foreach($this->userRoles as $k => $v)
			{
				if (floatval($v) === floatval($roleID))
				{
					return true;
				}
			}
			return false;
		}
		
		
		function getUsername($userID)
		{
			$strSQL = "SELECT `username` FROM `users` WHERE `ID` = " . floatval($userID) . " LIMIT 1";
			$data = mysql_query($strSQL);
			$row = mysql_fetch_array($data);
			return $row[0];
		}
		
		/*function hasAccess(){
			echo substr(basename($_SERVER['PHP_SELF']),0,-4);
			
			print_r($this->getAllPerms());
			
			if($this->hasPermission(substr(basename($_SERVER['PHP_SELF']),0,-4))){echo 'tiene';
			}else{echo 'no tiene';} 
			
			return $this->hasPermission(substr(basename($_SERVER['PHP_SELF']),0,-4));		
				//return substr(basename($_SERVER['PHP_SELF']),0,-4);
			
		}*/
		function page(){
			
			$uri = basename($_SERVER['REQUEST_URI']);
			//echo "<pre>";
			//echo "<br>aaaaa<br>";
			$acl = array();
			$tok = strtok($uri, "=");
			$acl[] = $tok; 
			while ($tok !== false) {
			    //echo "Word=$tok<br/>";
			    $tok = strtok("=");
				$acl[] = $tok;
			}
			//print_r($acl);
						
			return $acl[1];	
			
		}
	}

?>
