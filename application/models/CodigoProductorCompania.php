<?php

class Model_CodigoProductorCompania extends Model_Base_CodigoProductorCompania
{

	public static function getCompaniasByProductorId($productor_id){
		
		$m_codigo_productor_compania = new Model_CodigoProductorCompania();

		$codigo_productor_compania = $m_codigo_productor_compania->getTable()
		->createQuery()
		->where("productor_id = ? ",$productor_id)
		->execute()
		->toArray();
		
		return $codigo_productor_compania;
	}
	
	public static function getCodigoProductorByCompaniaId($productor_id,$compania_id){
		
		$m_codigo_productor_compania = new Model_CodigoProductorCompania();

		$codigo_productor_compania = $m_codigo_productor_compania->getTable()
		->createQuery()
		->where("productor_id = ? AND compania_id = ? ",array($productor_id,$compania_id))
		->execute()
		->toArray();
		
		return $codigo_productor_compania[0]['codigo_productor'];
	}
	
}
