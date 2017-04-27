<?php

abstract class Model_Base_DetallePago extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('detalle_pago');
		$this->hasColumn('detalle_pago_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
        	 'autoincrement' => true,
		));

		$this->hasColumn('poliza_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
              'notnull' => false,
        	 'autoincrement' => true,
		));

		$this->hasColumn('forma_pago_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
              'notnull' => false,
        	 'autoincrement' => true,
		));
		
		$this->hasColumn('moneda_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
              'notnull' => false,
        	 'autoincrement' => true,
		));

	
	}
	
	
public function setUp()
    {
        $this->hasMany('Model_DetallePagoCuota', array(
                'local' => 'detalle_pago_id',
                'foreign' => 'detalle_pago_id'
            )
        );
    }

}

