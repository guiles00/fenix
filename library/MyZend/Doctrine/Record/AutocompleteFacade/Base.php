<?php
/**
 * MyZend_Doctrine_Record_AutocompleteFacade_Base
 * 
 * This class decora un objeto que utiliza un Doctrine_Record y nos 
 * simplifica metodos comunes de utilidad para interfase con JQueryAutocomplete
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    
 */
require_once 'MyZend/Doctrine/Record/AutocompleteFacade/Interface.php';
class MyZend_Doctrine_Record_AutocompleteFacade_Base implements MyZend_Doctrine_Record_AutocompleteFacade_Interface
{
	private  $_modelo;
	
	public function __construct($c, $options=array())
	{
       	if (! $c instanceof Doctrine_Record)
       	{
       		throw new Exception('tratando de construir el facade para un objeto no admitido.');
       	}    
		$this->_modelo=$c;
		$this->_options=$options;
    }
	
    public function ajaxAutocomplete($params)
    {
    	$table=$this->_modelo
    					->getTable();
    	$query=$table->createQuery();
		
    	if (isset($params['search_fields']))
    	{    	
    		foreach (explode(',',$params['search_fields']) as $search_field)
    		{
    			if (in_array($search_field,$table->getColumnNames()))
    			{
    				$query=$query->orWhere($search_field.' LIKE ?', '%'.$params['q'].'%');
    			}    			
    		}
    	}
    	        
    	$items=$query->limit((int)@$params['limit'])->execute()->toArray();

    	$data=array();
    	
    	foreach ($items as $item_key=>$item_value)
    	{
    		$item_data=array();
	    	if (isset($params['fetch_fields']))
	    	{    	
	    		foreach (explode(',',$params['fetch_fields']) as $fetch_field)
	    		{
	    			$item_data[$fetch_field]=$items[$item_key][$fetch_field];
	    		}
	    	}	    		
    		$data[]=$item_data;	
    	}    	

    	
    	$respuesta="";
    	foreach ($data as $item_data)
    	{
    		$title_glue=(isset($params['title_glue']))?$params['title_glue']:" , ";
    		$desc_glue=(isset($params['desc_glue']))?$params['desc_glue']:" - ";
    		
    		$title=array();
    		foreach (split(',',$params['title_fields']) as $title_field)
    		{
				$title[]=$item_data[$title_field];
    		}

    		$desc=array();
    		foreach (split(',',$params['desc_fields']) as $desc_field)
    		{
				$desc[]=$item_data[$desc_field]; 			
    		}
    		
    		$respuesta.=implode($title_glue, $title)."<br />";	
    		$respuesta.=implode($desc_glue, $desc)."|";	
    		$respuesta.=implode('|', $item_data)."\n";    		
    	}

    	return $respuesta;
    	
    }
    
}