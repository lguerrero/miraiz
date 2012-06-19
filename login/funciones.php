<?php session_start();
function conectar(){
    //datos para establecer la conexion con la base de mysql.
    mysql_connect('localhost','usuario','password')or die ('Ha fallado la conexión: '.mysql_error());
    mysql_select_db('mi_base')or die ('Error al seleccionar la Base de Datos: '.mysql_error()); 
}
 
function puede_ver($pagina=''){
    $usuario = strtoupper($_SESSION['k_username']);
    $result = mysql_query('SELECT usuario FROM autorizaciones WHERE pagina=\''.$pagina.'\' ');
    if($row = mysql_fetch_array($result)){
        $usuarios=$row['usuario']; // obtenemos los nombres de los usuarios
        $array_usuarios= array(); // creamos un arrar
        $array_usuarios=explode(',',$usuarios); // y los metemos pos la separación de la coma
        $total=count($array_usuarios); // cuantos tenemos ?
        if (in_array($usuario, $array_usuarios)) { // si encontramos al ganador ok
            return true;
        }else{ // si no pues un false como respuesta
            return false;
        }
    }
}
?>