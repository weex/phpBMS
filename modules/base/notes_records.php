<?php 
	$displayTable= new displaySearchTable;	
	$displayTable->base=$base;
	$displayTable->initialize(12);	
	$displayTable->querywhereclause=$whereclause;
	$displayTable->tableoptions["printex"]=0;
	$displayTable->tableoptions["othercommands"]=false;
	$displayTable->tableoptions["select"]=0;
	
	if(isset($_POST["command"])){
		
		switch($_POST["command"]){
			//command switches go here
			case "new":
				// relocate to new screen
				//=====================================================================================================
				header("Location: ".$displayTable->base.$displayTable->thetabledef["addfile"]."?backurl=".$backurl."&reftableid=".$reftableid."&refid=".$refid);
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
				header("Location: ".$displayTable->base.$displayTable->thetabledef["addfile"]."?backurl=".$backurl."&reftableid=".$reftableid."&refid=".$refid."&id=".$theids[0]);
			break;
		}//end switch
	}	

	//on the fly sorting... this needs to be done after command processing or the querystatement will not work.
	if(!isset($_POST["newsort"])) $_POST["newsort"]="";
	if(!isset($_POST["desc"])) $_POST["desc"]="";
	
	if($_POST["newsort"]!="") {
		//$displayTable->setSort($_POST["newsort"]);
		foreach ($displayTable->thecolumns as $therow){
			if ($_POST["newsort"]==$therow["name"]) $therow["sortorder"]? $displayTable->querysortorder=$therow["sortorder"] : $displayTable->querysortorder=$therow["column"];
		}
		$_POST["startnum"]=1;		
	} elseif($_POST["desc"]!="")  $displayTable->querysortorder.=" DESC";

	if($displayTable->querytype!="new" and $displayTable->querytype!="edit") {
	
		$displayTable->issueQuery();		
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" >
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/queryfunctions.js"></script>
</head>
<body><?php include("../../menu.php")?><?php doTabs()?><div class="untabbedbox">
	<div class="addedittitle"><?php echo $pageTitle ?></div>
	<div>
		<form name="search" id="searchform" action="<?php echo $_SERVER["REQUEST_URI"]?>" method="post" onSubmit="setSelIDs(this);return true;">
		<input name="theids" type="hidden" value="">
		<?php
			$displayTable->displayQueryButtons();
			$displayTable->displayQueryHeader();
			if($displayTable->numrows>0){
				$displayTable->displayQueryResults();
				$displayTable->displayQueryFooter();
			}
			else
				$displayTable->displayNoResults();				
		?>
		</form>
	</div>
</div>
</body>
</html><?php }//endif?>