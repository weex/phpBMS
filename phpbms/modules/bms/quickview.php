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
	
	$pagetitle="Quick View";

	$phpbms->cssIncludes[] = "pages/quickview.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/quickview.js";


		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		
		if(isset($_GET["cid"]))
			$passedValue = ((int) $_GET["cid"]);
		else
			$passedValue = NULL;

			$theinput = new inputSmartSearch($db, "clientid", "Pick Sales Order Client", $passedValue, "client", false, 55, 255, false);
			$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements
	
		$lookUp["name / address"] = $theinput->getSearchInfo("Pick Sales Order Client");
		$lookUp["e-mail address"] = $theinput->getSearchInfo("Pick Client By Email");
		$lookUp["phone"] = $theinput->getSearchInfo("Pick Client By Phone");
	
	include("header.php");
	?>
<form method="post" name="record" id="record" onsubmit="return false;" action="#">
<div class="bodyline">
	<h1><?php echo $pagetitle?></h1>

	<div class="fauxDiv">

		<label for="lookupby">search by</label><br />

		<select id="lookupby">
			<?php foreach($lookUp as $key=>$value) {?>			
			<option value="<?php echo $value["id"] ?>"><?php echo $key?></option>
			<?php }//endforeach - lookup?>
		</select>				

		<?php $theform->showField("clientid")?>		

		<input type="hidden" id="addeditfile" value="<?php echo getAddEditFile($db,2,"add")?>" />
		<input type="button" value="view" id="viewButton" class="disabledButtons" />
		<input type="button" value="add new" id="addButton" class="Buttons" />
	</div>

	
</div>
<div id="clientrecord" />
</form>
<?php include("footer.php");?>