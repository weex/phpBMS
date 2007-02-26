<?php 
/*
 +-------------------------------------------------------------------------+
 | Copyright (c) 2005, Kreotek LLC                                         |
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
	include("../../include/common_functions.php");
	include("../../include/fields.php");
		
	include("include/invoices_functions.php");
	
	$refid=(integer) $_GET["refid"];
	$refquery="select firstname,lastname,company from clients where id=".$refid;
	$refquery=mysql_query($refquery,$dblink);
	$refrecord=mysql_fetch_array($refquery);

	$refquery="SELECT
			   invoices.id, if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company) as name 
			   FROM invoices INNER JOIN clients ON invoices.clientid=clients.id 
			   WHERE invoices.id=".$refid;
	$refquery=mysql_query($refquery,$dblink);
	$refrecord=mysql_fetch_array($refquery);	

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
					$queryresult=mysql_query($querystatement,$dblink);
				}//end find if
			}//end for each
			$statusmessage="Statuses Updated";
		}//end command if

	//================================================================
	
	$querystatement="SELECT id,name FROM invoicestatuses WHERE inactive=0 ORDER BY priority,name";
	$statusresult=mysql_query($querystatement,$dblink);
	
	$querystatement="SELECT id, invoicestatusid, assignedtoid, statusdate
					FROM invoicestatushistory WHERE invoiceid=".$refid;
	$historyresult=mysql_query($querystatement,$dblink);
	if(!$historyresult) reportError(300,"Error fetching status history:<br />".mysql_error($dblink));
	while($therecord=mysql_fetch_array($historyresult))
		$history[]=$therecord;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/invoicestatushistory.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/datepicker.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>
<?php invoice_tabs("Status History",$refid);?><div class="bodyline">
	<h1><span><?php echo $pageTitle ?></span></h1>
	<form action="<?PHP echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onsubmit="return validateForm(this);">
		<p>
			<input accesskey="s" title="(alt+s)" name="command" type="submit" value="update statuses" class="Buttons"/>
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
				while($therecord=mysql_fetch_array($statusresult)){
				$row==1? $row++ : $row--;
				$historyid=false;
				$historydate=false;
				foreach($history as $historyrecord)
					if($historyrecord["invoicestatusid"]==$therecord["id"]) {
						$historyid=$historyrecord["id"];
						$historydate=$historyrecord["statusdate"];
						$assignedtoid=$historyrecord["assignedtoid"];
					}
			?>
				<tr class="row<?php echo $row ?> <?php if(!$historyid){?>disabledstatus<?php }?>">
					<td><?php if($historyid) echo "<strong>".$therecord["name"]."</strong>"; else  echo $therecord["name"]?></td>
					<td><?php if($historyid) {
							field_datepicker("sh".$historyid,$historydate,true,$therecord["name"]." status date must be a valid date and not blank.",Array("size"=>"11","maxlength"=>"11","tabindex"=>"14"));
						} else {
							echo "&nbsp;";
						}
					?>					
					</td>
					<td><?php if($historyid) {
							autofill("as".$historyid,$assignedtoid,9,"users.id","concat(users.firstname,\" \",users.lastname)","\"\"","users.revoked!=1",Array("size"=>"30","maxlength"=>"128"));
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
			<input name="command" type="submit" value="update statuses" class="Buttons"/>
		</p>
			
	</form>	
</div><?php include("../../footer.php")?>
</body>
</html>