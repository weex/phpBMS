<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/invoices_functions.php");
	include("include/invoices_addedit_include.php");
	
	$pageTitle="Order/Invoice";
	
	$_SESSION["printing"]["tableid"]=3;
	$_SESSION["printing"]["theids"]=array($therecord["id"]);
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" >
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/common.js"></script>
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
<script language="JavaScript" src="javascript/invoice.js"></script>
<script language="JavaScript" src="../../common/javascript/cal.js"></script>
</head>
<body><?php include("../../menu.php")?>
<?PHP if (isset($statusmessage)) {?>
	<div class="standout" style="margin-bottom:3px;"><?PHP echo $statusmessage ?></div>	
<?PHP } // end if ?>
<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<?php invoice_tabs("General",$therecord["id"]);?><div class="untabbedbox">
	<div style="float:right;width:250px;margin-bottom:0px;padding-bottom:0px;">
			<?php include("../../include/savecancel.php"); ?>
			  </script>
			  <?php if($therecord["id"]){?>
			  	<div align="right">
				  	<input name="doprint" type="button" value="print" onClick="doPrint(<?php echo $therecord["id"]?>)" class="buttons" style="width:139px;">
				</div>
			  <?php }//end if?>
			<div class="box" style="margin-bottom:0px;" align="right">
				<div align="left" style="float:right">
					<div>
						id<br>
						<input name="id" id="id" type="text" value="<?php echo $therecord["id"]; ?>" size="11" maxlength="11" readonly="true" class="uneditable" style="">
					</div>
					<div>
						invoice date<br>
						<?PHP field_cal("invoicedate",$therecord["invoicedate"],0,"Invoice date must be a valid date",Array("size"=>"11","maxlength"=>"11"),false);?>
					</div>
					<div>
						required date<br>
						<?PHP field_cal("requireddate",$therecord["requireddate"],0,"Required date must be a valid date",Array("size"=>"11","maxlength"=>"11"),false);?>
					</div>
				</div>
				<div align="left">
					<div class="important">
						type<br>
						<?PHP  if($therecord["status"]=="VOID" || $therecord["status"]=="Invoice") {?>
							<input name="status" type="text" value="<?php echo $therecord["status"]?>" size="11" maxlength="11" readonly="true" class="uneditable" style="width:100%;font-weight:bold">
						<?php }else 
							basic_choicelist("status",$therecord["status"],Array(Array("name"=>"Quote","value"=>"Quote"),Array("name"=>"Order","value"=>"Order"),Array("name"=>"Invoice","value"=>"Invoice"),Array("name"=>"VOID","value"=>"VOID")),Array("onChange"=>"checkStatus(this)","class"=>"important","style"=>"width:90px"));
						?>
					</div>
					<div>
						order date<br>
						<?PHP field_cal("orderdate",$therecord["orderdate"],0,"Order date must be a valid date",Array("size"=>"11","maxlength"=>"11"),false);?>
					</div>
					<div>
						Their PO<br>
						<input name="ponumber" id="ponumber" type="text" value="<?PHP echo $therecord["ponumber"]?>" size="11" maxlength="64">
					</div>
				</div>
				<div align="left"><?PHP field_checkbox("weborder",$therecord["weborder"],0,Array("onClick"=>"showWebConfirmationNum(this);"));?> web order</div>
				<div align="left" style="display:<?php if($therecord["weborder"]==1) echo "block"; else echo "none"; ?>" id="webconfirmdiv">
					web confirmation number<br>
					<input name="webconfirmationno" type="text" value="<?PHP echo $therecord["webconfirmationno"] ?>" size="38" maxlength="64">
				</div>						

				<div align="left">
					lead source<br>
					<?PHP choicelist("leadsource",$therecord["leadsource"],"leadsource",Array("style"=>"width:220px;")); ?>
				</div>				

			</div>
	</div>
	
	<div style="margin-right:255px;">
		<h1><?php echo $pageTitle ?></h1>
		<div class="important" style="margin-bottom:13px;">
			  client<br>			  
			  <?PHP autofill("clientid",$therecord["clientid"],2,"clients.id","if(clients.lastname!=\"\",concat(clients.lastname,\", \",clients.firstname,if(clients.company!=\"\",concat(\" (\",clients.company,\")\"),\"\")),clients.company)","if(clients.city!=\"\",concat(clients.city,\", \",clients.state),\"\")","clients.inactive!=1 AND clients.type=\"client\"",Array("size"=>"70","maxlength"=>"128","style"=>"","style"=>"font-weight:bold"),1,"The quote/order/invoice must have a client.") ?>			  
			  <script language="JavaScript">
				document.forms["record"]["clientid"].onchange=populateShipping;
				document.forms["record"]["ds-clientid"].focus();
			  </script>
			  <?php if($therecord["clientid"]){?>
			  <input name="viewclient" type="button" value="view client" onClick="viewClient(this.form)" class="buttons">
			  <?php }//end if?>
		</div>	
		<div>
			shipping address<br>
			<input name="address1" id="address1" type="text" style=";margin-bottom:2px;" value="<?PHP echo $therecord["address1"]?>" size="70" maxlength="128">
			<br>
			<input name="address2" id="address2" type="text"  value="<?PHP echo $therecord["address2"]?>" size="70" maxlength="128">
		</div>
		<table border="0" cellpadding="0" cellspacing="0">
        	<tr>
        		<td nowrap><div>
						city<br>
            			<input name="city" type="text" id="city" value="<?php echo $therecord["city"]?>" size="35" maxlength="64">
        			</div></td>
        		<td nowrap ><div>
						state/province<br>
            			<input name="state" type="text" id="state" value="<?php echo $therecord["state"]?>" size="2" maxlength="2">
					</div></td>
        		<td nowrap><div>
					zip/postal code<br>
            			<input name="postalcode" type="text" id="postalcode" value="<?php echo $therecord["postalcode"]?>" size="12" maxlength="15">
					</div>
				</td>
        		</tr>
				<tr><td colspan=3><div>
				country<br>
				<input name="country" id="country" type="text" value="<?PHP echo $therecord["country"]?>" size="44" maxlength="64">
				</div></td></tr>
        	</table>
	</div>
	<div style="clear:both;"></div>
	<div style="margin-bottom:0px;padding-bottom:0px;"><iframe src="invoices_lineitems.php?id=<?PHP echo $therecord["id"];?>" name="lineitems" width="99%" marginwidth="0" height="215" marginheight="0" scrolling="auto"></iframe></div>
	<div style="float:left;width:370px;">
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td nowrap>
					<div>
						tax area<br>
				   <?PHP autofill("taxareaid",$therecord["taxareaid"],6,"tax.id","tax.name","concat(tax.percentage,\"%\")","",Array("size"=>"22","maxlength"=>"64"),0) ?>		  
				   <script language="JavaScript">
						document.forms["record"]["taxareaid"].onchange=getPercentage;
				  </script>
					</div>
				</td>
				<td nowrap>
					<div>
						tax percentage<br>
						<input name="displaytaxpercentage" id="displaytaxpercentage" type="text" value="<?PHP echo $therecord["taxpercentage"]?>%" size="11" maxlength="9" readonly="true" class="uneditable" style="text-align:right;">
						<input name="taxpercentage" id="taxpercentage" type="hidden" value="<?PHP echo $therecord["taxpercentage"]?>">&nbsp;&nbsp;&nbsp;&nbsp;
					</div>
				</td>
				<td nowrap>
					<div>
					total weight<br>
					<input name="totalweight" type="text" value="<?PHP echo $therecord["totalweight"]?>" size="9" maxlength="9" readonly="true" class="uneditable" style="text-align:right;">
					</div>
				</td>				
			</tr>
		</table>
	</div>
	<div style="margin-left:373px;;margin-right:28px;margin-top:0px;padding-top:0px;" align="right">
		<div style="margin:0px;padding:0px;">subtotal&nbsp;<input name="totaltni" type="text" value="<?PHP echo $therecord["totaltni"]?>" size="10" maxlength="10" readonly="true" onChange="calculateTotal();" class="uneditable" style="text-align:right;"></div>
		<div style="margin:0px;padding:0px;">tax&nbsp;<input name="tax" id="tax" type="text" value="<?PHP echo $therecord["tax"]?>" size="10" maxlength="10" onChange="calculateTotal();" style="text-align:right;"></div>
		<div style="margin:0px;padding:0px;">shipping&nbsp;<input name="shipping" id="shipping" type="text" value="<?PHP echo $therecord["shipping"]?>" size="10" maxlength="10" onChange="calculateTotal();" style="text-align:right;" ></div>
		<div style="margin:0px;padding:0px;" class=important>total&nbsp;<input name="totalti" type="text" value="<?PHP echo $therecord["totalti"]?>" size="10" maxlength="10" onChange="calculateTotal();" class="uneditable"  readonly="true" style="text-align:right;font-weight:bold"></div>
		<input name="totalcost" type="hidden" value="<?PHP echo $therecord["totalcost"] ?>">
	</div><div style="width:300px;float:right;">
	<h2>payment information</h2>
	<div>
		payment method<br>
		<?PHP choicelist("paymentmethod",$therecord["paymentmethod"],"paymentmethod"); ?>
		  <script language="JavaScript">document.forms["record"]["paymentmethod"].onchange2=new Function("showPaymentOptions()");</script>
	</div>
	<div style="display:none;" id="checkpaymentinfo">
		<div>
			check number<br>
			<input name="checkno" type="text" id="checkno" value="<?PHP echo $therecord["checkno"] ?>" size="24" maxlength="32">
		</div>		
		<div>
			bank name<br>
			<input name="bankname" type="text" value="<?PHP echo $therecord["bankname"] ?>" size="24" maxlength="64" style="width:100%">
		</div>
	</div>
	
	<div style="padding:0px;display:none;" id="ccpaymentinfo">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<div>
						card number<br>
						<input name="ccnumber" type="text" id="ccnumber" value="<?PHP echo $therecord["ccnumber"] ?>" size="28" maxlength="40">
					</div>
				</td>
				<td>
					<div>
						expiration<br>
						<input name="ccexpiration" id="ccexpiration" type="text"  value="<?PHP echo $therecord["ccexpiration"] ?>" size="8" maxlength="10">
					</div>
				</td>
			</tr>
		</table>
		<div>
			verification/pin<br>
			<input name="ccverification" type="text"  value="<?PHP echo $therecord["ccverification"] ?>" size="8" maxlength="7">
		</div>		
	</div>
	<script language="JavaScript">showPaymentOptions();</script>
<table border="0" cellpadding="0" cellspacing="0" class="box">
	 <tr>
	  <td width="100%" align="right" nowrap ><div><strong>amount paid</strong></div></td>
	  <td nowrap>
<input name="amountpaid" type="text" value="<?PHP echo $therecord["amountpaid"]?>" size="11" maxlength="11" onChange="calculatePaidDue();" style="text-align:right; font-weight:bold;">
	   <input name="Button" type="button" class="Buttons" onClick="this.form['amountpaid'].value=this.form['totalti'].value;calculatePaidDue();" value="mark paid in full" style="margin-left:3px;"></td>
	 </tr>
	 <tr>
	  <td align="right" nowrap ><div><strong>amount due</strong></div></td>
	  <td >
<input name="amountdue" type="text" value="<?PHP echo $therecord["amountdue"] ?>" size="11" maxlength="11" onChange="calculatePaidDue();" style="text-align:right; font-weight:bold;"></td>
	 </tr>	  
	</table>
</div><div style="margin-right:330px;">
			<h2>shipping information</h2>
				<div>
				   ship via<br>
				   <?PHP choicelist("shippingmethod",$therecord["shippingmethod"],"shippingmethod"); ?><input name="estimate" type="button" class="Buttons" id="estimate" value="estimate UPS shipping" onClick="estimateShipping()" style="margin-left:10px;">		
				</div>			
				<div>
					tracking number<br>
					<input name="trackingno" type="text" value="<?PHP echo $therecord["trackingno"] ?>" size="52" maxlength="64" style="">
				</div>
				<div>
				   <?PHP field_checkbox("shipped",$therecord["shipped"],$therecord["shipped"],Array("onChange"=>"setShipped(this)","style","vertical-align:middle"));?> shipped							   
				</div>
				<div>
					shipped date<br>
					<?PHP 
						$theattributes=Array("size"=>"15","maxlength"=>"11","onChange"=>"setShipped(this)");	
						if($therecord["shipped"]){
							$theattributes["readonly"]="true";
							$theattributes["class"]="uneditable";				
							}
						field_text("shippeddate",$therecord["shippeddate"],0,"Shipped date must be a valid date.","date",$theattributes); 
					?>
				</div>
		</div>
		

<div>
<h2>other information</h2>
<div>
	special instructions (will not print on invoice)<br>
	<textarea name="specialinstructions" cols="45" rows="3" style="width:100%"><?PHP echo $therecord["specialinstructions"]?></textarea>	
</div>
<div>
	printed instructions<br>
	<textarea name="printedinstructions" cols="45" rows="3" id="printedinstructions" style="width:100%"><?PHP echo $therecord["printedinstructions"]?></textarea>	
</div>
</div>		
<?php include("../../include/createmodifiedby.php"); ?>
<?PHP if($therecord["status"]=="VOID" || $therecord["status"]=="Invoice"){?>
<script language="JavaScript">disableSaves(document.forms["record"]);</script>
<?PHP }// end if ?>
</div>
</form></body>
</html>