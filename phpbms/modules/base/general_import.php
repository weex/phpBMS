<?php
/*
 $Rev: 258 $ | $LastChangedBy: brieb $
 $LastChangedDate: 2007-08-08 21:59:28 -0600 (Wed, 08 Aug 2007) $
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

	include("../../include/session.php");
	include("include/tables.php");
	include("include/fields.php");
	include("include/imports.php");
	include("include/parsecsv.lib.php");

        if(!isset($_GET["id"]))
            exit;

	$tabledefid = mysql_real_escape_string($_GET["id"]);

	$querystatement = "
	    SELECT
                `modules`.`name` AS `modulename`,
		`tabledefs`.`maintable` AS `maintable`
	    FROM
                `tabledefs` INNER JOIN `modules` ON `tabledefs`.`moduleid` = `modules`.`uuid`
	    WHERE
                `tabledefs`.`uuid` = '".$tabledefid."'";

	$queryresult = $db->query($querystatement);

	$thereturn = $db->fetchArray($queryresult);

	//try to include table specific functions
        $tableFile = "../".$thereturn["modulename"]."/include/".$thereturn["maintable"].".php";

	if(file_exists($tableFile))
	    include_once($tableFile);

	//next, see if the table class exists
	if(class_exists($thereturn["maintable"])){

            $classname = $thereturn["maintable"];
            $thetable = new $classname($db,$tabledefid);

	} else
            $thetable = new phpbmsTable($db,$tabledefid);

	//finally, check to see if import class exists
	if(class_exists($thereturn["maintable"]."Import")){

            $classname = $thereturn["maintable"]."Import";
            $import = new $classname($thetable);

	} else
		$import = new phpbmsImport($thetable);

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


		//Form Elements
		//==============================================================

		// Create the form
		$theform = new importForm();
		$theform->enctype = "multipart/form-data";

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
		<fieldset >
			<legend>import</legend>

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
						Delimeters are commas (,) and enclosures are double-quotes (").  If you
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
			</div>

		</fieldset>
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
