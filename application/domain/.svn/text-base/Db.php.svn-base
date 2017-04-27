<?php
class Domain_Db  // se declara una clase para hacer la conexion con la base de datos 
{
    var $con;
    
    
    function __construct()
    {
        //@TODO
        //Sacar los datos de un archivo de configuracion
        //se definen los datos del servidor de base de datos
        $db_data = $this->getValues();  
        $conection['server']="localhost";  //host 
        $conection['user']=$db_data['user'];         //  usuario 
        $conection['pass']=$db_data['password'];        //password 
        $conection['base']=$db_data['db'];            //base de datos 


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

    private function getValues(){
        
        $entorno = getenv('ENTORNO');
        $entorno = 'dev';
        $config = new Zend_Config_Ini('../config.ini',$entorno);

        return $config->gseguros->toArray();
    } 
}


?>
