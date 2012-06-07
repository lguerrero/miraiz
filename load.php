<?php 
//mysql
$db_host = "74.54.131.130";
$db_user = "visual_test";
$db_pass = "proyecto1234";
$db_name = "visual_pruebas";
$table_fb_sessions = "fb_sessions";
$table_fb_user = "fb_user";
$table_fb_status = "fb_status";

$base = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$f_core = $base . "core" . DIRECTORY_SEPARATOR;
$f_template = $base . "template" . DIRECTORY_SEPARATOR;
 
$extension_file = ".php";

$appTitle = "Mi Raiz (Arbol Genealogico)";
$appUrl = "http://apps.facebook.com/arbol-genealogico";

/* 
* Facebook dirige al usuario a la baseUrl tras autentificarlo
* Comprobamos si nos ha devuelto un $_GET['code']
* para redirigirlo al appBaseUrl 
*/
if (isset($_GET['code'])){
 	header("Location: " . $appUrl);
  	exit;
}  

//requiere
require $f_core . 'base_facebook.php'; //cargando 
require $f_core . 'facebook.php'; //Incluimos el PHP SDK v.3.0.0 de Facebook 
require $f_core . 'mysql.php'; //Incluidmos las funciones mysql
require $f_core . 'functions.php'; //Incluimos las funciones propias
require $f_core . 'gfacebook.php'; //cargando 
//fB datos app
$appId = '232238550217441';
$appSecret = 'fc6a3a1a162a562b37a5f4aa4a478c52';
$invitarMessage = "Te invito a crear tu propia Arbol Genealogico";
 
// Creamos un nuevo objeto Facebook con los datos de nuestra aplicación (cambia los datos por los de tu App ID y tu App Secret).
$facebook = new Facebook(array(
  'appId'  => $appId,
  'secret' => $appSecret,
  'cookie' => true,
));

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
	insert_datos_user($user_profile);
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
} // validacion para saber si el usuario esta logueado

// la url de Login o Logout dependerá del estado actual del usuario, si está autentificado o no en nuestra aplicación
// Aquí obtenemos los permisos del usuario. Por defecto obtenemos una serie de permisos básicos
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl(array(  
    'scope' => 'email,user_birthday,publish_stream,user_photos,user_online_presence,user_relationships,offline_access','redirect_uri'=> $appUrl  
)); 
}

//redirecciona a loginUrl
if (!$user) {
	echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
    exit;
}

if ($user)	//voy aclarar las variables solo si la sesion esta activa
{
	$userID = $_SESSION['fb_'.$appId. '_user_id'];
	$access_token = $_SESSION['fb_'.$appId. '_access_token'];
}

$json_main = json_decode(
'{"bottons":[	{"name":"Inicio","target":"_top","sk":"home"},
				{"name":"Mi Arbol","target":"_top","sk":"miarbol"},
				{"name":"Invitar Amigos","target":"_top","sk":"invitar"},
				{"name":"Amigos Que ya lo Utilizan","target":"_top","sk":"amigos"},
				{"name":"Donar","target":"_top","sk":"donar"},
				{"name":"Salir","target":"_top","sk":"cancelar"}]}');

$json_publish = json_decode(
'{"bottons":[{"name":"Publicar","target":"_top","sk":"send"}]}');

$page = ( isset( $_GET['sk'] ) && !empty( $_GET['sk'] ) ) ? $_GET['sk'] : "home";



?>