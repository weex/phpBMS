<?php 
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
	
	$querystatement="SELECT invoices.id,invoices.status,
					if(find_in_set(invoices.status,\"Quote,Order,Invoice,VOID\")>2,invoicedate,orderdate) as theDate,
					if(invoices.status=\"Quote\",1,0) as theQuote,
					if(invoices.status=\"Order\",1,0) as theOrder,
					if(invoices.status=\"Invoice\",1,0) as theOrder,
					DATE_FORMAT(invoices.invoicedate,\"%c/%e/%Y\") as idate,
					DATE_FORMAT(invoices.orderdate,\"%c/%e/%Y\") as odate,
					totalti
					FROM invoices WHERE invoices.clientid=".$clientid." ORDER BY theQuote DESC,theOrder DESC, theDate DESC";
	$invoiceresult=mysql_query($querystatement,$dblink);
	if(!$invoiceresult) reportError(300,"Could Not Retrieve Invoices: ".mysql_error($dblink)." -- ".$querystatement);

	$querystatement="SELECT notes.id,notes.type,notes.subject,notes.category,
					ELT(notes.importance+3,\"&nbsp;\",\"&middot;\",\"-\",\"*\",\"!\",\"!!\")  as importance
					FROM notes WHERE notes.attachedtabledefid=2 AND notes.attachedid=".$clientid." 
					ORDER BY notes.category,notes.type,notes.importance DESC,notes.creationdate";
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
				<select style="width:99%" size="12" class="mono" id="invoice" onChange="selectEditEnable(this)">
					<?php if(!mysql_num_rows($invoiceresult)) {?>
						<option value="" class="choiceListBlank">No Records</option>
					<?php } else while($invoicerecord=mysql_fetch_array($invoiceresult)) {
							if($invoicerecord["totalti"]<0)
								$invoicerecord["totalti"]="-$".number_format($invoicerecord["totalti"],2);
							else
								$invoicerecord["totalti"]="$".number_format($invoicerecord["totalti"],2);
							$thedisplay=sprintf("%s%'|11s%'|5d%'|20s",substr($invoicerecord["status"],0,1),$invoicerecord["odate"],$invoicerecord["id"],$invoicerecord["totalti"]);
							$thedisplay=str_replace("|","&nbsp;",$thedisplay);			
					?>
						<option value="<?php echo $invoicerecord["id"]?>" class="selectListItems <?php echo $invoicerecord["status"]?>"><?php echo $thedisplay?></option>
					<?php }?>
				</select>
			</fieldset>
			<?php }?>
			<fieldset>
				<legend>notes / tasks / events</legend>
				<div style="padding:0px;padding-right:5px;" align="right">
					<button id="noteedit" type="button" class="invisibleButtons" onClick="addEditRecord('edit','note','<?php echo getAddEditFile(12)?>')"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-edit-disabled.png" align="absmiddle" alt="edit" width="16" height="16" border="0" /></button><button type="button" class="invisibleButtons" onClick="addEditRecord('new','note','<?php echo getAddEditFile(12,"add")?>')"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/button-plus.png" align="absmiddle" alt="new" width="16" height="16" border="0" /></button>
				</div>
				<select style="width:99%" size="9" id="note" onChange="selectEditEnable(this)">
					<?php if(!mysql_num_rows($noteresult)) {?>
						<option value="" class="choiceListBlank">No Records</option>
					<?php } else while($noterecord=mysql_fetch_array($noteresult)) {
							if(strlen($noterecord["subject"])>17)
								$noterecord["subject"]=substr($noterecord["subject"],0,17)."...";
							$thedisplay=sprintf("%s %s %s %s",$noterecord["type"],$noterecord["category"],$noterecord["importance"],$noterecord["subject"]);
							$thedisplay=str_replace("|","&nbsp;",$thedisplay);			
					?>
						<option value="<?php echo $noterecord["id"]?>" class="selectListItems" ><?php echo $thedisplay?></option>
					<?php }?>
				</select>		
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