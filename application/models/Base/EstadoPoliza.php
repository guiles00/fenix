<?php


abstract class Model_Base_EstadoPoliza extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('estado_poliza');
		$this->hasColumn('estado_poliza_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
             'autoincrement' => true,
		));
		

		$this->hasColumn('estado_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		
		$this->hasColumn('codigo', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
		));
		
		$this->hasColumn('nombre', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
		));
	}
}


