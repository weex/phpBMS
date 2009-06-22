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

require("../../include/session.php");
require("include/tables.php");
require("include/fields.php");
require("include/clients.php");
require("include/addresses.php");
require("include/addresstorecord.php");

class quickView{

	function quickView($db){

		$this->db = $db;

	}//end method - id


	function get($clientid){

		$clientid = mysql_real_escape_string($clientid);

		$thetable = new clients($this->db, "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083");

		$clientRecord = $thetable->getRecord($clientid, true);

		$clientRecord["invoices"] = $this->_getInvoices($clientid);
		$clientRecord["notes"] = $this->_getnotes($clientid);

		return $clientRecord;

	}//end method - get


	function _getInvoices($clientid){

		$querystatement = "
			SELECT
				invoices.id,
				invoices.type,
				if(invoices.type='Invoice',invoices.invoicedate,invoices.orderdate) as thedate,
				totalti
			FROM
				invoices
			WHERE
				invoices.clientid = '".$clientid."'
			ORDER BY
				type,
				thedate";

		return $this->_buildRecordArray($this->db->query($querystatement));

	}//end method - _getInvoices


	function _getNotes($clientid){

		$querystatement = "
			SELECT
				notes.id,
				notes.type,
				notes.subject,
				notes.category,
				notes.completed
			FROM
				notes
			WHERE
				notes.attachedtabledefid='tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083'
				AND notes.attachedid='".$clientid."'
			ORDER BY
				notes.completed,
				notes.category,
				notes.type,
				notes.importance DESC,
				notes.creationdate";

		return $this->_buildRecordArray($this->db->query($querystatement));

	}//end method - _getNotes


	function _buildRecordArray($queryresult){

		$returnArray = array();

		while($therecord = $this->db->fetchArray($queryresult))
			$returnArray[] = $therecord;

		return $returnArray;

	}//end method - _buildRecordArray


	function display($clientInfo){

            $invoiceEditFile = getAddEditFile($this->db, "tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883");
            $noteEditFile = getAddEditFile($this->db, "tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1");
            $clientEditFile = getAddEditFile($this->db, "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083");

            ?><div class="bodyline" id="theDetails">

                    <div id="rightSideDiv">

                            <fieldset>
                                    <legend>sales</legend>
                                    <p>
                                            <button type="button" class="graphicButtons buttonNew" onclick="addEditRecord('new','invoice','<?php echo getAddEditFile($this->db, "tbld:62fe599d-c18f-3674-9e54-b62c2d6b1883","add")?>')"><span>new</span></button>
                                            <button id="invoiceedit" type="button" disabled="disabled" class="graphicButtons buttonEditDisabled" onclick="addEditRecord('edit','invoice','<?php echo $invoiceEditFile?>')"><span>edit</span></button>
                                    </p>
                                    <div class="fauxP">
                                    <div id="salesTable" class="smallQueryTableHolder">
                                            <?php if(!count($clientInfo["invoices"])) {?>
                                                    <div class="small"><em>no records</em></div>
                                            <?php } else {?>
                                            <table border="0" cellpadding="0" cellspacing="0" class="smallQueryTable">
                                                    <tr>
                                                            <th align="left">ID</th>
                                                            <th align="left">Type</th>
                                                            <th align="left">Date</th>
                                                            <th align="right" width="100%">Total</th>
                                                    </tr>
                                            <?php foreach($clientInfo["invoices"] as $invoicerecord) {
                                                            if($invoicerecord["type"]=="VOID")
                                                                    $invoicerecord["totalti"]="-----"
                                            ?><tr onclick="selectEdit(this,<?php echo $invoicerecord["id"]?>,'invoice')" ondblclick="selectedInvoice=<?php echo $invoicerecord["id"]?>;addEditRecord('edit','invoice','<?php echo $invoiceEditFile?>')">
                                                    <td><?php echo $invoicerecord["id"]?></td>
                                                    <td><?php echo $invoicerecord["type"]?></td>
                                                    <td nowrap="nowrap"><?php echo formatFromSQLDate($invoicerecord["thedate"])?></td>
                                                    <td align="right"><?php echo numberToCurrency($invoicerecord["totalti"])?></td>
                                            </tr>
                                            <?php }?></table><?php }?>
                                    </div>
                                    </div>

                            </fieldset>

                            <fieldset>
                                    <legend>notes</legend>

                                    <div class="fauxP">
                                    <p>
                                            <button type="button" class="graphicButtons buttonNew" onclick="addEditRecord('new','note','<?php echo getAddEditFile($this->db, "tbld:a4cdd991-cf0a-916f-1240-49428ea1bdd1","add")?>')"><span>new</span></button>
                                            <button id="noteedit" type="button" class="graphicButtons buttonEditDisabled" disabled="disabled" onclick="addEditRecord('edit','note','<?php echo $noteEditFile?>')"><span>edit</span></button>
                                    </p>
                                    <div id="notesTable"  class="smallQueryTableHolder">
                                            <?php if(!count($clientInfo["notes"])) {?>
                                                    <div class="small"><em>no records</em></div>
                                            <?php } else {?>
                                            <table border="0" cellpadding="0" cellspacing="0" class="smallQueryTable">
                                                    <tr>
                                                            <th align="left">type</th>
                                                            <th align="left">category</th>
                                                            <th align="left" width="100%">title</th>
                                                            <th align="center">done</th>
                                                    </tr>
                                            <?php foreach($clientInfo["notes"] as $noterecord) {
                                                            if(strlen($noterecord["subject"])>17)
                                                                    $noterecord["subject"]=substr($noterecord["subject"],0,17)."...";
                                                            if(strlen($noterecord["category"])>17)
                                                                    $noterecord["category"]=substr($noterecord["category"],0,17)."...";
                                            ?><tr onclick="selectEdit(this,<?php echo $noterecord["id"]?>,'note')" ondblclick="selectedNote=<?php echo $noterecord["id"]?>;addEditRecord('edit','note','<?php echo $noteEditFile?>')">
                                                    <td><?php echo $noterecord["type"]?></td>
                                                    <td><?php echo $noterecord["category"]?></td>
                                                    <td><?php echo $noterecord["subject"]?></td>
                                                    <td align="center"><?php echo booleanFormat($noterecord["completed"])?></td>
                                            </tr>
                                            <?php }?></table><?php }?>
                                    </div>
                                    </div>

                            </fieldset>

                    </div>

                    <div id="leftSideDiv">

                            <fieldset id="crTile" class="fs<?php echo $clientInfo["type"]?>">

                                    <h1>
                                        <input type="hidden" id="theid" value="<?php echo $clientInfo["id"] ?>" />
										<input type="hidden" id="theuuid" value="<?php echo $clientInfo["uuid"] ?>" />
                                    <?php
                                            if($clientInfo["company"])
                                                    echo htmlQuotes($clientInfo["company"]);
                                            else
                                                    echo htmlQuotes($clientInfo["firstname"]." ".$clientInfo["lastname"]);
                                    ?> <button id="viewClientButton" type="button" title="view client" class="graphicButtons buttonInfo" onclick="addEditRecord('edit','client','<?php echo $clientEditFile?>')"><span>view client</span></button></h1>

                                    <?php
                                    if($clientInfo["company"] && $clientInfo["firstname"] && $clientInfo["lastname"]){

                                            ?><p id="crName"><?php echo htmlQuotes($clientInfo["firstname"])?> <?php echo htmlQuotes($clientInfo["lastname"])?></p><?php

                                    }//endif
                                    ?>

                                    <?php

                                    $location = "";
                                    $location .= htmlQuotes($clientInfo["address1"]);

                                    if($clientInfo["address2"])
                                            $location .= "<br />".htmlQuotes($clientInfo["address2"]);

                                    if($clientInfo["city"] || $clientInfo["state"] || $clientInfo["postalcode"]){

                                            $location .= "<br/>".htmlQuotes($clientInfo["city"]);
                                            if($clientInfo["city"] && $clientInfo["state"])
                                                    $location .= ", ";
                                            $location .= htmlQuotes($clientInfo["state"]);
                                            $location .= " ".htmlQuotes($clientInfo["postalcode"]);

                                    }//endif

                                    if($clientInfo["country"])
                                            $location .= "<br />".htmlQuotes($clientInfo["country"]);

                                    if($location == "")
                                            $location = "unspecified location";

                                    ?><p id="crLocation"><?php echo $location?></p>

                            </fieldset>

                            <fieldset>
                                    <legend>Contact</legend>
                                    <?php if($clientInfo["workphone"] || $clientInfo["homephone"] || $clientInfo["mobilephone"] || $clientInfo["otherphone"] || $clientInfo["fax"]){ ?>

                                            <p class="RDNames">phone</p>

                                            <div class="fauxP RDData">
                                                    <ul>
                                                    <?php if($clientInfo["workphone"]){ ?>
                                                            <li><?php echo $clientInfo["workphone"] ?> (w)</li>
                                                    <?php }?>

                                                    <?php if($clientInfo["homephone"]){ ?>
                                                            <li><?php echo $clientInfo["homephone"] ?> (h)</li>
                                                    <?php }?>

                                                    <?php if($clientInfo["mobilephone"]){ ?>
                                                            <li><?php echo $clientInfo["mobilephone"] ?> (m)</li>
                                                    <?php }?>

                                                    <?php if($clientInfo["otherphone"]){ ?>
                                                            <li><?php echo $clientInfo["otherphone"] ?> (o)</li>
                                                    <?php }?>

                                                    <?php if($clientInfo["fax"]){ ?>
                                                            <li><?php echo $clientInfo["fax"] ?> (fax)</li>
                                                    <?php }?>
                                                    </ul>
                                            </div>

                                    <?php }?>

                                    <?php if($clientInfo["email"]){ ?>
                                            <p class="RDNames">e-mail</p>
                                            <p class="RDData">
                                                    <button type="button" class="graphicButtons buttonEmail" onclick="document.location='mailto:<?php echo $clientInfo["email"]?>'"><span>send email</span></button>
                                                    &nbsp;<a href="mailto:<?php echo $clientInfo["email"]?>"><?php echo htmlQuotes($clientInfo["email"])?></a>
                                            </p>
                                    <?php }?>


                                    <?php if($clientInfo["webaddress"]){ ?>
                                            <p class="RDNames">web site</p>
                                            <p class="RDData">
                                                    <button type="button" class="graphicButtons buttonWWW" onclick="window.open('<?php echo $clientInfo["webaddress"]?>')"><span>visit site</span></button>
                                                    &nbsp;<a href="<?php echo $clientInfo["webaddress"]?>" target="_blank"><?php echo htmlQuotes($clientInfo["webaddress"])?></a>
                                            </p>
                                    <?php }?>
                            </fieldset>

                            <fieldset>
                                    <legend>Details</legend>

                                    <?php if($clientInfo["becameclient"]){ ?>
                                            <p class="RDNames">became client</p>
                                            <p class="RDData">
                                                    <?php echo formatVariable($clientInfo["becameclient"],"date")?>
                                            </p>
                                    <?php }?>

                                    <?php if($clientInfo["category"]){ ?>
                                            <p class="RDNames">category</p>
                                            <p class="RDData">
                                                    <?php echo htmlQuotes($clientInfo["category"])?>
                                            </p>
                                    <?php }?>

                                    <?php if($clientInfo["leadsource"]){ ?>
                                            <p class="RDNames">lead source</p>
                                            <p class="RDData">
                                                    <?php echo htmlQuotes($clientInfo["leadsource"])?>
                                            </p>
                                    <?php }?>

                                    <?php if($clientInfo["salesmanagerid"]){
                                            global $phpbms; ?>
                                            <p class="RDNames">sales person</p>
                                            <p class="RDData">
                                                    <?php echo htmlQuotes($phpbms->getUserName($clientInfo["salesmanagerid"]))?>
                                            </p>
                                    <?php }?>

                            </fieldset>


                            <?php if($clientInfo["comments"]){?>
                            <fieldset>
                                    <legend>memo</legend>
                                    <p>
                                            <?php echo htmlQuotes($clientInfo["comments"])?>
                                    </p>
                            </fieldset>
                            <?php }?>

                    </div>
                    <p id="theclear">&nbsp;</p>
            </div>
            <?php

	}//endMethod - display

}//end class

// PROCESSING =====================================================================================
// =================================================================================================
if(isset($_GET["id"])){

	$quickView = new quickView($db);

	$clientInfo = $quickView->get($_GET["id"]);

	$quickView->display($clientInfo);

}//endif - id

?>
