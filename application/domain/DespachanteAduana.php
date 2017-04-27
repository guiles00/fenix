<?php
class Domain_DespachanteAduana implements Domain_IEntidad {
	private $_model ;
	private $_nombre = "DESPACHANTE_ADUANA";

	public function __construct($id=null){

		$model = new Model_DespachanteAduana();
		$this->_model = ($id==null)?new $model: $model->getTable()->find($id) ;


	}
	public function getModel(){
		return $this->_model;
	}

	public function getNombre(){
		return $this->_nombre;
	}

	//Trae las solicitudes, por ahora hardcodeado
	public function getById($id){

		$row = $this->_model
		->getTable()
		->createQuery()
		->Where('estado_id in (0,1)')
		->andWhere('despachante_aduana_id = ?',$this->_model->despachante_aduana_id)
		->execute()
		->toArray();
		return $row;
	}


	/*
	 * Filtra poliza por despachante_aduana
	 * estado_id=1 => solicitudes confirmadas
	 */

	public function getPolizas(){
		$this->_model_poliza = new Model_Poliza();

		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id in ?',array(2))
		->andWhere('despachante_aduana_id = ?',$this->_model->despachante_aduana_id)
		->execute()
		->toArray();

		return $row;

	}
	//Filtra poliza por despachante_aduana
	public function findPolizaByNumero($numero){

		$estado = Domain_EstadoPoliza::getIdByCodigo('VIGENTE');

		$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->andWhere('estado_id = ?',$estado)
		->andWhere('despachante_aduana_id = ?',$this->_model->despachante_aduana_id)
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->execute()
		->toArray();

		return $row;

	}

	// Estado = 0 es Poliza no confirmada => Solicitud
	public function findSolicitudByNumero($numero){

		//$estado = Domain_EstadoPoliza::getIdByCodigo('SOLICITUD_PENDIENTE');

		$this->_model_poliza = new Model_Poliza();
		$row = $this->_model_poliza
		->getTable()
		->createQuery()
		->Where('estado_id in (0,1)')
		->andWhere('despachante_aduana_id = ?',$this->_model->despachante_aduana_id)
		->andWhere('numero_solicitud like ? OR numero_poliza like ?',array("%$numero%","%$numero%"))
		->execute()
		->toArray();
		//->getSqlQuery();
		//echo "<pre>";
		//print_r($row);
		//exit;
		return $row;

	}
	public function getSaldo(){

	}
	public function getSolicitudes(){

	}

	public static function getNameById($id){

		$m_despachante_aduana = new Model_DespachanteAduana();
		$despachante_aduana = $m_despachante_aduana->getTable()->find($id);

		return $despachante_aduana->nombre;
	}
	
public function getPolizasDefault(){
		
	}
}

