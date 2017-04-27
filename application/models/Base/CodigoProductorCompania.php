<?php

abstract class Model_Base_CodigoProductorCompania extends Doctrine_Record
{
	public function setTableDefinition()
	{
		$this->setTableName('codigo_productor_compania');
		$this->hasColumn('codigo_productor_compania_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => true,
        	 'autoincrement' => true,
		));
		$this->hasColumn('codigo_productor', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             'fixed' => false,
             'notnull' => false,
             'primary' => false,
             'autoincrement' => false,
		));

		$this->hasColumn('productor_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => false,
        	 'autoincrement' => false,
		));
		
		$this->hasColumn('compania_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             'primary' => false,
        	 'autoincrement' => false,
		));
		
		

	}

}

