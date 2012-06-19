<?
//mysql
$db_host = "74.54.131.130";
$db_user = "visual_test";
$db_pass = "proyecto1234";
$db_name = "visual_pruebas";
$table_fb_sessions = "fb_sessions";
$table_fb_user = "fb_user";
$table_fb_status = "fb_status";

// cargamos la libreria
$base = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$f_core = $base . "core" . DIRECTORY_SEPARATOR;
$f_template = $base . "template" . DIRECTORY_SEPARATOR;

$extension_file = ".php";

require $f_core . 'base_facebook.php'; //cargando 
require $f_core . 'facebook.php'; //Incluimos el PHP SDK v.3.0.0 de Facebook 
require $f_core . 'mysql.php'; //Incluidmos las funciones mysql
require $f_core . 'functions.php'; //Incluimos las funciones propias
require $f_core . 'gfacebook.php'; //cargando 

$config = array();
$config['appId'] = '232238550217441';
$config['secret'] = 'fc6a3a1a162a562b37a5f4aa4a478c52';
$facebook = new Facebook($config);

// Get User ID
$user = $facebook->getUser();
// Get Session Facebook
$session = $facebook->getSession();

if ($session) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
	$user_profile = $facebook->api('/me');
	$amigos = $facebook->api('/me/friends');
	save_sesion($session);
	//insert_datos_user($user_profile);
	insert_user($user_profile);
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
} // validacion para saber si el usuario esta logueado
?>
<br><strong>Elegir hermano:</strong>
<?php 
	echo "<select name='friends'>";
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
?> <br>
				
