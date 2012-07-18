<div id="main">
	<div id="logueo">
    	<div style="position:relative; font-size:15px; top:180px; font-family: 'Stencil Std'; font-weight:bold; color:#CC0000">
			<?php session_start();
             
      
             
            function quitar($mensaje)
            {
                $nopermitidos = array("'",'\\','<','>',"\"");
                $mensaje = str_replace($nopermitidos, "", $mensaje);
                return $mensaje;
            }     
             
            if(trim($_POST["usuario"]) != "" && trim($_POST["password"]) != "")
            {
                // Puedes utilizar la funcion para eliminar algun caracter en especifico
                //$usuario = strtolower(quitar($HTTP_POST_VARS["usuario"]));
                //$password = $HTTP_POST_VARS["password"];
               
                // o puedes convertir los a su entidad HTML aplicable con htmlentities
                $usuario = strtolower(htmlentities($_POST["usuario"], ENT_QUOTES));   
                $password = $_POST["password"];
                    
             
                $result = mysql_query('SELECT password, usuario FROM usuarios WHERE usuario=\''.$usuario.'\'');
                if($row = mysql_fetch_array($result)){
                    if($row["password"] == $password){
             
                        $_SESSION["k_username"] = $row['usuario'];
                       
                        echo 'Logueado: '.$_SESSION['k_username'].' <p>';                
						
						?>
                       <div style="margin:10px;"><a href="<?php echo $appUrl . "/?sk=../login/mantenedor" ?>">Mantenedor</a></div>
                        <?php
                        //Elimina el siguiente comentario si quieres que re-dirigir autom&aacute;ticamente a index.php
                       
                        /*Ingreso exitoso, ahora sera dirigido a la pagina principal.
                        <SCRIPT LANGUAGE="javascript">
                        location.href = "index.php";
                        </SCRIPT>*/
             
                    }else{
                        echo 'Password incorrecto';
                    }
                }else{
                    echo 'Usuario no existente en la base de datos';
                }
                mysql_free_result($result);
            }else{
                echo 'Debe especificar un usuario y password';
            }
            mysql_close();
            ?>
    	</div>
	</div>
</div>