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


	$querystatement="SELECT id,
					clients.type, clients.city,clients.state,clients.postalcode,
					if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as thename
					FROM clients
					WHERE creationdate>= DATE_SUB(NOW(),INTERVAL ".$interval.") 
					ORDER BY creationdate DESC LIMIT 0,50
	";
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
			if($i==1) $i=2; else $i=1;		
			$displayType=str_pad($therecord["type"],10,".",STR_PAD_RIGHT);
			$displayType=ucwords(str_replace(".","&nbsp;",$displayType));
			$displayCSZ=$therecord["city"].", ".$therecord["state"]." ".$therecord["postalcode"];
			if($displayCSZ==",  ") $displayCSZ="&nbsp;";
		?><tr onclick="document.location='<?php echo getAddEditFile($db,2)."?id=".$therecord["id"] ?>&amp;backurl='+encodeURIComponent('<?php echo APP_PATH."modules/base/snapshot.php"?>')" class="qr<?php echo $i?>">
			<td align="left" nowrap="nowrap"><?php echo $therecord["id"]?></td>
			<td align="left" nowrap="nowrap"><?php echo $therecord["type"]?></td>
			<td><?php echo htmlQuotes($therecord["thename"])?></td>
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
						invoices.statusid = 4						
						AND invoices.type='Order'
						AND invoices.totalti-invoices.amountpaid = 0
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
