<?php
/*
 *
te paso ahora este listado (creo que debería aparecer en el Menú Polizas y se puede llamar simplemente Vencimientos
 
Al ingresar te permite selecionar entre que fechas buscar: desde y hasta. Y si le das click a buscar sin completar nada te muestra los proximos 30 días.
 
Lo listaria de la siguiente forma:
 
Vencimiento
Vigencia (desde / hasta)
Tomador
Nº de Póliza
Endoso
Operación
Riesgo
Suma Asegurada
Prima
Premio Compañia
Premio
Plus
Documentación (SI / NO) -acordate que esto indicaba si les llegó el documento-
Botón Baja
Botón Refacturar (sólo en caución)
 *
 */

require_once ('IndexController.php');

class Poliza_OperacionesController extends Poliza_IndexController
{
	private $_poliza = null;
	private $_sesion = null;
	private $_usuario = null;
	private $_t_usuario = null;


	public function init()
	{

		if ( ! (Zend_Auth::getInstance()->hasIdentity())  ) {

			$this->_helper->redirector('index','login','default');
		}

		$this->_helper->layout->disableLayout();
		$this->_poliza = new Domain_Poliza();
		$this->_sesion = Domain_Sesion::getInstance();
		$this->_usuario = $this->_sesion->getUsuario();
		$this->_t_usuario = $this->_usuario->getTipoUsuario();

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
		$this->_forward('list','listado','informes');

	}

	public function listAction()
	{

		$params = $this->_request->getParams();


		$this->view->buscar = true;

		if( !empty($params['fecha_desde']) ) {
			$fecha_desde  = $params['fecha_desde'];
			$fecha_hasta = $params['fecha_hasta'];
		}else{
			//saco el mes en curso(despues lo mejoro, por ahora resta 15)
			$date_desde = new DateTime();
			$date_desde->sub(new DateInterval('P15D'));
			$fecha_desde =  $date_desde->format('Y-m-d');
				
			$date_hasta = new DateTime();
			$date_hasta->add(new DateInterval('P15D'));
			$fecha_hasta =  $date_hasta->format('Y-m-d');
		}
		 

		$rows = $this->_t_usuario->getPolizasInformeDiario($fecha_desde,$fecha_hasta);



			
		//Esto para despues
		/*
		$page=$this->_getParam('page',1);
			
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];

		$this->view->rows = $paginator;
		*/

		$this->view->rows = $rows;
	}

	public function listadoVencimientoAction()
	{

		$params = $this->_request->getParams();

		
		$cliente = new Domain_Cliente();
		$this->view->asegurados= $cliente->getModel()->getTable()->findAll()->toArray();
		
		$this->view->buscar = true;

		if( !empty($params['fecha_desde']) ) {
			$fecha_desde  = $params['fecha_desde'];
			$fecha_hasta = $params['fecha_hasta'];
			
			//echo $fecha_desde." hasta ".$fecha_hasta;
			 
		}else{
			//saco el mes en curso(despues lo mejoro, por ahora resta 15)
			$date_desde = new DateTime();
			$date_desde->sub(new DateInterval('P15D'));
			$fecha_desde =  $date_desde->format('Y-m-d');
				
			$date_hasta = new DateTime();
			$date_hasta->add(new DateInterval('P15D'));
			$fecha_hasta =  $date_hasta->format('Y-m-d');
		}
		
		$asegurado_id = ( !empty($params['asegurado_id']) )? $params['asegurado_id'] : null;
		 
	  //echo "id:".$asegurado_id;
		 
		$rows = $this->_t_usuario->getPolizasVencimiento($fecha_desde,$fecha_hasta,$asegurado_id);
		


		



			
		//Esto para despues
		/*
		$page=$this->_getParam('page',1);
			
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];

		$this->view->rows = $paginator;
		*/

		$this->view->rows = $rows;
	}

	
public function listadoProduccionAction()
	{

		$params = $this->_request->getParams();


		$this->view->buscar = true;
/*
		if( !empty($params['fecha_desde']) ) {
			$fecha_desde  = $params['fecha_desde'];
			$fecha_hasta = $params['fecha_hasta'];
		}else{
			//saco el mes en curso(despues lo mejoro, por ahora resta 15)
			$date_desde = new DateTime();
			$date_desde->sub(new DateInterval('P15D'));
			$fecha_desde =  $date_desde->format('Y-m-d');
				
			$date_hasta = new DateTime();
			$date_hasta->add(new DateInterval('P15D'));
			$fecha_hasta =  $date_hasta->format('Y-m-d');
		}
	*/	 

		$rows = $this->_t_usuario->getPolizasListadoProduccion();



			
		//Esto para despues
		/*
		$page=$this->_getParam('page',1);
			
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];

		$this->view->rows = $paginator;
		*/

		$this->view->rows = $rows;
	}
	
	
	public function deudaAseguradoAction(){

		$params = $this->_request->getParams();
		//Me trae las polizas del asegurado por ahora todas
		//$rows = $this->_t_usuario->getPolizaByAsegurado($params['asegurado_id']);
		$rows = $this->_t_usuario->getPolizaInpagaByAsegurado($params['asegurado_id']);

		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');

		$this->view->polizas = $rows;
		$this->view->asegurado_id = $params['asegurado_id'];
		//echo "<pre>";
		//print_r($rows);

	}


	public function pagarDeudaAseguradoAction(){

		$params = $this->_request->getParams();
		echo"<pre>";
		print_r($params);
		//aca tiene que pasar el parametro de "pagar" o algo parecido
		if(!empty($params['array_polizas'])){
				
				
			//1. Guardo el movimiento del pago
			$m_movimiento = new Model_Movimiento();
			$m_movimiento->importe = $params['importe'];
			$m_movimiento->asegurado_id = $params['asegurado_id'];
			$m_movimiento->fecha_pago = $params['fecha_pago'];
			$m_movimiento->moneda_id = $params['moneda_id'];
			$m_movimiento->save();
			/*
			 * 2.Pongo como paga la poliza
			 * 3.Guardo las asociaciones con las polizas
			 */
			$id_pago_poliza = explode(',',$params['array_polizas']);
			foreach($id_pago_poliza as $id_cuota_poliza){
				echo "<br>Paga esta parte de la poliza".$id_cuota_poliza;

				if(empty($id_cuota_poliza)) break;
				//2.Marca como paga
				$m_detalle_pago_cuota = new Model_DetallePagoCuota();
				$res = $m_detalle_pago_cuota->getTable()
				->createQuery()//->toArray();
				->where("detalle_pago_cuota_id = ?",$id_cuota_poliza)
				->execute();
				//->toArray();
				//->getSqlQuery();

				$detalle = $res->getFirst();
				$detalle->pago_id = 1;
				$detalle->save();

				//3. Asocia el movimiento con la poliza

			}
		}


		/*if(!empty($params['array_polizas'])){
			$id_pago_poliza = explode(',',$params['array_polizas']);

			foreach($id_pago_poliza as $id_cuota_poliza){
			//Marca como pagada
			$m_detalle_pago_cuota = new Model_DetallePagoCuota();
			$res = $m_detalle_pago_cuota->getTable()
			->createQuery()//->toArray();
			->where("detalle_pago_cuota_id = ?",$id_cuota_poliza)
			->execute();
			//->toArray();
			//->getSqlQuery();
			$detalle = $res->getFirst();
			$detalle->pago = 1;
			$detalle->save();

			//print_r($res->getFirst()->detalle_pago_cuota_id);
			//print_r($m_detalle_pago_cuota);
			//echo " y esta:".$id_cuota_poliza;
			}
			}
			*/

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
		$this->view->cuotas = Domain_Helper::getHelperByDominio('cuota');
		$compania = new Domain_Compania();
		$this->view->companias= $compania->getModel()->getTable()->findAll()->toArray();
		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();
		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();
		$cobrador = new Domain_Cobrador();
		$this->view->cobradores= $cobrador->getModel()->getTable()->findAll()->toArray();
		//2. Traigo el POST
		$params = $this->_request->getParams();
		echo "<pre>";
		print_r($params);

		//Si viene con ID es para guardar y traigo la solicitud con los datos
		if(! empty($params['solicitud_id']) ){
			$solicitud = new Domain_Poliza($params['solicitud_id']);
			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalleAutomotor();
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);

		}else{
			//Nueva solicitud
			$solicitud = $this->_solicitud;
		}

		if($params['save']){
			/*
			 * Service_Poliza::saveSolicitud()
			 * @parametros: Domain_Poliza,$params(datos del POST)
			 */

			$solicitud = $this->_services_solicitud->saveSolicitudAutomotor($solicitud,$params);
			$this->_services_solicitud->saveDetallePago($solicitud,$params);

			$this->view->solicitud = $solicitud->getModelPoliza();
			$this->view->poliza_valores = $solicitud->getModelPolizaValores();
			$this->view->poliza_detalle = $solicitud->getModelDetalleAutomotor();
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($solicitud->getModelPoliza()->asegurado_id);
		}

	}
	public function confirmarSolicitudAction(){
		$this->_helper->viewRenderer->setNoRender();
		$params = $this->_request->getParams();
		echo "<pre>";
		print_r($params);
		$solicitud = new Domain_Solicitud($params['solicitud_id']);
		$m_solicitud = $solicitud->getModelPoliza();
		$m_solicitud->estado_id=1;
		$m_solicitud->save();

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
			$asegurados_format[$asegurado['nombre']]=$asegurado['asegurado_id'];
		}
		$params = $this->_request->getParams();
		$q = $params['q'];

		foreach ($asegurados_format as $key=>$value) {

			if (strpos(strtolower($key), $q) !== false) {
				echo "$key|$value\n";
			}

		}


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
		$this->view->baja=true;
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
	
	
	
}
