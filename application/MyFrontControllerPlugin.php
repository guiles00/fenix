<?php
class MyFrontControllerPlugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
    	$recursos_mc_publicos=array('default*login');
    	$recursos_mca_publicos=array('default*barcode*draw39',
    								'default*error*error',
    								'default*error*no-autorizado',
    	    						'default*error*token-invalido',
    								'default*login*logout',
    								);
    	
	//return true; //bypass temporal del Control de ACLs
	$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
	$auth = Zend_Auth::getInstance();
	$data_tmp['modulo']=$request->getModuleName();
	$data_tmp['controlador']=$request->getControllerName();
	$data_tmp['accion']=$request->getActionName();
	
	$recurso_m_tmp  =$data_tmp['modulo'];	
	$recurso_mc_tmp =$recurso_m_tmp. "*".$data_tmp['controlador'];	
	$recurso_mca_tmp=$recurso_mc_tmp."*".$data_tmp['accion'];	
	
	if (!$auth->hasIdentity()){
	    if (!in_array($recurso_mc_tmp,$recursos_mc_publicos) && !in_array($recurso_mca_tmp,$recursos_mca_publicos) )
	    {
		$redirector->gotoSimple('index', 'login', 'default', array());
	    }
	}else{
	    if (!in_array($recurso_mca_tmp,$recursos_mca_publicos)){
	    	$permitido=false;
			try{
				$token_ok= ($request->getParam('token')==MyZend_Acl::getInstance()->getToken());
				$token_required= ($recurso_mca_tmp!='default*index*index');
				$permitido_por_acl=MyZend_Acl::getInstance()->getAcl()->isAllowed($auth->getIdentity(), $recurso_mca_tmp);
		    	$permitido = ($token_ok||!$token_required) && $permitido_por_acl;
			}catch(Exception $e){}
			if (!$permitido){
		    	if (!$permitido_por_acl){
		    		if ($recurso_mca_tmp=='default*index*index')
		    		{
						Zend_Auth::getInstance()->clearIdentity();
						MyZend_Acl::getInstance()->clearAcl();
						// 	Borro la Session
						Zend_Session::destroy();
						$redirector->gotoSimple('index', 'login', 'default', array());
		    		}else{
		    			$redirector->gotoSimple('no-autorizado','error','default',$data_tmp);	
		    		}
		    	}else if (!$token_ok){
			    	$redirector->gotoSimple('token-invalido','error','default',$data_tmp);
		    	}
			}
	    }
	}
    }
    
    public function dispatchLoopShutdown()
    {
    	return;
    	//deshabilitado todo lo de abajo
    	$profiler = Zend_Registry::get('profiler_iurix');
    	$logger = Zend_Registry::get('logger');
    	$time = 0;
    	$data_profiler=array();
	foreach ($profiler as $event){
	    $time += $event->getElapsedSecs();
	    $item=array();
	    $item['name']=$event->getName();
	    $item['ElapsedSecs']=$event->getElapsedSecs();
	    $item['Query']=$event->getQuery();
	    $item['Params']=$event->getParams();
	    $data_profiler[]=$item;
	    $logger->log(array('Profiler_iurix_query'=>$item), Zend_Log::INFO);
	}
	$data_profiler['total_time']= $time;
	$logger->log(array('Profiler_iurix_total_time'=>$time), Zend_Log::INFO);
    	$response = $this->getResponse();
    	$response->setBody($response->getBody()."");
    }
}