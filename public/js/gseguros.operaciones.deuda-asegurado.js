var id_check = 0;


$("#div_form_cheques_show,#datos_pago_show").hover(
		  function () {
		    $(this).append($("<span> (Clic para mostrar u ocultar)</span>"));
		  }, 
		  function () {
		    $(this).find("span:last").remove();
		  }
		);


$('#div_form_cheques').hide();


$('#datos_pago_show').click(function() {
	$('#datos_pago').toggle();
});

$('#div_form_cheques_show').click(function() {
	$('#div_form_cheques').toggle();
});
function listarDeudaAsegurado(){
	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');
	// Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	// //console.debug($('#tabs ul li a'));
	var href = $(tab).attr('href') ;
	// console.debug(href);
	// var href_t = href+" #test_inner";
	// console.debug(href_t);
	// var url = "./poliza/solicitud/confirmar-solicitud";
	var url = "./operaciones/deuda-asegurado/list";

	$.ajax({
	    url: url,
    	    success:function(result){
        // console.debug(result);
        $(href).html(result);
      	}
      });
	   
	    return false;


}
$('#fecha_pago').datepicker(
		{
			dateFormat : 'yy-mm-dd',
			dayNamesMin : [ 'Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa' ],
			monthNames : [ 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo',
					'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre',
					'Noviembre', 'Diciembre' ]
		});
/*
 * $("input:checkbox").click(function(){
 * 
 * for each(var checkbox in checkboxes){ //array_polizas.push(checkbox.val());
 * //console.debug(checkbox); if(typeof checkbox=='undefined'){
 *  } if(checkbox.checked){ //console.debug(checkbox);
 * 
 * if(moneda_id.val() != $(checkbox).attr('moneda')){ alert("debe seleccionar el
 * mismo tipo de moneda"); return false; }
 * 
 *  } }
 * 
 * });
 */


function BakrealizarPagoAsegurado(){

// Aca tiene que llamar a otro controlador
// En el phtml del controlador tiene que tener la data para pagar
		var importe = $('#importe'),
		    moneda_id = $('#moneda_id'),
		    asegurado_id = $('#asegurado_id'),
		    fecha_pago = $('#fecha_pago');
		importe_cheque_0 = $('#importe_cheque_0'),
		importe_cheque_1 = $('#importe_cheque_1'),
		importe_cheque_2 = $('#importe_cheque_2'),
		numero_factura = $('#numero_factura');
		
		
		var importe_total_a_pagar = 0;
		
		var tabs_sel = $('#tabs').tabs();
		var idx = tabs_sel.tabs('option', 'selected');

		// Trae el tab correspondiente
		var tab = $('#tabs ul li a')[idx];
		// //console.debug($('#tabs ul li a'));
		var href = $(tab).attr('href') ;
		// console.debug(href);
		var href_t = href+" #test_inner";
		console.debug(href_t); 
		// var url = "./poliza/solicitud/confirmar-solicitud";
		var url = "./operaciones/deuda-asegurado/pagar-deuda-asegurado";

		var array_polizas = new Array();
		
		var	 checkboxes = document.getElementsByName('poliza_pago');

			  // Chequear que seleccionen alguna poliza
			  // control de errores
		// var checkbox;
					
			 for each(var checkbox in checkboxes){
				 
			    // array_polizas.push(checkbox.val());
			    // console.debug(checkbox);
			  if(typeof checkbox=='undefined'){
				  
				 alert('Ocurrio un error intente nuevamente');
				  return false;
			  }
			    if(checkbox.checked){
			  // console.debug(checkbox);
			    	// alert("debe seleccionar el mismo tipo de moneda");
					// return false;
			if(moneda_id.val() != $(checkbox).attr('moneda')){
				alert("debe seleccionar el mismo tipo de moneda");
			return false;
			}
			  importe_total_a_pagar = importe_total_a_pagar + parseFloat($(checkbox).attr('importe'));			
			  array_polizas.push(checkbox.value);
			  
			    }
			  }
			// console.debug(array_polizas);
		
		// chequea que la moneda sea del mismo tipo

		// alert(importe.val());
		// alert(importe_total_a_pagar);

		if(importe.val() != importe_total_a_pagar){
			
		var resp = confirm("El importe a pagar es diferente, desea continuar?");

		if(!resp)return false;

			}
		var importe_total = 0;
		importe_total = importe.val() +	importe_cheque_0.val() + importe_cheque_1.val() + importe_cheque_2.val()	 ;
		
		alert(importe_total);
		$.ajax({
		    url: url,
	    	data:{
		
		    	 array_polizas: array_polizas.toString()
		    	 ,importe: importe_total	
		    	 ,moneda_id: moneda_id.val()
		    	 ,fecha_pago: fecha_pago.val()
		    	 ,asegurado_id: asegurado_id.val()
	            }

		    ,success:function(result){
	        // console.debug(result);
	        $(href_t).html(result);
	      	}
	      });
		   
		    return false;
		}





function realizarPagoAsegurado(){

	// Aca tiene que llamar a otro controlador
	// En el phtml del controlador tiene que tener la data para pagar
			var importe = $('#importe'),
			    moneda_id = $('#moneda_id'),
			    asegurado_id = $('#asegurado_id'),
			    fecha_pago = $('#fecha_pago');
			importe_cheque_0 = $('#importe_cheque_0'),
			importe_cheque_1 = $('#importe_cheque_1'),
			importe_cheque_2 = $('#importe_cheque_2'),
			importe_cheque_2 = $('#importe_cheque_3'),
			importe_cheque_2 = $('#importe_cheque_4'),
			importe_cheque_2 = $('#importe_cheque_5'),
			numero_factura = $('#numero_factura');
			
			var importe_total_a_pagar = 0;
			
			var tabs_sel = $('#tabs').tabs();
			var idx = tabs_sel.tabs('option', 'selected');

			// Trae el tab correspondiente
			var tab = $('#tabs ul li a')[idx];
			// //console.debug($('#tabs ul li a'));
			var href = $(tab).attr('href') ;
			// console.debug(href);
			var href_t = href+" #test_inner";
			console.debug(href_t); 
			// var url = "./poliza/solicitud/confirmar-solicitud";
			var url = "./operaciones/deuda-asegurado/pagar-deuda-asegurado";

			var array_polizas = new Array();
			
			var	 checkboxes = document.getElementsByName('poliza_pago');
			
			// var checkboxes = document.getElementById("pago_poliza").checkbox;
				  // Chequear que seleccionen alguna poliza
				  // control de errores
			// var checkbox;
				
		    
			// console.debug(checkboxes);
		    for (var x=0; x < checkboxes.length; x++) {
		    
		    	
		    	if (checkboxes[x].checked) {
		   // console.debug(checkboxes[x]);
		   // alert($(checkboxes[x]).attr('moneda'));
		    
		    		 if( checkboxes[x]=='undefined' ){
						  
						 alert('Ocurrio un error intente nuevamente');
						  return false;
					  }
		    		if(moneda_id.val() != $(checkboxes[x]).attr('moneda')){
						alert("debe seleccionar el mismo tipo de moneda");
					return false;
					}
					  importe_total_a_pagar = importe_total_a_pagar + parseFloat($(checkboxes[x]).attr('importe'));			
					  array_polizas.push(checkboxes[x].value);
					  
		    
		    	}
		    }
				 /*
					 * for each(var checkbox in checkboxes){
					 * 
					 * //array_polizas.push(checkbox.val());
					 * //console.debug(checkbox); if(typeof
					 * checkbox=='undefined'){
					 * 
					 * alert('Ocurrio un error intente nuevamente'); return
					 * false; } if(checkbox.checked){ //console.debug(checkbox);
					 * //alert("debe seleccionar el mismo tipo de moneda");
					 * //return false; if(moneda_id.val() !=
					 * $(checkbox).attr('moneda')){ alert("debe seleccionar el
					 * mismo tipo de moneda"); return false; }
					 * importe_total_a_pagar = importe_total_a_pagar +
					 * parseFloat($(checkbox).attr('importe'));
					 * array_polizas.push(checkbox.value);
					 *  } }
					 */
				// console.debug(array_polizas);
			
			// chequea que la moneda sea del mismo tipo

			// alert(importe.val());
			// alert(importe_total_a_pagar);

			if(importe.val() != importe_total_a_pagar){
				
			var resp = confirm("El importe a pagar es diferente, desea continuar?");

			if(!resp)return false;

				}
			
			var data = $('#pago_poliza').serializeArray();
			
			var data_cheques = $('#div_form_cheques').serializeArray();
			var array_cheques = new Array();
			
			// console.debug(data[0]);
			// alert(data[0]);
			
			
			var importe_total = 0;
			importe_total = parseFloat(importe.val()) +	parseFloat(importe_cheque_0.val()) + 
			parseFloat(importe_cheque_1.val()) + parseFloat(importe_cheque_2.val())
			+ parseFloat(importe_cheque_3.val())+ parseFloat(importe_cheque_4.val())	
			+ parseFloat(importe_cheque_5.val());
			
			 alert(importe_total);
			
			$.ajax({
				
			    url: url,
		    	data:{
		    		data: data
			    	 ,array_polizas: array_polizas.toString()
			    	 ,importe: importe_total	
			    	 ,moneda_id: moneda_id.val()
			    	 ,fecha_pago: fecha_pago.val()
			    	 ,asegurado_id: asegurado_id.val()
			    	 // por ahora los paso separados porque no se por que
						// mierda no me pasa el array*/
			    	
		            }

			    ,success:function(result){
		        // console.debug(result);
		        $(href_t).html(result);
		      	}
		      });
			   
			    return false;
			}


// Guardar Solicitud
$('#realizar_pago').click(function() {

	
	//alert('dentro de click function');
	var importe = $('#importe'),
    moneda_id = $('#moneda_id'),
    asegurado_id = $('#asegurado_id'),
    fecha_pago = $('#fecha_pago');
importe_cheque_0 = $('#importe_cheque_0'),
importe_cheque_1 = $('#importe_cheque_1'),
importe_cheque_2 = $('#importe_cheque_2')
nro_cheque_0 = $('#nro_cheque_0'),
nro_cheque_1 = $('#nro_cheque_1'),
nro_cheque_2 = $('#nro_cheque_2')
banco_cheque_0 = $('#banco_cheque_0'),
banco_cheque_1 = $('#banco_cheque_1'),
banco_cheque_2 = $('#banco_cheque_2'),
numero_factura = $('#numero_factura');






var importe_total_a_pagar = 0;

var tabs_sel = $('#tabs').tabs();
var idx = tabs_sel.tabs('option', 'selected');

// Trae el tab correspondiente
var tab = $('#tabs ul li a')[idx];
// //console.debug($('#tabs ul li a'));
var href = $(tab).attr('href') ;
// console.debug(href);
var href_t = href+" #test_inner";
console.debug(href_t); 
// var url = "./poliza/solicitud/confirmar-solicitud";
var url = "./operaciones/deuda-asegurado/pagar-deuda-asegurado";

var array_polizas = new Array();

var	 checkboxes = document.getElementsByName('poliza_pago');

// var checkboxes = document.getElementById("pago_poliza").checkbox;
	  // Chequear que seleccionen alguna poliza
	  // control de errores
// var checkbox;
	

// console.debug(checkboxes);
for (var x=0; x < checkboxes.length; x++) {

	
	if (checkboxes[x].checked) {
// console.debug(checkboxes[x]);
// alert($(checkboxes[x]).attr('moneda'));

		 if( checkboxes[x]=='undefined' ){
			  
			 alert('Ocurrio un error intente nuevamente');
			  return false;
		  }
		if(moneda_id.val() != $(checkboxes[x]).attr('moneda')){
			alert("debe seleccionar el mismo tipo de moneda");
		return false;
		}
		  importe_total_a_pagar = importe_total_a_pagar + parseFloat($(checkboxes[x]).attr('importe'));			
		  array_polizas.push(checkboxes[x].value);
		  

	}
}
	 /*
		 * for each(var checkbox in checkboxes){
		 * 
		 * //array_polizas.push(checkbox.val()); //console.debug(checkbox);
		 * if(typeof checkbox=='undefined'){
		 * 
		 * alert('Ocurrio un error intente nuevamente'); return false; }
		 * if(checkbox.checked){ //console.debug(checkbox); //alert("debe
		 * seleccionar el mismo tipo de moneda"); //return false;
		 * if(moneda_id.val() != $(checkbox).attr('moneda')){ alert("debe
		 * seleccionar el mismo tipo de moneda"); return false; }
		 * importe_total_a_pagar = importe_total_a_pagar +
		 * parseFloat($(checkbox).attr('importe'));
		 * array_polizas.push(checkbox.value);
		 *  } }
		 */
	// console.debug(array_polizas);

// chequea que la moneda sea del mismo tipo

// alert(importe.val());
// alert(importe_total_a_pagar);



var data = $('#pago_poliza').serializeArray();

var data_cheques = $('#div_form_cheques').serializeArray();
var array_cheques = new Array();

// console.debug(data[0]);
// alert(data[0]);


var importe_total = 0;
importe_total = parseFloat(importe.val()) +	parseFloat(importe_cheque_0.val()) + 
parseFloat(importe_cheque_1.val()) + parseFloat(importe_cheque_2.val())	;

// alert(importe_total);
if(importe_total != importe_total_a_pagar){
	
	var resp = confirm("El importe a pagar es diferente, desea continuar?");

	if(!resp)return false;

		}

$.ajax({
	
    url: url,
	data:{
    	 array_polizas: array_polizas.toString()
    	 ,importe: importe_total	
    	 ,moneda_id: moneda_id.val()
    	 ,fecha_pago: fecha_pago.val()
    	 ,asegurado_id: asegurado_id.val()
    	 // por ahora los paso separados porque no se por que mierda no me
			// pasa el array*/
    	,importe_cheque_0 :importe_cheque_0.val()
,importe_cheque_1 : importe_cheque_1.val()
,importe_cheque_2 : importe_cheque_2.val()
,nro_cheque_0 :nro_cheque_0.val()
,nro_cheque_1 : nro_cheque_1.val()
,nro_cheque_2 : nro_cheque_2.val()
,banco_cheque_0 : banco_cheque_0.val()
,banco_cheque_1 : banco_cheque_1.val()
,banco_cheque_2 : banco_cheque_2.val()
,numero_factura : numero_factura.val()    	
        }

    ,success:function(result){
    // console.debug(result);
    $(href_t).html(result);
  	}
  });

	
});


// Cuando realiza el pago
$('#add_check').click(function(){

        id_check = id_check + 1;
        var cheque = id_check;

        var html = "<tr id=cheque_"+id_check+"> <td>Banco: </td><td> <input type='text' name=banco_cheque_"+id_check+" value='' </input></td>   <td>Nro: </td><td> <input type='text' name=nro_cheque_"+id_check+" value='' </input></td>       <td>Importe: </td><td> <input type='text' name=importe_cheque_0"+id_check+" value='' </input></td>              </tr>";

        $('#table_form_cheques').append(html);
        $('#del_check_val').val(cheque)

        });

$('#del_check').click(function(){

        var del_id = $('#del_check_val').val();

        var el = "#"+'cheque_'+del_id;
        del_id = parseInt(del_id) - 1;


        var el_new = "#cheque_"+del_id;

        $(el).remove();
        $('#del_check_val').val(del_id)

        });

