<?php
/*
 $Rev: 267 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-14 13:08:27 -0600 (Tue, 14 Aug 2007) $
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
					$hasCompany = false;
					$hasType = false;
					$mergeVars = $api->listMergeVars($variables["mailchimp_list_id"]);
					if($api->errorCode){
						unset($variables["mailchimp_list_id"]);
						$this->updateErrorMessage = "Unable to change the MailChimp list id: ".$api->errorMessage." (".$api->errorCode.")";
						return $variables;
					}//end if

					$req = array();
					foreach($mergeVars as $mergeVar){

						switch($mergeVar["tag"]){

							case "UUID":
								$hasUuid = true;
								break;

							case "COMPANY":
								$hasCompany = true;
								break;

							case "TYPE":
								$hasType = true;
								break;

						}//end switch

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
						$api->listMergeVarAdd($variables["mailchimp_list_id"], "UUID", "phpBMS unique user id", $req);
						if($api->errorCode){
							unset($variables["mailchimp_list_id"]);
							$this->updateErrorMessage = "Unable to change the MailChimp list id: ".$api->errorMessage." (".$api->errorCode.")";
							return $variables;
						}//end if

					}//end if

					/**
					  *  If it doesn't have a company field, create it.
					  */
					if(!$hasCompany){
						$req = array(
							"req"=>false,
							"public"=>true,
							"field_type"=>"text"
						);
						$api->listMergeVarAdd($variables["mailchimp_list_id"], "COMPANY", "Company", $req);
						if($api->errorCode){
							unset($variables["mailchimp_list_id"]);
							$this->updateErrorMessage = "Unable to change the MailChimp list id: ".$api->errorMessage." (".$api->errorCode.")";
							return $variables;
						}//end if

					}//end if

					/**
					  *  If it doesn't have a type field, create it.
					  */
					if(!$hasType){
						$req = array(
							"req"=>false,
							"public"=>true,
							"field_type"=>"text"
						);
						$api->listMergeVarAdd($variables["mailchimp_list_id"], "TYPE", "Type", $req);
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

			$theinput = new inputField("mailchimp_apikey",$therecord["mailchimp_apikey"],"mailchimp apikey", false, NULL, 48);
			$fields[] = $theinput;

			$theinput = new inputCheckbox("mailchimp_secure", $therecord["mailchimp_secure"], "use ssl connection");
			$fields[] = $theinput;

			$theinput = new inputField("mailchimp_list_id", $therecord["mailchimp_list_id"], "list id");
			$fields[] = $theinput;

			$theinput = new inputField("mailchimp_last_sync_date", $therecord["mailchimp_last_sync_date"], "last sync date");
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

	<p>
		<span class="notes">
			To use this module, you need to create an account with MailChimp
			(<a href="http://mailchimp.com">http://mailchimp.com</a>).
		</span>
	</p>

    <p>
		<?php echo $theform->showField("mailchimp_apikey");?>
		<br/>
		<span class="notes">
			Your MailChimp api key may found under the "API Keys &amp; Info" section
			in your "Account" page (<a href="http://admin.mailchimp.com/account/api" >http://admin.mailchimp.com/account/api</a>).
		</span>
	</p>
	<input type="hidden" id="listid_changed" name="apilist_changed" value="0" />
	<p><?php echo $theform->showField("mailchimp_list_id");?>
		<br/>
		<span class="notes">
			The list id for the list can be found under the list's settings near
			the bottom of the page.  It should say "unique id for list |*list name*|"
			where |*list name*| is the name of the list.
			<br/>
			When selecting a list to use, be aware that the sync process will
			remove records (on the MailChimp side) that do not exist in the client
			table.
		</span>
	</p>
	<p><?php echo $theform->showField("mailchimp_secure");?></p>
	<p><?php echo $theform->showField("mailchimp_last_sync_date");?></p>

    </fieldset>
    <p class="updateButtonP"><button type="button" class="Buttons UpdateButtons">save</button></p>
</div>

<?php
		}//end method
	}//end class
?>
