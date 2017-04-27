<?php
class Servicios_Clasificadores
{
	private $_autorizador;
	
	function __construct(Servicios_Autorizacion_Clasificadores $sa)
	{
		if ($sa instanceof Servicios_Autorizacion_Clasificadores)
		{
			$this->_autorizador=$sa;	
		}else{
			$this->_autorizador=null;//new Servicios_Autorizacion_Clasificadores($u);	
		}
    } 
    
    public function setAutorizador(Servicios_Autorizacion_Clasificadores $sa)
    {
    	if ($sa instanceof Servicios_Autorizacion_Clasificadores)
		{
			$this->_autorizador=$sa;	
		}else{
			throw new Servicios_Exception_Clasificadores_Generic("clase Autorizadora invalida!");	
		}
    }
    
    public function getAutorizador()
    {
    	if ($this->_autorizador instanceof Servicios_Autorizacion_Clasificadores)
		{
    		return $this->_autorizador;
		}else{
			throw new Servicios_Exception_Clasificadores_Generic("No hay clase Autorizadora!");
		}
    }
    
    public function ejemploDeMetodo(/*Model_Expediente $e, Model_PasoExpediente $pe,*/ Servicios_Autorizacion_Clasificadores  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Clasificadores) $sa=$this->getAutorizador();
		//	
		if ($sa->isAllowed('metodo_a_consultar'/*, $e, $pe*/))
		{
			return true;
		}else{
			throw new Servicios_Exception_Clasificadores_Generic("No Autorizado!");
		}
	}
	
}