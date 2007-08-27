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
	include("include/usersearches.php");

	$thetable = new userSearches($db,17);
	$therecord = $thetable->processAddEditPage();
	
	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	if($therecord["userid"])
		$username = $phpbms->getUserName($therecord["userid"]);
	else
		$username = "global";

	
	$pageTitle="Saved Searches";
	
	$phpbms->cssIncludes[] = "pages/usersearches.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputBasicList("type",$therecord["type"],array("Search"=>"SCH","Sort"=>"SRT"));
		$theinput->setAttribute("class","uneditable");
		$theinput->setAttribute("disabled","disabled");
		$theform->addField($theinput);			

		if($therecord["userid"]==0){
			$theinput = new inputRolesList($db,"roleid",$therecord["roleid"],"access (role)");
			$theform->addField($theinput);			
		}

		$theinput = new inputField("name",$therecord["name"],NULL,true,NULL,64,128);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);		

		$theform->jsMerge();
		//==============================================================
		//End Form Elements	
		
	include("header.php");
	
?><div class="bodyline">
	<?php $theform->startForm($pageTitle)?>
	
	<fieldset id="fsAttributes">
		<legend>attributes</legend>
		<p>
			<label for="id">id</label><br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="readonly" class="uneditable"/>
		</p>
		
		<p><?php $theform->showField("type"); ?></p>
		
		<p>
			<label for="tabledefid">table</label><br />
			<?php $thetable->displayTables("tabledefid",$therecord["tabledefid"]) ?>		
		</p>
		
		<p>
			<label for="username">user</label><br />
			<input type="hidden" id="userid" name="userid" value="<?php echo $therecord["userid"]?>" />
			<input id="username" name="username" type="text" value="<?php echo htmlQuotes($username) ?>" size="32" readonly="readonly" class="uneditable" />
		</p>
		
		<?php if($therecord["userid"]!=0) {?>
		<p>
			<input id="makeglobal" name="makeglobal" type="checkbox" class="radiochecks" value="1" /><label for="makeglobal">make global</label>
		</p>
		<?php } else {?>
		
			<p><?php $theform->showField("roleid")?></p>
			
		<?php } ?>
	</fieldset>

	<div id="leftSideDiv">
		<fieldset>
			<legend>name / sql</legend>
			<p><?php $theform->showField("name");?></p>
			
			<p>
				<label for="sqlclause">SQL where clause</label><br />
				<textarea id="sqlclause" name="sqlclause" cols="62" rows="12"><?php echo htmlQuotes($therecord["sqlclause"])?></textarea>
			</p>
		</fieldset>
	</div>

	<div id="createmodifiedby"><input id="cancelclick" name="cancelclick" type="hidden" value="0" />
		<div id="savecancel2"><?php showSaveCancel(2)?></div>
	</div>

	<?php 
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>