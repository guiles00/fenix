<?php
class MyZend_Doctrine_Record_ExcelExporterFacade_Base // implements MyZend_Doctrine_Record_ExcelExporterFacade_Interface
{
	public function __construct($c, $options=array())
	{
       	if (! $c instanceof Doctrine_Record)
       	{
       		throw new Exception('tratando de construir el facade para un objeto no admitido.');
       	}    
		$this->_modelo=$c;
		$this->_options=$options;
    }
    
    public function StreamAsXls($params)
    {
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
		// @todo cuando sean muchisimos, va a consumir mucha memoria
		// buscar una solucion mas eficiente en memoria, que recorra
		// la coleccion y vaya enviando en xls y liberando la memoria.		
		MyZend_Document_Xls::getInstance($items)->streamAsXls();
		return true;
    }
}