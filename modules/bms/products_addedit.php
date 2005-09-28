<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/products_functions.php");
	include("include/products_addedit_include.php");
	
	$pageTitle="Product"?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
<script language="JavaScript" src="javascript/product.js"></script>
</head>
<body><?php include("../../menu.php")?>
<?PHP if (isset($statusmessage)) {?>
	<div class="standout" style="margin-bottom:3px;"><?PHP echo $statusmessage ?></div>
<?PHP } // end if ?>

<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<?php product_tabs("General",$therecord["id"]);?><div class="untabbedbox">

	<div style="float:right;width:200px;"><?php include("../../include/savecancel.php"); ?></div>
	<h1 style="margin-right:203px;"><?php echo $pageTitle ?></h1>
	
	<FIELDSET style="float:right;width:200px;margin-top:0px;">
		<LEGEND>attributes</LEGEND>
		<label for="id">
			id<br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:98%">
		</label>
		<label for="status">
			status<br />
			<?PHP basic_choicelist("status",$therecord["status"],Array(Array("name"=>"In Stock","value"=>"In Stock"),Array("name"=>"Out of Stock","value"=>"Out of Stock"),Array("name"=>"Backordered","value"=>"Backordered"),Array("name"=>"Discontinued","value"=>"DISCONTINUED")));?>
		</label>
		<label for="ds-categoryid">
			product category<br />
			<?PHP autofill("categoryid",$therecord["categoryid"],7,"productcategories.id","productcategories.name","\"\"","",Array("size"=>"24","maxlength"=>"64","style"=>"width:98%"),1,"Category name cannot be blank.") ?>
		</label>
		<label for="webenabled" style="text-align:center">
			<?PHP field_checkbox("webenabled",$therecord["webenabled"])?>web enabled
		</label>
	</FIELDSET>
	
	<FIELDSET>
		<LEGEND>general</LEGEND>
		<label for="partnumber" class="important">
			part number <em>(must be unique)</em><br/>
			<?PHP field_text("partnumber",$therecord["partnumber"],1,"Part number name cannot be blank.","",Array("size"=>"24","maxlength"=>"32","class"=>"important","onChange"=>"checkUnique('../../',this.value,this.name,'products','partnumber','".$therecord["id"]."')")); ?>
			<script language="javascript">var thepn=getObjectFromID("partnumber");thepn.focus();</script>
		</label>
		<label for="partname" class="important">
			part name<br>
			<input name="partname" type="text" class="important" id="partname" value="<?php echo $therecord["partname"]?>" size="34" maxlength="128" style="width:98%">			
		</label>
		<label for="description" style="margin-top:26px;">
			description<br />
			<input name="description" type="text" id="description" style="width:98%" value="<?php echo $therecord["description"]?>" size="34" maxlength="255">
		</label>
	</FIELDSET>
	
	<FIELDSET style="clear:both;">
		<LEGEND>price / cost</LEGEND>
		<label for="unitcost">
			unit cost<br />
			<?PHP field_dollar("unitcost",$therecord["unitcost"],0,"",Array("size"=>"10","maxlength"=>"32"))?>		
			<script language="javascript">var myitem=getObjectFromID("unitcost"); myitem.thechange=calculateMarkUp;</script>
		</label>		
		<label for="markup">
			mark-up percentage <br>
			<?php 
				$markup="0.0%";
				if($therecord["unitcost"]!=0){
					$markup=round($therecord["unitprice"]/$therecord["unitcost"],1)."%";
				}				
			?>
			<input type="text" name="markup" id="markup" size="10" maxlength="10" value="<?php echo $markup?>" align="right" onChange="changeMarkup();"> <input type="button" name="updateprice" value="update price" class="Buttons" onClick="calculatePrice()">
		</label>
		<label for="unitprice" class="important">
			unit price<br>
			<?PHP field_dollar("unitprice",$therecord["unitprice"],0,"",Array("size"=>"10","maxlength"=>"32","class"=>"important"))?>
			<script language="javascript">var myitem=getObjectFromID("unitprice"); myitem.thechange=calculateMarkUp;</script>
		</label>
	</FIELDSET>
	
	<FIELDSET>
	<LEGEND>shipping information</LEGEND>
	<label for="unitofmeasure">
		unit of measure<br>
		<input name="unitofmeasure" type="text" id="unitofmeasure" value="<?php echo $therecord["unitofmeasure"]?>" size="24" maxlength="32">
	</label>
	<label for="weight">
		weight <em>(lbs.)</em><br />
		<?PHP field_text("weight",$therecord["weight"],0,"Weight must be a valid number.","real",Array("size"=>"5","maxlength"=>"16")); ?>	
	</label>
	<label for="packagesperitem">
		items per package<br />
		<?php 
			if ($therecord["packagesperitem"])
				$itemsperpackage=1/$therecord["packagesperitem"];
			else
				$itemsperpackage=NULL;
		?><?PHP field_text("packagesperitem",$itemsperpackage,0,"Packages per item must be a valid number.","real",Array("size"=>"5","maxlength"=>"16")); ?>	
	</label>
	<label for="isprepackaged">
		<?PHP field_checkbox("isprepackaged",$therecord["isprepackaged"])?>pre-packaged <em>(Package is not packed with any other product.)</em>
	</label>
	<label for="isoversized">
		<?PHP field_checkbox("isoversized",$therecord["isoversized"])?>oversized <em>(Package must be delivered in a oversized box.)</em>
	</label>
	</FIELDSET>
	<?php include("../../include/createmodifiedby.php"); ?>	
</div>
</form>
</body>
</html>