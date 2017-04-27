<?php
   class Domain_TipoGarantia{
	
	public static function getIdByTipoPolizaAndName($tipo_garantia,$tipo_poliza_id){
		
		$m_tipo_garantia = new Model_TipoGarantia();
		

		$tipo_garantia = $m_tipo_garantia->getTable()
		->createQuery()
		->andWhere("nombre = ? and tipo_poliza_id = ?" ,array($tipo_garantia,$tipo_poliza_id))
		->execute()
		->toArray();

		return $tipo_garantia[0]['t_garantia_id'];
		
		 
	}
	
   public static function getNameByTipoPolizaAndId($tipo_garantia,$tipo_poliza_id){
		
		$m_tipo_garantia = new Model_TipoGarantia();
		

		$tipo_garantia = $m_tipo_garantia->getTable()
		->createQuery()
		->andWhere("t_garantia_id = ? and tipo_poliza_id = ?",array($tipo_garantia,$tipo_poliza_id))
		->execute()
		->toArray();

		return $tipo_garantia[0]['nombre'];
		 
	}
	
   public static function getTipoGarantiaByTipoPoliza($tipo_poliza_id){


   	$m_tipo_garantia = new Model_TipoGarantia();
		

		$tipo_garantias = $m_tipo_garantia->getTable()
		->createQuery()
		->andWhere("tipo_poliza_id = ?",$tipo_poliza_id)
		->execute()
		->toArray();

		return $tipo_garantias;

	}
} 