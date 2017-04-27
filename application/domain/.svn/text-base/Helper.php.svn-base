<?php
class Domain_Helper{


	public static function getHelperNameById($dominio,$id){

		$model = new Model_Helper();

		$res = $model->getTable()
		->createQuery()
		->andWhere("dominio = ? and dominio_id = ?",array($dominio,$id))
		->execute()
		->toArray();

		return $res[0]['nombre'];

	}

	public static function getHelperByDominio($dominio){

		$model = new Model_Helper();

		$res = $model->getTable()
		->createQuery()
		->andWhere("dominio = ?",$dominio)
		->orderBy('orden')
		->execute()
		->toArray();

		return $res;

	}
	
public static function getHelperIdByDominioAndName($dominio,$nombre){
		
		$model = new Model_Helper();

		$res = $model->getTable()
		->createQuery()
		->andWhere("dominio = ? and nombre = ?",array($dominio,$nombre))
		->execute()
		->toArray();

		return $res[0]['dominio_id'];
		 
	}
	
}