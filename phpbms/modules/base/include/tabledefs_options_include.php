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
	
	class tableOptions {
	
		function tableOptions($db, $tabledefid){
			
			$this->db = $db;
			$this->tabledefid = ((int) $tabledefid);
						
		}//end method


		function getTableName(){
			
			$querystatement = "
				SELECT 
					displayname 
				FROM 
					tabledefs 
				WHERE 
					id=".$this->tabledefid;
					
			$queryresult = $this->db->query($querystatement);
			
			$therecord = $this->db->fetchArray($queryresult);

			return formatVariable($therecord["displayname"]);
		
		}//end method
		
	
		function getDefaults(){
		
			return array(
				"id" => NULL,
				"name" => "",
				"option" => "",
				"othercommand" => 0,
				"roleid" => 0,
				"displayorder" => 0
			);
		
		}//end method
		
		
		function get($id = NULL){
		
			$querystatement = "
				SELECT
					tableoptions.id,
					tableoptions.name,
					tableoptions.option,
					tableoptions.othercommand,
					tableoptions.displayorder,
					tableoptions.roleid,
					roles.name AS rolename
				FROM
					tableoptions LEFT JOIN roles ON tableoptions.roleid = roles.id
				WHERE";
			if($id)
				$querystatement .= "
					tableoptions.id = ".((int) $id);
			else
				$querystatement .= "
					tabledefid = ".$this->tabledefid;			
			
			$querystatement .= "
				ORDER BY
					tableoptions.othercommand,
					tableoptions.displayorder,
					tableoptions.name";
					
			return $this->db->query($querystatement);
		
		}//end method


		function showRecords($queryresult){
		
			global $phpbms;
		
		?><table border="0" cellpadding="3" cellspacing="0" class="querytable">
			<thead>
				<tr>
					<th nowrap="nowrap"align="left" width="100%">name</th>
					<th nowrap="nowrap"align="center">allowed</th>
					<th nowrap="nowrap"align="left">function name</th>
					<th nowrap="nowrap"align="left">access</th>
					<th nowrap="nowrap"align="right">display order</th>
					<th nowrap="nowrap">&nbsp;</th>
				</tr>
			</thead>
			
			<tfoot>
				<tr class="queryfooter">
					<td colspan="6">&nbsp;</td>
				</tr>
			</tfoot>

			<tbody>
				<?php 
				
					if($this->db->numRows($queryresult)){
					
						$row = 1;
						
						$other = 3;
						
						while($therecord = $this->db->fetchArray($queryresult)){ 

							$row = ($row == 1) ? 2 : 1;
							
							if($therecord["othercommand"] !== $other){
																
								?><tr class="queryGroup"><td colspan="6"><?php echo ($therecord["othercommand"] == 1)? "Additional Commands" : "Integrated Features";?></td></tr><?php 

								$other = $therecord["othercommand"];
							}//end if
							
						?>
				
					<tr class="qr<?php echo $row?> noselects">
					
						<td nowrap="nowrap" class="important">
							<?php 

							if($therecord["othercommand"]) 
								echo formatVariable($therecord["option"]); 
							else
								echo formatVariable($therecord["name"]);
								
							?>
						</td>
						
						<td nowrap="nowrap" align="center">
							<?php 

							if($therecord["othercommand"]) 
								echo "&nbsp;"; 
							else
								echo formatVariable($therecord["option"], "boolean");
								
							?>
						</td>
						
						<td nowrap="nowrap" align="center">
							<?php 

							if($therecord["othercommand"]) 
								echo formatVariable($therecord["name"]);
							else
								echo "&nbsp;"; 
								
							?>
						</td>

						<td nowrap="nowrap">
							<?php $phpbms->displayRights($therecord["roleid"], $therecord["rolename"])?>
						</td>	
				 
						<td nowrap="nowrap" align="right">
							<?php echo $therecord["displayorder"] ?>
						</td>	

						<td nowrap="nowrap" valign="top">
						
							<button id="edt<?php echo $therecord["id"]?>" type="button" class="graphicButtons buttonEdit"><span>edit</span></button>
							
							<button id="del<?php echo $therecord["id"]?>" type="button" class="graphicButtons buttonDelete"><span>delete</span></button>
							
						</td>
					</tr>	
				<?php 
					
					}//endwhile
					
				} else { 
				
					?><tr class="norecords"><td colspan="6">No Options Set</td></tr><?php
				
				}//end if
				
				?>
				</tbody>
				
			</table>
			<?php 
		
		}//end method
		
		
		function add($variables){
		
			if(!isset($variables["ifOption"]))
				$variables["ifOption"] = 0;

			if($variables["type"]){
				
				$name = $variables["acName"];
				$option = $variables["acOption"];
				
			} else {
			
				$name = $variables["ifName"];
				$option = $variables["ifOption"];

			} //end if

			$insertstatement = "
				INSERT INTO
					tableoptions

					(tabledefid,
					name,
					`option`,
					roleid,
					displayorder,
					othercommand)
				VALUES (
					".$this->tabledefid.",
					'".$name."',
					'".$option."',
					".((int) $variables["roleid"]).",
					".((int) $variables["displayorder"]).",
					".((int) $variables["type"])."
				)";
			
			$this->db->query($insertstatement);

		}//end method

		
		function update($variables){
		
			if(!isset($variables["ifOption"]))
				$variables["ifOption"] = 0;

			$updatestatement = "
				UPDATE
					tableoptions
				SET
					roleid = ".((int) $variables["roleid"]).", 
					displayorder = ".((int) $variables["displayorder"]).",
					othercommand = ".((int) $variables["type"]).", ";
			
			if(!$variables["type"])
				$updatestatement .= "
					name = '".$variables["ifName"]."',
					`option` = '".$variables["ifOption"]."'
				";
			else
				$updatestatement .= "
					name = '".$variables["acName"]."',
					`option` = '".$variables["acOption"]."'
				";			
			
			$updatestatement .= "
				WHERE id =".((int) $variables["id"]);
				
			$this->db->query($updatestatement);

		}//end method

		
		function delete($id){
		
			$deletestatement = "
				DELETE FROM
					tableoptions
				WHERE
					id = ".((int) $id);
					
			$this->db->query($deletestatement);
		
		}//end method
				
		
		function processForm($variables){
		
			switch($variables["command"]){
			
				case "add":
					$this->add($variables);
					$therecord = $this->getDefaults();
					$therecord["statusmessage"] = "Option added";
					break;
					
				case "edit":
					$queryresult = $this->get($variables["id"]);
					$therecord = $this->db->fetchArray($queryresult);
					break;
					
				case "update":
					$this->update($variables);
					$therecord = $this->getDefaults();
					$therecord["statusmessage"] = "Option updated";
					break;
				
				case "delete":
					$this->delete($variables["id"]);
					$therecord = $this->getDefaults();
					$therecord["statusmessage"] = "Option deleted";
					break;
			
				case "cancel":
					$therecord = $this->getDefaults();
					break;

			}//endswitch
			return $therecord;
		
		}//end method
	
	}//end class	
?>