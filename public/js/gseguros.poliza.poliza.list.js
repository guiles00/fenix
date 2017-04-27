function confirmar(){
	return confirm('Desea realizar la operacion?');
	}



function notaCreditoPoliza(url){

	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	//Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	var href = $(tab).attr('href') ;                       
	
    $.ajax({url: url
    	,data:{from:'poliza'}
        , success:function(result){
        $(href).html(result);
      }});
   
    return false;
	
}


function lbajaPoliza(url){

	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	//Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	var href = $(tab).attr('href') ;                       
	
    $.ajax({url: url
    	,data:{from:'poliza'}
        , success:function(result){
        $(href).html(result);
      }});
   
    return false;
	
}

function bajaOficioPoliza(url){

	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	//Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	var href = $(tab).attr('href') ;                       
	
    $.ajax({url: url
    	,data:{from:'poliza'}
        , success:function(result){
        $(href).html(result);
      }});
   
    return false;	
}


function bajaLiberacionPoliza(url){

	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	//Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	var href = $(tab).attr('href') ;                       
	
    $.ajax({url: url
    	,data:{from:'poliza'}
        , success:function(result){
        $(href).html(result);
      }});
   
    return false;
	
}

function renovarPoliza(url, title,tipo_poliza) {
	//Para que le paso la URL???
	var existe = false;
	

	
	/* if ... tipo poliza */
	if (tipo_poliza == 'ALQUILER') {
		tab_url = './poliza/solicitud/renovacion-alquiler';
		temp_url = './poliza/solicitud/renovacion-alquiler';
	}else if (tipo_poliza == 'RESPONSABILIDAD_CIVIL') {
		tab_url = './poliza/solicitud/renovacion-responsabilidad-civil';
		temp_url = './poliza/solicitud/renovacion-responsabilidad-civil';
	}else if (tipo_poliza == 'AUTOMOTORES') {
		tab_url = './poliza/solicitud/renovacion-automotores';
		temp_url = './poliza/solicitud/renovacion-automotores';
	}else if (tipo_poliza == 'ADUANEROS') {
		tab_url = './poliza/solicitud/renovacion-aduaneros';
		temp_url = './poliza/solicitud/renovacion-aduaneros';
	}else if (tipo_poliza == 'IGJ') {
		tab_url = './poliza/solicitud/renovacion-igj';
		temp_url = './poliza/solicitud/renovacion-igj';
	}else if (tipo_poliza == 'ACCIDENTES_PERSONALES') {
	tab_url = './poliza/solicitud/renovacion-accidentes-personales';
	temp_url = './poliza/solicitud/renovacion-accidentes-personales';
	}else if (tipo_poliza == 'JUDICIALES') {
		tab_url = './poliza/solicitud/renovacion-judiciales';
		temp_url = './poliza/solicitud/renovacion-judiciales';
	}else if (tipo_poliza == 'VIDA') {
		tab_url = './poliza/solicitud/renovacion-vida';
		temp_url = './poliza/solicitud/renovacion-vida';
	}else if (tipo_poliza == 'VIDA') {
		tab_url = './poliza/solicitud/renovacion-integral-comercio';
		temp_url = './poliza/solicitud/renovacion-integral-comercio';
	}else if (tipo_poliza == 'SEGURO_TECNICO') {
		tab_url = './poliza/solicitud/renovacion-seguro-tecnico';
		temp_url = './poliza/solicitud/renovacion-seguro-tecnico';
	}

	//console.debug('temp'+temp_url);
	
	$('div#CenterPane>div#tabs>ul.ui-tabs-nav>li a').each(function() {

		var href = $(this).attr('tab_url');
		
	
		if (href == tab_url)
			existe = true;
	});

	if (!existe)
		$("#tabs").tabs("add", url, title);
	if (existe)
		alert('Ya esta abierta la solapa, cerrala para poder ver la Solicitud');

	return false;
}


function endosoPoliza(url, title,tipo_poliza) {
	//Para que le paso la URL???
	var existe = false;
	

	
	/* if ... tipo poliza */
	if (tipo_poliza == 'ALQUILER') {
		tab_url = './poliza/solicitud/endoso-poliza-alquiler';
		temp_url = './poliza/solicitud/endoso-poliza-alquiler';
	}else if (tipo_poliza == 'RESPONSABILIDAD_CIVIL') {
		tab_url = './poliza/solicitud/endoso-poliza-responsabilidad-civil';
		temp_url = './poliza/solicitud/endoso-poliza-responsabilidad-civil';
	}else if (tipo_poliza == 'AUTOMOTORES') {
		tab_url = './poliza/solicitud/endoso-poliza-automotores';
		temp_url = './poliza/solicitud/endoso-poliza-automotores';
	}else if (tipo_poliza == 'ADUANEROS') {
		tab_url = './poliza/solicitud/endoso-poliza-aduaneros';
		temp_url = './poliza/solicitud/endoso-poliza-aduaneros';
	}else if (tipo_poliza == 'IGJ') {
		tab_url = './poliza/solicitud/endoso-poliza-igj';
		temp_url = './poliza/solicitud/endoso-poliza-igj';
	}else if (tipo_poliza == 'ACCIDENTES_PERSONALES') {
	tab_url = './poliza/solicitud/endoso-poliza-accidentes-personales';
	temp_url = './poliza/solicitud/endoso-poliza-accidentes-personales';
	}else if (tipo_poliza == 'JUDICIALES') {
		tab_url = './poliza/solicitud/endoso-poliza-judiciales';
		temp_url = './poliza/solicitud/endoso-poliza-judiciales';
	}else if (tipo_poliza == 'VIDA') {
		tab_url = './poliza/solicitud/endoso-poliza-vida';
		temp_url = './poliza/solicitud/endoso-poliza-vida';
	}else if (tipo_poliza == 'CONSTRUCCION') {
		tab_url = './poliza/solicitud/endoso-poliza-construccion';
		temp_url = './poliza/solicitud/endoso-poliza-construccion';
	}else if (tipo_poliza == 'CONSTRUCCION') {
		tab_url = './poliza/solicitud/endoso-poliza-integral-comercio';
		temp_url = './poliza/solicitud/endoso-poliza-integral-comercio';
	}else if (tipo_poliza == 'SEGURO_TECNICO') {
		tab_url = './poliza/solicitud/endoso-seguro-tecnico';
		temp_url = './poliza/solicitud/endoso-seguro-tecnico';
	}


	//console.debug('temp'+temp_url);
	
	$('div#CenterPane>div#tabs>ul.ui-tabs-nav>li a').each(function() {

		var href = $(this).attr('tab_url');
		
	
		if (href == tab_url)
			existe = true;
	});

	if (!existe)
		$("#tabs").tabs("add", url, title);
	if (existe)
		alert('Ya esta abierta la solapa, cerrala para poder ver la Solicitud');

	return false;
}



function afectarPoliza(url){

	if(!confirmar()) return false;
	
	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	//Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	var href = $(tab).attr('href') ;                       
	
    $.ajax({url: url
    	,data:{from:'poliza'}
        , success:function(result){
        $(href).html(result);
      }});
   
    return false;
	
	
}


function listPolizas(f) {
	var url = f.action;
	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	// Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	var href = $(tab).attr('href');

	$.ajax({
		url : url,
		data : {
			buscar : f.buscar.value
			,busqueda : f.busqueda.value
			,criterio : f.criterio.value
			,asegurado_id : f.asegurado_id.value
		},
		success : function(result) {
			$(href).html(result);
		}
	});

	return false;
}

function refacturarPoliza(url){

	if(!confirmar()) return false;

	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	//Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	var href = $(tab).attr('href') ;                       
	
    $.ajax({url: url
        , success:function(result){
        $(href).html(result);
      }});
   
    return false;
	
	
}


function loadTabPoliza(url, title, tipo_poliza) {
	//Para que le paso la URL???
	var existe = false;
	/* if ... tipo poliza */
	if (tipo_poliza == 'AUTOMOTORES') {
		tab_url = './poliza/poliza/view-poliza-automotores';
		temp_url = './poliza/poliza/view-poliza-automotores';
	} else if (tipo_poliza == 'ADUANEROS') {
		tab_url = './poliza/poliza/view-poliza-aduaneros';
		temp_url = './poliza/poliza/view-poliza-aduaneros';
	}else if (tipo_poliza == 'CONSTRUCCION') {
		tab_url = './poliza/poliza/view-poliza-construccion';
		temp_url = './poliza/poliza/view-poliza-construccion';
	}else if (tipo_poliza == 'ALQUILER') {
		tab_url = './poliza/poliza/view-poliza-alquiler';
		temp_url = './poliza/poliza/view-poliza-alquiler';
	}else if (tipo_poliza == 'RESPONSABILIDAD_CIVIL') {
		tab_url = './poliza/poliza/view-poliza-responsabilidad-civil';
		temp_url = './poliza/poliza/view-poliza-responsabilidad-civil';
	}else if (tipo_poliza == 'TRANSPORTE_MERCADERIA') {
		tab_url = './poliza/poliza/view-poliza-transporte-mercaderia';
		temp_url = './poliza/poliza/view-poliza-transporte-mercaderia';
	}else if (tipo_poliza == 'IGJ') {
		tab_url = './poliza/poliza/view-poliza-igj';
		temp_url = './poliza/poliza/view-poliza-igj';
	}else if (tipo_poliza == 'ACCIDENTES_PERSONALES') {
		tab_url = './poliza/poliza/view-poliza-accidentes-personales';
		temp_url = './poliza/poliza/view-poliza-accidentes-personales';
	}else if (tipo_poliza == 'JUDICIALES') {
		tab_url = './poliza/poliza/view-poliza-judiciales';
		temp_url = './poliza/poliza/view-poliza-judiciales';
	}else if (tipo_poliza == 'VIDA') {
		tab_url = './poliza/poliza/view-poliza-vida';
		temp_url = './poliza/poliza/view-poliza-vida';
	}else if (tipo_poliza == 'INTEGRAL_COMERCIO') {
		tab_url = './poliza/poliza/view-poliza-integral-comercio';
		temp_url = './poliza/poliza/view-poliza-integral-comercio';
	}else if (tipo_poliza == 'INCENDIO') {
		tab_url = './poliza/poliza/view-poliza-incendio';
		temp_url = './poliza/poliza/view-poliza-incendio';
	}else if (tipo_poliza == 'SEGURO_TECNICO') {
		tab_url = './poliza/solicitud/view-seguro-tecnico';
		temp_url = './poliza/solicitud/view-seguro-tecnico';
	}

	$('div#CenterPane>div#tabs>ul.ui-tabs-nav>li a').each(function() {

		var href = $(this).attr('tab_url');
		// console.debug(href);
		if (href == temp_url)
			existe = true;
	});

	if (!existe)
		$("#tabs").tabs("add", url, title);
	if (existe)
		alert('Ya esta abierta la solapa, cerrala para poder ver la Solicitud');

	return false;
}

//Este puede ser uno para todos, deberia cargarlo una sola vez
function listPaginator(g){

	var url = $(g).attr('url')
	var tabs_sel = $('#tabs').tabs();
	var idx = tabs_sel.tabs('option', 'selected');

	//Trae el tab correspondiente
	var tab = $('#tabs ul li a')[idx];
	//console.debug($('#tabs ul li a'));
	var href = $(tab).attr('href') ;       
	var url = $(g).attr('href')

	    $.ajax({url: url,success:function(result){
	        $(href).html(result);
	      }});
	   
	    return false;
	}

$(document).ready(function() 
	    { 
	        $("#poliza_list").tablesorter();
	        /*$("#poliza_list tr").hover(
	        	     function()
	        	     {
	        	      $(this).children("td").addClass("ui-state-hover");
	        	     },
	        	     function()
	        	     {
	        	      $(this).children("td").removeClass("ui-state-hover");
	        	     }
	        	    );*/
	    } 
	); 