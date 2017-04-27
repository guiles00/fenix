<?php
require_once ('IndexController.php');
class OpcionesController extends IndexController
{
	public function init()
	{
		//use the parent initialization
		parent::init();
	}
	
    public function indexAction()
    {
    }

    public function guardarMaintabsAction()
    {
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender();
    	$parametros = $this->getRequest()->getParams();
    	
    	$model = new Model_Usuario();    	
		$usuario= $model->getTable()->findOneByUsuario( Zend_Auth::getInstance()->getIdentity() );
		
		$userdata=unserialize($usuario->userdata);
		$userdata['maintabs'] = $parametros['maintabs'];		
    	$usuario->userdata = serialize( $userdata );
    	$usuario->save();
    	echo "se han guardado los tabs activos";
    }

    public function traerMaintabsAction()
    {
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender();
    	$parametros = $this->getRequest()->getParams();
    	
    	$model = new Model_Usuario();    	
		$usuario= $model->getTable()->findOneByUsuario( Zend_Auth::getInstance()->getIdentity() );		
		$userdata=unserialize($usuario->userdata);
		
		echo $userdata['maintabs'];		
    }

    public function traerUrlEstiloAction()
    {
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender();
    	$parametros = $this->getRequest()->getParams();
    	
    	$model = new Model_Usuario();    	
		$usuario= $model->getTable()->findOneByUsuario( Zend_Auth::getInstance()->getIdentity() );		
		$userdata=unserialize($usuario->userdata);
		
		if (!isset($userdata['tema'])) {
			$userdata['tema']=1;
		}
		$estilo=Doctrine::getTable('Model_Estilo')->find($userdata['tema']);
		if (isset($estilo) && $estilo!=null){
			$estilo=$estilo->toArray();
			echo $estilo['url'];
		}
    }
}