<?php
/*
 $Rev: 611 $ | $LastChangedBy: nate $
 $LastChangedDate: 2009-06-18 17:26:06 -0600 (Thu, 18 Jun 2009) $
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

require("../../../include/session.php");

class MCReport{

	var $selectcolumns;
	var $selecttable;
	var $whereclause="";
	var $reportOutput = "";

	function MCReport($db,$variables = NULL){
		$this->db = $db;
        
		//next we do the columns
		$this->addColumn("Email","`email`");//0
		$this->addColumn("First Name","`firstname`");//1
		$this->addColumn("Last Name","`lastname`");//2
		$this->addColumn("Company","`company`");//3
		$this->addColumn("Uuid","`uuid`");//4
		
		$this->addColumn("Type","`type`");//5
		$this->addColumn("Id","`id`");//6
		

		if($variables){
			//$tempArray = explode("::", $variables["columns"]);
			$tempArray = json_decode($variables["columns"], true);

			foreach($tempArray as $id)
				$this->selectcolumns[] = $this->columns[$id];
			$this->selectcolumns = array_reverse($this->selectcolumns);
			
			$this->selecttable = "`clients`";

			$this->whereclause = $_SESSION["printing"]["whereclause"];
			if($this->whereclause=="") $this->whereclause="WHERE clients.id != -1";

			if($this->whereclause!="") $this->whereclause=" WHERE (".substr($this->whereclause,6).") ";
		}// endif
		
	}//end method


	function addColumn($name, $field, $format = NULL){
		$temp = array();
		$temp["name"] = $name;
		$temp["field"] = $field;
		$temp["format"] = $format;

		$this->columns[] = $temp;
	}//end method
	
	
	function generate(){
		
		$querystatement = "SELECT ";
		foreach($this->selectcolumns as $thecolumn)
			$querystatement .= $thecolumn["field"]." AS `".$thecolumn["name"]."`,";
		$querystatement = substr($querystatement, 0, -1);
		$querystatement .= " FROM ".$this->selecttable.$this->whereclause;
		
		$queryresult = $this->db->query($querystatement);

		$num_fields = $this->db->numFields($queryresult);
		
		for($i=0;$i<$num_fields;$i++)
			$this->reportOutput .= ",".$this->db->fieldName($queryresult, $i);

		$this->reportOutput = substr($this->reportOutput, 1)."\n";

		while($therecord = $this->db->fetchArray($queryresult)){

			$line = "";

			foreach($therecord as $value)
				$line .= ',"'.mysql_real_escape_string($value).'"';

			$line = substr($line, 1)."\n";

			$this->reportOutput .= $line;

		}//endwhile

		$this->reportOutput = "----- The headings should be deleted before importing into MailChimp -----\n".$this->reportOutput;
		$this->reportOutput = substr($this->reportOutput, 0, strlen($this->reportOutput)-1);
	}
	
	
	function output(){
		
		header("Content-type: text/plain");
		header('Content-Disposition: attachment; filename="clients_mailchimp_export.csv"');

		echo $this->reportOutput;
		
	}//end function --output--
	

	function showOptions($what){
		?><option value="0">----- Choose One -----</option>
		<?php
		$i=0;

		foreach($this->$what as $value){
			?><option value="<?php echo $i+1; ?>"><?php echo $value["name"];?></option>
			<?php
			$i++;
		}// endforeach

	}//end mothd


	function showSelectScreen(){

        global  $phpbms;

        $pageTitle="Invoice Total";
        $phpbms->showMenu = false;
        $phpbms->cssIncludes[] = "pages/totalreports.css";
        $phpbms->jsIncludes[] = "modules/bms/javascript/totalreports.js";

        include("header.php");

        ?>

        <div class="bodyline">
            <h1>Invoice Total Options</h1>
            <form id="GroupForm" action="<?php echo $_SERVER["PHP_SELF"]?>" method="post" name="GroupForm">

                <fieldset>

                    <legend>report</legend>
                    <p>
                        <label for="reporttitle">report title</label><br />
                        <input type="text" name="reporttitle" id="reporttitle" size="45"/>
                    </p>

		</fieldset>

                <fieldset>

                    <legend>groupings</legend>
                    <input id="groupings" type="hidden" name="groupings"/>
                    <div id="theGroups">
                        <div id="Group1">
                            <select id="Group1Field">
                                <?php $this->showOptions("groupings")?>
                            </select>
                            <button type="button" id="Group1Minus" class="graphicButtons buttonMinusDisabled"><span>-</span></button>
                            <button type="button" id="Group1Plus" class="graphicButtons buttonPlus"><span>+</span></button>
                        </div>
                    </div>

                </fieldset>

		<fieldset>

			<legend>columns</legend>
			<input id="columns" type="hidden" name="columns"/>
			<div id="theColumns">
				<div id="Column1">
					<select id="Column1Field">
						<?php $this->showOptions("columns")?>
					</select>
					<button type="button" id="Column1Minus" class="graphicButtons buttonMinusDisabled"><span>-</span></button>
					<button type="button" id="Column1Plus" class="graphicButtons buttonPlus"><span>+</span></button>
				</div>
			</div>
		</fieldset>

		<fieldset>
			<legend>Options</legend>
			<p>
			<label for="showwhat">information shown</label><br />
			<select name="showwhat" id="showwhat">
				<option selected="selected" value="totals">Totals Only</option>
				<option value="invoices">Invoices</option>
				<option value="lineitems">Invoices &amp; Line Items</option>
			</select>
			</p>
		</fieldset>

                <p align="right">
                    <button id="print" type="button" class="Buttons">Print</button>
                    <button id="cancel" type="button" class="Buttons">Cancel</button>
                </p>

            </form>
        </div>

        <?php

        include("footer.php");
    }//end method

}//endclass

// Processing ===================================================================================================================
if(!isset($dontProcess)){
	if(isset($_POST["columns"])){
		$myreport= new MCReport($db,$_POST);
		$myreport->generate();
		$myreport->output();
	} else {
		//$myreport = new MCReport($db);
		//$myreport->showSelectScreen();
	}
}?>