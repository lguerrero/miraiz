<?php
//mysql
$db_host = "74.54.131.130";
$db_user = "visual_test";
$db_pass = "proyecto1234";
$db_name = "visual_pruebas";
?>
<?php
if ( $GLOBALS['user'])
{
	$data = array(
			"access_token" => $GLOBALS['access_token'],
			"link" => $GLOBALS['appUrl'],
			"name" => "Mi Raiz",
			"description" => "Crea tu propio arbol genealogico en Facebook",
			"caption" => "Arbol Genealogico",
			"picture" => "http://alkaos.com/images/523Icono_Logo_260x260jpg_tn.jpg"
		);
		$GraphFacebook_post = new GraphFacebook( $GLOBALS['userID'], $data ); //le paso el arreglo 
		$res = $GraphFacebook_post->Publish( ); // guardo el resultado de la publicacion
		//echo ("$res");
		$array = array( "uid"=>$GLOBALS['userID'], "send_dt"=>date("Y-m-d H:i:s"), "result"=>$res);
		$GLOBALS['mysql']->query( "INSERT INTO ".$GLOBALS['table_fb_status']." ( ".$GLOBALS['mysql']->set_keys($array)." ) VALUES(".$GLOBALS['mysql']->set_values($array).") ON DUPLICATE KEY UPDATE send_dt=now()" );
}
?>