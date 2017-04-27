<?php
interface MyZend_Doctrine_Record_JQGridFacade_Interface
{
    /*
     * Lista en formato json desde un query, 
     * pensado para usar en grillas solo lectura
     * cuando los filtros simples por Or o And no bastan,
     * acepta solo parametros pagina y filas
     * */
    public static function ajaxGridListFromQuery($params, $query,$idx);
    /* funciones de ABM standar jqgrid */
	public function ajaxGridList($params);
    public function ajaxGridAdd($params);
    public function ajaxGridDel($params);
    public function ajaxGridSet($params);
}