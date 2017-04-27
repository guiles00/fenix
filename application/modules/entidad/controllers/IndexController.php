<?php
class Entidad_IndexController extends Zend_Controller_Action
{

	public function init()
	{

		 
		if($this->_request->isXmlHttpRequest() || ($this->getRequest()->getParam('ajax')==1)){
			$this->_helper->layout->disableLayout();
		}


	}

	public function indexAction()
	{
		$params = $this->_request->getParams();
		$logger = Zend_Registry::get('logger');
		$logger->log($params, Zend_Log::INFO);
		$this->_forward('view','entidad','entidad');
	}


}