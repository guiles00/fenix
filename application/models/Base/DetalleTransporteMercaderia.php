<?php

abstract class Model_Base_DetalleTransporteMercaderia extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('detalle_transporte_mercaderia');
		$this->hasColumn('detalle_transporte_mercaderia_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
        	 'autoincrement' => true,
		));

		$this->hasColumn('tipo_garantia_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => false,
        	 'autoincrement' => false,
		));

		$this->hasColumn('motivo_garantia_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => false,
        	 'autoincrement' => false,
		));
		$this->hasColumn('beneficiario_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
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
		$this->hasColumn('transporte', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		$this->hasColumn('cuit_transportista', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		$this->hasColumn('origen_desde', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		$this->hasColumn('origen_hasta', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));


		$this->hasColumn('tipo_transporte_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => false,
        	 'autoincrement' => false,
		));

		$this->hasColumn('marca', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('modelo', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));



		$this->hasColumn('patente', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));


		$this->hasColumn('patente_semi', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('custodia_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => false,
        	 'autoincrement' => false,
		));

		$this->hasColumn('nombre_chofer', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));


		$this->hasColumn('documento_chofer', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('datos_custodia', 'string', 255, array(
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

