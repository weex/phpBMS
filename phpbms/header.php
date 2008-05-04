<?php 
	if(!isset($pageTitle)) $pageTitle = APPLICATION_NAME;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
	$phpbms->cssIncludes = array_merge(array("base.css"),$phpbms->cssIncludes);
	$phpbms->showCssIncludes();
	
	$tempjsarray[] = "common/javascript/common.js";
	$tempjsarray[] = "common/javascript/menu.js";
	$tempjsarray[] = "include/jstransport.php";
	$tempjsarray[] = "common/javascript/moo/prototype.lite.js";
	$tempjsarray[] = "common/javascript/moo/moo.fx.js";
	$tempjsarray[] = "common/javascript/moo/moo.fx.pack.js";
	
	$phpbms->jsIncludes = array_merge($tempjsarray,$phpbms->jsIncludes);
	$phpbms->showJsIncludes();
	
	if(PERSISTENT_LOGIN && isset($_SESSION["userinfo"]["id"]))
		$phpbms->topJS[]="setLoginRefresh();";
		
	$phpbms->showExtraJs($phpbms->topJS);
	
	if($phpbms->onload) {
		?><script language="JavaScript" type="text/javascript">
			connect(window,"onload",function() {		
			<?php
					foreach($phpbms->onload as $theextra)
						echo $theextra."\n";
			?>
			})
		</script><?php
	}//end if onload
?>
</head>
<body>
<?php 

if($phpbms->showMenu){
	include("include/menu_class.php");
	
	$topMenu = new topMenu($db);
	$topMenu->display();

	//See if the statusmessage is set
	if (isset($statusmessage)) {?>
	<div id="statusmessage">
		<div id="SMLeft">
			<div id="SMText">
				<?php echo $statusmessage ?>
			</div>
		</div>
	</div><?php 
		$phpbms->bottomJS[]='var statusM=getObjectFromID("statusmessage");
	var SMAni=new fx.Combo(statusM,{opacity:false,duration:500});
	SMAni.hide();
	statusM.style.display="block";
	SMAni.toggle();';
	} // end if 
	
	?>
	<noscript>
		<div class="bodyline">
			<h1>JavaScript Disabled</h1>
			<p>phpBMS requires JavaScript to be enabled.</p>
		</div>
	</noscript><?php	
	
}//end if showMenu
?>