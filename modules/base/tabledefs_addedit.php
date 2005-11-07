<?php 
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
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
</head>
<body><?php include("../../menu.php")?>


<?php tabledefs_tabs("General",$therecord["id"]);?><div class="bodyline">
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
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
		<label for="moduleid-ds">
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