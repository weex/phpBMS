<?php 
	require_once("../../include/session.php");
	require_once("../../include/common_functions.php");
	require_once("../../include/fields.php");
	require_once("snapshot_ajax.php");

	require_once("include/notes_addedit_include.php");

	$attachedtableinfo=getAttachedTableDefInfo($therecord["attachedtabledefid"]);
	
?><?PHP $pageTitle="Note/Task/Event"?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js"></script>
<script language="JavaScript" src="../../common/javascript/datepicker.js"></script>
<script language="JavaScript" src="../../common/javascript/timepicker.js"></script>
<script language="JavaScript" src="javascript/notes.js"></script>
</head>
<body onLoad="initialize();"><?php include("../../menu.php")?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<div class="bodyline">
	<div style="float:right;width:150px;"><?php showSaveCancel(1); ?></div>
	<h1 style="margin-right:150px;"><?php echo $pageTitle ?></h1>

	<fieldset style="clear:both;">
		<label for="id" style="float:right">id <br/>
			<input name="id" id="id"  type="text" value="<?php echo $therecord["id"]; ?>" size="8" maxlength="8" readonly="true" class="uneditable"/>
			<input name="parentid" id="parentid" type="hidden" value="<?php echo $therecord["parentid"]; ?>" />
			<input name="thebackurl" id="thebackurl" type="hidden" value="<?php if(isset($_GET["backurl"])) echo $_GET["backurl"]; ?>" />
		</label>
		<label for="type" class="important">type<br />
			<?php basic_choicelist("thetype",$therecord["type"],array(array("value"=>"NT","name"=>"Note"),array("value"=>"TS","name"=>"Task"),array("value"=>"EV","name"=>"Event"),array("value"=>"SM","name"=>"System Message")),Array("class"=>"important","onChange"=>"changeType();","style"=>"width:150px;"));?>
			<input type="hidden" id="typeCheck" name="typeCheck" value="<?php echo $therecord["type"]?>" />		
		</label>
		<label for="title">title<br />
			<?PHP field_text("subject",$therecord["subject"],0,"","",Array("size"=>"28","maxlength"=>"128","class"=>"important","style"=>"width:99%")); ?>				
		</label>		
	</fieldset>
	
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign=top width="44%" style="padding-right:5px;">
				<fieldset>
					<legend><label for="importance" style="padding:0px;">importance / privacy</label></legend>
					<div style="padding-top:0px;">
						<?PHP basic_choicelist("importance",$therecord["importance"],array(array("value"=>"3","name"=>"Highest"),array("value"=>"2","name"=>"High"),array("value"=>"1","name"=>"Medium"),array("value"=>"0","name"=>"Normal"),array("value"=>"-1","name"=>"Low"),array("value"=>"-2","name"=>"Lowest")),Array("onClick"=>"changeType();")); ?>
						&nbsp;<?PHP field_checkbox("private",$therecord["private"])?><label for="private" style="display:inline;padding:0px;">&nbsp;private</label>
					</div>
				</fieldset>

				<fieldset id="thedates">
					<legend>dates</legend>
					<div>
						<label for="startdate" id="starttext" style="padding:0px;padding-left:30px;">start</label>
						<input name="dostart" id="startcheck" type="checkbox" value="1" <?php if($therecord["startdate"]) echo "checked" ?> onClick="dateChecked('start')" class="radiochecks" />
						&nbsp;<?PHP field_datepicker("startdate",$therecord["startdate"],0,"",Array("size"=>"11","maxlength"=>"15","onChange"=>"checkEndDate();setEnglishDates()"));?>	
						&nbsp;<?PHP field_timepicker("starttime",$therecord["starttime"],0,"",Array("size"=>"11","maxlength"=>"15"));?>			
					</div>
					<div>
						<label for="enddate" id="endtext" style="padding:0px;padding-left:30px;">end</label>
						<input name="doend" id="endcheck" type="checkbox" value="1" <?php if($therecord["enddate"]) echo "checked" ?> onClick="dateChecked('end')" class="radiochecks" />
						&nbsp;<?PHP field_datepicker("enddate",$therecord["enddate"],0,"",Array("size"=>"11","maxlength"=>"15"));?>			
						&nbsp;<?PHP field_timepicker("endtime",$therecord["endtime"],0,"",Array("size"=>"11","maxlength"=>"15"));?>			
					</div>
				</fieldset>
				<div id="thecompleted">
					<div>
						<input type="hidden" name="completedChange" id="completedChange" value="<?php echo $therecord["completed"]?>">
						<?PHP field_checkbox("completed",$therecord["completed"],false,Array("onClick"=>"completedCheck()"))?>&nbsp;<label for="completed" id="completedtext" style="display:inline;padding:0px;">completed</label>
						&nbsp;<?PHP field_datepicker("completeddate",$therecord["completeddate"],0,"",Array("size"=>"11","maxlength"=>"15","readonly"=>"true"));?>
					</div>
					<label for="status" id="thestatus">
					   status<br />
					   <?PHP choicelist("status",$therecord["status"],"notestatus"); ?>
					</label>
				</div>
				<fieldset>
					<legend><label for="ds-assignedtoid" style="padding:0px;">assigned to</label></legend>
					<label for="ds-assignedtoid">
						<?PHP autofill("assignedtoid",$therecord["assignedtoid"],9,"users.id","concat(users.firstname,\" \",users.lastname)","\"\"","users.revoked=0",Array("size"=>"20","maxlength"=>"32","style"=>"width:90%")) ?>				
						<input type="hidden" id="assignedtochange" name="assignedtochange" value="<?php echo $therecord["assignedtoid"] ?>" />
					</label>
					<?php if($therecord["assignedbyid"]!=0){ ?>
					<label for="assignedbyid">
						assigned by<br />
						<input value="<?php echo getUserName($therecord["assignedbyid"])?>" readonly="readonly" class="uneditable" style="width:90%">
					</label>
					<?php if($therecord["assignedbyid"]==$_SESSION["userinfo"]["id"]){?>
					<div>
						<button type="button" id="sendemailnotice" class="Buttons" onClick="sendEmailNotice('<?php echo $_SESSION["app_path"]?>')">send e-mail notice</button>
					</div>
					<?php } }?>
					<div>
						<label for="assignedtodate" style="padding:0px;">follow up date</label>
						<?PHP field_datepicker("assignedtodate","",0,"",Array("size"=>"11","maxlength"=>"15"),1);?>
						&nbsp;<?PHP field_timepicker("assignedtotime","",0,"",Array("size"=>"11","maxlength"=>"15"),1);?>
					</div>
				</fieldset>
		
				<input id="attachedtabledefid" name="attachedtabledefid" type="hidden" value="<?PHP echo $therecord["attachedtabledefid"]?>">
				<input id="attachedid" name="attachedid" type="hidden" value="<?PHP echo $therecord["attachedid"]?>">
				<fieldset id="theassociated" style="display:none;">
					<legend>associated with</legend>
						<label for="assocarea">area<br />
							<input id="assocarea" type="text" readonly="true" class="uneditable" value="<?php echo $attachedtableinfo["displayname"];?>" style="width:98%">
						</label>
						<label for="attachedid">record id</label>
						<div style="padding-top:0px;">
							<input id="attachedid" type="text" readonly="true" class="uneditable" value="<?PHP echo $therecord["attachedid"]?>" size="6">&nbsp;
							<input name="link" type="button" class="Buttons" value=" go to record " onClick="document.location='<?php echo $_SESSION["app_path"]?><?PHP echo $attachedtableinfo["editfile"]."?id=".$therecord["attachedid"]; ?>'">
						</div>
				</fieldset>
			<fieldset>
				<label for="location">
					location<br />
					<input name="location" id="location" type="text" value="<?php echo $therecord["location"]?>" style="width:90%"/>
				</label>
				<label for="category">
					category<br />
					<?PHP choicelist("category",$therecord["category"],"notecategories",array("style"=>"width:91%")); ?>
				</label>
			</fieldset>
			</td>
			<td valign=top width="55%">
				<fieldset>
					<legend><label for="content" style="padding:0px;">memo</label></legend>
					<div align="right" style="padding:0px;padding-right:5px;">
						<button id="timeStampButton" type="button" class="invisibleButtons" onClick="timeStamp();">timestamp <img src="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-timestamp.png" align="absmiddle" alt="timestamp" width="16" height="16" border="0" /></button>
					</div>
					<div style="padding-top:0px;">
						<textarea name="content" cols="45" rows="23" id="content" style="width:98%"><?PHP echo $therecord["content"]?></textarea>
						<input name="username" type="hidden" value="<?PHP echo $_SESSION["userinfo"]["firstname"]." ".$_SESSION["userinfo"]["lastname"]?>">
					</div>
				</fieldset>
			</td>
		</tr>
	</table>
	
	<span <?php if($therecord["parentid"]) echo "style=\"display:none;\""?>>
		<fieldset id="therepeat" style="padding-top:7px;">
			<legend>recurrence</legend>
			<input type="hidden" id="repeatchange" name="repeatChanges" value="<?php echo $therecord["repeatdays"]."*".$therecord["repeatfrequency"]."*".$therecord["repeattimes"]."*".$therecord["repeattype"]."*".$therecord["repeatuntildate"]?>" />
			<?PHP field_checkbox("repeat",$therecord["repeat"],false,array("onClick"=>"doRepeat()"))?> <label for="repeat" style="padding:0px;display:inline;">repeat every</label>&nbsp;&nbsp;<span id="repeatoptions">
			<?php field_text("repeatfrequency",$therecord["repeatfrequency"],false,$message="The repeat frequency must be a valid integer","integer",array("size"=>"2","maxlength"=>"3","onKeyup"=>"addS(this)"))?>
			<?php 
				$plural="";
				if($therecord["repeatfrequency"]>1) $plural="s";
			?>
			<select id="repeattype" name="repeattype" style="width:100px;" onChange="changeRepeatType();">
				<option value="Daily" <?php if ($therecord["repeattype"]=="repeatDaily") echo "selected"?>>Day<?php echo $plural?></option>
				<option value="Weekly" <?php if ($therecord["repeattype"]=="repeatWeekly") echo "selected"?>>Week<?php echo $plural?></option>
				<option value="Monthly" <?php if (substr($therecord["repeattype"],0,13)=="repeatMonthly") echo "selected"?>>Month<?php echo $plural?></option>
				<option value="Yearly" <?php if ($therecord["repeattype"]=="repeatYearly") echo "selected"?>>Year<?php echo $plural?></option>
			</select>
			
			<div id="weeklyoptions" <?php if ($therecord["repeattype"]!="repeatweekly"){?>style="display:none;"<?php }?>>
				<span id="wos" style="padding-right:10px;"><input name="wosc" type="checkbox" value="s" <?php if(strpos(" ".$therecord["repeatdays"],"s",0)) echo "checked"?> class="radiochecks" />Sun</span>
				<span id="wom" style="padding-right:10px;"><input name="womc" type="checkbox" value="m" <?php if(strpos(" ".$therecord["repeatdays"],"m",0)) echo "checked"?> class="radiochecks" />Mon</span>
				<span id="wot" style="padding-right:10px;"><input name="wotc" type="checkbox" value="t" <?php if(strpos(" ".$therecord["repeatdays"],"t",0)) echo "checked"?> class="radiochecks" />Tue</span>
				<span id="wow" style="padding-right:10px;"><input name="wowc" type="checkbox" value="w" <?php if(strpos(" ".$therecord["repeatdays"],"w",0)) echo "checked"?> class="radiochecks" />Wed</span>
				<span id="wor" style="padding-right:10px;"><input name="worc" type="checkbox" value="r" <?php if(strpos(" ".$therecord["repeatdays"],"r",0)) echo "checked"?> class="radiochecks" />Thu</span>
				<span id="wof" style="padding-right:10px;"><input name="wofc" type="checkbox" value="f" <?php if(strpos(" ".$therecord["repeatdays"],"f",0)) echo "checked"?> class="radiochecks" />Fri</span>
				<span id="woa" style="padding-right:10px;"><input name="woac" type="checkbox" value="a" <?php if(strpos(" ".$therecord["repeatdays"],"a",0)) echo "checked"?> class="radiochecks" />Sat</span>
			</div>
			<div id="monthlyoptions" style=" <?php if (substr($therecord["repeattype"],0,13)!="repeatMonthly"){?>display:none;<?php }?>margin-bottom:5px;">
				<input type="radio" class="radiochecks" name="rpmo" id="rpmobdt" value="byDate" <?php if (substr($therecord["repeattype"],13)=="byDate"){?>checked<?php }?>/>On the <span id="rpmobydate"></span> of the month.<br />
				<input type="radio" class="radiochecks" name="rpmo" id="rpmobda" value="byDay" <?php if (substr($therecord["repeattype"],13)=="byDay"){?>checked<?php }?>/><span id="rpmobyday"></span> of the month.
			</div>
			<div id="rpuntilforever">
				<input id="rprduntilforever" class="radiochecks" name="rpuntil" type="radio" <?php if($therecord["repeattimes"]==0) echo "checked" ?> value="0" onClick="updateRepeatUntil()"/> <label for="rprduntilforever" style="display:inline;padding:0px;">forever</label>
			</div>
			<div id="rpuntiltimes">
				<input id="rprduntilftimes" class="radiochecks" name="rpuntil" type="radio" <?php if($therecord["repeattimes"]>0) echo "checked" ?> value="1" onClick="updateRepeatUntil()" /> <label for="rprduntilftimes" style="display:inline;padding:0px;">number of times</label>&nbsp;&nbsp;
				<?php 
				$tempvalue="";
				$attribs=array("size"=>"2","maxlength"=>"3");				
				if($therecord["repeattimes"]<1){
					$attribs["class"]="uneditable";
					$attribs["readonly"]="readonly";					
				}
				if($therecord["repeattimes"]>0) $tempvalue=$therecord["repeattimes"];
				field_text("repeattimes",$tempvalue,false,$message="The number of times to repeat must be a valid integer","integer",$attribs)?>
			</div>
			<div id="rpuntildate">
				<input id="rprduntildate" class="radiochecks" name="rpuntil" type="radio" <?php if($therecord["repeattimes"]==-1) echo "checked" ?> value="-1" onClick="updateRepeatUntil()"/> <label for="rprduntildate" style="display:inline;padding:0px;">until</label>&nbsp;&nbsp;
				<?PHP field_datepicker("repeatuntildate",$therecord["repeatuntildate"],0,"",Array("size"=>"11","maxlength"=>"15"));?>
			</div>
			</span>
	</fieldset></span>
	<fieldset id="hasparent" class="box small" <?php if(!$therecord["parentid"]) echo "style=\"display:none;\""?>>
		<legend>recurrence</legend>
		<div>
		This task/event was created from a repeated task/event.  <br />
		Click the <strong>Edit Repeating Options</strong> button to edit the options for the repeatable parent record. 
		<br />
		(Any unsaved changes with the current record will be lost.)
		</div>
		<div><input id="goparent" name="goparent" type="button" value="Edit Repeating Options..." onClick="goParent('<?php echo getAddEditFile(12) ?>')" class="Buttons"></div>
	</fieldset>
	<?php include("../../include/createmodifiedby.php"); ?>
	<?PHP if ($_SESSION["userinfo"]["id"] != $therecord["createdby"] && $therecord["createdby"]!="" && $_SESSION["userinfo"]["id"] != $therecord["assignedtoid"] && $_SESSION["userinfo"]["accesslevel"]<90)
		  echo "<SCRIPT language=\"javascript\">disableSave()</SCRIPT>";?>  
</div>
<?php include("../../footer.php")?>
</form>
</body>
</html>