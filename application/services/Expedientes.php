<?php
/*
 * Capa de servicios de dominio
 * 
 * Comunica la capa de aplicacion con las entidades y repositorios
 * 
 * La acceden en nuestro caso los controladores, enviando objetos de dominio
 * (no arrays ni nada de eso). Estos objetos pueden ser "Managed" (ya existentes
 *  y manejados por el ORM), o objetos nuevos, volatiles, que pueden persistirse, 
 *  o usar solo como temporales.
 * 
 * La idea es que esta capa pueda ser tambien testeada con el PHPUnit.
 * 
 * Toda la logica de negocio sera contenida aquí.
 * 
 * Vamos a empezar a usar tambien excepciones, ver el tema de "nested exceptions"
 * 
 * Cada capa tendra sus excepciones propias
 */

/*
 * Esto aun es un borrador...
 */

/* Asi se prueba:
 *     	$u=MyZend_Usuario::getInstance()->getUsuario();
    	$sae=new Servicios_Autorizacion_Expedientes($u);
    	$prueba=new Servicios_Expedientes($sae);
    	if ($prueba->addPasoExpediente()) echo "Oka";
    	die();
 * */
class Servicios_Expedientes
{
	private $_autorizador;
	
	function __construct(Servicios_Autorizacion_Expedientes $sae)
	{
		if ($sae instanceof Servicios_Autorizacion_Expedientes)
		{
			$this->_autorizador=$sae;	
		}else{
			$this->_autorizador=null;//new Servicios_Autorizacion_Expedientes($u);	
		}
    } 
    
    public function setAutorizador(Servicios_Autorizacion_Expedientes $sae)
    {
    	if ($sae instanceof Servicios_Autorizacion_Expedientes)
		{
			$this->_autorizador=$sae;	
		}else{
			throw new Servicios_Exception_Expedientes_Generic("clase Autorizadora invalida!");	
		}
    }
    
    public function getAutorizador()
    {
    	if ($this->_autorizador instanceof Servicios_Autorizacion_Expedientes)
		{
    		return $this->_autorizador;
		}else{
			throw new Servicios_Exception_Expedientes_Generic("No hay clase Autorizadora!");
		}
    }
    
    public function addPasoExpediente(Model_Expediente $e, Model_PasoExpediente $pe, Servicios_Autorizacion_Expedientes  $sae)
	{
		if (!$sae instanceof Servicios_Autorizacion_Expedientes) $sae=$this->getAutorizador();
		//	
		if ($sae->isAllowed('addPasoExpediente', $e, $pe))
		{
			return true;
		}else{
			throw new Servicios_Exception_Expedientes_Generic("No Autorizado!");
		}
	}
	
}