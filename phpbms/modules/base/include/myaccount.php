<?php

function displayRoles($db){

	$uuid = $_SESSION["userinfo"]["uuid"];

	$querystatement = "
		SELECT
			`roles`.`id`,
			`roles`.`name`
		FROM
			`roles` INNER JOIN `rolestousers` ON `rolestousers`.`roleid`=`roles`.`uuid`
		WHERE
			`rolestousers`.`userid` = '".mysql_real_escape_string($uuid)."'
		";
	$assignedquery = $db->query($querystatement);
	while($therecord = $db->fetchArray($assignedquery))
		echo "<li>".$therecord["name"]."</li>";
}

function changePassword($variables,$id,$db){
	if(DEMO_ENABLED=="false"){
		$querystatement="SELECT id FROM users WHERE id=".$id." AND password=ENCODE(\"".$variables["curPass"]."\",\"".mysql_real_escape_string(ENCRYPTION_SEED)."\")";
		$queryresult=$db->query($querystatement);
		if($queryresult)
			if ($db->numRows($queryresult)){
				$querystatement="UPDATE users SET password=ENCODE(\"".$variables["newPass"]."\",\"".ENCRYPTION_SEED."\") WHERE id=".$id;
				$queryresult=$db->query($querystatement);
				return "Password Updated";
			} else
				return "Current Password Incorrect";
	} else
		return "Changing password is disabled in demonstration mode.";
}

function updateContact($variables,$id,$db){
	$querystatement="UPDATE users SET email=\"".$variables["email"]."\", phone=\"".$variables["phone"]."\" WHERE id=".$id;
	$queryresult=$db->query($querystatement);
	$_SESSION["userinfo"]["email"]=$variables["email"];
	$_SESSION["userinfo"]["phone"]=$variables["phone"];
	return "Contact Information Updated";
}


if(isset($_POST["command"]))
	switch($_POST["command"]){
		case "Change Password":
			$statusmessage=changePassword(addSlashesToArray($_POST),$_SESSION["userinfo"]["id"],$db);
		break;
		case "Update Contact":
			$statusmessage=updateContact(addSlashesToArray($_POST),$_SESSION["userinfo"]["id"],$db);
		break;
		default:
			$statusmessage="\"".$_POST["command"]."\"";
		break;
	}
?>