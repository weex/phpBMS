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

		$temparray = array("Inventory"=>"Inventory","Non-Inventory"=>"Non-Inventory","Service"=>"Service","Kit"=>"Kit","Assembly"=>"Assembly");
		$theinput = new inputBasicList("type",$therecord["type"],$temparray);
		$theform->addField($theinput);

		$temparray = array("In Stock (Available)"=>"In Stock","Out of Stock (Unavailable)"=>"Out of Stock","Back Ordered"=>"Backordered");
		$theinput = new inputBasicList("status",$therecord["status"],$temparray,"availablity");
		$theform->addField($theinput);

		$theinput = new inputField("partname",$therecord["partname"],"name");
		$theform->addField($theinput);

		$theinput = new inputField("partnumber",$therecord["partnumber"],"part number",true);
		$theform->addField($theinput);

		$theinput = new inputField("upc",$therecord["upc"],"UPC");
		$theform->addField($theinput);

		$theinput = new inputField("description",$therecord["description"],"description",false,NULL,96,255);
		$theform->addField($theinput);

		$theinput = new inputCurrency("unitprice", $therecord["unitprice"], "unit price" ,true);
                $theinput->setAttribute("class", "important");
		$theform->addField($theinput);

		$theinput = new inputCurrency("unitcost", $therecord["unitcost"], "unit cost");
		$theform->addField($theinput);

		$markup=0;
		if($therecord["unitcost"]!=0)
			$markup=round(($therecord["unitprice"]/$therecord["unitcost"])-1,4)*100;

		$theinput = new inputPercentage("markup", $markup, "mark-up",2);
		$theinput->setAttribute("size","10");
		$theform->addField($theinput);

		$theinput = new inputField("unitofmeasure",$therecord["unitofmeasure"],"unit of measure");
		$theform->addField($theinput);

		$theinput = new inputField("weight",$therecord["weight"],NULL,false,"real");
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

		$theinput = new inputSmartSearch($db, "morecategories", "Pick Product Category For Product", "", "category");
		$theform->addField($theinput);

                $theinput = new inputTextarea("memo", $therecord["memo"], "memo", false, 4, 48, false);
                $theform->addField($theinput);

		$thetable->getCustomFieldInfo();
		$theform->prepCustomFields($db, $thetable->customFieldsQueryResult, $therecord);
		$theform->jsMerge();
		//==============================================================
		//End Form Elements

	include("header.php");
?>
<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data" name="record" id="record" onsubmit="return false;">
<?php $phpbms->showTabs("products entry","tab:17346362-261b-4d1d-fa77-99e84cfd9b8a",$therecord["id"]);?><div class="bodyline">
        <input type="hidden" value="" name="command" id="hiddenCommand"/>

	<div id="topButtons"><?php showSaveCancel(1); ?></div>
	<h1 id="topTitle"><?php echo $pageTitle ?></h1>

	<div id="rightsideDiv">
		<fieldset>
			<legend>attributes</legend>


			<p><?php $theform->showField("inactive")?></p>

			<p>
                            <label for="categoryid">master category</label><br />
                            <?php $thetable->displayProductCategories($therecord["categoryid"]) ?>
			</p>

			<p><?php $theform->showField("type")?></p>

			<p><?php $theform->showField("status")?></p>

			<p><?php $theform->showField("taxable")?></p>

		</fieldset>

                <fieldset>
                    <legend>Additional Categories</legend>
                    <p>
                        <?php $theform->showField("morecategories")?>
                        <button type="button" id="addCatButton" class="graphicButtons buttonPlus" title="Add Category"><span>+</span></button>
                    </p>

                    <?php $thetable->displayAdditionalCategories($therecord["id"]) ?>

                </fieldset>

	</div>

	<div id="leftsideDiv">
		<fieldset>
			<legend>identification</legend>

			<p class="big" id="partNameP"><?php $theform->showField("partname")?></p>

			<p class="big"><?php $theform->showField("partnumber") ?></p>


			<p><?php $theform->showField("description") ?></p>

			<p><?php $theform->showField("upc") ?></p>

		</fieldset>

		<fieldset>
			<legend>price / cost</legend>


			<p class="costsP"><?php $theform->showField("unitprice")?> = </p>

			<p class="costsP"><?php $theform->showField("unitcost")?> + </p>

			<p class="costsP"><?php $theform->showField("markup")?></p>

			<p>
			    <br />
                            <button type="button" id="updatePrice" class="Buttons">calculate price</button>
			</p>

		</fieldset>

                <fieldset>
                    <legend>Weight / Measurements</legend>

                    <p><?php $theform->showField("weight")?></p>

                    <p><?php $theform->showField("unitofmeasure")?></p>

                </fieldset>

		<fieldset>
			<legend>shipping</legend>
			<p>
				<label for="packagesperitem">items per package <span class="notes">(number of product items that can fit in a shipping package)</span></label><br />
				<?php $theform->showfield("packagesperitem")?>
			</p>

			<p><?php $theform->showfield("isprepackaged");?> <span class="notes">(product is not packed with any other product.)</span></p>

			<p><?php $theform->showfield("isoversized");?> <span class="notes">(product must be delivered in a box designated as oversized for shipping purposes.)</span></p>

                </fieldset>

		<fieldset>
			<legend><label for="memo">memo</label></legend>

                        <p><?php $theform->showField("memo");?></p>

		</fieldset>

		<fieldset>
			<legend>web</legend>
			<p>
				<?php $theform->showfield("webenabled");?>
			</p>

			<div style=" <?php if(!$therecord["webenabled"]) echo "display:none;" ?>" id="webstuff">
				<p>
					<label for="keywords">keywords <span class="notes">(comma separated key word list)</span></label><br />
					<input type="text" id="keywords" name="keywords" value="<?php echo htmlQuotes($therecord["keywords"])?>" size="40" maxlength="255"/>
				</p>
				<div class="fauxP">
					<label for="webdescription">web description <span class="notes">(HTML acceptable)</span></label><br />

					<div style=" <?php if($therecord["webdescription"]) echo "display:none;"?>" id="webDescEdit">
						<textarea id="webdescription" name="webdescription" cols="60" rows="6"><?php echo $therecord["webdescription"] ?></textarea>
					</div>
					<div style=" <?php if(!$therecord["webdescription"]) echo "display:none;"?>" id="webDescPreview">
					<?php echo $therecord["webdescription"] ?>
					</div>
					<div><button id="buttonWebPreview" type="button" class="Buttons"><?php if(!$therecord["webdescription"]) echo "preview"; else echo "edit"?></button></div>

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
					<div id="thumbdelete" style="display:<?php if($therecord["thumbnailmime"]) echo "block"; else echo "none";?>"><button id="deleteThumbButton" type="button" class="Buttons">delete thumbnail</button></div>
					<div id="thumbadd" style="display:<?php if($therecord["thumbnailmime"]) echo "none"; else echo "block";?>"><input id="thumbnailupload" name="thumbnailupload" type="file" size="40"/></div>
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
					<div id="picturedelete" style="display:<?php if($therecord["picturemime"]) echo "block"; else echo "none";?>"><button id="deletePictureButton" type="button" class="Buttons">delete picture</button></div>
					<div id="pictureadd" style="display:<?php if($therecord["picturemime"]) echo "none"; else echo "block";?>"><input id="pictureupload" name="pictureupload" type="file" size="40" onchange="updatePictureStatus('picture','upload')" tabindex="270"/></div>
				</div>
			</div>
		</fieldset>

                <?php $theform->showCustomFields($db, $thetable->customFieldsQueryResult) ?>

	</div>


	<?php $theform->showGeneralInfo($phpbms,$therecord);?>
</div>
</form>
<?php include("footer.php");?>
