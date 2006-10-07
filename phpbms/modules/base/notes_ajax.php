<?php 
/*
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
 | All rights reserved.                                                    |
 +-------------------------------------------------------------------------+
 |                                                                         |
 | Redistribution and use in source and binary forms, with or without      |
 | modification, are permitted provided that the following conditions are  |
 | met:                                                                    |
 |                                                                         |
 | - Redistributions of source code must retain the above copyright        |
 |   notice, this list of conditions and the following disclaimer.         |
 |                                                                         |
 | - Redistributions in binary form must reproduce the above copyright     |
 |   notice, this list of conditions and the following disclaimer in the   |
 |   documentation and/or other materials provided with the distribution.  |
 |                                                                         |
 | - Neither the name of Kreotek LLC nor the names of its contributore may |
 |   be used to endorse or promote products derived from this software     |
 |   without specific prior written permission.                            |
 |                                                                         |
 | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS     |
 | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT       |
 | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A |
 | PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
 | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,   |
 | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT        |
 | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,   |
 | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY   |
 | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT     |
 | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE   |
 | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.    |
 |                                                                         |
 +-------------------------------------------------------------------------+
*/

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