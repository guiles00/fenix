<?php
class Domain_Operador implements Domain_IEntidad {

	private $_nombre = "OPERADOR";

	public function __construct($id=null){


		/*		if($id==null){

		}else{

		$model_poliza = new Model_Poliza();
		$model_poliza_valores = new Model_PolizaValores();

		$this->_model_poliza =  $model_poliza->getTable()->find($id) ;
		$this->_model_poliza_valores = $model_poliza_valores->getTable()->find($this->_model_poliza->poliza_valores_id);
		}
		*/
		//como tengo varios modelos en esta clase tengo que instanciar primero el Modelo
		//Principal, luego traer los id de los otros modelos e instanciarlos
		//$model_poliza_valores = new Model_PolizaValores();
		//$this->_model_poliza_valores = ($id==null)?new $model_poliza_valores: $model_poliza->getTable()->find($id) ;

	}
	public function getNombre(){
		return $this->_nombre;
	}
	public function getModelPoliza(){
		return $this->_model_poliza;
	}
	public function getModelPolizaValores(){
		return $this->_model_poliza_valores;
	}
	//trae polizas sin filtrar porque es el perfil Operador
	public function getPolizas(){
		//Buscar el estado Vigente
		$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$this->_model_poliza = new Model_Poliza();
		//estado_id=1 => solicitudes confirmadas
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id = ?',$estado)
		->orderBy('poliza_id DESC')
		->execute()
		->toArray();

		return $row;

	}
	//Trae solicitudes sin filtrar porque es el perfil Operador
	//Trae las solicitudes, por ahora lo hardcodeo
	public function getSolicitudes(){

		//$estado = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_PENDIENTE');

		$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->Where('estado_id in (0,1)')
		->orderBy('numero_solicitud DESC')
		->execute()
		->toArray();

		return $row;

	}
	/**
	* Este metodo arma el array de busqueda, no tiene en cuenta el tipo de operacion
	* Como me pidieron que a un campo le ponga un like, tuve que poner un if() dentro del for()
	*/
	public function searchPoliza($params,$fechas){

		
		$this->_model_poliza = new Model_Poliza();
		$table_poliza = $this->_model_poliza->getTable();
		
		$query=$table_poliza->createQuery();
		
		foreach ($params as $arr) {

			if($arr['nombre'] !== "numero_factura"){ 		//saco de params el numero de factura
			$campo = $arr['nombre'];
			$valor = $arr['valor'];
			
			$query->addWhere("$campo = ? ", $valor) ;
			}else{
			$campo = $arr['nombre'];
			$valor = $arr['valor'];
			$query->addWhere("numero_factura like ? ", array("%$valor%") ) ;
			}
		}
		//Siempre va a ser entre fechas
		$query->andWhere("fecha_vigencia between ? AND ?", array($fechas['fecha_desde'],$fechas['fecha_hasta']));

		//$q = $query->getSqlQuery();
		//print_r($q);
		//exit;
		$q = $query->execute()->toArray();
		
		return $q;
	}
	
	//Filtrar solicitud por perfil
	//Por ahora trae las vigentes
	public function findPolizaByNumero($numero){

		$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_caucion = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_renovada = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
		
		$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->Where('estado_id not in (0,1)' )
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))

		->orderBy('numero_solicitud DESC')
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		//exit;
		return $row;

	}

	public function findPolizaByNumeroAndAsegurado($numero,$asegurado_id){

		$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_caucion = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');

		$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->Where('estado_id not in (0,1)' )
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->andWhere('asegurado_id = ?',$asegurado_id)
		->orderBy('numero_solicitud DESC')
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		//exit;
		return $row;

	}


	public function getPolizasDefault(){

		$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_caucion = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');

		$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->Where('estado_id not in (0,1)' )
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		//->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->limit(50)
		->orderBy('numero_solicitud DESC')
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		//exit;
		return $row;

	}

	// Estado = 0 es Poliza no confirmada => Solicitud
	public function findSolicitudByNumero($numero){

		//$estado = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_PENDIENTE');

		$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->Where('estado_id in (0,1)')
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->orderBy('numero_solicitud DESC')
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		//exit;
		return $row;

	}
	//Aca no filtra pero en los otros tipos de usuario deberia filtrar por agente_id
	public function findAseguradoByNombre($nombre){

		$asegurado = new Model_Asegurado();
		$rows = $asegurado->getTable()
		->createQuery()
		->where('nombre like ?',"%$nombre%")
		->execute()
		->toArray();
		return $rows;
	}
	
	public function findProductorByNombre($nombre){

		$productor = new Model_Productor();
		$rows = $productor->getTable()
		->createQuery()
		->where('nombre like ?',"%$nombre%")
		->execute()
		->toArray();
		return $rows;
	}

	public function getAsegurados(){

		$asegurado = new Model_Asegurado();

		return $asegurado->getTable()->findAll()->toArray();

	}

	//Filtrar solicitud por perfil
	public function getPolizaByAsegurado($asegurado_id,$estado='VIGENTE'){

		$estado_id = Domain_EstadoPoliza::getIdByCodigo($estado);

		$this->_model_poliza = new Model_Poliza();
		$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		->Where('estado_id in ?',$estado)
		->andWhere('asegurado_id = ?',$asegurado_id)
		->getSqlQuery();
		//->execute()
		//->toArray();
		print_r($rows);
		return $rows;

	}

	/*
	 * Trae las polizas del asegurado que tiene alguna cuota impaga
	 * Si tiene todas las cuotas pagas esa poliza obviamente esta paga
	 * y no se muestra
	 */
	public function getPolizaInpagaByAsegurado($asegurado_id){

		$estado_id = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');

		$rows = Doctrine_Query::create()
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.asegurado_id = ? and dpc.pago_id = ?',array($asegurado_id,$estado_id))
		->execute()
		->toArray();

		return $rows;
	}


	/*
	 * 1.Trae las polizas del asegurado y por tipo de poliza que tiene alguna cuota impaga
	 * 2.Si tiene todas las cuotas pagas esa poliza obviamente esta paga
	 * y no se muestra
	 * 3.Solo muestra las polizas con los estados siguientes:
	 * -AFECTADA
	 * -BAJA POR LIBERACION
	 */
	public function getPolizaInpagaByAseguradoAndTipo($asegurado_id,$tipo_poliza_id){

		$estado_id = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');
		$estado_baja = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_baja_devolucion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_DEVOLUCION');
		$estado_no_renovado = Domain_EstadoPoliza::getIdByCodigo('NO_RENOVADO');
		$estado_nota_credito = Domain_EstadoPoliza::getIdByCodigo('NOTA_DE_CREDITO');
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');


		$rows = Doctrine_Query::create()
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.asegurado_id = ? and dpc.pago_id = ?',array($asegurado_id,$estado_id))
		->andwhere('p.tipo_poliza_id = ? ',$tipo_poliza_id)
		->andwhere('p.estado_id = ? OR p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id = ? OR p.estado_id = ? OR p.estado_id =? OR p.estado_id =?'
		,array($estado_baja,$estado_afectada,$estado_refacturado,$estado_vigente,$estado_baja_devolucion,$estado_no_renovado,$estado_nota_credito,$estado_endosada))
		->execute()
		->toArray();
		//->getSqlQuery();


		//echo $rows;
		//exit;
		return $rows;
	}

	/*
	 * Panel Deuda Asegurado : Busqueda de poliza
	 */
	public function findPolizaInpagaByAsegurado($nro_poliza,$asegurado_id){

		$estado_id = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');
		$estado_baja = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_baja_devolucion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_DEVOLUCION');
		$estado_no_renovado = Domain_EstadoPoliza::getIdByCodigo('NO_RENOVADO');
		$estado_nota_credito = Domain_EstadoPoliza::getIdByCodigo('NOTA_DE_CREDITO');
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');
		$estado_renovada = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
		$estado_vigencia_cumplida = Domain_EstadoPoliza::getIdByCodigo('VIGENCIA_CUMPLIDA');




		$rows = Doctrine_Query::create()
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.asegurado_id = ? and dpc.pago_id = ? and p.numero_poliza like ?',array($asegurado_id,$estado_id,"%$nro_poliza%"))
		->andwhere('p.estado_id = ? OR p.estado_id = ? OR p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id = ? OR p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?'
		,array($estado_renovada,$estado_baja,$estado_afectada,$estado_refacturado,$estado_vigente,$estado_baja_devolucion,$estado_no_renovado,$estado_nota_credito,$estado_endosada,$estado_vigencia_cumplida))
		->execute()
		->toArray();
		//->getSqlQuery();


		//echo $rows;
		//exit;
		return $rows;
	}

	public function  getSaldo(){
		return false;
	}

	/*
	 * En este informe, Que estados de la poliza muestra??
	 */
	public function getPolizasInformeDiarioRango($fecha_desde,$fecha_hasta,$operacion_id){

		//Buscar el estado Vigente
		$estado_aceptada = Domain_EstadoPoliza::getIdByCodigo('ACEPTADA');
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_pago = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'PAGO');

		if(empty($operacion_id)){
		
		$rows = Doctrine_Query::create()
		->from('Model_Poliza p')
		->andWhere("fecha_pedido between ? AND ?", array($fecha_desde,$fecha_hasta))
		->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?'
		,array($estado_vigente,$estado_afectada,$estado_refacturado,$estado_aceptada))
		->orderBy('p.fecha_pedido')
		->execute()
		->toArray();
		
		}else{
		
		$rows = Doctrine_Query::create()
		->from('Model_Poliza p')
		->andWhere("fecha_pedido between ? AND ?", array($fecha_desde,$fecha_hasta))
		->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?'
		,array($estado_vigente,$estado_afectada,$estado_refacturado,$estado_aceptada))
		->andWhere("p.operacion_id = ?", $operacion_id)
		->orderBy('p.fecha_pedido')
		->execute()
		->toArray();	
		}
		
		//->getSqlQuery();

		//print_r($rows);
		//exit;
		return $rows;
	}

	public function getPolizasInformeDiario($fecha_pedido,$operacion_id){

		//Buscar el estado Vigente
		$estado_aceptada = Domain_EstadoPoliza::getIdByCodigo('ACEPTADA');
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_pago = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'PAGO');

		if(empty($operacion_id)){
		
		$rows = Doctrine_Query::create()
		->from('Model_Poliza p')
		->andWhere("fecha_pedido = ? ", $fecha_pedido)
		->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?'
		,array($estado_vigente,$estado_afectada,$estado_refacturado,$estado_aceptada))
		->orderBy('p.fecha_pedido')
		->execute()
		->toArray();
		
		}else{

			$rows = Doctrine_Query::create()
		->from('Model_Poliza p')
		->andWhere("fecha_pedido = ? ", $fecha_pedido)
		->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?'
		,array($estado_vigente,$estado_afectada,$estado_refacturado,$estado_aceptada))
		->andWhere("p.operacion_id = ?", $operacion_id)
		->orderBy('p.fecha_pedido')
		->execute()
		->toArray();
		
			
		}

		return $rows;

	}

	public function getPolizasListadoProduccion(){
		//Buscar el estado Vigente
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');

		$date = new DateTime();
		$hoy =  $date->format('Y-m-d');

		$mes_pasado_timestamp = mktime(0, 0, 0, date("m")-1, date("d"),   date("Y"));
		$mes_pasado =  date('Y-m-d',$mes_pasado_timestamp);
		 
		$this->_model_poliza = new Model_Poliza();
		$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado_vigente,$estado_afectada,$estado_refacturado))
		//->andWhere("fecha_vigencia = ?" , $fecha)
		->andWhere("fecha_vigencia between ? AND ?", array($mes_pasado,$hoy))
		//->getSqlQuery();
		->execute()
		->toArray();
		//echo $rows;
		//exit;
		return $rows;

	}

	public function getPolizasListadoProduccionByMonth($mes,$anio){
		
		//Buscar el estado Vigente
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');

		$imes = intval($mes);
		$ianio = intval($anio);
		
		$mes_pasado_timestamp = mktime(0, 0, 0, $imes-1, "01", $ianio);
		$mes_pasado =  date('Y-m-d',$mes_pasado_timestamp);
		
		$mes_actual_timestamp = mktime(0, 0, 0, $imes, "01", $ianio);
		$mes_actual =  date('Y-m-d',$mes_actual_timestamp);
		
				
		$this->_model_poliza = new Model_Poliza();
		$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado_vigente,$estado_afectada,$estado_refacturado))
		//->andWhere("fecha_vigencia = ?" , $fecha)
		->andWhere("fecha_vigencia between ? AND ?", array($mes_pasado,$mes_actual))
		//->getSqlQuery();
		->execute()
		->toArray();
		
		//echo $rows;
		//exit;
		
		return $rows;

	}

	//trae polizas sin filtrar porque es el perfil Operador
	public function getPolizasVencimiento($fecha_desde,$fecha_hasta,$asegurado_id){
		//Buscar el estado Vigente
		$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_caucion = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_baja_devolucion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_DEVOLUCION');
		$estado_baja_liberacion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');
		$estado_baja_oficio = Domain_EstadoPoliza::getIdByCodigo('BAJA_DE_OFICIO');
		$estado_no_renovado = Domain_EstadoPoliza::getIdByCodigo('NO_RENOVADO');
		$estado_vigencia_cumplida = Domain_EstadoPoliza::getIdByCodigo('VIGENCIA_CUMPLIDA');
		
		/*
		
		NO RENOVADO
		VIGENCIA CUMPLIDA*/


		//Que muester todas las polizas, despues veo el filtro
		$this->_model_poliza = new Model_Poliza();
		
		if(empty($asegurado_id)){

		$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		->Where('estado_id not in (0,1)' )
		->andWhere("fecha_vigencia_hasta between ? AND ?", array($fecha_desde,$fecha_hasta))
		->andWhere("estado_id not in ($estado_baja_devolucion,$estado_baja_liberacion,$estado_baja_oficio,$estado_no_renovado,$estado_vigencia_cumplida)")
		//->andWhere("fecha_vigencia_hasta => ? AND fecha_vigencia_hasta =< ?", array($fecha_desde,$fecha_hasta))
		->orderBy('fecha_vigencia_hasta')
		->execute()
		->toArray();
			
		}else{
		$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		->Where('estado_id not in (0,1)' )
		->andWhere("fecha_vigencia_hasta between ? AND ?", array($fecha_desde,$fecha_hasta))
		->andWhere("asegurado_id = ?",$asegurado_id)
		->andWhere("estado_id not in ($estado_baja_devolucion,$estado_baja_liberacion,$estado_baja_oficio,$estado_no_renovado,$estado_vigencia_cumplida)")

		//->andWhere("fecha_vigencia_hasta => ? AND fecha_vigencia_hasta =< ?", array($fecha_desde,$fecha_hasta))
		->orderBy('fecha_vigencia_hasta')
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo $rows;
		//exit;
		}
		//echo $fecha_desde."hasta".$fecha_hasta;
		/*
		 *
		// Buscar el estado Vigente
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_debe = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');
		$estado_baja_devolucion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_DEVOLUCION');
		$estado_baja_liberacion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');

		$rows = Doctrine_Query::create()
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->andWhere("dpc.pago_id = ? ", $estado_debe)
		->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?' 
		,array($estado_vigente,$estado_afectada,$estado_refacturado,$estado_baja_devolucion,$estado_baja_liberacion))
		->andWhere("p.asegurado_id = ? ", $asegurado_id)
		->execute()
		->toArray();
		 * 
		 */
		return $rows;

	}

	//trae polizas sin filtrar porque es el perfil Operador
	public function getPolizasVencimientoCuotas($fecha_desde,$fecha_hasta,$asegurado_id){
		//Buscar el estado Vigente
		$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_caucion = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		
		//Que muestre todas las polizas, despues veo el filtro
		$this->_model_poliza = new Model_Poliza();
		
		if(empty($asegurado_id)){

		$rows = Doctrine_Query::create()
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		//->andWhere("dpc.pago_id = ? ", $estado_debe)
		//->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?' 
		//,array($estado_vigente,$estado_afectada,$estado_refacturado,$estado_baja_devolucion,$estado_baja_liberacion))
		->Where('estado_id not in (0,1)' )
		->andWhere("fecha_vigencia_hasta between ? AND ?", array($fecha_desde,$fecha_hasta))
		//->andWhere("p.asegurado_id = ? ", $asegurado_id)
		->execute()
		->toArray();
			
		/*	
		$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		->Where('estado_id not in (0,1)' )
		->andWhere("fecha_vigencia_hasta between ? AND ?", array($fecha_desde,$fecha_hasta))
		//->andWhere("fecha_vigencia_hasta => ? AND fecha_vigencia_hasta =< ?", array($fecha_desde,$fecha_hasta))
		->orderBy('fecha_vigencia_hasta')
		->execute()
		->toArray();
		*/
		}else{
		/*	
		$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		->Where('estado_id not in (0,1)' )
		->andWhere("fecha_vigencia_hasta between ? AND ?", array($fecha_desde,$fecha_hasta))
		->andWhere("asegurado_id = ?",$asegurado_id)
		//->andWhere("fecha_vigencia_hasta => ? AND fecha_vigencia_hasta =< ?", array($fecha_desde,$fecha_hasta))
		->orderBy('fecha_vigencia_hasta')
		->execute()
		->toArray();*/
		//->getSqlQuery();
		
		$rows = Doctrine_Query::create()
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		//->andWhere("dpc.pago_id = ? ", $estado_debe)
		//->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?' 
		//,array($estado_vigente,$estado_afectada,$estado_refacturado,$estado_baja_devolucion,$estado_baja_liberacion))
		->Where('estado_id not in (0,1)' )
		->andWhere("fecha_vigencia_hasta between ? AND ?", array($fecha_desde,$fecha_hasta))
		->andWhere("p.asegurado_id = ? ", $asegurado_id)
		->execute()
		->toArray();
		
		
		}
		//echo $fecha_desde."hasta".$fecha_hasta;
		/*
		 *
		// Buscar el estado Vigente
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_debe = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');
		$estado_baja_devolucion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_DEVOLUCION');
		$estado_baja_liberacion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');

				 * 
		 */
		return $rows;

	}
	

	//trae polizas sin filtrar porque es el perfil Operador
	public function getListadoOperacionesByProductorId($productor_id){
		//Buscar el estado Vigente
		//$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		//$estado_caucion = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		//$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		//Que muester todas las polizas, despues veo el filtro
		$this->_model_poliza = new Model_Poliza();
		$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		->Where('estado_id not in (0,1)' )
		->andWhere("productor_id = ? ", $productor_id)
		->execute()
		->toArray();
		//->getSqlQuery();
		return $rows;
	}

	//trae polizas sin filtrar porque es el perfil Operador
	public function getListadoOperacionesByProductorIdAndMonth($productor_id,$mes,$anio){
		//Buscar el estado Vigente
		//$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		//$estado_caucion = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		//$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		//Que muester todas las polizas, despues veo el filtro
		$imes = intval($mes);
		$ianio = intval($anio);
		
		$mes_pasado_timestamp = mktime(0, 0, 0, $imes-1, "01", $ianio);
		$mes_pasado =  date('Y-m-d',$mes_pasado_timestamp);
		
		$mes_actual_timestamp = mktime(0, 0, 0, $imes, "00", $ianio);
		$mes_actual =  date('Y-m-d',$mes_actual_timestamp);
		
		$mes_que_viene_timestamp = mktime(0, 0, 0, $imes+1, "00", $ianio);
		$mes_que_viene =  date('Y-m-d',$mes_que_viene_timestamp);
		
		
		
		//echo "<br>mes actual".$mes_actual;
		//echo "<br>mes que viene".$mes_que_viene;
		//exit;
		
		$this->_model_poliza = new Model_Poliza();
		$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		->Where('estado_id not in (0,1)' )
		->andWhere("productor_id = ? ", $productor_id)
		//->andWhere("fecha_vigencia between ? AND ?", array($mes_actual,$mes_que_viene))
		->andWhere("fecha_vigencia > ? AND fecha_vigencia <= ?", array($mes_actual,$mes_que_viene))
		->orderBy("fecha_vigencia")
		->execute()
		->toArray();
		//->getSqlQuery();
		
		return $rows;
	}
	
//trae polizas sin filtrar porque es el perfil Operador
	public function getListadoOperacionesByProductorIdAndMonthRefacturado($productor_id,$mes,$anio){
		//Buscar el estado Vigente
		//$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		//$estado_caucion = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		//$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		//Que muester todas las polizas, despues veo el filtro
		$imes = intval($mes);
		$ianio = intval($anio);
		
		$mes_pasado_timestamp = mktime(0, 0, 0, $imes-1, "01", $ianio);
		$mes_pasado =  date('Y-m-d',$mes_pasado_timestamp);
		
		$mes_actual_timestamp = mktime(0, 0, 0, $imes, "00", $ianio);
		$mes_actual =  date('Y-m-d',$mes_actual_timestamp);
		
		$mes_que_viene_timestamp = mktime(0, 0, 0, $imes+1, "00", $ianio);
		$mes_que_viene =  date('Y-m-d',$mes_que_viene_timestamp);
		
		
		
		//echo "<br>mes actual".$mes_actual;
		//echo "<br>mes que viene".$mes_que_viene;
		//exit;
		$this->_model_poliza = new Model_Poliza();
		$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		->Where('estado_id not in (0,1,14)' )
		->andWhere("productor_id = ? ", $productor_id)
		//->andWhere("fecha_vigencia between ? AND ?", array($mes_actual,$mes_que_viene))
		->andWhere("fecha_vigencia > ? AND fecha_vigencia <= ?", array($mes_actual,$mes_que_viene))
		->orderBy("fecha_vigencia")
		->execute()
		->toArray();
		//->getSqlQuery();
		
		return $rows;
	}
	



	//trae polizas sin filtrar porque es el perfil Operador
	//trae todas las polizas adeudadas
	public function getListadoDeudaClienteByEntidadId($asegurado_id,$agente_id=null,$compania_id=null){

		$estado_debe = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');
		

		$estado_s_anulada = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_ANULADA');
		$estado_anulada = Domain_EstadoPoliza::getIdByCodigo('ANULADA');
		$estado_baja_oficio = Domain_EstadoPoliza::getIdByCodigo('BAJA_DE_OFICIO');


		if( (empty($agente_id) AND empty($compania_id)) ){
			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
			->andWhere("dpc.pago_id = ? ", $estado_debe)
			->andWhere("p.asegurado_id = ? ", $asegurado_id)
			->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
			->orderBy("p.numero_poliza")
			->execute()
			->toArray();
			//->getSqlQuery();
			}elseif(empty($agente_id)){
			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
			->andWhere("dpc.pago_id = ? ", $estado_debe)
			->andWhere("p.asegurado_id = ? and p.compania_id = ?", array($asegurado_id,$compania_id))
			->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
			->orderBy("p.numero_poliza")
			->execute()
			->toArray();
			//->getSqlQuery();

		}elseif(empty($compania_id)){
			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
			->andWhere("dpc.pago_id = ? ", $estado_debe)
			->andWhere("p.asegurado_id = ? and p.agente_id = ?", array($asegurado_id,$agente_id))
			->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
			->orderBy("p.numero_poliza")
			->execute()
			->toArray();

		}else{
			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
			->andWhere("dpc.pago_id = ? ", $estado_debe)
			->andWhere("p.asegurado_id = ? and p.agente_id = ? and p.compania_id = ?", array($asegurado_id,$agente_id,$compania_id))
			->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
			->orderBy("p.numero_poliza")
			->execute()
			->toArray();
			//->getSqlQuery();
		}
			
		return $rows;
	}
	
	public function getListadoDeudaCliente($asegurado_id){

		$estado_debe = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');

		$estado_s_anulada = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_ANULADA');
		$estado_anulada = Domain_EstadoPoliza::getIdByCodigo('ANULADA');
		$estado_baja_oficio = Domain_EstadoPoliza::getIdByCodigo('BAJA_DE_OFICIO');


		$rows = Doctrine_Query::create()
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->andWhere("dpc.pago_id = ? ", $estado_debe)
		->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
		->andWhere("p.asegurado_id = ? ", $asegurado_id)
		->orderBy("p.fecha_pedido")
		->limit(100)
		//->getSqlQuery();
		->execute()
		->toArray();

		return $rows;

	}

public function getListadoDeudaCompaniaByEntidadId($compania_id,$agente_id=null,$asegurado_id=null){
/*echo "<pre>";
		print_r($compania_id);
		print_r($agente_id);
				print_r($asegurado_id);

		exit;*/
		//Buscar el estado Vigente
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_debe = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');
		$estado_baja_devolucion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_DEVOLUCION');
		$estado_renovada = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
		$estado_no_renovado = Domain_EstadoPoliza::getIdByCodigo('NO_RENOVADO');

		$estado_endosada =  Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');
		$estado_vigencia_cumplida = Domain_EstadoPoliza::getIdByCodigo('VIGENCIA_CUMPLIDA');
		$estado_no_refacturado = Domain_EstadoPoliza::getIdByCodigo('NO_REFACTURADO');
		$estado_nota_credito = Domain_EstadoPoliza::getIdByCodigo('NOTA_DE_CREDITO');

		$estado_baja_liberacion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');

		if( (empty($agente_id) AND empty($asegurado_id)) ){

			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_PolizaValores pv')
			->andWhere("p.pago_compania_id = ? ", $estado_debe)
			->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? 
				OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?
				OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?  OR p.estado_id =?'
			,array($estado_vigente,$estado_afectada,$estado_refacturado
			,$estado_baja_devolucion,$estado_renovada,$estado_no_renovado
			,$estado_endosada,$estado_vigencia_cumplida,$estado_no_refacturado
			,$estado_nota_credito,$estado_baja_liberacion))
			->andWhere("p.compania_id = ? ", $compania_id)
			->orderBy("p.numero_poliza")
			->execute()
			->toArray();
			//->getSqlQuery();

		}elseif(empty($agente_id)){
			//echo "agente is empty";
			//exit;

			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_PolizaValores pv')
			->andWhere("p.pago_compania_id = ? ", $estado_debe)
			->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? 
				OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?
				OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?
				OR p.estado_id =?'
			,array($estado_vigente,$estado_afectada,$estado_refacturado
				,$estado_baja_devolucion,$estado_renovada,$estado_no_renovado
				,$estado_endosada,$estado_vigencia_cumplida,$estado_no_refacturado
				,$estado_nota_credito,$estado_baja_liberacion))
			->andWhere("p.asegurado_id = ? and p.compania_id = ?", array($asegurado_id,$compania_id))
			->orderBy("p.numero_poliza")
			->execute()
			->toArray();
			//->getSqlQuery();


		}elseif(empty($asegurado_id)){
		
			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_PolizaValores pv')
			->andWhere("p.pago_compania_id = ? ", $estado_debe)
			->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? 
				OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?
				OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?'
			,array($estado_vigente,$estado_afectada,$estado_baja_liberacion,$estado_refacturado,
				$estado_baja_devolucion,$estado_renovada,$estado_no_renovado
				,$estado_endosada,$estado_vigencia_cumplida,$estado_no_refacturado,$estado_nota_credito))
			->andWhere("p.compania_id = ? and p.agente_id = ?", array($compania_id,$agente_id))
			->orderBy("p.numero_poliza")
			->execute()
			->toArray();

		}else{

			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_PolizaValores pv')
			->andWhere("p.pago_compania_id = ? ", $estado_debe)
			->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? 
				OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?
				OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?'
			,array($estado_vigente,$estado_afectada,
				$estado_refacturado,$estado_baja_devolucion
				,$estado_renovada,$estado_no_renovado,$estado_baja_liberacion
				,$estado_endosada,$estado_vigencia_cumplida,$estado_no_refacturado,$estado_nota_credito))
			->andWhere("p.asegurado_id = ? and p.agente_id = ? and p.compania_id = ?", array($asegurado_id,$agente_id,$compania_id))
			->orderBy("p.numero_poliza")
			->execute()
			->toArray();
			//->getSqlQuery();


		}
			

		return $rows;
	}
	
	public function getListadoDeudaCompania($compania_id){

		//Buscar el estado Vigente
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_debe = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');
		$estado_baja_devolucion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_DEVOLUCION');
		$estado_renovada = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
		$estado_no_renovado = Domain_EstadoPoliza::getIdByCodigo('NO_RENOVADO');


		$estado_endosada =  Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');
		$estado_vigencia_cumplida = Domain_EstadoPoliza::getIdByCodigo('VIGENCIA_CUMPLIDA');
		$estado_no_refacturado = Domain_EstadoPoliza::getIdByCodigo('NO_REFACTURADO');
		$estado_nota_credito = Domain_EstadoPoliza::getIdByCodigo('NOTA_DE_CREDITO');
		$estado_baja_liberacion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');



		$rows = Doctrine_Query::create()
		->from('Model_Poliza p, p.Model_PolizaValores pv')
		->andWhere("p.pago_compania_id = ? ", $estado_debe)
		->andwhere('p.estado_id = ? OR p.estado_id = ? OR p.estado_id = ? 
			OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?
			OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?'
		,array($estado_vigente,$estado_afectada,$estado_refacturado,$estado_baja_devolucion
			,$estado_renovada,$estado_no_renovado,$estado_baja_liberacion
			,$estado_endosada,$estado_vigencia_cumplida,$estado_no_refacturado,$estado_nota_credito))
		->andWhere("p.compania_id = ? ", $compania_id)
		->orderBy("p.fecha_pedido")
		->limit(100)
		->execute()
		->toArray();
		//->getSqlQuery();
		
		return $rows;

	}
	
	//Seccion Pago Compania
	//Aca no filtra pero en los otros tipos de usuario deberia filtrar por agente_id
	public function findCompaniaByNombre($nombre){

		$compania = new Model_Compania();
		$rows = $compania->getTable()
		->createQuery()
		->where('nombre like ?',"%$nombre%")
		->execute()
		->toArray();
		return $rows;
	}
//Panel Pago Compania

public function findPolizaInpagaByCompania($nro_poliza,$compania_id){

		$estado_id = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');
		$estado_baja = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_baja_devolucion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_DEVOLUCION');
		$estado_no_renovado = Domain_EstadoPoliza::getIdByCodigo('NO_RENOVADO');
		$estado_nota_credito = Domain_EstadoPoliza::getIdByCodigo('NOTA_DE_CREDITO');
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');
		$estado_vigencia_cumplida = Domain_EstadoPoliza::getIdByCodigo('VIGENCIA_CUMPLIDA');



		$rows = Doctrine_Query::create()
		->from('Model_Poliza p')
		->where('p.compania_id = ? and p.pago_compania_id = ? and p.numero_poliza like ?',array($compania_id,$estado_id,"%$nro_poliza%"))
		->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id = ? OR p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =? OR p.estado_id =?'
		,array($estado_baja,$estado_afectada,$estado_refacturado,$estado_vigente,$estado_baja_devolucion,$estado_no_renovado,$estado_nota_credito
			,$estado_endosada,$estado_vigencia_cumplida))
		->execute()
		->toArray();
		//->getSqlQuery();

		//echo $rows;
		//exit;
		return $rows;
	}
	
	public function getMovimientosByFecha($fecha_desde,$fecha_hasta){
		
		//Que muester todas las polizas, despues veo el filtro
		$this->_model_poliza = new Model_Poliza();
		
		
	   /*
		* SELECT *
		* FROM movimiento m
		* LEFT JOIN movimiento_poliza mp ON m.movimiento_id = mp.movimiento_id
		* WHERE m.movimiento_id =423
        */
		
		$m_movimiento = new Model_Movimiento();

		$movimiento_polizas = Doctrine_Query::create()
		->from('Model_Movimiento m')
		->leftJoin('m.Model_MovimientoPoliza mp')
		//->where('m.movimiento_id = ? ',$movimiento_id)
		->where("m.fecha_pago between ? AND ?", array($fecha_desde,$fecha_hasta))
		->execute()
		->toArray();
		
		
		
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($movimiento_polizas);
		//exit;
		return $movimiento_polizas;
	}
//Este metodo esta mal
	public function getMovimientosByProductorAndFecha($fecha_desde,$fecha_hasta,$productor_id){
		
		//Que muester todas las polizas, despues veo el filtro
		$this->_model_poliza = new Model_Poliza();
		
		
	   /*
		* SELECT *
		* FROM movimiento m
		* LEFT JOIN movimiento_poliza mp ON m.movimiento_id = mp.movimiento_id
		* WHERE m.movimiento_id =423
        */
		//Lo que trae aca es el id de la cuota de la poliza ( poliza_id ) no es la poliza, hay 
		//que ver como lo mejoro o filtro por otro lado
		$m_movimiento = new Model_Movimiento();

		$movimiento_polizas = Doctrine_Query::create()
		->from('Model_Movimiento m')
		->leftJoin('m.Model_MovimientoPoliza mp')
		->innerJoin('mp.Model_Poliza p')
		//->where('m.movimiento_id = ? ',$movimiento_id)
		->where("m.fecha_pago between ? AND ? AND p.productor_id = ?", array($fecha_desde,$fecha_hasta,$productor_id))
		//->where("p.productor_id = ?", $productor_id)
		//->execute()
		//->toArray();
		->getSqlQuery();
		//echo "<pre>";
		//print_r($movimiento_polizas);
		//exit;
		return $movimiento_polizas;
	}

	public function getMovimientosByFechaAndProductorId($fecha_desde,$fecha_hasta,$productor_id){
		
		//Que muester todas las polizas, despues veo el filtro
		$this->_model_poliza = new Model_Poliza();
		
		
	   /*
		* SELECT *
		* FROM movimiento m
		* LEFT JOIN movimiento_poliza mp ON m.movimiento_id = mp.movimiento_id
		* WHERE m.movimiento_id =423
        */
		//1. Traigo los movimientos por fecha
		$m_movimiento = new Model_Movimiento();

		$movimiento_polizas = Doctrine_Query::create()
		->from('Model_Movimiento m')
		->leftJoin('m.Model_MovimientoPoliza mp')
		//->where('m.movimiento_id = ? ',$movimiento_id)
		->where("m.fecha_pago between ? AND ?", array($fecha_desde,$fecha_hasta))
		->execute()
		->toArray();
		
		echo "<pre>";
		print_r($movimiento_polizas);
		echo "FIN.";
		$res_movimiento_poliza = array();
		$res_item_movimiento_poliza = array();
		$count = 0;
		$count2 = 0;
		//recorro el resultado para sacar si la poliza es del productor_id
		foreach ($movimiento_polizas as $movimientos) {
			echo "<br>dentro del primer Foreach:".$count++;
			foreach ( $movimientos['Model_MovimientoPoliza'] as $movimiento){
				echo "<br>dentro del segundo Foreach:".$count2++;
				print_r($movimientos);
				//Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota
				$detalle_pago_cuota_id = $movimiento['poliza_id']; 
				$s_poliza = Domain_Movimiento::getPolizaByDetallePagoId($detalle_pago_cuota_id);
				$d_poliza = new Domain_Poliza($s_poliza['poliza_id']);
				$m_poliza = $d_poliza->getModelPoliza();

			//	if($m_poliza->productor_id == $productor_id){
					
					echo "<br>Entro al if:".$count2;
					//echo "<br>productor_id".$m_poliza->productor_id."es igual a".$productor_id;
					//$res_item_movimiento_poliza['movimiento_id'] = $movimientos['movimiento_id'];
					//$res_item_movimiento_poliza['poliza_id'] = $m_poliza->productor_id;
					
			$res_item_movimiento_poliza['movimiento_id'] = $movimientos['movimiento_id'];
            $res_item_movimiento_poliza['cuenta_corriente_id'] = $movimientos['cuenta_corriente_id'];
            $res_item_movimiento_poliza['asegurado_id'] = $movimientos['asegurado_id'];
            $res_item_movimiento_poliza['compania_id'] = $movimientos['compania_id']; 
            $res_item_movimiento_poliza['cheque_id'] = $movimientos['cheque_id']; 
            $res_item_movimiento_poliza['moneda_id'] = $movimientos['moneda_id'];
            $res_item_movimiento_poliza['fecha_pago'] = $movimientos['fecha_pago']; 
            $res_item_movimiento_poliza['numero_factura'] = $movimientos['numero_factura']; 
            $res_item_movimiento_poliza['importe'] = $movimientos['importe'];
            $res_item_movimiento_poliza['descuento'] = $movimientos['descuento']; 
            $res_item_movimiento_poliza['tipo_movimiento_id'] = $movimientos['tipo_movimiento_id'];
            
            $premio_asegurado = floatval($d_poliza->getModelPolizaValores()->premio_asegurado);
			$cantidad_cuotas = Domain_DetallePago::getCantidadCuotas($m_poliza->detalle_pago_id);
            $res_item_movimiento_poliza['premio_asegurado'] = $premio_asegurado;
            $res_item_movimiento_poliza['cantidad_cuotas'] = $cantidad_cuotas;
            $res_item_movimiento_poliza['numero_poliza'] = $m_poliza->numero_poliza;
            $res_item_movimiento_poliza['plus'] =$d_poliza->getModelPolizaValores()->plus;
            $res_item_movimiento_poliza['premio_compania'] =$d_poliza->getModelPolizaValores()->premio_compania;
            $res_item_movimiento_poliza['endoso'] = $m_poliza->endoso;
            $res_item_movimiento_poliza['detalle_pago_cuota_id'] = $detalle_pago_id;
			$res_item_movimiento_poliza['poliza_id'] = $m_poliza->poliza_id;
					
			$res_movimiento_poliza[] = $res_item_movimiento_poliza;
					
					 				
				//}
			}
		}
		
		
		return $res_movimiento_poliza;
	}
	
}


