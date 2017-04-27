<?php


abstract class Model_Base_UsuarioPerfil extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('usuario_perfil');
		$this->hasColumn('usuario_perfil_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
             'autoincrement' => true,
		));
		

		$this->hasColumn('usuario_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('perfil_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));
		
		
	}
}

