<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/products_functions.php");
	include("include/products_addedit_include.php");
	
	$pageTitle="Product"?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" >
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
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

	<div style="float:right;width:200px;">
			<?php include("../../include/savecancel.php"); ?>
			<div class="box">
				<div>
					id<br>
					<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:100%">
				</div>
				<div>
					status<br>
					<?PHP basic_choicelist("status",$therecord["status"],Array(Array("name"=>"In Stock","value"=>"In Stock"),Array("name"=>"Out of Stock","value"=>"Out of Stock"),Array("name"=>"Backordered","value"=>"Backordered"),Array("name"=>"Discontinued","value"=>"DISCONTINUED")));?>
				</div>
				<div>
					product category<br>
					<?PHP autofill("categoryid",$therecord["categoryid"],7,"productcategories.id","productcategories.name","\"\"","",Array("size"=>"24","maxlength"=>"64","style"=>"width:100%"),1,"Category name cannot be blank.") ?>
				</div>
				<div>
					<?PHP field_checkbox("webenabled",$therecord["webenabled"])?>web enabled
				</div>
			</div>
	</div>
	
	<div style="margin-right:203px;">
		<h1><?php echo $pageTitle ?></h1>
		<div>
			part number<br>
			<?PHP field_text("partnumber",$therecord["partnumber"],1,"Part number name cannot be blank.","",Array("size"=>"24","maxlength"=>"32","class"=>"important","onChange"=>"checkUnique('../../',this.value,this.name,'products','partnumber','".$therecord["id"]."')")); ?>
			<script language="javascript">var thepn=getObjectFromID("partnumber");thepn.focus();</script>
		</div>
		<div>
			name<br>
			<input name="partname" type="text" id="partname" value="<?php echo $therecord["partname"]?>" size="34" maxlength="128" style="width:100%">			
		</div>
		<div>
			description
			<input name="description" type="text" id="description" style="width:100%" value="<?php echo $therecord["description"]?>" size="34">
		</div>
		<div>
			unit of measure<br>
			<input name="unitofmeasure" type="text" id="unitofmeasure" value="<?php echo $therecord["unitofmeasure"]?>" size="24" maxlength="32">
		</div>
		
		
	</div>
	<div>
		<h2>Cost/Price</h2>
		<div>
			unit cost<br>
			<?PHP field_dollar("unitcost",$therecord["unitcost"],0,"",Array("size"=>"10","maxlength"=>"32"))?>		
			<script language="javascript">var myitem=getObjectFromID("unitcost"); myitem.thechange=calculateMarkUp;</script>
		</div>		
		<div>
			mark up percentage <br>
			<?php 
				$markup="0.0%";
				if($therecord["unitcost"]!=0){
					$markup=round($therecord["unitprice"]/$therecord["unitcost"],1)."%";
				}				
			?>
			<input type="text" name="markup" id="markup" size="10" maxlength="10" value="<?php echo $markup?>" align="right" onChange="changeMarkup();"> <input type="button" name="updateprice" value="update price" class="buttons" onClick="calculatePrice()">
		</div>
		<div class="important">
			unit price<br>
			<?PHP field_dollar("unitprice",$therecord["unitprice"],0,"",Array("size"=>"10","maxlength"=>"32","class"=>"important"))?>
			<script language="javascript">var myitem=getObjectFromID("unitprice"); myitem.thechange=calculateMarkUp;</script>
		</div>
	</div>
	
	<div>
	<h2>shipping information</h2>
	<div>
		weight (lbs.)<br>
		<?PHP field_text("weight",$therecord["weight"],0,"Weight must be a valid number.","real",Array("size"=>"16","maxlength"=>"16")); ?>	
	</div>
	<div>
		items per package<br>
		<?php 
			if ($therecord["packagesperitem"])
				$itemsperpackage=1/$therecord["packagesperitem"];
			else
				$itemsperpackage=NULL;
		?>
		<?PHP field_text("packagesperitem",$itemsperpackage,0,"Packages per item must be a valid number.","real",Array("size"=>"16","maxlength"=>"16")); ?>	
	</div>
	<div>
		<?PHP field_checkbox("isprepackaged",$therecord["isprepackaged"])?>pre-packaged (Package is not packed with any other product.)
	</div>
	<div></div>
		<?PHP field_checkbox("isoversized",$therecord["isoversized"])?>oversized (Package must be delivered in a oversized box.)
	</div>
	<?php include("../../include/createmodifiedby.php"); ?>	
</div>
</form>
</body>
</html>