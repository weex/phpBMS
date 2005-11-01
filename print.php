<?php
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
						$querystatement="SELECT reportfile from reports where id=".$_POST["choosereport"][$i].";";
						$queryresult=mysql_query($querystatement,$dblink);
						if(!$queryresult) reportError(100,"Could not Retreive Report Information");				
						$reportrecord=mysql_fetch_array($queryresult);	
						//javascript open each report in new window
						$tablePrinter->openwindows.="window.open('".$_SESSION["app_path"].$reportrecord["reportfile"]."?tabledefid=".urlencode($tablePrinter->tableid)."','print".$i."');\n";
					}
				}
				$tablePrinter->openwindows.="</script>\n";
			}			
		break;
	}
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="common/javascript/common.js"></script>
<script language="JavaScript" src="common/javascript/print.js"></script>
<script language="JavaScript" >
 var buttonUp=new Image();
 buttonUp.src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-up.png";
 var buttonDown=new Image();
 buttonDown.src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-down.png";
</script>
<?PHP  $tablePrinter->showJavaScriptArray();?>
</head>
<body>
<?PHP 	if($tablePrinter->openwindows) echo "\n".$tablePrinter->openwindows; ?>
<div class="bodyline" style="width:550px;margin-top:2px;">
	<h1><?php echo $pageTitle ?><a name="top"></a></h1>
	
<form action="print.php" method="post" name="print">
	<input type="hidden" name="backurl" value="<?php echo $_GET["backurl"]?>">
	<fieldset style="float:right;width:300px;margin-top:0px;">
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
		<label for="name">
			name<br />
			<input name="reportid" type="hidden" value="<?PHP echo $therecord["id"] ?>" />
			<input name="reportfile" type="hidden" value="<?PHP echo htmlQuotes($therecord["reportfile"]) ?>" />
			<input name="name" type="text" class="uneditable" id="name" style="font-weight:bold; width:98%" value="<?PHP echo htmlQuotes($therecord["name"]) ?>" size="32" maxlength="64" readonly="true" />
		</label>
		<label for="id">
			type<br />
			<input name="type" type="text" class="uneditable" id="type" value="<?PHP echo $therecord["type"] ?>" size="20" maxlength="64" readonly="true" style="width:98%" />
		</label>
		<label>
		   description<br>
		   <textarea name="description" cols="45" rows="3" readonly="readonly" id="description" style="width:98%;height:71px;" class="uneditable"><?PHP echo stripcslashes($therecord["description"]) ?></textarea>
		</label>		
	</fieldset><fieldset>
		<legend>select report(<span style="text-transform:lowercase">s</span>)</legend>
		<div>
			<?php $tablePrinter->displayReportList()?>
		</div>
	</fieldset>

	<div align="left" style="padding-top:0px;padding-left:10px;"><a href="" onClick="showMoreOptions(this);return false;"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-down.png" id="moreOptionsGraphic" align="absmiddle" alt="show"  width="16" height="16" border="0" /> more options</a></div>
	<div id="moreoptions" style="margin:0px;padding:0px;display:none;">
	<fieldset>
		<legend>data</legend>
		<label id="showsavedsearches" style="display:none;float:right;width:320px">
			load saved search...<br />
			<?php $tablePrinter->showSaved($tablePrinter->savedSearches,"savedsearches");?>
		</label>
		<label class="important" for="therecords">
		from<br />
		<select id="therecords" name="therecords" onChange="showSavedSearches(this);" style="width:200px;">
			<option value="selected">selected records (<?php echo count($tablePrinter->theids) ?> record<?php if(count($tablePrinter->theids)>1) echo "s"?>)</option>
			<option value="savedsearch">saved search...</option>
			<?php if($_SESSION["userinfo"]["accesslevel"]>=30){?><option value="all">all records in table</option><?php }?>
		</select>
		</label>
	</fieldset>
	<fieldset>
		<legend>sort</legend>
		<div id="savedsortdiv" style="display:none;float:right;width:320px;">
			saved sort...<br />
			<?php $tablePrinter->showSaved($tablePrinter->savedSorts,"savedsorts");?>
		</div>
		<div id="singlesortdiv" style="display:none;float:right;width:320px;">
			field<br>
			<?php $tablePrinter->showFieldSort()?>
			<select name="order">
				<option value="ASC" selected>Ascending</option>
				<option value="DESC">Descending</option>
			</select>			
		</div>
		<div class="important">
			by<br />
			<select name="thesort" onChange="showSortOptions(this)" style="width:200px;">
				<option value="default" selected>report default</option>
				<option value="single">single field</option>
				<option value="savedsort">saved sort...</option>
			</select>
		</div>
	</fieldset>
	<fieldset class="small">
		<legend>Notes</legend>
		<div>
			<strong>Window Pop-ups<br/></strong>Each report will display in its own window. If you have disabled
			pop-ups within your browser's options or are running a third-party pop-up blocker, the report will not appear.
		</div>
		<div><strong>Need More Reports?</strong><br/>
		Need more reports, or want to cuztomize an existing report to meet your specific needs?  Contact <a href="http://www.kreotek.com" target="_blank">kreotek</a> for more information.
		</div>
	</fieldset>
	</div>	

    <div align="right" class="box">
	 <input name="command" type="submit" class="Buttons" id="print" value="print" style="width:75px;margin-right:3px;">
	 <input name="command" type="submit" class="Buttons" id="cancel" value="done" style="width:75px;">	 
	 </div>
   </form>
</div>
<div style="margin:0px;padding:0px;width:550px;"><?php include("footer.php")?></div>
</body>
</html>