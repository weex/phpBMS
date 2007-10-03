<?php 

	class clientCredit{
		var $db;
		var $id = 0;
	
		function clientCredit($db, $id){
			$this->db = $db;
			$this->id = (int) $id;
		}//end method

		
		function get(){
			$querystatement = "
				SELECT 
					id, 
					type, 
					firstname, 
					lastname, 
					company, 
					hascredit,
					creditlimit
				FROM
					clients
				WHERE
					id=".$this->id;
			
			$queryresult = $this->db->query($querystatement);
			
			$therecord = $this->db->fetchArray($queryresult);
			
			$querystatement = "
				SELECT 
					SUM(`amount` - `paid`) AS outstanding
				FROM
					aritems
				WHERE
					`status` = 'open'
					AND clientid = ".$this->id."
					AND posted = 1";

			$queryresult = $this->db->query($querystatement);
			
			return array_merge($this->db->fetchArray($queryresult), $therecord);

		}//end method
		
			
		function update($variables){
		
			if(!isset($variables["creditlimit"]))
				$variables["creditlimit"] = 0;
			else
				$variables["creditlimit"] = currencyToNumber($variables["creditlimit"]);
				
			if(!isset($variables["hascredit"])){
				$variables["hascredit"] = 0;
				$variables["creditlimit"] = 0;
			}
			
			$variables["hascredit"] = (int) $variables["hascredit"];
			
			$updatestatement = "
				UPDATE
					clients
				SET
					hascredit = ".$variables["hascredit"].",
					creditlimit = ".$variables["creditlimit"]."
				WHERE
					id =".$this->id;

			$this->db->query($updatestatement);

			return true;

		}//endmethod
		
		
		function showHistory($clientid){
		
			$querystatement = "
				SELECT
					relatedid,
					amount,
					paid,
					itemdate,
					posted
				FROM
					aritems
				WHERE
					clientid = ".((int) $clientid)."
					AND `status` = 'open'
				ORDER BY
					posted";
			
			$queryresult = $this->db->query($querystatement);
			
		?><table border="0" cellpadding="0" cellspacing="0" class="querytable" id="openItems">

			<thead>
				<tr>
					<th align="left" nowrap="nowrap">doc ref</th>
					<th align="left" nowrap="nowrap">doc date</th>
					<th align="left" nowrap="nowrap">due date</th>
					<th align="right" width="100%" nowrap="nowrap">ammount</th>
					<th align="right">due</th>
				</tr>
			</thead>
			
			<tfoot>
				<tr class="queryfooter">
					<td colspan="4" align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
				</tr>
			</tfoot>
			
			<tbody>
				<?php
					if($this->db->numRows($queryresult)){
						
						$postedGroup = "";
						$row = 1;
						while($therecord = $this->db->fetchArray($queryresult)) {
							
							$row = ($row ==1)?2:1;
							
							if($postedGroup != $therecord["posted"]){

								$postedGroup = $therecord["posted"];
								
								$title = ($therecord["posted"] == 1)? "Current" : "Pending (non-posted)";
								
								?><tr class="queryGroup">
									<td colspan="5"><?php echo $title?></td>
								</tr><?php
								
							}//end if
							
							$dueDate = strtotime(TERM1_DAYS." days", stringToDate($therecord["itemdate"], "SQL"));
						
							?><tr class="row<?php echo $row?>">
								<td><?php echo $therecord["relatedid"]?></td>
								<td><?php echo formatFromSQLDate($therecord["itemdate"])?></td>
								<td><?php echo dateToString($dueDate)?></td>
								<td align="right"><?php echo formatVariable($therecord["amount"],"currency")?></td>
								<td align="right"><?php echo formatVariable($therecord["amount"] - $therecord["paid"],"currency")?></td>
							</tr><?php 
						}//end while 
						
					} else {
						?><tr class="norecords"><td colspan="5" >No open items</td></tr><?php
					}//endif
				?>
			</tbody>
		</table><?php			
		
		}//end method
		
	}//end class

?>