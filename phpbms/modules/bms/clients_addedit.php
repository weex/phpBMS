<?php 
/*
 $Rev$ | $LastChangedBy$
 $LastChangedDate$
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

	include("include/clients_functions.php");
	include("include/clients_addedit_include.php");
	
	$pageTitle=ucwords($therecord["type"]);
	if($therecord["inactive"])
		$pageTitle="Inactive ".$pageTitle;
	
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php require("../../head.php")?>
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/pages/client.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" src="javascript/client.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/fields.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js" type="text/javascript"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js" type="text/javascript"></script>
</head>
<body><?php include("../../menu.php")?>

<form action="<?php echo $_SERVER["REQUEST_URI"]; if(isset($_GET["invoiceid"])) echo "&invoiceid=".$_GET["invoiceid"];  ?>" method="post" name="record" onSubmit="return validateForm(this);"><div id="dontSubmit"><input type="submit" value=" " onClick="return false;" /></div>
<?php client_tabs("General",$therecord["id"]); ?>
<div class="bodyline">
	<div id="topButtons">
		<?php showSaveCancel(1); ?>
		<?php if(isset($_GET["invoiceid"])){?>
		<div>
			<input name="gotoinvoice" type="button" value="back to order" onClick="location.href='<?php echo getAddEditFile(3) ?>?id=<?php echo $_GET["invoiceid"] ?>'" style="width:100%" class="Buttons">
		</div>
		<?php } ?>			
	</div>

	<h1 id="h1Title"><span><?php echo $pageTitle ?></span></h1>
	
	<div id="rightSideDiv">
		<fieldset>
			<legend>attributes</legend>
			<p>
				<label for="id">id</label><br />
				<input id="id" name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" tabindex="0" />
			</p>
				
			<p>
				<label for="type" class="important">type</label><br />
				<?php 
					$disabled=false;
					if($therecord["type"]=="client")
						$disabled=checkForInvoices($therecord["id"],$dblink);
							
					$attributes["class"]="important";
					$attributes["tabindex"]="5";
					$attributes["onchange"]="changeClientType(this)";
					if($disabled)
						$attributes["disabled"]="disabled";
					basic_choicelist("type",$therecord["type"],Array(Array("name"=>"prospect","value"=>"prospect"),Array("name"=>"client","value"=>"client")),$attributes);
				?>
			</p>
			
			<p>
				<?php field_checkbox("inactive",$therecord["inactive"],false,Array("tabindex"=>"4"))?><label for="inactive" class="important">inactive</label>
			</p>
			
			<p id="becameclientDiv" <?php if($therecord["type"]=="prospect") echo "style=\"display:none;\"" ?>>
				<label for="becameclient">became a client</label><br />
				<input type="text" id="becameclient" name="becameclient" readonly="true" class="uneditable" value="<?php echo $therecord["becameclient"]?>" size="8" />
			</p>
			
			<p>
				<label for="category">category</label><br />
				<?php choicelist("category",$therecord["category"],"clientcategories",array("tabindex"=>"5")); ?>
			</p>
			
		</fieldset>
	
		<fieldset>
			<legend>sales</legend>
			<div class="fauxP">
				<label for="ds-salesmanagerid">sales manager</label><br />
				<?php autofill("salesmanagerid",$therecord["salesmanagerid"],9,"users.id","concat(users.firstname,\" \",users.lastname)","\"\"","users.revoked=0 AND users.id > 1",Array("maxlength"=>"64","tabindex"=>"25")) ?>
			</div>
			
			<p>
				<label for="leadsource">lead source</label><br />
				<?php choicelist("leadsource",$therecord["leadsource"],"leadsource",Array("tabindex"=>"26","class"=>"small")); ?>
			</p>
		</fieldset>
				
		<fieldset>
			<legend>order defaults</legend>
			<p>
				<label for="paymentmethodid">payment method</label><br />
				<?php table_choicelist("paymentmethodid",$therecord["paymentmethodid"],$table="paymentmethods","id","name","","inactive=0","priority,name")?>
			</p>

			<p>
				<label for="shippingmethodid">shipping method</label><br />
				<?php table_choicelist("shippingmethodid",$therecord["shippingmethodid"],$table="shippingmethods","id","name","","inactive=0","priority,name")?>
			</p>

			<p>
				<label for="discountid">discount</label><br />
				<?php table_choicelist("discountid",$therecord["discountid"],$table="discounts","id","name","","inactive=0","name")?>
			</p>

			<p>
				<label for="taxareaid">tax area</label><br />
				<?php table_choicelist("taxareaid",$therecord["taxareaid"],$table="tax","id","name","","","name")?>
			</p>

		</fieldset>	
	</div>
		
	<div id="leftSideDiv">		
		<fieldset>
			<legend>name / company</legend>
			<p id="firstnameP">
				<label for="firstname" class="important">first name</label><br />
				<input name="firstname" id="firstname" type="text" value="<?php echo htmlQuotes($therecord["firstname"])?>" size="32" maxlength="65" class="important" tabindex="1" />
				<script language="JavaScript" type="text/javascript">var thefirstname=getObjectFromID("firstname");thefirstname.focus()</script>
			</p>
			<p>
				<label for="lastname" class="important">last name</label><br />
				<input id="lastname" name="lastname" type="text" value="<?php echo htmlQuotes($therecord["lastname"])?>" size="32" maxlength="65" class="important" tabindex="2" />				
			</p>
			<p>
				<label for="company" class="important">company</label><br />
				<input name="company" type="text" id="company" value="<?php echo htmlQuotes($therecord["company"])?>" size="71" maxlength="128" class="important" tabindex="3" />
			</p>
		</fieldset>	
							
		<fieldset>					
			<legend>contact</legend>
			<p class="phonelefts">
				<label for="workphone">work phone</label><br />
				<?php field_text("workphone",$therecord["workphone"],0,"Work phone must be in valid format.<br \/><em>(example: 505-994-6388)<\/em>","phone",Array("tabindex"=>"6","size"=>"25","maxlength"=>"32")); ?>			
			</p>
			
			<p>
				<label for="homephone">home phone</label><br />
				<?php field_text("homephone",$therecord["homephone"],0,"Home phone must be in valid format.<br \/><em>(example: 505-994-6388)<\/em>","phone",Array("tabindex"=>"7","size"=>"25","maxlength"=>"32")); ?>				
			</p>
			
			<p class="phonelefts">
				<label for="mobilephone">mobile phone</label><br />
				<?php field_text("mobilephone",$therecord["mobilephone"],0,"Mobile phone must be in valid format.<br \/><em>(example: 505-994-6388)<\/em>","phone",Array("tabindex"=>"8","size"=>"25","maxlength"=>"32")); ?>						
			</p>

			<p>
				<label for="fax">fax number</label><br />
					<?php field_text("fax",$therecord["fax"],0,"Fax number must be in valid format.<br \/><em>(example: 505-994-6388)<\/em>","phone",Array("tabindex"=>"9","size"=>"25","maxlength"=>"32")); ?>				
			</p>
			
			<p>
				<label for="otherphone">other phone</label><br />
				<?php field_text("otherphone",$therecord["otherphone"],0,"Other phone must be in valid format.<br \/><em>(example: 505-994-6388)<\/em>","phone",Array("tabindex"=>"10","size"=>"25","maxlength"=>"32")); ?>				
			</p>
			
			<p>
				<label for="email">e-mail address</label><br />
				<?php field_email("email",$therecord["email"],Array("tabindex"=>"11","size"=>"68","maxlength"=>"128")); ?>
			</p>
			
			<p>
				<label for="webaddress">web site</label><br />
				<?php field_web("webaddress",$therecord["webaddress"],Array("tabindex"=>"12","size"=>"68","maxlength"=>"128")); ?>
			</p>
		</fieldset>
		
		<fieldset>
			<legend><label for="address1">billing / main address</label></legend>
			<p><br />
				<input id="address1" name="address1" type="text" size="71" maxlength="128" value="<?php echo htmlQuotes($therecord["address1"])?>" tabindex="13"/><br />
				<input id="address2" name="address2" type="text" size="71" maxlength="128" value="<?php echo htmlQuotes($therecord["address2"])?>" tabindex="14"/>
			</p>

			<p class="csz">
				<label for="city">city</label><br />
				<input name="city" type="text" id="city" value="<?php echo htmlQuotes($therecord["city"])?>" size="35" maxlength="64" tabindex="15"/>
			</p>

			<p class="csz">
				<label for="state">state/province</label><br />
				<input name="state" type="text" id="state" value="<?php echo htmlQuotes($therecord["state"])?>" size="3" maxlength="5" tabindex="16" />
			</p>
			<p>
				<label for="postalcode">zip/postal code</label><br />
				<input name="postalcode" type="text" id="postalcode" value="<?php echo htmlQuotes($therecord["postalcode"])?>" size="12" maxlength="15" tabindex="17" />				
			</p>
			<p>
				<label for="country">country</label><br />
				<input id="country" name="country" type="text" value="<?php echo htmlQuotes($therecord["country"])?>" size="44" maxlength="128" tabindex="18"/>
			</p>
			
		</fieldset>
		
		<fieldset>
			<legend><label for="shiptoaddress1">shipping address</label></legend>
			<p>
				<span class="notes">(if different from billing/main address)</span><br />
				<input id="shiptoaddress1" name="shiptoaddress1" type="text" size="71" maxlength="128" value="<?php echo htmlQuotes($therecord["shiptoaddress1"])?>" tabindex="19" /><br />
				<input id="shiptoaddress2" name="shiptoaddress2" type="text" size="71" maxlength="128" value="<?php echo htmlQuotes($therecord["shiptoaddress2"])?>" tabindex="20" />
			</p>

			<p class="csz">
				<label for="shiptocity">city</label><br />
				<input id="shiptocity" name="shiptocity" type="text" value="<?php echo htmlQuotes($therecord["shiptocity"])?>" size="35" maxlength="64" tabindex="21" />				
			</p>

			<p class="csz">
				<label for="shiptostate">state/province</label><br />
				<input id="shiptostate" name="shiptostate" type="text" value="<?php echo htmlQuotes($therecord["shiptostate"])?>" size="3" maxlength="5" tabindex="22" />				
			</p>
			
			<p>
				<label for="shiptopostalcode">zip/postal code</label><br />
				<input id="shiptopostalcode" name="shiptopostalcode" type="text" value="<?php echo htmlQuotes($therecord["shiptopostalcode"])?>" size="12" maxlength="15" tabindex="23" />				
			</p>
			<p>
				<label for="shiptocountry">country</label><br />
				<input id="shiptocountry" name="shiptocountry" type="text" value="<?php echo htmlQuotes($therecord["shiptocountry"])?>" size="44" maxlength="128" tabindex="24"/>				
			</p>
		</fieldset>
		
		<fieldset>
			<legend><label for="comments">memo</label></legend>
			<p><br />
			<textarea name="comments" cols="20" rows="8" id="comments" style="width:98%" tabindex="30"><?php echo $therecord["comments"]?></textarea>
			</p>
		</fieldset>
		
	</div><?php include("../../include/createmodifiedby.php"); ?>
	</div>
	<?php include("../../footer.php")?>
</form>
</body>
</html>
