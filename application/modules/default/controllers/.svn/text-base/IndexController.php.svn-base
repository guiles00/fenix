<?php
class IndexController extends Zend_Controller_Action
{

    public function init()
    {

    	
	//if($this->_request->isXmlHttpRequest() || ($this->getRequest()->getParam('ajax')==1)){
	  //  $this->_helper->layout->disableLayout();
	//}  
	
	//exit('Hasta aca llego');
    }

    public function indexAction()
    {
    	
    	
    	//Aca chequea que este logueado
    	if( !Zend_Auth::getInstance()->hasIdentity() ){
    		$this->_forward('index', 'login', 'default');	
    	}
    	
    	//exit;
    	//$this->_helper->layout->disableLayout();
    	//$this->view->token=MyZend_Acl::getInstance()->getToken();
    	//En index.phtml ahora cargo TODA la nueva aplicacion, y ya no deberia volver a usar phtmls
    	// las antiguas librerias tampoco seran requeridas
    	//exit('Hasta aca llego');
    	$params = $this->_request->getParams();
    	$logger = Zend_Registry::get('logger');
    	$logger->log($_SESSION, Zend_Log::INFO);
    }

    //function __call($method, $args) {
//    	exit('Hasta aca llego');
      //  $this->_forward('index/', 'index', 'default');
    //}
}