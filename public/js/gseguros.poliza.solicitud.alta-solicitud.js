function confirmar(){
	return confirm('Desea agregar el registro?');
	}

//Autocomplete asegurado
function formatItem(row) {
		//console.debug(row);
		return row[0]+' '+row[1] ;//+ "(<strong>id: " + row[1] + "</strong>)";
	}
	function formatResult(row) {
		//console.debug(row);
		return row[0]+' '+row[1];
		//return row[0].replace(/(<.+?>)/gi, '');
	}


$(document).ready(function () {

		 $("#asegurado").autocomplete('./poliza/solicitud/listar-asegurados', {
				width: 300,
				multiple: true,
				matchContains: true,
				formatItem: formatItem,
				formatResult: formatResult
			});

		$("#asegurado").result(function(event, data, formatted) {
				var hidden = $(this).parent().next().find(">:input");
				hidden.val( (hidden.val() ? hidden.val() + ";" : hidden.val()) + data[1]);
				//console.debug(data[1]);
				$("#asegurado_id").val(data[1]);
		});
		
	 	  
	         $('#fecha_pedido').datepicker(
	            {   dateFormat: 'yy-mm-dd',
	                dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
	                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo',
	                    'Junio', 'Julio', 'Agosto', 'Septiembre',
	                    'Octubre', 'Noviembre', 'Diciembre']
	            });         

	         $('#fecha_vigencia_desde').datepicker(
	            {   dateFormat: 'yy-mm-dd',
	                dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
	                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo',
	                    'Junio', 'Julio', 'Agosto', 'Septiembre',
	                    'Octubre', 'Noviembre', 'Diciembre']
	            });
	         
	         //Guardar Solicitud
	         $('#save_solicitud_automotor').click(function(){
	        	 
	     		var tabs_sel = $('#tabs').tabs();
	    		var idx = tabs_sel.tabs('option', 'selected');

	    		//Trae el tab correspondiente
	    		var tab = $('#tabs ul li a')[idx];
	    		////console.debug($('#tabs ul li a'));
	    		var href = $(tab).attr('href') ; 
	        	 
	        	 //alert('clic!');
	        	 //suponemos que el form es valido
	        	
	        	 var data = $('#solicitud_poliza').serializeArray();
	        	 //console.debug(data);
	        	 
	        	 $.ajax({
	     		    url: "./poliza/solicitud/alta-solicitud",
	     	    	data:	data
	     		    ,success:function(result){
	     	        //console.debug(result);
	     	        $(href).html(result);
	     	      }});
	        	 
	         });
	}); 


	
	function confirmarSolicitud(id){

		if(id==null) return false;
		//console.debug(id);
		
		var tabs_sel = $('#tabs').tabs();
		var idx = tabs_sel.tabs('option', 'selected');

		//Trae el tab correspondiente
		var tab = $('#tabs ul li a')[idx];
		////console.debug($('#tabs ul li a'));
		var href = $(tab).attr('href') ;
		var url = "./poliza/solicitud/confirmar-solicitud";                       
		$.ajax({
		    url: url,
	    	data:{
		    	  confirmar:true
		    	 ,solicitud_id: id
	            }

		    ,success:function(result){
	        //console.debug(result);
	        $(href).html(result);
	      	}
	      });
		   
		    return false;
		}
