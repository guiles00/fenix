<?php
require_once ('IndexController.php');
class LoginController extends IndexController
{


	public function getAuthAdapter(array $params)
	{
		$manager = Doctrine_Manager::getInstance();
		$conn =	$manager->getCurrentConnection();
		$authAdapter = new MyZend_Doctrine_Auth_Adapter_Adapter($conn);
		$authAdapter->setTableName('Model_usuario u')
		->setIdentityColumn('u.username')
		->setCredentialColumn('u.password')
		->setIdentity($params['username'])
		->setCredentialTreatment('md5(?)')
		->setCredential($params['password']);
		return $authAdapter;
	}

	public function preDispatch()
	{
		/*
		 if (Zend_Auth::getInstance()->hasIdentity()) {
		 // If the user is logged in, we don't want to show the login form;
		 // however, the logout action should still be available
		 if (('logout' != $this->getRequest()->getActionName())&& ('recargar-acl' != $this->getRequest()->getActionName())&& ('cambiar-clave' != $this->getRequest()->getActionName()))
		 {
		 $this->_helper->redirector('index', 'index', 'index');
		 }
		 } else {
		 // If they aren't, they can't logout, so that action should
		 // redirect to the login form
		 if ('logout' == $this->getRequest()->getActionName()) {
		 $this->_helper->redirector('login');
		 }
		 }
		 */
	}

	public function indexAction()
	{
		$this->_helper->layout->disableLayout();
		//  $this->view->headLink(array('rel' => 'shortcut icon', 'type' => 'image/x-icon','href' => '/images/favicon.ico'),'PREPEND');
		//  $this->view->form = $this->getForm();
		//	$this->getResponse()
		//	    ->setHeader('Status','409 Conflict');

	}

	public function processAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$request = $this->getRequest();
		$params = $this->_request->getParams();
	  
		// Check if we have a POST request
		/*if (!$request->isPost()) {
		return $this->_helper->redirector('index');
		}*/
		// Obtengo el formulario que se envio al iniciar sesion
		// echo "<pre>";
		// print_r($params);
		// exit;
	  

		$auth    = Zend_Auth::getInstance();

		// Get our authentication adapter and check credentials
		$authAdapter = $this->getAuthAdapter($params);
		$result  = $auth->authenticate($authAdapter);
		echo "<pre>";
		print_r($result);


		if($result->isValid()){

			//Por ahora hago una consulta, despues tengo que ver por que
			//no me tre desde este metodo
			/*$userInfo = $authAdapter->getResultRowObject();
			 $authStorage = $auth->getStorage();*/
			$identity = Zend_Auth::getInstance()->getIdentity();
			$m_usuario = new Model_Usuario();
			
			$userInfo = $m_usuario
			->getTable()
			->createQuery()
			->andWhere( "username = ?" , $identity )
			->execute()
			->toArray();
			//$userInfo = $m_usuario->getTable()->findByUsername('{$identity}');
			 $_SESSION['userInfo']['usuario_id'] = $userInfo[0]['usuario_id'];
			 $_SESSION['userInfo']['username']= $userInfo[0]['username'];

			// $logger = Zend_Registry::get ( 'logger' );
			//$logger->log ( "es valido", Zend_Log::INFO );

			$this->_helper->redirector('index', 'index','default');
		}else{
			$this->_helper->redirector('index','login','default');
		}
		// En el momento de loguearse correctamente, cargo la acl para el usuario
		//MyZend_Acl::getInstance()->setUsuario($auth->getIdentity());
		// We're authenticated! Redirect to the home page
		//$this->_helper->redirector('index', 'index');

	}

	public function logoutAction()
	{
		$this->_helper->layout->disableLayout();
		Zend_Auth::getInstance()->clearIdentity();
		MyZend_Acl::getInstance()->clearAcl();
		// Borro la Session
		Zend_Session::destroy();
		$this->_helper->redirector('index', 'index');
	}

	public function recargarAclAction() {
		$this->_helper->layout->disableLayout();
		 
		$usuario = Zend_Auth::getInstance()->getIdentity();
		// Elimino el Acl
		MyZend_Acl::getInstance()->clearAcl();
		// En el momento de loguearse correctamente, cargo la acl para el usuario
		MyZend_Acl::getInstance()->setUsuario($usuario);
	}

	public function cambiarClaveAction()
	{
		$this->_helper->layout->disableLayout();
		$params=$this->_getAllParams();
		if ( isset($params['old_pass']) && isset($params['new_pass']) && isset($params['new_pass2']) ){
			$model_usuario = new Model_Usuario();
			$auth= Zend_Auth::getInstance();
			$usuario    = $model_usuario->getTable()->findOneByUsuario($auth->getIdentity()) ;
			$todo_ok=true;
			if ($params['new_pass']!=$params['new_pass2']){
				$respuesta=array("mensaje"=>'La verificaci\u00F3n de clave nueva no coincide');
				$todo_ok=false;
			}
			if ($params['old_pass']!=$usuario->clave){
				$respuesta=array("mensaje"=>'Introdujo Mal su clave actual');
				$todo_ok=false;
			}
			if ($todo_ok)
			{
				$usuario->clave=$params['new_pass'];
				$usuario->save();
				$respuesta=array("mensaje"=>'Clave cambiada con \u00E9xito');
			}
			echo Zend_Json::encode($respuesta);
			$this->_helper->viewRenderer->setNoRender();
		}
		//dejo que renderise la vista
	}
}