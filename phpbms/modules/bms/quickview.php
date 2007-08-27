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
	require_once("../../include/session.php");
	require_once("include/fields.php");
	
	$pagetitle="Client/Prospect Quick View";

	$phpbms->cssIncludes[] = "pages/quickview.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/quickview.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		
		if(isset($_GET["cid"]))
			$passedValue = $_GET["cid"];
		else
			$passedValue = NULL;

		$theinput = new inputAutofill($db, "namecid",$passedValue,2,"clients.id","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)",
										"if(clients.city!=\"\",concat(clients.city,\", \",clients.state),\"\")","clients.inactive=0","name");
		$theinput->setAttribute("size",45);
		$theinput->setAttribute("maxlength",128);
		$theinput->setAttribute("class","important lookupWhats");
		$phpbms->bottomJS[] = 'document.forms["record"]["namecid"].onchange=updateViewButton;';
		$theform->addField($theinput);

		$theinput = new inputAutofill($db, "emailcid","",2,"clients.id","clients.email",
										"if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","clients.inactive=0","e-mail address");
		$theinput->setAttribute("size",45);
		$theinput->setAttribute("maxlength",128);
		$theinput->setAttribute("class","important lookupWhats");
		$phpbms->bottomJS[] = 'document.forms["record"]["emailcid"].onchange=updateViewButton;';
		$theform->addField($theinput);
		
		$theinput = new inputAutofill($db, "workphonecid","",2,"clients.id","clients.workphone",
										"if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","clients.inactive=0","work phone");
		$theinput->setAttribute("size",45);
		$theinput->setAttribute("maxlength",128);
		$theinput->setAttribute("class","important lookupWhats");
		$phpbms->bottomJS[] = 'document.forms["record"]["workphonecid"].onchange=updateViewButton;';
		$theform->addField($theinput);

		$theinput = new inputAutofill($db, "homephonecid","",2,"clients.id","clients.homephone",
										"if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","clients.inactive=0","home phone");
		$theinput->setAttribute("size",45);
		$theinput->setAttribute("maxlength",128);
		$theinput->setAttribute("class","important lookupWhats");
		$phpbms->bottomJS[] = 'document.forms["record"]["homephonecid"].onchange=updateViewButton;';
		$theform->addField($theinput);

		$theinput = new inputAutofill($db, "mobilephonecid","",2,"clients.id","clients.mobilephone",
										"if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","clients.inactive=0","mobile phone");
		$theinput->setAttribute("size",45);
		$theinput->setAttribute("maxlength",128);
		$theinput->setAttribute("class","important lookupWhats");
		$phpbms->bottomJS[] = 'document.forms["record"]["mobilephonecid"].onchange=updateViewButton;';
		$theform->addField($theinput);

		$theinput = new inputAutofill($db, "mainaddresscid","",2,"clients.id","clients.address1",
										"if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","clients.inactive=0","address");
		$theinput->setAttribute("size",45);
		$theinput->setAttribute("maxlength",128);
		$theinput->setAttribute("class","important lookupWhats");
		$phpbms->bottomJS[] = 'document.forms["record"]["mainaddresscid"].onchange=updateViewButton;';
		$theform->addField($theinput);


		$theform->jsMerge();
		//==============================================================
		//End Form Elements
	
	include("header.php");
	?>
<form method="post" name="record" id="record" onsubmit="return false;" action="">
<div class="bodyline">
	<h1><?php echo $pagetitle?></h1>

			<p id="lookupByP">
				<label for="lookupby">look up by</label><br />
				<select id="lookupby" onchange="updateLookup(this)" tabindex="5">
					<option value="namecid">name</option>
					<option value="emailcid">e-mail address</option>
					<option value="workphonecid">work phone</option>
					<option value="homephonecid">home phone</option>
					<option value="mobilephonecid">mobile phone</option>
					<option value="mainaddresscid">main address</option>
				</select>				
			</p>
			<p id="lookupButtonsP">
				<input type="button" value="view" id="dolookup" class="Buttons" disabled="disabled" tabindex="20" onclick="viewClient()" />
				<input type="button" value="add new" id="addnew" class="Buttons" tabindex="20" onclick="addEditRecord('new','client','<?php echo getAddEditFile($db,2,"add")?>')" />
			</p>			
			<div class="fauxP" id="lookupWhatP">

				<p id="lookupNameLabel"><?php $theform->showField("namecid")?></p>
				
				<p id="lookupEmailLabel" class="disabledP"><?php $theform->showField("emailcid")?></p>
				
				<p id="lookupWorkPhoneLabel" class="disabledP"><?php $theform->showField("workphonecid")?></p>

				<p id="lookupHomePhoneLabel" class="disabledP"><?php $theform->showField("homephonecid")?></p>

				<p id="lookupMobilePhoneLabel" class="disabledP"><?php $theform->showField("mobilephonecid")?></p>

				<p id="lookupMainAddressLabel" class="disabledP"><?php $theform->showField("mainaddresscid")?></p>
			</div>
</div>
<div id="clientrecord"></div>
</form>
<?php include("footer.php");?>