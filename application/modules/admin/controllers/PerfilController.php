<?php
require_once ('IndexController.php');
class Admin_PerfilController extends Admin_IndexController
{
	private $_perfil = null;
	private $_services_simple_crud = null;

	public function init()
	{
		$this->_helper->layout->disableLayout();
		$this->_perfil = new Domain_Perfil();
		$this->_services_simple_crud = new Services_SimpleCrud();
	}

	public function indexAction()
	{
		$this->_forward('list','perfil','admin');
	}

	public function listAction(){
			
		$service_model = new Services_SimpleCrud();
		$rows = $this->_services_simple_crud->getAll($this->_perfil->getModel());
		
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

	
	public function viewItemsAction()
	{
		$params = $this->_request->getParams();
		
		$perfil = new Domain_Perfil($params['id']);
		$this->view->perfil_id = $params['id'];
		$this->view->perfil = $perfil->getModel()->nombre;
		echo "<pre>";
		
		//print_r($params);
		$menu_items = explode(",", $params['menu_items_list']);
		//print_r($menu_items);
		//Treame el listado del menu
		$menu = new Model_Menu();
		$this->view->menu_items = $menu->getTable()->findAll()->toArray();
		 
		//print_r($menu_items);
		 
		//$row = $this->_services_simple_crud
		//->getById($this->_usuario->getModel(),array('primary_key'=>'usuario_id','value'=>$params['id']));

		//$this->view->row = $row;

	}
	
public function saveMenuPerfilAction()
	{
		$params = $this->_request->getParams();
		
		echo "<pre>";
		//print_r($params);
		$perfil = new Domain_Perfil();
		$perfil->deleteByPerfilId($params['perfil_id']);
		
		$menu_items = explode(",", $params['menu_items_list']);
		$perfil->addMenuByPerfil($params['perfil_id'],$menu_items);
		print_r($menu_items);
		//Borro 
		//Treame el listado del menu
		//$menu = new Model_Menu();
		//$this->view->menu_items = $menu->getTable()->findAll()->toArray();
		 
		//print_r($menu_items);
		 
		//$row = $this->_services_simple_crud
		//->getById($this->_usuario->getModel(),array('primary_key'=>'usuario_id','value'=>$params['id']));

		//$this->view->row = $row;

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
			
			$this->_services_simple_crud->save($this->_perfil->getModel(),$values);
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
