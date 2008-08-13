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
	class products extends phpbmsTable{
	
		function getDefaults(){
			$therecord = parent::getDefaults();
			
			$therecord["type"]="Inventory";
			$therecord["status"]="In Stock";		
			$therecord["taxable"]=1;
			
			return $therecord;
		}
	
		function getPicture($name){
			if (function_exists('file_get_contents')) {
				$file = addslashes(file_get_contents($_FILES[$name]['tmp_name']));
			} else {
				// If using PHP < 4.3.0 use the following:
				$file = addslashes(fread(fopen($_FILES[$name]['tmp_name'], 'r'), filesize($_FILES[$name]['tmp_name'])));
			}
			
			return $file;
		}
		
		//This method is for when there is not categoryid specified
		function _getCategoryID(){
			
			$querystatement = "
				SELECT
					`id`
				FROM
					`productcategories`
				ORDER BY
					`inactive` ASC
				LIMIT 1;
				";
			
			$queryresult = $this->db->query($querystatement);
			$therecord = $this->db->fetchArray($queryresult);
			
			if(!isset($therecord["id"]))
				$therecord["id"] = 0;
			
			if(!$therecord["id"]){
				
				$querystatement = "
					INSERT INTO
						`productcategories`
						(
							`name`,
							`inactive`,
							`description`,
							`webenabled`,
							`webdisplayname`,
							`createdby`,
							`creationdate`,
							`modifiedby`,
							`modifieddate`
						)VALUES(
							'import_category',
							'0',
							'This category was automatically created by an import routine.  Please replace with a more applicable record',
							'0',
							'',
							'".((int) $_SESSION["userinfo"]["id"])."',
							NOW(),
							'".((int) $_SESSION["userinfo"]["id"])."',
							NOW()
						);
					";
				
				$queryresult = $this->db->query($querystatement);
				$therecord["id"] = $this->db->insertId();
				
			}//end if
			
			return $therecord["id"];
			
		}//end method --_getCategoryID--
		
		function formatVariables($variables){
			
			if(!isset($variables["unitprice"]))
				$variables["thumchange"] = 0;
			
			if(!isset($variables["unitcost"]))
				$variables["thumchange"] = 0;
			
			$variables["unitprice"] = currencyToNumber($variables["unitprice"]);
			$variables["unitcost"] = currencyToNumber($variables["unitcost"]);
			
			if(!isset($variables["thumbchange"]))
				$variables["thumbchange"] = NULL;
			
			if($variables["thumbchange"]){
	
				if($variables["thumbchange"] == "upload"){
					$variables["thumbnail"] = $this->getPicture("thumbnailupload");
					$variables["thumbnailmime"] = $_FILES['thumbnailupload']['type'];					
				} else {
					//delete
					$variables["thumbnail"] = NULL;
					$variables["thumbnailmime"] = NULL;
				}
			
			} // end thumbnail picture change if
			
			
			if(!isset($variables["picturechange"]))
				$variables["picturechange"] = NULL;
			
			if($variables["picturechange"]){
	
				if($variables["picturechange"] == "upload"){
					$variables["picture"] = $this->getPicture("pictureupload");
					$variables["picturemime"] = $_FILES['pictureupload']['type'];					
				} else {
					//delete
					$variables["picture"] = NULL;
					$variables["picturemime"] = NULL;
				}
	
			}//end main picture change if			
			
			if(!isset($variables["categoryid"]))
				$variables["categoryid"] = 0;

			if(!$variables["categoryid"])
				$variables["categoryid"] = $this->_getCategoryID();
			
			return $variables;
		
		}//end function
		
	
		function updateRecord($variables, $modifiedby = NULL){
	
			//need to override field information if they don't have the rights
			if(!hasRights(20)){
				unset($this->fields["partnumber"]);
				unset($this->fields["partname"]);
				unset($this->fields["upc"]);
				unset($this->fields["description"]);
				unset($this->fields["inactive"]);
				unset($this->fields["taxable"]);
				unset($this->fields["unitprice"]);
				unset($this->fields["unitcost"]);
				unset($this->fields["unitofmeasure"]);
				unset($this->fields["type"]);
				unset($this->fields["categoryid"]);			
	
				unset($this->fields["webenabled"]);			
				unset($this->fields["keywords"]);			
				unset($this->fields["webdescription"]);
	
			} else {
				//user has rights.  Let's format everything.
	
				$variables = $this->formatVariables($variables);
	
		
			}		
			
			if($variables["packagesperitem"])
				$variables["packagesperitem"]=1/$variables["packagesperitem"];
	
	
			parent::updateRecord($variables, $modifiedby);
	
			//need to reset the field information.  If they did not have rights
			// we temporarilly removed the fields to be updated.
			$this->getTableInfo();
		}
	
	
		function insertRecord($variables, $createdby = NULL){

		
			$variables = $this->formatVariables($variables);
			
			if(isset($variables["packagesperitem"]))
				if($variables["packagesperitem"])
					$variables["packagesperitem"]=1/$variables["packagesperitem"];

			$newid = parent::insertRecord($variables, $createdby);
			
			return $newid;
		}
	
	
		function checkNumberCategories(){
			$querystatement="SELECT count(id) AS thecount FROM productcategories WHERE inactive=0";
			$queryresult=$this->db->query($querystatement);
			$therecord=$this->db->fetchArray($queryresult);
	
			return $therecord["thecount"];
		}
	
	
		function displayProductCategories($categoryid){
			$querystatement="SELECT `id`,`name` FROM `productcategories` WHERE `inactive` =0 OR `id` =".((int) $categoryid)." ORDER BY `name`";
			$queryresult=$this->db->query($querystatement);
	
			?><select name="categoryid" id="categoryid">
				<?php 
					while($therecord = $this->db->fetchArray($queryresult)){
						?><option value="<?php echo $therecord["id"]?>" <?php if($categoryid==$therecord["id"]) echo "selected=\"selected\""?>><?php echo $therecord["name"];?></option>
						<?php
					}
				?>
			</select><?php
			
		}
	
	}//end products class
}//end if
?>