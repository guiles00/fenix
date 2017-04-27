<?php
   class Domain_EstadoPoliza{
	
	public static function getIdByCodigo($estado_poliza){
		
		$m_estado_poliza = new Model_EstadoPoliza();
		

		$estado_poliza = $m_estado_poliza->getTable()
		->createQuery()
		->andWhere("codigo = ? ",$estado_poliza)
		->execute()
		->toArray();

		return $estado_poliza[0]['estado_id'];
		
		 
	}
	
   public static function getNameById($estado_poliza){
		
		$m_estado_poliza = new Model_EstadoPoliza();
		

		$estado_poliza = $m_estado_poliza->getTable()
		->createQuery()
		->andWhere("estado_id = ? ",$estado_poliza)
		->execute()
		->toArray();

		return $estado_poliza[0]['nombre'];
		
		 
	}
	
   public static function getCodigoById($estado_poliza){
		
		$m_estado_poliza = new Model_EstadoPoliza();
		

		$estado_poliza = $m_estado_poliza->getTable()
		->createQuery()
		->andWhere("estado_id = ? ",$estado_poliza)
		->execute()
		->toArray();

		return $estado_poliza[0]['codigo'];
		
		 
	}
	
   	public static function getEstados(){

   		$m_estado_poliza = new Model_EstadoPoliza();
   		
		$row =$m_estado_poliza->getTable()
		->createQuery()
		->orderBy('nombre')
		->execute()
		->toArray();
		return $row;
	}
	
} 
