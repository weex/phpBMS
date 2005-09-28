<?php 
require_once("include/session.php");
		
function checkUserPassword($password,$id){
	global $dblink;
	
	$querystatement="SELECT id FROM users WHERE id=".$id." AND password=ENCODE(\"".$password."\",\"".$_SESSION["encryption_seed"]."\")";
	$queryresult=mysql_query($querystatement,$dblink);
	if($queryresult)
		if (mysql_num_rows($queryresult))
			echo "ok";
}

function updatePassword($newpassword,$id){
	global $dblink;
	
	$querystatement="UPDATE users SET password=ENCODE(\"".$newpassword."\",\"".$_SESSION["encryption_seed"]."\") WHERE id=".$id;
	$queryresult=mysql_query($querystatement,$dblink);
	if($queryresult)
			echo "ok";
	else echo mysql_error($dblink)."<br><br>".$querystatement;
}
		
function showUserInfo($base){
?>
<div>
<div class=small>
	current password<br>
	<input type="password" id="userCurPass" name="userCurPass" maxlength="32" style="width:99%"/>
</div>

<div class=small>
	new password<br>
	<input type="password" id="userNewPass" name="userNewPass" maxlength="32" style="width:99%"/>
	re-type new password<br>
	<input type="password" id="userNew2Pass" name="userNew2Pass" maxlength="32" style="width:99%"/>
</div>
<div id="cpStatus" align="center" style="font-size:10px;">&nbsp;</div>
<div align=right><input class="smallButtons" type="button" value="change password" onClick="changePassword('<?php echo $base?>')"/><input id="userCP" class="smallButtons" type="button" value="done" style="margin-left:3px;" onClick="closeModal()" /></div>
</div>
<?php
}// end funtion

//=============================================
if(isset($_GET["cm"])){
	switch($_GET["cm"]){
		case "shw":
			showUserInfo($_GET["base"]);
		break;
		case "chk":
			checkUserPassword($_GET["pass"],$_SESSION["userinfo"]["id"]);
		break;
		case "upd":
			updatePassword($_GET["pass"],$_SESSION["userinfo"]["id"]);
		break;
	}
}
?>