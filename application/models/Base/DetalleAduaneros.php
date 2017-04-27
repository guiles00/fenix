<?php
abstract class Model_Base_DetalleAduaneros extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('detalle_aduaneros');
		$this->hasColumn('detalle_aduaneros_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
        	 'autoincrement' => true,
		));

		$this->hasColumn('beneficiario_id', 'integer', 4, array(
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


		$this->hasColumn('motivo_garantia_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
		  'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('despachante_aduana_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
		     'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		
		$this->hasColumn('domicilio_riesgo', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('localidad_riesgo', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('provincia_riesgo', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('acreedor_prendario', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));


		$this->hasColumn('mercaderia', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));


		$this->hasColumn('descripcion_adicional', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('bl', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		$this->hasColumn('sim', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('factura', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('documentacion_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
		     'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		
	}

}

