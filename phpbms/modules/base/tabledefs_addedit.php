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

	include("include/tabledefs_functions.php");
	include("include/tabledefs_addedit_include.php");
	
	$pageTitle="Table Definition";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php require("../../head.php")?>

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
</head>
<body><?php include("../../menu.php")?>


<?php tabledefs_tabs("General",$therecord["id"]);?><div class="bodyline">
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;"></div>
	<div style="float:right;width:160px;">
		  <?php showSaveCancel(1); ?>
	</div>
	<h1 style="margin-right:165px;"><?php echo $pageTitle ?></h1>
		  
	
	<fieldset style="clear:both;float:right;width:200px;">
		<legend>attributes</legend>
		<label for="id">
			id<br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:98%">				
		</label>
		<label for="type">
			type<br />
			<?PHP basic_choicelist("type",$therecord["type"],Array(Array("name"=>"table","value"=>"table"),Array("name"=>"view","value"=>"view"),Array("name"=>"system","value"=>"system")),Array("class"=>"important"));?>
		</label>
		<label for="ds-moduleid">
			module<br />
			<?PHP autofill("moduleid",$therecord["moduleid"],21,"modules.id","modules.name","concat('v',modules.version)","",Array("size"=>"20","maxlength"=>"32","style"=>"width:98%",true,"Module is requred.")) ?>
		</label>
		<label for="deletebutton">
			delete button name<br />
			<input id="deletebutton" name="deletebutton" type="text" value="<?PHP echo htmlQuotes($therecord["deletebutton"])?>" size="20" maxlength="20">
		</label>		
	</fieldset>

	<div style="margin-right:210px;">
		<fieldset>
			<legend><label for="displayname">display name</label></legend>
			<label>
				<?PHP field_text("displayname",$therecord["displayname"],1,"Display Name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","style"=>"width:98%","class"=>"important")); ?>
			</label>
		</fieldset>
		<fieldset>
			<legend>sql</legend>
			<label for="maintable">
				primary table<br />
				<?PHP field_text("maintable",$therecord["maintable"],1,"Main Table cannot be blank.","",Array("size"=>"32","maxlength"=>"64","class"=>"")); ?>			
			</label>
			<label for="querytable">
				sql query table <em>(SQL FROM clause)</em><br />
				<?PHP field_text("querytable",$therecord["querytable"],1,"Query Table cannot be blank.","",Array("size"=>"32","maxlength"=>"255","style"=>"width:98%")); ?>
			</label>
			<div class=small>
				<em>The sql query table represents the FROM clause of the sql statement.  
				For some tables definitions, two tables with a JOIN / ON or separated by comments.</em>
			</div>
		</fieldset>
	</div>
	<fieldset>
		<legend>file references</legend>
		<label for="addfile">
			add file<br />
			<?PHP field_text("addfile",$therecord["addfile"],1,"Add file name cannot be blank.","",Array("size"=>"64","maxlength"=>"128","style"=>"width:98%")); ?>
		</label>
		<label for="editfile">
			edit file<br />
			<?PHP field_text("editfile",$therecord["editfile"],1,"Edit file name cannot be blank.","",Array("size"=>"64","maxlength"=>"128","style"=>"width:98%")); ?>
		</label>		
	</fieldset>
	
	<fieldset>
		<legend>defaults</legend>
		<label for="defaultwhereclause">
			search <em>(SQL WHERE clause)</em><br />
			<textarea id="defaultwhereclause" name="defaultwhereclause" cols="32" rows="3" style="width:98%"><?php echo $therecord["defaultwhereclause"]?></textarea>
		</label>
		<label for="defaultsordorder">
			sort order <em>(SQL ORDER BY clause)</em><br />
			<textarea id="defaultsortorder" name="defaultsortorder" cols="32" rows="3" style="width:98%"><?php echo $therecord["defaultsortorder"]?></textarea>
		</label>
		<label for="defaultsearchtype">
			search type<br />
			<?PHP basic_choicelist("defaultsearchtype",$therecord["defaultsearchtype"],Array(Array("name"=>"none","value"=>""),Array("name"=>"search","value"=>"search")));?>
		</label>
		<label for="defaultcriteriafindoptions">
			critera: selected find option <em>(quick search)</em><br/>
			<textarea id="defaultcriteriafindoptions" name="defaultcriteriafindoptions" cols="32" rows="3" style="width:98%"><?php echo $therecord["defaultcriteriafindoptions"]?></textarea>
		</label>
		<label for="defaultcriteriaselection">
			criteria: selected search field <br />
			<textarea id="defaultcriteriaselection" name="defaultcriteriaselection" cols="32" rows="3" style="width:98%"><?php echo $therecord["defaultcriteriaselection"]?></textarea>
		</label>
	</fieldset>
	
	<?php include("../../include/createmodifiedby.php"); ?>
	
</form>
</div><?php include("../../footer.php");?>
</body>
</html>