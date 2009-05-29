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

	include("../../include/fields.php");

	$refquery="select partname from products where id=".((int) $_GET["id"]);
	$refquery=$db->query($refquery);
	$refrecord=$db->fetchArray($refquery);

if(isset($_POST["command"])){

	switch($_POST["command"]){

		case"delete":
			$deletestatement = "
				DELETE FROM
					prerequisites
				WHERE
					id=".((int) $_POST["deleteid"]);

			$db->query($deletestatement);

			$statusmessage = "Prerequisite removed.";
			break;

		case"add":
			if($_POST["productid"]!=$_GET["id"] && $_POST["productid"]!=""){

				$insertstatement = "
					INSERT INTO
						prerequisites
						(parentid,
						childid)
					VALUES
						(".((int) $_GET["id"]).",
						".((int)$_POST["productid"]).")";

				$db->query($insertstatement);

				$statusmessage = "Prerequisite added.";

			} else {

				$statusmessage = "Prerequisite not added.";

			}//endif
			break;
	}//endswitch - comand

}//endif - command

	$prerequstatement="SELECT DISTINCT prerequisites.id,partnumber,partname,description
						FROM prerequisites INNER JOIN products ON prerequisites.childid=products.id
						WHERE prerequisites.parentid=\"".$_GET["id"]."\"";
	$prereqresult=$db->query($prerequstatement);
	$prereqresult? $numrows=$db->numRows($prereqresult): $numrows=0;

	$pageTitle="Product Prerequisites: ".$refrecord["partname"];

	$phpbms->cssIncludes[] = "pages/products.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/prereq.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();

		$theinput = new inputSmartSearch($db, "productid", "Pick Product", "", "product", false, 51, 255);
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");

?>
<?php $phpbms->showTabs("products entry","tab:9bfc7eea-5abb-f5d8-763f-f78fe499464d",$_GET["id"]);?><div class="bodyline">
	<h1><span><?php echo $pageTitle ?></span></h1>
	<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record">
	<input id="deleteid" name="deleteid" type="hidden" value="0" />
	<input id="command" name="command" type="hidden" value="" />
	<div class="fauxP">
		<table border="0" cellpadding="3" cellspacing="0" class="querytable">
		<tr>
		 <th align="left" nowrap="nowrap" class="queryheader">Part Number</th>
		 <th align="left" nowrap="nowrap" class="queryheader">Name</th>
		 <th align="left" width="100%" class="queryheader">Description</th>
		 <th align="center" nowrap="nowrap" class="queryheader">&nbsp;</th>
		</tr>
		<?php
		$row = 1;
		if($numrows){
			while ($prereq=$db->fetchArray($prereqresult)){
			$row = ($row == 1)? 2: 1;
	?>
		<tr class="qr<?php echo $row?>">
			<td align="left" nowrap="nowrap"><?php echo $prereq["partnumber"] ?></td>
			<td align="left" nowrap="nowrap"><?php echo $prereq["partname"] ?></td>
			<td align="left" width="100%"><?php echo $prereq["description"]?$prereq["description"]:"&nbsp;" ?></td>
			<td align="center">
				<button type="submit" class="graphicButtons buttonMinus" onclick="return deleteLine(<?php echo $prereq["id"] ?>)"><span>-</span></button>
			</td>
		</tr>
		<?php }//end while
		?>
		<tr class="queryfooter">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?php
		} else {?>
		<tr><td colspan="4" align="center" style="padding:0px;"><div class="norecords">No Prerequisites to Display</div></td></tr>
		<?php
		}//end if
		?>
	   </table>
	</div>

	<fieldset>
		<legend>add new prerequisite</legend>

		<div class="preqAdd fauxP"><?php $theform->showField("productid")?></div>

		<p id="addButtonP"><br />
			<button type="submit" class="graphicButtons buttonPlus" onclick="return addLine()"><span>+</span></button>
		</p>
   </fieldset>

   <fieldset>
		<legend>notes</legend>
		<p class="notes">
			Prerequisites are products that must be purchased by the client
			on a prior order before this product can be purchased.
		</p>
		<p class="notes">
			For example, if you run a membership organization, a membership (product) might be required
			before any other products (membership magazine, patches) can be bought.
		</p>
   </fieldset>
	</form>
</div>
<?php include("footer.php");?>