<?php
	include("include/session.php");
	include("include/advancedsort_functions.php");
	
	if(!isset($_GET["id"])) reportError(100,"Passed Variable Not Present");
	
	//First, grab table name from id	
	$querystatement="SELECT id,displayname,maintable FROM tabledefs WHERE id=".$_GET["id"];
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(500,"Cannot retrieve Table Information");
	$thetabledef=mysql_fetch_array($queryresult);

	//Grab query for all columns (for sort purposes)
	$querystatement="SELECT * FROM ".$thetabledef["maintable"]." LIMIT 1";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(500,"Cannot retrieve Table Information");
	$numfields = mysql_num_fields($queryresult);
	for ($i=0;$i<$numfields;$i++) $fieldlist[]=mysql_field_name($queryresult,$i);
	
	//saved search commands
	$loadedsort="";
	if(isset($_POST["command"])){
		switch($_POST["command"]){
			case "save sort":
				save_sort($_SESSION["userinfo"]["id"],$thetabledef["id"],$_POST["savename"],$_POST["constructedquery"]);
			break;
	
			case "delete sort":
				delete_sort($_SESSION["userinfo"]["id"],$_POST["loadsearch"]);
			break;
	
			case "load sort":
				$loadedsort=load_sort($_POST["loadsearch"]);
			break;
		}//end switch
	}//end if
	
	//get all saved searches for this table, for hthe user (or global)
	$savedsorts=get_saved_sorts($_SESSION["userinfo"]["id"],$thetabledef["id"]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $thetabledef["displayname"]; ?> Advanced Sort</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="common/javascript/common.js"></script>
<script language="JavaScript" src="common/javascript/advancedsort.js"></script>
</head>
<body>
<script>self.resizeTo(540,360)</script>
<div class=bodyline>
	<div class=large><strong><?php echo $thetabledef["displayname"]; ?> Advanced Sort</strong></div>
	<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]?>" method="post" name="queryconstruct">
		<input name="tablename" type="hidden" value="<?php echo $thetabledef["maintable"] ?>">
<table border="0" cellspacing="0" cellpadding="0" class="box">
	<tr>
		<td width="100%">
			<div>
				sort by<br>
				<select name="sortby" onChange="checkForCustom(this.value)" style="width:100%">
					<?php 
						foreach($fieldlist as $field){
							echo "<option value=\"".$field."\"";
							if($field=="id") echo "selected";
							echo ">".$field."</option>\n";
						}
					?>
					<option value="**CUSTOM**" class="important">custom SQL</option>
				</select>
				<div id="sqlsortby" class="" style="display:none;padding-left:0px;padding-right:0px">
					<input type="text" name="freetextsortby"  size="5" style="width:100%;">
				</div>
			</div>
		</td>
		<td valign="top">
			<div>
				order<br>
				<select name="order">
					<option value="ASC">Ascending</option>
					<option value="DESC">Descending</option>
				</select>
			</div>
		</td>
		<td valign="top">
			<div>
				<br><input type="button" name="addterm" value="add to sort" class="Buttons" onClick="addToSort(this.form,'<?php echo $thetabledef["maintable"]?>')">
			</div>
		</td>
	</tr>
</table>
<div>
<select style="width:98%" class="small" size="6" name="englishsortby"></select>
</div>
<div align="center">
</div>
<div class="box">
	<div>
		<input name="movedn" type="button" class="smallButtons" value="move item down" style="" onClick="moveItem(this.form,'down')">
		<input name="moveup" type="button" class="smallButtons" value="move item up" style="" onClick="moveItem(this.form,'up')">
		<input name="deleteitem" type="button" class="smallButtons" value="remove item" style="" onClick="removeItem(this.form)">
		<input name="reset" type="button" class="smallButtons" value="clear sort" style="" onClick="clearSort(this.form)">
	</div>
	<div style="padding-top:0px;">
		<input name="showsql" id="showsql"type="button" class="smallButtons" onClick="togglesql();" value="show SQL" style="width:120px;">
		<input name="savedsorts" id="savedsorts" onClick="togglesavedsorts()" type="button" class="smallButtons" value="saved sorts..."  style="width:120px;">
	</div>
</div>
	<div class=box id="sqlbox" style="display:none;">
		<div>SQL<br>
			<textarea name="constructedquery" cols="85" rows="3" class=small style="width:100%"><?PHP echo $loadedsort; ?></textarea>
		</div>		
	</div>
	<div class=box id="savedsortsbox" style="display:none;">
		<div style="float:right;width:120px;"><br>
			<input name="command" type="submit" class="Buttons" value="load sort" style="width:120px;margin-bottom:3px;" <?php if(!mysql_num_rows($savedsorts)) echo "disabled"?>><br>
        	<input name="command" type="submit" class="Buttons" value="delete sort" style="width:120px;" <?php if(!mysql_num_rows($savedsorts)) echo "disabled"?>>
		</div>
		<div style="margin-right:130px;">
			saved sorts<br>
			<?PHP display_saved_sort_list($savedsorts); ?><br><br>&nbsp;
		</div>
	</div>
	<div class="recordbottom" align="right">
		<input name="savename" type="hidden" size="32" maxlength="64" style="">
		<input class=Buttons name="command" type="button" value="execute sort" onClick="dosort(this.form)" style="width:100px;">
		<input name="command" type="submit" onClick="getname(this.form);" class="Buttons" value="save sort" style="width:100px;">
		<input class=Buttons name="cancel" type="button" onClick="window.close();" value="cancel" style="width:100px;"> 		
	</div>
	<?php if($loadedsort){?>
		<script language="javascript">document["queryconstruct"]["showsql"].click();</script>
	<?php }?>
	</form>
</div>




</body>
</html>
