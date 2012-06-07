<?php
//require "load.php";
?>
<center>
	<div id="main">
		<div id="miarbol">	
			<div class="tab">  
				<?php foreach( $GLOBALS['json_publish']->bottons as $botton ) : ?>
					<?php $tabClass = ( $GLOBALS['page'] == $botton->sk ) ? "tab-selected" : "tab-normal"; ?>
						<a class="<?php echo $tabClass; ?>" href="<?php echo $GLOBALS['appUrl'] . "?sk=" . $botton->sk; ?>" target="<?php echo $botton->target; ?>"><?php echo $botton->name; ?></a>
				<?php endforeach; ?>	
					
				<div style="margin:10px;"><a href="<?php echo $appUrl . "/?sk=send" ?>">Publicar</a></div>
			</div><!-- div tab -->
			
			<?php
				$fotoPerfil = "<img src='http://graph.facebook.com/".$GLOBALS['userID']."/picture?type=large'/>"; //guarda la imagen en una  variable
				$fotoApp = "<img src='http://graph.facebook.com/".$GLOBALS['appId']."/picture?type=large'/>"; //guarda la imagen de la app en una  variable
				$fotoPerfilchica = "<img src='http://graph.facebook.com/".$GLOBALS['userID']."/picture'/>"; //guarda la imagen pequeña en una  variable
				echo $fotoPerfil; //imprime la foto en pantalla
				echo $fotoApp; //imprime la foto de la app en pantalla
				echo $fotoPerfilchica; //imprime la foto en pantalla
				$AToken = $GLOBALS['access_token'];
				echo $AToken;
			?>
						
		</div>
		<!-- div miarbol -->
	</div><!-- div main -->	

</center>