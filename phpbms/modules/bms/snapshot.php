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
		?><table border="0" cellpadding="0" cellspacing="0" class="smallQueryTable">
			<tr>
				<th align="left">ID</th>
				<th align="left">Type</th>
				<th align="left" width="100%">Name</th>
				<th nowrap align="right">City, State/Prov Zip/Postal</th>
			</tr>
		<?php 	
		while($therecord=mysql_fetch_array($queryresult)){
			$displayType=str_pad($therecord["type"],10,".",STR_PAD_RIGHT);
			$displayType=ucwords(str_replace(".","&nbsp;",$displayType));
			$displayCSZ=$therecord["city"].", ".$therecord["state"]." ".$therecord["postalcode"];
			if($displayCSZ==",  ") $displayCSZ="&nbsp;";
		?><tr onClick="document.location='<?php echo getAddEditFile(2)."?id=".$therecord["id"] ?>'">
			<td><?php echo $therecord["id"]?></td>
			<td><?php echo $therecord["type"]?></td>
			<td><?php echo $therecord["thename"]?></td>
			<td align="right"><?php echo $displayCSZ?></td>
		</tr><?php }?></table><?php
	} else {?><div class="small disabledtext">no clients/prospects entered in last day</div><?php
	}
}

function showTodaysOrders($interval="1 DAY"){
	global $dblink;
	$querystatement="SELECT invoices.id,
					invoices.status,
					if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as thename,
					invoices.totalti as total,
					invoices.totalti-invoices.amountpaid as amtdue
					FROM invoices INNER JOIN clients ON invoices.clientid=clients.id
					WHERE invoices.creationdate>= DATE_SUB(NOW(),INTERVAL ".$interval.") AND (invoices.type=\"Order\")
					ORDER BY invoices.creationdate DESC LIMIT 0,50
	";
	
	$queryresult=mysql_query($querystatement,$dblink);
	if(!$queryresult) reportError(300,"Error Retrieving System Messages: ".mysql_error($dblink)."<br />".$querystatement);
	if(mysql_num_rows($queryresult)){
		?><table border="0" cellpadding="0" cellspacing="0" class="smallQueryTable">
			<tr>
				<th align="left">ID</th>
				<th align="left">Status</th>
				<th width="100%" align="left">Name</th>
				<th align="right">Total</th>
				<th align="right">Due</th>
			</tr>
		<?php 
		while($therecord=mysql_fetch_array($queryresult)){				
		?><tr onClick="document.location='<?php echo getAddEditFile(3)."?id=".$therecord["id"] ?>'">
			<td><?php echo $therecord["id"]?></td>
			<td><?php echo $therecord["status"]?></td>
			<td><?php echo $therecord["thename"]?></td>
			<td align="right"><?php echo currencyFormat($therecord["total"])?></td>
			<td align="right"><?php echo currencyFormat($therecord["amtdue"])?></td>
		</tr><?php }?>
		</table><?php
	} else {?><div class="small disabledtext">no orders entered in last day</div><?php
	}
}

if ($_SESSION["userinfo"]["accesslevel"]>=20) {?>
<div class="box" style="display:inline-block;">	
	<div style="float:right;cursor:pointer;cursor:hand;"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-up.png" align="absmiddle" alt="hide" onClick="hideSection(this,'TodaysOrders')" width="16" height="16" border="0" /></div>
	<h2 style="margin-top:4px;"><a href="../../search.php?id=3">Recent Orders</a></h2>
		<div id="TodaysOrders">
			<?php 
			if(date("D")=="Mon")
				$interval="3 DAY";
			else
				$interval="1 DAY";
			showTodaysOrders($interval) 
			?>
		</div>
		
	<div style="float:right;cursor:pointer;cursor:hand;"><img src="<?php echo $_SESSION["app_path"]?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/image/button-up.png" align="absmiddle" alt="hide" onClick="hideSection(this,'TodaysClients')" width="16" height="16" border="0" /></div>
	<h2 style="margin-top:4px;"><a href="../../search.php?id=2">Recently Added Clients/Propects</a></h2>
		<div id="TodaysClients"><?php showTodaysClients($interval)?></div>
</div>
<?php }?>				
