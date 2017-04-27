<?php
require_once ('IndexController.php');

class Entidad_BeneficiarioController extends Entidad_IndexController
{
	private $_beneficiario = null;
	private $_services_simple_crud = null;
	private $_sesion = null;
	private $_usuario = null;

	public function init()
	{

		if ( ! (Zend_Auth::getInstance()->hasIdentity())  ) {

			$this->_helper->redirector('index','login','default');
		}

		$this->_helper->layout->disableLayout();
		$this->_beneficiario = new Domain_Beneficiario();
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
		$this->_forward('list','beneficiario','entidad');

	}

	public function listAction()
	{

		$service_model = new Services_SimpleCrud();
		$params = $this->_request->getParams();
		$rows = $this->_services_simple_crud
		->findByName($this->_beneficiario->getModel(),array('campo'=>'nombre','valor'=>$params['criterio']));
		$page=$this->_getParam('page',1);
		 
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];
		$this->view->rows = $paginator;
	}
	public function viewAction()
	{

		$params = $this->_request->getParams();
		$row = $this->_services_simple_crud
		->getById($this->_beneficiario->getModel(),array('primary_key'=>'beneficiario_id','value'=>$params['id']));

		$this->view->row = $row;

	}
	public function addAction()
	{
	$params = $this->_request->getParams();
	$values =  array_slice($params,4); //saca la data de mas
	
		if( isset($params['add']) ){
			$this->_services_simple_crud->save($this->_beneficiario->getModel(),$values);
		}

	}
	public function editAction()
	{
		
		$params = $this->_request->getParams();
		$values =  array_slice($params,3); //saca la data de mas
		$this->_services_simple_crud->save($this->_beneficiario->getModel(),$values);
		$this->view->params = $params;
		

	}
	public function deleteAction()
	{

	}
	
}

