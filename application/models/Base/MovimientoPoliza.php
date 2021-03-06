<?php


abstract class Model_Base_MovimientoPoliza extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('movimiento_poliza');
		$this->hasColumn('movimiento_poliza_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
             'autoincrement' => true,
		));

		$this->hasColumn('movimiento_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('poliza_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

	}

	public function setUp()
	{
		$this->hasOne('Model_Poliza', array(
                'local' => 'poliza_id',
                'foreign' => 'poliza_id'
                )
                );
	}
}

