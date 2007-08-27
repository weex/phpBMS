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
	class menus extends phpbmsTable{
	
		function formatVariables($variables){
			switch($variables["radio"]){
				case "cat":
					$variables["link"] = "";
					$variables["parentid"] = 0;
				break;
				case "search":
					$variables["link"] = $variables["linkdropdown"];
				break;
				case "link":
				default:
			}
		
			return $variables;
		}
	
		
		function updateRecord($variables, $modifiedby = NULL){
			
			$variables = $this->formatVariables($variables);
		
			parent::updateRecord($variables, $modifiedby);
		}
	
	
		function insertRecord($variables, $createdby = NULL){
			
			$variables = $this->formatVariables($variables);
		
			return parent::insertRecord($variables, $createdby );
		}
	
	
		function displayTableDropDown($selectedlink){
	
			$querystatement="select id, displayname from tabledefs order by displayname";
			$thequery=$this->db->query($querystatement);
			
			echo "<select id=\"linkdropdown\" name=\"linkdropdown\">\n";
			while($therecord=$this->db->fetchArray($thequery)){
				echo "<option value=\"search.php?id=".$therecord["id"]."\" ";
	
				if ($selectedlink == "search.php?id=".$therecord["id"]) 
					echo "selected=\"selected\"";
	
				echo " >".$therecord["displayname"]."</option>\n";
			}
			echo "</select>\n";
		
		}//end method
		
		
		function displayParentDropDown($selectedpid,$id=0){
	
			if($id=="")$id=0;
			$querystatement="SELECT id, name FROM menu WHERE id!=".$id." and parentid=0 and (link=\"\" or link is null) ORDER BY displayorder";
			$thequery=$this->db->query($querystatement);
	
			echo "<select name=\"parentid\" id=\"parentid\">\n";
			echo "<option value=\"0\" ";
	
			if ($selectedpid=="0")
				echo "selected=\"selected\"";
				
			echo " >-- none --</option>\n";
			while($therecord=$this->db->fetchArray($thequery)){
				echo "<option value=\"".$therecord["id"]."\" ";
				if ($selectedpid==$therecord["id"]) 
					echo "selected=\"selected\"";
	
				echo " >".$therecord["name"]."</option>\n";
			}
			echo "</select>\n";
			
		}//end method
	
	}//end class
}//end if


if(class_exists("searchFunctions")){
	class menuSearchFunctions extends searchFunctions{
	
		function delete_record(){
			
			//passed variable is array of user ids to be revoked
			$whereclause = $this->buildWhereClause();
			$verifywhereclause = $this->buildWhereClause("menu.parentid");
			
			$querystatement = "SELECT id FROM menu WHERE ".$verifywhereclause;
			$queryresult = $this->db->query($querystatement);

			if (!$this->db->numRows($queryresult)){
				$querystatement = "DELETE FROM menu WHERE ".$whereclause;
				$queryresult = $this->db->query($querystatement);
			}
			
			$message=$this->buildStatusMessage();
			$message.=" deleted.";
			return $message;
		}
	
	}//end class
}//end if
?>