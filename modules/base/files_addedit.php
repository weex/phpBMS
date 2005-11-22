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

	include("include/files_addedit_include.php");

	$pageTitle="File";

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
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
		<label for="accesslevel">
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
		<legend>names</legend>
		<label for="name" class="important">
			name<br />
			<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","style"=>"width:98%","class"=>"important","tabindex"=>"5")); ?>
		</label>
		<label for="servename" class="important">
			serve name<br />
			<?PHP field_text("servename",$therecord["servename"],1,"Serve name cannot be blank.","",Array("size"=>"40","maxlength"=>"64","style"=>"","class"=>"important","tabindex"=>"5")); ?>
		</label>
		<div class="small" style="padding-top:6px;padding-bottom:6px;"><em>
			Serve name is the file name, including extension that will be used when the file is sent through the web server to the client.  
			If the extension does not match the type (mime file type) the client browser may misinterpret the file.
		</em></div>
	</fieldset>
	<fieldset style="clear:both">
		<legend>file</legend>
		<?php if(!$therecord["nofile"]){?>
		<label for="type">
			type <em>(MIME)</em><br />
			<input type="text" id="type" name="type" value="<?php echo htmlQuotes($therecord["type"])?>" size="40" maxlength="100" readonly="true" class="uneditable" style="" />
		</label>
		<div><button type="button" class="Buttons" onClick="document.location='../../servefile.php?i=<?php echo $therecord["id"]?>'">view file</button></div>
		<?php } ?>
		<label for="upload">
			<?php if(!$therecord["nofile"]) echo "change file"; else echo "upload file"?><br />
			<input id="upload" name="upload" type="file" size="40" tabindex="260" />
		</label>
		
	</fieldset>
	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php");?>
</form>
</body>
</html>