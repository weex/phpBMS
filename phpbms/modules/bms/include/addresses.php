<?php
/*
 $Rev: 254 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-07 18:38:38 -0600 (Tue, 07 Aug 2007) $
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

if(class_exists("phpbmsTable")){
	class addresses extends phpbmsTable{


		function getName($tabledefid, $recordid){
		
			switch($tabledefid){
			
				case 2:
				default:
					$querystatement = "
						SELECT 
							if(clients.lastname!='',concat(clients.lastname,', ',clients.firstname,if(clients.company!='',concat(' (',clients.company,')'),'')),clients.company) AS thename
						FROM
							clients
						WHERE
							id = ".$recordid;
					break;
							
			}//endswitch tabledefid
		
			$queryresult = $this->db->query($querystatement);
			
			if($this->db->numRows($queryresult)){
			
				$therecord = $this->db->fetchArray($queryresult);
				return htmlQuotes($therecord["thename"]);
				
			} else
				return "orphaned record: tableDefinitionID=".$tabledefid.", RecordID:".$recordid;
		
		}//end method - getName
		

		function showAssociations($addressid){
			// This function generates a table listing all the records 
			// associated with the address record.
			
			$querystatement = "
				SELECT
					addresstorecord.tabledefid,
					addresstorecord.recordid,
					addresstorecord.primary,
					addresstorecord.defaultshipto					
				FROM 
					addresstorecord
				WHERE
					addresstorecord.addressid =".((int) $addressid)."
				ORDER BY
					addresstorecord.tabledefid
			";
			
			$queryresult = $this->db->query($querystatement);
			
			?>
			<table class="querytable" cellspacing="0" cellpadding="0" border="0">
				<thead>
					<tr>
						<th align="left" nowrap="nowrap">id</th>
						<th width="100%" align="left">record</th>
						<th align="center" nowrap="nowrap">primary</th>
						<th align="center" nowrap="nowrap">default ship to</th>
					</tr>
				</thead>
				<tfoot>
					<tr class="queryfooter">
						<td colspan="4">&nbsp;</td>
					</tr>
				</tfoot>
				<tbody>
				<?php 
					$row =1;
					while($therecord = $this->db->fetchArray($queryresult)) {
					
						$row = ($row==1)? 2:1;
						
						?><tr class="qr<?php echo $row?>">
							<td><?php echo $therecord["recordid"]?></td>
							<td><?php echo $this->getName($therecord["tabledefid"], $therecord["recordid"])?></td>
							<td align="center"><?php echo formatVariable($therecord["primary"], "boolean")?></td>
							<td align="center"><?php echo formatVariable($therecord["defaultshipto"], "boolean")?></td>
						</tr>
						<?php
						
					}//endwhile - therecord					
				?>
				</tbody>
			</table>
			<?php
		
		}//end method - showAssociation


		// CLASS OVERRIDES =================================================
			
		function formatVariables($variables){
						
			return $variables;			
		
		}//end function
	
	
		function updateRecord($variables, $modifiedby = NULL){
	
			$variables = $this->formatVariables($variables);

			$thereturn = parent::updateRecord($variables, $modifiedby);
						
			return $thereturn;
			
		}//end method
		
		
		function insertRecord($variables, $createdby = NULL){
		
			$variables = $this->formatVariables($variables);
	
			$newid = parent::insertRecord($variables, $createdby);
									
			return $newid;
			
		}//end method
			
	}//end class
	
}//end if

if(class_exists("searchFunctions")){
	class filesSearchFunctions extends searchFunctions{
	
		function delete_record(){
		
			$whereclause = $this->buildWhereClause();
			$attachmentwhereclause = $this->buildWhereClause("attachments.fileid");
		
			$querystatement = "DELETE FROM attachments WHERE ".$attachmentwhereclause." AND attachments.fileid!=1;";
			$queryresult = $this->db->query($querystatement);
		
			$querystatement = "DELETE FROM files WHERE ".$whereclause." AND files.id!=1;";
			$queryresult = $this->db->query($querystatement);
			
			$message = $this->buildStatusMessage();
			$message.=" deleted";
			return $message;
		}
	
	}//end class
}//end if
?>