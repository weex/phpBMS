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

	include("include/tabledefs_columns_include.php");

	//process page
	$thecommand="";
	$action="add column";
	$thecolumn=setColumnDefaults();
	
	//grab the table name
	$querystatement = "SELECT displayname FROM tabledefs WHERE id=".((int) $_GET["id"]);
	$queryresult = $db->query($querystatement);
	$tableRecord = $db->fetchArray($queryresult);
	
	if (isset($_GET["command"])) $thecommand=$_GET["command"];
	if (isset($_POST["command"])) $thecommand=$_POST["command"];
	
	switch($thecommand){
		case "edit":
			$singlecolumnsquery=getColumns($db,$_GET["id"],$_GET["columnid"]);
			$thecolumn = $db->fetchArray($singlecolumnsquery);
			$action="edit column";
		break;
		
		case "delete":
			$statusmessage=deleteColumn($db,$_GET["columnid"]);
		break;
		
		case "add column":
			$statusmessage=addColumn($db,addSlashesToarray($_POST),$_GET["id"]);
		break;
		
		case "edit column":
			$statusmessage=updateColumn($db,addSlashesToarray($_POST));
		break;
		
		case "moveup":
			$statusmessage=moveColumn($db,$_GET["columnid"],"up");
		break;
		
		case "movedown":
			$statusmessage=moveColumn($db,$_GET["columnid"],"down");
		break;
	}//end switch
	
	$columnsquery=getColumns($db,$_GET["id"]);
	
	$pageTitle="Table Definition Columns: ".$tableRecord["displayname"];
	$phpbms->cssIncludes[] = "pages/tablecolumns.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		
		$theinput = new inputField("name",$thecolumn["name"],NULL,true,NULL,32,64);
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);

		$theinput = new inputBasicList ("align",$thecolumn["align"],array("left"=>"left","center"=>"center","right"=>"right"));
		$theform->addField($theinput);

		$theinput = new inputCheckbox("wrap",$thecolumn["wrap"]);
		$theform->addField($theinput);

		$formatArray["None"] = "";
		$formatArray["Date"] = "date";
		$formatArray["Time"] = "time";
		$formatArray["Date and Time"] = "datetime";
		$formatArray["Currency"] = "currency";
		$formatArray["Boolean (yes / no)"] = "boolean";
		$formatArray["File Link"] = "filelink";
		$formatArray["No Encoding (HTML acceptable)"] = "noencoding";
		$theinput = new inputBasicList ("format", $thecolumn["format"], $formatArray);
		$theform->addField($theinput);

		$theinput = new inputRolesList($db,"roleid",$thecolumn["roleid"],"access (role)");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements	
		
	include("header.php");
	
	$phpbms->showTabs("tabledefs entry",2,$_GET["id"])?><div class="bodyline">
	<h1><span><?php echo $pageTitle?></span></h1>
	<div class="fauxP">
   <table border="0" cellpadding="0" cellspacing="0" class="querytable">
	<tr>
	 <th nowrap="nowrap" class="queryheader">move</th>
	 <th align="left" nowrap="nowrap" class="queryheader" width="100%">name/field</th>
	 <th align="left" nowrap="nowrap" class="queryheader">align</th>
	 <th align="center" nowrap="nowrap" class="queryheader">wrap</th>
	 <th align="left" nowrap="nowrap" class="queryheader">size</th>
	 <th align="left" nowrap="nowrap" class="queryheader">format</th>
	 <th align="left" nowrap="nowrap" class="queryheader">access</th>
	 <th nowrap="nowrap" class="queryheader">&nbsp;</th>
	</tr>
	<?php 
		$topdisplayorder=-1;
		$row=1;
		while($therecord=$db->fetchArray($columnsquery)){ 
			$topdisplayorder=$therecord["displayorder"];
			if($row==1) $row=2; else $row=1;
	?>
	<tr class="qr<?php echo $row?> noselects">
		<td nowrap="nowrap"valign="top">
		 	<button type="button" class="graphicButtons buttonUp" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=moveup&amp;columnid=".$therecord["id"]?>';"><span>Move Up</span></button>
		 	<button type="button" class="graphicButtons buttonDown" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=movedown&amp;columnid=".$therecord["id"]?>';"><span>Move Down</span></button>
			<?php echo $therecord["displayorder"];?>
		</td>
		<td valign="top">
			<strong><?php echo $therecord["name"]?></strong><br />
			<?php echo htmlQuotes($therecord["column"])?>
		</td>
		<td nowrap="nowrap"valign="top"><?php echo $therecord["align"]?></td>
		<td align="center" nowrap="nowrap"valign="top"><?php echo booleanFormat($therecord["wrap"])?></td>
		<td nowrap="nowrap" valign="top"><?php if($therecord["size"]) echo $therecord["size"]; else echo "&nbsp;";?></td>
		<td nowrap="nowrap" valign="top"><?php  
			if($therecord["format"]) {
				echo array_search($therecord["format"],$formatArray);
			}else  echo "&nbsp;"
		?></td>
		<td valign="top"><?php $phpbms->displayRights($therecord["roleid"])?></td>
		<td nowrap="nowrap"valign="top">
			 <button id="edit<?php echo $therecord["id"]?>" name="doedit" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=edit&amp;columnid=".$therecord["id"]?>';" class="graphicButtons buttonEdit"><span>edit</span></button>
			 <button id="delete<?php echo $therecord["id"]?>" name="dodelete" type="button" onclick="document.location='<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"]."&amp;command=delete&amp;columnid=".$therecord["id"]?>';" class="graphicButtons buttonDelete"><span>delete</span></button>
		</td>
	</tr>
	<?php } ?>
	<tr class="queryfooter">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	</table></div>
	
	<fieldset>
		<legend><?php echo $action?></legend>
		<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" name="record" onsubmit="return validateForm(this);">
		<input id="columnid" name="columnid" type="hidden" value="<?php echo $thecolumn["id"]?>" />
		<input id="displayorder" name="displayorder" type="hidden" value="<?php if($action=="add column") echo $topdisplayorder+1; else echo $thecolumn["displayorder"]?>" />

		<p><?php  $theform->showField("name")?></p>
		
		<p>
			<label for="column">field</label><br />
			<textarea id="column" name="column" cols="64" rows="2"><?php echo $thecolumn["column"] ?></textarea><br />
			<span class="notes">This can be a simple SQL field name (e.g notes.title) or a complex SQL field clause (e.g. concat(clients.firstname," ",clients.lastname)</span>
		</p>
		
		<p><?php $theform->showField("roleid")?></p>
		
		<p><?php $theform->showField("align");?></p>
		
		<p><?php $theform->showField("wrap")?></p>
		
		<p>
			<label for="size">column size</label><br />
			<input id="size" name="size" type="text" value="<?php echo htmlQuotes($thecolumn["size"])?>" size="32" maxlength="128" /><br />
			<span class="notes">HTML sizing conventions (e.g. 95%, or 150px)</span>
		</p>
		<p>
			<?php $theform->showField("format")?><br />
			<span class="notes">if you are using HTML code in your field, you will want to choose the no-encoding option, but special character in the database may not display correctly.</span>
		</p>
		<p>
			<label for="sortorder">sorting</label><br />
			<textarea id="sortorder" name="sortorder" cols="64" rows="2"><?php echo $thecolumn["sortorder"] ?></textarea><br />
			<span class="notes">
				sorting affects how phpBMS will sort when you click on the cloumn header.  Leave blank if you want the sort to reflect the field exactly.<br />
				This can be a simple SQL field name (e.g notes.title) or a complex SQL field clause (e.g. concat(clients.firstname," ",clients.lastname).
			</span>
		</p>
		
		<p>
			<label for="footerquery">footer</label><br />
			<textarea id="footerquery" name="footerquery" cols="32" rows="2"><?php echo $thecolumn["footerquery"] ?></textarea><br />
			<span class="notes">SQL Group by function (e.g avg(invoices.totalti) whill display the average invoice total at the bottom of the table)</span>		</p>

		<p align="right">
			<input name="command" id="save" type="submit" value="<?php echo $action?>" class="Buttons" />
		</p>
		</form>
	</fieldset>
</div>
<?php include("footer.php")?>
