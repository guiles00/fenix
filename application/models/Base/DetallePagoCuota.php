<?php

abstract class Model_Base_DetallePagoCuota extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('detalle_pago_cuota');
		$this->hasColumn('detalle_pago_cuota_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
        	 'autoincrement' => true,
		));

		$this->hasColumn('detalle_pago_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
              'notnull' => false,
        	 'autoincrement' => true,
		));

		$this->hasColumn('cuota_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
              'notnull' => false,
        	 'autoincrement' => true,
		));
		

		$this->hasColumn('importe', 'float', 16, array(
             'type' => 'float',
             'length' => '16',
              'notnull' => false,
        	 'autoincrement' => true,
		));
		$this->hasColumn('fecha_pago', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		$this->hasColumn('fecha_cobro', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		
			$this->hasColumn('pago_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
              'notnull' => false,
        	 'autoincrement' => true,
		));
		

	}

}

