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
	include("../../include/session.php");
	include("include/fields.php");

	include("./include/clients_email_include.php");
	
	
	$thecommand="showoptions";
	if(isset($_POST["command"])) $thecommand=$_POST["command"];
	
	switch($thecommand){
		case "send email":
			 // first build the where clause
			switch($_POST["therecords"]){
				case "selected":
					$whereclause="WHERE ";
					foreach($_SESSION["emailids"] as $id)
						$whereclause.="clients.id=".$id." or ";
					$whereclause=substr($whereclause,0,strlen($whereclause)-3);					
				break;
				case "savedsearch":
					$querystatement="SELECT sqlclause FROM usersearches WHERE id=".$_POST["savedsearches"];
					$queryresult=$db->query($querystatement);
					$therecord=$db->fetchArray($queryresult);
					$whereclause="WHERE ".$therecord["sqlclause"];
				break;
				case "all":
					$whereclause="";
				break;						
			}//end switch
			//next the from:
			$_SESSION["massemail"]["from"]=str_replace("]",">",str_replace("[","<",$_POST["ds-email"]));			
			$_SESSION["massemail"]["whereclause"]=$whereclause;
			$_SESSION["massemail"]["subject"]=$_POST["subject"];
			$_SESSION["massemail"]["body"]=$_POST["body"];
			$_SESSION["massemail"]["savedproject"]=$_POST["pid"];
			
			$querystatement="SELECT id,email, if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) AS name FROM clients ".$whereclause;
			$sendqueryresult=$db->query($querystatement);
			if(!$sendqueryresult) $error = new appError(300,"Error with: ".$querystatement);
			
		break;
		case "delete project":
			deleteProject($db, $_POST["projectid"]);
			$statusmessage="Project Deleted";
			$thecommand="showoptions";
		case "showoptions":
			$therecord["emailto"]="selected";
			$therecord["emailfrom"]=$_SESSION["userinfo"]["id"];
			$therecord["subject"]="";
			$therecord["body"]="";
			$therecord["id"]="";
		break;
		case "load project":
			$therecord=loadProject($db,$_POST["projectid"]);
			$statusmessage="Project Loaded";
			$thecommand="showoptions";
		break;
		case "save project":
			$id=saveProject($db,addSlashesToArray($_POST));
			$statusmessage="Project Saved";
			$therecord=loadProject($id);
			$thecommand="showoptions";
		break;
		
		case "done":
		case "cancel":
			goURL(APP_PATH."search.php?id=2");
			
		break;
	}
	
	
	$pageTitle="Client/Prospect E-Mail";
 
 	$phpbms->cssIncludes[] = "pages/clientemail.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/clientemail.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		
		if(is_numeric($therecord["emailfrom"]))
			$theid=$therecord["emailfrom"];
		else
			$theid=0;
		
		$theinput = new inputSmartSearch($db, "email", "Pick Active User Email", $theid, "from");
		$theform->addField($theinput);
		
		$theform->jsMerge();
		//==============================================================
		//End Form Elements
		
		if($therecord["emailto"]!="selected" AND $therecord["emailto"]!="all")
			$phpbms->bottomJS[] ='thediv=getObjectFromID("showsavedsearches");thediv.style.display="block"';

		if(!is_numeric($therecord["emailfrom"]))
			$phpbms->bottomJS[] ='thefield=getObjectFromID("ds-email");thefield.value="'.$therecord["emailfrom"].'"';
		
		if($thecommand=="send email"){
		
			$phpbms->topJS[]='		
			ids=new Array();			
			emails=new Array();
			names= new Array();';
			
			while($therecord = $db->fetchArray($sendqueryresult)){
				$phpbms->topJS[]="ids[ids.length]=".$therecord["id"].";";
				$phpbms->topJS[]="names[names.length]=\"".$therecord["name"]."\";";
				$phpbms->topJS[]="emails[emails.length]=\"".$therecord["email"]."\";";
			}			
		}//end if
 
 	include("header.php")

?>

	<div class="bodyline" id="mainBG">
		<h1 id="topTitle"><span><?php echo $pageTitle?></span></h1>
		
		<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="theform" id="theform">
	<?php if($thecommand=="showoptions") { ?>
	
		<input type="hidden" name="pid" id="pid" value="<?php echo $therecord["id"]?>" />
		<div class="box">
			
			<p id="toP">
				<label for="therecords">to</label><br />			
				<select id="therecords" name="therecords" onchange="showSavedSearches(this);">
					<option value="selected" <?php if ($therecord["emailto"]=="selected") echo "selected=\"selected\""?>>e-mail addresses from selected records (<?php echo count($_SESSION["emailids"]) ?> record<?php if(count($_SESSION["emailids"])>1) echo "s"?>)</option>
					<option value="savedsearch" <?php if ($therecord["emailto"]!="selected" AND $therecord["emailto"]!="all") echo "selected=\"selected\""?>>e-mail addresses from saved search...</option>
				</select>				
			</p>
			<p id="showsavedsearches" >
				<label for="savedsearches">load e-mail addresses from saved search...</label><br />
				<?php showSavedSearches($db,$therecord["emailto"]); ?>			
			</p>
			
			<div class="fauxP" id="fromDiv"><?php  $theform->showField("email")?></div>
			
			<p>
				<label for="subject">subject</label><br />
				<input type="text" name="subject" id="subject" maxlength="128" value="<?php echo htmlQuotes($therecord["subject"])?>"/>			
			</p>
		</div>
		
		<div class="box">
			<p>
				<label for="addfield">add data field</label><br />
				<?php showClientFields($db); ?>
				<input type="button" name="addfield" id="addfield" value="add field" class="smallButtons" onclick="addField()" />
				<input type="button" name="adddate" id="adddate" value="add today's date" class="smallButtons" onclick="insertAtCursor('body','[[todays_date]]')" />
			</p>
			<p>
				<textarea class="mono" rows="15" name="body" id="body" cols="40" ><?php echo $therecord["body"]?></textarea>
			</p>
		</div>
		
		<div class="box">
			<div id="projectButtons">
				<input type="button" name="loademail"	id="loademil" value="load project..." class="Buttons" onclick="showSavedProjects();" />
				<input type="button" name="saveproject"	id="saveproject" value="save project..." class="Buttons" onclick="return saveProject();" />
				<input type="hidden" name="savename"	id="savename" value="" />
				<input type="hidden" name="projectid"	id="projectid" value="" />
			</div>
			<div align="right">
				<input type="submit" name="command" 	id="sendemail" value="send email" class="Buttons" />
				<input type="submit" name="command" 	id="cancel" value="cancel" class="Buttons" />
				<input type="submit" name="command" 	id="othercommand" value="" class="Buttons" />				
			</div>
		</div>
		
		<div id="loadedprojects">
			<p><?php showSavedProjects($db)?></p>
			<p align="right">
				<input type="button" name="deleteproject" id="deleteproject" value="delete project" class="Buttons" disabled="disabled" onclick="deleteProject()" />
				<input type="button" name="loadproject" id="loadproject" value="load project" class="Buttons" disabled="disabled" onclick="loadProject()" />
				<input type="button" name="closeproject" id="closeproject" value="cancel" onclick="hideSavedProjects()" class="Buttons" />
			</p>
		</div>
<?php } elseif($thecommand=="send email"){?>
		
		<div id="processingWrap">
			<div class="box">
				<div class="fauxP">
					<table id="results" cellpadding="0" cellspacing="0" border="0" class="querytable" width="100%">
						<tr>
							<th nowrap="nowrap" id="tablereference">#</th>
							<th nowrap="nowrap">ID</th>
							<th nowrap="nowrap" align="left">Name</th>
							<th nowrap="nowrap" align="left">E-Mail Address</th>
							<th nowrap="nowrap" width=50% align="left">Status</th>
						</tr>
						<tr class="queryfooter" id="lastrow">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="box">
				<div id="processP">
					<span id="amountprocessed">0</span> / <?php echo $db->numRows($sendqueryresult)?> records processed
				</div>
				<div align="right">
					<input type="button" id="beginprocessing" name="beginprocessing" value=" begin processing " class="Buttons" onclick="sendMailButton();"/> <input type="submit" name="command" id="done" value="done" class="Buttons"  />
				</div>
			</div>
		</div>

<?php } ?>
	</form>
	</div>
<?php include("../../footer.php");?>
