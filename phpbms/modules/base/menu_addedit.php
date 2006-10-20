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
	include("include/menu_addedit_include.php");
	
	$pageTitle="Menu Item";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php require("../../head.php")?>
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
</head>
<body><?php include("../../menu.php")?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;"></div>
<div class="bodyline">
	<div style="float:right;width:160px;">
		<?php showSaveCancel(1); ?>
	</div>
	<h1 style="margin-right:165px;"><?php echo $pageTitle ?></h1>
	<div style="float:right;width:200px;padding:0px;">
		<fieldset style="padding-top:0px;margin-top:0px;">
			<legend>attributes</legend>
			<label for="id">
				id<br />
				<input id="id" name="id" type="text" value="<?php echo htmlQuotes($therecord["id"]); ?>" size="10" maxlength="10" readonly="true" class="uneditable" style="width:99%" />
			</label>
			<label for="displayorder">
				 display order<br />
				 <?PHP field_text("displayorder",$therecord["displayorder"],1,"Display order cannot be blank and must be a valid integer.","integer",Array("size"=>"9","maxlength"=>"3")); ?>
			</label>
			<div class=small><em>(Lower numbers are displayed first.)</em></div>
			<label for="accesslevel">
				access level<br />
				<?php basic_choicelist("accesslevel",$therecord["accesslevel"],array(array("value"=>"-10","name"=>"portal access only"),array("value"=>"10","name"=>"basic user (shipping)"),array("value"=>"20","name"=>"Power User (sales)"),array("value"=>"30","name"=>"Manager (sales manager)"),array("value"=>"50","name"=>"Upper Manager"),array("value"=>"90","name"=>"Administrator")),Array("class"=>"important"));?>
			</label>
		</fieldset>
	</div>
	<fieldset>
		<legend><label for="name">name</label></legend>
		<div style="padding-top:0px;">
			<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"28","maxlength"=>"64","class"=>"important","style"=>"width:99%")); ?>			
		</div>
	</fieldset>
	<fieldset>
		<legend>type</legend>
		<table border="0" cellpadding="0" cellspacing="0" width="99%">
			<tr>
				<td valign="top" width="33%">
					<label for="type1"><input type="radio" id="type1" value="cat" <?php if($therecord["link"]=="") echo "checked" ?> name="radio" onClick="showTypeDetails();"  class="radiochecks" /> category</label>
					<div>
					<img src="menu-example-category.png" width="150" height="72" style="border:2px inset #999999;">
					</div>
				</td>
				<td valign="top" width="33%">
					<label for="type2"><input type="radio" id="type2" value="search" <?php if(strpos($therecord["link"],"search.php?id=")!==false) echo "checked" ?> name="radio" onClick="showTypeDetails();" class="radiochecks" /> table definition search</label>
					<div>
					<img src="menu-example-tabledef.png" width="150" height="72" style="border:spx inset #999999;">
					</div>
				</td>
				<td valign="top" width="33%">
					<label for="type3"><input type="radio" id="type3" value="link" <?php if(strpos($therecord["link"],"search.php?id=")===false && $therecord["link"]!="") echo "checked" ?> name="radio" onClick="showTypeDetails();" class="radiochecks" /> page link</label>
					<div>
					<img src="menu-example-link.png" width="150" height="72" style="border:2px inset #999999;">
					</div>
				</td>
			</tr>		
		</table>
	</fieldset>
	<fieldset id="details">
		<legend>link / parent</legend>
		<label id="thelink" for="link">
			link <em>(URL)</em><br />
			<input id="link" name="link" type="text" value="<?php if(substr($therecord["link"],0,10)!="search.php" ) echo htmlQuotes($therecord["link"])?>" size="64" maxlength="255" />
		</label>
		<label id="thetabledef" for="linkdropdown">
			table definition<br /> 
			<?php displayTableDropDown($therecord["link"]) ?>		
		</label>
		<label>
			parent<br/>
			<?php displayParentDropDown($therecord["parentid"],$therecord["id"]) ?>
		</label>
	</fieldset><script language="javascript">showTypeDetails();</script>
	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php"); ?>
</form>
</body>
</html>