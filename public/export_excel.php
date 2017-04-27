<?
require_once("Articulo.php");
require_once("sQuery.php");
require_once("Conexion.php");


header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=articulos.xls");
header("Pragma: no-cache");
header("Expires: 0");

function __autoload($class_name) {
   $file = $class_name.'.php';
  //$file = './'.$class_name.'.php';
  if (file_exists($file)) require_once $file;
}

$articulo = new Articulo();
$articulos  = $articulo->getArticulos();



echo"   <table border='1'> 
        <tr>
        <th>Nombre</th>
        <th>Descripcion</th>
        <th>Codigo</th>
        <th>Precio Mayorista</th>  
        <th>Precio Distribuidor</th>  
        </tr>" ;



while($row = mysql_fetch_array($articulos)){ 
    
    echo "<tr>";
    echo "<td>".$row['nombre']."</td>"; 
    echo "<td>".$row['descripcion']."</td>"; 
    echo "<td>".$row['codigo']."</td>"; 
    echo "<td>".$row['precio_mayorista']."</td>";
    echo "<td>".$row['precio_distribuidor']."</td>";
    echo "</tr>";

} 

echo "</table>";

?>
