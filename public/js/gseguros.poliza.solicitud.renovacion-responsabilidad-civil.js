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
			$("#asegurado_responsabilidad_civil").autocomplete(
					'./poliza/solicitud/listar-asegurados', {
						width : 300,
						multiple : true,
						matchContains : true,
						formatItem : formatItem,
						formatResult : formatResult
					});

			$("#asegurado_responsabilidad_civil").result(
					function(event, data, formatted) {
						var hidden = $(this).parent().next().find(">:input");
						hidden.val((hidden.val() ? hidden.val() + ";" : hidden
								.val())
								+ data[2]);
						// console.debug(data[1]);
						$("#asegurado_responsabilidad_civil_id").val(data[2]);
					});

			$('#fecha_pedido_responsabilidad_civil').datepicker(
					{
						dateFormat : 'yy-mm-dd',
						dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi',
								'Sa' ],
						monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril',
								'Mayo', 'Junio', 'Julio', 'Agosto',
								'Septiembre', 'Octubre', 'Noviembre',
								'Diciembre' ]
					});

			$('#fecha_vigencia_responsabilidad_civil').datepicker(
					{
						dateFormat : 'yy-mm-dd',
						dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi',
								'Sa' ],
						monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril',
								'Mayo', 'Junio', 'Julio', 'Agosto',
								'Septiembre', 'Octubre', 'Noviembre',
								'Diciembre' ]
					});

			$('#fecha_vigencia_hasta_responsabilidad_civil').datepicker(
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

			$('#datos_seguro_responsabilidad_civil_show').click(function() {
				$('#datos_seguro_responsabilidad_civil').toggle();
			});
			$('#detalle_riesgo_responsabilidad_civil_show').click(function() {
				
				$('#detalle_riesgo_responsabilidad_civil').toggle();
			});
			$('#datos_solicitud_responsabilidad_civil_show').click(function() {
				$('#datos_solicitud_responsabilidad_civil').toggle();
			});
			$('#valores_seguro_responsabilidad_civil_show').click(function() {
				$('#valores_seguro_responsabilidad_civil').toggle();
			});
			$('#observaciones_seguro_responsabilidad_civil_show').click(function() {
				$('#observaciones_seguro_responsabilidad_civil').toggle();
			});
			
			
			$("#datos_tarjeta_responsabilidad_civil_show").hide();
			$('#forma_pago_responsabilidad_civil_id').change(function(){
				//Si es el ID de pago con tarjeta
				//No deberia estar hardcodeado
				if($('#forma_pago_responsabilidad_civil_id').val()==2	){
					$("#datos_tarjeta_responsabilidad_civil_show").show();
				}else{
					$("#datos_tarjeta_responsabilidad_civil_show").hide();
				}
			});
			

			//cuando modifica los valores
			$('#valor_cuota_responsabilidad_civil')
					.change(
							function() {

								var s_cuotas = $('#cuotas_responsabilidad_civil'),
								s_valor_cuota = $('#valor_cuota_responsabilidad_civil');
								var importe=0;
								var cuotas=0;
								var valor_cuota = 0;

								cuotas = parseFloat(s_cuotas.val());
								valor_cuota = parseFloat(s_valor_cuota.val());

								if ((cuotas != 0 && valor_cuota != 0)) {
									importe = (valor_cuota * cuotas);
									$('#importe_responsabilidad_civil').val(importe);
								}

							});

			$('#cuotas_responsabilidad_civil')
					.change(
							function() {

								var s_cuotas = $('#cuotas_responsabilidad_civil'), 
								s_valor_cuota = $('#valor_cuota_responsabilidad_civil');
								var importe=0;
								var cuotas=0;
								var valor_cuota = 0;

								cuotas = parseFloat(s_cuotas.val());
								valor_cuota = parseFloat(s_valor_cuota.val());

								if ((cuotas != 0 && valor_cuota != 0)) {
									importe = (valor_cuota * cuotas);
									$('#importe_responsabilidad_civil').val(importe);
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
				dd = '0' + dd;
			}
			if (mm < 10) {
				mm = '0' + mm;
			}
			var date_today = yyyy + '-' + mm + '-' + dd;
			// alert(date_today);
			// Por defecto va la fecha del pedido del dia

		 if($('#fecha_pedido_responsabilidad_civil').val()==''){
			 
			 $('#fecha_pedido_responsabilidad_civil').val(date_today);
			$('#fecha_vigencia_responsabilidad_civil').val(date_today);
 
		 }
			
		 $('#fecha_pedido_agente_responsabilidad_civil').val(date_today);
			$('#fecha_vigencia_agente_responsabilidad_civil').val(date_today);
			
			
			
			// Guardar Solicitud
			$('#save_solicitud_responsabilidad_civil').click(function() {
				
				if(!$("#solicitud_poliza_responsabilidad_civil").valid())return false;	 

								// alert('clic');
								// trae id de tab
								var tabs_sel = $('#tabs').tabs();
								var idx = tabs_sel.tabs('option',
										'selected');

								// Trae el tab correspondiente
								var tab = $('#tabs ul li a')[idx];
								// //console.debug($('#tabs ul li a'));
								var href = $(tab).attr('href');

								// suponemos que el form es valido

								var data = $('#solicitud_poliza_responsabilidad_civil').serializeArray();
								// console.debug(data);

								$.ajax({
											url : "./poliza/solicitud/renovacion-responsabilidad-civil",
											data : data,
											success : function(result) {
												$(href).html(result);
											}
										});

							});

			// Aprobar Solicitud
			$('#aprobar_solicitud_responsabilidad_civil').click(function() {

				// busca el id de la solicitud a confirmar
				var solicitud_id = $('#solicitud_responsabilidad_civil_id').val();
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
			$('#confirmar_solicitud_responsabilidad_civil').click(function() {

				// busca el id de la solicitud a confirmar
				var solicitud_id = $('#solicitud_responsabilidad_civil_id').val();
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
			$('#anular_solicitud_responsabilidad_civil').click(function() {

				// busca el id de la solicitud a confirmar
				var solicitud_id = $('#solicitud_responsabilidad_civil_id').val();
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
			$("#solicitud_poliza_responsabilidad_civil").validate({
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
			
			
});//End JQuery
