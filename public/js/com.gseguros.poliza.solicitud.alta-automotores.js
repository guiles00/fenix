var com = com || {};
com.gseguros = com.gseguros || {};
com.gseguros.poliza = com.gseguros.poliza || {};
com.gseguros.poliza.solicitud = com.gseguros.poliza.solicitud || {};
com.gseguros.poliza.solicitud.altaAutomotores = com.gseguros.poliza.solicitud.altaAutomotores
		|| {};

// Autocomplete asegurado
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
function confirmar() {
	return confirm('Desea agregar el registro?');
}

// Funcion que inicializa las acciones que realiza el Tab
com.gseguros.poliza.solicitud.altaAutomotores.init = function() {
	// Autocomplete Asegurados
	$("#asegurado").autocomplete('./poliza/solicitud/listar-asegurados', {
		width : 300,
		multiple : true,
		matchContains : true,
		formatItem : formatItem,
		formatResult : formatResult
	});

	$("#asegurado").result(
			function(event, data, formatted) {
				var hidden = $(this).parent().next().find(">:input");
				hidden.val((hidden.val() ? hidden.val() + ";" : hidden.val())
						+ data[1]);
				// console.debug(data[1]);
				$("#asegurado_id").val(data[1]);
			});
	// End Autocomplete Asegurados

	// JQuery UI Fechas
	$('#fecha_pedido').datepicker(
			{
				dateFormat : 'yy-mm-dd',
				dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa' ],
				monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo',
						'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre',
						'Noviembre', 'Diciembre' ]
			});

	$('#fecha_vigencia_desde').datepicker(
			{
				dateFormat : 'yy-mm-dd',
				dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa' ],
				monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo',
						'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre',
						'Noviembre', 'Diciembre' ]
			});

	$('#fecha_vigencia_hasta').datepicker(
			{
				dateFormat : 'yy-mm-dd',
				dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa' ],
				monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo',
						'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre',
						'Noviembre', 'Diciembre' ]
			});

	// End JQueri UI Fechas

	// Oculta campos dar de alta solicitud o buscar
	$('#datos_seguro_automotor').toggle();
	$('#detalle_riesgo_automotor').toggle();
	$('#valores_seguro_automotor').toggle();
	// $('#datos_solicitud').toggle();

	$('#datos_seguro_automotor_show').click(function() {
		$('#datos_seguro_automotor').toggle();
	});
	$('#detalle_riesgo_automotor_show').click(function() {
		$('#detalle_riesgo_automotor').toggle();
	});
	$('#datos_solicitud_automotor_show').click(function() {
		$('#datos_solicitud_automotor').toggle();
	});
	$('#valores_seguro_automotor_show').click(function() {
		$('#valores_seguro_automotor').toggle();
	});

	$('#datos_solicitud_show').click(function() {
		$('#datos_solicitud').toggle();
	});
	// End Oculta campos dar de alta solicitud o buscar

	//Add Solicitud
	
	$('#add_solicitud_automotores').click(function(){
		
		var url = $('#add_solicitud_automotores').attr('action');
		console.debug(url);
		exit();
		var tabs_sel = $('#tabs').tabs();
		var idx = tabs_sel.tabs('option', 'selected');

		// Trae el tab correspondiente
		var tab = $('#tabs ul li a')[idx];
		// //console.debug($('#tabs ul li a'));
		var href = $(tab).attr('href');
		$
				.ajax({
					url : url,
					data : {
						save : f.save.value,
						solicitud_id : f.solicitud_id.value,
						asegurado_id : f.asegurado_id.value,
						agente_id : f.agente_id.value,
						compania_id : f.compania_id.value,
						productor_id : f.productor_id.value,
						cobrador_id : f.cobrador_id.value
						// Detalle Pago
						,
						monto_asegurado : f.monto_asegurado.value,
						moneda_id : f.moneda_id.value,
						forma_pago_id : f.forma_pago_id.value
						// ,iva:f.iva.value
						// ,prima_comision:f.prima_comision.value
						// ,prima_tarifa:f.prima_tarifa.value
						// ,premio_compania:f.premio_compania.value
						,
						premio_asegurado : f.premio_asegurado.value,
						plus : f.plus.value
						// Detalle Automotor
						,
						tipo_vehiculo_id : f.tipo_vehiculo_id.value,
						anio_automotor : f.anio_automotor.value,
						marca_automotor : f.marca_automotor.value,
						tipo_automotor : f.tipo_automotor.value,
						modelo_automotor : f.modelo_automotor.value,
						color_automotor : f.color_automotor.value,
						patente_automotor : f.patente_automotor.value,
						cilindrada_automotor : f.cilindrada_automotor.value,
						serial_c_automotor : f.serial_c_automotor.value,
						serial_automotor : f.serial_automotor.value,
						accesorios_automotor : f.accesorios_automotor.value,
						uso_automotor : f.uso_automotor.value,
						capacidad_automotor : f.capacidad_automotor.value,
						pasajeros_automotor : f.pasajeros_automotor.value,
						flota_automotor : f.flota_automotor.value,
						fecha_titulo_automotor : f.fecha_titulo_automotor.value,
						titular_automotor : f.titular_automotor.value,
						numero_certificado_automotor : f.numero_certificado_automotor.value,
						estado_vehiculo_automotor : f.estado_vehiculo_automotor.value,
						estado_luces_automotor : f.estado_luces_automotor.value,
						estado_motor_automotor : f.estado_motor_automotor.value,
						estado_carroceria_automotor : f.estado_carroceria_automotor.value,
						estado_automotor : f.estado_automotor.value,
						tipo_combustion_automotor : f.tipo_combustion_automotor.value,
						sistema_seguridad_automotor : f.sistema_seguridad_automotor.value,
						acreedor_prendario_automotor : f.acreedor_prendario_automotor.value,
						otros_automotor : f.otros_automotor.value,
						fecha_pedido : f.fecha_pedido.value,
						periodo_id : f.periodo_id.value,
						fecha_vigencia_desde : f.fecha_vigencia_desde.value,
						cuotas : f.cuotas.value,
						observaciones_asegurado : f.observaciones_asegurado.value,
						observaciones_compania : f.observaciones_compania.value
					}

					,
					success : function(result) {
						// console.debug(result);
						$(href).html(result);
					}
				});
		
		
	}); 

		
	
	//End Add Solicitud
	
	return false;
}

$(document).ready(function() {

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
	
	// PeriodoFecha En funcion del periodo y con la fecha del dia
	$("#periodo_id").change(function() {

		var select = $("#periodo_id option:selected").val();

		switch (select) {
		case '1':
			var date_periodo = Date.today().add({
				months : 1
			})
			var dd = date_periodo.getDate();
			var mm = date_periodo.getMonth() + 1;// January is 0!
			var yyyy = date_periodo.getFullYear();
			if (dd < 10) {
				dd = '0' + dd
			}
			if (mm < 10) {
				mm = '0' + mm
			}
			var date_format = yyyy + '-' + mm + '-' + dd;

			$('#fecha_vigencia_desde').val(date_today);
			$('#fecha_vigencia_hasta').val(date_format);

			break;
		case '2':
			var date_periodo = Date.today().add({
				months : 3
			})
			var dd = date_periodo.getDate();
			var mm = date_periodo.getMonth() + 1;// January is 0!
			var yyyy = date_periodo.getFullYear();
			if (dd < 10) {
				dd = '0' + dd
			}
			if (mm < 10) {
				mm = '0' + mm
			}
			var date_format = yyyy + '-' + mm + '-' + dd;

			$('#fecha_vigencia_desde').val(date_today);
			$('#fecha_vigencia_hasta').val(date_format);

			break;
		case '3':

			var date_periodo = Date.today().add({
				months : 4
			})
			var dd = date_periodo.getDate();
			var mm = date_periodo.getMonth() + 1;// January is 0!
			var yyyy = date_periodo.getFullYear();
			if (dd < 10) {
				dd = '0' + dd
			}
			if (mm < 10) {
				mm = '0' + mm
			}
			var date_format = yyyy + '-' + mm + '-' + dd;

			$('#fecha_vigencia_desde').val(date_today);
			$('#fecha_vigencia_hasta').val(date_format);

			break;

		case '4':
			var date_periodo = Date.today().add({
				months : 6
			})
			var dd = date_periodo.getDate();
			var mm = date_periodo.getMonth() + 1;// January is 0!
			var yyyy = date_periodo.getFullYear();
			if (dd < 10) {
				dd = '0' + dd
			}
			if (mm < 10) {
				mm = '0' + mm
			}
			var date_format = yyyy + '-' + mm + '-' + dd;

			$('#fecha_vigencia_desde').val(date_today);
			$('#fecha_vigencia_hasta').val(date_format);

			break;
		case '5':
			var date_periodo = Date.today().add({
				year : 1
			})
			var dd = date_periodo.getDate();
			var mm = date_periodo.getMonth() + 1;// January is 0!
			var yyyy = date_periodo.getFullYear();
			if (dd < 10) {
				dd = '0' + dd
			}
			if (mm < 10) {
				mm = '0' + mm
			}
			var date_format = yyyy + '-' + mm + '-' + dd;

			$('#fecha_vigencia_desde').val(date_today);
			$('#fecha_vigencia_hasta').val(date_format);
			break;
		default:
			break;
		}// EndPeriodoFecha

	});
	
});


function confirmarSolicitudAutomotores(id) {

	if (id == null)
		return false;
	// console.debug(id);

	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	// Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	// //console.debug($('#tabs ul li a'));
	var href = $(tab).attr('href');
	var url = "./poliza/solicitud/confirmar-solicitud";
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
