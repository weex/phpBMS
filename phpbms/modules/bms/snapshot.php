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

function showTodaysClients($db){

	if(date("D")=="Mon")
		$interval="3 DAY";
	else
		$interval="1 DAY";


	$querystatement="
		SELECT 
			clients.id,
			clients.type,
			addresses.city,
			addresses.state,
			addresses.postalcode,
			clients.firstname,
			clients.lastname,
			clients.company
		FROM 
			(clients INNER JOIN addresstorecord ON clients.id = addresstorecord.recordid AND addresstorecord.tabledefid=2 AND addresstorecord.primary=1)
			INNER JOIN addresses ON addresstorecord.addressid = addresses.id
		WHERE 
			clients.creationdate >= DATE_SUB(NOW(),INTERVAL ".$interval.") 
		ORDER BY 
			clients.creationdate DESC LIMIT 0,50";
			
	$queryresult=$db->query($querystatement);

	if($db->numRows($queryresult)){
		?><table border="0" cellpadding="0" cellspacing="0" class="querytable">
			<tr>
				<th align="left">ID</th>
				<th align="left">Type</th>
				<th align="left" width="100%">Name</th>
				<th nowrap="nowrap" align="right">Location</th>
			</tr>
		<?php 
		$i=1;
		while($therecord=$db->fetchArray($queryresult)){
		
			$i = ($i==1) ? 2: 1;

			$clientDisplay = $therecord["company"];
			
			$name = $therecord["lastname"].", ".$therecord["lastname"];
			if($name = ", ")
				$name = "";
				
			if(!$clientDisplay)
				$clientDisplay .= $name;
			else
				if($name)
					$clientDisplay .=  " (".$name.")";
						
			$displayCSZ = $therecord["city"].", ".$therecord["state"]." ".$therecord["postalcode"];
			if($displayCSZ == ",  ")
				$displayCSZ = "(unspecified location)";
			
			if($displayCSZ==",  ") $displayCSZ="&nbsp;";
		?><tr onclick="document.location='<?php echo getAddEditFile($db,2)."?id=".$therecord["id"] ?>&amp;backurl='+encodeURIComponent('<?php echo APP_PATH."modules/base/snapshot.php"?>')" class="qr<?php echo $i?>">
			<td align="left" nowrap="nowrap"><?php echo $therecord["id"]?></td>
			<td align="left" nowrap="nowrap"><?php echo $therecord["type"]?></td>
			<td><?php echo htmlQuotes($clientDisplay)?></td>
			<td align="right" nowrap="nowrap"><?php echo $displayCSZ?></td>
		</tr><?php }?>
		<tr class="queryfooter">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><?php $reccount=$db->numRows($queryresult);echo $reccount?> record<?php if($reccount!=1) echo "s"?></td>
		</tr>
		</table><?php
	} else {?><div class="small disabledtext">no clients/prospects entered in last day</div><?php
	}
}

class invoiceList{
	var $db;
	
	var $querystatement;
	var $interval;
	var $queryName;	
	
	function invoiceList($db, $queryName = "Recent Orders"){
		$this->db = $db;

		if(date("D")=="Mon")
			$this->interval="3 DAY";
		else
			$this->interval="1 DAY";

		$this->setQuery($queryName);
	}//end method
	
	
	function setQuery($what){
	
		$this->queryName = $what;
		
		switch($what){
			case "Recent Orders";
				$this->querystatement="
					SELECT 
						invoices.id,
						invoicestatuses.name as `status`,
						if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as `name`,
						invoices.totalti as `total`,
						invoices.totalti-invoices.amountpaid as `due`
					FROM 
						(invoices INNER JOIN clients ON invoices.clientid=clients.id) INNER JOIN invoicestatuses on invoices.statusid=invoicestatuses.id
					WHERE 
						invoices.creationdate>= DATE_SUB(NOW(),INTERVAL ".$this->interval.") 
						AND (invoices.type=\"Order\")
					ORDER BY 
						invoices.creationdate DESC 
					LIMIT 0,50";
				break;

			case "Orders Ready to Post":
				$this->querystatement="
					SELECT 
						invoices.id,
						if(clients.lastname!='',concat(clients.lastname,', ',clients.firstname,if(clients.company!='',concat(' (',clients.company,')'),'')),clients.company) as `name`,
						invoices.totalti as `total`
					FROM 
						(invoices INNER JOIN clients ON invoices.clientid=clients.id)
					WHERE 
						invoices.type != 'Invoice'
						AND invoices.type != 'VOID'
						AND invoices.readytopost =1
					ORDER BY 
						invoices.creationdate DESC 
					LIMIT 0,50";
				break;
		}//endswitch
		
	}//end method


	function show(){
	
		$queryresult = $this->db->query($this->querystatement);
		
		$numRows = $this->db->numRows($queryresult)
		?>
		<h3 class="invoiceLinks"><?php echo $this->queryName; if($numRows) echo " (".$numRows.")"?></h3>
		<div class="invoiceDivs">		
		<?php
		if($numRows){
		
			$numfields = $this->db->numFields($queryresult);
			
			?>
			<div class="fauxP">
			<table border="0" cellpadding="0" cellspacing="0" class="querytable">
				<thead>
					<tr>
						<?php 
						$fieldArray = array();
						for($i=0; $i<$numfields; $i++) {
							
							$fieldName = $this->db->fieldName($queryresult,$i);
							switch($fieldName){

								case "name":
									$fieldArray[$fieldName]["size"]="100%";
								case "id":
								case "status":
									$fieldArray[$fieldName]["align"]="left";
									$fieldArray[$fieldName]["format"]="";
									break;
								case "total":
								case "due":
									$fieldArray[$fieldName]["align"]="right";
									$fieldArray[$fieldName]["format"]="currency";
									break;
								
							}//endswitch
							?>
							<th nowrap="nowrap" align="<?php echo $fieldArray[$fieldName]["align"]?>" <?php 
								if(isset($fieldArray[$fieldName]["size"]))
									echo 'width="'.$fieldArray[$fieldName]["size"].'" '
							?>><?php echo $fieldName ?></th>
						<?php }//endfor?>
					</tr>
				</thead>
				<tfoot>
					<tr class="queryfooter">
						<td colspan="<?php echo $numfields?>">&nbsp;</td>
					</tr>
				</tfoot>
				<tbody>
					<?php 
						$qr = 1;
						while($therecord = $this->db->fetchArray($queryresult)){
							$qr = ($qr==1)? 2 : 1;
							?><tr class="qr<?php echo $qr?>" onclick="document.location='<?php echo getAddEditFile($this->db,3)."?id=".$therecord["id"] ?>&amp;backurl='+encodeURIComponent('<?php echo APP_PATH."modules/base/snapshot.php"?>')"><?php
								foreach($fieldArray as $fieldName=>$thefield){
									?><td align="<?php echo $thefield["align"]?>" <?php if(!isset($thefield["size"])) echo 'nowrap="nowrap"'?>>
										<?php echo formatVariable($therecord[$fieldName],$thefield["format"])?>
									</td><?php									
								}//endforeach
							?></tr><?php
						}//endwhile
					?>
				</tbody>
			</table>
			</div>
			<?php
		} else {
			?><p class="small disabledtext">none</p><?php		
		}
		?></div><?php
	}//endmethod
}//end class


class accountsReceivable {

	function accountsReceivable($db){
	
		$this->db = $db;
	
	}//end method
	
	
	function showTotals(){
	
		$querystatement="
			SELECT
			  SUM((1-(aged1 * aged2 * aged3)) * (amount-paid)) AS current,
			  SUM((amount-paid)*aged1*(1-aged2)) AS term1,
			  SUM((amount-paid)*aged2*(1-aged3)) AS term2,
			  SUM((amount-paid)*aged3) AS term3,
			  SUM(amount-paid) AS due
			FROM
			  aritems
			WHERE
			  `status` = 'open'
			  AND posted=1";
		
		$queryresult = $this->db->query($querystatement);
		
		$therecord = $this->db->fetchArray($queryresult);
		
		?>
			<table class="querytable" border="0" cellpadding="0" cellspacing="0" id="arTotals">
				<thead>
					<tr>
						<th align="right">current</th>
						<th align="right"><?php echo (TERM1_DAYS+1)." - ".TERM2_DAYS?></th>
						<th align="right"><?php echo (TERM2_DAYS+1)." - ".TERM3_DAYS?></th>
						<th align="right"><?php echo (TERM3_DAYS+1)."+"?></th>
						<th align="right">total</th>
					</tr>
				</thead>
				<tbody>
					<tr>
					<?php 
					
						foreach($therecord as $value)
							echo '<td align="right">'.formatVariable($value, "currency").'</td>';
						
					?>
					</tr>
				</tbody>
			</table>
		<?php
	
	}//end method
	
	
	function showRTPReceipts(){
	
		$querystatement = "
			SELECT 
				if(clients.lastname!='',concat(clients.lastname,', ',clients.firstname,if(clients.company!='',concat(' (',clients.company,')'),'')),clients.company) AS client, 
				receipts.id, 
				receipts.receiptdate as 'date', 
				paymentmethods.name as 'payment', 
				receipts.amount as 'amount'
			FROM 
				((receipts INNER JOIN clients ON receipts.clientid = clients.id) LEFT JOIN paymentmethods ON receipts.paymentmethodid = paymentmethods.id) 
			WHERE 
				receipts.posted = 0
				AND receipts.readytopost = 1
			ORDER BY 
				receipts.receiptdate";
		
		$queryresult = $this->db->query($querystatement);
		
		$querystatement = "
			SELECT
				COUNT(id) AS thecount,
				SUM(amount) AS thesum
			FROM
				receipts
			WHERE
				receipts.posted = 0
				AND receipts.readytopost = 1";
		
		$sumresult = $this->db->query($querystatement);
		$sumrecord = $this->db->fetchArray($sumresult);
		
		if($this->db->numRows($queryresult) == 0){
		
			?>
			<div class="small disabledtext">none</div>
			<?php
		} else {
		
			?>
			
			<table border="0" cellpadding="0" cellspacing="0" class="querytable">
				
				<thead>
					<tr>
						<th align="left">id</th>
						<th align="left">date</th>
						<th align="left" width="100%">client</th>
						<th align="left">payment</th>
						<th align="right">amount</th>
					</tr>
				</thead>
				
				<tfoot>
					<tr class="queryfooter">
						<td colspan="5" align="right"><?php echo formatVariable($sumrecord["thesum"], "currency");?></td>
					</tr>
				</tfoot>
				
				<tbody>
				<?php 
				
					$row = 1;
					while($therecord = $this->db->fetchArray($queryresult)){
						
						$row = ($row==1)? 1 : 2;
					
						?>
							<tr class="qr<?php echo $row?> receiptLinks" id="receipt<?php echo $therecord["id"]?>">
								<td><?php echo $therecord["id"]?></td>
								<td><?php echo formatVariable($therecord["date"], "date")?></td>
								<td><?php echo formatVariable($therecord["client"])?></td>
								<td nowrap="nowrap"><?php echo formatVariable($therecord["payment"])?></td>
								<td align="right"><?php echo formatVariable($therecord["amount"], "currency")?></td>
							</tr>
						
						<?php
					
					}//endwhile
				
				?>
				</tbody>
			</table>
			
			<?php
		
		}//end if	

	}//end method
	

}//end class


//PROCESSING
//===============================================================================================

if (hasRights(20) || hasRights(30)) {

	$invoiceList = new invoiceList($db);
?><div class="box" id="invoiceBox">
	<h2>Quotes, Orders &amp; Invoices <button id="bms3" type="button" title="order search screen" class="bmsInfo graphicButtons buttonInfo"><span>view invoices</span></button></h2>

	<?php 
	if (hasRights(20)) 
		$invoiceList->show();

	if (hasRights(30)) {
		$invoiceList->setQuery("Orders Ready to Post");
		$invoiceList->show();
	}//endif has rights?>

</div>
<?php }//endif has rights?>

<?php if (hasRights(20) || hasRights(30)) {?>
<div class="box" id="clientBox">
	<h2>Clients &amp; Prospects <button id="bms2" type="button" title="client/prospect search screen" class="bmsInfo graphicButtons buttonInfo"><span>view clients</span></button></h2>
	<h3 class="clientLinks">Recently Added Clients &amp; Prospects</h3>
	
	<div class="clientDivs"><div class="fauxP"><?php showTodaysClients($db)?></div></div>
</div>	
<?php }//endif has rights?>

<?php 
	if (hasRights(80)) {	
	
		$ar = new accountsReceivable($db);
?>
<div class="box" id="arBox">

	<h2>Accounts Receivable <button id="bms303" type="button" title="Accounts Receivable" class="bmsInfo graphicButtons buttonInfo"><span>view ar</span></button></h2>

	<h3 class="arLinks">Current AR totals</h3>
	<div class="arDivs"><div class="fauxP"><?php $ar->showTotals()?></div></div>
	
	<h3 class="arLinks">Receipts Ready To Post</h3>
	<div class="arDivs"><div class="fauxP"><?php $ar->showRTPReceipts();?></div></div>
	<input id="receiptEdit" type="hidden" value="<?php echo getAddEditFile($db, 304)?>"/>

</div>	
<?php 
	}//endif has rights
?>
