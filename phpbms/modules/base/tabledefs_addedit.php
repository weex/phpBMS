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

	include("include/tabledefs_functions.php");
	include("include/tabledefs_addedit_include.php");
	
	$pageTitle="Table Definition";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/tabledefs.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/tabledefs.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>


<?php tabledefs_tabs("General",$therecord["id"]);?><div class="bodyline">
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;" /></div>
	<div id="topButtons">
		  <?php showSaveCancel(1); ?>
	</div>
	<h1 id="topTitle"><span><?php echo $pageTitle ?></span></h1>
		  
	
	<fieldset id="fsAttributes">
		<legend>attributes</legend>
		<p>
			<label for="id">id</label><br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" />		
		</p>
		<p>
			<label for="type" class="important">type</label><br />
			<?php fieldBasicList("type",$therecord["type"],Array(Array("name"=>"table","value"=>"table"),Array("name"=>"view","value"=>"view"),Array("name"=>"system","value"=>"system")),Array("class"=>"important"));?>		
		</p>
		<div class="fauxP">
			<label for="ds-moduleid">module</label><br />
			<?php fieldAutofill("moduleid",$therecord["moduleid"],21,"modules.id","modules.name","concat('v',modules.version)","",Array("size"=>"20","maxlength"=>"32"),true,"Module is requred.") ?>		
		</div>
	</fieldset>

	<div id="leftSideDiv">
		<fieldset>
			<legend><label for="displayname">display name</label></legend>
			<p>
				<?php fieldText("displayname",$therecord["displayname"],1,"Display Name cannot be blank.","",Array("size"=>"50","maxlength"=>"64","class"=>"important")); ?>
			</p>				
		</fieldset>
		
		<fieldset>
			<legend>Table</legend>
			<p>
				<label for="maintable">primary table name</label><br />
				<?php fieldText("maintable",$therecord["maintable"],1,"Primary table name cannot be blank.","",Array("size"=>"50","maxlength"=>"64","class"=>"")); ?>
			</p>
			<p>
				<label for="querytable">search/display SQL FROM clause</label><br />
				<textarea id="querytable" name="querytable" rows="3"><?php echo htmlQuotes($therecord["querytable"])?></textarea><br />
				<script language="JavaScript" type="text/javascript">requiredArray[requiredArray.length]=new Array('querytable','Search/Display SQL FROM clause cannot be blank.');</script>
				<span class="notes">
					Note: For simple tables, entering the same information as the primary table name is sufficient.<br />
					For complex data views that invlolve multiple tables, you will want to enter the SQL's FROM clause.<br /><br />
					For example, for invoices, you want to show both the invoice information and the client's name, so you would enter:<br />
					invoices INNER JOIN clients ON invoices.clientid=clients.id
				</span>
			</p>			
		</fieldset>
	
		<fieldset>
			<legend>add/edit options</legend>
			<p>
				<label for="addfile">add record file</label><br />
				<?php fieldText("addfile",$therecord["addfile"],1,"Add file name cannot be blank.","",Array("size"=>"100","maxlength"=>"128")); ?><br />
				<span class="notes">file name, including path from application root, that is used for creating new records.</span>
			</p>
			<p>
				add access (role)<br />
				<?php fieldRolesList("addroleid",$therecord["addroleid"],$dblink)?>
			</p>
			<p>&nbsp;</p>
			<p>
				<label for="editfile">edit record file </label><br />
				<?php fieldText("editfile",$therecord["editfile"],1,"Edit file name cannot be blank.","",Array("size"=>"100","maxlength"=>"128")); ?><br />
				<span class="notes">file name, including path from application root, that is used for editing existing records.</span>			
			</p>
			<p>
				edit access (role)<br />
				<?php fieldRolesList("editroleid",$therecord["editroleid"],$dblink)?>
			</p>
		</fieldset>
		
		<fieldset>
			<legend>search screen options</legend>
			<p>
				<label for="searchroleid">search access (role)</label><br />
				<?php fieldRolesList("searchroleid",$therecord["searchroleid"],$dblink)?>
			</p>
			<p>
				<label for="advsearchroleid">advanced search access (role)</label><br />
				<?php fieldRolesList("advsearchroleid",$therecord["advsearchroleid"],$dblink)?>			
			</p>
			<p>
				<label for="viewsqlroleid">view sql access (role)</label><br />
				<?php fieldRolesList("viewsqlroleid",$therecord["viewsqlroleid"],$dblink)?>
			</p>
			<p>
				<label for="deletebutton">delete button name</label><br />
				<input id="deletebutton" name="deletebutton" type="text" value="<?php echo htmlQuotes($therecord["deletebutton"])?>" size="20" maxlength="20" /><br />
			</p>		
			<p>
				<label for="defaultwhereclause">default search</label> <span class="notes">SQL WHERE clause</span><br />
				<textarea id="defaultwhereclause" name="defaultwhereclause" cols="32" rows="4"><?php echo htmlQuotes($therecord["defaultwhereclause"])?></textarea>			
				<script language="JavaScript" type="text/javascript">requiredArray[requiredArray.length]=new Array('defaultwhereclause','default search cannot be blank.');</script>
			</p>
			
			<p>
				<label for="defaultsortorder">default sort order</label> <span class="notes">SQL ORDER BY clause</span><br />
				<textarea id="defaultsortorder" name="defaultsortorder" cols="32" rows="4"><?php echo htmlQuotes($therecord["defaultsortorder"])?></textarea>			
				<script language="JavaScript" type="text/javascript">requiredArray[requiredArray.length]=new Array('defaultsortorder','default sort order cannot be blank.');</script>
			</p>
			<p>
				Does the default search (above) correspond to a quick search (find drop down) item?<br />
				<input type="radio" id="defaultsearchtypeNone" name="defaultsearchtype" class="radiochecks" value="" <?php if($therecord["defaultsearchtype"]=="") echo "checked=\"checked\""?> onchange="toggleDefaultSearch()" />
				<label for="defaultsearchtypeNone">no</label>&nbsp;
				
				<input type="radio" id="defaultsearchtypeSearch" name="defaultsearchtype" class="radiochecks" value="search" <?php if($therecord["defaultsearchtype"]=="search") echo "checked=\"checked\""?>  onchange="toggleDefaultSearch()" />
				<label for="defaultsearchtypeNone">yes</label>&nbsp;			
			</p>
			<div id="defaultQuickSearch" <?php if($therecord["defaultsearchtype"]=="") echo "style=\"display:none;\""?>>
				<p>
					<label for="defaultcriteriafindoptions">critera: selected find option</label> <span class="notes">(quick search)</span><br/>
					<textarea id="defaultcriteriafindoptions" name="defaultcriteriafindoptions" cols="32" rows="2"><?php echo htmlQuotes($therecord["defaultcriteriafindoptions"])?></textarea>
				
				</p>
				<p>
					<label for="defaultcriteriaselection">criteria: selected search field</label><br />
					<textarea id="defaultcriteriaselection" name="defaultcriteriaselection" cols="32" rows="2" ><?php echo htmlQuotes($therecord["defaultcriteriaselection"])?></textarea>		
				</p>
			</div>
		</fieldset>
	</div>
	
	<?php include("../../include/createmodifiedby.php"); ?>
	
</form>
</div><?php include("../../footer.php");?>
</body>
</html>