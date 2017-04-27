<?php
require_once ('IndexController.php');
class UsuarioController extends IndexController
{
	private $_usuario = null;
	private $_services_simple_crud = null;

	public function init()
	{
		$this->_helper->layout->disableLayout();
		$this->_usuario = new Domain_Usuario();
		$this->_services_simple_crud = new Services_SimpleCrud();
	}

	public function indexAction()
	{
		$this->_forward('list','usuario','default');
	}

	public function listAction(){
			
		$service_model = new Services_SimpleCrud();
		$rows = $this->_services_simple_crud->getAll($this->_usuario->getModel());
		
			$page=$this->_getParam('page',1);
		 
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		
		$this->view->rows = $paginator;
	}

	public function viewAction()
	{
		$params = $this->_request->getParams();
		$row = $this->_services_simple_crud
		->getById($this->_usuario->getModel(),array('primary_key'=>'usuario_id','value'=>$params['id']));

		$this->view->row = $row;

	}

	public function editAction()
	{
		$params = $this->_request->getParams();
		$values =  array_slice($params,3); //saca la data de mas
		$this->_services_simple_crud->save($this->_usuario->getModel(),$values);
		$this->view->params = $params;

	}

	public function addAction()
	{
		$params = $this->_request->getParams();
		$values =  array_slice($params,4); //saca la data de mas
		
		if( isset($params['add']) ){
			echo "estraa";	
			$this->_services_simple_crud->save($this->_usuario->getModel(),$values);
		}
	}
		public function ajaxListadoCargosUsuarioAction()
		{
			$this->_helper->viewRenderer->setNoRender();
			$model = new Model_CargoUsuario();
			$options=array();
			$model_grid_panel_facade = new MyZend_Doctrine_Record_GridPanelFacade_Base($model, $options);

			$params=$this->_getAllParams();
			unset($params['columns']);//.=',ubicacion,secretaria';
			$respuesta = $model_grid_panel_facade->ajaxGridList($params);

			$this->getResponse()->setHeader('Content-Type', 'application/json');
			$json = Zend_Json::encode($respuesta);
			$this->getResponse()->appendBody($json);
		}

		public function getUbicacionIdUsuarioAction()
		{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			$respuesta=array('success'=>true, data=>array('ubicacion_id'=>MyZend_Usuario::getInstance()->getUbicacionId()) );
			$this->getResponse()->setHeader('Content-Type', 'application/json');
			$json = Zend_Json::encode($respuesta);
			$this->getResponse()->appendBody($json);
		}

		public function getUbicacionesIdJuzgadoUsuarioAction()
		{
			$this->_helper->layout->disableLayout();
			$this->_helper->viewRenderer->setNoRender();
			// **
			$ubicaciones_id= MyZend_Usuario::getInstance()->getUbicacionesIdJuzgadoUsuario();
			// **
			$respuesta=array('success'=>true, data=>array('ubicaciones_id'=>$ubicaciones_id) );
			$this->getResponse()->setHeader('Content-Type', 'application/json');
			$json = Zend_Json::encode($respuesta);
			$this->getResponse()->appendBody($json);
		}


	}