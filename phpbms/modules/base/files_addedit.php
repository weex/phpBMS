<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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

	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	if(!isset($_GET["backurl"])){
		include("include/files_addedit_include.php");
		$pageTitle="File";
	} else {
		include("include/attachments_addedit_include.php");
		$pageTitle="File Attachment";
	}
	
	function getAttachments($id){
		global $dblink;
		$querystatement="SELECT tabledefs.displayname, attachments.recordid, attachments.creationdate, tabledefs.editfile
						FROM attachments INNER JOIN tabledefs ON attachments.tabledefid=tabledefs.id 
						WHERE fileid=".$id;
		$queryresult=mysql_query($querystatement,$dblink);
		if(!$queryresult) reportError(300,"Error Retrieving Attachments: ".mysql_error($dblink)." -- ".$querystatement);
		return $queryresult;
	}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/files.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js" type="text/javascript"></script>
<script language="JavaScript" src="../base/javascript/file.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>


<form action="<?php echo htmlQuotes($_SERVER["REQUEST_URI"]) ?>" method="post" enctype="multipart/form-data" name="record" onSubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;" /></div>
<div class="bodyline">
	<div id="topButtons"><?php showSaveCancel(1); ?></div>
	
	<h1 id="topTitle"><span><?php echo $pageTitle ?></span></h1>

	<fieldset id="fsAttributes">
		<legend>Attributes</legend>
		<p>
			<label for="id">id</label><br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" />		
		</p>
		<p id="roleidP">
			<label for="roleid">access (role)</label><br />
			<?php fieldRolesList("roleid",$therecord["roleid"],$dblink)?>
		</p>
	</fieldset>
	
	<div class="leftSideDiv">
	<fieldset>
		<legend>file</legend>
		<?php if(isset($_GET["tabledefid"])){?>
			<input id="attachmentid" name="attachmentid" type="hidden" value="<?php echo $therecord["attachmentid"]?>" />
			<input id="tabledefid" name="tabledefid" type="hidden" value="<?php echo (integer) $_GET["tabledefid"]?>" />
			<input id="recordid" name="recordid" type="hidden" value="<?php echo (integer) $_GET["refid"]?>" />
		<?php }?>
		
		<?php if($therecord["id"]) {?>
			<p>
				<button  type="button" class="Buttons" onclick="document.location='../../servefile.php?i=<?php echo $therecord["id"]?>'">View/Download <?php echo $therecord["name"] ?></button>
			</p>
			<p>
				<label for="name" class="important">name</label><br />
				<?php fieldText("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"64","maxlength"=>"128","style"=>"","class"=>"important","tabindex"=>"5")); ?><br />
				<span class="notes">If the file name does <strong>not</strong> include an extension your browser may not be able to download/view the file correctly.</span>
			</p>
			<p>
				<label for="type">file type </label><span class="notes">(MIME)</span><br />
				<input type="text" id="type" name="type" value="<?php echo htmlQuotes($therecord["type"])?>" size="64" maxlength="100" readonly="true" class="uneditable" style="" />			
			</p>
			<p>
				<label for="upload">replace file</label><br />
				<input id="upload" name="upload" type="file" size="64" tabindex="260" />			
			</p>
		<?php } else {?>
			<?php if(isset($_GET["tabledefid"])){?>
				<p><br />
					<input class="radiochecks" type="radio" name="newexisting" id="newfile" value="new" checked="checked" onclick="switchFile()" /><label for="newfile">new file</label>&nbsp;&nbsp;
					<input type="radio"  class="radiochecks" name="newexisting" id="existingfile" value="existing" onclick="switchFile()" /><label for="existingfile">existing file</label><br />
					<span class="notes">Choose "existing file" if the file has already been uploaded into phpBMS.</span>
				</p>
				<p id="fileidlabel">
					<label for="fileid-ds" >existing file name</label><br />
					<?php 
						
						$securitywhere="";
						if ($_SESSION["userinfo"]["admin"]!=1 && count($_SESSION["userinfo"]["roles"])>0)		
							$securitywhere=" AND files.roleid IN (".implode(",",$_SESSION["userinfo"]["roles"]).",0)";
						fieldAutofill("fileid","",26,"files.id","files.name","if(length(files.description)>20,concat(left(files.description,17),\"...\"),files.description)","files.id!=1 ".$securitywhere,Array("size"=>"40","maxlength"=>"128","style"=>"",false)) 
					?>				
				</p>
			<?php }?>
				<p id="uploadlabel">
					<label for="upload">upload new file</label><br />
					<input id="upload" name="upload" type="file" size="64" tabindex="260" />				
				</p>
		<?php } ?>
		<p id="descriptionlabel">
			<label for="content">description</label><br />
			<textarea name="description" cols="45" rows="4" id="content"><?php echo htmlQuotes($therecord["description"])?></textarea>		
		</p>
	</fieldset>
	<?php 
	if($therecord["id"]) {
		$attchmentsquery=getAttachments($therecord["id"]);
		if(mysql_num_rows($attchmentsquery)){
		?>
		<h2>Record Attachments</h2>
		<div class="fauxP">
		<div style="" class="smallQueryTableHolder">
		<table border="0" cellpadding="0" cellspacing="0" class="smallQueryTable">
			<tr>
				<th align="left">table</th>
				<th align="left" nowrap>ID</th>
				<th align="right" width="99%">attached</th>
				<th align="left" nowrap>&nbsp;</th>
			</tr>
		<?php
			while($attachmentrecord=mysql_fetch_array($attchmentsquery)){
	?>
			<tr>
				<td nowrap><?php echo $attachmentrecord["displayname"] ?></td>
				<td><?php echo $attachmentrecord["recordid"] ?></td>
				<td align="right"><?php echo formatFromSQLDatetime($attachmentrecord["creationdate"]) ?></td>
				<td>
					<button class="graphicButtons buttonEdit" type="button" onclick="document.location='<?php echo $_SESSION["app_path"].$attachmentrecord["editfile"]."?id=".$attachmentrecord["recordid"] ?>'"><span>edit</span></button>
				</td>
			</tr>
	<?php 
			} ?></table></div></div><?php
		} 
	}?>
	</div>

	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php");?>
</form>
</body>
</html>