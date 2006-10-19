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
require_once("include/session.php");
require_once("include/common_functions.php");
require_once("include/print_class.php");

$pageTitle="Print/Export";

if(!isset($_GET["backurl"])) $_GET["backurl"]="";
if(isset($_POST["backurl"])) $_GET["backurl"]=$_POST["backurl"];

$tablePrinter= new printer;
$tablePrinter->initialize($_SESSION["printing"]["tableid"],$_SESSION["printing"]["theids"]);

$tablePrinter->saveVariables();

if (isset($_POST["command"])){
	switch($_POST["command"]){
		case "done":
			$tablePrinter->donePrinting($_GET["backurl"]);
		break;
		case "print":
			//let's build the whereclause
			$whereclause="";
			$dataprint="";
			switch($_POST["therecords"]){
				case "all":
					$dataprint="All Records";
				break;
				case "savedsearch":
					if($_POST["savedsearches"]!="" and $_POST["savedsearches"]!="NA")	{
						$querystatement="SELECT name,sqlclause FROM usersearches WHERE id=".$_POST["savedsearches"];
						$queryresult=mysql_query($querystatement,$dblink);
						If(!$queryresult) reportError(500,"Could not retrieve saved search. ".$querystatement);
						$therecord=mysql_fetch_array($queryresult);
						$whereclause="WHERE ".$therecord["sqlclause"];
						$dataprint=$therecord["name"];
					}
				break;
				case "selected":
					foreach($tablePrinter->theids as $theid){
						$whereclause.=" or ".$tablePrinter->maintable.".id=".$theid;
					}
					$whereclause="where ".substr($whereclause,3);
					$dataprint="Selected Records";
				break;
			}			
			$_SESSION["printing"]["whereclause"]=$whereclause;
			$_SESSION["printing"]["dataprint"]=$dataprint;
			
			//next let's do the sort
			$sortorder="";
			switch($_POST["thesort"]){
				case "single":
					$sortorder=" ORDER BY ".$tablePrinter->maintable.".".$_POST["singlefield"]." ".$_POST["order"];
				break;
				case "savedsort":
					if($_POST["savedsorts"]!="" and $_POST["savedsorts"]!="NA")	{
						$querystatement="SELECT sqlclause FROM usersearches WHERE id=".$_POST["savedsorts"];
						$queryresult=mysql_query($querystatement,$dblink);
						If(!$queryresult) reportError(500,"Could not retrieve saved search. ".$querystatement);
						$therecord=mysql_fetch_array($queryresult);
						$sortorder=" ORDER BY ".$therecord["sqlclause"];
					}
				break;
			}
			$_SESSION["printing"]["sortorder"]=$sortorder;
			
			if(isset($_POST["choosereport"])){
				$tablePrinter->openwindows="<script language=\"JavaScript\">\n";
				for($i=0;$i<count($_POST["choosereport"]);$i++){
					if($_POST["choosereport"][$i]){
						$querystatement="SELECT reportfile,type from reports where id=".$_POST["choosereport"][$i].";";
						$queryresult=mysql_query($querystatement,$dblink);
						if(!$queryresult) reportError(100,"Could not Retreive Report Information");				
						$reportrecord=mysql_fetch_array($queryresult);	
						$fakeExtForIE="";
						if($reportrecord["type"]=="PDF Report")
							$fakeExtForIE="&ext=.pdf";
						//javascript open each report in new window
						$tablePrinter->openwindows.="window.open('".$_SESSION["app_path"].$reportrecord["reportfile"]."?tid=".urlencode($tablePrinter->tableid).$fakeExtForIE."','print".$i."');\n";
					}
				}
				$tablePrinter->openwindows.="</script>\n";
			}			
		break;
	}
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php require("head.php")?>
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/print.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="common/javascript/print.js"></script>

<?PHP  $tablePrinter->showJavaScriptArray();?>
</head>
<body>
<div id="mainbody">
<div class="bodyline">
	<h1><?php echo $pageTitle ?><a name="top"></a></h1>
	
<form action="print.php" method="post" name="print">
	<input type="hidden" name="backurl" value="<?php echo $_GET["backurl"]?>">

	<fieldset id="fsReportInformation" >
		<legend>report information</legend>
		<?PHP 
			if (mysql_num_rows($tablePrinter->reports)){
				mysql_data_seek($tablePrinter->reports,0); 
				$therecord=mysql_fetch_array($tablePrinter->reports);
			} else {
				$therecord["id"]=0;
				$therecord["reportfile"]="";
				$therecord["name"]="";
				$therecord["type"]="";
				$therecord["description"]="";
			}
		?>
		<p>
			name<br />
			<input name="reportid" type="hidden" value="<?PHP echo $therecord["id"] ?>" />
			<input name="reportfile" type="hidden" value="<?PHP echo htmlQuotes($therecord["reportfile"]) ?>" />
			<input name="name" type="text" class="uneditable important" id="name" value="<?PHP echo htmlQuotes($therecord["name"]) ?>" size="32" maxlength="64" readonly="true" />		
		</p>
		
		<p>
			type<br />
			<input name="type" type="text" class="uneditable" id="type" value="<?PHP echo $therecord["type"] ?>" size="20" maxlength="64" readonly="true" />
		</p>
		<p>
			description<br />
			<textarea name="description" cols="45" rows="3" readonly="readonly" id="description" class="uneditable"><?PHP echo stripcslashes($therecord["description"]) ?></textarea>
		</p>
	</fieldset>

	<div id="selectReportsDiv">
	<fieldset>
		<legend>select report(s)</legend>
		<p>available reports<br />
			<?php $tablePrinter->displayReportList()?>
		</p>
	</fieldset>
	</div>

	<p><button id="showoptions" class="graphicButtons buttonDown" type="button"><span>more options</span></button></p>
	
	<div id="moreoptions">
		<fieldset>
			<legend>data</legend>
			<p id="showsavedsearches">
				<label for="savedsearches">load saved search...</label><br />
				<?php $tablePrinter->showSaved($tablePrinter->savedSearches,"savedsearches");?>
			</p>

			<p>
				<label class="important" for="therecords">from</label><br />
				<select id="therecords" name="therecords" onChange="showSavedSearches(this);">
					<option value="selected">selected records (<?php echo count($tablePrinter->theids) ?> record<?php if(count($tablePrinter->theids)>1) echo "s"?>)</option>
					<option value="savedsearch">saved search...</option>
					<?php if($_SESSION["userinfo"]["accesslevel"]>=30){?><option value="all">all records in table</option><?php }?>
				</select>
			</p>
		</fieldset>
		
		<fieldset>
			<legend>sort</legend>
			
			<p id="savedsortdiv">
				<label for="savedsorts">saved sort...</label><br />
				<?php $tablePrinter->showSaved($tablePrinter->savedSorts,"savedsorts");?>
			</p>
			
			<p id="singlesortdiv">
				<label for="sortfield">field</label><br />
				<?php $tablePrinter->showFieldSort()?>
				<select name="order">
					<option value="ASC" selected>Ascending</option>
					<option value="DESC">Descending</option>
				</select>			
			</p>
			<p class="important">
				<label for="thesort">by</label><br />
				<select id="thesort" name="thesort" onChange="showSortOptions(this)">
					<option value="default" selected>report default</option>
					<option value="single">single field</option>
					<option value="savedsort">saved sort...</option>
				</select>
			</p>
		</fieldset>
		<fieldset>
			<legend>customizng reports</legend>
			<p class="notes">
			Many reports feature a logo and your company infromation.  These can be set in the admin settings area. 
			</p>
			<p class="notes">
			Need more reports, or want to cuztomize an existing report to meet your specific needs? <br />
			if you are unfamiliar with PHP, or programming phpBMS, you can try visiting the
			<a href="http://www.phpbms.org">phpBMS project site</a>, or visit <a href="http://kreotek.com">Kreotek's website</a> for more information.
			</p>
		</fieldset>
	</div>	
	<fieldset class="small">
		<legend>Pop-Up Windows</legend>
		<p class="notes">
			Each report will display in its own window. If you have disabled
			pop-ups within your browser's options or are running a third-party pop-up blocker, the report may not show.
		</p>
	</fieldset>

	<p id="printFooter" class="box">
		<input name="command" type="submit" class="Buttons" id="print" value="print" accesskey="p" title="print (alt+p)">
		<input name="command" type="submit" class="Buttons" id="cancel" value="done" accesskey="d" title="done (alt+d)">	 
	</p>
   </form>
</div>
<?php include("footer.php")?>
</div>
</body>
</html><?PHP 	if($tablePrinter->openwindows) echo "\n".$tablePrinter->openwindows; ?>