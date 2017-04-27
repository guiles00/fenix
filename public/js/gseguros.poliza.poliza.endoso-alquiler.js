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
			//$('#observaciones_seguro_alquiler').toggle();
			//$('#valores_seguro_alquiler').toggle();

			$('#datos_seguro_alquiler_show').click(function() {
				$('#datos_seguro').toggle();
			});
			$('#detalle_riesgo_alquiler_show').click(function() {
				$('#detalle_riesgo').toggle();
			});
			$('#datos_solicitud_alquiler_show').click(function() {
				$('#datos_solicitud').toggle();
			});
			$('#valores_seguro_alquiler_show').click(function() {
				$('#valores_seguro_alquiler').toggle();
			});

			$('#datos_solicitud_alquiler_show').click(function() {
				$('#datos_solicitud').toggle();
			});

			$('#observaciones_seguro_alquiler_show').click(function() {
				$('#observaciones_seguro_alquiler').toggle();
			});
			$('#detalle_riesgo_alquiler_show').click(function() {
				$('#detalle_riesgo_alquiler').toggle();
			});
			
			$("#datos_tarjeta_alquiler_show").hide();
			$('#forma_pago_id').change(function(){
				//Si es el ID de pago con tarjeta
				//No deberia estar hardcodeado
				if($('#forma_pago_id').val()==2	){
					$("#datos_tarjeta_show").show();
				}else{
					$("#datos_tarjeta_show").hide();
				}
			});
			alert('hola');
			/*
			 * Calcular importe
			 */
			//cuando modifica los valores
			$('#valor_cuota_alquiler_endoso')
					.change(
							function() {
								alert('hola');

								var s_cuotas = $('#cuotas_alquiler_endoso'),
								s_valor_cuota = $('#valor_cuota_alquiler_endoso');
								var importe=0;
								var cuotas=0;
								var valor_cuota = 0;

								cuotas = parseFloat(s_cuotas.val());
								valor_cuota = parseFloat(s_valor_cuota.val());

								
								if ((cuotas != 0 && valor_cuota != 0)) {
									
									importe = (valor_cuota * cuotas);
									$('#importe_alquiler_endoso').val(importe);
								}

							});

			$('#cuotas_alquiler_endoso')
					.change(
							function() {
								alert('hola');

								var s_cuotas = $('#cuotas_alquiler_endoso'), 
								s_valor_cuota = $('#valor_cuota_alquiler_endoso');
								var importe=0;
								var cuotas=0;
								var valor_cuota = 0;

								cuotas = parseFloat(s_cuotas.val());
								valor_cuota = parseFloat(s_valor_cuota.val());

								if ((cuotas != 0 && valor_cuota != 0)) {
									importe = (valor_cuota * cuotas);
									$('#importe_alquiler_endoso').val(importe);
								}

							});


			/*
			 * Muestra importe
			 */

					
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
			$('#save_solicitud_alquiler').click(function() {
				
				if(!$("#solicitud_poliza_alquiler").valid())return false;	 

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

								var data = $('#solicitud_poliza_alquiler').serializeArray();
								// console.debug(data);

								$.ajax({
											url : "./poliza/solicitud/renovacion-alquiler",
											data : data,
											success : function(result) {
												$(href).html(result);
											}
										});

							});

			// Aprobar Solicitud
			$('#aprobar_solicitud_alquiler').click(function() {

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
			$('#confirmar_solicitud_alquiler').click(function() {

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
			$('#anular_solicitud_alquiler').click(function() {

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
			$("#solicitud_poliza_alquiler").validate({
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

function addSolicitudAduaneros(f) {

	var url = f.action;

	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	// Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	// //console.debug($('#tabs ul li a'));
	var href = $(tab).attr('href');
	$.ajax({
		url : url,
		data : {
			save : f.save.value
			// Cabecera de la solicitud
			,solicitud_id : f.solicitud_id.value
			,agente_id : f.agente_id.value
			,asegurado_id : f.asegurado_id.value
			,compania_id : f.compania_id.value
			,productor_id : f.productor_id.value
			,cobrador_id : f.cobrador_id.value
			// Detalle Particulares de la poliza
			,tipo_garantia_id:f.tipo_garantia_id.value
			,motivo_garantia_id:f.motivo_garantia_id.value
			,domicilio_riesgo:f.domicilio_riesgo.value
			 ,localidad_riesgo:f.localidad_riesgo.value
			 ,provincia_riesgo:f.provincia_riesgo.value
			// ,acreedor_prendario:f.acreedor_prendario.value
			 ,mercaderia:f.mercaderia.value
			 ,despachante_aduana_id:f.despachante_aduana_id.value 
			 ,beneficiario_id:f.beneficiario_id.value
			 ,descripcion_adicional:f.descripcion_adicional.value
			 , bl:f.bl.value
			 , factura:f.factura.value
			 , sim :f.sim.value
			 //Valores del Seguro
			,monto_asegurado : f.monto_asegurado.value
			,moneda_id : f.moneda_id.value
			,forma_pago_id : f.forma_pago_id.value
			//premio_asegurado : f.premio_asegurado.value,
			//plus : f.plus.value
			,fecha_pedido : f.fecha_pedido.value
			,periodo_id : f.periodo_id.value
			,fecha_vigencia : f.fecha_vigencia.value
			,cuotas : f.cuotas.value
			,valor_cuota : f.valor_cuota.value
			,observaciones_asegurado : f.observaciones_asegurado.value
			,observaciones_compania : f.observaciones_compania.value
		}

		,
		success : function(result) {
			// console.debug(result);
			$(href).html(result);
		}
	});

	return false;
}

function confirmarSolicitudAduaneros(id) {

	if (id == null)
		return false;
	// console.debug(id);

	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	// Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	// //console.debug($('#tabs ul li a'));
	var href = $(tab).attr('href');
	var url = "./poliza/solicitud/confirmar-solicitud-caucion";
	$.ajax({
		url : url,
		data : {
			confirmar : true,
			solicitud_id : id
		}

		,
		success : function(result) {
			// console.debug(result);
			$(href).html(result);
		}
	});

	return false;
}


function aprobarSolicitudAduaneros(id){

	if(id==null) return false;
	//console.debug(id);
	
	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	//Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	////console.debug($('#tabs ul li a'));
	var href = $(tab).attr('href') ;
	var url = "./poliza/solicitud/aprobar-solicitud";                       
	$.ajax({
	    url: url,
    	data:{
	    	  aprobar:true
	    	 ,solicitud_id: id
            }

	    ,success:function(result){
        //console.debug(result);
        $(href).html(result);
      	}
      });
	   
	    return false;
	}
