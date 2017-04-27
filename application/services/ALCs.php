<?php
class Servicios_ALCs
{
	private $_autorizador;
	
	function __construct(Servicios_Autorizacion_ALCs $sa)
	{
		if ($sa instanceof Servicios_Autorizacion_ALCs)
		{
			$this->_autorizador=$sa;	
		}else{
			$this->_autorizador=null;//new Servicios_Autorizacion_ALCs($u);	
		}
    } 
    
    public function setAutorizador(Servicios_Autorizacion_ALCs $sa)
    {
    	if ($sa instanceof Servicios_Autorizacion_ALCs)
		{
			$this->_autorizador=$sa;	
		}else{
			throw new Servicios_Exception_ALCs_Generic("clase Autorizadora invalida!");	
		}
    }
    
    public function getAutorizador()
    {
    	if ($this->_autorizador instanceof Servicios_Autorizacion_ALCs)
		{
    		return $this->_autorizador;
		}else{
			throw new Servicios_Exception_ALCs_Generic("No hay clase Autorizadora!");
		}
    }
    
    public function ejemploDeMetodo(/*Model_Expediente $e, Model_PasoExpediente $pe,*/ Servicios_Autorizacion_ALCs  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_ALCs) $sa=$this->getAutorizador();
		//	
		if ($sa->isAllowed('metodo_a_consultar'/*, $e, $pe*/))
		{
			return true;
		}else{
			throw new Servicios_Exception_ALCs_Generic("No Autorizado!");
		}
	}
	
}