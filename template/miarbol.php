
<!--<center>-->
	<div id="main">
		<div id="miarbol">
			<br>
			<strong>Elegir Padre:</strong>
			<?php
				echo "<select name='padre'>";
				echo "<option value='0'></option>";
				$facebookFriendsURL = "https://graph.facebook.com/me/friends?access_token=".$GLOBALS['access_token'];
				$respuesta = file_get_contents($facebookFriendsURL);
				$amigos =  json_decode($respuesta,true);
				asort($amigos['data']);
				foreach($amigos['data'] as $amigo)
				{
					echo "<option value='".$amigo['name']."'>".$amigo['name']."</option>";
				}
				echo "</select>";
			?>
			<strong>Elegir Madre:</strong>
			<?php
				echo "<select name='madre'>";
				echo "<option value='0'></option>";
				$facebookFriendsURL = "https://graph.facebook.com/me/friends?access_token=".$GLOBALS['access_token'];
				$respuesta = file_get_contents($facebookFriendsURL);
				$amigos =  json_decode($respuesta,true);
				asort($amigos['data']);
				foreach($amigos['data'] as $amigo)
				{
					
					echo "<option value='".$amigo['name']."'>".$amigo['name']."</option>";
				}
				echo "</select>";
			
			?>
			<div id="actualizar">
			<form action ="<?=$PHP_SELF ?>" method="post" name= "arbol">
			<strong>Cantidad de hermanos:</strong> <input type="text" size = "2" name="num_hermano" value=""/> <input type="submit" value="Elegir" name="btnElegir"/><br>
			</form>
			<?php			
			
			if(isset($_POST['btnElegir']))
			{
				echo "el numero es: ".$_POST['num_hermano'];
				
				$num = $_POST['num_hermano'];
				if($num == "")
				{
					$nombre = "No ha escrito numero";
				}
				else
				for ($i = 1; $i <= $num; $i++)  
				{
				?> <br><br>Elegir Hermano
					<?php
					echo "<select name='hermano'>";
					echo "<option value='0'></option>";
					$facebookFriendsURL = "https://graph.facebook.com/me/friends?access_token=".$GLOBALS['access_token'];
					$respuesta = file_get_contents($facebookFriendsURL);
					$amigos =  json_decode($respuesta,true);
					asort($amigos['data']);
					foreach($amigos['data'] as $amigo)
					{
						echo "<option value='".$amigo['name']."'>".$amigo['name']."</option>";
					}
					echo "</select>";
				}
			}
				?> 
				<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
				<script>
				function cargar()
				{
     				$('#actualizar').load('miarbol.html');
				}
				</script>
			</div>
			<a href="#" onclick="cargar()"; return false>Cargar contenido</a>
			<?php
				/*
				$fotoPerfil = "<img src='http://graph.facebook.com/".$GLOBALS['userID']."/picture?type=large'/>"; //guarda la imagen en una  variable
				$fotoApp = "<img src='http://graph.facebook.com/".$GLOBALS['appId']."/picture?type=large'/>"; //guarda la imagen de la app en una  variable
				$fotoPerfilchica = "<img src='http://graph.facebook.com/".$GLOBALS['userID']."/picture'/>"; //guarda la imagen pequeña en una  variable
				echo $fotoPerfil; //imprime la foto en pantalla
				echo $fotoApp; //imprime la foto de la app en pantalla
				echo $fotoPerfilchica; //imprime la foto en pantalla
			*/
			?>
			
			
			
		</div><!-- div miarbol -->
	</div><!-- div main -->	
<!--</center>-->