<?php
	class phpbmsImport{

		var $table;
		var $error = "";
		var $docError = "";
		var $row = 0;
		var $transactionIDs = array();
		var $importType;
		var $data;
		var $revertID = 0;

		// Do not manually override
		var $transactionRecords = array();
		var $tempFileID = 0;
		var $pageType = "main";


		function phpbmsImport($table, $importType = "csv"){

			$this->table = $table;
			$this->importType = $importType;
			switch($this->importType){
				case "csv":
					$this->parser = new parseCSV;
				break;
			}//end switch

			$this->table->db->logError = true;
			//So, that, when there is a db error, it will go all the way through and not just stop
			$this->table->db->stopOnError = false;
			//Won't display db errors, just log them.
			$this->table->db->showError = false;

		}//end method --imports--

		function _parseFromData($data){

			switch($this->importType){

				case "csv":

					if(is_readable($data)){
						$contents = $this->_getFile($data);

						if(is_readable($contents)){
							$this->docError = "invalid csv document";
							return false;
						}//end if

					}//end if

					$this->parser->parse($data);

					if(!count($this->parser->titles) || !count($this->parser->data)){
						$this->docError = "invalid csv document";
						return false;
					}//end if

					return true;
				break;

			}//end swtich

		}//end method --_parseFromFile--


		function _getTransactionData(){
		//needs to be changed for more complicated tables
			$inStatement = "";
			foreach($this->transactionIDs as $theid)
				$inStatement .= $theid.",";

			if($inStatement){

				$inStatement = substr($inStatement, 0, -1);

				$querystatement = "
					SELECT
						*
					FROM
						`".$this->table->maintable."`
					WHERE
						`id` IN (".$inStatement.");
					";

				$queryresult = $this->table->db->query($querystatement);

				while($therecord = $this->table->db->fetchArray($queryresult))
					$this->transactionRecords[] = $therecord;

			}//end if

		}//end method --_getTransactionData--


		function _getFile($fileName){
			if(function_exists('file_get_contents')){
				$file = addslashes(file_get_contents($fileName));
			}else{
				// If using PHP < 4.3.0 use the following:
				$file = addslashes(fread(fopen($fileName, 'r'), filesize($fileName)));
			}//end if

			return $file;
		}//end method --_getFile--

		//DO NOT CALL IN TRANSACTION
		function _storeTempCSV($fileName){
			
			$querystatement = "
				INSERT INTO
					`files`
					(
						`uuid`,
						`name`,
						`description`,
						`file`,
						`type`,
						`roleid`,
						`creationdate`,
						`createdby`,
						`modifiedby`
					)
					VALUES
					(
						'".uuid(getUuidPrefix($this->table->db, "tbld:80b4f38d-b957-bced-c0a0-ed08a0db6475"))."',
						'temporary',
						'This is a temporary import file',
						'".$this->_getFile($fileName)."',
						'phpbms/temp',
						'Admin',
						NOW(),
						'".$_SESSION["userinfo"]["id"]."',
						'".$_SESSION["userinfo"]["id"]."'

					)
				";

			$this->table->db->query($querystatement);

			$id = $this->table->db->insertId();

			if($id)
				$this->tempFileID = ((int) $id);
			else
				$this->error .= '<li> inserting temporary file failure </li>';

		}//end method --_storeTempCSV--


		function _getTempCSV($tempFileID){

			if($tempFileID){

				$querystatement = "
					SELECT
						`file`
					FROM
						`files`
					WHERE
						id = ".((int)$tempFileID)."
					";

				$queryresult = $this->table->db->query($querystatement);

				$therecord = $this->table->db->fetchArray($queryresult);

				return $therecord["file"];

			}//end if

			return false;

		}//end method --_getTempCSV--

		//DO NOT CALL IN TRANSACTION
		function _removeTempCSV($tempFileID = 0){

			$querystatement = "
				DELETE FROM
					`files`
				WHERE
					`type` = 'phpbms/temp'
					AND
					(
						`id` = ".((int)$tempFileID)."
						OR
						`creationdate` <= NOW() - INTERVAL 30 MINUTE
					);
				";

			$queryresult = $this->table->db->query($querystatement);

			$querystatement = "
				ALTER TABLE
					`files`
				AUTO_INCREMENT = ".((int) $tempFileID).";
				";

			$queryresult = $this->table->db->query($querystatement);

		}//end method --_removeTempCSV--

		//DO NOT USE THIS METHOD INSIDE AN OPEN TRANSACTION.
		//IT WILL AUTOMATICALLY COMMIT THE TRANSACTION
		function _revertAutoIncrement($revertID = 0){

			//check to see if there is a revert id (i.e. there was a valid insert)
			if($revertID)
				if(is_numeric($revertID)){

					$querystatement = "
						ALTER TABLE
							`".$this->table->maintable."`
						AUTO_INCREMENT = ".((int) $revertID).";
						";

					$this->table->db->query($querystatement);

				}//end if

		}//end method --_revertAutoIncrement--


		function importRecords($rows, $titles){

			switch($this->importType){

				case "csv":
					//count total fieldnames (top row of csv document)
					$fieldNum = count($titles);

					//the file starts at line number 1, but since line 1 is
					//supposed to be the fieldnames in the table(s), the lines
					//being insereted start @ 2.
					$rowNum = 2;

					//get the data one row at a time
					foreach($rows as $rowData){

						$theid = 0; // set for when verifification does not pass
						$verify = array(); //set for when number of field rows does not match number of titles

						//trim off leading/trailing spaces
						$trimmedRowData = array();

						foreach($rowData as $name => $data)
							$trimmedRowData[$name] = trim($data);

						//check to see if number of fieldnames is consistent for each row
						$rowFieldNum = count($trimmedRowData);

						//if valid, insert, if not, log error and don't insert.
						if($rowFieldNum == $fieldNum){
							$verify = $this->table->verifyVariables($trimmedRowData);
							if(!count($verify)){
								$createdby = NULL;
								$overrideID = true;
								$replace = false;
								if(!isset($trimmedRowData["uuid"])){
									$useUuid = true;
									$thereturn = $this->table->insertRecord($trimmedRowData, $createdby, $overrideID, $replace, $useUuid);
									$theid = $thereturn["id"];
								}else{
									$useUuid = false;
									$thereturn = $this->table->insertRecord($trimmedRowData, $createdby, $overrideID, $replace, $useUuid);
									$theid = $thereturn;
								}
							}//end if
						}else
							$this->error .= '<li> incorrect amount of fields for line number '.$rowNum.'.</li>';

						if($theid){
							//keep track of the ids in the transaction to be able to select them
							//for preview purposes
							$this->transactionIDs[] = $theid;

							//get first id to correct auto increment
							if(!$this->revertID)
								$this->revertID = $theid;
						}else
							$this->error .= '<li> failed insert for line number '.$rowNum.'.</li>';

						foreach($verify as $error)
							$this->error .= '<li class="subError">'.$error.'</li>';

						$rowNum++;

					}//end foreach
				break;

			}//end switch

		}//end method --importRecords--


		function displayTransaction($recordsArray, $fieldsArray){
		//needs to be changed for more complicated tables
			if(count($recordsArray) && count($fieldsArray)){
				?>
				<h2>Import Preview</h2>
				<div id="transactionDiv">
				<table id="transactionTable">
					<thead>
						<tr>
							<?php
							foreach($fieldsArray as $field => $junk){
								?><th align="left" nowrap="nowrap"><?php
								echo formatVariable($field);
								?></th><?php
							}//end foreach
							$field = NULL;
							?>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						foreach($recordsArray as $record){
							?><tr class="qr<?php echo $i ?>" ><?php
							foreach($fieldsArray as $field => $junk){
								?><td nowrap="nowrap"><?php
								echo formatVariable($record[$field]);
								?></td><?php
							}//end foreach
							?></tr><?php
							$i = ($i == 1)?2:1;
						}//end while
						?>
					</tbody>
				</table>
				</div>
				<?php
			}//end if

		}//end method --displayTransaction--


		function processImportPage(){

			$this->table->getTableInfo();

			if(isset($_POST["pageType"]))
				$this->pageType = $_POST["pageType"];

			if(isset($_POST["tempFileID"]))
				$this->tempFileID = ((int)$_POST["tempFileID"]);

			if(!isset($_POST["command"])){

				//happens upon first coming to page

				//remove any other temporary csv files in the `files` table
				//present from previous imports
				$this->_removeTempCSV();

				//check to see if user has the rights to be here.
				//If not, kick him to the no access page.
				if(!hasRights($this->table->importroleid))
					goURL(APP_PATH."noaccess.php");

			}else{
				//form has been submitted
				switch($_POST["command"]){

					//cancel button pressed.
					case "cancel":
						//Cancel button needs to do different things depending upon which page
						//its at.
						if($this->pageType == "main")
							goURL($this->table->backurl);
						else{
							$this->_removeTempCSV($this->tempFileID);
							$therecord["phpbmsStatus"] = "Record(s) Not Imported";
							$this->pageType = "main";
						}//end if
					break;

					case "upload":

						//check for valid file upload
						if(!$_FILES["import"]["error"] && ($_FILES["import"]["size"] > 0)){

							//check and parse the file
							if($this->_parseFromData($_FILES["import"]["tmp_name"])){

								//start transaction
								$this->table->db->startTransaction();

								$this->importRecords($this->parser->data, $this->parser->titles);

								//get data for preview purposes
								$this->_getTransactionData();
								//"undo" any inserts
								$this->table->db->rollbackTransaction();

								//DO NOT CALL IN TRANSACTION
								//ALTER TABLES AUTO COMMIT AND THE FILE NEEDS TO CARRY
								//OVER.
								$this->_revertAutoIncrement($this->revertID);
								$this->_storeTempCSV($_FILES["import"]["tmp_name"]);

							}//end if

						}else
							$this->docError .= "failed file upload";

						//switch page types
						$this->pageType = "confirm";

						if(!$this->error && !$this->docError){
							$therecord["phpbmsStatus"] = "Confirm Import";
						}elseif($this->docError){
							$therecord["phpbmsStatus"] = "Import Error: ".$this->docError;
							$this->pageType = "main";
						}else
							$therecord["phpbmsStatus"] = "Import Error";

					break;

					case "import":

						//get the contents of the stored csv document
						$CSVcontents = $this->_getTempCSV($this->tempFileID);

						//parser uses newline character to be able to parse the last line
						if(substr($CSVcontents,-1,1) != "\n")
							$CSVcontents .= "\n";


						$this->parser->parse($CSVcontents);

						$this->importRecords($this->parser->data, $this->parser->titles);

						$this->table->db->commitTransaction();

						//DO NOT CALL IN TRANSACTION

						//get rid of temporary csv document
						$this->_removeTempCSV($this->tempFileID);

						$therecord["phpbmsStatus"] = "Record(s) Imported";
						//change page type
						$this->pageType = "main";
					break;

				}//end command switch

			}// end if

			//display the title
			$therecord["title"] = $this->table->displayname." Import";
			return $therecord;

		}//end method --imports--

	}//end class --imports--


	//this class is to have different buttons, and no created/modified.
	if(class_exists("phpbmsForm")){
		class importForm extends phpbmsForm{

			function importForm($action = NULL, $method="post", $name="record", $onsubmit="return validateForm(this);", $dontSubmit = true){

				parent::phpbmsForm($action,$method,$name,$onsubmit,$dontSubmit);

			}//end method --importForm--

			function startForm($pageTitle, $pageType, $numberOfRecords = 0){

				?><form action="<?php echo htmlentities($this->action) ?>" method="<?php echo $this->method?>" name="<?php echo $this->name?>" onsubmit="<?php echo $this->onsubmit?>" <?php
					if(isset($this->enctype)) echo ' enctype="'.$this->enctype.'" ';
					if(isset($this->id)) echo ' id="'.$this->id.'" ';
				?>><?php
				if($this->dontSubmit){
					?><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div><?php
				} ?>
				<div id="topButtons"><?php $this->showButtons(1, $pageType, $numberOfRecords); ?></div>
				<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1><?php

			}//end method --startForm--

			function showButtons($ids = 1, $pageType = "main", $numberOfRecords = 0){
				?>
				<div class="importCancels">
					<?php if($pageType == "main"){ ?>
					<input <?php if($ids==1) {?>accesskey="u"<?php }?> title="Upload (alt+u)" id="uploadButton<?php echo $ids?>" name="command" type="submit" value="upload" class="Buttons" />
					<input id="cancelButton<?php echo $ids?>" name="command" type="submit" value="cancel" class="Buttons" <?php if($ids==1) {?>accesskey="x" <?php }?> title="(access key+x)" />
					<?php }else{?>
					<input type="submit" class="Buttons" value="import" name="command" id="import<?php echo $ids?>" title="commit" <?php if($ids==1) {?>accesskey="i"<?php }?> <?php echo ($numberOfRecords? '':'disabled="disabled"') ?>/>
					<input type="submit" class="Buttons" value="cancel" name="command" id="cancelButton<?php echo $ids?>" <?php if($ids==1) {?>accesskey="x"<?php }?> title="rollback"/>
					<?php }//end if ?>
				</div><?php
			}//end method --showButtons--

		}//end class --importForm--
	}
?>
