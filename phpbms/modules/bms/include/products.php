<?php
/*
 $Rev: 254 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-07 18:38:38 -0600 (Tue, 07 Aug 2007) $
 +-------------------------------------------------------------------------+
 | Copyright (c) 2004 - 2010, Kreotek LLC                                  |
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

		var $availableProducts = NULL;

		function products($db, $tabledefid, $backurl = NULL){
			parent::phpbmsTable($db, $tabledefid, $backurl);
		}//end function

		/**
		 * Overriden phpbmstable function
		 */
		function getDefaults(){

			$therecord = parent::getDefaults();

			$therecord["type"] = "Inventory";
			$therecord["status"] = "In Stock";
			$therecord["taxable"] = 1;
			$therecord["categoryid"] = "";
			$therecord["addcats"] = array();

			return $therecord;

		}//end function getDefaults


		/**
		* retruns the file contents uploaded by the form
		*
		* @param string $name name of the post field that was used to upload
		*
		* @retrun mixed the data from the post $name or FALSE on failure.
		*/
        function getPicture($name){

			if (function_exists('file_get_contents'))
				$file = addslashes(file_get_contents($_FILES[$name]['tmp_name']));
			else {

				// If using PHP < 4.3.0 use the following:
				$file = addslashes(fread(fopen($_FILES[$name]['tmp_name'], 'r'), filesize($_FILES[$name]['tmp_name'])));

			}//endif

			return $file;

		}//end function getPicture


		/**
         * function getRecord
         *
         * Retrieves a single record from the database
         *
         * @param integer|string $id the record id or uuid
         * @param bool $useUuid specifies whther the $id is a uuid (true) or not.  Default is false
         *
         * @return array the record as an associative array
         */

		function getRecord($id, $useUuid = false){

			$therecord = parent::getRecord($id, $useUuid);

			$therecord["catschanged"] = 1;
			$therecord["addcats"] = $this->getAdditionalCategories($therecord["uuid"]);

			return $therecord;

		}//end if


		function populateProductArray(){

			// I need id as well to let updates work with our verify function
			// i.e. if its an update on existing record, its ok if the productnumber
			// is not unique iff its already associated to the record being updated
			$this->availableProducts = array();

			$querystatement = "
				SELECT
					`id`,
					`partnumber`
				FROM
					`products`;
				";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult)){

				while($therecord = $this->db->fetchArray($queryresult)){

					$partnumber = $therecord["partnumber"];
					$id = $therecord["id"];

					$this->availableProducts[$partnumber]["id"] = $id;

				}//end while

			}//wndif

		}//end method --populateProductArray--


		function verifyVariables($variables){

			//must have a partnumber...table default is not enough
			if(isset($variables["partnumber"])){

				//must have some sort of partnumber
				if($variables["partnumber"] !== "" || $variables["partnumber"] !== NULL){

					if($this->availableProducts === NULL)
						$this->populateProductArray();

					//can't have this partnumber already chosen
					if(!isset($variables["id"]))
						$tempid = 0;
					else
						$tempid = $variables["id"];

					$temppartnumber = $variables["partnumber"];// using this because it looks ugly to put the brackets within brackets
					if( array_key_exists($variables["partnumber"], $this->availableProducts) ){

						if( $this->availableProducts[$temppartnumber]["id"] !== $tempid )
							$this->verifyErrors[] = "The `partnumber` field must give an unique part number.";

					}else{
						$this->availableProducts[$temppartnumber]["id"] = "aoihweoighaow giuahrweughauerhgaiudsf iaheiugaiuweg iagweiuha wiueg";// impossible id put in
					}//end if

				}else
					$this->verifyErrors[] = "The `partnumber` field must not be blank.";

			}else
				$this->verifyErrors[] = "The `partnumber` field must be set.";


			if(isset($variables["status"])){

				switch($variables["status"]){

					case "In Stock":
					case "Out of Stock":
					case "Backordered":
						break;

					default:
						$this->verifyErrors[] = "The value of the `status` field is invalid.
							It must be 'In Stock', 'Out of Stock', or 'Backordered'.";
						break;

				}//end switch

			}//end if


			if(isset($variables["type"])){

				switch($variables["type"]){

					case "Inventory":
					case "Non-Inventory":
					case "Service":
					case "Kit":
					case "Assembly":
						break;

					default:
						$this->verifyErrors[] = "The value of the `type` field is invalid.
							It must be 'Inventory', 'Non-Inventory', 'Service', 'Kit', or 'Assembly'.";
						break;

				}//end switch

			}//end if

			//check boolean
			if(isset($variables["webenabled"]))
				if($variables["webenabled"] && $variables["webenabled"] != 1)
					$this->verifyErrors[] = "The `webenabled` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["isoversized"]))
				if($variables["isoversized"] && $variables["isoversized"] != 1)
					$this->verifyErrors[] = "The `isoversized` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["isprepackaged"]))
				if($variables["isprepackaged"] && $variables["isprepackaged"] != 1)
					$this->verifyErrors[] = "The `isprepackaged` field must be a boolean (equivalent to 0 or exactly 1).";

			if(isset($variables["taxable"]))
				if($variables["taxable"] && $variables["taxable"] != 1)
					$this->verifyErrors[] = "The `taxable` field must be a boolean (equivalent to 0 or exactly 1).";

			return parent::verifyVariables($variables);

		}//end method --verifyVariables--


		function _commonPrepareVariables($variables){

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

			if(!isset($variables["addcats"]))
				$variables["addcats"] = array();

			if($variables["catschanged"]){
				$variables["addcats"] = stripslashes($variables["addcats"]);
				$variables["addcats"] = json_decode($variables["addcats"], true);
			}//end if

			return $variables;

		}//end method --_commonPrepareVariables--


		function prepareVariables($variables){

			switch($variables["id"]){

				case "":
				case NULL:
				case 0:
					if(!hasRights("role:de7e6679-8bb2-29ee-4883-2fcd756fb120")){
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
						$variables = $this->_commonPrepareVariables($variables);

					}//end if

					if($variables["packagesperitem"])
						$variables["packagesperitem"]=1/$variables["packagesperitem"];

					break;

				default:
					$variables = $this->_commonPrepareVariables($variables);
					if(isset($variables["packagesperitem"]))
						if($variables["packagesperitem"])
							$variables["packagesperitem"] = 1 / $variables["packagesperitem"];
					break;

			}//end switch

			return $variables;

		}//end function prepareVariables

		/**
		 * Overriden phpbmstable function
		 */
		function updateRecord($variables, $modifiedby = NULL, $useUuid = false){

			parent::updateRecord($variables, $modifiedby, $useUuid);

			if($variables["catschanged"])
				$this->updateCategories($variables["uuid"], $variables["addcats"]);

			//need to reset the field information.  If they did not have rights
			// we temporarilly removed the fields to be updated.
			$this->getTableInfo();

		}//end function updateRecord


		/**
		 * Overriden phpbmstable function
		 */
		function insertRecord($variables, $createdby = NULL, $overrideID = false, $replace = false, $useUuid = false){

			if($createdby === NULL)
				$createdby = $_SESSION["userinfo"]["id"];

			$newid = parent::insertRecord($variables, $createdby, $overrideID, $replace, $useUuid);

			if(is_array($newid))
				$uuid = $newid["uuid"];
			else
				$uuid = $variables["uuid"];

			if($variables["catschanged"])
				$this->updateCategories($uuid, $variables["addcats"]);

			return $newid;

		}//end function insertRecord


		/**
		* Retrieves and displays a list of possible product categories
		*
		* @param string $categoryid product category uuid
		*/
		function displayProductCategories($categoryid){

            $categoryid = mysql_real_escape_string($categoryid);

			$querystatement = "
				SELECT
					`uuid`,
					`name`
				FROM
					`productcategories`
				WHERE
					`inactive` = 0 OR `uuid` ='".$categoryid."'
				ORDER BY
					`name`
				";

			$queryresult = $this->db->query($querystatement);

			?>
				<select name="categoryid" id="categoryid">
						<option value="" <?php if($categoryid=="") echo 'selected="selected"'?>>No Master Category</option>
				<?php
					while($therecord = $this->db->fetchArray($queryresult)){

						?>
						<option value="<?php echo $therecord["uuid"]?>" <?php if($categoryid==$therecord["uuid"]) echo 'selected="selected"' ?>><?php echo $therecord["name"];?></option>
						<?php

					}//endwhile
				?>
			</select>
            <?php

		}//end function displayProductCategories


		/**
		 * function getAdditionalCategories
		 * @param $uuid
		 *
		 * @return array Array of category records
		 */

		function getAdditionalCategories($uuid) {

			$thereturn = array();

			$querystatement = "
				SELECT
					productcategories.uuid AS catid,
					productcategories.name,
					productcategories.uuid AS `productcategoryuuid`
				FROM
					(products INNER JOIN productstoproductcategories ON products.uuid = productstoproductcategories.productuuid)
					INNER JOIN productcategories ON productstoproductcategories.productcategoryuuid = productcategories.uuid
				WHERE
					products.uuid = '".$uuid."'
			";

			$queryresult = $this->db->query($querystatement);

			if($this->db->numRows($queryresult))
				while($therecord = $this->db->fetchArray($queryresult))
					$thereturn[] = $therecord;

			return $thereturn;

		}//end if


		/**
		 * displays a list of additional categories associated with the product.
		 *
		 * @param array $categoryArray array of category records
		 *
		 */
		function displayAdditionalCategories($categoryArray){

			?>
			<div id="catDiv">
				<input type="hidden" id="addcats" name="addcats" value="" />
				<input type="hidden" id="catschanged" name="catschanged" value="0" />
			<?php

			$i = 0;

			foreach($categoryArray as $therecord){

				?>
				<div class="moreCats" id="AC<?php echo $i; ?>">
					<input type="text" value="<?php echo formatVariable($therecord["name"]); ?>" id="AC-<?php echo $i ?>" size="30" readonly="readonly"/>
					<input type="hidden" id="AC-CatId-<?php echo $i ?>" value="<?php echo $therecord["catid"];?>" class="catIDs"/>
					<button type="button" class="graphicButtons buttonMinus catButtons" title="Remove Category"><span>-</span></button>
				</div>
				<?php

				$i++;

			}//endwhile

			?></div><?php

		}//end function displayAdditionalCategories


		/**
		 * updates additional categories for product (by wiping current list and adding new ones)
		 *
		 * @param string $recorduuid product's uuid
		 * @param string $categoryList comma separated list of product category uuids
		 *
		 */
		function updateCategories($recorduuid, $categoryList){

			//first remove any existing records
			$deletestatement = "
				DELETE FROM
					`productstoproductcategories`
				WHERE
					`productuuid` = '".$recorduuid."'
				";

			$this->db->query($deletestatement);

			foreach($categoryList as $item){

				$insertstatement = "
					INSERT INTO
						`productstoproductcategories`
						(productuuid, productcategoryuuid)
					VALUES
						(
						'".$recorduuid."',
						'".$item["productcategoryuuid"]."'
						)";

				$this->db->query($insertstatement);

			}//endforeach

		}//end function updateCategories
		
		/*
		 * function api_searchByPartNumber
		 * 
		 * @param array $requestData Array containing the "partnumber" key.
		 * @param bool $returnUuid If true, returns result's uuid , if
		 * false, the id.
		 * 
		 * @return array An array containing response information
		 * @returnf string type The type of response (e.g. 'error' or 'result')
		 * @returnf string message Message explaining the type / result
		 * @returnf array extras Either the array of uuid / ids if no errors
		 * were encountered, or the original $requestData if there was an error
		 */
		
		function api_searchByPartNumber($requestData, $returnUuid = true) {
			
			/**
			  *  verify data integrity. 
			  */
			
			$response["type"] = "";
			$response["message"] = "";
			if(!isset($requestData["partnumber"])){
				$response["type"] = "error";
				$response["message"] = "Data does not contain a key of 'partnumber'";
			}//end if
			
			if(!isset($requestData["webenabled"])){
				$response["type"] = "error";
				if($response["message"])
					$response["message"] .= " or 'webenabled'.";
				else
					$response["message"] = "Data does not contain a key of 'webenabled'.";
				
			}//end if
			
			if(!isset($requestData["inactive"])){
				$response["type"] = "error";
				if($response["message"])
					$response["message"] .= " or 'inactive'.";
				else
					$response["message"] = "Data does not contain a key of 'inactive'.";
				
			}//end if
			
			if($response["type"] == "error")
				return $response;
			
			
			/**
			  *  do query search 
			  */
			$querystatement = "
				SELECT
					`id`,
					`uuid`
				FROM
					`products`
				WHERE
					`partnumber` = '".mysql_real_escape_string($requestData["partnumber"])."'
					AND
					`webenabled` = '".(int)$response["webenabled"]."'
					AND
					`inactive` = '".(int)$response["inactive"]."'
			";
			
			$queryresult = $this->db->query($querystatement);
			
			/**
			  *  report result 
			  */
			$thereturn["message"] = "The function api_searchByPartNumber has been run successfully.";
			$thereturn["type"] = "";
			$thereturn["extras"] = array();
			while($therecord = $this->db->fetchArray($queryresult)){
				
				if($returnUuid)
					$thereturn["extras"][] = $therecord["uuid"];
				else
					$thereturn["extras"][] = $therecord["id"];
					
			}//end while
			
			return $thereturn;
			
		}//end function --api_searchByPartNumber--

	}//end class products

}//end if
?>
