<?php
class Db  // se declara una clase para hacer la conexion con la base de datos 
{
    var $con;
    
    
    function __construct()
    {
        //@TODO
        //Sacar los datos de un archivo de configuracion
        //se definen los datos del servidor de base de datos  
        $conection['server']="localhost";  //host 
        $conection['user']="guiles";         //  usuario 
        $conection['pass']="guiles";        //password 
        $conection['base']="gseguros";            //base de datos 


        // crea la conexion pasandole el servidor , usuario y clave 
        $conect= mysql_connect($conection['server'],$conection['user'],$conection['pass']);

        if ($conect) // si la conexion fue exitosa , selecciona la base 
        {
            mysql_select_db($conection['base']);
            $this->con=$conect;
        }
    }
    function getConexion() // devuelve la conexion 
    {
        return $this->con;
    }
    function Close()  // cierra la conexion 
    {
        mysql_close($this->con);
    }

}


?>
