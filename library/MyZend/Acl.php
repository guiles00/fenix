<?php
class MyZend_Acl {

	private static $_instancia;
	private $_storage = null;
	private $_usuario;
	private $_acl;
	private $_token;
	
	/**
	 * Devuelve el manejador de almacenamiento de persistencias..
	 * Almacenamiento de sesion se utiliza por defecto a menos que un adaptador diferente
	 *  de almacenamiento se ha establecido
	 * @return MyZend_Acl_Storage_Interface
	 */
	public function getStorage() {
		if (NULL === $this->_storage) {
			/**
			 * @see MyZend_Acl_Storage_Session
			 */
			require_once 'MyZend/Acl/Storage/Session.php';
			$this->setStorage(new MyZend_Acl_Storage_Session());
		}
		return $this->_storage;
	}

	/*
	public function setStorage(MyZend_Acl_Storage_Session $storage) {
		$this->_storage = $storage;
	}
	*/
	
	public function setStorage($storage) {
		if($storage instanceof MyZend_Acl_Storage_Session) {
			$this->_storage = $storage;			
		}else {
			$this->_storage = NULL;
		}
	}
	
	private function __construct() {

	}

	public static function getInstance() {
		if (!self::$_instancia instanceof self) {
			self::$_instancia = new self;
		}
		return self::$_instancia;
	}
	
	public function getAcl() {
		$storage = $this->getStorage();

		if ($storage->isEmpty()) {
			return new Zend_Acl();
		}

		$data=$storage->read();
		
		return $data['acl'];
	}
	
	public function loadAcl($usuario) {
    	$logger = Zend_Registry::get('logger');
		
		$this->_acl = new Zend_Acl();
				
		// Se Obtienen todos los recursos
		$recurso_items = Doctrine::getTable('Model_Recurso')->findAll();
		// Carga los Recursos, permitira hasta 4 niveles jerarquicos
		//echo "<pre>";
		for($i=1; $i <= 4; $i++) {
			foreach($recurso_items as $key=>$item) {
				$padre=($item->padre==0)?null:Doctrine::getTable('Model_Recurso')->find($item->padre)->recurso;
				try {
					$recurso=new Zend_Acl_Resource($item->recurso);
					$this->_acl->add(new Zend_Acl_Resource($item->recurso),$padre);
					// Si ya lo agregue lo quito del array
					unset($recurso_items[$key]);
				} catch(Exception $e) {
					
				}
			}
			
		}
		// Obtengo el objeto Usuario si este existe, sino, salgo de la funcion
		$model_usuario = Doctrine::getTable('Model_Usuario')->findOneByUsuario($usuario);
		if(!$model_usuario instanceof Model_Usuario){
			throw new Exception('tratando de cargar el ACL de un usuario inexistente.');
		}
		
		$model_grupos_usuario = Doctrine::getTable('Model_GruposUsuario')->findByUsuario($model_usuario->usuario_id);
		
		$grupos_usuario_items=$model_grupos_usuario->toArray();
		
		$arr_grupos = array();
		
		$arr_nombre_grupos = array();
		
		foreach($grupos_usuario_items as $item) {
			$model_grupo = Doctrine::getTable('Model_Grupo')->findOneByGrupoId($item['grupo']);
			// Agrego un rol al grupo del Acl
			$this->_acl->addRole(new Zend_Acl_Role($model_grupo->grupo));
			// Agrego el nombre del grupo y el id
			$arr_grupos[] = array(
								'grupo'=>$model_grupo->grupo, 
								'id'=>$model_grupo->grupo_id,
							);
			// Agrego el nombre del grupo al vector
			$arr_nombre_grupos[] = $model_grupo->grupo;
		}
		
		
		// Quiero que el orden sea deny sobre allow, por que voy a guardar una lista de los 
		// recursos que denegue, (*1) para luego darle mas PESO, denegandole al usuario como ultimo 
		// paso(para que zend_acl lo tome como 1er regla aplicable...).
		$arr_recursos_denegados=array();
		// Agrego los permisos de los grupos DEL USUARIO levantandolos desde el modelo
		foreach($arr_grupos as $grupo) {
			$model_accesos_grupo = Doctrine::getTable('Model_AccesosGrupo')->findByGrupo($grupo['id']);
			$accesos_grupo_items = $model_accesos_grupo->toArray();
			foreach($accesos_grupo_items as $acceso) {
				$model_grupo = Doctrine::getTable('Model_Grupo')->findOneByGrupoId($acceso['grupo']);
				$model_recurso = Doctrine::getTable('Model_Recurso')->findOneByRecursoId($acceso['recurso']);
				if($acceso['permitido']){
					// Chequeo , si el recurso esta vacio le da permiso a toda la aplicacion!
					if ($model_recurso->recurso!='')
					{
						$this->_acl->allow($model_grupo->grupo,$model_recurso->recurso);
					}
				}else {
					$this->_acl->deny($model_grupo->grupo,$model_recurso->recurso);
					$arr_recursos_denegados[]=$model_recurso->recurso;
				}
			}
		}
		
		// Agrego un usuario como hijo de los grupos
		$this->_acl->addRole(new Zend_Acl_Role($model_usuario->usuario),$arr_nombre_grupos);
				
		// (*1) Agrego como ultima regla, directo al usuario, los recursos que se denegaron al menos una vez.
		foreach ($arr_recursos_denegados as $recurso_denegado)
		{
			try{
				$this->_acl->deny($model_usuario->usuario,$recurso_denegado);
			}catch(Exception $e){}
		}

		// Obtengo el Storage
		$this->getStorage()->write(array('acl'=>$this->_acl, 'token'=>$this->_token));
	}
	
	public function getUsuario() {
		return $this->_usuario;		
	}

	public function setUsuario($usuario) {
		$this->_usuario = $usuario;
		$this->_token= md5(uniqid(rand(), true)); 
		$this->loadAcl($this->_usuario);				
	}

	public function getToken(){
		$storage = $this->getStorage();

		if ($storage->isEmpty()) {
			return null;
		}

		$data=$storage->read();
		
		return $data['token'];
	}
	
	public function clearAcl(){
		$this->getStorage()->write(null);
	}
}
?>