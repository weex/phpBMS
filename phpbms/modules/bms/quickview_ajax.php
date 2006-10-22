<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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
	
	$querystatement="SELECT clients.id, clients.firstname, clients.lastname, clients.company,
					if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as name,
					clients.type, clients.inactive, clients.category,
					clients.address1,clients.address2,clients.city,clients.state,clients.postalcode,clients.country,
					clients.shiptoaddress1,clients.shiptoaddress2,clients.shiptocity,clients.shiptostate,clients.shiptopostalcode,clients.shiptocountry,
					clients.workphone,clients.homephone,clients.mobilephone,clients.fax, clients.email,clients.webaddress,clients.comments
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
<div class="bodyline" id="theDetails">
<h1><?php echo htmlQuotes($therecord["name"])?></h1>

<div id="rightSideDiv" class="box">
	<?php if($therecord["type"]!="prospect") {?>

	<div class="salesNotesButtons">
		<button id="invoiceedit" type="button" class="graphicButtons buttonEditDisabled" onclick="addEditRecord('edit','invoice','<?php echo getAddEditFile(3)?>')"><span>edit</span></button>
		<button type="button" class="graphicButtons buttonNew" onclick="addEditRecord('new','invoice','<?php echo getAddEditFile(3,"add")?>')"><span>new</span></button>			
	</div>
	
	<h2>Sales</h2>
	
	<div class="fauxP">
	<div id="salesTable" class="smallQueryTableHolder">
		<?php if(!mysql_num_rows($invoiceresult)) {?>
			<div class="small"><em>no records</em></div>
		<?php } else {?>
		<table border="0" cellpadding="0" cellspacing="0" class="smallQueryTable">
			<tr>
				<th align="left">ID</th>
				<th align="left">Type</th>
				<th align="left">Date</th>
				<th align="right" width="100%">Total</th>
			</tr>
		<?php while($invoicerecord=mysql_fetch_array($invoiceresult)) {
				if($invoicerecord["type"]=="VOID")
					$invoicerecord["totalti"]="-----"
		?><tr onClick="selectEdit(this,<?php echo $invoicerecord["id"]?>,'invoice')">
			<td><?php echo $invoicerecord["id"]?></td>
			<td><?php echo $invoicerecord["type"]?></td>
			<td><?php echo dateFormat($invoicerecord["thedate"])?></td>
			<td align="right"><?php echo currencyFormat($invoicerecord["totalti"])?></td>
		</tr>
		<?php }?></table><?php }?>	
	</div>
	</div>
	<?php }?>
	
	
	
	<div class="salesNotesButtons">
		<button id="noteedit" type="button" class="graphicButtons buttonEditDisabled" onClick="addEditRecord('edit','note','<?php echo getAddEditFile(12)?>')"><span>edit</span></button>
		<button type="button" class="graphicButtons buttonNew" onClick="addEditRecord('new','note','<?php echo getAddEditFile(12,"add")?>')"><span>new</span></button>
	</div>
	
	<h2>Notes</h2>
	
	<div class="fauxP">
	<div id="notesTable"  class="smallQueryTableHolder">
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
	</div>
</div>

<div id="leftSideDiv" class="box">
		
		<h2>Name</h2>
		<button id="editClient" type="button" onclick="addEditRecord('edit','client','<?php echo getAddEditFile(2)?>')" class="Buttons">edit</button>
		<?php if($therecord["firstname"] || $therecord["lastname"]) {?>
		<p class="RDNames">name:</p>
		<p class="RDData Uppers important"><?php echo htmlentities($therecord["firstname"]." ".$therecord["lastname"])?></p>
		<?php } ?>
		<?php if($therecord["company"]){?>
		<p class="RDNames">company:</p>
		<p class="RDData Uppers important"><?php echo htmlentities($therecord["company"])?></p>
		<?php } ?>

		<p class="RDNames"><br/>type:</p>
		<p class="RDData important"><br/><?php echo htmlentities($therecord["type"])?></p>

		<?php if($therecord["category"]){?>
		<p class="RDNames">category:</p>
		<p class="RDData important"><?php echo htmlentities($therecord["category"])?></p>
		<?php } ?>

		<h2>Address</h2>
		<p class="RDNames">main:</p>
		<p class="RDData important"><?php 
		
			$theaddress=htmlentities($therecord["address1"])."<br />";
			if($therecord["address2"]) $theaddress.=htmlentities($therecord["address2"])."<br />";
			if($therecord["city"]) $theaddress.=htmlentities($therecord["city"]).", ";
			$theaddress.=htmlentities($therecord["state"])." ";
			$theaddress.=htmlentities($therecord["postalcode"])." ";
			$theaddress.=htmlentities($therecord["country"]);
			
			echo $theaddress?>
		</p>
		
		<?php 
		
		$theaddress=htmlentities($therecord["shiptoaddress1"])."<br />";
		if($therecord["shiptoaddress2"]) $theaddress.=htmlentities($therecord["address2"])."<br />";
		if($therecord["shiptocity"]) $theaddress.=htmlentities($therecord["city"]).", ";
		$theaddress.=htmlentities($therecord["shiptostate"])." ";
		$theaddress.=htmlentities($therecord["shiptopostalcode"])." ";
		$theaddress.=htmlentities($therecord["shiptocountry"]);

		if($theaddress!="<br />  "){?>
		<p class="RDNames">shipping:</p>
		<p class="RDData important"><?php echo $theaddress?></p>
		<?php } ?>

		<h2>Contact</h2>

		<?php if($therecord["workphone"]){?>
		<p class="RDNames">work phone:</p>
		<p class="RDData important"><?php echo htmlentities($therecord["workphone"])?></p>
		<?php } ?>

		<?php if($therecord["homephone"]){?>
		<p class="RDNames">home phone:</p>
		<p class="RDData important"><?php echo htmlentities($therecord["homephone"])?></p>
		<?php } ?>

		<?php if($therecord["mobilephone"]){?>
		<p class="RDNames">mobile phone:</p>
		<p class="RDData important"><?php echo htmlentities($therecord["mobilephone"])?></p>
		<?php } ?>

		<?php if($therecord["fax"]){?>
		<p class="RDNames">fax:</p>
		<p class="RDData important"><?php echo htmlentities($therecord["fax"])?></p>
		<?php } ?>
		
		<p>&nbsp;</p>
		
		<?php if($therecord["email"]){?>
		<p class="RDNames">e-mail addres:</p>
		<p class="RDData important">
			<button type="button" class="graphicButtons buttonEmail" onclick="document.location='mailto:<?php echo $therecord["email"]?>'"><span>send email</span></button>
			&nbsp;<a href="mailto:<?php echo $therecord["email"]?>"><?php echo htmlentities($therecord["email"])?></a>
		</p>
		<?php } ?>

		<?php if($therecord["webaddress"]){?>
		<p class="RDNames">web site:</p>
		<p class="RDData important">
			<button type="button" class="graphicButtons buttonWWW" onclick="window.open('<?php echo $therecord["webaddress"]?>')"><span>visit site</span></button>
			&nbsp;<a href="<?php echo $therecord["webaddress"]?>" target="_blank"><?php echo htmlentities($therecord["webaddress"])?></a>
		</p>
		<?php } ?>

		<?php if($therecord["comments"]){?>
		<h2>Memo</h2>
		<p id="RDMemo"><?php echo str_replace("\n","<br />",htmlentities($therecord["comments"]))?></p>
		<?php } ?>
	</div>
	<div id="endClient"></div>
</div>
<?php 
include("../../footer.php");
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