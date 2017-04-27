<?php
class Servicios_Autenticacion
{
	private $_autorizador;
	
	function __construct(Servicios_Autorizacion_Autenticacion $sa)
	{
		if ($sa instanceof Servicios_Autorizacion_Autenticacion)
		{
			$this->_autorizador=$sa;	
		}else{
			$this->_autorizador=null;//new Servicios_Autorizacion_Autenticacion($u);	
		}
    } 
    
    public function setAutorizador(Servicios_Autorizacion_Autenticacion $sa)
    {
    	if ($sa instanceof Servicios_Autorizacion_Autenticacion)
		{
			$this->_autorizador=$sa;	
		}else{
			throw new Servicios_Exception_Autenticacion_Generic("clase Autorizadora invalida!");	
		}
    }
    
    public function getAutorizador()
    {
    	if ($this->_autorizador instanceof Servicios_Autorizacion_Autenticacion)
		{
    		return $this->_autorizador;
		}else{
			throw new Servicios_Exception_Autenticacion_Generic("No hay clase Autorizadora!");
		}
    }
    
    public function ejemploDeMetodo(/*Model_Expediente $e, Model_PasoExpediente $pe,*/ Servicios_Autorizacion_Autenticacion  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Autenticacion) $sa=$this->getAutorizador();
		//	
		if ($sa->isAllowed('metodo_a_consultar'/*, $e, $pe*/))
		{
			return true;
		}else{
			throw new Servicios_Exception_Autenticacion_Generic("No Autorizado!");
		}
	}
	
}