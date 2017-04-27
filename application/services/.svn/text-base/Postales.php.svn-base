<?php
class Servicios_Postales
{
	private $_autorizador;
	
	function __construct(Servicios_Autorizacion_Postales $sa)
	{
		if ($sa instanceof Servicios_Autorizacion_Postales)
		{
			$this->_autorizador=$sa;	
		}else{
			$this->_autorizador=null;//new Servicios_Autorizacion_Postales($u);	
		}
    } 
    
    public function setAutorizador(Servicios_Autorizacion_Postales $sa)
    {
    	if ($sa instanceof Servicios_Autorizacion_Postales)
		{
			$this->_autorizador=$sa;	
		}else{
			throw new Servicios_Exception_Postales_Generic("clase Autorizadora invalida!");	
		}
    }
    
    public function getAutorizador()
    {
    	if ($this->_autorizador instanceof Servicios_Autorizacion_Postales)
		{
    		return $this->_autorizador;
		}else{
			throw new Servicios_Exception_Postales_Generic("No hay clase Autorizadora!");
		}
    }
    
    public function ejemploDeMetodo(/*Model_Expediente $e, Model_PasoExpediente $pe,*/ Servicios_Autorizacion_Postales  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Postales) $sa=$this->getAutorizador();
		//	
		if ($sa->isAllowed('metodo_a_consultar'/*, $e, $pe*/))
		{
			return true;
		}else{
			throw new Servicios_Exception_Postales_Generic("No Autorizado!");
		}
	}
	
	 /**
     * getListadoTiposDomiciliosPersonaAplicables
     *
     * @param MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params
     * @param Servicios_Autorizacion_Postales  $sa
     * @return MyZend_Doctrine_Record_GridPanelFacade_Response_List
     */	
    public function getListadoTiposDomiciliosPersonaAplicables(MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params, Servicios_Autorizacion_Postales   $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Postales) $sa=$this->getAutorizador();
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model= new Model_TipoDomicilio();
			$options=new MyZend_Doctrine_Record_GridPanelFacade_Config_Options(array());			
			$grid_panel_facade=new MyZend_Doctrine_Record_GridPanelFacade_Base($model, $options);
			return $grid_panel_facade->ajaxGridList($params);
		}else{
			throw new Servicios_Exception_Postales_Generic("No Autorizado!");
		}
	}
	
	 /**
     * getListadoCalles
     *
     * @param MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params
     * @param Servicios_Autorizacion_Postales  $sa
     * @return MyZend_Doctrine_Record_GridPanelFacade_Response_List
     */	
    public function getListadoCalles(MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params, Servicios_Autorizacion_Postales   $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Postales) $sa=$this->getAutorizador();
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model= new Model_Calle();
			$options=new MyZend_Doctrine_Record_GridPanelFacade_Config_Options(array());			
			$grid_panel_facade=new MyZend_Doctrine_Record_GridPanelFacade_Base($model, $options);
			return $grid_panel_facade->ajaxGridList($params);
		}else{
			throw new Servicios_Exception_Postales_Generic("No Autorizado!");
		}
	}
}