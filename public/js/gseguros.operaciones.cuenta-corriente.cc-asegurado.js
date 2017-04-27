function viewDetalleMovimiento(url){

  
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
     //var asegurado_id = $('#id_asegurado_movimientos');
  
   
           var asegurado_id = $('#id_asegurado_movimientos').val();
     

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
                     url : "./operaciones/cuenta-corriente/cc-asegurado/asegurado_id/"+asegurado_id+"/busqueda_poliza/1/numero_poliza/"+numero_poliza,
                   //  data : asegurado_id,
                     success : function(result) {
                     $(href).html(result);
                             }
                });

            });



            $('.imprimir_detalle_movimiento_asegurado_poliza').click(function(x){
               
               var movimiento_id = x.target.parentNode.parentNode.id;
               var buscar_poliza_anterior = $('#buscar_poliza_anterior').val();
              // console.debug(buscar_poliza_anterior);
              // return false;
                var tabs_sel = $('#tabs').tabs();
                var idx = tabs_sel.tabs('option', 'selected');

                //Trae el tab correspondiente
                var tab = $('#tabs ul li a')[idx]; 
                var href = $(tab).attr('href') ;
                 $.ajax({
                     url : "./operaciones/cuenta-corriente/imprimir-detalle-movimiento-asegurado/movimiento_id/"+movimiento_id+"/numero_poliza_busqueda/"+buscar_poliza_anterior,
                   //  data : compania_id,
                     success : function(result) {
                     $(href).html(result);
                             }
                });
                
            });



            $('.eliminar_movimiento_asegurado_poliza').click(function(x){
               
               if(!confirm('Desea Eliminar el Movimiento')) return false;

               var movimiento_id = x.target.parentNode.parentNode.id;

                var tabs_sel = $('#tabs').tabs();
                var idx = tabs_sel.tabs('option', 'selected');

                //Trae el tab correspondiente
                var tab = $('#tabs ul li a')[idx]; 
                var href = $(tab).attr('href') ;
                
                 $.ajax({
                     url : "./operaciones/cuenta-corriente/eliminar-movimiento-asegurado/movimiento_id/"+movimiento_id+"/asegurado_id/"+asegurado_id,
                   //  data : compania_id,
                     success : function(result) {
                     $(href).html(result);
                    }
                });
                
            });
});            