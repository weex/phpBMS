<?php
    $_POST["columns"] = array(5,4,3,2,1,0);
    $_POST["columns"] = json_encode($_POST["columns"]);

	require("client_mailchimp_export.php");

?>