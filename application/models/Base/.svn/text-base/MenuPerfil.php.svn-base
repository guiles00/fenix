<?php

abstract class Model_Base_MenuPerfil extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('menu_perfil');
		$this->hasColumn('menu_perfil_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
             'autoincrement' => true,
		));
		$this->hasColumn('perfil_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
             'autoincrement' => true,
		));
		$this->hasColumn('menu_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
             'autoincrement' => true,
		));
	}

}