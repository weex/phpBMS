<?php 
/*
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
	include("../../include/fields.php");
		
	if(isset($_GET["refid"])) $_GET["id"]=$_GET["refid"];
	$refid=(integer) $_GET["id"];

	$refquery="SELECT
			   invoices.id, if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as name,
			   invoices.type
			   FROM invoices INNER JOIN clients ON invoices.clientid=clients.id 
			   WHERE invoices.id=".$refid;
	$refquery=$db->query($refquery);
	$refrecord=$db->fetchArray($refquery);	

	$pageTitle="Status History: ".$refrecord["id"].": ".$refrecord["name"];	
	//================================================================

		if(isset($_POST["command"])){
			foreach($_POST as $key=>$value){
				if(strpos($key,"sh")===0){
					$historyid=substr($key,2);
					$assignedtoid=((int) $_POST["as".$historyid]);
					$querystatement="UPDATE invoicestatushistory SET statusdate=";
					if($value=="" || $value=="0/0/0000") $tempdate="NULL";
					else
						$tempdate="\"".sqlDateFromString($value)."\"";
					$querystatement.=$tempdate;
					$querystatement.=",assignedtoid=".$assignedtoid;
					$querystatement.=" WHERE id=".((int) $historyid);
					$queryresult=$db->query($querystatement);
				}//end find if
			}//end for each
			$statusmessage="Statuses Updated";
		}//end command if

	//================================================================
	
	$querystatement="SELECT id,name FROM invoicestatuses WHERE inactive=0 ORDER BY priority,name";
	$statusresult=$db->query($querystatement);
	
	$querystatement="SELECT invoicestatushistory.id, invoicestatuses.name, invoicestatusid, assignedtoid, statusdate
					FROM invoicestatushistory INNER JOIN invoicestatuses 
					ON invoicestatushistory.invoicestatusid = invoicestatuses.id
					WHERE invoiceid=".$refid;
	$historyresult=$db->query($querystatement);

	while($therecord=$db->fetchArray($historyresult))
		$history[]=$therecord;

	$phpbms->cssIncludes[] = "pages/invoicestatushistory.css";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		foreach($history as $historyrecord){
			
			$theinput = new inputSmartSearch($db, "as".$historyrecord["id"], "Pick Active User", $historyrecord["assignedtoid"], $historyrecord["name"]." assigned to", false, 36,255,false);
			$theform->addField($theinput);

			$theinput = new inputDatePicker("sh".$historyrecord["id"],$historyrecord["statusdate"], $historyrecord["name"]." status date" ,true, 11, 15, false);
			$theform->addField($theinput);										
		}

		$theform->jsMerge();
		//==============================================================
		//End Form Elements	
		
	include("header.php");

	$phpbms->showTabs("invoices entry",16,$_GET["id"]);?><div class="bodyline">
	<h1><span><?php echo $pageTitle ?></span></h1>
	<form action="<?PHP echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onsubmit="return validateForm(this);">
		<p>
			<input accesskey="s" title="(access key+s)" name="command" type="submit" value="update statuses" class="Buttons" <?php if($refrecord["type"]=="Invoice") echo "disabled=\"disabled\""?>/>
		</p>
		<div class="fauxP">
			<table class="querytable">
				<tr>
					<th align="left">status</th>
					<th align="left">date</th>
					<th align="left">assigned to</th>
				</tr>
			<?php 
				$row=1;
				while($therecord=$db->fetchArray($statusresult)){
				$row==1? $row++ : $row--;
				$historyid=false;
				$historydate=false;
				
				foreach($history as $historyrecord)
					if($historyrecord["invoicestatusid"] == $therecord["id"]) {
						$historyid=$historyrecord["id"];
						$historydate=$historyrecord["statusdate"];
						$assignedtoid=$historyrecord["assignedtoid"];
					}
			?>
				<tr class="row<?php echo $row ?> <?php if(!$historyid){?>disabledstatus<?php }?>">
					<td><?php if($historyid) echo "<strong>".$therecord["name"]."</strong>"; else  echo $therecord["name"]?></td>
					<td><?php if($historyid) {
							$theform->showField("sh".$historyid);
						} else {
							echo "&nbsp;";
						}
					?>					
					</td>
					<td><?php if($historyid) {
							$theform->showField("as".$historyid);
						} else {
							echo "&nbsp;";
						}
					?>					
					</td>
				</tr>
			<?php }//end while ?>
				<tr><td colspan="3" class="queryfooter">&nbsp;</td></tr>
			</table>
		</div>
		<p>
			<input name="command" type="submit" value="update statuses" class="Buttons" <?php if($refrecord["type"]=="Invoice") echo "disabled=\"disabled\""?>/>
		</p>
			
	</form>	
</div><?php include("footer.php")?>
