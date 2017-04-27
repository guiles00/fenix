<?php

abstract class Model_Base_MotivoGarantia extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('motivo_garantia');
		$this->hasColumn('motivo_garantia_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
             'autoincrement' => true,
		));
		
		$this->hasColumn('tipo_poliza_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('tipo_garantia_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		
		
		$this->hasColumn('motivo_garantia', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
		));

	}
}

