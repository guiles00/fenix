<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initDoctype()
	{
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
	}
	

    protected function _initAutoload()
    {
    	
    	$moduleLoader = new Zend_Application_Module_Autoloader(array( 
	     'namespace' => '', 
	     'basePath' => APPLICATION_PATH));
		$moduleLoader->addResourceType('MyZend', '../library/MyZend', 'MyZend');	 
		 $moduleLoader->addResourceType('Services', './services', 'Services');
		 $moduleLoader->addResourceType('Domain', './domain', 'Domain');
		return $moduleLoader; 
    }
    
	public function _initDomPDF(){

		//require_once('dompdf/dompdf_config.inc.php');
		//$autoloader = Zend_Loader_Autoloader::getInstance();
		//$this->getApplication()->getAutoloader()->pushAutoloader('DOMPDF_autoload', '');
		//spl_autoload_register(array('DOMPDF_autoload', 'dompdf'));
		//require_once('dompdf/dompdf_config.inc.php');
		//spl_autoload_register('DOMPDF_autoload');
		//require_once('dompdf/dompdf_config.inc.php');
//Zend_Loader::registerAutoload();
//$this->getApplication()->getAutoloader()->pushAutoloader('DOMPDF_autoload', '');
//	spl_autoload_register('DOMPDF_autoload');

	}
    //Para utilizar Doctrine
    public function _initDoctrine()
    {    	    	
        $this->getApplication()->getAutoloader()
                               ->pushAutoloader(array('Doctrine', 'autoload'));
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(
            Doctrine::ATTR_MODEL_LOADING,
            Doctrine::MODEL_LOADING_CONSERVATIVE
        );
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
		
        $doctrineConfig = $this->getOption('doctrine');
        $manager = Doctrine_Manager::getInstance();
        
        /*
         * @TODO no me gusta que sea asi, marito tu codigo no me gusta
         */
        // $entorno = getenv('ENTORNO');
$entorno = 'dev';
        $config = new Zend_Config_Ini('../config.ini',$entorno);
        
//echo "<pre>";
//print_r($config);
//exit; 
        $gseguros = $config->gseguros->toArray();

        $connection_string = "mysql://{$gseguros['user']}:{$gseguros['password']}@localhost/{$gseguros['db']}";
                
//        $connection_string = "mysql://guiles:guiles@localhost/gseguros";
        //$connection_string = "mysql://guiles:guiles@localhost/sconsultora";
        $manager->openConnection($connection_string, 'gseguros');
//       print_r($connection_string);
 //      print_r($manager);
//exit; 
		return $manager;
    }
    
/*	protected function _initPlugin()
	{	
		Zend_Loader::loadClass('MyFrontControllerPlugin', APPLICATION_PATH );
		$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new MyFrontControllerPlugin());
	}
*/
	protected function _initViewResources() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
	$view->addHelperPath(APPLICATION_PATH . '/modules/default/views/helpers/', 'My_View_Helper');
	}
	
	protected function _initTimezone()
	{
		date_default_timezone_set('America/Buenos_Aires');
	}

	protected function _initFirePHP()
	{
		$logger = new Zend_Log();
		$writer = new Zend_Log_Writer_Firebug();
		$logger->addWriter($writer);
		Zend_Registry::set('logger',$logger);
		$logger = Zend_Registry::get('logger');
		//$logger->log('El Logger fue iniciado!', Zend_Log::INFO);
	}
      
	
}
