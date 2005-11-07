<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/products_functions.php");

	$refquery="select partname from products where id=".$_GET["id"];
	$refquery=mysql_query($refquery,$dblink);
	$refrecord=mysql_fetch_array($refquery);	

if(isset($_POST["command"])){
	switch($_POST["command"]){
		case"del":
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

$pageTitle="Product Prerequisites: ".$refrecord["partname"];?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="javascript/prereq.js"></script>
</head>
<body><?php include("../../menu.php")?>
<?php product_tabs("Prerequisites",$_GET["id"]);?><div class="bodyline">
	<h1><?php echo $pageTitle ?></h1>
	<form action="<?PHP echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record">
		<input name="deleteid" type="hidden" value="0" />
		<div><table border="0" cellpadding="3" cellspacing="0" class="querytable">
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
	    <td align="center"><input name="command" type="submit" class="Buttons" value="del" style="width:40px;" onClick="return deleteLine(<?PHP echo $prereq["id"] ?>,this)"></td>
	</tr>
    <?PHP }//end while
	} else {?>
	<tr><td colspan="4" align=center style="padding:0px;"><div class="norecords">No Prerequisites to Display</div></td></tr>
	<?php
	}//end if
	?>
   </table></div>
   <fieldset>
   	<legend>add prerequisite</legend>
   <table  border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left" nowrap>
			<div>
			part number<br>
			<?PHP autofill("partnumber","",4,"products.id","products.partnumber","products.partname","products.status=\"In Stock\"",Array("size"=>"15","maxlength"=>"32"),false,"") ?>
			<script language="JavaScript">document.forms["record"]["partnumber"].onchange=populateLineItem;</script>
			</div>
		</td>
		<td align="left" nowrap>
			<div>
			part name<br>
			<?PHP autofill("partname","",4,"products.id","products.partname","products.partnumber","products.status=\"In Stock\"",Array("size"=>"32","maxlength"=>"32"),false,"") ?>
			<script language="JavaScript">document.forms["record"]["partname"].onchange=populateLineItem;</script>
			</div>
		</td>
		<td align="right"><div><br>
		<input name="command" type="submit" class="Buttons" value="add" style="width:40px;" /></div></td>
	</tr>
	</table>
   </fieldset>
   <fieldset>
   	<legend>notes</legend>
	<div class="small">
		Prerequisites are products that must be purchased by the client
		on a prior order before this product can be purchased.
	</div>
	<div class="small">
		For example, if you run a membership organization, a membership (product) might be required
		before any other products (membership magazine, patches) can be bought.
	</div>
   </fieldset>
	</form>
</div>
</body>
</html>