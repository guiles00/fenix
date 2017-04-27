<?php
class Domain_Productor {
	private $_model ;


	public function __construct($id=null){

		$model = new Model_Productor();
		$this->_model = ($id==null)?new $model: $model->getTable()->find($id) ;


	}
	public function getModel(){
		return $this->_model;
	}

	public function getById($id){
		$row = $this->_model
		->getTable()
		->createQuery()
		->andWhere('productor_id = ?',$id)
		->execute()
		->toArray();
		return $row;
	}

	public static function getNameById($id){

		$m_productor = new Model_Productor();
		$productor = $m_productor->getTable()->find($id);
		
		return $productor->nombre;
	}
	
	
	public static function getMovimientosByProductorId($id){
//		$m_movimiento = new Model_Movimiento();
//		$rows = $m_movimiento->getTable()
//		->createQuery()
//		->where('productor_id = ?',$id)
//		->execute()
//		->toArray();
//		
		return $rows;
	}

}
