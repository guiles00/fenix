$(document).ready(
		function() {
			function confirmar() {
				return confirm('Desea agregar el registro?');
			}

		
			// Guardar poliza
			$('#save_factura_vida').click(function() {
				//if(!$("#poliza_poliza_responsabilidad_civil").valid())return false;	 

								 
								// trae id de tab
								var tabs_sel = $('#tabs').tabs();
								var idx = tabs_sel.tabs('option',
										'selected');

								// Trae el tab correspondiente
								var tab = $('#tabs ul li a')[idx];
								// //console.debug($('#tabs ul li a'));
								var href = $(tab).attr('href');

								// suponemos que el form es valido

								var data = $('#poliza_poliza_factura').serializeArray();
							 //console.debug(data);

								$.ajax({
											url : "./poliza/poliza/edit-poliza-factura-vida",
											data : data,
											success : function(result) {
												$(href).html(result);
											}
										});
								
							});

});