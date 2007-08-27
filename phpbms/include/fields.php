<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2007, Kreotek LLC                                  |
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

class phpbmsForm{
	
	var $jsIncludes = array("common/javascript/fields.js");
	var $topJS = array(
					"requiredArray= new Array();",
					"integerArray= new Array();",
					"phoneArray= new Array();",
					"emailArray= new Array();",
					"wwwArray= new Array();",
					"realArray= new Array();",
					"dateArray= new Array();",
					"timeArray= new Array();",
				 );
	var $bottomJS = array();
	
	var $fields = array();
	
	
	function phpbmsForm($action = NULL, $method="post", $name="record", $onsubmit="return validateForm(this);", $dontSubmit = true){
		if ($action == NULL)
			$action = $_SERVER["REQUEST_URI"];
			
		$this->action= $action;
		$this->method = $method;
		$this->name = $name;
		$this->onsubmit = $onsubmit;
		
		$this->dontSubmit = $dontSubmit;
		
	}

	function startForm($pageTitle){

		?><form action="<?php echo str_replace("&","&amp;",$this->action) ?>" method="<?php echo $this->method?>" name="<?php echo $this->name?>" onsubmit="<?php echo $this->onsubmit?>" <?php 
			if(isset($this->enctype)) echo ' enctype="'.$this->enctype.'" ';
		?>><?php 
		if($this->dontSubmit){
			?><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div><?php
		} ?>
		<div id="topButtons"><?php showSaveCancel(1); ?></div>
		<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1><?php	
		
	}//end method
	
	
	function showCreateModify($phpbms, $therecord){
	?>
<div id="createmodifiedby" >
	<div id="savecancel2"><?php showSaveCancel(2)?></div>
	<table>
		<tr id="cmFirstRow">
			<td class="cmTitles">
				<input name="createdby" type="hidden" value="<?php $therecord["createdby"] ?>" />
				<input name="creationdate" type="hidden" value="<?php echo formatFromSQLDatetime($therecord["creationdate"]) ?>"/>
				created			
			</td>
			<td><?php echo htmlQuotes($phpbms->getUserName($therecord["createdby"]))?></td>
			<td><?php echo formatFromSQLDatetime($therecord["creationdate"]) ?></td>
		</tr>
		<tr>
			<td class="cmTitles">
				<input name="modifiedby" type="hidden" value="<?php $therecord["modifiedby"] ?>" />
				<input id="cancelclick" name="cancelclick" type="hidden" value="0" />
				<input name="modifieddate" type="hidden" value="<?php echo formatFromSQLDatetime($therecord["modifieddate"]) ?>"/>
				modified
			</td>
			<td><?php echo htmlQuotes($phpbms->getUserName($therecord["modifiedby"]))?></td>
			<td><?php echo formatFromSQLDatetime($therecord["modifieddate"]) ?></td>
		</tr>
	</table>
</div>
	<?php
	}//end method

	
	function endForm(){
		?></form><?php
	}
	
	
	function addField($inputObject){
		if(is_object($inputObject))
			$this->fields[$inputObject->id] = $inputObject;
	}
	
	
	function showField($fieldname){
		if(isset($this->fields[$fieldname])){
			if(is_object($this->fields[$fieldname])){
				if(method_exists($this->fields[$fieldname],"display"))
					$this->fields[$fieldname]->display();
				else
					echo "Error in form contruction (wrong object): ".$fieldname;
			} else
				echo "Error in form contruction: ".$fieldname;
		}else
			echo "Field Not Defined: ".$fieldname;
	}
	
	
	function jsMerge(){		
		global $phpbms;
		
		$phpbms->jsIncludes = array_merge($phpbms->jsIncludes,$this->jsIncludes);
		$phpbms->topJS = array_merge($this->topJS,$phpbms->topJS);
		$phpbms->bottomJS = array_merge($this->bottomJS,$phpbms->bottomJS);
		
		//next we go through the list of fields
		foreach($this->fields as $field){
			$toAdd = $field->getJSMods();
			
			foreach($toAdd["jsIncludes"] as $jsinclude)
				if(!in_array($jsinclude,$phpbms->jsIncludes))
					$phpbms->jsIncludes[] = $jsinclude;

			$phpbms->topJS = array_merge($phpbms->topJS,$toAdd["topJS"]);
			$phpbms->bottomJS = array_merge($phpbms->bottomJS,$toAdd["bottomJS"]);			
		}
	}
}


//============================================================================================
//============================================================================================
class inputField{
	/*
		id =				id/name of input

		value =			Value of input
		displayName =	Name to displayed in label, and on default messages when not overriden 
		required =		true/false wether the field is validated by javascript before submitting for blank values
		type =			Type of field (integer, phone, email, wwww, real, date) to validate against
	   
		size =			size of the input
		maxlength		max length of the input
	   
		displayLabel		(boolean default = true) use this if you want the object to display a label tag above the input 
	   					when displaying
						
						==overridable variables==
						
		message =		message displayed if not validated
		name =			if your input needs a name different from the id
	   
	   					== variable setting methods ==
						
		setAttribute($name,$values)
						
						Use this method to set an additional HTML property for the input
						e.g. setAttribute("onclick","someJavascriptFunction()")
						
						== methods ==
		getJSMods()
		
						Typically this get called from the form container object, but
						you can use it to get an array of all the Javascript this input affects (include, top JS, and bottom JS)
						
		display()
						
						Use this method to display the input in your page.				
	*/

	var $id;
	var $name;
	var $value;
	
	var $displayName ="";
	var $message = "";
	var $displayLabel = true;
	
	var $_attributes = array();
	
	var $required = false;
	var $type = NULL;
	
	var $jsIncludes = array();
	
	function inputField($id, $value, $displayName = NULL ,$required = false, $type = NULL, $size = 32, $maxlength = 128, $displayLabel = true){
		$this->id = $id;
		$this->name = $id;
		if($displayName == "")
			$this->displayName = $id;
		else
			$this->displayName = $displayName;
			
		if($size)
			$this->_attributes["size"] = $size;
		if($maxlength)
			$this->_attributes["maxlength"] = $maxlength;
		
		$this->displayLabel = $displayLabel;
		
		$this->value = $value;

		$this->required = $required;
		$this->type = $type;
	}
	
	
	function setAttribute($name,$value){
		$this->_attributes[strtolower($name)] = $value;
	}

	
	function getJSMods(){
		$thereturn = array("jsIncludes" => array(), "topJS" => array(), "bottomJS" => array());
		
		foreach($this->jsIncludes as $theinclude)
			$thereturn["jsIncludes"][] = $theinclude;

		if($this->required){
			$message = $this->message;
			if($message == "")
				$message = $this->displayName." cannot be blank.";
			$thereturn["topJS"][] = "requiredArray[requiredArray.length]=new Array(\"".$this->name."\",\"".$message."\");";
		}
		
		if($this->type){
			$message = $this->message;			
			if($message == ""){
				switch($this->type){
					case "integer":
						$message = $this->displayName." must be a valid whole number.";
					break;
					case "real":
						$message = $this->displayName." must be a valid number.";
					break;
					case "phone":
						$message = $this->displayName." must be a valid phone number.";
					break;
					case "www":
						$message = $this->displayName." must be a valid web address.";
					break;
					case "email":
						$message = $this->displayName." must be a valid email address.";
					break;
					case "date":
						$message = $this->displayName." must be a valid date.";
					break;
					case "time":
						$message = $this->displayName." must be a valid time.";
					break;					
				}
			}//end if
			$thereturn["topJS"][] = $this->type."Array[".$this->type."Array.length]=new Array(\"".$this->name."\",\"".$message."\");";
		}
		
		return $thereturn;
	}//end if
	
	
	function displayAttributes(){
		foreach($this->_attributes as $key => $value)
			echo " ".$key."=\"".$value."\"";		
	}
	
	
	function showLabel(){
		?><label for="<?php echo $this->id?>" <?php 
			if(isset($this->_attributes["class"]))
				if(strpos($this->_attributes["class"],"important") !== false)
					echo 'class="important"';
		?>><?php echo $this->displayName?></label><br /><?php
	}
	
	
	function display(){
		
		if($this->displayLabel)
			$this->showLabel();
		
		?><input type="text" id="<?php echo $this->id?>" name="<?php echo $this->name?>" <?php 
			if($this->value !== "") 
				echo " value=\"".htmlQuotes($this->value)."\"";
			$this->displayAttributes();
		?> /><?php
		
		switch($this->type){
			case "email":
				?><button id="<?php echo $this->id?>Button" type="button" class="graphicButtons buttonEmail" onclick="openEmail('<?php echo $this->id?>')" title="Send E-Mail"><span>send e-mail</span></button><?php
			break;
			
			case "www":
				?><button id="<?php echo $this->id?>Button" type="button" class="graphicButtons buttonWWW" onclick="openWebpage('<?php echo $this->id?>')" title="Visit site in new window"><span>visit site</span></button><?php
			break;
		}
		
	}//end method
}//end class



//============================================================================================
class inputCheckbox extends inputField{
	/*
	   value =			Whether the check box is checked
	   disabled =		Whether the check box is checkable
	*/
	function inputCheckbox($id,$value = false, $displayName = NULL, $disabled = false, $displayLabel = true){
		
		parent::inputField($id, $value, $displayName, false, NULL, NULL, NULL, $displayLabel);
		
		if($disabled)
			$this->_attributes["disabled"] = "disabled";		
	}//end method
	
	function showLabel(){
		$classText="";
		if(isset($this->_attributes["class"]))
			if(strpos($this->_attributes["class"],"important") !== false)
				$classText="important";
		if(isset($this->_attributes["disabled"])){
			if($classText!="")
				$classText.=" ";
			$classText.="disabledtext";
		}
		if($classText!="")
			$classText = ' class="'.$classText.'"';
			
		?><label id="<?php echo $this->id?>Label" for="<?php echo $this->id?>" <?php echo $classText?>><?php echo $this->displayName?></label><?php
	}


	function display(){
		?><input type="checkbox" id="<?php echo $this->id?>" name="<?php echo $this->name?>" value="1" class="radiochecks" <?php 
			if($this->value) echo "checked=\"checked\" ";
			$this->displayAttributes();
		?> /> <?php 
		
		if($this->displayLabel)
			$this->showLabel();
	}
}//end class


//============================================================================================
class inputBasicList extends inputField{
	/*
	   list =	associative array of key (display), => value (value) for the option tags
	*/
	function inputBasicList ($id,$value = "",$list = array(), $displayName = NULL, $displayLabel = true){
		parent::inputField($id, $value, $displayName, false, NULL, NULL, NULL, $displayLabel);
		
		$this->thelist = $list;
	}
	
	function display(){
	
		if($this->displayLabel)
			$this->showLabel();
	
		?><select name="<?php echo $this->name?>" id="<?php echo $this->id?>" <?php 
			$this->displayAttributes();		
		?> > <?php
			foreach($this->thelist as $key => $value){
											
				?><option value="<?php echo htmlQuotes($value)?>" <?php if ($value == $this->value) echo " selected=\"selected\" "?> ><?php echo $key?></option><?php echo "\n";

			}//end for
		?></select>
		<?php
	}
}


//============================================================================================
class inputDataTableList extends inputField{
	/*
	   table =			SQL table clause to pull from
	   valuefield =		SQL column clasue to use for the value
	   displayfield		SQL column clause to use for display

	   whereclause = 	SQL WHERE clause (minus the WHERE)
	   orderclasue = 	SQL ORDER BY clause (minus the ORDER BY)
	   hasblank =		boolean, wehterh <none> (0) can be an option
	*/
	
	function inputDataTableList($db, $id, $value, $table, $valuefield, $displayfield, 
								$whereclause = "", $orderclause = "", $hasblank = true, $displayName=NULL, $displayLabel = true){
								
		parent::inputField($id, $value, $displayName, false, NULL, NULL, NULL, $displayLabel);
		
		$this->hasblank = $hasblank;
		$this->db = $db;

		$querystatement = "SELECT (".$valuefield.") AS thevalue, (".$displayfield.") as thedisplay FROM (".$table.")";
		if($whereclause)
			$querystatement.=" WHERE ".$whereclause;
		if($orderclause)
			$querystatement.=" ORDER BY ".$orderclause;
		
		$this->queryresult=$this->db->query($querystatement);

	}//end method
	
	function display(){
		
		if($this->displayLabel)
			$this->showLabel();

		?><select name="<?php echo $this->name?>" id="<?php echo $this->id?>" <?php 
			$this->displayAttributes();
		?> ><?php
			if($this->hasblank)?><option value="0" <?php if ($this->value==0 || $this->value=="") echo " selected=\"selected\" "?>>&lt;none&gt;</option><?php

			while($therecord=$this->db->fetchArray($this->queryresult)){
				?><option value="<?php echo htmlQuotes($therecord["thevalue"])?>" <?php if ($therecord["thevalue"]==$this->value) echo " selected=\"selected\" "?> ><?php echo htmlQuotes($therecord["thedisplay"])?></option>
				<?php
			}
		?></select>
		<?php 		
		
	}
}//end class


//============================================================================================
class inputChoiceList extends inputField{
	/*
	listname = 		name of database list to retrieve
	blankvalue =	What to display for a blank value.
	*/
	function inputChoiceList($db, $id, $value, $listname, $displayName="", $blankvalue="none", $displayLabel = true){
		parent::inputField($id, $value, $displayName, false, NULL, NULL, NULL, $displayLabel);
		
		$this->db = $db;
		$this->listname = $listname;
		$this->blankvalue = $blankvalue;
		
		$querystatement="SELECT thevalue FROM choices WHERE listname=\"".$this->listname."\" ORDER BY thevalue;";
		$this->queryresult = $this->db->query($querystatement);
		
		$this->jsIncludes[] = "common/javascript/choicelist.js";

	}//end method

	function display(){

		if($this->displayLabel)
			$this->showLabel();
		?><select name="<?php echo $this->name?>" id="<?php echo $this->id?>" <?php 
			$this->displayAttributes();
			?> onchange="changeChoiceList(this,'<?php echo APP_PATH?>','<?php echo $this->listname?>','<?php echo $blankvalue?>');"  onfocus="setInitialML(this)">
		<?php 
			$inlist=false;
			while($therecord = $this->db->fetchArray($this->queryresult)){

				$display=$therecord["thevalue"];
				$theclass="";
				$selected="";
				if($therecord["thevalue"]==""){
					$display="&lt;".$this->blankvalue."&gt;";
					$theclass=" class=\"choiceListBlank\" ";
				}
				if($therecord["thevalue"]==$this->value){
					$selected=" selected=\"selected\"";
					$inlist=true;
				}
				if($this->value=="" and $therecord["thevalue"])
				?><option value="<?php echo $therecord["thevalue"]?>" <?php echo $theclass?> <?php echo $selected?>><?php echo $display?></option><?php
			}//end while
			if(!$inlist){
				if ($this->value==""){
					$display="&lt;".$this->blankvalue."&gt;";
					$theclass=" class=\"choiceListBlank\" ";
				}
				else{
					$display=$this->value;
					$theclass="";
				}
				?><option value="<?php echo $this->value?>" <?php echo $theclass?> selected="selected"><?php echo $display?></option><?php					
			}//end if
		?>
	<option value="*mL*" class="choiceListModify">modify list...</option></select><?php 

	}

}//end class


//============================================================================================
class inputCurrency extends inputField{

	function inputCurrency($id, $value, $displayName = NULL ,$required = false, $size = 10, $maxlength = 12, $displayLabel = true){
	
		$type = NULL;
		parent::inputField($id, $value, $displayName,$required, $type, $size, $maxlength, $displayLabel);
	}


	function display(){

		if($this->displayLabel)
			$this->showLabel();

		if(!is_numeric($this->value)) $this->value = 0;
		$this->value = htmlQuotes(numberToCurrency($this->value));
		
		if(!isset($this->_attributes["onchange"])) $this->_attributes["onchange"] = "";
		$this->_attributes["onchange"] = "validateCurrency(this);".$this->_attributes["onchange"];

		if(!isset($this->_attributes["class"])) 
			$this->_attributes["class"] = "";
		else
			$this->_attributes["class"] = " ".$this->_attributes["class"];
		
		$this->_attributes["class"] = "currency".$this->_attributes["class"];

		
		?><input name="<?php echo $this->name?>" id="<?php echo $this->id?>" type="text" value="<?php echo $this->value?>" <?php
			$this->displayAttributes();
		?>/><?php

	}//end method

}//end class


//============================================================================================
class inputTextarea extends inputField{
	
	function inputTextarea($id, $value, $displayName = NULL ,$required = false, $rows = 5, $cols= 48, $displayLabel = true){
		parent::inputField($id, $value, $displayName, $required, NULL, NULL, NULL, $displayLabel);

		unset($this->_attributes["size"]);
		unset($this->_attributes["maxlength"]);

		$this->_attributes["rows"] = $rows;
		$this->_attributes["cols"] = $cols;		
			
	}
	

	function display(){

		if($this->displayLabel)
			$this->showLabel();
		
		?><textarea id="<?php echo $this->id?>" name="<?php echo $this->name?>" <?php 
			$this->displayAttributes();
		?>><?php echo htmlQuotes($this->value)?></textarea><?php
	
	}//end method

}//end class


//============================================================================================
class inputPercentage extends inputField{
	/*
	precision = 	decimal points of accuracy to display
	*/
	function inputPercentage($id, $value, $displayName = NULL , $precision = 1, $required = false, $size = 9, $maxlength = 10, $displayLabel = true){
		
		$this->precision = (int) $precision;
		
		$type = NULL;
		parent::inputField($id, $value, $displayName,$required, $type, $size, $maxlength, $displayLabel);
	}



	function display() {
	
		if($this->displayLabel)
			$this->showLabel();

		if(is_numeric($this->value)) $this->value = $this->value."%";	
		
		if(!isset($this->_attributes["onchange"])) $this->_attributes["onchange"] = "";
		$this->_attributes["onchange"] = "validatePercentage(this,".$this->precision.");".$this->_attributes["onchange"];

		?><input name="<?php echo $this->name?>" id="<?php echo $this->id?>" type="text" value="<?php echo $this->value?>" <?php
			$this->displayAttributes();
		?> style="text-align:right;"/><?php

	}//end methdo

}//end class


//============================================================================================
class inputDatePicker extends inputField{

	function inputDatePicker($id, $value, $displayName = NULL ,$required = false, $size = 10, $maxlength = 15, $displayLabel = true){
		$type = "date";
		
		parent::inputField($id, $value, $displayName,$required, $type, $size, $maxlength, $displayLabel);
		
		$this->jsIncludes[] = "common/javascript/datepicker.js";
	}
	
	function display(){

		if($this->displayLabel)
			$this->showLabel();

		$value = formatFromSQLDate($this->value);
		
		if(!isset($this->_attributes["onchange"])) $this->_attributes["onchange"] = "";
		$this->_attributes["onchange"] = "formatDateField(this);".$this->_attributes["onchange"];
		
		?><input name="<?php echo $this->name?>" id="<?php echo $this->id?>" type="text" value="<?php echo $value?>" <?php
			$this->displayAttributes();
		?>/><button id="<?php echo $this->id?>Button" type="button" class="graphicButtons buttonDate" onclick="showDP('<?php echo APP_PATH?>','<?php echo $this->id?>');"><span>pick date</span></button><?php
		
	}//end method
	
}//end class


//============================================================================================
class inputTimePicker extends inputField{

	function inputTimePicker($id, $value, $displayName = NULL ,$required = false, $size = 10, $maxlength = 15, $displayLabel = true){
		$type = "time";
		
		parent::inputField($id, $value, $displayName,$required, $type, $size, $maxlength, $displayLabel);
				
		$this->jsIncludes[] = "common/javascript/timepicker.js";
	}

	function display(){

		if($this->displayLabel)
			$this->showLabel();

		$value = formatFromSQLTime($this->value);
				
		?><input name="<?php echo $this->name?>" id="<?php echo $this->id?>" type="text" value="<?php echo $value?>" <?php
			$this->displayAttributes();
		?>/><button id="<?php echo $this->id?>Button" type="button" class="graphicButtons buttonTime" onclick="showTP('<?php echo APP_PATH?>','<?php echo $this->id?>');"><span>pick time</span></button><?php
		
	}//end method

}//end class

 
//============================================================================================
class inputRolesList extends inputField{

	function inputRolesList($db,$id,$selected,$displayName = NULL, $required = false, $displayLabel = true){
				
		parent::inputField($id, $selected, $displayName, $required, NULL, NULL, NULL, $displayLabel);

		$this->db = $db;
		
		$querystatement = "SELECT name, id FROM roles WHERE inactive = 0";
		$this->queryresult = $this->db->query($querystatement);
			
	}
	
	
	function display(){
		if($this->displayLabel)
			$this->showLabel();
		
			?><select id="<?php echo $this->id?>" name="<?php echo $this->name?>" <?php $this->displayAttributes();?>>
			<option value="0" <?php if($this->value==0) echo "selected=\"selected\""?>>EVERYONE</option>
			<?php while($therecord = $this->db->fetchArray($this->queryresult)){ ?>
			<option value="<?php echo $therecord["id"]?>" <?php if($this->value==$therecord["id"]) echo "selected=\"selected\""?>><?php echo $therecord["name"]?></option>	
			<?php }?>
			<option value="-100" <?php if($this->value == -100) echo "selected=\"selected\""?>>Administrators</option>
			</select><?php

	}
	
}//end class


//============================================================================================
class inputAutofill extends inputField{
/*
	   initialvalue =	Value for get field (usually and id)
	   tabledefid = 		id of table to pull information from
	   getfield =		Field to match value from
	   displayfield = 	Field to display
	   extrafield =		Extra table information to display on drop down
	   whereclause =	SQL where clause (without WHERE) narrowing search lookup
	   blankout =		Wether to blank out invlaid entries
*/
	function inputAutofill($db, $id, $initialvalue, $tabledefid, $getfield, $displayfield, 
										$extrafield="", $whereclause="", $displayName = NULL, $required=false, 
										$blankout=true, $displayLabel = true)  {
		$size = 32;
		$maxlength = 128;
		
		parent::inputField($id, $initialvalue, $displayName,$required, NULL, $size, $maxlength, $displayLabel);
		
		$this->db = $db;
		
		$this->tabledefid = $tabledefid;
		$this->getfield = $getfield;
		$this->displayfield = $displayfield;
		$this->extrafield = $extrafield;
		$this->whereclause = $whereclause;
		
		$this->blankout = $blankout;
		
		//First let's grab the Table information
		$querystatement = "SELECT maintable,querytable from tabledefs where id=".$tabledefid;	
		$queryresult = $this->db->query($querystatement);
		$tableinfo = $this->db->fetchArray($queryresult);

		$querystatement = "SELECT ".$displayfield." AS display FROM ".$tableinfo["maintable"]." WHERE ".$getfield."=\"".$initialvalue."\" LIMIT 1;";
		$queryresult = $this->db->query($querystatement);

		if($this->db->numRows($queryresult))
			$displayresult = $this->db->fetchArray($queryresult);
		else
			$displayresult["display"]="";
		
		$this->displayValue = $displayresult["display"];
		
	}//end method
	
	
	function getJSMods(){
		$thereturn = array("jsIncludes" => array(), "topJS" => array(), "bottomJS" => array());
		
		$thereturn["jsIncludes"][] = "common/javascript/autofill.js";

		if($this->required){
			$message = $this->message;
			if($message == "")
				$message = $this->displayName." cannot be blank.";
			$thereturn["topJS"][] = "requiredArray[requiredArray.length]=new Array(\"".$this->name."\",\"".$message."\");";
		}
		
		$thereturn["topJS"][] = 'autofill["'.$this->id.'"] = new Array();';
		$thereturn["topJS"][] = 'autofill["'.$this->id.'"]["ch"] = "";';
		$thereturn["topJS"][] = 'autofill["'.$this->id.'"]["uh"] = "";';
		$thereturn["topJS"][] = 'autofill["'.$this->id.'"]["fl"] = "'.urlencode(stripslashes($this->displayfield)).'"';
		$thereturn["topJS"][] = 'autofill["'.$this->id.'"]["xt"] = "'.urlencode(stripslashes($this->extrafield)).'"';
		$thereturn["topJS"][] = 'autofill["'.$this->id.'"]["td"] = '.urlencode(stripslashes($this->tabledefid)).';';
		$thereturn["topJS"][] = 'autofill["'.$this->id.'"]["gf"] = "'.urlencode(stripslashes($this->getfield)).'"';
		$thereturn["topJS"][] = 'autofill["'.$this->id.'"]["wc"] = "'.urlencode(stripslashes($this->whereclause)).'"';
		$thereturn["topJS"][] = 'autofill["'.$this->id.'"]["bo"] = '.(($this->blankout) ? 'true' : 'false').'';
		$thereturn["topJS"][] = 'autofill["'.$this->id.'"]["vl"] = "'.htmlQuotes($this->displayValue).'"';
		$thereturn["topJS"][] = 'appPath = "'.APP_PATH.'"';

		$thereturn["bottomJS"][] = 'var display=getObjectFromID("ds-'.$this->id.'");';
		$thereturn["bottomJS"][] = 'display.autocomplete="off";';								
				
		return $thereturn;
	}//end if


	function showLabel(){
		?><label for="ds-<?php echo $this->id?>"><?php echo $this->displayName?></label><br /><?php
	}

	
	function display(){
	
		if($this->displayLabel)
			$this->showLabel();

		if(!isset($this->_attributes["class"])) 
			$this->_attributes["class"] = "";
		else
			$this->_attributes["class"] = " ".$this->_attributes["class"];
		
		$this->_attributes["class"] = "autofillField".$this->_attributes["class"];

			
		?><input type="hidden" name="<?php echo $this->id?>" id="<?php echo $this->id?>" value="<?php echo $this->value?>" />
			<input type="text" name="ds-<?php echo $this->id?>" id="ds-<?php echo $this->id?>"  title="Use % for wildcard searches." <?php 
			
			$this->displayAttributes();
		
			?> value="<?php echo htmlQuotes($this->displayValue) ?>" onkeyup="autofillChange(this);return true;" onblur="setTimeout('blurAutofill(\'<?php echo $this->id ?>\')', 50)"  onkeydown="captureKey(event)" />
			<?php 
		
	}//end method			

	
}//end class

?>