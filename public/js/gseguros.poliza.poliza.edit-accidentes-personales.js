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

			// Oculta campos dar de alta poliza o buscar

			//$('#datos_seguro').toggle();
			//$('#detalle_riesgo').toggle();
			//$('#valores_seguro').toggle();
			// $('#datos_poliza').toggle();
			//$('#observaciones_seguro_accidentes_personales').toggle();
			//$('#valores_seguro_accidentes_personales').toggle();

			$('#datos_seguro_show').click(function() {
				$('#datos_seguro').toggle();
			});
			$('#detalle_riesgo_show').click(function() {
				$('#detalle_riesgo').toggle();
			});
			$('#datos_poliza_show').click(function() {
				$('#datos_poliza').toggle();
			});
			$('#valores_seguro_accidentes_personales_show').click(function() {
				$('#valores_seguro_accidentes_personales').toggle();
			});

			$('#datos_poliza_show').click(function() {
				$('#datos_poliza').toggle();
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
			
			/*
			 * Calcular importe
			 */
			
			/*
			 * Muestra importe
			 */

			//cuando modifica los valores
			$('#valor_cuota_accidentes_personales')
					.change(
							function() {

								var s_cuotas = $('#cuotas_accidentes_personales'),
								s_valor_cuota = $('#valor_cuota_accidentes_personales');
								var importe=0;
								var cuotas=0;
								var valor_cuota = 0;

								cuotas = parseFloat(s_cuotas.val());
								valor_cuota = parseFloat(s_valor_cuota.val());

								if ((cuotas != 0 && valor_cuota != 0)) {
									importe = (valor_cuota * cuotas);
									$('#importe_accidentes_personales').val(importe);
								}

							});

			$('#cuotas_accidentes_personales')
					.change(
							function() {

								var s_cuotas = $('#cuotas_accidentes_personales'), 
								s_valor_cuota = $('#valor_cuota_accidentes_personales');
								var importe=0;
								var cuotas=0;
								var valor_cuota = 0;

								cuotas = parseFloat(s_cuotas.val());
								valor_cuota = parseFloat(s_valor_cuota.val());

								if ((cuotas != 0 && valor_cuota != 0)) {
									importe = (valor_cuota * cuotas);
									$('#importe_accidentes_personales').val(importe);
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
			
		

//calcula importe - plus - premio asegurado
			
			$('#plus')
			.change(
					function() {

						var s_plus = $('#plus'),
						s_importe = $('#importe_accidentes_personales');
						 
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
						s_importe = $('#importe_accidentes_personales');
						 
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

			// Guardar poliza
			$('#save_poliza_accidentes_personales').click(function() {
				
				//if(!$("#poliza_poliza_accidentes_personales").valid())return false;	 

								 
								// trae id de tab
								var tabs_sel = $('#tabs').tabs();
								var idx = tabs_sel.tabs('option',
										'selected');

								// Trae el tab correspondiente
								var tab = $('#tabs ul li a')[idx];
								// //console.debug($('#tabs ul li a'));
								var href = $(tab).attr('href');

								// suponemos que el form es valido

								var data = $('#poliza_poliza_accidentes_personales').serializeArray();
							 //console.debug(data);

								$.ajax({
											url : "./poliza/poliza/edit-poliza-accidentes-personales",
											data : data,
											success : function(result) {
												$(href).html(result);
											}
										});
								
							});

		

			// Anular poliza
			$('#anular_poliza_accidentes_personales').click(function() {

				// busca el id de la poliza a confirmar
				var poliza_id = $('#poliza_id').val();
				// trae id de tab
				var tabs_sel = $('#tabs').tabs();
				var idx = tabs_sel.tabs('option', 'selected');

				// Trae el tab correspondiente
				var tab = $('#tabs ul li a')[idx];
				// //console.debug($('#tabs ul li a'));
				var href = $(tab).attr('href');

				$.ajax({
					url : "./poliza/poliza/anular-poliza",
					data : {
						poliza_id : poliza_id
					},
					success : function(result) {
						$(href).html(result);
					}
				});

			});

			
//validar formulario de alta poliza
			$("#poliza_poliza_accidentes_personales").validate({
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
			
			
			// Enviar poliza Compania
			$('#enviar_compania_poliza_accidentes_personales').click(function() {

				// busca el id de la poliza a confirmar
				var poliza_id = $('#poliza_id').val();
				// trae id de tab
				var tabs_sel = $('#tabs').tabs();
				var idx = tabs_sel.tabs('option', 'selected');

				// Trae el tab correspondiente
				var tab = $('#tabs ul li a')[idx];
				// //console.debug($('#tabs ul li a'));
				var href = $(tab).attr('href');
				var show_result = href+' #show_result';	
				$.ajax({
					url : "./poliza/poliza/enviar-poliza-compania-accidentes_personales",
					data : {
						poliza_id : poliza_id
					},
					success : function(result) {
						$(show_result).html(result);
						//devuelve el resultado e indica si tuvo exito o no
					}
				});

			});

			
			
		});

