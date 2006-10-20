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
	require_once("../../include/session.php");
	require_once("../../include/common_functions.php");	
	require_once("./include/quickview_include.php");
	require_once("../../include/fields.php");
	
	$pagetitle="Quick View";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pagetitle?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/quickview.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="./javascript/quickview.js" type="text/javascript" ></script>
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
<script language="javascript">
editButton = new Image();
editButton.src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-edit.png";
editButtonDisabled = new Image();
editButtonDisabled.src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-edit-disabled.png";
</script>
</head>
<body>
<?php include("../../menu.php");?>
<form method="post" name="record" id="record" onSubmit="return false;">
<div class="bodyline">
	<h1><?php echo $pagetitle?></h1>

	<fieldset>
		<legend>Look Up Client/Prospect</legend>
			<p id="lookupByP">
				<label for="lookupby">by</label><br />
				<select id="lookupby" onchange="updateLookup(this)" tabindex="5">
					<option value="namecid">name</option>
					<option value="emailcid">e-mail address</option>
					<option value="workphonecid">work phone</option>
					<option value="homephonecid">home phone</option>
					<option value="mobilephonecid">mobile phone</option>
					<option value="mainaddresscid">main address</option>
				</select>				
			</p>
			<p id="lookupButtonsP">
				<input type="button" value="view" id="dolookup" class="Buttons" disabled="true" tabindex="20" onclick="viewClient('<?php echo $_SESSION["app_path"]?>')" />
				<input type="button" value="add new" id="addnew" class="Buttons" tabindex="20" onclick="addEditRecord('new','client','<?php echo getAddEditFile(2,"add")?>')" />
			</p>			
			<div class="fauxP" id="lookupWhatP">
				<p id="lookupNameLabel">
					<label for="ds-namecid">name</label><br />
					<?PHP 
						$passedValue="";
						if(isset($_GET["cid"]))
							$passedValue=$_GET["cid"];
						autofill("namecid",$passedValue,2,"clients.id","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","if(clients.city!=\"\",concat(clients.city,\", \",clients.state),\"\")","",Array("size"=>"45","maxlength"=>"128","class"=>"important lookupWhats","tabindex"=>"10"),0)
					?>
					<script language="JavaScript">document.forms["record"]["namecid"].onchange=updateViewButton;</script>						
				</p>
				
				<p id="lookupEmailLabel" class="disabledP">
					<label for="ds-emailcid">e-mail address</label><br />
					<?PHP autofill("emailcid","",2,"clients.id","clients.email","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","",Array("size"=>"45","maxlength"=>"128","style"=>"","class"=>"important lookupWhats","tabindex"=>"10"),0) ?>
					<script language="JavaScript">document.forms["record"]["emailcid"].onchange=updateViewButton;</script>
				</p>
				<p id="lookupWorkPhoneLabel" class="disabledP">
					<label for="ds-workphonecid" >work phone</label><br />
					<?PHP autofill("workphonecid","",2,"clients.id","clients.workphone","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","",Array("size"=>"45","maxlength"=>"128","style"=>"","class"=>"important lookupWhats","tabindex"=>"10"),0) ?>
					<script language="JavaScript">document.forms["record"]["workphonecid"].onchange=updateViewButton;</script>
				</p>
				<p id="lookupHomePhoneLabel" class="disabledP">
					<label for="ds-homephonecid">home phone</label><br />
					<?PHP autofill("homephonecid","",2,"clients.id","clients.homephone","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","",Array("size"=>"45","maxlength"=>"128","style"=>"","class"=>"important lookupWhats","tabindex"=>"10"),0) ?>
					<script language="JavaScript">document.forms["record"]["homephonecid"].onchange=updateViewButton;</script>
				</p>
				<p id="lookupMobilePhoneLabel" class="disabledP">
					<label for="lookupname">mobile phone</label><br />
					<?PHP autofill("mobilephonecid","",2,"clients.id","clients.mobilephone","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","",Array("size"=>"45","maxlength"=>"128","style"=>"","class"=>"important lookupWhats","tabindex"=>"10"),0) ?>
					<script language="JavaScript">document.forms["record"]["mobilephonecid"].onchange=updateViewButton;</script>
				</p>
				<p id="lookupMainAddressLabel" class="disabledP">
					<label for="lookupname" >address</label><br />
					<?PHP autofill("mainaddresscid","",2,"clients.id","clients.address1","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","",Array("size"=>"45","maxlength"=>"128","style"=>"","class"=>"important lookupWhats","tabindex"=>"10"),0) ?>
					<script language="JavaScript">document.forms["record"]["mainaddresscid"].onchange=updateViewButton;</script>
				</p>
			</div>
	</fieldset>
</div>
<div id="clientrecord"></div>
</form>
</body>
</html>
