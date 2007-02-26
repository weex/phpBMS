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

function showTodaysClients($interval="1 DAY"){
	global $dblink;
	$querystatement="SELECT id,
					clients.type, clients.city,clients.state,clients.postalcode,
					if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as thename
					FROM clients
					WHERE creationdate>= DATE_SUB(NOW(),INTERVAL ".$interval.") 
					ORDER BY creationdate DESC LIMIT 0,50
	";
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Error Retrieving System Messages: ".mysql_error($dblink)."<br />".$querystatement);	
	if(mysql_num_rows($queryresult)){
		?><table border="0" cellpadding="0" cellspacing="0" class="querytable">
			<tr>
				<th align="center">ID</th>
				<th align="center">Type</th>
				<th align="left" width="100%">Name</th>
				<th nowrap align="right">Location</th>
			</tr>
		<?php 
		$i=1;
		while($therecord=mysql_fetch_array($queryresult)){
			if($i==1) $i=2; else $i=1;		
			$displayType=str_pad($therecord["type"],10,".",STR_PAD_RIGHT);
			$displayType=ucwords(str_replace(".","&nbsp;",$displayType));
			$displayCSZ=$therecord["city"].", ".$therecord["state"]." ".$therecord["postalcode"];
			if($displayCSZ==",  ") $displayCSZ="&nbsp;";
		?><tr onclick="document.location='<?php echo getAddEditFile(2)."?id=".$therecord["id"] ?>'" class="qr<?php echo $i?>">
			<td align="center"><?php echo $therecord["id"]?></td>
			<td align="center"><?php echo $therecord["type"]?></td>
			<td><?php echo htmlQuotes($therecord["thename"])?></td>
			<td align="right" nowrap><?php echo $displayCSZ?></td>
		</tr><?php }?>
		<tr class="queryfooter">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><?php $reccount=mysql_num_rows($queryresult);echo $reccount?> record<?php if($reccount!=1) echo "s"?></td>
		</tr>
		</table><?php
	} else {?><div class="small disabledtext">no clients/prospects entered in last day</div><?php
	}
}

function showTodaysOrders($interval="1 DAY"){
	global $dblink;
	$querystatement="SELECT invoices.id,
					invoicestatuses.name as status,
					if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as thename,
					invoices.totalti as total,
					invoices.totalti-invoices.amountpaid as amtdue
					FROM (invoices INNER JOIN clients ON invoices.clientid=clients.id) INNER JOIN invoicestatuses on invoices.statusid=invoicestatuses.id
					WHERE invoices.creationdate>= DATE_SUB(NOW(),INTERVAL ".$interval.") AND (invoices.type=\"Order\")
					ORDER BY invoices.creationdate DESC LIMIT 0,50
	";
	
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Error Retrieving System Messages: ".mysql_error($dblink)."<br />".$querystatement);
	if(mysql_num_rows($queryresult)){
		?><table border="0" cellpadding="0" cellspacing="0" class="querytable">
			<tr>
				<th align="left">ID</th>
				<th align="center">Status</th>
				<th width="100%" align="left">Name</th>
				<th align="right">Total</th>
				<th align="right">Due</th>
			</tr>
		<?php 
		$i=1;
		$total=0;
		$totaldue=0;
		while($therecord=mysql_fetch_array($queryresult)){				
			if($i==1) $i=2; else $i=1;
			$total+=$therecord["total"];
			$totaldue+=$therecord["amtdue"];
		?><tr onClick="document.location='<?php echo getAddEditFile(3)."?id=".$therecord["id"] ?>'" class="qr<?php echo $i?>">
			<td><?php echo $therecord["id"]?></td>
			<td align=center><?php echo $therecord["status"]?></td>
			<td><?php echo htmlQuotes($therecord["thename"])?></td>
			<td align="right"><?php echo numberToCurrency($therecord["total"])?></td>
			<td align="right"><?php echo numberToCurrency($therecord["amtdue"])?></td>
		</tr><?php }?>
		<tr class="queryfooter">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><?php echo numberToCurrency($total)?></td>
			<td align="right"><?php echo numberToCurrency($totaldue)?></td>
		</tr>
		</table><?php
	} else {?><div class="small disabledtext">no orders entered in last day</div><?php
	}
}

if (hasRights(20)) {?>
<div class="box" id="bmsBox">
	<button class="graphicButtons buttonDown" id="todaysOrdersLink"><span>Up</span></button>	
	<h2><a href="../../search.php?id=3">Recent Orders</a></h2>
	<div id="todaysOrders">
		<div class="fauxP">
			<?php 
			if(date("D")=="Mon")
				$interval="3 DAY";
			else
				$interval="1 DAY";
			showTodaysOrders($interval) 
			?>
		</div>
	</div>
		
	<button class="graphicButtons buttonDown" id="todaysClientsLink"><span>Up</span></button>	
	<h2><a href="../../search.php?id=2">Recently Added Clients/Prospects</a></h2>
		<div id="todaysClients"><div class="fauxP"><?php showTodaysClients($interval)?></div></div>
</div>
<?php }?>				
