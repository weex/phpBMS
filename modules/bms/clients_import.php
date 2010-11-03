<?php
/*
 $Rev: 258 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-08 21:59:28 -0600 (Wed, 08 Aug 2007) $
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

	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");
	include("include/imports.php");
	include("include/parsecsv.lib.php");

	//if you need to overide the phpbmsTable class make sure to include the modules file

	include("modules/bms/include/clients.php");
	include("modules/bms/include/addresses.php");
	include("modules/bms/include/addresstorecord.php");

	//If the addedit page will be accessd directly from a page other than the
	// basic search results page, you may want to grab and pass the previous URL
	//with the following code

	//===================================================
	if(!isset($_GET["backurl"]))
		$backurl = NULL;
	else{
		$backurl = $_GET["backurl"];
		if(isset($_GET["refid"]))
			$backurl .= "?refid=".$_GET["refid"];
	}
	//===================================================

	if(isset($_GET["id"]))
		$tabledefid = ((int)$_GET["id"]);


	//Here you invoke the table and import classes.  If you are going to use the standard phpbmsTable class
	// for updates and the such you would access it like this

			//$thetable = new phpbmsTable($db,$tabledefid);
			//$import = new phpbmsImport($thetable);

	//if you are going to overide the class you would instantiate
	// like this

			$importType = "csv";
			if(isset($_POST["importType"])){

				switch($_POST["importType"]){

					case 0:
						$importType = "csv";
					break;

					case 1:
						$importType = "sugarcrm";
					break;

				}//end switch

			}//end if

	 		$thetable = new clients($db,"tbld:6d290174-8b73-e199-fe6c-bcf3d4b61083",$backurl);
			$import = new clientsImport($thetable, $importType);

	//Next we process the form (if submitted) and
	// return the current record as an array ($therecord)
	// or if this is a new record, it returns the defaults
	$therecord = $import->processImportPage();

	//make sure that we set the status message id the processing returned one
	// (e.g. "Record Updated")
	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];

	$pageTitle = ($therecord["title"])?$therecord["title"]:"General Table Import";

	$phpbms->cssIncludes[] = "pages/imports.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/clients_import.js";


	// We need to define them here in the head
	// so that any necessay javascript is loaded appropriately.

		//Form Elements
		//==============================================================

		// Create the form
		$theform = new importForm();
		$theform->enctype = "multipart/form-data";
		//if you need to set specific form vaiables (like enctype, or extra onsubmit
		// you can set those form properties here.

		// for each field we will use, create the field object and add it to
		// the forms list.

		$list = array("phpBMS csv file" => 0, "Sugar CRM (v5.5.0)" => 1);
		$default = 0;
		if(isset($_POST["importType"]))
			$default = ((int) $_POST["importType"]);
		$theinput = new inputBasicList("importType", $default, $list, "File Type");
		$theform->addField($theinput);


		// lastly, use the jsMerge method to create the final Javascript formatting
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?><div class="bodyline">
	<!--
		Next we start the form.  This also prints the H1 with title, and top save,cancel buttons
		If you need to have other buttons, or need a specific top, you will need to create your form manually.
	-->
	<?php $theform->startForm($pageTitle, $import->pageType, count($import->transactionRecords))?>

	<div id="leftSideDiv">
		<!-- /* This next input is to store the temporary mysql table used for the confirmation insert */ -->
		<input id="tempFileID" name="tempFileID" type="hidden" value="<?php echo $import->tempFileID?>" />
		<!-- /* This next input is to determine the action of the cancel button (i.e. whether to redirect to backurl or not)*/ -->
		<!-- /* This next input also determines whether the file/import fieldset will be displayed or if the preview sections will be displayed*/ -->
		<input id="pageType" name="pageType" type="hidden" value="<?php echo $import->pageType?>" />

		<?php
		if($import->pageType == "main"){ ?>
		<fieldset>
			<legend>import</legend>

			<p><?php $theform->showField("importType"); ?></p>

			<div id="uploadlabel">
				<p>
					<label for="import">file</label><br />
					<input id="import" name="import" type="file" size="64"/><br/>
				</p>

				<div id="info0" class="info">
					<p>
						For any file that is a comma seperated value (csv) file:
					</p>
					<p>
						Delimiters are commas (,) and enclosures are double-quotes (").  If you
						wish to escape a double-quote character inside of an enclosure, add another
						double-quote character (e.g ...,"Benny ""The Jet"" Rodriguez",...), or with
						a backslash character (\) (e.g ...,"Benny \"The Jet\" Rodriguez",...).
					</p>
					<p>
						The first row of your csv file should be the field-names of the table(s)
						that you wish to import to.  Additional lines will be the actual data
						that will be imported.
					</p>
					<p>
						When entering in currency, dates, or times use the format in the bms's configuration
						(e.g. use English, US style dates if that is what the bms is configured to).
					</p>
				</div>
				<div id="info1" class="info" style="display:none;">
					<p>
						For any file that is a comma seperated value (csv) file:
					</p>
					<p>
						Delimiters are commas (,) and enclosures are double-quotes ("). <span class="notes" >(This
						is done by default by Sugar CRM)</span> If you wish to escape a double-quote character
						inside of an enclosure, add another double-quote character (e.g ...,"Benny
						""The Jet"" Rodriguez",...).
					</p>
					<p>
						The first row of your csv file should be the field-names of the table(s)
						that you wish to import to.  Additional lines will be the actual data
						that will be imported. <span class="notes" >(Again, this is done by default by Sugar CRM)</span>
					</p>
					<p>
						When entering in currency, dates, or times use the format in the bms's configuration
						(e.g. use English, US style dates if that is what the bms is configured to).
					</p>
				</div>
			</div>

		</fieldset>
		<?php
		}else{
		?>
			<!-- This input is to be able to keep the importType the same for both the initial upload and the final upload -->
			<input id="importType" name="importType" type="hidden" value="<?php echo (isset($_POST["importType"]))?($_POST["importType"]):('0'); ?>" />
		<?php
		}//end if

		if($import->error && $import->pageType != "main"){
			?>
			<h2>Import Errors</h2>
			<div id="importError">
				<ul>
				<?php echo $import->error ?>
				</ul>
			</div>
			<?php
		}//end if
		$import->displayTransaction($import->transactionRecords,$import->table->fields);
	?>
	</div>
	<div id="createmodifiedby" >
	<?php
		//Last, we show the create/modifiy with the bottom save and cancel buttons
		// and then close the form.
		$theform->showButtons(2, $import->pageType, count($import->transactionRecords));
		?></div><?php
		$theform->endForm();
	?>
</div>
<?php include("footer.php");?>