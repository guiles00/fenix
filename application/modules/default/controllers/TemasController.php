<?php
require_once ('IndexController.php');
class TemasController extends IndexController
{
	public function init()
	{
		//use the parent initialization
		parent::init();
	}
	
    public function indexAction()
    
    {
    	$this->_helper->layout->disableLayout();
    	echo "en index";
    }

    public function cambiartemasAction()
    {
        $this->view->estilo_items = Doctrine::getTable('Model_Estilo')->findAll();
    }
    
    public function guardarTemaAction()
    {
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender();
    	$estilo_id=$this->getRequest()->getParam('estilo_id');
    	
    	$model = new Model_Usuario();    	
		$usuario= $model->getTable()->findOneByUsuario( Zend_Auth::getInstance()->getIdentity() );
		
		$estilo=Doctrine::getTable('Model_Estilo')->find($estilo_id)->toArray();
		if (isset($estilo['estilo_id'])) {
			$userdata=unserialize($usuario->userdata);
			$userdata['tema'] = $estilo['estilo_id']; 		
	    	$usuario->userdata = serialize( $userdata );
	    	$usuario->save();
		   	//echo "Elegido el tema ".$estilo['nombre'].", se ha guardado su seleccion".Zend_Auth::getInstance()->getIdentity();
		   	echo '<script>$("#linc").attr({ href: "'.$estilo['url'].'" });</script>';
		}
    }
}