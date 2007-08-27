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
	class roles extends phpbmsTable{
	
		function updateRecord($variables, $modifiedby = NULL){	
			parent::updateRecord($variables, $modifiedby);

			if($variables["userschanged"]==1)
				$this->assignUsers($variables["id"],$variables["newusers"]);
		}
	
	
		function insertRecord($variables, $createdby = NULL){	
			$theid = parent::insertRecord($variables, $createdby);
			
			if($variables["userschanged"]==1)
				$this->assignUsers($theid,$variables["newusers"]);
				
			return $theid;
		}
	
	
		function assignUsers($id,$users){
			$querystatement="DELETE FROM rolestousers WHERE roleid=".$id;
			$queryresult=$this->db->query($querystatement);
	
			$newusers=explode(",",$users);
	
			foreach($newusers as $theuser)
				if($theuser!=""){
					$querystatement="INSERT INTO rolestousers (roleid,userid) VALUES(".$id.",".$theuser.")";
					$queryresult=$this->db->query($querystatement);
				}
		}
	
	
		function displayUsers($id,$type){
			$querystatement="SELECT users.id,concat(users.firstname,' ',users.lastname) as name
							FROM users INNER JOIN rolestousers ON rolestousers.userid=users.id 
							WHERE rolestousers.roleid=".((int) $id);
			$assignedquery=$this->db->query($querystatement);
	
			$thelist=array();
			
			if($type=="available"){
				$excludelist=array();
				while($therecord=$this->db->fetchArray($assignedquery))
					$excludelist[]=$therecord["id"];
					
				$querystatement="SELECT id,concat(users.firstname,' ',users.lastname) as name FROM users WHERE revoked=0 AND portalaccess=0";
				$availablequery=$this->db->query($querystatement);
	
				while($therecord=$this->db->fetchArray($availablequery))
					if(!in_array($therecord["id"],$excludelist))
						$thelist[]=$therecord;		
			} else 
				while($therecord=$this->db->fetchArray($assignedquery))
					$thelist[]=$therecord;
					
			foreach($thelist as $theoption){
				?>	<option value="<?php echo $theoption["id"]?>"><?php echo htmlQuotes($theoption["name"])?></option>
		<?php 
			}
		}//end function
	
	}//end class
}//end if
?>