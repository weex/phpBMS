<?php
/*
 $Rev: 285 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-27 14:05:27 -0600 (Mon, 27 Aug 2007) $
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
class relatedClient{

	var $clientid = "";

	function relatedClient($db, $clientid){

		$this->db = $db;

		$this->clientid = $clientid;

	}//end method

	function getClientInfo(){

		$querystatement = "
			SELECT
				`id`,
				`firstname`,
				`lastname`,
				`company`
			FROM
				`clients`
			WHERE
				`uuid`='".$this->clientid."'
		";

		$queryresult = $this->db->query($querystatement);
		$therecord = $this->db->fetchArray($queryresult);

		$thename = "";
		if($therecord["lastname"]){

			$thename = $therecord["lastname"];

			if($therecord["firstname"])
				$thename .= ", ".$therecord["firstname"];

			if($therecord["company"])
				$thename .= " (".$therecord["company"].")";
		} else
			$thename = $therecord["company"];

		$thereturn["name"] = $thename;
		$thereturn["id"] = $therecord["id"];

		return $thereturn;

	}//end method

}//end class

class aritemPayments{

	function aritemPayments($db, $aritem){

		$this->db = $db;
		$this->aritem = $aritem;

		$this->_get();

	}//end method


	function _get(){

		$querystatement = "
			SELECT
				receiptitems.applied,
				receiptitems.discount,
				receiptitems.taxadjustment,
				receipts.id,
				receipts.receiptdate,
				receipts.posted,
				paymentmethods.name,
				receipts.status
			FROM
				(receiptitems INNER JOIN receipts ON receiptitems.receiptid = receipts.uuid)
				LEFT JOIN paymentmethods ON receipts.paymentmethodid = paymentmethods.uuid
			WHERE
				receiptitems.aritemid = ".mysql_real_escape_string($this->aritem["uuid"]);

			if($this->aritem["type"] == "deposit")
				$querystatement.="
					AND receipts.uuid != ".mysql_real_escape_string($this->aritem["relatedid"]);

			$querystatement.=
				"
			ORDER BY
				receipts.posted,
				receipts.receiptdate,
				receipts.id";

		$this->queryresult = $this->db->query($querystatement);

		$querystatement = "
			SELECT
				SUM(receiptitems.applied) AS applied,
				SUM(receiptitems.discount) AS discount,
				SUM(receiptitems.taxadjustment) AS taxadjustment
			FROM
				receiptitems
			WHERE
				receiptitems.aritemid = ".mysql_real_escape_string($this->aritem["uuid"]);

		$totalresult = $this->db->query($querystatement);

		$this->totals = $this->db->fetchArray($totalresult);

		if($this->aritem["type"] == "deposit")
			$this->totals["applied"] = $this->totals["applied"] + $this->aritem["amount"];

	}//end method


	function show(){

		?><table border="0" cellpadding="0" cellspacing="0" class="querytable" id="paymentTable">

			<thead>
				<tr>
					<th align="left">status</th>
					<th align="left" nowrap="nowrap">receipt id</th>
					<th align="left">date</th>
					<th align="right" width="100%">payment</th>
					<th align="right">applied</th>
					<th align="right">discount</th>
					<th align="right" nowrap="nowrap">tax adj.</th>
				</tr>
			</thead>

			<tfoot>
				<tr class="queryfooter">
					<td colspan="4" align="right">total: <strong><?php echo formatVariable($this->totals["applied"] + $this->totals["discount"] + $this->totals["taxadjustment"], "currency")?></strong> </td>
					<td align="right"><?php echo formatVariable($this->totals["applied"],"currency")?></td>
					<td align="right"><?php echo formatVariable($this->totals["discount"],"currency")?></td>
					<td align="right"><?php echo formatVariable($this->totals["taxadjustment"],"currency")?></td>
				</tr>
			</tfoot>

			<tbody>
				<?php
					if($this->db->numRows($this->queryresult)){

						$postedGroup = "";
						$row = 1;
						while($therecord = $this->db->fetchArray($this->queryresult)) {

							$row = ($row ==1)?2:1;

							if($postedGroup != $therecord["posted"]){

								$postedGroup = $therecord["posted"];

								$title = ($therecord["posted"] == 1)? "Posted Payments" : "Pending Payments (non-posted)";

								?><tr class="queryGroup">
									<td colspan="7"><?php echo $title?></td>
								</tr><?php

							}//end if

							if(!$therecord["name"])
								$therecord["name"] = "other";

							if($therecord["id"] == $this->aritem["relatedid"] && $this->aritem["type"] == "deposit")
								$therecord["applied"] = -1 * $therecord["applied"];

							?><tr class="row<?php echo $row?>">
								<td><?php echo $therecord["status"]?></td>
								<td><?php echo $therecord["id"]?></td>
								<td><?php echo formatFromSQLDate($therecord["receiptdate"])?></td>
								<td align="right"><?php echo $therecord["name"]?></td>
								<td align="right"><?php echo formatVariable($therecord["applied"],"currency")?></td>
								<td align="right"><?php echo formatVariable($therecord["discount"],"currency")?></td>
								<td align="right"><?php echo formatVariable($therecord["taxadjustment"],"currency")?></td>
							</tr><?php
						}//end while

					} else {
						?><tr class="norecords"><td colspan="7" >No payments recorded</td></tr><?php
					}//endif
				?>
			</tbody>
		</table><?php

	}//end method

}//end class


if(class_exists("searchFunctions")){

	class aritemsSearchFunctions extends searchFunctions{

		function run_aging(){

			goURL("modules/bms/aritems_aging.php");

		}//end method

	}//end class

}//end if
?>