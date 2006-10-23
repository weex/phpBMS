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
	?><input id="<?php echo $name?>" name="<?php echo $name?>" type="text" value="<?php echo htmlQuotes($value) ?>" <?php 
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> /><?php if ($required) { ?><script language="JavaScript" type="text/javascript">requiredArray[requiredArray.length]=new Array('<?php echo $name ?>','<?php echo $message ?>');</script><?php } //end required if 
	if ($type) {?><script language="JavaScript" type="text/javascript"><?php echo $type?>Array[<?php echo $type?>Array.length]=new Array('<?php echo $name ?>','<?php echo $message ?>');</script><?php }//end $type if
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
		echo "<input name=\"".$name."\" type=\"hidden\" value=\"".$value."\" />";
		$name.="forshow";
	}
	
	?><input name="<?php echo $name ?>" id="<?php echo $name ?>" type="checkbox" value="1" <?php 
	if ($value) echo "checked ";
	if ($disabled) echo "disabled=\"true\" ";
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> class="radiochecks" /><?php 
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

	?><select name="<?php echo $name?>" id="<?php echo $name?>" <?php 
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> > <?php
		foreach($list as $theitem){
			$theitem["value"]=str_replace("\"","&quot;",$theitem["value"]);
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

	?><select name="<?php echo $name?>" id="<?php echo $name?>" <?php if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";?> onchange="changeChoiceList(this,'<?php echo $_SESSION["app_path"]?>','<?php echo $listname?>','<?php echo $blankvalue?>');"  onFocus="setInitialML(this)">
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
	$value=str_replace("\"","&quot;",$value);	
	?><input name="<?php echo $name?>" id="<?php echo $name?>" type="text" value="<?php echo $value?>" <?php
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> /><button id="<?php echo $name?>Button" type="button" class="graphicButtons buttonEmail" onclick="openEmail('<?php echo $name?>')" title="Send E-Mail"><span>send e-mail</span></button>
	<script language="JavaScript" type="text/javascript">emailArray[emailArray.length]=new Array('<?php echo $name?>','One or more e-mail fields are invalid.');</script><?php	
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
	$value=str_replace("\"","&quot;",$value);	
	?><input name="<?php echo $name?>" id="<?php echo $name?>" type="text" value="<?php echo $value?>" <?php
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> /><button id="<?php echo $name?>Button" type="button" class="graphicButtons buttonWWW" onclick="openWebpage('<?php echo $name?>')" title="Visit site in new window"><span>visit site</span></button>
	<script language="JavaScript" type="text/javascript">wwwArray[wwwArray.length]=new Array('<?php echo $name?>','One or more web page fields are invalid.');</script>	
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

	if(!is_numeric($value)) $value=0;
	$value=currencyFormat($value);
	
	?><input name="<?php echo $name?>" id="<?php echo $name?>" type="text" value="<?php echo $value?>" <?php
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";
	?> onchange="validateCurrency(this);" style="text-align:right;" /><?php
	if ($required) {?><script language="JavaScript" type="text/javascript">requiredArray[requiredArray.length]=new Array('<?php echo $name?>','<?php echo $message?>');</script><?php }//end required if
}

//============================================================================================
function field_percentage($name,$value,$precision=1,$required=false,$message="",$attributes="") {
	/*
	   name =			Name of the field
	   value =			Value for field 
	   precision =		Number of decimal points to round the percentage
	   required =		true/false wether the field is validated by javascript before submitting for blank values
	   message =		message displayed if not validate						
	   attribute =		Associateive array for extra tag properties.  the key is the attribute and the value is the
						attribute value.
	*/

	if(is_numeric($value)) $value=$value."%";	
	?><input name="<?php echo $name?>" id="<?php echo $name?>" type="text" value="<?php echo $value?>" <?php
	if ($attributes) foreach($attributes as $attribute => $tvalue) if($attribute!="onChange") echo " ".$attribute."=\"".$tvalue."\"";
	?> onchange="validatePercentage(this,<?php echo $precision ?>);<?php if(isset($attributes["onChange"])) echo $attributes["onChange"] ?>" style="text-align:right;" /><?php
	if ($required) {?><script language="JavaScript" type="text/javascript">requiredArray[requiredArray.length]=new Array('<?php echo $name?>','<?php echo $message?>');</script><?php }//end required if
}


//============================================================================================
function field_datepicker($name,$value,$required=0,$message="",$attributes="") {
	/*
	   name =			Name of the field
	   value =			Value for field 
	   required =		true/false wether the field is validated by javascript before submitting for blank values
	   message =		message displayed if not validate						
	   attribute =		Associateive array for extra tag properties.  the key is the attribute and the value is the
						attribute value.
	*/
	?> <input id="<?php echo $name?>" name="<?php echo $name?>" type="text" value="<?php echo $value?>" <?php
	if ($attributes) 
		foreach($attributes as $attribute => $tvalue) 
			if($attribute!="onChange") 
				echo " ".$attribute."=\"".$tvalue."\"";				
	?> onchange="formatDateField(this);<?php if(isset($attributes["onChange"])) echo $attributes["onChange"]?>" /><button id="<?php echo $name?>Button" type="button" class="graphicButtons buttonDate" onclick="showDP('<?php echo $_SESSION["app_path"]?>','<?php echo $name?>');"><span>pick date</span></button>
	<?php if ($required) {?><script language="JavaScript" type="text/javascript">requiredArray[requiredArray.length]=new Array('<?php echo $name?>','<?php echo $message?>');</script><?php }//end if
	?><script language="JavaScript" type="text/javascript">dateArray[dateArray.length]=new Array('<?php echo $name?>','<?php echo $message?>');</script><?php 
}//end function

//============================================================================================
function field_timepicker($name,$value,$required=0,$message="",$attributes="") {
	/*
	   name =			Name of the field
	   value =			Value for field 
	   required =		true/false wether the field is validated by javascript before submitting for blank values
	   message =		message displayed if not validate						
	   attribute =		Associateive array for extra tag properties.  the key is the attribute and the value is the
						attribute value.
	*/
	?> <input id="<?php echo $name?>" name="<?php echo $name?>" type="text" value="<?php echo $value?>" <?php
	if ($attributes) foreach($attributes as $attribute => $tvalue) echo " ".$attribute."=\"".$tvalue."\"";				
	?> /><button id="<?php echo $name?>Button" type="button" class="graphicButtons buttonTime" onclick="showTP('<?php echo $_SESSION["app_path"]?>','<?php echo $name?>');"><span>pick time</span></button>
	<?php if ($required) {?><script language="JavaScript" type="text/javascript">requiredArray[requiredArray.length]=new Array('<?php echo $name?>','<?php echo $message?>');</script><?php }//end if
	?><script language="JavaScript" type="text/javascript">timeArray[timeArray.length]=new Array('<?php echo $name?>','<?php echo $message?>');</script><?php 
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
	<input type="hidden" name="<?php echo $fieldname?>" id="<?php echo $fieldname?>" value="<?php echo $initialvalue?>" />
	<script language="JavaScript" type="text/javascript">
		autofill["<?php echo $fieldname?>"]=new Array();
		autofill["<?php echo $fieldname?>"]["ch"]="";
		autofill["<?php echo $fieldname?>"]["uh"]="";
		autofill["<?php echo $fieldname?>"]["fl"]="<?php echo urlencode(stripslashes($displayfield)) ?>";
		autofill["<?php echo $fieldname?>"]["xt"]="<?php echo urlencode(stripslashes($extrafield)) ?>";
		autofill["<?php echo $fieldname?>"]["td"]=<?php echo urlencode(stripslashes($tabledefid)) ?>;
		autofill["<?php echo $fieldname?>"]["gf"]="<?php echo urlencode(stripslashes($getfield)) ?>";
		autofill["<?php echo $fieldname?>"]["wc"]="<?php echo urlencode(stripslashes($whereclause)) ?>";
		autofill["<?php echo $fieldname?>"]["bo"]=<?php if ($blankout) echo "true"; else echo "false" ?>;
		autofill["<?php echo $fieldname?>"]["vl"]="<?php echo htmlQuotes($displayresult["display"]) ?>";
		appPath="<?php echo $_SESSION["app_path"]?>";
	</script>
	<input type="text" name="ds-<?php echo $fieldname?>" id="ds-<?php echo $fieldname?>"  class="autofillField <?php if(isset($attributes["class"])) echo $attributes["class"] ?>" title="Use '%' for wildcard." <?php 
		if ($attributes) foreach($attributes as $attribute => $tvalue) if($attribute!="class") echo " ".$attribute."=\"".$tvalue."\"";
	?> value="<?php echo htmlQuotes($displayresult["display"]) ?>" onkeyup="autofillChange(this);return true;" onblur="setTimeout('blurAutofill(\'<?php echo $fieldname ?>\')', 50)"  onkeydown="captureKey(event)" />
	<?php if ($required) {
		?><script language="JavaScript" type="text/javascript">
			var display=getObjectFromID("ds-<?php echo $fieldname?>");
			display.autocomplete="off";
			requiredArray[requiredArray.length]=new Array('<?php echo $fieldname?>','<?php echo $message?>');
		</script><?php
	}//end required if
}//end function
?>