<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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

	$displayTable= new displaySearchTable;	
	$displayTable->base=$base;
	$displayTable->initialize(12);	
	$displayTable->querywhereclause=$whereclause;
	$displayTable->tableoptions["printex"]=0;
	$displayTable->tableoptions["othercommands"]=false;
	$displayTable->tableoptions["select"]=0;

	if(isset($_POST["deleteCommand"]))
		if($_POST["deleteCommand"]) $_POST["command"]=$_POST["deleteCommand"];

	if(isset($_POST["command"])){
		switch($_POST["command"]){
			case $displayTable->thetabledef["deletebutton"]:
				//=====================================================================================================
			$theids=explode(",",$_POST["theids"]);
			$tempmessage=delete_record($theids);
			if($tempmessage) $statusmessage=$tempmessage;
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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/search.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/queryfunctions.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
	xtraParamaters="<?php echo "backurl=".$backurl."&reftableid=".$reftableid."&refid=".$refid ?>";
</script>
</head>
<body><?php include("../../menu.php")?><?php doTabs()?><div class="bodyline">
	<h1><?php echo $pageTitle ?></h1>
	<div>
		<form name="search" id="search" action="<?php echo $_SERVER["REQUEST_URI"]?>" method="post" onSubmit="setSelIDs(this);return true;">
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
<?php include("../../footer.php");?>
</body>
</html><?php }//endif?>