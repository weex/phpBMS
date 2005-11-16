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