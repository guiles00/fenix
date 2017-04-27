
function viewDetalleMovimientoCompania(url){

  
		var tabs_sel = $('#tabs').tabs();
        var idx = tabs_sel.tabs('option', 'selected');
    
        //Trae el tab correspondiente
        var tab = $('#tabs ul li a')[idx]; 
        //console.debug($('#tabs ul li a'));
        var href = $(tab).attr('href') ;
       // var url = $(g).attr('href')
    
            $.ajax({url: url,success:function(result){
                $(href).html(result);
              }});
  
            return false;


}

function onClickAtras(url){

	var tabs_sel = $('#tabs').tabs();
    var idx = tabs_sel.tabs('option', 'selected');

    //Trae el tab correspondiente
    var tab = $('#tabs ul li a')[idx]; 
    //console.debug($('#tabs ul li a'));
    var href = $(tab).attr('href') ;
   // var url = $(g).attr('href')

        $.ajax({url: url,
            success:function(result){
            $(href).html(result);
          }});

        return false;
	
}

$(document).ready(
        function() {
   
           var compania_id = $('#id_compania_movimientos').val();
     

            // Busqueda de poliza
            $('#busqueda_poliza_movimientos').click(function() {

                var numero_poliza = $('#numero_poliza').val();
                console.debug(numero_poliza);
                var tabs_sel = $('#tabs').tabs();
                var idx = tabs_sel.tabs('option', 'selected');

                //Trae el tab correspondiente
                var tab = $('#tabs ul li a')[idx]; 
                var href = $(tab).attr('href') ;

                $.ajax({
                     url : "./operaciones/cuenta-corriente/cc-compania/compania_id/"+compania_id+"/busqueda_poliza/1/numero_poliza/"+numero_poliza,
                   //  data : compania_id,
                     success : function(result) {
                     $(href).html(result);
                             }
                });


            });


            $('.imprimir_detalle_movimiento_poliza').click(function(x){
               
               var movimiento_id = x.target.parentNode.parentNode.id;


                var tabs_sel = $('#tabs').tabs();
                var idx = tabs_sel.tabs('option', 'selected');

                //Trae el tab correspondiente
                var tab = $('#tabs ul li a')[idx]; 
                var href = $(tab).attr('href') ;
                 $.ajax({
                     url : "./operaciones/cuenta-corriente/imprimir-detalle-movimiento-compania/movimiento_id/"+movimiento_id,
                   //  data : compania_id,
                     success : function(result) {
                     $(href).html(result);
                             }
                });
                
            });


            $('.eliminar_movimiento_poliza').click(function(x){
               
               if(!confirm('Desea Eliminar el Movimiento')) return false;
               
               var movimiento_id = x.target.parentNode.parentNode.id;


                var tabs_sel = $('#tabs').tabs();
                var idx = tabs_sel.tabs('option', 'selected');

                //Trae el tab correspondiente
                var tab = $('#tabs ul li a')[idx]; 
                var href = $(tab).attr('href') ;
                
                 $.ajax({
                     url : "./operaciones/cuenta-corriente/eliminar-movimiento-compania/movimiento_id/"+movimiento_id+"/compania_id/"+compania_id,
                   //  data : compania_id,
                     success : function(result) {
                     $(href).html(result);
                    }
                });
                
            });
});            