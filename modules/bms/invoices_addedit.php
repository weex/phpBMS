<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/invoices_functions.php");
	include("include/invoices_addedit_include.php");
	
	$pageTitle="Order/Invoice";
	
	$_SESSION["printing"]["tableid"]=3;
	$_SESSION["printing"]["theids"]=array($therecord["id"]);
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
<script language="JavaScript" src="javascript/invoice.js"></script>
<script language="JavaScript" src="../../common/javascript/datepicker.js"></script>
</head>
<body onLoad="initializePage()"><?php include("../../menu.php")?>
<?PHP if (isset($statusmessage)) {?>
	<div class="standout" style="margin-bottom:3px;"><?PHP echo $statusmessage ?></div>	
<?PHP } // end if ?>
<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="if(validateForm(this)) return checkShipping(); else return false;"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<?php invoice_tabs("General",$therecord["id"]);?><div class="untabbedbox">
	<div style="float:right;width:225px;margin-bottom:0px;padding-bottom:0px;">
		  <?php if($therecord["id"]){?>
			<div align="right" style="float:left;padding-left:0px;padding-right:0px;">
				<input name="doprint" type="button" value="print" onClick="doPrint(<?php echo $therecord["id"]?>)" class="Buttons" style="width:75px;"/>
			</div>
		  <?php }//end if?>
		<?php include("../../include/savecancel.php"); ?>
		  </script>
	</div>
	<h1 style="margin-right:<?php if($therecord["id"]) echo 230; else echo 155 ?>px;"><?php echo $pageTitle ?></h1>		
	
	 <fieldset style="clear:both;float:right;width:240px;margin-top:0px;">
		<legend>attributes</legend>
		<div align="left" style="float:right;padding-top:0px;">
			<label for="id">
				id<br>
				<input name="id" id="id" type="text" value="<?php echo $therecord["id"]; ?>" size="11" maxlength="11" readonly="true" class="uneditable" style="" tabindex="-1">
			</label>
			<label for="invoicedate">
				invoice date<br>
				<?PHP field_datepicker("invoicedate",$therecord["invoicedate"],0,"Invoice date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"11"),false);?>
			</label>
			<label for="requireddate">
				required date<br>
				<?PHP field_datepicker("requireddate",$therecord["requireddate"],0,"Required date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"13"),false);?>
			</label for="">
		</div>
		<div align="left" style=";padding-top:0px;margin-bottom:27px;">
			<label for="type" class="important">
				type<br>
				<?PHP  if($therecord["status"]=="VOID" || $therecord["status"]=="Invoice") {?>
					<input name="status" type="text" value="<?php echo $therecord["status"]?>" size="11" maxlength="11" readonly="true" class="uneditable" style="width:90px;font-weight:bold" tabindex=9 />
				<?php }else 
					basic_choicelist("status",$therecord["status"],Array(Array("name"=>"Quote","value"=>"Quote"),Array("name"=>"Order","value"=>"Order"),Array("name"=>"Invoice","value"=>"Invoice"),Array("name"=>"VOID","value"=>"VOID")),Array("onChange"=>"checkStatus(this)","class"=>"important","style"=>"width:90px","tabindex"=>"9"));
				?>
			</label>
			<label for="orderdate">
				order date<br>
				<?PHP field_datepicker("orderdate",$therecord["orderdate"],0,"Order date must be a valid date",Array("size"=>"11","maxlength"=>"11","tabindex"=>"10"),false);?>
			</label>
			<label for="ponumber">
				client PO#<br>
				<input name="ponumber" id="ponumber" type="text" value="<?PHP echo $therecord["ponumber"]?>" size="11" maxlength="64" tabindex=12 />
			</label>
		</div>
		<div>
			<label for="leadsource" style="text-align:left">
			lead source<br>
			<?PHP choicelist("leadsource",$therecord["leadsource"],"leadsource",Array("style"=>"width:98%;","tabindex"=>"14")); ?>
			</label>
		</div>
	</fieldset>
	
	<fieldset>
		<legend><label for="ds-clientid">client</label></legend>
		<div class="important" style="margin-bottom:13px;">
			  <?PHP autofill("clientid",$therecord["clientid"],2,"clients.id","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","if(clients.city!=\"\",concat(clients.city,\", \",clients.state),\"\")","clients.inactive!=1 AND clients.type=\"client\"",Array("size"=>"45","maxlength"=>"128","style"=>"","style"=>"font-weight:bold","tabindex"=>"1"),1,"The record must have a client chosen.") ?>
			  <script language="JavaScript">
				document.forms["record"]["clientid"].onchange=populateShipping;
				document.forms["record"]["ds-clientid"].focus();
			  </script>
			  <?php if($therecord["clientid"]){?>
			  <input name="viewclient" type="button" value="view client" onClick="viewClient(this.form)" class="Buttons" tabindex=2 />
			  <?php }//end if?>
		</div>
	</fieldset>
	<fieldset>
		<legend><label for="address1">shipping address</label></legend>		
		<div>
		<input name="address1" id="address1" type="text" style=";margin-bottom:2px;" value="<?PHP echo $therecord["address1"]?>" size="70" maxlength="128" tabindex=3 />
		<br>
		<input name="address2" id="address2" type="text"  value="<?PHP echo $therecord["address2"]?>" size="70" maxlength="128" tabindex=4 />
		</div>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td nowrap><label for="city">
						city<br>
						<input name="city" type="text" id="city" value="<?php echo $therecord["city"]?>" size="35" maxlength="64" tabindex=5 />
					</label></td>
				<td nowrap ><label for="state">
						state/prov<br>
						<input name="state" type="text" id="state" value="<?php echo $therecord["state"]?>" size="2" maxlength="2" tabindex=6 />
					</label></td>
				<td nowrap><label for="postalcode">
					zip/postal code<br>
						<input name="postalcode" type="text" id="postalcode" value="<?php echo $therecord["postalcode"]?>" size="12" maxlength="15" tabindex=7 />
					</label>
				</td>
				</tr>
				<tr><td colspan=3><label for="country">
				country<br>
				<input name="country" id="country" type="text" value="<?PHP echo $therecord["country"]?>" size="45" maxlength="64" tabindex=8 />
				</label></td></tr>
			</table>
	</fieldset>
	<div style="clear:both;margin-bottom:0px;padding-bottom:0px;"><iframe src="invoices_lineitems.php?id=<?PHP echo $therecord["id"];?>" name="lineitems" width="99%" marginwidth="0" height="215" marginheight="0" scrolling="auto" tabindex="15"></iframe></div>
	<fieldset style="float:left;width:370px;">
		<legend>tax / weight</legend>
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td nowrap>
					<label for="ds-taxareaid">
						tax area<br>
				   <?PHP autofill("taxareaid",$therecord["taxareaid"],6,"tax.id","tax.name","concat(tax.percentage,\"%\")","",Array("size"=>"29","maxlength"=>"64","tabindex"=>"16"),0) ?>		  
				   <script language="JavaScript">
						document.forms["record"]["taxareaid"].onchange=getPercentage;
				  </script>
					</label>
				</td>
				<td nowrap>
					<label for="displaytaxpercentage">
						tax percentage<br>
						<input name="displaytaxpercentage" id="displaytaxpercentage" type="text" value="<?PHP echo $therecord["taxpercentage"]?>%" size="11" maxlength="9" readonly="true" class="uneditable" style="text-align:right;" />
						<input name="taxpercentage" id="taxpercentage" type="hidden" value="<?PHP echo $therecord["taxpercentage"]?>" />&nbsp;&nbsp;&nbsp;&nbsp;
					</label>
				</td>
				<td nowrap>
					<label for="totalweight">
					total weight<br>
					<input name="totalweight" type="text" value="<?PHP echo $therecord["totalweight"]?>" size="9" maxlength="9" readonly="true" class="uneditable" style="text-align:right;" />
					</label>
				</td>				
			</tr>
		</table>
	</fieldset>
	<div style="margin-left:373px;;margin-right:34px;margin-top:0px;padding-top:0px;" align="right" >
		<label for="totaltni" style="margin:0px;padding:0px;">subtotal&nbsp;<input class="uneditable" name="totaltni" id="totaltni" type="text" value="<?PHP echo $therecord["totaltni"]?>" size="10" maxlength="10" readonly="true" onChange="calculateTotal();" style="text-align:right;" /></label>
		<label for="tax" style="margin:0px;padding:0px;">tax&nbsp;<input name="tax" id="tax" type="text" value="<?PHP echo $therecord["tax"]?>" size="10" maxlength="10" onChange="calculateTotal();" style="text-align:right;" tabindex=17 /></label>
		<label for="shipping" style="margin:0px;padding:0px;">shipping&nbsp;<input name="shipping" id="shipping" type="text" value="<?PHP echo $therecord["shipping"]?>" size="10" maxlength="10" onChange="calculateTotal();" style="text-align:right;" tabindex=18 /></label>
		<label for="totalti" style="margin:0px;padding:0px;" class=important>total&nbsp;<input class="uneditable" name="totalti" id="totalti" type="text" value="<?PHP echo $therecord["totalti"]?>" size="10" maxlength="10" onChange="calculateTotal();"  readonly="true" style="text-align:right;font-weight:bold"/ ></label>
		<input name="totalcost" type="hidden" value="<?PHP echo $therecord["totalcost"] ?>">
	</div>
	
	<div style="width:380px;float:right;padding:0px;margin:0px;">
	<fieldset>
		<legend>payment</legend>
		<label for="paymentmethod">
			method<br>
			<?PHP choicelist("paymentmethod",$therecord["paymentmethod"],"paymentmethod",array("tabindex"=>"19")); ?>
			  <script language="JavaScript">document.forms["record"]["paymentmethod"].onchange2=new Function("showPaymentOptions()");</script>
		</label>
		<div style="display:none;" id="checkpaymentinfo">
			<label for="checkno">check number<br>
				<input name="checkno" type="text" id="checkno" value="<?PHP echo $therecord["checkno"] ?>" size="24" maxlength="32" tabindex=20/>
			</label>		
			<label for="bankname">bank name<br>
				<input name="bankname" type="text" value="<?PHP echo $therecord["bankname"] ?>" size="24" maxlength="64" style="width:98%" tabindex=21/>
			</label>
		</div>	
		<div style="padding:0px;display:none;" id="ccpaymentinfo">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<label for="ccnumber">
							card number<br>
							<input name="ccnumber" type="text" id="ccnumber" value="<?PHP echo $therecord["ccnumber"] ?>" size="28" maxlength="40" tabindex=20/>
						</label>
					</td>
					<td>
						<label for="ccexpiration">
							expiration<br>
							<input name="ccexpiration" id="ccexpiration" type="text"  value="<?PHP echo $therecord["ccexpiration"] ?>" size="8" maxlength="10" tabindex=21 />
						</label>
					</td>
				</tr>
			</table>
			<label for="ccverification">verification/pin<br>
				<input id="ccverification" name="ccverification" type="text"  value="<?PHP echo $therecord["ccverification"] ?>" size="8" maxlength="7" tabindex=22 />
			</label>		
		</div><script language="JavaScript">showPaymentOptions();</script>
	</fieldset>
	<fieldset>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="100%" align="right" nowrap ><label for="amountpaid" class="important">amount paid</label></td>
				<td nowrap>
					<input name="amountpaid" id="amountpaid" type="text" value="<?PHP echo $therecord["amountpaid"]?>" size="11" maxlength="11" onChange="calculatePaidDue();" style="text-align:right;" class="important" tabindex=23/>
					<input name="Button" type="button" class="Buttons" onClick="this.form['amountpaid'].value=this.form['totalti'].value;calculatePaidDue();" value="mark paid in full" style="margin-left:3px;" tabindex=24/>
				</td>
			 </tr>
			 <tr>
				<td align="right" nowrap ><label for="amountdue" class="important">amount due</label></td>
				<td>
					<input id="amountdue" name="amountdue" type="text" value="<?PHP echo $therecord["amountdue"] ?>" size="11" maxlength="11" onChange="calculatePaidDue();" style="text-align:right;" class="important" tabindex=25/>
				</td>
			 </tr>	  
		</table>
	</fieldset>
	</div>
	
	<fieldset>
		<legend><label for="ds-shippingmethod">ship via</label></legend>
		<div style="padding-top:0px;">
		   <?PHP choicelist("shippingmethod",$therecord["shippingmethod"],"shippingmethod",array("tabindex"=>"26")); ?>&nbsp;
		   <input name="estimate" type="button" class="Buttons" id="estimate" value="calculate shipping" onClick="estimateShipping()" tabindex=27/>
		</div>
	</fieldset>
	<fieldset>
		<legend>shipping status</legend>
		<label for="shipped">
		   <?PHP field_checkbox("shipped",$therecord["shipped"],($therecord["shipped"]==1),Array("onChange"=>"setShipped(this)","style","vertical-align:middle","tabindex"=>"28"));?> shipped &nbsp;&nbsp;				   
		</label>
		<label for="shippeddate">
		shipped date<br>
		<?PHP field_datepicker("shippeddate",$therecord["shippeddate"],0,"Shipped date must be a valid date.",Array("size"=>"15","maxlength"=>"11","tabindex"=>"29"),false); ?>
		</label>
		<label for="trackingno">
			tracking number<br>
			<input id="trackingno" name="trackingno" type="text" value="<?PHP echo $therecord["trackingno"] ?>" size="26" maxlength="64" style="width:98%" tabindex=30 />
		</label>
	</fieldset>

<fieldset style="clear:both;">
	<legend>web</legend>
	<label for="weborder">
		<?PHP field_checkbox("weborder",$therecord["weborder"],0,array("tabindex"=>"31"));?> web order
	</label>
	<label for="webconfirmationno">
		confirmation number<br>
		<input name="webconfirmationno" type="text" value="<?PHP echo $therecord["webconfirmationno"] ?>" size="38" maxlength="64" tabindex=32 />
	</label>								
</fieldset>
<fieldset>
<legend>instructions</legend>
	<label for="specialinstructions">
		special instructions (will not print on invoice)<br>
		<textarea id="specialinstructions" name="specialinstructions" cols="45" rows="3" style="width:100%" tabindex=33><?PHP echo $therecord["specialinstructions"]?></textarea>	
	</label>
	<label for="printedinstructions">
		printed instructions<br>
		<textarea id="printedinstructions" name="printedinstructions" cols="45" rows="3"  style="width:100%" tabindex=34><?PHP echo $therecord["printedinstructions"]?></textarea>	
	</label>
</fieldset>		
<?php include("../../include/createmodifiedby.php"); ?>
<?PHP if($therecord["status"]=="VOID" || $therecord["status"]=="Invoice"){?>
<script language="JavaScript">disableSaves(document.forms["record"]);</script>
<?PHP }// end if ?>
</div>
</form></body>
</html>