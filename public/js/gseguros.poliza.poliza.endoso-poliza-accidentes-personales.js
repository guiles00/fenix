//gseguros.poliza.poliza.endoso-poliza-accidentes_personales.js
$(document).ready(
		function() {
			function confirmar() {
				return confirm('Desea agregar el registro?');
			}

			// Autocomplete cliente
			function formatItem(row) {
				// console.debug(row);
				return row[0] + ' ' + row[1];// + "(<strong>id: " + row[1] +
												// "</strong>)";
			}
			function formatResult(row) {
				// console.debug(row);
				return row[0] + ' ' + row[1];
				// return row[0].replace(/(<.+?>)/gi, '');
			}
			$("#asegurado").autocomplete(
					'./poliza/solicitud/listar-asegurados', {
						width : 300,
						multiple : true,
						matchContains : true,
						formatItem : formatItem,
						formatResult : formatResult
					});

			$("#asegurado").result(
					function(event, data, formatted) {
						var hidden = $(this).parent().next().find(">:input");
						hidden.val((hidden.val() ? hidden.val() + ";" : hidden
								.val())
								+ data[2]);
						// console.debug(data[1]);
						$("#asegurado_id").val(data[2]);
					});

			$('#fecha_pedido').datepicker(
					{
						dateFormat : 'yy-mm-dd',
						dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi',
								'Sa' ],
						monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril',
								'Mayo', 'Junio', 'Julio', 'Agosto',
								'Septiembre', 'Octubre', 'Noviembre',
								'Diciembre' ]
					});

			$('#fecha_vigencia').datepicker(
					{
						dateFormat : 'yy-mm-dd',
						dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi',
								'Sa' ],
						monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril',
								'Mayo', 'Junio', 'Julio', 'Agosto',
								'Septiembre', 'Octubre', 'Noviembre',
								'Diciembre' ]
					});

			$('#fecha_vigencia_hasta').datepicker(
					{
						dateFormat : 'yy-mm-dd',
						dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi',
								'Sa' ],
						monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril',
								'Mayo', 'Junio', 'Julio', 'Agosto',
								'Septiembre', 'Octubre', 'Noviembre',
								'Diciembre' ]
					});

			// Oculta campos dar de alta solicitud o buscar

			//$('#datos_seguro').toggle();
			//$('#detalle_riesgo').toggle();
			//$('#valores_seguro').toggle();
			// $('#datos_solicitud').toggle();
			//$('#observaciones_seguro_accidentes_personales').toggle();
			//$('#valores_seguro_accidentes_personales').toggle();

			$('#datos_seguro_show').click(function() {
				$('#datos_seguro').toggle();
			});
			$('#detalle_riesgo_show').click(function() {
				$('#detalle_riesgo').toggle();
			});
			$('#datos_solicitud_show').click(function() {
				$('#datos_solicitud').toggle();
			});
			$('#valores_seguro_accidentes_personales_show').click(function() {
				$('#valores_seguro_accidentes_personales').toggle();
			});

			$('#datos_solicitud_show').click(function() {
				$('#datos_solicitud').toggle();
			});

			$('#observaciones_seguro_accidentes_personales_show').click(function() {
				$('#observaciones_seguro_accidentes_personales').toggle();
			});
			$('#detalle_riesgo_accidentes_personales_show').click(function() {
				$('#detalle_riesgo_accidentes_personales').toggle();
			});
			
			$("#datos_tarjeta_show").hide();
			$('#forma_pago_id').change(function(){
				//Si es el ID de pago con tarjeta
				//No deberia estar hardcodeado
				if($('#forma_pago_id').val()==2	){
					$("#datos_tarjeta_show").show();
				}else{
					$("#datos_tarjeta_show").hide();
				}
			});
			
//calcula importe - plus - premio asegurado
			
			$('#plus_endoso')
			.change(
					function() {

						var s_plus = $('#plus_endoso'),
						s_importe = $('#importe_accidentes_personales_endoso');
						 
						var plus= parseFloat(s_plus.val());
						var importe = parseFloat(s_importe.val());

						//console.debug(plus);
						//console.debug(importe);
						if ((plus != 0 && importe != 0)) {
							//importe = plus + premio_asegurado;
							premio_asegurado = importe - plus;
							$('#premio_asegurado_endoso').val(premio_asegurado.toFixed(2));
						}
					});
			$('#premio_asegurado_endoso')
			.change(
					function() {

						var s_premio_asegurado = $('#premio_asegurado_endoso'),
						s_importe = $('#importe_accidentes_personales_endoso');
						 
						var premio_asegurado= parseFloat(s_premio_asegurado.val());
						var importe = parseFloat(s_importe.val());

						//console.debug(premio_asegurado);
						//console.debug(importe);
						if ((premio_asegurado != 0 && importe != 0)) {
							//importe = plus + premio_asegurado;
							 plus = importe - premio_asegurado;
							$('#plus_endoso').val(plus.toFixed(2));
						}

					});
			

			//cuando modifica los valores
			$('#valor_cuota_accidentes_personales_endoso')
					.change(
							function() {

								var s_cuotas = $('#cuotas_accidentes_personales_endoso'),
								s_valor_cuota = $('#valor_cuota_accidentes_personales_endoso');
								var importe=0;
								var cuotas=0;
								var valor_cuota = 0;

								cuotas = parseFloat(s_cuotas.val());
								valor_cuota = parseFloat(s_valor_cuota.val());

								if ((cuotas != 0 && valor_cuota != 0)) {
									importe = (valor_cuota * cuotas);
									$('#importe_accidentes_personales_endoso').val(importe);
								}

							});

			$('#cuotas_accidentes_personales_endoso')
					.change(
							function() {

								var s_cuotas = $('#cuotas_accidentes_personales_endoso'), 
								s_valor_cuota = $('#valor_cuota_accidentes_personales_endoso');
								var importe=0;
								var cuotas=0;
								var valor_cuota = 0;

								cuotas = parseFloat(s_cuotas.val());
								valor_cuota = parseFloat(s_valor_cuota.val());

								if ((cuotas != 0 && valor_cuota != 0)) {
									importe = (valor_cuota * cuotas);
									$('#importe_accidentes_personales_endoso').val(importe);
								}

							});
			
			// traigo la fecha actual con el formato yyyy-mm-dd
			// sumo un mes, tres meses, etc.. y cambio el formato
			// le asigno los valores
			var today = new Date();
			var dd = today.getDate();
			var mm = today.getMonth() + 1;// January is 0!
			var yyyy = today.getFullYear();
			if (dd < 10) {
				dd = '0' + dd
			}
			if (mm < 10) {
				mm = '0' + mm
			}
			var date_today = yyyy + '-' + mm + '-' + dd;
			// alert(date_today);
			// Por defecto va la fecha del pedido del dia
			// alert($('#fecha_pedido').val());
			 if($('#fecha_pedido').val()==''){
			$('#fecha_pedido').val(date_today);
			$('#fecha_vigencia').val(date_today);
		}
			 $('#fecha_pedido_agente').val(date_today);
				$('#fecha_vigencia_agente').val(date_today);
			
			
			// Guardar Solicitud
			$('#save_endoso_poliza_accidentes_personales').click(function() {
			//	alert('clic');
								// trae id de tab
								var tabs_sel = $('#tabs').tabs();
								var idx = tabs_sel.tabs('option',
										'selected');

								// Trae el tab correspondiente
								var tab = $('#tabs ul li a')[idx];
								// //console.debug($('#tabs ul li a'));
								var href = $(tab).attr('href');

								// suponemos que el form es valido

								var data = $('#endoso_poliza_accidentes_personales').serializeArray();
								// console.debug(data);

								$.ajax({
											url : "./poliza/poliza/endoso-poliza-accidentes-personales",
											data : data,
											success : function(result) {
												$(href).html(result);
											}
										});
								
							});

			
			// Aprobar Solicitud
			$('#aprobar_solicitud_aduanero').click(function() {

				// busca el id de la solicitud a confirmar
				var solicitud_id = $('#solicitud_id').val();
				// trae id de tab
				var tabs_sel = $('#tabs').tabs();
				var idx = tabs_sel.tabs('option', 'selected');

				// Trae el tab correspondiente
				var tab = $('#tabs ul li a')[idx];
				// //console.debug($('#tabs ul li a'));
				var href = $(tab).attr('href');

				$.ajax({
					url : "./poliza/solicitud/aprobar-solicitud",
					data : {
						solicitud_id : solicitud_id
					},
					success : function(result) {
						$(href).html(result);
					}
				});

			});

			// Confirmar Solicitud
			$('#confirmar_solicitud_aduanero').click(function() {

				// busca el id de la solicitud a confirmar
				var solicitud_id = $('#solicitud_id').val();
				// trae id de tab
				var tabs_sel = $('#tabs').tabs();
				var idx = tabs_sel.tabs('option', 'selected');

				// Trae el tab correspondiente
				var tab = $('#tabs ul li a')[idx];
				// //console.debug($('#tabs ul li a'));
				var href = $(tab).attr('href');

				var importe_accidentes_personales = $('#importe_accidentes_personales').val();
				if(importe_accidentes_personales <= 0 ){
					alert('No se puede crear la poliza - El importe debe ser mayor a cero!');
					return false;
				}
				$.ajax({
					url : "./poliza/solicitud/confirmar-solicitud-caucion",
					data : {
						solicitud_id : solicitud_id
					},
					success : function(result) {
						$(href).html(result);
					}
				});

			});

			// Anular Solicitud
			$('#anular_solicitud_aduanero').click(function() {

				// busca el id de la solicitud a confirmar
				var solicitud_id = $('#solicitud_id').val();
				// trae id de tab
				var tabs_sel = $('#tabs').tabs();
				var idx = tabs_sel.tabs('option', 'selected');

				// Trae el tab correspondiente
				var tab = $('#tabs ul li a')[idx];
				// //console.debug($('#tabs ul li a'));
				var href = $(tab).attr('href');

				$.ajax({
					url : "./poliza/solicitud/anular-solicitud",
					data : {
						solicitud_id : solicitud_id
					},
					success : function(result) {
						$(href).html(result);
					}
				});

			});

			
			
			// Enviar Solicitud Compania
			$('#enviar_compania_endoso_poliza_accidentes_personales').click(function() {

				//$(show_result).html('');
				// busca el id de la solicitud a confirmar
				var poliza_id = $('#poliza_endoso_id').val();
				// trae id de tab
				var tabs_sel = $('#tabs').tabs();
				var idx = tabs_sel.tabs('option', 'selected');

				// Trae el tab correspondiente
				var tab = $('#tabs ul li a')[idx];
				// //console.debug($('#tabs ul li a'));
				var href = $(tab).attr('href');
				var show_result = href+' #show_result';	

				$.ajax({
					url : "./poliza/poliza/enviar-solicitud-endoso-compania-accidentes_personales",
					data : {
						poliza_id : poliza_id
					},
					success : function(result) {
						$(show_result).html(result);
						//devuelve el resultado e indica si tuvo exito o no
					}
				});

			});

			
			var poliza_id = $('#poliza_endoso_id').val();
			/** Si es de refacturacion **/

			var tipo_endoso_id = $('#tipo_accidentes_personales_endoso').val();
			
			/*if(tipo_endoso_id == 5){

				$.ajax({
							url : "./poliza/poliza/trae-data-poliza",
							data : {
								poliza_id : poliza_id
							},
							success : function(result) {
								
								var data = JSON.parse(result);
								//Le pongo la fecha de vigencia, es la fecha de vigencia que tiene la anterior poliza
								$('#fecha_pedido').val(data.fecha_vigencia_hasta);
								$('#fecha_vigencia').val(data.fecha_vigencia_hasta);

								}
							});

			}*/
			/** Modificar parametros del formulario cuando selecciona endoso de refacturacion **/
/*
			$('#tipo_accidentes_personales_endoso').change(function(data){
				//console.debug(data.target.value);
				var opcion = data.target.value;
				//alert('modificandola');
				//Si es refacturacion (Pongo un switch porque puede ser que hayan otras opciones)
				switch(opcion) {
					
					case '5':
					
							$.ajax({
							url : "./poliza/poliza/trae-data-poliza",
							data : {
								poliza_id : poliza_id
							},
							success : function(result) {
								
								var data = JSON.parse(result);
								//Le pongo la fecha de vigencia, es la fecha de vigencia que tiene la anterior poliza
								$('#fecha_pedido').val(data.fecha_vigencia_hasta);
								$('#fecha_vigencia').val(data.fecha_vigencia_hasta);
								//La fecha de vigencia es -- No es lo mas elegante pero lo quiero terminar ya
								
								//console.debug(fecha_hasta);
								}
							});
					//$('#fecha_vigencia').val('01/01/2011');
					console.debug('modifica el formulario');



					break;
					default:
					return false;

				}

			});

*/
		function calcularPeriodo(a,m,d,fecha_desde,periodo){

		if( fecha_desde == '') return false;
		
		//var date = new Date(fecha_desde);
		var date = new Date(a,m,d);
		console.debug(date);
		//var a = Date.parse(fecha_desde);
		console.debug(date.getDay());
		/*date.setMonth(date.getMonth() + 1) ;	
		console.debug(date);
		*/
		switch (periodo) {
			case 1:
				
			date.setMonth(date.getMonth() + 1) ;	
			
			if (date.getMonth() < 10) { var mes = '0' + date.getMonth(); }

			var string_date = date.getFullYear()+'-'+ mes +'-'+ date.getDate();
			return string_date;

			break;
			case 7:
				
			
			break;
		/*	case '2':
				
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
			*/
			case 5:
				
			date.setYear(date.getFullYear() + 1) ;	
			
			if (date.getMonth() < 10) { var mes = '0' + date.getMonth(); }

			var string_date = date.getFullYear()+'-'+ mes +'-'+ date.getDate();
			return string_date;
			break;
			
			/*
			case '6':
				
			$date->add(new DateInterval('P2Y'));
			//$fecha_hasta =  $date->format('Y-m-d') . "\n";
			$fecha_hasta =  $date->format('Y-m-d') . "\n";
			break;
			*/
			default:
			return  null	;
			break;
		}
		
		/*$date_parche = new DateTime($fecha_desde);
		if($date_parche->format('d') == '31') $date->sub(new DateInterval('P1D'));
		
		$fecha_hasta =  $date->format('Y-m-d') . "\n";

		return $fecha_hasta;
		*/
	}


			
		});

