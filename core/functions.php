<?php
function get_template( $file )
	{
	$patch = $GLOBALS['f_template'] . $file . $GLOBALS['extension_file'];
	if( file_exists( $patch ) )//si el archivo existe, por la direccion o patch
		{
		require $patch; //entonces lo incluimos
		}
		else
			{
			die("El archivo de plantilla " . $file . " no existe."); 
			}
	}

function is_logged() //funcion que compara si la session existe
	{
	if( isset( $_SESSION['fb_'.$GLOBALS['appId'].'_user_id']) && isset( $_SESSION['fb_'.$GLOBALS['appId'].'_access_token'])) 
		{
		return true;
		}
	return false;
	}
	
function save_sesion( $array )
	{
	$GLOBALS['mysql']->query( "INSERT INTO ".$GLOBALS['table_fb_sessions']." (".$GLOBALS['mysql']->set_keys( $array ).") VALUES(".$GLOBALS['mysql']->set_values( $array ).") ON DUPLICATE KEY UPDATE last_login=now()" );
	}

function insert_sesion($array) //funcion de prueba
	{
	$GLOBALS['mysql']->query( "INSERT INTO ".$GLOBALS['table_fb_sessions']." (".$GLOBALS['mysql']->set_keys( $array ).") 
	VALUES('12345', '1223wr44r4rer', '11', 'sig11111111') ON DUPLICATE KEY UPDATE last_login=now()" );
	}

function insert_datos_user($array) //guarda datos de usuarios manual
	{
	$GLOBALS['mysql']->query( "INSERT INTO ".$GLOBALS['table_fb_user']." (id,name,first_name,middle_name,last_name,link,username,birthday,gender,email,locale) 
	VALUES(".$GLOBALS['mysql']->set_values_user( $array ).")ON DUPLICATE KEY UPDATE email=email" ); 
	}

function insert_user($array) //guarda datos de usuarios automatic
	{
	$GLOBALS['mysql']->query( "INSERT INTO ".$GLOBALS['table_fb_user']." 
	(".$GLOBALS['mysql']->set_keys_user( $array ).")
	VALUES(".$GLOBALS['mysql']->set_values_user( $array ).")ON DUPLICATE KEY UPDATE email=email" ); 
	}
	
function get_list_users()
	{
	$results = $GLOBALS['mysql']->results( "SELECT uid FROM ".$GLOBALS['table_fb_sessions'] );
	if( $GLOBALS['mysql']->num_rows )
		{
		return $results;
		}
	return array();
	}
	
function delete_user( $uid, $sig )
	{
	$q = 
	$GLOBALS['mysql']->query( "DELETE FROM " . $GLOBALS['table_fb_sessions'] ." WHERE uid = ".$GLOBALS['mysql']->escape( $uid ) ." AND sig = '" . $GLOBALS['mysql']->escape( $sig ) . "'" );
	if( $GLOBALS['mysql']->rows_affected > 0 )
		{
		return true;
		}
		else
			{
			return false;
			}
	}
?>