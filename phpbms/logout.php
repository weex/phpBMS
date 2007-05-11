<?php
	session_name("phpBMSv08ID");
	session_start();
	session_destroy();
	header("Location: index.php");
?>
