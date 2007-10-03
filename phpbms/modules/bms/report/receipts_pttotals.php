<?php
/*
 $Rev: 290 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-27 18:15:00 -0600 (Mon, 27 Aug 2007) $
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

if(!class_exists("phpbmsReport"))
	include("../../../report/report_class.php");

class receiptsPTTotals extends phpbmsReport{
	
	function receiptsPTTotals($db){

		parent::phpbmsReport($db);

	}//end method
	
	
	function generate() {
	
		$querystatement = "
			SELECT
				receipts.id,
				receipts.status,
				receipts.posted,
				receipts.readytopost,
				receipts.receiptdate,
				receipts.amount,
				if(clients.lastname!='',concat(clients.lastname,', ',clients.firstname,if(clients.company!='',concat(' (',clients.company,')'),'')),clients.company) AS client,
				paymentmethods.id AS pid,
				paymentmethods.name
			FROM
				((receipts INNER JOIN clients ON receipts.clientid = clients.id) 
				LEFT JOIN paymentmethods ON receipts.paymentmethodid = paymentmethods.id)";

		if($this->sortorder)
			$this->sortorder = "paymentmethods.id DESC, ".$this->sortorder;
		else
			$this->sortorder = "paymentmethods.id DESC, receipts.receiptdate";
		
		$querystatement = $this->assembleSQL($querystatement);
				
		$this->queryresult = $this->db->query($querystatement);
	
	}//end method
	
		
	function show()	{
	
		global $phpbms;
		$db = &$this->db;
	
		$phpbms->cssIncludes[] = "reports.css";
		$phpbms->showMenu = false;
		$phpbms->showFooter = false;
		
		include("header.php");

		?>
		
		<div id="container">
			<h1>Receipt Payment Totals</h1>
			<table border="0" cellpadding="0" cellspacing="0" id="results">
			
				<thead>
					<tr>
						<th align="left">id</th>
						<th align="left">date</th>
						<th align="center">posted</th>
						<th align="center">rtp</th>
						<th align="left">status</th>
						<th align="left" width="100%">client</th>
						<th align="right" id="lastHeader">amount</th>
					</tr>
				</thead>
			
				<tbody>
				<?php 
					
					$grandTotals = array(
						"total" => 0,
						"count" => 0
					);
					
					$payment = array(
						"type" => "",
						"total" => 0,
						"count" => 0
					);

					while($therecord = $this->db->fetchArray($this->queryresult)){
					
						if($therecord["pid"] != $payment["type"]){
						
							if($payment["type"] != ""){
							
								?>
								
									<tr class="groupTotals">
										<td colspan="6" align="right">count: <?php echo $payment["count"]?></td>
										<td align="right"><?php echo formatVariable($payment["total"], "currency")?></td>
									</tr>
								
								<?php 
							
							
							}//endif
						
							?>
							
							<tr class="groupHeaders">
								<td colspan="7">
									<p class="name"><?php 

										if($therecord["pid"] > 0 )
											echo formatVariable($therecord["name"]);
										else
											echo "Other";
											
									?></p></td>
							</tr>
							
							<?php
							
							$grandTotals["count"] += $payment["count"];
							$grandTotals["total"] += $payment["total"];
							
							$payment = array(
								"type" => $therecord["pid"],
								"total" => 0,
								"count" => 0
							);
							
						}//end if
						
						$payment["total"] += $therecord["amount"];
					
						?>
						
						<tr>
							<td><?php echo $therecord["id"]?></td>
							<td><?php echo formatVariable($therecord["receiptdate"], "date")?></td>
							<td align="center"><?php echo formatVariable($therecord["posted"], "boolean")?></td>
							<td align="center"><?php echo formatVariable($therecord["readytopost"], "boolean")?></td>
							<td nowrap="nowrap"><?php echo formatVariable($therecord["status"])?></td>
							<td><?php echo formatVariable($therecord["client"])?></td>
							<td align="right"><?php echo formatVariable($therecord["amount"], "currency")?></td>
						</tr>						
						
						<?php
					
						$payment["count"] ++;
						
					}//end while
					
					$grandTotals["count"] += $payment["count"];
					$grandTotals["total"] += $payment["total"];
					
					?>
								
					<tr class="groupTotals">
						<td colspan="6" align="right">count: <?php echo $payment["count"]?></td>
						<td align="right"><?php echo formatVariable($payment["total"], "currency")?></td>
					</tr>
								
					<tr class="grandTotals">
					
						<td colspan="6" align="right">count: <?php echo $grandTotals["count"]?></td>
						<td align="right"><?php echo formatVariable($grandTotals["total"], "currency")?></td>					
						
					</tr>
				
				</tbody>
			
			</table>
		
		</div>
		
		<?php
		
		include("footer.php");		
	
	}//end method

}//end method


	//PROCESSING 
	//========================================================================
	
	if(!isset($noOutput)){

		session_cache_limiter('private');
	
		require("../../../include/session.php");
	
		$report = new receiptsPTTotals($db);
		$report->setupFromPrintScreen();
		$report->generate();	
		$report->show();	
		
	}//end if

?>