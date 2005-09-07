<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/notes_addedit_include.php");

	if($therecord["attachedtabledefid"])
		$attachedtableinfo=getAttachedTableDefInfo($therecord["attachedtabledefid"]);
	
?>

<?PHP $pageTitle="Note"?>

<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/common.js"></script>
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
<script language="JavaScript" src="../../common/javascript/cal.js"></script>
<script language="JavaScript" >
	function timeStamp(){
		var theDate= new Date()
		var theform=document.forms["record"]
		theform["content"].value=theform["content"].value+"[ "+theform["username"].value+" - "+theDate.toLocaleString()+" ]"
	}
	function checkStatus(statusfield){
		var theform=document.forms["record"]
		if(statusfield.value=="system"){
			theform["assignedtoid"].value="";
			theform["ds-assignedtoid"].disabled=true;
			theform["beenread"].checked=false;
			theform["beenread"].disabled=true;			
			theform["followup"].disabled=true;			
			theform["followup"].value="";			
			theform["assignedtoid"].value="";			
			theform["ds-assignedtoid"].value="";			
			theform["ds-assignedtoid"].disabled=true;			
		} else {
			theform["beenread"].disabled=false;			
			theform["followup"].disabled=false;			
			theform["ds-assignedtoid"].disabled=false;
		}
	}
</script>
</head>
<body><?php include("../../menu.php")?>
<?PHP if (isset($statusmessage)) {?>
	<div class="standout" style="margin-bottom:3px;"><?PHP echo $statusmessage ?></div>
<?PHP } // end if ?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<div class="bodyline">
	<div style="float:right;width:180px;">

			<?php include("../../include/savecancel.php"); ?>
			<div class="box">
				<div>
					id<br>
					<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:100%">
				</div>
				<div>
					note type<br>
					  <?PHP 
						if($therecord["type"]!="record")
							basic_choicelist("type",$therecord["type"],array(array("value"=>"personal","name"=>"Personal"),array("value"=>"system","name"=>"System Wide")),Array("class"=>"important","onChange"=>"checkStatus(this);","style"=>"width:150px;"));
						else {
					  ?>
						<input name="type" type="text" value="<?PHP echo $therecord["type"]?>" size="20" maxlength="32" readonly="true" class="uneditable" style="text-align:center;width:100%">			  
					  <?PHP } ?>
				</div>
				<div>
					importance<br>
					<?PHP basic_choicelist("importance",$therecord["importance"],array(array("value"=>"High","name"=>"High"),array("value"=>"Medium","name"=>"Medium"),array("value"=>"Normal","name"=>"Normal"),array("value"=>"Low","name"=>"Low")),Array("class"=>"important","style"=>"width:150px")); ?>
				</div>
				<div align="center">
					<strong>mark as read&nbsp;</strong><?PHP field_checkbox("beenread",$therecord["beenread"])?>
				</div>
			</div>

			<div class=box>
				<div>
					assigned to<br>
					<?PHP autofill("assignedtoid",$therecord["assignedtoid"],9,"users.id","concat(users.firstname,\" \",users.lastname)","\"\"","users.revoked=0",Array("size"=>"20","maxlength"=>"32","style"=>"width:100%")) ?>				
				</div>
				<div>
					follow up<br>
					<?PHP field_cal("followup",$therecord["followup"],0,"",Array("size"=>"20","maxlength"=>"32","readonly"=>"true","onClick"=>"calfollowup.popup()"),1);?>
				</div>
			</div>
			
			<input name="attachedtabledefid" 	type="hidden" value="<?PHP echo $therecord["attachedtabledefid"]?>">
			<input name="attachedid" 			type="hidden" value="<?PHP echo $therecord["attachedid"]?>">
			<?php if($therecord["attachedtabledefid"]){?>
			<div class="box">
					<div>
						<strong>note associated with...</strong><br>
						<input type="text" readonly="true" class="uneditable" value="<?php echo $attachedtableinfo["displayname"];?>" style="width:100%">
					</div>
					<div>
						associated record id<br> 
						<input type="text" readonly="true" class="uneditable" value="<?PHP echo $therecord["attachedid"]?>" style="width:100%">
					</div>
					<div align=right>
						<input name="link" type="button" class="Buttons" value=" go to record " onClick="document.location='<?php echo $_SESSION["app_path"]?><?PHP echo $attachedtableinfo["editfile"]."?id=".$therecord["attachedid"]; ?>'">									
					</div>
			</div>
		<?PHP }// end if ?>	
	</div>

	<div style="margin-right:180px;">
	  <div class="addedittitle"><?php echo $pageTitle ?></div>			
	  <div>
		  subject<br>
		  <?PHP field_text("subject",$therecord["subject"],0,"","",Array("size"=>"28","maxlength"=>"64","class"=>"important","style"=>"width:100%")); ?>
		  <script language="javascript">var thesubject=getObjectFromID("subject");thesubject.focus();</script>
	  </div>
	  <div>
	  	<input name="username" type="hidden" value="<?PHP echo $_SESSION["userinfo"]["firstname"]." ".$_SESSION["userinfo"]["lastname"]?>">
		content&nbsp;<a href="javascript:timeStamp();"><img src="../../common/image/timestamp.gif" width="15" height="15" border="0" align="absmiddle"></a><br>
		<textarea name="content" cols="45" rows="23" id="content" style="width:100%"><?PHP echo $therecord["content"]?></textarea>
	  </div>

 
  <?PHP 
  	  if ($_SESSION["userinfo"]["id"] != $therecord["createdby"] && $therecord["createdby"]!="" && $_SESSION["userinfo"]["id"] != $therecord["assignedtoid"] && $_SESSION["userinfo"]["accesslevel"]<90)
	  echo "<SCRIPT>document.forms['record']['save'].disabled=true</SCRIPT>";
  ?>  
</div>
<div><?php include("../../include/createmodifiedby.php"); ?></div>
</div>
</form>
</body>
</html>