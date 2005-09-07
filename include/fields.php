<?PHP
//============================================================================================
// This file houses creation of the input boxes, from booleans to text boxes, to list picks
// most inputs will require the fields.js file to be loaded in the corresponding calling file 
//============================================================================================

// common text input
//============================================================================================
function field_text($name,$value="",$required=false,$message="",$type="",$attributes="") {

	/* 
	   name =		Name of the field
	   value =		Value for field
	   required =	true/false wether the field is validated by javascript before submitting for blank values
	   message =	message displayed if not validate
	   type =		Type of field (integer, phone, email, wwww, real, date) to validate for
	   attribute =	Associateive array for extra tag properties.  the key is the attribute and the value is the
					attribute value;	
	*/
	
	?><input name="<?php echo $name?>"  id="<?php echo $name?>" type="text" value="<?php echo $value ?>" <?php 
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> ><?php if ($required) { ?>
	<script language="JavaScript">requiredArray[requiredArray.length]=new Array('<?php echo $name ?>','<?php echo $message ?>');</script>
	<?php } //end required if 
	if ($type) {?>
	<script language="JavaScript"><?php echo $type?>Array[<?php echo $type?>Array.length]=new Array('<?php echo $name ?>','<?php echo $message ?>');</script>	
	<?php }//end $type if
}//end function


//============================================================================================
function field_checkbox($name,$value="",$disabled=false,$attributes=""){
	/*
	   name =		Name of the field
	   value =		Value for field when checked
	   disabled =	Wethere the check box is checkable
	   attribute =	Associateive array for extra tag properties.  the key is the attribute and the value is the
					attribute value.
	*/
	
	if ($disabled) {
		echo "<input name=\"".$name."\" type=\"hidden\" value=\"".$value."\">";
		$name.="forshow";
	}
	
	?><input name="<?php echo $name ?>" id="<?php echo $name ?>" type="checkbox" value="1" <?php 
	if ($value) echo "checked ";
	if ($disabled) echo "disabled=\"true\" ";
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> class="radiochecks"><?php 
}

//============================================================================================
function basic_choicelist($name,$value="",$list="",$attributes=""){
	/*
	   name =		Name of the field
	   value =		Value for selefted item
	   list =		Array of associateive arrays.  Each
	   				Associateive array house keys name, and value
	   attribute =	Associateive array for extra tag properties.  the key is the attribute and the value is the
					attribute value.
	*/

	?><select name="<?php echo $name?>" <?php 
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> > <?php
		foreach($list as $theitem){
			?><option value="<?php echo $theitem["value"]?>" <?php if ($theitem["value"]==$value) echo " selected "?> ><?php echo $theitem["name"]?></option>
			<?php
		}
	?></select>
	<?php
}

// choicelist
//============================================================================================
function choicelist($name,$value="",$listname,$attributes=array(),$blankvalue="none"){
	error_reporting(E_ALL);
	/*
	name =			Name of the field
	value =			Value for field
	listname = 		name of database list to retrieve
	attribute =		Associateive array for extra tag properties.  the key is the attribute and the value is the
					attribute value.
	blankvalue =	What to display for a blank value.
	*/
	
	global $dblink;
	
	$querystatement="SELECT thevalue FROM choices WHERE listname=\"".$listname."\" ORDER BY thevalue;";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$querystatement) reportError(100,"SQL Statement Could not be executed.");

	?><select name="<?php echo $name?>" id="<?php echo $name?>" <?php if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";?> onChange="changeChoiceList(this,'<?php echo $_SESSION["app_path"]?>','<?php echo $listname?>','<?php echo $blankvalue?>');"  onFocus="setInitialML(this)">
	<?php 
		$inlist=false;
		while($therecord=mysql_fetch_array($queryresult)){
			$display=$therecord["thevalue"];
			$theclass="";
			$selected="";
			if($therecord["thevalue"]==""){
				$display="&lt;".$blankvalue."&gt;";
				$theclass=" class=\"choiceListBlank\" ";
			}
			if($therecord["thevalue"]==$value){
				$selected=" selected ";
				$inlist=true;
			}
			if($value=="" and $therecord["thevalue"])
			?><option value="<?php echo $therecord["thevalue"]?>" <?php echo $theclass?> <?php echo $selected?>><?php echo $display?></option><?php
		}//end while
		if(!$inlist){
			if ($value==""){
				$display="&lt;".$blankvalue."&gt;";
				$theclass=" class=\"choiceListBlank\" ";
			}
			else{
				$display=$value;
				$theclass="";
			}
			?><option value="<?php echo $value?>" <?php echo $theclass?> selected><?php echo $display?></option><?php					
		}//end if
	?>
<option value="*mL*" class="choiceListModify">modify list...</option></select><?php 
}//end function


//============================================================================================
function field_email($name,$value,$attributes){
	/*
	   name =			Name of the field
	   value =			Value for field 
	   attribute =		Associateive array for extra tag properties.  the key is the attribute and the value is the
						attribute value.
	*/
	?> 
	<input name="<?php echo $name?>" type="text" value="<?php echo $value?>" <?php
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> >&nbsp;<a href="" onClick="openEmail('<?php echo $name?>');return false"><img src="<?php echo $_SESSION["app_path"]?>common/image/email.gif" align="absmiddle" alt="send email" width="15" height="15" border="0"></a>
	<script language="JavaScript">emailArray[emailArray.length]=new Array('<?php echo $name?>','One or more e-mail fields are invalid.');</script><?php	
}

//============================================================================================
function field_web($name,$value="http://",$attributes=""){
	/*
	   name =			Name of the field
	   value =			Value for field 
	   attribute =		Associateive array for extra tag properties.  the key is the attribute and the value is the
						attribute value.
	*/
	
	if(!$value) $value="http://";
	?><input name="<?php echo $name?>" id="<?php echo $name?>" type="text" value="<?php echo $value?>" <?php
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?>>&nbsp;<a href="" onClick="openWebpage('<?php echo $name?>');return false"><img src="<?php echo $_SESSION["app_path"] ?>common/image/www.gif" align="absmiddle" alt="visit webpage" width="15" height="15" border="0"></a>
	<script language="JavaScript">wwwArray[wwwArray.length]=new Array('<?php echo $name?>','One or more web page fields are invalid.');</script>	
	<?php
}



//============================================================================================
function field_dollar($name,$value=0,$required=false,$message="",$attributes="") {
	/*
	   name =			Name of the field
	   value =			Value for field 
	   required =		true/false wether the field is validated by javascript before submitting for blank values
	   message =		message displayed if not validate						
	   attribute =		Associateive array for extra tag properties.  the key is the attribute and the value is the
						attribute value.
	*/

	if(is_numeric($value)) $value="\$".number_format($value,2);
	else $value="\$0.00";
	
	?> <input name="<?php echo $name?>" id="<?php echo $name?>" type="text" value="<?php echo $value?>" <?php
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> onChange="validateDollar(this);" style="text-align:right;" ><?php
	if ($required) {?>
		echo "<script language="JavaScript">requiredArray[requiredArray.length]=new Array('<?php echo $name?>','<?php echo $message?>');</script>
	<?php }//end required if
}


//============================================================================================
function field_cal($name,$value,$required=0,$message="",$attributes="",$showtime=false) {
	/*
	   name =			Name of the field
	   value =			Value for field 
	   required =		true/false wether the field is validated by javascript before submitting for blank values
	   message =		message displayed if not validate						
	   attribute =		Associateive array for extra tag properties.  the key is the attribute and the value is the
						attribute value.
	   showtime = 		Boolean wether to display the time or not.
	*/
	?> <input name="<?php echo $name?>" type="text" value="<?php echo $value?>" <?php
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> >&nbsp;<a href="javascript:cal<?php echo $name?>.popup();"><img src="<?php echo $_SESSION["app_path"]?>common/image/cal.gif" alt="Click Here to pick date" width="16" height="16" border="0" align="absmiddle"></a>
	<script language="JavaScript">
	var cal<?php echo $name?> = new calendar2(document.forms['record'].elements['<?php echo $name?>']);
	cal<?php echo $name?>.year_scroll = true;
	cal<?php echo $name?>.time_comp = <?php 
	if ($showtime) echo "true;\n";
	else echo "false;\n"; ?>
	cal<?php echo $name?>.basepath="<?php echo $_SESSION["app_path"]?>";
	</script>	
	<?php if ($required) {?>
		<script language="JavaScript">requiredArray[requiredArray.length]=new Array('<?php echo $name?>','<?php echo $messsage?>');</script>
	<?php }//end if
	if(!$showtime) {?>
		<script language="JavaScript">dateArray[dateArray.length]=new Array('<?php echo $name?>','<?php echo $message?>');</script>
	<?php }
}//end function

//============================================================================================
function autofill($fieldname,$initialvalue,$tabledefid,$getfield,$displayfield,$extrafield="",$whereclause="",$attributes="",$required=false,$message="",$blankout=true){
	/*
	   fieldname =		Name(id) of the input 
	   initialvalue =	Value for get field (usually and id)
	   tabledefid = 		id of table to pull information from
	   getfield =		Field to match value from
	   displayfield = 	Field to display
	   extrafield =		Extra table information to display on drop down
	   whereclause =	SQL where clause (without WHERE) narrowing search lookup
	   attributes =		Associateive array for extra tag properties.  the key is the attribute and the value is the
						attribute value.
	   required =		true/false wether the field is validated by javascript before submitting for blank values
	   message =		message displayed if not validate						
	   blankout =		Wether to blank out invlaid entries
	*/
	
	global $dblink;
	
	//First let's grab the Table information
	$querystatement="SELECT maintable,querytable from tabledefs where id=".$tabledefid;	
	$queryresult=mysql_query($querystatement,$dblink);
	$tableinfo=mysql_fetch_array($queryresult);
	
	$querystatement="SELECT ".$displayfield." AS display FROM ".$tableinfo["maintable"]." WHERE ".$getfield."=\"".$initialvalue."\" LIMIT 1;";
	$queryresult = mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(100,"Could not retrieve autofill inital data.<br>".$querystatement);
	if(mysql_num_rows($queryresult))
		$displayresult = mysql_fetch_array($queryresult);
	else
		$displayresult["display"]="";

	?>
	<input type="hidden" name="<?php echo $fieldname?>" id="<?php echo $fieldname?>" value="<?php echo $initialvalue?>">
	<script language="javascript">
		autofill["<?php echo $fieldname?>"]=new Array();
		autofill["<?php echo $fieldname?>"]["ch"]="";
		autofill["<?php echo $fieldname?>"]["uh"]="";
		autofill["<?php echo $fieldname?>"]["fl"]="<?php echo urlencode(stripslashes($displayfield)) ?>";
		autofill["<?php echo $fieldname?>"]["xt"]="<?php echo urlencode(stripslashes($extrafield)) ?>";
		autofill["<?php echo $fieldname?>"]["td"]=<?php echo urlencode(stripslashes($tabledefid)) ?>;
		autofill["<?php echo $fieldname?>"]["gf"]="<?php echo urlencode(stripslashes($getfield)) ?>";
		autofill["<?php echo $fieldname?>"]["wc"]="<?php echo urlencode(stripslashes($whereclause)) ?>";
		autofill["<?php echo $fieldname?>"]["bo"]=<?php if ($blankout) echo "true"; else echo "false" ?>;
		appPath="<?php echo $_SESSION["app_path"]?>";
	</script>
	<input autocomplete="off" type="text" name="ds-<?php echo $fieldname?>" id="ds-<?php echo $fieldname?>" <?php 
		if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> value="<?php echo $displayresult["display"] ?>" onKeyUp="autofillChange(this);return true;" onBlur="blurAutofill(this)"  onKeyDown="captureKey(event)" class="autofillField">
	<div id="dd-<?php echo $fieldname?>" class="autofillDropDown" style="position:absolute;white-space:nowrap;display:none;"></div>
	<?php if ($required) {
		?><script language="JavaScript">
			requiredArray[requiredArray.length]=new Array('<?php echo $fieldname?>','<?php echo $message?>');
			autofill["<?php echo $fieldname?>"]["vl"]=<? echo $displayresult ?>;
		</script><?php
	}//end required if
}//end function

?>