<?php
	require_once("include/session.php");
	require_once("include/search_class.php");
	require_once("include/common_functions.php");	
	
	if(!isset($_GET["id"])) reportError(100,"Table ID Required");
	
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
			header("Location: print.php");
		break;
		case "other":
			$displayTable->recordoffset=0;		
			// process table specific commands (passed by settings)		
			//=====================================================================================================
			$theids=explode(",",$_POST["theids"]);
			eval($_POST["othercommands"]."(\$theids);");
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
		case "new":
			// relocate to new screen
			//=====================================================================================================
			header("Location: ".$displayTable->thetabledef["addfile"]);
		break;
		case $displayTable->thetabledef["deletebutton"]:
			//=====================================================================================================
			$theids=explode(",",$_POST["theids"]);
			delete_record($theids);
		break;
		case "edit":
			// relocate to edit screen
			//=====================================================================================================
			$theids=explode(",",$_POST["theids"]);
			$connector="?";
			if (strpos($displayTable->thetabledef["editfile"],"?"))
				$connector="&";
			header("Location: ".$displayTable->thetabledef["editfile"].$connector."id=".$theids[0]);
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

			header("Location: ".$goto);
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
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $displayTable->thetabledef["displayname"] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<!-- These Javscript files and scripts are required for the query_searchdisplay and query_function files to
	 work properly -->
<script language="JavaScript" src="common/javascript/common.js" type="text/javascript" ></script>
<script language="JavaScript" src="common/javascript/queryfunctions.js" type="text/javascript" ></script>
</head>
<body>
<?php include("menu.php");?>
<?php 
	$table_class="bodyline";
	if(isset($has_header)) {
		display_header();
		$table_class=$displayTable->isselect?"bodyline":"untabbedbox";
	}
	
?>
<div class="<?php echo $table_class ?>">
	<h1 id="srchScreen<?php echo $displayTable->thetabledef["id"] ?>"><?php echo $displayTable->thetabledef["displayname"] ?></h1>
	<div>
<?PHP  
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
	?></div>
</div>
</body>
</html><?php }// end relate/print if?>