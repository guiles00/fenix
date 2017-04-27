$(document).ready(
		function() {
			
			$('#s_fecha_desde').datepicker(
					{
						dateFormat : 'yy-mm-dd',
						dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi',
								'Sa' ],
						monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril',
								'Mayo', 'Junio', 'Julio', 'Agosto',
								'Septiembre', 'Octubre', 'Noviembre',
								'Diciembre' ]
					});

			$('#s_fecha_hasta').datepicker(
					{
						dateFormat : 'yy-mm-dd',
						dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi',
								'Sa' ],
						monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril',
								'Mayo', 'Junio', 'Julio', 'Agosto',
								'Septiembre', 'Octubre', 'Noviembre',
								'Diciembre' ]
					});
			

			
			// Confirmo busqueda de polizas
			$('#confirm_search_poliza').click(function() {
				
								// trae id de tab
								var tabs_sel = $('#tabs').tabs();
								var idx = tabs_sel.tabs('option',
										'selected');

								// Trae el tab correspondiente
								var tab = $('#tabs ul li a')[idx];
								// //console.debug($('#tabs ul li a'));
								var href = $(tab).attr('href');

								// suponemos que el form es valido

								var data = $('#form_search_poliza').serializeArray();
								//console.debug('dataaaa');
								//console.debug(data);
								$.ajax({
											url : "./poliza/poliza/res-search",
											data : data,
											success : function(result) {
												//$(href).html(result);
												$("#g_res_search").html(result);
											}
										});

							});
			
				$('#tab_search_poliza').click(function() {
					
					// trae id de tab
					var tabs_sel = $('#tabs').tabs();
					var idx = tabs_sel.tabs('option',
							'selected');
	
					// Trae el tab correspondiente
					var tab = $('#tabs ul li a')[idx];
					// //console.debug($('#tabs ul li a'));
					var href = $(tab).attr('href');
					//var searchParams = JSON.parse( $('#g_search_params').val() );
					
					//console.debug(searchParams);

					$.ajax({
								url : "./poliza/poliza/search"
								//,params: searchParams
								,success : function(result) {
									$(href).html(result);
								}
							});

				});
			
				/*
				 * 
				 */
			$('#imprimir_search_poliza').click(function() {
					
			var DocumentContainer = document.getElementById('div_table_poliza_search');
					
				var html = '<html><head>'+
	               '<link href="css/template.css" rel="stylesheet" type="text/css" />'+
	               '</head><body style="background:#ffffff;">'+
	               DocumentContainer.innerHTML+
	               '</body></html>';
//var html =  DocumentContainer.innerHTML;
	    var WindowObject = window.open("", "PrintWindow",
	    	    "width=750,height=650,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes");
	    WindowObject.document.writeln(html);
	    WindowObject.document.close();
	    WindowObject.focus();
	    WindowObject.print();
	    WindowObject.close();
	    document.getElementById('div_table_poliza_search').style.display='block';
	
		});
				
		});

