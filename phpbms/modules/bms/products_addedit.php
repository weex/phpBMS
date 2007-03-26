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
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/products_functions.php");
	include("include/products_addedit_include.php");
	
	$catnumber=checkNumberCategories($dblink);
	
	$pageTitle="Product"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php require("../../head.php")?>
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/products.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript">
	var numcats=<?php echo $catnumber?>;
</script>
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/product.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>

<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data" name="record" onsubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div>
<?php product_tabs("General",$therecord["id"]);?><div class="bodyline">

	<div id="topButtons"><?php showSaveCancel(1); ?></div>
	<h1 id="topTitle"><?php echo $pageTitle ?></h1>
	
	<div id="rightsideDiv">
		<fieldset>
			<legend>attributes</legend>
			<p>
				<label for="id">id</label><br />
				<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="readonly" class="uneditable" />			
			</p>

			<p>
				<?php fieldCheckbox("inactive",$therecord["inactive"],false,array("tabindex"=>"50"))?><label for="inactive">inactive</label>
			</p>

			<p>
				<?php fieldCheckbox("taxable",$therecord["taxable"],false,array("tabindex"=>"60"))?><label for="taxable">taxable</label>
			</p>

			<p>
				<span class="important">product type</span><br />
				<input type="radio" name="type" id="typeInventory" value="Inventory" <?php if($therecord["type"]=="Inventory") echo "checked=\"checked\""?> class="radiochecks" tabindex="70"/><label for="typeInventory">inventory</label><br />
				<input type="radio" name="type" id="typeNonInventory" value="Non-Inventory" <?php if($therecord["type"]=="Non-Inventory") echo "checked=\"checked\""?> class="radiochecks" tabindex="80"/><label for="typeNonInventory" >non-inventory</label><br />
				<input type="radio" name="type" id="typeService" value="Service" <?php if($therecord["type"]=="Service") echo "checked=\"checked\""?> class="radiochecks"  tabindex="90"/><label for="typeService" >service</label><br />
				<input type="radio" name="type" id="typeKit" value="Kit" <?php if($therecord["type"]=="Kit") echo "checked=\"checked\""?> class="radiochecks"  tabindex="100"/><label for="typeKit" >kit</label><br />
				<input type="radio" name="type" id="typeAssembly" value="Assembly" <?php if($therecord["type"]=="Assembly") echo "checked=\"checked\""?> class="radiochecks"  tabindex="110"/><label for="typeAssembly">assembly</label>
			</p>
			
			<p>
				<span class="important">available status</span><br />
				<input type="radio" name="status" id="statusInStock" value="In Stock" <?php if($therecord["status"]=="In Stock") echo "checked=\"checked\""?> class="radiochecks"  tabindex="120"/> <label for="statusInStock">in stock / available</label><br />
				<input type="radio" name="status" id="statusOutOfStock" value="Out of Stock" <?php if($therecord["status"]=="Out of Stock") echo "checked=\"checked\""?> class="radiochecks"  tabindex="130"/><label for="statusOutOfStock">out of stock / unavailable</label><br />
				<input type="radio" name="status" id="statusBackordered" value="Backordered" <?php if($therecord["status"]=="Backordered") echo "checked=\"checked\""?> class="radiochecks"  tabindex="140"/><label for="statusBackordered">backordered</label>
			</p>
		</fieldset>
		
		<fieldset>
			<legend><label for="memo">memo</label></legend>
			<p>
				<textarea cols="10" rows="11" style="width:98%" name="memo" id="memo" tabindex="300"><?php echo $therecord["memo"]?></textarea>
			</p>
		</fieldset>
	</div>
	
	<div id="leftsideDiv">
		<fieldset>
			<legend>product</legend>
			
			<p>
				<label for="partname" class="important">name</label><br />
				<input name="partname" type="text" class="important" id="partname" value="<?php echo htmlQuotes($therecord["partname"])?>" size="42" maxlength="128" tabindex="10" />							
			</p>
			
			<p>
				<label for="partnumber"><span class="important">part number</span> <span class="notes">(must be unique)</span></label><br />
				<?php fieldText("partnumber",$therecord["partnumber"],1,"Part number name cannot be blank.","",Array("size"=>"20","maxlength"=>"32","class"=>"important","tabindex"=>"20","onchange"=>"checkUnique('../../',this.value,this.name,'products','partnumber','".$therecord["id"]."')")); ?>				
			</p>
			
			<div class="fauxP">
				<label for="categoryid" class="important">product category</label><br />
				<?php displayProductCategories($therecord["categoryid"],$dblink) ?>
			</div>
			
			<p>
				<label for="upc">UPC</label><br />
				<input name="upc" type="text" id="upc" value="<?php echo htmlQuotes($therecord["upc"])?>" size="40" maxlength="128" tabindex="40" />
			</p>
			
			<p>
				<label for="description" style="">description</label><br />
				<input name="description" type="text" id="description" value="<?php echo htmlQuotes($therecord["description"])?>" size="34" maxlength="255" tabindex="40" />			
				<span class="notes">
					<strong>Note:</strong> The description will show by default in the memo field of invoice
					line items, but can be modified on the invoice.
				</span>
			</p>
			
		</fieldset>
		
		<fieldset>
			<legend>price / cost</legend>
			<p>
				<label for="unitprice" class="important">sell price</label><br />
				<?php fieldCurrency("unitprice",$therecord["unitprice"],0,"",Array("size"=>"10","maxlength"=>"32","class"=>"important","tabindex"=>"170"))?>
				<script language="JavaScript" type="text/javascript">var myitem=getObjectFromID("unitprice"); myitem.thechange=calculateMarkUp;</script>			
			</p>
			
			<p class="costsP">
				<label for="unitcost">cost</label><br />
				<?php fieldCurrency("unitcost",$therecord["unitcost"],0,"",Array("size"=>"10","maxlength"=>"32","tabindex"=>"150"))?>		
				<script language="JavaScript" type="text/javascript">var myitem=getObjectFromID("unitcost"); myitem.thechange=calculateMarkUp;</script>								
			</p>			
			<p class="costsP">
				<label for="markup">mark-up</label><br />
				<?php 
					$markup=0;
					if($therecord["unitcost"]!=0){
						$markup=round(($therecord["unitprice"]/$therecord["unitcost"])-1,4)*100;
					}				
					fieldPercentage("markup",$markup,2,0,"",Array("size"=>"10","maxlength"=>"10"));
				?>
			</p>
			<p>
				<br />
				<input type="button" name="updateprice" value="update price" class="Buttons" onclick="calculatePrice()" tabindex="160" />
			</p>			
		</fieldset>
		
		<fieldset>
			<legend>weight / shipping</legend>
			<p>
				<label for="weight">weight</label><br />
				<?php fieldText("weight",$therecord["weight"],0,"Weight must be a valid number.","real",Array("size"=>"10","maxlength"=>"16","tabindex"=>"180")); ?><br />
				<span class="notes"><strong>Note:</strong> Weight must be in lbs. in order for shipping to be auto-estimated correctly.</span>
			</p>
			
			<p>
				<label for="unitofmeasure">units of measure</label><br />
				<input name="unitofmeasure" type="text" id="unitofmeasure" value="<?php echo htmlQuotes($therecord["unitofmeasure"])?>" size="40" maxlength="32" tabindex="190"/>
			</p>											
			
			<p>
				<label for="packagesperitem">items per package <span class="notes">(number of product items that can fit in a shipping package)</span></label><br />
				<?php 
					if ($therecord["packagesperitem"])
						$itemsperpackage=1/$therecord["packagesperitem"];
					else
						$itemsperpackage=NULL;
				?><?php fieldText("packagesperitem",$itemsperpackage,0,"Packages per item must be a valid number.","real",Array("size"=>"10","maxlength"=>"16","tabindex"=>"200")); ?>	
			</p>
			
			<p>
				<?php fieldCheckbox("isprepackaged",$therecord["isprepackaged"],false,Array("tabindex"=>"210"))?><label for="isprepackaged">pre-packaged</label> <span class="notes">(product is not packed with any other product.)</span>
			</p>
			<p>
				<?php fieldCheckbox("isoversized",$therecord["isoversized"],false,Array("tabindex"=>"210"))?><label for="isoversized">oversized</label> <span class="notes">(product must be delivered in a box designated as oversized for shipping purposes.)</span>
			</p>
		</fieldset>
	</div>
	
	<fieldset>
		<legend>web</legend>
		<p>
			<?php fieldCheckbox("webenabled",$therecord["webenabled"],false,Array("tabindex"=>"220"))?><label for="webenabled">web enabled</label>
		</p>
				
		<div style=" <?php if(!$therecord["webenabled"]) echo "display:none;" ?>" id="webstuff">
			<p>
				<label for="keywords">keywords <span class="notes">(comma separated key word list)</span></label><br />
				<input type="text" id="keywords" name="keywords" value="<?php echo htmlQuotes($therecord["keywords"])?>" tabindex="230" style="width:98%" />
			</p>
			<div class="fauxP">
				<label for="webdescription">web description <span class="notes">(HTML acceptable)</span></label><br />
				
				<div style=" <?php if($therecord["webdescription"]) echo "display:none;"?>" id="webDescEdit">
					<textarea id="webdescription" name="webdescription" cols="60" rows="6" style="width:98%" tabindex="240"><?php echo $therecord["webdescription"] ?></textarea>
				</div>
				<div style=" <?php if(!$therecord["webdescription"]) echo "display:none;"?>" id="webDescPreview">
				<?php echo $therecord["webdescription"] ?>
				</div>			
				<div><input id="buttonWebPreview" type="button" class="Buttons" onclick="editPreviewWebDesc(this)" value="<?php if(!$therecord["webdescription"]) echo "preview"; else echo "edit"?>" tabindex="250"/></div>

			</div>
			
			<div class="fauxP">
				thumbnail graphic<br />
				<?php if($therecord["thumbnailmime"]) {?>
					<img id="thumbpic" src="<?php echo $_SESSION["app_path"] ?>dbgraphic.php?t=products&f=thumbnail&mf=thumbnailmime&r=<?php echo $therecord["id"]?>" style="border: 1px solid black; display: block; margin: 3px;;" />
				<?php } else {?>
					<div id="noThumb" class="tiny" align="center">no thumbnail</div>
				<?php } ?>
				upload thumbnail<br />
				<input type="hidden" id="thumbchange" name="thumbchange" value="" />
				<div id="thumbdelete" style="display:<?php if($therecord["thumbnailmime"]) echo "block"; else echo "none";?>"><input type="button" class="Buttons" value="delete thumbnail" onclick="deletePicture('thumb')" tabindex="260"/></div>
				<div id="thumbadd" style="display:<?php if($therecord["thumbnailmime"]) echo "none"; else echo "block";?>"><input id="thumbnailupload" name="thumbnailupload" type="file" size="40" onchange="updatePictureStatus('thumb','upload')" tabindex="260" /></div>
			</div>

			<div class="fauxP">
				main picture<br />
				<?php if($therecord["picturemime"]) {?>
					<img id="picturepic" src="<?php echo $_SESSION["app_path"] ?>dbgraphic.php?t=products&f=picture&mf=picturemime&r=<?php echo $therecord["id"]?>" style="border: 1px solid black; display: block; margin: 3px;;" />
				<?php } else {?>
					<div id="noPicture" class="tiny" align="center">no picture</div>
				<?php } ?>
				upload picture <br />
				<input type="hidden" id="picturechange" name="picturechange" value="" />
				<div id="picturedelete" style="display:<?php if($therecord["picturemime"]) echo "block"; else echo "none";?>"><input type="button" class="Buttons" value="delete picture" onclick="deletePicture('picture')" tabindex="270"/></div>				
				<div id="pictureadd" style="display:<?php if($therecord["picturemime"]) echo "none"; else echo "block";?>"><input id="pictureupload" name="pictureupload" type="file" size="40" onchange="updatePictureStatus('picture','upload')" tabindex="270"/></div>				
			</div>
		</div>
	</fieldset>	

	<?php include("../../include/createmodifiedby.php"); ?>	
</div>
<?php include("../../footer.php");?>
</form>
</body>
</html>