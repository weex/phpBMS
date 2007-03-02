<?php

function displayRoles($id,$dblink){
	$querystatement="SELECT roles.id,roles.name
					FROM roles INNER JOIN rolestousers ON rolestousers.roleid=roles.id 
					WHERE rolestousers.userid=".$id;
	$assignedquery=mysql_query($querystatement,$dblink);
	while($therecord=mysql_fetch_array($assignedquery))
		echo "<li>".$therecord["name"]."</li>";
}

function changePassword($variables,$id,$dblink){
	if($_SESSION["demo_enabled"]=="false"){
		$querystatement="SELECT id FROM users WHERE id=".$id." AND password=ENCODE(\"".$variables["curPass"]."\",\"".mysql_real_escape_string($_SESSION["encryption_seed"])."\")";
		$queryresult=mysql_query($querystatement,$dblink);
		if($queryresult)
			if (mysql_num_rows($queryresult)){
				$querystatement="UPDATE users SET password=ENCODE(\"".$variables["newPass"]."\",\"".$_SESSION["encryption_seed"]."\") WHERE id=".$id;
				$queryresult=mysql_query($querystatement,$dblink);
				return "Password Updated";
			} else 
				return "Current Password Incorrect";			
	} else
		return "Changing password is disbabled in demonstration mode.";
}

function updateContact($variables,$id,$dblink){
	$querystatement="UPDATE users SET email=\"".$variables["email"]."\", phone=\"".$variables["phone"]."\" WHERE id=".$id;
	$queryresult=mysql_query($querystatement,$dblink);
	$_SESSION["userinfo"]["email"]=$variables["email"];
	$_SESSION["userinfo"]["phone"]=$variables["phone"];
	return "Contact Information Updated";
}


if(isset($_POST["command"]))
	switch($_POST["command"]){
		case "Change Password":
			$statusmessage=changePassword(addSlashesToArray($_POST),$_SESSION["userinfo"]["id"],$dblink);
		break;
		case "Update Contact":
			$statusmessage=updateContact(addSlashesToArray($_POST),$_SESSION["userinfo"]["id"],$dblink);
		break;
		default:
			$statusmessage="\"".$_POST["command"]."\"";
		break;
	}
?>