<?php


abstract class Model_Base_TipoPoliza extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('tipo_poliza');
		$this->hasColumn('tipo_poliza_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
             'autoincrement' => true,
		));
		$this->hasColumn('nombre', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('t_poliza_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
	}
}

