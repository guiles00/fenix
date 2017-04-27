<?php
class Domain_Agente implements Domain_IEntidad {
	private $_model ;
	private $_nombre = "AGENTE";

	public function __construct($id=null){

		$model = new Model_Agente();
		$this->_model = ($id==null)?new $model: $model->getTable()->find($id) ;


	}
	public function getModel(){
		return $this->_model;
	}

	public function getNombre(){
		return $this->_nombre;
	}

	//Trae las solicitudes, por ahora hardcodeado
	public function getById($id){

		$row = $this->_model
		->getTable()
		->createQuery()
		->Where('estado_id in (0,1)')
		->andWhere('agente_id = ?',$this->_model->agente_id)
		->execute()
		->toArray();
		return $row;
	}


	/*
	 * Filtra poliza por agente
	 * estado_id=1 => solicitudes confirmadas
	 */

	public function getPolizas(){
		$this->_model_poliza = new Model_Poliza();

		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id in ?',array(2))
		->andWhere('agente_id = ?',$this->_model->agente_id)
		->execute()
		->toArray();

		return $row;

	}
	//Filtra poliza por agente
	public function findPolizaByNumero($numero){

		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		
		$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado_vigente,$estado_caucion,$estado_refacturado))
		->andWhere('agente_id = ?',$this->_model->agente_id)
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->execute()
		->toArray();

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
		->andWhere('agente_id = ?',$this->_model->agente_id)
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		//exit;
		return $row;

	}
	public function getSaldo(){

	}
	public function getSolicitudes(){

	}

	public static function getNameById($id){

		$m_agente = new Model_Agente();
		$agente = $m_agente->getTable()->find($id);

		return $agente->nombre;
	}

	//Filtra todos los asegurados que tienen alguna poliza
	/* @params: $nombre
	*/
	public function findAseguradoByNombre($nombre){

	//Traeme los asegurados que tienen este agente en alguna poliza
	
	$rows = Doctrine_Query::create()
        ->from('Model_Poliza p')
        ->innerJoin('p.Model_Asegurado a')
        ->innerJoin('p.Model_Agente ag')
        ->where('ag.agente_id = ?',$this->_model->agente_id)
        ->andWhere('a.nombre like ?',"%$nombre%")
       // ->limit(10)
        //->getSqlQuery();
        ->execute()
        ->toArray();

		/*$asegurado = new Model_Asegurado();
		$rows = $asegurado->getTable()
		->createQuery()
		->where('nombre like ?',"%$nombre%")
		->execute()
		->toArray();*/
	//	->getSqlQuery();

		/*
		* Formateo para mostrar el resultado
		* 1. saco la data de mas
		* 2. saco lo repetido
		*/
		
		$asegurados = array();
		foreach ($rows as $key => $value) {
		$asegurados[]=$value[Model_Asegurado];	
		}

		//Saco lo repetidos
		$asegurados_norep = array();
		foreach ($asegurados as $key => $value) {
			
			if ( !in_array($value, $asegurados_norep) ) 
				$asegurados_norep[] = $value;
		}
		

		return $asegurados_norep;
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
		//print_r($rows);
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
		->where('p.asegurado_id = ? and p.agente_id = ? and dpc.pago_id = ?',array($asegurado_id,$this->_model->agente_id,$estado_id))
		->execute()
		->toArray();

		return $rows;
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
		->andWhere('agente_id = ?',$this->_model->agente_id)
		->limit(50)
		->orderBy('numero_solicitud DESC')
		->execute()
		->toArray();
		return $row;
	}

	//trae todas las polizas adeudadas
	public function getListadoDeudaClienteByEntidadId($asegurado_id,$agente_id=null,$compania_id=null){

		$estado_debe = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');


		$estado_s_anulada = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_ANULADA');
		$estado_anulada = Domain_EstadoPoliza::getIdByCodigo('ANULADA');
		$estado_baja_oficio = Domain_EstadoPoliza::getIdByCodigo('BAJA_DE_OFICIO');



		if(empty($compania_id)){
			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
			->andWhere("dpc.pago_id = ? ", $estado_debe)
			->andWhere('agente_id = ?',$this->_model->agente_id)
			->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
			->andWhere("p.asegurado_id = ? ", $asegurado_id)
			->execute()
			->toArray();
				
				
		}else{

			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
			->andWhere("dpc.pago_id = ? ", $estado_debe)
			->andWhere('agente_id = ?',$this->_model->agente_id)
			->andWhere("compania_id = ? ", $compania_id)
			->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
			->andWhere("p.asegurado_id = ? ", $asegurado_id)
			->execute()
			->toArray();
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
		->andWhere('agente_id = ?',$this->_model->agente_id)
		->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
		->andWhere("p.asegurado_id = ? ", $asegurado_id)
//		->getSqlQuery();
		->execute()
		->toArray();

		return $rows;

	}
	
public function getPolizaInpagaByAseguradoAndTipo($asegurado_id,$tipo_poliza_id){

		$estado_id = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');
		$estado_baja = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_baja_devolucion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_DEVOLUCION');
		$estado_no_renovado = Domain_EstadoPoliza::getIdByCodigo('NO_RENOVADO');
		$estado_nota_credito = Domain_EstadoPoliza::getIdByCodigo('NOTA_DE_CREDITO');


		$rows = Doctrine_Query::create()
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.asegurado_id = ? and dpc.pago_id = ?',array($asegurado_id,$estado_id))
		->andWhere('agente_id = ?',$this->_model->agente_id)
		->andwhere('p.tipo_poliza_id = ? ',$tipo_poliza_id)
		->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id = ? OR p.estado_id = ? OR p.estado_id =? OR p.estado_id =?'
		,array($estado_baja,$estado_afectada,$estado_refacturado,$estado_vigente,$estado_baja_devolucion,$estado_no_renovado,$estado_nota_credito))
		->execute()
		->toArray();
		//->getSqlQuery();


		//echo $rows;
		//exit;
		return $rows;
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
		->andWhere('agente_id = ?',$this->_model->agente_id)
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->andWhere('asegurado_id = ?',$asegurado_id)
		->orderBy('numero_solicitud DESC')
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		//print_r($estado_caucion);
		//exit;
		return $row;

	}
	
	public function findPolizaInpagaByAsegurado($nro_poliza,$asegurado_id){

		$estado_id = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');
		$estado_baja = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');
		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		$estado_baja_devolucion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_DEVOLUCION');
		$estado_no_renovado = Domain_EstadoPoliza::getIdByCodigo('NO_RENOVADO');
		$estado_nota_credito = Domain_EstadoPoliza::getIdByCodigo('NOTA_DE_CREDITO');


		$rows = Doctrine_Query::create()
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->where('p.asegurado_id = ? and dpc.pago_id = ? and p.numero_poliza like ?',array($asegurado_id,$estado_id,"%$nro_poliza%"))
		->andWhere('agente_id = ?',$this->_model->agente_id)
		->andwhere('p.estado_id = ? OR p.estado_id =? OR p.estado_id =? OR p.estado_id = ? OR p.estado_id = ? OR p.estado_id =? OR p.estado_id =?'
		,array($estado_baja,$estado_afectada,$estado_refacturado,$estado_vigente,$estado_baja_devolucion,$estado_no_renovado,$estado_nota_credito))
		->execute()
		->toArray();
		//->getSqlQuery();


		//echo $rows;
		//exit;
		return $rows;
	}
	
	
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


		//Que muester todas las polizas, despues veo el filtro
		$this->_model_poliza = new Model_Poliza();
		
		if(empty($asegurado_id)){

			$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		->Where('estado_id not in (0,1)' )
		->andWhere("fecha_vigencia_hasta between ? AND ?", array($fecha_desde,$fecha_hasta))
		->andWhere('agente_id = ?',$this->_model->agente_id)
		->andWhere("estado_id not in ($estado_baja_devolucion,$estado_baja_liberacion,$estado_baja_oficio,$estado_no_renovado,$estado_vigencia_cumplida)")

		//->andWhere("fecha_vigencia_hasta => ? AND fecha_vigencia_hasta =< ?", array($fecha_desde,$fecha_hasta))
		->execute()
		->toArray();
			
		}else{
		$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		//->Where('estado_id = ? OR estado_id = ? OR estado_id = ?',array($estado,$estado_caucion,$estado_refacturado))
		->Where('estado_id not in (0,1)' )
		->andWhere('agente_id = ?',$this->_model->agente_id)
		->andWhere("fecha_vigencia_hasta between ? AND ?", array($fecha_desde,$fecha_hasta))
		->andWhere("asegurado_id = ?",$asegurado_id)
		->andWhere("estado_id not in ($estado_baja_devolucion,$estado_baja_liberacion,$estado_baja_oficio,$estado_no_renovado,$estado_vigencia_cumplida)")

		//->andWhere("fecha_vigencia_hasta => ? AND fecha_vigencia_hasta =< ?", array($fecha_desde,$fecha_hasta))
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo $rows;
		//exit;
		}
		//echo $fecha_desde."hasta".$fecha_hasta;
		return $rows;

	}
	
	static function chkPermisoVista($asegurado_id,$agente_id){
		
		//veo si hay alguna poliza con ese asegurado que tenga ese agente
		
		$m_poliza = new Model_Poliza();
		
		$rows = $m_poliza
		->getTable()
		->createQuery()
		->andWhere("asegurado_id = ? AND agente_id = ?", array($asegurado_id,$agente_id))
		->execute()
		->toArray();
		
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
	
}

