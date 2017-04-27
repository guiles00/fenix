<?php
class Servicios_GUI
{
	private $_autorizador;
	
	function __construct(Servicios_Autorizacion_GUI $sa)
	{
		if ($sa instanceof Servicios_Autorizacion_GUI)
		{
			$this->_autorizador=$sa;	
		}else{
			$this->_autorizador=null;//new Servicios_Autorizacion_GUI($u);	
		}
    } 
    
    public function setAutorizador(Servicios_Autorizacion_GUI $sa)
    {
    	if ($sa instanceof Servicios_Autorizacion_GUI)
		{
			$this->_autorizador=$sa;	
		}else{
			throw new Servicios_Exception_GUI_Generic("clase Autorizadora invalida!");	
		}
    }
    
    public function getAutorizador()
    {
    	if ($this->_autorizador instanceof Servicios_Autorizacion_GUI)
		{
    		return $this->_autorizador;
		}else{
			throw new Servicios_Exception_GUI_Generic("No hay clase Autorizadora!");
		}
    }
    
    public function ejemploDeMetodo(/*Model_Expediente $e, Model_PasoExpediente $pe,*/ Servicios_Autorizacion_GUI  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_GUI) $sa=$this->getAutorizador();
		//	
		if ($sa->isAllowed('metodo_a_consultar'/*, $e, $pe*/))
		{
			return true;
		}else{
			throw new Servicios_Exception_GUI_Generic("No Autorizado!");
		}
	}
	
}