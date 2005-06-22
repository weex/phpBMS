<?PHP
	include("include/session.php");

	$name=stripslashes($name);
	$list=stripslashes($list);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" >
<html>
<head>
<title>Choose...</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
function getObjectFromID(id){
	var theObject;
	if(document.getElementById)
		theObject=document.getElementById(id);
	else
		theObject=document.all[id];
	return theObject;
}

function sendInfo(name,thevalue){
		theform=opener.document.forms['record'];
		theform[name].value=thevalue;
		if(theform[name].onchange) theform[name].onchange();
		window.close();
}

function doOptions(){
	var showit=document.forms["thechoice"]["showoptions"].value;
	
	if (document.getElementById) {
		var thegraphic=document.getElementById("thearrow");
		var thebox=document.getElementById("theextra");
	}else{
		var thegraphic=document.all["thearrow"];
		var thebox=document.all["theextra"];
	}
	
	if (showit==0){
		thegraphic.src="<?php echo $_SESSION["app_path"]?>common/image/down_arrow.gif";
		thebox.style.display="block";
		self.resizeBy(0,175);
		showit=1;
	} else{
		thegraphic.src="<?php echo $_SESSION["app_path"]?>common/image/left_arrow.gif";
		thebox.style.display="none";
		self.resizeBy(0,-175);
		showit=0;
	}
	document.forms["thechoice"]["showoptions"].value=showit;
	
}//end function

function setFocus(){
	var theselect=getObjectFromID("theselect");
	theselect.focus();		
}
</script>
</head>
<body>
<script>self.resizeTo(330,290)</script>
<?PHP 
	//The Bulk of the Processing is going to be done here to allow access to the javascript function
	if(isset($_POST["command"])){
		switch($_POST["command"]){
			//Process commands
			case "select":
			// send javascript back
				echo "<script language=\"JavaScript\">sendInfo('".$_POST["name"]."','".$_POST["theselect"]."')</script>";
			break;
			case "delete":
				$thequery="delete from choices where listname=\"".$_POST["list"]."\" and thevalue=\"".$_POST["theselect"]."\";";
				$thequery=mysql_query($thequery,$dblink);
			break;
			case "set as default":
				$thequery="update choices set selected=0 where listname=\"".$_POST["list"]."\" ;";
				$thequery=mysql_query($thequery,$dblink);		
	
				$thequery="update choices set selected=1 where listname=\"".$_POST["list"]."\" and thevalue=\"".$_POST["theselect"]."\";";
				$thequery=mysql_query($thequery,$dblink);		
			break;
			case "add":
				$thequery="insert into choices (listname,thevalue) values (\"".$_POST["list"]."\",\"".$_POST["addnew"]."\");";
				$thequery=mysql_query($thequery,$dblink);		
			break;
		}//end switch
	}//end if
	
	if(isset($_GET["list"])) $_POST["list"]=$_GET["list"];
	$thequery="Select thevalue,selected from choices where listname=\"".$_POST["list"]."\" order by selected DESC,thevalue;";
	$thequery=mysql_query($thequery,$dblink);
?>
<form action="choicelist.php" method="post" name="thechoice"><input name="name" type="hidden" value="<?PHP echo $name ?>"><input name="list" type="hidden" value="<?PHP echo $list ?>">
<div class="bodyline">
	<div>
		<strong>choose from list...</strong><br>
		<select name="theselect" size="10" id="theselect" style="width:96%" ondblclick="this.form['select'].click()">
		  <?PHP
			while($queryrow = mysql_fetch_array($thequery)){
				echo "<option value=\"".$queryrow["thevalue"]."\" onDblClick=\"this.form['select'].click()\" ";
				if($queryrow["selected"]) echo "selected";
				echo ">".$queryrow["thevalue"]."</option>\n";
			}
		  ?>
		</select>
		<script language="javascript">setFocus();</script>
	</div>
	<div class="small" style="padding-top:0px;padding-bottom:0px;">
		<a href="#" onClick="doOptions()">options<img src="<?php echo $_SESSION["app_path"]?>common/image/left_arrow.gif" id="thearrow" width=10 height=10 border="0" align="absmiddle"></a>
		<input type="hidden" name="showoptions" value="0">
	</div>
	<div align="right" id="thebottom">
		<input name="command" id="select" type="submit" class="Buttons" value="select" style="font-weight:bold; width:110px; height:25px;">
    </div>
	
	<div id="theextra" style="display:none;">
		<div class="box">
			<div>
				<div>
				<strong>modify selected</strong>
				</div>
				<div align="center">
				<input name="command" id="delete" type="submit" class="Buttons" value="delete" style="width:120px;margin-right:3px;"><input name="command" id="setdefault" type="submit" class="Buttons" value="set as default" style="width:120px;">
				</div>
			</div>
		</div>
		<div class="box">
			<div>
    	    	<strong>add new item</strong><br>
	            <input name="addnew" type="text" id="addnew" style="width:100%;" size="45" maxlength="128" >
			</div>
			<div align="right">
				<input name="command" id="new" type="submit" class="Buttons" value="add" style="width:110px">        		
			</div>
		</div>
	</div>
	</div>
</form>
</body>
</html>
