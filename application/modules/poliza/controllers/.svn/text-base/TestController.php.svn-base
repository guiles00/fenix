<?php
require_once ('IndexController.php');

class Poliza_TestController extends Poliza_IndexController
{
	private $_solicitud = null;
	private $_sesion = null;
	private $_usuario = null;
	private $_services_solicitud = null;
	private $_t_usuario = null;


	public function init()
	{

		if ( ! (Zend_Auth::getInstance()->hasIdentity())  ) {

			$this->_helper->redirector('index','login','default');
		}

		$this->_helper->layout->disableLayout();
		$this->_solicitud = new Domain_Poliza();
		$this->_services_simple_crud = new Services_SimpleCrud();
		$this->_services_solicitud = new Services_Solicitud();
		$this->_sesion = Domain_Sesion::getInstance();
		$this->_usuario = $this->_sesion->getUsuario();
		$this->_t_usuario = $this->_usuario->getTipoUsuario();

		//le agrego aca los permisos, despues deberia ver si lo hago con el ACL q
		// Pero primero tengo que arreglarlo
		$operador_id = Domain_Helper::getHelperIdByDominioAndName('entidad','operador');
		$this->view->operador = ( $operador_id==$this->_usuario->getModel()->tipo_usuario_id )? true : false;
		
		
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
		$this->_forward('list','solicitud','poliza');

	}

	public function listAction()
	{
		$solicitud = new Domain_Solicitud();

		$params = $this->_request->getParams();

		/*
		 if(isset($params['buscar'])){
			$rows =$this->_t_usuario->findSolicitudByNumero($params['numero']);
			$this->view->buscar = true;
			}else{
			$rows = $this->_t_usuario->getSolicitudes();
			}
			*/
		 
		$rows =$this->_t_usuario->findSolicitudByNumero($params['criterio']);

		$page=$this->_getParam('page',1);
			
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];

		$this->view->rows = $paginator;
	}
	public function altaAction()
	{

		//$service_model = new Services_SimpleCrud();
		//$rows = $this->_services_simple_crud->getAll($this->_solicitud->getModel());
		//$this->view->rows = $rows;
	}
	public function altaFormularioAction()
	{

sleep(3);
$params = $this->_request->getParams();
echo "<pre>";
print_r($params);
		

	}

	
}







