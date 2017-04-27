<?php
class Domain_MotivoGarantia{


	public static function getMotivoGarantiaById($id){

		$model = new Model_MotivoGarantia();

		$res = $model->getTable()
		->createQuery()
		->andWhere("motivo_garantia_id = ? ",$id)
		->execute()
		->toArray();

		return $res[0]['motivo_garantia'];

	}


	public static function getMotivoGarantiaIdByCodigo($codigo){

		$model = new Model_MotivoGarantia();

		$res = $model->getTable()
		->createQuery()
		->andWhere("motivo_garantia = ?",$codigo)
		->execute()
		->toArray();

		return $res[0]['motivo_garantia_id'];
			
	}

	public static function getMotivoGarantias(){

		$model = new Model_MotivoGarantia();

		$res = $model->getTable()
		->createQuery()
		->orderBy('motivo_garantia')
		->execute()
		->toArray();

		return $res;

	}
	
	
	
public static function getMotivoGarantiaByIdAndTipoPoliza($id,$tipo_poliza_id){

		$model = new Model_MotivoGarantia();

		$res = $model->getTable()
		->createQuery()
		->andWhere("motivo_garantia_id = ? and tipo_poliza_id = ?",array($id,$tipo_poliza_id))
		->execute()
		->toArray();

		return $res[0]['motivo_garantia'];

	}


	public static function getMotivoGarantiaIdByCodigoAndTipoPoliza($codigo,$tipo_poliza_id){ 

		$model = new Model_MotivoGarantia();

		$res = $model->getTable()
		->createQuery()
		->andWhere("motivo_garantia = ? and tipo_poliza_id = ?",array($codigo,$tipo_poliza_id))
		->execute()
		->toArray();

		return $res[0]['motivo_garantia_id'];
			
	}

	public static function getMotivoGarantiasByTipoPoliza($tipo_poliza_id){

		$model = new Model_MotivoGarantia();

		$res = $model->getTable()
		->createQuery()
		->where('tipo_poliza_id = ?',$tipo_poliza_id)
		->orderBy('motivo_garantia')
		->execute()
		->toArray();

		return $res;

	}
	
	public static function getMotivoGarantiasCascadeByTipoPoliza($tipo_poliza_id,$tipo_garantia_id){

		$model = new Model_MotivoGarantia();

		$res = $model->getTable()
		->createQuery()
		->andWhere("tipo_garantia_id = ? and tipo_poliza_id = ?",array($tipo_garantia_id,$tipo_poliza_id))
		->orderBy('motivo_garantia')
		->execute()
		->toArray();

		return $res;

	}
	
}
