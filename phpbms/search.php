<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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
	require_once("include/session.php");
	require_once("include/search_class.php");
	require_once("include/common_functions.php");	
	
	if(!isset($_GET["id"])) reportError(100,"Passed Parameter not present.");
	$_GET["id"]= (integer) $_GET["id"];
	
	$displayTable= new displaySearchTable;	

	//initialize the object
	$displayTable->initialize($_GET["id"]);	

	session_register("passedjoinclause");
	session_register("passedjoinwhere");
	

	//process commands... 
	if(!isset($_POST["othercommands"])) $_POST["othercommands"]="";
	if(!isset($_POST["relationship"])) $_POST["relationship"]="";
	if(!isset($_POST["advancedsearch"])) $_POST["advancedsearch"]="";
	if(!isset($_POST["advancedsort"])) $_POST["advancedsort"]="";
			
	if(isset($_POST["doprint"])) $_POST["command"]="print";
	if(isset($_POST["deleteCommand"]))
		if($_POST["deleteCommand"]) $_POST["command"]=$_POST["deleteCommand"];
	if($_POST["othercommands"]) $_POST["command"]="other";
	if($_POST["relationship"]) $_POST["command"]="relate records";
	if($_POST["advancedsearch"]) $_POST["command"]="advanced search";
	if($_POST["advancedsort"]) $_POST["command"]="advanced sort";
	

	if(isset($_POST["command"])){
		
		switch($_POST["command"]){
			//command switches go here
		case "print":
			// run the print routine
			//=====================================================================================================
			$displayTable->querytype="print";
			$theids=explode(",",$_POST["theids"]);
			$_SESSION["printing"]["tableid"]=$displayTable->thetabledef["id"];
			$_SESSION["printing"]["maintable"]=$displayTable->thetabledef["maintable"];
			$_SESSION["printing"]["theids"]=$theids;
			goURL("print.php");
		break;
		case "other":
			$displayTable->recordoffset=0;		
			// process table specific commands (passed by settings)		
			//=====================================================================================================
			$theids=explode(",",$_POST["theids"]);
			
			$querystatement="SELECT name FROM tableoptions WHERE id=".((int) $_POST["othercommands"]);
			$queryresult=mysql_query($querystatement,$dblink);
			if(!$queryresult)
				reportError(300,"Could not retrieve section other commands");
			if($therecord=mysql_fetch_array($queryresult) || ((int) $_POST["othercommands"])==-1){
				if(((int) $_POST["othercommands"])==-1)
					$therecord["name"]="delete_record";
				eval("\$tempmessage=".$therecord["name"]."(\$theids);");
				if($tempmessage) $statusmessage=$tempmessage;				
			}
		break;
		case "search":
			$displayTable->recordoffset=0;		
			$displayTable->buildSearch($_POST);		
		break;		
		case "reset":		
			$displayTable->recordoffset=0;		
			$displayTable->resetQuery();
		break;
		case "omit":
			// omit selected from current query
			//=====================================================================================================
			$displayTable->recordoffset=0;		
			$tempwhere="";
			$theids=explode(",",$_POST["theids"]);
			foreach($theids as $theid){
				$tempwhere.=" or ".$displayTable->thetabledef["maintable"].".id=".$theid;
			}
			$tempwhere=substr($tempwhere,3);
			$displayTable->querywhereclause="(".$displayTable->querywhereclause.") and not (".$tempwhere.")";			
		break;
		case "keep":
			// keep only those ids
			//=====================================================================================================
			$displayTable->recordoffset=0;		
			$tempwhere="";
			$theids=explode(",",$_POST["theids"]);
			foreach($theids as $theid){
				$tempwhere.=" or ".$displayTable->thetabledef["maintable"].".id=".$theid;
			}
			$tempwhere=substr($tempwhere,3);
			$displayTable->querywhereclause=$tempwhere;
		break;
		case "delete":
			//=====================================================================================================
			$theids=explode(",",$_POST["theids"]);
			if(function_exists("delete_record"))
				$tempmessage=delete_record($theids);
			else
				$tempmessage="Delete function not defined.";
			if($tempmessage) $statusmessage=$tempmessage;
		break;
		case "advanced search":
			$displayTable->recordoffset=0;		
			$displayTable->querywhereclause=stripslashes($_POST["advancedsearch"]);			
			$displayTable->querytype="advanced search";
		break;
		case "advanced sort":
			$displayTable->recordoffset=0;		
			$displayTable->querysortorder=$_POST["advancedsort"];
		break;
		case "relate records":
			include("include/relationships.php");
			$theids=explode(",",$_POST["theids"]);
			$goto=perform_relationship($_POST["relationship"],$theids);			
			$_SESSION["temp_relateto"]=$displayTable->thetabledef["maintable"];
			$displayTable->querytype="relate";

			goURL($goto);
		break;		
			
		}//end switch
	}//end if
	
	//on the fly sorting... this needs to be done after command processing or the querystatement will not work.
	if(!isset($_POST["newsort"])) $_POST["newsort"]="";
	if(!isset($_POST["desc"])) $_POST["desc"]="";
	
	if($_POST["newsort"]!="") {
		//$displayTable->setSort($_POST["newsort"]);
		$displayTable->recordoffset=0;		
		foreach ($displayTable->thecolumns as $therow){
			if ($_POST["newsort"]==$therow["name"]) $therow["sortorder"]? $displayTable->querysortorder=$therow["sortorder"] : $displayTable->querysortorder=$therow["column"];
		}
		$_POST["startnum"]=1;		
	} elseif($_POST["desc"]!="") {
		 $displayTable->querysortorder.=" DESC";
		 $displayTable->recordoffset=0;		
	}
	
if($displayTable->querytype!="print" and $displayTable->querytype!="relate" and $displayTable->querytype!="new" and $displayTable->querytype!="edit") {
	
	//Check for passed join clause from relationships
	//==============================================================
	if(isset($_SESSION["passedjoinclause"])) {
		$displayTable->recordoffset=0;
		$displayTable->queryjoinclause=$_SESSION["passedjoinclause"];
		session_unregister("passedjoinclause");			
	}

	//Check for passed whereclause
	//==============================================================
	if(isset($_SESSION["passedjoinwhere"])) {
		$displayTable->recordoffset=0;
		//gettin kinda ugly here... maybe there's a better solution somewhere?
		$displayTable->querywhereclause=$_SESSION["passedjoinwhere"];
		session_unregister("passedjoinwhere");
	
		//keeping settings
		$displayTable->querytype="related records from ".$_SESSION["temp_relateto"];	
		session_unregister("temp_relateto");
	}
	
	//record offset?
	if(isset($_POST["offset"])) if($_POST["offset"]!="") $displayTable->recordoffset=$_POST["offset"];
	
	
	$displayTable->issueQuery();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $displayTable->thetabledef["displayname"] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("head.php")?>

<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/search.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="common/javascript/queryfunctions.js" type="text/javascript" ></script>
</head>
<body><?php include("menu.php");?><?php if(isset($has_header)) display_header();?><div class="bodyline">
	<h1 id="srchScreen<?php echo $displayTable->thetabledef["id"] ?>"><?php echo $displayTable->thetabledef["displayname"] ?></h1>
<?php  
		//Search//select
			$displayTable->displaySearch();
			$displayTable->displayQueryButtons();
			$displayTable->displayQueryHeader();
			if($displayTable->numrows>0){
				$displayTable->displayQueryResults();
				$displayTable->displayQueryFooter();
			}
			else
				$displayTable->displayNoResults();				
			
			$displayTable->displayRelationships();
			$displayTable->saveQueryParameters();
	?>
</div>
<?php include("footer.php")?>
</body>
</html><?php }// end relate/print if?>