<?php 
	require("../../include/session.php");

	$result="sent";
	if(!isset($_GET["id"])){
		$result="bad passed variable";
	} else {
		$themessage=$_SESSION["massemail"]["body"];
		$querystatement="SELECT * FROM clients WHERE id=".$_GET["id"];
		$queryresult=mysql_query($querystatement,$dblink);
		$therecord=mysql_fetch_array($queryresult);
		
		$themessage=str_replace("[[todays_date]]",date("m/d/Y"),$themessage);
		foreach($therecord as $key=>$value)
			$themessage=str_replace("[[".$key."]]",$value,$themessage);
		
		if(!mail($therecord["email"],$_SESSION["massemail"]["subject"],$themessage,"From: ".$_SESSION["massemail"]["from"]))
			$result="error sending email";
	}
	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>';
?>
<response>
  <result><?php echo $result?></result>
</response>