<?php
	$loginNoKick = true;
	$loginNoDisplayError =true;
	include("include/session.php");
	
	session_destroy();
	header("Location: index.php");
?>
