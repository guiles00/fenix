<?php
class Servicios_Personas
{
	private $_autorizador;
	
	function __construct(Servicios_Autorizacion_Personas $sa)
	{
		if ($sa instanceof Servicios_Autorizacion_Personas)
		{
			$this->_autorizador=$sa;	
		}else{
			$this->_autorizador=null;//new Servicios_Autorizacion_Personas($u);	
		}
    } 
    
    public function setAutorizador(Servicios_Autorizacion_Personas $sa)
    {
    	if ($sa instanceof Servicios_Autorizacion_Personas)
		{
			$this->_autorizador=$sa;	
		}else{
			throw new Servicios_Exception_Personas_Generic("clase Autorizadora invalida!");	
		}
    }
    
    public function getAutorizador()
    {
    	if ($this->_autorizador instanceof Servicios_Autorizacion_Personas)
		{
    		return $this->_autorizador;
		}else{
			throw new Servicios_Exception_Personas_Generic("No hay clase Autorizadora!");
		}
    }
    
    public function ejemploDeMetodo(/*Model_Expediente $e, Model_PasoExpediente $pe,*/ Servicios_Autorizacion_Personas  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Personas) $sa=$this->getAutorizador();
		//	
		if ($sa->isAllowed('metodo_a_consultar'/*, $e, $pe*/))
		{
			return true;
		}else{
			throw new Servicios_Exception_Personas_Generic("No Autorizado!");
		}
	}
	
	 /**
     * getListadoSexosAplicables
     *
     * @param MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params
     * @param Servicios_Autorizacion_Personas  $sa
     * @return MyZend_Doctrine_Record_GridPanelFacade_Response_List
     */	
    public function getListadoSexosAplicables(MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params, Servicios_Autorizacion_Personas  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Personas) $sa=$this->getAutorizador();
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model= new Model_Sexo();
			$options=new MyZend_Doctrine_Record_GridPanelFacade_Config_Options(array());			
			$grid_panel_facade=new MyZend_Doctrine_Record_GridPanelFacade_Base($model, $options);
			return $grid_panel_facade->ajaxGridList($params);
		}else{
			throw new Servicios_Exception_Personas_Generic("No Autorizado!");
		}
	}
	
	 /**
     * getListadoTiposPersonaAplicables
     *
     * @param MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params
     * @param Servicios_Autorizacion_Personas  $sa
     * @return MyZend_Doctrine_Record_GridPanelFacade_Response_List
     */	
    public function getListadoTiposPersonaAplicables(MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params, Servicios_Autorizacion_Personas  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Personas) $sa=$this->getAutorizador();
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model= new Model_TipoPersona();
			$options=new MyZend_Doctrine_Record_GridPanelFacade_Config_Options(array());			
			$grid_panel_facade=new MyZend_Doctrine_Record_GridPanelFacade_Base($model, $options);
			return $grid_panel_facade->ajaxGridList($params);
		}else{
			throw new Servicios_Exception_Personas_Generic("No Autorizado!");
		}
	}
	
	 /**
     * getListadoTiposDocumentoAplicables
     *
     * @param MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params
     * @param Servicios_Autorizacion_Personas  $sa
     * @return MyZend_Doctrine_Record_GridPanelFacade_Response_List
     */	
    public function getListadoTiposDocumentoAplicables(MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params, Servicios_Autorizacion_Personas  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Personas) $sa=$this->getAutorizador();
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model= new Model_TipoDocumento();
			$options=new MyZend_Doctrine_Record_GridPanelFacade_Config_Options(array());			
			$grid_panel_facade=new MyZend_Doctrine_Record_GridPanelFacade_Base($model, $options);
			return $grid_panel_facade->ajaxGridList($params);
		}else{
			throw new Servicios_Exception_Personas_Generic("No Autorizado!");
		}
	}
	
	 /**
     * getListadoIncumbenciasAplicables
     *
     * @param MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params
     * @param Servicios_Autorizacion_Personas  $sa
     * @return MyZend_Doctrine_Record_GridPanelFacade_Response_List
     */	
    public function getListadoIncumbenciasAplicables(MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params, Servicios_Autorizacion_Personas  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Personas) $sa=$this->getAutorizador();
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model= new Model_Incumbencia();
			$options=new MyZend_Doctrine_Record_GridPanelFacade_Config_Options(array());			
			$grid_panel_facade=new MyZend_Doctrine_Record_GridPanelFacade_Base($model, $options);
			return $grid_panel_facade->ajaxGridList($params);
		}else{
			throw new Servicios_Exception_Personas_Generic("No Autorizado!");
		}
	}
	
	 /**
     * getListadoTitulosAplicables
     *
     * @param MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params
     * @param Servicios_Autorizacion_Personas  $sa
     * @return MyZend_Doctrine_Record_GridPanelFacade_Response_List
     */	
    public function getListadoTitulosAplicables(MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params, Servicios_Autorizacion_Personas  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Personas) $sa=$this->getAutorizador();
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model= new Model_Titulo();
			$options=new MyZend_Doctrine_Record_GridPanelFacade_Config_Options(array());			
			$grid_panel_facade=new MyZend_Doctrine_Record_GridPanelFacade_Base($model, $options);
			return $grid_panel_facade->ajaxGridList($params);
		}else{
			throw new Servicios_Exception_Personas_Generic("No Autorizado!");
		}
	}
	
	 /**
     * getListadoEspecialidadesAplicables
     *
     * @param MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params
     * @param Servicios_Autorizacion_Personas  $sa
     * @return MyZend_Doctrine_Record_GridPanelFacade_Response_List
     */	
    public function getListadoEspecialidadesAplicables(MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params, Servicios_Autorizacion_Personas  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Personas) $sa=$this->getAutorizador();
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model= new Model_Especialidad();
			$options=new MyZend_Doctrine_Record_GridPanelFacade_Config_Options(array());			
			$grid_panel_facade=new MyZend_Doctrine_Record_GridPanelFacade_Base($model, $options);
			return $grid_panel_facade->ajaxGridList($params);
		}else{
			throw new Servicios_Exception_Personas_Generic("No Autorizado!");
		}
	}
	
	 /**
     * getListadoPersonas
     *
     * @param MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params
     * @param Servicios_Autorizacion_Personas  $sa
     * @return MyZend_Doctrine_Record_GridPanelFacade_Response_List
     */	
    public function getListadoPersonas(MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams $params, Servicios_Autorizacion_Personas  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Personas) $sa=$this->getAutorizador();
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model= new Model_Persona();
			$options=new MyZend_Doctrine_Record_GridPanelFacade_Config_Options(
				array(
					'foreign_columns'=>	
						array(
							"Model_Persona.Model_Sexo.sexo as sexo_text",
							"Model_Persona.Model_TipoPersona.tipo as tipo_persona_text",
							"Model_Persona.Model_TipoDocumento.tipo as tipo_documento_text",
							"Model_Persona.Model_Incumbencia.incumbencia as incumbrencia_text",
							"Model_Persona.Model_Especialidad.especialidad as especialidad_text",
							"Model_Persona.Model_Titulo.titulo as titulo_text",
						)
				)
			);
			$grid_panel_facade=new MyZend_Doctrine_Record_GridPanelFacade_Base($model, $options);
			return $grid_panel_facade->ajaxGridList($params);
		}else{
			throw new Servicios_Exception_Personas_Generic("No Autorizado!");
		}
	}
	
	 /**
     * getPersonaById
     *
     * @param Integer $persona_id
     * @param Servicios_Autorizacion_Personas  $sa
     * @return Model_Persona
     */	
    public function getPersonaById(Integer $persona_id, Servicios_Autorizacion_Personas  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Personas) $sa=$this->getAutorizador();
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model_persona= new Model_Persona();
			$persona = $model_persona->getTable()->find($persona_id);
			if ($persona instanceof Model_Persona){
				return $persona;
			}else{
				throw new Servicios_Exception_Personas_NotFound("Registro inexistente");				
			}
		}else{
			throw new Servicios_Exception_Personas_Generic("No Autorizado!");
		}
	}
	
	 /**
     * addPersona
     *
     * @param Model_Persona $persona
     * @param Servicios_Autorizacion_Personas  $sa
     * @return Model_Persona
     */	
    public function addPersona(Model_Persona $persona, Servicios_Autorizacion_Personas  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Personas) $sa=$this->getAutorizador();
		$filter_to_upper = new Zend_Filter_StringToUpper('UTF-8');
		$filter_to_upper->setEncoding('UTF-8');		
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			if (isset($persona->persona_id) && is_integer($persona->persona_id))
			{
				throw new Servicios_Exception_Personas_ForcedId("error en la operacion: intentando agregar un registro con id forzado"); 
			}
			$persona->persona_id=null;
			$persona->nombre  =  $filter_to_upper->filter($persona->nombre);
			$persona->apellido=  $filter_to_upper->filter($persona->apellido);						
			try{
				$persona->save();
				return $persona;
			}catch(Exception $e){
				throw new Servicios_Exception_Personas_InsertFailure("error al insertar");
			}			
		}else{
			throw new Servicios_Exception_Personas_Generic("No Autorizado!");
		}
	}
	
	 /**
     * setPersona
     *
     * @param Model_Persona $persona
     * @param Servicios_Autorizacion_Personas  $sa
     * @return Model_Persona
     */	
    public function setPersona(Model_Persona $persona, Servicios_Autorizacion_Personas  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Personas) $sa=$this->getAutorizador();
		$filter_to_upper = new Zend_Filter_StringToUpper('UTF-8');
		$filter_to_upper->setEncoding('UTF-8');		
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model_persona= new Model_Persona();
			$persona_base = $model_persona->getTable()->find($persona->persona_id);
			if ($persona_base instanceof Model_Persona){
				$datos_persona=$persona->toArray();
				unset($datos_persona['persona_id']);
				$persona_base->fromArray($datos_persona);
				$persona_base->nombre  =  $filter_to_upper->filter($persona_base->nombre);
				$persona_base->apellido=  $filter_to_upper->filter($persona_base->apellido);
				try{
					$persona_base->save();
					return $persona_base;
				}catch(Exception $e){
					throw new Servicios_Exception_Personas_UpdateFailure("error al actualizar");
				}
			}else{
				throw new Servicios_Exception_Personas_NotFound("Registro inexistente");				
			}			
		}else{
			throw new Servicios_Exception_Personas_Generic("No Autorizado!");
		}
	}	
	
	 /**
     * delPersonaById
     *
     * @param Integer $persona_id
     * @param Servicios_Autorizacion_Personas  $sa
     * @return Boolean
     */	
    public function delPersonaById(Integer $persona_id, Servicios_Autorizacion_Personas  $sa)
	{
		if (!$sa instanceof Servicios_Autorizacion_Personas) $sa=$this->getAutorizador();
		//	
		if (true/*$sa->isAllowed('metodo_a_consultar')*/)
		{
			$model_persona= new Model_Persona();
			$persona = $model_persona->getTable()->find($persona_id);
			if ($persona instanceof Model_Persona){
				try{
					$persona->delete();
					return true;
				}catch(Exception $e){
					throw new Servicios_Exception_Personas_DeleteFailure("error al eliminar");
				}				
			}else{
				throw new Servicios_Exception_Personas_NotFound("Registro inexistente");				
			}
		}else{
			throw new Servicios_Exception_Personas_Generic("No Autorizado!");
		}
	}
	
	/**
	 *   
	  addDomicilioPersona(Persona, Domicilio)

	  setDomicilioPersona(Persona, Domicilio)
	
	  getDomicilioPersonaById(domicilio_id)
	
	  delDomicilioPersonaById(domicilio_id)
	
	  getListadoDomiciliosPersona(Persona, ListParams)
	  */
}