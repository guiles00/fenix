<?php
require_once ('IndexController.php');

class Poliza_PolizaController extends Poliza_IndexController
{
	private $_poliza = null;
	private $_sesion = null;
	private $_usuario = null;
	private $_t_usuario = null;
	private $_services_poliza = null;

	public function init()
	{

		if ( ! (Zend_Auth::getInstance()->hasIdentity())  ) {

			$this->_helper->redirector('index','login','default');
		}

		$this->_helper->layout->disableLayout();
		$this->_poliza = new Domain_Poliza();
		$this->_services_simple_crud = new Services_SimpleCrud();
		$this->_services_poliza = new Services_Poliza();
		$this->_sesion = Domain_Sesion::getInstance();
		$this->_usuario = $this->_sesion->getUsuario();
		$this->_t_usuario = $this->_usuario->getTipoUsuario();
		
		//le agrego aca los permisos, despues deberia ver si lo hago con el ACL q
		// Pero primero tengo que arreglarlo
		$operador_id = Domain_Helper::getHelperIdByDominioAndName('entidad','operador');
		$this->view->operador = ( $operador_id==$this->_usuario->getModel()->tipo_usuario_id )? true : false;
		$arr_perfil_id = $this->_usuario->getUserPerfilTemp();
		$this->view->perfil_id = $arr_perfil_id[0];

				
		$perfil = $this->_usuario->getAcl()->userPerfil;
		
		$this->view->isOperadorSolicitud = false;
		if(!empty($perfil['OPERADOR_SOLICITUD'])) $this->view->isOperadorSolicitud = true;

		if( $this->_usuario->getAcl()->hasPermission('solicitud') ) {

			//exit('tiene permiso');
		} else {
			//exit('no tiene permiso');
		}
			
		
	//Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$this->view->isCliente = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}elseif($rol_usuario=='CLIENTE'){
				
			$this->view->isCliente = true;
		}

		//$logger = Zend_Registry::get('logger');
		//$logger->log($this->_usuario->getEntidad() , Zend_Log::INFO);
			
	}

	public function indexAction()
	{
		$this->_forward('list','solicitud','poliza');

	}

	public function listAction()
	{
		$solicitud = new Domain_Poliza();

		$params = $this->_request->getParams();

		//print_r($params);
		//exit;
		$cliente = new Domain_Cliente();
		$this->view->asegurados= $cliente->getAsegurados();
		
		if($params['busqueda']){
		
		
		if(!empty($params['asegurado_id'])){
		$rows = $this->_t_usuario->findPolizaByNumeroAndAsegurado($params['criterio'],$params['asegurado_id']);
		}else{
		$rows = $this->_t_usuario->findPolizaByNumero($params['criterio']);
		}
		
		$this->view->criterio = $params['criterio'];
		$this->view->asegurado_id = $params['asegurado_id'];
		$this->view->busqueda = true;
		
		
		}else{
		
		$rows = $this->_t_usuario->getPolizasDefault();
		}	
		//echo"<pre>";
		//print_r($this->_t_usuario);
		$this->view->tipo_usuario_id = $this->_t_usuario->tipo_usuario_id;
		$page=$this->_getParam('page',1);
			
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		//$this->view->criterio = $params['criterio'];
		//$this->view->rows = $paginator;
			
			
		//}
		//echo "dentro del controlador de la poliza";
		//echo "<pre>";
		//print_r($rows);
		//$rows = $this->_services_simple_crud->getAll($this->_solicitud->getModel());
		$this->view->rows = $paginator;
	}
	
	public function searchAction()
	{
		
	/*
	 * @Params: Asegurado,Compania,Estado,Tipo Poliza
	 */
		
		$this->view->poliza_estados= Domain_EstadoPoliza::getEstados();
		$this->view->poliza_tipos= Domain_TipoPoliza::getTipos();
		$this->view->operacion = Domain_Helper::getHelperByDominio('operacion');
		
		$cliente = new Domain_Cliente();
		$this->view->asegurados= $cliente->getAsegurados();
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		//$this->_helper->viewRenderer->setNoRender();


		$params = $this->_request->getParams();
		
		$date = date("Y-m-d");
		$fecha_desde = strtotime ( '-1 month' , strtotime ( $date ) ) ;
		$fecha_desde = date ( 'Y-m-j' , $fecha_desde );

		//Fechas de busqueda
		$this->view->fecha_desde = $fecha_desde;	
		
		$this->view->fecha_hasta = date("Y-m-d");	
		
		if($params['busqueda']){
		$this->view->busqueda = true;
		//echo "<pre>";
		//print_r($params);
		$array_parametros = array_slice($params, 4); //No se como hacer que solo me traiga los parametros 
		$this->view->searchParams = $array_parametros;
		//print_r($array_parametros);
		//exit;
		$fechas = array('fecha_desde'=>$params['fecha_desde'],'fecha_hasta'=>$params['fecha_hasta']);
		unset($array_parametros['fecha_desde']);
		unset($array_parametros['fecha_hasta']);
		//armo el array de busqueda
		$array_busqueda = array();
		foreach ($array_parametros as $key=>$value) {
		     //echo $key."=".$value;
		     if(!empty($value)){
		     $campos=array("nombre"=>$key,"valor"=>$value);	
		     $array_busqueda[]=$campos;
		     }
		}
		//echo "<pre>";
		//print_r($array_busqueda);
		
		//$poliza = new Domain_Poliza();
		
		//$campo_1 = array("nombre"=>"estado_id","valor"=>14);
		///$campo_2 = array("nombre"=>tipo_poliza_id,"valor"=>2);
		//$array_busqueda  = array($campo_1,$campo_2);
		
		//echo "<pre>";
		//print_r($array_busqueda);
		//exit;
		
		$this->view->arrayBusqueda = json_encode($array_busqueda);
		$rows = $this->_t_usuario->searchPoliza($array_busqueda,$fechas);
//		echo "<prr>";
//		print_r($rows);
//		
		//$this->view->criterio = $params['criterio'];
		//$this->view->asegurado_id = $params['asegurado_id'];
		//$this->view->busqueda = true;
		
		
		}	
		$this->view->rows = $rows;
	//	$this->view->tipo_usuario_id = $this->_t_usuario->tipo_usuario_id;
	//	$page=$this->_getParam('page',1);
			
	//	$paginator = Zend_Paginator::factory($rows);
	//	$paginator->setItemCountPerPage(10);
	//	$paginator->setCurrentPageNumber($page);
	//	
	//	$this->view->rows = $paginator;
	}

	public function resSearchAction()
	{
		
	/*
	 * @Params: Asegurado,Compania,Estado,Tipo Poliza
	 */
		
		$this->view->poliza_estados= Domain_EstadoPoliza::getEstados();
		$this->view->poliza_tipos= Domain_TipoPoliza::getTipos();
		$this->view->operacion = Domain_Helper::getHelperByDominio('operacion');
		
		$cliente = new Domain_Cliente();
		$this->view->asegurados= $cliente->getAsegurados();
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		//$this->_helper->viewRenderer->setNoRender();


		$params = $this->_request->getParams();
		
		$date = date("Y-m-d");
		$fecha_desde = strtotime ( '-1 month' , strtotime ( $date ) ) ;
		$fecha_desde = date ( 'Y-m-j' , $fecha_desde );

		//Fechas de busqueda
		$this->view->fecha_desde = $fecha_desde;	
		
		$this->view->fecha_hasta = date("Y-m-d");	
		
		if($params['busqueda']){
		$this->view->busqueda = true;
		//echo "<pre>";
		//print_r($params);
		$array_parametros = array_slice($params, 4); //No se como hacer que solo me traiga los parametros 
		$this->view->searchParams = $array_parametros;
		//print_r($array_parametros);
		//exit;
		$fechas = array('fecha_desde'=>$params['fecha_desde'],'fecha_hasta'=>$params['fecha_hasta']);
		unset($array_parametros['fecha_desde']);
		unset($array_parametros['fecha_hasta']);
		//armo el array de busqueda
		$array_busqueda = array();
		foreach ($array_parametros as $key=>$value) {
		     //echo $key."=".$value;
		     if(!empty($value)){
		     $campos=array("nombre"=>$key,"valor"=>$value);	
		     $array_busqueda[]=$campos;
		     }
		}
		
		$this->view->arrayBusqueda = json_encode($array_busqueda);
		$rows = $this->_t_usuario->searchPoliza($array_busqueda,$fechas);
	
		
		}	
		$this->view->rows = $rows;
	
	}

	public function imprimirListadoPolizaDetalleAction()
	{
		
	/*
	 * @Params: Asegurado,Compania,Estado,Tipo Poliza
	 */
		
		$this->view->poliza_estados= Domain_EstadoPoliza::getEstados();
		$this->view->poliza_tipos= Domain_TipoPoliza::getTipos();
		$this->view->operacion = Domain_Helper::getHelperByDominio('operacion');
		
		$cliente = new Domain_Cliente();
		$this->view->asegurados= $cliente->getAsegurados();
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		//$this->_helper->viewRenderer->setNoRender();


		$params = $this->_request->getParams();
		
		$this->view->busqueda = true;
		//echo "<pre>";
		//print_r($params);
		//exit;
		$array_parametros = array_slice($params, 3); //No se como hacer que solo me traiga los parametros 
		//armo el array de busqueda
		//print_r($array_parametros);
		//exit;

		$fechas = array('fecha_desde'=>$params['fecha_desde'],'fecha_hasta'=>$params['fecha_hasta']);
		unset($array_parametros['fecha_desde']);
		unset($array_parametros['fecha_hasta']);
		
		$array_busqueda = array();
		foreach ($array_parametros as $key=>$value) {
		     //echo $key."=".$value;
		     if(!empty($value)){
		     $campos=array("nombre"=>$key,"valor"=>$value);	
		     $array_busqueda[]=$campos;
		     }
		}
		
		$rows = $this->_t_usuario->searchPoliza($array_busqueda,$fechas);
	//print_r($rows);
		//exit;
		$this->view->rows = $rows;
	}

	public function altaAutomotoresAction()
	{
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();



		$params = $this->_request->getParams();
		echo "<pre>";
		print_r($params);
		//exit;
		//Trae datos para mostrar en la solicitud

		//Si viene con ID es para guardar y traigo la solicitud con los datos
		if(! empty($params['solicitud_id']) ){
			$solicitud = new Domain_Poliza($params['solicitud_id']);
			$this->view->d_solicitud = $solicitud->getModelPoliza();
			$this->view->d_poliza_valores = $solicitud->getModelPolizaValores();
		}else{
			$solicitud = $this->_solicitud;
		}
			
		//echo"<pre>";
		//print_r($solicitud);
			
		if($params['save']){
			//doy de alta la solicitud
			//primero guardo las tablas asociadas
			$m_poliza_valores = $solicitud->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			//$m_poliza_valores->premio_cliente=$params['premio_cliente'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
			//$m_poliza_valores->poliza_valores_id;

			$m_solicitud = $solicitud->getModelPoliza();
			$m_solicitud->asegurado_id=$params['asegurado_id'];
			$m_solicitud->agente_id=$params['agente_id'];
			$m_solicitud->compania_id=$params['compania_id'];
			$m_solicitud->productor_id=$params['productor_id'];
			$m_solicitud->poliza_valores_id = $m_poliza_valores->poliza_valores_id;

			//Guardo la poliza
			$m_solicitud->save();
			$m_solicitud->numero_solicitud = $m_solicitud->poliza_id ;
			$m_solicitud->save();

			$this->view->d_solicitud = $m_solicitud;
			$this->view->d_poliza_valores = $m_poliza_valores;
		}
		//$service_model = new Services_SimpleCrud();
		//$rows = $this->_services_simple_crud->getAll($this->_solicitud->getModel());

	}
	public function confirmarPolizaAction(){
		$estado = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_CONFIRMADA');
		$this->_helper->viewRenderer->setNoRender();
		$params = $this->_request->getParams();
		//echo "<pre>";
		//print_r($params);
		$solicitud = new Domain_Poliza($params['solicitud_id']);
		$m_solicitud = $solicitud->getModelPoliza();
		$m_solicitud->estado_id=$estado;
		$m_solicitud->save();

	}


	public function refacturarPolizaAction(){
		//$this->_helper->viewRenderer->setNoRender();
		
		$params = $this->_request->getParams();


		$poliza = new Domain_Poliza($params['poliza_id']);

		$tipo_poliza = Domain_TipoPoliza::getNameById($poliza->getModelPoliza()->tipo_poliza_id);
		
		switch ($tipo_poliza) {
			case 'ADUANEROS':
				$poliza = $this->_services_poliza->refacturarPolizaAduaneros($poliza);
					
				break;
					
			case 'AUTOMOTORES':
				$poliza = $this->_services_poliza->refacturarPolizaConstruccion($poliza);
					
				break;

			case 'ALQUILER':
				$poliza = $this->_services_poliza->refacturarPolizaAlquiler($poliza);
					
				break;
			
			case 'CONSTRUCCION':
				$poliza = $this->_services_poliza->refacturarPolizaConstruccion($poliza);
					
				break;
			
			case 'JUDICIALES':
				$poliza = $this->_services_poliza->refacturarPolizaJudiciales($poliza);
					
				break;

			default:
				;
				break;
		}


		$nro_poliza = $poliza->getModelPoliza()->numero_poliza;
		echo "Poliza $nro_poliza refacturada con exito ;)";

	}


public function validarEndosoAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		
		$params = $this->_request->getParams();
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		
		echo "<pre>";
		$cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$importe = $cantidad_cuotas * $valor_cuotas;
		
		if($params['tipo_endoso_id'] == 1){ //Aumento
		
		//echo"aumento";
		//echo "<br>compara esto".$importe;
		//echo "<br>con".$params['importe_alquiler_endoso'];

			if($params['importe_alquiler_endoso']>=$importe) echo 'true';

			echo 'false' ;

		}else if($params['tipo_endoso_id'] == 2){ //Reduccion

		//echo "reduccion";
		//echo "<br>compara esto".$importe;
		//echo "<br>con".$params['importe_alquiler_endoso'];

			if($params['importe_alquiler_endoso']<=$importe) echo 'true';

			echo 'false';
		
		}else{

			return false;
		}
		

	}


public function endosoPolizaAction(){
		//$this->_helper->viewRenderer->setNoRender();
		
		$params = $this->_request->getParams();


		$poliza = new Domain_Poliza($params['poliza_id']);

		$tipo_poliza = Domain_TipoPoliza::getNameById($poliza->getModelPoliza()->tipo_poliza_id);
		
		switch ($tipo_poliza) {
			case 'ADUANEROS':
				$poliza = $this->_services_poliza->endosoPolizaAduaneros($poliza);
					
				break;
					
			case 'AUTOMOTORES':
				$poliza = $this->_services_poliza->endosoPolizaConstruccion($poliza);
					
				break;

			case 'ALQUILER':
				$poliza = $this->_services_poliza->endosoPolizaAlquiler($poliza);
					
				break;
			case 'CONSTRUCCION':
				$poliza = $this->_services_poliza->endosoPolizaConstruccion($poliza);
					
				break;
				
			default:
				;
				break;
		}


		$nro_poliza = $poliza->getModelPoliza()->numero_poliza;
		echo "Poliza $nro_poliza refacturada con exito ;)";

	}



public function endosoPolizaIgjAction()
	{
		
	    $tipo_poliza_id = Domain_TipoPoliza::getIdByName('IGJ');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		//1. Traigo el POST
		$params = $this->_request->getParams();
		//$poliza = $this->_poliza;

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$m_poliza = $d_poliza->getModelPoliza();
		

		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);


		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		$this->view->poliza_endoso_id = $params['poliza_id'];
		
		//$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		//if(! empty($params['poliza_id']) )$poliza = new Domain_Poliza($params['poliza_id']);
		
		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
		$this->view->poliza_detalle = $d_poliza->getModelDetalle();
		$this->view->detalle_pago = $d_poliza->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');


		if($params['save']){

		//1. Traigo el POST
		$params = $this->_request->getParams();
		
			//Hice un save para endosar la poliza.
			/*
			 * Service_Poliza::savepoliza()
			 * @param: $params(datos del POST)
			 */
		
			$d_poliza_endosada = $this->_services_poliza->endosarPolizaIgj($d_poliza,$params);
			$this->_services_poliza->saveDetallePagoEndoso($d_poliza_endosada,$params); //Devuelve el objeto poliza ( pero no lo uso)
			
			echo "Poliza endosada con exito";
			exit;
		}

	}


public function endosoPolizaSeguroTecnicoAction()
	{
		
	    $tipo_poliza_id = Domain_TipoPoliza::getIdByName('SEGURO_TECNICO');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		//1. Traigo el POST
		$params = $this->_request->getParams();
		//$poliza = $this->_poliza;

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$m_poliza = $d_poliza->getModelPoliza();
		

		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);


		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		$this->view->poliza_endoso_id = $params['poliza_id'];
		
		//$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		//if(! empty($params['poliza_id']) )$poliza = new Domain_Poliza($params['poliza_id']);
		
		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
		$this->view->poliza_detalle = $d_poliza->getModelDetalle();
		$this->view->detalle_pago = $d_poliza->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');


		if($params['save']){

		//1. Traigo el POST
		$params = $this->_request->getParams();
		
			//Hice un save para endosar la poliza.
			/*
			 * Service_Poliza::savepoliza()
			 * @param: $params(datos del POST)
			 */
		
			$d_poliza_endosada = $this->_services_poliza->endosarPolizaSeguroTecnico($d_poliza,$params);
			$this->_services_poliza->saveDetallePagoEndoso($d_poliza_endosada,$params); //Devuelve el objeto poliza ( pero no lo uso)
			
			echo "Poliza endosada con exito";
			exit;
		}

	}






public function endosoPolizaAlquilerAction()
	{
		
	    $tipo_poliza_id = Domain_TipoPoliza::getIdByName('ALQUILER');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		//1. Traigo el POST
		$params = $this->_request->getParams();
		//$poliza = $this->_poliza;

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$m_poliza = $d_poliza->getModelPoliza();
		
		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);


		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		$this->view->poliza_endoso_id = $params['poliza_id'];
		
		//$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		//if(! empty($params['poliza_id']) )$poliza = new Domain_Poliza($params['poliza_id']);
		
		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
		$this->view->poliza_detalle = $d_poliza->getModelDetalle();
		$this->view->detalle_pago = $d_poliza->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');


		if($params['save']){

		//1. Traigo el POST
		$params = $this->_request->getParams();
		//echo "<pre>";
		//print_r($params);
		//exit;
	
		//Validar 

		//exit;
		//if() exit;
			//Hice un save para endosar la poliza.
			/*
			 * Service_Poliza::savepoliza()
			 * @param: $params(datos del POST)
			 */
		
			$d_poliza_endosada = $this->_services_poliza->endosarPolizaAlquiler($d_poliza,$params);
			$this->_services_poliza->saveDetallePagoEndoso($d_poliza_endosada,$params); //Devuelve el objeto poliza ( pero no lo uso)
			
			//endoso y listo, no necesito mostrar. 
			//La operacion termino, muestro mensaje de exito.
			/*
			$this->view->poliza = $poliza->getModelPoliza();
			$this->view->poliza_valores = $poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $poliza->getModelDetalle();

			$this->view->detalle_pago = $poliza->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($poliza->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
			*/
			
			/*
			 * Cambio el estado de la solicitud que se endoso
			 */
			
			/*if( empty($params['poliza_id']) ){
				//echo "entro aca para renovar la poliza";
			$estado_id = Domain_EstadoPoliza::getIdByCodigo('RENOVADA'); //Ver si se cambia a endosada
			$poliza_renovada = new Domain_Poliza($params['poliza_renovada_id']);
			$poliza_renovada_model = $poliza_renovada->getModelPoliza();
			$poliza_renovada_model->estado_id = $estado_id;
			$poliza_renovada_model->save();
			}*/
			
		/*
		 * Les paso los ids de la nueva poliza
		 */	
			/*$this->view->poliza_id = $this->view->solicitud->poliza_id;
			$this->view->numero_solicitud = $this->view->solicitud->numero_solicitud;
			$this->view->solicitud_renovada = $params['poliza_renovada_id'];
			*/
			echo "Poliza endosada con exito";
			exit;
		}

	}
/**
*
* @method endosoPolizaIntegralComercio
*/

public function endosoPolizaIntegralComercioAction()
	{
		
	    $tipo_poliza_id = Domain_TipoPoliza::getIdByName('INTEGRAL_COMERCIO');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		//1. Traigo el POST
		$params = $this->_request->getParams();
		//$poliza = $this->_poliza;

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$m_poliza = $d_poliza->getModelPoliza();
		
		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);


		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		$this->view->poliza_endoso_id = $params['poliza_id'];
		
		//$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		//if(! empty($params['poliza_id']) )$poliza = new Domain_Poliza($params['poliza_id']);
		
		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
		$this->view->poliza_detalle = $d_poliza->getModelDetalle();
		$this->view->detalle_pago = $d_poliza->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($d_poliza->getModelDetalle()->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($d_poliza->getModelDetalle()->motivo_garantia_id, $tipo_poliza_id);
		

		if($params['save']){

		//1. Traigo el POST
		$params = $this->_request->getParams();
		
			//Hice un save para endosar la poliza.
			/*
			 * Service_Poliza::savepoliza()
			 * @param: $params(datos del POST)
			 */
		
			$d_poliza_endosada = $this->_services_poliza->endosarPolizaIntegralComercio($d_poliza,$params);
			$this->_services_poliza->saveDetallePagoEndoso($d_poliza_endosada,$params); //Devuelve el objeto poliza ( pero no lo uso)
			
			echo "Poliza endosada con exito";
			exit;
		}

	}

public function endosoPolizaAduanerosAction()
	{
		
	    $tipo_poliza_id = Domain_TipoPoliza::getIdByName('ADUANEROS');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		//$poliza = $this->_poliza;

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$m_poliza = $d_poliza->getModelPoliza();
		//echo "<pre>";
		//print_r($params);
		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$m_poliza->tipo_endoso_id);
		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		$this->view->poliza_endoso_id = $params['poliza_id'];
		
		//$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		//if(! empty($params['poliza_id']) )$poliza = new Domain_Poliza($params['poliza_id']);
		
		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
		$this->view->poliza_detalle = $d_poliza->getModelDetalle();
		$this->view->detalle_pago = $d_poliza->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $d_poliza->getModelPolizaValores()->moneda_id);
		
		
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $d_poliza->getModelDetallePago()->forma_pago_id);
		$this->view->forma_pago_id = $d_poliza->getModelDetallePago()->forma_pago_id;
		
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($d_poliza->getModelDetalle()->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($d_poliza->getModelDetalle()->motivo_garantia_id, $tipo_poliza_id);
		$this->view->despachante_aduana = Domain_DespachanteAduana::getNameById($d_poliza->getModelDetalle()->despachante_aduana_id);
		
	
		if($params['save']){

		//1. Traigo el POST
		$params = $this->_request->getParams();
		
		$d_poliza_endosada = $this->_services_poliza->endosarPolizaAduaneros($d_poliza,$params);
		$d_detalle_pago_poliza = $this->_services_poliza->saveDetallePagoEndoso($d_poliza_endosada,$params); //Devuelve el objeto poliza 
		
		//echo "<pre>";
		//print_r($temp);

		//3.Traigo el modelo de la poliza
		//reutilizo esta variable
		$d_poliza_endosada = new Domain_Poliza($d_poliza_endosada->getModelPoliza()->poliza_id);
		$m_poliza = $d_poliza_endosada->getModelPoliza();

		//echo"<pre>";
		//print_r($d_poliza_endosada->getModelDetalle());
		$m_poliza_valores = $d_poliza_endosada->getModelPolizaValores();
		$m_poliza_detalle = $d_poliza_endosada->getModelDetalle();
		$m_detalle_pago = $d_detalle_pago_poliza->getModelDetallePago(); //Traigo el otro objeto, no se por que no me trae todos los objs.
		
		$cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($m_detalle_pago->detalle_pago_id);
		$valor_cuotas = (float)Domain_DetallePago::getValorCuotas($m_detalle_pago->detalle_pago_id);

		$poliza_endoso_id = $m_poliza->poliza_id;
		$this->view->poliza_endoso_id = $poliza_endoso_id;
		
		//Endoso la poliza y ahora la muestra
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ADUANEROS');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		
		//echo "<pre>";
		//print_r($params);
		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);


		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		
		//$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		//if(! empty($params['poliza_id']) )$poliza = new Domain_Poliza($params['poliza_id']);
		
		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $m_poliza_valores;
		$this->view->poliza_detalle = $m_poliza_detalle;
		$this->view->detalle_pago = $m_detalle_pago;
		$this->view->cantidad_cuotas = $cantidad_cuotas;
		$this->view->valor_cuotas = $valor_cuotas;
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza_endosada->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$params['tipo_endoso_id']);


		//Envia por mail

		//$this->enviarSolicitudEndosoCompaniaAduaneros($d_poliza_endosada);
		echo "<br><font color='blue'>Poliza endosada con exito: ".$m_poliza->numero_poliza."/".$m_poliza->endoso."</font>";
		
		echo "<br><font color='blue'>".$this->view->tipo_endoso_text."</font>";
		exit;
		}

	}

public function endosoPolizaConstruccionAction()
	{
		
	    $tipo_poliza_id = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		//$poliza = $this->_poliza;

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$m_poliza = $d_poliza->getModelPoliza();
		//echo "<pre>";
		//print_r($params);
		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);
		
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();


		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$m_poliza->tipo_endoso_id);
		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		$this->view->poliza_endoso_id = $params['poliza_id'];
		
		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
		$this->view->poliza_detalle = $d_poliza->getModelDetalle();
		$this->view->detalle_pago = $d_poliza->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $d_poliza->getModelDetallePago()->forma_pago_id);
		$this->view->forma_pago_id = $d_poliza->getModelDetallePago()->forma_pago_id;
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $d_poliza->getModelPolizaValores()->moneda_id);


		if($params['save']){

		//1. Traigo el POST
		$params = $this->_request->getParams();
		//echo "<pre>";
		//print_r($params);
		//exit;
		$d_poliza_endosada = $this->_services_poliza->endosarPolizaConstruccion($d_poliza,$params);
		$d_detalle_pago_poliza = $this->_services_poliza->saveDetallePagoEndoso($d_poliza_endosada,$params); //Devuelve el objeto poliza 
		
		//echo "<pre>";
		//print_r($temp);

		//3.Traigo el modelo de la poliza
		//reutilizo esta variable
		$d_poliza_endosada = new Domain_Poliza($d_poliza_endosada->getModelPoliza()->poliza_id);
		$m_poliza = $d_poliza_endosada->getModelPoliza();

		//echo"<pre>";
		//print_r($d_poliza_endosada->getModelDetalle());
		$m_poliza_valores = $d_poliza_endosada->getModelPolizaValores();
		$m_poliza_detalle = $d_poliza_endosada->getModelDetalle();
		$m_detalle_pago = $d_detalle_pago_poliza->getModelDetallePago(); //Traigo el otro objeto, no se por que no me trae todos los objs.
		
		$cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($m_detalle_pago->detalle_pago_id);
		$valor_cuotas = (float)Domain_DetallePago::getValorCuotas($m_detalle_pago->detalle_pago_id);

		$poliza_endoso_id = $m_poliza->poliza_id;
		$this->view->poliza_endoso_id = $poliza_endoso_id;
		
		//Endoso la poliza y ahora la muestra
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		
		
		//echo "<pre>";
		//print_r($params);
		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);


		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $m_poliza_valores;
		$this->view->poliza_detalle = $m_poliza_detalle;
		$this->view->detalle_pago = $m_detalle_pago;
		$this->view->cantidad_cuotas = $cantidad_cuotas;
		$this->view->valor_cuotas = $valor_cuotas;
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza_endosada->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$params['tipo_endoso_id']);


		//Envia por mail

		//$this->enviarSolicitudEndosoCompaniaAduaneros($d_poliza_endosada);
		echo "<br><font color='blue'>Poliza endosada con exito: ".$m_poliza->numero_poliza."/".$m_poliza->endoso."</font>";
		
		echo "<br><font color='blue'>".$this->view->tipo_endoso_text."</font>";
		//exit;
		}

	}

public function endosoPolizaResponsabilidadCivilAction()
	{
		
	    $tipo_poliza_id = Domain_TipoPoliza::getIdByName('RESPONSABILIDAD_CIVIL');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		//$poliza = $this->_poliza;

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$m_poliza = $d_poliza->getModelPoliza();
		//echo "<pre>";
		//print_r($params);
		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$m_poliza->tipo_endoso_id);
		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		$this->view->poliza_endoso_id = $params['poliza_id'];
		
		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
		$this->view->poliza_detalle = $d_poliza->getModelDetalle();
		$this->view->detalle_pago = $d_poliza->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');


		if($params['save']){

		//1. Traigo el POST
		$params = $this->_request->getParams();
		//echo "<pre>";
		//print_r($params);
		//exit;
		$d_poliza_endosada = $this->_services_poliza->endosarPolizaResponsabilidadCivil($d_poliza,$params);
		$d_detalle_pago_poliza = $this->_services_poliza->saveDetallePagoEndoso($d_poliza_endosada,$params); //Devuelve el objeto poliza 
		
		//echo "<pre>";
		//print_r($temp);

		//3.Traigo el modelo de la poliza
		//reutilizo esta variable
		$d_poliza_endosada = new Domain_Poliza($d_poliza_endosada->getModelPoliza()->poliza_id);
		$m_poliza = $d_poliza_endosada->getModelPoliza();

		//echo"<pre>";
		//print_r($d_poliza_endosada->getModelDetalle());
		$m_poliza_valores = $d_poliza_endosada->getModelPolizaValores();
		$m_poliza_detalle = $d_poliza_endosada->getModelDetalle();
		$m_detalle_pago = $d_detalle_pago_poliza->getModelDetallePago(); //Traigo el otro objeto, no se por que no me trae todos los objs.
		
		$cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($m_detalle_pago->detalle_pago_id);
		$valor_cuotas = (float)Domain_DetallePago::getValorCuotas($m_detalle_pago->detalle_pago_id);

		$poliza_endoso_id = $m_poliza->poliza_id;
		$this->view->poliza_endoso_id = $poliza_endoso_id;
		
		//Endoso la poliza y ahora la muestra
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		
		//echo "<pre>";
		//print_r($params);
		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);


		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $m_poliza_valores;
		$this->view->poliza_detalle = $m_poliza_detalle;
		$this->view->detalle_pago = $m_detalle_pago;
		$this->view->cantidad_cuotas = $cantidad_cuotas;
		$this->view->valor_cuotas = $valor_cuotas;
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza_endosada->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$params['tipo_endoso_id']);


		//Envia por mail

		//$this->enviarSolicitudEndosoCompaniaAduaneros($d_poliza_endosada);
		echo "<br><font color='blue'>Poliza endosada con exito: ".$m_poliza->numero_poliza."/".$m_poliza->endoso."</font>";
		
		echo "<br><font color='blue'>".$this->view->tipo_endoso_text."</font>";
		//exit;
		}

	}


public function endosoPolizaAccidentesPersonalesAction()
	{
		
	    $tipo_poliza_id = Domain_TipoPoliza::getIdByName('ACCIDENTES_PERSONALES');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		//$poliza = $this->_poliza;

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$m_poliza = $d_poliza->getModelPoliza();
		//echo "<pre>";
		//print_r($params);
		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$m_poliza->tipo_endoso_id);
		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		$this->view->poliza_endoso_id = $params['poliza_id'];
		
		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
		$this->view->poliza_detalle = $d_poliza->getModelDetalle();
		$this->view->detalle_pago = $d_poliza->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');


		if($params['save']){

		//1. Traigo el POST
		$params = $this->_request->getParams();
		//echo "<pre>";
		//print_r($params);
		//exit;
		$d_poliza_endosada = $this->_services_poliza->endosarPolizaAccidentesPersonales($d_poliza,$params);
		$d_detalle_pago_poliza = $this->_services_poliza->saveDetallePagoEndoso($d_poliza_endosada,$params); //Devuelve el objeto poliza 
		
		//echo "<pre>";
		//print_r($temp);

		//3.Traigo el modelo de la poliza
		//reutilizo esta variable
		$d_poliza_endosada = new Domain_Poliza($d_poliza_endosada->getModelPoliza()->poliza_id);
		$m_poliza = $d_poliza_endosada->getModelPoliza();

		//echo"<pre>";
		//print_r($d_poliza_endosada->getModelDetalle());
		$m_poliza_valores = $d_poliza_endosada->getModelPolizaValores();
		$m_poliza_detalle = $d_poliza_endosada->getModelDetalle();
		$m_detalle_pago = $d_detalle_pago_poliza->getModelDetallePago(); //Traigo el otro objeto, no se por que no me trae todos los objs.
		
		$cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($m_detalle_pago->detalle_pago_id);
		$valor_cuotas = (float)Domain_DetallePago::getValorCuotas($m_detalle_pago->detalle_pago_id);

		$poliza_endoso_id = $m_poliza->poliza_id;
		$this->view->poliza_endoso_id = $poliza_endoso_id;
		
		//Endoso la poliza y ahora la muestra
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		
		//echo "<pre>";
		//print_r($params);
		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);


		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $m_poliza_valores;
		$this->view->poliza_detalle = $m_poliza_detalle;
		$this->view->detalle_pago = $m_detalle_pago;
		$this->view->cantidad_cuotas = $cantidad_cuotas;
		$this->view->valor_cuotas = $valor_cuotas;
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza_endosada->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$params['tipo_endoso_id']);


		//Envia por mail

		//$this->enviarSolicitudEndosoCompaniaAduaneros($d_poliza_endosada);
		echo "<br><font color='blue'>Poliza endosada con exito: ".$m_poliza->numero_poliza."/".$m_poliza->endoso."</font>";
		
		echo "<br><font color='blue'>".$this->view->tipo_endoso_text."</font>";
		exit;
		}

	}

public function endosoPolizaJudicialesAction()
	{
		
	    $tipo_poliza_id = Domain_TipoPoliza::getIdByName('JUDICIALES');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		//$poliza = $this->_poliza;

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$m_poliza = $d_poliza->getModelPoliza();
		//echo "<pre>";
		//print_r($params);
		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$m_poliza->tipo_endoso_id);
		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		$this->view->poliza_endoso_id = $params['poliza_id'];
		
		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
		$this->view->poliza_detalle = $d_poliza->getModelDetalle();
		$this->view->detalle_pago = $d_poliza->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');


		if($params['save']){

		//1. Traigo el POST
		$params = $this->_request->getParams();
		//echo "<pre>";
		//print_r($params);
		//exit;
		$d_poliza_endosada = $this->_services_poliza->endosarPolizaJudiciales($d_poliza,$params);
		$d_detalle_pago_poliza = $this->_services_poliza->saveDetallePagoEndoso($d_poliza_endosada,$params); //Devuelve el objeto poliza 
		
		//echo "<pre>";
		//print_r($temp);

		//3.Traigo el modelo de la poliza
		//reutilizo esta variable
		$d_poliza_endosada = new Domain_Poliza($d_poliza_endosada->getModelPoliza()->poliza_id);
		$m_poliza = $d_poliza_endosada->getModelPoliza();

		//echo"<pre>";
		//print_r($d_poliza_endosada->getModelDetalle());
		$m_poliza_valores = $d_poliza_endosada->getModelPolizaValores();
		$m_poliza_detalle = $d_poliza_endosada->getModelDetalle();
		$m_detalle_pago = $d_detalle_pago_poliza->getModelDetallePago(); //Traigo el otro objeto, no se por que no me trae todos los objs.
		
		$cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($m_detalle_pago->detalle_pago_id);
		$valor_cuotas = (float)Domain_DetallePago::getValorCuotas($m_detalle_pago->detalle_pago_id);

		$poliza_endoso_id = $m_poliza->poliza_id;
		$this->view->poliza_endoso_id = $poliza_endoso_id;
		
		//Endoso la poliza y ahora la muestra
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso = Domain_Helper::getHelperByDominio('tipo_endoso');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		
		//echo "<pre>";
		//print_r($params);
		//Datos de la poliza

		$this->view->asegurado= Domain_Asegurado::getNameById($m_poliza->asegurado_id);
		$this->view->compania= Domain_Compania::getNameById($m_poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($m_poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($m_poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($m_poliza->cobrador_id);


		//Traigo todos los datos que hay que traducir y de tablas relacionadas
	
		$this->view->poliza = $m_poliza;
		$this->view->poliza_valores = $m_poliza_valores;
		$this->view->poliza_detalle = $m_poliza_detalle;
		$this->view->detalle_pago = $m_detalle_pago;
		$this->view->cantidad_cuotas = $cantidad_cuotas;
		$this->view->valor_cuotas = $valor_cuotas;
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza_endosada->getModelPoliza()->asegurado_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $m_poliza->periodo_id);
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$params['tipo_endoso_id']);


		//Envia por mail

		//$this->enviarSolicitudEndosoCompaniaAduaneros($d_poliza_endosada);
		echo "<br><font color='blue'>Poliza endosada con exito: ".$m_poliza->numero_poliza."/".$m_poliza->endoso."</font>";
		
		echo "<br><font color='blue'>".$this->view->tipo_endoso_text."</font>";
		//exit;
		}

	}




	
	public function notaCreditoPolizaAction(){
		$this->_helper->viewRenderer->setNoRender();
		$params = $this->_request->getParams();

		//echo "<pre>";
		//print_r($params);

		//exit;		
		$poliza = new Domain_Poliza($params['poliza_id']);

		//print_r($poliza);
		//echo "tipo poliza:";
		//print_r($poliza->tipo_poliza_id);
		$tipo_poliza = Domain_TipoPoliza::getNameById($poliza->getModelPoliza()->tipo_poliza_id);
		switch ($tipo_poliza) {
			case 'ADUANEROS':
				$poliza = $this->_services_poliza->notaCreditoPolizaAduaneros($poliza);
					
				break;
					
			case 'AUTOMOTORES':
				//$poliza = $this->_services_poliza->refacturarPolizaConstruccion($poliza);
				echo "En contruccion...";
				break;

			case 'ALQUILER':
				//$poliza = $this->_services_poliza->refacturarPolizaAlquiler($poliza);
				echo "En contruccion...";	
				break;
			case 'CONSTRUCCION':
				
				$poliza = $this->_services_poliza->notaCreditoPolizaConstruccion($poliza);
				
				break;
			case 'JUDICIALES':
				
				$poliza = $this->_services_poliza->notaCreditoPolizaJudiciales($poliza);
				
				break;	

			default:
				;
				break;
		}


		$nro_poliza = $poliza->getModelPoliza()->numero_poliza;
		echo "Nota de cr&eacute;dito creada - Poliza: $nro_poliza";

	}
	
	
	
	public function afectarPolizaAction(){
		//$this->_helper->viewRenderer->setNoRender();
		$params = $this->_request->getParams();

		//echo "<pre>";
		//print_r($params);

		$poliza = new Domain_Poliza($params['poliza_id']);

		//print_r($poliza);
		//echo "tipo poliza:";
		//print_r($poliza->tipo_poliza_id);

		$estado_afectada = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$m_poliza = $poliza->getModelPoliza();
		$m_poliza->estado_id = $estado_afectada;
		$m_poliza->save();
		echo "Poliza $nro_poliza afectada con exito ;)";

	}

	public function bajaOficioPolizaAction(){
		//$this->_helper->viewRenderer->setNoRender();
		$params = $this->_request->getParams();

		//	echo "<pre>";
		//print_r($params);
		$this->view->poliza_id = $params['poliza_id'];
		if(isset($params['baja'])){
		$poliza = new Domain_Poliza($params['poliza_id']);

		$estado_baja_oficio = Domain_EstadoPoliza::getIdByCodigo('BAJA_DE_OFICIO');

		$m_poliza = $poliza->getModelPoliza();
		$m_poliza->estado_id = $estado_baja_oficio;
		$m_poliza->fecha_baja = $params['fecha_baja'];
		$m_poliza->save();
		$this->view->baja = true;
		echo "Poliza $nro_poliza modificada con exito ;)";
		}
	}

public function bajaLiberacionPolizaAction(){
		//$this->_helper->viewRenderer->setNoRender();
		$params = $this->_request->getParams();
        $this->view->poliza_id = $params['poliza_id'];
		//	echo "<pre>";
		//print_r($params);
		if(isset($params['baja'])){
		$poliza = new Domain_Poliza($params['poliza_id']);

		$estado_baja_liberacion = Domain_EstadoPoliza::getIdByCodigo('BAJA_POR_LIBERACION');

		$m_poliza = $poliza->getModelPoliza();
		$m_poliza->estado_id = $estado_baja_liberacion;
		$m_poliza->fecha_baja = $params['fecha_baja'];
		$m_poliza->save();
		$this->view->baja=true;
		echo "Poliza $nro_poliza modificada con exito ;)";
		}
	}

	public function bajaPolizaAction(){
		//$this->_helper->viewRenderer->setNoRender();
		$params = $this->_request->getParams();
		
		$this->view->poliza_id = $params['poliza_id'];
		$this->view->bajas = Domain_Helper::getHelperByDominio('tipo_baja_comunes');
		$this->view->baja=false;
		
		/*echo "<pre>";
		print_r($params);
*/
			
	
		if(isset($params['baja'])){
			$d_poliza = new Domain_Poliza($params['poliza_id']);
			$m_poliza = $d_poliza->getModelPoliza();
			$m_poliza->fecha_baja = $params['fecha_baja'] ;
			
			//el nombre de la baja corresponde con el estado
			$m_poliza->estado_id = $params['baja_id'];
			
			$m_poliza->save();
			$this->view->baja=true;
			//dar de baja
		}

		//$poliza = new Domain_Poliza($params['poliza_id']);
		//$poliza = $this->_services_poliza->refacturarPolizaAduaneros($poliza);
		//

		//$m_solicitud->save();

	}


	public function altaTransportesAction()
	{

		//$service_model = new Services_SimpleCrud();
		//$rows = $this->_services_simple_crud->getAll($this->_solicitud->getModel());
		//$this->view->rows = $rows;
	}

	public function viewAction()
	{

		$params = $this->_request->getParams();
		$row = $this->_services_simple_crud
		->getById($this->_solicitud->getModel(),array('primary_key'=>'solicitud_id','value'=>$params['id']));

		$this->view->row = $row;

	}

	public function viewPolizaAutomotoresAction()
	{
		/*
		 * Solo se modifican los siguientes campos:
		 * - Nº de poliza
		 * - Fecha Vigencia
		 * - Prima
		 * - Premio Compañia
		 * - Premio
		 * - Plus
		 */

		//La Poliza siempre tiene poliza_id



			
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;
		//echo "<pre>";
		//print_r($params);
		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);


		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;


		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);

		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);
		$this->view->tipo_cobertura_automotor = Domain_Helper::getHelperNameById('tipo_cobertura_automotores', $poliza_detalle->tipo_cobertura_id);
		$this->view->tipo_vehiculo_automotor = Domain_Helper::getHelperNameById('tipo_vehiculo_automotores', $poliza_detalle->tipo_vehiculo_id);
		$this->view->capacidad_automotor = Domain_Helper::getHelperNameById('capacidad_automotores', $poliza_detalle->capacidad_id);
		$this->view->pasajeros_automotor = Domain_Helper::getHelperNameById('pasajeros_automotores', $poliza_detalle->pasajeros_id);
		$this->view->flota_automotor = Domain_Helper::getHelperNameById('pasajeros_automotores', $poliza_detalle->flota_id);
		$this->view->estado_vehiculo_automotor = Domain_Helper::getHelperNameById('estado_vehiculo_automotores', $poliza_detalle->estado_vehiculo_id);
		$this->view->tipo_combustion_automotor = Domain_Helper::getHelperNameById('tipo_combustion_automotores',$poliza_detalle->tipo_combustion_id);
		$this->view->sistema_seguridad_automotor = Domain_Helper::getHelperNameById('sistema_seguridad_automotores',$poliza_detalle->sistema_seguridad_id);
		$this->view->tipo_vehiculo_automotor = Domain_Helper::getHelperNameById('tipo_vehiculo_automotores',$poliza_detalle->tipo_vehiculo_id);

		$this->view->estado_vehiculo_automotor = Domain_Helper::getHelperNameById('estado_vehiculo_automotores',$poliza_detalle->estado_vehiculo_id);
		$this->view->estado_luces_automotor = Domain_Helper::getHelperNameById('estado_vehiculo_automotores',$poliza_detalle->estado_luces_id);
		$this->view->estado_motor_automotor = Domain_Helper::getHelperNameById('estado_vehiculo_automotores',$poliza_detalle->estado_motor_id);
		$this->view->estado_carroceria_automotor = Domain_Helper::getHelperNameById('estado_vehiculo_automotores',$poliza_detalle->estado_carroceria_id);
		$this->view->estado_cubiertas_automotor = Domain_Helper::getHelperNameById('estado_vehiculo_automotores',$poliza_detalle->estado_cubiertas_id);
		$this->view->tipo_combustion_automotor = Domain_Helper::getHelperNameById('tipo_combustion_automotores',$poliza_detalle->tipo_combustion_id);
		$this->view->sistema_seguridad_automotor = Domain_Helper::getHelperNameById('sistema_seguridad_automotores',$poliza_detalle->sistema_seguridad_id);

			//Si se renovo que lo muestre
		if(!empty($poliza->poliza_poliza_id)){
			$this->view->renovada = true;
			$poliza_poliza = new Domain_Poliza($poliza->poliza_poliza_id);

			$this->view->poliza_renovada_numero_poliza = $poliza_poliza->getModelPoliza()->numero_poliza;
		}



		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->saveViewPolizaAutomotor($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();

		}
	}
	public function viewPolizaAduanerosAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ADUANEROS');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->despachante_aduana= Domain_DespachanteAduana::getNameById($d_poliza->getModelDetalle()->despachante_aduana_id);
		
		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);

		
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);
		$this->view->despachante_aduana = Domain_DespachanteAduana::getNameById($poliza_detalle->despachante_aduana_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $d_poliza->getModelDetallePago()->forma_pago_id);
		$this->view->forma_pago_id = $d_poliza->getModelDetallePago()->forma_pago_id;

		//Si se renovo que lo muestre
		if(!empty($poliza->poliza_poliza_id)){
			$this->view->renovada = true;
			$poliza_poliza = new Domain_Poliza($poliza->poliza_poliza_id);

			$this->view->poliza_renovada_numero_poliza = $poliza_poliza->getModelPoliza()->numero_poliza;
		}

		if($params['save']){
			//echo '<pre>';
		//print_r($params);
		//exit;
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->saveViewPolizaAduaneros($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();

		}
	}

	public function editPolizaFacturaAduanerosAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ADUANEROS');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);

		

		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
		//echo '<pre>';
		//print_r($params);
		//exit;
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaAduaneros($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}

		public function editPolizaFacturaConstruccionAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		$this->view->isAgente = false;
		

		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);

		

		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaConstruccion($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}


	public function editPolizaFacturaSeguroTecnicoAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('SEGURO_TECNICO');
		$this->view->isAgente = false;
		

		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);

		

		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaSeguroTecnico($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}


	public function editPolizaFacturaAlquilerAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ALQUILER');
		$this->view->isAgente = false;
		

		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);

		

		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaAlquiler($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}


	public function editPolizaFacturaIntegralComercioAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('INTEGRAL_COMERCIO');
		$this->view->isAgente = false;
		

		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);

		

		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaIntegralComercio($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}


		public function editPolizaFacturaResponsabilidadCivilAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('RESPONSABILIDAD_CIVIL');
		$this->view->isAgente = false;
		

		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaResponsabilidadCivil($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}



	public function editPolizaFacturaAccidentesPersonalesAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ACCIDENTES_PERSONALES');
		$this->view->isAgente = false;
		

		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);

		
		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaAccidentesPersonales($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}


	public function editPolizaFacturaIncendioAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('INCENDIO');
		$this->view->isAgente = false;
		

		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);

		

		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaIncendio($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}


	public function editPolizaFacturaIgjAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('IGJ');
		$this->view->isAgente = false;
		

		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);

		

		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaIgj($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}



	public function editPolizaFacturaJudicialesAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('IGJ');
		$this->view->isAgente = false;
		

		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);

		

		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaJudiciales($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}


	public function editPolizaFacturaVidaAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('VIDA');
		$this->view->isAgente = false;
		

		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);

		

		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaVida($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}


public function editPolizaFacturaTransporteMercaderiaAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('TRANSPORTE_MERCADERIA');
		$this->view->isAgente = false;
		

		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);

		

		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaTransporteMercaderia($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}


public function editPolizaFacturaAutomotoresAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('AUTOMOTORES');
		$this->view->isAgente = false;
		

		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;


		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($poliza->asegurado_id);

		

		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->editPolizaFacturaAutomotores($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			echo "Factura Modificada";	
		}

	}


	public function viewPolizaConstruccionAction()
	{
		//La Poliza siempre tiene poliza_id
        $tipo_poliza_id = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);

			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//echo '<pre>';
		//print_r($params);
		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');		
		
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		
		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $d_poliza->getModelDetallePago()->forma_pago_id);

		//Motivo de garantia son diferentes
		$this->view->tipo_garantia = $this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id,$tipo_poliza_id);
		//$this->view->despachante_aduana = Domain_DespachanteAduana::getNameById($poliza_detalle->despachante_aduana_id);

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->saveViewPolizaConstruccion($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();

		}


	}

	public function viewPolizaSeguroTecnicoAction()
	{
		//La Poliza siempre tiene poliza_id
        $tipo_poliza_id = Domain_TipoPoliza::getIdByName('SEGURO_TECNICO');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);

			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//echo '<pre>';
		//print_r($params);
		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');		
		
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		
		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);

		//Motivo de garantia son diferentes
		$this->view->tipo_garantia = $this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id,$tipo_poliza_id);
		//$this->view->despachante_aduana = Domain_DespachanteAduana::getNameById($poliza_detalle->despachante_aduana_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->saveViewPolizaSeguroTecnico($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();

		}


	}


	public function viewPolizaIgjAction()
		{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('IGJ');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

	
		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);
		
		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);

		//Motivo de garantia son diferentes
		$this->view->tipo_garantia = $this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id,$tipo_poliza_id);
		//$this->view->despachante_aduana = Domain_DespachanteAduana::getNameById($poliza_detalle->despachante_aduana_id);

//Si se renovo que lo muestre
		if(!empty($poliza->poliza_poliza_id)){
			$this->view->renovada = true;
			$poliza_poliza = new Domain_Poliza($poliza->poliza_poliza_id);

			$this->view->poliza_renovada_numero_poliza = $poliza_poliza->getModelPoliza()->numero_poliza;
		}
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->saveViewPolizaIgj($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();

		}


	}

	
	public function viewPolizaJudicialesAction()
		{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('JUDICIALES');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		
		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');		

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		//$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $d_poliza->getModelDetallePago()->forma_pago_id);
		
		//Motivo de garantia son diferentes
		$this->view->tipo_garantia = $this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id,$tipo_poliza_id);
		$this->view->beneficiario= Domain_Beneficiario::getNameById($d_poliza->getModelDetalle()->beneficiario_id);
		//Si se renovo que lo muestre
		if(!empty($poliza->poliza_poliza_id)){
			$this->view->renovada = true;
			$poliza_poliza = new Domain_Poliza($poliza->poliza_poliza_id);

			$this->view->poliza_renovada_numero_poliza = $poliza_poliza->getModelPoliza()->numero_poliza;
		}

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->saveViewPolizaJudiciales($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();

		}


	}

	/*
	 * Poliza Seguro de Vida
	 */

public function viewPolizaVidaAction()
		{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('VIDA');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//echo '<pre>';
		//print_r($params);
		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);

		//Motivo de garantia son diferentes
		$this->view->tipo_garantia = $this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id,$tipo_poliza_id);
		//$this->view->despachante_aduana = Domain_DespachanteAduana::getNameById($poliza_detalle->despachante_aduana_id);
//Si se renovo que lo muestre
		if(!empty($poliza->poliza_poliza_id)){
			$this->view->renovada = true;
			$poliza_poliza = new Domain_Poliza($poliza->poliza_poliza_id);

			$this->view->poliza_renovada_numero_poliza = $poliza_poliza->getModelPoliza()->numero_poliza;
		}

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->saveViewPolizaVida($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();

		}
	}
	
	
	public function viewPolizaResponsabilidadCivilAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('RESPONSABILIDAD_CIVIL');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

	
		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);

		//Motivo de garantia son diferentes
		$this->view->tipo_garantia = $this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id,$tipo_poliza_id);
		//$this->view->despachante_aduana = Domain_DespachanteAduana::getNameById($poliza_detalle->despachante_aduana_id);
//Si se renovo que lo muestre
		if(!empty($poliza->poliza_poliza_id)){
			$this->view->renovada = true;
			$poliza_poliza = new Domain_Poliza($poliza->poliza_poliza_id);

			$this->view->poliza_renovada_numero_poliza = $poliza_poliza->getModelPoliza()->numero_poliza;
		}

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, analizar que seria mejor
			$poliza = $this->_services_poliza->saveViewPolizaResponsabilidadCivil($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();

		}


	}


	public function viewPolizaTransporteMercaderiaAction()
	{
		
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('TRANSPORTE_MERCADERIA');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//echo '<pre>';
		//print_r($params);
		
		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza


		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);

		//Motivo de garantia son diferentes
		$this->view->tipo_garantia = $this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id,$tipo_poliza_id);
		//$this->view->despachante_aduana = Domain_DespachanteAduana::getNameById($poliza_detalle->despachante_aduana_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, analizar que seria mejor
			$poliza = $this->_services_poliza->saveViewPolizaTransporteMercaderia($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();

		}


	}
	
	
	
	
	public function viewPolizaAlquilerAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ALQUILER');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//echo '<pre>';
		//print_r($params);
		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);

		//Motivo de garantia son diferentes
		//$this->view->tipo_garantia = $this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		//$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaById($poliza_detalle->motivo_garantia_id);
		//$this->view->despachante_aduana = Domain_DespachanteAduana::getNameById($poliza_detalle->despachante_aduana_id);
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);
		
		//Si se renovo muestro los datos de la poliza
		if(!empty($poliza->poliza_poliza_id)){
		$this->view->renovada = true;
		$poliza_renovada = new Domain_Poliza($poliza->poliza_poliza_id);
		$this->view->poliza_renovada_numero_poliza = $poliza_renovada->getModelPoliza()->numero_poliza;
		echo " deberia mostrar el numero".$this->view->numero_poliza_renovada;
		}


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->saveViewPolizaAlquiler($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();

		}


	}

public function viewPolizaAccidentesPersonalesAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ACCIDENTES_PERSONALES');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
		
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');		
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);

//Si se renovo que lo muestre
		if(!empty($poliza->poliza_poliza_id)){
			$this->view->renovada = true;
			$poliza_poliza = new Domain_Poliza($poliza->poliza_poliza_id);

			$this->view->poliza_renovada_numero_poliza = $poliza_poliza->getModelPoliza()->numero_poliza;
		}

		if($params['save']){
			
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por cada tipo de Poliza
			$poliza = $this->_services_poliza->saveViewPolizaAccidentesPersonales($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();
		}
	}
	/**
	* Metodo ver la Póliza Integral de Comercio
	* @method viewPolizaIntegralComercioAction
	*/
public function viewPolizaIntegralComercioAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('INTEGRAL_COMERCIO');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();

		//Datos de la poliza
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);


		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);
		
		//Si se renovo muestro los datos de la poliza
		if(!empty($poliza->poliza_poliza_id)){
		$this->view->renovada = true;
		$poliza_renovada = new Domain_Poliza($poliza->poliza_poliza_id);
		$this->view->poliza_renovada_numero_poliza = $poliza_renovada->getModelPoliza()->numero_poliza;
		echo " deberia mostrar el numero".$this->view->numero_poliza_renovada;
		}


		if($params['save']){
			/**
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->saveViewPolizaIntegralComercio($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();

		}


	}

	/**
	* Metodo ver la Póliza Incendio
	* @method viewPolizaIncendioAction
	*/
public function viewPolizaIncendioAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('INCENDIO');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();

		//Datos de la poliza
		$this->view->compania= Domain_Compania::getNameById($poliza->compania_id);
		$this->view->productor= Domain_Productor::getNameById($poliza->productor_id);
		$this->view->agente= Domain_Agente::getNameById($poliza->agente_id);
		$this->view->cobrador= Domain_Cobrador::getNameById($poliza->cobrador_id);
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');
		$this->view->tipo_endoso_text = Domain_Helper::getHelperNameById('tipo_endoso',$poliza->tipo_endoso_id);

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);


		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);
		
		//Si se renovo muestro los datos de la poliza
		if(!empty($poliza->poliza_poliza_id)){
		$this->view->renovada = true;
		$poliza_renovada = new Domain_Poliza($poliza->poliza_poliza_id);
		$this->view->poliza_renovada_numero_poliza = $poliza_renovada->getModelPoliza()->numero_poliza;
		echo " deberia mostrar el numero".$this->view->numero_poliza_renovada;
		}


		if($params['save']){
			/**
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$poliza = $this->_services_poliza->saveViewPolizaIncendio($d_poliza,$params);
			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $d_poliza->getModelDetallePago();

		}


	}




	public function addAction()
	{
		$params = $this->_request->getParams();
		$values =  array_slice($params,$tipo_poliza_id); //saca la data de mas

		if( isset($params['add']) ){
			$this->_services_simple_crud->save($this->_solicitud->getModel(),$values);
		}

	}
	public function editAction()
	{
		$params = $this->_request->getParams();
		$values =  array_slice($params,3); //saca la data de mas
		$this->_services_simple_crud->save($this->_solicitud->getModel(),$values);
		$this->view->params = $params;


	}
	public function deleteAction()
	{

	}
	public function autocompleteAction(){

		echo "Hola";
	}
	/*enviar emails de solicitud de endoso*/

public  function enviarSolicitudEndosoCompaniaAduanerosAction(){
		
		$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();
		
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		

		$m_poliza = $d_poliza->getModelPoliza();
		$compania_id = $m_poliza->compania_id;
		//chequear si tiene asignada compania
		
		if(empty($compania_id)){
		echo "<font color='red'>Error: La poliza no tiene compania!</font>";
			return false;
		}
		
		$compania = new Domain_Compania($compania_id);
		$m_compania = $compania->getModel(); 
		$email = $m_compania->email;
		
		if(empty($email)){
		echo "<font color='red'>Error: La compania no tiene email!</font>";	
		return false;
		}
		
			$asegurado = new Domain_Asegurado($m_poliza->asegurado_id);
		$m_asegurado = $asegurado->getModel();
		
		$headers = "MIME-Version: 1.0\r\n";
	    $headers .= "Content-type: text/html; charset=ISO-8859-1\r\n";
	    $headers .= "From: SConsultora <solicitud@sconsultora.com.ar>\r\n";
	    $headers .= "Bcc:solicitud@sconsultora.com.ar\r\n";
	   //Si es solicitud de endoso
	    $subject = " Solicitud de Endoso de Poliza Nro.".$m_poliza->numero_poliza." de Cliente".$m_asegurado->nombre;
	   
	    $to = $email;
		
	
	    
	    $contenido = file_get_contents (APPLICATION_PATH.'/../plantillas/email_sconsultora_aduaneros.html' );
		
	    $template=$contenido;
		
		$dia = date('w');
		$meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$mes =$meses[date('n')];
		$año = date('Y');

		/*Cargo variables*/
		
	
		
		
		$productor = new Domain_Productor($m_poliza->productor_id);
		$m_productor = $productor->getModel();
		$tipo_seguro = Domain_TipoPoliza::getNameById($m_poliza->tipo_poliza_id);
		$m_detalle_poliza = $d_poliza->getModelDetalle();
		$m_detalle_poliza_valores = $d_poliza->getModelPolizaValores();
		// echo "<pre>";
		//print_r($m_detalle_poliza_valores);
		//	exit;
		
		$motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($m_detalle_poliza->motivo_garantia_id, $m_poliza->tipo_poliza_id);
		 
		$codigo_productor = Model_CodigoProductorCompania::getCodigoProductorByCompaniaId($m_poliza->productor_id, $m_poliza->compania_id);
		
		$beneficiario = new Domain_Beneficiario($m_detalle_poliza->beneficiario_id);
		$m_beneficiario = $beneficiario->getModel();
		
		$d_despachante = new Domain_DespachanteAduana($m_detalle_poliza->despachante_aduana_id);
		$m_despachante = $d_despachante->getModel();
		$tipo_endoso = Domain_Helper::getHelperNameById('tipo_endoso',$m_poliza->tipo_endoso_id);
		$tipo_movimiento = 'Solicitud de endoso '.$tipo_endoso;
		
		$moneda = '$';	
		if($m_detalle_poliza_valores->moneda_id == 1){
		$moneda = '$';	
		}elseif($m_detalle_poliza_valores->moneda_id == 2){
		$moneda = 'USD';		
		}elseif($m_detalle_poliza_valores->moneda_id == 3){
		$moneda = 'EURO';		
		};
		
		//echo "<pre>";
		//print_r($despachante);
		//exit;
		$variables = array(
			'{numero_solicitud}'=>(empty($m_poliza->numero_solicitud))?'':$m_poliza->numero_solicitud,
			'{compania}'=>(empty($m_poliza->compania_id))?'':$m_compania->nombre,
			'{cliente}'=>(empty($m_poliza->asegurado_id))?'':$m_asegurado->nombre,
			'{domicilio_cliente}'=>(empty($m_asegurado->domicilio))?'':$m_asegurado->domicilio,
			'{cuit_cliente}'=>(empty($m_asegurado->cuit))?'':$m_asegurado->cuit,
			'{iva_cliente}'=>(empty($m_asegurado->iva))?'':$m_asegurado->iva	,
			'{productor}'=>(empty($m_productor->nombre))?'':$m_productor->nombre,	
			'{codigo_productor}'=>(empty($codigo_productor))?'':$codigo_productor,
			'{tipo_seguro}'=>(empty($tipo_seguro))?'':$tipo_seguro,
			'{tipo_movimiento}'=>(empty($tipo_movimiento))?'':$tipo_movimiento,
			'{vigencia_desde} '=>(empty($m_poliza->fecha_vigencia))?'':$m_poliza->fecha_vigencia,
			'{vigencia_hasta}'=>(empty($m_poliza->fecha_vigencia_hasta))?'':$m_poliza->fecha_vigencia_hasta,
			'{asegurado}'=>(empty($m_poliza->asegurado_id))?'':$m_asegurado->nombre,
			'{motivo_garantia}'=>(empty($motivo_garantia))?'':$motivo_garantia,
			'{suma_asegurada}'=>(empty($m_detalle_poliza_valores->monto_asegurado))?'':$m_detalle_poliza_valores->monto_asegurado,
			'{mercaderia}'=>(empty($m_detalle_poliza->mercaderia))?'':$m_detalle_poliza->mercaderia,
			'{despachante}'=>(empty($m_despachante->nombre))?'':$m_despachante->nombre,
			'{cuit_despachante}'=>(empty($m_despachante->cuit))?'':$m_despachante->cuit,
			'{beneficiario}'=>(empty($m_beneficiario->nombre))?'':$m_beneficiario->nombre,
			'{domicilio_riesgo}'=>(empty($m_detalle_poliza->domicilio_riesgo))?'':$m_detalle_poliza->domicilio_riesgo,
			'{localidad_riesgo}'=>(empty($m_detalle_poliza->localidad_riesgo))?'':$m_detalle_poliza->localidad_riesgo,
			'{provincia_riesgo} '=>(empty($m_detalle_poliza->provincia_riesgo))?'':$m_detalle_poliza->provincia_riesgo,
		    '{observaciones_compania} '=>(empty($m_poliza->observaciones_compania))?'':$m_poliza->observaciones_compania,
			'{factura}'=>(empty($m_detalle_poliza->factura))?'':$m_detalle_poliza->factura,
			'{bl}'=>(empty($m_detalle_poliza->bl))?'':$m_detalle_poliza->bl,
			'{adicional}'=>(empty($m_detalle_poliza->descripcion_adicional))?'':$m_detalle_poliza->descripcion_adicional,
			'{prima}'=>(empty($m_detalle_poliza_valores->prima_comision))?'':$m_detalle_poliza_valores->prima_comision,
			'{moneda}'=>(empty($moneda))?'':$moneda,
			'{localidad_beneficiario}'=>(empty($m_beneficiario->localidad))?'':$m_beneficiario->localidad,
			'{provincia_beneficiario}'=>(empty($m_beneficiario->provincia))?'':$m_beneficiario->provincia,
			'{localidad_cliente}'=>(empty($m_asegurado->localidad))?'':$m_asegurado->localidad,
			'{provincia_cliente}'=>(empty($m_asegurado->provincia))?'':$m_asegurado->provincia
			);

			/*Fin Cargo Variables*/
			
		$contenido= str_replace(array_keys($variables),array_values($variables),$template);


		//echo $contenido;
	    	   
	    
		$res = mail("$to", "$subject",$contenido, $headers);
		
		if(!$res){
		echo "<font color='red'>Ocurrio un error al tratar de enviar el email</font>";
		}else{

			echo "<font color='blue'>El email ha sido enviado correctamente!</font>";
		}
		
				
		return true;
	}

public function enviarSolicitudEndosoCompaniaConstruccionAction(){
		
		$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();

		$poliza = new Domain_Poliza($params['poliza_id']);
		$m_poliza = $poliza->getModelPoliza();
		$compania_id = $m_poliza->compania_id;
		//chequear si tiene asignada compania
		
		if(empty($compania_id)){
		echo "<font color='red'>Error: La poliza no tiene compania!</font>";
			return false;
		}
		
		$compania = new Domain_Compania($compania_id);
		$m_compania = $compania->getModel(); 
		$email = $m_compania->email;
		
		if(empty($email)){
		echo "<font color='red'>Error: La compania no tiene email!</font>";	
		return false;
		}
		$asegurado = new Domain_Asegurado($m_poliza->asegurado_id);
		$m_asegurado = $asegurado->getModel();

		$headers = "MIME-Version: 1.0\r\n";
	    $headers .= "Content-type: text/html; charset=ISO-8859-1\r\n";
	    $headers .= "From: SConsultora <solicitud@sconsultora.com.ar>\r\n";
	    $headers .= "Bcc:solicitud@sconsultora.com.ar\r\n";
	      $subject = " Solicitud de Endoso de Poliza Nro.".$m_poliza->numero_poliza." de Cliente".$m_asegurado->nombre;
	    $to = $email;
	
	    
	    $contenido = file_get_contents (APPLICATION_PATH.'/../plantillas/email_sconsultora_construccion.html' );
		
	    $template=$contenido;
		
		$dia = date('w');
		$meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
		$mes =$meses[date('n')];
		$año = date('Y');

		/*Cargo variables*/
		
	
		
		$productor = new Domain_Productor($m_poliza->productor_id);
		$m_productor = $productor->getModel();
		$tipo_seguro = Domain_TipoPoliza::getNameById($m_poliza->tipo_poliza_id);
		$m_detalle_poliza = $poliza->getModelDetalle();
		$m_detalle_poliza_valores = $poliza->getModelPolizaValores();

		$motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($m_detalle_poliza->motivo_garantia_id, $m_poliza->tipo_poliza_id);
		
		$codigo_productor = Model_CodigoProductorCompania::getCodigoProductorByCompaniaId($m_poliza->productor_id, $m_poliza->compania_id);
		
		$beneficiario = new Domain_Beneficiario($m_detalle_poliza->beneficiario_id);
		$m_beneficiario = $beneficiario->getModel();
		$tipo_movimiento = 'Emisi&oacute;n';
		
		$moneda = '$';	
		if($m_detalle_poliza_valores->moneda_id == 1){
		$moneda = '$';	
		}elseif($m_detalle_poliza_valores->moneda_id == 2){
		$moneda = 'USD';		
		}elseif($m_detalle_poliza_valores->moneda_id == 3){
		$moneda = 'EURO';		
		};
		
		$variables = array(
			'{numero_solicitud}'=>(empty($m_poliza->numero_solicitud))?'':$m_poliza->numero_solicitud,
			'{compania}'=>(empty($m_poliza->compania_id))?'':$m_compania->nombre,
			'{cliente}'=>(empty($m_poliza->asegurado_id))?'':$m_asegurado->nombre,
			'{domicilio_cliente}'=>(empty($m_asegurado->domicilio))?'':$m_asegurado->domicilio,
			'{localidad_cliente}'=>(empty($m_asegurado->localidad))?'':$m_asegurado->localidad,
			'{provincia_cliente}'=>(empty($m_asegurado->provincia))?'':$m_asegurado->provincia,
			'{cuit_cliente}'=>(empty($m_asegurado->cuit))?'':$m_asegurado->cuit,
			'{iva_cliente}'=>(empty($m_asegurado->iva))?'':$m_asegurado->iva	,
			'{productor}'=>(empty($m_poliza->productor_id))?'':$m_productor->nombre,	
			'{codigo_productor}'=>(empty($codigo_productor))?'':$codigo_productor,
			'{tipo_seguro}'=>(empty($tipo_seguro))?'':$tipo_seguro,
			'{tipo_movimiento}'=>(empty($tipo_movimiento))?'':$tipo_movimiento,
			'{vigencia_desde} '=>(empty($m_poliza->fecha_vigencia))?'':$m_poliza->fecha_vigencia,
			'{vigencia_hasta}'=>(empty($m_poliza->fecha_vigencia_hasta))?'':$m_poliza->fecha_vigencia_hasta,
			'{asegurado}'=>(empty($m_poliza->asegurado_id))?'':$m_asegurado->nombre,
			'{motivo_garantia}'=>(empty($motivo_garantia))?'':$motivo_garantia,
			'{suma_asegurada}'=>(empty($m_detalle_poliza_valores->monto_asegurado))?'':$m_detalle_poliza_valores->monto_asegurado,
			'{beneficiario}'=>(empty($m_beneficiario->nombre))?'':$m_beneficiario->nombre,
			'{domicilio_beneficiario}'=>(empty($m_beneficiario->domicilio))?'':$m_beneficiario->domicilio,
			'{localidad_beneficiario}'=>(empty($m_beneficiario->localidad))?'':$m_beneficiario->localidad,
			'{provincia_beneficiario}'=>(empty($m_beneficiario->provincia))?'':$m_beneficiario->provincia,
			'{cuit_beneficiario}'=>(empty($m_beneficiario->cuit))?'':$m_beneficiario->cuit,
			'{licitacion}'=>(empty($m_detalle_poliza->numero_licitacion))?'':$m_detalle_poliza->numero_licitacion,
			'{obra}'=>(empty($m_detalle_poliza->obra))?'':$m_detalle_poliza->obra,
			'{expediente}'=>(empty($m_detalle_poliza->expediente))?'':$m_detalle_poliza->expediente,
			'{apertura}'=>(empty($m_detalle_poliza->apertura_licitacion))?'':$m_detalle_poliza->apertura_licitacion,
			'{clausula}'=>(empty($m_detalle_poliza->clausula_especial))?'':$m_detalle_poliza->clausula_especial,
			'{certificaciones}'=>(empty($m_detalle_poliza->certificaciones))?'':$m_detalle_poliza->certificaciones,
			'{adicional}'=>(empty($m_detalle_poliza->descripcion_adicional))?'':$m_detalle_poliza->descripcion_adicional,
			'{observaciones_compania}'=>(empty($m_poliza->observaciones_compania))?'':$m_poliza->observaciones_compania,
			'{prima}'=>(empty($m_detalle_poliza_valores->prima_comision))?'':$m_detalle_poliza_valores->prima_comision,
			'{objeto}'=>(empty($m_detalle_poliza->objeto))?'':$m_detalle_poliza->objeto,
			'{moneda}'=>(empty($moneda))?'':$moneda
			);

			/*Fin Cargo Variables*/
			
	$contenido= str_replace(array_keys($variables),array_values($variables),$template);


		//echo $contenido;
	    	   
	    
		$res = mail("$to", "$subject",$contenido, $headers);
		
		if(!$res){
		echo "<font color='red'>Ocurrio un error al tratar de enviar el email</font>";
		}else{

			echo "<font color='blue'>El email ha sido enviado correctamente!</font>";
		}
		
				
		return true;
	}
	


	/************** metodos para el super admin **************/
	public function editPolizaAduanerosAction()
	{
		
		//$this->_usuario = $this->_sesion->getUsuario();
		/*echo "<pre>";
		print_r($this->_usuario);*/
		 
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ADUANEROS');
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//Estados de la poliza

		$estado_poliza = new Domain_EstadoPoliza();
		$this->view->poliza_estados = $estado_poliza->getEstados();

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		//este seria ejecutivo de cuentas, Lo unifico con agente
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$despachante_aduana = new Domain_DespachanteAduana();
		$this->view->despachante_aduanas= $despachante_aduana->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;

		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);

		
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);
		$this->view->despachante_aduana = Domain_DespachanteAduana::getNameById($poliza_detalle->despachante_aduana_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */

			
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$d_poliza = $this->_services_poliza->saveEditPolizaAduaneros($d_poliza,$params);
			//$d_poliza = $this->_services_poliza->saveDetallePago($d_poliza,$params);
			

			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			
			

			//Datos del seguro - detalle - valores
			$poliza_valores = $d_poliza->getModelPolizaValores();
			$poliza_detalle = $d_poliza->getModelDetalle();
			$detalle_pago = $d_poliza->getModelDetallePago();

			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $detalle_pago;
			$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;

			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);

		}


	}
	
	/************** metodos para el super admin **************/
	public function editPolizaAccidentesPersonalesAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ACCIDENTES_PERSONALES');
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//Estados de la poliza

		$estado_poliza = new Domain_EstadoPoliza();
		$this->view->poliza_estados = $estado_poliza->getEstados();

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		//este seria ejecutivo de cuentas, Lo unifico con agente
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;
		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
//echo "<pre>";
//print_r($params);
			
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$d_poliza = $this->_services_poliza->saveEditPolizaAccidentesPersonales($d_poliza,$params);
			//$d_poliza = $this->_services_poliza->saveDetallePago($d_poliza,$params);
			

			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			
			//Datos del seguro - detalle - valores
			$poliza_valores = $d_poliza->getModelPolizaValores();
			$poliza_detalle = $d_poliza->getModelDetalle();
			$detalle_pago = $d_poliza->getModelDetallePago();

			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $detalle_pago;
			$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);

		}
	}

public function editPolizaIgjAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('IGJ');
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//Estados de la poliza

		$estado_poliza = new Domain_EstadoPoliza();
		$this->view->poliza_estados = $estado_poliza->getEstados();

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		//este seria ejecutivo de cuentas, Lo unifico con agente
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;
		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */

			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$d_poliza = $this->_services_poliza->saveEditPolizaIgj($d_poliza,$params);
			//$d_poliza = $this->_services_poliza->saveDetallePago($d_poliza,$params);
			

			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			
			//Datos del seguro - detalle - valores
			$poliza_valores = $d_poliza->getModelPolizaValores();
			$poliza_detalle = $d_poliza->getModelDetalle();
			$detalle_pago = $d_poliza->getModelDetallePago();

			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $detalle_pago;
			$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);

		}
	}
	public function editPolizaJudicialesAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('JUDICIALES');
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//Estados de la poliza

		$estado_poliza = new Domain_EstadoPoliza();
		$this->view->poliza_estados = $estado_poliza->getEstados();

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		//este seria ejecutivo de cuentas, Lo unifico con agente
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;
		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */

			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$d_poliza = $this->_services_poliza->saveEditPolizaJudiciales($d_poliza,$params);
			//$d_poliza = $this->_services_poliza->saveDetallePago($d_poliza,$params);
			

			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			
			//Datos del seguro - detalle - valores
			$poliza_valores = $d_poliza->getModelPolizaValores();
			$poliza_detalle = $d_poliza->getModelDetalle();
			$detalle_pago = $d_poliza->getModelDetallePago();

			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $detalle_pago;
			$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);

		}
	}

public function editPolizaVidaAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('VIDA');
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//Estados de la poliza

		$estado_poliza = new Domain_EstadoPoliza();
		$this->view->poliza_estados = $estado_poliza->getEstados();

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		//este seria ejecutivo de cuentas, Lo unifico con agente
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;
		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */

			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$d_poliza = $this->_services_poliza->saveEditPolizaVida($d_poliza,$params);
			//$d_poliza = $this->_services_poliza->saveDetallePago($d_poliza,$params);
			

			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			
			//Datos del seguro - detalle - valores
			$poliza_valores = $d_poliza->getModelPolizaValores();
			$poliza_detalle = $d_poliza->getModelDetalle();
			$detalle_pago = $d_poliza->getModelDetallePago();

			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $detalle_pago;
			$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);

		}
	}

public function editPolizaTransporteMercaderiaAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('TRANSPORTE_MERCADERIA');
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//Estados de la poliza

		$estado_poliza = new Domain_EstadoPoliza();
		$this->view->poliza_estados = $estado_poliza->getEstados();

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		//este seria ejecutivo de cuentas, Lo unifico con agente
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;
		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */

			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$d_poliza = $this->_services_poliza->saveEditPolizaTransporteMercaderia($d_poliza,$params);
			//$d_poliza = $this->_services_poliza->saveDetallePago($d_poliza,$params);
			

			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			
			//Datos del seguro - detalle - valores
			$poliza_valores = $d_poliza->getModelPolizaValores();
			$poliza_detalle = $d_poliza->getModelDetalle();
			$detalle_pago = $d_poliza->getModelDetallePago();

			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $detalle_pago;
			$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);

		}
	}
public function editPolizaIntegralComercioAction()
	{
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('INTEGRAL_COMERCIO');
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//Estados de la poliza

		$estado_poliza = new Domain_EstadoPoliza();
		$this->view->poliza_estados = $estado_poliza->getEstados();

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		//este seria ejecutivo de cuentas, Lo unifico con agente
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;
		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */

			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$d_poliza = $this->_services_poliza->saveEditPolizaIntegralComercio($d_poliza,$params);
			//$d_poliza = $this->_services_poliza->saveDetallePago($d_poliza,$params);
			

			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			
			//Datos del seguro - detalle - valores
			$poliza_valores = $d_poliza->getModelPolizaValores();
			$poliza_detalle = $d_poliza->getModelDetalle();
			$detalle_pago = $d_poliza->getModelDetallePago();

			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $detalle_pago;
			$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);

		}
	}


	public function editPolizaAlquilerAction()
	{
 
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ALQUILER');
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//Estados de la poliza

		$estado_poliza = new Domain_EstadoPoliza();
		$this->view->poliza_estados = $estado_poliza->getEstados();

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		//este seria ejecutivo de cuentas, Lo unifico con agente
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;

		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);

		
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */

			
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$d_poliza = $this->_services_poliza->saveEditPolizaAlquiler($d_poliza,$params);
			//$d_poliza = $this->_services_poliza->saveDetallePago($d_poliza,$params);
			

			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			
			

			//Datos del seguro - detalle - valores
			$poliza_valores = $d_poliza->getModelPolizaValores();
			$poliza_detalle = $d_poliza->getModelDetalle();
			$detalle_pago = $d_poliza->getModelDetallePago();

			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $detalle_pago;
			$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;

			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);

		}


	}


	/********* **************/
	public function editPolizaConstruccionAction(){
		
		//$this->_usuario = $this->_sesion->getUsuario();
		/*echo "<pre>";
		print_r($this->_usuario);*/
		 
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		
		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;

		//Estados de la poliza

		$estado_poliza = new Domain_EstadoPoliza();
		$this->view->poliza_estados = $estado_poliza->getEstados();

		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		//este seria ejecutivo de cuentas, Lo unifico con agente
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$despachante_aduana = new Domain_DespachanteAduana();
		$this->view->despachante_aduanas= $despachante_aduana->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;

		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);

		
		$this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id, $tipo_poliza_id);
	
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */

			
			//Hago un save distinto por ahora, para salir del paso, estoy cansado
			$d_poliza = $this->_services_poliza->saveEditPolizaConstruccion($d_poliza,$params);
			//$d_poliza = $this->_services_poliza->saveDetallePago($d_poliza,$params);
			

			$this->view->poliza = $d_poliza->getModelPoliza();
			$this->view->poliza_valores = $d_poliza->getModelPolizaValores();
			
			

			//Datos del seguro - detalle - valores
			$poliza_valores = $d_poliza->getModelPolizaValores();
			$poliza_detalle = $d_poliza->getModelDetalle();
			$detalle_pago = $d_poliza->getModelDetallePago();

			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $detalle_pago;
			$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;

			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);

		}
	}
	function editPolizaResponsabilidadCivilAction(){
		
		//La Poliza siempre tiene poliza_id
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('RESPONSABILIDAD_CIVIL');
		$this->view->isAgente = false;
		if($this->_t_usuario->getNombre()=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}


		//1. Traigo el POST
		$params = $this->_request->getParams();
		$poliza = $this->_poliza;
		
		//Estados de la poliza
		$estado_poliza = new Domain_EstadoPoliza();
		$this->view->poliza_estados = $estado_poliza->getEstados();
		
		//3.Traigo la poliza
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$poliza = $d_poliza->getModelPoliza();
		//Datos de la poliza

		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		//este seria ejecutivo de cuentas, Lo unifico con agente
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$despachante_aduana = new Domain_DespachanteAduana();
		$this->view->despachante_aduanas= $despachante_aduana->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();

		//Datos del seguro - detalle - valores
		$poliza_valores = $d_poliza->getModelPolizaValores();
		$poliza_detalle = $d_poliza->getModelDetalle();
		$detalle_pago = $d_poliza->getModelDetallePago();


		$this->view->poliza = $poliza;
		$this->view->poliza_valores = $poliza_valores;
		$this->view->poliza_detalle = $poliza_detalle;

		$this->view->detalle_pago = $detalle_pago;
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->documentacion = Domain_Helper::getHelperByDominio('documentacion');

		//2.Traigo los datos de las tablas asociadas
		$this->view->moneda = Domain_Helper::getHelperNameById('moneda', $poliza_valores->moneda_id);
		$this->view->periodo = Domain_Helper::getHelperNameById('periodo', $poliza->periodo_id);
		$this->view->forma_pago = Domain_Helper::getHelperNameById('forma_pago', $poliza->forma_pago_id);

		//Motivo de garantia son diferentes
		$this->view->tipo_garantia = $this->view->tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($poliza_detalle->tipo_garantia_id, $tipo_poliza_id);
		$this->view->motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($poliza_detalle->motivo_garantia_id,$tipo_poliza_id);
		//$this->view->despachante_aduana = Domain_DespachanteAduana::getNameById($poliza_detalle->despachante_aduana_id);
		if(!empty($poliza->poliza_poliza_id)){
			$this->view->renovada = true;
			$poliza_poliza = new Domain_Poliza($poliza->poliza_poliza_id);

			$this->view->poliza_renovada_numero_poliza = $poliza_poliza->getModelPoliza()->numero_poliza;
		}

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */


			$d_poliza = $this->_services_poliza->saveEditPolizaResponsabilidadCivil($d_poliza,$params);
			//$d_poliza = $this->_services_poliza->saveDetallePago($d_poliza,$params);


			//Datos del seguro - detalle - valores
			$poliza_valores = $d_poliza->getModelPolizaValores();
			$poliza_detalle = $d_poliza->getModelDetalle();
			$detalle_pago = $d_poliza->getModelDetallePago();

			$this->view->poliza_detalle = $d_poliza->getModelDetalle();
			$this->view->detalle_pago = $detalle_pago;
			$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->asegurado = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($d_poliza->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;

			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($d_poliza->getModelPoliza()->asegurado_id);
		}


	}
	

	/** Endoso de refacturacion **/

	public function traeDataPolizaAction(){
		
		$this->_helper->viewRenderer->setNoRender();

		$params = $this->_request->getParams();
		//Treae poliza
		
		$d_poliza = new Domain_Poliza($params['poliza_id']);
		$m_poliza = $d_poliza->getModelPoliza();
		$data = $m_poliza->toArray();

		echo  json_encode($data);
	}




}
