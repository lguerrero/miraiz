<?php
/**
 * name		MySQL Class
 * package	McNally Developers
 * date		2010/08/05 21:41
 * require	None
 * version	1.0
 */

define( "OBJECT", "OBJECT", true );
define( "ARRAY_A","ARRAY_A", true );
define( "ARRAY_N","ARRAY_N", true );

class MySQL
	{
	private $show_errors = true;
	public $num_queries = 0;	
	public $last_query;
	public $insert_id;
	
	/**
 	 * descrip	Obtiene una conexion con el servidor de base de datos
 	 * date		2010/08/05 21:55
 	 * from		1.0
 	*/
	function MySQL ( $dbhost, $dbuser, $dbpassword, $dbname )
		{
		$this->dbh = @mysql_connect( $dbhost, $dbuser, $dbpassword );
		if ( ! $this->dbh )
			{
			$this->print_error( "Error al intentar establecer la conexion con el servidor de base de datos" );
			}
		$this->select( $dbname );
		}
	
	/**
 	 * descrip	Establece una base de datos
 	 * date		2010/08/05 21:55
 	 * from		1.0
 	*/
	public function select( $db )
		{
		if ( !@mysql_select_db( $db, $this->dbh ) )
			{
			$this->print_error( "Error al intentar usar la base de datos" );
			}
		}
	
	/**
 	 * descrip	Escapa los caracteres ' para prevenir sql inject
 	 * date		2010/08/05 22:55
 	 * from		1.0
 	*/
	public function escape( $str )
		{
		if( get_magic_quotes_gpc() )
			{
    		$str = mysql_real_escape_string( stripslashes( $str) );
			}
			else
				{
				$str = mysql_real_escape_string( $str );
				}
		return $str;				
		}
	
	/**
 	 * descrip	Convierte caracteres a utf-8
 	 * date		2010/08/05 22:28
 	 * from		1.0
 	*/
	public function html( $str )
		{
		return htmlentities( $str, ENT_QUOTES, "utf-8");		
		}
	
	public function set_keys( $array )
		{
		if( is_array( $array ) )
			{
			if( count( $array ) > 0 )
				{
				$keys	= array_keys( $array );
				return implode( ",", $keys );
				}
			}
		}
	
	public function set_values( $array )
		{
		$values	= array_values( $array );
		$values_temp = array();
		foreach( $values as $value )
			{
			array_push( $values_temp, "'" . $value . "'" );
			}
		return implode( ",", $values_temp );
		}
	/**
 	 * descrip	retorna valores user
 	 * date		2010/08/05 22:34
 	 * from		1.0
 	*/
	public function set_values_user( $array )
		{
		$values_user	= $array;
		$values_temp_user = array();
		foreach( $values_user as $key => $temp )
			{
				if($key == "id"){$values_temp_user[] = $temp;}
				if($key == "name"){$values_temp_user[] = "'".$temp."'";}
				if($key == "first_name"){$values_temp_user[] = "'".$temp."'";}
				if($key == "middle_name"){$values_temp_user[] = "'".$temp."'";}
				if($key == "last_name"){$values_temp_user[] = "'".$temp."'";}
				if($key == "link"){$values_temp_user[] = "'".$temp."'";}
				if($key == "username"){$values_temp_user[] = "'".$temp."'";}
				if($key == "birthday"){$values_temp_user[] = "'".$temp."'";}
				if($key == "gender"){$values_temp_user[] = "'".$temp."'";}
				if($key == "email"){$values_temp_user[] = "'".$temp."'";}
				if($key == "locale"){$values_temp_user[] = "'".$temp."'";}
			}
			//print_r($values_temp_user); //imprime el arreglo
		return implode( ",", $values_temp_user);
		}
	
	public function set_keys_user( $array )
		{
		if( is_array( $array ) )
			{
			if( count( $array ) > 0 )
				{
				if($value == "id"){
				$keys	= array_keys( $array );}
				if($value == "name"){
				$keys	= array_keys( $array );}
				if($value == "first_name"){
				$keys	= array_keys( $array );}
				if($value == "middle_name"){
				$keys	= array_keys( $array );}
				if($value == "last_name"){
				$keys	= array_keys( $array );}
				if($value == "link"){
				$keys	= array_keys( $array );}
				if($value == "username"){
				$keys	= array_keys( $array );}
				if($value == "gender"){
				$keys	= array_keys( $array );}
				if($value == "email"){
				$keys	= array_keys( $array );}
				if($value == "locale"){
				$keys	= array_keys( $array );}
			
				return implode( ",", $keys );
				}
			}
		}
	/**
 	 * descrip	Imprime los errores de MySQL
 	 * date		2010/08/05 22:34
 	 * from		1.0
 	*/
	private function print_error($str = "")
		{
		global $MYSQL_ERROR;
		if ( !$str )
			{
			$str = mysql_error( $this->dbh );
			$error_no = mysql_errno( $this->dbh );
			}
		$MYSQL_ERROR[] = array( "query" => $this->last_query, "error_str" => $str, "error_no" => $error_no );
		if ( $this->show_errors )
			{
			echo $str;
			}
			else
				{
				return false;	
				}
		}
	
	/**
 	 * descrip	Mata el resultado en cache de las query
 	 * date		2010/08/05 22:39
 	 * from		1.0
 	*/
	private function cflush()
		{
		$this->last_result = NULL;
		$this->last_query = NULL;
		}
	
	/**
 	 * descrip	Ejecuta una consulta a la base de datos
 	 * date		2010/08/05 22:39
 	 * from		1.0
 	*/
	public function query($query)
		{
		$query = trim($query); 
		$return_val = 0;
		$this->cflush();
		$this->func_call = "query(\"$query\")";
		$this->last_query = $query;
		$this->result = @mysql_query( $query, $this->dbh );
		$this->num_queries++;
		if ( mysql_error() )
			{
			$this->print_error();
			return false;
			}
		if ( preg_match( "/^(insert|delete|update|replace)\s+/i", $query ) )
			{
			$this->rows_affected = mysql_affected_rows();
			if ( preg_match( "/^(insert|replace)\s+/i", $query ) )
				{
				$this->insert_id = mysql_insert_id( $this->dbh );	
				}
			$return_val = $this->rows_affected;
			}
			else
				{
				$num_rows=0;
				while ( $row = @mysql_fetch_object( $this->result ) )
					{
					$this->last_result[$num_rows] = $row;
					$num_rows++;
					}
				@mysql_free_result( $this->result );
				$this->num_rows = $num_rows;
				$return_val = $this->num_rows;
				}
		return $return_val;
		}
	
	/**
 	 * descrip	Retorna un valor de query, solo obtiene el valor solicitado
 	 * date		2010/08/05 22:39
 	 * from		1.0
 	*/
	public function value( $query=NULL, $x=0, $y=0 )
		{
		$this->func_call = "get_var(\"$query\",$x,$y)";
		if ( $query )
			{
			$this->query( $query );
			}
		if ( $this->last_result[$y] )
			{
			$values = array_values( get_object_vars( $this->last_result[$y] ) );
			}
		return ( isset( $values[$x] ) && $values[$x]!=='' ) ? $values[$x]:NULL;
		}
	
	/**
 	 * descrip	Retorna los valores solicitados de una fila
 	 * date		2010/08/05 22:39
 	 * from		1.0
 	*/
	public function row( $query=NULL, $output=OBJECT, $y=0 )
		{
		$this->func_call = "row(\"$query\",$output,$y)";
		if ( $query )
			{
			$this->query( $query );
			}
		if( $output == OBJECT )
			{
			return $this->last_result[$y] ? $this->last_result[$y]:NULL;
			}
			elseif ( $output == ARRAY_A )
				{
				return $this->last_result[$y] ? get_object_vars( $this->last_result[$y] ):NULL;
				}
				elseif ( $output == ARRAY_N )
					{
				return $this->last_result[$y] ? array_values( get_object_vars( $this->last_result[$y] ) ):NULL;
				}
				else
					{
					$this->print_error( "get_row(string query, output type, int offset) OBJECT, ARRAY_A, ARRAY_N" );
					}
		}
	
	/**
 	 * descrip	Retorna un objeto/arregle multidimencional de los resultados obtenidos
 	 * date		2010/08/05 23:13
 	 * from		1.0
 	*/
	public function results( $query=null, $output = OBJECT )
		{
		$this->func_call = "results(\"$query\", $output)";
		if ( $query )
			{
			$this->query( $query );
			}
		if ( $output == OBJECT )
			{
			return $this->last_result;
			}
			elseif ( $output == ARRAY_A || $output == ARRAY_N )
				{
				if ( $this->last_result )
					{
					$i=0;
					foreach( $this->last_result as $row )
						{
						$new_array[$i] = get_object_vars($row);
						if ( $output == ARRAY_N )
							{
							$new_array[$i] = array_values( $new_array[$i] );
							}
						$i++;
						}
					return $new_array;
					}
					else
						{
						return NULL;
						}
				}
		}

	}
$mysql = new MySQL( $db_host, $db_user, $db_pass, $db_name );
?>