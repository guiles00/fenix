<?php
   class Domain_TipoPoliza{
	
	public static function getIdByName($tipo_poliza){
		
		$m_tipo_poliza = new Model_TipoPoliza();
		

		$tipo_poliza = $m_tipo_poliza->getTable()
		->createQuery()
		->andWhere("nombre = ? ",$tipo_poliza)
		->execute()
		->toArray();

		return $tipo_poliza[0]['t_poliza_id'];
		
		 
	}
	
   public static function getNameById($tipo_poliza){
		
		$m_tipo_poliza = new Model_TipoPoliza();
		

		$tipo_poliza = $m_tipo_poliza->getTable()
		->createQuery()
		->andWhere("t_poliza_id = ? ",$tipo_poliza)
		->execute()
		->toArray();

		return $tipo_poliza[0]['nombre'];
		
		 
	}
	
	public static function getTipos(){
		
		$m_tipo_poliza = new Model_TipoPoliza();
		

		$poliza_tipos = $m_tipo_poliza->getTable()
		->createQuery()
		->execute()
		->toArray();

		return $poliza_tipos;
		
		 
	}
	
	
} 