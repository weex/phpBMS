<?php
	include("include/session.php");
	include("include/advancedsearch_functions.php");
	
	if(!isset($_GET["id"])) reportError(100,"Passed Variable Not Present");
	
	//First, grab table name from id	
	$querystatement="SELECT id,displayname,maintable FROM tabledefs WHERE id=".$_GET["id"];
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(500,"Cannot retrieve Table Information");
	$thetabledef=mysql_fetch_array($queryresult);

	//Grab query for all columns
	$querystatement="SELECT * FROM ".$thetabledef["maintable"]." LIMIT 1";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(500,"Cannot retrieve Table Information");
	$numfields = mysql_num_fields($queryresult);
	for ($i=0;$i<$numfields;$i++) $fieldlist[]=mysql_field_name($queryresult,$i);
	
	//saved search commands
	$loadedsearch="";
	if(isset($_POST["command"])){
		switch($_POST["command"]){
			case "save search":
				save_search($_SESSION["userinfo"]["id"],$thetabledef["id"],$_POST["savename"],$_POST["constructedquery"]);
			break;
	
			case "delete search":
				delete_search($_SESSION["userinfo"]["id"],$_POST["loadsearch"]);
			break;
	
			case "load search":
				$loadedsearch=load_search($_POST["loadsearch"]);
			break;
		}//end switch
	}//end if
	
	//get all saved searches for this table, for hthe user (or global)
	$savedsearches=get_saved_searches($_SESSION["userinfo"]["id"],$thetabledef["id"]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $thetabledef["displayname"]; ?> Advanced Search</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="common/javascript/common.js"></script>
<script language="JavaScript" src="common/javascript/advancedsearch.js"></script>
</head>
<body>
<script>self.resizeTo(540,360)</script>
<div class=bodyline>
	<div class=large><strong><?php echo $thetabledef["displayname"]; ?> Advanced Search</strong></div>
	<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]?>" method="post" name="queryconstruct">
		<input name="tablename" type="hidden" value="<?php echo $thetabledef["maintable"] ?>">
<div align="center">
<table border="0" cellpadding="0" cellspacing="0" class="querytable">
	<tr>
		<td align="center" colspan=2 nowrap class="queryheader">and/or</td>
		<td class="queryheader">field</td>
		<td class="queryheader">operator</td>
		<td class="queryheader">argument</td>
	</tr>
	<tr>
<?php 
// Repeat for each one
for($i=1;$i<6;$i++) { ?>
   <td>
   	<?php if($i!=1) { ?>  
	   <input name="line<?php echo $i ?>" type="checkbox" value="1" class=radiochecks onClick="unlockLine(<?PHP echo $i ?>,this)">
	<?php } else echo "&nbsp;" ?>
   </td>
   <td align="left" class=small nowrap>
   	<?php if($i!=1) { ?>
		<input name="andor<?php echo $i ?>" disabled=true id="andorand<?php echo $i ?>" type="radio" class="radiochecks" value="and" checked onChange="updatequery(this);">and&nbsp;<input onChange="updatequery(this);" name="andor<?php echo $i ?>" id="andoror<?php echo $i ?>" type="radio" class="radiochecks" value="or" disabled=true>or
	<?php } else echo "&nbsp;" ?>
   </td>
   <td>
   	<select name="field<?php echo $i ?>" onChange="updatequery(this);" <?php if($i!=1) echo "disabled=true" ?>>
	   	<?php 
			foreach($fieldlist as $field){
				echo "<option value=\"".$field."\"";
				if($field=="id") echo "selected";
				echo ">".$field."</option>\n";
			}
		?>
	</select>
   </td>
   <td><select name="operator<?php echo $i ?>" id="operator<?php echo $i ?>" onChange="updatequery(this);" <?php if($i!=1) echo "disabled=true" ?>>
	 <option value="=" selected>=</option>
	 <option value="!=">!=</option>
	 <option value=">">&gt;</option>
	 <option value="<">&lt;</option>
	 <option value=">=">&gt;=</option>
	 <option value="<=">&lt;=</option>
	 <option value="like">like</option>
	 <option value="not like">not like</option>
	</select></td>
   <td width="100%"><input name="thetext<?php echo $i?>" id="thetext<?php echo $i?>" type="text" onKeyUp="updatequery(this);" size="32" maxlength="128" <?php if($i!=1) echo "disabled=true" ?> style="width:100%"></td>
  </tr>
<?php } ?>
</table></div>
<div class="box">
	<div style="float:left">
		<input name="showsql" id="showsql"type="button" class="smallButtons" onClick="togglesql();" value="show SQL" style="margin-bottom:3px;width:100px;"><br>
		<input name="showtips" id="showtips" type="button" class="smallButtons" onClick="toggletips();" value="show tips" style="width:100px;">
	</div>
	<div align="right">
		
		
		<input name="reset" type="reset" class="smallButtons" value="clear search criteria" style="margin-bottom:3px; width:120px;"><br>
		<input name="savedsearches" id="savedsearches" onClick="togglesavedsearches()" type="button" class="smallButtons" value="saved searches..."  style="width:120px;">
	</div>
</div>
	<div class="box" id="tipsbox" style="display:none;">
		<div>
			<strong>Tips</strong><br>
			<ul style="margin:0px;padding-left:20px;">
				<li>Type dates using the format "YYYY-MM-DD"</li>
				<li>When using the "like" or "contains" operator use the "%" character as a wildcard</li>
				<li>Invalid searches will not return any records, but will indicate an error.</li>
			</ul>
		</div>
	</div>
	<div class=box id="sqlbox" style="display:none;">
		<div>SQL<br>
			<textarea name="constructedquery" cols="85" rows="3" class=small style="width:100%"><?PHP echo $loadedsearch; ?></textarea>
		</div>		
	</div>
	<div class=box id="savedsearchesbox" style="display:none;">
		<div style="float:right;width:120px;"><br>
			<input name="command" type="submit" class="Buttons" value="load search" style="width:120px;margin-bottom:3px;" <?php if(!mysql_num_rows($savedsearches)) echo "disabled"?>><br>
        	<input name="command" type="submit" class="Buttons" value="delete search" style="width:120px;" <?php if(!mysql_num_rows($savedsearches)) echo "disabled"?>>
		</div>
		<div style="margin-right:130px;">
			saved searches<br>
			<?PHP display_saved_search_list($savedsearches); ?><br><br>&nbsp;
		</div>
	</div>
	<div class="recordbottom" align="right">
		<input name="savename" type="hidden" size="32" maxlength="64" style="">
		<input class=Buttons name="command" type="button" value="execute search" onClick="dosearch(this.form)" style="width:100px;">
		<input name="command" type="submit" onClick="getname(this.form);" class="Buttons" value="save search" style="width:100px;">
		<input class=Buttons name="cancel" type="button" onClick="window.close();" value="cancel" style="width:100px;"> 		
	</div>
	<?php if($loadedsearch){?>
		<script language="javascript">document["queryconstruct"]["showsql"].click();</script>
	<?php }?>
	</form>
</div>




</body>
</html>
