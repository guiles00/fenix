<?php

class MyZend_Controller_Plugin_ServerLog extends Zend_Controller_Plugin_Abstract
{
    protected $_log = null;
    protected $_microtime = null;
	//
	protected $_timestamp=null;
    protected $_method= null;
    protected $_modulo= null;
    protected $_controlador= null;
    protected $_accion= null;
	protected $_json= null;
	protected $_cookie= null;
	protected $_post= null;
	protected $_get= null;
			
    public function __construct(Zend_Log $log)
    {
    	$this->_log = $log;    
    }

    public function dispatchLoopStartup()
    {
    	$this->_microtime = microtime(true);
    	$this->_timestamp= date("Y/m/d H:i:s");
    	$this->_method=$this->getRequest()->getMethod();
    	$this->_modulo=$this->getRequest()->getModuleName();
    	$this->_controlador=$this->getRequest()->getControllerName();
    	$this->_accion=$this->getRequest()->getActionName();
    	$this->_json = 'aun no implementado';
    	try{
    		// Ver como hacer esto! getRawJson, usa php:input, y es de una sola lectura
    		// si lo leo aca, despues desaparece! 
    		// pienso en quizas hacer una version nueva de json_server que sea singleton(mejor). 
    		// por ahora modifique el core de Zend para que sea una variable estatica(un espanto!)
    		if (Zend_Json_Server_Request_Http::$version_modificada_para_persistir===1)
    		{ 
    			$server = new Zend_Json_Server();
    			$this->_json = $server->getRequest()->getRawJson();
    		}    	    		
    	}catch(Exception $e){
    		$this->_json = null;
    	}
		// buscar alguna manera de hacer esto usando ZendFramework
		$this->_cookie=$_COOKIE;
		$this->_post=$_POST;
    	$this->_get=$_GET;
    }
    
    public function dispatchLoopShutdown()
    {       	
    	$response = $this->getResponse();
    	$peakUsage = memory_get_peak_usage(true);
    	$url = $this->getRequest()->getRequestUri();
    	$time_elapsed=microtime(true) - $this->_microtime;
    	if ($this->_log instanceof Zend_Log)
    	{
    		$this->_log->info($this->_method.' - '.$url.' time elapsed: '.$time_elapsed);
    	}
    	//traigo el nivel de logueo para el usuario(1-basico, 2-basico+parametros, 3-full)
		$nivel_logueo=2;
		if (Zend_Auth::getInstance()->hasIdentity())
		{
	    	$usuario = Doctrine::getTable('Model_Usuario')->findOneByUsuario(Zend_Auth::getInstance()->getIdentity());
			if($usuario instanceof Model_Usuario){
				$nivel_logueo=$usuario->nivel_logueo;
			}
		}
		// Corro el IDS
	    		$json=Zend_Json::decode($this->_json);
	    		if (isset($json['filters']))
	    		{
	    			//usado por el grid Panel, lo evaluo, esta en json tambien
	    			$json['filters']=Zend_Json::decode($json['filters']);	
	    		}
			  $request = array(
			      //'REQUEST' => $_REQUEST,
			      'GET' => $_GET,
			      'POST' => $_POST,
			      'COOKIE' => $_COOKIE,
			  	  'JSONDATA' => $json
			  	  );
			  $init = IDS_Init::init(APPLICATION_PATH . '/../library/'.'IDS/Config/Config.ini.php' );
			  $ids = new IDS_Monitor($request, $init);
			  $ids_result = $ids->run();		
    	//logueo en la base.
    	$audit_server_log= new Model_Audit_ServerLog();
    	$audit_server_log->timestamp= $this->_timestamp;
    	$audit_server_log->usuario=(Zend_Auth::getInstance()->hasIdentity())?Zend_Auth::getInstance()->getIdentity():'anonimo';
    	$audit_server_log->nivel_logueo=$nivel_logueo;
    	$audit_server_log->method=$this->_method;
    	$audit_server_log->modulo=$this->_modulo;
    	$audit_server_log->controlador=$this->_controlador;
    	$audit_server_log->accion=$this->_accion;
	    $audit_server_log->time_elapsed=$time_elapsed;
	    $audit_server_log->peak_memory_usage=$peakUsage;
    	$audit_server_log->ids_total_impact=0;
	    $audit_server_log->ids_result='';
		if (!$ids_result->isEmpty()) {
		   $audit_server_log->ids_total_impact= $ids_result->getImpact();
		   $audit_server_log->ids_result= $ids_result->__toString();
	    }
	    
    	if ($nivel_logueo>=2)
    	{
	    	$audit_server_log->uri=$this->getRequest()->getRequestUri();
	    	$audit_server_log->cookie=var_export($this->_cookie,true);
	    	$audit_server_log->json=var_export($this->_json,true);
	    	$audit_server_log->post=var_export($this->_post,true);
	    	$audit_server_log->get=var_export($this->_get,true);
    	}
    	if ($nivel_logueo==3)
    	{
	    	$audit_server_log->response_header=var_export($this->getResponse()->getHeaders(),true);
	    	$audit_server_log->response_body=$this->getResponse()->getBody();
    	}
    	$audit_server_log->trySave();
    }

}
