<?php
class mailchimpUpdate{

    function updateSettings($variables){
		
		if(!isset($variables["mailchimp_secure"]))
			$variables["mailchimp_secure"] = 0;
        
		/**
		  *   Check for a valid api key.
		  */
		if($variables["apikey_changed"] == "1" && $variables["mailchimp_apikey"] != ""){
            include_once("include/MCAPI.class.php");
            
            $api = new MCAPI($variables["mailchimp_apikey"]);
            $api->ping();
            if ($api->errorCode){
                unset($variables["mailchimp_apikey"]);
                $this->updateErrorMessage = "Unable to change the MailChimp apikey: ".$api->errorMessage." (".$api->errorCode.")";
				return $variables;
            }//end if
            
        }//end if
		
		
		/**
		  *  Check for valid list id 
		  */
		if($variables["apilist_changed"] == "1" && $variables["mailchimp_list_id"] != ""){
			include_once("include/MCAPI.class.php");
			
			/**
			  *  Check to see if api is already defined (from a possible api key check)
			  *  If not, define it and check the key/connection
			  */
			if(!isset($api)){
				$api = new MCAPI($variables["mailchimp_apikey"]);
				$api->ping();
				if ($api->errorCode){
					unset($variables["mailchimp_list_id"]);
					$this->updateErrorMessage = "Unable to change the MailChimp list id: ".$api->errorMessage." (".$api->errorCode.")";
					return $variables;
				}//end if
			}//end if
			
			/**
			  *   Look up the lists
			  */
			$lists = $api->lists();
			if ($api->errorCode){
				unset($variables["mailchimp_list_id"]);
				$this->updateErrorMessage = "Unable to change the MailChimp list id: ".$api->errorMessage." (".$api->errorCode.")";
				return $variables;
			}else{
				
				/**
				  *  Check to see if list id is valid 
				  */
				$validId = false;
				foreach($lists as $list)
					if($list["id"] == $variables["mailchimp_list_id"]){
						$validId = true;
						break;
					}//endif
				
				if(!$validId){
					unset($variables["mailchimp_list_id"]);
					$this->updateErrorMessage = "Unable to change the MailChimp list id: the id does not match a valid id on the account.";
					return $variables;
				}else{
					
					/**
					  *  Check to see if the list has a uuid. 
					  */
					$hasUuid = false;
					$mergeVars = $api->listMergeVars($variables["mailchimp_list_id"]);
					if($api->errorCode){
						unset($variables["mailchimp_list_id"]);
						$this->updateErrorMessage = "Unable to change the MailChimp list id: ".$api->errorMessage." (".$api->errorCode.")";
						return $variables;
					}//end if
						
					foreach($mergeVars as $mergeVar){
						
						if($mergeVar["tag"] == "UUID"){
							$hasUuid = true;
							break;
						}//end if
						
					}//end foreach
					
					/**
					  *  If it doesn't have a uuid field, create it. 
					  */
					if(!$hasUuid){
						$req = array(
							"req"=>true,
							"public"=>false,
							"field_type"=>"text"
						);
						$api->listMergeVarAdd($variables["mailchimp_list_id"], "UUID", "phpbms unique user id", $req);
						if($api->errorCode){
							unset($variables["mailchimp_list_id"]);
							$this->updateErrorMessage = "Unable to change the MailChimp list id: ".$api->errorMessage." (".$api->errorCode.")";
							return $variables;
						}//end if
						
					}//end if
					
					
					/**
					  *  If the date list id has changed, the last sync date must be reset: 
					  */
					$variables["mailchimp_last_sync_date"] = "";
					
				}//end if
				
			}//end if
			
		}//end if
        
        return $variables;
        
    }//end function
    
    
}//end class


class mailchimpDisplay{

		function getFields($therecord){

			global $db;

			$theinput = new inputField("mailchimp_apikey",$therecord["mailchimp_apikey"],"MailChimp Apikey", false, NULL, 48);
			$fields[] = $theinput;
			
			$theinput = new inputCheckbox("mailchimp_secure", $therecord["mailchimp_secure"], "secure");
			$fields[] = $theinput;
			
			$theinput = new inputField("mailchimp_batch_limit", $therecord["mailchimp_batch_limit"], "Batch Limit");
			$fields[] = $theinput;
			
			$theinput = new inputField("mailchimp_list_id", $therecord["mailchimp_list_id"], "List Id");
			$fields[] = $theinput;
			
			$theinput = new inputField("mailchimp_last_sync_date", $therecord["mailchimp_last_sync_date"], "Last Sync Date");
			$theinput->setAttribute("class", "uneditable");
			$theinput->setAttribute("readonly", "readonly");
			$fields[] = $theinput;

			return $fields;
		}//end method --getFields--

		function display($theform,$therecord){
?>
<div class="moduleTab" title="MailChimp">
<fieldset>
	<legend>Main</legend>

	<input type="hidden" id="apikey_changed" name="apikey_changed" value="0" />
    <p><?php echo $theform->showField("mailchimp_apikey");?></p>
	<input type="hidden" id="listid_changed" name="apilist_changed" value="0" />
	<p><?php echo $theform->showField("mailchimp_list_id");?></p>
	<p><?php echo $theform->showField("mailchimp_secure");?></p>
	<p><?php echo $theform->showField("mailchimp_batch_limit");?></p>
	<p><?php echo $theform->showField("mailchimp_last_sync_date");?></p>

    </fieldset>
    <p class="updateButtonP"><button type="button" class="Buttons UpdateButtons">save</button></p>
</div>

<?php
		}//end method
	}//end class
?>