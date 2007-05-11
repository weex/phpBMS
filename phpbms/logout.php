<?php
	session_name("phpBMS v0.8");
	session_start();
	session_destroy();
	header("Location: index.php");
?>
