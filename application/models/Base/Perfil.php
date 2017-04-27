<?php


abstract class Model_Base_Perfil extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('perfil');
		$this->hasColumn('perfil_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
             'autoincrement' => true,
		));
		$this->hasColumn('nombre', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => true,
             'primary' => false,
             'autoincrement' => false,
		));

		
	}
}

