<?php
require_once ('IndexController.php');

class Operaciones_CuentaCorrienteController extends Operaciones_IndexController
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

		if( $this->_usuario->getAcl()->hasPermission('solicitud') ) {

			//exit('tiene permiso');
		} else {
			//exit('no tiene permiso');
		}
		$arr_perfil_id = $this->_usuario->getUserPerfilTemp();
		$this->view->perfil_id = $arr_perfil_id[0];

		$logger = Zend_Registry::get('logger');
		$logger->log($this->_usuario->getEntidad() , Zend_Log::INFO);
			
	}

	public function indexAction()
	{
		$this->_forward('list','solicitud','poliza');

	}

	public function listAction()
	{
		$solicitud = new Domain_Asegurado();

		$params = $this->_request->getParams();
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
	
	public function listCompaniaAction()
	{
		$solicitud = new Domain_Compania();

		$params = $this->_request->getParams();
		//Buscar es siempre true por ahora
		$this->view->buscar = true;
		
		$rows =$this->_t_usuario->findCompaniaByNombre($params['criterio']);

		$page=$this->_getParam('page',1);
			
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->criterio = $params['criterio'];

		$this->view->rows = $paginator;

	}
	
	public function ccAseguradoAction(){
		$params = $this->_request->getParams();
		
		//echo "<pre>";
		//print_r($params);
	   //Domain_Movimiento::getPolizasByMovimiento(125);
		//Trae el nombre del asegurado
		$this->view->asegurado = Domain_Asegurado::getNameById($params['asegurado_id']);
		$this->view->asegurado_id = $params['asegurado_id'];
		//Trae el pago del asegurado
		//Seria la sumatoria de todos los pagos que hizo
		$moneda_pesos = Domain_Helper::getHelperIdByDominioAndName('moneda', 'PESOS');
		$moneda_dolar = Domain_Helper::getHelperIdByDominioAndName('moneda', 'DOLAR');
		$moneda_euro = Domain_Helper::getHelperIdByDominioAndName('moneda', 'EURO');
		 
		
		
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
		

					//if($params['busqueda_poliza']){
			if($params['numero_poliza']!=''){
				//echo "<pre>";
				//print_r($params);
				
				$this->view->busqueda_numero_poliza = $params['numero_poliza'];

				$rows = Domain_Asegurado::getMovimientosByAseguradoIdAndPoliza($params['asegurado_id'],$params['numero_poliza']);


				}else{

					$rows = Domain_Asegurado::getMovimientosByAseguradoId($params['asegurado_id']);
				}
		

		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->rows = $paginator;

		//$this->view->rows = $rows; 
	}
	
	
	
	
public function detalleMovimientoAction(){
		$params = $this->_request->getParams();
		
		//echo "<pre>";
		//print_r($params);
	    $rows = Domain_Movimiento::getPolizasByMovimiento($params['movimiento_id']);
		//print_r($rows);

		
		$array_detalle_poliza = $rows[0]['Model_MovimientoPoliza'];
		$polizas_result = array();
	    //Trae el nombre del asegurado
	    foreach ($array_detalle_poliza as $value) {

	    	$d = Domain_Movimiento::getPolizaByDetallePagoId($value['poliza_id']);
	    	$polizas_result[]=$d;
	    }
	    /*print_r($polizas_result);
		exit;*/
		$this->view->rows = $polizas_result; 
		
	}
	
	public function deudaAseguradoAction(){
		//Traigo las polizas del asegurado
		$params = $this->_request->getParams();
		//Me trae las polizas del asegurado por ahora todas
		$rows = $this->_t_usuario->getPolizaByAsegurado($params['asegurado_id']);
		$this->view->monedas = Domain_Helper::getHelperByDominio('moneda');

		$this->view->polizas = $rows;
		//$this->view->asegurado_id = $params['asegurado_id'];
		//echo "<pre>";
		//print_r($rows);

	}
	
	
	/*echo "<pre>";
		print_r($params);
		echo "El importe que puso es este:".$params['importe'];
		if(!empty($params['array_polizas'])){
		$id_pago_poliza = explode(',',$params['array_polizas']);

		foreach($id_pago_poliza as $id_cuota_poliza){
		echo "<br>Paga esta parte de la poliza".$id_cuota_poliza;

		//Marca como pagada
		//	$m_detalle_pago_cuota = new Model_DetallePagoCuota();
		//	$res = $m_detalle_pago_cuota->getTable()
		//	->createQuery()//->toArray();
		//->where("detalle_pago_cuota_id = ?",$id_cuota_poliza)
		//->execute();
		//->toArray();
		//->getSqlQuery();
		//$detalle = $res->getFirst();
		//	$detalle->pago = 1;
		//$detalle->save();
			
		//print_r($res->getFirst()->detalle_pago_cuota_id);
		//print_r($m_detalle_pago_cuota);
		//echo " y esta:".$id_cuota_poliza;
		}
		}*/



	public function pagarDeudaAseguradoAction(){

		$params = $this->_request->getParams();

		//aca tiene que pasar el parametro de "pagar" o algo parecido
		if(!empty($params['array_polizas'])){
			
			
			//1. Guardo el movimiento del pago
			$m_movimiento = new Model_Movimiento();
			$m_movimiento->importe = $params['importe'];
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
	
	/**
	 * Movimientos y Pagos a compania
	 * @method ccCompaniaAction
	 */
	
	public function ccCompaniaAction(){
		$params = $this->_request->getParams();
		//echo"<pre>";
		//print_r($params);

		//Trae el nombre del asegurado
		$this->view->compania = Domain_Compania::getNameById($params['compania_id']);
		$this->view->compania_id = $params['compania_id'];
		//Seria la sumatoria de todos los pagos que hizo
		$moneda_pesos = Domain_Helper::getHelperIdByDominioAndName('moneda', 'PESOS');
		$moneda_dolar = Domain_Helper::getHelperIdByDominioAndName('moneda', 'DOLAR');
		$moneda_euro = Domain_Helper::getHelperIdByDominioAndName('moneda', 'EURO');
		 
		
		
		//Trae la sumatoria de todo lo que debe segun las polizas
		$this->view->debe_pesos = Domain_Compania::getDebePremioCompaniaByCompaniaIdAndMoneda($params['compania_id'],$moneda_pesos);
		$this->view->debe_dolar = Domain_Compania::getDebePremioCompaniaByCompaniaIdAndMoneda($params['compania_id'],$moneda_dolar);
		$this->view->debe_euro = Domain_Compania::getDebePremioCompaniaByCompaniaIdAndMoneda($params['compania_id'],$moneda_euro);
		
		
		
		//Trae la sumatoria de todo lo que pago
	    $this->view->pago_pesos = Domain_Compania::getPagoPremioCompaniaByCompaniaIdAndMoneda($params['compania_id'],$moneda_pesos);
		$this->view->pago_dolar = Domain_Compania::getPagoPremioCompaniaByCompaniaIdAndMoneda($params['compania_id'],$moneda_dolar);
		$this->view->pago_euro = Domain_Compania::getPagoPremioCompaniaByCompaniaIdAndMoneda($params['compania_id'],$moneda_euro);
	

		//Si es Compania que muestre los ultimos dos meses
		//Arreglo rapido
		$es_compania = ($this->_usuario->getTipoUsuario()->getNombre() == 'COMPANIA')? 1 : 0;
		
			if($params['numero_poliza']!=''){

					$rows = Domain_Compania::getMovimientosByCompaniaIdAndPoliza($params['compania_id'],$params['numero_poliza'],$es_compania);


			}else{
					$rows = Domain_Compania::getMovimientosByCompaniaId($params['compania_id'],$es_compania);
			}

		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);
		$this->view->rows = $paginator;
		//$this->view->rows = $rows; 
		
	}
	
	public function detalleMovimientoCompaniaAction(){
		$params = $this->_request->getParams();
		
		//echo "<pre>";
		//print_r($params);
	    $rows = Domain_Movimiento::getPolizasByMovimiento($params['movimiento_id']);
		
		
		$array_detalle_poliza = $rows[0]['Model_MovimientoPoliza'];
		
		
		$polizas_result = array();
	    //Trae el nombre del asegurado
	    foreach ($array_detalle_poliza as $value) {

	    	
	    	$polizas_result[]=$value['poliza_id'];
    	
	    }
	    $this->view->rows = $polizas_result; 
		
	}

	public function imprimirDetalleMovimientoCompaniaAction(){
			$params = $this->_request->getParams();
		
		//echo "<pre>";
		//print_r($params);
	    $rows = Domain_Movimiento::getPolizasByMovimiento($params['movimiento_id']);
		
		$d_movimiento = new Domain_Movimiento($params['movimiento_id']);
		
		$this->view->m_movimiento = $d_movimiento->getModel();
		$this->view->datos_cheques = Domain_Movimiento::getDatosCheques($params['movimiento_id']);

	

		$array_detalle_poliza = $rows[0]['Model_MovimientoPoliza'];
		
		
		$polizas_result = array();
	    //Trae el nombre del asegurado
	    foreach ($array_detalle_poliza as $value) {

	    	
	    	$polizas_result[]=$value['poliza_id'];
    	
	    }
	    $this->view->rows = $polizas_result; 
	}
	public function impresionDetalleMovimientoCompaniaAction(){
		$params = $this->_request->getParams();
		
		//echo "<pre>";
		//print_r($params);
	    $rows = Domain_Movimiento::getPolizasByMovimiento($params['movimiento_id']);
		
		$d_movimiento = new Domain_Movimiento($params['movimiento_id']);
		
		$this->view->m_movimiento = $d_movimiento->getModel();
		$this->view->datos_cheques = Domain_Movimiento::getDatosCheques($params['movimiento_id']);

	

		$array_detalle_poliza = $rows[0]['Model_MovimientoPoliza'];
		
		
		$polizas_result = array();
	    //Trae el nombre del asegurado
	    foreach ($array_detalle_poliza as $value) {

	    	
	    	$polizas_result[]=$value['poliza_id'];
    	
	    }
	    $this->view->rows = $polizas_result; 

	}

	public function eliminarMovimientoCompaniaAction(){
		$params = $this->_request->getParams();
		$this->view->compania_id = $params['compania_id'];
	
		$polizas = Domain_Movimiento::getMovimientosPoliza($params['movimiento_id']);

		foreach($polizas as $poliza){
		Domain_Poliza::setPago($poliza['poliza_id']);
		}

		//Elimino movimientos de la tabla movimiento Poliza
		Domain_Movimiento::eliminarMovimientosPoliza($params['movimiento_id']);

		$d_movimiento = new Domain_Movimiento($params['movimiento_id']);
		
		$m_movimiento = $d_movimiento->getModel();
		$m_movimiento->delete();

	}

	public function imprimirDetalleMovimientoAseguradoAction(){
		$params = $this->_request->getParams();
		//echo "<pre>";
	//	print_r($params);

	    $rows = Domain_Movimiento::getPolizasByMovimiento($params['movimiento_id']);
		
		$d_movimiento = new Domain_Movimiento($params['movimiento_id']);
		
		$this->view->m_movimiento = $d_movimiento->getModel();
		$this->view->datos_cheques = Domain_Movimiento::getDatosCheques($params['movimiento_id']);

		$array_detalle_poliza = $rows[0]['Model_MovimientoPoliza'];
		
		//echo "<pre>";
		//print_r($array_detalle_poliza);
		$polizas_result = array();
	    //Trae el nombre del asegurado
	    foreach ($array_detalle_poliza as $value) {
	    	//print_r($value);

	    	$d = Domain_Movimiento::getPolizaByDetallePagoId($value['poliza_id']);
	    	$polizas_result[]=$d;
    	
	    }
	    $this->view->rows = $polizas_result;
	    $this->view->asegurado_id = $rows[0]['asegurado_id']; 
	    $this->view->numero_poliza_busqueda = $params['numero_poliza_busqueda']; 
	    
	   // print_r($rows[0]['asegurado_id']);
	   // print_r($this->view->rows);
	}
	public function impresionDetalleMovimientoAseguradoAction(){
		$params = $this->_request->getParams();
	//	echo "<pre>";
	//	print_r($params);

	    $rows = Domain_Movimiento::getPolizasByMovimiento($params['movimiento_id']);
		
		$d_movimiento = new Domain_Movimiento($params['movimiento_id']);
		
		$this->view->m_movimiento = $d_movimiento->getModel();
		$this->view->datos_cheques = Domain_Movimiento::getDatosCheques($params['movimiento_id']);

		$array_detalle_poliza = $rows[0]['Model_MovimientoPoliza'];
		
		$polizas_result = array();
	    //Trae el nombre del asegurado
	    foreach ($array_detalle_poliza as $value) {

	    	
	    	$d = Domain_Movimiento::getPolizaByDetallePagoId($value['poliza_id']);
	    	$polizas_result[]=$d;
    	
	    }
	    $this->view->rows = $polizas_result;
	    $this->view->asegurado_id = $rows[0]['asegurado_id']; 
	   // print_r($rows[0]['asegurado_id']);
	   // print_r($this->view->rows);
	}


	public function eliminarMovimientoAseguradoAction(){
		$params = $this->_request->getParams();
		$this->view->asegurado_id = $params['asegurado_id'];
		
		//detalle_pago_id
		$polizas = Domain_Movimiento::getMovimientosPoliza($params['movimiento_id']);
		
		foreach($polizas as $poliza){
		//$d = Domain_Movimiento::getPolizaByDetallePagoId($poliza['poliza_id']);
		//es parte de la cuota (detalle_pago_cuota_id)
		
		Domain_DetallePago::setPago($poliza['poliza_id']);
		
		}
		//Elimino movimientos de la tabla movimiento Poliza
		Domain_Movimiento::eliminarMovimientosPoliza($params['movimiento_id']);

		$d_movimiento = new Domain_Movimiento($params['movimiento_id']);
		
		$m_movimiento = $d_movimiento->getModel();
		$m_movimiento->delete();

	}


	
	/*
	 * 
	 */
	
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
}