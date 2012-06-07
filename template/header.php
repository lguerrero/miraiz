<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <title>Mi Raiz (Arbol Genealogico)</title>
	<link href="style.css" rel="stylesheet" type="text/css">
	<link href="item.css" rel="stylesheet" type="text/css">
	<link href="template/item.css" rel="stylesheet" />
    <link href="template/style.css" rel="stylesheet" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
	<script type="text/javascript">
		var flashvars = {};
		flashvars.folderPath = "http://www.pruebas.visualdiagnostic.cl/coverflow/";
		var params = {};
		params.scale = "noscale";
		params.salign = "tl";
		params.wmode = "transparent";
		params.allowScriptAccess = "always";
		params.allowFullScreen = "true";
		var attributes = {};
		swfobject.embedSWF("http://www.pruebas.visualdiagnostic.cl/coverflow/CoverFlowFX.swf", "DivFlashComponent", "600", "320", "9.0.0", false, flashvars, params, attributes);
	</script>
	
</head>
<body>
<center>
	<div id="fb-root"></div>
	<script>window.fbAsyncInit = function(){
		FB.init({appId:'<?php echo $GLOBALS['appId']; ?>',status:true,cookie:true,xfbml:true});
		FB.Canvas.setAutoResize();
		<?php if( $GLOBALS['page'] == "invitar" ): ?>
		FB.ui({ method: 'apprequests',message: '<?php echo $GLOBALS['invitarMessage']; ?>'}, function(res){
						var req_ids = res.request_ids;
			var _batch = [];
			for(var i=0; i<req_ids.length; i++ ){
				_batch.push({"method":"get","relative_url":req_ids[i]});
			}
			if(_batch.length>0)
				{
				FB.api('/','POST',{batch:_batch},function(res){
					for(var x=0; x<res.length; x++ )
						{
						/*
						$.ajax({
							type:"POST",
							url: "ajax/ajax.suggest.save.php",
							data:"json="+base64_encode(res[x]['body']),
							success:function(r)
								{
								$("#json").append(r);
								}
						});
						*/
					}
				});     
			}
				}
			);
		<?php endif; ?>
		};
		(function(){var e = document.createElement('script');e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';e.async = true;document.getElementById('fb-root').appendChild(e);}());
	</script>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/es_LA/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

  	<div id="menu">
		<img src="../images/fondologo.png" align="left" />
		<img src="../images/fondofinal.png" align="right" />
		<div class="tab">  
				<?php foreach( $GLOBALS['json_main']->bottons as $botton ) : ?>
					<?php $tabClass = ( $GLOBALS['page'] == $botton->sk ) ? "tab-selected" : "tab-normal"; ?>
						<a class="<?php echo $tabClass; ?>" href="<?php echo $GLOBALS['appUrl'] . "?sk=" . $botton->sk; ?>" target="<?php echo $botton->target; ?>"><?php echo $botton->name; ?></a>
				<?php endforeach; ?>	
		</div><!-- div tab -->
	</div><!-- div menu -->
