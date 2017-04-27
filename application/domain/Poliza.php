<?php
class Domain_Poliza {
	private $_model_poliza ;
	private $_model_poliza_valores;
	private $_model_detalle_pago;
	private $_model_detalle_poliza;

	public function __construct($id=null){

		//La poliza puede ser de cualquier tipo, ahora me trae solamente la del tipo automotor
		//corregirlo con un factory ( creo )
		//por ahora traeme el principal
		if($id==null){
			$this->_model_poliza = new Model_Poliza();
			$this->_model_poliza_valores = new Model_PolizaValores();
			$this->_model_detalle_automotor = new Model_DetalleAutomotor();
			$this->_model_detalle_pago = new Model_DetallePago();
			$this->_mode_detalle_poliza = null; //Aca todavia no se el tipo de poliza, no puedo traer el modelo asociado
		}else{

			$model_poliza = new Model_Poliza();
			$model_poliza_valores = new Model_PolizaValores();
			$model_detalle_pago = new Model_DetallePago();

			$this->_model_poliza =  $model_poliza->getTable()->find($id) ;
			$this->_model_poliza_valores = $model_poliza_valores->getTable()
			->find($this->_model_poliza->poliza_valores_id);

			//$this->_model_detalle_automotor = $model_detalle_automotor->getTable()
			//->find($this->_model_poliza->poliza_detalle_id);

			$this->_model_detalle_pago = $model_detalle_pago->getTable()
			->find($this->_model_poliza->detalle_pago_id);

			//En este caso tengo el tipo de poliza ergo traigo el detalle correspondiente
			$this->_model_detalle_poliza = $this->getModelDetallePoliza($this->_model_poliza->tipo_poliza_id);

		}

		//como tengo varios modelos en esta clase tengo que instanciar primero el Modelo
		//Principal, luego traer los id de los otros modelos e instanciarlos
		//$model_poliza_valores = new Model_PolizaValores();
		//$this->_model_poliza_valores = ($id==null)?new $model_poliza_valores: $model_poliza->getTable()->find($id) ;

	}
	public function getModelPoliza(){
		return $this->_model_poliza;
	}
	public function getModelPolizaValores(){
		return $this->_model_poliza_valores;
	}
	public function getModelDetalleAutomotor(){
		return $this->_model_detalle_automotor;
	}
	public function getModelDetallePago(){
		return $this->_model_detalle_pago;
	}
	public function getModelDetalle(){
		return $this->_model_detalle_poliza;
	}

	public function getModelDetallePoliza($tipo=null){
		//Simple Factory que devuelve la poliza
		$factory_detalle_poliza = new Domain_FactoryDetallePoliza();
		
		$tipo_poliza = Domain_TipoPoliza::getNameById($tipo);

		//Por ahora con los ids
		switch ($tipo_poliza) {
			case '1':
					
				$m_detalle_poliza_caucion = $factory_detalle_poliza->crearDetallePolizaCaucion();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza_caucion;

				}
					
				return $m_detalle_poliza_caucion->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;
				//Aduaneros
			case 'ADUANEROS':
					
				$m_detalle_poliza_aduaneros = $factory_detalle_poliza->crearDetallePolizaAduaneros();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza_aduaneros;

				}
					
				return $m_detalle_poliza_aduaneros->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;
					
				//Accidentes Personales
			case 'ACCIDENTES_PERSONALES':
					
				$m_detalle_poliza = $factory_detalle_poliza->crearDetallePolizaAccidentesPersonales();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza;

				}
					
				return $m_detalle_poliza->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;

				//Caucion - Construccion
			case 'CONSTRUCCION':
					
				$m_detalle_poliza = $factory_detalle_poliza->crearDetallePolizaConstruccion();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza;

				}
					
				return $m_detalle_poliza->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;
					
			case 'ALQUILER':
					
				$m_detalle_poliza = $factory_detalle_poliza->crearDetallePolizaAlquiler();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza;

				}
					
				return $m_detalle_poliza->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;

			case 'RESPONSABILIDAD_CIVIL':
					
				$m_detalle_poliza = $factory_detalle_poliza->crearDetallePolizaResponsabilidadCivil();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza;

				}
					
				return $m_detalle_poliza->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;

			case 'TRANSPORTE_MERCADERIA':
					
				$m_detalle_poliza = $factory_detalle_poliza->crearDetallePolizaTransporteMercaderia();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza;

				}
					
				return $m_detalle_poliza->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;

			case 'IGJ':
					
				$m_detalle_poliza = $factory_detalle_poliza->crearDetallePolizaIGJ();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza;

				}
					
				return $m_detalle_poliza->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;
			
			case 'JUDICIALES':
				
				$m_detalle_poliza = $factory_detalle_poliza->crearDetallePolizaJudiciales();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza;

				}
					
				return $m_detalle_poliza->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;

						
			case 'VIDA':
				
				$m_detalle_poliza = $factory_detalle_poliza->crearDetallePolizaVida();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza;

				}
					
				return $m_detalle_poliza->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;
					case 'INTEGRAL_COMERCIO':
				
				$m_detalle_poliza = $factory_detalle_poliza->crearDetallePolizaIntegralComercio();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza;

				}
					
				return $m_detalle_poliza->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;	
				
				case 'INCENDIO':
				$m_detalle_poliza = $factory_detalle_poliza->crearDetallePolizaIncendio();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza;
				}

				return $m_detalle_poliza->getTable()
				->find($this->_model_poliza->poliza_detalle_id);

				break;
				case 'TECNICO':
				
				$m_detalle_poliza = $factory_detalle_poliza->crearDetallePolizaTecnico();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza;

				}
					
				return $m_detalle_poliza->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;
				case 'SEGURO_TECNICO':
				
				$m_detalle_poliza = $factory_detalle_poliza->crearDetallePolizaSeguroTecnico();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza;

				}
					
				return $m_detalle_poliza->getTable()
				->find($this->_model_poliza->poliza_detalle_id);
					
				break;	
			case 'AUTOMOTORES':
					
				$m_detalle_poliza_automotor = $factory_detalle_poliza->crearDetallePolizaAutomotor();
				//Si no tiene poliza_id devuelve el modelo solo
				if($this->_model_poliza->poliza_id ==  null){

					return $m_detalle_poliza_automotor;

				}
					
				return $m_detalle_poliza_automotor->getTable()
				->find($this->_model_poliza->poliza_detalle_id);

				break;
					

			default:
				//echo "nada todavia"	;
				break;
		}



	}

	//aca tiene que filtrar por perfil
	public function getPolizas(){

		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id = ?',1)
		->execute()
		->toArray();
		//->getSqlQuery();
		//	echo "<pre>";
		//print_r("traeme las polizas");
		return $row;

	}
	//Filtrar solicitud por perfil
	public function findPolizaByNumero($numero){

		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->Where('estado_id = ?',1)
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		return $row;

	}

	static public function setPago($poliza_id){

		$q = Doctrine_Query::create()
        ->update('Model_Poliza p')
        ->set('p.pago_compania_id',0)
        ->where('p.poliza_id = ?',array($poliza_id))
		->execute();

	return $q;
	}

}

