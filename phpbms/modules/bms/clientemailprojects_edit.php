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
	include("include/clientemailprojects.php");

	if(isset($_GET["backurl"]))
		$backurl=$_GET["backurl"]."?refid=".$_GET["refid"];
	else
		$backurl = NULL;

	$thetable = new clientEmailProjects($db,"tbld:157b7707-5503-4161-4dcf-6811f8b0322f",$backurl);
	$therecord = $thetable->processAddEditPage();

	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	if($therecord["userid"])
		$username = $phpbms->getUserName($therecord["userid"]);
	else
		$username = "global";

	$pageTitle="Client E-mail Project";

	$phpbms->cssIncludes[] = "pages/clientemailprojects.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputField("name",$therecord["name"],NULL,true,NULL,60,128);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");
?>
	<div class="bodyline">
	<?php $theform->startForm($pageTitle)?>
	<fieldset id="fsID">
		<legend><label for="id">id</label></label></legend>
		<p>
			<br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="readonly" class="uneditable"/>
		</p>
	</fieldset>

	<div id="leftSideDiv">
		<fieldset>
			<legend>name</legend>
			<p>
				<?php $theform->showField("name"); ?>
			</p>
		</fieldset>
	</div>

	<fieldset id="fsUser">
		<legend><label for="username">user</label></legend>
		<p><br />
			<input type="hidden" id="userid" name="userid" value="<?php echo $therecord["userid"]?>" />
			<input id="username" name="username" type="text" value="<?php echo $username ?>" size="50" readonly="readonly" class="uneditable" />
		</p>
		<?php if($therecord["userid"]!=0) {?>
		<p>
			<input id="makeglobal" name="makeglobal" type="checkbox" class="radiochecks" value="1" /><label for="makeglobal">make global</label>
		</p>
		<?php } ?>
	</fieldset>

	<fieldset>
		<legend>e-mail</legend>
		<p>
			<label for="from">from</label><br />
			<?php if(is_numeric($therecord["emailfrom"])) $therecord["emailfrom"]=getEmailInfo($therecord["emailfrom"]);?>
			<input id="from" name="from" value="<?php echo htmlQuotes($therecord["emailfrom"])?>" readonly="readonly" class="uneditable" size="60" />
		</p>
		<p>
			<label for="to">to</label><br />
			<input id="to" name="to" value="<?php if($therecord["emailto"]=="selected" or $therecord["emailto"]=="all") echo htmlQuotes($therecord["emailto"]); else echo "saved search"?>" size="60" readonly="readonly" class="uneditable" />
			<?php if(is_numeric($therecord["emailto"]))	{?>
			<br /><input id="to2" name="to2" value="<?php echo htmlQuotes(showSavedSearch($therecord["emailto"]))?>" readonly="readonly" class="uneditable" size="60"/>
			<?php } ?>
		</p>

		<p>
			<label for="subject">subject</label><br />
			<input id="subject" name="subject" type="text" value="<?php echo $therecord["subject"]?>" size="32" readonly="readonly" class="uneditable" />
		</p>
		<p>
			<textarea id="body" name="body" readonly="readonly" class="uneditable" cols="80" rows="40"><?php echo htmlQuotes($therecord["body"])?></textarea>
		</p>
	</fieldset>

	<div align="right">
		<?php showSaveCancel(2); ?>
		<input id="cancelclick" name="cancelclick" type="hidden" value="0" />
	</div>
	<?php
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>