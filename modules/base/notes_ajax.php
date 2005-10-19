<?php 
	require_once("../../include/session.php");
	require_once("../../include/common_functions.php");

	function sendNoticeEmail($noteid) {
		global $dblink;
		
		$querystatement="SELECT subject,assignedtoid,assignedbyid,content,type,
						date_Format(assignedtodate,\"%c/%e/%Y\") as assignedtodate,time_format(assignedtotime,\"%l:%i %p\") as assignedtotime,
						date_Format(startdate,\"%c/%e/%Y\") as startdate, time_format(starttime,\"%l:%i %p\") as starttime,
						date_Format(enddate,\"%c/%e/%Y\") as enddate, time_format(endtime,\"%l:%i %p\") as endtime
						FROM notes WHERE id=".$noteid;
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Could not retrieve note record: ".mysql_error($dblink)."<br />".$querystatement);		
		$therecord=mysql_fetch_array($queryresult);
		
		$querystatement="SELECT firstname,lastname,email FROM users WHERE id=".$therecord["assignedtoid"];
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Could not retrieve user to record: ".mysql_error($dblink)."<br />".$querystatement);		
		$torecord=mysql_fetch_array($queryresult);
		if($torecord["email"]=="")
			return "Assignee has no e-mail address set.";
		
		$querystatement="SELECT firstname,lastname,email FROM users WHERE id=".$therecord["assignedbyid"];
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Could not retrieve user from record: ".mysql_error($dblink)."<br />".$querystatement);		
		$fromrecord=mysql_fetch_array($queryresult);
		if($fromrecord["email"]=="")
			return "You have no e-mail address set.";
		$from="".trim($fromrecord["firstname"]." ".$fromrecord["lastname"])." <".$fromrecord["email"].">";
		
		$subject=$_SESSION["application_name"]." Assignment: ".$therecord["subject"];
		switch($therecord["type"]){
			case "NT":
				$therecord["type"]="note";
			break;
			case "TS":
				$therecord["type"]="task";
			break;
			case "EV":
				$therecord["type"]="event";
			break;
			case "SY":
				$therecord["type"]="system message";
			break;
		}
		if($_SERVER["SERVER_PORT"] == 443)
			$protocol = "https";
		else
			$protocol = "http";

		$themessage= "You have received an e-mail notification of a ".$therecord["type"]." that has been assigned to you. To log on and view more details go to ".$protocol."://".$_SERVER["HTTP_HOST"].$_SESSION["app_path"]."\n\n";
		$themessage.="Title: ".$therecord["subject"]."\n\n";
		if($therecord["assignedtodate"]) {
			$themessage.="Follow Up: ".$therecord["assignedtodate"];
			if($therecord["assignedtotime"])
				$themessage.=" ".$therecord["assignedtotime"];
			$themessage.="\n\n";
		}
		if($therecord["type"]=="task"){
			if($therecord["startdate"]){
				$themessage.="Start Date: ".$therecord["startdate"];
				if($therecord["starttime"])
					$themessage.=" ".$therecord["starttime"];
				$themessage.="\n";
			}
			if($therecord["enddate"]){
				$themessage.="Due Date: ".$therecord["enddate"];
				if($therecord["endtime"])
					$themessage.=" ".$therecord["endtime"];
				$themessage.="\n";
			}
			$themessage.="\n";
		}
		if($therecord["type"]=="event"){
			$themessage.="Start Date: ".$therecord["startdate"];
			if($therecord["starttime"])
				$themessage.=" ".$therecord["starttime"];
			$themessage.="\n";
			$themessage.="End Date: ".$therecord["enddate"];
			if($therecord["endtime"])
				$themessage.=" ".$therecord["endtime"];
			$themessage.="\n\n";
		}		
		$themessage.="Memo:\n".$therecord["content"];
		
		if(!mail($torecord["email"],$subject,$themessage,"From: ".$from))
			return "Error Processing E-Mail.";
		
		return "E-Mail Sent.";
	}

	//=================================================================================================
	if(isset($_GET["cm"])){
		$thereturn="";
		switch($_GET["cm"]){
			case "sendemail":
				$thereturn=sendNoticeEmail($_GET["id"]);
			break;
		}
		echo $thereturn;
	}
?>