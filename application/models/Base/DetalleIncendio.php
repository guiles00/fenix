<?php
abstract class Model_Base_DetalleIncendio extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('detalle_incendio');
		$this->hasColumn('detalle_incendio_id', 'integer', 4, array(
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

		
		$this->hasColumn('descripcion_adicional', 'string', 255, array(
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

