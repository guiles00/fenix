<?php
require_once ('IndexController.php');

class Entidad_EntidadController extends Entidad_IndexController
{
	private $_model_entidad = null;
	private $_services_entidad = null;
	private $_services_simple_crud = null;

	public function init()
	{
		$this->_helper->layout->disableLayout();
		$this->_model_entidad = new Model_Entidad();
		$this->_services_simple_crud = new Services_SimpleCrud();

	}

	// Por ahora uso el index para realizar las pruebas
	public function indexAction()
	{
		/*
		 $model = $this->_model_entidad;
		 $service_model = new Services_SimpleCrud();
		 $rows = $service_model->getAll($model);

		 $row = $service_model->getById($model,array("primary_key"=>'entidad_id' ,"value"=>1)  );

		 echo "<pre>";
		 //print_r($rows);
		 print_r($row);
		 $logger = Zend_Registry::get('logger');
		 $logger->log($entidades,Zend_Log::INFO);
		 //exit;
		 */
	}

	public function listAction()
	{
		$service_model = new Services_SimpleCrud();
		$entidades = $this->_services_simple_crud->getAll($this->_model_entidad);
		$this->view->entidades = $entidades;

	}
	public function viewAction()
	{

		$params = $this->_request->getParams();
		$entidad = $this->_services_simple_crud
		->getById($this->_model_entidad,array('primary_key'=>'entidad_id','value'=>$params['id']));
		$this->view->entidad = $entidad;

	}
	public function addAction()
	{
		$params = $this->_request->getParams();
		$values =  array_slice($params,4); //saca la data de mas
			
		if( isset($params['add']) ){
			$this->_services_simple_crud->save($this->_model_entidad,$values);
		}

	}
	public function editAction()
	{
		$params = $this->_request->getParams();
		$values =  array_slice($params,3); //saca la data de mas
		$this->_services_simple_crud->save($this->_model_entidad,$values);
		$this->view->values = $values;

	}

	public function deleteAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		$params = $this->_request->getParams();
			
		$this->_services_simple_crud->delete($this->_model_entidad,$params['id']);
		$this->view->params = $params;
			
	}
}