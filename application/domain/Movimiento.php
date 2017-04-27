<?php
class Domain_Movimiento {
	private $_model ;


	public function __construct($id=null){

		$model = new Model_Movimiento();
		$this->_model = ($id==null)?new $model: $model->getTable()->find($id) ;


	}
	public function getModel(){
		return $this->_model;
	}

	public function getNombre(){
		return $this->_nombre;
	}


	static	public function getPolizasByMovimientoAndAsegurado($movimiento_id){
		/*
		 *SELECT *
		 FROM poliza p, detalle_pago dp, detalle_pago_cuota dpc, movimiento_poliza mp
		 WHERE p.detalle_pago_id = dp.detalle_pago_id
		 AND dp.detalle_pago_id = dpc.detalle_pago_id
		 AND mp.poliza_id = dpc.detalle_pago_cuota_id
		 */
		$m_movimiento = new Model_Movimiento();

		$movimiento_polizas = Doctrine_Query::create()
		->select('*')
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc, Model_MovimientoPoliza mp')
		->where('mp.movimiento_id = ? ',$movimiento_id)
		//->execute()
		//->toArray();
		->getSqlQuery();
		echo "<pre>";
		print_r($movimiento_polizas);
		exit;
		return $rows[0]['suma_importe'];
	}

	static	public function getPolizasByMovimiento($movimiento_id){
		/*
		 *SELECT * FROM movimiento m, movimiento_poliza mp
		 WHERE m.movimiento_id = mp.movimiento_id
		 and m.movimiento_id=53
		 */
		$m_movimiento = new Model_Movimiento();

		$movimiento_polizas = Doctrine_Query::create()
		->from('Model_Movimiento m')
		->leftJoin('m.Model_MovimientoPoliza mp')
		->where('m.movimiento_id = ? ',$movimiento_id)
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($movimiento_polizas);
		//exit;
		return $movimiento_polizas;
	}

	static public function getPolizaByDetallePagoId($detalle_pago_cuota_id){
		
		
		
     	$m_detalle_pago_cuota = new Model_DetallePagoCuota();

		$detalle_pago_cuota = $m_detalle_pago_cuota->getTable()->findOneBy('detalle_pago_cuota_id', $detalle_pago_cuota_id)->toArray();
		 
		$m_poliza = new Model_Poliza();
		$m_poliza = $m_poliza->getTable()->findOneBy('detalle_pago_id', $detalle_pago_cuota['detalle_pago_id']);
	

       		if(!empty($m_poliza) ){
                $poliza = $m_poliza->toArray();
                }else{
                echo "No se encontro poliza para este pago";

                echo "<pre>";
                print_r($detalle_pago_cuota);
                }
		
		return $poliza;
	}
	static public function getDatosCheques($movimiento_id){

		$m_datos_cheques = Doctrine_Query::create()
		->from('Model_DatosCheque dc')
		->where('dc.movimiento_id = ? ',$movimiento_id)
		->execute()
		->toArray();
		
		return $m_datos_cheques;
	}
	static public function eliminarMovimientosPoliza($movimiento_id){

		$q = Doctrine_Query::create()
        ->delete('Model_MovimientoPoliza mp')
        ->where('mp.movimiento_id = ?',array($movimiento_id))
        ->execute();
		//->toArray();
		
		//$q->getSqlQuery();

		return $q;
	}
	
	static public function getMovimientosPoliza($movimiento_id){

		$q = Doctrine_Query::create()
		->select('*')
        ->from('Model_MovimientoPoliza mp')
        ->where('mp.movimiento_id = ?',$movimiento_id)
		->execute()
		->toArray();
		
		//echo $q->getSqlQuery();
		return $q;
	}

}

