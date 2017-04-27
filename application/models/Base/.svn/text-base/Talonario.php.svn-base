<?php

abstract class Model_Base_Talonario extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('talonario');
		$this->hasColumn('talonario_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
        	 'autoincrement' => true,
		));
		$this->hasColumn('codigo', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('numero', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

	}

}
