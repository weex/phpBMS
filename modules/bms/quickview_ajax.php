<?php 
/*
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
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

require("../../include/session.php");
require("../../include/common_functions.php");
require("../../include/fields.php");

function showClient($clientid,$basepath){
	global $dblink;
	
	$querystatement="SELECT clients.id, clients.firstname,
					if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as name,
					clients.type, clients.inactive, clients.category,
					clients.address1,clients.address2,clients.city,clients.state,clients.postalcode,
					clients.workphone,clients.homephone,clients.mobilephone,clients.email,clients.webaddress,clients.comments
					FROM clients WHERE id=".$clientid;
					
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Could Not Retrieve Client: ".mysql_error($dblink)." -- ".$querystatement);
	$therecord=mysql_fetch_array($queryresult);
	
	$querystatement="SELECT invoices.id,invoices.type,
					if(invoices.type=\"Invoice\",invoices.invoicedate,invoices.orderdate) as thedate,
					totalti
					FROM invoices WHERE invoices.clientid=".$clientid." ORDER BY type,thedate";
	$invoiceresult=mysql_query($querystatement,$dblink);
	if(!$invoiceresult) reportError(300,"Could Not Retrieve Invoices: ".mysql_error($dblink)." -- ".$querystatement);

	$querystatement="SELECT notes.id,notes.type,notes.subject,notes.category, notes.completed,
					ELT(notes.importance+3,\"&nbsp;\",\"&middot;\",\"-\",\"*\",\"!\",\"!!\")  as importance
					FROM notes WHERE notes.attachedtabledefid=2 AND notes.attachedid=".$clientid." 
					ORDER BY notes.completed,notes.category,notes.type,notes.importance DESC,notes.creationdate";
	$noteresult=mysql_query($querystatement,$dblink);
	if(!$noteresult) reportError(300,"Could Not Retrieve Notes: ".mysql_error($dblink)." -- ".$querystatement);
?>
<div class="bodyline" style="margin-top:15px;">
<h1><?php echo htmlQuotes($therecord["name"])?> <button type="button" class="invisibleButtons" onClick="addEditRecord('edit','client','<?php echo getAddEditFile(2)?>')"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-edit.png" align="absmiddle" alt="edit" width="16" height="16" border="0" /></button></h1>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td valign=top>
			<fieldset>
				<legend>attributes</legend>
				<label for="detailsCategory" style="float:right">
					category<br />
					<input id="detailsCategory" type="text" size="35" readonly="readonly" class="uneditable" value="<?php echo htmlQuotes($therecord["category"])?>">					
				</label>
				<label for="detailsType" >type<br />
					<?php if ($therecord["inactive"]) $therecord["type"]="inactive ".$therecord["type"];?>
					<input id="detailsType" type="text" size="20" readonly="readonly" class="uneditable important" value="<?php echo $therecord["type"]?>">					
				</label>				
			</fieldset>
			<fieldset>
				<legend><label for="detailsAddress" style="padding:0px;">address</label></legend>
				<?php 
					$theaddress=$therecord["address1"]."\n";
					if($therecord["address2"]) $theaddress.=$therecord["address2"]."\n";
					if($therecord["city"]) $theaddress.=$therecord["city"].", ";
					$theaddress.=$therecord["state"]." ";
					$theaddress.=$therecord["postalcode"];		
				?>
				<textarea id="detailsAddress" readonly="readonly" class="uneditable" rows="3" cols="40" style="width:97%"><?php echo $theaddress?></textarea>
			</fieldset>
			<fieldset>
				<legend>contact</legend>
				<label for="detailsWorkPhone" style="float:left;">
					work phone<br />
			<input id="detailsWorkPhone" type="text" size="18" readonly="readonly" class="uneditable" value="<?php echo htmlQuotes($therecord["workphone"])?>">
		</label>
		<label for="detailsHomePhone" style="float:left;">
			home phone<br />
			<input id="detailsHomePhone" type="text" size="18" readonly="readonly" class="uneditable" value="<?php echo htmlQuotes($therecord["homephone"])?>">		
		</label>
		<label for="detailsMobilePhone" style="">
			mobile phone<br />
			<input id="detailsMobilePhone" type="text" size="18" readonly="readonly" class="uneditable" value="<?php echo htmlQuotes($therecord["mobilephone"])?>">		
		</label>
		<label for="detailsEmail" style="">
			e-mail address<br />
			<?PHP field_email("detailsEmail",$therecord["email"],Array("readonly"=>"readonly","size"=>"63","class"=>"uneditable")); ?>
		</label>
		<label for="detailsWeb" style="">
			web address<br />
			<?PHP field_web("detailsWeb",$therecord["webaddress"],Array("readonly"=>"readonly","size"=>"63","class"=>"uneditable")); ?>
		</label>
		
	</fieldset>
	<fieldset>
		<legend><label for="detailsMemo" style="padding:0px;">Memo</label></legend>
		<textarea id="detailsMemo" readonly="readonly" class="uneditable" rows="5" cols="40" style="width:97%"><?php echo $therecord["comments"]?></textarea>
	</fieldset>
		</td>
		<td valign=top width="300px;">
			<?php if($therecord["type"]!="prospect") {?>
			<fieldset style="">
				<legend>quotes / orders / Invoices</legend>
				<div style="padding:0px;padding-right:5px;" align="right">
					<button id="invoiceedit" type="button" class="invisibleButtons" onClick="addEditRecord('edit','invoice','<?php echo getAddEditFile(3)?>')"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-edit-disabled.png" align="absmiddle" alt="edit" width="16" height="16" border="0" /></button><button type="button" class="invisibleButtons" onClick="addEditRecord('new','invoice','<?php echo getAddEditFile(3,"add")?>')"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-plus.png" align="absmiddle" alt="new" width="16" height="16" border="0" /></button>
				</div>
				<div style="height:188px;overflow:auto" class="smallQueryTableHolder">
					<?php if(!mysql_num_rows($invoiceresult)) {?>
						<div class="small"><em>no records</em></div>
					<?php } else {?>
					<table border="0" cellpadding="0" cellspacing="0" class="smallQueryTable">
						<tr>
							<th align="left">Type</th>
							<th align="left">Date</th>
							<th align="left">ID</th>
							<th align="right" width="100%">Total</th>
						</tr>
					<?php while($invoicerecord=mysql_fetch_array($invoiceresult)) {
							if($invoicerecord["type"]=="VOID")
								$invoicerecord["totalti"]="-----"
					?><tr onClick="selectEdit(this,<?php echo $invoicerecord["id"]?>,'invoice')">
						<td><?php echo $invoicerecord["type"]?></td>
						<td><?php echo dateFormat($invoicerecord["thedate"])?></td>
						<td><?php echo $invoicerecord["id"]?></td>
						<td align="right"><?php echo currencyFormat($invoicerecord["totalti"])?></td>
					</tr>
					<?php }?></table><?php }?>
	
				</div>
			</fieldset>
			<?php }?>
			<fieldset>
				<legend>notes / tasks / events</legend>
				<div style="padding:0px;padding-right:5px;" align="right">
					<button id="noteedit" type="button" class="invisibleButtons" onClick="addEditRecord('edit','note','<?php echo getAddEditFile(12)?>')"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-edit-disabled.png" align="absmiddle" alt="edit" width="16" height="16" border="0" /></button><button type="button" class="invisibleButtons" onClick="addEditRecord('new','note','<?php echo getAddEditFile(12,"add")?>')"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-plus.png" align="absmiddle" alt="new" width="16" height="16" border="0" /></button>
				</div>
				<div style="height:140px;overflow:auto" class="smallQueryTableHolder">
					<?php if(!mysql_num_rows($noteresult)) {?>
						<div class="small"><em>no records</em></div>
					<?php } else {?>
					<table border="0" cellpadding="0" cellspacing="0" class="smallQueryTable">
						<tr>
							<th align="center">!</th>
							<th align="left">type</th>
							<th align="left">category</th>
							<th align="left" width="100%">title</th>
							<th align="center">done</th>
						</tr>
					<?php while($noterecord=mysql_fetch_array($noteresult)) {
							if(strlen($noterecord["subject"])>17)
								$noterecord["subject"]=substr($noterecord["subject"],0,17)."...";
							if(strlen($noterecord["category"])>17)
								$noterecord["category"]=substr($noterecord["category"],0,17)."...";
					?><tr onClick="selectEdit(this,<?php echo $noterecord["id"]?>,'note')">
						<td align=center><?php echo $noterecord["importance"]?></td>
						<td><?php echo $noterecord["type"]?></td>
						<td><?php echo $noterecord["category"]?></td>
						<td><?php echo $noterecord["subject"]?></td>
						<td align="center"><?php echo booleanFormat($noterecord["completed"])?></td>
					</tr>
					<?php }?></table><?php }?>	
				</div>
			</fieldset>
					</td>
	</tr>
</table>
</div>
<?php 
}//end function

	//=================================================================================================
	if(isset($_GET["cm"])){
		switch($_GET["cm"]){
			case "showClient":
				$thereturn=showClient($_GET["id"],$_GET["base"]);
			break;
		}
	}

?>