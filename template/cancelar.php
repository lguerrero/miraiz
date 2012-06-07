
<center>
	<div id="main">
		<div class="container"></div> 
		
			<?php if ( $GLOBALS['user']): ?>
				<?php
				unset($_SESSION['fb_'.$GLOBALS['appId'].'_user_id']);
				unset($_SESSION['fb_'.$GLOBALS['appId'].'_access_token']);
				?>
				<script type="text/javascript">window.top.location = "http://www.facebook.com"</script>
			<?php else: ?>
				<script type="text/javascript">window.top.location = '<?php echo $GLOBALS['loginUrl']; ?>';</script>
			<?php endif; ?>
			
	</div><!-- div main -->	
</center>