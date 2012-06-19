<?php session_start();
// no olvidemos incluir nuestras funciones
include("funciones.php");
conectar();
if (puede_ver('pagina')==false){
    die('<br /><br />No tiene permiso para esta &aacute;rea, consulte a <a href="http://empresario.mx">www.empresario.mx</a><br /><br /><br />');
}
?>