<?php
require_once ('IndexController.php');
class MenuesController extends IndexController
{
	private $_model_menu = null;
	private $_services_simple_crud = null;
	private $_sesion = null;
	private $_usuario = null;
	
	public function init()
	{
		//use the parent initialization
		/* parent::init();
		 * 
		 */
		if ( ! (Zend_Auth::getInstance()->hasIdentity())  ) {
		
		$this->_helper->redirector('index','login','default');
		}
		
		
		//$this->_forward('index', 'index', 'default');	
		//pregunto si tiene acceso a este modulo
		//si no tiene voy al index
		$this->_helper->layout->disableLayout();
		$this->_model_menu = new Model_Menu();
		$this->_services_simple_crud = new Services_SimpleCrud();
		$this->_sesion = Domain_Sesion::getInstance();
		$this->_usuario = $this->_sesion->getUsuario();
		
		if( $this->_usuario->getAcl()->hasPermission('solicitud') ) {
			
			//exit('tiene permiso');
		} else {
		//exit('no tiene permiso');	
		} 
			
		
		$logger = Zend_Registry::get('logger');
    	$logger->log($this->_usuario->getEntidad() , Zend_Log::INFO);
    	
		 
	}

	public function indexAction()
	{

		 
		//$logger = Zend_Registry::get('logger');
    	//$logger->log('entro'	, Zend_Log::INFO);     	        	
		
    	//
		/*$this->getResponse()
     	    ->setHeader('Status','409 Conflict');
		$logger = Zend_Registry::get('logger');
    	$logger->log('entro'	, Zend_Log::INFO);     	        	
		$this->_helper->redirector->gotoSimple('index','login','default');*/
		//Trae el usuario para ver el perfil/permisos/roles/etc..
		$this->_forward('list','menues','default');
		
    	// Si no esta logueado que vuelva al login
		
	}

	public function listAction()
	{
		$service_model = new Services_SimpleCrud();
		$rows = $this->_services_simple_crud->getAll($this->_model_menu);
		$page=$this->_getParam('page',1);
		 
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);

		$this->view->rows = $paginator;

	}
	public function viewAction()
	{
		$params = $this->_request->getParams();
		$menu = $this->_services_simple_crud
		->getById($this->_model_menu,array('primary_key'=>'menu_id','value'=>$params['id']));
		
		$this->view->menu = $menu;

	}
	public function addAction()
	{
		$params = $this->_request->getParams();
		$values =  array_slice($params,4); //saca la data de mas 

		if( isset($params['add']) ){
			$this->_services_simple_crud->save($this->_model_menu,$values);
			echo "agrego";	
		}

	}
	public function editAction()
	{
		$params = $this->_request->getParams();
		$values =  array_slice($params,3); //saca la data de mas
		$this->_services_simple_crud->save($this->_model_menu,$values);
		$this->view->params = $params;

	}

	public function deleteAction()
	{
			$this->_helper->viewRenderer->setNoRender();
			$params = $this->_request->getParams();
			
			$this->_services_simple_crud->delete($this->_model_menu,$params['id']);
			$this->view->params = $params;
			
	}

}