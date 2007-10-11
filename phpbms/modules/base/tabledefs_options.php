<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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
	include("include/fields.php");
	include("modules/base/include/tabledefs_options_include.php");

	if(!isset($_GET["id"]))
		$error = new appError(100, "Passed Parameter not present.");

	$options = new tableOptions($db, $_GET["id"]);

	$pageTitle = "Table Definition Options: ".$options->getTableName();
	
	if(isset($_POST["command"]))
		$therecord = $options->processForm(addSlashesToArray($_POST));
	else
		$therecord = $options->getDefaults();

	if(isset($therecord["statusmessage"]))	
		$statusmessage = $therecord["statusmessage"];
	
	$queryresult = $options->get();
		
	$phpbms->cssIncludes[] = "pages/tableoptions.css";
	$phpbms->jsIncludes[] = "modules/base/javascript/tableoptions.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
	
		$temparray = array(
			"Integrated Feature" => 0,
			"Additional Commands" => 1,
		);
		$theinput = new inputBasicList("type", $therecord["othercommand"], $temparray);
		$theform->addField($theinput);
	
		$temparray =array(
			"new" => "new",
			"edit" => "edit",
			"select" => "select",
			"reporting" => "printex",			
		);		
		$theinput = new inputBasicList("ifName", $therecord["name"], $temparray, "name");
		$theform->addField($theinput);
		
		$theinput = new inputCheckBox("ifOption", $therecord["option"], "allowed");
		$theform->addField($theinput);
		
		$theinput = new inputField("acName", $therecord["name"],"php method", false, NULL, 64, 64);
		$theform->addField($theinput);

		$theinput = new inputField("acOption", $therecord["option"], "display name", false, NULL, 64, 64);
		$theform->addField($theinput);

		$theinput = new inputRolesList($db, "roleid", $therecord["roleid"], "access (role)");
		$theform->addField($theinput);

		$theinput = new inputField("displayorder", $therecord["displayorder"], "display order", "integer", NULL, 4, 5);
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements	
		
	include("header.php");
	
	$phpbms->showTabs("tabledefs entry",3,$_GET["id"])?>
<div class="bodyline">

	<h1 id="topTitle"><span><?php echo $pageTitle?></span></h1>

	<div class="fauxP">
	
		<?php $options->showRecords($queryresult)?>

	</div>
	
	<?php 
		if($therecord["id"]){

			$title = "Edit Option";
			$command = "update";

		} else {

			$title = "Add New Option";
			$command = "add";
			
		}
	?>
	
	<form action="<?php echo $_SERVER["PHP_SELF"]."?id=".$_GET["id"] ?>" method="post" id="record" name="record">

		<fieldset>
			<legend><?php echo $title?></legend>

			<input type="hidden" id="command" name="command" value="<?php echo $command?>"/>
			<input id="id" name="id" type="hidden" value="<?php echo $therecord["id"]?>" />
			
			<p><?php $theform->showField("type")?></p>			
			
			<div id="ifDiv">
			
				<p class="notes">
					Integrated features allow you to set and access to
					main buttons on the table definitions search screen.					
				</p>

				<p><?php $theform->showField("ifName")?></p>

				<p><?php $theform->showField("ifOption")?></p>

			</div>

			<div id="acDiv">
			
				<p class="notes">
					Additional command allows you to add items to the
					other commands drop down on the search screen.
					The PHP method name should refrence a function
					in the tables extended searchFunctions class
					in the [tablename].php located in the modules 
					include folder.
				</p>

				<p><?php $theform->showField("acOption") ?></p>

				<p><?php $theform->showField("acName") ?></p>
				
				<p><?php $theform->showField("displayorder")?></p>
				
				<p class="notes">Lower numbered display orders are displayed first, and grouped by display order.</p>
				
			</div>

			<p><?php $theform->showField("roleid")?></p>
						
		</fieldset>

		<p align="right">
			<button id="save" type="button" class="Buttons">save</button>
			<?php 
				if($therecord["id"]){
					?><button id="cancel" type="button" class="Buttons">cancel edit</button><?php
				}//end if
			?>
		</p>

	</form>
</div>
<?php include("footer.php"); ?>
