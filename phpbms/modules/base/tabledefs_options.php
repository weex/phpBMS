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
	include("include/fields.php");
	include("./include/tabledefs_options_include.php");

	//grab the table name
	$querystatement = "SELECT displayname FROM tabledefs WHERE id=".((int) $_GET["id"]);
	$queryresult = $db->query($querystatement);
	$tableRecord = $db->fetchArray($queryresult);

	//process page
	$thecommand="";
	$action="add option";
	$theoption=setOptionDefaults();
	if (isset($_GET["command"])) $thecommand=$_GET["command"];
	if (isset($_POST["command"])) $thecommand=$_POST["command"];
	
	switch($thecommand){
		case "edit":
			$singleoptionquery=getOptions($db, $_GET["id"],$_GET["optionid"]);
			$theoption=$db->fetchArray($singleoptionquery);
			$action="edit option";
		break;
		
		case "delete":
			$statusmessage=deleteOption($db, $_GET["optionid"]);
		break;
		
		case "add option":
			$statusmessage=addOption($db, addSlashesToArray($_POST),$_GET["id"]);
		break;
		
		case "edit option":
			$statusmessage=updateOption($db, addSlashesToArray($_POST));
		break;
		
	}//end switch
	
	$optionsquery=getOptions($db, $_GET["id"]);
	
	$pageTitle="Table Definition Options: ".$tableRecord["displayname"];
	
	$phpbms->cssIncludes[] = "pages/tableoptions.css";
	$phpbms->jsIncludes[] = "modules/base/javascript/tableoptions.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
	
		$temparray["new"] = "new";
		$temparray["select"] = "select";
		$temparray["edit"] = "edit";
		$temparray["reporting"] = "printex";		
		$theinput = new inputBasicList("pdName",$theoption["name"],$temparray,"function");
		$theform->addField($theinput);
		
		$theinput = new inputField("name",$theoption["name"],"function name",false,NULL,64,64);
		$theform->addField($theinput);

		$theinput = new inputField("option",$theoption["option"],"display name",false,NULL,64,64);
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"roleid",$theoption["roleid"],"access (role)");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements	
		
	include("header.php");
	
	$phpbms->showTabs("tabledefs entry",3,$_GET["id"])?>
<div class="bodyline">

	<h1 id="topTitle"><span><?php echo $pageTitle?></span></h1>

	<div class="fauxP">	
	<table border="0" cellpadding="3" cellspacing="0" class="querytable">
		<tr>
			<th nowrap="nowrap"align="center">other</th>
			<th nowrap="nowrap"align="left">option / function</th>
			<th nowrap="nowrap"align="left" width="100%">name</th>
			<th nowrap="nowrap"align="center">access</th>
			<th nowrap="nowrap">&nbsp;</th>
		</tr>

	<?php 
		$row=1;
		while($therecord=$db->fetchArray($optionsquery)){ 
		if($row==1)$row=2;else $row=1;
	?>
		<tr class="qr<?php echo $row?> noselects">
			<td align="center" nowrap="nowrap"><?php echo booleanFormat($therecord["othercommand"])?></td>
			<td nowrap="nowrap"class="small"><?php 
				if($therecord["othercommand"]) 
					echo $therecord["name"]; 
				else {
					if($therecord["option"]==1)
						echo "allowed";
					else
						echo "not allowed";				
				}
			?></td>
			<td nowrap="nowrap" class="important"><?php 
	 			if($therecord["othercommand"]) echo $therecord["option"]; else echo $therecord["name"];	?>
			</td>
			<td nowrap="nowrap"align="center"><?php $phpbms->displayRights($therecord["roleid"],$therecord["rolename"])?></td>	
	 
			<td nowrap="nowrap"valign="top">
				<button id="edit<?php echo $therecord["id"]?>" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=edit&amp;optionid=".$therecord["id"]?>';" class="graphicButtons buttonEdit"><span>edit</span></button>
				<button id="delete<?php echo $therecord["id"]?>" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=delete&amp;optionid=".$therecord["id"]?>';" class="graphicButtons buttonDelete"><span>delete</span></button>
			</td>
		</tr>	
	<?php } ?>
		<tr class="queryfooter">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	</div>
	
	<fieldset>
		<legend><?php echo $action?></legend>
		<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onsubmit="return validateForm(this);">
			<input id="optionid" name="optionid" type="hidden" value="<?php echo $theoption["id"]?>" />
			<p>command type</p>
			<p>			
				<input type="radio" id="oc1" name="othercommand" value="0" onclick="switchType()" class="radiochecks" <?php if(!$theoption["othercommand"]) echo "checked=\"checked\""?>  /><label for="oc1">pre-defined</label>
				&nbsp;
				
				<input name="othercommand" id="oc2" type="radio" class="radiochecks" value="1" onclick="switchType()" <?php if($theoption["othercommand"]) echo "checked=\"checked\""?>  /><label for="oc2">other</label>
			</p>
			
			<div id="pdList">

				<p><?php $theform->showField("pdName")?></p>

				<p>option</p>
				<p>
					<input type="radio" class="radiochecks" id="pdOptionEnabled" name="pdOption" value="1" <?php if ($theoption["option"]!==0) echo "checked=\"checked\""?> /><label for="pdOptionAllowed">allowed</label>
					&nbsp;
					<input type="radio" class="radiochecks" id="pdOptionNot" name="pdOption" value="0" <?php if ($theoption["option"]===0) echo "checked=\"checked\""?> /><label for="pdOptionNot">not allowed</label>
				</p>			
			</div>

			<div id="other">
				<p><?php $theform->showField("name") ?></p>

				<p><?php $theform->showField("option") ?></p>
			</div>

			<p><?php $theform->showField("roleid")?></p>

			<p>
				<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" />
			</p>
		</form>
	</fieldset>
</div>
<?php include("footer.php"); ?>
