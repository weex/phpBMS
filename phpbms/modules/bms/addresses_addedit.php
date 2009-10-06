<?php
/*
 $Rev: 285 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-27 14:05:27 -0600 (Mon, 27 Aug 2007) $
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

	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");
	include("include/addresses.php");

	if(!isset($_GET["backurl"])){

		$thetable = new addresses($db,"tbld:27b99bda-7bec-b152-8397-a3b09c74cb23");

		$pageTitle="Address";

	} else {

		include("include/addresstorecord.php");

		$backurl = $_GET["backurl"];

		if(isset($_GET["refid"]))
			$backurl .= "?refid=".$_GET["refid"];

		$thetable = new addresstorecord($db,"tbld:27b99bda-7bec-b152-8397-a3b09c74cb23",$backurl);
		$pageTitle="Client Address";

	}//end if

	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];


	$phpbms->cssIncludes[] = "pages/bms/addresses.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/addresstorecord.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputField("title",$therecord["title"],NULL,false,NULL,71,128);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputField("address1",$therecord["address1"],"address",false,NULL,71,128);
		$theform->addField($theinput);

		$theinput = new inputField("address2",$therecord["address2"],NULL,false,NULL,71,128, false);
		$theform->addField($theinput);

		$theinput = new inputField("city",$therecord["city"],NULL,false,NULL,35,64);
		$theform->addField($theinput);

		$theinput = new inputField("state",$therecord["state"],"state/province",false,NULL,10,20);
		$theform->addField($theinput);

		$theinput = new inputField("postalcode",$therecord["postalcode"],"zip/postal code",false,NULL,12,15);
		$theform->addField($theinput);

		$theinput = new inputField("country",$therecord["country"],NULL,false,NULL,44,128);
		$theform->addField($theinput);

		$theinput = new inputField("shiptoname",$therecord["shiptoname"],"ship to name",false,NULL,71,128);
		$theform->addField($theinput);

		$theinput = new inputField("phone",$therecord["phone"],NULL,false,"phone",25,32);
		$theform->addField($theinput);

		$theinput = new inputField("email",$therecord["email"],NULL,false,"email",68,128);
		$theform->addField($theinput);

		$theinput = new inputTextarea("notes",$therecord["notes"], NULL ,false, 4,80, false);
		$theform->addField($theinput);

		$showNewChoice = false;

		if(isset($therecord["tabledefid"])){
			//these are fields used only when displaying connected addresses

			if($therecord["addressid"] == ""){
				//this is a new record from a linked item
				// so we need to generate the controls that
				// allow creating a new, or linking an existing.

				$showNewChoice = true;

				$therecord["tabledefid"] = mysql_real_escape_string($_GET["tabledefid"]);
				$querystatement = "
					SELECT
						`maintable`
					FROM
						`tabledefs`
					WHERE
						`uuid` = '".$therecord["tabledefid"]."'
				";
				$queryresult = $thetable->db->query($querystatement);
				if($thetable->db->numRows($queryresult)){
					$record = $thetable->db->fetchArray($queryresult);
					$maintable = $record["maintable"];

					$querystatement = "
						SELECT
							`uuid`
						FROM
							`".$maintable."`
						WHERE
							`id`='".(int)$_GET["refid"]."'
					";

					$queryresult = $thetable->db->query($querystatement);

					if($thetable->db->numRows($queryresult)){
						$brecord = $thetable->db->fetchArray($queryresult);
						$therecord["recordid"] = mysql_real_escape_string($brecord["uuid"]);

					}else{
						$therecord["recordid"] = mysql_real_escape_string($_GET["refid"]);
					}//endif

				}else{
					$therecord["recordid"] = mysql_real_escape_string($_GET["refid"]);
				}//end if

				switch($therecord["tabledefid"]){
					//diferent tables will need different views
					//and displays (will eventurally need one for vendors)

					case "tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083": //clients
					default:
						$smartSearch = "Pick Existing Client Address";
						break;

				}//endswitch tabledefid

				$theinput = new inputSmartSearch($db, "existingaddressid", $smartSearch, "", "existing address", false, 71);
				$theform->addField($theinput);

			}//endif - addressid

			$theinput = new inputDataTableList($db, "table", $therecord["tabledefid"],
												"tabledefs", "id", "displayname",
												"", "", false, "table");
			$theinput->setAttribute("disabled","disabled");
			$theinput->setAttribute("class","uneditable");
			$theform->addField($theinput);

			$theinput = new inputField("recordid",$therecord["recordid"],"record",false,NULL,5,12);
			$theinput->setAttribute("readonly","readonly");
			$theinput->setAttribute("class","uneditable");
			$theform->addField($theinput);

			$theinput = new inputCheckbox("primary",$therecord["primary"]);
			$theinput->setAttribute("disabled","disabled");
			$theform->addField($theinput);

			$theinput = new inputCheckbox("defaultshipto",$therecord["defaultshipto"],"default ship to");
			$theinput->setAttribute("disabled","disabled");
			$theform->addField($theinput);

		}//endif - tabledefid

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?><div class="bodyline">
	<?php $theform->startForm($pageTitle)?>

	<div id="rightSideDiv">
		<fieldset>
			<legend>Attributes</legend>


			<?php if(isset($therecord["addressid"])) {?>

				<p>
					<input type="hidden" name="addressid" id="addressid" value="<?php echo $therecord["addressid"]?>" />
					<input type="hidden" name="tabledefid" id="tabledefid" value="<?php echo $therecord["tabledefid"]?>" />
					<?php $theform->showField("table")?>
				</p>

				<p>
					<?php $theform->showField("recordid")?>
				</p>

			<?php }//endif - addressid ?>

			<?php if(isset($_GET["id"])){ ?>
				<input type="hidden" name="getid" id="getid" value="<?php echo((int)$_GET["id"]); ?>" />
			<?php }//end if ?>

		</fieldset>

		<?php if(isset($therecord["addressid"])) {?>

			<fieldset>
				<legend>record defaults</legend>

				<p>
					<?php $theform->showField("primary")?>
				</p>

				<p>
					<?php $theform->showField("defaultshipto")?>
				</p>

				<p class="notes">
					Set address defaults for record on address list screen.
				</p>

			</fieldset>

		<?php }//endif - addressid ?>
	</div>

	<div class="leftSideDiv" id="chooseNew">

		<?php if($showNewChoice) { ?>

		<fieldset>
			<legend>add address</legend>

			<p>
				<input type="radio" name="chooseNew" class="radiochecks" checked="checked" id="newAddressRadio" value="create"/><label for="newAddressRadio">create new address</label>
				<input type="radio" name="chooseNew" class="radiochecks" id="linkExistingRadio" value="link"/><label for="linkExistingRadio">link existing address</label>
			</p>

			<div id="selectExistingP" class="fauxp">
				<?php $theform->showField("existingaddressid")?>
			</div>

		</fieldset>

		<?php  }//endif - showNewChoice ?>

	</div>

	<div class="leftSideDiv" id="newAddressDiv">

		<fieldset>
			<legend>address <button type="button" class="graphicButtons buttonMap" id="buttonMap" title="show map"><span>map</span></button></legend>

			<p class="big"><?php $theform->showField("title")?></p>

			<p>
				<?php $theform->showField("address1")?><br />
				<?php $theform->showField("address2")?>
			</p>

			<p class="csz">
				<?php $theform->showField("city")?>
			</p>

			<p class="csz">
				<?php $theform->showField("state")?>
			</p>
			<p>
				<?php $theform->showField("postalcode")?>
			</p>
			<p>
				<?php $theform->showField("country")?>
			</p>

		</fieldset>

		<fieldset>
			<legend>additional information</legend>

			<p><?php $theform->showField("shiptoname")?></p>

			<p><?php $theform->showField("phone")?></p>

			<p><?php $theform->showField("email")?></p>

		</fieldset>

		<fieldset>
			<legend><label for="notes">notes</label></legend>

			<p><?php $theform->showField("notes")?></p>

		</fieldset>

		<?php if($therecord["id"]) {?>
			<fieldset>
				<legend>Associations</legend>

				<div class="fauxP">
				<?php
					$addressid = $therecord["addressid"];
					if(isset($therecord["addressuuid"]))
						$addressid = $therecord["addressuuid"];
					$thetable->showAssociations($addressid);
					?>
				</div>
			</fieldset>
		<?php } //endif record if?>

		<?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>
	</div>


	<?php
		$theform->showGeneralInfo($phpbms,$therecord);
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>
