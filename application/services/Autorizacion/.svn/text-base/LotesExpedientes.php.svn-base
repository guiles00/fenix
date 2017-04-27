<?php 

class Servicios_Autorizacion_LotesExpedientes
{
	private $_usuario;
	
	function __construct(Model_Usuario $u)
	{		
		if ($u instanceof Model_Usuario)
		{
			$this->_usuario=$u;
		}else{
			// Exception?
		}
	}
	
	private function isAllowedToMetodoEjemplo(/*Model_Expediente $e, Model_PasoExpediente $pe*/)
	{
		return false;		
	}
	
	public function isAllowed()
	{
		if (func_num_args()<1) return false;// o lanzo una excepcion?
		
		$args= func_get_args();
		
		$action_to_check=array_shift($args);	
		$checker_method='isAllowedTo'.ucfirst($action_to_check);
				
	    if (!method_exists($this, $checker_method))
        {
            return true; // Permitimos por defecto
        }
        else
        {
            return call_user_func_array( array($this, $checker_method), $args );
        }        
	}
}