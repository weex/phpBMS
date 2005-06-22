<?php
	require("include/session.php");

	function getAutofillXML($what,$displayfield,$xtrafield,$tabledefid,$thefield,$whereclause){
		global $dblink;
		
		$tblquerystatement="SELECT maintable FROM tabledefs WHERE id=".$tabledefid;
		$queryresult=mysql_query($tblquerystatement,$dblink);
		$therecord=mysql_fetch_array($queryresult);
		
		$querystatement="SELECT ".stripslashes($displayfield)." as display,
						".stripslashes($xtrafield)." as xtra ";
		if($thefield)
			$querystatement.=", ".$thefield." as thefield";			
		$querystatement.=" FROM ".$therecord["maintable"]." WHERE ";
		if($whereclause)
			$querystatement.=" (".stripslashes($whereclause).") AND ";
		$querystatement.=stripslashes($displayfield)." ";
		if($thefield)
				$querystatement.="=\"".addslashes($what)."\"";
		else 
				$querystatement.="LIKE \"".addslashes($what)."%\"";
		$querystatement.="ORDER BY ".stripslashes($displayfield);
		if($thefield)
			$querystatement.=" LIMIT 1";			
		else
			$querystatement.=" LIMIT 10";			
			
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult){
			echo $querystatement;
			$numrows=0;die();}
		else {
			$numrows=mysql_num_rows($queryresult);
		}
				
		header('Content-Type: text/xml');
		?>
<?php echo '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'; ?>
<response>
<numrec><?php echo $numrows ?></numrec>
	<?php if($numrows){
		while($therecord=mysql_fetch_array($queryresult)) {
	?><rec>
<fld>display</fld>
<val><?php echo xmlEncode($therecord["display"])?></val>
<fld>extra</fld>
<val><?php echo xmlEncode($therecord["xtra"])?></val><?php
	if(isset($therecord["thefield"])) {
	?><fld>thefield</fld><val><?php echo xmlEncode($therecord["thefield"])?></val><?php
	}
?></rec><?php } //end while
		}//end if
	?></response><?php 
	}//end function

	if(isset($_GET["l"])){
		if(!isset($_GET["gf"]))$_GET["gf"]="";
		if(!isset($_GET["wc"]))$_GET["wc"]="";
		getAutofillXML($_GET["l"],$_GET["fl"],$_GET["xt"],$_GET["tid"],$_GET["gf"],$_GET["wc"]);
	}
?>