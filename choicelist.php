<?php
	require_once("include/session.php");
	
	function deleteList($listname){
		global $dblink;

		$querystatement="DELETE FROM choices WHERE listname=\"".$listname."\" ";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$querystatement) 
			reportError(100,"SQL Statement Could not be executed.");
		else
			echo "ok";
	}
	
	function addToList($listname,$value){
		global $dblink;

		$querystatement="INSERT INTO choices (listname,thevalue) VALUES(\"".$listname."\",\"".$value."\") ";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$querystatement) 
			reportError(100,"SQL Statement Could not be executed.");
		else
			echo "ok";
	}

	function displayList($queryresult,$blankvalue){
		while($therecord=mysql_fetch_array($queryresult)){
			$display=$therecord["thevalue"];
			$theclass="";
			if($therecord["thevalue"]==""){
				$display="&lt;".$blankvalue."&gt;";
				$theclass=" class=\"choiceListBlank\" ";
			}
			if($therecord["thevalue"]==$value){
				$inlist=true;
			}
			if($value=="" and $therecord["thevalue"])
			?><option value="<?php echo $therecord["thevalue"]?>" <?php echo $theclass?>><?php echo $display?></option><?php
		}//end while
	
	}
	
	function displayBox($listname,$blankvalue,$listid){
		global $dblink;
		
		$blankvalue=str_replace("<","",$blankvalue);
		$blankvalue=str_replace(">","",$blankvalue);
		
		$querystatement="SELECT thevalue FROM choices WHERE listname=\"".$listname."\" ORDER BY thevalue;";
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$querystatement) reportError(100,"SQL Statement Could not be executed.");
?>
	<table width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td valign="top"><select id="MLlist" name="MLList" size="12" style="width:230px;margin-right:10px;margin-bottom:10px;" onChange="updateML(this)"><?php displayList($queryresult,$blankvalue)?>
			</select></td>
			<td valign="top" style="width:100%">
			<input type="button" id="MLDelete" name="MLDelete" value="delete" class="Buttons" style="width:50px;;margin-bottom:5px;" disabled onClick="delML()" /><br/>
			<input type="button" id="MLInsert" name="MLInsert" value="insert" class="Buttons" style="width:50px" onClick="insertML()"/>
			</td>
			<tr>
				<td valign="top">
					<input name="MLaddedit" id="MLaddedit" type="text" style="width:220px;margin-right:10px;margin-bottom:5px;"/>
					<input name="MLblankvalue" id="MLblankvalue" type="hidden" value="<?php echo $blankvalue?>"/>
				</td>
				<td valign="top"><input type="button" id="MLaddeditbutton" name="MLaddeditbutton" value="add" class="Buttons" style="width:50px" onClick="addeditML('<?php echo $blankvalue?>')" /></td>
			</tr>
		</tr>
	</table>
	<div id="MLStatus" class="small" align="center">&nbsp;</div>
	<div align="right">
		<input type="button" id="MLok" name="MLok" value="ok" class="Buttons" style="width:75px;" onClick="clickOK('<?php echo $_SESSION["app_path"]?>','<?php echo $listid?>','<?php echo $listname?>')"/>
		<input type="button" id="MLcancel" name="MLcancel" value="cancel" class="Buttons" style="width:75px;" onClick="closeBox('<?php echo $listid?>');"/>&nbsp;
	</div>
<?php	}//end function

	if(!isset($_GET["cm"])) 
		$_GET["cm"]="shw";
	
	if(!isset($_GET["ln"]))
		$_GET["ln"]="shippingmethod";
	if(!isset($_GET["bv"]))
		$_GET["bv"]="none";
	switch($_GET["cm"]){
		case "shw":
			displayBox($_GET["ln"],$_GET["bv"],$_GET["lid"]);
		break;
		case "del":
			deleteList($_GET["ln"]);
		break;
		case "add":
			addToList($_GET["ln"],$_GET["val"]);
		break;
	}
	
?>