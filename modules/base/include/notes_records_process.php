<?php
if(isset($_POST["command"])) {
	
	//convert any checked records to an array of ids
	foreach($HTTP_POST_VARS as $key=>$value){
		if (substr($key,0,5)=="check") $theids[]=$value;
	}

	//Search Options Command Process
	//=====================================================================================================
	switch($command) {
	case "new":
		// relocate to new screen
		//=====================================================================================================
		$theurl=getAddEditFile(12)."?reftable=".$reftable."&refid=".$_GET["refid"]."&backurl=".$backurl;
		header("Location: ".$theurl );
	break;
	case "delete":
		//a bit more complicated so we'll put it in it's own function?
		//=====================================================================================================

			//passed variable is array of user ids to be revoked
			$dwhereclause="";
			foreach($theids as $theid){
				$dwhereclause=$dwhereclause." or id=".$theid;
			}
			$dwhereclause=substr($dwhereclause,3);		
			$thequery = "delete from notes where (createdby=".$_SESSION["userinfo"]["id"]." or assignedtoid=".$_SESSION["userinfo"]["id"].") and (".$dwhereclause.");";
			$theresult = mysql_query($thequery);
			if (!$theresult) die ("Couldn't Update: ".mysql_error($dblink)."<BR>\n SQL STATEMENT [".$thequery."]");		
	break;
	case "edit/view":
		// relocate to edit screen
		//=====================================================================================================
		  header("Location: ".getAddEditFile(12)."?id=".$theids[0]."&refid=".$_GET["refid"]."&backurl=".$backurl);
	break;
	}//end switch
} //end if
?>