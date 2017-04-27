$(document).ready(
		function() {
			function confirmar() {
				return confirm('Desea agregar el registro?');
			}

		
		
			$('#fecha_vigencia_poliza_integral_comercio').datepicker(
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
			$('#valores_seguro_integral_comercio_show').click(function() {
				$('#valores_seguro_integral_comercio').toggle();
			});

			$('#datos_solicitud_show').click(function() {
				$('#datos_solicitud').toggle();
			});

			$('#observaciones_seguro_integral_comercio_show').click(function() {
				$('#observaciones_seguro_integral_comercio').toggle();
			});
			$('#detalle_riesgo_integral_comercio_show').click(function() {
				$('#detalle_riesgo_integral_comercio').toggle();
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
			


			$('#save_poliza_integral_comercio').click(function() {
				
								// trae id de tab
								var tabs_sel = $('#tabs').tabs();
								var idx = tabs_sel.tabs('option',
										'selected');

								// Trae el tab correspondiente
								var tab = $('#tabs ul li a')[idx];
								// //console.debug($('#tabs ul li a'));
								var href = $(tab).attr('href');

								// suponemos que el form es valido

								var data = $('#poliza_poliza_integral_comercio').serializeArray();
							
								$.ajax({
											url : "./poliza/poliza/view-poliza-integral-comercio",
											data : data,
											success : function(result) {
												$(href).html(result);
											}
										});

							});

		});
