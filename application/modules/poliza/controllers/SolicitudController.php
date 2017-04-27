<?php
require_once ('IndexController.php');

class Poliza_SolicitudController extends Poliza_IndexController
{
	private $_solicitud = null;
	private $_sesion = null;
	private $_usuario = null;
	private $_services_solicitud = null;
	private $_t_usuario = null;


	public function init()
	{

		if ( ! (Zend_Auth::getInstance()->hasIdentity())  ) {

			$this->_helper->redirector('index','login','default');
		}

		$this->_helper->layout->disableLayout();
		$this->_solicitud = new Domain_Poliza();
		$this->_services_simple_crud = new Services_SimpleCrud();
		$this->_services_solicitud = new Services_Solicitud();
		$this->_sesion = Domain_Sesion::getInstance();
		$this->_usuario = $this->_sesion->getUsuario();
		$this->_t_usuario = $this->_usuario->getTipoUsuario();

		//le agrego aca los permisos, despues deberia ver si lo hago con el ACL q
		// Pero primero tengo que arreglarlo
		$operador_id = Domain_Helper::getHelperIdByDominioAndName('entidad','operador');
		
		$this->view->operador = ( $operador_id==$this->_usuario->getModel()->tipo_usuario_id )? true : false;
		
		
		if( $this->_usuario->getAcl()->hasPermission('solicitud') ) {

			//exit('tiene permiso');
		} else {
			//exit('no tiene permiso');
		}
			

		$logger = Zend_Registry::get('logger');
		$logger->log($this->_usuario->getEntidad() , Zend_Log::INFO);
			
	}

	public function indexAction()
	{
		$this->_forward('list','solicitud','poliza');

	}

	public function listAction()
	{
		$solicitud = new Domain_Solicitud();

		$params = $this->_request->getParams();

		/*
		 if(isset($params['buscar'])){
			$rows =$this->_t_usuario->findSolicitudByNumero($params['numero']);
			$this->view->buscar = true;
			}else{
			$rows = $this->_t_usuario->getSolicitudes();
			}
			*/
		 
		$rows =$this->_t_usuario->findSolicitudByNumero($params['criterio']);

		$page=$this->_getParam('page',1);
			
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];

		$this->view->rows = $paginator;
	}
	public function altaAction()
	{

		//$service_model = new Services_SimpleCrud();
		//$rows = $this->_services_simple_crud->getAll($this->_solicitud->getModel());
		//$this->view->rows = $rows;
	}
	public function altaAutomotoresAction()
	{



		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->capacidad_automotores = Domain_Helper::getHelperByDominio('capacidad_automotores');
		$this->view->tipo_cobertura_automotores = Domain_Helper::getHelperByDominio('tipo_cobertura_automotores');
		$this->view->pasajeros_automotores = Domain_Helper::getHelperByDominio('pasajeros_automotores');
		$this->view->estado_vehiculo_automotores = Domain_Helper::getHelperByDominio('estado_vehiculo_automotores');
		$this->view->tipo_combustion_automotores = Domain_Helper::getHelperByDominio('tipo_combustion_automotores');
		$this->view->sistema_seguridad_automotores = Domain_Helper::getHelperByDominio('sistema_seguridad_automotores');
		$this->view->tipo_vehiculo_automotores = Domain_Helper::getHelperByDominio('tipo_vehiculo_automotores');

		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();

		//Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}

		//2. Traigo el POST
		$params = $this->_request->getParams();
		//echo "<pre>";
		////print_r($params);
		//Si viene con ID es para guardar y traigo la solicitud con los datos
		if(! empty($params['solicitud_id']) ){
			$solicitud = new Domain_Poliza($params['solicitud_id']);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();
			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);

		}else{
			//Nueva solicitud
			$solicitud = $this->_solicitud;
		}

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */

			$solicitud = $this->_services_solicitud->saveSolicitudAutomotor($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);


			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
		}

	}

	public function altaSolicitudAction()
	{

		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->capacidad_automotores = Domain_Helper::getHelperByDominio('capacidad_automotores');
		$this->view->pasajeros_automotores = Domain_Helper::getHelperByDominio('pasajeros_automotores');
		$this->view->estado_vehiculo_automotores = Domain_Helper::getHelperByDominio('estado_vehiculo_automotores');
		$this->view->tipo_combustion_automotores = Domain_Helper::getHelperByDominio('tipo_combustion_automotores');
		$this->view->sistema_seguridad_automotores = Domain_Helper::getHelperByDominio('sistema_seguridad_automotores');
		$this->view->tipo_vehiculo_automotores = Domain_Helper::getHelperByDominio('tipo_vehiculo_automotores');

		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		//$agente = new Domain_Agente();
		//$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();

		//2. Traigo el POST
		$params = $this->_request->getParams();
		//echo "<pre>";
		////print_r($params);
		//exit;
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		//$this->view->poliza_detalle = $solicitud->getModelDetalleAutomotor();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		//$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);



		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudAccidentesPersonales($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);


			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalleAutomotor();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
		}

	}

	public function altaAduanerosAction()
	{
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ADUANEROS');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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


	//Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}
		

		//2. Traigo el POST
		$params = $this->_request->getParams();

		//echo "<pre>";
		////print_r($params);
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudAduaneros($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		}

	}



/**
*/

public function altaSeguroTecnicoAction()
	{
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('SEGURO_TECNICO');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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


	//Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}

		//2. Traigo el POST
		$params = $this->_request->getParams();

		echo "<pre>";
		//print_r($params);
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudSeguroTecnico($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		}

	}



	public function altaConstruccionAction()
	{
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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


	//Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}

		//2. Traigo el POST
		$params = $this->_request->getParams();

		echo "<pre>";
		//print_r($params);
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudConstruccion($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		}

	}

	/*
	 * Alta IGJ
	 */
public function altaIgjAction()
	{
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('IGJ');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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


	//Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}

		//2. Traigo el POST
		$params = $this->_request->getParams();

		echo "<pre>";
		//print_r($params);
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudIgj($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		}

	}
/*
 * Alta Judiciales
 */
	
public function altaJudicialesAction()
	{
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('JUDICIALES');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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


	//Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}

		//2. Traigo el POST
		$params = $this->_request->getParams();

		echo "<pre>";
		//print_r($params);
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudJudiciales($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		}

	}
	
/*
 * Alta Seguro de Vida
 */
	
public function altaVidaAction()
	{
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('VIDA');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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


	//Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}

		//2. Traigo el POST
		$params = $this->_request->getParams();

		echo "<pre>";
		//print_r($params);
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudVida($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		}

	}
	

public function altaAlquilerAction()
	{
$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ALQUILER');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		
		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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
		//$this->view->despachante_aduanas= $despachante_aduana->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();


	//Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}

		//2. Traigo el POST
		$params = $this->_request->getParams();

		echo "<pre>";
		//print_r($params);
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudAlquiler($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_Helper::getHelperByDominio('tipo_garantia');
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantias();

		}

	}
	
	public function altaResponsabilidadCivilAction()
	{
		
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('RESPONSABILIDAD_CIVIL');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		////print_r($tipo_poliza_id);
		////print_r($this->view->motivo_garantias);
		//exit;
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


	//Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}
		

		//2. Traigo el POST
		$params = $this->_request->getParams();

		echo "<pre>";
		//print_r($params);
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudResponsabilidadCivil($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		}

	}
	
	
public function altaTransporteMercaderiaAction()
	{
		
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('TRANSPORTE_MERCADERIA');
			
		
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		////print_r($tipo_poliza_id);
		////print_r($this->view->motivo_garantias);
		//exit;
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


	//Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}
		

		//2. Traigo el POST
		$params = $this->_request->getParams();

		echo "<pre>";
		//print_r($params);
		$solicitud = $this->_solicitud;
		
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudTransporteMercaderia($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		}

	}
	
	
	
	
	public function altaAccidentesPersonalesAction()
	{

		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ACCIDENTES_PERSONALES');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();
		
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
	   //Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			//print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}
		
		//2. Traigo el POST
		$params = $this->_request->getParams();
		//echo "<pre>";
		//print_r($params);
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

	    $this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudAccidentesPersonales($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

		}

	}


/**
* Alta de tipo de Seguro integral de comercio
* @method altaIntegralComercioAction 
*/
public function altaIntegralComercioAction()
	{
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('INTEGRAL_COMERCIO');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		
		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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
		//$this->view->despachante_aduanas= $despachante_aduana->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();


	     //Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}

		//2. Traigo el POST
		$params = $this->_request->getParams();

		//echo "<pre>";
		//print_r($params);
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudIntegralComercio($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_Helper::getHelperByDominio('tipo_garantia');
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantias();

		}

	}

/**
* Alta de tipo de Seguro integral de comercio
* @method altaIntegralComercioAction 
*/
public function altaIncendioAction()
	{
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('INCENDIO');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		
		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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
		//$this->view->despachante_aduanas= $despachante_aduana->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();


	     //Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}

		//2. Traigo el POST
		$params = $this->_request->getParams();

		//echo "<pre>";
		//print_r($params);
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['solicitud_id']) )$solicitud = new Domain_Poliza($params['solicitud_id']);

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);


		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
			$solicitud = $this->_services_solicitud->saveSolicitudIncendio($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_Helper::getHelperByDominio('tipo_garantia');
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantias();

		}

	}




	public function aprobarSolicitudAction(){
		$estado = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_CONFIRMADA');
		$this->_helper->viewRenderer->setNoRender();
		$params = $this->_request->getParams();
		
		$solicitud = new Domain_Solicitud($params['solicitud_id']);

		$m_solicitud = $solicitud->getModelPoliza();
		$m_solicitud->estado_id=$estado;

		try {
			$m_solicitud->save();
		}catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
		echo "<br>Solicitud aprobada con exito</br>";
		return true;
	}

	public function confirmarSolicitudAction(){
		$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion','EMISION');
		$this->_helper->viewRenderer->setNoRender();
		$params = $this->_request->getParams();
		$solicitud = new Domain_Solicitud($params['solicitud_id']);
		$m_solicitud = $solicitud->getModelPoliza();
		$m_solicitud->operacion_id=$operacion_id;
		$m_solicitud->estado_id=$estado;

		try {
			$m_solicitud->save();
		}catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
		$nro_solicitud = $m_solicitud->numero_solicitud;
		echo "<br>Poliza Creada con Exito, Solicitud Nro:$nro_solicitud";

		return true;
	}

	public function confirmarSolicitudCaucionAction(){

		$estado_aceptada = Domain_EstadoPoliza::getIdByCodigo('ACEPTADA');
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion','EMISION');
		$this->_helper->viewRenderer->setNoRender();
		
		$params = $this->_request->getParams();
		$solicitud = new Domain_Solicitud($params['solicitud_id']);
		$m_solicitud = $solicitud->getModelPoliza();
		$m_solicitud->operacion_id=$operacion_id;

		$detalle_poliza = $solicitud->getModelDetalle();
		$solvencia_economica_aduaneros = Domain_Helper::getHelperIdByDominioAndName('constante','solvencia_economica_aduaneros');
		
		//echo "<pre>";
		//print_r($detalle_poliza->motivo_garantia_id);
		//echo '<br>'.$solvencia_economica_aduaneros;
		//exit;
		
		if($solvencia_economica_aduaneros == $detalle_poliza->motivo_garantia_id){
		$m_solicitud->estado_id=$estado_vigente;
		}else{
		$m_solicitud->estado_id=$estado_aceptada;	
		}
		
		try {
			$m_solicitud->save();
		}catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}

		$nro_solicitud = $m_solicitud->numero_solicitud;
		echo "<br>Solicitud confirmada con exito, Nro:$nro_solicitud";

		return true;
	}
	public function anularSolicitudAction(){
		$estado = Domain_EstadoPoliza::getIdByCodigo('ANULADA');
		$this->_helper->viewRenderer->setNoRender();
		$params = $this->_request->getParams();
		$solicitud = new Domain_Solicitud($params['solicitud_id']);
		$m_solicitud = $solicitud->getModelPoliza();
		$m_solicitud->estado_id=$estado;

		try {
			$m_solicitud->save();
		}catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
		echo "<br>Solicitud anulada con exito</br>";
		return true;
	}
	public function altaTransportesAction()
	{

		//$service_model = new Services_SimpleCrud();
		//$rows = $this->_services_simple_crud->getAll($this->_solicitud->getModel());
		//$this->view->rows = $rows;
	}

	
	
public function renovacionResponsabilidadCivilAction()
	{
		
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('RESPONSABILIDAD_CIVIL');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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
		//1. Traigo el POST
		$params = $this->_request->getParams();

		//echo "<pre>";
		//print_r($params);
		
		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		$this->view->solicitud_renovada = $params['poliza_id'];
		
		$solicitud = $this->_solicitud;
				//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['poliza_id']) )$solicitud = new Domain_Poliza($params['poliza_id']);

		//echo "Este es el id".$params['poliza_id']. "<br>";

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
		
		$this->view->numero_poliza = $solicitud->getModelPoliza()->numero_poliza;
		$this->view->endoso = $solicitud->getModelPoliza()->endoso;

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
		//echo"<pre>";
		//print_r($params['poliza_renovada_id']);
		//exit;
			$solicitud = $this->_services_solicitud->saveSolicitudResponsabilidadCivil($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);
			
			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

			/*
			 * Cambio el estado de la solicitud que se renovo
			 */

			if( empty($params['poliza_id']) ){
			//	echo "entra aca para cambiar de estado!!!<br>";
	
			$poliza_poliza_id = $solicitud->getModelDetallePago()->poliza_id;
			//echo "poliza id para actualizar".$poliza_padre_id;

			$estado_id = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
			$poliza_renovada = new Domain_Poliza($params['poliza_renovada_id']);
			$poliza_renovada_model = $poliza_renovada->getModelPoliza();
			$poliza_renovada_model->poliza_poliza_id = $poliza_poliza_id;
			$poliza_renovada_model->estado_id = $estado_id;
			$poliza_renovada_model->save();

			$this->view->numero_poliza = $poliza_renovada_model->numero_poliza;
			$this->view->endoso = $poliza_renovada_model->endoso;			
			}
			
		/*
		 * Les paso los ids de la nueva poliza
		 */	
			$this->view->poliza_id = $this->view->solicitud->poliza_id;
			$this->view->numero_solicitud = $this->view->solicitud->numero_solicitud;
			$this->view->solicitud_renovada = $params['poliza_renovada_id'];
			
		}

	}
	
public function renovacionIgjAction()
	{
		
	$tipo_poliza_id = Domain_TipoPoliza::getIdByName('IGJ');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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
		//1. Traigo el POST
		$params = $this->_request->getParams();

		//echo "<pre>";
		//print_r($params);
		
		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */

		$this->view->solicitud_renovada = $params['poliza_id'];
		
		$solicitud = $this->_solicitud;
				//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['poliza_id']) )$solicitud = new Domain_Poliza($params['poliza_id']);

	//echo "Este es el id".$params['poliza_id']. "<br>";

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
		
		$this->view->numero_poliza = $solicitud->getModelPoliza()->numero_poliza;
		$this->view->endoso = $solicitud->getModelPoliza()->endoso;

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
		
			$solicitud = $this->_services_solicitud->saveSolicitudIgj($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);
			
			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

			/*
			 * Cambio el estado de la solicitud que se renovo
			 */
			if( empty($params['poliza_id']) ){
		//echo "entra aca para cambiar de estado!!!<br>";
			
			$poliza_poliza_id = $solicitud->getModelPoliza()->poliza_id;
			
			$estado_id = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
			$poliza_renovada = new Domain_Poliza($params['poliza_renovada_id']);
			$poliza_renovada_model = $poliza_renovada->getModelPoliza();
			$poliza_renovada_model->poliza_poliza_id = $poliza_poliza_id;
			$poliza_renovada_model->estado_id = $estado_id;
			$poliza_renovada_model->save();

			$this->view->numero_poliza = $poliza_renovada_model->numero_poliza;
			$this->view->endoso = $poliza_renovada_model->endoso;
			}
			
		/*
		 * Les paso los ids de la nueva poliza
		 */	
			$this->view->poliza_id = $this->view->solicitud->poliza_id;
			$this->view->numero_solicitud = $this->view->solicitud->numero_solicitud;
			$this->view->solicitud_renovada = $params['poliza_renovada_id'];
			
		}

	}
	
public function renovacionJudicialesAction()
	{
		
	$tipo_poliza_id = Domain_TipoPoliza::getIdByName('JUDICIALES');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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
		//1. Traigo el POST
		$params = $this->_request->getParams();

		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */

		$this->view->solicitud_renovada = $params['poliza_id'];
		
		$solicitud = $this->_solicitud;
				//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['poliza_id']) )$solicitud = new Domain_Poliza($params['poliza_id']);
	

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
		
		$this->view->numero_poliza = $solicitud->getModelPoliza()->numero_poliza;
		$this->view->endoso = $solicitud->getModelPoliza()->endoso;

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
		
			$solicitud = $this->_services_solicitud->saveSolicitudJudiciales($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);
			
			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

			/*
			 * Cambio el estado de la solicitud que se renovo
			 */
			if( empty($params['poliza_id']) ){
		    //echo "entra aca para cambiar de estado!!!<br>";
			
			$poliza_poliza_id = $solicitud->getModelPoliza()->poliza_id;
			
			$estado_id = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
			$poliza_renovada = new Domain_Poliza($params['poliza_renovada_id']);
			$poliza_renovada_model = $poliza_renovada->getModelPoliza();
			$poliza_renovada_model->poliza_poliza_id = $poliza_poliza_id;
			$poliza_renovada_model->estado_id = $estado_id;
			$poliza_renovada_model->save();

			$this->view->numero_poliza = $poliza_renovada_model->numero_poliza;
			$this->view->endoso = $poliza_renovada_model->endoso;
			}
			
		/*
		 * Les paso los ids de la nueva poliza
		 */	
			$this->view->poliza_id = $this->view->solicitud->poliza_id;
			$this->view->numero_solicitud = $this->view->solicitud->numero_solicitud;
			$this->view->solicitud_renovada = $params['poliza_renovada_id'];
			
		}

	}
	
public function renovacionVidaAction()
	{
		
	$tipo_poliza_id = Domain_TipoPoliza::getIdByName('VIDA');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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
		//1. Traigo el POST
		$params = $this->_request->getParams();

		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */

		$this->view->solicitud_renovada = $params['poliza_id'];
		
		$solicitud = $this->_solicitud;
				//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['poliza_id']) )$solicitud = new Domain_Poliza($params['poliza_id']);
	

		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
		
		$this->view->numero_poliza = $solicitud->getModelPoliza()->numero_poliza;
		$this->view->endoso = $solicitud->getModelPoliza()->endoso;

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
		
			$solicitud = $this->_services_solicitud->saveSolicitudVida($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);
			
			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

			/*
			 * Cambio el estado de la solicitud que se renovo
			 */
			if( empty($params['poliza_id']) ){
		//echo "entra aca para cambiar de estado!!!<br>";
			
			$poliza_poliza_id = $solicitud->getModelPoliza()->poliza_id;
			
			$estado_id = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
			$poliza_renovada = new Domain_Poliza($params['poliza_renovada_id']);
			$poliza_renovada_model = $poliza_renovada->getModelPoliza();
			$poliza_renovada_model->poliza_poliza_id = $poliza_poliza_id;
			$poliza_renovada_model->estado_id = $estado_id;
			$poliza_renovada_model->save();
			
			$this->view->numero_poliza = $poliza_renovada_model->numero_poliza;
			$this->view->endoso = $poliza_renovada_model->endoso;
			}
			
		/*
		 * Les paso los ids de la nueva poliza
		 */	
			$this->view->poliza_id = $this->view->solicitud->poliza_id;
			$this->view->numero_solicitud = $this->view->solicitud->numero_solicitud;
			$this->view->solicitud_renovada = $params['poliza_renovada_id'];
			
		}

	}
	
	
	
	
	
	public function renovacionAlquilerAction()
	{
		
	$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ALQUILER');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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
		//1. Traigo el POST
		$params = $this->_request->getParams();

		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		//echo "renueva esta?".$params['poliza_id'];
		
		$this->view->solicitud_renovada = $params['poliza_id'];
		
		$solicitud = $this->_solicitud;
				//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['poliza_id']) )$solicitud = new Domain_Poliza($params['poliza_id']);

	
		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
		
		$this->view->numero_poliza = $solicitud->getModelPoliza()->numero_poliza;
		$this->view->endoso = $solicitud->getModelPoliza()->endoso;

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
		
			$solicitud = $this->_services_solicitud->saveSolicitudAlquiler($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);
			
			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

			/*
			 * Cambio el estado de la solicitud que se renovo
			 */
			//echo "PARAMSSS";
			//print_r($params);
			
			//echo "END PARAMSSS";
			
			if( empty($params['poliza_id']) ){
		//echo "entra aca para cambiar de estado!!!<br>";
			
			$poliza_poliza_id = $solicitud->getModelPoliza()->poliza_id;
			
			$estado_id = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
			$poliza_renovada = new Domain_Poliza($params['poliza_renovada_id']);
			$poliza_renovada_model = $poliza_renovada->getModelPoliza();
			$poliza_renovada_model->poliza_poliza_id = $poliza_poliza_id;
			$poliza_renovada_model->estado_id = $estado_id;
			$poliza_renovada_model->save();

			$this->view->numero_poliza = $poliza_renovada_model->numero_poliza;
			$this->view->endoso = $poliza_renovada_model->endoso;
			}
			
		/*
		 * Les paso los ids de la nueva poliza
		 */	
			$this->view->poliza_id = $this->view->solicitud->poliza_id;
			$this->view->numero_solicitud = $this->view->solicitud->numero_solicitud;
			$this->view->solicitud_renovada = $params['poliza_renovada_id'];
			
		}

	}
/**
* Renueva Poliza Integral de Comercio - Genera una nueva Solicitud
* @method renovacionIntegralComercioAction
*/
public function renovacionIntegralComercioAction()
	{
		
	    $tipo_poliza_id = Domain_TipoPoliza::getIdByName('INTEGRAL_COMERCIO');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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
		//1. Traigo el POST
		$params = $this->_request->getParams();

		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		//echo "renueva esta?".$params['poliza_id'];
		
		$this->view->solicitud_renovada = $params['poliza_id'];
		
		$solicitud = $this->_solicitud;
		//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['poliza_id']) )$solicitud = new Domain_Poliza($params['poliza_id']);

	
		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
		
		$this->view->numero_poliza = $solicitud->getModelPoliza()->numero_poliza;
		$this->view->endoso = $solicitud->getModelPoliza()->endoso;

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
		
			$solicitud = $this->_services_solicitud->saveSolicitudIntegralComercio($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);
			
			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

			/*
			 * Cambio el estado de la solicitud que se renovo
			 */
		
			
			if( empty($params['poliza_id']) ){
			
			$poliza_poliza_id = $solicitud->getModelPoliza()->poliza_id;
			
			$estado_id = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
			$poliza_renovada = new Domain_Poliza($params['poliza_renovada_id']);
			$poliza_renovada_model = $poliza_renovada->getModelPoliza();
			$poliza_renovada_model->poliza_poliza_id = $poliza_poliza_id;
			$poliza_renovada_model->estado_id = $estado_id;
			$poliza_renovada_model->save();

			$this->view->numero_poliza = $poliza_renovada_model->numero_poliza;
			$this->view->endoso = $poliza_renovada_model->endoso;
			}
			
		/*
		 * Les paso los ids de la nueva poliza
		 */	
			$this->view->poliza_id = $this->view->solicitud->poliza_id;
			$this->view->numero_solicitud = $this->view->solicitud->numero_solicitud;
			$this->view->solicitud_renovada = $params['poliza_renovada_id'];
			
		}

	}
	

public function renovacionAccidentesPersonalesAction()
	{
		$tipo_poliza_id = Domain_TipoPoliza::getIdByName('ACCIDENTES_PERSONALES');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);
		
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		$beneficiario = new Domain_Beneficiario();
		$this->view->beneficiarios= $beneficiario->getModel()->getTable()->findAll()->toArray();
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
	

		//1. Traigo el POST
		$params = $this->_request->getParams();

		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		//echo "renueva esta?".$params['poliza_id'];
		
		$this->view->solicitud_renovada = $params['poliza_id'];

		//echo "solilcitud renovada".$this->view->solicitud_renovada;
		$solicitud = $this->_solicitud;
				//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['poliza_id']) )$solicitud = new Domain_Poliza($params['poliza_id']);

	
		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
		
		$this->view->numero_poliza = $solicitud->getModelPoliza()->numero_poliza;
		$this->view->endoso = $solicitud->getModelPoliza()->endoso;

		if($params['save']){
			
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */


			$solicitud = $this->_services_solicitud->saveSolicitudAccidentesPersonales($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);
			
			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

				/*
			 * Cambio el estado de la solicitud que se renovo
			 */
			if( empty($params['poliza_id']) ){
				//echo "entra aca para cambiar de estado!!!<br>";
			
			$poliza_poliza_id = $solicitud->getModelPoliza()->poliza_id;
			
			$estado_id = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
			$poliza_renovada = new Domain_Poliza($params['poliza_renovada_id']);
			$poliza_renovada_model = $poliza_renovada->getModelPoliza();
			$poliza_renovada_model->poliza_poliza_id = $poliza_poliza_id;
			$poliza_renovada_model->estado_id = $estado_id;
			$poliza_renovada_model->save();


			$this->view->numero_poliza = $poliza_renovada_model->numero_poliza;
			$this->view->endoso = $poliza_renovada_model->endoso;
			}
			
		/*
		 * Les paso los ids de la nueva poliza
		 */	
			$this->view->poliza_id = $this->view->solicitud->poliza_id;
			$this->view->numero_solicitud = $this->view->solicitud->numero_solicitud;
			$this->view->solicitud_renovada = $params['poliza_renovada_id'];
			
		}

	}
	
	
	public function renovacionAduanerosAction()
	{
		
	   $tipo_poliza_id = Domain_TipoPoliza::getIdByName('ADUANEROS');
		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');

		$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
		
		$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

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
		//1. Traigo el POST
		$params = $this->_request->getParams();

		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		//echo "renueva esta?".$params['poliza_id'];
		
		$this->view->solicitud_renovada = $params['poliza_id'];
		
		$solicitud = $this->_solicitud;
				//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['poliza_id']) )$solicitud = new Domain_Poliza($params['poliza_id']);

	
		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
		
		$this->view->numero_poliza = $solicitud->getModelPoliza()->numero_poliza;
		$this->view->endoso = $solicitud->getModelPoliza()->endoso;

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */
		
			$solicitud = $this->_services_solicitud->saveSolicitudAduaneros($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);
			
			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			$this->view->tipo_garantias = Domain_TipoGarantia::getTipoGarantiaByTipoPoliza($tipo_poliza_id);
			$this->view->motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasByTipoPoliza($tipo_poliza_id);

			/*
			 * Cambio el estado de la solicitud que se renovo
			 */

			
			if( empty($params['poliza_id']) ){
			echo "entra aca para cambiar de estado!!!<br>";
			
			$poliza_poliza_id = $solicitud->getModelPoliza()->poliza_id;
			
			$estado_id = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
			$poliza_renovada = new Domain_Poliza($params['poliza_renovada_id']);
			$poliza_renovada_model = $poliza_renovada->getModelPoliza();
			$poliza_renovada_model->poliza_poliza_id = $poliza_poliza_id;
			$poliza_renovada_model->estado_id = $estado_id;
			$poliza_renovada_model->save();


			
			}
			
		/*
		 * Les paso los ids de la nueva poliza
		 */	
			$this->view->poliza_id = $this->view->solicitud->poliza_id;
			$this->view->numero_solicitud = $this->view->solicitud->numero_solicitud;
			$this->view->solicitud_renovada = $params['poliza_renovada_id'];
			
		}

	}
	

	
public function renovacionAutomotoresAction()
	{



		//1.traigo todos los helpers para dar el alta de la solicitud
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');
		$this->view->periodos = Domain_Helper::getHelperByDominio('periodo');
		$this->view->forma_pagos = Domain_Helper::getHelperByDominio('forma_pago');
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$this->view->capacidad_automotores = Domain_Helper::getHelperByDominio('capacidad_automotores');
		$this->view->tipo_cobertura_automotores = Domain_Helper::getHelperByDominio('tipo_cobertura_automotores');
		$this->view->pasajeros_automotores = Domain_Helper::getHelperByDominio('pasajeros_automotores');
		$this->view->estado_vehiculo_automotores = Domain_Helper::getHelperByDominio('estado_vehiculo_automotores');
		$this->view->tipo_combustion_automotores = Domain_Helper::getHelperByDominio('tipo_combustion_automotores');
		$this->view->sistema_seguridad_automotores = Domain_Helper::getHelperByDominio('sistema_seguridad_automotores');
		$this->view->tipo_vehiculo_automotores = Domain_Helper::getHelperByDominio('tipo_vehiculo_automotores');

		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();

		//Esto es un asco tengo que modificarlo cuando tenga tiempo
		$this->view->isAgente = false;
		$this->view->isOperador = false;
		$rol_usuario = $this->_t_usuario->getNombre();
		if($rol_usuario=='AGENTE'){

			$this->view->isAgente = true;
			$this->view->agente_id = $this->_usuario->getModel()->usuario_tipo_id;
			$this->view->agente_nombre = Domain_Agente::getNameById($this->_usuario->getModel()->usuario_tipo_id);
			////print_r($this->view->agente_nombre);
		}elseif($rol_usuario=='OPERADOR'){
				
			$this->view->isOperador = true;
		}

		//2. Traigo el POST
		$params = $this->_request->getParams();
		
		/*Chequeo por las dudas pero siempre va a venir con solicitud/poliza ID
		 * Me trae todos los datos de la poliza nueva a crear
		 */
		$this->view->solicitud_renovada = $params['poliza_id'];
		
		$solicitud = $this->_solicitud;
		
				//Si viene con ID es para guardar y traigo la solicitud con los datos, sobreescribo la variable
		if(! empty($params['poliza_id']) )$solicitud = new Domain_Poliza($params['poliza_id']);

	
		$this->view->solicitud = $solicitud->getModelPoliza();
		$this->view->poliza_valores = $solicitud->getModelPolizaValores();
		$this->view->poliza_detalle = $solicitud->getModelDetalle();
		$this->view->detalle_pago = $solicitud->getModelDetallePago();
		$this->view->cantidad_cuotas = (int)Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
		$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
		$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
		
		$this->view->numero_poliza = $solicitud->getModelPoliza()->numero_poliza;
		$this->view->endoso = $solicitud->getModelPoliza()->endoso;

		
		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @param: Domain_Poliza,$params(datos del POST)
			 */

			$solicitud = $this->_services_solicitud->saveSolicitudAutomotor($solicitud,$params);
			$solicitud = $this->_services_solicitud->saveDetallePago($solicitud,$params);


			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalle();

			$this->view->detalle_pago = $solicitud->getModelDetallePago();
			//Por ahora es un metodo estatico, podria ponerlo dentro de la clase solicitud
			$this->view->cantidad_cuotas = (int) Domain_DetallePago::getCantidadCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->valor_cuotas = (float)Domain_DetallePago::getValorCuotas($solicitud->getModelDetallePago()->detalle_pago_id);
			$this->view->importe = $this->view->cantidad_cuotas * $this->view->valor_cuotas;
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
			
			//echo "<pre>";
			//print_r($params);
		
		if( empty($params['poliza_id']) ){
			//echo "entro aca para renovar la poliza";
			//Guardo la poliza que renovo
			$poliza_poliza_id = $solicitud->getModelPoliza()->poliza_id;
			//echo "renovo?";
			//echo $poliza_poliza_id;

			$estado_id = Domain_EstadoPoliza::getIdByCodigo('RENOVADA');
			$poliza_renovada = new Domain_Poliza($params['poliza_renovada_id']);
			$poliza_renovada_model = $poliza_renovada->getModelPoliza();
			$poliza_renovada_model->poliza_poliza_id = $poliza_poliza_id;
			$poliza_renovada_model->estado_id = $estado_id;
			$poliza_renovada_model->save();
			
			$this->view->numero_poliza = $poliza_renovada_model->numero_poliza;
			$this->view->endoso = $poliza_renovada_model->endoso;

			}
			
			/*
		 * Les paso los ids de la nueva poliza
		 */	
			$this->view->poliza_id = $this->view->solicitud->poliza_id;
			$this->view->numero_solicitud = $this->view->solicitud->numero_solicitud;
			$this->view->solicitud_renovada = $params['poliza_renovada_id'];
		}

	}
	
	public function enviarSolicitudCompaniaAduanerosAction(){
		
		$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();
		
		$poliza = new Domain_Poliza($params['solicitud_id']);
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
	    $headers .= "From: Fenix Seguros <info@fenixseguros.com.ar>\r\n";
	    $headers .= "Bcc:info@fenixseguros.com.ar\r\n";
	    $subject = " Solicitud de Poliza de ".$m_asegurado->nombre;
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
		$m_detalle_poliza = $poliza->getModelDetalle();
		$m_detalle_poliza_valores = $poliza->getModelPolizaValores();
		// echo "<pre>";
		//print_r($m_detalle_poliza_valores);
		//	exit;
		
		$motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($m_detalle_poliza->motivo_garantia_id, $m_poliza->tipo_poliza_id);
		 
		$codigo_productor = Model_CodigoProductorCompania::getCodigoProductorByCompaniaId($m_poliza->productor_id, $m_poliza->compania_id);
		
		$beneficiario = new Domain_Beneficiario($m_detalle_poliza->beneficiario_id);
		$m_beneficiario = $beneficiario->getModel();
		
		$d_despachante = new Domain_DespachanteAduana($m_detalle_poliza->despachante_aduana_id);
		$m_despachante = $d_despachante->getModel();
		$tipo_movimiento = 'Emisi&oacute;n';
		
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


		echo $contenido;
	    	   
	    
		$res = mail("$to", "$subject",$contenido, $headers);
		
		if(!$res){
		echo "<font color='red'>Ocurrio un error al tratar de enviar el email</font>";
		}else{

			echo "<font color='blue'>El email ha sido enviado correctamente!</font>";
		}
		
				
		return true;
	}
	
	
	public function enviarSolicitudCompaniaConstruccionAction(){
		
		$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();

		$poliza = new Domain_Poliza($params['solicitud_id']);
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
	    $headers .= "From: Fenix Seguros <info@fenixseguros.com.ar>\r\n";
	    $headers .= "Bcc:info@fenixseguros.com.ar\r\n";
	    $subject = " Solicitud de Poliza de ".$m_asegurado->nombre;
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
	
	
public function enviarSolicitudCompaniaResponsabilidadCivilAction(){
		
		$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();
		//echo "<pre>";
		
		//print_r($params);
		//exit;
		$poliza = new Domain_Poliza($params['solicitud_id']);
		$m_poliza = $poliza->getModelPoliza();
		$compania_id = $m_poliza->compania_id;
		
		//chequear si tiene asignada compania
		if(empty($compania_id)){
		echo "<font color='red'>Error: La poliza no tiene compania!</font>";
			return false;
		}
		//echo "<pre>";
		//print_r($m_poliza);
		//exit;
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
	    $headers .= "From: Fenix Seguros <info@fenixseguros.com.ar>\r\n";
	    $headers .= "Bcc:info@fenixseguros.com.ar\r\n";
	    $subject = " Solicitud de Poliza de ".$m_asegurado->nombre;
	    $to = $email;
		
	
	    
	    $contenido = file_get_contents (APPLICATION_PATH.'/../plantillas/email_sconsultora_responsabilidad_civil.html' );
		
	    $template=$contenido;
		
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
			'{obra}'=>(empty($m_detalle_poliza->obra))?'':$m_detalle_poliza->obra,
			'{expediente}'=>(empty($m_detalle_poliza->expediente))?'':$m_detalle_poliza->expediente,
			//'{apertura}'=>(empty($m_detalle_poliza->apertura_licitacion))?'':$m_detalle_poliza->apertura_licitacion,
			'{clausula}'=>(empty($m_detalle_poliza->clausula_especial))?'':$m_detalle_poliza->clausula_especial,
			'{adicional}'=>(empty($m_detalle_poliza->descripcion_adicional))?'':$m_detalle_poliza->descripcion_adicional,
			'{observaciones_compania}'=>(empty($m_poliza->observaciones_compania))?'':$m_poliza->observaciones_compania,
			'{prima}'=>(empty($m_detalle_poliza_valores->prima_comision))?'':$m_detalle_poliza_valores->prima_comision,
			'{moneda}'=>(empty($moneda))?'':$moneda,
			'{localidad_beneficiario}'=>(empty($m_beneficiario->localidad))?'':$m_beneficiario->localidad,
			'{provincia_beneficiario}'=>(empty($m_beneficiario->provincia))?'':$m_beneficiario->provincia,
			'{localidad_cliente}'=>(empty($m_asegurado->localidad))?'':$m_asegurado->localidad,
			'{provincia_cliente}'=>(empty($m_asegurado->provincia))?'':$m_asegurado->provincia
			);

			
			/*Fin Cargo Variables*/
			
	$contenido= str_replace(array_keys($variables),array_values($variables),$template);


		echo $contenido;
	    	   
	    
		$res = mail("$to", "$subject",$contenido, $headers);
		
		if(!$res){
		echo "<font color='red'>Ocurrio un error al tratar de enviar el email</font>";
		}else{

			echo "<font color='blue'>El email ha sido enviado correctamente!</font>";
		}
		
				
		return true;
	}
	
	public function enviarSolicitudCompaniaAlquilerAction(){
		
		$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();
		$poliza = new Domain_Poliza($params['solicitud_id']);
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
	    $headers .= "From: Fenix Seguros <info@fenixseguros.com.ar>\r\n";
	    $headers .= "Bcc:info@fenixseguros.com.ar\r\n";
	    $subject = " Solicitud de Poliza de ".$m_asegurado->nombre;
	    $to = $email;
		
	
	    
	    $contenido = file_get_contents (APPLICATION_PATH.'/../plantillas/email_sconsultora_alquiler.html' );
		
	    $template=$contenido;
		
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
			'{adicional}'=>(empty($m_detalle_poliza->descripcion_adicional))?'':$m_detalle_poliza->descripcion_adicional,
			'{observaciones_compania}'=>(empty($m_poliza->observaciones_compania))?'':$m_poliza->observaciones_compania,
			'{prima}'=>(empty($m_detalle_poliza_valores->prima_comision))?'':$m_detalle_poliza_valores->prima_comision,
			'{moneda}'=>(empty($moneda))?'':$moneda,
			'{localidad_beneficiario}'=>(empty($m_beneficiario->localidad))?'':$m_beneficiario->localidad,
			'{provincia_beneficiario}'=>(empty($m_beneficiario->provincia))?'':$m_beneficiario->provincia,
			'{localidad_cliente}'=>(empty($m_asegurado->localidad))?'':$m_asegurado->localidad,
			'{provincia_cliente}'=>(empty($m_asegurado->provincia))?'':$m_asegurado->provincia
			);

			
			/*Fin Cargo Variables*/
			
	$contenido= str_replace(array_keys($variables),array_values($variables),$template);


		echo $contenido;
	    	   
	    
		$res = mail("$to", "$subject",$contenido, $headers);
		
		if(!$res){
		echo "<font color='red'>Ocurrio un error al tratar de enviar el email</font>";
		}else{

			echo "<font color='blue'>El email ha sido enviado correctamente!</font>";
		}
		
				
		return true;
	}
	
public function enviarSolicitudCompaniaTransporteMercaderiaAction(){
		
		$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();
		$poliza = new Domain_Poliza($params['solicitud_id']);
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
	    $headers .= "From: Fenix Seguros <info@fenixseguros.com.ar>\r\n";
	    $headers .= "Bcc:info@fenixseguros.com.ar\r\n";
	    $subject = " Solicitud de Poliza de ".$m_asegurado->nombre;
	    $to = $email;
		
	    
	    $contenido = file_get_contents (APPLICATION_PATH.'/../plantillas/email_sconsultora_transporte_mercaderia.html' );
		
	    $template=$contenido;
		
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
			
		//$tipo_transporte = Domain_Helper::getHelperNameById('tipo_transporte', $m_poliza_detalle->tipo_transporte_id);
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
			'{adicional}'=>(empty($m_detalle_poliza->descripcion_adicional))?'':$m_detalle_poliza->descripcion_adicional,
			'{observaciones_compania}'=>(empty($m_poliza->observaciones_compania))?'':$m_poliza->observaciones_compania,
			'{mercaderia}'=>(empty($m_detalle_poliza->mercaderia))?'':$m_detalle_poliza->mercaderia,
			'{transporte}'=>(empty($m_detalle_poliza->transporte))?'':$m_detalle_poliza->transporte,
			'{cuit_transportista}'=>(empty($m_detalle_poliza->cuit_transportista))?'':$m_detalle_poliza->cuit_transportista,
			'{desde}'=>(empty($m_detalle_poliza->origen_desde))?'':$m_detalle_poliza->origen_desde,
			'{hasta}'=>(empty($m_detalle_poliza->origen_hasta))?'':$m_detalle_poliza->origen_hasta,
			'{tipo_transporte}'=>(empty($m_detalle_poliza->tipo_transporte))?'':$m_detalle_poliza->tipo_transporte,
			'{marca}'=>(empty($m_detalle_poliza->marca))?'':$m_detalle_poliza->marca,
			'{modelo}'=>(empty($m_detalle_poliza->modelo))?'':$m_detalle_poliza->modelo,
			'{patente}'=>(empty($m_detalle_poliza->patente))?'':$m_detalle_poliza->patente,
			'{patente_semi}'=>(empty($m_detalle_poliza->patente_semi))?'':$m_detalle_poliza->patente_semi,
			'{nombre_chofer}'=>(empty($m_detalle_poliza->nombre_chofer))?'':$m_detalle_poliza->nombre_chofer,
			'{documento_chofer}'=>(empty($m_detalle_poliza->documento_chofer))?'':$m_detalle_poliza->documento_chofer,
			'{custodia}'=>(empty($m_detalle_poliza->custodia))?'':$m_detalle_poliza->custodia,
			'{datos_custodia}'=>(empty($m_detalle_poliza->nombre_chofer))?'':$m_detalle_poliza->datos_custodia,
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
	
public function enviarSolicitudCompaniaAccidentesPersonalesAction(){
		
		$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();
		$poliza = new Domain_Poliza($params['solicitud_id']);
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
		
		$beneficiario = new Domain_Beneficiario($m_detalle_poliza->beneficiario_id);
		$m_beneficiario = $beneficiario->getModel();
		
		$headers = "MIME-Version: 1.0\r\n";
	    $headers .= "Content-type: text/html; charset=ISO-8859-1\r\n";
	    $headers .= "From: Fenix Seguros <info@fenixseguros.com.ar>\r\n";
	    $headers .= "Bcc:info@fenixseguros.com.ar\r\n";
	    $subject = " Solicitud de Poliza de ".$m_asegurado->nombre;
	    $to = $email;
		
	    
	    $contenido = file_get_contents (APPLICATION_PATH.'/../plantillas/email_sconsultora_accidentes_personales.html' );
	    
	    $template=$contenido;
		
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
			
		//$tipo_transporte = Domain_Helper::getHelperNameById('tipo_transporte', $m_poliza_detalle->tipo_transporte_id);
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
			'{observaciones_compania}'=>(empty($m_poliza->observaciones_compania))?'':$m_poliza->observaciones_compania,
			'{custodia}'=>(empty($m_detalle_poliza->custodia))?'':$m_detalle_poliza->custodia,
			'{prima}'=>(empty($m_detalle_poliza_valores->prima_comision))?'':$m_detalle_poliza_valores->prima_comision,
			'{moneda}'=>(empty($moneda))?'':$moneda,
			'{cantidad_personas}'=>(empty($m_detalle_poliza->cantidad_personas))?'':$m_detalle_poliza->cantidad_personas,
			'{tareas_a_realizar}'=>(empty($m_detalle_poliza->tareas_a_realizar))?'':$m_detalle_poliza->tareas_a_realizar,
			'{altura_maxima}'=>(empty($m_detalle_poliza->altura_maxima))?'':$m_detalle_poliza->altura_maxima,
			'{datos_adicionales}'=>(empty($m_detalle_poliza->datos_adicionales))?'':$m_detalle_poliza->datos_adicionales,
			'{domicilio_beneficiario}'=>(empty($m_beneficiario->domicilio))?'':$m_beneficiario->domicilio,
			'{cuit_beneficiario}'=>(empty($m_beneficiario->cuit))?'':$m_beneficiario->cuit,
			'{adicional}'=>(empty($m_detalle_poliza->descripcion_adicional))?'':$m_detalle_poliza->descripcion_adicional,
			'{localidad_beneficiario}'=>(empty($m_beneficiario->localidad))?'':$m_beneficiario->localidad,
			'{provincia_beneficiario}'=>(empty($m_beneficiario->provincia))?'':$m_beneficiario->provincia,
			'{localidad_cliente}'=>(empty($m_asegurado->localidad))?'':$m_asegurado->localidad,
			'{provincia_cliente}'=>(empty($m_asegurado->provincia))?'':$m_asegurado->provincia
			);

			/*Fin Cargo Variables*/
			
	$contenido= str_replace(array_keys($variables),array_values($variables),$template);


		echo $contenido;
	    	   
	    
		$res = mail("$to", "$subject",$contenido, $headers);
		
		if(!$res){
		echo "<font color='red'>Ocurrio un error al tratar de enviar el email</font>";
		}else{

			echo "<font color='blue'>El email ha sido enviado correctamente!</font>";
		}
		
				
		return true;
	}
	
public function enviarSolicitudCompaniaIgjAction(){
		
		$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();
		$poliza = new Domain_Poliza($params['solicitud_id']);
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
	    $headers .= "From: Fenix Seguros <info@fenixseguros.com.ar>\r\n";
	    $headers .= "Bcc:info@fenixseguros.com.ar\r\n";
	    $subject = " Solicitud de Poliza de ".$m_asegurado->nombre;
	    $to = $email;
		
	    
	    $contenido = file_get_contents (APPLICATION_PATH.'/../plantillas/email_sconsultora_igj.html' );
		
	    $template=$contenido;
		
		/*Cargo variables*/
		
		
		
		$productor = new Domain_Productor($m_poliza->productor_id);
		$m_productor = $productor->getModel();
		$tipo_seguro = Domain_TipoPoliza::getNameById($m_poliza->tipo_poliza_id);
		$m_detalle_poliza = $poliza->getModelDetalle();
		$m_detalle_poliza_valores = $poliza->getModelPolizaValores();

		$motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($m_detalle_poliza->motivo_garantia_id, $m_poliza->tipo_poliza_id);
		$tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($m_poliza->tipo_poliza_id,$m_detalle_poliza->tipo_garantia_id);
		
		$codigo_productor = Model_CodigoProductorCompania::getCodigoProductorByCompaniaId($m_poliza->productor_id, $m_poliza->compania_id);
		
		$beneficiario = new Domain_Beneficiario($m_detalle_poliza->beneficiario_id);
		$m_beneficiario = $beneficiario->getModel();
			
		//$tipo_transporte = Domain_Helper::getHelperNameById('tipo_transporte', $m_poliza_detalle->tipo_transporte_id);
		$tipo_movimiento = 'Emisi&oacute;n';
		
		$moneda = '$';	
		if($m_detalle_poliza_valores->moneda_id == 1){
		$moneda = '$';	
		}elseif($m_detalle_poliza_valores->moneda_id == 2){
		$moneda = 'USD';		
		}elseif($m_detalle_poliza_valores->moneda_id == 3){
		$moneda = 'EURO';		
		};
		/*
		
		 */
		$variables = array(
			'{numero_solicitud}'=>(empty($m_poliza->numero_solicitud))?'':$m_poliza->numero_solicitud,
			'{compania}'=>(empty($m_poliza->compania_id))?'':$m_compania->nombre,
			'{cliente}'=>(empty($m_poliza->asegurado_id))?'':$m_asegurado->nombre,
			'{domicilio_cliente}'=>(empty($m_asegurado->domicilio))?'':$m_asegurado->domicilio,
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
			'{observaciones_compania}'=>(empty($m_poliza->observaciones_compania))?'':$m_poliza->observaciones_compania,
			'{custodia}'=>(empty($m_detalle_poliza->custodia))?'':$m_detalle_poliza->custodia,
			'{prima}'=>(empty($m_detalle_poliza_valores->prima_comision))?'':$m_detalle_poliza_valores->prima_comision,
			'{moneda}'=>(empty($moneda))?'':$moneda,
			'{tipo_garantia}'=>(empty($tipo_garantia))?'':$m_detalle_poliza_valores->monto_asegurado,
			//'{motivo_garantia}'
			//'{beneficiario}'
			'{domicilio_riesgo}'=>(empty($m_detalle_poliza->domicilio_riesgo))?'':$m_detalle_poliza->domicilio_riesgo,
			'{localidad_riesgo}'=>(empty($m_detalle_poliza->localidad_riesgo))?'':$m_detalle_poliza->localidad_riesgo,
			'{provincia_riesgo}'=>(empty($m_detalle_poliza->provincia_riesgo))?'':$m_detalle_poliza->provincia_riesgo,
			'{nro_licitacion}'=>(empty($m_detalle_poliza->numero_licitacion))?'':$m_detalle_poliza->numero_licitacion,
			'{obra}'=>(empty($m_detalle_poliza->obra))?'':$m_detalle_poliza->obra,
			'{expediente}'=>(empty($m_detalle_poliza->expediente))?'':$m_detalle_poliza->expediente,
			'{objeto}'=>(empty($m_detalle_poliza->objeto))?'':$m_detalle_poliza->objeto,
			'{apertura_licitacion}'=>(empty($m_detalle_poliza->apertura_licitacion))?'':$m_detalle_poliza->apertura_licitacion,
			'{clausula_especial}'=>(empty($m_detalle_poliza->clausula_especial))?'':$m_detalle_poliza->clausula_especial,
			'{descripcion_adicional}'=>(empty($m_detalle_poliza->descripcion_adicional))?'':$m_detalle_poliza->descripcion_adicional,
			'{localidad_beneficiario}'=>(empty($m_beneficiario->localidad))?'':$m_beneficiario->localidad,
			'{provincia_beneficiario}'=>(empty($m_beneficiario->provincia))?'':$m_beneficiario->provincia,
			'{localidad_cliente}'=>(empty($m_asegurado->localidad))?'':$m_asegurado->localidad,
			'{provincia_cliente}'=>(empty($m_asegurado->provincia))?'':$m_asegurado->provincia
			);

			/*Fin Cargo Variables*/
			
	$contenido= str_replace(array_keys($variables),array_values($variables),$template);


		echo $contenido;
	    	   
	    
		$res = mail("$to", "$subject",$contenido, $headers);
		
		if(!$res){
		echo "<font color='red'>Ocurrio un error al tratar de enviar el email</font>";
		}else{

			echo "<font color='blue'>El email ha sido enviado correctamente!</font>";
		}
		
				
		return true;
	}

	public function enviarSolicitudCompaniaVidaAction(){
		
		$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();
		$poliza = new Domain_Poliza($params['solicitud_id']);
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
	    $headers .= "From: Fenix Seguros <info@fenixseguros.com.ar>\r\n";
	    $headers .= "Bcc:info@fenixseguros.com.ar\r\n";
	    $subject = " Solicitud de Poliza de ".$m_asegurado->nombre;
	    $to = $email;
		
	    
	    $contenido = file_get_contents (APPLICATION_PATH.'/../plantillas/email_sconsultora_igj.html' );
		
	    $template=$contenido;
		
		/*Cargo variables*/
		
		
		
		$productor = new Domain_Productor($m_poliza->productor_id);
		$m_productor = $productor->getModel();
		$tipo_seguro = Domain_TipoPoliza::getNameById($m_poliza->tipo_poliza_id);
		$m_detalle_poliza = $poliza->getModelDetalle();
		$m_detalle_poliza_valores = $poliza->getModelPolizaValores();

		$motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($m_detalle_poliza->motivo_garantia_id, $m_poliza->tipo_poliza_id);
		$tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($m_poliza->tipo_poliza_id,$m_detalle_poliza->tipo_garantia_id);
		
		$codigo_productor = Model_CodigoProductorCompania::getCodigoProductorByCompaniaId($m_poliza->productor_id, $m_poliza->compania_id);
		
		$beneficiario = new Domain_Beneficiario($m_detalle_poliza->beneficiario_id);
		$m_beneficiario = $beneficiario->getModel();
			
		//$tipo_transporte = Domain_Helper::getHelperNameById('tipo_transporte', $m_poliza_detalle->tipo_transporte_id);
		$tipo_movimiento = 'Emisi&oacute;n';
		
		$moneda = '$';	
		if($m_detalle_poliza_valores->moneda_id == 1){
		$moneda = '$';	
		}elseif($m_detalle_poliza_valores->moneda_id == 2){
		$moneda = 'USD';		
		}elseif($m_detalle_poliza_valores->moneda_id == 3){
		$moneda = 'EURO';		
		};
		/*
		
		 */
		$variables = array(
			'{numero_solicitud}'=>(empty($m_poliza->numero_solicitud))?'':$m_poliza->numero_solicitud,
			'{compania}'=>(empty($m_poliza->compania_id))?'':$m_compania->nombre,
			'{cliente}'=>(empty($m_poliza->asegurado_id))?'':$m_asegurado->nombre,
			'{domicilio_cliente}'=>(empty($m_asegurado->domicilio))?'':$m_asegurado->domicilio,
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
			'{observaciones_compania}'=>(empty($m_poliza->observaciones_compania))?'':$m_poliza->observaciones_compania,
			'{custodia}'=>(empty($m_detalle_poliza->custodia))?'':$m_detalle_poliza->custodia,
			'{prima}'=>(empty($m_detalle_poliza_valores->prima_comision))?'':$m_detalle_poliza_valores->prima_comision,
			'{moneda}'=>(empty($moneda))?'':$moneda,
			'{tipo_garantia}'=>(empty($tipo_garantia))?'':$m_detalle_poliza_valores->monto_asegurado,
			//'{motivo_garantia}'
			//'{beneficiario}'
			'{domicilio_riesgo}'=>(empty($m_detalle_poliza->domicilio_riesgo))?'':$m_detalle_poliza->domicilio_riesgo,
			'{localidad_riesgo}'=>(empty($m_detalle_poliza->localidad_riesgo))?'':$m_detalle_poliza->localidad_riesgo,
			'{provincia_riesgo}'=>(empty($m_detalle_poliza->provincia_riesgo))?'':$m_detalle_poliza->provincia_riesgo,
			'{nro_licitacion}'=>(empty($m_detalle_poliza->numero_licitacion))?'':$m_detalle_poliza->numero_licitacion,
			'{obra}'=>(empty($m_detalle_poliza->obra))?'':$m_detalle_poliza->obra,
			'{expediente}'=>(empty($m_detalle_poliza->expediente))?'':$m_detalle_poliza->expediente,
			'{objeto}'=>(empty($m_detalle_poliza->objeto))?'':$m_detalle_poliza->objeto,
			'{apertura_licitacion}'=>(empty($m_detalle_poliza->apertura_licitacion))?'':$m_detalle_poliza->apertura_licitacion,
			'{clausula_especial}'=>(empty($m_detalle_poliza->clausula_especial))?'':$m_detalle_poliza->clausula_especial,
			'{descripcion_adicional}'=>(empty($m_detalle_poliza->descripcion_adicional))?'':$m_detalle_poliza->descripcion_adicional,
			'{localidad_beneficiario}'=>(empty($m_beneficiario->localidad))?'':$m_beneficiario->localidad,
			'{provincia_beneficiario}'=>(empty($m_beneficiario->provincia))?'':$m_beneficiario->provincia,
			'{localidad_cliente}'=>(empty($m_asegurado->localidad))?'':$m_asegurado->localidad,
			'{provincia_cliente}'=>(empty($m_asegurado->provincia))?'':$m_asegurado->provincia
			);

			/*Fin Cargo Variables*/
			
	$contenido= str_replace(array_keys($variables),array_values($variables),$template);


		echo $contenido;
	    	   
	    
		$res = mail("$to", "$subject",$contenido, $headers);
		
		if(!$res){
		echo "<font color='red'>Ocurrio un error al tratar de enviar el email</font>";
		}else{

			echo "<font color='blue'>El email ha sido enviado correctamente!</font>";
		}
		
				
		return true;
	}

	public function enviarSolicitudCompaniaJudicialesAction(){
		
		$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();
		$poliza = new Domain_Poliza($params['solicitud_id']);
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
	    $headers .= "From: Fenix Seguros <info@fenixseguros.com.ar>\r\n";
	    $headers .= "Bcc:info@fenixseguros.com.ar\r\n";
	    $subject = " Solicitud de Poliza de ".$m_asegurado->nombre;
	    $to = $email;
		
	    
	    $contenido = file_get_contents (APPLICATION_PATH.'/../plantillas/email_sconsultora_igj.html' );
		
	    $template=$contenido;
		
		/*Cargo variables*/
		
		
		
		$productor = new Domain_Productor($m_poliza->productor_id);
		$m_productor = $productor->getModel();
		$tipo_seguro = Domain_TipoPoliza::getNameById($m_poliza->tipo_poliza_id);
		$m_detalle_poliza = $poliza->getModelDetalle();
		$m_detalle_poliza_valores = $poliza->getModelPolizaValores();

		$motivo_garantia = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($m_detalle_poliza->motivo_garantia_id, $m_poliza->tipo_poliza_id);
		$tipo_garantia = Domain_TipoGarantia::getNameByTipoPolizaAndId($m_poliza->tipo_poliza_id,$m_detalle_poliza->tipo_garantia_id);
		
		$codigo_productor = Model_CodigoProductorCompania::getCodigoProductorByCompaniaId($m_poliza->productor_id, $m_poliza->compania_id);
		
		$beneficiario = new Domain_Beneficiario($m_detalle_poliza->beneficiario_id);
		$m_beneficiario = $beneficiario->getModel();
			
		//$tipo_transporte = Domain_Helper::getHelperNameById('tipo_transporte', $m_poliza_detalle->tipo_transporte_id);
		$tipo_movimiento = 'Emisi&oacute;n';
		
		$moneda = '$';	
		if($m_detalle_poliza_valores->moneda_id == 1){
		$moneda = '$';	
		}elseif($m_detalle_poliza_valores->moneda_id == 2){
		$moneda = 'USD';		
		}elseif($m_detalle_poliza_valores->moneda_id == 3){
		$moneda = 'EURO';		
		};
		/*
		
		 */
		$variables = array(
			'{numero_solicitud}'=>(empty($m_poliza->numero_solicitud))?'':$m_poliza->numero_solicitud,
			'{compania}'=>(empty($m_poliza->compania_id))?'':$m_compania->nombre,
			'{cliente}'=>(empty($m_poliza->asegurado_id))?'':$m_asegurado->nombre,
			'{domicilio_cliente}'=>(empty($m_asegurado->domicilio))?'':$m_asegurado->domicilio,
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
			'{observaciones_compania}'=>(empty($m_poliza->observaciones_compania))?'':$m_poliza->observaciones_compania,
			'{custodia}'=>(empty($m_detalle_poliza->custodia))?'':$m_detalle_poliza->custodia,
			'{prima}'=>(empty($m_detalle_poliza_valores->prima_comision))?'':$m_detalle_poliza_valores->prima_comision,
			'{moneda}'=>(empty($moneda))?'':$moneda,
			'{tipo_garantia}'=>(empty($tipo_garantia))?'':$m_detalle_poliza_valores->monto_asegurado,
			//'{motivo_garantia}'
			//'{beneficiario}'
			'{domicilio_riesgo}'=>(empty($m_detalle_poliza->domicilio_riesgo))?'':$m_detalle_poliza->domicilio_riesgo,
			'{localidad_riesgo}'=>(empty($m_detalle_poliza->localidad_riesgo))?'':$m_detalle_poliza->localidad_riesgo,
			'{provincia_riesgo}'=>(empty($m_detalle_poliza->provincia_riesgo))?'':$m_detalle_poliza->provincia_riesgo,
			'{nro_licitacion}'=>(empty($m_detalle_poliza->numero_licitacion))?'':$m_detalle_poliza->numero_licitacion,
			'{obra}'=>(empty($m_detalle_poliza->obra))?'':$m_detalle_poliza->obra,
			'{expediente}'=>(empty($m_detalle_poliza->expediente))?'':$m_detalle_poliza->expediente,
			'{objeto}'=>(empty($m_detalle_poliza->objeto))?'':$m_detalle_poliza->objeto,
			'{apertura_licitacion}'=>(empty($m_detalle_poliza->apertura_licitacion))?'':$m_detalle_poliza->apertura_licitacion,
			'{clausula_especial}'=>(empty($m_detalle_poliza->clausula_especial))?'':$m_detalle_poliza->clausula_especial,
			'{descripcion_adicional}'=>(empty($m_detalle_poliza->descripcion_adicional))?'':$m_detalle_poliza->descripcion_adicional,
			'{localidad_beneficiario}'=>(empty($m_beneficiario->localidad))?'':$m_beneficiario->localidad,
			'{provincia_beneficiario}'=>(empty($m_beneficiario->provincia))?'':$m_beneficiario->provincia,
			'{localidad_cliente}'=>(empty($m_asegurado->localidad))?'':$m_asegurado->localidad,
			'{provincia_cliente}'=>(empty($m_asegurado->provincia))?'':$m_asegurado->provincia
			);

			/*Fin Cargo Variables*/
			
	$contenido= str_replace(array_keys($variables),array_values($variables),$template);


		echo $contenido;
	    	   
	    
		$res = mail("$to", "$subject",$contenido, $headers);
		
		if(!$res){
		echo "<font color='red'>Ocurrio un error al tratar de enviar el email</font>";
		}else{

			echo "<font color='blue'>El email ha sido enviado correctamente!</font>";
		}
		
				
		return true;
	}
	
	
	public function traeMotivoGarantiaAction(){
		
		$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();
		//print_r($params);
		$id = $params['id'];
		$tipo_poliza_id = $params['tipo_poliza_id'];
		//echo "<pre>";
		
	//	echo "tipo_garantia_id".$id;
		//echo "tipo_poliza_id".$tipo_poliza_id;
		$motivo_garantias = Domain_MotivoGarantia::getMotivoGarantiasCascadeByTipoPoliza($tipo_poliza_id,$id);
		//print_r($motivo_garantias);
		
		$inicio_select = "<select id='motivo_garantia_id' name='motivo_garantia_id'>
							<option value='0'>Seleccione Motivo Garantia</option>";
		echo $inicio_select; 
		
		foreach($motivo_garantias as $row ){

		$dentro_select = "<option value=".$row['motivo_garantia_id'].">".$row['motivo_garantia']."</option>";
		echo $dentro_select ;
		}
		
		$fin_select="</select>";
		
		echo $fin_select;
		
							
	}
	
	public function uploadAction(){
		
		
$fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);

if ($fn) {

        // AJAX call
        file_put_contents(
                '/var/www/file/temp/' . $fn,
                file_get_contents('php://input')
        );
        echo "$fn uploaded";
        exit();

}
else {

        // form submit
        $files = $_FILES['fileselect'];

        foreach ($files['error'] as $id => $err) {
                if ($err == UPLOAD_ERR_OK) {
                        $fn = $files['name'][$id];
                        move_uploaded_file(
                                $files['tmp_name'][$id],
                                'uploads/' . $fn
                        );
                        echo "<p>File $fn uploaded.</p>";
                }
        }

}
		
		
	}
	
	public function viewAction()
	{

		$params = $this->_request->getParams();
		$row = $this->_services_simple_crud
		->getById($this->_solicitud->getModel(),array('primary_key'=>'solicitud_id','value'=>$params['id']));

		$this->view->row = $row;

	}
	public function addAction()
	{
		$params = $this->_request->getParams();
		$values =  array_slice($params,4); //saca la data de mas

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
	public function listarAseguradosAction(){

		$this->_helper->viewRenderer->setNoRender ();

		$asegurado= new Domain_Asegurado();
		$asegurados = $asegurado->getModel()->getTable()->findAll()->toArray();
		$asegurados_format = array();
		//le doy formato al array
		foreach($asegurados as $asegurado){
			$asegurados_format[$asegurado['nombre']]=array("id"=>$asegurado['asegurado_id'],"dni"=>$asegurado['numero_documento']);
		}
		$params = $this->_request->getParams();
		$q = $params['q'];

		foreach ($asegurados_format as $key=>$value) {

			$dni = $value['dni'];
			$id = $value['id'];
			//echo "<pre>";
			////print_r($value);
			if (strpos(strtolower($key), $q) !== false) {
				echo "$key|$dni|$id\n";
			}

		}

	}

	/*
	 * foreach ($asegurados_format as $key=>$value) {

	 if (strpos(strtolower($key), $q) !== false) {
	 echo "$key|$value\n";
	 }

		}

	 */
	public function listarClientesAction(){

		$this->_helper->viewRenderer->setNoRender ();

		$cliente= new Domain_Cliente();
		$clientes = $cliente->getModel()->getTable()->findAll()->toArray();
		$clientes_format = array();
		//le doy formato al array
		foreach($clientes as $cliente){
			$clientes_format[$cliente['nombre']]=$cliente['asegurado_id'];
		}
		$params = $this->_request->getParams();
		$q = $params['q'];

		foreach ($clientes_format as $key=>$value) {

			if (strpos(strtolower($key), $q) !== false) {
				echo "$key|$value\n";
			}

		}

	}

	/*public function fileUploadAction(){
		$this->_helper->viewRenderer->setNoRender();
		$params = $this->_request->getParams();

		$upload = new Zend_File_Transfer_Adapter_Http();

		$name = rand(0, 1000);
		//Cambiar la ruta
		$upload->setDestination("/var/www/appmvc/files/");


		//$upload->addFilter('Rename', "/var/www/appmvc/files/", 'file2');
		//	realpath(dirname(__FILE__) . '/../application');
		$path =  realpath(APPLICATION_PATH . "/../files/" );

		//$upload->addFilter('Rename', $path);

		//$upload->addFilter('Rename', "/var/www/appmvc/files/");

		if ($upload->receive()) {
		echo "The file has been uploaded!";
		}
		//Trae el nombre para guardarlo en la base
		$names = $upload->getFileName();

		echo"<pre>";
		//print_r($names);
		////print_r($_FILES);

		}*/

	public function fileUploadAction(){
		$this->_helper->viewRenderer->setNoRender();
		echo"<pre>";

		//print_r($_FILES);
		$target_filepath = "/var/www/appmvc/files/" . basename($_FILES['upload_scn']['name']);

		echo "target_filepath".$target_filepath;

		if (move_uploaded_file($_FILES['upload_scn']['tmp_name'], $target_filepath)) {
			$result = true;
		}

		echo json_encode($result);

	}

	public function fileDownloadAction(){
		//	$this->_helper->viewRenderer->setNoRender ();
		$params = $this->_request->getParams();
		//trae el nombre de la base
		$fullPath = "/var/www/appmvc/files/phpHiMBCZ";

		$this->_helper->viewRenderer->setNoRender();

	 //$this->getResponse()->setHeader('Content-type', 'application/pdf')
		//             ->setHeader('X-Sendfile', $fullPath)
		//           ->sendResponse();

		//	//print_r($fullPath);

		/*if ($fd = fopen ($fullPath, "r")) {

		$fsize = filesize($fullPath);
		$path_parts = pathinfo($fullPath);
		$ext = strtolower($path_parts["extension"]);

		header("Content-type: application/pdf");
		header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
		header("Content-length: $fsize");
		header("Cache-control: private");

		// $this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();


		while(!feof($fd)) {
		$buffer = fread($fd, 2048);
		echo $buffer;
		}
		}

		fclose ($fd);


		*/


	}
}







