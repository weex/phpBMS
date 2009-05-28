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

	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");
	include("include/addresses.php");
	include("include/addresstorecord.php");
	include("include/clients.php");

	if(!isset($_GET["backurl"]))
		$backurl = NULL;
	else{
		$backurl = $_GET["backurl"];
		if(isset($_GET["refid"]))
			$backurl .= "?refid=".$_GET["refid"];
	}

	$thetable = new clients($db,2,$backurl);
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle=ucwords($therecord["type"]);

	if($therecord["inactive"])
		$pageTitle="Inactive ".$pageTitle;


	$phpbms->cssIncludes[] = "pages/client.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/client.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$phpbms->bottomJS[] = 'var thefirstname=getObjectFromID("firstname");thefirstname.focus();';

		$theinput = new inputCheckbox("inactive",$therecord["inactive"]);
		$theform->addField($theinput);

		$theinput = new inputBasicList("type",$therecord["type"],array("prospect"=>"prospect","client"=>"client"), "type");
		$theinput->setAttribute("class","important");
		$theinput->setAttribute("onchange","changeClientType(this)");
		$disabled=false;
		if($therecord["type"]=="client" && $therecord["id"]){
			$disabled = $thetable->checkForInvoices($therecord["id"]);
			if($disabled)
				$theinput->setAttribute("disabled","disabled");
		}//endif
		$theform->addField($theinput);

		$theinput = new inputChoiceList($db, "category",$therecord["category"],"clientcategories");
		$theform->addField($theinput);

		$theinput = new inputSmartSearch($db, "salesmanagerid", "Pick Active User", $therecord["salesmanagerid"], "sales person");
		$theform->addField($theinput);

		$theinput = new inputChoiceList($db, "leadsource",$therecord["leadsource"],"leadsource", "lead source");
		$theinput->setAttribute("class","small");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "paymentmethodid",$therecord["paymentmethodid"],"paymentmethods","id","name",
								"inactive=0", "priority,name", true, "payment method");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "shippingmethodid",$therecord["shippingmethodid"],"shippingmethods","id","name",
								"inactive=0", "priority,name", true, "shipping method");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "discountid",$therecord["discountid"],"discounts","id","name",
								"inactive=0", "name", true, "discount");
		$theform->addField($theinput);

		$theinput = new inputDataTableList($db, "taxareaid",$therecord["taxareaid"],"tax","id","name",
								"inactive=0", "name", true, "tax area");
		$theform->addField($theinput);

		$theinput = new inputField("workphone",$therecord["workphone"],"work phone",false,"phone",25,32);
		$theform->addField($theinput);

		$theinput = new inputField("homephone",$therecord["homephone"],"home phone",false,"phone",25,32);
		$theform->addField($theinput);

		$theinput = new inputField("mobilephone",$therecord["mobilephone"],"mobile phone",false,"phone",25,32);
		$theform->addField($theinput);

		$theinput = new inputField("fax",$therecord["fax"],NULL,false,"phone",25,32);
		$theform->addField($theinput);

		$theinput = new inputField("otherphone",$therecord["otherphone"],"other phone",false,"phone",25,32);
		$theform->addField($theinput);

		$theinput = new inputField("email",$therecord["email"],NULL,false,"email",68,128);
		$theform->addField($theinput);

		$theinput = new inputField("webaddress",$therecord["webaddress"],"web address",false,"www",68,128);
		$theform->addField($theinput);

		$theinput = new inputField("taxid", $therecord["taxid"], "tax id", false, "", 25, 32);
		$theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

	$action = htmlQuotes($_SERVER["REQUEST_URI"]);
	if(isset($_GET["invoiceid"]))
		$action .= "&amp;invoiceid=".$_GET["invoiceid"];
?>
<form action="<?php echo $action; ?>" method="post" name="record" onsubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div>
<?php $phpbms->showTabs("clients entry",6,$therecord["id"]); ?>
<div class="bodyline">
	<div id="topButtons">
		<?php showSaveCancel(1); ?>
	</div>

	<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1>

	<div id="rightSideDiv">
		<?php if(isset($_GET["invoiceid"])){?>
		<p id="backtoorderP">
			<input name="gotoinvoice" id="gotoinvoice" type="button" value="return to order" onclick="location.href='<?php echo getAddEditFile($db,3) ?>?id=<?php echo $_GET["invoiceid"] ?>'" class="Buttons" />
		</p>
		<?php } ?>
		<fieldset>
			<legend>attributes</legend>
			<p>
				<label for="id">id</label><br />
				<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="readonly" class="uneditable" />
				<input type="hidden" id="hascredit" name="hascredit" value="<?php echo $therecord["hascredit"]?>"/>
				<input type="hidden" id="creditlimit" name="creditlimit" value="<?php echo $therecord["creditlimit"]?>"/>
			</p>

			<p><?php $theform->showField("type");?></p>

			<p><?php $theform->showField("inactive")?></p>

			<p id="becameclientDiv" <?php if($therecord["type"]=="prospect") echo "style=\"display:none;\"" ?>>
				<label for="becameclient">became a client</label><br />
				<input type="text" id="becameclient" name="becameclient" readonly="readonly" class="uneditable" value="<?php echo formatFromSQLDate($therecord["becameclient"])?>" size="8" />
			</p>

			<p><?php $theform->showField("category")?></p>

		</fieldset>

		<fieldset>
			<legend>sales</legend>
			<div class="fauxP"><?php $theform->showField("salesmanagerid")?></div>

			<p><?php $theform->showField("leadsource")?></p>
		</fieldset>

		<fieldset>
			<legend>order defaults</legend>

			<p><?php $theform->showField("paymentmethodid")?></p>

			<p><?php $theform->showField("shippingmethodid")?></p>

			<p><?php $theform->showField("discountid")?></p>

			<p><?php $theform->showField("taxareaid")?></p>

		</fieldset>

		<fieldset>
			<legend>e-Commerce Login</legend>
			<p>
				<label for="username">user name</label><br />
				<input id="username" name="username" value="<?php echo htmlQuotes($therecord["username"])?>" maxlength="32" size="20"/>
			</p>
			<p>
				<label for="password">password</label><br />
				<input id="password" name="password" value="<?php echo htmlQuotes($therecord["password"])?>" maxlength="32" size="20"/>
			</p>

			<p><button class="Buttons" onclick="generateUserAndPass()" type="button">Generate</button></p>

			<p class="notes">Client user names and passwords are <strong>not</strong> used for clients' to log in directly to phpBMS.</p>
		</fieldset>
	</div>

	<div id="leftSideDiv">
		<fieldset>
			<legend>name / company</legend>
			<p id="firstnameP">
				<label for="firstname" class="important">first name</label><br />
				<input name="firstname" id="firstname" type="text" value="<?php echo htmlQuotes($therecord["firstname"])?>" size="32" maxlength="65" class="important" />
			</p>
			<p>
				<label for="lastname" class="important">last name</label><br />
				<input id="lastname" name="lastname" type="text" value="<?php echo htmlQuotes($therecord["lastname"])?>" size="32" maxlength="65" class="important" />
			</p>
			<p>
				<label for="company" class="important">company</label><br />
				<input name="company" type="text" id="company" value="<?php echo htmlQuotes($therecord["company"])?>" size="71" maxlength="128" class="important"/>
			</p>
		</fieldset>

		<fieldset>
			<legend>contact</legend>

			<p class="phonelefts"><?php $theform->showField("workphone")?></p>

			<p><?php $theform->showField("homephone")?></p>

			<p class="phonelefts"><?php $theform->showField("mobilephone")?></p>

			<p><?php $theform->showField("fax")?></p>

			<p><?php $theform->showField("otherphone")?></p>

			<p><?php $theform->showField("email")?></p>

			<p><?php $theform->showField("webaddress")?></p>

			<p><?php $theform->showField("taxid")?></p>
		</fieldset>

		<fieldset>
			<legend>
				<label for="address1">primary address</label>
				<button type="button" class="graphicButtons buttonMap" id="buttonMap" title="show map"><span>map</span></button>
			</legend>

			<p>
				<input type="hidden" id="addressid" name="addressid" value="<?php echo $therecord["addressid"]?>"/>
				<input id="address1" name="address1" type="text" size="71" maxlength="128" value="<?php echo htmlQuotes($therecord["address1"])?>" /><br />
				<input id="address2" name="address2" type="text" size="71" maxlength="128" value="<?php echo htmlQuotes($therecord["address2"])?>" />
			</p>

			<p class="csz">
				<label for="city">city</label><br />
				<input name="city" type="text" id="city" value="<?php echo htmlQuotes($therecord["city"])?>" size="35" maxlength="64" />
			</p>

			<p class="csz">
				<label for="state">state/province</label><br />
				<input name="state" type="text" id="state" value="<?php echo htmlQuotes($therecord["state"])?>" size="10" maxlength="20" />
			</p>
			<p>
				<label for="postalcode">zip/postal code</label><br />
				<input name="postalcode" type="text" id="postalcode" value="<?php echo htmlQuotes($therecord["postalcode"])?>" size="12" maxlength="15" />
			</p>
			<p>
				<label for="country">country</label><br />
				<input id="country" name="country" type="text" value="<?php echo htmlQuotes($therecord["country"])?>" size="44" maxlength="128" />
			</p>

		</fieldset>


		<fieldset>
			<legend><label for="comments">memo</label></legend>
			<p>
			<textarea name="comments" cols="20" rows="10" id="comments"><?php echo $therecord["comments"]?></textarea>
			</p>
		</fieldset>

		<?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>
	</div><?php $theform->showGeneralInfo($phpbms,$therecord);?>
	</div>
</form>
<?php include("footer.php")?>
