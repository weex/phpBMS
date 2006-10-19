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

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php require("../../head.php")?>
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
<script language="JavaScript" src="../base/javascript/file.js"></script>
</head>
<body><?php include("../../menu.php")?>


<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" enctype="multipart/form-data" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<div class="bodyline">
	<div style="float:right;width:160px;">
		  <?php showSaveCancel(1); ?>
	</div>
	<div style="margin-right:160px;">
		<h1><?php echo $pageTitle ?></h1>
	</div>

	<fieldset style="float:right;clear:both;width:170px">
		<legend>Attributes</legend>
		<label for="id">
			id<br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:99%" />
		</label>
		<label for="accesslevel" id="accesslevellabel">
			minimum access level<br />
			<?php 
				$choices=array();
				switch(TRUE){
					case ($_SESSION["userinfo"]["accesslevel"]>=90):
						$choices[]=array("value"=>"90","name"=>"Administrator");
					case ($_SESSION["userinfo"]["accesslevel"]>=50):
						$choices[]=array("value"=>"50","name"=>"Upper Manager");
					case ($_SESSION["userinfo"]["accesslevel"]>=30):
						$choices[]=array("value"=>"30","name"=>"Manager (sales manager)");
					case ($_SESSION["userinfo"]["accesslevel"]>=20):
						$choices[]=array("value"=>"20","name"=>"Power User (sales)");
					case ($_SESSION["userinfo"]["accesslevel"]>=10):
						$choices[]=array("value"=>"10","name"=>"basic user (shipping)");
				}
				basic_choicelist("accesslevel",$therecord["accesslevel"],$choices,Array("class"=>"important","tabindex"=>"10"));
			?>
		</label>
	</fieldset>
	<fieldset>
		<legend>file</legend>
		<?php if(isset($_GET["tabledefid"])){?>
			<input id="attachmentid" name="attachmentid" type="hidden" value="<?php echo $therecord["attachmentid"]?>">
			<input id="tabledefid" name="tabledefid" type="hidden" value="<?php echo (integer) $_GET["tabledefid"]?>">
			<input id="recordid" name="recordid" type="hidden" value="<?php echo (integer) $_GET["refid"]?>">
		<?php }?>
		
		<?php if($therecord["id"]) {?>
			<button  type="button" class="Buttons" onClick="document.location='../../servefile.php?i=<?php echo $therecord["id"]?>'">View/Download <?php echo $therecord["name"] ?></button>
			<label for="name" class="important">
				name<br />
				<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"64","maxlength"=>"128","style"=>"","class"=>"important","tabindex"=>"5")); ?>
			</label>
			<div class="small"><em>If the file name does <strong>not</strong> include an extension your browser may not be able to download/view the file correctly.</em></div>
			<label for="type">
				type <em>(MIME)</em><br />
				<input type="text" id="type" name="type" value="<?php echo htmlQuotes($therecord["type"])?>" size="64" maxlength="100" readonly="true" class="uneditable" style="" />
			</label>
			<label for="upload">
				replace file<br />
				<input id="upload" name="upload" type="file" size="64" tabindex="260" />			
			</label>
		<?php } else {?>
			<?php if(isset($_GET["tabledefid"])){?>
				<div>
					<label for="newfile" style="display:inline"><input class="radiochecks" type="radio" name="newexisting" id="newfile" value="new" checked onClick="switchFile()"> new file</label>
					<label for="existingfile" style="display:inline"><input type="radio"  class="radiochecks" name="newexisting" id="existingfile" value="existing" onClick="switchFile()"> existing file</label>
				</div>
				<label for="fileid-ds" id="fileidlabel" style="display:none;">
					existing file<br />
					<?PHP autofill("fileid","",26,"files.id","files.name","if(length(files.description)>20,concat(left(files.description,17),\"...\"),files.description)","files.id!=1 AND files.accesslevel<=".$_SESSION["userinfo"]["accesslevel"],Array("size"=>"40","maxlength"=>"128","style"=>"",false)) ?>					
				</label>
				<label for="upload" id="uploadlabel" style="display:block;">
					upload new file<br />
					<input id="upload" name="upload" type="file" size="64" tabindex="260" />			
				</label>
			<?php } else {?>
				<label for="upload">
					upload file<br />
					<input id="upload" name="upload" type="file" size="64" tabindex="260" />			
				</label>
			<?php } ?>
		<?php } ?>
		<label for="servename" id="descriptionlabel">
			description<br />
			<textarea name="description" cols="45" rows="4" id="content" style="width:98%"><?PHP echo $therecord["description"]?></textarea>
		</label>
	</fieldset>
	<?php 
	if($therecord["id"]) {
		$attchmentsquery=getAttachments($therecord["id"]);
		if(mysql_num_rows($attchmentsquery)){
		?>
		<fieldset style="margin-right:185px;">
			<legend>attachments</legend>
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
			<TR>
				<TD nowrap><?php echo $attachmentrecord["displayname"] ?></TD>
				<TD><?php echo $attachmentrecord["recordid"] ?></TD>
				<TD align="right"><?php echo formatDateTime($attachmentrecord["creationdate"]) ?></TD>
				<TD>
					<a href="<?php echo $_SESSION["app_path"].$attachmentrecord["editfile"]."?id=".$attachmentrecord["recordid"] ?>">
						<img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-edit.png" align="absmiddle" alt="edit" width="16" height="16" border="0" />
					</a>
				</TD>
			</TR>
	<?php 
			} ?></table></div></fieldset><?php
		} 
	}?>

	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php");?>
</form>
</body>
</html>