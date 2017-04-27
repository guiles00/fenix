<?php
 Class Domain_FactoryDetallePoliza {

	
	public function crearDetallePolizaCaucion(){

		return new Model_DetalleCaucion();
		
	}
	
 	public function crearDetallePolizaAutomotor(){

		return new Model_DetalleAutomotor();
		
	}
	public function crearDetallePolizaAduaneros(){

		return new Model_DetalleAduaneros();
		
	}
	
 public function crearDetallePolizaAccidentesPersonales(){

		return new Model_DetalleAccidentesPersonales();
		
	}

  public function crearDetallePolizaConstruccion(){

		return new Model_DetalleConstruccion();
		
	}
	
 public function crearDetallePolizaAlquiler(){

		return new Model_DetalleAlquiler();
		
	}
	
 public function crearDetallePolizaResponsabilidadCivil(){

		return new Model_DetalleResponsabilidadCivil();
		
	}
 public function crearDetallePolizaTransporteMercaderia(){

		return new Model_DetalleTransporteMercaderia();
		
	}
 public function crearDetallePolizaIGJ(){

		return new Model_DetalleIGJ();	
 }
  public function crearDetallePolizaJudiciales(){

		return new Model_DetalleJudiciales();
	}
  public function crearDetallePolizaVida(){

		return new Model_DetalleVida();
		
	}
 	public function crearDetallePolizaIntegralComercio(){

		return new Model_DetalleIntegralComercio();
		
	}
	public function crearDetallePolizaIncendio(){

		return new Model_DetalleIncendio();
		
	}
	public function crearDetallePolizaTecnico(){

		return new Model_DetalleTecnico();
		
	}
	public function crearDetallePolizaSeguroTecnico(){

		return new Model_DetalleSeguroTecnico();
		
	}
	
	
	
}
