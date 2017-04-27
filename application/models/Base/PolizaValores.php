<?php

abstract class Model_Base_PolizaValores extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('poliza_valores');
		$this->hasColumn('poliza_valores_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
        	 'autoincrement' => true,
		));

		
		$this->hasColumn('moneda_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
              'notnull' => false,
        	 'autoincrement' => true,
		));
		
			$this->hasColumn('monto_asegurado', 'string', 10, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
			$this->hasColumn('iva', 'string', 10, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
			
			$this->hasColumn('prima_comision', 'string', 10, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
			$this->hasColumn('prima_tarifa', 'string', 10, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
			$this->hasColumn('premio_compania', 'string', 10, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
	
		$this->hasColumn('premio_asegurado', 'string', 10, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		
		$this->hasColumn('plus', 'string', 10, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
	

	}
	
}
