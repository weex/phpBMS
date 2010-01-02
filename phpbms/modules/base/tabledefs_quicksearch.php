<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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

	include("include/tabledefs_quicksearch_include.php");

	if(!hasRights("Admin"))
		goURL(APP_PATH."noaccess.php");

	if(!isset($_GET["id"]))
		$error = new appError(-200, "Passed parameter missing", "Invalid request", true);

	$quicksearches = new quickSearches($db, $_GET["id"]);

	//grab the table name
	$querystatement = "SELECT displayname FROM tabledefs WHERE id=".((int) $_GET["id"]);
	$queryresult = $db->query($querystatement);
	$tableRecord = $db->fetchArray($queryresult);

	//process page
	$thecommand="";
	$action="add quick search item";
	$thequicksearch = $quicksearches->getDefaults();

	if (isset($_GET["command"])) $thecommand=$_GET["command"];
	if (isset($_POST["command"])) $thecommand=$_POST["command"];

	switch($thecommand){
		case "edit":
			$singlequicksearchsquery = $quicksearches->get($_GET["quicksearchid"]);
			$thequicksearch = $db->fetchArray($singlequicksearchsquery);
			$action = "edit quick search item";
			break;

		case "delete":
			$statusmessage = $quicksearches->delete($_GET["quicksearchid"]);
		break;

		case "add quick search item":
			$statusmessage = $quicksearches->add(addSlashesToArray($_POST));
		break;

		case "edit quick search item":
			$statusmessage = $quicksearches->update(addSlashesToArray($_POST));
		break;

		case "moveup":
			$statusmessage = $quicksearches->move($_GET["quicksearchid"],"up");
		break;

		case "movedown":
			$statusmessage = $quicksearches->move($_GET["quicksearchid"],"down");
		break;

	}//end switch

	$quicksearchsquery = $quicksearches->get();

	$pageTitle="Table Definition Quick Search: ".$tableRecord["displayname"];

	$phpbms->cssIncludes[] = "pages/tablequicksearch.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputField("name",$thequicksearch["name"],NULL,true,NULL,28,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"roleid",$thequicksearch["roleid"],"access (role)");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

	$phpbms->showTabs("tabledefs entry","tab:276dacd4-4a37-d979-aeda-a7982f632559",$_GET["id"])?><div class="bodyline">
	<h1 id="topTitle"><span><?php echo $pageTitle?></span></h1>
	<div class="fauxP">
	<table border="0" cellpadding="3" cellspacing="0" class="querytable">
		<tr>
			 <th nowrap="nowrap">move</th>
			 <th width="100%" align="left">item</th>
			 <th nowrap="nowrap"class="queryheader" align="left">access role</th>
			 <th nowrap="nowrap">&nbsp;</th>
		</tr>
	<?php
		$topdisplayorder=-1;
		$row=1;
		while($therecord=$db->fetchArray($quicksearchsquery)){
			$topdisplayorder=$therecord["displayorder"];
			if($row==1) $row=2; else $row=1;
	?>
	<tr class="qr<?php echo $row?> noselects">
	 <td nowrap="nowrap"valign="top">
	 	<button type="button" class="graphicButtons buttonUp" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=moveup&amp;quicksearchid=".$therecord["id"]?>';"><span>up</span></button>
	 	<button type="button" class="graphicButtons buttonDown" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=movedown&amp;quicksearchid=".$therecord["id"]?>';"><span>dn</span></button>
		 <?php echo $therecord["displayorder"]?>
	 </td>
	 <td valign="top">
		<strong><?php echo htmlQuotes($therecord["name"])?></strong><br />
		<?php echo htmlQuotes($therecord["search"])?>
	 </td>
	 <td valign="top" class="small" nowrap="nowrap"><?php echo $phpbms->displayRights($therecord["roleid"])?></td>
	 <td nowrap="nowrap" valign="top">
		 <button id="edit<?php echo $therecord["id"]?>" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=edit&amp;quicksearchid=".$therecord["id"]?>';" class="graphicButtons buttonEdit"><span>edit</span></button>
		 <button id="delete<?php echo $therecord["id"]?>" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=delete&amp;quicksearchid=".$therecord["id"]?>';" class="graphicButtons buttonDelete"><span>delete</span></button>
	 </td>
	</tr>
	<?php } ?>
	<tr class="queryfooter">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	</table>
	</div>
	<form action="<?php echo htmlentities($_SERVER["PHP_SELF"])."?id=".$_GET["id"] ?>" method="post" name="record" onsubmit="return validateForm(this);">
	<fieldset>
		<legend><?php echo $action?></legend>
		<input name="quicksearchid" type="hidden" value="<?php echo $thequicksearch["id"]?>" />
		<input name="displayorder" type="hidden" value="<?php if($action=="add quick search item") echo $topdisplayorder+1; else echo $thequicksearch["displayorder"]?>" />

		<p><?php $theform->showField("name");?></p>

		<p><?php $theform->showField("roleid");?></p>

		<p>
			<label for="search">search</label> <span class="notes">(SQL WHERE clause)</span><br />
			<textarea id="search" name="search" cols="32" rows="2"><?php echo htmlQuotes($thequicksearch["search"]) ?></textarea>
		</p>
	</fieldset>

		<p align="right">
			<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" />
			<?php if($action == "edit quick search item"){?>
				<input name="command" id="cancel" type="submit" value="cancel edit" class="Buttons" />
			<?php }?>
		</p>

	</form>
</div>
<?php include("footer.php")?>
