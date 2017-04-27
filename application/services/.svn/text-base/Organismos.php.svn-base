<?php
class Servicios_Organismos
{
	private $_autorizador;
	
	function __construct(Servicios_Autorizacion_Organismos $sa)
	{
		if ($sa instanceof Servicios_Autorizacion_Organismos)
		{
			$this->_autorizador=$sa;	
		}else{
			$this->_autorizador=null;//new Servicios_Autorizacion_Organismos($u);	
		}
    } 
    
    public function setAutorizador(Servicios_Autorizacion_Organismos $sa)
    {
    	if ($sa instanceof Servicios_Autorizacion_Organismos)
		{
			$this->_autorizador=$sa;	
		}else{
			throw new Servicios_Exception_Organismos_Generic("clase Autorizadora invalida!");	
		}
    }
    
    public function getAutorizador()
    {
    	if ($this->_autorizador instanceof Servicios_Autorizacion_Organismos)
		{
    		return $this->_autorizador;
		}else{
			throw new Servicios_Exception_Organismos_Generic("No hay clase Autorizadora!");
		}
    }
    
    public function ejemploDeMetodo(/*Model_Expediente $e, Model_PasoExpediente $pe,*/ Servicios_Autorizacion_Organismos  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Organismos) $sa=$this->getAutorizador();
		//	
		if ($sa->isAllowed('metodo_a_consultar'/*, $e, $pe*/))
		{
			return true;
		}else{
			throw new Servicios_Exception_Organismos_Generic("No Autorizado!");
		}
	}
	
}