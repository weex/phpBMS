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

<?PHP if (isset($statusmessage)) {?>
	<div class="standout" style="margin-bottom:3px;"><?PHP echo $statusmessage ?></div>
<?PHP } // end if ?>

<?php tabledefs_tabs("General",$therecord["id"]);?><div class="untabbedbox">
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
	<div style="float:right;width:170px;">
		  <?php include("../../include/savecancel.php"); ?>
		  <div class="box">
		  	<div>
				id<br>
				<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:100%">				
			</div>
			<div>
				type<br>
				<?PHP basic_choicelist("type",$therecord["type"],Array(Array("name"=>"table","value"=>"table"),Array("name"=>"view","value"=>"view"),Array("name"=>"system","value"=>"system")),Array("class"=>"important"));?>
			</div>
			<div>
				module<br>
				<?PHP autofill("moduleid",$therecord["moduleid"],21,"modules.id","modules.name","concat('v',modules.version)","",Array("size"=>"20","maxlength"=>"32","style"=>"width:100%",true,"Module is requred.")) ?>
			</div>
		  </div>
	</div>
	<div style="margin-right:173px;">
		<h1><?php echo $pageTitle ?></h1>
		<div class="important">
			display name<br>
			<?PHP field_text("displayname",$therecord["displayname"],1,"Display Name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","style"=>"width:100%","class"=>"important")); ?>
		</div>
		<div >
			main table name<br>
			<?PHP field_text("maintable",$therecord["maintable"],1,"Main Table cannot be blank.","",Array("size"=>"32","maxlength"=>"64","class"=>"")); ?>			
		</div>
		<div>
			query table name<br>
			<?PHP field_text("querytable",$therecord["querytable"],1,"Query Table cannot be blank.","",Array("size"=>"32","maxlength"=>"255","style"=>"width:100%")); ?>
			<div class=small>
				the name of the table used in the select statement.  For some tables, the default query table name might include two tables names
				with a join statement.
			</div>
		</div>
		<div>
			add file name<br>
			<?PHP field_text("addfile",$therecord["addfile"],1,"Add file name cannot be blank.","",Array("size"=>"64","maxlength"=>"128")); ?>
		</div>
		<div>
			edit file name<br>
			<?PHP field_text("editfile",$therecord["editfile"],1,"Edit file name cannot be blank.","",Array("size"=>"64","maxlength"=>"128")); ?>
		</div>
		<div>
			delete button name<br>
			<input name="deletebutton" type="text" value="<?PHP echo $therecord["deletebutton"]?>" size="32">
		</div>
	</div>
	<div>
		<h2>defaults</h2>
		<div>
			default where clause<br>
			<textarea name="defaultwhereclause" cols="32" rows="3" style="width:100%"><?php echo $therecord["defaultwhereclause"]?></textarea>
		</div>
		<div>
			default search type<br>
			<?PHP basic_choicelist("defaultsearchtype",$therecord["defaultsearchtype"],Array(Array("name"=>"none","value"=>""),Array("name"=>"search","value"=>"search")));?>
		</div>
		<div>
			default critera: find options<br>
			<textarea name="defaultcriteriafindoptions" cols="32" rows="3" style="width:100%"><?php echo $therecord["defaultcriteriafindoptions"]?></textarea>
		</div>
		<div>
			default criteria: selection<br>
			<textarea name="defaultcriteriaselection" cols="32" rows="3" style="width:100%"><?php echo $therecord["defaultcriteriaselection"]?></textarea>
		</div>
		<div>
			default sort order<br>
			<textarea name="defaultsortorder" cols="32" rows="3" style="width:100%"><?php echo $therecord["defaultsortorder"]?></textarea>
		</div>
	</div>
	
	<?php include("../../include/createmodifiedby.php"); ?>
	
</form>
</div>		
</body>
</html>