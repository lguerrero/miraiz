<div id="mantenedor">
	<?php
	// Incluimos la clase
	require_once 'Aco_DataGrid.php'; 
	
	// Conectamos a la base de datos
	$c = mysql_connect('...','...','...')or die ('Ha fallado la conexi&oacute;n: '.mysql_error());
    mysql_select_db('...', $c)or die ('Error al seleccionar la Base de Datos: '.mysql_error());
	
	// Consulta
	$sql = 'select * from fb_user';
	
	// Campos seleccionados
	$campos = array('id' => 'id',
					'Nombre' => 'name',
					'Primer Nombre' => 'first_name',
					'Segundo Nombre' => 'middle_name',
					'Apellido' => 'last_name',
					'Url Personal' => 'link',
					'Nombre Usuario' => 'username',
					'Cumplea&ntilde;os' => 'birthday',
					'Genero' => 'gender',
					'Email' => 'email',
					'Localidad' => 'locale');
	
	// Objeto de la clase
	$grid1 = new Aco_DataGrid;
	// Se crea la instancia del grid
	$grid1->iniciar($sql, $c, $campos, '', array( 0, 10, 3 ), '');
	
	// Aqui se configuran algunas cosas de apariencia del grid
	// Hay muchos metodos utiles en la clase
	$colores = array ( '#E5EECC', '#FFFFFF' );
	$grid1->grid_BgColorFC('#FFFFFF', $colores);
	$grid1->grid_PacingAndPadding( 5, 1 );
	
	//contar el total de registros
	$SqlTotalRegistros = mysql_query('SELECT COUNT(*) FROM fb_user'); 
	echo ($SqlTotalRegistros);  
	
	// Mostrarmos el grid
	$grid1->gridMostrar();
	?>
</div>
