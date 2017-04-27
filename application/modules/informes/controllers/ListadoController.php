<?php
/*
 *
 Cuando ingresas por default te muestra las operaciones del mes en curso y arriba tenés que dejar que se pueda elegir la fecha, y tambien que se pueda filtrar la operación (para el caso de caución): emisión / refacturación

 Te paso los datos que tienen que mostrarse en el listado:

 Fecha de Pedido
 Tomador
 Nº de Póliza
 Endoso
 Agente
 Riesgo
 Moneda
 Suma Asegurada
 Compañia
 Operación
 Premio Compañia
 Premio
 Plus
 Importe
 Fecha de Pago
 Cobrador
 Importe Cobrado

 *
 */

require_once ('IndexController.php');

class Informes_ListadoController extends Informes_IndexController
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

	public function bkinformeDiarioAction()
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

	public function informeDiarioAction()
	{

		$params = $this->_request->getParams();

		//echo "<pre>";
		//print_r($params);
		$operacion_id= $params['operacion_id'];
		$this->view->buscar = true;

		if( !empty($params['fecha_desde']) ) {
			$fecha_desde  = $params['fecha_desde'];
			$fecha_hasta = $params['fecha_hasta'];
				
			$rows = $this->_t_usuario->getPolizasInformeDiarioRango($fecha_desde,$fecha_hasta,$operacion_id);
			//$parse_rows = $this->parseaInformeDiario($rows);
			//echo "---------------------------------------------";
			$this->view->fecha_desde = $fecha_desde;
			$this->view->fecha_hasta = $fecha_hasta;
			$this->view->operacion = $operacion_id;
		}else{
			$this->view->operacion = $operacion_id;

			//saco el mes en curso(despues lo mejoro, por ahora resta 15)
			/*	$date_desde = new DateTime();
			$date_desde->sub(new DateInterval('P15D'));
			$fecha_desde =  $date_desde->format('Y-m-d');

			$date_hasta = new DateTime();
			$date_hasta->add(new DateInterval('P15D'));
			$fecha_hasta =  $date_hasta->format('Y-m-d');
			*/
			$date_desde = new DateTime();
			//$date_desde->sub(new DateInterval('P15D'));
			$fecha_pedido =  $date_desde->format('Y-m-d');
			$rows = $this->_t_usuario->getPolizasInformeDiario($fecha_pedido,$operacion_id);

			//echo "<pre>";
			//print_r($rows);
			//exit;
		}
			
		//Esto para despues
		/*
		$page=$this->_getParam('page',1);
			
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];

		$this->view->rows = $paginator;
		*/

		$this->view->informe_diario = $rows;
	}

	public function imprimirInformeDiarioAction()
	{

		$params = $this->_request->getParams();


		$this->view->buscar = true;

		if( !empty($params['fecha_desde']) ) {
			$fecha_desde  = $params['fecha_desde'];
			$fecha_hasta = $params['fecha_hasta'];

			$this->view->fecha_desde = $params['fecha_desde'];
			$this->view->fecha_hasta = $params['fecha_hasta'];
				
			$rows = $this->_t_usuario->getPolizasInformeDiarioRango($fecha_desde,$fecha_hasta);

		}else{

			$date_desde = new DateTime();
			//$date_desde->sub(new DateInterval('P15D'));
			$fecha_pedido =  $date_desde->format('Y-m-d');
			$rows = $this->_t_usuario->getPolizasInformeDiario($fecha_pedido);
		}
			
		//Esto para despues
		/*
		$page=$this->_getParam('page',1);
			
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];

		$this->view->rows = $paginator;
		*/

		$this->view->informe_diario = $rows;
	}


	private function parseaInformeDiario($rows){

		$array_temp = Array();

		foreach($rows as $key_poliza=>$value_poliza){

			if(is_array($value_poliza)){
				echo"entro a $value_poliza";
					
			}else{
				echo "guarda".$key_poliza. "con este valor".$value_poliza;
				echo "<br>";
			}


			//foreach ($value_poliza['Model_DetallePago'] as $key_detalle_pago=>$value_detalle_pago) {
			//	$array_temp[$key_detalle_pago]=$value_detalle_pago;
			//}
		}


		return $array_temp;
	}


	public function listadoProduccionAction()
	{

		$params = $this->_request->getParams();

		$this->view->buscar = false;

		if ($params['buscar']){
			$this->view->buscar = true;

			$rows = $this->_t_usuario->getPolizasListadoProduccionByMonth($params['mes'],$params['anio']);

			$imes = intval($params['mes']);
			$ianio = intval($params['anio']);

			$mes_pasado_timestamp = mktime(0, 0, 0, $imes-1, "01", $ianio);
			$mes_pasado =  date('d-m-Y',$mes_pasado_timestamp);

			$mes_actual_timestamp = mktime(0, 0, 0, $imes, "01", $ianio);
			$mes_actual =  date('d-m-Y',$mes_actual_timestamp);

			$this->view->mes_pasado = $mes_pasado;
			$this->view->mes_actual = $mes_actual;

			$this->view->mes = $params['mes'];
			$this->view->anio = $params['anio'];


		}
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

	public function imprimirListadoProduccionAction()
	{

		$params = $this->_request->getParams();



		$rows = $this->_t_usuario->getPolizasListadoProduccionByMonth($params['mes'],$params['anio']);
			
		$imes = intval($params['mes']);
		$ianio = intval($params['anio']);

		$mes_pasado_timestamp = mktime(0, 0, 0, $imes-1, "01", $ianio);
		$mes_pasado =  date('d-m-Y',$mes_pasado_timestamp);

		$mes_actual_timestamp = mktime(0, 0, 0, $imes, "01", $ianio);
		$mes_actual =  date('d-m-Y',$mes_actual_timestamp);

		$this->view->mes_pasado = $mes_pasado;
		$this->view->mes_actual = $mes_actual;

			
		$this->view->rows = $rows;
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

	public function listadoLibroOperacionesAction(){

		/*
		 * Fecha de solicitud / Tomador / Domicilio / Ubicación Riesgo / Compañia
		 * / Tipo de cobertura / Tipo de seguro (aca tenemos que ver si hace falta agregar algo)
		 *  / Suma asegurada / Vigencia del seguro / Observaciones
		 */

		$params = $this->_request->getParams();
		//echo"<pre>";
		//print_r($params);

		$this->view->buscar = false;

		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();



		if($params['buscar']){
			$this->view->numero_orden = $params['numero_inicio'];
			$this->view->anio = $params['anio'];
			$this->view->mes = $params['mes'];
			
			//echo "anio".$params['anio']."<br>";
			//echo "mes".$params['mes'];
			//exit;
			$this->view->buscar = true;
			$this->view->productor_id=$params['productor_id'];
			$listado_libro_operaciones = $this->_t_usuario->getListadoOperacionesByProductorIdAndMonth($params['productor_id'],$params['mes'],$params['anio']);
		}
		$this->view->listado_libro_operaciones = $listado_libro_operaciones;

	}


	public function listadoDeudaClienteAction(){

		/*
		 * Fecha de solicitud / Tomador / Domicilio / Ubicación Riesgo / Compañia
		 * / Tipo de cobertura / Tipo de seguro (aca tenemos que ver si hace falta agregar algo)
		 *  / Suma asegurada / Vigencia del seguro / Observaciones
		 */



		/*
		 * Busca siempre por cliente y despues puede ser por agente o compania
		 */
		$params = $this->_request->getParams();
		//echo"<pre>";
		//print_r($params);
		$this->view->asegurado_id=$params['asegurado_id'];
		//	print_r($this->view->asegurado_id);
		//exit;
		$this->view->buscar = false;

		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();

		$compania = new Domain_Compania();
		$this->view->companias = $compania->getModel()->getTable()->findAll()->toArray();

		if($params['buscar']){
			$this->view->buscar = true;

			$this->view->compania_id=$params['compania_id'];
			$this->view->agente_id=$params['agente_id'];
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($params['asegurado_id']);
			
			$listado_deuda_cliente = $this->_t_usuario->getListadoDeudaClienteByEntidadId($params['asegurado_id'],$params['agente_id'],$params['compania_id']);
		}else{
				
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($params['asegurado_id']);
			//$listado_deuda_cliente = $this->_t_usuario->getListadoDeudaCliente($params['asegurado_id']);
			//Trae los ultimos del dia
			$listado_deuda_cliente = $this->_t_usuario->getListadoDeudaCliente($params['asegurado_id']);
		}

		$this->view->listado_deuda_cliente = $listado_deuda_cliente;

	}

	public function listadoDeudaCompaniaAction(){

		/*
		 * Fecha de solicitud / Tomador / Domicilio / Ubicación Riesgo / Compañia
		 * / Tipo de cobertura / Tipo de seguro (aca tenemos que ver si hace falta agregar algo)
		 *  / Suma asegurada / Vigencia del seguro / Observaciones
		 */
		/*
		 * Busca siempre por cliente y despues puede ser por agente o compania
		 */
		$params = $this->_request->getParams();

		$this->view->compania_id=$params['compania_id'];
		//	print_r($this->view->asegurado_id);
		//echo "<pre>";
		//print_r($params);
		//exit;
		$this->view->buscar = false;

		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();

		$asegurado = new Domain_Asegurado();
		$this->view->asegurados = $asegurado->getModel()->getTable()->findAll()->toArray();

		if($params['buscar']){
			$this->view->buscar = true;

			$this->view->asegurado_id=$params['asegurado_id'];
			$this->view->agente_id=$params['agente_id'];
			$this->view->compania_nombre = Domain_Compania::getNameById($params['compania_id']);
				
			$listado_deuda_compania = $this->_t_usuario->getListadoDeudaCompaniaByEntidadId($params['compania_id'],$params['agente_id'],$params['asegurado_id']);
		}else{
			$this->view->compania_nombre = Domain_Compania::getNameById($params['compania_id']);
			$listado_deuda_compania = $this->_t_usuario->getListadoDeudaCompania($params['compania_id']);
		}

		$this->view->listado_deuda_compania = $listado_deuda_compania;

	}

	public function imprimirListadoDeudaCompaniaAction(){

		/*
		 * Fecha de solicitud / Tomador / Domicilio / Ubicación Riesgo / Compañia
		 * / Tipo de cobertura / Tipo de seguro (aca tenemos que ver si hace falta agregar algo)
		 *  / Suma asegurada / Vigencia del seguro / Observaciones
		 */
			
			 	
		/*
		 * Busca siempre por cliente y despues puede ser por agente o compania
		 */
		$params = $this->_request->getParams();


		$this->view->compania_id=$params['compania_id'];
		//	print_r($this->view->asegurado_id);
		//echo "<pre>";
		//print_r($params);
		//exit;
		$this->view->buscar = false;

		$agente = new Domain_Agente();
		$this->view->agentes= $agente->getModel()->getTable()->findAll()->toArray();

		$asegurado = new Domain_Asegurado();
		$this->view->asegurados = $asegurado->getModel()->getTable()->findAll()->toArray();


		if( isset($params['asegurado_id']) || isset($params['agente_id'])){
			$this->view->asegurado_id=$params['asegurado_id'];
			$this->view->agente_id=$params['agente_id'];
			$this->view->compania_nombre = Domain_Compania::getNameById($params['compania_id']);
				
			$listado_deuda_compania = $this->_t_usuario->getListadoDeudaCompaniaByEntidadId($params['compania_id'],$params['agente_id'],$params['asegurado_id']);
		}else{
			$this->view->compania_nombre = Domain_Compania::getNameById($params['compania_id']);
			$listado_deuda_compania = $this->_t_usuario->getListadoDeudaCompania($params['compania_id']);
		}

		$this->view->listado_deuda_compania = $listado_deuda_compania;

	}

	public function imprimirListadoDeudaClienteAction(){

		/*
		 * Fecha de solicitud / Tomador / Domicilio / Ubicación Riesgo / Compañia
		 * / Tipo de cobertura / Tipo de seguro (aca tenemos que ver si hace falta agregar algo)
		 *  / Suma asegurada / Vigencia del seguro / Observaciones
		 */
		$params = $this->_request->getParams();
		//echo"<pre>";
		//print_r($params);
		//exit;
		$this->view->asegurado_id=$params['asegurado_id'];

		if( isset($params['compania_id']) || isset($params['agente_id'])){
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($params['asegurado_id']);
			$listado_deuda_cliente = $this->_t_usuario->getListadoDeudaClienteByEntidadId($params['asegurado_id'],$params['agente_id'],$params['compania_id']);
		}else{
			$this->view->asegurado_nombre = Domain_Asegurado::getNameById($params['asegurado_id']);
			$listado_deuda_cliente = $this->_t_usuario->getListadoDeudaCliente($params['asegurado_id']);
		}

		$this->view->listado_deuda_cliente = $listado_deuda_cliente;

	}

	/* Este metodo no iria mas
	 * Uso el de abajo
	 */
	public function  imprimirListadoLibroOperacionesAction(){

		$params = $this->_request->getParams();

		$this->view->numero_orden = $params['numero_orden'];
		$this->view->productor_id=$params['productor_id'];
		//$this->view->listado_libro_operaciones = $this->_t_usuario->getListadoOperacionesByProductorIdAndMonth($params['productor_id'],$params['mes'],$params['anio']);
		$this->view->listado_libro_operaciones = $this->_t_usuario->getListadoOperacionesByProductorIdAndMonthRefacturado($params['productor_id'],$params['mes'],$params['anio']);

	}
	
	public function  xmlListadoLibroOperacionesAction(){
/*
 * Existen dos tipos de archivos para el envío de información. Uno para presentar información referida a los libros de
 * Registros de Operaciones de Seguro (ROS)
 */
		$params = $this->_request->getParams();

		$this->view->numero_orden = $params['numero_orden'];
		$numero_orden = $params['numero_orden'];
		$this->view->productor_id=$params['productor_id'];


	$listado_libro_operaciones = $this->_t_usuario->getListadoOperacionesByProductorIdAndMonth($params['productor_id'],$params['mes'],$params['anio']);
	
	$cantidad_registros = count($listado_libro_operaciones);

	$productor = new Domain_Productor($params['productor_id']);
//	echo "<pre>";
//	print_r($listado_libro_operaciones);
//	exit;
    /* create a dom document with encoding utf8 */
    $domtree = new DOMDocument('1.0', 'UTF-8');
   // $domtree = new DOMDocument('1.0', 'ISO-8859-1');

    /* creo raiz de xml */
    $xml_ssn = $domtree->createElement("SSN");
    /* agrego al documento */
    //$xmlRoot = $domtree->appendChild($xmlRoot);
	$xmlRoot = $domtree->appendChild($xml_ssn);
    //Creo elemento Cabecera
    $xml_cabecera = $domtree->createElement("Cabecera");
    $xmlRoot = $xml_ssn->appendChild($xml_cabecera);
    //Creo elemento Version
    
    //$xml_cabecera->appendChild($domtree->createElement("Version",'1'));//NOVAMAS
    $xml_productor = $domtree->createElement("Productor");
    $xml_productor->setAttribute('TipoPersona', $productor->getModel()->tipo_persona_id); 
    $xml_productor->setAttribute('Matricula', $productor->getModel()->matricula);
    $xml_productor->setAttribute('CUIT', $productor->getModel()->cuit);
    $xml_cabecera->appendChild($xml_productor);
    
    $xml_cabecera->appendChild($domtree->createElement("CantidadRegistros",$cantidad_registros));
    
    
    $xml_detalle = $xml_ssn->appendChild($domtree->createElement("Detalle"));
    
	foreach ($listado_libro_operaciones as $poliza) {
	$d_poliza = new Domain_Poliza($poliza['poliza_id']);
	$m_poliza = $d_poliza->getModelPoliza();
	$m_detalle_pago = $d_poliza->getModelDetallePago();
	$m_poliza_valores = $d_poliza->getModelPolizaValores();
	$m_poliza_detalle = $d_poliza->getModelDetalle();
		
	if( $m_poliza->estado_id == 2 || $m_poliza->estado_id == 4 || $m_poliza->estado_id == 18){
	$fecha_registro = $poliza['fecha_vigencia'];
	}else{	
	$fecha_registro = $poliza['fecha_pedido'];
	}	
	//echo $m_poliza->tipo_poliza_id;
	switch ($m_poliza->tipo_poliza_id) {
		case 6:
		$tipo_poliza_id = 36;
		break;
		
		case 3:
		$tipo_poliza_id = 38;
		break;
		case 8:
		$tipo_poliza_id = 45;
		break;
		
		default:
		$tipo_poliza_id = 39;
		break;
	}
	
 	if( $poliza['tipo_poliza_id'] <> 7 ){

        $html_riesgo = Domain_MotivoGarantia::getMotivoGarantiaByIdAndTipoPoliza($m_poliza_detalle->motivo_garantia_id,$poliza['tipo_poliza_id']);
	//$riesgo = htmlspecialchars_decode($html_riesgo) ;

	$riesgo = htmlentities($html_riesgo); 
	//$riesgo = html_entity_decode($html_riesgo,ENT_QUOTES,"ISO-8859-1") ;
        }
	
	if( $m_poliza->estado_id == 2 || $m_poliza->estado_id == 4 ){
        $observacion_tipo = 1;
        }elseif($m_poliza->estado_id == 18){
        $observacion_tipo = 2;
        }elseif($m_poliza->estado_id == 14){
        $observacion_tipo = 3;
        }elseif($m_poliza->estado_id == 7){
        $observacion_tipo = 4;
        }elseif($m_poliza->estado_id == 19){
        	$observacion_tipo = 5;
        if($m_poliza->tipo_endoso_id == 3)$observacion_tipo = 6;
        }
        
//5 Modificación de registro
//6 Anulación de registro

	$fecha_vigencia_desde = $poliza['fecha_vigencia'];
	$fecha_vigencia_hasta = $poliza['fecha_vigencia_hasta'];
	$numero_poliza = $poliza['numero_poliza'];
	$monto_asegurado = $m_poliza_valores->monto_asegurado;
	
	//echo "<pre>";
	//echo "asegurado:".$poliza['asegurado_id'];
	
	$asegurado = new Domain_Asegurado($poliza['asegurado_id']);
	$compania = new Domain_Compania($poliza['compania_id']);
	
	$asegurado_nombre = $asegurado->getModel()->nombre;
	
	$xml_registro = $xml_detalle->appendChild($domtree->createElement("Registro"));
		
    //$xml_registro->appendChild($domtree->createElement("NroOrden",$numero_orden));//NOVAMAS
    $xml_registro->appendChild($domtree->createElement("FechaRegistro",$fecha_registro));
    
    $asegurados = $domtree->createElement("Asegurados");
    
    //foreach
    $xml_asegurado = $domtree->createElement("Asegurado");
    $xml_asegurado->setAttribute('TipoAsegurado',$asegurado->getModel()->tipo_persona_id);
    $xml_asegurado->setAttribute('TipoDoc',$asegurado->getModel()->tipo_documento_id);
    $xml_asegurado->setAttribute('NroDoc',$asegurado->getModel()->numero_documento);
    $xml_asegurado->setAttribute('Nombre',$asegurado_nombre);
    $asegurados->appendChild($xml_asegurado);
    //endForeach
    $xml_registro->appendChild($asegurados);
    
    $xml_registro->appendChild($domtree->createElement("CPAProponente",$asegurado->getModel()->codigo_postal));
    $xml_registro->appendChild($domtree->createElement("ObsProponente",'Observaciones Proponente'));
    
    $xml_registro->appendChild($domtree->createElement("CPACantidad",'1'));
    
    $xml_codigo_postal = $xml_registro->appendChild($domtree->createElement("CodigosPostales"));
    $xml_codigo_postal->appendChild($domtree->createElement("CPA",$asegurado->getModel()->codigo_postal));
    $xml_registro->appendChild($domtree->createElement("CiaID",$compania->getModel()->afip_id));//falta agregar campo
	

    $xml_organizador = $domtree->createElement("Organizador");
    $xml_organizador->setAttribute('TipoPersona', $productor->getModel()->tipo_persona_id); 
    $xml_organizador->setAttribute('Matricula', $productor->getModel()->matricula);
    $xml_registro->appendChild($xml_organizador);




	$xml_registro->appendChild($domtree->createElement("BienAsegurado",$riesgo));
	$xml_registro->appendChild($domtree->createElement("Ramo",$tipo_poliza_id));
	
	//Evito todos los errores que pueda traer el "punto"
	$monto_asegurado =  str_replace(".",",",$monto_asegurado);
	
	$xml_registro->appendChild($domtree->createElement("SumaAsegurada",$monto_asegurado));
	
	$xml_registro->appendChild($domtree->createElement("SumaAseguradaTipo",'1'));

	$xml_cobertura_desde = $domtree->createElement("CoberturaFechaDesde",$fecha_vigencia_desde); //AGREGO
    $xml_cobertura_hasta = $domtree->createElement("CoberturaFechaHasta",$fecha_vigencia_hasta); //AGREGO
    $xml_registro->appendChild($xml_cobertura_desde); //MODIFICO
   	$xml_registro->appendChild($xml_cobertura_hasta);

  // 	$xml_observacion = $domtree->createElement("Observacion");
	

  // 	$xml_observacion->setAttribute('Tipo',$observacion_tipo);

  // 	if($observacion_tipo == 5 || $observacion_tipo == 6)
  //  $xml_observacion->setAttribute('NroOrdenAnulaModifica',$numero_orden);

   	//if($observacion_tipo <> 1) $xml_observacion->setAttribute('Poliza',$numero_poliza);
   	//if($observacion_tipo <> 1) 
   		
   	$xml_registro->appendChild($xml_observacion);
   	
	$xml_registro->appendChild($domtree->createElement("TipoOperacion",'2')); // MODIFICO (OperacionOrigen = 1)
    $xml_registro->appendChild($domtree->createElement("Poliza",$numero_poliza));
   	$xml_registro->appendChild($domtree->createElement("Flota",'0'));
	$xml_registro->appendChild($domtree->createElement("TipoContacto",'4'));

    $numero_orden++;
	}
    

    /* get the xml printed */
    $xml = $domtree->saveXML();

    // Output headers
    //header('Content-type: "text/xml"; charset="utf8"');
    //header('Content-disposition: attachment; filename="example.xml"');

  $this->getResponse()
     ->setHeader('Content-Disposition', 'attachment; filename=result.xml')
     ->setHeader('Content-type', 'text/xml');
    echo $domtree->saveXML();
	}

	public function pdfAction(){

		$params = $this->_request->getParams();
		$this->view->listado_libro_operaciones = $this->_t_usuario->getListadoOperacionesByProductorId($params['productor_id']);
		$this->view->numero_orden = $params['numero_orden'];
		//echo "<pre>";
		//print_r($params);

	}

	public function buscarAseguradoAction()
	{
		//$this->_helper->viewRenderer->setNoRender ();
		$solicitud = new Domain_Asegurado();

		$params = $this->_request->getParams();
		//echo "<pre>";
		//print_r($params);
		//Buscar es siempre true por ahora
		$this->view->buscar = true;


		/*if(isset($params['buscar'])){
			$rows =$this->_t_usuario->findAseguradoByNombre($params['nombre']);

			}else{
			$rows = $this->_t_usuario->getAsegurados();
			}*/
		$rows =$this->_t_usuario->findAseguradoByNombre($params['criterio']);

		$page=$this->_getParam('page',1);
			
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];

		$this->view->rows = $paginator;

	}

	public function buscarCompaniaAction()
	{
		$params = $this->_request->getParams();

		//Buscar es siempre true por ahora
		$this->view->buscar = true;

		$rows =$this->_t_usuario->findCompaniaByNombre($params['criterio']);

		$page=$this->_getParam('page',1);
			
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(50);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];

		$this->view->rows = $paginator;

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
				
			$this->view->fecha_desde = $params['fecha_desde'];
			$this->view->fecha_hasta = $params['fecha_hasta'];
				
		}else{
			//saco el mes en curso(despues lo mejoro, por ahora resta 15)
			$date_desde = new DateTime();
			$date_desde->sub(new DateInterval('P15D'));
			$fecha_desde =  $date_desde->format('Y-m-d');

			$date_hasta = new DateTime();
			$date_hasta->add(new DateInterval('P15D'));
			$fecha_hasta =  $date_hasta->format('Y-m-d');
		}

		$this->view->fecha_desde = $fecha_desde;
		$this->view->fecha_hasta = $fecha_hasta;

		$asegurado_id = ( !empty($params['asegurado_id']) )? $params['asegurado_id'] : null;

		$this->view->asegurado_id = $asegurado_id;

		$rows = $this->_t_usuario->getPolizasVencimiento($fecha_desde,$fecha_hasta,$asegurado_id);
		//$rows_cuota = $this->_t_usuario->getPolizasVencimientoCuotas($fecha_desde,$fecha_hasta,$asegurado_id);

		$this->view->rows = $rows;
	}

	public function imprimirListadoVencimientoAction()
	{

		$params = $this->_request->getParams();


		$cliente = new Domain_Cliente();
		$this->view->asegurados= $cliente->getModel()->getTable()->findAll()->toArray();

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


		$asegurado_id = ( !empty($params['asegurado_id']) )? $params['asegurado_id'] : null;
			
		$rows = $this->_t_usuario->getPolizasVencimiento($fecha_desde,$fecha_hasta,$asegurado_id);


		$this->view->fecha_desde = $fecha_desde;
		$this->view->fecha_hasta = $fecha_hasta;
		$this->view->asegurado_id =$asegurado_id;

		$this->view->rows = $rows;
	}

	public function listProductorAction()
	{
		$solicitud = new Domain_Productor();

		$params = $this->_request->getParams();

		//Buscar es siempre true por ahora
		$this->view->buscar = true;

		$rows =$this->_t_usuario->findProductorByNombre($params['criterio']);

		$page=$this->_getParam('page',1);
			
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];

		$this->view->rows = $paginator;

	}

	public function movimientosProductorAction(){
		$params = $this->_request->getParams();

		$this->view->productor = Domain_Productor::getNameById($params['productor_id']);
		//Trae el pago del asegurado
		//Seria la sumatoria de todos los pagos que hizo
		$moneda_pesos = Domain_Helper::getHelperIdByDominioAndName('moneda', 'PESOS');
		$moneda_dolar = Domain_Helper::getHelperIdByDominioAndName('moneda', 'DOLAR');
		$moneda_euro = Domain_Helper::getHelperIdByDominioAndName('moneda', 'EURO');
			
		/*
		 * @TODO Aca seria todo para el productor
		 */

		//Trae la sumatoria de todo lo que pago
		$this->view->debe_pesos = Domain_Asegurado::getDebeByAseguradoIdAndMoneda($params['asegurado_id'],$moneda_pesos);
		$this->view->debe_dolar = Domain_Asegurado::getDebeByAseguradoIdAndMoneda($params['asegurado_id'],$moneda_dolar);
		$this->view->debe_euro = Domain_Asegurado::getDebeByAseguradoIdAndMoneda($params['asegurado_id'],$moneda_euro);


		//Trae la sumatoria de todo lo que debe segun las polizas
		$this->view->pago_pesos = Domain_Asegurado::getPagoByAseguradoIdAndMoneda($params['asegurado_id'],$moneda_pesos);
		$this->view->pago_dolar = Domain_Asegurado::getPagoByAseguradoIdAndMoneda($params['asegurado_id'],$moneda_dolar);
		$this->view->pago_euro = Domain_Asegurado::getPagoByAseguradoIdAndMoneda($params['asegurado_id'],$moneda_euro);




		$this->view->suma_movimientos_pesos = Domain_Asegurado::getSumaMovimientosByAseguradoIdAndMoneda($params['asegurado_id'],$moneda_pesos);
		$this->view->suma_movimientos_dolar = Domain_Asegurado::getSumaMovimientosByAseguradoIdAndMoneda($params['asegurado_id'],$moneda_dolar);
		$this->view->suma_movimientos_euro = Domain_Asegurado::getSumaMovimientosByAseguradoIdAndMoneda($params['asegurado_id'],$moneda_euro);


		//$rows = Domain_Productor::getMovimientosByProductorId($params['asegurado_id']);
		$this->view->rows = $rows;

	}


	public function baklistadoInformeCobranzasAction(){

		$params = $this->_request->getParams();
		//echo "<pre>";
		//print_r($params);

		$this->view->buscar = false;

		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();



		if($params['buscar']){

			if(empty($params['productor_id'])){

				echo "<h4 style='color:red'>Debe Seleccionar un Productor.</h4>";
				return false;
			}
				
			$this->view->buscar = true;
				
			$productor_id = $params['productor_id'];
				
			$informe_cobranzas = array();
			$informe_cobranzas_item = array();
				
			//1. Me trae los movimientos por fecha
			$listado_movimientos = $this->_t_usuario->getMovimientosByFecha($params['fecha_desde'],$params['fecha_hasta']);
				
				
			//Filtro por productor_id
			echo "<pre>";
			print_r($listado_movimientos);

			//	}
			//	echo "<pre>";
			//Por ahora filtro aca el productor
			foreach ($listado_movimientos as $movimientos) {


				//No deberia haber un movimiento con poliza_id vacio

				$informe_cobranzas_item['fecha_registracion'] = $movimientos['fecha_pago'];
				$informe_cobranzas_item['concepto'] = '@TODO: de donde saco esto?';

				//1. Me fijo si es pago a compania o cobro a cliente
				//echo "tipo movimiento id:".$movimiento['tipo_movimiento_id']."<br>";
				switch ($movimientos['tipo_movimiento_id']) {
					case 0://DEUDA CLIENTE
							
						if(!empty($movimientos['asegurado_id'])){
							$d_cliente = new Domain_Asegurado($movimientos['asegurado_id']);
							$informe_cobranzas_item['entidad_aseguradora'] = $d_cliente->getModel()->nombre;
						}else{
							$informe_cobranzas_item['entidad_aseguradora'] ="S/N";
						}


						$importe_total = 0 ;

						$numeros_polizas = null;

						foreach ( $movimientos['Model_MovimientoPoliza'] as $movimiento){
							//print_r($movimiento);
							if(!empty($movimiento['poliza_id'])){
								//Aca puede ser en cuotas entonces el campo poliza_id es el id de la cuota
								//Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota
								$detalle_pago_id = $movimiento['poliza_id']; //Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota
								$s_poliza = Domain_Movimiento::getPolizaByDetallePagoId($detalle_pago_id);
								$d_poliza = new Domain_Poliza($s_poliza['poliza_id']);

								$m_poliza = $d_poliza->getModelPoliza();
								echo "<br>";

								//echo "Productor".$m_poliza->productor_id." y ".$params['productor_id'];
								if($m_poliza->productor_id == $params['productor_id']){

									$informe_cobranzas_item['productor_flag'] = true;
									//Esto puede servir para otra tipo de informe
									//$valor_cuota = Domain_DetallePago::getValorCuotas($s_poliza['detalle_pago_id']);

									$premio_asegurado = floatval($d_poliza->getModelPolizaValores()->premio_asegurado);
									$cantidad_cuotas = Domain_DetallePago::getCantidadCuotas($detalle_pago_id);
									//echo "<pre>";
									//echo "Premio:";
									//print_r($premio_asegurado);
									//echo "Cuotas:";
									//print_r($cantidad_cuotas);
									/*$importe = $premio_asegurado + $plus;
									$importe_total = $importe_total + $importe;
								 */
									//$importe_total = $importe_total + $valor_cuota;
									$importe_total = $importe_total + ($premio_asegurado/$cantidad_cuotas);
									//$importe_asegurado = $premio_asegurado/$cantidad_cuotas;

									$numeros_polizas = $numeros_polizas." ".$d_poliza->getModelPoliza()->numero_poliza."/".$d_poliza->getModelPoliza()->endoso;
									$plus = floatval($d_poliza->getModelPolizaValores()->plus);
										
								}
									
							}
						}
						//Me fijo si hay mas de una poliza para configurar el campo Polizas
							
						//	if(count($movimientos['Model_MovimientoPoliza']) > 1 ) $polizas_varias="Pol Varias:";

						//$informe_cobranzas_item['numero_poliza'] =$polizas_varias." ".$numeros_polizas;
						$informe_cobranzas_item['numero_poliza'] =$numeros_polizas;
						$informe_cobranzas_item['importe_egreso'] =  0 ;
						//$informe_cobranzas_item['importe_ingreso'] =$importe_total;
						$informe_cobranzas_item['importe_ingreso'] = $importe_total;

						break;

					case 1://PAGO COMPANIA

						if(!empty($movimientos['compania_id'])){
							$d_compania = new Domain_Compania($movimientos['compania_id']);
							$informe_cobranzas_item['entidad_aseguradora'] = $d_compania->getModel()->nombre;
						}else{
							$informe_cobranzas_item['entidad_aseguradora'] ="S/N";
						}

						$importe_total = 0;
						$numeros_polizas = null;
						foreach ( $movimientos['Model_MovimientoPoliza'] as $movimiento){

							if(!empty($movimiento['poliza_id'])){

								$detalle_pago_id = $movimiento['poliza_id']; //Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota
								$s_poliza = Domain_Movimiento::getPolizaByDetallePagoId($detalle_pago_id);
								$d_poliza = new Domain_Poliza($s_poliza['poliza_id']);

								$m_poliza = $d_poliza->getModelPoliza();
								if($m_poliza->productor_id == $params['productor_id']){

									$informe_cobranzas_item['productor_flag'] = true;

									$importe = floatval($d_poliza->getModelPolizaValores()->premio_compania);
									$importe_total = $importe_total + $importe;
									$numeros_polizas = $numeros_polizas." ".$d_poliza->getModelPoliza()->numero_poliza."/".$d_poliza->getModelPoliza()->endoso;

								}
							}
						}
							
						//if(count($movimientos['Model_MovimientoPoliza']) > 1 ) $polizas_varias="Pol Varias:";


						//$informe_cobranzas_item['numero_poliza'] =$polizas_varias." ".$numeros_polizas;
						$informe_cobranzas_item['numero_poliza'] =$numeros_polizas;
						$informe_cobranzas_item['importe_egreso'] = $importe_total;
						$informe_cobranzas_item['importe_ingreso'] = 0 ;
							
						break;

					default:
						exit("Ocurrio un error llame a su administrador - Algun Movimiento no tiene tipo de moviento");
						break;
				}


				$informe_cobranzas[]=$informe_cobranzas_item;

			}
				
			$informe_filtrado = array();
			echo "<pre>";
			foreach ($informe_cobranzas as $registro) {
				//					print_r($registro);
				if($registro['productor_flag'])
				$informe_filtrado[] = $registro;
			}

			print_r($informe_filtrado);
			//$this->view->informe_cobranzas = $informe_cobranzas;
			$this->view->informe_cobranzas = $informe_filtrado;
		}
	}

	public function bakDOSlistadoInformeCobranzasAction(){

		$params = $this->_request->getParams();
		//echo "<pre>";
		//print_r($params);

		$this->view->buscar = false;

		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();



		if($params['buscar']){

			if(empty($params['productor_id'])){

				echo "<h4 style='color:red'>Debe Seleccionar un Productor.</h4>";
				return false;
			}
				
			$this->view->buscar = true;
				
			$productor_id = $params['productor_id'];
				
			$informe_cobranzas = array();
			$informe_cobranzas_item = array();
				
			//1. Me trae los movimientos por fecha, sin importar de que productor sea
				
			$listado_movimientos = $this->_t_usuario->getMovimientosByFecha($params['fecha_desde'],$params['fecha_hasta']);
				
				
				
				
			foreach ($listado_movimientos as $movimientos) {

				//print_r($movimientos);
				//No deberia haber un movimiento con poliza_id vacio

				$informe_cobranzas_item['fecha_registracion'] = $movimientos['fecha_pago'];
				$informe_cobranzas_item['concepto'] = '@TODO: de donde saco esto?';

				//1. Me fijo si es pago a compania o cobro a cliente
				//echo "tipo movimiento id:".$movimiento['tipo_movimiento_id']."<br>";
				switch ($movimientos['tipo_movimiento_id']) {
					case 0://DEUDA CLIENTE
							
						if(!empty($movimientos['asegurado_id'])){
							$d_cliente = new Domain_Asegurado($movimientos['asegurado_id']);
							$informe_cobranzas_item['entidad_aseguradora'] = $d_cliente->getModel()->nombre;
						}else{
							$informe_cobranzas_item['entidad_aseguradora'] ="S/N";
						}


						$importe_total = 0 ;

						$numeros_polizas = null;

						foreach ( $movimientos['Model_MovimientoPoliza'] as $movimiento){
							//print_r($movimiento);
							if(!empty($movimiento['poliza_id'])){
								//Aca puede ser en cuotas entonces el campo poliza_id es el id de la cuota
								//Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota
								//	$detalle_pago_id = $movimiento['poliza_id']; //Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota
								//$s_poliza = Domain_Movimiento::getPolizaByDetallePagoId($detalle_pago_id);
								//	$d_poliza = new Domain_Poliza($movimientos['poliza_id']);

								//	$m_poliza = $d_poliza->getModelPoliza();

								//Esto puede servir para otra tipo de informe
								//$valor_cuota = Domain_DetallePago::getValorCuotas($s_poliza['detalle_pago_id']);

								//	$premio_asegurado = floatval($d_poliza->getModelPolizaValores()->premio_asegurado);
								//	$cantidad_cuotas = Domain_DetallePago::getCantidadCuotas($detalle_pago_id);
							 $premio_asegurado = $movimientos['premio_asegurado'];
							 $cantidad_cuotas = $movimientos['cantidad_cuotas'];
								/*$importe = $premio_asegurado + $plus;
								 $importe_total = $importe_total + $importe;
								 */
								//$importe_total = $importe_total + $valor_cuota;
								$importe_total = $importe_total + ($premio_asegurado/$cantidad_cuotas);
								//$importe_asegurado = $premio_asegurado/$cantidad_cuotas;

								//$numeros_polizas = $numeros_polizas." ".$d_poliza->getModelPoliza()->numero_poliza."/".$d_poliza->getModelPoliza()->endoso;
								$numeros_polizas = $numeros_polizas." ".$movimientos['numero_poliza']."/".$movimientos['endoso'];
								//$plus = floatval($d_poliza->getModelPolizaValores()->plus);

								$plus = floatval(  $movimientos['plus']);
									
							}
						}
						//Me fijo si hay mas de una poliza para configurar el campo Polizas
							
						//	if(count($movimientos['Model_MovimientoPoliza']) > 1 ) $polizas_varias="Pol Varias:";

						//$informe_cobranzas_item['numero_poliza'] =$polizas_varias." ".$numeros_polizas;
						$informe_cobranzas_item['numero_poliza'] =$numeros_polizas;
						$informe_cobranzas_item['importe_egreso'] =  0 ;
						//$informe_cobranzas_item['importe_ingreso'] =$importe_total;
						$informe_cobranzas_item['importe_ingreso'] = $importe_total;

						break;

					case 1://PAGO COMPANIA

						if(!empty($movimientos['compania_id'])){
							$d_compania = new Domain_Compania($movimientos['compania_id']);
							$informe_cobranzas_item['entidad_aseguradora'] = $d_compania->getModel()->nombre;
						}else{
							$informe_cobranzas_item['entidad_aseguradora'] ="S/N";
						}

						$importe_total = 0;
						$numeros_polizas = null;


							
						$importe = floatval($movimientos['premio_compania']);
						$importe_total = $importe_total + $importe;
						$numeros_polizas = $numeros_polizas." ".$movimientos['numero_poliza']."/".$movimientos['endoso'];
							
						$informe_cobranzas_item['numero_poliza'] =$numeros_polizas;
						$informe_cobranzas_item['importe_egreso'] = $importe_total;
						$informe_cobranzas_item['importe_ingreso'] = 0 ;
							
						break;

					default:
						exit("Ocurrio un error llame a su administrador - Algun Movimiento no tiene tipo de moviento");
						break;
				}

				$informe_cobranzas[]=$informe_cobranzas_item;
			}
			echo "<pre>";
			print_r($informe_cobranzas);
			$this->view->informe_cobranzas = $informe_cobranzas;
		}
	}

	public function listadoInformeCobranzasAction(){

		$params = $this->_request->getParams();
		//echo "<pre>";
		//print_r($params);


		//Parametros para la impresion
		$this->view->productor_id = $params['productor_id'];
		$this->view->fecha_desde = $params['fecha_desde'];
		$this->view->fecha_hasta = $params['fecha_hasta'];

		$this->view->buscar = false;

		$productor = new Domain_Productor();
		$this->view->productores= $productor->getModel()->getTable()->findAll()->toArray();



		if($params['buscar']){

			if(empty($params['productor_id'])){

				echo "<h4 style='color:red'>Debe Seleccionar un Productor.</h4>";
				return false;
			}
				
			$this->view->buscar = true;
				
			$productor_id = $params['productor_id'];
				
			$informe_cobranzas = array();
			$informe_cobranzas_item = array();
				
			//1. Me trae los movimientos por fecha
			$listado_movimientos = $this->_t_usuario->getMovimientosByFecha($params['fecha_desde'],$params['fecha_hasta']);
				
				
				
			//	echo "<pre>";
			//	print_r($listado_movimientos);


			/*
			 * Recorro el array.
			 * Tengo dos posibilidades:
			 * 1- que sea un movimiento de pago a cliente
			 * Aca tengo que considerar las cuotas
			 * 2- que sea un movimiento de pago a compania
			 */
			//echo "<pre>";
			//print_r($listado_movimientos);
			foreach ($listado_movimientos as $movimientos) {
				//print_r($movimientos);
				//$informe_cobranzas_item['fecha_registracion'] = $movimientos['fecha_pago'];
							 
				$informe_cobranzas_item['fecha_registracion'] = $movimientos['fecha_pago'];
				
				//$informe_cobranzas_item['concepto'] = 'Pago Cli 1/1';
				//1. Me fijo si es pago a compania o cobro a cliente
				//echo "tipo movimiento id:".$movimiento['tipo_movimiento_id']."<br>";
				switch ($movimientos['tipo_movimiento_id']) {
					case 0://DEUDA CLIENTE

						$informe_cobranzas_item['tipo_movimiento'] ='0';
						$informe_cobranzas_item['concepto'] = 'Pago Cli 1/1';

						/*if(!empty($movimientos['asegurado_id'])){
							$d_cliente = new Domain_Asegurado($movimientos['asegurado_id']);
							$informe_cobranzas_item['entidad_aseguradora'] = $d_cliente->getModel()->nombre;
						}else{
							$informe_cobranzas_item['entidad_aseguradora'] ="S/N";
						}*/

							
						$importe_total = 0 ;

						$numeros_polizas = null;
						/*
						 * Recorro los movimientos si es que tiene mas de uno
						 */
						foreach ( $movimientos['Model_MovimientoPoliza'] as $movimiento){
								
							if(!empty($movimiento['poliza_id'])){
								//Aca puede ser en cuotas entonces el campo poliza_id es el id de la cuota
								//Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota
								$detalle_pago_id = $movimiento['poliza_id']; //Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota
								$s_poliza = Domain_Movimiento::getPolizaByDetallePagoId($detalle_pago_id);
								$d_poliza = new Domain_Poliza($s_poliza['poliza_id']);
								//Traigo la poliza
								$m_poliza = $d_poliza->getModelPoliza();
									
							if(!empty($m_poliza->compania_id)){
							$d_compania = new Domain_Compania($m_poliza->compania_id);
							$informe_cobranzas_item['entidad_aseguradora'] = $d_compania->getModel()->nombre;
							}else{
							$informe_cobranzas_item['entidad_aseguradora'] ="S/N";
							}
								//	echo "<br>";

								//echo "Productor".$m_poliza->productor_id." y ".$params['productor_id'];
								/*Pregunto si esta poliza es del productor
								*Si es suma al importe
								*/
								if($m_poliza->productor_id == $params['productor_id']){
									//Esto puede servir para otra tipo de informe
									//$valor_cuota = Domain_DetallePago::getValorCuotas($s_poliza['detalle_pago_id']);

									$premio_asegurado = floatval($d_poliza->getModelPolizaValores()->premio_asegurado);
									$cantidad_cuotas = Domain_DetallePago::getCantidadCuotas($detalle_pago_id);
									//echo "<pre>";
									//echo "Premio:";
									//print_r($premio_asegurado);
									//echo "Cuotas:";
									//print_r($cantidad_cuotas);
									/*$importe = $premio_asegurado + $plus;
									$importe_total = $importe_total + $importe;
								 */
									//$importe_total = $importe_total + $valor_cuota;
									$importe_total = $importe_total + ($premio_asegurado/$cantidad_cuotas);
									//$importe_asegurado = $premio_asegurado/$cantidad_cuotas;

									$numeros_polizas = $numeros_polizas." ".$d_poliza->getModelPoliza()->numero_poliza."/".$d_poliza->getModelPoliza()->endoso;
									$plus = floatval($d_poliza->getModelPolizaValores()->plus);
										
								}
									
							}
						}
						//Me fijo si hay mas de una poliza para configurar el campo Polizas
							
						//	if(count($movimientos['Model_MovimientoPoliza']) > 1 ) $polizas_varias="Pol Varias:";

						//Si es mas de uno le pongo Polizas Varias
						$informe_cobranzas_item['numero_poliza'] = $numeros_polizas;
						if( count($movimientos['Model_MovimientoPoliza']) > 1 ) $informe_cobranzas_item['numero_poliza'] ='Pólizas varias';
						//$informe_cobranzas_item['numero_poliza'] =$polizas_varias." ".$numeros_polizas;
						
						$informe_cobranzas_item['importe_egreso'] =  0 ;
						//$informe_cobranzas_item['importe_ingreso'] =$importe_total;
						$informe_cobranzas_item['importe_ingreso'] = $importe_total;

						break;

					case 1://PAGO COMPANIA
						//echo "tipo 1";
						//print_r($movimientos);
						$informe_cobranzas_item['tipo_movimiento'] ='1';
						$concepto = 'Rendición '.$movimiento['movimiento_id'].'/1';;
						$informe_cobranzas_item['concepto'] = $concepto;

						 
						if(!empty($movimientos['compania_id'])){
							$d_compania = new Domain_Compania($movimientos['compania_id']);
							$informe_cobranzas_item['entidad_aseguradora'] = $d_compania->getModel()->nombre;
						}else{
							$informe_cobranzas_item['entidad_aseguradora'] ="S/N";
						}

						$importe_total = 0;
						$numeros_polizas = null;
						//En el caso de ser tipo movimiento pago a compania si va el id de la poliza
						foreach ( $movimientos['Model_MovimientoPoliza'] as $movimiento){

							if(!empty($movimiento['poliza_id'])){

								$detalle_pago_id = $movimiento['poliza_id']; //Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota

								$d_poliza = new Domain_Poliza($movimiento['poliza_id']);


								$m_poliza = $d_poliza->getModelPoliza();
									
								if($m_poliza->productor_id == $params['productor_id']){


									$importe = floatval($d_poliza->getModelPolizaValores()->premio_compania);
									$importe_total = $importe_total + $importe;
									//$numeros_polizas = $numeros_polizas." ".$d_poliza->getModelPoliza()->numero_poliza."/".$d_poliza->getModelPoliza()->endoso;
									$numeros_polizas = $numeros_polizas." ".$m_poliza->numero_poliza."/".$m_poliza->endoso;

								}
							}
						}
							
						//if(count($movimientos['Model_MovimientoPoliza']) > 1 ) $polizas_varias="Pol Varias:";


						//$informe_cobranzas_item['numero_poliza'] =$polizas_varias." ".$numeros_polizas;
						
						//Si es mas de uno le pongo Polizas Varias
						$informe_cobranzas_item['numero_poliza'] = $numeros_polizas;
						if( count($movimientos['Model_MovimientoPoliza']) > 1 ) $informe_cobranzas_item['numero_poliza'] ='Pólizas varias';
						
						$informe_cobranzas_item['importe_egreso'] = $importe_total;
						$informe_cobranzas_item['importe_ingreso'] = 0 ;
							
						break;

					default:
						exit("Ocurrio un error llame a su administrador - Algun Movimiento no tiene tipo de moviento");
						break;
				}


				$informe_cobranzas[]=$informe_cobranzas_item;

			}
				
			$informe_filtrado = array();
			//echo "<pre>";
			//	print_r($informe_cobranzas);
			//Pregunto que tipo de importe tengo que controlar
			foreach ($informe_cobranzas as $registro) {
				//					print_r($registro);
				if($registro['tipo_movimiento']==0){

					if($registro['importe_ingreso']>0)
					$informe_filtrado[] = $registro;

				}elseif($registro['tipo_movimiento']==1){

					if($registro['importe_egreso']>0)
					$informe_filtrado[] = $registro;
				}
					
			}

//echo "<pre>";
//print_r($informe_filtrado);

asort($informe_filtrado);	

			//$this->view->informe_cobranzas = $informe_cobranzas;
			$this->view->informe_cobranzas = $informe_filtrado;
		}
	}



	public function imprimirListadoInformeCobranzasAction(){

		$params = $this->_request->getParams();
		//echo "<pre>";
		//print_r($params);


		//Parametros para la impresion
		$this->view->productor_id = $params['productor_id'];
		$this->view->fecha_desde = $params['fecha_desde'];
		$this->view->fecha_hasta = $params['fecha_hasta'];



		$this->view->productor_nombre = Domain_Productor::getNameById($params['productor_id']);

		if(empty($params['productor_id'])){

			echo "<h4 style='color:red'>Error, no hay productor.</h4>";
			return false;
		}
			
			
		$productor_id = $params['productor_id'];
			
		$informe_cobranzas = array();
		$informe_cobranzas_item = array();
			
		//1. Me trae los movimientos por fecha
		$listado_movimientos = $this->_t_usuario->getMovimientosByFecha($params['fecha_desde'],$params['fecha_hasta']);
			
			
		/*
		 * Recorro el array.
		 * Tengo dos posibilidades:
		 * 1- que sea un movimiento de pago a cliente
		 * Aca tengo que considerar las cuotas
		 * 2- que sea un movimiento de pago a compania
		 */
			
		foreach ($listado_movimientos as $movimientos) {

			$informe_cobranzas_item['fecha_registracion'] = $movimientos['fecha_pago'];
			//$informe_cobranzas_item['concepto'] = 'Pago Cli 1/1';
			//1. Me fijo si es pago a compania o cobro a cliente
			//echo "tipo movimiento id:".$movimiento['tipo_movimiento_id']."<br>";
			switch ($movimientos['tipo_movimiento_id']) {
				case 0://DEUDA CLIENTE

					$informe_cobranzas_item['tipo_movimiento'] ='0';
					$informe_cobranzas_item['concepto'] = 'Pago Cli 1/1';

				/*	if(!empty($movimientos['asegurado_id'])){
						$d_cliente = new Domain_Asegurado($movimientos['asegurado_id']);
						$informe_cobranzas_item['entidad_aseguradora'] = $d_cliente->getModel()->nombre;
					}else{
						$informe_cobranzas_item['entidad_aseguradora'] ="S/N";
					}
*/
	
					$importe_total = 0 ;

					$numeros_polizas = null;
					/*
					 * Recorro los movimientos si es que tiene mas de uno
					 */
					foreach ( $movimientos['Model_MovimientoPoliza'] as $movimiento){
							
						if(!empty($movimiento['poliza_id'])){
							//Aca puede ser en cuotas entonces el campo poliza_id es el id de la cuota
							//Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota
								$detalle_pago_id = $movimiento['poliza_id']; //Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota
								$s_poliza = Domain_Movimiento::getPolizaByDetallePagoId($detalle_pago_id);
								$d_poliza = new Domain_Poliza($s_poliza['poliza_id']);
								//Traigo la poliza
								$m_poliza = $d_poliza->getModelPoliza();
									
							if(!empty($m_poliza->compania_id)){
							$d_compania = new Domain_Compania($m_poliza->compania_id);
							$informe_cobranzas_item['entidad_aseguradora'] = $d_compania->getModel()->nombre;
							}else{
							$informe_cobranzas_item['entidad_aseguradora'] ="S/N";
							}
							if($m_poliza->productor_id == $params['productor_id']){
								//Esto puede servir para otra tipo de informe
								//$valor_cuota = Domain_DetallePago::getValorCuotas($s_poliza['detalle_pago_id']);

								$premio_asegurado = floatval($d_poliza->getModelPolizaValores()->premio_asegurado);
								$cantidad_cuotas = Domain_DetallePago::getCantidadCuotas($detalle_pago_id);
								//echo "<pre>";
								//echo "Premio:";
								//print_r($premio_asegurado);
								//echo "Cuotas:";
								//print_r($cantidad_cuotas);
								/*$importe = $premio_asegurado + $plus;
								$importe_total = $importe_total + $importe;
								*/
								//$importe_total = $importe_total + $valor_cuota;
								$importe_total = $importe_total + ($premio_asegurado/$cantidad_cuotas);
								//$importe_asegurado = $premio_asegurado/$cantidad_cuotas;

								$numeros_polizas = $numeros_polizas." ".$d_poliza->getModelPoliza()->numero_poliza."/".$d_poliza->getModelPoliza()->endoso;
								$plus = floatval($d_poliza->getModelPolizaValores()->plus);
									
							}
								
						}
					}
					//Me fijo si hay mas de una poliza para configurar el campo Polizas
						
					//	if(count($movimientos['Model_MovimientoPoliza']) > 1 ) $polizas_varias="Pol Varias:";

					//$informe_cobranzas_item['numero_poliza'] =$polizas_varias." ".$numeros_polizas;
					//Si es mas de uno le pongo Polizas Varias
						$informe_cobranzas_item['numero_poliza'] = $numeros_polizas;
						if( count($movimientos['Model_MovimientoPoliza']) > 1 ) $informe_cobranzas_item['numero_poliza'] ='Pólizas varias';
					$informe_cobranzas_item['importe_egreso'] =  0 ;
					//$informe_cobranzas_item['importe_ingreso'] =$importe_total;
					$informe_cobranzas_item['importe_ingreso'] = $importe_total;

					break;

				case 1://PAGO COMPANIA
					//echo "tipo 1";
					//print_r($movimientos);
					$informe_cobranzas_item['tipo_movimiento'] ='1';
					$concepto = 'Rendición '.$movimiento['movimiento_id'].'/1';
					$informe_cobranzas_item['concepto'] = $concepto;

				if(!empty($movimientos['compania_id'])){
							$d_compania = new Domain_Compania($movimientos['compania_id']);
							$informe_cobranzas_item['entidad_aseguradora'] = $d_compania->getModel()->nombre;
				}else{
							$informe_cobranzas_item['entidad_aseguradora'] ="S/N";
				}
						
					$importe_total = 0;
					$numeros_polizas = null;
					//En el caso de ser tipo movimiento pago a compania si va el id de la poliza
					foreach ( $movimientos['Model_MovimientoPoliza'] as $movimiento){

						if(!empty($movimiento['poliza_id'])){

							$detalle_pago_id = $movimiento['poliza_id']; //Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota

							$d_poliza = new Domain_Poliza($movimiento['poliza_id']);

							$m_poliza = $d_poliza->getModelPoliza();

							if($m_poliza->productor_id == $params['productor_id']){


								$importe = floatval($d_poliza->getModelPolizaValores()->premio_compania);
								$importe_total = $importe_total + $importe;
								//$numeros_polizas = $numeros_polizas." ".$d_poliza->getModelPoliza()->numero_poliza."/".$d_poliza->getModelPoliza()->endoso;
								$numeros_polizas = $numeros_polizas." ".$m_poliza->numero_poliza."/".$m_poliza->endoso;

							}
						}
					}
						
					//if(count($movimientos['Model_MovimientoPoliza']) > 1 ) $polizas_varias="Pol Varias:";


					//$informe_cobranzas_item['numero_poliza'] =$polizas_varias." ".$numeros_polizas;
					//Si es mas de uno le pongo Polizas Varias
						$informe_cobranzas_item['numero_poliza'] = $numeros_polizas;
						if( count($movimientos['Model_MovimientoPoliza']) > 1 ) $informe_cobranzas_item['numero_poliza'] ='Pólizas varias';
					$informe_cobranzas_item['importe_egreso'] = $importe_total;
					$informe_cobranzas_item['importe_ingreso'] = 0 ;
						
					break;

				default:
					exit("Ocurrio un error llame a su administrador - Algun Movimiento no tiene tipo de moviento");
					break;
			}


			$informe_cobranzas[]=$informe_cobranzas_item;

		}
			
		$informe_filtrado = array();
		//echo "<pre>";
		//	print_r($informe_cobranzas);
		//Pregunto que tipo de importe tengo que controlar
		foreach ($informe_cobranzas as $registro) {
			//					print_r($registro);
			if($registro['tipo_movimiento']==0){

				if($registro['importe_ingreso']>0)
				$informe_filtrado[] = $registro;

			}elseif($registro['tipo_movimiento']==1){

				if($registro['importe_egreso']>0)
				$informe_filtrado[] = $registro;
			}
				
		}


asort($informe_filtrado);
		//$this->view->informe_cobranzas = $informe_cobranzas;
		$this->view->informe_cobranzas = $informe_filtrado;

	}

	public function xmlListadoInformeCobranzasAction(){

		$params = $this->_request->getParams();
	/*
<?xml version='1.0' encoding='utf-8' ?>
<SSN>
<Cabecera>
<Version>1</Version>
<Productor TipoPersona="1" Matricula="69083"
CUIT="20361584792"/>
<CantidadRegistros>1</CantidadRegistros>
</Cabecera>
<Detalle>
<Registro>
<TipoRegistro>2</TipoRegistro>
<FechaRegistro>2011-12-05</FechaRegistro>
<Concepto>Cuota No 1/6. Recibo No 7878</Concepto>
<Polizas>
<Poliza>10212</Poliza>
<Poliza>10213</Poliza>
</Polizas>
<CiaID></CiaID>
<Organizador TipoPersona="1" Matricula="12345"
CUIT"20361584792"/>
<Importe>1410</Importe>
<ImporteTipo>1</ImporteTipo>
</Registro>
 </Detalle>
 </SSN>
 	 */
		//Parametros 
		$this->view->productor_id = $params['productor_id'];
		$this->view->fecha_desde = $params['fecha_desde'];
		$this->view->fecha_hasta = $params['fecha_hasta'];
		
		//Me trae el listado de movimientos
		$listado_movimientos = $this->_t_usuario->getMovimientosByFecha($params['fecha_desde'],$params['fecha_hasta']);
		asort($listado_movimientos);
		//echo "<pre>";
		//print_r($listado_movimientos);
		//exit;
		$productor = new Domain_Productor($params['productor_id']);

		//Empiezo con el XML 
 	   $domtree = new DOMDocument('1.0', 'UTF-8');
	   $xml_ssn = $domtree->createElement("SSN");    
	   $xmlRoot = $domtree->appendChild($xml_ssn);
       $xml_cabecera = $domtree->createElement("Cabecera");
       $xmlRoot = $xml_ssn->appendChild($xml_cabecera);
    
       //Creo elemento Version
        //$xml_cabecera->appendChild($domtree->createElement("Version",'1'));
        $xml_productor = $domtree->createElement("Productor");
        $xml_productor->setAttribute('TipoPersona', $productor->getModel()->tipo_persona_id); 
        $xml_productor->setAttribute('Matricula', $productor->getModel()->matricula);
//    	$xml_productor->setAttribute('CUIT', $productor->getModel()->cuit);
    	$xml_cabecera->appendChild($xml_productor);
        $cantidad_registros = count($listado_movimientos);
    	$xml_cabecera->appendChild($domtree->createElement("CantidadRegistros",$cantidad_registros));
    
    
    $xml_detalle = $xml_ssn->appendChild($domtree->createElement("Detalle"));
    
	foreach ($listado_movimientos as $movimiento) {
		
	
	$tipo_registro = ($movimiento['tipo_movimiento_id'] == 0)? 1 : 2;



	$fecha_registro = $movimiento['fecha_pago'];
	
	$xml_registro = $xml_detalle->appendChild($domtree->createElement("Registro"));
		
    $xml_registro->appendChild($domtree->createElement("TipoRegistro",$tipo_registro));
    $xml_registro->appendChild($domtree->createElement("FechaRegistro",$fecha_registro));
	if($tipo_registro == 1){
		$concepto = 'Pago Cli 1/1';
	}else{
	$concepto = 'Rendición '.$movimiento['movimiento_id'].'/1';
	}
    
    $xml_registro->appendChild($domtree->createElement("Concepto",$concepto));
    $polizas = $domtree->createElement("Polizas");
    foreach ( $movimiento['Model_MovimientoPoliza'] as $poliza){
					
    //foreach
   // $xml_poliza = $domtree->createElement("Poliza");
    
   
    if($movimiento['tipo_movimiento_id']== 0){ //Si el tipo de moviento es 0, el id es el de la cuota.
    	
    	$detalle_pago_id = $poliza['poliza_id']; //Hago esto para que se sepa que no es poliza_id sino el detalle de pago cuota
		$s_poliza = Domain_Movimiento::getPolizaByDetallePagoId($detalle_pago_id);
		$d_poliza = new Domain_Poliza($s_poliza['poliza_id']);
		//Traigo la poliza
		$m_poliza = $d_poliza->getModelPoliza();
 	    $numero_poliza = $m_poliza->numero_poliza . '/' . $m_poliza->endoso;
        $xml_poliza = $domtree->createElement("Poliza",$numero_poliza);
//		$xml_poliza->setAttribute('Poliza',$numero_poliza);
		//$concepto = 'Pago Cli 1/1';
	$compania = new Domain_Compania($m_poliza->compania_id);
    }else{
    	
		$d_poliza = new Domain_Poliza($poliza['poliza_id']);
	
		$m_poliza = $d_poliza->getModelPoliza();
		
    	$numero_poliza = $m_poliza->numero_poliza . '/' . $m_poliza->endoso;
//		$xml_poliza->setAttribute('Poliza',$numero_poliza);
    $xml_poliza = $domtree->createElement("Poliza",$numero_poliza);

	$compania = new Domain_Compania($m_poliza->compania_id);
    }
    
    $polizas->appendChild($xml_poliza);
    
    }//endForeach

    //$xml_registro->appendChild($domtree->createElement("Concepto",''));
    $xml_registro->appendChild($polizas);
    
    
    
    $xml_registro->appendChild($domtree->createElement("CiaID",$compania->getModel()->afip_id));//falta agregar campo
	
//	$xml_organizador = $domtree->createElement("Organizador");
//    $xml_organizador->setAttribute('TipoPersona','');
//    $xml_organizador->setAttribute('Matricula','');
//    $xml_organizador->setAttribute('CUIT','');
//   	$xml_registro->appendChild($xml_organizador);

    //Evito todos los errores que pueda traer el "punto"
    $importe = round($movimiento['importe'],2);
	$importe =  str_replace(".",",",$importe);
   	
   	$xml_registro->appendChild($domtree->createElement("Importe",$importe));
	$xml_registro->appendChild($domtree->createElement("ImporteTipo",$movimiento['moneda_id']));
	//$xml_registro->appendChild($domtree->createElement("NroRegistroAnulaModifica"));
   
	}
	
    $xml = $domtree->saveXML();

   $this->getResponse()
   ->setHeader('Content-Disposition', 'attachment; filename=result.xml')
   ->setHeader('Content-type', 'text/xml');
   
    echo $domtree->saveXML();
	}
	
}


