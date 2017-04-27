<?php


abstract class Model_Base_Movimiento extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('movimiento');
		$this->hasColumn('movimiento_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
             'autoincrement' => true,
		));
	
		$this->hasColumn('cuenta_corriente_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('asegurado_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		
		$this->hasColumn('compania_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		
		$this->hasColumn('cheque_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		$this->hasColumn('moneda_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		
		$this->hasColumn('fecha_pago', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
		));
		
			$this->hasColumn('numero_factura', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
		));
		$this->hasColumn('importe', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
		));
		$this->hasColumn('importe_efectivo', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
        ));
        $this->hasColumn('cotizacion_divisa', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
        ));
		$this->hasColumn('descuento', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
		));
		
		$this->hasColumn('tipo_movimiento_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
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

        $this->hasMany('Model_DetallePagoCuota', array(
                'local' => 'poliza_id',
                'foreign' => 'detalle_pago_cuota_id'
            )
        );


    }
	

}

