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
<body onLoad="init();"><?php include("../../menu.php")?>

<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<?php product_tabs("General",$therecord["id"]);?><div class="bodyline">

	<div style="float:right;width:160px;"><?php showSaveCancel(1); ?></div>
	<h1 style="margin-right:162px;"><?php echo $pageTitle ?></h1>
	
	<div style="clear:both;float:right;width:200px;margin-top:0px;">
		<fieldset>
			<legend>attributes</legend>
			<label for="id">
				id<br />
				<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:98%">
			</label>
			<label for="inactive">
				<?PHP field_checkbox("inactive",$therecord["inactive"],false,array("tabindex"=>"50"))?> inactive
			</label>
			<label for="inactive"></label>
				<?PHP field_checkbox("taxable",$therecord["taxable"],false,array("tabindex"=>"60"))?> taxable
			</label>
			<div>
				<strong>product type</strong><br />
				<label for="typeInventory" style="padding-bottom:0px;"><input type="radio" name="type" id="typeInventory" value="Inventory" <?php if($therecord["type"]=="Inventory") echo "checked"?> class="radiochecks" align="baseline" tabindex="70"/> inventory</label>
				<label for="typeNonInventory" style="padding-bottom:0px;"><input type="radio" name="type" id="typeNonInventory" value="Non-Inventory" <?php if($therecord["type"]=="Non-Inventory") echo "checked"?> class="radiochecks" align="baseline" tabindex="80"/> non-inventory</label>
				<label for="typeService" style="padding-bottom:0px;"><input type="radio" name="type" id="typeService" value="Service" <?php if($therecord["type"]=="Service") echo "checked"?> class="radiochecks" align="baseline" tabindex="90"/> service</label>
				<label for="typeKit" style="padding-bottom:0px;"><input type="radio" name="type" id="typeKit" value="Kit" <?php if($therecord["type"]=="Kit") echo "checked"?> class="radiochecks" align="baseline" tabindex="100"/> kit</label>
				<label for="typeAssembly" style="padding-bottom:0px;"><input type="radio" name="type" id="typeAssembly" value="Assembly" <?php if($therecord["type"]=="Assembly") echo "checked"?> class="radiochecks" align="baseline" tabindex="110"/> assembly</label>
			</div>
			<div>
				<strong>available status</strong><br />
				<label for="statusInStock" style="padding-bottom:0px;"><input type="radio" name="status" id="statusInStock" value="In Stock" <?php if($therecord["status"]=="In Stock") echo "checked"?> class="radiochecks" align="baseline" tabindex="120"/> in stock / available</em></label>
				<label for="statusOutOfStock" style="padding-bottom:0px;"><input type="radio" name="status" id="statusOutOfStock" value="Out of Stock" <?php if($therecord["status"]=="Out of Stock") echo "checked"?> class="radiochecks" align="baseline" tabindex="130"/> out of stock / unavailable</label>
				<label for="statusBackordered" style="padding-bottom:0px;"><input type="radio" name="status" id="statusBackordered" value="Backordered" <?php if($therecord["status"]=="Backordered") echo "checked"?> class="radiochecks" align="baseline" tabindex="140"/> backordered</label>
			</div>
		</fieldset>
		<fieldset>
			<legend><label style="margin:0px;padding:0px;" for="memo">memo</label></legend>
			<div style="padding-top:0px">
				<textarea cols="10" rows="11" style="width:98%" name="memo" id="memo" tabindex="300"><?php echo $therecord["memo"]?></textarea>
			</div>
		</fieldset>
	</div>
	<div style="margin-right:200px;">
		<fieldset>
			<legend>product</legend>
			<label for="partname" class="important">
				name<br />
				<input name="partname" type="text" class="important" id="partname" value="<?php echo htmlQuotes($therecord["partname"])?>" size="34" maxlength="128" style="width:98%" tabindex="10">			
			</label>
			<table border="0" cellspacing="0" cellpadding="0" width="99%">
				<tr>
					<td width="100%">
						<label for="partnumber" >
							<strong>part number</strong> <em>(must be unique)</em><br/>
							<?PHP field_text("partnumber",$therecord["partnumber"],1,"Part number name cannot be blank.","",Array("size"=>"28","maxlength"=>"32","style"=>"width:98%","class"=>"important","tabindex"=>"20","onChange"=>"checkUnique('../../',this.value,this.name,'products','partnumber','".$therecord["id"]."')")); ?>
						</label>
					</td>
					<td valign="top" nowrap>
						<label for="ds-categoryid" class="important">
							category<br />
							<?PHP autofill("categoryid",$therecord["categoryid"],7,"productcategories.id","productcategories.name","\"\"","",Array("size"=>"28","maxlength"=>"64","tabindex"=>"30"),1,"Category name cannot be blank.") ?>
						</label>
					</td>
				</tr>
			</table>
			<label for="upc" style="">
				UPC<br />
				<input name="upc" type="text" id="upc" style="width:98%" value="<?php echo htmlQuotes($therecord["upc"])?>" size="34" maxlength="128" tabindex="40" />
			</label>
			<label for="description" style="">
				description<br />
				<input name="description" type="text" id="description" style="width:98%" value="<?php echo htmlQuotes($therecord["description"])?>" size="34" maxlength="255" tabindex="40" />
			</label>
			<div class="small"><em>The description will show by default in the memo field of invoice
									line items, but can be modified on the invoice.</em></div>
		</fieldset>
		
		<fieldset>
			<legend>cost / weight</legend>
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="middle">
						<label for="unitcost" >
							cost<br />
							<?PHP field_dollar("unitcost",$therecord["unitcost"],0,"",Array("size"=>"10","maxlength"=>"32","tabindex"=>"150"))?>		
							<script language="javascript">var myitem=getObjectFromID("unitcost"); myitem.thechange=calculateMarkUp;</script>
						</label>		
					</td>
					<td>
						<label for="markup" style="padding-bottom:0px;">mark-up</label>
						<div style="padding-top:0px;">
							<?php 
								$markup=0;
								if($therecord["unitcost"]!=0){
									$markup=round(($therecord["unitprice"]/$therecord["unitcost"])-1,4)*100;
								}				
								field_percentage("markup",$markup,2,0,"",Array("size"=>"10","maxlength"=>"10"));
							?> <input type="button" name="updateprice" value="update price" class="Buttons" onClick="calculatePrice()" tabindex="160" />
						</div>
					</td>
					<td>
						<label for="unitprice" class="important">
							sell price<br />
							<?PHP field_dollar("unitprice",$therecord["unitprice"],0,"",Array("size"=>"10","maxlength"=>"32","class"=>"important","tabindex"=>"170"))?>
							<script language="javascript">var myitem=getObjectFromID("unitprice"); myitem.thechange=calculateMarkUp;</script>
						</label>
					</td>
				</tr>
				<tr>
					<td>
						<label for="weight">
							weight<br />
							<?PHP field_text("weight",$therecord["weight"],0,"Weight must be a valid number.","real",Array("size"=>"10","maxlength"=>"16","style"=>"text-align:right;","tabindex"=>"180")); ?>	
						</label>
					</td>
					<td colspan="2">
						<label for="unitofmeasure">
							units (UOM)<br />
							<input name="unitofmeasure" type="text" id="unitofmeasure" value="<?php echo htmlQuotes($therecord["unitofmeasure"])?>" size="24" maxlength="32" style="width:98%" tabindex="190"/>
						</label>
					</td>
				</tr>
			</table>
			<div class="small"><em>Weight must be in lbs. in order for shipping to be estimated correctly.</em></div>
		</fieldset>
		
		<fieldset>
			<legend>shipping</legend>
			<label for="packagesperitem">
				items per package<br />
				<?php 
					if ($therecord["packagesperitem"])
						$itemsperpackage=1/$therecord["packagesperitem"];
					else
						$itemsperpackage=NULL;
				?><?PHP field_text("packagesperitem",$itemsperpackage,0,"Packages per item must be a valid number.","real",Array("size"=>"10","maxlength"=>"16","tabindex"=>"200")); ?>	
			</label>
			<label for="isprepackaged">
				<?PHP field_checkbox("isprepackaged",$therecord["isprepackaged"],false,Array("tabindex"=>"210"))?>pre-packaged <em>(product is not packed with any other product.)</em>
			</label>
			<label for="isoversized">
				<?PHP field_checkbox("isoversized",$therecord["isoversized"],false,Array("tabindex"=>"210"))?>oversized <em>(product must be delivered in a oversized box.)</em>
			</label>
			</fieldset>
		<fieldset>
			<legend>web</legend>
			<label for="webenabled">
				<?PHP field_checkbox("webenabled",$therecord["webenabled"],false,Array("onClick"=>"showWeb(this)","tabindex"=>"220"))?>web enabled
			</label>
			<div style="padding:0px;margin:0px;<?php if(!$therecord["webenabled"]) echo "display:none;" ?>" id="webstuff">
				<label for="keywoards">
					keywords <em>(comma separated key word list)</em><br />
					<input type="text" id="keywords" name="keywords" value="<?php echo htmlQuotes($therecord["keywords"])?>" tabindex="230" style="width:98%" />
				</label>
				<label for="webdescription">
					<div style="margin:0px;padding:0px;">web description <em>(HTML acceptable)</em></div>
					<div style="margin:0px;padding:0px;<?php if($therecord["webdescription"]) echo "display:none;"?>" id="webDescEdit">
						<textarea id="webdescription" name="webdescription" cols="60" rows="6" style="width:98%" tabindex="240"><?php echo $therecord["webdescription"] ?></textarea>
					</div>
					<div style="margin:0px;border:1px solid black; background-color:white;<?php if(!$therecord["webdescription"]) echo "display:none;"?>" id="webDescPreview">
					<?php echo $therecord["webdescription"] ?>
					</div>			
					<div style="width:98%" align="right"><input type="button" class="Buttons" onClick="editPreviewWebDesc(this)" value="<?php if(!$therecord["webdescription"]) echo "preview"; else echo "edit"?>" style="width:75px;" tabindex="250"/></div>
				</label>
				<label for="thumb" style="">
					<?php if($therecord["thumbnailmime"]) {?>
						<img id="thumbpic" src="<?php echo $_SESSION["app_path"] ?>dbgraphic.php?t=products&f=thumbnail&mf=thumbnailmime&r=<?php echo $therecord["id"]?>" style="border: 1px solid black; display: block; margin: 3px;;" />
					<?php } else {?>
						<div style="margin:3px;border:1px solid black;background-color:white;color:#999999;width:75px;height:75px" class="tiny" align="center"><br/><br/>no thumbnail</div>
					<?php } ?>
					thumbnail <input type="hidden" id="thumbchange" name="thumbchange" value=""><br />
					<div id="thumbdelete" style="padding:0px;display:<?php if($therecord["thumbnailmime"]) echo "block"; else echo "none";?>"><input type="button" class="Buttons" value="delete thumbnail" onClick="deletePicture('thumb')" tabindex="260"/></div>
					<div id="thumbadd" style="padding:0px;display:<?php if($therecord["thumbnailmime"]) echo "none"; else echo "block";?>"><input id="thumbnailupload" name="thumbnailupload" type="file" size="40" onChange="updatePictureStatus('thumb','upload')" tabindex="260" /></div>
				</label>
				<label for="picture" style="">
					<?php if($therecord["picturemime"]) {?>
						<img id="picturepic" src="<?php echo $_SESSION["app_path"] ?>dbgraphic.php?t=products&f=picture&mf=picturemime&r=<?php echo $therecord["id"]?>" style="border: 1px solid black; display: block; margin: 3px;;" />
					<?php } else {?>
						<div style="margin:3px;border:1px solid black;background-color:white;color:#999999;width:150px;height:150px" class="tiny" align="center"><br/><br/><br/>no picture</div>
					<?php } ?>
					picture <input type="hidden" id="picturechange" name="picturechange" value=""><br />
					<div id="picturedelete" style="padding:0px;display:<?php if($therecord["picturemime"]) echo "block"; else echo "none";?>"><input type="button" class="Buttons" value="delete picture" onClick="deletePicture('picture')" tabindex="270"/></div>
					<div id="pictureadd" style="padding:0px;display:<?php if($therecord["picturemime"]) echo "none"; else echo "block";?>"><input id="pictureupload" name="pictureupload" type="file" size="40" onChange="updatePictureStatus('picture','upload')" tabindex="270"/></div>
				</label>
			</div>
		</fieldset>
	</div>
	<?php include("../../include/createmodifiedby.php"); ?>	
</div>
<?php include("../../footer.php");?>
</form>
</body>
</html>