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
			//$('#observaciones_seguro_integral_comercio').toggle();
			//$('#valores_seguro_integral_comercio').toggle();

			$('#datos_seguro_integral_comercio_show').click(function() {
				$('#datos_seguro').toggle();
			});
			$('#detalle_riesgo_integral_comercio_show').click(function() {
				$('#detalle_riesgo').toggle();
			});
			$('#datos_solicitud_integral_comercio_show').click(function() {
				$('#datos_solicitud').toggle();
			});
			$('#valores_seguro_integral_comercio_show').click(function() {
				$('#valores_seguro_integral_comercio').toggle();
			});

			$('#datos_solicitud_integral_comercio_show').click(function() {
				$('#datos_solicitud').toggle();
			});

			$('#observaciones_seguro_integral_comercio_show').click(function() {
				$('#observaciones_seguro_integral_comercio').toggle();
			});
			$('#detalle_riesgo_integral_comercio_show').click(function() {
				$('#detalle_riesgo_integral_comercio').toggle();
			});
			
			$("#datos_tarjeta_integral_comercio_show").hide();

			//Validacion tipo endoso
			//Endoso de Aumento: se permite sólo corregir los campos que están en “valores del seguro” por sobre el importe actual.
			//Lo dejo comentado porque la validacion se puede hacer desde el controlador.
			/*$('#tipo_integral_comercio_endoso')
					.change(
							function() {
								
							//Trae los valores
								var s_cuotas = $('#cuotas_integral_comercio_endoso'),
								s_valor_cuota = $('#valor_cuota_integral_comercio_endoso');
								var importe=0;
								var cuotas=0;
								var valor_cuota = 0;

								cuotas = parseFloat(s_cuotas.val());
								valor_cuota = parseFloat(s_valor_cuota.val());

								
								if ((cuotas != 0 && valor_cuota != 0)) {
									
									importe = (valor_cuota * cuotas);
									$('#importe_integral_comercio_endoso').val(importe);
								}

							});


			*/
			$('#forma_pago_id').change(function(){
				//Si es el ID de pago con tarjeta
				//No deberia estar hardcodeado
				if($('#forma_pago_id').val()==2	){
					$("#datos_tarjeta_show").show();
				}else{
					$("#datos_tarjeta_show").hide();
				}
			});
			
			/*
			 * Calcular importe
			 */
			
			/*
			 * Muestra importe
			 */

			//cuando modifica los valores
			$('#valor_cuota_integral_comercio_endoso')
					.change(
							function() {
								

								var s_cuotas = $('#cuotas_integral_comercio_endoso'),
								s_valor_cuota = $('#valor_cuota_integral_comercio_endoso');
								var importe=0;
								var cuotas=0;
								var valor_cuota = 0;

								cuotas = parseFloat(s_cuotas.val());
								valor_cuota = parseFloat(s_valor_cuota.val());

								
								if ((cuotas != 0 && valor_cuota != 0)) {
									
									importe = (valor_cuota * cuotas);
									$('#importe_integral_comercio_endoso').val(importe);
								}

							});

			$('#cuotas_integral_comercio_endoso')
					.change(
							function() {
								
								var s_cuotas = $('#cuotas_integral_comercio_endoso'), 
								s_valor_cuota = $('#valor_cuota_integral_comercio_endoso');
								var importe=0;
								var cuotas=0;
								var valor_cuota = 0;

								cuotas = parseFloat(s_cuotas.val());
								valor_cuota = parseFloat(s_valor_cuota.val());

								if ((cuotas != 0 && valor_cuota != 0)) {
									importe = (valor_cuota * cuotas);
									$('#importe_integral_comercio_endoso').val(importe);
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
			// alert($('#fecha_pedido'));
		 if($('#fecha_pedido').val()==''){
			 
			 $('#fecha_pedido').val(date_today);
			$('#fecha_vigencia').val(date_today);
 
		 }
			
		 $('#fecha_pedido_agente').val(date_today);
			$('#fecha_vigencia_agente').val(date_today);
			
			
			


			// Guardar Solicitud
			$('#save_endoso_poliza_integral_comercio').click(function() {
				
								// trae id de tab
								var tabs_sel = $('#tabs').tabs();
								var idx = tabs_sel.tabs('option',
										'selected');

								// Trae el tab correspondiente
								var tab = $('#tabs ul li a')[idx];
								// //console.debug($('#tabs ul li a'));
								var href = $(tab).attr('href');

								// suponemos que el form es valido

								var data = $('#endoso_poliza_integral_comercio').serializeArray();
								// console.debug(data);

								$.ajax({
											url : "./poliza/poliza/endoso-poliza-integral-comercio",
											data : data,
											success : function(result) {
												$(href).html(result);
											}
										});
								
							});



			// Aprobar Solicitud
			$('#aprobar_solicitud_integral_comercio').click(function() {

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
			$('#confirmar_solicitud_integral_comercio').click(function() {

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
					url : "./poliza/solicitud/confirmar-solicitud",
					data : {
						solicitud_id : solicitud_id
					},
					success : function(result) {
						$(href).html(result);
					}
				});

			});

			// Anular Solicitud
			$('#anular_solicitud_integral_comercio').click(function() {

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

			
//validar formulario de alta solicitud
			$("#endoso_poliza_integral_comercio").validate({
			    rules: {
			        asegurado: {
			            required: true
			        }
			    },
			    messages: {
			        asegurado: {
			            required: "No puede estar vacio"
			        }
			    }
			});
			
			
		});
 
