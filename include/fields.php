<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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


// phpBMS form handles the creation and display of most forms n phpBMS
// it is a necessity in order to correctly implement any of the special
// input fields ad verification
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
                "onchangeArray = new Array();"
                );

    var $bottomJS = array();

    var $fields = array();

    var $onload = array();

    function phpbmsForm($action = NULL, $method="post", $name="record", $onsubmit="return validateForm(this);", $dontSubmit = true){

        if ($action == NULL)
            $action = $_SERVER["REQUEST_URI"];

        $this->action= $action;
        $this->method = $method;
        $this->name = $name;
        $this->onsubmit = $onsubmit;

        $this->dontSubmit = $dontSubmit;

    }//end function init (phpbmsForm)


    //creates the form tag, displays the top save and cancel buttons
    // and include the page title
    function startForm($pageTitle){

        ?><form action="<?php echo htmlentities($this->action) ?>" method="<?php echo $this->method?>" name="<?php echo $this->name?>" <?php
                if($this->onsubmit !== NULL) { ?>onsubmit="<?php echo $this->onsubmit?>" <?php }
                if(isset($this->enctype)) echo ' enctype="'.$this->enctype.'" ';
                if(isset($this->id)) echo ' id="'.$this->id.'" ';
        ?>><?php
        if($this->dontSubmit){
                ?><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div><?php
        } ?>
        <div id="topButtons"><?php showSaveCancel(1); ?></div>
        <h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1><?php

    }//end function startFrom


    // Displays the bottom record details that are present on almost all phpBMS
    // records.  These are non-modifiable
    function showGeneralInfo($phpbms, $therecord){
        ?>
<div id="createmodifiedby" >
    <table>
        <tbody>
            <tr class="topRows">
                <td class="cmTitles">
                        <input name="createdby" type="hidden" value="<?php $therecord["createdby"] ?>" />
                        <input name="creationdate" type="hidden" value="<?php echo formatFromSQLDatetime($therecord["creationdate"]) ?>"/>
                        created
                </td>
                <td><?php echo htmlQuotes($phpbms->getUserName($therecord["createdby"]))?></td>
                <td><?php echo formatFromSQLDatetime($therecord["creationdate"]) ?></td>
                <td id="cmButtonContainer" rowspan="3">
                    <?php showSaveCancel(2)?>
                </td>
            </tr>
            <tr class="topRows">
                <td class="cmTitles">
                        <input name="modifiedby" type="hidden" value="<?php $therecord["modifiedby"] ?>" />
                        <input id="cancelclick" name="cancelclick" type="hidden" value="0" />
                        <input name="modifieddate" type="hidden" value="<?php echo formatFromSQLDatetime($therecord["modifieddate"]) ?>"/>
                        modified
                </td>
                <td><?php echo htmlQuotes($phpbms->getUserName($therecord["modifiedby"]))?></td>
                <td><?php echo formatFromSQLDatetime($therecord["modifieddate"]) ?></td>
            </tr>
            <tr>
                <td class="cmTitles">
                        uuid / id
                        <input name="uuid" id="uuid" type="hidden" value="<?php if(isset($therecord["uuid"])) echo $therecord["uuid"] ?>" />
                        <input id="id" name="id" type="hidden" value="<?php echo $therecord["id"]?>" />
                </td>
                <td colspan="2" id="cmIds"><span><?php echo isset($therecord["uuid"])?$therecord["uuid"]:'&nbsp;' ?></span><span id="cmId"><?php echo $therecord["id"] ?></span></td>
            </tr>
        </tbody>
    </table>
</div>
        <?php
    }//end function showGeneralInfo


    //placeholder end form function for consistency (helps editors with HTML
    // validation)
    function endForm(){

        ?></form><?php

    }//end function endForm


    //adds a phpBMS input field to the form
    function addField($inputObject){

        if(is_object($inputObject))
            $this->fields[$inputObject->id] = $inputObject;

    }//end function addField


    //given a field's unique name (to the form object)
    //output the HTML used to display the field
    function showField($fieldname){

        //check to see if the form element even exists
        if(isset($this->fields[$fieldname])){

            //check to see if the field is a valid boject
            if(is_object($this->fields[$fieldname])){

                //check to see if it has a display method
                if(method_exists($this->fields[$fieldname],"display"))
                    $this->fields[$fieldname]->display();
                else
                    echo "Error in form construction (wrong object): ".$fieldname;

            } else
                echo "Error in form construction: ".$fieldname;

        }else
            echo "Field Not Defined: ".$fieldname;

    }//end function showField


    // merges includes, top, bottom and onload javascripts that may have been
    // generateed for individual fiels, with the corresponding main phpbms
    // javascript sections.
    //
    // top and bottom are depreciated. Everything should eventually go through
    // either an include javascript file, or an onload (document) event
    function jsMerge(){

        global $phpbms;

        $phpbms->jsIncludes = array_merge($phpbms->jsIncludes,$this->jsIncludes);
        $phpbms->topJS = array_merge($this->topJS,$phpbms->topJS);
        $phpbms->bottomJS = array_merge($this->bottomJS,$phpbms->bottomJS);
        $phpbms->onload = array_merge($this->onload,$phpbms->onload);

        //next we go through the list of fields
        foreach($this->fields as $field){

            $toAdd = $field->getJSMods();

            // only add an include if it is not already in the list
            // of includes.  Don't want to redefine stuff in Javascript
            foreach($toAdd["jsIncludes"] as $jsinclude)
                if(!in_array($jsinclude,$phpbms->jsIncludes))
                    $phpbms->jsIncludes[] = $jsinclude;

            $phpbms->topJS = array_merge($phpbms->topJS,$toAdd["topJS"]);
            $phpbms->bottomJS = array_merge($phpbms->bottomJS,$toAdd["bottomJS"]);
            $phpbms->onload = array_merge($phpbms->onload,$toAdd["onload"]);

        }//endforeach

    }//end method - jsMerge

    // defines and adds fields specified by administratively set custom fields
    // the table's object should provide the queryresult that has all defined
    // custom field information.  Make sure not to forget the record information
    function prepCustomFields($db, $queryresult, $therecord){

        while ($fieldInfo = $db->fetchArray($queryresult)){

            $id = $fieldInfo["field"];
            $name = $fieldInfo["name"];
            $required = ((bool) $fieldInfo["required"]);
            $format = ($fieldInfo["format"]) ? $fieldInfo["format"] : null;
            $size = "40";
            $value = (isset($therecord[$id])) ? $therecord[$id] : "";

            //need to handle roleid
            $disabled = !(hasRights($fieldInfo["roleid"]));

            //different custom fields (based on number) have different types
            switch(substr($id, 6)){

                case 1:
                case 2:
                    if($value === "")
                        $value = 0;

                    if($format == "currency")
                        $theinput = new inputCurrency($id, $value, $name, $required);
                    else
                        $theinput = new inputField($id, $value, $name, $required, $format, 8, 128);

                    $generator = true;

                    if($disabled) {

                        $theinput->setAttribute("readonly","readonly");
        		$theinput->setAttribute("class","uneditable");
                        $generator = false;

                    }//endif
                    break;

                case 3:
                case 4:
                    if($disabled){

                        $theinput = new inputField($id, $value, $name, $required, null, 10, 15);
                        $theinput->setAttribute("readonly","readonly");
                        $theinput->setAttribute("class","uneditable");
                        $generator = false;

                    } else {

                        if($format == "date")
                            $theinput = new inputDatePicker($id, $value, $name, $required);
                        else{

                            $value = explode(" ", $value);
                            $value = (count($value) > 1) ? $value[1] : "";
                            $theinput = new inputTimePicker($id, $value, $name, $required);

                        }//endif

                        $generator = true;

                    }//endif

                    break;

                case 5:
                case 6:
                    if($format == "list" && !$disabled){

                        $theinput = new inputChoiceList($db, $id, $value, $id."-".$fieldInfo["tabledefid"], $name);
                        $generator = false;

                    } else {

                        $theinput = new inputField($id, $value, $name, $required, $format, 40, 254);
                        $generator = true;

                    }//endif

                    if($disabled){

                        $theinput->setAttribute("readonly","readonly");
        		$theinput->setAttribute("class","uneditable");
                        $generator = false;

                    }//endif
                    break;

                case 7:
                case 8:
                    $generator = false;
                    $theinput = new inputCheckbox($id, $value, $name, $disabled);
                    break;

            }//endswitch

            //need to handle creation of onload js for generator, but only if type
            // not = checkbox or list.
            if($generator && $fieldInfo["generator"])
                $this->onload[] = "var ".$id."Button = getObjectFromID('".$id."Button'); connect(".$id."Button, 'onclick', function(){var ".$id." = getObjectFromID('".$id."');".$id.".value = ".$fieldInfo["generator"]."})";

            $this->addField($theinput);

        }//endwile

        //rewind the queryresult pointer (if not false)
        if($queryresult)
            $db->seek($queryresult, 0);

    }//end function prepCustomFields


    //show (HTML) the custom fields
    //in their own fieldset
    function showCustomFields($db, $queryresult){

        if(!$queryresult)
            return false;

        if($db->numRows($queryresult)){

        ?><fieldset id="customFields">
            <legend>Additional Information</legend>
            <?php

                while ($fieldInfo = $db->fetchArray($queryresult)){

                    ?><p><?php $this->showField($fieldInfo["field"]) ?>
                    <?php
                        //if the field has a gnerator javascript, let's add the button
                        if($fieldInfo["generator"] && hasRights($fieldInfo["roleid"]) && $fieldInfo["format"] != "list"){

                            ?><button class="Buttons" type="button" id="<?php echo $fieldInfo["field"]?>Button">generate</button><?php

                        }//endif
                    ?>
                    </p><?php

                }//endwhile

            ?>
        </fieldset>
        <?php

        }//endif

        return true;

    }//end function showCustomFields

}//end class phpbmsForm



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
		$thereturn = array("jsIncludes" => array(), "topJS" => array(), "bottomJS" => array(), "onload" => array());

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
	   hasblank =		boolean, whether <none> (0) can be an option
	*/

	function inputDataTableList($db, $id, $value, $table, $valuefield, $displayfield,
								$whereclause = "", $orderclause = "", $hasblank = true, $displayName=NULL, $displayLabel = true, $blankValue = 0){

		parent::inputField($id, $value, $displayName, false, NULL, NULL, NULL, $displayLabel);

		$this->hasblank = $hasblank;
		$this->db = $db;
        $this->blankValue = $blankValue;

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
			if($this->hasblank){
				?><option value="<?php echo($this->blankValue); ?>" <?php
				if ($this->value==0 || $this->value==""){
					echo " selected=\"selected\" ";
				}//end if --value--
				?>>&lt;none&gt;</option><?php
			}//end if --hasblank--

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
			?> onchange="changeChoiceList(this,'<?php echo APP_PATH?>','<?php echo $this->listname?>','<?php echo $this->blankvalue?>');"  onfocus="setInitialML(this)">
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

        $querystatement = "
            SELECT
                name,
                uuid
            FROM
                roles
            WHERE
                inactive = 0";

        $this->queryresult = $this->db->query($querystatement);

    }//end function init


    function display(){

            if($this->displayLabel)
                $this->showLabel();

            ?>
            <select id="<?php echo $this->id?>" name="<?php echo $this->name?>" <?php $this->displayAttributes();?>>
                <option value="" <?php if($this->value == "") echo 'selected="selected"' ?>>EVERYONE</option>
                <?php
                    while($therecord = $this->db->fetchArray($this->queryresult)){ ?>
                    <option value="<?php echo $therecord["uuid"]?>" <?php if($this->value == $therecord["uuid"]) echo 'selected="selected"'?>><?php echo formatVariable($therecord["name"])?></option>
                <?php }//endwhile ?>
                <option value="Admin" <?php if($this->value == "Admin") echo 'selected="selected"'?>>Administrators</option>
            </select>
            <?php

    }//end function display

}//end class inputRolesList


class inputSmartSearch extends inputField{

/*
		*db = 			(dbObj)		Database Object
		*id =			(string)	name of hidden field to be created
		*searchName =	(string)	unique name of a stored search
		initialvalue = 	(var)		initial value for field (blank)
		displayName =	(string)	Name to display (uses id by default)
		displayName =	(string)	Name to display (uses id by default)
		size =			(int)		size attribute for displayed input tag (32)
		maxlength =		(int)		max length attribute for displayed input tag (255)
		displayLabel	(boolean)	Show label tag with displayName (true)

		The JS used by this field type requires that the field NOT be implemented inside a p tag,
		inline element, or any tag that should not contain a div tag.  In IE, if the field placed
		inside an element that should not be able to handle a DIV tag inside it (standards-wise),
		IE will report a Javascript error.
*/
	function inputSmartSearch($db, $id, $searchName, $initialvalue = "", $displayName = NULL, $required=false,
					$size = 32, $maxlength = 255, $displayLabel = true, $allowFreeForm = false)  {
		$this->db = $db;

		parent::inputField($id, $initialvalue, $displayName,$required, NULL, $size, $maxlength, $displayLabel);

		$this->searchName = $searchName;
		$this->allowFreeForm = $allowFreeForm;


		//next I need to initialize and do the correct search
		$this->searchInfo = $this->getSearchInfo($searchName);

		$this->displayValue = $this->getInitialDisplay();

	}//end method - init


	function getSearchInfo($searchInfo){

		$querystatement = "
			SELECT
				*
			FROM
				smartsearches
			WHERE
				name = '".mysql_real_escape_string($searchInfo)."'
		";

		return  $this->db->fetchArray($this->db->query($querystatement));

	}//end method getInfo

	function getInitialDisplay(){

		$querystatement = "
			SELECT
				".$this->searchInfo["displayfield"]." AS display
			FROM
				".$this->searchInfo["fromclause"]."
			WHERE
				".$this->searchInfo["valuefield"]." = '".mysql_real_escape_string($this->value)."'
		";

		$queryresult = $this->db->query($querystatement);

		if($this->db->numRows($queryresult)){

			$therecord = $this->db->fetchArray($queryresult);
			return $therecord["display"];

		} else
			return '';

	}//end method getInitialDisplay


	// CLASS OVERIDES ================================================
	function getJSMods(){

		$thereturn = array("jsIncludes" => array(), "topJS" => array(), "bottomJS" => array(), "onload" => array());

		$thereturn["jsIncludes"][] = "common/javascript/smartsearch.js";

		if($this->required){

			$message = $this->message;

			if($message == "")
				$message = $this->displayName." cannot be blank.";
			$thereturn["topJS"][] = "requiredArray[requiredArray.length]= [ '".$this->name."','".$message."' ];";

		}//endif - required

		return $thereturn;

	}//end method - getJSMods


	function showLabel(){
		?><label for="ds-<?php echo $this->id?>"><?php echo $this->displayName?></label><br /><?php
	}//end method


	function display(){

		if($this->displayLabel)
			$this->showLabel();

		if(!isset($this->_attributes["class"]))
			$this->_attributes["class"] = "";
		else
			$this->_attributes["class"] = " ".$this->_attributes["class"];

		$this->_attributes["class"] = "inputSmartSearch".$this->_attributes["class"];

		?><input type="hidden" name="<?php echo $this->id?>" id="<?php echo $this->id?>" value="<?php echo $this->value?>" />
		<input type="hidden" id="sff-<?php echo $this->id?>" value="<?php echo ((int) $this->allowFreeForm); ?>"/>
		<input type="hidden" id="sdbid-<?php echo $this->id?>" value="<?php echo $this->searchInfo["id"]?>"/>
		<input type="text" name="ds-<?php echo $this->id?>" id="ds-<?php echo $this->id?>"  title="Use % for wildcard searches." <?php

		$this->displayAttributes();

		?> value="<?php echo htmlQuotes($this->displayValue) ?>"/><?php

	}//end method -display

}//end class - inputSmartSearch
//=====================================================================================
class inputOnChangeField extends inputField{

	function inputOnChangeField($id, $value, $displayName = NULL ,$required = false, $type = NULL, $size = 32, $maxlength = 128, $displayLabel = true){

		parent::inputField($id, $value, $displayName,$required, $type, $size, $maxlength, $displayLabel);

	}

    function getJSMods(){

        $thereturn = parent::getJSMods();

        $thereturn["jsIncludes"][] = "common/javascript/onchange.js";
        $thereturn["topJS"][] = "onchangeArray[onchangeArray.length]=new Array(\"".$this->name."\");";

        return $thereturn;

    }//end method --getJSMods--

	function display(){

		if($this->displayLabel)
			$this->showLabel();


        ?>
        <input type="hidden" name="<?php echo $this->name.'_changed';?>" id="<?php echo $this->id.'_changed';?>" value="0" />
        <input name="<?php echo $this->name?>" id="<?php echo $this->id?>" type="text" value="<?php echo $this->value?>" <?php
			$this->displayAttributes();
		?>/><?php

	}//end method

}//end class
//=====================================================================================
class inputComparisonField extends inputField{

    function display(){

        if($this->displayLabel)
            $this->showLabel();

        ?>
        <input type="hidden" name="<?php echo $this->name.'_old';?>" id="<?php echo $this->id.'_old';?>" value="<?php echo $this->value?>" />
        <input name="<?php echo $this->name?>" id="<?php echo $this->id?>" type="text" value="<?php echo $this->value?>" <?php
			$this->displayAttributes();
		?>/><?php

    }//end method

}//end class
?>
