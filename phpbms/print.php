<?php
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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
require_once("include/session.php");
require_once("include/print_class.php");

if(!isset($_GET["backurl"])) $_GET["backurl"]="";
if(isset($_POST["backurl"])) $_GET["backurl"]=$_POST["backurl"];

$tablePrinter= new printer($db,$_SESSION["printing"]["tableid"],$_SESSION["printing"]["theids"]);

$tablePrinter->saveVariables();

if (isset($_POST["command"])){
	switch($_POST["command"]){
		case "done":
			$tablePrinter->donePrinting($_GET["backurl"]);
		break;
		case "print":
			//let's build the whereclause
			$whereclause="";
			$dataprint="";
			switch($_POST["therecords"]){
				case "all":
					$dataprint="All Records";
				break;
				case "savedsearch":
					if($_POST["savedsearches"]!="" and $_POST["savedsearches"]!="NA")	{
						$querystatement="SELECT name,sqlclause FROM usersearches WHERE id=".$_POST["savedsearches"];
						$queryresult=$db->query($querystatement);
						If(!$queryresult) $error = new appError(500,"Could not retrieve saved search. ".$querystatement);
						$therecord=$db->fetchArray($queryresult);
						$whereclause="WHERE ".$therecord["sqlclause"];
						$dataprint=$therecord["name"];
					}
				break;
				case "selected":
					foreach($tablePrinter->theids as $theid){
						$whereclause.=" or ".$tablePrinter->maintable.".id=".$theid;
					}
					$whereclause="where ".substr($whereclause,3);
					$dataprint="Selected Records";
				break;
			}
			$_SESSION["printing"]["whereclause"]=$whereclause;
			$_SESSION["printing"]["dataprint"]=$dataprint;

			//next let's do the sort
			$sortorder="";
			switch($_POST["thesort"]){
				case "single":
					$sortorder=" ORDER BY ".$tablePrinter->maintable.".".$_POST["singlefield"]." ".$_POST["order"];
				break;
				case "savedsort":
					if($_POST["savedsorts"]!="" and $_POST["savedsorts"]!="NA")	{
						$querystatement="SELECT sqlclause FROM usersearches WHERE id=".$_POST["savedsorts"];
						$queryresult=$db->query($querystatement);
						If(!$queryresult) $error = new appError(500,"Could not retrieve saved search. ".$querystatement);
						$therecord=$db->fetchArray($queryresult);
						$sortorder=" ORDER BY ".$therecord["sqlclause"];
					}
				break;
			}
			$_SESSION["printing"]["sortorder"]=$sortorder;

			if(isset($_POST["choosereport"])){

				$tablePrinter->openwindows="";

				for($i=0;$i<count($_POST["choosereport"]);$i++){

					if($_POST["choosereport"][$i]){

						$querystatement = "
                                                    SELECT
                                                        `uuid`,
                                                        `reportfile`,
                                                        `type`
                                                    FROM
                                                        `reports`
                                                    WHERE
                                                        id = ".$_POST["choosereport"][$i];

						$queryresult = $db->query($querystatement);

						if(!$queryresult)
                                                    $error = new appError(100,"Could not Retrieve Report Information");

                                                $reportrecord = $db->fetchArray($queryresult);

						$fakeExtForIE="";

                                                if($reportrecord["type"] == "PDF Report")
							$fakeExtForIE = "' + String.fromCharCode(38) + ' &amp;ext=.pdf";

                                                // make the url unique to avoid using browser cache
						$dateTimeStamp = "' + String.fromCharCode(38) + 'ts=".time();

						//javascript open each report in new window
						$tablePrinter->openwindows .= "window.open('".APP_PATH.$reportrecord["reportfile"]."?rid=".urlencode($reportrecord["uuid"])."' + String.fromCharCode(38) + 'tid=".urlencode($tablePrinter->tableid).$dateTimeStamp.$fakeExtForIE."','print".$i."');\n";

					}//endif

				}//endfor

                        }//endif

		break;
	}
}

$pageTitle="Print/Export";

$phpbms->showMenu = false;

$phpbms->cssIncludes[] = "pages/print.css";

$phpbms->jsIncludes[] = "common/javascript/print.js";

$phpbms->topJS[] = $tablePrinter->showJavaScriptArray();

if($tablePrinter->openwindows) $phpbms->bottomJS[] = $tablePrinter->openwindows;

include("header.php");
?>
<div >
<div class="bodyline" id="mainbody">
	<h1><?php echo $pageTitle ?><a name="top"></a></h1>

<form action="print.php" method="post" name="print">
	<input type="hidden" name="backurl" value="<?php echo $_GET["backurl"]?>" />

	<fieldset id="fsReportInformation" >
		<legend>report information</legend>
		<?php
			if ($db->numRows($tablePrinter->reports)){
				$db->seek($tablePrinter->reports,0);
				$therecord=$db->fetchArray($tablePrinter->reports);
			} else {
				$therecord["id"]=0;
				$therecord["reportfile"]="";
				$therecord["name"]="";
				$therecord["type"]="";
				$therecord["description"]="";
			}
		?>
		<p>
			name<br />
			<input name="reportid" type="hidden" value="<?php echo $therecord["id"] ?>" />
			<input name="reportfile" type="hidden" value="<?php echo htmlQuotes($therecord["reportfile"]) ?>" />
			<input name="name" type="text" class="uneditable important" id="name" value="<?php echo htmlQuotes($therecord["name"]) ?>" size="32" maxlength="64" readonly="readonly" />
		</p>

		<p>
			type<br />
			<input name="type" type="text" class="uneditable" id="type" value="<?php echo $therecord["type"] ?>" size="20" maxlength="64" readonly="readonly" />
		</p>
		<p>
			description<br />
			<textarea name="description" cols="45" rows="3" readonly="readonly" id="description" class="uneditable"><?php echo stripcslashes($therecord["description"]) ?></textarea>
		</p>
	</fieldset>

	<div id="selectReportsDiv">
	<fieldset>
		<legend>select report(s)</legend>
		<p>available reports<br />
			<?php $tablePrinter->displayReportList()?>
		</p>
	</fieldset>
	</div>

	<p><button id="showoptions" class="graphicButtons buttonDown" type="button"><span>more options</span></button></p>

	<div id="moreoptions">
		<fieldset>
			<legend>data</legend>
			<p id="showsavedsearches">
				<label for="savedsearches">load saved search...</label><br />
				<?php $tablePrinter->showSaved($tablePrinter->savedSearches,"savedsearches");?>
			</p>

			<p>
				<label class="important" for="therecords">from</label><br />
				<select id="therecords" name="therecords" onchange="showSavedSearches(this);">
					<option value="selected">selected records (<?php echo count($tablePrinter->theids) ?> record<?php if(count($tablePrinter->theids)>1) echo "s"?>)</option>
					<option value="savedsearch">saved search...</option>
					<?php if($_SESSION["userinfo"]["admin"]==1){?><option value="all">all records in table</option><?php }?>
				</select>
			</p>
		</fieldset>

		<fieldset>
			<legend>sort</legend>

			<p id="savedsortdiv">
				<label for="savedsorts">saved sort...</label><br />
				<?php $tablePrinter->showSaved($tablePrinter->savedSorts,"savedsorts");?>
			</p>

			<p id="singlesortdiv">
				<label for="singlefield">field</label><br />
				<?php $tablePrinter->showFieldSort()?>
				<select name="order">
					<option value="ASC" selected="selected">Ascending</option>
					<option value="DESC">Descending</option>
				</select>
			</p>
			<p class="important">
				<label for="thesort">by</label><br />
				<select id="thesort" name="thesort" onchange="showSortOptions(this)">
					<option value="default" selected="selected">report default</option>
					<option value="single">single field</option>
					<option value="savedsort">saved sort...</option>
				</select>
			</p>
		</fieldset>
		<fieldset>
			<legend>customizing reports</legend>
			<p class="notes">
			Many reports feature a logo and your company information.  This information can be set administratively in the configuration area
			</p>
			<p class="notes">
			Need more reports, or want to customize an existing report to meet your specific needs? <br />
			if you are unfamiliar with PHP, or programming phpBMS, you can try visiting the
			<a href="http://www.phpbms.org">phpBMS project site</a>, or visit <a href="http://kreotek.com">Kreotek's website</a> for more information.
			</p>
		</fieldset>
	</div>
	<fieldset class="small">
		<legend>Pop-Up Windows</legend>
		<p class="notes">
			Each report will display in its own window. If you have disabled
			pop-ups within your browser's options or are running a third-party pop-up blocker, the report may not show.
		</p>
	</fieldset>

	<p id="printFooter">
		<input name="command" type="submit" class="Buttons" id="printButton" value="print" accesskey="p" title="print (alt+p)" />
		<input name="command" type="submit" class="Buttons" id="cancel" value="done" accesskey="d" title="done (alt+d)" />
	</p>
   </form>
</div>
</div>
<?php include("footer.php")?>
