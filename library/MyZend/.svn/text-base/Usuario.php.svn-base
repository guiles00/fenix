<?php
class MyZend_Usuario {

 protected static $_instance = null;

    protected function __construct() {}
    protected function __clone() {}

     /**
     * getInstance
     *
     * @return MyZend_Usuario
     */	
	public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();            
        }

        return self::$_instance;
    }
	
    public function getUsername(){

    	$auth = Zend_Auth::getInstance();
		
    	return $auth->getIdentity(); 
    
    }
    
    public function permitidoPorAcl($recurso)
    {
     $acl = MyZend_Acl::getInstance()->getAcl();
     $permitido_por_acl=false;
     try{
      $permitido_por_acl = $acl->isAllowed($this->getUsuario()->usuario, $recurso);     
     }catch(Exception $e){}
     return $permitido_por_acl;
    }
    
    public function getUbicacionId(){

    		$auth = Zend_Auth::getInstance();
    		
    		$usuario_ubicacion_query = Doctrine_Query::create()
    		->from('Model_Usuario u')
    		->leftJoin('u.Model_Ubicacion ub')
    		->where('u.usuario = ? ',$auth->getIdentity())
    		->execute()
    		->toArray();
    	
    	return $usuario_ubicacion_query[0]['ubicacion_id']; 
    
    }
    
    public function getUbicacion(){
    	$model_ubicacion=new Model_Ubicacion();
    	return $model_ubicacion->getTable ()->findOneByUbicacionId($this->getUbicacionId());
    }
    
    public function getUbicacionesIdJuzgadoUsuario(){
    	$ubicacion = MyZend_Usuario::getInstance()->getUbicacion();
		$ubicaciones_id= array();
		if ($ubicacion->tipo_organismo_id==Model_ConfiguracionManager::getValue('tipo_organismo_juzgado'))
		{
			$model_ubicacion=new Model_Ubicacion();
			$ubicaciones_mismo_juzgado=$model_ubicacion->getTable()->findByUbicacion($ubicacion->ubicacion);
			foreach ($ubicaciones_mismo_juzgado as $item)
			{
				$ubicaciones_id[]=$item->ubicacion_id;
			}			
		}
		return $ubicaciones_id;
    } 
    
    public function getUsuarioId(){

    		$auth = Zend_Auth::getInstance();
    		
    		$usuario_query = Doctrine_Query::create()
    		->from('Model_Usuario u')
    		->leftJoin('u.Model_Ubicacion ub')
    		->where('u.usuario = ? ',$auth->getIdentity())
    		->execute()
    		->toArray();
    	
    	return $usuario_query[0]['usuario_id']; 
    
    }
    
    public function getUsuario(){
    	$auth = Zend_Auth::getInstance();
		$model_usuario = new Model_Usuario ();
		$usuario = $model_usuario->getTable ()->findOneByUsuario($auth->getIdentity());
		return $usuario;
    }
    
    public function getJuzgado(){

    	$auth = Zend_Auth::getInstance();
    		
    		$usuario_ubicacion_query = Doctrine_Query::create()
    		->from('Model_Usuario u')
    		->leftJoin('u.Model_Ubicacion ub')
    		->where('u.usuario = ? ',$auth->getIdentity())
    		->execute()
    		->toArray();
    	
    	return $usuario_ubicacion_query[0]['Model_Ubicacion']['juzgado_id']; 
    
    }
}
?>
