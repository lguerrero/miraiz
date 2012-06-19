
<div id="main">
	<div id="logueo">
    		<div style="position:relative; font-size:30px; top:140px; font-family: 'Rosewood Std Regular'; color:#CC0000">
			<?php 
			if($GLOBALS['userID'] == $GLOBALS['uidsergio'] or $GLOBALS['userID'] == $GLOBALS['uidchristian'] or $GLOBALS['userID'] == $GLOBALS['uidluis']){
			session_start();
            $fotoperfil = "<img src='http://graph.facebook.com/".$GLOBALS['userID']."/picture?type=large'/>"; //guarda la imagen en una  variable
			echo $fotoperfil; 
            if (isset($_SESSION['k_username'])) {
				echo '<br>Usuario: '.$_SESSION['k_username'].'<br>';
               
                ?><div style="margin:5px;"><a href="<?php echo $appUrl . "/?sk=../login/logout" ?>">Logout</a></div>
				  <div style="margin:10px;"><a href="<?php echo $appUrl . "/?sk=../login/mantenedor" ?>">Mantenedor</a></div><?php
            }
            else{
                ?>
                <div class="tab">  
                    <?php foreach( $GLOBALS['json_login']->bottons as $botton ) : ?>
                        <?php $tabClass = ( $GLOBALS['page'] == $botton->sk ) ? "tab-selected" : "tab-normal"; ?>
                            <a  style="border-bottom: thin;" class="<?php echo $tabClass; ?>" href="<?php echo $GLOBALS['appUrl'] . "?sk=../login/" . $botton->sk; ?>" target="<?php echo $botton->target; ?>"><?php echo $botton->name; ?></a>
                    <?php endforeach; ?>	
                </div><!-- div tab -->
                <?php 
			}
			}
			else{ ?>
				<SCRIPT LANGUAGE="javascript">
            location.href = "index.php";
            </SCRIPT>
				<?php }?>
			
			
    	</div><!-- div style -->
	</div><!-- div logueo -->        
</div><!-- div main -->