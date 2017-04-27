$(document).ready(
		function() {
			function confirmar() {
				return confirm('Desea agregar el registro?');
			}

		
		
			$('#fecha_vigencia_poliza_transporte_mercaderia').datepicker(
					{
						dateFormat : 'yy-mm-dd',
						dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi',
								'Sa' ],
						monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril',
								'Mayo', 'Junio', 'Julio', 'Agosto',
								'Septiembre', 'Octubre', 'Noviembre',
								'Diciembre' ]
					});


			$('#datos_poliza_poliza_transporte_mercaderia_show').click(function() {
				$('#datos_poliza_poliza_transporte_mercaderia').toggle();
			});
			$('#datos_seguro_transporte_mercaderia_show').click(function() {
				$('#datos_seguro_transporte_mercaderia').toggle();
			});
			
			$('#detalle_riesgo_transporte_mercaderia_show').click(function() {
				$('#detalle_riesgo_transporte_mercaderia').toggle();
			});
			$('#datos_solicitud_transporte_mercaderia_show').click(function() {
				$('#datos_solicitud_transporte_mercaderia').toggle();
			});
			$('#valores_seguro_transporte_mercaderia_show').click(function() {
				$('#valores_seguro_transporte_mercaderia').toggle();
			});

			$('#datos_solicitud_transporte_mercaderia_show').click(function() {
				$('#datos_solicitud_transporte_mercaderia').toggle();
			});

			$('#observaciones_seguro_transporte_mercaderia_show').click(function() {
				$('#observaciones_seguro_transporte_mercaderia').toggle();
			});
		
			
			$("#datos_tarjeta_transporte_mercaderia_show").hide();
			$('#forma_pago_transporte_mercaderia_id').change(function(){
				//Si es el ID de pago con tarjeta
				//No deberia estar hardcodeado
				if($('#forma_pago_transporte_mercaderia_id').val()==2	){
					$("#datos_tarjeta_transporte_mercaderia_show").show();
				}else{
					$("#datos_tarjeta_transporte_mercaderia_show").hide();
				}
			});
			
//calcula importe - plus - premio asegurado
			
			$('#plus_transporte_mercaderia')
			.change(
					function() {

						var s_plus = $('#plus_transporte_mercaderia'),
						s_importe = $('#importe_transporte_mercaderia');
						 
						var plus= parseFloat(s_plus.val());
						var importe = parseFloat(s_importe.val());

						console.debug(plus);
						console.debug(importe);
						if ((plus != 0 && importe != 0)) {
							//importe = plus + premio_asegurado;
							premio_asegurado = importe - plus;
							$('#premio_asegurado_transporte_mercaderia').val(premio_asegurado.toFixed(2));
						}
					});
			$('#premio_asegurado_transporte_mercaderia')
			.change(
					function() {
						
						var s_premio_asegurado = $('#premio_asegurado_transporte_mercaderia'),
						s_importe = $('#importe_transporte_mercaderia');
						 
						var premio_asegurado= parseFloat(s_premio_asegurado.val());
						var importe = parseFloat(s_importe.val());

						console.debug(premio_asegurado);
						console.debug(importe);
						if ((premio_asegurado != 0 && importe != 0)) {
							//importe = plus + premio_asegurado;
							 plus = importe - premio_asegurado;
							$('#plus_transporte_mercaderia').val(plus.toFixed(2));
						}

					});
			
			
			
			
			// Guardar Solicitud
			$('#save_poliza_transporte_mercaderia').click(function() {
				
				//if(!$("#solicitud_poliza_transporte_mercaderia").valid())return false;	 

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

								var data = $('#poliza_poliza_transporte_mercaderia').serializeArray();
								// console.debug(data);

								$.ajax({
											url : "./poliza/poliza/view-poliza-transporte-mercaderia",
											data : data,
											success : function(result) {
												$(href).html(result);
											}
										});

							});
			
			
			
			
		});
