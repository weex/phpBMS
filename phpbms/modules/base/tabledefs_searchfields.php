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
	include("include/tabledefs_searchfields_include.php");

	//grab the table name
	$querystatement = "SELECT displayname FROM tabledefs WHERE id=".((int) $_GET["id"]);
	$queryresult = $db->query($querystatement);
	$tableRecord = $db->fetchArray($queryresult);

	//process page
	$thecommand="";
	$action="add search field";
	$thesearchfield=setDefaultSearchField();

	if (isset($_GET["command"])) $thecommand=$_GET["command"];
	if (isset($_POST["command"])) $thecommand=$_POST["command"];

	switch($thecommand){
		case "edit":
			$singlesearchfieldsquery=getSearchfields($db,$_GET["id"],$_GET["searchfieldid"]);
			$thesearchfield=$db->fetchArray($singlesearchfieldsquery);
			$action="edit search field";
		break;

		case "delete":
			$statusmessage=deleteSearchfield($db,$_GET["searchfieldid"]);
		break;

		case "add search field":
			$statusmessage=addSearchfield($db,addSlashesToArray($_POST),$_GET["id"]);
		break;

		case "edit search field":
			$statusmessage=updateSearchfield($db,addSlashesToArray($_POST));
		break;

		case "moveup":
			$statusmessage=moveSearchfield($db,$_GET["columnid"],"up");
		break;

		case "movedown":
			$statusmessage=moveSearchfield($db,$_GET["columnid"],"down");
		break;
	}//end switch

	$searchfieldsquery=getSearchfields($db,$_GET["id"]);
	$pageTitle="Table Definition Search Fields: ".$tableRecord["displayname"];

	$phpbms->cssIncludes[] = "pages/tablequicksearch.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputField("name",$thesearchfield["name"],NULL,true,NULL,32,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new  inputBasicList("type",$thesearchfield["type"],array("field"=>"field","SQL where clause"=>"whereclause"));
		$theform->addField($theinput);

//		$theinput = new inputField("field",$thesearchfield["field"],"field name / SQL where clause",true,NULL,32,255);
//		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

	$phpbms->showTabs("tabledefs entry","tab:22d08e82-5047-4150-6de7-49e89149f56b",$_GET["id"])?><div class="bodyline">
	<h1 id="topTitle"><span><?php echo $pageTitle?></span></h1>

	<div class="fauxP">
   <table border="0" cellpadding="0" cellspacing="0" class="querytable">
	<tr>
	 <th align="left" nowrap="nowrap" class="queryheader">move</th>
	 <th align="left" nowrap="nowrap" class="queryheader">type</th>
	 <th align="left" nowrap="nowrap"class="queryheader" width="100%">name</th>
	 <th nowrap="nowrap" class="queryheader">&nbsp;</th>
	</tr>
	<?php
		$topdisplayorder=-1;
		$row=1;
		while($therecord=$db->fetchArray($searchfieldsquery)){
			$topdisplayorder=$therecord["displayorder"];
			if($row==1) $row=2; else $row=1;
	?>
	<tr class="qr<?php echo $row?> noselects">
		<td nowrap="nowrap" valign="top" class="small">
			<button type="button" class="graphicButtons buttonUp" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=moveup&amp;columnid=".$therecord["id"]?>';"><span>up</span></button>
			<button type="button" class="graphicButtons buttonDown" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=movedown&amp;columnid=".$therecord["id"]?>';"><span>dn</span></button>
			<?php echo $therecord["displayorder"]?>
		</td>
		<td nowrap="nowrap" valign="top" ><?php echo htmlQuotes($therecord["type"]);?></td>
		<td valign="top">
			<strong><?php echo htmlQuotes($therecord["name"])?></strong><br />
			<span class="small"><?php echo htmlQuotes($therecord["field"])?></span>
		</td>
		<td nowrap="nowrap" valign="top">
			 <button id="edit<?php echo $therecord["id"]?>" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=edit&amp;searchfieldid=".$therecord["id"]?>';" class="graphicButtons buttonEdit"><span>edit</span></button>
			 <button id="delete<?php echo $therecord["id"]?>" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=delete&amp;searchfieldid=".$therecord["id"]?>';" class="graphicButtons buttonDelete"><span>delete</span></button>
		</td>
	</tr>
	<?php } ?>
	<tr class="queryfooter">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	</table></div>

	<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onsubmit="return validateForm(this);">
	<fieldset>
		<legend><?php echo $action?></legend>
			<input id="searchfieldid" name="searchfieldid" type="hidden" value="<?php echo $thesearchfield["id"]?>" />
			<input id="displayorder" name="displayorder" type="hidden" value="<?php if($action=="add search field") echo $topdisplayorder+1; else echo $thesearchfield["displayorder"]?>" />

			<p><?php $theform->showField("name")?></p>

			<p><?php $theform->showField("type")?></p>

			<p>
				<textarea id="field" name="field" cols="64" rows="2" style="width: 99%"><?php echo $thesearchfield["field"] ?></textarea><br />
				<span class="notes">This can be a simple SQL field name (e.g notes.title) or a complex SQL clause where the value is passed
				(e.g. assignedto.firstname like "{{value}}%"or assignedto.lastname like "{{value}}%") depending on the type drop down (above) chosen.</span>
			</p>
	</fieldset>
		<p align="right">
			<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" />
		<?php if($action == "edit search field"){?>
			<input name="command" id="cancel" type="submit" value="cancel edit" class="Buttons" />
		<?php }?>
		</p>
	</form>

</div>
<?php include("footer.php");?>
