<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");

	include("include/clients_functions.php");
	include("include/clients_addedit_include.php");
?><?PHP $pageTitle="Client/Prospect"?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../../common/javascript/common.js"></script>
<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js"></script>
<script language="JavaScript" src="../../common/javascript/autofill.js"></script>
</head>
<body><?php include("../../menu.php")?>
<?PHP if (isset($statusmessage)) {?>
	<div class="standout" style="margin-bottom:3px;"><?PHP echo $statusmessage ?></div>
<?PHP } // end if ?>

<form action="<?php echo $_SERVER["PHP_SELF"]; if(isset($_GET["invoiceid"])) echo "?invoiceid=".$_GET["invoiceid"]; ?>" method="post" name="record" onSubmit="return validateForm(this);"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<?php client_tabs("General",$therecord["id"]);?>
<div class="untabbedbox">
	<div style="float:right;width:200px;">
			<?php include("../../include/savecancel.php"); ?>
			<?php if(isset($_GET["invoiceid"])){?>
			<div class="box">
				<div>
					<input name="gotoinvoice" type="button" value="back to order" onClick="location.href='invoices_addedit.php?id=<?php echo $_GET["invoiceid"] ?>'" style="width:100%" class="Buttons">
				</div>			
			</div>
			<?php } ?>			
			<div class="box">
				<div>
					id<br>
					<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:100%">
				</div>
				<div>
					<strong>type</strong><br>
				    <?PHP basic_choicelist("type",$therecord["type"],Array(Array("name"=>"prospect","value"=>"prospect"),Array("name"=>"client","value"=>"client")),Array("style"=>"width:170px;","class"=>"important"));?>
				</div>
				<div align="center">
					<?PHP field_checkbox("inactive",$therecord["inactive"])?><strong>inactive</strong>
				</div>
			</div>

			<div class="box">
			<div>
			sales manager<br>
        	<?PHP autofill("salesmanagerid",$therecord["salesmanagerid"],9,"users.id","concat(users.firstname,\" \",users.lastname)","\"\"","users.revoked=0 AND users.id > 1",Array("style"=>"width:100%;","maxlength"=>"64")) ?>
			</div>
			<div>
				lead source<br>
        		<?PHP choicelist("leadsource",$therecord["leadsource"],"leadsource",Array("class"=>"small","style"=>"width:170px")); ?>
			</div>
			</div>
			
			<div class="box">
				<div><strong>default payment information</strong></div>
				<div>
					payment type<br>
					<?PHP choicelist("paymentmethod",$therecord["paymentmethod"],"paymentmethod",Array("style"=>"width:170px;")); ?>
				</div>
				<div>
					credit card number<br>
				  	<input name="ccnumber" type="text" value="<?PHP echo $therecord["ccnumber"] ?>" size="22" maxlength="32" style="width:100%" >
				</div>
				<div>
					credit card expiration<br>
				  	<input name="ccexpiration" type="text"  value="<?PHP echo $therecord["ccexpiration"] ?>" size="7" maxlength="7">
				</div>
			</div>	
				
			<div class="box">
				<div>
					comments<br>
					<textarea name="comments" cols="20" rows="13" id="comments" style="width:100%"><?php echo $therecord["comments"]?></textarea>
				</div>
			</div>
	</div>
	
<div style="margin-right:200px;">
<h1><?php echo $pageTitle ?></h1>

				<table border="0" cellpadding="0" cellspacing="0" class="recordtable">
						<tr>
						 <td nowrap>
							<div>
								<strong>first name</strong><br>								
								<input name="firstname" id="firstname" type="text" value="<?php echo $therecord["firstname"]?>" size="32" maxlength="65" class="important" style="font-weight:bold;">
								<script language="javascript">var thefirstname=getObjectFromID("firstname");thefirstname.focus()</script>
							</div>
						 </td>			
						 <td nowrap>
							<div>
								<strong>last name</strong><br>
								<input name="lastname" type="text" value="<?php echo $therecord["lastname"]?>" size="32" maxlength="65" class="important" style="font-weight:bold;">
							</div>
							</td>
						</tr>
						<tr>
						 <td colspan="2" nowrap></td>
					    </tr>
		    </table>
					<div>
						<strong>company</strong><br>
						<input name="company" type="text" id="company" value="<?php echo $therecord["company"]?>" size="71" maxlength="128" class="important">
					</div>	
					
					<h2>contact information</h2>
					
<table border="0" cellpadding="0" cellspacing="0" class="recordtable" width="">
    	<tr>
    		<td nowrap><div>
				work phone<br>
        		<?PHP field_text("workphone",$therecord["workphone"],0,"Work phone must be a valid.(format example: 505-896-3522)","phone",Array("style"=>"","size"=>"22","maxlength"=>"64","")); ?>
    		</div></td>
    		<td nowrap><div>home phone<br>
        			<?PHP field_text("homephone",$therecord["homephone"],0,"Home phone must be a valid.(format example: 505-896-3522)","phone",Array("style"=>"","size"=>"22","maxlength"=>"64","")); ?>
			</div></td>
    		</tr>
    	<tr>
    		<td nowrap><div>
				mobile phone<br>
        		<?PHP field_text("mobilephone",$therecord["mobilephone"],0,"Mobile phone must be a valid.(format example: 505-896-3522)","phone",Array("style"=>"","size"=>"22","maxlength"=>"64","")); ?>
			</div></td>
    		<td><div>
				fax number <br>
        		<?PHP field_text("fax",$therecord["fax"],0,"Fax must be a valid.(format example: 505-896-3522)","phone",Array("style"=>"","size"=>"22","maxlength"=>"64","")); ?>
    		</div></td>
    		</tr>
    	</table>
		<div>
			other phone<br>
			<?PHP field_text("otherphone",$therecord["otherphone"],0,"Home phone must be a valid.(format example: 505-896-3522)","phone",Array("style"=>"","size"=>"22","maxlength"=>"64","")); ?>
		</div>
		<div style="margin-top:10px;">
			e-mail address <br>
        	<?PHP field_email("email",$therecord["email"],Array("size"=>"71","maxlength"=>"128")); ?>
		</div>
		<div>
			web address<br>
        	<?PHP field_web("webaddress",$therecord["webaddress"],Array("size"=>"71","maxlength"=>"128")); ?>
		</div>				
		<h2>addresses</h2>
		<div>
		<strong>billing/main address</strong><br>
        <input name="address1" type="text" size="71" maxlength="128" value="<?PHP echo $therecord["address1"]?>"><br>
		<input name="address2" type="text" size="71" maxlength="128" style="margin-top:2px;" value="<?PHP echo $therecord["address2"]?>">
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
        	</table>
		<div class="dottedline">country<br>
            	<input name="country" type="text" value="<?PHP echo $therecord["country"]?>" size="44" maxlength="128">
        	</div>
			<div style="margin-top:10px;"><strong>shipping address (if different from billing)</strong><br>
                	<input name="shiptoaddress1" type="text" size="71" maxlength="128" value="<?PHP echo $therecord["shiptoaddress1"]?>">
                	<br>
                	<input name="shiptoaddress2" type="text" size="71" maxlength="128" style="margin-top:2px;" value="<?PHP echo $therecord["shiptoaddress2"]?>">
           	</div>
			<table border="0" cellpadding="0" cellspacing="0">
            	<tr>
            		<td nowrap><div> city<br>
                    				<input name="shiptocity" type="text" value="<?php echo $therecord["shiptocity"]?>" size="35" maxlength="64">
            				</div></td>
            		<td nowrap ><div> state/province<br>
                    				<input name="shiptostate" type="text" value="<?php echo $therecord["shiptostate"]?>" size="2" maxlength="2">
            				</div></td>
            		<td nowrap><div> zip/postal code<br>
                    				<input name="shiptopostalcode" type="text" value="<?php echo $therecord["shiptopostalcode"]?>" size="12" maxlength="15">
            				</div></td>
           		</tr>
           	</table>
			<div> 				
			country<br>
            	<input name="shiptocountry" type="text" value="<?PHP echo $therecord["shiptocountry"]?>" size="44" maxlength="128">
        	</div>
</div>					
		
<?php include("../../include/createmodifiedby.php"); ?>

	</div>						
</form>
</body>
</html>