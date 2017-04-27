$(document).ready(
		function() {
			function confirmar() {
				return confirm('Desea agregar el registro?');
			}

		
		
			$('#fecha_vigencia_poliza_aduaneros').datepicker(
					{
						dateFormat : 'yy-mm-dd',
						dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi',
								'Sa' ],
						monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril',
								'Mayo', 'Junio', 'Julio', 'Agosto',
								'Septiembre', 'Octubre', 'Noviembre',
								'Diciembre' ]
					});


			$('#datos_seguro_show').click(function() {
				$('#datos_seguro').toggle();
			});
			$('#detalle_riesgo_show').click(function() {
				$('#detalle_riesgo').toggle();
			});
			$('#datos_solicitud_show').click(function() {
				$('#datos_solicitud').toggle();
			});
			$('#valores_seguro_aduaneros_show').click(function() {
				$('#valores_seguro_aduaneros').toggle();
			});

			$('#datos_solicitud_show').click(function() {
				$('#datos_solicitud').toggle();
			});

			$('#observaciones_seguro_aduaneros_show').click(function() {
				$('#observaciones_seguro_aduaneros').toggle();
			});
			$('#detalle_riesgo_aduaneros_show').click(function() {
				$('#detalle_riesgo_aduaneros').toggle();
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
			,poliza_id : f.poliza_id.value
			,numero_poliza : f.numero_poliza.value
			,numero_factura : f.numero_factura.value
			//,agente_id : f.agente_id.value
			//,asegurado_id : f.asegurado_id.value
			//,compania_id : f.compania_id.value
			//,productor_id : f.productor_id.value
			//,cobrador_id : f.cobrador_id.value
			// Detalle Particulares de la poliza
			//,tipo_garantia_id:f.tipo_garantia_id.value
			//,motivo_garantia_id:f.motivo_garantia_id.value
			//,domicilio_riesgo:f.domicilio_riesgo.value
			 //,localidad_riesgo:f.localidad_riesgo.value
			 //,provincia_riesgo:f.provincia_riesgo.value
			 //,acreedor_prendario:f.acreedor_prendario.value
			 //,mercaderia:f.mercaderia.value
			 //,despachante_aduana_id:f.despachante_aduana_id.value 
			 //,descripcion_adicional:f.descripcion_adicional.value
			 , bl:f.bl.value
			 , factura:f.factura.value
			 , sim :f.sim.value
			 //Valores del Seguro
			 , iva :f.iva.value
			 //, prima_tarifa :f.prima_tarifa.value
			 , prima_comision :f.prima_comision.value
			 , premio_asegurado :f.premio_asegurado.value
			 , premio_compania :f.premio_compania.value
			 , plus :f.plus.value
			
			,monto_asegurado : f.monto_asegurado.value
			//,moneda_id : f.moneda_id.value
			//,forma_pago_id : f.forma_pago_id.value
			//plus : f.plus.value
			//,fecha_pedido : f.fecha_pedido.value
			//,periodo_id : f.periodo_id.value
			,fecha_vigencia : f.fecha_vigencia.value
			//,cuotas : f.cuotas.value
			//,observaciones_asegurado : f.observaciones_asegurado.value
			//,observaciones_compania : f.observaciones_compania.value
			,documentacion_id : f.documentacion_id.value
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
