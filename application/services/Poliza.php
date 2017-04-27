<?php
/**
 *
 * @author guiles
 * @param: Objeto Poliza, Datos del POST
 */
class Services_Poliza
{

	public function savePolizaAutomotor($poliza,$params){
		$tipo_poliza = Domain_TipoPoliza::getIdByName('AUTOMOTORES');
		$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		//Tipo poliza Automotor => id = 7
		try {
			//1. Poliza Detalle Automotor
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->tipo_vehiculo_id =$params['tipo_vehiculo_id'];
			$m_poliza_detalle->tipo_cobertura_id =$params['tipo_cobertura_id'];
			$m_poliza_detalle->anio =$params['anio_automotor'];
			$m_poliza_detalle->marca=$params['marca_automotor'];
			$m_poliza_detalle->tipo=$params['tipo_automotor'];
			$m_poliza_detalle->modelo=$params['modelo_automotor'];
			$m_poliza_detalle->color=$params['color_automotor'];
			$m_poliza_detalle->patente=$params['patente_automotor'];
			$m_poliza_detalle->cilindrada_id=$params['cilindrada_automotor'];
			$m_poliza_detalle->serial_carroceria=$params['serial_c_automotor'];
			$m_poliza_detalle->serial_motor=$params['serial_automotor'];
			$m_poliza_detalle->uso_vehiculo=$params['uso_automotor'];
			$m_poliza_detalle->capacidad_id=$params['capacidad_automotor'];
			$m_poliza_detalle->pasajeros_id=$params['pasajeros_automotor'];
			$m_poliza_detalle->flota_id=$params['flota_automotor'];
			$m_poliza_detalle->fecha_titulo=$params['fecha_titulo_automotor'];
			$m_poliza_detalle->titular=$params['titular_automotor'];
			$m_poliza_detalle->numero_certificado=$params['numero_certificado_automotor'];
			$m_poliza_detalle->estado_luces_id=$params['estado_luces_automotor'];
			$m_poliza_detalle->accesorios=$params['accesorios_automotor'];
			$m_poliza_detalle->estado_motor_id=$params['estado_motor_automotor'];
			$m_poliza_detalle->estado_carroceria_id=$params['estado_carroceria_automotor'];
			$m_poliza_detalle->tipo_combustion_id=$params['tipo_combustion_automotor'];
			$m_poliza_detalle->acreedor_prendario=$params['acreedor_prendario_automotor'];
			$m_poliza_detalle->estado_vehiculo_id=$params['estado_vehiculo_automotor'];
			$m_poliza_detalle->estado_cubiertas_id=$params['estado_cubiertas_automotor'];
			$m_poliza_detalle->otros=$params['otros_automotor'];
			$m_poliza_detalle->sistema_seguridad_id=$params['sistema_seguridad_automotor'];

			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 * `moneda_id`, `monto_asegurado`, `iva`, `prima_comision`, `prima_tarifa`,
		 * `premio_compania`, `premio_asegurado`, `plus`
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->endoso=0;
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			$m_poliza->tipo_poliza_id = $tipo_poliza; //Tipo poliza Automotor
			$m_poliza->estado_id = $estado;



			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_automotor_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			//$m_poliza->numero_poliza = $m_poliza->poliza_id ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		//echo"<pre>";

		//	print_r($poliza);
		//exit;
		return $poliza;
	}


	public function savePolizaCaucion($poliza,$params){
		$tipo_poliza = Domain_TipoPoliza::getIdByName('CAUCION');
		$estado = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_PENDIENTE');
		//Tipo Caucion (id = 1 )
		try {
			//1. Poliza Detalle Seguro Comun (Ver si es para Caucion solamente)
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->domicilio_riesgo ='Domicilio';
				
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			$m_poliza->endoso=0;
			$m_poliza->tipo_poliza_id = $tipo_poliza; //Es del tipo caucion
			$m_poliza->estado_id = $estado;


			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_caucion_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $m_poliza->poliza_id ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return $poliza;
	}


	public function savePolizaAduaneros($poliza,$params){

		//Tipo Aduaneros (id = 2 )
		$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');
		$estado = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		try {
			//1. Poliza Detalle Seguro Comun (Ver si es para Caucion solamente)
			$m_poliza_detalle = $solicitud->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->acreedor_prendario=$params['acreedor_prendario'];
			$m_poliza_detalle->mercaderia=$params['mercaderia'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];
			$m_poliza_detalle->bl=$params['bl'];
			$m_poliza_detalle->factura=$params['factura'];
			$m_poliza_detalle->sim=$params['sim'];
				
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			//$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			$m_poliza->tipo_poliza_id = $tipo_poliza; //Es del tipo Aduaneros
			$m_poliza->estado_id = $estado;
			$m_poliza->endoso = 0;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_aduaneros_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $m_poliza->poliza_id ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return $poliza;
	}


	public function savePolizaAccidentesPersonales($poliza,$params){

		//Tipo Accidentes Personales (id = 3 )
		$tipo_poliza = Domain_TipoPoliza::getIdByName('ACCIDENTES_PERSONALES');
		$estado = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_PENDIENTE');


		try {
			//1. Poliza Detalle Seguro Comun (Ver si es para Caucion solamente)
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->domicilio_riesgo ='Domicilio';
				
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			$m_poliza->tipo_poliza_id = $tipo_poliza; //Es del tipo Aduaneros
			$m_poliza->estado_id = $estado;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_accidentes_personales_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $m_poliza->poliza_id ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return $poliza;
	}




	public function bksaveDetallePago($poliza,$params){

		$m_poliza = $poliza->getModelPoliza();
		//1. Borra los datos de pago
		Domain_DetallePago::deleteDetallePago($m_poliza->poliza_id);

		if($params['forma_pago_id']==1){
			try {

				//echo "Guarda la forma de pago Efectivo";
				//Guardo el tipo de pago
				$detalle_pago = new Model_DetallePago();
				$detalle_pago->forma_pago_id = $params['forma_pago_id'];
				$detalle_pago->save();
				//Guardo en la poliza el id para asociarlo
					
				$m_poliza->detalle_pago_id = $detalle_pago->detalle_pago_id;
				$m_poliza->save();
				//Guardo los valores de las cuotas
				//En este caso es una sola cuota porque es efectivo
				$detalle_pago_cuota = new Model_DetallePagoCuota();
				$detalle_pago_cuota->detalle_pago_id = $detalle_pago->detalle_pago_id;
				$detalle_pago_cuota->importe = $params['importe'];
					
				$detalle_pago_cuota->save();
			} catch (Exception $e) {
				echo $e->getMessage();
			}

				
			return new Domain_Poliza($m_poliza->poliza_id);
		}

		//Si es en cuotas
		if($params['forma_pago_id']==2){
			//echo "Guarda la forma de pago Tarjeta";
			$detalle_pago = new Model_DetallePago();
			$detalle_pago->forma_pago_id = $params['forma_pago_id'];
			$detalle_pago->save();
			//Guardo en la poliza el id para asociarlo
				
			$m_poliza->detalle_pago_id = $detalle_pago->detalle_pago_id;
			$m_poliza->save();


			for ($i = 1; $i <= $params['cuotas']; $i++) {
				$detalle_pago_cuota = new Model_DetallePagoCuota();
				$detalle_pago_cuota->detalle_pago_id = $detalle_pago->detalle_pago_id;
				$detalle_pago_cuota->cuota_id=$i;
				$detalle_pago_cuota->importe=$params['importe'];
				$detalle_pago_cuota->save();

			}
			//devuelvo la poliza actualizada
			return new Domain_Poliza($m_poliza->poliza_id);
		}
	}

public function saveDetallePago($d_poliza,$params){

		$m_poliza = $d_poliza->getModelPoliza();

		//1. Borra los datos de pago
		Domain_DetallePago::deleteDetallePago($m_poliza->poliza_id);

		//echo "<pre>";
		//print_r($params);
		$fecha_vigencia = $m_poliza->fecha_vigencia;


		try {

			//echo "Guarda la forma de pago Efectivo";
			//Guardo el tipo de pago
			$detalle_pago = new Model_DetallePago();
			$detalle_pago->forma_pago_id = $params['forma_pago_id'];
			$detalle_pago->moneda_id = $params['moneda_id'];
			$detalle_pago->save();
			
			//Guardo en la poliza el id para asociarlo

			$m_poliza->detalle_pago_id = $detalle_pago->detalle_pago_id;
			$m_poliza->save();
			//Guardo los valores de las cuotas
			//En este caso es una sola cuota porque es efectivo

			for ($i = 1; $i <= $params['cuotas']; $i++) {

				$detalle_pago_cuota = new Model_DetallePagoCuota();
				$detalle_pago_cuota->detalle_pago_id = $detalle_pago->detalle_pago_id;
				$detalle_pago_cuota->cuota_id=$i;
				$detalle_pago_cuota->importe=$params['valor_cuota'];
				$detalle_pago_cuota->fecha_cobro= $fecha_vigencia;
				$detalle_pago_cuota->save();
				//Agrego un mes a la fecha
				$fecha_vigencia = Domain_DetallePago::addMonthbyDate($fecha_vigencia);
				//echo"<br>";
				////print_r($fecha_vigencia);
			}

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		//deberia guardar el detalle pago cuota en la poliza

		//devuelvo la poliza actualizada
		return new Domain_Poliza($m_poliza->poliza_id);

	}




	public static function calcularPlus($importe,$premio_asegurado){

		//devuelvo -1 como error porque false puede interpretarse como cero
		if( !(is_numeric($importe) AND is_numeric($premio_asegurado)) )
		return -1;

		if($importe<$premio_asegurado) return -1;

		$plus = $importe - $premio_asegurado;

		return $plus;
	}


	public function saveViewPolizaAutomotor($poliza,$params){
	
		$tipo_poliza = Domain_TipoPoliza::getIdByName('AUTOMOTORES');
		//$estado = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_PENDIENTE');
		//Tipo poliza Automotor => id = 7

		/*
		 * 2- Poliza Valores
		 * `moneda_id`, `monto_asegurado`, `iva`, `prima_comision`, `prima_tarifa`,
		 * `premio_compania`, `premio_asegurado`, `plus`
		 */
		/*
		 *
		 - Nº de poliza
		 * - Fecha Vigencia
		 * - Prima
		 * - Premio Compañia
		 * - Premio
		 * - Plus
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}

	public function saveViewPolizaAduaneros($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		/*
		 * Nº de poliza, Vigencia desde, BL,Factura,SIM,Prima,Premio Compañia
		 * Premio,Plus
		 */
		//	echo"<pre>";
		//print_r( $poliza );
		//print_r( $poliza->getModelDetalle() );
//print_r($params);

		try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->bl=$params['bl'];
			$m_poliza_detalle->factura=$params['factura'];
			$m_poliza_detalle->sim=$params['sim'];
			$m_poliza_detalle->documentacion_id=$params['documentacion_id'];
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}


		public function saveViewPolizaConstruccion($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		/*
		 * Nº de poliza, Vigencia desde, BL,Factura,SIM,Prima,Premio Compañia
		 * Premio,Plus
		 */
		//	echo"<pre>";
		//print_r( $params );
		//print_r( $poliza->getModelDetalle() );


		try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->documentacion_id = $params['documentacion_id'];
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}
	
public function saveViewPolizaSeguroTecnico($poliza,$params){
	
		/*
		 * Nº de poliza, Vigencia desde, BL,Factura,SIM,Prima,Premio Compañia
		 * Premio,Plus
		 */
	

		try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->documentacion_id = $params['documentacion_id'];
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}
	

	public function saveViewPolizaIgj($poliza,$params){

		try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->documentacion_id=$params['documentacion_id'];	
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}
	

		public function saveViewPolizaJudiciales($poliza,$params){

		try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->documentacion_id=$params['documentacion_id'];
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}
	
public function saveViewPolizaVida($poliza,$params){

		try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->documentacion_id=$params['documentacion_id'];	
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}
	
	
		
		public function saveViewPolizaResponsabilidadCivil($poliza,$params){

				try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->documentacion_id=$params['documentacion_id'];
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}

	public function saveViewPolizaTransporteMercaderia($poliza,$params){
		
		
				try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->documentacion_id=$params['documentacion_id'];
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}
	
	
	
	
	public function saveViewPolizaAlquiler($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		/*
		 * Nº de poliza, Vigencia desde, BL,Factura,SIM,Prima,Premio Compañia
		 * Premio,Plus
		 */
		//	echo"<pre>";
		//print_r( $poliza );
		//print_r( $poliza->getModelDetalle() );


		try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->documentacion_id=$params['documentacion_id'];
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return $poliza;
	}
	
public function saveViewPolizaAccidentesPersonales($poliza,$params){
		
	//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->documentacion_id=$params['documentacion_id'];
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];;
			$m_poliza->observaciones_compania=$params['observaciones_compania'];;
			
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}
	/**Metdos Super Admin Edit Polizas****/

public function saveEditPolizaAduaneros($poliza,$params){
//echo "<pre>";
//print_r($poliza);

		//Tipo Aduaneros (id = 2 )
		$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');
		//$estado = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_PENDIENTE');
		try {
			//1. Poliza Detalle Seguro Comun (Ver si es para Caucion solamente)
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->despachante_aduana_id=$params['despachante_aduana_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->acreedor_prendario=$params['acreedor_prendario'];
			$m_poliza_detalle->mercaderia=$params['mercaderia'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];
			$m_poliza_detalle->bl=$params['bl'];
			$m_poliza_detalle->factura=$params['factura'];
			$m_poliza_detalle->sim=$params['sim'];

			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

			$fecha_vigencia_hasta = $this->calcularPeriodo($params['fecha_vigencia'], $params['periodo_id']);
			$m_poliza = $poliza->getModelPoliza();

			$m_poliza->estado_id=$params['estado_poliza_id'];
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->poliza_poliza_id = $params['poliza_poliza_id'];
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->fecha_vigencia_hasta=$fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			//$m_poliza->tipo_poliza_id = $tipo_poliza; //Es del tipo Aduaneros
			//$m_poliza->estado_id = $estado;
			//$m_poliza->endoso = 0;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_aduaneros_id;
		
			$m_poliza->save();


		} catch (Exception $e) {
			echo $e->getMessage();
		}


		return 	$poliza;
	}
/**************************************************************************/
public function saveEditPolizaAlquiler($poliza,$params){

		$tipo_poliza = Domain_TipoPoliza::getIdByName('ALQUILER');
		try {
		
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];


			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

			$fecha_vigencia_hasta = $this->calcularPeriodo($params['fecha_vigencia'], $params['periodo_id']);
			$m_poliza = $poliza->getModelPoliza();

			$m_poliza->estado_id=$params['estado_poliza_id'];
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->poliza_poliza_id = $params['poliza_poliza_id'];
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->fecha_vigencia_hasta=$fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			//$m_poliza->tipo_poliza_id = $tipo_poliza; 
			//$m_poliza->estado_id = $estado;
			//$m_poliza->endoso = 0;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_alquiler_id;
		
			$m_poliza->save();


		} catch (Exception $e) {
			echo $e->getMessage();
		}


		return 	$poliza;
	}

		public function saveEditPolizaAccidentesPersonales($poliza,$params){
//echo "<pre>";
//print_r($params);
		$tipo_poliza = Domain_TipoPoliza::getIdByName('ACCIDENTES_PERSONALES');
		try {
		
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			
			$m_poliza_detalle->cantidad_personas =$params['cantidad_personas'];
			$m_poliza_detalle->tareas_a_realizar =$params['tareas_a_realizar'];
			$m_poliza_detalle->altura_maxima =$params['altura_maxima'];
			$m_poliza_detalle->datos_adicionales =$params['datos_adicionales'];


			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

			$fecha_vigencia_hasta = $this->calcularPeriodo($params['fecha_vigencia'], $params['periodo_id']);
			$m_poliza = $poliza->getModelPoliza();

			$m_poliza->estado_id=$params['estado_poliza_id'];
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->poliza_poliza_id = $params['poliza_poliza_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->fecha_vigencia_hasta=$fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			//$m_poliza->tipo_poliza_id = $tipo_poliza; 
			//$m_poliza->estado_id = $estado;
			//$m_poliza->endoso = 0;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_accidentes_personales_id;
		
			$m_poliza->save();
		//print_r($m_poliza->poliza_poliza_id);

		} catch (Exception $e) {
			echo $e->getMessage();
		}


		return 	$poliza;
	}

public function saveEditPolizaIgj($poliza,$params){

		$tipo_poliza = Domain_TipoPoliza::getIdByName('IGJ');
		try {
		
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->numero_licitacion=$params['numero_licitacion'];
			$m_poliza_detalle->obra=$params['obra'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];
			$m_poliza_detalle->expediente=$params['expediente'];
			$m_poliza_detalle->objeto=$params['objeto'];
			$m_poliza_detalle->apertura_licitacion=$params['apertura_licitacion'];
			$m_poliza_detalle->clausula_especial=$params['clausula_especial'];
			$m_poliza_detalle->certificaciones=$params['certificaciones'];


			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

			$fecha_vigencia_hasta = $this->calcularPeriodo($params['fecha_vigencia'], $params['periodo_id']);
			$m_poliza = $poliza->getModelPoliza();

			$m_poliza->estado_id=$params['estado_poliza_id'];
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->poliza_poliza_id = $params['poliza_poliza_id'];
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->fecha_vigencia_hasta=$fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			//$m_poliza->tipo_poliza_id = $tipo_poliza; 
			//$m_poliza->estado_id = $estado;
			//$m_poliza->endoso = 0;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_igj_id;
		
			$m_poliza->save();


		} catch (Exception $e) {
			echo $e->getMessage();
		}


		return 	$poliza;
	}

		public function saveEditPolizaJudiciales($poliza,$params){

		$tipo_poliza = Domain_TipoPoliza::getIdByName('JUDICIALES');
		try {
		
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->numero_licitacion=$params['numero_licitacion'];
			$m_poliza_detalle->obra=$params['obra'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];
			$m_poliza_detalle->expediente=$params['expediente'];
			$m_poliza_detalle->objeto=$params['objeto'];
			$m_poliza_detalle->apertura_licitacion=$params['apertura_licitacion'];
			$m_poliza_detalle->clausula_especial=$params['clausula_especial'];
			$m_poliza_detalle->certificaciones=$params['certificaciones'];


			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

			$fecha_vigencia_hasta = $this->calcularPeriodo($params['fecha_vigencia'], $params['periodo_id']);
			$m_poliza = $poliza->getModelPoliza();

			$m_poliza->estado_id=$params['estado_poliza_id'];
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->poliza_poliza_id = $params['poliza_poliza_id'];
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->fecha_vigencia_hasta=$fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			//$m_poliza->tipo_poliza_id = $tipo_poliza; 
			//$m_poliza->estado_id = $estado;
			//$m_poliza->endoso = 0;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_judiciales_id;
		
			$m_poliza->save();


		} catch (Exception $e) {
			echo $e->getMessage();
		}


		return 	$poliza;
	}

public function saveEditPolizaVida($poliza,$params){

		$tipo_poliza = Domain_TipoPoliza::getIdByName('VIDA');
		try {
		
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->numero_licitacion=$params['numero_licitacion'];
			$m_poliza_detalle->obra=$params['obra'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];
			$m_poliza_detalle->expediente=$params['expediente'];
			$m_poliza_detalle->objeto=$params['objeto'];
			$m_poliza_detalle->apertura_licitacion=$params['apertura_licitacion'];
			$m_poliza_detalle->clausula_especial=$params['clausula_especial'];
			$m_poliza_detalle->certificaciones=$params['certificaciones'];


			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

			$fecha_vigencia_hasta = $this->calcularPeriodo($params['fecha_vigencia'], $params['periodo_id']);
			$m_poliza = $poliza->getModelPoliza();

			$m_poliza->estado_id=$params['estado_poliza_id'];
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->poliza_poliza_id = $params['poliza_poliza_id'];
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->fecha_vigencia_hasta=$fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			//$m_poliza->tipo_poliza_id = $tipo_poliza; 
			//$m_poliza->estado_id = $estado;
			//$m_poliza->endoso = 0;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_vida_id;
		
			$m_poliza->save();


		} catch (Exception $e) {
			echo $e->getMessage();
		}


		return 	$poliza;
	}

public function saveEditPolizaIntegralComercio($poliza,$params){

		$tipo_poliza = Domain_TipoPoliza::getIdByName('INTEGRAL_COMERCIO');
		try {
		
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];
			$m_poliza_detalle->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

			$fecha_vigencia_hasta = $this->calcularPeriodo($params['fecha_vigencia'], $params['periodo_id']);
			$m_poliza = $poliza->getModelPoliza();

			$m_poliza->estado_id=$params['estado_poliza_id'];
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->poliza_poliza_id = $params['poliza_poliza_id'];
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->fecha_vigencia_hasta=$fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			//$m_poliza->tipo_poliza_id = $tipo_poliza; 
			//$m_poliza->estado_id = $estado;
			//$m_poliza->endoso = 0;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_integral_comercio_id;
		
			$m_poliza->save();


		} catch (Exception $e) {
			echo $e->getMessage();
		}


		return 	$poliza;
	}

public function saveEditPolizaTransporteMercaderia($poliza,$params){

		$tipo_poliza = Domain_TipoPoliza::getIdByName('TRANSPORTE_MERCADERIA');
		try {
		
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];

			$m_poliza_detalle->mercaderia=$params['mercaderia'];
			$m_poliza_detalle->transporte=$params['transporte'];
			$m_poliza_detalle->cuit_transportista=$params['cuit_transportista'];
			$m_poliza_detalle->origen_desde=$params['origen_desde'];
			$m_poliza_detalle->origen_hasta=$params['origen_hasta'];
			$m_poliza_detalle->tipo_transporte_id=$params['tipo_transporte_id'];
			$m_poliza_detalle->marca=$params['marca'];
			$m_poliza_detalle->modelo=$params['modelo'];
			$m_poliza_detalle->patente=$params['patente'];
			$m_poliza_detalle->patente_semi=$params['patente_semi'];
			
			$m_poliza_detalle->nombre_chofer=$params['nombre_chofer'];
			$m_poliza_detalle->documento_chofer=$params['documento_chofer'];
			$m_poliza_detalle->custodia_id=$params['custodia_id'];
			$m_poliza_detalle->datos_custodia=$params['datos_custodia'];
			$m_poliza_detalle->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

			$fecha_vigencia_hasta = $this->calcularPeriodo($params['fecha_vigencia'], $params['periodo_id']);
			$m_poliza = $poliza->getModelPoliza();

			$m_poliza->estado_id=$params['estado_poliza_id'];
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->poliza_poliza_id = $params['poliza_poliza_id'];
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->fecha_vigencia_hasta=$fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			//$m_poliza->tipo_poliza_id = $tipo_poliza; 
			//$m_poliza->estado_id = $estado;
			//$m_poliza->endoso = 0;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_transporte_mercaderia_id;
		
			$m_poliza->save();


		} catch (Exception $e) {
			echo $e->getMessage();
		}


		return 	$poliza;
	}

	public function saveEditPolizaConstruccion($poliza,$params){
//echo "<pre>";
//print_r($poliza);

		$tipo_poliza = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		try {
		
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->numero_licitacion=$params['numero_licitacion'];
			$m_poliza_detalle->obra=$params['obra'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];
			$m_poliza_detalle->expediente=$params['expediente'];
			$m_poliza_detalle->objeto=$params['objeto'];
			$m_poliza_detalle->apertura_licitacion=$params['apertura_licitacion'];
			$m_poliza_detalle->clausula_especial=$params['clausula_especial'];
			$m_poliza_detalle->certificaciones=$params['certificaciones'];


			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

			$fecha_vigencia_hasta = $this->calcularPeriodo($params['fecha_vigencia'], $params['periodo_id']);
			$m_poliza = $poliza->getModelPoliza();

			$m_poliza->estado_id=$params['estado_poliza_id'];
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->poliza_poliza_id = $params['poliza_poliza_id'];
			$m_poliza->poliza_poliza_id = $params['poliza_poliza_id'];
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->fecha_vigencia_hasta=$fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			//$m_poliza->tipo_poliza_id = $tipo_poliza; 
			//$m_poliza->estado_id = $estado;
			//$m_poliza->endoso = 0;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_construccion_id;
		
			$m_poliza->save();


		} catch (Exception $e) {
			echo $e->getMessage();
		}


		return 	$poliza;
	}


public function saveEditPolizaResponsabilidadCivil($poliza,$params){
//echo "<pre>";
//print_r($poliza);
		$tipo_poliza = Domain_TipoPoliza::getIdByName('RESPONSABILIDAD_CIVIL');
		try {
			//1. Poliza Detalle Seguro Comun (Ver si es para Caucion solamente)
			$m_poliza_detalle = $poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->obra=$params['obra'];
			$m_poliza_detalle->expediente=$params['expediente'];
			$m_poliza_detalle->personal=$params['personal'];
			$m_poliza_detalle->presupuesto_oficial=$params['presupuesto_oficial'];
			$m_poliza_detalle->clausula_especial=$params['clausula_especial'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];

			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

			$fecha_vigencia_hasta = $this->calcularPeriodo($params['fecha_vigencia'], $params['periodo_id']);
			$m_poliza = $poliza->getModelPoliza();

			$m_poliza->estado_id=$params['estado_poliza_id'];
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->poliza_poliza_id = $params['poliza_poliza_id'];
			$m_poliza->asegurado_id=$params['asegurado_id'];
			$m_poliza->agente_id=$params['agente_id'];
			$m_poliza->compania_id=$params['compania_id'];
			$m_poliza->productor_id=$params['productor_id'];
			$m_poliza->cobrador_id=$params['cobrador_id'];
			$m_poliza->fecha_pedido=$params['fecha_pedido'];
			$m_poliza->periodo_id=$params['periodo_id'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->fecha_vigencia_hasta=$fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			//$m_poliza->tipo_poliza_id = $tipo_poliza; //Es del tipo Aduaneros
			//$m_poliza->estado_id = $estado;
			//$m_poliza->endoso = 0;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_responsabilidad_civil_id;
		
			$m_poliza->save();


		} catch (Exception $e) {
			echo $e->getMessage();
		}


		return 	$poliza;
	}


	/**FIN Metdos Super Admin Edit Polizas**/



	public function refacturarPolizaAduaneros($poliza){

		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza->getModelPoliza();
		$model_poliza->estado_id=$estado_refacturado;
		$model_poliza->save();
		$poliza_valores = $poliza->getModelPolizaValores();
		$poliza_detalle = $poliza->getModelDetalle();
		$detalle_pago = $poliza->getModelDetallePago();

		//Creo nueva poliza
		$refactura_poliza = new Domain_Poliza(); 
		//Tipo Aduaneros (id = 2 )
		$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');
		$estado = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Refacturacion');
		try {
			//1. Poliza Detalle Seguro Comun (Ver si es para Caucion solamente)
			$m_poliza_detalle = $refactura_poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->motivo_garantia_id=$poliza_detalle->motivo_garantia_id;
			$m_poliza_detalle->domicilio_riesgo=$poliza_detalle->domicilio_riesgo;
			$m_poliza_detalle->localidad_riesgo=$poliza_detalle->localidad_riesgo;
			$m_poliza_detalle->provincia_riesgo=$poliza_detalle->provincia_riesgo;
			$m_poliza_detalle->acreedor_prendario=$poliza_detalle->acreedor_prendario;
			$m_poliza_detalle->mercaderia=$poliza_detalle->mercaderia;
			$m_poliza_detalle->descripcion_adicional=$poliza_detalle->descripcion_adicional;
			$m_poliza_detalle->bl=$poliza_detalle->bl;
			$m_poliza_detalle->factura=$poliza_detalle->factura;
			$m_poliza_detalle->sim=$poliza_detalle->sim;
				
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $refactura_poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$poliza_valores->monto_asegurado;
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$poliza_valores->iva;
			$m_poliza_valores->prima_comision=$poliza_valores->prima_comision;
			$m_poliza_valores->prima_tarifa=$poliza_valores->prima_tarifa;
			$m_poliza_valores->premio_compania=$poliza_valores->premio_compania;
			$m_poliza_valores->premio_asegurado=$poliza_valores->premio_asegurado;
			$m_poliza_valores->plus=$poliza_valores->plus;
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $refactura_poliza->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$model_poliza->fecha_vigencia_hasta;
			$m_poliza->periodo_id=$model_poliza->periodo_id;
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->tipo_poliza_id = $tipo_poliza; //Es del tipo Aduaneros
			$m_poliza->estado_id = $estado;
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_aduaneros_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		$this->saveDetallePagoRefacturar($m_poliza, $detalle_pago);
		
		return $refactura_poliza;
	}

	public function refacturarPolizaConstruccion($poliza){

		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza->getModelPoliza();
		$model_poliza->estado_id=$estado_refacturado;
		$model_poliza->save();
		$poliza_valores = $poliza->getModelPolizaValores();
		$poliza_detalle = $poliza->getModelDetalle();
		$detalle_pago = $poliza->getModelDetallePago();

		//Creo nueva poliza
		$refactura_poliza = new Domain_Poliza(); 
		//Tipo Aduaneros (id = 2 )
		$tipo_poliza = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Refacturacion');
		try {
			//1. Poliza Detalle Seguro Comun (Ver si es para Caucion solamente)
			$m_poliza_detalle = $refactura_poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->motivo_garantia_id=$poliza_detalle->motivo_garantia_id;
			$m_poliza_detalle->domicilio_riesgo=$poliza_detalle->domicilio_riesgo;
			$m_poliza_detalle->localidad_riesgo=$poliza_detalle->localidad_riesgo;
			$m_poliza_detalle->provincia_riesgo=$poliza_detalle->provincia_riesgo;
			$m_poliza_detalle->tipo_garantia_id=$poliza_detalle->tipo_garantia_id;
			$m_poliza_detalle->beneficiario_id=$poliza_detalle->beneficiario_id;
			$m_poliza_detalle->numero_licitacion=$poliza_detalle->numero_licitacion;
			$m_poliza_detalle->obra=$poliza_detalle->obra;
			$m_poliza_detalle->descripcion_adicional=$poliza_detalle->descripcion_adicional;
			$m_poliza_detalle->expediente=$poliza_detalle->expediente;
			$m_poliza_detalle->objeto=$poliza_detalle->objeto;
			$m_poliza_detalle->apertura_licitacion=$poliza_detalle->apertura_licitacion;
			$m_poliza_detalle->clausula_especial=$poliza_detalle->clausula_especial;
		    $m_poliza_detalle->certificaciones=$poliza_detalle->certificaciones;
		 			
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $refactura_poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$poliza_valores->monto_asegurado;
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$poliza_valores->iva;
			$m_poliza_valores->prima_comision=$poliza_valores->prima_comision;
			$m_poliza_valores->prima_tarifa=$poliza_valores->prima_tarifa;
			$m_poliza_valores->premio_compania=$poliza_valores->premio_compania;
			$m_poliza_valores->premio_asegurado=$poliza_valores->premio_asegurado;
			$m_poliza_valores->plus=$poliza_valores->plus;
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $refactura_poliza->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$model_poliza->fecha_vigencia_hasta;
			$m_poliza->periodo_id=$model_poliza->periodo_id;
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->tipo_poliza_id = $tipo_poliza; //Es del tipo Aduaneros
			$m_poliza->estado_id = $estado;
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_construccion_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		$this->saveDetallePagoRefacturar($m_poliza, $detalle_pago);
		
		return $refactura_poliza;
	}

/*
Refacturar Poliza Judiciales
*/




	public function refacturarPolizaJudiciales($poliza){
//AAA
		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza->getModelPoliza();
		$model_poliza->estado_id=$estado_refacturado;
		$model_poliza->save();
		$poliza_valores = $poliza->getModelPolizaValores();
		$poliza_detalle = $poliza->getModelDetalle();
		$detalle_pago = $poliza->getModelDetallePago();

		//Creo nueva poliza
		$refactura_poliza = new Domain_Poliza(); 
		
		$tipo_poliza = Domain_TipoPoliza::getIdByName('JUDICIALES');
		$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Refacturacion');
		try {
			
			$m_poliza_detalle = $refactura_poliza->getModelDetallePoliza($tipo_poliza);
			
			$m_poliza_detalle->tipo_garantia_id = $poliza_detalle->tipo_garantia_id;
			$m_poliza_detalle->motivo_garantia_id = $poliza_detalle->motivo_garantia_id;
			$m_poliza_detalle->beneficiario_id = $poliza_detalle->beneficiario_id;
			$m_poliza_detalle->domicilio_riesgo= $poliza_detalle->domicilio_riesgo;
			$m_poliza_detalle->localidad_riesgo= $poliza_detalle->localidad_riesgo;
			$m_poliza_detalle->provincia_riesgo= $poliza_detalle->provincia_riesgo;
			$m_poliza_detalle->numero_licitacion= $poliza_detalle->numero_licitacion;
			$m_poliza_detalle->obra= $poliza_detalle->obra;
			$m_poliza_detalle->descripcion_adicional= $poliza_detalle->descripcion_adicional;
			$m_poliza_detalle->expediente= $poliza_detalle->expediente;
			$m_poliza_detalle->objeto= $poliza_detalle->objeto;
			$m_poliza_detalle->apertura_licitacion= $poliza_detalle->apertura_licitacion;
			$m_poliza_detalle->clausula_especial= $poliza_detalle->clausula_especial;
			$m_poliza_detalle->certificaciones= $poliza_detalle->certificaciones;
			$m_poliza_detalle->save();

				
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $refactura_poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$poliza_valores->monto_asegurado;
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$poliza_valores->iva;
			$m_poliza_valores->prima_comision=$poliza_valores->prima_comision;
			$m_poliza_valores->prima_tarifa=$poliza_valores->prima_tarifa;
			$m_poliza_valores->premio_compania=$poliza_valores->premio_compania;
			$m_poliza_valores->premio_asegurado=$poliza_valores->premio_asegurado;
			$m_poliza_valores->plus=$poliza_valores->plus;
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $refactura_poliza->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$model_poliza->fecha_vigencia_hasta;
			$m_poliza->periodo_id=$model_poliza->periodo_id;
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->tipo_poliza_id = $tipo_poliza;
			$m_poliza->estado_id = $estado;
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_judiciales_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		$this->saveDetallePagoRefacturar($m_poliza, $detalle_pago);
		
		return $refactura_poliza;
	}

	public function saveViewPolizaIntegralComercio($poliza,$params){


		try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->documentacion_id=$params['documentacion_id'];
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}
	
	public function saveViewPolizaIncendio($poliza,$params){


		try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->documentacion_id=$params['documentacion_id'];
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->numero_factura=$params['numero_factura'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}
	
	
public function refacturarPolizaAlquiler($poliza){

		$estado_refacturado = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza->getModelPoliza();
		$model_poliza->estado_id=$estado_refacturado;
		$model_poliza->save();
		$poliza_valores = $poliza->getModelPolizaValores();
		$poliza_detalle = $poliza->getModelDetalle();
		$detalle_pago = $poliza->getModelDetallePago();

		//Creo nueva poliza
		$refactura_poliza = new Domain_Poliza(); 
		//Tipo Aduaneros (id = 2 )
		$tipo_poliza = Domain_TipoPoliza::getIdByName('ALQUILER');
		$estado = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Refacturacion');
		try {
			//1. Poliza Detalle Seguro Comun (Ver si es para Caucion solamente)
			$m_poliza_detalle = $refactura_poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->motivo_garantia_id=$poliza_detalle->motivo_garantia_id;
			$m_poliza_detalle->domicilio_riesgo=$poliza_detalle->domicilio_riesgo;
			$m_poliza_detalle->localidad_riesgo=$poliza_detalle->localidad_riesgo;
			$m_poliza_detalle->provincia_riesgo=$poliza_detalle->provincia_riesgo;
			$m_poliza_detalle->tipo_garantia_id=$poliza_detalle->tipo_garantia_id;
			$m_poliza_detalle->beneficiario_id=$poliza_detalle->beneficiario_id;
			$m_poliza_detalle->descripcion_adicional=$poliza_detalle->descripcion_adicional;
			
		 			
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $refactura_poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$poliza_valores->monto_asegurado;
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$poliza_valores->iva;
			$m_poliza_valores->prima_comision=$poliza_valores->prima_comision;
			$m_poliza_valores->prima_tarifa=$poliza_valores->prima_tarifa;
			$m_poliza_valores->premio_compania=$poliza_valores->premio_compania;
			$m_poliza_valores->premio_asegurado=$poliza_valores->premio_asegurado;
			$m_poliza_valores->plus=$poliza_valores->plus;
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $refactura_poliza->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$model_poliza->fecha_vigencia_hasta;
			$m_poliza->periodo_id=$model_poliza->periodo_id;
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			$m_poliza->observaciones_compania=$model_poliza->observaciones_compania	;
			$m_poliza->tipo_poliza_id = $tipo_poliza; //Es del tipo Aduaneros
			$m_poliza->estado_id = $estado;
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_integral_comercio_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		$this->saveDetallePagoRefacturar($m_poliza, $detalle_pago);
		
		return $refactura_poliza;
	}
	/*
	*  Endosa poliza Aduaneros
	*  @poliza: Objecto Poliza
	*  @parames: Parametros del panel
	*/


public function endosarPolizaAduaneros($d_poliza,$params){
		echo "entra aca";
		echo "<pre>";
		//print_r($d_poliza->getModelDetalle());
		//exit;
		$e_m_poliza_detalle = $d_poliza->getModelDetalle();
		//Pongo como endosada a la poliza vieja y la nueva es afectada
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');

		//1. Traigo la poliza actual		
		$poliza_a_endosar = new Domain_Poliza($params['poliza_id']);  


		if($params['tipo_endoso_id'] == 5)
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Endoso');		
		
		if($params['tipo_endoso_id'] == 5)
			$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Refacturacion');

		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza_a_endosar->getModelPoliza();
		$model_poliza->estado_id=$estado_endosada;
		$model_poliza->save();
		
		//1.1 Traigo detalle poliza a endosar
		$poliza_detalle = $d_poliza->getModelDetalle();
		//echo "<pre>";
		//print_r($poliza_detalle);
		//exit;
		//2. Creo nueva poliza
		$poliza_endosada = new Domain_Poliza(); 

		//Tipo Alquiler
		$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');
		
		//Estado Afectada
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		
		
		try {
			//3. Guardo detalle poliza			
			$m_poliza_detalle = $poliza_endosada->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->motivo_garantia_id= $e_m_poliza_detalle->motivo_garantia_id; //$params['motivo_garantia_id'];
			$m_poliza_detalle->domicilio_riesgo= $e_m_poliza_detalle->domicilio_riesgo; //$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo= $e_m_poliza_detalle->localidad_riesgo; //$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo= $e_m_poliza_detalle->provincia_riesgo; //$params['provincia_riesgo'];
			$m_poliza_detalle->tipo_garantia_id= $e_m_poliza_detalle->tipo_garantia_id; //$params['tipo_garantia_id'];
			$m_poliza_detalle->beneficiario_id= $e_m_poliza_detalle->beneficiario_id; //$params['beneficiario_id'];
			$m_poliza_detalle->despachante_aduana_id= $e_m_poliza_detalle->despachante_aduana_id; //$params['despachante_aduana_id'];
			$m_poliza_detalle->descripcion_adicional= $e_m_poliza_detalle->descripcion_adicional; //$params['descripcion_adicional'];
			$m_poliza_detalle->acreedor_prendario= $e_m_poliza_detalle->acreedor_prendario;//$params['acreedor_prendario'];
			$m_poliza_detalle->mercaderia= $e_m_poliza_detalle->mercaderia; //$params['mercaderia'];
			$m_poliza_detalle->bl= $e_m_poliza_detalle->bl;//$params['bl'];
			$m_poliza_detalle->factura= $e_m_poliza_detalle->factura; //$params['factura'];
			$m_poliza_detalle->sim= $e_m_poliza_detalle->sim; //$params['sim'];

			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{

			$m_poliza_valores = $poliza_endosada->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {
			//$model_poliza->fecha_vigencia
            //$fecha_vigencia_desde = $this->calcularPeriodo($params['fecha_vigencia'], $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($params['fecha_vigencia'], $model_poliza->periodo_id);
			
			$m_poliza = $poliza_endosada->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$params['fecha_pedido']; //fecha de pedido se modifica
			$m_poliza->periodo_id= $params['periodo_id'];//$model_poliza->periodo_id; // El resto de las fechas no se modifica
			$m_poliza->fecha_vigencia= $params['fecha_vigencia'];//$fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->tipo_poliza_id = $tipo_poliza; 
			$m_poliza->estado_id = $estado_vigente;
			$m_poliza->tipo_endoso_id = $params['tipo_endoso_id'];
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_aduaneros_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
		return $poliza_endosada;
	}


public function endosarPolizaSeguroTecnico($d_poliza,$params){
		//Pongo como endosada a la poliza vieja y la nueva es afectada
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');

		//1. Traigo la poliza actual		
		$poliza_a_endosar = new Domain_Poliza($params['poliza_id']);  
		
		
		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza_a_endosar->getModelPoliza();
		$model_poliza->estado_id=$estado_endosada;
		$model_poliza->save();
		
		//1.1 Traigo detalle poliza a endosar
		$poliza_detalle = $d_poliza->getModelDetalle();
		
		//2. Creo nueva poliza
		$poliza_endosada = new Domain_Poliza(); 

		//Tipo Alquiler
		$tipo_poliza = Domain_TipoPoliza::getIdByName('SEGURO_TECNICO');
		
		//Estado Afectada
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Endoso');

		try {
			//3. Guardo detalle poliza			
			$m_poliza_detalle = $poliza_endosada->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->numero_licitacion=$params['numero_licitacion'];
			$m_poliza_detalle->obra=$params['obra'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];
			$m_poliza_detalle->expediente=$params['expediente'];
			$m_poliza_detalle->objeto=$params['objeto'];
			$m_poliza_detalle->apertura_licitacion=$params['apertura_licitacion'];
			$m_poliza_detalle->clausula_especial=$params['clausula_especial'];
			$m_poliza_detalle->certificaciones=$params['certificaciones'];

			
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza_endosada->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $poliza_endosada->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$params['fecha_pedido']; //fecha de pedido se modifica
			$m_poliza->periodo_id=$model_poliza->periodo_id; // El resto de las fechas no se modifica
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->tipo_poliza_id = $tipo_poliza; 
			$m_poliza->estado_id = $estado_vigente;
			$m_poliza->tipo_endoso_id = $params['tipo_endoso_id'];
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_seguro_tecnico_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
		return $poliza_endosada;
	}

	public function endosarPolizaConstruccion($d_poliza,$params){
		//Pongo como endosada a la poliza vieja y la nueva es afectada
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');

		if($params['tipo_endoso_id'] == 5)
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
		
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Endoso');		
		
		if($params['tipo_endoso_id'] == 5)
			$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Refacturacion');


		//1. Traigo la poliza actual		
		$poliza_a_endosar = new Domain_Poliza($params['poliza_id']);  
   		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza_a_endosar->getModelPoliza();
		$model_poliza->estado_id=$estado_endosada;
		$model_poliza->save();
		
		//1.1 Traigo detalle poliza a endosar
		$m_poliza_detalle = $d_poliza->getModelDetalle();
		//print_r($m_poliza_detalle);
		//exit;
		//2. Creo nueva poliza
		$poliza_endosada = new Domain_Poliza(); 

		//Tipo Alquiler
		$tipo_poliza = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		
		//Estado Afectada
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		

		try {
			//3. Guardo detalle poliza						
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->numero_licitacion=$params['numero_licitacion'];
			$m_poliza_detalle->obra=$params['obra'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];
			$m_poliza_detalle->expediente=$params['expediente'];
			$m_poliza_detalle->objeto=$params['objeto'];
			$m_poliza_detalle->apertura_licitacion=$params['apertura_licitacion'];
			$m_poliza_detalle->clausula_especial=$params['clausula_especial'];
		    $m_poliza_detalle->certificaciones=$params['certificaciones'];
		    
			//echo "<pre>";
			//print_r($m_poliza_detalle);
			//exit;
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza_endosada->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$params['moneda_id'];
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($params['fecha_vigencia'], $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $poliza_endosada->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			
			$m_poliza->fecha_pedido=$params['fecha_pedido']; //fecha de pedido se modifica
			$m_poliza->periodo_id= $params['periodo_id'];//$model_poliza->periodo_id; // El resto de las fechas no se modifica
			$m_poliza->fecha_vigencia= $params['fecha_vigencia'];//$fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			
			//$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			//$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];

			$m_poliza->tipo_poliza_id = $tipo_poliza; 
			$m_poliza->estado_id = $estado_vigente;
			$m_poliza->tipo_endoso_id = $params['tipo_endoso_id'];
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_construccion_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
		return $poliza_endosada;
	}



public function endosarPolizaResponsabilidadCivil($d_poliza,$params){
		//Pongo como endosada a la poliza vieja y la nueva es afectada
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');

		//1. Traigo la poliza actual		
		$poliza_a_endosar = new Domain_Poliza($params['poliza_id']);  
   	//	echo "<pre>"	;
	//	print_r($params);

		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza_a_endosar->getModelPoliza();
		$model_poliza->estado_id=$estado_endosada;
		$model_poliza->save();
		
		//1.1 Traigo detalle poliza a endosar
		$m_poliza_detalle = $d_poliza->getModelDetalle();
	
		//2. Creo nueva poliza
		$poliza_endosada = new Domain_Poliza(); 

		//Tipo Alquiler
		$tipo_poliza = Domain_TipoPoliza::getIdByName('RESPONSABILIDAD_CIVIL');
		
		//Estado Afectada
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Endoso');

		try {
			//3. Guardo detalle poliza						
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->obra=$params['obra'];
			$m_poliza_detalle->expediente=$params['expediente'];
			$m_poliza_detalle->personal=$params['personal'];
			$m_poliza_detalle->presupuesto_oficial=$params['presupuesto_oficial'];
			$m_poliza_detalle->clausula_especial=$params['clausula_especial'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];

			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza_endosada->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $poliza_endosada->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$params['fecha_pedido']; //fecha de pedido se modifica
			$m_poliza->periodo_id=$model_poliza->periodo_id; // El resto de las fechas no se modifica
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			//$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			//$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];

			$m_poliza->tipo_poliza_id = $tipo_poliza; 
			$m_poliza->estado_id = $estado_vigente;
			$m_poliza->tipo_endoso_id = $params['tipo_endoso_id'];
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_responsabilidad_civil_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
		return $poliza_endosada;
	}


	public function endosarPolizaAccidentesPersonales($d_poliza,$params){
		
		//exit;
		//Pongo como endosada a la poliza vieja y la nueva es afectada
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');

		if($params['tipo_endoso_id'] == 5)
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('REFACTURADO');
	

		//1. Traigo la poliza actual		
		$poliza_a_endosar = new Domain_Poliza($params['poliza_id']);  
   	//	echo "<pre>"	;
	//	print_r($params);

		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza_a_endosar->getModelPoliza();
		$model_poliza->estado_id=$estado_endosada;
		$model_poliza->save();
		
		//1.1 Traigo detalle poliza a endosar
		$m_poliza_detalle = $d_poliza->getModelDetalle();
	
		//2. Creo nueva poliza
		$poliza_endosada = new Domain_Poliza(); 

		//Tipo Alquiler
		$tipo_poliza = Domain_TipoPoliza::getIdByName('ACCIDENTES_PERSONALES');
		
		//Estado Afectada
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		
		//Trae la operacion endoso por default
		//$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Endoso');

		//Si es endoso de refacturacion que traiga la otra operacion, sobreescribe la operacion y el estado
		if($params['tipo_endoso_id'] == 5)
			$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Refacturacion');
				
		

		try {
			//3. Guardo detalle poliza						
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			
			$m_poliza_detalle->cantidad_personas =$params['cantidad_personas'];
			$m_poliza_detalle->tareas_a_realizar =$params['tareas_a_realizar'];
			$m_poliza_detalle->altura_maxima =$params['altura_maxima'];
			$m_poliza_detalle->datos_adicionales =$params['datos_adicionales'];

			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza_endosada->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $poliza_endosada->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$params['fecha_pedido']; //fecha de pedido se modifica
			$m_poliza->periodo_id=$model_poliza->periodo_id; // El resto de las fechas no se modifica
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			//$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			//$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];

			$m_poliza->tipo_poliza_id = $tipo_poliza; 
			$m_poliza->estado_id = $estado_vigente;
			$m_poliza->tipo_endoso_id = $params['tipo_endoso_id'];
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_accidentes_personales_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
		return $poliza_endosada;
	}


public function endosarPolizaJudiciales($d_poliza,$params){
		//Pongo como endosada a la poliza vieja y la nueva es afectada
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');

		//1. Traigo la poliza actual		
		$poliza_a_endosar = new Domain_Poliza($params['poliza_id']);  
   	//	echo "<pre>"	;
	//	print_r($params);

		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza_a_endosar->getModelPoliza();
		$model_poliza->estado_id=$estado_endosada;
		$model_poliza->save();
		
		//1.1 Traigo detalle poliza a endosar
		$m_poliza_detalle = $d_poliza->getModelDetalle();
	
		//2. Creo nueva poliza
		$poliza_endosada = new Domain_Poliza(); 

		//Tipo Alquiler
		$tipo_poliza = Domain_TipoPoliza::getIdByName('JUDICIALES');
		
		//Estado Afectada
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Endoso');

		try {
			//3. Guardo detalle poliza						
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->numero_licitacion=$params['numero_licitacion'];
			$m_poliza_detalle->obra=$params['obra'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];
			$m_poliza_detalle->expediente=$params['expediente'];
			$m_poliza_detalle->objeto=$params['objeto'];
			$m_poliza_detalle->apertura_licitacion=$params['apertura_licitacion'];
			$m_poliza_detalle->clausula_especial=$params['clausula_especial'];
			$m_poliza_detalle->certificaciones=$params['certificaciones'];
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza_endosada->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $poliza_endosada->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$params['fecha_pedido']; //fecha de pedido se modifica
			$m_poliza->periodo_id=$model_poliza->periodo_id; // El resto de las fechas no se modifica
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			//$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			//$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];

			$m_poliza->tipo_poliza_id = $tipo_poliza; 
			$m_poliza->estado_id = $estado_vigente;
			$m_poliza->tipo_endoso_id = $params['tipo_endoso_id'];
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_judiciales_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
		return $poliza_endosada;
	}

public function endosarPolizaIgj($d_poliza,$params){
		//Pongo como endosada a la poliza vieja y la nueva es afectada
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');

		//1. Traigo la poliza actual		
		$poliza_a_endosar = new Domain_Poliza($params['poliza_id']);  
   	//	echo "<pre>"	;
	//	print_r($params);

		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza_a_endosar->getModelPoliza();
		$model_poliza->estado_id=$estado_endosada;
		$model_poliza->save();
		
		//1.1 Traigo detalle poliza a endosar
		$m_poliza_detalle = $d_poliza->getModelDetalle();
	
		//2. Creo nueva poliza
		$poliza_endosada = new Domain_Poliza(); 

		//Tipo Alquiler
		$tipo_poliza = Domain_TipoPoliza::getIdByName('IGJ');
		
		//Estado Afectada
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Endoso');

		try {
			//3. Guardo detalle poliza						
			$m_poliza_detalle->tipo_garantia_id=$params['tipo_garantia_id'];
			$m_poliza_detalle->motivo_garantia_id=$params['motivo_garantia_id'];
			$m_poliza_detalle->beneficiario_id=$params['beneficiario_id'];
			$m_poliza_detalle->domicilio_riesgo=$params['domicilio_riesgo'];
			$m_poliza_detalle->localidad_riesgo=$params['localidad_riesgo'];
			$m_poliza_detalle->provincia_riesgo=$params['provincia_riesgo'];
			$m_poliza_detalle->numero_licitacion=$params['numero_licitacion'];
			$m_poliza_detalle->obra=$params['obra'];
			$m_poliza_detalle->descripcion_adicional=$params['descripcion_adicional'];
			$m_poliza_detalle->expediente=$params['expediente'];
			$m_poliza_detalle->objeto=$params['objeto'];
			$m_poliza_detalle->apertura_licitacion=$params['apertura_licitacion'];
			$m_poliza_detalle->clausula_especial=$params['clausula_especial'];
			$m_poliza_detalle->certificaciones=$params['certificaciones'];


			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza_endosada->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $poliza_endosada->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$params['fecha_pedido']; //fecha de pedido se modifica
			$m_poliza->periodo_id=$model_poliza->periodo_id; // El resto de las fechas no se modifica
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			//$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			//$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->observaciones_asegurado=$params['observaciones_asegurado'];
			$m_poliza->observaciones_compania=$params['observaciones_compania'];

			$m_poliza->tipo_poliza_id = $tipo_poliza; 
			$m_poliza->estado_id = $estado_vigente;
			$m_poliza->tipo_endoso_id = $params['tipo_endoso_id'];
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_igj_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
		return $poliza_endosada;
	}




public function endosarPolizaAlquiler($d_poliza,$params){

		//Pongo como endosada a la poliza vieja y la nueva es afectada
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');

		//1. Traigo la poliza actual		
		$poliza_a_endosar = new Domain_Poliza($params['poliza_id']);  
		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza_a_endosar->getModelPoliza();
		$model_poliza->estado_id=$estado_endosada;
		$model_poliza->save();
		
		//1.1 Traigo detalle poliza a endosar
		$poliza_detalle = $d_poliza->getModelDetalle();
		
		//2. Creo nueva poliza
		$poliza_endosada = new Domain_Poliza(); 

		//Tipo Alquiler
		$tipo_poliza = Domain_TipoPoliza::getIdByName('ALQUILER');
		
		//Estado Afectada
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Endoso');

		try {
			//3. Guardo detalle poliza			
			$m_poliza_detalle = $poliza_endosada->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->motivo_garantia_id=$poliza_detalle->motivo_garantia_id;
			$m_poliza_detalle->domicilio_riesgo=$poliza_detalle->domicilio_riesgo;
			$m_poliza_detalle->localidad_riesgo=$poliza_detalle->localidad_riesgo;
			$m_poliza_detalle->provincia_riesgo=$poliza_detalle->provincia_riesgo;
			$m_poliza_detalle->tipo_garantia_id=$poliza_detalle->tipo_garantia_id;
			$m_poliza_detalle->beneficiario_id=$poliza_detalle->beneficiario_id;
			$m_poliza_detalle->descripcion_adicional=$poliza_detalle->descripcion_adicional;
			
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza_endosada->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $poliza_endosada->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$model_poliza->fecha_vigencia_hasta;
			$m_poliza->periodo_id=$model_poliza->periodo_id;
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->tipo_poliza_id = $tipo_poliza; 
			$m_poliza->estado_id = $estado_vigente;
			$m_poliza->tipo_endoso_id = $params['tipo_endoso_id'];
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_alquiler_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		//$this->saveDetallePagoRefacturar($poliza_a_endosar, $detalle_pago);
		
		return $poliza_endosada;
	}
	
/**
*
* @method endosarPolizaIntegralComercio
*/


public function endosarPolizaIntegralComercio($d_poliza,$params){

		//Pongo como endosada a la poliza vieja y la nueva es afectada
		$estado_endosada = Domain_EstadoPoliza::getIdByCodigo('ENDOSADA');

		//1. Traigo la poliza actual		
		$poliza_a_endosar = new Domain_Poliza($params['poliza_id']);  
		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza_a_endosar->getModelPoliza();
		$model_poliza->estado_id=$estado_endosada;
		$model_poliza->save();
		
		//1.1 Traigo detalle poliza a endosar
		$poliza_detalle = $d_poliza->getModelDetalle();
		
		//2. Creo nueva poliza
		$poliza_endosada = new Domain_Poliza(); 

		//Tipo Alquiler
		$tipo_poliza = Domain_TipoPoliza::getIdByName('INTEGRAL_COMERCIO');
		
		//Estado Afectada
		$estado_vigente = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');
		
		$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Endoso');

		try {
			//3. Guardo detalle poliza			
			$m_poliza_detalle = $poliza_endosada->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->motivo_garantia_id=$poliza_detalle->motivo_garantia_id;
			$m_poliza_detalle->domicilio_riesgo=$poliza_detalle->domicilio_riesgo;
			$m_poliza_detalle->localidad_riesgo=$poliza_detalle->localidad_riesgo;
			$m_poliza_detalle->provincia_riesgo=$poliza_detalle->provincia_riesgo;
			$m_poliza_detalle->tipo_garantia_id=$poliza_detalle->tipo_garantia_id;
			$m_poliza_detalle->beneficiario_id=$poliza_detalle->beneficiario_id;
			$m_poliza_detalle->descripcion_adicional=$poliza_detalle->descripcion_adicional;
			
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $poliza_endosada->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $poliza_endosada->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$model_poliza->fecha_vigencia_hasta;
			$m_poliza->periodo_id=$model_poliza->periodo_id;
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->tipo_poliza_id = $tipo_poliza; 
			$m_poliza->estado_id = $estado_vigente;
			$m_poliza->tipo_endoso_id = $params['tipo_endoso_id'];
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_integral_comercio_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		//$this->saveDetallePagoRefacturar($poliza_a_endosar, $detalle_pago);
		
		return $poliza_endosada;
	}
	

/* Uso este metodo para refacturar y endosar (?)
*/

public function saveDetallePagoEndoso($d_poliza,$params){

		$m_poliza = $d_poliza->getModelPoliza();

		//1. Borra los datos de pago
		Domain_DetallePago::deleteDetallePago($m_poliza->poliza_id);

		//echo "<pre>";
		//print_r($params);
		$fecha_vigencia = $m_poliza->fecha_vigencia;


		try {

			//echo "Guarda la forma de pago Efectivo";
			//Guardo el tipo de pago
			$detalle_pago = new Model_DetallePago();
			$detalle_pago->forma_pago_id = $params['forma_pago_id'];
			$detalle_pago->moneda_id = $params['moneda_id'];
			$detalle_pago->save();
			//Guardo en la poliza el id para asociarlo

			$m_poliza->detalle_pago_id = $detalle_pago->detalle_pago_id;
			$m_poliza->save();
			//Guardo los valores de las cuotas
			//En este caso es una sola cuota porque es efectivo

			for ($i = 1; $i <= $params['cuotas']; $i++) {

				$detalle_pago_cuota = new Model_DetallePagoCuota();
				$detalle_pago_cuota->detalle_pago_id = $detalle_pago->detalle_pago_id;
				$detalle_pago_cuota->cuota_id=$i;
				$detalle_pago_cuota->importe=$params['valor_cuota'];
				$detalle_pago_cuota->fecha_cobro= $fecha_vigencia;
				$detalle_pago_cuota->save();
				//Agrego un mes a la fecha
				$fecha_vigencia = Domain_DetallePago::addMonthbyDate($fecha_vigencia);
				//echo"<br>";
				////print_r($fecha_vigencia);

			}

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		
		//devuelvo la poliza actualizada
		return new Domain_Poliza($m_poliza->poliza_id);

	}


private function saveDetallePagoRefacturar($m_solicitud,$m_poliza_detalle_pago){
	
		
		//1. Borra los datos de pago
		//Domain_DetallePago::deleteDetallePago($m_solicitud->poliza_id);

		
		$fecha_vigencia = $m_solicitud->fecha_vigencia;
	
	
	try {

			//echo "Guarda la forma de pago Efectivo";
			//Guardo el tipo de pago
			$detalle_pago = new Model_DetallePago();
			$detalle_pago->forma_pago_id = $m_poliza_detalle_pago->forma_pago_id;
			$detalle_pago->moneda_id = $m_poliza_detalle_pago->moneda_id;
			$detalle_pago->save();
			//Guardo en la poliza el id para asociarlo
				
			$m_solicitud->detalle_pago_id = $detalle_pago->detalle_pago_id;
			$m_solicitud->save();
			//Guardo los valores de las cuotas
			//En este caso es una sola cuota porque es efectivo
			$d_detalle_pago = new Domain_DetallePago($m_poliza_detalle_pago->detalle_pago_id);
			
			//Por ahora trae el primer importe para realizar la cuota
			$detalle_pago_cuotas = $d_detalle_pago->getModelDetallePagoCuota();
			
			//	echo"<pre>";
			//print_r($detalle_pago_cuotas);
			//exit;
			$cant_cuotas = Domain_DetallePago::getCantidadCuotas($m_poliza_detalle_pago->detalle_pago_id);

			for ($i = 1; $i <= $cant_cuotas; $i++) {

				$detalle_pago_cuota = new Model_DetallePagoCuota();
				$detalle_pago_cuota->detalle_pago_id = $detalle_pago->detalle_pago_id;
				$detalle_pago_cuota->cuota_id=$i;
				$detalle_pago_cuota->importe=$detalle_pago_cuotas->importe;
				$detalle_pago_cuota->fecha_cobro= $fecha_vigencia;
				$detalle_pago_cuota->save();
				//Agrego un mes a la fecha
				$fecha_vigencia = Domain_DetallePago::addMonthbyDate($fecha_vigencia);
				//echo"<br>";
				//print_r($detalle_pago_cuotas->importe);

			}

		} catch (Exception $e) {
			echo $e->getMessage();
		}
	
	
}


public function notaCreditoPolizaAduaneros($poliza){

		$estado_nota_credito = Domain_EstadoPoliza::getIdByCodigo('NOTA_DE_CREDITO');
		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza->getModelPoliza();
		//$model_poliza->estado_id=$estado_refacturado;
		//$model_poliza->save();
		$poliza_valores = $poliza->getModelPolizaValores();
		$poliza_detalle = $poliza->getModelDetalle();
		$detalle_pago = $poliza->getModelDetallePago();

		//Creo nueva poliza
		$nota_credito_poliza = new Domain_Poliza(); 
		//Tipo Aduaneros (id = 2 )
		$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');
		//$estado = Domain_EstadoPoliza::getIdByCodigo('AFECTADA');
		//$operacion_id = Domain_Helper::getHelperIdByDominioAndName('operacion', 'Refacturacion');
		try {
			//1. Poliza Detalle Seguro Comun (Ver si es para Caucion solamente)
			$m_poliza_detalle = $nota_credito_poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->motivo_garantia_id=$poliza_detalle->motivo_garantia_id;
			$m_poliza_detalle->domicilio_riesgo=$poliza_detalle->domicilio_riesgo;
			$m_poliza_detalle->localidad_riesgo=$poliza_detalle->localidad_riesgo;
			$m_poliza_detalle->provincia_riesgo=$poliza_detalle->provincia_riesgo;
			$m_poliza_detalle->acreedor_prendario=$poliza_detalle->acreedor_prendario;
			$m_poliza_detalle->mercaderia=$poliza_detalle->mercaderia;
			$m_poliza_detalle->descripcion_adicional=$poliza_detalle->descripcion_adicional;
			$m_poliza_detalle->bl=$poliza_detalle->bl;
			$m_poliza_detalle->factura=$poliza_detalle->factura;
			$m_poliza_detalle->sim=$poliza_detalle->sim;
				
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $nota_credito_poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$poliza_valores->monto_asegurado;
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$poliza_valores->iva;
			$m_poliza_valores->prima_comision=$poliza_valores->prima_comision;
			$m_poliza_valores->prima_tarifa=$poliza_valores->prima_tarifa;
			$m_poliza_valores->premio_compania=$poliza_valores->premio_compania;
			$m_poliza_valores->premio_asegurado=$poliza_valores->premio_asegurado;
			$m_poliza_valores->plus=$poliza_valores->plus;
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $nota_credito_poliza->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$model_poliza->fecha_pedido;
			$m_poliza->periodo_id=$model_poliza->periodo_id;
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->tipo_poliza_id = $tipo_poliza; //Es del tipo Aduaneros
			$m_poliza->estado_id = $estado_nota_credito;
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_aduaneros_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		$this->saveDetallePagoNotaCredito($m_poliza, $detalle_pago);
		
		return $nota_credito_poliza;
	}



//notaCreditoPolizaConstruccion

public function notaCreditoPolizaConstruccion($poliza){

		$estado_nota_credito = Domain_EstadoPoliza::getIdByCodigo('NOTA_DE_CREDITO');
		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza->getModelPoliza();
		$poliza_valores = $poliza->getModelPolizaValores();
		$poliza_detalle = $poliza->getModelDetalle();
		$detalle_pago = $poliza->getModelDetallePago();

		//Creo nueva poliza
		$nota_credito_poliza = new Domain_Poliza(); 
		
		$tipo_poliza = Domain_TipoPoliza::getIdByName('CONSTRUCCION');
		
		try {
			//1. Poliza Detalle Seguro Comun (Ver si es para Caucion solamente)
			$m_poliza_detalle = $nota_credito_poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->motivo_garantia_id=$poliza_detalle->motivo_garantia_id;
			$m_poliza_detalle->domicilio_riesgo=$poliza_detalle->domicilio_riesgo;
			$m_poliza_detalle->localidad_riesgo=$poliza_detalle->localidad_riesgo;
			$m_poliza_detalle->provincia_riesgo=$poliza_detalle->provincia_riesgo;
			$m_poliza_detalle->tipo_garantia_id=$poliza_detalle->tipo_garantia_id;
			$m_poliza_detalle->beneficiario_id=$poliza_detalle->beneficiario_id;
			$m_poliza_detalle->numero_licitacion=$poliza_detalle->numero_licitacion;
			$m_poliza_detalle->obra=$poliza_detalle->obra;
			$m_poliza_detalle->descripcion_adicional=$poliza_detalle->descripcion_adicional;
			$m_poliza_detalle->expediente=$poliza_detalle->expediente;
			$m_poliza_detalle->objeto=$poliza_detalle->objeto;
			$m_poliza_detalle->apertura_licitacion=$poliza_detalle->apertura_licitacion;
			$m_poliza_detalle->clausula_especial=$poliza_detalle->clausula_especial;
		    $m_poliza_detalle->certificaciones=$poliza_detalle->certificaciones;
		 			
			$m_poliza_detalle->save();
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $nota_credito_poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$poliza_valores->monto_asegurado;
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$poliza_valores->iva;
			$m_poliza_valores->prima_comision=$poliza_valores->prima_comision;
			$m_poliza_valores->prima_tarifa=$poliza_valores->prima_tarifa;
			$m_poliza_valores->premio_compania=$poliza_valores->premio_compania;
			$m_poliza_valores->premio_asegurado=$poliza_valores->premio_asegurado;
			$m_poliza_valores->plus=$poliza_valores->plus;
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $nota_credito_poliza->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$model_poliza->fecha_pedido;
			$m_poliza->periodo_id=$model_poliza->periodo_id;
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->tipo_poliza_id = $tipo_poliza; //Es del tipo Aduaneros
			$m_poliza->estado_id = $estado_nota_credito;
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_construccion_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		$this->saveDetallePagoNotaCredito($m_poliza, $detalle_pago);
		
		return $nota_credito_poliza;
	}

//notaCreditoPolizaJudiciales
public function notaCreditoPolizaJudiciales($poliza){

		$estado_nota_credito = Domain_EstadoPoliza::getIdByCodigo('NOTA_DE_CREDITO');
		//Traigo la poliza que tengo que copiar
		$model_poliza = $poliza->getModelPoliza();
		$poliza_valores = $poliza->getModelPolizaValores();
		$poliza_detalle = $poliza->getModelDetalle();
		$detalle_pago = $poliza->getModelDetallePago();

		//Creo nueva poliza
		$nota_credito_poliza = new Domain_Poliza(); 
		
		$tipo_poliza = Domain_TipoPoliza::getIdByName('JUDICIALES');
		
		try {

			//1. Poliza Detalle Seguro Comun (Ver si es para Caucion solamente)
			$m_poliza_detalle = $nota_credito_poliza->getModelDetallePoliza($tipo_poliza);
			$m_poliza_detalle->tipo_garantia_id=$poliza_detalle->motivo_garantia_id;
			$m_poliza_detalle->motivo_garantia_id=$poliza_detalle->motivo_garantia_id;
			$m_poliza_detalle->beneficiario_id=$poliza_detalle->beneficiario_id;
			$m_poliza_detalle->domicilio_riesgo=$poliza_detalle->domicilio_riesgo;
			$m_poliza_detalle->localidad_riesgo=$poliza_detalle->localidad_riesgo;
			$m_poliza_detalle->provincia_riesgo=$poliza_detalle->provincia_riesgo;
			$m_poliza_detalle->numero_licitacion=$poliza_detalle->numero_licitacion;
			$m_poliza_detalle->obra=$poliza_detalle->obra;
			$m_poliza_detalle->descripcion_adicional=$poliza_detalle->descripcion_adicional;
			$m_poliza_detalle->expediente=$poliza_detalle->expediente;
			$m_poliza_detalle->objeto=$poliza_detalle->objeto;
			$m_poliza_detalle->apertura_licitacion=$poliza_detalle->apertura_licitacion;
			$m_poliza_detalle->clausula_especial=$poliza_detalle->clausula_especial;
			$m_poliza_detalle->certificaciones=$poliza_detalle->certificaciones;


			$m_poliza_detalle->save();


		} catch (Exception $e) {
			echo $e->getMessage();
		}

		/*
		 * 2- Poliza Valores
		 */
		try{
			$m_poliza_valores = $nota_credito_poliza->getModelPolizaValores();
			$m_poliza_valores->monto_asegurado=$poliza_valores->monto_asegurado;
			$m_poliza_valores->moneda_id=$poliza_valores->moneda_id;
			$m_poliza_valores->iva=$poliza_valores->iva;
			$m_poliza_valores->prima_comision=$poliza_valores->prima_comision;
			$m_poliza_valores->prima_tarifa=$poliza_valores->prima_tarifa;
			$m_poliza_valores->premio_compania=$poliza_valores->premio_compania;
			$m_poliza_valores->premio_asegurado=$poliza_valores->premio_asegurado;
			$m_poliza_valores->plus=$poliza_valores->plus;
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {

            $fecha_vigencia_desde = $this->calcularPeriodo($model_poliza->fecha_vigencia, $model_poliza->periodo_id);
			$fecha_vigencia_hasta = $this->calcularPeriodo($fecha_vigencia_desde, $model_poliza->periodo_id);
			
			$m_poliza = $nota_credito_poliza->getModelPoliza();
			$m_poliza->numero_solicitud=$model_poliza->numero_solicitud;
			$m_poliza->asegurado_id=$model_poliza->asegurado_id;
			$m_poliza->agente_id=$model_poliza->agente_id;
			$m_poliza->compania_id=$model_poliza->compania_id;
			$m_poliza->productor_id=$model_poliza->productor_id;
			$m_poliza->cobrador_id=$model_poliza->cobrador_id;
			$m_poliza->fecha_pedido=$model_poliza->fecha_pedido;
			$m_poliza->periodo_id=$model_poliza->periodo_id;
			$m_poliza->fecha_vigencia= $fecha_vigencia_desde;
			$m_poliza->fecha_vigencia_hasta= $fecha_vigencia_hasta;
			$m_poliza->observaciones_asegurado=$model_poliza->observaciones_asegurado;
			$m_poliza->observaciones_compania=$model_poliza->observaciones_compania;
			$m_poliza->tipo_poliza_id = $tipo_poliza; //Es del tipo Aduaneros
			$m_poliza->estado_id = $estado_nota_credito;
			$m_poliza->operacion_id = $operacion_id;
			$m_poliza->endoso = $model_poliza->endoso+1;
			//Guarda el ID de las tablas asociadas
			$m_poliza->poliza_valores_id = $m_poliza_valores->poliza_valores_id;
			$m_poliza->poliza_detalle_id = $m_poliza_detalle->detalle_judiciales_id;
			$m_poliza->save();
			//Guardo Numero de Poliza
			$m_poliza->numero_poliza = $model_poliza->numero_poliza ;
			$m_poliza->save();

		} catch (Exception $e) {
			echo $e->getMessage();
		}
		$this->saveDetallePagoNotaCredito($m_poliza, $detalle_pago);
		
		return $nota_credito_poliza;
	}


	private function saveDetallePagoNotaCredito($m_solicitud,$m_poliza_detalle_pago){
	
		
		//1. Borra los datos de pago
		//Domain_DetallePago::deleteDetallePago($m_solicitud->poliza_id);

		
		$fecha_vigencia = $m_solicitud->fecha_vigencia;
	
	
	try {

			//echo "Guarda la forma de pago Efectivo";
			//Guardo el tipo de pago
			$detalle_pago = new Model_DetallePago();
			$detalle_pago->forma_pago_id = $m_poliza_detalle_pago->forma_pago_id;
			$detalle_pago->moneda_id = $m_poliza_detalle_pago->moneda_id;
			$detalle_pago->save();
			//Guardo en la poliza el id para asociarlo
				
			$m_solicitud->detalle_pago_id = $detalle_pago->detalle_pago_id;
			$m_solicitud->save();
			//Guardo los valores de las cuotas
			//En este caso es una sola cuota porque es efectivo
			$d_detalle_pago = new Domain_DetallePago($m_poliza_detalle_pago->detalle_pago_id);
			$detalle_pago_cuotas = $d_detalle_pago->getModelDetallePagoCuota();
			
		
			$cant_cuotas = Domain_DetallePago::getCantidadCuotas($m_poliza_detalle_pago->detalle_pago_id);

			for ($i = 1; $i <= $cant_cuotas; $i++) {

				$importe_nc = -1 * (int)$detalle_pago_cuotas->importe;
				
				$detalle_pago_cuota = new Model_DetallePagoCuota();
				$detalle_pago_cuota->detalle_pago_id = $detalle_pago->detalle_pago_id;
				$detalle_pago_cuota->cuota_id=$i;
				$detalle_pago_cuota->importe=$importe_nc;
				$detalle_pago_cuota->fecha_cobro= $fecha_vigencia;
				$detalle_pago_cuota->save();
				//Agrego un mes a la fecha
				$fecha_vigencia = Domain_DetallePago::addMonthbyDate($fecha_vigencia);
				//echo"<br>";
				//print_r($fecha_vigencia);

			}

		} catch (Exception $e) {
			echo $e->getMessage();
		}
	
	
}
	/**************Servicio para modificar poliza super admin*******************/
	public function editPolizaAduaneros($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza_detalle = $poliza->getModelDetalle();
			$m_poliza_detalle->bl=$params['bl'];
			$m_poliza_detalle->factura=$params['factura'];
			$m_poliza_detalle->sim=$params['sim'];
			$m_poliza_detalle->documentacion_id=$params['documentacion_id'];
			$m_poliza_detalle->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try{
			$m_poliza_valores = $poliza->getModelPolizaValores();
			$m_poliza_valores->iva=$params['iva'];
			$m_poliza_valores->monto_asegurado=$params['monto_asegurado'];
			$m_poliza_valores->prima_comision=$params['prima_comision'];
			$m_poliza_valores->prima_tarifa=$params['prima_tarifa'];
			$m_poliza_valores->premio_compania=$params['premio_compania'];
			$m_poliza_valores->premio_asegurado=$params['premio_asegurado'];
			$m_poliza_valores->plus=$params['plus'];
			$m_poliza_valores->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		try {


			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_poliza=$params['numero_poliza'];
			$m_poliza->fecha_vigencia=$params['fecha_vigencia'];
			$m_poliza->save();
				
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}
/**************Servicio para modificar poliza super admin*******************/
	public function editPolizaFacturaAduaneros($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}
  
	public function editPolizaFacturaConstruccion($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}

	public function editPolizaFacturaSeguroTecnico($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}

	public function editPolizaFacturaAlquiler($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}



	public function editPolizaFacturaIntegralComercio($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}

	public function editPolizaFacturaResponsabilidadCivil($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}


	public function editPolizaFacturaAccidentesPersonales($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}

	public function editPolizaFacturaIncendio($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}


	public function editPolizaFacturaIgj($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}

	public function editPolizaFacturaJudiciales($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}


	public function editPolizaFacturaVida($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}


	public function editPolizaFacturaTransporteMercaderia($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}

	public function editPolizaFacturaAutomotores($poliza,$params){
		//	$tipo_poliza = Domain_TipoPoliza::getIdByName('ADUANEROS');

		try{
			$m_poliza = $poliza->getModelPoliza();
			$m_poliza->numero_factura=$params['numero_factura'];
			
			$m_poliza->save();
		}catch (Exception $e) {
			echo $e->getMessage();
		}

		return $poliza;
	}

private function calcularPeriodo($fecha_desde,$periodo){

		if(empty($fecha_desde)) return false;
		$date = new DateTime($fecha_desde);
		
		switch ($periodo) {
			case '1':
				
			$date->add(new DateInterval('P1M'));
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			case '7':
				
			$date->add(new DateInterval('P2M'));
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			case '2':
				
			$date->add(new DateInterval('P3M'));
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			
			case '3':
				
			$date->add(new DateInterval('P4M'));
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			
			case '4':
				
			$date->add(new DateInterval('P6M'));
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			
			case '5':
				
			$date->add(new DateInterval('P1Y'));
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			
			
			case '6':
				
			$date->add(new DateInterval('P2Y'));
			//$fecha_hasta =  $date->format('Y-m-d') . "\n";
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			default:
			$fecha_hasta = null	;
			break;
		}
		
		$date_parche = new DateTime($fecha_desde);
		if($date_parche->format('d') == '31') $date->sub(new DateInterval('P1D'));
		
		$fecha_hasta =  $date->format('Y-m-d') . "\n";

		return $fecha_hasta;
	}

}

