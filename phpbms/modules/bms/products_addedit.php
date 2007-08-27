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
	include("include/tables.php");
	include("include/fields.php");
	include("include/products.php");

	$thetable = new products($db,4);
	$therecord = $thetable->processAddEditPage();
	
	if(isset($therecord["phpbmsStatus"]))
		$statusmessage = $therecord["phpbmsStatus"];
	
	$phpbms->topJS[] = "numcats = ".$thetable->checkNumberCategories().";";
	
	$pageTitle="Product";
	
	$phpbms->cssIncludes[] = "pages/products.css";
	$phpbms->jsIncludes[] = "modules/bms/javascript/product.js";

		//Form Elements
		//==============================================================
		$theform = new phpbmsForm();
		$theform->enctype = "multipart/form-data";
		
		$theinput = new inputCheckbox("inactive",$therecord["inactive"]);
		$theform->addField($theinput);

		$theinput = new inputCheckbox("taxable",$therecord["taxable"]);
		$theform->addField($theinput);
		
		$theinput = new inputField("partnumber",$therecord["partnumber"],"part number",true,NULL,20,32,false);
		$theinput->setAttribute("onchange","checkPartNumber()");
		$theinput->setAttribute("class","important");
		$theform->addField($theinput);
		
		$theinput = new inputCurrency("unitprice", $therecord["unitprice"], "sell price" ,true);
		$theinput->setAttribute("class","important");
		$phpbms->bottomJS[]='var myitem=getObjectFromID("unitprice"); myitem.thechange=calculateMarkUp;';	
		$theform->addField($theinput);

		$theinput = new inputCurrency("unitcost", $therecord["unitcost"], "cost");
		$phpbms->bottomJS[]='var myitem=getObjectFromID("unitcost"); myitem.thechange=calculateMarkUp;';	
		$theform->addField($theinput);
		

		$markup=0;
		if($therecord["unitcost"]!=0){
			$markup=round(($therecord["unitprice"]/$therecord["unitcost"])-1,4)*100;
		}				
		$theinput = new inputPercentage("markup", $markup, "mark-up",2);
		$theinput->setAttribute("size","10");
		$theform->addField($theinput);

		$theinput = new inputField("weight",$therecord["weight"],NULL,false,"real",10,16,false);
		$theform->addField($theinput);

		if ($therecord["packagesperitem"])
			$itemsperpackage=1/$therecord["packagesperitem"];
		else
			$itemsperpackage=NULL;
		
		$theinput = new inputField("packagesperitem",$itemsperpackage,NULL,false,"real",10,16,false);
		$theform->addField($theinput);
		
		$theinput = new inputCheckbox("isprepackaged",$therecord["isprepackaged"],"prepackaged");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("isoversized",$therecord["isoversized"],"oversized");
		$theform->addField($theinput);

		$theinput = new inputCheckbox("webenabled",$therecord["webenabled"],"web enabled");
		$theform->addField($theinput);

		$theform->jsMerge();
		//==============================================================
		//End Form Elements
	
	include("header.php");	
?>
<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data" name="record" onsubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onclick="return false;" /></div>
<?php $phpbms->showTabs("products entry",10,$therecord["id"]);?><div class="bodyline">

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
				<?php $theform->showField("inactive")?>
			</p>

			<p>
				<?php $theform->showField("taxable")?>
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
				<textarea cols="10" rows="11" name="memo" id="memo" tabindex="300"><?php echo $therecord["memo"]?></textarea>
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
				<?php $theform->showField("partnumber") ?>
			</p>
			
			<div class="fauxP">
				<label for="categoryid" class="important">product category</label><br />
				<?php $thetable->displayProductCategories($therecord["categoryid"]) ?>
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
				<?php $theform->showField("unitprice")?>
			</p>

			<p class="costsP">
				<?php $theform->showField("unitcost")?>
			</p>			
			<p class="costsP">
				<?php $theform->showField("markup")?>
			</p>
			<p>
				<br />
				<input type="button" name="updateprice" value="update price" class="Buttons" onclick="calculatePrice()" tabindex="160" />
			</p>

			<p style="clear:both;">
				<label for="unitofmeasure">units of measure</label><br />
				<input name="unitofmeasure" type="text" id="unitofmeasure" value="<?php echo htmlQuotes($therecord["unitofmeasure"])?>" size="40" maxlength="32" tabindex="190"/>
			</p>											

		</fieldset>
		
		<fieldset>
			<legend>weight / shipping</legend>
			<p>
				<label for="weight">weight</label> (lbs.)<br />
				<?php  $theform->showfield("weight")?><br />
				<span class="notes"><strong>Note:</strong> Weight must be in lbs. in order for shipping to be auto-estimated correctly.</span>
			</p>
			
			<p>
				<label for="packagesperitem">items per package <span class="notes">(number of product items that can fit in a shipping package)</span></label><br />
				<?php $theform->showfield("packagesperitem")?>	
			</p>
			
			<p>
				<?php $theform->showfield("isprepackaged");?> <span class="notes">(product is not packed with any other product.)</span>
			</p>
			<p>
				<?php $theform->showfield("isoversized");?> <span class="notes">(product must be delivered in a box designated as oversized for shipping purposes.)</span>
			</p>
		</fieldset>
	</div>
	
	<fieldset>
		<legend>web</legend>
		<p>
			<?php $theform->showfield("webenabled");?>
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
					<img id="thumbpic" src="<?php echo APP_PATH ?>dbgraphic.php?t=products&f=thumbnail&mf=thumbnailmime&r=<?php echo $therecord["id"]?>" style="border: 1px solid black; display: block; margin: 3px;;" />
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
					<img id="picturepic" src="<?php echo APP_PATH ?>dbgraphic.php?t=products&f=picture&mf=picturemime&r=<?php echo $therecord["id"]?>" style="border: 1px solid black; display: block; margin: 3px;;" />
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

	<?php $theform->showCreateModify($phpbms,$therecord);?>	
</div>
</form>
<?php include("footer.php");?>
