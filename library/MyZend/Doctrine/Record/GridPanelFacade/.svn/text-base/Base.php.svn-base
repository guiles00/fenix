<?php
require_once 'MyZend/Doctrine/Record/GridPanelFacade/Interface.php';
class MyZend_Doctrine_Record_GridPanelFacade_Base implements MyZend_Doctrine_Record_GridPanelFacade_Interface
{
	private  $_modelo;
	private  $_qopers = array(
	  'eq'=>" = ",
	  'ne'=>" <> ",
	  'lt'=>" < ",
	  'le'=>" <= ",
	  'gt'=>" > ",
	  'ge'=>" >= ",
	  'bw'=>" LIKE ",
	  'bn'=>" NOT LIKE ",
	  'in'=>" IN ",
	  'ni'=>" NOT IN ",
	  'ew'=>" LIKE ",
	  'en'=>" NOT LIKE ",
	  'cn'=>" LIKE " ,
	  'nc'=>" NOT LIKE " );
		
	public function __construct($c, $options=array())
	{
       	if (! $c instanceof Doctrine_Record)
       	{
       		throw new Exception('tratando de construir el facade para un objeto no admitido.');
       	}    
		$this->_modelo=$c;
		$this->_options=($options instanceof MyZend_Doctrine_Record_GridPanelFacade_Config_Options)?$options->toArray():$options;
    }

// *** nueva forma de interpretar filtros, ahora pueden ser anidados
private function procesa_regla($rule, $_qopers, $foreign_columns_aliases, $table)
{
	$field=$rule['field'];
	$data=$rule['data'];
	$op=$rule['op'];
	//veo los fields foraneos para ponerle su nombre original (le cambio el alias por el nombre real)
	if (isset($foreign_columns_aliases[$field]))
	{
		$field=$foreign_columns_aliases[$field];
	}	
	//fin veo los fields foraneos para ponerle su nombre original
	if ((in_array($field,array_values($foreign_columns_aliases)) || in_array($field,$table->getColumnNames()) ) &&
		in_array($op, array_keys($_qopers))   )
	{
		$definition=$table->getDefinitionOf($field);
		/** @todo ver como obtener definicion cuando son tablas relacionadas... ahora lo harcodeo como string */
		if (!isset($definition['type']))
		{
			$definition['type']="string";
		}
		$qop=$_qopers[$op];
		if ( $definition['type']=='string' )
		{
			switch ($op) {
				case 'in' : // In
					$params=explode(',',$data);
					$signos_pregunta=implode(',',array_fill(0, count($params), '?'));
					$text= "$field $qop ($signos_pregunta)";
					break;
				case 'ni' : // Not In
					$params=explode(',',$data);
					$signos_pregunta=implode(',',array_fill(0, count($params), '?'));
					$text= "$field $qop ($signos_pregunta)";
					break;
				case 'bw': // Comienza con
					$params=array("$data%");
					$text= "$field $qop ?";
					break;
				case 'bn': // No Comienza con
					$params=array("$data%");
					$text= "$field $qop ?";
					break;
				case 'ew': // Termina con
					$params=array("%$data");
					$text= "$field $qop ?";
					break;
				case 'en': // No Termina con
					$params=array("%$data");
					$text= "$field $qop ?";
					break;
				case 'cn':	// Contiene
					$params=array("%$data%");
					$text= "$field $qop ?";
					break;			
				case 'nc':  // No Contiene
					$params=array("%$data%");
					$text= "$field $qop ?";
					break;			
				default:
					$params=array("$data");
					$text= "$field $qop ?";
					break;
			}    
		}else{
			if (!in_array($op, array('in','ni','bw','bn','ew','en','cn','nc')))
			{
				if ($definition['type']=='integer')
				{
					$params=array((int)$data);
					$text= "$field $qop ?";
				}else{
					$params=array("$data");
					$text= "$field $qop ?";
				}				
			}else{
				return false;
			}
		}
		return array('text'=>$text, 'params'=>$params);
	}else{
		return false;
	}
} 

private function procesa_filtro($filtros, $_qopers, $foreign_columns_aliases, $table)
{
	$group_op=$filtros['groupOp'];
	$t=array();
	$p=array();
	foreach($filtros['rules'] as $rule)
	{
		if (isset($rule['groupOp'])){
			$res=$this->procesa_filtro($rule, $_qopers, $foreign_columns_aliases, $table);
			$t[]=$res['text'];
			$p[]=$res['params'];
		}else{
			$res=$this->procesa_regla($rule, $_qopers, $foreign_columns_aliases, $table);
			if ($res!==false)
			{
				$t[]=$res['text'];
				$p[]=$res['params'];
			}
		}
	}
	if (count($filtros['rules'])==0 || count($t)==0)
	{
		$t[]='( 1=? )';
		$p[]=array(1);
	}
	$params=array();
	foreach ($p as $item)
	{
		$params= array_merge($params, $item);
	}
	return array('text'=>' ( '.implode(" $group_op ", $t).' ) ','params'=>$params);
}
// *** fin nueva forma de interpretar filtros, ahora pueden ser anidados

    public function ajaxGridList($params)
    {    
    	$is_version_con_objetos=($params instanceof MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams);
		$params=($params instanceof MyZend_Doctrine_Record_GridPanelFacade_Config_ListParams)?$params->toArray():$params;
    	
    	//
    	$table=$this->_modelo
    					->getTable();
    	$query=$table->createQuery();

		// ADD COLUMNAS LOCALES
    	$query=$query->select(join($table->getColumnNames(),','));
		// fin ADD COLUMNAS LOCALES
    	
		// ADD COLUMNAS FORANEAS //
		$foreign_columns_arr=array();
		$foreign_columns_aliases=array();
		if (isset($this->_options['foreign_columns']))
		{
			foreach ($this->_options['foreign_columns'] as $foreign_column)
			{
				/** @todo Agregar verificacion de que la columna foranea exista...  */	
				$query=$query->addSelect($foreign_column);
				//list($foreign_column_item_name,$foreign_column_item_alias)=split(' as ',$foreign_column);
				list($foreign_column_item_name,$foreign_column_item_alias)=preg_split("/ as /", $foreign_column);				
				$foreign_column_item_name=trim($foreign_column_item_name);
				$foreign_column_item_alias=trim($foreign_column_item_alias);				
				$foreign_columns_aliases[$foreign_column_item_alias]=$foreign_column_item_name;
			}
		}
		// fin ADD COLUMNAS FORANEAS //
		$logger = Zend_Registry::get ( 'logger' );
		
		//ACA LOS FILTROS		
    	if (isset($params['filters']))
    	{
    		//$logger->log ( 'filtros!'.$params['filters'], Zend_Log::INFO );    			
    		$filters = Zend_Json::decode($params['filters']);
    		// var_dump($filters);die();
			$fp=$this->procesa_filtro($filters, $this->_qopers, $foreign_columns_aliases, $table);
			// var_dump($fp['text']);var_dump($fp['params']);die();
			$query=$query->where($fp['text'], $fp['params']);
    	}
		//fin ACA LOS FILTROS
    		
		/*** ordenamiento... ***/
		//determinamos el ordenamiento
		$sidx = isset($params['sort'])?$params['sort']:''; // get index row - i.e. user click to sort
		$sord = isset($params['dir'])?$params['dir']:'asc'; // get the direction
		$sord = (strtolower($sord)=="desc")?"DESC":"ASC";
		if (in_array($sidx,$table->getColumnNames()))
		{
			$query=$query->orderBy(implode(',',array_merge(array("$sidx $sord"),array_diff($table->getIdentifierColumnNames(),array($sidx)) )));
		}else{
			$query=$query->orderBy(implode(',',$table->getIdentifierColumnNames()));			
		}
    	//fin determinamos el ordenamiento
		
    	/*** paginador... ***/		
		if (isset($params['start']))
		{
			$query=$query->offset((int)$params['start']);
		}
		if (isset($params['limit']))
		{
			$query=$query->limit((int)$params['limit']);
		}
		
		$logger = Zend_Registry::get ( 'logger' );
		$logger->log ( $query->getSqlQuery(), Zend_Log::INFO );
		$items=$query->execute()->toArray();
		
		/*** @todo columnas calculadas... ***/
		////////   ADD COMPUTED COLUMNS   ///////
    	if (isset($this->_options['computed_columns']))
    	{
    		foreach ($this->_options['computed_columns'] as $computed_column)
    		{
    			$compute_fnc=$computed_column['compute_fnc'];		
		    	foreach ($items as $item_key=>$item_value)
		    	{
		    		$items[$item_key][$computed_column['name']]=$compute_fnc($items[$item_key]);
		    	}
		    			
    		}
    	}
    	////////   fin ADD COMPUTED COLUMNS   ///////
    	
		/*** @todo hidden columns... ***/
    	// si me paso un array columns, solo le muestro esas!
    	// osea: quito todas las demas
    	if(isset($params['columns'])){
	    	if (($params['columns']||'')!='')
	    	{
	    		//recorro data y hago unset de las columnas que no estan en el listado $params['columns']
	    		$columns=explode(',',$params['columns']);
	    		foreach ($items as $item_key=>$item_value)    		
	    		{
	    			foreach ($items[$item_key] as $column_key=>$column_value)
	    			{    			
		    			if (!in_array($column_key, $columns))
		    			{
		    				unset ($items[$item_key][$column_key]);
		    			}
	    			}
	    		}
	    	}
    	}
    	
    	/*** ***/
		if($items){
			$respuesta = array(
				'success'=>true,
				'message'=>'Listado',
				'total'=>$query->count(),
				'data'=> $items
			);
		}else{
			$respuesta = array(
				'success'=>true,
				'message'=>'No se encontro ningun registro',
				'total'=>0,
				'data'=> array()
			);			
		}
		
		if ($is_version_con_objetos)
		{
			$respuesta=new MyZend_Doctrine_Record_GridPanelFacade_Response_List($respuesta);	
		}
		return $respuesta;
    }
	
	public function ajaxGridSet($id,$params)
    {
    	$modelo=$this->_modelo->getTable()->find($id['valor']);
    	if ($modelo==null)
    	{
    		return false;
    	}
    	$modelo->synchronizeWithArray($params);
    	$modelo->save();
		
		$respuesta = $this->ajaxFindRecord($id);
		$respuesta['message']='Se modifico el registro';
		return $respuesta;
    }
	
	public function ajaxGridAdd($params)
	{
		$modelo=$this->_modelo;
		$modelo->fromArray($params);
		$modelo->save();
		
		$respuesta = array(
			'success'=>true,
			'message'=> 'Se agrego el registro',
			'data'=>$modelo->toArray()
		);
		return $respuesta;
	}
	
	public function ajaxGridDel($id)
	{
		$modelo=$this->_modelo->getTable()->find($id);
    	if ($modelo==null)
    	{
    		return false;
    	}
		/**
		*	Este metodo esta comentado para que ricardito ford no se mande cagadas
		*/
    	$modelo->delete();
		
		/**
		*	Importante!!! esta condicion esta al reves o negada
		*	Esto es debido a que por el momento elimina en forma imaginaria
		*	y deberia mostrar el mensaje que si se pudo eliminar el modelo
		*	Para realmente poder eliminar se deberia descomentar //$modelo->delete();
		*	y el if deberia ser if($modelo)
		*/
		if(!$modelo){
			$respuesta = array(
				'success'=>false,
				'message'=> 'Error no se pudo eliminar el registro '.$id,
				'data'=>array()
			);
		}else{
			$respuesta = array(
				'success'=>true,
				'message'=> 'Se elimino el registro '.$id,
				'data'=>array()
			);
		}
		return $respuesta;
	}
	
	
    /*
     * Lista en formato json desde un query, 
     * pensado para usar en grillas solo lectura
     * cuando los filtros simples por Or o And no bastan,
     * acepta solo parametros pagina y filas
     * */
    public static function ajaxGridListFromQuery($params, $query, $idx,$options=array())
    {
    	/*** paginador... ***/		
		if (isset($params['start']))
		{
			$query=$query->offset((int)$params['start']);
		}
		if (isset($params['limit']))
		{
			$query=$query->limit((int)$params['limit']);
		}
    	
    	// NECESITO QUE ME ESPECIFIQUEN IDX
    	$query=$query->orderBy($idx);    	
    	    	
    	$items=$query->execute()->toArray();
    	//die($query->getSqlQuery());
    	
		/*** @todo columnas calculadas... ***/
		////////   ADD COMPUTED COLUMNS   ///////
    	if (isset($options['computed_columns']))
    	{
    		foreach ($options['computed_columns'] as $computed_column)
    		{
    			$compute_fnc=$computed_column['compute_fnc'];
		    	foreach ($items as $item_key=>$item_value)
		    	{
		    		$items[$item_key][$computed_column['name']]=$compute_fnc($items[$item_key]);
		    	}
		    			
    		}
    	}
    	////////   fin ADD COMPUTED COLUMNS   ///////
    	
    	$respuesta = array(
            'success'=>true,
			'total'=>$query->count(),
            'data'=> $items
        );
		return $respuesta;
    }
	
	public function ajaxFindRecord($id)
	{
		$rules = array(
			'field'=>$id['nombre'],
			'op'=>'eq',
			'data'=>$id['valor']
		);
		$filtros = array(
			'groupOp'=>'',
			'rules'=> array($rules)
		);
		$params['filters'] = Zend_Json::encode($filtros);
		$respuesta = $this->ajaxGridList($params);
		unset($respuesta['total']);
		return $respuesta;
	}
}