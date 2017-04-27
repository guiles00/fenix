<?php
class MyZend_Doctrine_Record_GridPanelFacade_Config_Options
{
	
	// @todo Hacer los seters y geters, usar propiedades fijas, 
	// guardarlas como variables separadas, que sea un objeto 
	// con propiedades bien definidas, que no adminta dinamicas
	private $_options;
	
	public function __construct($params=array())
	{
		$this->_options=$params;
    }
    
	public function fromArray($params=array())
	{
		$this->_options=$params;
	}	
    
	public function toArray()
	{
		return $this->_options;
	}	

}