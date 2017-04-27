<?php
class Domain_Asegurado {
	private $_model ;


	public function __construct($id=null){

		$model = new Model_Asegurado();
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
		->andWhere('asegurado_id = ?',$this->_model->asegurado_id)
		->execute()
		->toArray();
		return $row;
	}


	/*
	 * Filtra poliza por asegurado
	 * estado_id=1 => solicitudes confirmadas
	 */

	public function getPolizas(){
		$this->_model_poliza = new Model_Poliza();

		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id = ?',1)
		->andWhere('asegurado_id = ?',$this->_model->asegurado_id)
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		//print_r($this->_model->asegurado_id);
		return $row;

	}
	//Filtra poliza por asegurado
	public function findPolizaByNumero($numero){

		$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id = ?',1)
		->andWhere('asegurado_id = ?',$this->_model->asegurado_id)
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		
		//print_r($row);
		return $row;

	}

	public static function getNameById($id){

		$m_asegurado = new Model_Asegurado();
		$asegurado = $m_asegurado->getTable()->find($id);
		
		return $asegurado->nombre;
	}
	
	public static function getDomicilioById($id){

		$m_asegurado = new Model_Asegurado();
		$asegurado = $m_asegurado->getTable()->find($id);
		
		return $asegurado->domicilio;
	}
	
	public static function getMovimientosByAseguradoId($id){
		//echo "trae movimientos:". $id;
		$m_movimiento = new Model_Movimiento();
		$rows = $m_movimiento->getTable()
		->createQuery()
		->where('asegurado_id = ?',$id)
		->orderBy('movimiento_id desc')
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo"<pre>";
		//print_r($rows);
		//exit;
		return $rows;
	}
public static function getMovimientosByAseguradoIdAndPoliza($id,$numero_poliza=null){
/*
SELECT mp . *
FROM movimiento_poliza mp, detalle_pago_cuota dpc, detalle_pago dp, poliza p
WHERE mp.poliza_id = dpc.detalle_pago_cuota_id
AND dp.detalle_pago_id = dpc.detalle_pago_id
AND dp.detalle_pago_id = p.detalle_pago_id
AND p.numero_poliza LIKE '%80679%'
LIMIT 10 
*/

	$rows = Doctrine_Query::create()
		->select('m.*')
		->from('Model_Movimiento m, Model_MovimientoPoliza mp, Model_DetallePagoCuota dpc, Model_DetallePago dp, Model_Poliza p ')
		->where('p.asegurado_id = ?',array($id))
		->andwhere('mp.poliza_id = dpc.detalle_pago_cuota_id')
		->andWhere('dp.detalle_pago_id = dpc.detalle_pago_id')
		->andWhere('dp.detalle_pago_id = p.detalle_pago_id')
		->andWhere('m.movimiento_id = mp.movimiento_id')
		->andWhere('p.numero_poliza like ? ',array("%$numero_poliza%"))
		->andWhere('m.tipo_movimiento_id = ?',0)
		->orderBy('movimiento_id desc')
		->execute()
		->toArray();
		//->getSqlQuery();
//print_r($rows);
//exit;
		return $rows;
	}


	public static function getDebeByAseguradoId($id){
		/*
		 * select p.asegurado_id, dpc.* from poliza p, 
		 * detalle_pago dp , 
		 * detalle_pago_cuota dpc where p.detalle_pago_id = dp.detalle_pago_id and dpc.detalle_pago_id = dp.detalle_pago_id
		 */
		
		$debe = Doctrine_Query::create()
		->select('sum(dpc.importe) as debe')
  		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.asegurado_id = ? and dpc.pago_id = ?',array($id,0))
		->execute()
		->toArray();
		//echo"<pre>";
		//print_r($debe);
		return $debe[0]['debe'];
	}
	
public static function bkgetDebeByAseguradoIdAndMoneda($id,$moneda_id){
		/*
		 * select p.asegurado_id, dpc.* from poliza p, 
		 * detalle_pago dp , 
		 * detalle_pago_cuota dpc where p.detalle_pago_id = dp.detalle_pago_id and dpc.detalle_pago_id = dp.detalle_pago_id
		 */
		
		$debe = Doctrine_Query::create()
		->select('sum(dpc.importe) as debe')
  		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.asegurado_id = ? and dpc.pago_id = ? and dp.moneda_id = ?',array($id,0,$moneda_id))
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo"<pre>";
		//print_r($debe);
		//exit;
		return round($debe[0]['debe'],2);
	}
	
	
public static function getDebeByAseguradoIdAndMoneda($asegurado_id,$moneda_id){

		$estado_id = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');
		
		$estado_baja = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_baja_devolucion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_DEVOLUCION');
		$estado_no_renovado = Domain_EstadoPoliza::getIdByCodigo('NO_RENOVADO');
		$estado_nota_credito = Domain_EstadoPoliza::getIdByCodigo('NOTA_DE_CREDITO');
		$estado_renovada = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
		$estado_vigencia_cumplida = Domain_EstadoPoliza::getIdByCodigo('VIGENCIA_CUMPLIDA');

		//$estado_refactovada = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
//REFACTURADO

		$debe = Doctrine_Query::create()
		->select('sum(dpc.importe) as debe')
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.asegurado_id = ? and dpc.pago_id = ? and dp.moneda_id = ? ',array($asegurado_id,$estado_id,$moneda_id))
		->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? 
		OR p.estado_id = ? OR p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?'
		,array($estado_baja,$estado_afectada,$estado_refacturado,$estado_vigente,
$estado_baja_devolucion,$estado_no_renovado,$estado_nota_credito,$estado_renovada,$estado_vigencia_cumplida))
		->execute()
		->toArray();
		//->getSqlQuery();


		/*$debe = Doctrine_Query::create()
		->select('sum(dpc.importe) as debe')
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.asegurado_id = ? and dpc.pago_id = ? and dp.moneda_id = ? ',array($asegurado_id,$estado_id,$moneda_id))
		->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? 
		OR p.estado_id = ? OR p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?'
		,array($estado_baja,$estado_afectada,$estado_refacturado,$estado_vigente,
$estado_baja_devolucion,$estado_no_renovado,$estado_nota_credito,$estado_renovada))
		->execute()
		->toArray();
		//->getSqlQuery();
*/
		
		
		

		return round($debe[0]['debe'],2);
	}
	
	
	
	public static function getPagoByAseguradoId($id){
		/*
		 * select p.asegurado_id, dpc.* from poliza p, 
		 * detalle_pago dp , 
		 * detalle_pago_cuota dpc where p.detalle_pago_id = dp.detalle_pago_id and dpc.detalle_pago_id = dp.detalle_pago_id
		 */
		
		$pago = Doctrine_Query::create()
		->select('sum(dpc.importe) as pago')
  		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.asegurado_id = ? and dpc.pago_id = ?',array($id,1))
		->execute()
		->toArray();
		
		//echo"<pre>";
		//print_r($pago);
		
		return round($pago[0]['pago'],2);
	}
	
public static function bkgetPagoByAseguradoIdAndMoneda($id,$moneda_id){
		/*
		 * select p.asegurado_id, dpc.* from poliza p, 
		 * detalle_pago dp , 
		 * detalle_pago_cuota dpc where p.detalle_pago_id = dp.detalle_pago_id and dpc.detalle_pago_id = dp.detalle_pago_id
		 */
		
		$pago = Doctrine_Query::create()
		->select('sum(dpc.importe) as pago')
  		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.asegurado_id = ? and dpc.pago_id = ? and dp.moneda_id = ?',array($id,1,$moneda_id))
		->execute()
		->toArray();
		
		//echo"<pre>";
		//print_r($pago);
		
		return round($pago[0]['pago'],2);
	}

	
	public static function getPagoByAseguradoIdAndMoneda($asegurado_id,$moneda_id){

		$estado_id = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'PAGO');
		$estado_baja = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_baja_devolucion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_DEVOLUCION');
		$estado_no_renovado = Domain_EstadoPoliza::getIdByCodigo('NO_RENOVADO');
		$estado_nota_credito = Domain_EstadoPoliza::getIdByCodigo('NOTA_DE_CREDITO');
		$estado_renovada = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
		$estado_vigencia_cumplida = Domain_EstadoPoliza::getIdByCodigo('VIGENCIA_CUMPLIDA');




		$debe = Doctrine_Query::create()
		->select('sum(dpc.importe) as debe')
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.asegurado_id = ? and dpc.pago_id = ? and dp.moneda_id = ? ',array($asegurado_id,$estado_id,$moneda_id))
		->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? 
		OR p.estado_id = ? OR p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id = ? OR p.estado_id =?'
		,array($estado_baja,$estado_afectada,$estado_refacturado,
$estado_vigente,$estado_baja_devolucion,$estado_no_renovado,$estado_nota_credito,$estado_renovada,$estado_vigencia_cumplida))
		->execute()
		->toArray();
		//->getSqlQuery();

//echo"<pre>";
	//	print_r($debe);
		//exit;
		
		//echo $rows;
		//exit;
		return round($debe[0]['debe'],2);
	}
	
	
	
public static function getSumaMovimientosByAseguradoId($id){
		$m_movimiento = new Model_Movimiento();
		$rows = $m_movimiento->getTable()
		->createQuery()
		->select('sum(importe) as suma_importe')
		->where('asegurado_id = ?',$id)
		->execute()
		->toArray();
		
		return $rows[0]['suma_importe'];
	}
	


	
public static function getSumaMovimientosByAseguradoIdAndMoneda($id,$moneda_id){
		$m_movimiento = new Model_Movimiento();
		$rows = $m_movimiento->getTable()
		->createQuery()
		->select('sum(importe) as suma_importe')
		->where('asegurado_id = ? and moneda_id = ?',array($id,$moneda_id))
		->execute()
		->toArray();
		
		return $rows[0]['suma_importe'];
	}
	
  public function getListadoDeudaCliente($agente_id = null){
		
		//Buscar el estado Vigente
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_debe = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');
		
		
				$rows = Doctrine_Query::create()
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->andWhere("dpc.pago_id = ? ", $estado_debe)
		->andWhere('asegurado_id = ?',$this->_model->asegurado_id)
		->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =?' 
		,array($estado_vigente,$estado_afectada,$estado_refacturado))
		->execute()
		->toArray();
			
		
		//->getSqlQuery();
		//echo $row;
		
		return $rows;

	}
	
}

