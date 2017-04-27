$(document).ready(
		function() {
			function confirmar() {
				return confirm('Desea agregar el registro?');
			}

			
			$('#fecha_vigencia_poliza').datepicker(
					{
						dateFormat : 'yy-mm-dd',
						dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi',
								'Sa' ],
						monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril',
								'Mayo', 'Junio', 'Julio', 'Agosto',
								'Septiembre', 'Octubre', 'Noviembre',
								'Diciembre' ]
					});

			

			// Oculta campos dar de alta poliza o buscar

			//$('#datos_seguro_automotor_poliza').toggle();
			//$('#detalle_riesgo_automotor_poliza').toggle();
			//$('#valores_seguro_automotor_poliza').toggle();
			// $('#datos_poliza').toggle();

			$('#datos_seguro_automotor_poliza_show').click(function() {
				$('#datos_seguro_automotor_poliza').toggle();
			});
			$('#detalle_riesgo_automotor_poliza_show').click(function() {
				$('#detalle_riesgo_automotor_poliza').toggle();
			});
			$('#datos_poliza_automotor_poliza_show').click(function() {
				$('#datos_poliza_automotor').toggle();
			});
			$('#valores_seguro_automotor_poliza_show').click(function() {
				$('#valores_seguro_automotor_poliza').toggle();
			});

			$('#datos_poliza_poliza_show').click(function() {
				$('#datos_poliza_poliza').toggle();
			});

			
			//calcula importe - plus - premio asegurado
			
			$('#plus')
			.change(
					function() {

						var s_plus = $('#plus'),
						s_importe = $('#importe');
						 
						var plus= parseFloat(s_plus.val());
						var importe = parseFloat(s_importe.val());

						//console.debug(plus);
						//console.debug(importe);
						if ((plus != 0 && importe != 0)) {
							//importe = plus + premio_asegurado;
							premio_asegurado = importe - plus;
							$('#premio_asegurado').val(premio_asegurado.toFixed(2));
						}
					});
			$('#premio_asegurado')
			.change(
					function() {

						var s_premio_asegurado = $('#premio_asegurado'),
						s_importe = $('#importe');
						 
						var premio_asegurado= parseFloat(s_premio_asegurado.val());
						var importe = parseFloat(s_importe.val());

						//console.debug(premio_asegurado);
						//console.debug(importe);
						if ((premio_asegurado != 0 && importe != 0)) {
							//importe = plus + premio_asegurado;
							 plus = importe - premio_asegurado;
							$('#plus').val(plus.toFixed(2));
						}

					});
			
		});

function savePolizaAutomotores(f) {

	var url = f.action;

	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	// Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	// //console.debug($('#tabs ul li a'));
	var href = $(tab).attr('href');
	
	/*
	 * - Nº de poliza
		 * - Fecha Vigencia
		 * - Prima
		 * - Premio Compañia
		 * - Premio
		 * - Plus

	 */
	$.ajax({
				url : url,
				data : {
					save : f.save.value
					,poliza_id : f.poliza_id.value
					,numero_poliza : f.numero_poliza.value
					//,asegurado_id : f.asegurado_id.value
					//,agente_id : f.agente_id.value
					//,compania_id : f.compania_id.value
					//,productor_id : f.productor_id.value
					//,cobrador_id : f.cobrador_id.value
					// Detalle Pago
					
					//,monto_asegurado : f.monto_asegurado.value
					//,moneda_id : f.moneda_id.value
					//,forma_pago_id : f.forma_pago_id.value
					,iva:f.iva.value
					,prima_comision:f.prima_comision.value
					//,prima_tarifa:f.prima_tarifa.value
					,premio_compania:f.premio_compania.value
					,premio_asegurado : f.premio_asegurado.value
					,plus : f.plus.value
					// Detalle Automotor
					
					//,tipo_vehiculo_id : f.tipo_vehiculo_id.value
					//,tipo_cobertura_id : f.tipo_cobertura_id.value
					//,anio_automotor : f.anio_automotor.value
					//,marca_automotor : f.marca_automotor.value
					//,tipo_automotor : f.tipo_automotor.value
					//,modelo_automotor : f.modelo_automotor.value
					//,color_automotor : f.color_automotor.value
					//,patente_automotor : f.patente_automotor.value
					//,cilindrada_automotor : f.cilindrada_automotor.value
					//,serial_c_automotor : f.serial_c_automotor.value
					//,serial_automotor : f.serial_automotor.value
					//,accesorios_automotor : f.accesorios_automotor.value
					//,uso_automotor : f.uso_automotor.value
					//,capacidad_automotor : f.capacidad_automotor.value
					//,pasajeros_automotor : f.pasajeros_automotor.value
					//,flota_automotor : f.flota_automotor.value
					//,fecha_titulo_automotor : f.fecha_titulo_automotor.value
					//,titular_automotor : f.titular_automotor.value
					//,numero_certificado_automotor : f.numero_certificado_automotor.value
					//,estado_vehiculo_automotor : f.estado_vehiculo_automotor.value
					//,estado_luces_automotor : f.estado_luces_automotor.value
					//,estado_motor_automotor : f.estado_motor_automotor.value
					//,estado_carroceria_automotor : f.estado_carroceria_automotor.value
					//,estado_cubiertas_automotor : f.estado_cubiertas_automotor.value
					// ,estado_automotor:f.estado_automotor.value
					
					//,tipo_combustion_automotor : f.tipo_combustion_automotor.value
					//,sistema_seguridad_automotor : f.sistema_seguridad_automotor.value
					//,acreedor_prendario_automotor : f.acreedor_prendario_automotor.value
					//,otros_automotor : f.otros_automotor.value
					//,fecha_pedido : f.fecha_pedido.value
					//,periodo_id : f.periodo_id.value
					,fecha_vigencia : f.fecha_vigencia.value
					//,cuotas : f.cuotas.value
					//,observaciones_asegurado : f.observaciones_asegurado.value
					//,observaciones_compania : f.observaciones_compania.value
				}

				,
				success : function(result) {
					// console.debug(result);
					$(href).html(result);
				}
			});

	return false;
}

