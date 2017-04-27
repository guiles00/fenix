<?php

abstract class Model_Base_Cobrador extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('cobrador');
		$this->hasColumn('cobrador_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
        	 'autoincrement' => true,
		));
		$this->hasColumn('nombre', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('cuit', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('domicilio', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('codigo_postal', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));


		$this->hasColumn('telefono', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));


		$this->hasColumn('provincia', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));


		$this->hasColumn('telefono', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		$this->hasColumn('localidad', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('email', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		$this->hasColumn('fecha_nacimiento', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

	}

}

