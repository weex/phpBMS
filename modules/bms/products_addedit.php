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

<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<?php product_tabs("General",$therecord["id"]);?><div class="bodyline">

	<div style="float:right;width:160px;"><?php showSaveCancel(1); ?></div>
	<h1 style="margin-right:162px;"><?php echo $pageTitle ?></h1>
	
	<fieldset style="clear:both;float:right;width:200px;margin-top:0px;">
		<legend>attributes</legend>
		<label for="id">
			id<br />
			<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:98%">
		</label>
		<label for="ds-categoryid">
			product category<br />
			<?PHP autofill("categoryid",$therecord["categoryid"],7,"productcategories.id","productcategories.name","\"\"","",Array("size"=>"24","maxlength"=>"64","style"=>"width:98%"),1,"Category name cannot be blank.") ?>
		</label>
		<label for="status">
			type<br />
			<?PHP basic_choicelist("type",$therecord["type"],Array(Array("name"=>"Inventoried","value"=>"Inventoried"),Array("name"=>"Non-Inventoried","value"=>"Non-Inventoried"),Array("name"=>"Kit","value"=>"Kit"),Array("name"=>"Assembly","value"=>"Assembly"),Array("name"=>"Raw Material","value"=>"Raw Material"),Array("name"=>"Service","value"=>"Service")));?>
		</label>
		<label for="status">
			status<br />
			<?PHP basic_choicelist("status",$therecord["status"],Array(Array("name"=>"In Stock","value"=>"In Stock"),Array("name"=>"Out of Stock","value"=>"Out of Stock"),Array("name"=>"Backordered","value"=>"Backordered"),Array("name"=>"Available","value"=>"Available"),Array("name"=>"Discontinued","value"=>"DISCONTINUED")));?>
		</label>
	</fieldset>
	
	<fieldset>
		<legend>general</legend>
		<label for="partnumber" class="important">
			part number <em>(must be unique)</em><br/>
			<?PHP field_text("partnumber",$therecord["partnumber"],1,"Part number name cannot be blank.","",Array("size"=>"24","maxlength"=>"32","class"=>"important","onChange"=>"checkUnique('../../',this.value,this.name,'products','partnumber','".$therecord["id"]."')")); ?>
			<script language="javascript">var thepn=getObjectFromID("partnumber");thepn.focus();</script>
		</label>
		<label for="partname" class="important">
			part name<br>
			<input name="partname" type="text" class="important" id="partname" value="<?php echo htmlQuotes($therecord["partname"])?>" size="34" maxlength="128" style="width:98%">			
		</label>
		<label for="description" style="margin-top:5px;">
			description<br />
			<input name="description" type="text" id="description" style="width:98%" value="<?php echo htmlQuotes($therecord["description"])?>" size="34" maxlength="255">
		</label>
		<div class="small"><em>The description will show by default in the memo field of invoice<br />
								line items, but can be modified on the invoice.</em></div>
	</fieldset>
	
	<fieldset style="clear:both;">
		<legend>price / cost</legend>
		<label for="unitcost">
			unit cost<br />
			<?PHP field_dollar("unitcost",$therecord["unitcost"],0,"",Array("size"=>"10","maxlength"=>"32"))?>		
			<script language="javascript">var myitem=getObjectFromID("unitcost"); myitem.thechange=calculateMarkUp;</script>
		</label>		
		<label for="markup">
			mark-up percentage <br>
			<?php 
				$markup=0;
				if($therecord["unitcost"]!=0){
					$markup=round(($therecord["unitprice"]/$therecord["unitcost"])-1,4)*100;
				}				
				field_percentage("markup",$markup,2,0,"",Array("size"=>"10","maxlength"=>"10"));
			?>
			<input type="button" name="updateprice" value="update price" class="Buttons" onClick="calculatePrice()">
		</label>
		<label for="unitprice" class="important">
			unit price<br>
			<?PHP field_dollar("unitprice",$therecord["unitprice"],0,"",Array("size"=>"10","maxlength"=>"32","class"=>"important"))?>
			<script language="javascript">var myitem=getObjectFromID("unitprice"); myitem.thechange=calculateMarkUp;</script>
		</label>
	</fieldset>
	
	<fieldset>
		<legend>shipping information</legend>
		<label for="unitofmeasure">
			unit of measure<br>
			<input name="unitofmeasure" type="text" id="unitofmeasure" value="<?php echo htmlQuotes($therecord["unitofmeasure"])?>" size="24" maxlength="32">
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
		</fieldset>
	<fieldset>
		<legend>web</legend>
		<label for="webenabled">
			<?PHP field_checkbox("webenabled",$therecord["webenabled"])?>web enabled
		</label>
		<label for="keywoards">
			keywords <em>(comma separated key word list)</em><br />
			<input type="text" id="keywords" name="keywords" value="<?php echo htmlQuotes($therecord["keywords"])?>" style="width:98%">
		</label>
		<label for="webdescription">
			<div style="margin:0px;padding:0px;">web description <em>(HTML acceptable)</em></div>
			<div style="clear:both;margin:0px;padding:0px;<?php if($therecord["webdescription"]) echo "display:none;"?>" id="webDescEdit">
				<textarea id="webdescription" name="webdescription" cols="60" rows="6" style="width:98%"><?php echo $therecord["webdescription"] ?></textarea>
			</div>
			<div style="clear:both;margin:0px;border:1px solid black; background-color:white;<?php if(!$therecord["webdescription"]) echo "display:none;"?>" id="webDescPreview">
			<?php echo $therecord["webdescription"] ?>
			</div>			
			<div style="width:98%" align="right"><input type="button" class="Buttons" onClick="editPreviewWebDesc(this)" value="<?php if(!$therecord["webdescription"]) echo "preview"; else echo "edit"?>" style="width:75px;" /></div>
		</label>
		<label for="thumb" style="">
			<?php if($therecord["thumbnailmime"]) {?>
				<img id="thumbpic" src="<?php echo $_SESSION["app_path"] ?>dbgraphic.php?t=products&f=thumbnail&mf=thumbnailmime&r=<?php echo $therecord["id"]?>" style="border: 1px solid black; display: block; margin: 3px;;" />
			<?php } else {?>
				<div style="margin:3px;border:1px solid black;background-color:white;color:#999999;width:75px;height:75px" class="tiny" align="center"><br/><br/>no thumbnail</div>
			<?php } ?>
			thumbnail <input type="hidden" id="thumbchange" name="thumbchange" value=""><br />
			<div id="thumbdelete" style="padding:0px;display:<?php if($therecord["thumbnailmime"]) echo "block"; else echo "none";?>"><input type="button" class="Buttons" value="delete thumbnail" onClick="deletePicture('thumb')"/></div>
			<div id="thumbadd" style="padding:0px;display:<?php if($therecord["thumbnailmime"]) echo "none"; else echo "block";?>"><input id="thumbnailupload" name="thumbnailupload" type="file" size="40" onChange="updatePictureStatus('thumb','upload')" /></div>
		</label>
		<label for="picture" style="">
			<?php if($therecord["picturemime"]) {?>
				<img id="picturepic" src="<?php echo $_SESSION["app_path"] ?>dbgraphic.php?t=products&f=picture&mf=picturemime&r=<?php echo $therecord["id"]?>" style="border: 1px solid black; display: block; margin: 3px;;" />
			<?php } else {?>
				<div style="margin:3px;border:1px solid black;background-color:white;color:#999999;width:150px;height:150px" class="tiny" align="center"><br/><br/><br/>no picture</div>
			<?php } ?>
			picture <input type="hidden" id="picturechange" name="picturechange" value=""><br />
			<div id="picturedelete" style="padding:0px;display:<?php if($therecord["picturemime"]) echo "block"; else echo "none";?>"><input type="button" class="Buttons" value="delete picture" onClick="deletePicture('picture')"/></div>
			<div id="pictureadd" style="padding:0px;display:<?php if($therecord["picturemime"]) echo "none"; else echo "block";?>"><input id="pictureupload" name="pictureupload" type="file" size="40" onChange="updatePictureStatus('picture','upload')" /></div>
		</label>
	</fieldset>
	<?php include("../../include/createmodifiedby.php"); ?>	
</div>
<?php include("../../footer.php");?>
</form>
</body>
</html>