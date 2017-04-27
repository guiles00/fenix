<?php
class Query   // se declara una clase para poder ejecutar las consultas, esta clase llama a la clase anterior 
{

    var $pconeccion;
    var $pconsulta;
    var $resultados;
    function __construct()  // constructor, solo crea una conexion usando la clase "Conexion" 
    {
        $this->pconeccion= new Db();
    }
    function executeQuery($cons)  // metodo que ejecuta una consulta y la guarda en el atributo $pconsulta 
    {
        $this->pconsulta= mysql_query($cons,$this->pconeccion->getConexion());
        return $this->pconsulta;
    }
    function getResults()   // retorna la consulta en forma de result. 
    {
        return $this->pconsulta;
    }

    function Close()        // cierra la conexion 
    {
        $this->pconeccion->Close();
    }

    function Clean() // libera la consulta 
    {
        mysql_free_result($this->pconsulta);
    }

    function getResultados() // debuelve la cantidad de registros encontrados 
    {
        return mysql_affected_rows($this->pconeccion->getConexion()) ;
    }

    function getAffect() // devuelve las cantidad de filas afectadas 
    {
        return mysql_affected_rows($this->pconeccion->getConexion()) ;
    }
    
    function getID() // devuelve la cantidad de registros encontrados 
    {
        return mysql_insert_id($this->pconeccion->getConexion()) ;
    }
}

?>
