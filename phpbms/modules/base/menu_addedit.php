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
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/menus.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="javascript/menu.js"></script>
</head>
<body><?php include("../../menu.php")?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;"></div>
<div class="bodyline">
	<div id="topButtons">
		<?php showSaveCancel(1); ?>
	</div>
	<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1>
	
	<fieldset id="fsAttributes">
		<legend>attributes</legend>
		<p>
			<label for="id">id</label><br />
			<input id="id" name="id" type="text" value="<?php echo htmlQuotes($therecord["id"]); ?>" size="10" maxlength="10" readonly="true" class="uneditable" />
		</p>
		
		<p>
			<label for="displayorder">display order</label><br />
			 <?PHP field_text("displayorder",$therecord["displayorder"],1,"Display order cannot be blank and must be a valid integer.","integer",Array("size"=>"9","maxlength"=>"3")); ?><br>
			 <span class="notes"><strong>Note:</strong> Lower numbers are displayed first.</span>
		</p>
		
		<p>
			<label for="accesslevel">access level</label><br />
			<?php basic_choicelist("accesslevel",$therecord["accesslevel"],array(array("value"=>"-10","name"=>"portal access only"),array("value"=>"10","name"=>"basic user (shipping)"),array("value"=>"20","name"=>"Power User (sales)"),array("value"=>"30","name"=>"Manager (sales manager)"),array("value"=>"50","name"=>"Upper Manager"),array("value"=>"90","name"=>"Administrator")),Array("class"=>"important"));?>		
		</p>
	</fieldset>
	
	<div id="leftSideDiv">
		<fieldset>
			<legend><label for="name">name</label></legend>
			<p><br />
				<?PHP field_text("name",$therecord["name"],1,"Name cannot be blank.","",Array("size"=>"28","maxlength"=>"64","class"=>"important","style"=>"width:99%")); ?>			
			</p>
		</fieldset>

		<fieldset>
			<legend>type</legend>
			<p class="typeP">
				<br />
				<input type="radio" id="type1" value="cat" <?php if($therecord["link"]=="") echo "checked" ?> name="radio" onClick="showTypeDetails();"  class="radiochecks" /><label for="type1">category</label><br />
				<img src="menu-example-category.png" width="150" height="72" class="typeImage">
			</p>
			
			<p class="typeP">
				<br />
				<input type="radio" id="type2" value="search" <?php if(strpos($therecord["link"],"search.php?id=")!==false) echo "checked" ?> name="radio" onClick="showTypeDetails();" class="radiochecks" /><label for="type2">table definition search</label><br />
				<img src="menu-example-tabledef.png" width="150" height="72" class="typeImage">
			</p>

			<p>
				<br />
				<input type="radio" id="type3" value="link" <?php if(strpos($therecord["link"],"search.php?id=")===false && $therecord["link"]!="") echo "checked" ?> name="radio" onClick="showTypeDetails();" class="radiochecks" /><label for="type3">page link</label><br />
				<img src="menu-example-link.png" width="150" height="72" class="typeImage">
			</p>
		</fieldset>
	</div>

	<div id="details">
		<fieldset>
			<legend>link / parent</legend>
			<p id="thelink">
				<label for="link">link</label> <span class="notes">(URL)</span><br />
				<input id="link" name="link" type="text" value="<?php if(substr($therecord["link"],0,10)!="search.php" ) echo htmlQuotes($therecord["link"])?>" size="64" maxlength="255" />				
			</p>
			<p id="thetabledef">
				<label  for="linkdropdown">table definition</label><br /> 
				<?php displayTableDropDown($therecord["link"]) ?>		
			</p>
			<p>
				parent<br/>
				<?php displayParentDropDown($therecord["parentid"],$therecord["id"]) ?>
			</p>
		</fieldset>
	</div>
	<script language="javascript">showTypeDetails();</script>
	<?php include("../../include/createmodifiedby.php"); ?>
</div>
<?php include("../../footer.php"); ?>
</form>
</body>
</html>