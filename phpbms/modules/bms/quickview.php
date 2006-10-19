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
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pagetitle?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php require("../../head.php")?>
<!-- These Javscript files and scripts are required for the query_searchdisplay and query_function files to
	 work properly -->
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
<body onLoad="pageInit()">
<?php include("../../menu.php");?>
<form method="post" name="record" id="record" onSubmit="return false;">
<div class="bodyline">
	<h1><?php echo $pagetitle?></h1>
	<fieldset>
		<legend>Look Up Client/Prospect</legend>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<label for="lookupby">
							by<br />
							<select id="lookupby"  onChange="updateLookup(this)" tabindex="5">
								<option value="namecid">name</option>
								<option value="emailcid">e-mail address</option>
								<option value="workphonecid">work phone</option>
								<option value="homephonecid">home phone</option>
								<option value="mobilephonecid">mobile phone</option>
								<option value="mainaddresscid">main address</option>
							</select>
						</label>					
					</td>
					<td width="99%">
						<label for="lookupname" id="lookupNameLabel">
							name<br />
							  <?PHP 
							  	$passedValue="";
							  	if(isset($_GET["cid"]))
								  	$passedValue=$_GET["cid"];
							  	autofill("namecid",$passedValue,2,"clients.id","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","if(clients.city!=\"\",concat(clients.city,\", \",clients.state),\"\")","",Array("size"=>"45","maxlength"=>"128","style"=>"","style"=>"width:98%;font-weight:bold","tabindex"=>"10"),0) ?>
							  <script language="JavaScript">
								document.forms["record"]["namecid"].onchange=updateViewButton;
							  </script>
						</label>					
						<label for="lookupname" id="lookupEmailLabel" style="display:none;">
							e-mail address<br />
							  <?PHP autofill("emailcid","",2,"clients.id","clients.email","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","",Array("size"=>"45","maxlength"=>"128","style"=>"","style"=>"width:98%;font-weight:bold","tabindex"=>"10"),0) ?>
							  <script language="JavaScript">
								document.forms["record"]["emailcid"].onchange=updateViewButton;
							  </script>
						</label>					
						<label for="lookupname" id="lookupWorkPhoneLabel" style="display:none;">
							work phone<br />
							  <?PHP autofill("workphonecid","",2,"clients.id","clients.workphone","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","",Array("size"=>"45","maxlength"=>"128","style"=>"","style"=>"width:98%;font-weight:bold","tabindex"=>"10"),0) ?>
							  <script language="JavaScript">
								document.forms["record"]["workphonecid"].onchange=updateViewButton;
							  </script>
						</label>					
						<label for="lookupname" id="lookupHomePhoneLabel" style="display:none;">
							home phone<br />
							  <?PHP autofill("homephonecid","",2,"clients.id","clients.homephone","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","",Array("size"=>"45","maxlength"=>"128","style"=>"","style"=>"width:98%;font-weight:bold","tabindex"=>"10"),0) ?>
							  <script language="JavaScript">
								document.forms["record"]["homephonecid"].onchange=updateViewButton;
							  </script>
						</label>					
						<label for="lookupname" id="lookupMobilePhoneLabel" style="display:none;">
							mobile phone<br />
							  <?PHP autofill("mobilephonecid","",2,"clients.id","clients.mobilephone","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","",Array("size"=>"45","maxlength"=>"128","style"=>"","style"=>"width:98%;font-weight:bold","tabindex"=>"10"),0) ?>
							  <script language="JavaScript">
								document.forms["record"]["mobilephonecid"].onchange=updateViewButton;
							  </script>
						</label>					
						<label for="lookupname" id="lookupMainAddressLabel" style="display:none;">
							address<br />
							  <?PHP autofill("mainaddresscid","",2,"clients.id","clients.address1","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","",Array("size"=>"45","maxlength"=>"128","style"=>"","style"=>"width:98%;font-weight:bold","tabindex"=>"10"),0) ?>
							  <script language="JavaScript">
								document.forms["record"]["mainaddresscid"].onchange=updateViewButton;
							  </script>
						</label>					
					</td>
					<td nowrap>
						<div><br />
							<input type="button" value="view" id="dolookup" class="Buttons" style="width:75px;margin-right:2px;" disabled="true" tabindex="20" onClick="viewClient('<?php echo $_SESSION["app_path"]?>')">
							<input type="button" value="add new" id="addnew" class="Buttons" style="width:75px;" tabindex="20" onClick="addEditRecord('new','client','<?php echo getAddEditFile(2,"add")?>')">
						</div>
					</td>
				</tr>				
			</table>
	</fieldset>

</div>
<span id="clientrecord"></span>
</form>
<?php include("../../footer.php")?>
</body>
</html>
