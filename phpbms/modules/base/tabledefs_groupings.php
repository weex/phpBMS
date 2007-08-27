<?php 
/*
 $Rev: 267 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-14 13:08:27 -0600 (Tue, 14 Aug 2007) $
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
	include("include/fields.php");

	include("include/tablegroupings.php");

	
	//grab the table name
	$querystatement = "SELECT displayname FROM tabledefs WHERE id=".((int) $_GET["id"]);
	$queryresult = $db->query($querystatement);
	$tableRecord = $db->fetchArray($queryresult);
	$pageTitle="Table Definition Groupings: ".$tableRecord["displayname"];

	$groupings = new groupings($db,$_GET["id"]);
	
	$thecommand="";
	if (isset($_GET["command"])) $thecommand=$_GET["command"];
	if (isset($_POST["command"])) $thecommand=$_POST["command"];
	
	$therecord = $groupings->processForm($thecommand,$_POST,$_GET);
	$allRecords = $groupings->getRecords();
	
	$action = $therecord["action"];
	
	if(isset($therecord["statusMessage"]))
		$statusmessage = $therecord["statusMessage"];
	
	$phpbms->cssIncludes[] = "pages/tablecolumns.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputTextarea("field",$therecord["field"], "SQL field" ,true, 3,80);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputField("name",$therecord["name"],NULL,false,NULL,32,64);
		$theform->addField($theinput);
	
		$theinput = new inputCheckbox("ascending",$therecord["ascending"]);
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"roleid",$therecord["roleid"],"access (role)");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements	
		
	include("header.php");
	
	$phpbms->showTabs("tabledefs entry",100,$_GET["id"])?><div class="bodyline">
	<h1><span><?php echo $pageTitle?></span></h1>
	
	<?php $groupings->showRecords($allRecords) ?>
	
	<fieldset>
		<legend><?php echo $action?></legend>
		<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onsubmit="return validateForm(this);">
		<input id="id" name="id" type="hidden" value="<?php echo $therecord["id"]?>" />
		
		<p>
			<?php $theform->showField("field")?><br />
			<span class="notes">This can be a simple SQL field name (e.g notes.title) or a complex SQL field clause (e.g. concat(clients.firstname," ",clients.lastname)</span>
		</p>

		<p><?php  $theform->showField("name")?></p>
		
		
		<p><?php $theform->showField("roleid")?></p>
		
		<p><?php $theform->showField("ascending")?></p>
		
		<p align="right">
			<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" />
		</p>
		</form>
	</fieldset>
</div>
<?php include("footer.php")?>
