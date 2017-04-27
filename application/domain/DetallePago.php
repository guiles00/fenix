<?php
class Domain_DetallePago {
	private $_model ;
	private $_model_detalle_pago_cuota ;

	public function __construct($id=null){


		if($id==null){
			$this->_model = new Model_DetallePago();
			$this->_model_detalle_pago_cuota = new Model_DetallePagoCuota();

		}else{

			$m_detalle_pago = new Model_DetallePago();
			$m_detalle_pago_cuota = new Model_DetallePagoCuota();
				

			$this->_model =  $m_detalle_pago->getTable()->find($id) ;
			
			
			
			$this->_model_detalle_pago_cuota = $m_detalle_pago_cuota->getTable()
			->findByDetallePagoId($this->_model->detalle_pago_id)->getFirst(); 
			
		}


	}

	public function getModel(){
		return $this->_model;
	}

	public function getModelDetallePagoCuota(){
		return $this->_model_detalle_pago_cuota;
	}
	public static function getCantidadCuotas($detalle_pago_id){

		if(empty($detalle_pago_id)) return false;

		$m_detalle_pago_cuota = new Model_DetallePagoCuota();

		$count = $m_detalle_pago_cuota
		->getTable()
		->findByDetallePagoId($detalle_pago_id)
		->count();

		return $count;

	}
	public static function getCuotas($detalle_pago_id,$estado = '0,1'){

		if(empty($detalle_pago_id)) return false;

		$m_detalle_pago_cuota = new Model_DetallePagoCuota();

		$rows = $m_detalle_pago_cuota
		->getTable()
		->createQuery()
		->Where('detalle_pago_id = ? ',$detalle_pago_id)
		->andWhere('pago_id in ?', $estado )
		->execute()
		->toArray();

		return $rows;

	}

	public static function getCuotasByPagoId($detalle_pago_id,$pago_id){

		if(empty($detalle_pago_id)) return false;

		$m_detalle_pago_cuota = new Model_DetallePagoCuota();

		$rows = $m_detalle_pago_cuota
		->getTable()
		->createQuery()
		->Where('detalle_pago_id = ? ',$detalle_pago_id)
		->andWhere('pago_id = ?', $pago_id )
		->execute()
		->toArray();

		return $rows;

	}
	
	public static function getDetalleCuotas($detalle_pago_id,$pago_id=null){

		if(empty($detalle_pago_id)) return false;

		
		$m_detalle_pago_cuota = new Model_DetallePagoCuota();

		if($pago_id!=null){
		
			$rows = $m_detalle_pago_cuota
		->getTable()
		->createQuery()
		->Where('detalle_pago_id = ? and pago_id = ?',array($detalle_pago_id,$pago_id))
		->execute()
		->toArray();
			
		}else{
		$rows = $m_detalle_pago_cuota
		->getTable()
		->createQuery()
		->Where('detalle_pago_id = ? ',$detalle_pago_id)
		->execute()
		->toArray();
		}
		
		return $rows;

	}
	
	public static function getValorCuotas($detalle_pago_id){

		if(empty($detalle_pago_id)) return false;

		$m_detalle_pago_cuota = new Model_DetallePagoCuota();

		$rows = $m_detalle_pago_cuota
		->getTable()
		->createQuery()
		->Where('detalle_pago_id = ?',$detalle_pago_id)
		->execute()
		->toArray();

		return $rows[0]['importe'];

	}

	public static function deleteDetallePago($poliza_id){

		if(empty($poliza_id)) return false;
		$model_detalle_pago = new Model_DetallePago();

		$model_detalle_pago
		->getTable()
		->findByPolizaId($poliza_id)
		->delete();

	}

	public static function setPago($detalle_pago_cuota_id){

		if(empty($detalle_pago_cuota_id)) return false;



		$q = Doctrine_Query::create()
        ->update('Model_DetallePagoCuota dpc')
        ->set('dpc.pago_id',0)
        ->where('dpc.detalle_pago_cuota_id = ?',array($detalle_pago_cuota_id))
		->execute();
		
	}

	public static function addMonthbyDate($date){
		//echo "<pre>";
		//echo "Esta es la fecha";

		$date_temp = new DateTime($date);
		$date_temp->add(new DateInterval('P1M'));
		//echo $date->format('Y-m-d') . "\n";
		
		return $date_temp->format('Y-m-d');
	}

}


