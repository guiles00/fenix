
<?

class Usuario {
    
    private $usuario_id;
    private $tipo_usuario_id;
    private $usuario_tipo_id;
    private $username;
    private $password;
    private $usuario;
    private $acl;
    
    function __construct($id=null){
        if($id != null){
            
            $query =new Query();

            $sql = "select * from usuario where usuario_id=$id";

            $result =$query->executeQuery($sql);
            $row=mysql_fetch_array($result);
            
            $this->setID($row['usuario_id']);
            $this->setTipoUsuario($row['tipo_usuario_id']);
            $this->setUsuarioTipo($row['usuario_tipo_id']);
            $this->setUsername($row['username']);
            $this->setPassword(md5($row['password']));
            
            
            $nombre = $this->getTipoUsuarioNombre($this->tipo_usuario_id);
            
           //echo $nombre;
            switch ($nombre) {
            case 'compania': $this->usuario = new Compania($this->usuario_tipo_id);
            break;
            case 'productor': $this->usuario = new Prouctor($this->usuario_tipo_id);
            break;
            case 'cliente': $this->usuario = new Cliente($this->usuario_tipo_id);
            break;
            case 'operador': $this->usuario = new Usuario();
            break;
	    case 'ejecutivo_cuenta': $this->usuario = new EjecutivoCuenta($this->usuario_tipo_id);
            break;
            default:
            $this->usuario = new Usuario();
            }
            
         }
        }
    //Setters
    function setID($val){
        $this->usuario_id=$val;
    }
    function setTipoUsuario($val){
        $this->tipo_usuario_id=$val;
    }
    function setUsuarioTipo($val){
        $this->usuario_tipo_id=$val;
    }
    
    function setUsername($val){
        $this->username=$val;
    }
    
    function setPassword($val){
        $this->password=md5($val);
    }
    function setACL($val){
        $this->acl=$val;
    }
    //getters
    function getTipoUsuario(){
        return $this->tipo_usuario_id;
    }
    function getUsuarioTipo(){
        return $this->usuario_tipo_id;
    }
    
    function getUsername(){
        return $this->username;
    }
    
    function getPassword(){
        return $this->password;
    }
    function getACL(){
        return $this->acl;
    }
    function getUsuario(){
        //podria hacer un factory
        return $this->usuario ;
    }
    //End setters y getters
    
    function validate($username,$password){
        
         $query =new Query();
    
            
            $sql = "select * from usuario where username='$username' and password='$password'";
            
            $result =$query->executeQuery($sql);
            
            if( empty($result) ) return false;
            
            //$res = mysql_fetch_array($result);
            //print_r($res);
            return mysql_fetch_array($result);
            
    }
    
    function getUsuarios($criterio=null){

        $query =new Query();

            if(!$criterio==null) $where = " where username like '%$criterio%'";

            $sql = "select * from usuario".$where;

            $result=$query->executeQuery($sql); // ejecuta la consulta para traer al cliente  

            return $result; // devuelve el recurso para recorrer los usuarios

    }

   
    
   
   
    function getPolizas($criterio=null,$inicio=null,$tamano_pagina=null){
    
    $query =new Query();
    
    if(!$criterio==null) $sql_where = " and numero_poliza like '%$criterio%'
    or solicitud_poliza_id like '%$criterio%' ";
    
    if(!$tamano_pagina==null) $sql_limit =" limit $inicio,$tamano_pagina";
            
    $sql = "select * from poliza where 1  ".$sql_where." order by poliza_id desc ".$sql_limit;

  
    $result =$query->executeQuery($sql); 
    
    if(!$result)$result = mysql_error();
    
    return $result;


    }

    function getTipoUsuarioNombre($id){
        
        $query =new Query();
       
        $sql = "select nombre from helper where helper_id = $id and dominio='entidad'";

  
        $result =$query->executeQuery($sql); 
    
        if(!$result)$result = mysql_error();
    
        $res = mysql_fetch_array($result);
        
        return $res['nombre'];
        
    }
    
     function save(){


            $query =new Query();
            if(! empty( $this->usuario_id ) ){
            $sql = "update usuario set  tipo_usuario_id='$this->tipo_usuario_id'
            , usuario_tipo_id='$this->usuario_tipo_id' , username='$this->username' , password = '$this->password'
		where usuario_id=$this->usuario_id ";
            
			
			
            } else {
            
            $sql =  "insert into usuario (tipo_usuario_id,usuario_tipo_id,username,password)
		values('$this->tipo_usuario_id','$this->usuario_tipo_id','$this->username','$this->password')";
		
		
	
            }
              
			$result = $query->executeQuery($sql);
            if(!$result)$result = mysql_error();
            
            return $result;
    }
    
    function getCC($cliente){
    
     $query =new Query();
    
	//$sql_where = " and fecha_pedido between $desde and $hasta ";
    
    $sql = "select * 
    from poliza where cliente_id in (select cliente_id from cliente where nombre like
    '%$cliente%') ";//.$sql_where.$sql_limit;
	
	//  if(!empty($desde)) $sql .= $sql_where;
    
	//echo $sql;
    $result =$query->executeQuery($sql); 
    
    if(!$result)$result = mysql_error();
    
    return $result;


	}
	
	function getSaldoCC($moneda,$fecha_desde=null,$fecha_hasta=null){
    
    //exit;
    return null;
	}
    
	function getCuentaCorriente(){
		
		$sql = "select * 
    from cuenta_corriente where usuario_id=$this->usuario_id ";
	
	//  if(!empty($desde)) $sql .= $sql_where;
    
	//echo $sql;
    $result =$query->executeQuery($sql); 
    
    if(!$result)$result = mysql_error();
    
    return $result;
		
	}
	
	function exists($username){
        
        $query =new Query();
       
        $sql = "select username from usuario where username='$username'";
  
        $result =$query->executeQuery($sql); 
    
        if(!$result)$result = mysql_error();
    
        $res = mysql_fetch_array($result);
        
        return $res;
        
    }
	
	function delete(){
        
        $query =new Query();
       
        $sql = "delete from usuario where usuario_id='$this->usuario_id'";
  
        $result =$query->executeQuery($sql); 
		
		$sql = "delete from cuenta_corriente where usuario_id='$this->usuario_id'";
    	$result =$query->executeQuery($sql); 
	
		$sql = "delete from usuario_rol where usuario_id='$this->usuario_id'";
    	$result =$query->executeQuery($sql); 
	
	
        if(!$result)$result = mysql_error();
    
        $res = mysql_fetch_array($result);
        
        return $res;
        
    }
    
}