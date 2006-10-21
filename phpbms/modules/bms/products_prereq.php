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

	include("include/products_functions.php");

	$refquery="select partname from products where id=".$_GET["id"];
	$refquery=mysql_query($refquery,$dblink);
	$refrecord=mysql_fetch_array($refquery);	

if(isset($_POST["command"])){
	switch($_POST["command"]){
		case"delete":
			$thequery="delete from prerequisites where id=".$_POST["deleteid"];
			$theresult=mysql_query($thequery);
		break;
		case"add":
			if($_POST["partnumber"]!=$_GET["id"] && $_POST["partnumber"]!=""){
				$thequery="insert into prerequisites (parentid,childid) VALUES(".$_GET["id"].",\"".$_POST["partnumber"]."\");";
				$theresult=mysql_query($thequery);
			}
		break;
	}
}

	$prerequstatement="SELECT DISTINCT prerequisites.id,partnumber,partname,description 
						FROM prerequisites INNER JOIN products ON prerequisites.childid=products.id 
						WHERE prerequisites.parentid=\"".$_GET["id"]."\"";
	$prereqresult=mysql_query($prerequstatement,$dblink);
	$prereqresult? $numrows=mysql_num_rows($prereqresult): $numrows=0;

$pageTitle="Product Prerequisites: ".$refrecord["partname"];?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/products.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="javascript/prereq.js"></script>
</head>
<body><?php include("../../menu.php")?>
<?php product_tabs("Prerequisites",$_GET["id"]);?><div class="bodyline">
	<h1><span><?php echo $pageTitle ?></span></h1>
	<form action="<?PHP echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record">
	<input id="deleteid" name="deleteid" type="hidden" value="0" />
	<input id="command" name="command" type="hidden" value="" />
	<div class="fauxP">
		<table border="0" cellpadding="3" cellspacing="0" class="querytable">
		<tr>
		 <th align="left" nowrap class="queryheader">Part Number</td>
		 <th align="left" nowrap class="queryheader">Name</td>
		 <th align="left" width=100% class="queryheader">Description</td>
		 <th align="center" nowrap class="queryheader">&nbsp;</td>
		</tr>
		<?PHP 	
		if($numrows){
			while ($prereq=mysql_fetch_array($prereqresult)){
	?>
		<tr>
			<td align="left" nowrap><?PHP echo $prereq["partnumber"] ?></td>
			<td align="left" nowrap><?PHP echo $prereq["partname"] ?></td>
			<td align="left" width="100%"><?PHP echo $prereq["description"]?$prereq["description"]:"&nbsp;" ?></td>
			<td align="center">
				<button type="submit" class="graphicButtons buttonMinus" onclick="return deleteLine(<?PHP echo $prereq["id"] ?>)"><span>-</span></button> 
			</td>
		</tr>
		<?PHP }//end while
		?>
		<tr class="queryfooter">
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?php
		} else {?>
		<tr><td colspan="4" align=center style="padding:0px;"><div class="norecords">No Prerequisites to Display</div></td></tr>
		<?php
		}//end if
		?>
	   </table>
	</div>

	<fieldset>
		<legend>add new prerequisite product</legend>
		<div class="preqAdd fauxP">
			<label for="ds-partnumber">part number</label><br />
			<?PHP autofill("partnumber","",4,"products.id","products.partnumber","products.partname","products.status=\"In Stock\"",Array("size"=>"15","maxlength"=>"32"),false,"") ?>
			<script language="JavaScript">document.forms["record"]["partnumber"].onchange=populateLineItem;</script>
		</div>
		<div class="preqAdd fauxP">
			<label for="ds-partname">part name</label><br />
			<?PHP autofill("partname","",4,"products.id","products.partname","products.partnumber","products.status=\"In Stock\"",Array("size"=>"32","maxlength"=>"32"),false,"") ?>
			<script language="JavaScript">document.forms["record"]["partname"].onchange=populateLineItem;</script>
		</div>
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
<?php include("../../footer.php");?>
</body>
</html>