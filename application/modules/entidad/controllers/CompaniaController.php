<?php
require_once ('IndexController.php');

class Entidad_CompaniaController extends Entidad_IndexController
{
	private $_compania = null;
	private $_services_simple_crud = null;
	private $_sesion = null;
	private $_usuario = null;

	public function init()
	{

		if ( ! (Zend_Auth::getInstance()->hasIdentity())  ) {

			$this->_helper->redirector('index','login','default');
		}

		$this->_helper->layout->disableLayout();
		$this->_compania = new Domain_Compania();
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
		$this->_forward('list','compania','entidad');

	}

	public function listAction()
	{

		$service_model = new Services_SimpleCrud();
		$params = $this->_request->getParams();
		$rows = $this->_services_simple_crud
		->findByName($this->_compania->getModel(),array('campo'=>'nombre','valor'=>$params['criterio']));
		$page=$this->_getParam('page',1);
		 
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(15);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];
		$this->view->rows = $paginator;
	}
	public function viewAction()
	{

		$params = $this->_request->getParams();
		$row = $this->_services_simple_crud
		->getById($this->_compania->getModel(),array('primary_key'=>'compania_id','value'=>$params['id']));

		$this->view->row = $row;

	}
	public function addAction()
	{
		$params = $this->_request->getParams();
		$values =  array_slice($params,4); //saca la data de mas

		echo"<pre>";
		print_r($params);
		if( isset($params['add']) ){
			$this->_services_simple_crud->save($this->_compania->getModel(),$values);
		}

	}
	public function editAction()
	{
		$params = $this->_request->getParams();
		$values =  array_slice($params,3); //saca la data de mas
		$this->_services_simple_crud->save($this->_compania->getModel(),$values);
		$this->view->params = $params;


	}
	public function deleteAction()
	{

	}
}