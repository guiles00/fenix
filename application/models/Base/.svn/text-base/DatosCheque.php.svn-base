<?php


abstract class Model_Base_DatosCheque extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('datos_cheque');
		$this->hasColumn('datos_cheque_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
             'autoincrement' => true,
		));

		
		$this->hasColumn('movimiento_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => false,
             'autoincrement' => false,
		));
		
		$this->hasColumn('banco', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		$this->hasColumn('numero', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		$this->hasColumn('importe', 'string', 36, array(
             'type' => 'string',
             'length' => '36',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
	}

public function setUp()
    {
        $this->hasMany('Model_MovimientoPoliza', array(
                'local' => 'movimiento_id',
                'foreign' => 'movimiento_id'
            )
        );
    }
	

}

