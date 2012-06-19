<div id="main">
	<div id="logueo">
    	<div style="position:relative; font-size:15px; top:180px; font-family: Arial, Helvetica, sans-serif; font-weight:bold; color:#CC0000">
			<?php session_start();
            // Borramos toda la sesion
            session_destroy();
            echo 'Ha terminado la session <p><a href="index.php">index</a></p>';
            ?>
            <SCRIPT LANGUAGE="javascript">
            location.href = "index.php";
            </SCRIPT>
        </div>
	</div>
</div>