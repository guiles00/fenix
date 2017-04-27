<?php
class Domain_Cliente implements Domain_IEntidad{
	private $_model ;
	private $_nombre = "CLIENTE";

	public function __construct($id=null){

		$model = new Model_Asegurado();
		$this->_model = ($id==null)?new $model: $model->getTable()->find($id) ;


	}
	public function getModel(){
		return $this->_model;
	}
	public function getNombre(){
		return $this->_nombre;
	}
	public function getById($id){

		$row = $this->_model
		->getTable()
		->createQuery()
		->andWhere('estado_id = ?',1)
		->andWhere('cliente_id = ?',$this->_model->cliente_id)
		->execute()
		->toArray();
		return $row;
	}

	public function getAsegurados(){

		$row = $this->_model
		->getTable()
		->createQuery()
		->orderBy('nombre')
		->execute()
		->toArray();
		return $row;
	}
	

	/*
	 * Filtra poliza por cliente
	 * estado_id=1 => solicitudes confirmadas
	 */

	public function getPolizas(){
		$this->_model_poliza = new Model_Poliza();

		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id = ?',1)
		->andWhere('cliente_id = ?',$this->_model->cliente_id)
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		//print_r($this->_model->cliente_id);
		return $row;

	}
	//Filtra poliza por cliente
	public function findPolizaByNumero($numero){

			$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_caucion = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		
		
		$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->Where('estado_id not in (0,1)')
		->andWhere('asegurado_id = ?',$this->_model->asegurado_id)
		//->andWhere('cliente_id = ?',$this->_model->cliente_id)
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->orderBy('numero_solicitud DESC')
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		return $row;

	}

	public static function getNameById($id){

		$m_cliente = new Model_Asegurado();
		$cliente = $m_cliente->getTable()->find($id);
		
		return $cliente->nombre;
	}

	//Metodos Compartidos
	
	// Estado = 0 es Poliza no confirmada => Solicitud
	public function findSolicitudByNumero($numero){

		//$estado = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_PENDIENTE');

		$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->Where('estado_id in (0,1)')
		->andWhere('asegurado_id = ?',$this->_model->asegurado_id)
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
	public function  getSaldo(){
		return false;
	}
	public function getSolicitudes(){

return false;

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
		->andWhere('asegurado_id = ?',$this->_model->asegurado_id)
		->limit(50)
		->orderBy('numero_solicitud DESC')
		->execute()
		->toArray();
		return $row;

	}
	
public function getListadoDeudaCliente($agente_id = null){
		
		$estado_debe = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');

		$estado_s_anulada = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_ANULADA');
		$estado_anulada = Domain_EstadoPoliza::getIdByCodigo('ANULADA');
		$estado_baja_oficio = Domain_EstadoPoliza::getIdByCodigo('BAJA_DE_OFICIO');

	
		
		$rows = Doctrine_Query::create()
		->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
		->andWhere("dpc.pago_id = ? ", $estado_debe)
		->andWhere('asegurado_id = ?',$this->_model->asegurado_id)
		->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
		->execute()
		->toArray();
			
		
		//->getSqlQuery();
		//echo $row;
		
		return $rows;

	}
	
public function getListadoDeudaClienteByEntidadId($asegurado_id,$agente_id=null,$compania_id=null){

		$estado_debe = Domain_Helper::getHelperIdByDominioAndName('estado_pago', 'DEBE');

		$estado_s_anulada = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_ANULADA');
		$estado_anulada = Domain_EstadoPoliza::getIdByCodigo('ANULADA');
		$estado_baja_oficio = Domain_EstadoPoliza::getIdByCodigo('BAJA_DE_OFICIO');

		if( (empty($agente_id) AND empty($compania_id)) ){

			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
			->andWhere("dpc.pago_id = ? ", $estado_debe)
			->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
			->andWhere("p.asegurado_id = ? ", $asegurado_id)
			->execute()
			->toArray();


		}elseif(empty($agente_id)){
			//echo "agente is empty";
			//exit;

			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
			->andWhere("dpc.pago_id = ? ", $estado_debe)
			->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
			->andWhere("p.asegurado_id = ? and p.compania_id = ?", array($asegurado_id,$compania_id))
			->execute()
			->toArray();
			//->getSqlQuery();


		}elseif(empty($compania_id)){
			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
			->andWhere("dpc.pago_id = ? ", $estado_debe)
			->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
			->andWhere("p.asegurado_id = ? and p.agente_id = ?", array($asegurado_id,$agente_id))
			->execute()
			->toArray();

		}else{

			$rows = Doctrine_Query::create()
			->from('Model_Poliza p, p.Model_DetallePago dp, dp.Model_DetallePagoCuota dpc')
			->andWhere("dpc.pago_id = ? ", $estado_debe)
			->andwhere("p.estado_id <> ? AND p.estado_id <> ? AND p.estado_id <> ?"
				,array($estado_anulada,$estado_baja_oficio,$estado_s_anulada))
			->andWhere("p.asegurado_id = ? and p.agente_id = ? and p.compania_id = ?", array($asegurado_id,$agente_id,$compania_id))
			->execute()
			->toArray();
			//->getSqlQuery();
		}
			

		return $rows;
	}
	
	
		public function findAseguradoByNombre($nombre){

		$asegurado = new Model_Asegurado();
		$rows = $asegurado->getTable()
		->createQuery()
		->where('nombre like ?',"%$nombre%")
		->andWhere('asegurado_id = ?',$this->_model->asegurado_id)
		->execute()
		->toArray();
		return $rows;
	}
	
	public function getPolizasVencimiento($fecha_desde,$fecha_hasta,$asegurado_id){

		//Buscar el estado Vigente
		$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$estado_caucion = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		//Que muester todas las polizas, despues veo el filtro
		$this->_model_poliza = new Model_Poliza();
		
		$rows = $this->_model_poliza
		->getTable()
		->createQuery()
		->Where('estado_id not in (0,1)' )
		->andWhere("fecha_vigencia_hasta between ? AND ?", array($fecha_desde,$fecha_hasta))
		->andWhere('asegurado_id = ?',$this->_model->asegurado_id)
		//->getSqlQuery();
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

