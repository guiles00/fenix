var x = $(document);
x.ready(inicializarEventos);

function inicializarEventos(){
		var x;
		x=$("input");
		x.focus(tomaFoco);
		x.blur(pierdeFoco);
}

function tomaFoco(){
	var x=$(this);
	x.css("border","#FA5858");
}

function pierdeFoco(){
	var x=$(this);
	x.css("border","2");
}