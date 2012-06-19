
	<div id="main">
		<div id="miarbol">
			<br>
			<strong>Elegir Padre:</strong>
			<?php
				ListBoxFriend();
			?>
			<strong>Elegir Madre:</strong>
			<?php
				ListBoxFriend();
			?> 
			<!--	
			<input type="text" name="num">
			<input type="button" value="Agregar" name="btnAgre" onClick="recargar(num);"/><br>
			<div id = "recargado"><img id="loader_gif" src="images/loader.gif" style="display:none;"/></div>
			-->
			
			<div id="stylized" class="myform">    
			<form id="myForm" action="../hermanos.php" method="post"> 
            <input type="submit" value="Agregar hermano" name="btnAgregar"/><br>
			 <div id = "actual"><img id="loader_gif" src="images/loader.gif" style="display:none;"/></div>
			</form>	
			</div>
            
			
        </div> 
	</div><!-- div main -->	
