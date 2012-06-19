<div id="main">
	<div id="logueo">
    	<div style="position:relative; font-size:15px; top:180px; font-family: Arial, Helvetica, sans-serif; font-weight:bold; color:#CC0000">
        <form action="<?php echo $appUrl . "/?sk=../login/validar_usuario"?>" method="post">
          <p>Usuario:
          <input type="text" name="usuario" size="20" maxlength="20" />
        </p>
          <p>Password:
  <input type="password" name="password" size="20" maxlength="20" />
            <br />
            <br />
            <input type="submit" value="Ingresar" />
          </p>
        </form>
        </div>
	</div>
</div>