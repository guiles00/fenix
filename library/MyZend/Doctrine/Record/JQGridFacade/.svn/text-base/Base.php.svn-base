<?php
/** 
 * PARA LAS FUNCIONES SIMPLES DE SELECCION no haremos clases nuevas,  
 * Usaremos Mejor las nativas de DOCTRINE
 * ejemplo: Doctrine::getTable('Role')->findOneByName('test');
 * 			Doctrine::getTable('Role')->findOneByName('test')->toArray();
 * 
 * SI USAREMOS UNA CLASE COMO ESTA PARA INTERFASEAR CON OTROS COMPONENTES COMUNES
 * Por Ejemplo: Podemos hacer una fachada para armar la interfase con JQGrid.
 * 

 * */

/**
 * MyZend_Doctrine_Record_JQGridFacade_Base
 * 
 * This class decora un objeto que utiliza un Doctrine_Record y nos 
 * simplifica metodos comunes de utilidad para interfase con JQGrid
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    
 */
require_once 'MyZend/Doctrine/Record/JQGridFacade/Interface.php';
class MyZend_Doctrine_Record_JQGridFacade_Base implements MyZend_Doctrine_Record_JQGridFacade_Interface
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
		$this->_options=$options;
    }
	
    /*
     * Lista en formato json desde un query, 
     * pensado para usar en grillas solo lectura
     * cuando los filtros simples por Or o And no bastan,
     * acepta solo parametros pagina y filas
     * */
    public static function ajaxGridListFromQuery($params, $query, $idx)
    {
    	//calculos para el paginador...
    	$page = (int)@$params['page']; // get the requested page
		$limit = (int)@$params['rows']; // get how many rows we want to have into the grid
    	$count=$query->count();
    	$total_pages = ($count<=0)?0:ceil($count/$limit);
    	$page=($page > $total_pages)?$total_pages:$page;
		$start = $limit*$page - $limit; 
		$start=($start<=0)?0:$start;
    	//fin calculos para el paginador...    	
    	// NECESITO QUE ME ESPECIFIQUEN IDX
    	$query->orderBy($idx);    	
    	$query=$query	->limit($limit)
  						->offset($start);
		$items=$query->execute(/* array(), Doctrine::HYDRATE_SCALAR */)->toArray();

        foreach ($items as $item_key=>$item_value)
    	{
    		// asumo PK simple (de una sola columna, QUE ME ESPECIFICARON EN LA LlAMADA)
    		$items[$item_key]['id']=$items[$item_key][$idx];
    	}
    	
    	$respuesta= array(
    		"page"=>$page,
    		"total"=>$total_pages,
    		"records"=>$count,
    		"rows"=>$items,
    	);
		return $respuesta;
    }
    
    /* 
     * 
     * funciones de ABM standar jqgrid 
     * 
     * */
	
    public function ajaxGridList($params)
    {    	
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
				list($foreign_column_item_name,$foreign_column_item_alias)=split(' as ',$foreign_column);
				$foreign_column_item_name=trim($foreign_column_item_name);
				$foreign_column_item_alias=trim($foreign_column_item_alias);				
				$foreign_columns_aliases[$foreign_column_item_alias]=$foreign_column_item_name;
			}
		}
		// fin ADD COLUMNAS FORANEAS //
    	
		if (isset($params['_search']) && ($params['_search']=="true"))
    	{
    		// casos: New in version 3.3 -> Multiple Form Search
    		//        New in version 3.5 -> Integrated Search Toolbar
    		//        Live data Manipulation-> Searching Data
    		// lo adapto para tratar los dos protocolos de busqueda
    		// tanto el avanzado como el basico por igual
    		if (!isset($params['filters']))
    		{
    			//recorro los nombres de columna y me fijo si recibi parametro correspondiente
    			$filters['groupOp']="AND";
    			$filters['rules']=array();
    			foreach ($table->getColumnNames() as $column_name)
    			{
    				if (isset($params[$column_name]))
    				{    					
    					$definition=$table->getDefinitionOf($column_name);
    					/** @todo ver como obtener definicion cuando son tablas relacionadas... ahora lo harcodeo como string */
    					if (!isset($definition['type']))
    					{
    						$definition['type']="string";
    					}
    					//para string hago una busqueda con LIKE
    					$filters['rules'][]=array(
    						'field'=>$column_name,    						
    						'op'=>($definition['type']=="string")?'cn':'eq',    						
    						'data'=>$params[$column_name],    						
    					);
    				}
    			}
    			$params['filters']=Zend_Json::encode($filters);
    		}
			// fin adaptacion
			// sigue el codigo para el protocolo de busqueda avanzada
			// casos: New in version 3.5 -> Advanced Searching
    		if (isset($params['filters']))
    		{    			
    			$filters = Zend_Json::decode($params['filters']);
    			foreach ($filters['rules'] as $filters_rule)
    			{    				
    				//veo los fields foraneos para ponerle su nombre original (le cambio el alias por el nombre real)
    				if (isset($foreign_columns_aliases[$filters_rule['field']]))
    				{
    					$filters_rule['field']=$foreign_columns_aliases[$filters_rule['field']];
    				}
    				//fin veo los fields foraneos para ponerle su nombre original
    				if ((in_array($filters_rule['field'],array_values($foreign_columns_aliases)) || in_array($filters_rule['field'],$table->getColumnNames()) ) &&
    					in_array($filters_rule['op'], array_keys($this->_qopers))   )
    				{
    					$definition=$table->getDefinitionOf($filters_rule['field']);
    					/** @todo ver como obtener definicion cuando son tablas relacionadas... ahora lo harcodeo como string */
    					if (!isset($definition['type']))
    					{
    						$definition['type']="string";
    					}
    					
    					    					
    					$v=$filters_rule['data'];
    					$op=$filters_rule['op'];
    					$qop=$this->_qopers[$op];
						$metodo=(@$filters['groupOp']!="OR")?"andWhere":"orWhere";
    					if ( ($definition['type']=='string') || (strpos($filters_rule['field'],'_id')!==false) )
						{
	    					switch ($op) {
								case 'in' :
									$metodo=$metodo.'In';
									$query=$query->$metodo($filters_rule['field'],explode(',', $v));
									break;
								case 'ni' :
									$metodo=$metodo.'NotIn';
									$query=$query->$metodo($filters_rule['field'],explode(',', $v));
									break;
								case 'bw':
								case 'bn':
									$query=$query->$metodo($filters_rule['field'].' '.$qop.' ?', "$v%");									
									break;
								case 'ew':
								case 'en':
									$query=$query->$metodo($filters_rule['field'].' '.$qop.' ?', "%$v");								
									break;
								case 'cn':				
								case 'nc':
									$query=$query->$metodo($filters_rule['field'].' '.$qop.' ?', "%$v%");																									
									break;
								default:
									$query=$query->$metodo($filters_rule['field'].' '.$qop.' ?', $v);
							}    					
						}else{
							if (!in_array($op, array('in','ni','bw','bn','ew','en','cn','nc')))
							{
								$query=$query->$metodo($filters_rule['field'].' '.$qop.' ?', $v);
							}
						}						
    				}
    			}
    		}else{
    			
    		}
    	}
    	//die($table->getDefinitionOf("Model_Expediente.Model_Materia.codigo_materia"));
    	//$query=$query->addWhere("Model_Expediente.Model_Materia.codigo_materia in ( 'A1', 'B1'  ) ");
		//calculos para el paginador...
    	$page = (int)@$params['page']; // get the requested page
		$limit = (int)@$params['rows']; // get how many rows we want to have into the grid
    	$count=$query->count();
    	$total_pages = ($count<=0)?0:ceil($count/$limit);
    	$page=($page > $total_pages)?$total_pages:$page;
		$start = $limit*$page - $limit; 
		$start=($start<=0)?0:$start;
    	//fin calculos para el paginador...    	    	
    	
    	//determinamos el ordenamiento
		$sidx = $params['sidx']; // get index row - i.e. user click to sort
		$sord = $params['sord']; // get the direction
		$sord = (strtolower($sord)=="desc")?"DESC":"ASC";
		if (in_array($sidx,$table->getColumnNames()))
		{
			$query=$query->orderBy(implode(',',array_merge(array("$sidx $sord"),array_diff($table->getIdentifierColumnNames(),array($sidx)) )));
		}else{
			$query=$query->orderBy(implode(',',$table->getIdentifierColumnNames()));			
		}
    	//fin determinamos el ordenamiento
		
    	$query=$query	->limit($limit)
  						->offset($start);
  						
    	//$sql=$query->getSql(); //cambio a getSqlQuery en la 1.2?
    	//die($sql);
    	$items=$query->execute(/* array(), Doctrine::HYDRATE_SCALAR */)->toArray();

    	foreach ($items as $item_key=>$item_value)
    	{
    		$identifier_column_names=$table->getIdentifierColumnNames();
    		// asumo PK simple (de una sola columna)
    		$items[$item_key]['id']=$items[$item_key][$identifier_column_names[0]];
    	}

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
    	
    	    	
    	////////   UNSET HIDDEN COLUMNS   ///////
    	if (isset($this->_options['hide_columns']))
	    {
	    	foreach ($items as $item_key=>$item_value)
	    	{
	    		foreach($this->_options['hide_columns'] as $hide_column)
	    		{
	    			unset($items[$item_key][$hide_column]);
	    		}
	    	}	    	
    	}
    	////////   fin UNSET HIDDEN COLUMNS   ///////
    			
    	$respuesta= array(
    		"page"=>$page,
    		"total"=>$total_pages,
    		"records"=>$count,
    		"rows"=>$items,
    	);
		return $respuesta;
    }
    
    public function ajaxGridAdd($params)
    {
		/** @todo chequear si el usuario no existia previamente */
		$this->_modelo->fromArray($params);		
		return $this->_modelo->save();    	
    }
    
    public function ajaxGridDel($params)
    {
    	$registro=$this->_modelo->getTable()->find($params['id']);
    	if ($registro==null)
    	{
    		return false;
    	}
    	return $registro->delete();        
    }
    
    public function ajaxGridSet($params)
    {
    	$registro=$this->_modelo->getTable()->find($params['id']);
    	if ($registro==null)
    	{
    		return false;
    	}
    	$registro->synchronizeWithArray($params);		
    	return $registro->save();        
    }
    
}