<?php
class Domain_Beneficiario {
	private $_model ;


	public function __construct($id=null){

		$model = new Model_Beneficiario();
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
		->andWhere('beneficiario_id = ?',$this->_model->beneficiario_id)
		->execute()
		->toArray();
		return $row;
	}


	/*
	 * Filtra poliza por beneficiario
	 * estado_id=1 => solicitudes confirmadas
	 */

	public function getPolizas(){
		$this->_model_poliza = new Model_Poliza();

		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id = ?',1)
		->andWhere('beneficiario_id = ?',$this->_model->beneficiario_id)
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		//print_r($this->_model->beneficiario_id);
		return $row;

	}
	//Filtra poliza por beneficiario
	public function findPolizaByNumero($numero){

		$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id = ?',1)
		->andWhere('beneficiario_id = ?',$this->_model->beneficiario_id)
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		return $row;

	}

	public static function getNameById($id){

		$m_beneficiario = new Model_Beneficiario();
		$beneficiario = $m_beneficiario->getTable()->find($id);
		
		return $beneficiario->nombre;
	}
	public static function getMovimientosByBeneficiarioId($id){
		$m_movimiento = new Model_Movimiento();
		$rows = $m_movimiento->getTable()
		->createQuery()
		->where('beneficiario_id = ?',$id)
		->execute()
		->toArray();
		
		return $rows;
	}

	public static function getDebeByBeneficiarioId($id){
		/*
		 * select p.beneficiario_id, dpc.* from poliza p, 
		 * detalle_pago dp , 
		 * detalle_pago_cuota dpc where p.detalle_pago_id = dp.detalle_pago_id and dpc.detalle_pago_id = dp.detalle_pago_id
		 */
		
		$debe = Doctrine_Query::create()
		->select('sum(dpc.importe) as debe')
  		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.beneficiario_id = ? and dpc.pago_id = ?',array($id,0))
		->execute()
		->toArray();
		//echo"<pre>";
		//print_r($debe);
		return $debe[0]['debe'];
	}
	
	public static function getPagoByBeneficiarioId($id){
		/*
		 * select p.beneficiario_id, dpc.* from poliza p, 
		 * detalle_pago dp , 
		 * detalle_pago_cuota dpc where p.detalle_pago_id = dp.detalle_pago_id and dpc.detalle_pago_id = dp.detalle_pago_id
		 */
		
		$pago = Doctrine_Query::create()
		->select('sum(dpc.importe) as pago')
  		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.beneficiario_id = ? and dpc.pago_id = ?',array($id,1))
		->execute()
		->toArray();
		
		//echo"<pre>";
		//print_r($pago);
		
		return $pago[0]['pago'];
	}
	
public static function getSumaMovimientosByBeneficiarioId($id){
		$m_movimiento = new Model_Movimiento();
		$rows = $m_movimiento->getTable()
		->createQuery()
		->select('sum(importe) as suma_importe')
		->where('beneficiario_id = ?',$id)
		->execute()
		->toArray();
		
		return $rows[0]['suma_importe'];
	}
	
}


