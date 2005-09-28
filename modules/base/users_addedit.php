<?php 
	include("../../include/session.php");
	include("../../include/common_functions.php");
	include("../../include/fields.php");
	include("include/admin_functions.php");
	include("include/users_addedit_include.php");
	
	$pageTitle="User";
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $pageTitle ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo $_SESSION["app_path"] ?>common/stylesheet/<?php echo $_SESSION["stylesheet"] ?>/base.css" rel="stylesheet" type="text/css">

<script language="JavaScript" src="../../common/javascript/fields.js"></script>
<script language="JavaScript" src="../../common/javascript/choicelist.js"></script>
<script language="JavaScript">
	function checkPassword(theform){
		if(theform["password"].value!=theform["password2"].value && theform["command"].value!="cancel"){
			alert("Passwords did not match.");
			return false;
		}
		return true;
	}
</script>
</head>
<body><?php include("../../menu.php")?>
<?PHP if (isset($statusmessage)) {?>
	<div class="standout" style="margin-bottom:3px;"><?PHP echo $statusmessage ?></div>
<?PHP } // end if ?>
<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="post" name="record" onSubmit="return (validateForm(this) && checkPassword(this));"><div style="position:absolute;display:none;"><input type="submit" value=" " onClick="return false;" style="background-color:transparent;border:0;position:absolute;"></div>
<div class="bodyline">
	<div style="float:right;width:200px;">
		<?php include("../../include/savecancel.php"); ?>
		<div class="box">
			<div>
				id<br>
				<input name="id" type="text" value="<?php echo $therecord["id"]; ?>" size="5" maxlength="5" readonly="true" class="uneditable" style="width:100%">
			</div>
			<div>
				last login<br>
				<input name="lastlogin" type="text" value="<?php echo $therecord["lastlogin"]; ?>" size="32" maxlength="64" readonly="true" class="uneditable" style="width:100%">
			</div>
			<div><?PHP field_checkbox("revoked",$therecord["revoked"])?>revoke access</div>
		</div>
		<div class=box>
			<div class="important">
				access level<br>
				<?PHP field_text("accesslevel",$therecord["accesslevel"],1,"Access Level cannot be blank and must be a valid integer.","integer",Array("size"=>"9","maxlength"=>"4")); ?>
			</div>
			<div class=tiny>
			<strong>Access Level Note:</strong><br>
			Access levels are determined by number. As security<br>
		   measures are further
			developed this may change. A <br>
		  general guidline for entering a access level number is:<br>
		 &nbsp;&nbsp;&gt; 90 Administrator<br>
		 &nbsp;&nbsp;&gt; 50 Manager<br>
		 &nbsp;&nbsp;&gt; 30 Sales Manager<br>
		 &nbsp;&nbsp;&gt; 20 Power User<br>
		 &nbsp;&nbsp;&gt;10 User<br>
		 &nbsp;&nbsp;&lt;10 Not Currently Used
			</div>
		</div>		
	</div>
	<div style="margin-right:203px;">
		<h1><?php echo $pageTitle ?></h1>
		<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td>
					<div class="important">
						first name<br>
						<?PHP field_text("firstname",$therecord["firstname"],1,"First name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","style"=>"font-weight:bolc;")); ?>
					</div>
				</td>
				<td>
					<div class="important">
					last name<br>
					<?PHP field_text("lastname",$therecord["lastname"],1,"Last name cannot be blank.","",Array("size"=>"32","maxlength"=>"64","style"=>"font-weight:bolc;")); ?>					
					</div>
				</td>
			</tr>
		</table>
		<div></div>
		<div class="box">
			<div class="important">log in information</div>
			<div class="important">
				name<br>
				<?PHP field_text("login",$therecord["login"],1,"Login cannot be blank.","",Array("size"=>"20","maxlength"=>"64","class"=>"important")); ?>
			</div>
			<div>
				set password<br>
				<input name="password" type="password" size="20" maxlength="32">
			</div>
			<div>
				confirm password<br>
				<input name="password2" type="password" size="20" maxlength="32">
			</div>		
		</div>
		<h2>other information</h2>
		<div>
			e-mail address<br>
			<?PHP field_email("email",$therecord["email"],Array("size"=>"64","maxlength"=>"128")); ?>			
		</div>
		<div>
			phone/extension<br>
			<input type="text" name="phone" value="<?php echo $therecord["phone"] ?>" size="32" maxlength="32">
		</div>
		<div>
			department<br>
			<?PHP choicelist("department",$therecord["department"],"department",Array("style"=>"")); ?>			
		</div>
		<div>
			employee number<br>
			<input type="text" name="employeenumber" value="<?php echo $therecord["employeenumber"] ?>" size="32" maxlength="32">
		</div>
	</div>
	<?php include("../../include/createmodifiedby.php"); ?>
</div>
</form>
</body>
</html>