<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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

	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");
	include("include/roles_addedit_include.php");
	
	$pageTitle="Role";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/roles.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/roles.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onsubmit="return submitForm(this);"><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div>
<div class="bodyline">
	<div id="topButtons">
		<?php showSaveCancel(1); ?>
	</div>
	<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1>
	
	<fieldset id="fsAttributes">
		<legend>attributes</legend>
		<p>
			<label for="id">id</label><br />
			<input id="id" name="id" type="text" value="<?php echo htmlQuotes($therecord["id"]); ?>" size="10" maxlength="10" readonly="readonly" class="uneditable" />
		</p>
		
		<p><?php fieldCheckbox("inactive",$therecord["inactive"])?><label for="inactive">inactive</label></p>
		
	</fieldset>
	
	<div id="leftSideDiv">
		<fieldset>
			<legend><label for="name">name</label></legend>
			<p>
				<?php fieldText("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"28","maxlength"=>"64","class"=>"important","style"=>"width:99%")); ?>			
			</p>
		</fieldset>

		<fieldset>
			<legend><label for="description">description</label></legend>
			<p>
				<textarea name="description" cols="45" rows="5" id="description"><?php echo htmlQuotes($therecord["description"])?></textarea>
			</p>
		</fieldset>
		
		<?php if($therecord["id"]){?>
		<fieldset>
			<legend>users</legend>
			<input type="hidden" name="userschanged" id="userschanged" value="0" />
			<input type="hidden" name="newusers" id="newusers" value="" />
			<div class="fauxP">
			<div id="assignedusersdiv">
				users assigned to group<br />
				<select id="assignedusers" size="10" multiple>
					<?php displayUsers($therecord["id"],"assigned",$dblink)?>
				</select>
			</div>
			<div id="usersbuttonsdiv">
				<p>
					<button type="button" class="Buttons" onclick="moveUser('availableusers','assignedusers')">&lt; add user</button>
				</p>
				<p>
					<button type="button" class="Buttons" onclick="moveUser('assignedusers','availableusers')">remove user &gt;</button>
				</p>
			</div>
			<div id="availableusersdiv">
				available users<br />
				<select id="availableusers" size="10" multiple>
					<?php displayUsers($therecord["id"],"available",$dblink)?>
				</select>
			</div>
			</div>
		</fieldset>		
		<?php }?>
	</div>

	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php"); ?>
</form>
</body>
</html>