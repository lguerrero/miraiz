<?php
/**
 *� Que es un data grid ?
 *
 *Un data grid, que viene a significar en castellano rejilla de datos, es una interfaz de usuario bastante t�pica,** *que sirve para visualizar informaci�n en una tabla. La informaci�n suele ser un conjunto de registros, y se suelen *mostrar cada uno de ellos en una fila. Adem�s, los data grid suelen tener integradas funcionalidades para la       *ordenaci�n de los datos y opciones para su edici�n o borrado entre muchas mas.
 *
 *
 * ******************
 * VERSIONES
 *
 * 1.1 Es completado el dataGrid
 *
 * 1.2
 *
 ***Se agrega...
 *
 *-> Se agrega la posibilidad de ordenar la informacion de forma asc o desc
 *-> Se agrega la posibilidad de crear multiples grids en una misma pagina
 *-> Se agrega la funcion $this->rem_SortAcolumna la cual permite quitarle el ( sort ) a  una columna
 *-> Se agrega la posibilidad de hacer callback a una columna del Grid
 *
 *
 ***Se quita...
 *
 *-> Apartir de la version 1.2 se elimina el MVC "singleton" ya que no era necesario para esta clase
 *-> Se arregla modifica el metodo ( rem_Columna() ), la columna era removida y quedaba fuera de propiedad
 *   arrayCampos.
 *
 * 1.3 <-------------ACTUAL----------------
 *
 * ->Se mejora el mensaje en las llamadas a los callbacks cuando este no se encuentra bien definido...
 * ->El metodo privado _thSort fue modificado
 * ->Ya no es obligatorio definir la conexion hacia la bd, de igual forma se puede definir si se requiere
 *   otra conexion..
 * ->El grid es capaz de crear un ID para la tabla.
 * ->Se corrigieron fallos en el uso de multiples grids en una misma pagina...
 * ->Ahora tiene un paginador, totalmente configurable
 * *******************
 *
 *
 * @package Aco_DataGrid
 * @author sebastian80_23 arroba hotmail.com   -> http://www.forosdelweb.com/miembros/acoevil/
 * @copyright 2009
 * @version 1.3 BETA
 * @access public
 */
class Aco_DataGrid
{

	/**
 	*Sql del usuario
	*@var String Sql del usuario
 	*@access private
 	*/
	private $sql;
	/**
 	*Campos que seran seleccionados de (  $sql  )
	*@var array con los campos que seran seleccionados
 	*@access private
 	*/
	private $campos = array();
	private $classCSS = '';
        private $nombreGrid = '';
	private static $nombreGridAleatorio;
        private $separador = '-{-';
        private $separadorLength = 3;
	/**
 	* Conexion a la db
	*@var Conexion  de la db
 	*@access private
 	*/
	private $conexion;
	/**
 	* Un array asociativo que contiene informacion de los campos de la db
 	*@var array
	*@access private
	*/
	private $arrayCampos = array();

	//	Las sgtes son atributos del grid
	private $cellpadding = 0;
	private $cellspacing = 0;
	private $width = 0;
	private $height = 0;
	private $bgColor = array('#FFFFFF');
	private $bgColorTh = '#FFFFFF';
	private $bgColorTabla = '#FFFFFF';
	private $border = 0;
	private $borderColor = '#FFFFFF';
	private $background = '';
	private $align = 'center';

	/**
 	*Las sgtes controlan las filas agregadas por encima y debajo del grid
 	*/
	// Fila arriba
	private $contenidoF;
	private $alignF;
	private $colspanF;
	// Fila abajo
	private $contenidoF2;
	private $alignF2;
	private $colspanF2;
	
	private $rem_columna = array();
 	private $rem_SortAcolumna = array();


        private $pgUbicacion = 3;
        private $pgCantidad;
        private $pgDespliegue;
        private $pgLang = array();
	
        //Datos que pueden ser utiles.
        // Contiene un array asociativo con valores que puedne ser utiles
        /*
        [filas_todo] Total de resultados
        [filas_por_pagina] Total de resultados por pagina
        [total_paginas] Total de pagina ( cuando se una el paginador )
        */
        public $gridInfo = array();
        /**
 	* Permite indicar los campos que seran procesados de la consulta (  $sql  )
	*
 	* @param String $sql Consulta Sql indicada
 	* @param Recurso de la conexion de la base de datos
 	* @param array Array asociativo que indica el titulo de la fila y el nombre del campo de la base de datos
  	* @param String nombreGrid permite mantener ordenadors los grids, cuando hay mas de uno en una misma pagina
	 */
         
	public function iniciar( $sql, $conexion = '', $campos, $classCSS = '', $pagina = '', $nombreGrid = ''  )
	{
                //Genero el id del grid...
		if( empty( $classCSS ) && empty( $nombreGrid ) ) {
			self::$nombreGridAleatorio += 1;
			$this->nombreGrid = 'grid' . self::$nombreGridAleatorio;
		} else {
			list( $this->nombreGrid ) = split( ' ',  ( empty( $nombreGrid ) ? $classCSS : $nombreGrid ) );
		}
		
                if( ! empty( $pagina ) ) {
                    $limit = $this->_grid_Pagina( $pagina, $sql );
                }
                
                $this->gridInfo['filas_todo'] = $this->_grid_filas( mysql_query($sql) );

                $this->sql = $sql . $limit;
                $this->conexion = $conexion;
		$this->campos = $campos;

                $this->classCSS = $classCSS;

                
                if( is_resource( $this->conexion ) ) {
                    $consulta = mysql_query( $this->sql, $this->conexion )  or die ( mysql_error() );
                } else {
                    $consulta = mysql_query( $this->sql )  or die ( mysql_error() );
                }
                // Obtiene la cantida de resultados por pagina
                $this->gridInfo['filas_por_pagina'] = $this->_grid_filas( $consulta );
		if( $this->gridInfo['filas_todo'] > 0 ) {

		foreach ( $this->campos as $key => $valor ) {

			while ( $fila = mysql_fetch_assoc($consulta) ) {
				$$valor .= $fila[ $valor ] . $this->separador;
			}

			$$valor = substr( $$valor, 0, strlen( $$valor ) - $this->separadorLength );
			$this->arrayCampos[ $key ] =  $$valor;
			$$valor = '';

			@mysql_data_seek($consulta, 0);
		}
                } else {
                    return false;
                }


	}
	/**
 	*
  	*@return array Retorna un array con cada uno de los valores del campo
 	*@param array Un array asociativo que contiene el (  nombre del campo )  y el (  valor  )
 	*/
	private function _explodeC( $array )
	{
		return explode( $this->separador, $array );

	}
	/**
 	* Permite organizar el valor de los arrays colocandole la separacion (  -{-  ) que diferencia cada campo
 	* de la db
 	*
  	*@param array Un array asociativo que contiene el (  nombre del campo )  y el (  valor  )
  	*@param String Un String donde cada valor es separado  (  -{-  )
 	*/
	private function _implodeC( $array )
	{
		return implode( $this->separador, $array );
	}
	/**
 	*Permite explorar un array asociativo pasandole el nombre del indice y devuelve el contenido de ese indice
 	*
 	*/
	private function _explorador( $contenido = '', $camposEscogido = '' )
	{
                if( is_array( $camposEscogido ) ) {
                    foreach( $camposEscogido as $clave => $var ) {
                        $filasSeleccionadas[ $clave ] = $this->_explodeC( $this->arrayCampos[ $clave ] );
                    }
                } else {
                    $filasSeleccionadas[ $camposEscogido ] = $this->_explodeC( $this->arrayCampos[ $camposEscogido ] );
                }
                if( is_array( $camposEscogido ) ) {
                    $elemento = array_keys( $camposEscogido );
                }
                $max = is_array( $camposEscogido ) ?
                count($filasSeleccionadas[ $elemento[ 0 ] ]) :
                count($filasSeleccionadas[ $camposEscogido ]);
                
                if( is_array( $camposEscogido ) ) {
                    for( $n=0; $n < $max; $n++ ) {
                        
                        $cadena = $contenido;
                        foreach( $camposEscogido as $clave => $valor ) {
                            
                            if(  preg_match_all( '/\{'. $valor .'\}/' , $contenido, $m ) ||  preg_match_all( '/\&'. $valor .'\&/' , $contenido, $m ) ) {
                                
                                $cadena = preg_replace('/\{'. $valor .'\}/', $filasSeleccionadas[ $clave ][ $n ] , $cadena);
                                $cadena = preg_replace('/\&'. $valor .'\&/', urlencode($filasSeleccionadas[ $clave ][ $n ]) , $cadena);
                                 } else {
                                continue;                                
                            }
                        }
                        
                        $elementoIndivudual[ $n ] = $cadena;                     
                    }
                    
                } else {                   
                    
                    for( $n=0; $n < $max; $n++ ) {

                        $cadena = preg_replace('/\{\}/', $filasSeleccionadas[ $camposEscogido ][ $n ] , $contenido);
                        $cadena = preg_replace('/\&\&/', urlencode($filasSeleccionadas[ $camposEscogido ][ $n ]) , $cadena);
                        $elementoIndivudual[ $n ] = $cadena;
                    }

                    
                }
             
                    for( $n=0; $n < $max; $n++ ) {

                        $cadenaFinal .= $elementoIndivudual[ $n ] . $this->separador;

                    }

                    return substr( $cadenaFinal , 0, - $this->separadorLength );
	}

	/**
 	*
  	*@return array Retorna un array con la separacion ( -{- )
 	*@param String Q indica que indice del array tiene que utilizar.
 	*/
	 public function add_InfoAcampo( $contenido = '', $camposEscogido = '', $valoresDe = '')
	 {

			$nuevoElemento = $this->_explorador( $contenido, $valoresDe );
			$nuevoElemento = $this->_explodeC( $nuevoElemento );
                        $campoSeleccionado = $this->arrayCampos[ $camposEscogido ];
			$campoSeleccionado = $this->_explodeC( $campoSeleccionado );

			for( $n=0; $n < count( $campoSeleccionado ); $n++ ) {

				$nuevoElemento[ $n ] = preg_replace( '/\$\$/', $campoSeleccionado[ $n ], $nuevoElemento[ $n ] );

			}

			$this->arrayCampos[ $camposEscogido ] =  $this->_implodeC( $nuevoElemento );
	 }
	 /**
	 *Permite añadir una nueva columna antes de una columna especificada
	 *
	 * @param String contenido Indica el tipo de contenido que se quiere agregar
	 * @param String campoEscogido indica el campo desde donde se obtiene el contenido
	 * @param String antesDe indica en donde sera la ubicacion del nuevo contenido en el grid
	 */

	 public function add_ColumnaAntesDe( $contenido, $campoEscogido, $antesDe, $titulo )
	 {

	 		$nuevoElemento = $this->_explorador( $contenido, $campoEscogido );
	 		$nuevoCampo = array();


	 		foreach( $this->arrayCampos as $key => $valor ) {

				if( $antesDe == $key ) {

					$nuevoCampo[ $titulo ] = $nuevoElemento;
					each( $nuevoCampo );
				 }

				$nuevoCampo[ $key ] = $valor;

			}

		$this->arrayCampos = $nuevoCampo;

	 }
	 /**
	 *Permite a�adir una nueva columna despues de una columna especificada
	 *
	 * @param String contenido Indica el tipo de contenido que se quiere agregar
	 * @param String campoEscogido indica el campo desde donde se obtiene el contenido
	 * @param String despuesDe indica en donde sera la ubicacion del nuevo contenido en el grid
	 */

	 public function add_ColumnaDespuesDe( $contenido, $campoEscogido, $despuesDe, $titulo )
	 {
	 		$nuevoElemento = $this->_explorador( $contenido, $campoEscogido );
	 		$nuevoCampo = array();
	 		foreach( $this->arrayCampos as $key => $valor ) {

	 			$nuevoCampo[ $key ] = $valor;

	 			if( $despuesDe == $key ) {

					$nuevoCampo[ $titulo ] = $nuevoElemento;
				}
	 		}
		$this->arrayCampos = $nuevoCampo;

	 }
	 /**
	 *Permite crear una fila por encima del grid, se puede utilizar para darle una descripcion al grid
	 * @param String contenido, indica el contenido que se visualizara en la nueva fila
	 * @param String align, indica la posicion del contenido, ( por defecto se ubica en el centro )
	 * @param String colspan, indica la cantidad de columnas que abarcara la nueva fila, ( por defecto son todas )
	 */

	 public function add_FilaArriba( $contenidoF = '', $alignF = 'center', $colspanF = null )
	 {

	 	// Si add_FilaArriba es llamada y el parametro $contenidoF se pasa vacio por defecto se colocara el
	 	// nombre del Grid (  $this->classCSS  )
	 	$this->contenidoF = (  $contenidoF == '' ? $this->nombreGrid : $contenidoF  );

		$this->alignF = $alignF;

		 $this->colspanF = ( $colspanF == null ? count( $this->arrayCampos ) : $colspanF );

		return $this;
	 }
	 public function add_FilaAbajo( $contenidoF = '', $alignF = 'center', $colspanF = null )
	 {

	 	// Si add_FilaAbajo es llamada y el parametro $contenidoF se pasa vacio por defecto se colocara el
	 	// nombre del Grid (  $this->classCSS  )
	 	$this->contenidoF2 = (  $contenidoF == '' ? $this->nombreGrid : $contenidoF  );

		$this->alignF2 = $alignF;

		 $this->colspanF2 = ( $colspanF == null ? count( $this->arrayCampos ) : $colspanF );

		return $this;
	 }	 

	/**
	 * Permite aplicar una funcion externa a una columna especificada, NOTA cada valor de la fila se vera
	 * afectado, el parametro $funcion tambien resive un metodo de un objeto indicandolo en un array de la
	 * siguiente forma
	 *
	 * 		add_FuncionA( $columna, array('clase', 'metodo') ){...}
	 *
	 * 		El metodo pasadro debe ser (  stactic  )
	 *
	 *
	 * @param String $columna es la columna a la cual se le aplicara la funcion.
	 * @param String $funcion es la funcion que se utilizara.
	 */
	 public function add_FuncionA( $columna, $funcion )
	 {

		if( is_callable( array( $funcion[ 0 ], $funcion[ 1 ] ) ) || function_exists( $funcion )) {

                        if( ! is_array( $columna ) ) {
			$valores = $this->_explodeC( $this->arrayCampos[ $columna ] );
                        $nuevos = array_map( $funcion, $valores );

			$this->arrayCampos[ $columna ] = $this->_implodeC( $nuevos );
                        } else {
                            for( $n=0; $n < count( $columna ); $n++ ) {
                                $valores = $this->_explodeC( $this->arrayCampos[ $columna[ $n ] ] );
                                $nuevos[ $n ] = array_map( $funcion, $valores );
                                $this->arrayCampos[ $columna[ $n ] ] = $this->_implodeC( $nuevos[ $n ] );
                            }
                        }


		} else {
                        $nombre = (  is_array( $funcion ) ? $funcion[ 1 ] : $funcion );
			echo "La funcion : ". $nombre . " No se encuentra definida.";

		}
	 }
	/**
	* array( 'Columna1' => array( 'valorColumna1' => 'valorColumna1Final' ),
	*	 'Columna2' => array( 'valorColumna1' => 'valorColumna1Final' ), 
	*      )
	*/
	 public function add_Relacion( $relaciones = array() )
	 {
	   
	   $columnas = array_keys( $relaciones );
	   for( $n=0; $n < count( $columnas ); $n++ ) {
	    // Obtengo informacion de las columna
	     $valores = $this->_explodeC( $this->arrayCampos[ $columnas[ $n ] ] );
	       unset( $valores_nuevos );
	       // Entro a cada valor devuelto por la columna
	       for( $v=0; $v < count( $valores ); $v++ ){
			$compara = false;
			// Obtengo la clave de cada relacion
			$claves = array_keys( $relaciones[ $columnas[ $n ] ] );
			for( $k=0; $k < count( $claves ); $k++ ) {
				if( $claves[ $k ] == $valores[ $v ] ){
			          $valores_nuevos[] = $relaciones[ $columnas[ $n ] ][ $claves[ $k ] ];
				  $compara = true;
				} 
			}
			if( ! $compara ) {
				$valores_nuevos[] = $valores[ $v ];
			}
			
	      }
	      $this->arrayCampos[ $columnas[ $n ] ] = $this->_implodeC( $valores_nuevos );
	    }
	 }

         public function add_SortAColumna( $valores = array() )
         {
             for( $n=0; $n < count( $valores ); $n++ ) {
                 $this->campos[ $valores[ $n ] ] = $valores[ $n ];
             }
         }
	 /**
	 *Permite remover columnas que no se deseen mostrar en el grid
	 *
	 *@param array Un array que indica las columnas que no se deben mostrar, se utiliza el mismo titulo de la
	 *       columna para especificar las que no se quieren ver  Eje:  array( 'titulo', 'titulo2' );
	 */
	 public function rem_Columna( $columna = array() )
	 {

	 	return $this->rem_columna = $columna;

	 }
	 private function _rem_ColumnaOculta()
	 {

		// Almacenara las nuevas columnas disponibles
		$nuevoElemento = array();
		foreach( $this->arrayCampos as $key => $valor ) {

			$si = false;

			for( $n=0; $n < count( $this->rem_columna ); $n++ ) {

				if( $key == $this->rem_columna[ $n ] ) {

					$si = true;

				}
			}

			if( ! $si ) {

				$nuevoElemento[ $key ] = $valor;
			}


		}

		return $nuevoElemento;

	 }
	 /**
	 * Permite remover el ( sort )   de una o varios columnas
	 *
	 * @param array Un array (  $valores  ) que indica las columnas a las cuales se les removera el ( sort )
	 */
	 public function rem_SortAcolumna( $valores = array() )
	 {
	 	return $this->rem_SortAcolumna = $valores;
	 }


	 /**
	 * Provee del link para el ordenamiento de la informacion en el grid, como consulta asc o desc
	 */
	 private function _thOrdenamiento( $key )
	 {

		// Obtenemos la URL
	 	$url = $_SERVER['REQUEST_URI'];
                /*
	 	*Patron en la posicion [ 0 ] se valida  de la siguiente forma ya que la url
		*puede estar de distintas formas por ejemplo:
		*
		*index.php?colum=campo&orden=asc <- Donde colum al principio no tiene el ampersan (  &  )
		*
		*index.php?otra=otra&colum=campo&orden=asc  <- Donde colum al principio  tiene el ampersan (  &  )
		*
		*/
		//$patron[ 0 ] = '/\?colum=[\w]+/';
		$patron[ 0 ] =  preg_match( '/&colum=[\w]+/', $url ) ? '/&colum=[\w]+/' : '/&colum=[\w]+/' ;
		$patron[ 1 ] = '/&orden=[\w]+/';
		$patron[ 2 ] = '/&sort=[\w]+/';

                // La URL se encuentra limpia
		$url = preg_replace( $patron, '', $url );

		// index.php <- Comprobar la extencion, si es .php le agrego ? de lo contrario solo agrego el &
		//$url = ( substr( $url, -4, strlen( $url )) == '.php'  ?  $url.'?'  :  $url.'&amp;' );
                $url = strrchr( $url, '?' ) ? $url . '&amp;' : $url . '?' ;

		$orden = ( isset( $_GET['orden'] ) ? $_GET['orden'] : 'asc' );
		$orden = (  $orden == 'asc' ? 'desc' : 'asc'  );

		//Valido que los enlaces solo se agreguen a $key iguales a los que tiene  ( $this->campos  );
		// Y que no esten removidos por medio de la funcion $this->rem_SortAcolumna
		if( in_array( $key, array_keys( $this->campos ) ) && ! in_array( $key, $this->rem_SortAcolumna ) ) {

                $url = '<a href="' . $url . 'colum=' . $this->campos[ $key ] .
		 '&amp;orden=' . $orden . '&amp;sort=' . $this->nombreGrid . '">' . $key . '</a>';

		} else { //De lo contrario la URL es devuelta sin cambio

		$url = $key;

		}
		return $url;
	 }


	/**
	 * Realize el ordenamiento del grid dependiendo de la columna seleccionada, y ubica los valores
	 * en sus nuevas celdas
	 */

  	private function _thSort()
	{
                if( $_GET['sort'] != $this->nombreGrid ) return false;

	 	$columna = $_GET['colum'];
	 	$orden = $_GET['orden'];

                if( isset( $columna ) && isset( $orden ) && $orden == 'asc' || $orden == 'desc' ) {

	 		 $campos = $this->arrayCampos;
                         // Key mantiene la clase donde se encuentra la columna...
			 $key = array_keys( $this->campos, $columna );
			 $key = $key[ 0 ];

			 $escogido = $this->_explodeC( $this->arrayCampos[ $key ] );
			 $tipOrden = ( $orden == 'asc' ? 'arsort' : 'asort' );

			 $tipOrden( $escogido );

			 $keys = array_keys( $escogido );

			 for( $n=0; $n < count( $keys ); $n++ ) {

                                $posicion = $keys[ $n ];

			 	foreach( $this->arrayCampos as $clave => $valor ) {

				 if( $key == $clave ) {  continue;  }

                                 $arrayValores = $this->_explodeC( $valor );

                                 $posicionadoEn = $arrayValores[ $posicion ];

                                 $valores[ $clave ] .= $posicionadoEn . $this->separador;

                                }

			 }
                         // Encontramos los ultimos separadores... y lo eliminamos...
                         foreach( $valores as $vari => $valo ) {
                            if( $vari == $key ) { continue; }
                            $valores[ $vari ] = substr( $valo, 0, strlen( $valo )- $this->separadorLength );
                         }
			 foreach( $this->arrayCampos as $cl => $vl ) {

			 	if( $cl != $key ) {

			 		$this->arrayCampos[ $cl ] = $valores[ $cl ];
			 	}
			 }
			 $this->arrayCampos[ $key ] = $this->_implodeC( $escogido );
			 }

	 }
         /**
          * Permite realizar paginacion de resultados
          *
          *@param $ubicacion 0 -> abajo ( defecto )  | 1 -> arriba  | 2 -> ambos
          *@param $nroMaximo El numero maximo de elementos por pagina
          *@param $despliegue Indicara el despliegue de los enlaces
          * por ejemplo se colocan 3 el resultado seria  1|2|3|->4<-|5|6|7  -> 3 enlaces apartir del cuatro.
          */
                 /**
         * @param  consulta
         * @return int numero de filas devueltas por la consulta.
         */
        public function _grid_filas( $consulta )
        {
            $consulta = mysql_num_rows( $consulta );
            return $consulta;
        }
         /**
          *
          * @param <int> $ubicacion La ubicacion del paginador, puede ser arriba, abajo o ambos...
          * @param <int> $nroMaximo El numero maximo de resultados por pagina
          * @param <int> $despliegue La cantidad de links que salen -> Pagina 1 de 5  1 | 2 | 3 | 4 | 5 |
          * @param <String> $mensaje Indica el idioma con el que saldra -> Pagina 1 de n o anterior y siguiente
          */
	 public function _grid_Pagina( $args, $sql )
         {
	    
            list( $ubicacion, $nroMaximo, $despliegue, $mensaje ) = $args;
	    $pg = $_GET['pg'];
            if( ! isset( $pg ) || empty( $pg ) || $pg < 0 || $this->nombreGrid != $_GET['pagina']) {
                $pg = 1;
             }
	    $this->pgUbicacion = $ubicacion;
            $this->pgDespliegue = $despliegue;
            
            // Los mensaje que se mostraran en el paginador...
            if( empty ( $mensaje ) ) {
                $this->pgLang['pg']['Pagina'] = 'Pagina ';
                $this->pgLang['pg']['De'] = ' de ';
                $this->pgLang['pg']['Anterior'] = ' Anterior ';
                $this->pgLang['pg']['Siguiente'] = ' Siguiente ';
            } else {
                $this->pgLang = $mensaje;
            }

            // Se obtiene la cantidad de elementos totales
            $totalRegistros = mysql_num_rows ( mysql_query( $sql ) );
            
            $this->pgCantidad = ceil( $totalRegistros / $nroMaximo );
            $this->gridInfo['total_paginas'] = $this->pgCantidad;

            // Si la pagina pg es mayor a la cantidad total entonces pg es igual a 1
            $pg = $pg > $this->pgCantidad ? 1 : $pg;

            $inicio = ($pg * $nroMaximo) - $nroMaximo;

            $limit = ' LIMIT ' . $inicio . ',' . $nroMaximo;
	    
	    return $limit;
	    
         }
         private function _grid_PaginaNumeros()
         {
             
            $pg = $_GET['pg'];
            if( ! isset( $pg ) || empty( $pg ) || $pg < 0 || $pg > $this->pgCantidad ) {
                $pg = 1;
            }
            $inicio = $pg - $this->pgDespliegue;
            if( $inicio <= 1 ) {

                // Si la resta de $inicio ^ da menor o igual a 1 el while se encargara de ajustarlo...
                //Ejemplo -> $inicio = -4 el while lo dejara en 2...
                while( $inicio <= 1 ) {
                   $inicio += 1;

                }

            }
            $fin = $pg + $this->pgDespliegue;

            if( $fin > $this->pgCantidad ) {

                while( $fin > $this->pgCantidad ) {
                    $fin -= 1;

                }

            }

            $url = $_SERVER['REQUEST_URI'];
	    // Se limpia el pagina
	    $url = preg_replace('/&pagina=[\w]+/', '' ,$url);
            // Se limpian las URLs
            if( preg_match('/\?pg=[\w]+&/', $url ) ) {

                $url = preg_replace('/pg=[\w]+&/', '', $url );

            } elseif( preg_match('/\?pg=[\w]+/', $url) ) {

                $url = preg_replace('/\?pg=[\w]+/', '', $url );

            } elseif( preg_match('/&pg=[\w]+/', $url ) ) {

                $url = preg_replace('/\&pg=[\w]+/', '', $url );
            }
	    $url = strrchr( $url, '?' ) ? $url . '&amp;pg=' : $url . '?pg=' ;
            // Pagina n de n
	    $actual = $this->nombreGrid == $_GET['pagina'] ? $pg : 1;
            echo $this->pgLang['pg']['Pagina'] . $actual . $this->pgLang['pg']['De'] . $this->pgCantidad ;

            if( $pg != 1 ) {
                echo "<a href=". $url . ($pg - 1) . '&pagina='. $this->nombreGrid .">". $this->pgLang['pg']['Anterior'] ."</a>";

            }
            if( $pg == 1 && $this->nombreGrid == $_GET['pagina']  ) {
                $resaltaInicio = '<strong>';
                $resaltaFinal = '</strong>';
            }

            echo  $resaltaInicio . "  <a href=" . $url . 1 .'&pagina='. $this->nombreGrid.">1</a> " . $resaltaFinal;

            for( $n=$inicio; $n <= $fin; $n++ ) {
                if( $pg == $n && $this->nombreGrid == $_GET['pagina'] ) {
                    $resaltaInicio = '<strong>';
                    $resaltaFinal = '</strong>';
                } else {
                    $resaltaInicio = '';
                    $resaltaFinal = '';
                }
                echo $resaltaInicio . "<a href=". $url . $n .'&pagina='. $this->nombreGrid . "> $n </a>" . $resaltaFinal;

            }
            if( $pg != $this->pgCantidad ) {
            echo "<a href=". $url . ($pg + 1) .'&pagina='. $this->nombreGrid . ">". $this->pgLang['pg']['Siguiente'] ."</a>";
            }
         }
	 /**
	 * Permite indicar el cellspacing y cellpadding
	 *@param cellspacing
	 *@param cellpadding
	 */
	 public function grid_PacingAndPadding( $cellspacing, $cellpadding )
	 {
	 	$this->cellspacing = $cellspacing;
	 	$this->cellpadding = $cellpadding;
		return $this;
	 }
	 /**
	 * Indica los colores de filas y columnas, ademas se puede indicar la manera como seran aplicados
	 *
	 * @param String Indica el primer color que tendra el titulo de cada columna
	 * @param array Un array de colores  hexadecimales
	 * @param true o false
	 */
	 public function grid_BgColorFC( $colorTagTh, $colores )
	 {
		$this->bgColor = $colores;
		$this->bgColorTh = $colorTagTh;
		return $this;
	 }
	 /**
	 *Define tanto la anchura como altura del grid
	 * @param int width - Define la anchura del grid en pixels o porcentaje.
	 * @param int height - Define la altura del grid en pixels o porcentaje.
	 */
	 public function grid_WidthAndHeight( $width, $height )
	 {
		$this->width = $width;
		$this->height = $height;
		return $this;
	 }
	 /**
	 * Define el border, borderColor, background y align del grid
	 * @rapam int border Define el n�mero de pixels del borde principal.
	 * @param String borderColor Define el color del borde.
	 * @param String bgcolor da color de fondo a la tabla.
	 * @param String background Nos permite colocar un fondo para la tabla a partir de un enlace a una imagen.
	 * @param String align Alinea horizontalmente la tabla con respecto a su entorno.
	 */
	 public function grid_AtributosTabla( $border, $borderColor, $bgColor, $background, $align )
	 {
	 	$this->border = $border;
	 	$this->borderColor = $borderColor;
	 	$this->bgColorTabla = $bgColor;
	 	$this->background = $background;
	 	$this->align = $align;
	 	return $this;
	 }

	 public function gridMostrar()
	 {
                $this->_thSort();
                $valoresCampos = ( empty( $this->rem_columna ) ? $this->arrayCampos : $this->_rem_ColumnaOculta() );
		
	 	$datos = array();

	 	 echo '
                 <table align="' . $this->align . '"
                 border="' . $this->border . '"
		 bordercolor="' . $this->borderColor . '"
                 background="' . $this->background . '"
		 cellspacing="' . $this->cellspacing . '"
                 cellpadding="' . $this->cellpadding . '"
		 bgcolor="' . $this->bgColorTabla . '"
                 class="'. $this->classCSS . '"
		 height="'. $this->height . '"
		 width="'. $this->width .'">';
		
		
                 ob_start();
                 $this->_grid_PaginaNumeros();
                 $salida = ob_get_clean();
		 // Con lo sgte se genera la nueva fila ( si el usuario lo indica )
                 if( $this->pgUbicacion == 1 || $this->pgUbicacion == 2 ) {
                        echo '<tr><td colspan="' . count( $this->arrayCampos ) . '" align="center">'. $salida .'</td></tr>';
                 }
		 if ( ! empty( $this->contenidoF ) ) {
			echo '<tr><td colspan="' . $this->colspanF . '" align="' . $this->alignF . '">'. $this->contenidoF .'</td></tr>';
		}
                echo '<tr bgcolor="'. $this->bgColorTh .'">';
		// Se generan los <th>
		foreach ( $valoresCampos as $key => $valor ) {
			echo '<th align="center">' .
			$this->_thOrdenamiento( $key ) . '</th>';
			// _thOrdenamiento es agregada en la version 1.2
			$datos[ $key ] = $this->_explodeC( $valoresCampos[ $key ] );
			$iteraciones = count( $datos[ $key ] );
		}
		echo '</tr>';
		$v = 0;
		$tam = count( $this->bgColor );
		// Se generan los td con los resultados de la Bds
		for ( $n=0; $n < $iteraciones; $n++ ) {
		 	//Si ( v ) es igual al tama�o del array de colores
			//lo reinicio en 0 de lo contrario sigue con el mismo valor
			$v = ( $v == $tam ? 0 : $v );
			echo '<tr bgcolor="' . $this->bgColor[ $v ] . '">';
			foreach ( $valoresCampos as $key => $valor ) {
			echo '<td align="center">' . $datos[ $key ][ $n ] . '</td>';
			}
			echo '</tr>';
			$v++;
		}
		 if ( ! empty( $this->contenidoF2 ) ) {
			echo '<tr><td colspan="' . $this->colspanF2 . '" align="' . $this->alignF2 . '">'. $this->contenidoF2 .'</td></tr>';
		}		
                if( $this->pgUbicacion == 0 || $this->pgUbicacion == 2 ) {
                        echo '<tr><td colspan="' . count( $this->arrayCampos ) . '" align="center">'. $salida .'</td></tr>';
                 }
		echo '</table>';
	 }

}

// Uso basico de la Clase Aco_dataGrid

// 1-> Realizamos un require_once donde se encuentra la clase

	// require_once 'dataGrid.php';

// 2-> Abrimos la conexion a la Bds y la guardamos en una variable, si ya tenemos uno utilizamos le existente


    //  $conexion = mysql_connect("localhost", "root","");
    //-----^


// 3->  Realizamos la consulta SQL y la guardamos en una variable...

	// $sql = "select * from usuarios";


// 4-> Creamos un Array asociativo donde el (  indice  ) indica el nombre de la columna y el (  valor  ) el
//	   nombre del campo en la base de datos

	// $campos = array(  'nombre_Columna1' => 'nombre del campo', ....  );


// 5-> Creamos un instancia de la clase y le pasamos los sgte parametros

	//  $grid = new Aco_DataGrid( $sql, $conexion, $campos, 'Nombre del Grid' );

	//  SQL que indicamos arriba---^
	//  $conexion de nuestra Bds------------^
	//  $campos de nuestra Bds------------------------^
    //  El ultimo es el nombre del grid------------------------------^

// 6-> Por ultimo llamamos al metodo gridMostrar(); para mirar el resultado final

	//  $grid->gridMostrar();


//NOTA: La clase implementa variedad de metodos que pueden ser usados para modificar los resultados que de
//      despliegan en el Grid, como agregar columnas, remover, eliminar, o usar callbacks





