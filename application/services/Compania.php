<?php
class Services_Compania
{

	public function getCompanias(Model_Compania $model_compania){

		$logger = Zend_Registry::get('logger');
		$logger->log($model_entidad,Zend_Log::INFO);

		$companias = $model_compania
		->getTable()
		->createQuery()
		->execute()
		->toArray();

		return $compania;
	}

	public function getEntidadById(Model_Entidad $model_entidad, Int $id){

		$logger = Zend_Registry::get('logger');

		$entidad = $model_entidad
		->getTable()
		->createQuery()
		->andWhere('entidad_id = ?',$id)
		->execute()
		->toArray();

		return $entidad[0];
	}

	public function editEntidad(Model_Entidad $model_entidad, $params){

		$model_entidad_base = ($params['entidad_id']==null)?$model_entidad :$model_entidad->getTable()->find($params['entidad_id']);
		
		$model_entidad_base->nombre = $params['nombre'];
		$model_entidad_base->apellido = $params['apellido'];
		$model_entidad_base->save();

		return true;
	}




	/**
	 * getListadoTicket
	 *
	 * @param MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params
	 * @param Servicios_Autorizacion_Defensoria  $sa
	 * @return MyZend_Doctrine_Record_GridPanelFacade_Response_List
	 */
	public function getListadoTicket(MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params, Servicios_Autorizacion_Defensoria  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Defensoria) $sa=$this->getAutorizador();
		//
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model= new Model_Derivacion();
			$options=new MyZend_Doctrine_Record_GridPanelFacade_Config_Options(
			array(
					'foreign_columns'=>	
			array(
						"Model_Derivacion.Model_Persona.nombre as nombre",
						"Model_Derivacion.Model_Persona.apellido as apellido",
						"Model_Derivacion.Model_Persona.numero_documento as numero_documento",
						"Model_Derivacion.Model_Motivo.motivo as motivo",
			)
			)
			);
			$grid_panel_facade=new MyZend_Doctrine_Record_GridPanelFacade_Base($model, $options);
			return $grid_panel_facade->ajaxGridList($params);
		}else{
			throw new Servicios_Exception_Defensoria_Generic("No Autorizado!");
		}
	}

	/**
	 * getDerivacionById
	 *
	 * @param Integer $derivacion_id
	 * @param Servicios_Autorizacion_Derivacion  $sa
	 * @return Model_Derivacion
	 */
	public function getConsultaById(Integer $consulta_def_id, Servicios_Autorizacion_Defensoria  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Defensoria) $sa=$this->getAutorizador();

		//
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model_consulta_def= new Model_ConsultaDef();
			$consulta = $model_consulta_def->getTable()->find($consulta_def_id);

			if ($consulta instanceof Model_ConsultaDef){
				return $consulta;
			}else{
				throw new Servicios_Exception_Defensoria_NotFound("Registro inexistente");
			}
		}else{
			throw new Servicios_Exception_Defensoria_Generic("No Autorizado!");
		}
	}

	/**
	 * addTicket
	 *
	 * @param Model_Derivacion $derivacion
	 * @param Servicios_Autorizacion_Defensoria  $sa
	 * @return Model_Derivacion
	 */
	public function addConsulta(Model_ConsultaDef $consulta_def,Model_Persona $persona_id, Servicios_Autorizacion_Defensoria  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Defensoria) $sa=$this->getAutorizador();
		//
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			if (isset($consulta_def->consulta_def_id) && is_integer($consulta_def->consulta_def_id))
			{
				throw new Servicios_Exception_Defensoria_ForcedId("error en la operacion: intentando agregar un registro con id forzado");
			}
			$consulta_def->consulta_def_id=null;
			//paso los datos para el sorteo defensoria
			$defensorias = Doctrine_Query::create ()->from ( 'Model_Ubicacion t' )->select('t.ubicacion_id')->Where ( 't.tipo_organismo_id = ?', 7 )->execute ()->toArray ();
			foreach($query as $key=>$defensoria){
				$model_consulta_def=new Model_ConsultaDef();
				$defensorias[$key]['cantidad_consultas']=$model_consulta_def->getTable()->findByUbicacionId($defensoria->ubicacion_id)->count();
			}
			$ubicacion=$defensorias[$this->sortear_defensoria($defensorias)];
			$ubicacion_id = $ubicacion['ubicacion_id'];
			$motivo_id = $consulta_def->motivo_id;
			$observaciones = $consulta_def->observaciones;
			$persona_id=$persona_id->persona_id;
			try{
				$consulta_def->ubicacion_id=$ubicacion_id;
				$consulta_def->motivo_id=$motivo_id;
				$consulta_def->observaciones=$observaciones;
				$consulta_def->fecha_consulta = date("d-m-Y");
				$consulta_def->save();
				//data para la funcion generar_ticket devuelve ticket_id
				$consulta_def_id=$consulta_def->consulta_def_id;
				$ticket_id=$this->generar_ticket($consulta_def_id,$persona_id);
				$consulta_def->ticket_id = $ticket_id;
				$consulta_def->save();
				return $consulta_def;
			}catch(Exception $e){
				throw new Servicios_Exception_Defensoria_InsertFailure("error al insertar");
			}
		}else{
			throw new Servicios_Exception_Defensoria_Generic("No Autorizado!");
		}
	}
	private function sortear_defensoria($defensorias)
	{
		// el sorteo es compensatorio aleatorio-probabilistico-prioritario
		// (aumenta las prioridades en las que menos tienen, pero no excluye ninguna defensoria)
		//Trae el numero cantidad_consultas mayor
		$maxima_cantidad=0;
		foreach($defensorias as $key=>$value)
		{
			if ($defensorias[$key]['cantidad_consultas']>$maxima_cantidad)
			{
				$maxima_cantidad= $defensorias[$key]['cantidad_consultas'];
			}
		}
		$puntaje_total=0;
		$potencia=3; // aumentarla acrecenta las diferencias en las probabilidades
		foreach($defensorias as $key=>$value)
		{
			$defensorias[$key]['puntaje']=pow(1+$maxima_cantidad-$defensorias[$key]['cantidad_consultas'],$potencia);
			$puntaje_total+=$defensorias[$key]['puntaje'];
		}
		$puntaje_sorteado=(rand()/getrandmax())*$puntaje_total;
		//
		$puntaje_acumulado=0;
		foreach($defensorias as $key=>$value)
		{
			$puntaje_acumulado+= $defensorias[$key]['puntaje'];
			if ($puntaje_acumulado>=$puntaje_sorteado)
			{
				return $key;
			}
		}
		return $key;
	}

	/**
	 * getListadoMotivo
	 *
	 * @param MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params
	 * @param Servicios_Autorizacion_Defensoria  $sa
	 * @return MyZend_Doctrine_Record_GridPanelFacade_Response_List
	 */
	public function getListadoMotivo(MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params, Servicios_Autorizacion_Defensoria  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Defensoria) $sa=$this->getAutorizador();
		//
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model= new Model_Motivo();
			$options=new MyZend_Doctrine_Record_GridPanelFacade_Config_Options(array());
			$grid_panel_facade=new MyZend_Doctrine_Record_GridPanelFacade_Base($model, $options);
			return $grid_panel_facade->ajaxGridList($params);
		}else{
			throw new Servicios_Exception_Defensoria_Generic("No Autorizado!");
		}
	}

	private function generar_ticket($consulta_def_id,$persona_id)
	{
		//esto va a la funcion generar ticket
		$sap = new Servicios_Autorizacion_Personas(MyZend_Usuario::getInstance()->getUsuario());
		$sp = new Servicios_Personas($sap );
		$persona = $sp->getPersonaById($persona_id);
		$consulta_def =$this->getConsultaById($consulta_def_id);

		$model_ubicacion = new Model_Ubicacion();
		$ubicacion = $model_ubicacion->getTable()->findOneByUbicacionId($consulta_def->ubicacion_id);

		$contenido_html = file_get_contents ( APPLICATION_PATH . './../public/plantillas/ticket-defensoria.html' );
		$template=$contenido_html;
		$variables = array(
			'{ubicacion_id}'=>$ubicacion->ubicacion,
			'{nro_derivacion}'=>$consulta_def->consulta_def_id,
			'{motivo}'=>$consulta_def->motivo_id,
			'{nombre}'=>$persona->nombre,
			'{apellido}'=>$persona->apellido,
			'{fecha}'=> $consulta_def->fecha_consulta
		);
		$contenido_html= str_replace(array_keys($variables),array_values($variables),$template);
		$ticket= new Model_Ticket();
		$ticket->ticket=$contenido_html;
		$ticket->save();
		return $ticket->ticket_id;

	}
	/*public function getTicket(Model_Ticket $ticket_id,$asPdf, Servicios_Autorizacion_Defensoria  $sa)
	 {
		if (!$sa instanceof Servicios_Autorizacion_Defensoria) $sa=$this->getAutorizador();
		$model_ticket = new Model_Ticket();
		$contenido_html=''; //poner meta tag utf8
		$ticket = $model_ticket->getTable()->findOneByTicketId($ticket_id);
		$contenido_html .= $ticket->ticket;
		if (isset ( $asPdf )){
		MyZend_Document_Html::getInstance($contenido_html)->streamAsPdf('Ticket' . '.pdf');
		exit ( 0 );
		}else{
		throw new Servicios_Exception_Defensoria_Generic("No Autorizado!");
		}
		return $contenido_html;die();
		}*/
}