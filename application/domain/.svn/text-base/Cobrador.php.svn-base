<?php
class Domain_Cobrador {
	private $_model ;


	public function __construct($id=null){

		$model = new Model_Cobrador();
		$this->_model = ($id==null)?new $model: $model->getTable()->find($id) ;


	}
	public function getModel(){
		return $this->_model;
	}

	public function getById($id){
		
		$row = $this->_model
		->getTable()
		->createQuery()
		->andWhere('estado_id = ?',1)
		->andWhere('cobrador_id = ?',$this->_model->cobrador_id)
		->execute()
		->toArray();
		return $row;
	}

	
	/*
	 * Filtra poliza por cobrador
	 * estado_id=1 => solicitudes confirmadas
	 */
		
	public function getPolizas(){
	$this->_model_poliza = new Model_Poliza();
	
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id = ?',1)
		->andWhere('cobrador_id = ?',$this->_model->cobrador_id)
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		//print_r($this->_model->cobrador_id);
		return $row;

	}
	//Filtra poliza por cobrador
	public function findPolizaByNumero($numero){
		
	$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id = ?',1)
		->andWhere('cobrador_id = ?',$this->_model->cobrador_id)
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		return $row;

	}
	
public static function getNameById($id){

		$m_cobrador = new Model_Cobrador();
		$cobrador = $m_cobrador->getTable()->find($id);
		
		return $cobrador->nombre;
	}
	
}


